<?php

//ini_set('display_errors', false); 
require_once("conexion.php");
//require_once("./vendor/autoload.php");
//require __DIR__ . "/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

//include 'conexion.php';


$codigo=trim($_GET['codigop']);
$activado= 1;
$link=$mysqli;
$titulo= 'LISTA DE LICENCIAS POR DEPENDENCIA'



try {
    
   
    $documento = new Spreadsheet();        
    $nombreDelDocumento = "pntm.xlsx";

    $hoja = $documento->getActiveSheet();
    $hoja->setTitle("Tecnopresta");

    $titulo = "Ministerio de Educación Pública";                                  

    //Imagen Logo
    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('Mep');
    $drawing->setDescription('Mep');
    $drawing->setPath('./img/fondo.png');
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
    $hoja->getColumnDimension('A')->setWidth(30);
    $hoja->getColumnDimension('B')->setWidth(20);
    $hoja->getColumnDimension('C')->setWidth(20);
    $hoja->getColumnDimension('D')->setWidth(20);
    $hoja->getColumnDimension('E')->setWidth(20);
    $hoja->getColumnDimension('F')->setWidth(20);
   

    // Fila encabezado
    $hoja->setCellValueByColumnAndRow(1, 2, "Licencia");
    $hoja->setCellValueByColumnAndRow(2, 2, "Nombre");
    $hoja->setCellValueByColumnAndRow(3, 2, "Placa");
    $hoja->setCellValueByColumnAndRow(4, 2, "Serial");
    $hoja->setCellValueByColumnAndRow(5, 2, "Fecha Activ.");
    $hoja->setCellValueByColumnAndRow(6, 2, "Vigencia");
   

    $hoja->getStyle('A2:G2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('286ce6');
    $hoja->getStyle('A2:G2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
    $hoja->getStyle('A2:G2')->getAlignment()->setHorizontal('center');
    $hoja->getStyle('A2:G2')->getFont()->setBold(true);
                      
    if(!empty($codigo)){
    	
 		$consulta=mysqli_query($link,"SELECT  Ts.licencia, Ts.factivacion, Ts.vigencia, Tl.id_placa, Tsg.etiqueta, Tp.placa, Tp.serial, Tp.codigo
        FROM (t_software Ts INNER JOIN t_licencia Tl ON Ts.id_software = Tl.id_software) LEFT JOIN t_placa Tp ON Tl.id_placa = Tp.id_placa
        INNER JOIN t_software_general Tsg ON Ts.id_sg = Tsg.id_sg
        WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'
		  ORDER BY Ts.factivacion ASC") or die(mysqli_error($link));

     	
	   	  	
     }

     }
     
      $fila=3;
      $filaInicio=$fila; 
      
      while ($row = mysqli_fetch_array($consulta)) {
        						
       
          $hoja->setCellValueByColumnAndRow(1, $fila, $row['licencia']);
          $hoja->setCellValueByColumnAndRow(2, $fila, $row['etiqueta']);
          $hoja->setCellValueByColumnAndRow(3, $fila, $row['placa']);
          $hoja->setCellValueByColumnAndRow(4, $fila, $row['serial']);
          $hoja->setCellValueByColumnAndRow(5, $fila, date("d-m-Y",strtotime($row['factivacion']));
          $hoja->setCellValueByColumnAndRow(6, $fila, $row['vigencia']);
          
          
         
          
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
} 
catch (Exception $e) {		
	console.log("Error de la aplicación: " + $e->getMessage());
	echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
    $db = null;
    die("error");
}

?>
