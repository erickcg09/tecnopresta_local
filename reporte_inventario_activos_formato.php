<?php  
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4 or $_SESSION['tipo']==5);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "formulario_menu_inventario.html"
</script>';
}
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$logdependencia = $_SESSION['dependencia'];
$logcorreomep = $_SESSION['correomep'];
$logdireccionreg = $_SESSION['direccionreg'];
$logcircuito = $_SESSION['circuito'];
$activado = 1;
		 
		 require('fpdf/fpdf.php');

        class PDF extends FPDF
        {
        // Cabecera de página
        function Header()
        {
            // Logo
//            $this->Image('logo.png',10,8,33);
            // Arial bold 15
            $this->SetFont('Arial','B',15);
            // Movernos a la derecha
            $this->Cell(80);
            // Título
            $this->Cell(30,10,'Titulo',0,0,'C');
            // Salto de línea
            $this->Ln(20);
        }
        
        // Pie de página
        function Footer()
        {
            // Posición: a 1,5 cm del final
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','I',8);
            // Número de página
            $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
        }
        }

		 $consulta = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo 
		                FROM t_activo Ta 
		                 INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
                		 INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
                		 INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
                		 INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
                		 WHERE Tp.codigo = '".$logcodigo."' AND Tp.activo = '".$activado."'
		                ";
		 $resultado = $link->query($consulta);


        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',8);

		 
		 while ($row = $resultado->fetch_assoc()) {
		     
		    $pdf->Cell(30, 10, utf8_decode($row['placa']), 1, 0, 'C', 0);
		    $pdf->Cell(40, 10, utf8_decode($row['clase']), 1, 0, 'C', 0);
		    $pdf->Cell(25, 10, utf8_decode($row['marca']), 1, 0, 'C', 0);
		    $pdf->Cell(40, 10, utf8_decode($row['modelo']), 1, 0, 'C', 0);
		    $pdf->Cell(55, 10, utf8_decode($row['serial']), 1, 1, 'C', 0);

		 }
		
		 $pdf->Output();
?>


