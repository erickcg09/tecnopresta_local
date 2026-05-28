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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Minimalista</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .card-custom {
            background-color: #f0f4f8; /* Color frío y no saturado */
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-custom .btn {
            background-color: #007bff; /* Color frío para el botón */
            color: white;
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
                <a class="nav-link" href="herramientas.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-all" viewBox="0 0 16 16">
                <path d="M8.098 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.7 8.7 0 0 0-1.921-.306 7 7 0 0 0-.798.008h-.013l-.005.001h-.001L8.8 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L4.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028zM9.3 10.386q.102 0 .223.006c.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96z"/>
                <path d="M5.232 4.293a.5.5 0 0 0-.7-.106L.54 7.127a1.147 1.147 0 0 0 0 1.946l3.994 2.94a.5.5 0 1 0 .593-.805L1.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028 4.012-2.954a.5.5 0 0 0 .106-.699"/>
                </svg> Regresar</a>
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
    <h3>Formulario de redistribuci&oacute;n de activos</h3>
    <div class="row">
        <!-- Formulario -->
        <form action="in_formulario_para_redistribuir.php" method="post">
            <!-- Select -->
            <div class="mb-3">
                <label for="fondos" class="form-label">Seleccionar fondo</label>
                <select class="form-select my-3 w-50" id="fondos" name="fondos" aria-label="Example select with button addon" required>
                    <option value="0">Seleccione..</option>
                    <?php 
                      $querz = $link->query("SELECT * FROM t_fondos");
                      while ($valorez = mysqli_fetch_array($querz)) {
                          echo '<option value="'.$valorez['id_fondos'].'">'.$valorez['fondos'].'</option>';
                      }
                    ?>
                </select>
            </div>
            
            <!-- Inputs en columnas -->
            <div class="row">
                  <!-- Tarjeta 1 -->
                  <div class="col-md-6">
                    <div class="card shadow-sm">
                      <div class="card-body">
                        <h5 class="card-title">Código de la Institución de Origen</h5>
                        <p class="card-text">
                          <strong>Descripción:</strong> En este campo, ingrese el código de la institución de la cual se tomarán los activos. Este código identifica de manera única a la institución de origen y es esencial para asegurar que los activos sean correctamente asignados desde la fuente correcta.
                        </p>
                        <p class="card-text">
                          <strong>Ejemplo:</strong> Si está tomando activos de la institución con el código "12345", debe ingresar "12345" en este campo.
                        </p>
                      </div>
                    </div>
                  </div>
                  <!-- Tarjeta 2 -->
                  <div class="col-md-6">
                    <div class="card shadow-sm">
                      <div class="card-body">
                        <h5 class="card-title">Código del Centro de Redistribución</h5>
                        <p class="card-text">
                          <strong>Descripción:</strong> En este campo, ingrese el código del centro donde se redistribuirán los activos. Este código es crucial para garantizar que los activos lleguen al destino correcto y se registren adecuadamente en el nuevo centro.
                        </p>
                        <p class="card-text">
                          <strong>Ejemplo:</strong> Si los activos se redistribuirán al centro con el código "67890", debe ingresar "67890" en este campo.
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="my-3"></div>
                <!-- Primera columna -->
                <div class="col-md-6 p-3" style="background-color: #f1f8e9;">
                    <label for="input1" class="form-label">Centro de origen</label>
                    <input type="text" class="form-control" id="input1" name="input1" placeholder="Ingrese dato 1" required>
                    <small id="result1" class="text-muted"></small> <!-- Etiqueta para el resultado -->
                </div>
                <!-- Segunda columna -->
                <div class="col-md-6 p-3" style="background-color: #fbe9e7;">
                    <label for="input2" class="form-label">Centro de destino</label>
                    <input type="text" class="form-control" id="input2" name="input2" placeholder="Ingrese dato 2" required>
                    <small id="result2" class="text-muted"></small> <!-- Etiqueta para el resultado -->
                </div>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-primary my-3" type="submit">Siguiente</button>
            </div>
        </form>
    </div>
</div>
<br>
    
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

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input1 = document.getElementById('input1');
            const input2 = document.getElementById('input2');
            const result1 = document.getElementById('result1');
            const result2 = document.getElementById('result2');
        
            // Función para realizar la búsqueda
            const fetchInstitution = (input, result) => {
                const codigo = input.value.trim();
                if (codigo) {
                    fetch(`buscar_institucion.php?codigo=${codigo}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                result.textContent = `Institución: ${data.institucion}`;
                            } else {
                                result.textContent = 'No se encontró ninguna institución.';
                            }
                        })
                        .catch(error => {
                            result.textContent = 'Error en la búsqueda.';
                            console.error('Error:', error);
                        });
                } else {
                    result.textContent = '';
                }
            };
        
            // Escuchar eventos en los inputs
            input1.addEventListener('input', () => fetchInstitution(input1, result1));
            input2.addEventListener('input', () => fetchInstitution(input2, result2));
        });
    </script>
</body>
</html>