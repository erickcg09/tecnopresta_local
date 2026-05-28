<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su administrador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}
date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];


$logdependencia = $_SESSION['dependencia'];
$logregional = $_SESSION['direccionreg'];
$logcircuito = $_SESSION['circuito'];
$fechaHoraServidor = date('Y-m-d H:i:s');

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


?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Panel Administrativo</title>
 

    <!-- Bootstrap core CSS -->
    <script src="js/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .ui-autocomplete-row
      {
        padding:8px;
        background-color: #f4f4f4;
        border-bottom:1px solid #ccc;
        font-weight:bold;
      }
      .ui-autocomplete-row:hover
      {
        background-color: #ddd;
      }

      .dropbtn {
      background-color: #2b2827;
      color: white;
      padding: 16px;
      font-size: 16px;
      border: none;
      cursor: pointer;
    }

    .dropbtn:hover, .dropbtn:focus {
      background-color: #808b96;
    }

    #myInput {
      box-sizing: border-box;
      background-image: url('matricula_imagenes/searchicon.png');
      background-position: 14px 12px;
      background-repeat: no-repeat;
      font-size: 16px;
      padding: 14px 20px 12px 45px;
      border: none;
      border-bottom: 1px solid #ddd;
    }

    #myInput:focus {outline: 3px solid #ddd;}

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f6f6f6;
      min-width: 230px;
      overflow: auto;
      border: 1px solid #ddd;
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
    }

    .dropdown a:hover {background-color: #ddd;}

    .show {display: block;}
    
    
    </style>

    
    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet">
  </head>
  <body>
    
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="menu_inventario_nacional.php">TecnoPresta</a>
  <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <input class="form-control form-control-dark w-100" type="text" id="FiltrarContenido" placeholder="Buscar" aria-label="Search">
  <div class="navbar-nav">
    <div class="nav-item text-nowrap">
      <a class="nav-link px-3" href="gameover.php">Cerrar sesi&oacute;n</a>
    </div>
  </div>
</header>

<?php
            // Incluye el menu izquierdo
            include('menu/menu_izquierdo.php');
?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4"> <!-- Apertura del main donde se coloca la informacion del panel derecho -->


        <h2 class="text-center mt-3">Inventario Nacional de Activos</h2>

    <div class="row">
        <div class="col-md-6">
            <p class="text-center mt-3">Activos por fondos</p>
            <canvas id="myChart" width="400" height="200"></canvas>
            <form action="exportar_grafico_activos_por_fondos.php" method="post">
              <button class="btn btn-success"
                      type="submit" 
              ><i class="bi bi-file-earmark-excel-fill"></i> 
              Excel
              </button>
            </form> 
        </div>
        <div class="col-md-6">
            <p class="text-center mt-3">Ubicaci&oacute;n equipos Programa 3</p>
            <canvas id="p3ubicar" width="400" height="200"></canvas>
        </div> 
    </div>
    <div class="row mt-3">
        <div class="col-md-6">
            <p class="text-center mt-3">Cantidad de activos hurtados</p>
            <canvas id="chart3" width="400" height="200"></canvas>
            <form action="exportar_grafico_activos_hurtados.php" method="post">
              <button class="btn btn-success"
                      type="submit" 
              ><i class="bi bi-file-earmark-excel-fill"></i> 
              Excel
              </button>
            </form>             
        </div>
        <div class="col-md-6">
            <p class="text-center mt-3">Cantidad de activos muy buenos o buenos a donar</p>
            <canvas id="chart4" width="400" height="200"></canvas>
            <form action="exportar_grafico_activos_donar.php" method="post">
              <button class="btn btn-success"
                      type="submit" 
              ><i class="bi bi-file-earmark-excel-fill"></i> 
              Excel
              </button>
            </form>             
            
        </div>
        <div class="col-md-6">
            <p class="text-center mt-3">Cantidad de activos en uso FONATEL y PRONIE</p>
            <canvas id="myChart4" width="400" height="200"></canvas>
        </div>
        <div class="col-md-6">
            <p class="text-center mt-3">Uso de equipos Programa 3</p>
            <canvas id="myPieChart" width="400" height="200"></canvas>
        </div>
    </div>


    </main> <!-- Cierre del main -->
  </div>
</div>

<!-- Seccion de modals -->
<script>
    fetch('in_data.php')
        .then(response => response.json())
        .then(data => {
            const fondos = data.map(item => item.fondos);
            const totales = data.map(item => item.total_registros);

            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: fondos,
                    datasets: [{
                        label: 'Total de Activos',
                        data: totales,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            formatter: (value, ctx) => {
                                return value;
                            },
                            color: '#000',
                            anchor: 'end',
                            align: 'top'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error obteniendo los datos:', error));
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('in_data2.php')
            .then(response => response.json())
            .then(data => {
                const totalInstituciones = 4482;
                const institucionesEntregaron = data.length;
                const institucionesNoEntregaron = totalInstituciones - institucionesEntregaron;

                const ctx = document.getElementById('chart2').getContext('2d');
                const chart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Instituciones que entregaron', 'Instituciones que no entregaron'],
                        datasets: [{
                            data: [institucionesEntregaron, institucionesNoEntregaron],
                            backgroundColor: ['#36A2EB', '#FF6384'],
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return tooltipItem.label + ': ' + tooltipItem.raw;
                                    }
                                }
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    let sum = ctx.chart.data.datasets.data.reduce((a, b) => a + b, 0);
                                    let percentage = (value * 100 / sum).toFixed(2) + "%";
                                    return `${value} (${percentage})`;
                                },
                                color: '#fff',
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    });
</script>
<script>
    fetch('in_data3.php')
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.fondos);
            const values = data.map(item => item.total_registros);

            const ctx = document.getElementById('chart3').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad de equipos hurtados',
                        data: values,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            formatter: (value, ctx) => {
                                return value;
                            },
                            color: '#000',
                            anchor: 'end',
                            align: 'top'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching data:', error));
</script>
<script>
    fetch('in_data4.php')
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.fondos);
            const values = data.map(item => item.total_registros);

            const ctx = document.getElementById('chart4').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad de artículos a donar',
                        data: values,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            formatter: (value, ctx) => {
                                return value;
                            },
                            color: '#000',
                            anchor: 'end',
                            align: 'top'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching data:', error));
</script>
<script>
fetch('datos4.php')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('myChart4').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['En Uso', 'No en Uso'],
                datasets: [
                    {
                        label: 'Total Activos',
                        data: [data.total_en_uso, data.total_no_en_uso],
                        backgroundColor: ['rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)'],
                        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    })
    .catch(error => console.error('Error al obtener datos:', error));    
</script> 
    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'datos5.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var ctx = document.getElementById('myPieChart').getContext('2d');
                    var myPieChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['En uso', 'No en uso'],
                            datasets: [{
                                data: [data.total_en_uso, data.total_no_en_uso],
                                backgroundColor: ['#FFCE56', '#FF6384']
                            }]
                        },
                        options: {
                            responsive: true
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener los datos:', error);
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'datos6.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var labels = [];
                    var values = [];
                    var backgroundColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF'];

                    data.forEach(function(item) {
                        switch(item.id_lugar) {
                            case '0':
                                labels.push('SIN UBICAR');
                                break;
                            case '1':
                                labels.push('BODEGA');
                                break;
                            case '2':
                                labels.push('LABORATORIO');
                                break;
                            case '3':
                                labels.push('SALA DE ROBÓTICA');
                                break;
                            case '4':
                                labels.push('AULAS');
                                break;
                            case '5':
                                labels.push('BIBLIOTECA');
                                break;
                            case '6':
                                labels.push('OFICINAS ADMINISTRATIVAS');
                                break;
                        }
                        values.push(item.total_activos);
                    });

                    var ctx = document.getElementById('p3ubicar').getContext('2d');
                    var myPieChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: backgroundColors
                            }]
                        },
                        options: {
                            responsive: true
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener los datos:', error);
                }
            });
        });
    </script>
<script>
function keepSessionAlive() {
    setInterval(function() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "keep_alive.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Opcional: Mantener la sesion activa
                // console.log("Session kept alive");
            }
        };
        xhr.send();
    }, 300000); // 300000 milisegundos = 5 minutos
}

// Iniciar la función cuando la página cargue
window.onload = keepSessionAlive;
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="js/feather.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/dashboard.js"></script>

  </body>
</html>