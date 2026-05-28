<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
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
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];



// Consultar los alias desde la tabla t_alias filtrando por el código
$query = "SELECT alias_id, alias FROM t_alias WHERE codigo = ?";
$stmt = $link->prepare($query);
$stmt->bind_param("s", $logcodigo);
$stmt->execute();
$result = $stmt->get_result();

// Crear un array para almacenar los resultados de la consulta
$aliases = array();
while ($row = $result->fetch_assoc()) {
    $aliases[] = $row;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminstraci&oacute;n de reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
            .card-custom {
                background-color: #f0f4f8; /* Color frío y suave */
                border: none;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }
            .card-body {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .card-img {
                width: 50px;
                height: 50px;
                object-fit: cover;
                border-radius: 50%;
                align-self: flex-end;
            }
            .btn-custom {
                background-color: #007bff; /* Color frío */
                border: none;
                border-radius: 5px;
            }
            .icon-large {
            font-size: 2em; /* Ajusta el tamaño del ícono */
            }
            #reservasChart {
            width: 100%;
            height: 600px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md bg-dark navbar-dark">
        <img src="img/logodelgobierno.png" width="35" height="30" alt="" loading="lazy">
        <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="informe_calendario.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-all" viewBox="0 0 16 16">
                <path d="M8.098 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.7 8.7 0 0 0-1.921-.306 7 7 0 0 0-.798.008h-.013l-.005.001h-.001L8.8 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L4.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028zM9.3 10.386q.102 0 .223.006c.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96z"/>
                <path d="M5.232 4.293a.5.5 0 0 0-.7-.106L.54 7.127a1.147 1.147 0 0 0 0 1.946l3.994 2.94a.5.5 0 1 0 .593-.805L1.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028 4.012-2.954a.5.5 0 0 0 .106-.699"/>
                </svg> Regresar</a>
            </li> 
            <li class="nav-item">
              <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#calendarModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16">
                  <path d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/>
                  <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
                Agregar reserva
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalEliminar">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar2-x" viewBox="0 0 16 16">
                  <path d="M6.146 8.146a.5.5 0 0 1 .708 0L8 9.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 10l1.147 1.146a.5.5 0 0 1-.708.708L8 10.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 10 6.146 8.854a.5.5 0 0 1 0-.708"/>
                  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/>
                  <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
                Eliminar reserva
              </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="gameover.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open" viewBox="0 0 16 16">
                <path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/>
                <path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117M11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5M4 1.934V15h6V1.077z"/>
                </svg> Cerrar Sesión</a>
            </li>  
            </ul>
        </div>  
    </nav>

<div class="container mt-5">
    <h2>Usuario: <?php echo $lognombre." ".$logcodigo;?></h2><br>
    <h4>Formulario para gestionar reservas de instancias.</h4>
    
    <?php
    if (isset($_GET['success']) && $_GET['success'] === 'true') {
        echo '<div id="mensaje" class="alert alert-success" role="alert">
                ¡Reservas registradas exitosamente!
              </div>';
    } elseif (isset($_GET['error'])) {
        $mensaje_error = '';
    
        switch ($_GET['error']) {
            case 'datos_incompletos':
                $mensaje_error = 'Error: Datos incompletos. Verifique el formulario.';
                break;
            case 'error_preparacion_alias':
                $mensaje_error = 'Error en la preparación de la consulta para obtener el alias.';
                break;
            case 'alias_no_encontrado':
                $alias_id = htmlspecialchars($_GET['alias_id'] ?? 'Desconocido');
                $mensaje_error = "Error: No se encontró el alias con ID: $alias_id.";
                break;
            case 'error_preparacion_verificacion':
                $mensaje_error = 'Error en la preparación de la consulta para verificar conflictos de horarios.';
                break;
            case 'conflicto_horarios':
                $fecha = htmlspecialchars($_GET['fecha'] ?? 'Desconocida');
                $mensaje_error = "Error: Existe un conflicto de horarios en la fecha $fecha.";
                break;
            case 'error_preparacion_insercion':
                $mensaje_error = 'Error en la preparación de la consulta de inserción.';
                break;
            case 'error_ejecucion_insercion':
                $mensaje_error = 'Error en la ejecución de la consulta de inserción.';
                break;
            case 'metodo_incorrecto':
                $mensaje_error = 'Error: Método no permitido.';
                break;
            default:
                $mensaje_error = 'Ocurrió un error desconocido.';
        }
    
        echo '<div id="mensaje" class="alert alert-danger" role="alert">' . $mensaje_error . '</div>';
    }
    ?>

    
    <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-calendar3" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#calendarModal">
          <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z"/>
          <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
        </svg>
    </div>
    
<div id="reservasChart" class="bg-light border"></div>    
    
</div>



<footer class="bg-dark text-white pt-4 pb-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0">Por favor, asegúrese de ingresar la información solicitada en cada instancia.</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <div class="border border-light p-3">
                    <p class="mb-0">© 2024 Ministerio de Educación Pública. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>
</footer>

    <!-- Modal -->
    <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="calendarModalLabel">Reservar instancias</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="insertar_reservas_alias.php" method="post">
                        <input type="hidden" name="codigo" value="<?php echo $logcodigo;?>">
                        <!-- Campo para la fecha de inicio -->
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>

                        <!-- Campo para la fecha de fin -->
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>

                        <!-- Campo para la hora de inicio -->
                        <div class="mb-3">
                            <label for="hora_inicio" class="form-label">Hora de inicio</label>
                            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                        </div>

                        <!-- Campo para la hora de fin -->
                        <div class="mb-3">
                            <label for="hora_fin" class="form-label">Hora de fin</label>
                            <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                        </div>

                      <!-- Casillas de verificación para cada día de la semana -->
                        <div class="mb-3">
                            <label class="form-label">Seleccionar días de la semana</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="lunes" name="dias[]" value="L">
                                <label class="form-check-label" for="lunes">Lunes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="martes" name="dias[]" value="K">
                                <label class="form-check-label" for="martes">Martes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="miercoles" name="dias[]" value="M">
                                <label class="form-check-label" for="miercoles">Miércoles</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="jueves" name="dias[]" value="J">
                                <label class="form-check-label" for="jueves">Jueves</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="viernes" name="dias[]" value="V">
                                <label class="form-check-label" for="viernes">Viernes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="sabado" name="dias[]" value="S">
                                <label class="form-check-label" for="sabado">Sábado</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="domingo" name="dias[]" value="D">
                                <label class="form-check-label" for="domingo">Domingo</label>
                            </div>
                        </div>

                        <!-- Campo para seleccionar el alias -->
                        <div class="mb-3">
                            <label for="alias" class="form-label">Seleccionar alias</label>
                            <select class="form-select" id="alias" name="alias" required>
                                <option value="">Seleccione un alias</option>
                                <?php foreach ($aliases as $alias): ?>
                                    <option value="<?php echo $alias['alias_id']; ?>"><?php echo $alias['alias']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Botones para cerrar o guardar -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Confirmación para Eliminar -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEliminarLabel">Confirmar Eliminación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <form id="formEliminar" method="POST" action="eliminar_reservaciones.php">
            <div class="modal-body">
              <div class="mb-3">
                <label for="selectCamada" class="form-label">Seleccione una</label>
                <select class="form-select" id="selectCamada" name="camada" required>
                  <!-- Opciones cargadas dinámicamente -->
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-danger">Eliminar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        // Función para obtener los datos de las reservas desde el servidor
        fetch('consulta_reservas.php')
            .then(response => response.json()) // Parseamos el JSON
            .then(reservas => {
                // Mapeamos las reservas a los datos que necesita ECharts
                const seriesData = reservas.map((reserva, index) => {
                    return {
                        name: `Reserva ${reserva.id_reserva}`, // ID de la reserva
                        value: [
                            reserva.fecha_inicio.split('-')[1], // Mes de inicio (X)
                            parseFloat(reserva.hora_inicio.split(':')[0]), // Hora de inicio (Y1)
                            reserva.fecha_fin.split('-')[1], // Mes de fin (X)
                            parseFloat(reserva.hora_fin.split(':')[0]) // Hora de fin (Y2)
                        ],
                        alias: reserva.alias, // Incluimos el alias
                        itemStyle: {
                            color: `hsl(${index * 100}, 70%, 50%)` // Colores dinámicos
                        }
                    };
                });

                // Inicializa el gráfico
                const chart = echarts.init(document.getElementById('reservasChart'));

                // Configuración del gráfico
                const option = {
                    tooltip: {
                        formatter: params => {
                            // Incluimos el alias en el tooltip
                            return `${params.name}<br>
                                    Alias: ${params.data.alias}<br>
                                    Inicio: ${params.value[0]}-${params.value[1]}:00<br>
                                    Fin: ${params.value[2]}-${params.value[3]}:00`;
                        }
                    },
                    xAxis: {
                        type: 'category',
                        data: ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"], // Meses
                        name: "Meses",
                        boundaryGap: true
                    },
                    yAxis: {
                        type: 'value',
                        min: 7,
                        max: 22,
                        name: "Horas",
                        axisLabel: {
                            formatter: value => `${Math.floor(value)}:00`
                        }
                    },
                    series: [
                        {
                            type: 'custom',
                            renderItem: (params, api) => {
                                const xStart = api.coord([api.value(0), api.value(1)]); // Coordenadas de inicio
                                const xEnd = api.coord([api.value(2), api.value(3)]); // Coordenadas de fin
                                const barHeight = api.size([0, 1])[1] * 0.4;

                                return {
                                    type: 'rect',
                                    shape: {
                                        x: xStart[0],
                                        y: xStart[1] - barHeight / 2,
                                        width: xEnd[0] - xStart[0],
                                        height: barHeight
                                    },
                                    style: api.style()
                                };
                            },
                            data: seriesData
                        }
                    ]
                };

                // Aplica la configuración
                chart.setOption(option);
            })
            .catch(error => {
                console.error('Error al cargar los datos:', error);
            });
    </script>
    <script>
      document.getElementById('modalEliminar').addEventListener('show.bs.modal', function () {
        const selectCamada = document.getElementById('selectCamada');
        selectCamada.innerHTML = '<option value="">Cargando...</option>';
    
        fetch('obtener_camadas.php')
          .then(response => response.json())
          .then(data => {
            selectCamada.innerHTML = '<option value="">Seleccione una camada</option>';
            data.forEach(camada => {
              const option = document.createElement('option');
              option.value = camada;
              option.textContent = camada;
              selectCamada.appendChild(option);
            });
          })
          .catch(error => {
            console.error('Error al cargar camadas:', error);
            selectCamada.innerHTML = '<option value="">Error al cargar datos</option>';
          });
      });
    </script>


</body>
</html>