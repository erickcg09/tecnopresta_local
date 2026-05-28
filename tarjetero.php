<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4, 5, 7]);

if ($tienellave == false) {
    echo '<script language="javascript">
        alert("No tienes permisos");
        self.location = "index.html";
    </script>';
}
require_once("conexion.php");
$link = $mysqli;
// Configurar el conjunto de caracteres
if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
}

// Procesar búsqueda
$busqueda = "";
$resultados = [];
if (isset($_GET['busqueda'])) {
    $busqueda = mysqli_real_escape_string($link, $_GET['busqueda']);
    $query = "SELECT * FROM tarjetas_ayuda 
              WHERE titulo LIKE '%$busqueda%' OR descripcion LIKE '%$busqueda%'";
    $result = mysqli_query($link, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $resultados[] = $row;
    }
} else {
    // Mostrar todas las tarjetas si no hay búsqueda
    $query = "SELECT * FROM tarjetas_ayuda";
    $result = mysqli_query($link, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $resultados[] = $row;
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
    <title>Tarjetero de Ayuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
            body {
            background-color: #f0f8ff; /* Un azul claro de ejemplo, puedes cambiarlo */
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); /* Opcional: gradiente */
            min-height: 100vh;
        }
        
        .container {
            background-color: rgba(255, 255, 255, 0.9); /* Blanco con 90% de opacidad */
            border-radius: 10px; /* Bordes redondeados para mejor estética */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Sombra sutil */
            padding: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        
        /* Tus estilos existentes... */
        .card {
            transition: transform 0.3s;
            height: 100%;
        }
        .container {
            background-color: transparent;
            padding: 20px;
        }
        .card {
            transition: transform 0.3s;
            height: 100%;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 180px;
            object-fit: cover;
        }
        /* Estilos para el modal de video */
        .modal-video {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 */
            height: 0;
            overflow: hidden;
        }
        .modal-video iframe,
        .modal-video video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        .video-container {
            width: 100%;
            max-height: 70vh;
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
                        <a class="nav-link" href="formulario_menu_principal.html">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>  
                    <li class="nav-item">
                        <a class="nav-link" href="subir_tarjeta.php">
                            <i class="bi bi-credit-card-2-front"></i> Agregar
                        </a>
                    </li> 
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <h1 class="text-center mb-5">Recursos de apoyo para uso de la plataforma</h1>
        <br>
        <div class="accordion mb-4">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDocs">
                📄 Documentación
              </button>
            </h2>
            <div id="collapseDocs" class="accordion-collapse collapse">
              <div class="accordion-body">
                <ul class="list-unstyled">
                  <li>
                    <a 
                      href="descargar.php?archivo=Registro de Involucrados en el Proyecto.pdf" 
                      class="text-decoration-none link-danger"
                    >
                      <i class="bi bi-file-pdf"></i> Registro de Involucrados
                    </a>
                  </li>
                  <li class="mt-2">
                    <a 
                      href="descargar.php?archivo=Circulartecnopresta.pdf" 
                      class="text-decoration-none link-danger"
                    >
                      <i class="bi bi-file-pdf"></i> Directriz DVM-009-05-2022
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>        
        <!-- Barra de búsqueda -->
        <div class="row mb-5">
            <div class="col-md-8 mx-auto">
                <form action="" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="busqueda" placeholder="Buscar por título o descripción..." value="<?php echo htmlspecialchars($busqueda); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Resultados -->
        <?php if (!empty($resultados)): ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($resultados as $tarjeta): ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($tarjeta['miniatura']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($tarjeta['titulo']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($tarjeta['titulo']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($tarjeta['descripcion']); ?></p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <button class="btn btn-outline-primary w-100 ver-video" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#videoModal"
                                        data-video-url="<?php echo htmlspecialchars($tarjeta['url']); ?>"
                                        data-video-title="<?php echo htmlspecialchars($tarjeta['titulo']); ?>">
                                    <i class="bi bi-play-circle"></i> Ver video
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                No se encontraron resultados para tu búsqueda.
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para videos -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="videoModalLabel">Reproductor de video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="video-container" id="videoContainer">
                        <!-- El contenido se insertará dinámicamente aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const videoButtons = document.querySelectorAll('.ver-video');
            const videoModal = document.getElementById('videoModal');
            const videoModalLabel = document.getElementById('videoModalLabel');
            const videoContainer = document.getElementById('videoContainer');
            
            videoButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const videoUrl = this.getAttribute('data-video-url');
                    const videoTitle = this.getAttribute('data-video-title');
                    
                    // Actualizar el título del modal
                    videoModalLabel.textContent = videoTitle;
                    
                    // Limpiar el contenedor primero
                    videoContainer.innerHTML = '';
                    
                    // Detectar si es un video de YouTube
                    if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
                        // Extraer el ID del video de YouTube
                        let videoId = '';
                        if (videoUrl.includes('v=')) {
                            videoId = videoUrl.split('v=')[1];
                            const ampersandPosition = videoId.indexOf('&');
                            if (ampersandPosition !== -1) {
                                videoId = videoId.substring(0, ampersandPosition);
                            }
                        } else if (videoUrl.includes('youtu.be/')) {
                            videoId = videoUrl.split('youtu.be/')[1];
                        }
                        
                        // Construir URL embebida de YouTube
                        const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
                        videoContainer.innerHTML = `
                            <iframe src="${embedUrl}" frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen></iframe>
                        `;
                    } else {
                        // Para videos locales (MP4)
                        videoContainer.innerHTML = `
                            <video controls autoplay style="width:100%">
                                <source src="${videoUrl}" type="video/mp4">
                                Tu navegador no soporta videos HTML5
                            </video>
                        `;
                    }
                });
            });
            
            // Limpiar el contenido cuando se cierra el modal
            videoModal.addEventListener('hidden.bs.modal', function () {
                videoContainer.innerHTML = '';
            });
        });
    </script>
</body>
</html>