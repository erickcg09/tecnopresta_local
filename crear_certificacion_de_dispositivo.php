<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}

require_once("conexion.php");

// Libreria FPDF
require_once('fpdf/fpdf.php'); // Crear un pdf

$link = $mysqli;
if (mysqli_connect_errno()) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
}

// Obtener parámetros
$id_fondos = $_POST['fondos'] ?? 0;
$codigo_centro = trim($_POST['codigo_centro'] ?? '');
$nombre_archivo_input = trim($_POST['nombre_archivo'] ?? ''); // Nuevo campo

// Función para sanitizar nombre de archivo
function sanitizarNombreArchivo($nombre) {
    // Eliminar caracteres no seguros para nombres de archivo
    $nombre = preg_replace('/[^\w\sáéíóúÁÉÍÓÚñÑ\-]/', '', $nombre);
    // Reemplazar espacios por guiones bajos
    $nombre = preg_replace('/\s+/', '_', $nombre);
    // Limitar longitud
    $nombre = substr($nombre, 0, 100);
    return $nombre;
}

// Si no se proporciona nombre, usar uno por defecto
if (empty($nombre_archivo_input)) {
    $nombre_base = 'Certificacion_' . $codigo_centro . '_' . date('Y-m-d');
} else {
    // Sanitizar y usar el nombre proporcionado por el usuario
    $nombre_base = sanitizarNombreArchivo($nombre_archivo_input);
}

if (!$id_fondos || !$codigo_centro) {
    echo '<script language="javascript">
    alert("Debe seleccionar un fondo e ingresar un código de centro");
    window.history.back();
    </script>';
    exit();
}

// Obtener información de la institución
$query_institucion = "SELECT * FROM t_instituciones WHERE codigo = ?";
$stmt_inst = $link->prepare($query_institucion);
$stmt_inst->bind_param("s", $codigo_centro);
$stmt_inst->execute();
$result_inst = $stmt_inst->get_result();
$institucion = $result_inst->fetch_assoc();
$stmt_inst->close();

// Obtener información del fondo
$query_fondo = "SELECT * FROM t_fondos WHERE id_fondos = ?";
$stmt_fondo = $link->prepare($query_fondo);
$stmt_fondo->bind_param("i", $id_fondos);
$stmt_fondo->execute();
$result_fondo = $stmt_fondo->get_result();
$fondo = $result_fondo->fetch_assoc();
$stmt_fondo->close();

// Obtener los activos según los parámetros
$consultaSQL = "SELECT fondos, t_placa.codigo AS codigo, institucion, placa, serial AS serie, clase, modelo, 
                marca, estado, enuso, donar, lugar, t_estado.id_estado
                FROM t_activo 
                INNER JOIN t_activo_general ON t_activo_general.id_ag = t_activo.id_ag 
                INNER JOIN t_marca ON t_activo.id_marca = t_marca.id_marca 
                INNER JOIN t_placa ON t_placa.id_activo = t_activo.id_activo 
                INNER JOIN t_lugar ON t_placa.id_lugar = t_lugar.id_lugar 
                INNER JOIN t_estado ON t_placa.id_estado = t_estado.id_estado 
                INNER JOIN t_fondos ON t_placa.id_fondos = t_fondos.id_fondos 
                INNER JOIN t_instituciones ON t_instituciones.codigo = t_placa.codigo 
                WHERE t_placa.id_fondos = ? AND t_placa.codigo = ? 
                ORDER BY t_placa.codigo, placa, clase, marca ASC";

$stmt = $link->prepare($consultaSQL);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $link->error);
}

$stmt->bind_param("is", $id_fondos, $codigo_centro);
$stmt->execute();
$result = $stmt->get_result();

// Función para convertir ID de estado a texto
function estadoATexto($id_estado) {
    switch($id_estado) {
        case 1: return 'MUY BUENO';
        case 2: return 'BUENO';
        case 3: return 'REGULAR';
        case 4: return 'MALO';
        case 5: return 'ROBADO O HURTADO';
        default: return 'DESCONOCIDO';
    }
}

// Función para convertir 1/0 a Sí/No
function siNoATexto($valor) {
    $valor = intval($valor);
    return ($valor == 1) ? 'Sí' : 'No';
}

// Función para convertir texto a formato compatible con FPDF
function convertirTexto($texto) {
    if (!is_string($texto)) {
        return $texto;
    }
    
    // Decodificar UTF-8 a ISO-8859-1 (soporte básico para caracteres españoles)
    $texto = utf8_decode($texto);
    
    // Si utf8_decode no funciona (devuelve vacío para caracteres no válidos)
    if ($texto === false || trim($texto) === '') {
        // Intentar con iconv
        $texto = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $texto);
        if ($texto === false) {
            return ''; // Si todo falla, devolver vacío
        }
    }
    
    // Reemplazar caracteres problemáticos comunes
    $reemplazos = array(
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'ñ' => 'n', 'Ñ' => 'N',
        'ü' => 'u', 'Ü' => 'U',
        '¿' => '?', '¡' => '!',
        '€' => 'EUR', '«' => '"', '»' => '"',
        'º' => '°', 'ª' => 'a'
    );
    
    return strtr($texto, $reemplazos);
}

// Calcular totales
$total_muy_bueno = 0;
$total_bueno = 0;
$total_regular = 0;
$total_malo = 0;
$total_robado = 0;
$total_utilizado = 0;
$total_donados = 0;
$total_general = 0;

$activos = [];
while ($row = $result->fetch_assoc()) {
    // Convertir valores numéricos a texto
    $row['estado_texto'] = estadoATexto($row['id_estado']);
    $row['enuso_texto'] = siNoATexto($row['enuso']);
    $row['donar_texto'] = siNoATexto($row['donar']);
    
    $activos[] = $row;
    
    // Contar por estado usando id_estado
    switch ($row['id_estado']) {
        case 1:
            $total_muy_bueno++;
            break;
        case 2:
            $total_bueno++;
            break;
        case 3:
            $total_regular++;
            break;
        case 4:
            $total_malo++;
            break;
        case 5:
            $total_robado++;
            break;
    }
    
    // Contar utilizados (asegurar que sea numérico)
    $enuso = intval($row['enuso']);
    if ($enuso == 1) {
        $total_utilizado++;
    }
    
    // Contar donables (asegurar que sea numérico)
    $donar = intval($row['donar']);
    if ($donar == 1) {
        $total_donados++;
    }
    
    $total_general++;
}

$stmt->close();

// Si no hay activos, mostrar mensaje
if ($total_general == 0) {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sin resultados</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-warning">
                <h4>No se encontraron activos para los criterios seleccionados</h4>
                <p>Fondo: ' . htmlspecialchars($fondo['fondos'] ?? 'Desconocido') . '</p>
                <p>Código de centro: ' . htmlspecialchars($codigo_centro) . '</p>
                <p>Nombre del archivo solicitado: ' . htmlspecialchars($nombre_archivo_input) . '</p>
                <a href="herramienta_generar_certificacion.php" class="btn btn-primary mt-3">Volver a generar certificación</a>
            </div>
        </div>
    </body>
    </html>';
    exit();
}

// Crear PDF con orientación vertical (Portrait) y tamaño Carta
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Logo
        if (file_exists('img/logodelgobierno.png')) {
            $this->Image('img/logodelgobierno.png', 10, 8, 20);
        }
        
        // Título
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, convertirTexto('CERTIFICACIÓN DE DISPOSITIVOS TECNOLÓGICOS'), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, convertirTexto('Ministerio de Educación Pública'), 0, 1, 'C');
        $this->Cell(0, 5, convertirTexto('Dirección de Recursos Humanos'), 0, 1, 'C');
        $this->Ln(8);
    }
    
    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    // Método para escribir texto con conversión UTF-8
    function CellUTF($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = 'L', $fill = false)
    {
        parent::Cell($w, $h, convertirTexto($txt), $border, $ln, $align, $fill);
    }
    
    // Método para MultiCell con conversión UTF-8
    function MultiCellUTF($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
    {
        parent::MultiCell($w, $h, convertirTexto($txt), $border, $align, $fill);
    }
    
    // Tabla de totales ajustada para vertical
    function TablaTotales($muybueno, $bueno, $regular, $malo, $robado, $total, $utilizado, $donados)
    {
        $this->SetFont('Arial', 'B', 9);
        
        // Primera fila: encabezados
        $this->Cell(24, 8, 'Muy Bueno', 1, 0, 'C');
        $this->Cell(24, 8, 'Bueno', 1, 0, 'C');
        $this->Cell(24, 8, 'Regular', 1, 0, 'C');
        $this->Cell(24, 8, 'Malo', 1, 0, 'C');
        $this->Cell(30, 8, 'Robado', 1, 1, 'C');
        
        // Segunda fila: valores
        $this->SetFont('Arial', '', 9);
        $this->Cell(24, 8, $muybueno, 1, 0, 'C');
        $this->Cell(24, 8, $bueno, 1, 0, 'C');
        $this->Cell(24, 8, $regular, 1, 0, 'C');
        $this->Cell(24, 8, $malo, 1, 0, 'C');
        $this->Cell(30, 8, $robado, 1, 1, 'C');
        
        // Tercera fila: encabezados
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(36, 8, 'Total General', 1, 0, 'C');
        $this->Cell(36, 8, 'En Uso', 1, 0, 'C');
        $this->Cell(36, 8, 'Donables', 1, 1, 'C');
        
        // Cuarta fila: valores
        $this->SetFont('Arial', '', 9);
        $this->Cell(36, 8, $total, 1, 0, 'C');
        $this->Cell(36, 8, $utilizado, 1, 0, 'C');
        $this->Cell(36, 8, $donados, 1, 1, 'C');
        
        $this->Ln(10);
    }
    
    // Tabla de detalles para vertical
    function TablaDetalles($activos)
    {
        // Usar una tabla más simple para vertical
        $this->SetFont('Arial', 'B', 8);
        
        // Encabezados ajustados
        $this->Cell(25, 8, 'Placa', 1, 0, 'C');
        $this->Cell(40, 8, 'Artículo', 1, 0, 'C');
        $this->Cell(40, 8, 'Marca/Modelo', 1, 0, 'C');
        $this->Cell(30, 8, 'Estado', 1, 0, 'C');
        $this->Cell(20, 8, 'En Uso', 1, 0, 'C');
        $this->Cell(25, 8, 'Donable', 1, 1, 'C');
        
        $this->SetFont('Arial', '', 8);
        
        foreach ($activos as $activo) {
            // Controlar saltos de página
            if ($this->GetY() > 250) {
                $this->AddPage();
                // Volver a poner encabezado
                $this->SetFont('Arial', 'B', 8);
                $this->Cell(25, 8, 'Placa', 1, 0, 'C');
                $this->Cell(40, 8, 'Artículo', 1, 0, 'C');
                $this->Cell(40, 8, 'Marca/Modelo', 1, 0, 'C');
                $this->Cell(30, 8, 'Estado', 1, 0, 'C');
                $this->Cell(20, 8, 'En Uso', 1, 0, 'C');
                $this->Cell(25, 8, 'Donable', 1, 1, 'C');
                $this->SetFont('Arial', '', 8);
            }
            
            $this->CellUTF(25, 8, $activo['placa'], 1, 0, 'C');
            $this->CellUTF(40, 8, substr($activo['clase'], 0, 25), 1, 0, 'L');
            
            // Combinar marca y modelo
            $marca_modelo = $activo['marca'];
            if (!empty($activo['modelo'])) {
                $marca_modelo .= ' / ' . $activo['modelo'];
            }
            $this->CellUTF(40, 8, substr($marca_modelo, 0, 25), 1, 0, 'L');
            $this->CellUTF(30, 8, $activo['estado_texto'], 1, 0, 'C');
            $this->CellUTF(20, 8, $activo['enuso_texto'], 1, 0, 'C');
            $this->CellUTF(25, 8, $activo['donar_texto'], 1, 1, 'C');
        }
    }
}

// Crear instancia de PDF en vertical (Portrait) y tamaño Carta
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();

// Texto de certificación
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCellUTF(0, 5, 'Yo ___________________________________________ , número de cédula o 
identificación _______________________ , en mi 
calidad de director(a) del centro educativo ___________________________________________ , 
código SABER  _______________________ , código presupuestario 
_______________________ , DRE ___________________________________________ , 
certifico amparado en el artículo 08 , inciso a de la ley 8292,  la información brindada a 
continuación:');

$pdf->Ln(5);

// Mostrar información si existe la institución
if ($institucion) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->CellUTF(0, 6, 'Información de la Institución:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->CellUTF(0, 6, 'Nombre: ' . $institucion['institucion'], 0, 1);
    $pdf->CellUTF(0, 6, 'Código: ' . $institucion['codigo'], 0, 1);
    if (!empty($institucion['cod_saber'])) {
        $pdf->CellUTF(0, 6, 'Código SABER: ' . $institucion['cod_saber'], 0, 1);
    }
    $pdf->Ln(5);
} else {
    // Si no existe la institución, mostrar espacios para que se complete a mano
    $pdf->SetFont('Arial', '', 10);
    $pdf->CellUTF(0, 6, 'Nombre de la Institución: ___________________________________________', 0, 1);
    $pdf->CellUTF(0, 6, 'Código: _______________________', 0, 1);
    $pdf->CellUTF(0, 6, 'Código SABER: _______________________', 0, 1);
    $pdf->Ln(5);
}

// Mostrar información del fondo
if ($fondo) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->CellUTF(0, 6, 'Fuente de Fondos:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->CellUTF(0, 6, $fondo['fondos'], 0, 1);
    $pdf->Ln(5);
}

// Tabla de totales
$pdf->SetFont('Arial', 'B', 12);
$pdf->CellUTF(0, 8, 'RESUMEN DE EQUIPOS', 0, 1, 'C');
$pdf->TablaTotales(
    $total_muy_bueno,
    $total_bueno,
    $total_regular,
    $total_malo,
    $total_robado,
    $total_general,
    $total_utilizado,
    $total_donados
);

// Tabla de detalles
$pdf->SetFont('Arial', 'B', 12);
$pdf->CellUTF(0, 8, 'LISTADO DETALLADO DE EQUIPOS', 0, 1, 'C');
$pdf->Ln(2);
$pdf->SetFont('Arial', 'I', 9);
$pdf->MultiCellUTF(0, 4, 'Nota: Para más detalles como número de serie y ubicación específica, consulte el sistema de inventario.');
$pdf->Ln(2);
$pdf->TablaDetalles($activos);

// Firma y fecha
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 11);
$pdf->CellUTF(0, 6, 'Firma del Director(a): ___________________________________________', 0, 1);
$pdf->CellUTF(0, 6, 'Nombre: ___________________________________________', 0, 1);
$pdf->CellUTF(0, 6, 'Cédula: ___________________________________________', 0, 1);
$pdf->CellUTF(0, 6, 'Fecha: _______________________', 0, 1);
$pdf->Ln(5);

// Nota final
$pdf->SetFont('Arial', 'I', 9);
$pdf->MultiCellUTF(0, 5, 'Nota: Esta certificación debe ser entregada firmada y sellada por el Director(a) del centro educativo a la Dirección de Recursos Humanos del MEP.');

// Definir nombre del archivo final
$nombre_archivo = $nombre_base . '.pdf';

// Enviar headers para descarga
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Generar PDF en buffer de salida
$pdf_content = $pdf->Output('S', $nombre_archivo);

// Enviar contenido del PDF
echo $pdf_content;

// Redireccionar después de la descarga usando JavaScript
echo '<script>
    // Mostrar mensaje de éxito
    alert("Archivo \'' . htmlspecialchars($nombre_archivo) . '\' descargado exitosamente. Será redirigido en 2 segundos.");
    
    // Esperar 2 segundos para asegurar que la descarga comience
    setTimeout(function() {
        window.location.href = "herramienta_generar_certificacion.php";
    }, 2000);
</script>';

// También podemos incluir un enlace por si JavaScript no funciona
echo '<noscript>
    <div style="padding: 20px; text-align: center;">
        <h3>¡Archivo descargado!</h3>
        <p>El archivo "' . htmlspecialchars($nombre_archivo) . '" se ha descargado correctamente.</p>
        <p><a href="herramienta_generar_certificacion.php" style="color: blue; text-decoration: underline;">Haga clic aquí para regresar y generar otra certificación</a></p>
    </div>
</noscript>';

exit();
?>