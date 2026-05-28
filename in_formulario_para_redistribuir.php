<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_origen = $_POST["input1"];
    $codigo_destino = $_POST["input2"];
    $fondos = $_POST["fondos"];

}
?>
<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <title>TecnoPresta</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-md bg-dark navbar-dark">
      <img src="img/logodelgobierno.png" width="45" height="30" alt="" loading="lazy">
      <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="in_formulario_redistribuir.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-all" viewBox="0 0 16 16">
                <path d="M8.098 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.7 8.7 0 0 0-1.921-.306 7 7 0 0 0-.798.008h-.013l-.005.001h-.001L8.8 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L4.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028zM9.3 10.386q.102 0 .223.006c.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96z"/>
                <path d="M5.232 4.293a.5.5 0 0 0-.7-.106L.54 7.127a1.147 1.147 0 0 0 0 1.946l3.994 2.94a.5.5 0 1 0 .593-.805L1.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028 4.012-2.954a.5.5 0 0 0 .106-.699"/>
                </svg> Regresar</a>
          </li> 
          <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#corregirModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eraser" viewBox="0 0 16 16">
                    <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z"/>
                </svg> Corregir placa y serie
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


<div class="container">
  <h2 class="my-4 text-center">Activos a redistribuir</h2>
  <form id="form-redistribucion" action="in_redistribucion_realizada.php" method="POST">
    <input type="hidden" name="id_placas" id="id-placas" value="">
    <input type="hidden" name="cod_o" id="cod_o" value="<?php echo $codigo_origen;?>">
    <input type="hidden" name="cod_d" id="cod_d" value="<?php echo $codigo_destino;?>">
    <input type="hidden" name="fondos" id="fondos_id" value="<?php echo $fondos;?>">
    <div class="row">
      <!-- Primera columna -->
      <div class="col-md-5">
        <div class="p-3 bg-light border">
          <h5>Activos del centro de origen <?php echo $codigo_origen;?></h5>
          <div class="table-responsive">
            <table id="tabla-origen" class="table table-hover table-bordered">
              <thead class="table-dark">
                <tr>
                  <th>ID Placa</th>
                  <th>Placa</th>
                  <th>Serial</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Consulta SQL
                $sql = "SELECT id_placa, placa, serial
                        FROM t_placa
                        WHERE codigo = ? AND id_fondos = ?
                        ORDER BY placa";

                $stmt = mysqli_prepare($link, $sql);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ii", $codigo_origen, $fondos);
                    mysqli_stmt_execute($stmt);
                    $resultado = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($resultado) > 0) {
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($fila['id_placa']) . '</td>';
                            echo '<td>' . htmlspecialchars($fila['placa']) . '</td>';
                            echo '<td>' . htmlspecialchars($fila['serial']) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3" class="text-center">No se encontraron registros.</td></tr>';
                    }
                } else {
                    echo '<tr><td colspan="3" class="text-center text-danger">Error en la consulta.</td></tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- Separación -->
      <div class="col-md-2">
            <div class="card">
              <div class="card-header text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-right" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5m14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5"/>
                </svg>
              </div>
              <div class="card-body">
                <h5 class="card-title">Cómo Mover Registros entre Listas</h5>
                <p class="card-text">
                  Puede hacer clic en cada registro para moverlo de una lista a la otra. De igual manera, puede hacer clic nuevamente para regresarlo a la lista original. Esta funcionalidad le permite gestionar los registros de manera eficiente y flexible.
                </p>
                <ul>
                  <li>Haga clic en un registro para moverlo a la lista de destino.</li>
                  <li>Haga clic nuevamente en el registro en la lista de destino para regresarlo a la lista original.</li>
                  <li>Haga clic en el bot&oacute;n Efectuar redistribuci&oacute;n para finalizar el proceso.</li>
                </ul>
              </div>
            </div>          
      </div>
      <!-- Segunda columna -->
      <div class="col-md-5">
        <div class="p-3 bg-light border">
          <h5>Activos a otorgar al centro destino <?php echo $codigo_destino;?></h5>
          <div class="table-responsive">
            <table id="tabla-destino" class="table table-striped table-bordered">
              <thead class="table-dark">
                <tr>
                  <th>ID Placa</th>
                  <th>Placa</th>
                  <th>Serial</th>
                </tr>
              </thead>
              <tbody>
                <!-- Esta tabla se llenará dinámicamente -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div> <!-- Cierre del row-->
    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary my-3">Efectuar redistribución</button>
    </div>
  </form>
</div> <!-- Cierre del container -->

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
    <div class="modal fade" id="corregirModal" tabindex="-1" aria-labelledby="corregirModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="corregirModalLabel">Corregir Placa y Serie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="corregirForm" method="POST" action="in_actualizar_placa_serie.php">
                        <div class="mb-3">
                            <label for="idPlaca" class="form-label">ID</label>
                            <input type="text" class="form-control" id="idPlaca" name="idPlaca">
                        </div>
                        <div class="mb-3">
                            <label for="placa" class="form-label">Placa</label>
                            <input type="text" class="form-control" id="placa" name="placa">
                        </div>
                        <div class="mb-3">
                            <label for="serial" class="form-label">Serie</label>
                            <input type="text" class="form-control" id="serial" name="serial">
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="buscarBtn">Buscar</button>
                    <button type="submit" class="btn btn-success" id="buscarBtn">Actualizar</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- JS para intercambios de activos y clasificación -->

<script>
// Función para actualizar el input hidden con los ID de la tabla de destino
function actualizarInputHidden() {
  const tablaDestino = document.querySelector("#tabla-destino tbody");
  const filas = tablaDestino.querySelectorAll("tr");
  const ids = Array.from(filas).map(fila => fila.cells[0].textContent.trim());
  
  // Actualizamos el valor del input hidden con la lista de IDs
  const idsString = ids.join(",");
  document.querySelector("#id-placas").value = idsString;
  
  // Mostrar en consola para verificar
  console.log("Valor de id_placas:", idsString);
}

// Manejar clics en las filas de la tabla de origen
document.querySelector("#tabla-origen tbody").addEventListener("click", function (event) {
  if (event.target.tagName === "TD") {
    const fila = event.target.parentElement; // Fila clicada
    const tablaDestino = document.querySelector("#tabla-destino tbody");
    tablaDestino.appendChild(fila); // Mover la fila a la tabla de destino
    actualizarInputHidden(); // Actualizar el input hidden
  }
});

// Manejar clics en las filas de la tabla de destino
document.querySelector("#tabla-destino tbody").addEventListener("click", function (event) {
  if (event.target.tagName === "TD") {
    const fila = event.target.parentElement; // Fila clicada
    const tablaOrigen = document.querySelector("#tabla-origen tbody");
    tablaOrigen.appendChild(fila); // Mover la fila de regreso a la tabla de origen
    actualizarInputHidden(); // Actualizar el input hidden
  }
});
</script>
<!-- JS para buscar la placa y la serie -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('buscarBtn').addEventListener('click', function () {
            // Obtener el valor del input idPlaca
            const idPlaca = document.getElementById('idPlaca').value;

            // Validar que no esté vacío
            if (idPlaca.trim() === '') {
                alert('Por favor, ingrese un ID válido.');
                return;
            }

            // Hacer la solicitud AJAX
            fetch(`in_buscar_placa_serie.php?id=${encodeURIComponent(idPlaca)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error); // Mostrar error si no se encuentra el registro
                    } else {
                        // Llenar los campos con los datos obtenidos
                        document.getElementById('placa').value = data.placa;
                        document.getElementById('serial').value = data.serial;
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud:', error);
                    alert('Ocurrió un error al buscar los datos.');
                });
        });
    });
</script>

  </body>
</html>