<?php

include 'conexion.php';


$codigo=trim($_GET['codigop']);

 
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
			$this->Cell(20); //Desplaza a la derecha
			$this->Cell(150,10, utf8_decode('LISTA DE LICENCIAS'),0,0,'C'); //Tamaño de la celda
			$this->Ln(10); //Salto de linea
			$this->SetFont('Arial','',12);
			$this->Cell(20);
			$this->Cell(150,10,utf8_decode('CÓDIGO PRESUPUESTARIO: ').trim($_GET['codigop']),0,0,'C');
			$this->Ln(10); //Salto de linea
			$this->SetFont('Arial','',12);
			$this->Cell(20);
			$this->Cell(150,10,utf8_decode('INSTITUCIÓN: ').utf8_decode(trim($_GET['dependenciap'])),0,0,'C');
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
	
	$pdf->Cell(40,6,utf8_decode('Licencia'),1,0,'C',1);
	$pdf->Cell(30,6,utf8_decode('Nombre'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Placa'),1,0,'C',1);
	$pdf->Cell(30,6,utf8_decode('Serial'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('F.Activación'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Vigencia'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('F.Expira'),1,1,'C',1);

	
	$pdf->SetFont('Arial','',8);
    
 //  $fecha_i= date("Y-m-d",strtotime(trim($_POST['fecha_inicio'])));
 //  $fecha_f= date("Y-m-d",strtotime(trim($_POST['fecha_fin'])));
   
    
    	
if(!empty($codigo)){
    	
 		 
   $consulta=mysqli_query($link,"SELECT  Ts.licencia, Ts.factivacion, Ts.vigencia, Tl.id_placa, Tsg.etiqueta, Tp.placa, Tp.serial, Tp.codigo
        FROM (t_software Ts INNER JOIN t_licencia Tl ON Ts.id_software = Tl.id_software) LEFT JOIN t_placa Tp ON Tl.id_placa = Tp.id_placa
        INNER JOIN t_software_general Tsg ON Ts.id_sg = Tsg.id_sg
        WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'
		  ORDER BY Ts.factivacion ASC") or die(mysqli_error($link));

    
   while($row = mysqli_fetch_array($consulta))
	{
		$pdf->Cell(40,6,utf8_decode($row['licencia']),1,0,'C',0);
		$pdf->Cell(30,6,utf8_decode($row['etiqueta']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['placa']),1,0,'C',0);
		$pdf->Cell(30,6,utf8_decode($row['serial']),1,0,'C',0);
		$pdf->Cell(20,6,date("d-m-Y",strtotime($row['factivacion'])),1,0,'C',0);
		$pdf->Cell(20,6,$row['vigencia'],1,0,'C',0);
		
		$vence= $activos['factivacion']." + ".$activos['vigencia']. "month";	
      			 
      	$pdf->Cell(20,6,date("d-m-Y",strtotime($vence)),1,1,'C',0);
		
	}
	$pdf->Output('Reporte_'.date('d-m-Y').'.pdf', 'D');
   
 }
  


	
?>

