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
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
$logtipo = $_SESSION['tipo'];
include "data.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Reportes</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <!-- jQuery y jQuery UI -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
  <!-- Google Charts -->
  <script src="https://www.gstatic.com/charts/loader.js"></script>
  <!-- Estilos personalizados -->
  <style>
    :root {
      --primary-color: #2c3e50;
      --secondary-color: #3498db;
      --accent-color: #e74c3c;
      --light-color: #ecf0f1;
      --dark-color: #34495e;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
      color: #333;
    }
    
    .navbar {
      background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
    }
    
    .nav-link {
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .nav-link:hover {
      transform: translateY(-2px);
    }
    
    .user-info {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      margin-bottom: 30px;
    }
    
    .menu-card {
      background-color: white;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      height: 100%;
      transition: all 0.3s ease;
    }
    
    .menu-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }
    
    .menu-title {
      color: var(--primary-color);
      font-weight: 700;
      margin-bottom: 20px;
      border-bottom: 2px solid var(--secondary-color);
      padding-bottom: 10px;
    }
    
    .menu-item {
      margin-bottom: 15px;
      padding: 12px 15px;
      border-left: 4px solid var(--secondary-color);
      background-color: var(--light-color);
      border-radius: 5px;
      transition: all 0.3s ease;
    }
    
    .menu-item:hover {
      background-color: var(--secondary-color);
      color: white;
      transform: translateX(5px);
    }
    
    .menu-item a {
      text-decoration: none;
      color: inherit;
      display: block;
      font-weight: 500;
    }
    
    .menu-item i {
      margin-right: 10px;
      color: var(--primary-color);
    }
    
    .menu-item:hover i {
      color: white;
    }
    
    .badge-new {
      background-color: var(--accent-color);
      font-size: 0.7rem;
      vertical-align: super;
    }
    
    .chart-container {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    footer {
      background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
      color: white;
    }
    
    .footer-title {
      font-weight: 700;
      margin-bottom: 20px;
    }
    
    .footer-link {
      color: #ddd;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .footer-link:hover {
      color: white;
      text-decoration: underline;
      transform: translateX(5px);
    }
    
    .copyright {
      background-color: rgba(0,0,0,0.2);
    }
    
    @media (max-width: 768px) {
      .menu-card {
        margin-bottom: 30px;
      }
    }
  </style>
</head>
<body>
  <!-- Barra de navegación -->
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <img src="img/logomep2020.png" width="45" height="30" alt="MEP" class="me-2">
      <a class="navbar-brand" href="formulario_menu_principal.html">TecnoPresta</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="formulario_menu_inventario.html">
              <i class="bi bi-house-door"></i> Principal
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="gameover.php">
              <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contenido principal -->
  <main class="container my-5">
    <!-- Información del usuario -->
    <div class="user-info text-center mb-5">
      <h2 class="fw-bold text-primary">Bienvenido/a, <?php echo $lognombre; ?></h2>
      <p class="text-muted">Código: <?php echo $logcodigo; ?></p>
    </div>
    
    <div class="row">
      <!-- Menú de opciones -->
      <div class="col-lg-6 mb-4">
        <div class="menu-card">
          <h3 class="menu-title">
            <i class="bi bi-list-check"></i> Menú de Reportes
          </h3>
          
          <div class="menu-item">
            <a href="formulario_informe_activos.php">
              <i class="bi bi-pie-chart-fill"></i> Lista de todos sus Activos Inventariados
            </a>
          </div>
          
          <div class="menu-item">
            <a href="formulario_informe_activos_codigo.php">
              <i class="bi bi-bar-chart-line-fill"></i> Lista de Activos por Dependencia
            </a>
          </div>
          
          <div class="menu-item">
            <a href="formulario_informe_activos_baja.php">
              <i class="bi bi-trash-fill"></i> Lista de activos dados de baja
            </a>
          </div>
          
          <div class="menu-item">
            <a href="formulario_informe_activos_codigo_fuente.php">
              <i class="bi bi-funnel-fill"></i> Lista de activos por dependencia y fuente de financiamiento
            </a>
          </div>
          
          <div class="menu-item">
            <a href="formulario_informe_licencia.php">
              <i class="bi bi-file-earmark-text-fill"></i> Lista de licencias
            </a>
          </div>
          
          <div class="menu-item">
            <a href="formulario_informe_licencia_codigo.php">
              <i class="bi bi-files-alt"></i> Lista de licencias por dependencia
            </a>
          </div>
          
          <div class="menu-item">
            <a href="prestamos_efectivos_por_regionales.php">
              <i class="bi bi-map-fill"></i> Solicitudes Efectivas por Regionales de Educación
            </a>
          </div>
          
          <div class="menu-item">
            <a href="reporte_beneficiarios_programa_3_conteo.php">
              <i class="bi bi-people-fill"></i> Reporte de Beneficiarios con Equipo Asignado General
            </a>
          </div>
          
          <div class="menu-item">
            <a href="formulario_informe_solicitudes.php">
              <i class="bi bi-clipboard-data-fill"></i> Reporte de préstamos realizados en su Institución
            </a>
          </div>
          
          <div class="menu-item">
            <a href="confirmacion_entrega_equipo_fonatel.php">
              <i class="bi bi-check-circle-fill"></i> Reportar confirmación de recibido del equipo FONATEL 
              <span class="badge bg-danger badge-new">Nuevo</span>
            </a>
          </div>
          
          <div class="menu-item">
            <a href="../reportes_fonatel/selector_informe.php">
              <i class="bi bi-file-earmark-spreadsheet-fill"></i> Reportes de las instituciones sobre el equipo recibido
              <span class="badge bg-danger badge-new">Nuevo</span>
            </a>
          </div>
          
          <div class="menu-item">
            <a href="../reportes_fonatel/selector_institucion_editar.php">
              <i class="bi bi-pencil-square"></i> Reporte para corrección de instituciones que regresaron equipo Fonatel al MEP
              <span class="badge bg-danger badge-new">Nuevo</span>
            </a>
          </div>
        </div>
      </div>
      
      <!-- Gráficos (solo visible en pantallas grandes) -->
      <div class="col-lg-6 d-none d-lg-block">
        <div class="chart-container mb-4">
          <h3 class="text-center mb-4">
            <i class="bi bi-pie-chart-fill"></i> Clasificación Global de Activos
          </h3>
          <div id="chartDiv" class="pie-chart"></div>
        </div>
        
        <div class="chart-container">
          <h3 class="text-center mb-4">
            <i class="bi bi-bar-chart-line-fill"></i> Ranking de Artículos más Solicitados
          </h3>
          <!-- Aquí iría el segundo gráfico  -->
 <!--          <div class="text-center py-4">
            <img src="https://via.placeholder.com/400x200?text=Gráfico+de+Ranking" alt="Ranking" class="img-fluid rounded">
          </div>-->
        </div>
      </div>
    </div>
  </main>

  <!-- Pie de página -->
  <footer class="text-white pt-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-3 col-md-6">
          <h5 class="footer-title">
            <i class="bi bi-gem"></i> TecnoPresta
          </h5>
          <p>
            Sistema de Administración del Inventario Tecnológico y el Préstamo de Equipos del Ministerio de Educación Pública de Costa Rica.
          </p>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <h5 class="footer-title">Relacionados</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <a href="/formulario_menu_prestamo.html" class="footer-link">
                <i class="bi bi-arrow-right-short"></i> Solicitar Activos
              </a>
            </li>
            <li class="mb-2">
              <a href="plataforma_clientes.php" class="footer-link">
                <i class="bi bi-arrow-right-short"></i> Centro de Soporte Educativo
              </a>
            </li>
            <li class="mb-2">
              <a href="formulario_menu_inventario.html" class="footer-link">
                <i class="bi bi-arrow-right-short"></i> Módulo de Inventario
              </a>
            </li>
            <li class="mb-2">
              <a href="formacion.php" class="footer-link">
                <i class="bi bi-arrow-right-short"></i> Manuales y Webinarios
              </a>
            </li>
          </ul>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <h5 class="footer-title">Accesos Rápidos</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <a href="/formulario_beneficiario.php" class="footer-link">
                <i class="bi bi-arrow-right-short"></i> Contratos Programa N°3 Fonatel
              </a>
            </li>
            <li class="mb-2">
              <a href="/formulario_informe_beneficiarios_programa_dos.php" class="footer-link">
                <i class="bi bi-arrow-right-short"></i> Beneficiarios Programa N°2
              </a>
            </li>
            <li class="mb-2">
              <a href="/formulario_informe_activos.php" class="footer-link">
                <i class="bi bi-arrow-right-short"></i> Activos Inventariados
              </a>
            </li>
            <li class="mb-2">
              <a href="/formulario_informe_solicitudes.php" class="footer-link">
                <i class="bi bi-arrow-right-short"></i> Préstamos por Institución
              </a>
            </li>
          </ul>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <h5 class="footer-title">Contactos</h5>
          <ul class="list-unstyled">
            <li class="mb-3">
              <i class="bi bi-geo-alt-fill me-2"></i> San Francisco de Calle Blancos, San José
            </li>
            <li class="mb-3">
              <i class="bi bi-envelope-fill me-2"></i> tecnopresta@mep.go.cr
            </li>
          </ul>
          <div class="mt-4">
            <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-5"></i></a>
            <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-5"></i></a>
            <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-5"></i></a>
            <a href="#" class="text-white"><i class="bi bi-linkedin fs-5"></i></a>
          </div>
        </div>
      </div>
      
      <div class="text-center py-3 mt-4 copyright">
        <b>Ministerio de Educación Pública de la República de Costa Rica &copy; <?php echo date("Y"); ?></b>
      </div>
    </div>
  </footer>

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Google Charts Script -->
  <script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = new google.visualization.arrayToDataTable([
        ['Language', 'Rating'],
        <?php
          while($row = mysqli_fetch_assoc($chartQueryRecords)){
            echo "['".$row['clase']."', ".$row['n']."],";
          }
        ?>
      ]);

      var options = {
        title: 'Cantidad de Modelos Registrados por Tipo',
        backgroundColor: 'transparent',
        legend: {position: 'labeled'},
        pieSliceText: 'value',
        chartArea: {width: '90%', height: '80%'},
        colors: ['#2c3e50', '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6']
      };

      var chart = new google.visualization.PieChart(document.getElementById('chartDiv'));
      chart.draw(data, options);
    }
    
    // Redibujar el gráfico cuando cambie el tamaño de la ventana
    window.addEventListener('resize', drawChart);
  </script>
</body>
</html>