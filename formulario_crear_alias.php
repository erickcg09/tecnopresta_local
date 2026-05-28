<?php
session_start();
// Verificación de permisos
$tienellave = isset($_SESSION['tipo']) && in_array($_SESSION['tipo'], [1, 2, 3]);
if (!$tienellave) {
    header("Location: formulario_menu_inventario.html");
    exit();
}

require_once("conexion.php");
$link = $mysqli;

// Obtener datos de sesión
$logusuario = $_SESSION['cedula'] ?? '';
$lognombre = $_SESSION['nombre'] ?? '';
$logcodigo = $_SESSION['codigo'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>TecnoPresta - Gestión de Alias</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .card { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .avatar-img { width: 70px; height: 70px; object-fit: cover; border-radius: 50%; }
        .image-selector { cursor: pointer; transition: transform 0.3s; }
        .image-selector:hover { transform: scale(1.05); }
        .selected-image { border: 3px solid #0d6efd !important; }
        .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 15px; }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="img/logomep2020.png" width="45" height="30" class="me-2">TecnoPresta
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="inventario_mantenimiento.php">
                            <i class="bi bi-tools"></i> Mantenimiento
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

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-6">
                <!-- Formulario -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-4"><i class="bi bi-tags"></i> Crear Nuevo Alias</h3>
                        
                        <form name="fralias" action="guardar_subir_alias.php" method="post">
                            <div class="mb-3">
                                <label for="alias" class="form-label">Nombre del Alias*</label>
                                <input type="text" class="form-control" id="alias" name="alias" required
                                       pattern="[A-Za-záéíóúÁÉÍÓÚñÑ0-9 ]+">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Imagen del Alias*</label>
                                <button type="button" class="btn btn-outline-dark w-100 mb-2" 
                                        data-bs-toggle="modal" data-bs-target="#imagenModal">
                                    <i class="bi bi-images"></i> Seleccionar del catálogo
                                </button>
                                <input type="hidden" name="imagen" id="imagen" required>
                                <!-- Vista previa -->
                                <div class="text-center mt-3" id="imagePreviewContainer" style="display:none;">
                                    <img id="imagePreview" src="" class="img-thumbnail" style="max-width:150px;">
                                    <p class="small text-muted mt-2" id="imageName"></p>
                                </div>
                            </div>
                            
                            <input type="hidden" name="codigo" value="<?= htmlspecialchars($logcodigo) ?>">
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Guardar Alias
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de alias existentes -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-list-check"></i> Alias Registrados</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Imagen</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT alias_id, alias, alias_imagen FROM t_alias 
                                              WHERE codigo = '".mysqli_real_escape_string($link, $logcodigo)."'";
                                    $result = mysqli_query($link, $query);
                                    
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>
                                            <td>'.htmlspecialchars($row['alias_id']).'</td>
                                            <td>'.htmlspecialchars($row['alias']).'</td>
                                            <td><img src="img/alias/'.htmlspecialchars($row['alias_imagen']).'" class="avatar-img"></td>
                                            <td>
                                                <a href="formulario_editar_alias.php?gps='.$row['alias_id'].'" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="eliminar_alias.php?gps='.$row['alias_id'].'" class="btn btn-sm btn-outline-danger" 
                                                   onclick="return confirm(\'¿Eliminar este alias?\')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>';
                                    }
                                    mysqli_close($link);
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 d-none d-lg-block">
                <img src="img/Alias_Mesa de trabajo 1.png" class="img-fluid mt-4" alt="Ilustración">
            </div>
        </div>
    </div>

<!-- Modal de selección de imágenes -->
<div class="modal fade" id="imagenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Catálogo de Imágenes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="image-grid">
                    <?php
                    // Generar imágenes del 002 al 031
                    for ($i = 2; $i <= 31; $i++) {
                        $num = str_pad($i, 3, '0', STR_PAD_LEFT);
                        $imgName = "Alias-$num.png";
                        echo '
                        <div class="image-selector" data-image="'.$imgName.'">
                            <img src="img/alias/'.$imgName.'" class="img-thumbnail" alt="Alias '.$num.'">
                            <div class="text-center small mt-1">'.$num.'</div>
                        </div>';
                    }
                    ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmImageBtn">Seleccionar</button>
            </div>
        </div>
    </div>
</div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function() {
    // Sistema de selección de imágenes
    let selectedImage = '';
    
    $('.image-selector').click(function() {
        $('.image-selector').removeClass('selected-image');
        $(this).addClass('selected-image');
        selectedImage = $(this).data('image');
    });
    
    $('#confirmImageBtn').click(function() {
        if (selectedImage) {
            $('#imagen').val(selectedImage);
            $('#imagePreview').attr('src', 'img/alias/' + selectedImage);
            $('#imageName').text(selectedImage.replace('.png', ''));
            $('#imagePreviewContainer').show();
            
            // Cierra el modal
            bootstrap.Modal.getInstance(document.getElementById('imagenModal')).hide();
        } else {
            alert('Por favor selecciona una imagen del catálogo');
        }
    });
});
    </script>
</body>
</html>