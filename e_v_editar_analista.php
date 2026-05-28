<?php 
  
  session_start();
  if (!isset($_POST['id_visita'])) {
    //die("ID de visita no recibido.");
  } else
  {   
  $_SESSION['id_visita'] = (int) $_POST['id_visita'];
  }
  
  require_once 'e_editar_analista.php';

  function caracteres($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');

}

?>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>-->

<!-- <link href="css/all.min.css" rel="stylesheet">
<link href="css/styles.css" rel="stylesheet"> -->

<!doctype html>
<html lang="en">
    <head>
        
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- <link rel="icon" href="icons/favicon.ico" type="image/x-icon"> -->
        <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
        <!-- Bootstrap CSS -->
        <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <!-- Bootstrap Icons CSS -->
         <link rel="stylesheet" href="css/bootstrap-icons.css"> <!--este es de los iconos de la lista -->
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"> -->
        <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"> -->
        
        <link rel="stylesheet" href="css/tom-select.css">
        <!-- <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet"> -->
        <style>
            /* Estilo y formato del Tom Select */
            .ts-control {
                font-size: 1rem;
                min-height: 46px;
                padding: 6px 10px;
            }

            .ts-control .item {
                background-color: #e9f2fb;
                color: #0d6efd;
                border-radius: 20px;
                padding: 5px 10px;
                margin: 3px;
                font-weight: 450;
            }

            .ts-control .item .remove {
                margin-left: 8px;
                color: #dc3545;
                font-size: 1rem;
                transition: all 0.2s ease;
            }

            .ts-control .item .remove:hover {
                color: #a71d2a;
                transform: scale(1.2);
            }

            .ts-wrapper.focus .ts-control {
                border-color: #0d6efd;
                box-shadow: 0 0 0 .2rem rgba(13,110,253,.25);
            }

            .ts-dropdown {
                font-size: 0.95rem;
            }

        </style>

        <title>Editar Profesional</title>
        
    </head>

    <body>
        
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="formulario_menu_principal.html">
                    <i class="bi bi-laptop"></i> Tecnopresta
                </a>
                <!-- Botón para móviles (actualizado a BS5) -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Menú colapsable -->
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="administracion_plataforma_soporte.php">
                                <i class="bi bi-arrow-left-circle"></i> Regresar
                            </a>
                        </li>   
                        <li class="nav-item">
                            <a class="nav-link" href="afinidades.php">
                                <i class="bi bi-person-vcard"></i> Afinidades
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <br>
            <div class="row">
                <div class="col-md-4">
                
                </div>
                <div class="col-md-6">
                    <form action="" method="post">
                        <div class="card shadow" style="background-color: #f8f9fa;">
                            <div class="card-header bg-dark text-white text-center py-2">
                                <h5 class="mb-0"><i class="bi bi-people"></i>  Profesionales Asignados</h5>  

                            </div>
                            <div class="card-body">                                
                                    <!-- Div para mostrar el mensaje de error -->
                                     <?php if (!empty($mensajeError)): ?>
                                        <div class="alert alert-warning alert-dismissible fade show position-fixed top-0 end-0 m-4 shadow"
                                            style="z-index: 1055; min-width: 350px;"
                                            role="alert"
                                            id="mensajeAlerta">
                                            <i class="bi bi-exclamation-triangle-fill"></i> <?= $mensajeError; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                        <?php endif; ?>
                                    
                                    <!-- Div para mostrar el mensaje -->
                                     <?php if (!empty($mensaje)): ?>
                                        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-4 shadow"
                                            style="z-index: 1055; min-width: 350px;"
                                            role="alert"
                                            id="mensajeOk">
                                            <i class="bi bi-check-circle-fill"></i> <?= $mensaje; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                        <?php endif; ?>

                                    <!-- <label for="visita_id" class="form-label">visita_id</label> -->
                                    <input
                                        type="hidden"
                                        class="form-control"
                                        name="visita_id"
                                        id="visita_id"    
                                        value="<?= $id_visita ?>"                                    
                                    />

                                <div class="mb-3">
                                    <label for="" class="form-label">Asunto</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="asunto"
                                        id="asunto"                                      
                                        value="<?= caracteres($asunto); ?>"
                                        aria-describedby="helpId"
                                        placeholder="Asunto"
                                    /> <!-- <?php //echo $asunto; ?> es lo mismo <?//= $asunto; ?> -->
                                </div>
                                
                                <div class="mb-3">
                                    <label for="" class="form-label">Descripción del Problema</label>                                    
                                    <textarea class="form-control"
                                        name="problema"
                                        id="problema"
                                        rows="3"
                                        placeholder="Describa el problema"><?=caracteres(trim($problema));?></textarea>                                    
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">Lista de Profesionales</label>
                                    <select multiple class="form-select form-select-md" name="profesionales[]" id="listaprofesionales">                                        
                                        <?php                                                                                         
                                            $asignados = $profesionalesSeleccionados;
                                            // Muestra la lista completa de profesionales
                                            foreach($listaProf as $prof) {                                                
                                                $selected = in_array($prof['id_analista'], $asignados) ? 'selected' : '';                                                
                                        ?>
                                        <option value="<?= $prof['id_analista']; ?>" <?=$selected; ?>> 
                                            <?= caracteres($prof['nombre']); ?>
                                        </option>;
                                        <?php }  ?>
    
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer text-muted bg-dark text-center text-white py-2">
                            <!-- <div class="card-footer d-flex justify-content-end gap-3"> -->
                                <!-- <div class="btn-group" role="group" aria-label="Button group name">-->
                                    <button type="submit" name="accion" value="guardar" data-bs-toggle="tooltip" title="Guardar cambios en la visita" class="btn btn-outline-primary btn-lg"><i class="bi bi-save"></i>  Guardar</button>
                                    <button type="submit" name="accion" value="cancelar" data-bs-toggle="tooltip" title="Cancelar los cambios" class="btn btn-outline-secondary btn-lg"><i class ="bi bi-x-circle"></i>  Cancelar</button>
                                    <button type="submit" name="accion" value="regresar" data-bs-toggle="tooltip" title="Regresar sin guardar cambios" class="btn btn-outline-warning btn-lg"><i class="bi bi-arrow-left"></i>  Regresar</button>
                                <!-- </div>  -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Para mostar el comentario o tooltips en una burbuja -->
        <!-- <script src="js/bootstrap.bundle.min.js"></script> -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        // Cerrar las Alertas de manera automática
        setTimeout(() => {
            const alert = document.getElementById('mensajeAlerta');
            const alertOk = document.getElementById('mensajeOk');
            if (alert) {
                new bootstrap.Alert(alert).close();
            }
            if (alertOk) {
                new bootstrap.Alert(alertOk).close();
            }
        }, 4000);
        </script>

        <script>
        // Activar tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        </script>  
        <!-- Tom Select ---->
        <script src="js/tom-select.complete.min.js"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>-->

        <script>    
            new TomSelect('#listaprofesionales',{
            plugins: {
                remove_button:{
                    title:'Quitar funcionario',
                    label: '<i class="bi bi-x-circle-fill"></i>'
                }
            },
            persist: false,
            create: true,
            onDelete: function(values) {
                return confirm(values.length > 1 
                    ? '¿Desea remover estos ' + values.length + ' funcionarios de la lista?' 
                    : '¿Desea remover este funcionario de la lista?');
            }
        });

        </script>
    </body>

</html>
