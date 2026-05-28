<?php
// Iniciar sesión para verificar permisos
session_start();

// Verificar permisos (mismo código que en el listado)
$permisosValidos = [1, 2, 3, 4, 5, 7];
$tienellave = isset($_SESSION['tipo']) && in_array($_SESSION['tipo'], $permisosValidos);

if (!$tienellave) {
    die("No tienes permisos para acceder a esta función.");
}

include 'conexion.php';

// Validar y obtener parámetros con valores por defecto
$codigo = isset($_GET['codigop']) ? trim($_GET['codigop']) : '';
$estado = isset($_GET['estadop']) ? trim($_GET['estadop']) : '';
$b_estado = isset($_GET['b_estadop']) ? trim($_GET['b_estadop']) : 0;
$dependencia = isset($_GET['dependenciap']) ? trim($_GET['dependenciap']) : '';

$activado = 1;
$link = $mysqli;

// Validar conexión
if (mysqli_connect_errno()) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres utf8");
}

// Validar parámetros esenciales
if (empty($codigo)) {
    die("Error: Código presupuestario no proporcionado.");
}

require('fpdf/fpdf.php');

// Clase PDF mejorada con orientación horizontal
class PDF extends FPDF
{
    private $codigo;
    private $dependencia;
    
    function setDatos($codigo, $dependencia) {
        $this->codigo = $codigo;
        $this->dependencia = $dependencia;
    }
    
    function Header()
    {
        // Logo
        if (file_exists('img/fondo.png')) {
            $this->Image('img/fondo.png', 10, 8, 20);
        }
        
        // Título principal
        $this->SetFont('Arial','B',16);
        $this->Cell(0,12, utf8_decode('LISTADO DE ACTIVOS'),0,1,'C');
        
        // Información de la dependencia
        $this->SetFont('Arial','',11);
        $this->Cell(0,7, utf8_decode('Código Presupuestario: ') . utf8_decode($this->codigo),0,1,'C');
        $this->Cell(0,7, utf8_decode('Institución: ') . utf8_decode($this->dependencia),0,1,'C');
        
        // Fecha de generación
        $this->SetFont('Arial','I',9);
        $this->Cell(0,5, utf8_decode('Generado el: ') . date('d/m/Y H:i:s'),0,1,'C');
        
        // Espacio después del encabezado
        $this->Ln(8);
    }
    
function Footer()
{
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C'); // Punto y coma añadido
}
    
    // Función para crear tabla optimizada para landscape
    function ImprovedTable($header, $data)
    {
        // Anchuras de las columnas optimizadas para landscape
        $w = array(40, 30, 35, 25, 25, 40, 25);
        
        // Cabecera de la tabla
        $this->SetFillColor(59, 89, 152);
        $this->SetTextColor(255);
        $this->SetDrawColor(50, 50, 50);
        $this->SetLineWidth(0.3);
        $this->SetFont('Arial','B',9);
        
        for($i=0; $i<count($header); $i++) {
            $this->Cell($w[$i],8, $header[$i],1,0,'C',true);
        }
        $this->Ln();
        
        // Restaurar colores y fuente para los datos
        $this->SetFillColor(240, 245, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',8);
        
        // Datos
        $fill = false;
        foreach($data as $row) {
            // Verificar si necesita nueva página (considerando margen inferior)
            if($this->GetY() > 180) {
                $this->AddPage('L');
                // Volver a dibujar la cabecera de la tabla
                $this->SetFillColor(59, 89, 152);
                $this->SetTextColor(255);
                $this->SetFont('Arial','B',9);
                for($i=0; $i<count($header); $i++) {
                    $this->Cell($w[$i],8, $header[$i],1,0,'C',true);
                }
                $this->Ln();
                $this->SetFillColor(240, 245, 255);
                $this->SetTextColor(0);
                $this->SetFont('Arial','',8);
                $fill = false;
            }
            
            // Ajustar texto largo con función para truncar si es necesario
            $clase = (strlen($row['clase']) > 30) ? substr($row['clase'], 0, 27) . '...' : $row['clase'];
            $marca = (strlen($row['marca']) > 20) ? substr($row['marca'], 0, 17) . '...' : $row['marca'];
            $modelo = (strlen($row['modelo']) > 25) ? substr($row['modelo'], 0, 22) . '...' : $row['modelo'];
            $color = (strlen($row['color']) > 15) ? substr($row['color'], 0, 12) . '...' : $row['color'];
            $serial = (strlen($row['serial']) > 25) ? substr($row['serial'], 0, 22) . '...' : $row['serial'];
            
            $this->Cell($w[0],6, utf8_decode($clase),'LR',0,'L',$fill);
            $this->Cell($w[1],6, utf8_decode($marca),'LR',0,'L',$fill);
            $this->Cell($w[2],6, utf8_decode($modelo),'LR',0,'L',$fill);
            $this->Cell($w[3],6, utf8_decode($color),'LR',0,'L',$fill);
            $this->Cell($w[4],6, utf8_decode($row['placa']),'LR',0,'C',$fill);
            $this->Cell($w[5],6, utf8_decode($serial),'LR',0,'L',$fill);
            $this->Cell($w[6],6, utf8_decode($row['estado']),'LR',0,'C',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        
        // Línea de cierre
        $this->Cell(array_sum($w),0,'','T');
    }
}

// Crear PDF en orientación horizontal
$pdf = new PDF('L'); // 'L' para landscape (horizontal)
$pdf->setDatos($codigo, $dependencia);
$pdf->AliasNbPages();
$pdf->AddPage();

// Cabecera de la tabla
$header = array('Clase', 'Marca', 'Modelo', 'Color', 'Placa', 'Serial', 'Estado');

// Consulta preparada para mayor seguridad
if ($b_estado == 1 && !empty($estado)) {
    $query = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, 
                     Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado
              FROM t_activo Ta
              INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
              INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
              INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
              INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
              WHERE Tp.codigo = ? AND Tp.activo = ? AND Tp.id_estado = ?
              ORDER BY Tg.clase ASC";
    
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "sii", $codigo, $activado, $estado);
} else {
    $query = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, 
                     Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado
              FROM t_activo Ta
              INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
              INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
              INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
              INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
              WHERE Tp.codigo = ? AND Tp.activo = ?
              ORDER BY Tg.clase ASC";
    
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "si", $codigo, $activado);
}

// Ejecutar consulta
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Procesar resultados
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Determinar el texto del estado
    switch($row['id_estado']) {
        case 1: $estado_texto = 'Excelente'; break;
        case 2: $estado_texto = 'Bueno'; break;
        case 3: $estado_texto = 'Regular'; break;
        case 4: $estado_texto = 'Malo'; break;
        case 5: $estado_texto = 'Hurtado'; break;
        default: $estado_texto = 'Desconocido';
    }
    
    $data[] = array(
        'clase' => $row['clase'],
        'marca' => $row['marca'],
        'modelo' => $row['modelo'],
        'color' => $row['color'],
        'placa' => $row['placa'],
        'serial' => $row['serial'],
        'estado' => $estado_texto
    );
}

// Verificar si hay datos
if (empty($data)) {
    $pdf->SetFont('Arial','I',12);
    $pdf->Cell(0,10, utf8_decode('No se encontraron activos con los criterios seleccionados.'),0,1,'C');
} else {
    // Crear tabla
    $pdf->ImprovedTable($header, $data);
    
    // Agregar resumen
    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,10, utf8_decode('Total de activos listados: ') . count($data),0,1,'L');
    
    // Si se aplicó filtro por estado, mostrar información del filtro
    if ($b_estado == 1 && !empty($estado)) {
        $nombres_estado = array(1 => 'Excelente', 2 => 'Bueno', 3 => 'Regular', 4 => 'Malo', 5 => 'Hurtado');
        $pdf->Cell(0,7, utf8_decode('Filtro aplicado: Estado = ') . $nombres_estado[$estado],0,1,'L');
    }
}

// Cerrar conexión
mysqli_stmt_close($stmt);
mysqli_close($link);

// Generar PDF
$nombre_archivo = 'Reporte_Activos_' . date('Y-m-d_H-i') . '.pdf';
$pdf->Output('D', $nombre_archivo); // 'D' para descarga forzada
?>
