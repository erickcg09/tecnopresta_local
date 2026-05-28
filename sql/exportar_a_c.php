<?php



include 'conexion.php';


 

$codigo=trim($_POST['codigo']);
$activado= 1;
$link=$mysqli;


require('fpdf/fpdf.php');
 
// Plantilla	
	class PDF extends FPDF
	{
		function Header()
		{
			$this->Image('img/logo.png', 5, 5, 30 ); //Margen- margen - tamaño
			$this->SetFont('Arial','B',14);
			$this->Cell(20); //Desplaza a la derecha
			$this->Cell(100,10, utf8_decode('Solicitudes de Equipo'),0,0,'C'); //Tamaño de la celda
			$this->Ln(10); //Salto de linea
			$this->SetFont('Arial','',12);
			$this->Cell(20);
			//$this->Cell(100,10,trim($_POST['centro_pdf']),0,0,'C');
			$this->Ln(10); //Salto de linea
			$this->SetFont('Arial','',12);
			$this->Cell(20);
			$this->Cell(100,10,'Codigo '.trim($_POST['codigo']),0,0,'C');
			$this->Ln(10); //Salto de linea
			$this->SetFont('Arial','',12);
			$this->Cell(20);
			//$this->Cell(100,10,'Del '.date("d-m-Y",strtotime(trim($_POST['inicial']))).' Al '.date("d-m-Y",strtotime(trim( $_POST['fin']))),0,0,'C');
			$this->Ln(10); //Salto de linea
			$this->SetFont('Arial','U',10);
			$this->Cell(60);
		//	$this->Cell(100,10,'Cantidad: '.trim($_POST['filas']),0,0,'C');
						
			$this->Ln(20);
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
	$pdf->Cell(20,6,utf8_decode('Modelo'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Color'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Placa'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Serial'),1,0,'C',1);
	$pdf->Cell(20,6,utf8_decode('Estado'),1,1,'C',1);
	
	$pdf->SetFont('Arial','',8);
    
 //  $fecha_i= date("Y-m-d",strtotime(trim($_POST['fecha_inicio'])));
 //  $fecha_f= date("Y-m-d",strtotime(trim($_POST['fecha_fin'])));
   
    
    	
   if(!empty($codigo)){
    	
 //       $query= "SELECT t_boleta.codigo_pre AS codigo, t_centroEducativo.nombre AS centro, t_boleta.cedula AS cedula, t_padron.nombre AS nombre,t_padron.apellidop AS apellidop, t_padron.apellidom AS apellidom, t_boleta.fecha_s AS fecha_s, t_boleta.fecha_d AS fecha_d FROM t_boleta INNER JOIN t_centroEducativo ON t_boleta.codigo_pre =t_centroEducativo.codigo_pre INNER JOIN t_padron ON  t_padron.cedula= t_boleta.cedula  WHERE  DATE(t_boleta.fecha_s) BETWEEN '$fecha_i' AND '$fecha_f' ORDER BY t_boleta.codigo_pre";
 //     
 //   $consultar_p = mysqli_query($conexion, "$query");
 
    $consulta=mysqli_query($link,"SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
		 FROM t_activo Ta
		 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
		 INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
		 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
		 WHERE Tp.codigo = '".$codigo."' AND Tp.activo = '".$activado."'
		 ORDER BY Tg.clase ASC") or die(mysqli_error($link));

   
   while($row = mysqli_fetch_array($consulta))
	{
		
		
		$pdf->Cell(20,6,utf8_decode($row['clase']),1,0,'C',0);
		$pdf->Cell(30,6,utf8_decode($row['marca']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['modelo']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['color']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['placa']),1,0,'C',0);
		$pdf->Cell(20,6,utf8_decode($row['serial']),1,0,'C',0);
		if ($row['id_estado']==1){
		$pdf->Cell(20,6,Excelente,1,0,'C',0);
		}
		if ($row['id_estado']==2){
		$pdf->Cell(20,6,Bueno,1,0,'C',0);
		}
	}
	$pdf->Output('Reporte_'.date('d-m-Y').'.pdf', 'D');
   
 }
  


	
?>

