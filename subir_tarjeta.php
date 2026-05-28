<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1]);
if ($tienellave == false){
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "formulario_menu_inventario.html"
    </script>';
}
require_once("conexion.php");
$link = $mysqli;

// Configurar el conjunto de caracteres
if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
}

// Procesar el formulario cuando se envía
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar datos
    $titulo = mysqli_real_escape_string($link, $_POST['titulo'] ?? '');
    $descripcion = mysqli_real_escape_string($link, $_POST['descripcion'] ?? '');
    
    // Procesar archivos
    $nombre_miniatura = '';
    $nombre_video = '';
    
    // Directorios de almacenamiento
    $dir_miniaturas = 'miniaturas/';
    $dir_tutoriales = 'tutoriales/';
    
    // Validar y subir miniatura
    if (isset($_FILES['miniatura']) && $_FILES['miniatura']['error'] === UPLOAD_ERR_OK) {
        $info_miniatura = pathinfo($_FILES['miniatura']['name']);
        $extension_miniatura = strtolower($info_miniatura['extension']);
        
        // Validar que sea una imagen
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($extension_miniatura, $extensiones_permitidas)) {
            // Generar nombre único para evitar colisiones
            $nombre_miniatura = uniqid() . '.' . $extension_miniatura;
            $ruta_miniatura = $dir_miniaturas . $nombre_miniatura;
            
            if (!move_uploaded_file($_FILES['miniatura']['tmp_name'], $ruta_miniatura)) {
                $error = 'Error al subir la miniatura.';
            }
        } else {
            $error = 'Formato de imagen no permitido. Use JPG, PNG o GIF.';
        }
    } else {
        $error = 'Debe seleccionar una imagen para la miniatura.';
    }
    
    // Validar y subir video (si no hay error con la miniatura)
    if (empty($error)) {
        if (isset($_POST['url_video']) && !empty($_POST['url_video'])) {
            // Es una URL externa (YouTube u otro)
            $nombre_video = mysqli_real_escape_string($link, $_POST['url_video']);
        } elseif (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            // Es un video local
            $info_video = pathinfo($_FILES['video']['name']);
            $extension_video = strtolower($info_video['extension']);
            
            // Validar que sea un video MP4
            if ($extension_video === 'mp4') {
                // Generar nombre único para evitar colisiones
                $nombre_video = uniqid() . '.mp4';
                $ruta_video = $dir_tutoriales . $nombre_video;
                
                if (!move_uploaded_file($_FILES['video']['tmp_name'], $ruta_video)) {
                    $error = 'Error al subir el video.';
                } else {
                    $nombre_video = $dir_tutoriales . $nombre_video; // Guardamos la ruta completa
                }
            } else {
                $error = 'Solo se permiten videos en formato MP4.';
            }
        } else {
            $error = 'Debe proporcionar un video local o una URL externa.';
        }
    }
    
    // Insertar en la base de datos si no hay errores
    if (empty($error)) {
        $query = "INSERT INTO tarjetas_ayuda (titulo, descripcion, miniatura, url) 
                  VALUES ('$titulo', '$descripcion', '$dir_miniaturas$nombre_miniatura', '$nombre_video')";
        
        if (mysqli_query($link, $query)) {
            $mensaje = 'Tarjeta de ayuda subida correctamente.';
            // Limpiar los campos del formulario
            $titulo = $descripcion = '';
        } else {
            $error = 'Error al guardar en la base de datos: ' . mysqli_error($link);
            // Eliminar archivos subidos si hubo error en la BD
            if (!empty($nombre_miniatura)) unlink($dir_miniaturas . $nombre_miniatura);
            if (!empty($nombre_video) && strpos($nombre_video, 'http') === false) unlink($nombre_video);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <title>Subir Tarjeta de Ayuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .file-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
        .video-option {
            display: none;
        }
        .active-option {
            display: block;
        }
    </style>
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
                        <a class="nav-link" href="tarjetero.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>  
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="form-container bg-white">
            <h2 class="text-center mb-4">Subir Nueva Tarjeta de Ayuda</h2>
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-success"><?php echo $mensaje; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="subir_tarjeta.php" method="POST" enctype="multipart/form-data">
                <!-- Campo Título -->
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título *</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" 
                           value="<?php echo htmlspecialchars($titulo ?? ''); ?>" required>
                </div>
                
                <!-- Campo Descripción -->
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción *</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" 
                              rows="3" required><?php echo htmlspecialchars($descripcion ?? ''); ?></textarea>
                </div>
                
                <!-- Campo Miniatura -->
                <div class="mb-3">
                    <label for="miniatura" class="form-label">Miniatura (Imagen) *</label>
                    <input type="file" class="form-control" id="miniatura" name="miniatura" accept="image/*" required>
                    <small class="text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño recomendado: 600x400px</small>
                    <img id="miniaturaPreview" src="#" alt="Vista previa de la miniatura" class="file-preview img-thumbnail">
                </div>
                
                <!-- Selección de tipo de video -->
                <div class="mb-3">
                    <label class="form-label">Tipo de video *</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="tipo_video" id="videoLocal" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="videoLocal">Video Local (MP4)</label>
                        
                        <input type="radio" class="btn-check" name="tipo_video" id="videoUrl" autocomplete="off">
                        <label class="btn btn-outline-primary" for="videoUrl">URL Externa (YouTube)</label>
                    </div>
                </div>
                
                <!-- Opción para video local -->
                <div id="localVideoOption" class="video-option active-option mb-3">
                    <label for="video" class="form-label">Video (MP4) *</label>
                    <input type="file" class="form-control" id="video" name="video" accept="video/mp4">
                    <small class="text-muted">Solo se aceptan archivos MP4. Tamaño máximo: 50MB</small>
                </div>
                
                <!-- Opción para URL externa -->
                <div id="urlVideoOption" class="video-option mb-3">
                    <label for="url_video" class="form-label">URL del Video (YouTube) *</label>
                    <input type="url" class="form-control" id="url_video" name="url_video" 
                           placeholder="https://www.youtube.com/watch?v=...">
                    <small class="text-muted">Ejemplo: https://www.youtube.com/watch?v=ABCD1234</small>
                </div>
                
                <!-- Botón de envío -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Subir Tarjeta de Ayuda</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Vista previa de la miniatura
        document.getElementById('miniatura').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('miniaturaPreview');
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Cambiar entre opciones de video
        document.querySelectorAll('input[name="tipo_video"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('localVideoOption').classList.remove('active-option');
                document.getElementById('urlVideoOption').classList.remove('active-option');
                
                if (this.id === 'videoLocal') {
                    document.getElementById('localVideoOption').classList.add('active-option');
                    document.getElementById('video').setAttribute('required', '');
                    document.getElementById('url_video').removeAttribute('required');
                } else {
                    document.getElementById('urlVideoOption').classList.add('active-option');
                    document.getElementById('url_video').setAttribute('required', '');
                    document.getElementById('video').removeAttribute('required');
                }
            });
        });
        
        // Validación antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const tipoVideo = document.querySelector('input[name="tipo_video"]:checked').id;
            const hayVideoLocal = tipoVideo === 'videoLocal' && document.getElementById('video').files.length > 0;
            const hayUrlVideo = tipoVideo === 'videoUrl' && document.getElementById('url_video').value.trim() !== '';
            
            if (!hayVideoLocal && !hayUrlVideo) {
                e.preventDefault();
                alert('Debe proporcionar un video local o una URL externa según la opción seleccionada.');
            }
        });
    </script>
</body>
</html>