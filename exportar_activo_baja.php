<?php

include 'conexion.php';


$logcodigo=trim($_GET['codigop']);
$estado= trim($_GET['estadop']);
$b_estado= trim($_GET['b_estadop']);  
$b_dependencia= trim($_GET['dependencia_p']); 
$activado= 0;
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
			$this->Cell(200,10, utf8_decode('LISTADO DE ACTIVOS DADOS DE BAJA'),0,0,'C'); //Tamaño de la celda
			$this->Ln(10); //Salto de linea
			$this->SetFont('Arial','',10);
			$this->Cell(5);
			$this->Cell(200,10,utf8_decode('CÓDIGO PRESUPUESTARIO: ').trim($_GET['codigop']),0,0,'C');
			$this->Ln(10); //Salto de linea
			$this->SetFont('Arial','',10);
			$this->Cell(5);
			$this->Cell(200,10,utf8_decode('INSTITUCIÓN: ').utf8_decode(trim($_GET['dependenciap'])),0,0,'C');
			$this->Ln(10); //Salto de linea
			$this->SetFont('Arial','',10);
			$this->Cell(5);
			$this->Cell(200,10,utf8_decode('Fecha: ').date('d-m-Y'),0,0,'C');
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
	$pdf->SetFont('Arial','B',9);
	
	$pdf->Cell(20,6,utf8_decode('Clase'),1,0,'C',1);
	$pdf->Cell(30,6,utf8_decode('Marca'),1,0,'C',1);
	$pdf->Cell(30,6,utf8_decode('Modelo'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Color'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Placa'),1,0,'C',1);
	$pdf->Cell(30,6,utf8_decode('Serial'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Fecha'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Cédula'),1,1,'C',1);
	//$pdf->Cell(20,6,utf8_decode('Nombre'),1,1,'C',1);
	
	$pdf->SetFont('Arial','',8);
    
 //  $fecha_i= date("Y-m-d",strtotime(trim($_POST['fecha_inicio'])));
 //  $fecha_f= date("Y-m-d",strtotime(trim($_POST['fecha_fin'])));
   
    
    	
   $consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, 
   								Tc.color, Tp.id_placa, Tp.placa, Tp.serial,Tp.id_estado, 
   								Tp.codigo, Tp.activo, Tls.cedula, Tls.nombre, Tls.fecha
								 FROM t_activo Ta
								 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
								 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
								 INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
								 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
						       INNER JOIN t_log_sacar Tls ON Tp.id_placa = Tls.id_placa
								 WHERE Tp.codigo = '".$logcodigo."' AND Tp.activo = '".$activado."'
								 ORDER BY Tg.clase ASC") or die(mysqli_error($link));

    
   while($row = mysqli_fetch_array($consulta))
	{
		$pdf->Cell(20,6,utf8_decode($row['clase']),1,0,'C',0);
		$pdf->Cell(30,6,utf8_decode($row['marca']),1,0,'C',0);
		$pdf->Cell(30,6,utf8_decode($row['modelo']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['color']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['placa']),1,0,'C',0);
		$pdf->Cell(30,6,utf8_decode($row['serial']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['fecha']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['cedula']),1,1,'C',0);
		//$pdf->Cell(20,6,utf8_decode($row['nombre']),1,1,'C',0);
		
	}
	$pdf->Output('Reporte_'.date('d-m-Y').'.pdf', 'D');
   

  


	
?>

