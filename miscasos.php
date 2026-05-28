<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "index.html"
    </script>';
}
date_default_timezone_set('America/Costa_Rica');
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
$loginstitucion = $_SESSION['dependencia'];
$logcorreo = $_SESSION['correomep'];
$estatus = "Abierto";
$tomado = "Si";
?>
<!doctype html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>

    <title>Casos que has aceptado</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">TecnoPresta</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link" aria-current="page" href="panel_soporte.php">
                        <i class="bi bi-arrow-left-circle"></i> Regresar
                    </a>
                    <a class="nav-link" href="gameover.php">
                        <i class="bi bi-door-open"></i> Cerrar sesi&oacute;n
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <img src="img/miscasos.png" class="img-fluid w-75" alt="Casos asignados">
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Funcionario</th>
                            <th>Instituci&oacute;n</th>
                            <th>Asunto</th>
                            <th>Problema</th>
                            <th colspan="3" class="text-center">Detalles de Acciones</th> <!-- Cambiado a colspan="3" -->
                        </tr>
                    </thead>
                    <tbody class="BusquedaRapida">
                        <?php
                        $consulta=mysqli_query($link,"SELECT id, funcionario, institucion, placa, problema, correo
                        FROM soporte
                        WHERE cedulatecnico='$logusuario' AND estatus='$estatus'
                        ORDER BY fecha ASC") or die(mysqli_error($link));
                
                        while ($row=mysqli_fetch_array($consulta)) { ?>
                        <tr>
                            <td><?php echo $row["funcionario"]." <br> ".$row["correo"] ?></td>
                            <td><?php echo $row['institucion']?></td>
                            <td><?php echo $row['placa']?></td>
                            <td><?php echo $row['problema']?></td>
                            <td>
                              <input 
                                type="button" 
                                name="view" 
                                value="Mensaje&#10;Adjunto" 
                                id="<?php echo $row['id']; ?>" 
                                class="btn btn-primary view_data" 
                                data-bs-toggle="modal" 
                                data-bs-target="#exampleModal" 
                                data-bs-whatever="@mdo" 
                                style="white-space: pre; height: auto; padding: 5px 10px; width: auto;"
                              />
                            </td>
                            <td><button type="button" class="btn btn-primary" id="btnmodal" data-bs-toggle="modal" data-bs-target="#solucioncierre" data-bs-whatever="@mdo" data-ids="<?php echo $row['id']; ?>">Soluci&oacute;n y Cierre</button></td>
                            <!-- Nuevo botón "a servicio" -->
                            <td>
                                <button 
                                    type="button" 
                                    class="btn btn-warning btn-servicio" 
                                    data-id="<?php echo $row['id']; ?>"
                                >
                                    a servicio
                                </button>
                            </td>
                        </tr>
                        <?php }
                        mysqli_close($link);    
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para mensajes -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Mensaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="enviar_chat_soporte.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body" id="soporte_detalles">
                        <!-- Aqu赤 se cargan los campos mediante ajax -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para soluci車n y cierre -->
    <div class="modal fade" id="solucioncierre" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Soluci&oacute;n & Cierre de Caso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="solucion_cerrar_caso.php" method="post">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="estatus" value="Cerrado">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Cerrar el Caso</label>
                        </div>
                        <div class="mb-3">
                            <input id="idsolu" name="id_solucion" type="hidden">
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Soluci&oacute;n:</label>
                            <textarea class="form-control" id="message-text" name="solucion"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>  
    $(document).ready(function(){
        $(document).on('click', '.view_data', function(){
            var soporte_id = $(this).attr("id");
            $.ajax({
                url:"precargar.php",
                method:"POST",
                data:{soporte_id:soporte_id},
                success:function(data){
                    $('#soporte_detalles').html(data);
                    $('#exampleModal').modal('show');
                }
            });
        });
    });  
    </script>
    <script>
        $(document).on("click", "#btnmodal", function(){
            var idsolucion = $(this).data('ids');
            $("#idsolu").val(idsolucion);
        })
    </script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los botones "a servicio"
    const botonesServicio = document.querySelectorAll('.btn-servicio');
    
    botonesServicio.forEach(boton => {
        boton.addEventListener('click', function() {
            const idCaso = this.getAttribute('data-id');
            
            // Confirmar con el usuario
            if (confirm('¿De verdad quiere referir este caso a servicio técnico?')) {
                // Crear formulario dinámico para enviar por POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'pasar_caso_a_servicio_tecnico.php';
                form.style.display = 'none';
                
                // Crear input para el ID
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = idCaso;
                
                // Agregar input al formulario
                form.appendChild(inputId);
                
                // Agregar formulario al body y enviar
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>

    <!-- Footer -->
<?php
session_start();

if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
    unset($_SESSION['exito']);
    ?>
    <div class="container mt-4">
        <div class="alert alert-success">
            <h4><?php echo htmlspecialchars($exito['titulo']); ?></h4>
            <p><?php echo htmlspecialchars($exito['mensaje']); ?></p>
            <?php if (!empty($exito['detalles'])): ?>
                <ul>
                <?php foreach ($exito['detalles'] as $key => $value): ?>
                    <li><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>

<footer class="bg-light text-center text-lg-start mt-5">
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        TecnoPresta es realizado por gente MEP, para la gente del MEP.
    </div>
</footer>
</body>
</html>