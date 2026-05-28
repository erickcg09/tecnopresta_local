<?php

include 'conexion.php';


$codigo=trim($_GET['codigop']);
//$b_dependencia= trim($_GET['dependencia_p']); 
$fuente= trim($_GET['fuentep']);
$activado= 1;
$link=$mysqli;



require('fpdf/fpdf.php');
 
// Plantilla	
	class PDF extends FPDF
	{
		function Header()
		{
			$this->Image('img/fondo.png', 5, 5, 30 ); //Margen- margen - tamaño
			$this->SetFont('Arial','B',14);
			$this->Cell(5); //Desplaza a la derecha
			$this->Cell(200,10, utf8_decode('ACTIVOS POR DEPENDENCIA Y FUENTE DE FINANCIAMIENTO'),0,0,'C'); //Tamaño de la celda
			$this->Ln(10); //Salto de linea
			$this->SetFont('Arial','',10);
			$this->Cell(5);
			$this->Cell(200,10,utf8_decode('CÓDIGO PRESUPUESTARIO: ').trim($_GET['codigop']),0,0,'C');
			$this->Ln(10); //Salto de linea
			//$this->SetFont('Arial','',12);
			//$this->Cell(20);
			//$this->Cell(100,10,utf8_decode('Institución: ').trim($_GET['dependenciap']),0,0,'C');
			//$this->Ln(10); //Salto de linea
			
			$this->SetFont('Arial','',10);
			$this->Cell(10);
			$this->Cell(200,10,utf8_decode('FUENTE DE FINANCIAMIENTO: ').utf8_decode(trim($_GET['fuente_n'])),0,0,'C');
			$this->Ln(10); //Salto de linea
			
		}
		
		function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial','I', 8);
			$this->Cell(0,10, utf8_decode('Página ').$this->PageNo().'/{nb}. Fecha '.date('d-m-Y') ,0,0,'C' );
		
		}		
	}
    // Reporte
    
   $pdf = new PDF();
	$pdf->AliasNbPages();//pie de página se repita
	$pdf->AddPage();
	
	$pdf->SetFillColor(232,232,232);
	$pdf->SetFont('Arial','B',7);
	
	$pdf->Cell(30,6,utf8_decode('Clase'),1,0,'C',1);
	$pdf->Cell(30,6,utf8_decode('Marca'),1,0,'C',1);
	$pdf->Cell(30,6,utf8_decode('Modelo'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Color'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Placa'),1,0,'C',1);
	$pdf->Cell(40,6,utf8_decode('Serial'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Estado'),1,1,'C',1);
	//$pdf->Cell(20,6,utf8_decode('Fondos'),1,1,'C',1);
	
	$pdf->SetFont('Arial','',8);
    

	
	       

    if(!empty($codigo)){
   	if(!empty ($fuente)) {			
    		$consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
		 	FROM t_activo Ta
		 	INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 	INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 	INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 	INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 	WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."' AND Tp.id_fondos = '".$fuente."'
		 	ORDER BY Tg.clase ASC") or die(mysqli_error($link));
	   	  	
     }else{
 
     $consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo, Tp.id_fondos 
		 FROM t_activo Ta
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'
		 ORDER BY Tg.clase ASC") or die(mysqli_error($link));

   } 
  
   while($row = mysqli_fetch_array($consulta))
	{
		$pdf->Cell(30,6,utf8_decode($row['clase']),1,0,'C',0);
		$pdf->Cell(30,6,utf8_decode($row['marca']),1,0,'C',0);
		$pdf->Cell(30,6,utf8_decode($row['modelo']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['color']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['placa']),1,0,'C',0);
		$pdf->Cell(40,6,utf8_decode($row['serial']),1,0,'C',0);
		if ($row['id_estado']==1){
		$pdf->Cell(20,6,'Excelente',1,1,'C',0);
		}
		if ($row['id_estado']==2){
		$pdf->Cell(20,6,'Bueno',1,1,'C',0);
		}
		if ($row['id_estado']==3){
		$pdf->Cell(20,6,'Regular',1,1,'C',0);
		}
		if ($row['id_estado']==4){
		$pdf->Cell(20,6,'Malo',1,1,'C',0);
		}
		
		
	}
	$pdf->Output('Reporte_'.date('d-m-Y').'.pdf', 'D');
   
 }
  

mysqli_close($link);
	
?>

