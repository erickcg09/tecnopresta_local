<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
if (!$tienellave) {
    die('No tienes permisos para acceder a esta página.');
}

require_once("conexion.php");
require('fpdf/fpdf.php');
$link = $mysqli;
if (!mysqli_set_charset($link, "utf8")) {
    printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($link));
    exit();
}
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcorreo = $_SESSION['correomep'];

// Obtener fechas del formulario
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : date('Y-m-t');

// Validar fechas
if (empty($fecha_inicio) || empty($fecha_fin)) {
    die('Error: Fechas no especificadas.');
}

// Convertir fechas a formato MySQL
$fecha_inicio_mysql = $fecha_inicio . ' 00:00:00';
$fecha_fin_mysql = $fecha_fin . ' 23:59:59';

// Consultar citas del usuario en el rango de fechas
$query = "SELECT * FROM t_control_citas_teams 
          WHERE cedula_creador = ? 
          AND fecha_hora_inicio BETWEEN ? AND ? 
          ORDER BY fecha_hora_inicio ASC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("sss", $logusuario, $fecha_inicio_mysql, $fecha_fin_mysql);
$stmt->execute();
$result = $stmt->get_result();

// Contar citas por estado
$total_citas = 0;
$programadas = 0;
$realizadas = 0;
$canceladas = 0;

// Función para convertir texto a formato compatible con FPDF
function convertirTexto($texto) {
    // Decodificar UTF-8 a ISO-8859-1 (soporte básico para caracteres españoles)
    $texto = utf8_decode($texto);
    
    // Reemplazar caracteres problemáticos comunes
    $reemplazos = array(
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'ñ' => 'n', 'Ñ' => 'N',
        'ü' => 'u', 'Ü' => 'U',
        '¿' => '?', '¡' => '!',
        '€' => 'EUR', '«' => '"', '»' => '"'
    );
    
    return strtr($texto, $reemplazos);
}

// Clase PDF personalizada
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // tamaño del logo es el ultimo valor en este caso el 20
        $this->Image('img/gobierno.jpg', 10, 8, 20);
        
        // Arial bold 15
        $this->SetFont('Arial', 'B', 16);
        
        // Título
        $this->SetY(15);
        $this->Cell(0, 10, convertirTexto('Reporte de Citas - TecnoPresta'), 0, 1, 'C');
        
        // Línea
        $this->SetLineWidth(0.5);
        $this->SetDrawColor(98, 100, 167); // Color morado Teams
        $this->Line(10, 30, 200, 30);
        
        // Espacio
        $this->Ln(10);
    }
    
    // Pie de página
    function Footer()
    {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        
        // Número de página
        $this->Cell(0, 10, convertirTexto('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        
        // Fecha de generación
        $this->SetX(-60);
        $this->Cell(0, 10, convertirTexto('Generado: ') . date('d/m/Y H:i:s'), 0, 0, 'R');
    }
    
    // Función para agregar tabla de citas (sin destinatarios)
    function agregarTablaCitas($header, $data)
    {
        // Colores, ancho de línea y fuente en negrita
        $this->SetFillColor(98, 100, 167); // Morado Teams
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        
        // Anchuras de las columnas (solo 4 columnas ahora)
        $w = array(90, 30, 25, 45); // Asunto, Fecha, Hora, Estado
        
        // Cabecera
        for($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, convertirTexto($header[$i]), 1, 0, 'C', true);
        $this->Ln();
        
        // Restauración de colores y fuentes
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        
        // Datos
        $fill = false;
        foreach($data as $row)
        {
            // Ajustar altura si la descripción es larga
            $desc_height = max(6, ceil(strlen($row[0]) / 40) * 6);
            
            // Asunto (más espacio ahora) - convertir texto
            $this->Cell($w[0], $desc_height, convertirTexto(substr($row[0], 0, 70)), 'LR', 0, 'L', $fill);
            // Fecha (no necesita conversión ya que son números)
            $this->Cell($w[1], $desc_height, $row[1], 'LR', 0, 'C', $fill);
            // Hora (no necesita conversión ya que son números)
            $this->Cell($w[2], $desc_height, $row[2], 'LR', 0, 'C', $fill);
            
            // Color según estado
            $estado_color = array(
                'programada' => array(0, 0, 255), // Azul
                'realizada' => array(0, 128, 0),  // Verde
                'cancelada' => array(255, 0, 0)   // Rojo
            );
            
            $this->SetTextColor($estado_color[$row[3]][0], $estado_color[$row[3]][1], $estado_color[$row[3]][2]);
            $this->Cell($w[3], $desc_height, convertirTexto(ucfirst($row[3])), 'LR', 0, 'C', $fill);
            $this->SetTextColor(0);
            
            $this->Ln();
            
            $fill = !$fill;
        }
        
        // Línea de cierre
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    
    // Función para agregar estadísticas
    function agregarEstadisticas($programadas, $realizadas, $canceladas, $total)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(98, 100, 167); // Morado Teams
        $this->Cell(0, 10, convertirTexto('Resumen Estadístico'), 0, 1);
        $this->Ln(2);
        
        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(0);
        
        // Tabla de estadísticas
        $this->Cell(60, 8, convertirTexto('Citas Programadas:'), 0, 0);
        $this->SetTextColor(0, 0, 255); // Azul
        $this->Cell(30, 8, $programadas, 0, 1);
        
        $this->SetTextColor(0);
        $this->Cell(60, 8, convertirTexto('Citas Realizadas:'), 0, 0);
        $this->SetTextColor(0, 128, 0); // Verde
        $this->Cell(30, 8, $realizadas, 0, 1);
        
        $this->SetTextColor(0);
        $this->Cell(60, 8, convertirTexto('Citas Canceladas:'), 0, 0);
        $this->SetTextColor(255, 0, 0); // Rojo
        $this->Cell(30, 8, $canceladas, 0, 1);
        
        $this->SetTextColor(0);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(60, 8, convertirTexto('TOTAL DE CITAS:'), 0, 0);
        $this->SetTextColor(98, 100, 167); // Morado Teams
        $this->Cell(30, 8, $total, 0, 1);
    }
    
    // Función para crear recuadro de firma
    function agregarRecuadroFirma()
    {
        // Título de la sección de firma
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(98, 100, 167); // Morado Teams
        $this->Cell(0, 10, convertirTexto('FIRMA DIGITAL'), 0, 1, 'C');
        $this->Ln(5);
        
        // Instrucción
        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(0);
        $this->Cell(0, 8, convertirTexto('Ubique su firma digital en el recuadro a continuación:'), 0, 1, 'C');
        $this->Ln(5);
        
        // Recuadro para firma (centrado)
        $x = ($this->GetPageWidth() - 80) / 2; // Centrar recuadro de 80mm de ancho
        $this->SetX($x);
        
        // Dibujar recuadro
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.5);
        $this->Rect($x, $this->GetY(), 80, 50);
        
        // Texto dentro del recuadro (centrado)
        $this->SetFont('Arial', '', 10);
        $this->SetXY($x, $this->GetY() + 20);
        $this->Cell(80, 5, convertirTexto('FIRMA DIGITAL DEL RESPONSABLE'), 0, 1, 'C');
        
        // Espacio debajo del recuadro
        $this->SetY($this->GetY() + 35);
        

    }
}

// Crear instancia de PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);

// Información del reporte
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, convertirTexto('Información del Reporte'), 0, 1);
$pdf->SetFont('Arial', '', 11);

$pdf->Cell(50, 8, convertirTexto('Generado por:'), 0, 0);
$pdf->Cell(0, 8, convertirTexto($lognombre) . ' (' . $logusuario . ')', 0, 1);

$pdf->Cell(50, 8, convertirTexto('Correo electrónico:'), 0, 0);
$pdf->Cell(0, 8, convertirTexto($logcorreo), 0, 1);

$pdf->Cell(50, 8, convertirTexto('Período del reporte:'), 0, 0);
$pdf->Cell(0, 8, date('d/m/Y', strtotime($fecha_inicio)) . ' al ' . date('d/m/Y', strtotime($fecha_fin)), 0, 1);

$pdf->Cell(50, 8, convertirTexto('Fecha de generación:'), 0, 0);
$pdf->Cell(0, 8, date('d/m/Y H:i:s'), 0, 1);

$pdf->Ln(10);

// Preparar datos para la tabla (sin destinatarios)
$data = array();
$header = array('Asunto', 'Fecha', 'Hora', 'Estado');

if ($result->num_rows > 0) {
    while ($cita = $result->fetch_assoc()) {
        $total_citas++;
        
        // Contar por estado
        switch ($cita['estado']) {
            case 'programada':
                $programadas++;
                break;
            case 'realizada':
                $realizadas++;
                break;
            case 'cancelada':
                $canceladas++;
                break;
        }
        
        // Formatear fecha y hora
        $fecha_hora = new DateTime($cita['fecha_hora_inicio']);
        $fecha = $fecha_hora->format('d/m/Y');
        $hora = $fecha_hora->format('H:i');
        
        // Solo asunto, fecha, hora y estado (sin destinatarios)
        $data[] = array(
            $cita['asunto'] ?: 'Sin asunto',
            $fecha,
            $hora,
            $cita['estado']
        );
    }
    
    // Agregar tabla de citas
    $pdf->agregarTablaCitas($header, $data);
    
    $pdf->Ln(15);
    
    // Agregar estadísticas
    $pdf->agregarEstadisticas($programadas, $realizadas, $canceladas, $total_citas);
    
} else {
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, convertirTexto('No se encontraron citas en el período seleccionado.'), 0, 1, 'C');
    $pdf->Ln(10);
}

$pdf->Ln(20);

// Agregar recuadro para firma digital
$pdf->agregarRecuadroFirma();

$pdf->Ln(15);

// Nota legal
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 6, convertirTexto('NOTA: Este documento ha sido generado automáticamente por el sistema de gestión de citas de Microsoft Teams de TecnoPresta. ' .
                     'La firma digital en este documento tiene validez para fines de control interno y seguimiento de actividades.'));

// Salida del PDF
$nombre_archivo = 'Reporte_Citas_' . $logusuario . '_' . date('Ymd_His') . '.pdf';
$pdf->Output('D', $nombre_archivo);

$stmt->close();
$mysqli->close();
?>