<?php

ini_set('display_errors', false); 
require_once("conexion.php");
require_once("../vendor/autoload.php");
//require __DIR__ . "/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$prestamo_fechaDevolucion = "";
$prestamo_fechaRetiro = "";
$prestamo_uso = "";
$seccion_Descripcion = "";
$software_Descripcion = "";
$nombre = "";
$prestamo_detalle_observacion = "";

try {
    
    $documento = new Spreadsheet();        
    $nombreDelDocumento = "pntm.xlsx";

    $hoja = $documento->getActiveSheet();
    $hoja->setTitle("Nombre Regional");

    $titulo = "Ministerio de Educación Pública";                                  

    //Imagen Logo
    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('Mep');
    $drawing->setDescription('Mep');
    $drawing->setPath('../img/fondo.png');
    $drawing->setCoordinates('A1');
    $drawing->setWidthAndHeight(210, 210);
    $drawing->setWorksheet($documento->getActiveSheet());

    // Titulo
    $hoja->setCellValueByColumnAndRow(1, 1, $titulo);
    $hoja->getStyle('A1')->getFont()->setBold(true);
    $hoja->getRowDimension(1)->setRowHeight(100);;
    $hoja->getStyle('A1:G1')->getAlignment()->setWrapText(true); 
    $hoja->mergeCells('A1:G1');
    $hoja->getStyle('A1:G1')->getAlignment()->setHorizontal('center');
            
  //ancho de columnas
    $hoja->getColumnDimension('A')->setWidth(20);
    $hoja->getColumnDimension('B')->setWidth(20);
    $hoja->getColumnDimension('C')->setWidth(20);
    $hoja->getColumnDimension('D')->setWidth(20);
    $hoja->getColumnDimension('E')->setWidth(20);
    $hoja->getColumnDimension('F')->setWidth(20);
    $hoja->getColumnDimension('G')->setWidth(30);

    // Fila encabezado
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Devolución");
    $hoja->setCellValueByColumnAndRow(2, 2, "Fecha de Retiro");
    $hoja->setCellValueByColumnAndRow(3, 2, "Uso");
    $hoja->setCellValueByColumnAndRow(4, 2, "Sección");
    $hoja->setCellValueByColumnAndRow(5, 2, "Software");
    $hoja->setCellValueByColumnAndRow(6, 2, "Nombre");
    $hoja->setCellValueByColumnAndRow(7, 2, "Observación");

    $hoja->getStyle('A2:G2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('286ce6');
    $hoja->getStyle('A2:G2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
    $hoja->getStyle('A2:G2')->getAlignment()->setHorizontal('center');
    $hoja->getStyle('A2:G2')->getFont()->setBold(true);
                      
    $consultaSQL = "SELECT prestamo_fechaDevolucion, prestamo_fechaRetiro,prestamo_uso, 
                    seccion_Descripcion, software_Descripcion, nombre, prestamo_detalle_observacion
                    FROM
                    t_prestamo 
                    INNER JOIN t_prestamo_detalle
                    ON t_prestamo.prestamo_Id=t_prestamo_detalle.prestamo_Id
                    INNER JOIN t_seccion
                    ON t_prestamo.seccion_Id = t_seccion.seccion_Id
                    INNER JOIN t_softwareEducativo
                    ON t_prestamo.software_Id = t_softwareEducativo.software_Id
                    INNER JOIN t_activo
                    ON t_prestamo_detalle.prestamo_detalle_id_activo = t_activo.id_activo";
      
      $pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
      $sql = $pdo->query($consultaSQL);

      $rs = [];
      while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
        $rs[] = [						
          'prestamo_fechaDevolucion' => $row['prestamo_fechaDevolucion'],	                
          'prestamo_fechaRetiro' => $row['prestamo_fechaRetiro'],
          'prestamo_uso' => $row['prestamo_uso'],
          'seccion_Descripcion' => $row['seccion_Descripcion'],
          'software_Descripcion' => $row['software_Descripcion'],
          'nombre' => $row['nombre'],
          'prestamo_detalle_observacion' => $row['prestamo_detalle_observacion']																		
        ];	
      }

      $fila=3;
      $filaInicio=$fila;

      foreach($rs as $rsExportar) {
        
          $prestamo_fechaDevolucion = $rsExportar["prestamo_fechaDevolucion"];
          $prestamo_fechaRetiro = $rsExportar["prestamo_fechaRetiro"];
          $prestamo_uso = $rsExportar["prestamo_uso"];
          $seccion_Descripcion = $rsExportar["seccion_Descripcion"];
          $software_Descripcion = $rsExportar["software_Descripcion"];
          $nombre = $rsExportar["nombre"];
          $prestamo_detalle_observacion = $rsExportar["prestamo_detalle_observacion"];       
          
          $hoja->setCellValueByColumnAndRow(1, $fila, $prestamo_fechaDevolucion);
          $hoja->setCellValueByColumnAndRow(2, $fila, $prestamo_fechaRetiro);
          $hoja->setCellValueByColumnAndRow(3, $fila, $prestamo_uso);
          $hoja->setCellValueByColumnAndRow(4, $fila, $seccion_Descripcion);
          $hoja->setCellValueByColumnAndRow(5, $fila, $software_Descripcion);
          $hoja->setCellValueByColumnAndRow(6, $fila, $nombre);
          $hoja->setCellValueByColumnAndRow(7, $fila, $prestamo_detalle_observacion); 
          
          $fila=$fila+1;
        
      }
              
      $filaFin=$fila-1;
                  
      $hoja->getStyle('A'.$fila.':G'.$fila)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('afc9f6');      

      $documento->createSheet();        
    
      $pdo = null;

      $documento->setActiveSheetIndex(0);

      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
      header('Cache-Control: max-age=0');
 
      $writer = IOFactory::createWriter($documento, 'Xlsx');      
      $writer->save('php://output');

      exit;
    
} 
catch (Exception $e) {		
	console.log("Error de la aplicación: " + $e->getMessage());
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
  $db = null;
  die("error");
}

?>
