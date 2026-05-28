<?php
ini_set('display_errors', true); 
require 'vendor/autoload.php';
require_once 'sql/conexion.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

//Fechas
$fechaDesde = $_POST['fechaDesde'];
$fechaHasta = $_POST['fechaHasta'];
date_default_timezone_set('America/Costa_Rica');
$fechaDesdeYMD = date_create($fechaDesde)->format('Y-m-d');
$fechaHastaYMD = date_create($fechaHasta)->format('Y-m-d');

//Estado
$arrayEstado = array();
$arrayEstado = $_POST['cboEstado'];
if(!empty($arrayEstado)) {
    $estadoDescripcion="";
    $totalRegistros = count($arrayEstado); 
    $i=0;
    foreach($arrayEstado as $key => $estados) {
        $i= $i+1;
        if ($i<$totalRegistros) {
          $estadoDescripcion = $estadoDescripcion . "'".$estados."'" . ",";
        } else {
          $estadoDescripcion = $estadoDescripcion . "'".$estados."'";
        }           
    }
} else {
  exit;
}

//Tema o Asunto
$arrayTema= array();
$arrayTema = $_POST['cboTemaoAsunto'];
if(!empty($arrayTema)) {
    $temaDescripcion="";
    $totalRegistros = count($arrayTema); 
    $i=0;
    foreach($arrayTema as $keytemas => $temas) {
        $i= $i+1;
        if ($i<$totalRegistros) {
          $temaDescripcion = $temaDescripcion . "'".$temas."'" . ",";
        } else {
          $temaDescripcion = $temaDescripcion . "'".$temas."'";
        }           
    }
} else {
  exit;
}

//Fondos
$arrayFondos= array();
$arrayFondos = $_POST['cboFondos'];
if(!empty($arrayFondos)) {
    $fondosDescripcion = implode(",",$arrayFondos);    
} else {
  exit;
}

try {
  
  $pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
  
  $consultaSQL = "SELECT * FROM soporte INNER JOIN t_fondos
                    ON soporte.id_fondos = t_fondos.id_fondos
                    WHERE estatus IN ($estadoDescripcion)
                    AND placa IN ($temaDescripcion)
                    AND soporte.id_fondos IN ($fondosDescripcion)
                    AND DATE(fecha) BETWEEN '".$fechaDesdeYMD."' AND '".$fechaHastaYMD."'
                    ORDER BY nombretecnico, estatus, placa, soporte.id_fondos, fecha";

  $sql = $pdo->query($consultaSQL);
  $rs = [];
  while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
    $rs[] = [
      'cedulatecnico' => $row['cedulatecnico'],
      'nombretecnico' => $row['nombretecnico'],
      'estatus' => $row['estatus'],
      'placa' => $row['placa'],
      'fondos' => $row['fondos'],
      'fecha' => date_create($row['fecha'])->format('d-m-Y'),
      'problema' => $row['problema'],
      'dre' => $row['dre'],
      'circuito' => $row['circuito'],
      'institucion' => $row['institucion'],
      'correo' => $row['correo'],
      'codigo' => $row['codigo'],
      'funcionario' => $row['funcionario']
    ];
  }
    
} catch (\Throwable $th) {
    throw $th;
}

if (empty($rs)) {
  echo "<script>alert('No hay datos para mostrar');
          window.location.href='https://tecnopresta.mep.go.cr/formulario_reporte_centro_de_consultas.html'
        </script>";    
  die();
}

$fecha = date_create('now')->format('m-d-Y');
$nombreDelDocumento = "Centro de Consultas DRTE-".$fecha.".xlsx";
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->setTitle("Centro Consultas");
$titulo = "REPORTE CENTRO DE CONSULTAS DRTE";
// Titulo
$worksheet->setCellValueByColumnAndRow(1, 1, $titulo);
$worksheet->getStyle('A1')->getFont()->setBold(true);
$worksheet->getRowDimension(1)->setRowHeight(30);;
$worksheet->getStyle('A1:M1')->getAlignment()->setWrapText(true); 
$worksheet->mergeCells('A1:M1');
$worksheet->getStyle('A1:M1')->getAlignment()->setHorizontal('center');
$worksheet->getStyle('A1:M1')->getAlignment()->setVertical('center');

$worksheet->getColumnDimension('A')->setWidth(12);
$worksheet->getColumnDimension('B')->setWidth(30);
$worksheet->getColumnDimension('C')->setWidth(15);
$worksheet->getColumnDimension('D')->setWidth(15);
$worksheet->getColumnDimension('E')->setWidth(35);
$worksheet->getColumnDimension('F')->setWidth(15);
$worksheet->getColumnDimension('G')->setWidth(35);
$worksheet->getColumnDimension('H')->setWidth(20);
$worksheet->getColumnDimension('I')->setWidth(15);
$worksheet->getColumnDimension('J')->setWidth(35);
$worksheet->getColumnDimension('K')->setWidth(40);
$worksheet->getColumnDimension('L')->setWidth(10);
$worksheet->getColumnDimension('M')->setWidth(35);

$worksheet->setCellValueByColumnAndRow(1, 2, "Cédula del soportista");
$worksheet->getStyle('A2')->getAlignment()->setWrapText(true);
$worksheet->setCellValueByColumnAndRow(2, 2, "Nombre del soportista");
$worksheet->setCellValueByColumnAndRow(3, 2, "Estado");
$worksheet->setCellValueByColumnAndRow(4, 2, "Tema o Asunto");
$worksheet->setCellValueByColumnAndRow(5, 2, "Fondo presupuestario");
$worksheet->setCellValueByColumnAndRow(6, 2, "Fecha");
$worksheet->setCellValueByColumnAndRow(7, 2, "Problema");
$worksheet->setCellValueByColumnAndRow(8, 2, "Dirección Regional");
$worksheet->setCellValueByColumnAndRow(9, 2, "Circuito");
$worksheet->setCellValueByColumnAndRow(10, 2, "Institución");
$worksheet->setCellValueByColumnAndRow(11, 2, "Correo");
$worksheet->setCellValueByColumnAndRow(12, 2, "Código");
$worksheet->setCellValueByColumnAndRow(13, 2, "Funcionario");

$worksheet->getStyle('A2:M2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('286ce6');
$worksheet->getStyle('A2:M2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
$worksheet->getStyle('A2:M2')->getAlignment()->setHorizontal('center');
$worksheet->getStyle('A2:M2')->getFont()->setBold(true);

$fila=3;
$filaInicio=$fila;
foreach($rs as $rsSoporte) {  
  $worksheet->setCellValueByColumnAndRow(1, $fila, $rsSoporte["cedulatecnico"]);
  $worksheet->setCellValueByColumnAndRow(2, $fila, $rsSoporte["nombretecnico"]);
  $worksheet->setCellValueByColumnAndRow(3, $fila, $rsSoporte["estatus"]);
  $worksheet->setCellValueByColumnAndRow(4, $fila, $rsSoporte["placa"]);
  $worksheet->setCellValueByColumnAndRow(5, $fila, $rsSoporte["fondos"]);
  $worksheet->setCellValueByColumnAndRow(6, $fila, $rsSoporte["fecha"]);
  $worksheet->setCellValueByColumnAndRow(7, $fila, $rsSoporte["problema"]);
  $worksheet->setCellValueByColumnAndRow(8, $fila, $rsSoporte["dre"]);
  $worksheet->setCellValueByColumnAndRow(9, $fila, $rsSoporte["circuito"]);
  $worksheet->setCellValueByColumnAndRow(10, $fila, $rsSoporte["institucion"]);
  $worksheet->setCellValueByColumnAndRow(11, $fila, $rsSoporte["correo"]);
  $worksheet->setCellValueByColumnAndRow(12, $fila, $rsSoporte["codigo"]);
  $worksheet->setCellValueByColumnAndRow(13, $fila, $rsSoporte["funcionario"]);
  $fila=$fila+1;
}
$filaFin=$fila-1;

$worksheet->getStyle('B'.$filaInicio.':B'.$filaFin)->getAlignment()->setWrapText(true);
$worksheet->getStyle('G'.$filaInicio.':G'.$filaFin)->getAlignment()->setWrapText(true);
$worksheet->getStyle('H'.$filaInicio.':H'.$filaFin)->getAlignment()->setWrapText(true);
$worksheet->getStyle('J'.$filaInicio.':J'.$filaFin)->getAlignment()->setWrapText(true);
$worksheet->getStyle('M'.$filaInicio.':M'.$filaFin)->getAlignment()->setWrapText(true);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'. $nombreDelDocumento . '"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
$writer->save('php://output');
$spreadsheet->disconnectWorksheets();
unset($spreadsheet);
exit;

?>