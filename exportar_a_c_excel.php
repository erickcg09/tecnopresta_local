<?php

//ini_set('display_errors', false); 
require_once("conexion.php");
require_once("./vendor/autoload.php");
//require __DIR__ . "/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

//include 'conexion.php';


$codigo=trim($_GET['codigop']);
$estado= trim($_GET['estadop']);
$b_estado= trim($_GET['b_estadop']);  
$dependencia= trim($_GET['dependenciap']); 
$activado= 1;
$link=$mysqli;
$titulo="Lista de Activos";



$i_clase = "";
$i_marca = "";
$i_modelo = "";
$i_color = "";
$i_placa = "";
$i_serial = "";
$i_estado = "";
 


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
    $hoja->getColumnDimension('A')->setWidth(20);
    $hoja->getColumnDimension('B')->setWidth(20);
    $hoja->getColumnDimension('C')->setWidth(20);
    $hoja->getColumnDimension('D')->setWidth(20);
    $hoja->getColumnDimension('E')->setWidth(20);
    $hoja->getColumnDimension('F')->setWidth(20);
    $hoja->getColumnDimension('G')->setWidth(30);

    // Fila encabezado
    $hoja->setCellValueByColumnAndRow(1, 2, "Clase");
    $hoja->setCellValueByColumnAndRow(2, 2, "Marca");
    $hoja->setCellValueByColumnAndRow(3, 2, "Modelo");
    $hoja->setCellValueByColumnAndRow(4, 2, "Color");
    $hoja->setCellValueByColumnAndRow(5, 2, "Placa");
    $hoja->setCellValueByColumnAndRow(6, 2, "Serial");
    $hoja->setCellValueByColumnAndRow(7, 2, "Estado");

    $hoja->getStyle('A2:G2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('286ce6');
    $hoja->getStyle('A2:G2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
    $hoja->getStyle('A2:G2')->getAlignment()->setHorizontal('center');
    $hoja->getStyle('A2:G2')->getFont()->setBold(true);
                      
    if(!empty($codigo)){
    	
 		if(($b_estado==1)) {
   		
			
    		$consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
		 	FROM t_activo Ta
		 	INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 	INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 	INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 	INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 	WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'AND Tp.id_estado = '".$estado."'
		 	ORDER BY Tg.clase ASC") or die(mysqli_error($link));
		 	
		 	
	   	  	
     }else{
         
 
     $consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
		 FROM t_activo Ta
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'
		 ORDER BY Tg.clase ASC") or die(mysqli_error($link));

     }
     
       $fila=3;
      $filaInicio=$fila; 
      
      while ($row = mysqli_fetch_array($consulta)) {
        						
       
          $hoja->setCellValueByColumnAndRow(1, $fila, $row['clase']);
          $hoja->setCellValueByColumnAndRow(2, $fila, $row['marca']);
          $hoja->setCellValueByColumnAndRow(3, $fila, $row['color']);
          $hoja->setCellValueByColumnAndRow(4, $fila, $row['modelo']);
          $hoja->setCellValueByColumnAndRow(5, $fila, $row['placa']);
          $hoja->setCellValueByColumnAndRow(6, $fila, $row['serial']);
          
          
          $hoja->setCellValueByColumnAndRow(7, $fila, $row['estado']); 
          
          $fila=$fila+1;
        
        	
      }
    

              
      $filaFin=$fila-1;
                
      $hoja->getStyle('A'.$fila.':G'.$fila)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('afc9f6');   
     
      

      $documento->createSheet();        
    
     // $pdo = null;

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
