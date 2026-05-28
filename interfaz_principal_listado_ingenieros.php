<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
</script>';
}
require_once("conexion.php");
$link = $mysqli;
if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
}
// Consulta para obtener todos los analistas con el nombre de la regional
$query = "SELECT a.id_analista, a.nombre, a.cedula, a.foto, a.id_regional, r.regional as nombre_regional 
          FROM t_analista a 
          JOIN t_regional r ON a.id_regional = r.id_regional
          ORDER BY a.nombre";
$result = mysqli_query($link, $query);

// Consulta para obtener todas las regionales para el filtro
$query_regionales = "SELECT id_regional, regional FROM t_regional ORDER BY id_regional";
$result_regionales = mysqli_query($link, $query_regionales);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ingenieros</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .card-img-top {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin: 0 auto;
            display: block;
        }
        .rating {
            font-size: 1.2rem;
            color: gold;
        }
        .card {
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .badge-regional {
            background-color: #6c757d;
            color: white;
            font-size: 0.8rem;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            vertical-align: middle;
        }
        /* Estilo para el campo de búsqueda con botón de limpieza */
        #buscarNombre {
            padding-right: 30px;
            position: relative;
        }
        
        .clear-search {
            z-index: 10;
        }
        
        .clear-search:hover {
            opacity: 0.8;
        }
        
        /* Estilo para el mensaje de no resultados */
        #noResults {
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="formulario_menu_principal.html">
                <i class="bi bi-laptop"></i> Tecnopresta
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="administracion_plataforma_soporte.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formulario_agregar_analistas.php">
                            <i class="bi bi-person-plus"></i> Agregar nuevo
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center mb-4">Editar Ingenieros</h2>
        
        <!-- Filtro por regional y búsqueda -->
        <div class="row mb-4">
            <div class="col-md-4">
                <select class="form-select" id="filtroRegional">
                    <option value="">Todas las regionales</option>
                    <?php while($regional = $result_regionales->fetch_assoc()): ?>
                        <option value="<?php echo $regional['id_regional']; ?>">
                            <?php echo htmlspecialchars($regional['regional']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-8 position-relative">
                <input type="text" class="form-control" id="buscarNombre" placeholder="Buscar por nombre o cédula...">
            </div>
        </div>
        <?php
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                '.htmlspecialchars($_GET['success']).'
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
        
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                '.htmlspecialchars($_GET['error']).'
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
        ?>
        <!-- Listado de ingenieros -->
        <div class="row" id="listaIngenieros">
            <?php while($analista = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?php echo htmlspecialchars($analista['foto']); ?>" 
                         class="card-img-top mt-3" 
                         alt="Foto de <?php echo htmlspecialchars($analista['nombre']); ?>"
                         onerror="this.src='img/default-user.png'">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($analista['nombre']); ?></h5>
                        <p class="card-text">
                            <small class="text-muted"><?php echo htmlspecialchars($analista['cedula']); ?></small><br>
                            <span class="badge-regional badge" title="<?php echo htmlspecialchars($analista['nombre_regional']); ?>">
                                <?php echo htmlspecialchars($analista['nombre_regional']); ?>
                            </span>
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="editar_analista.php?id=<?php echo $analista['id_analista']; ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                            <a href="eliminar_analista.php?id=<?php echo $analista['id_analista']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('¿Está seguro de eliminar este ingeniero?');">
                                <i class="bi bi-trash"></i> Eliminar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Filtrado dinámico por regional y búsqueda
            $('#filtroRegional, #buscarNombre').on('change keyup', function() {
                let regional = $('#filtroRegional').val();
                let searchTerm = $('#buscarNombre').val().toLowerCase().trim();
                
                $('.col-md-4').each(function() {
                    let card = $(this);
                    let cardRegionalId = card.find('a[href*="editar_analista.php"]').attr('href').split('id=')[1];
                    let cardNombre = card.find('.card-title').text().toLowerCase();
                    let cardCedula = card.find('.text-muted').text().toLowerCase();
                    
                    // Verificar coincidencia con la regional seleccionada
                    let regionalMatch = (regional === "" || cardRegionalId == regional);
                    
                    // Verificar coincidencia con el término de búsqueda (nombre o cédula)
                    let searchMatch = (searchTerm === "" || 
                                     cardNombre.includes(searchTerm) || 
                                     cardCedula.includes(searchTerm));
                    
                    // Mostrar u ocultar según los filtros
                    card.toggle(regionalMatch && searchMatch);
                });
                
                // Opcional: Mostrar mensaje si no hay resultados
                if ($('.col-md-4:visible').length === 0) {
                    $('#noResults').remove();
                    $('#listaIngenieros').append(
                        '<div id="noResults" class="col-12 text-center py-4 text-muted">' +
                        'No se encontraron ingenieros que coincidan con los criterios de búsqueda' +
                        '</div>'
                    );
                } else {
                    $('#noResults').remove();
                }
            });
            
            // Opcional: Limpiar búsqueda con botón o icono
            $('#buscarNombre').on('input', function() {
                if ($(this).val().length > 0) {
                    $(this).next('.clear-search').show();
                } else {
                    $(this).next('.clear-search').hide();
                }
            });
            
            // Opcional: Añadir botón para limpiar búsqueda
            $('#buscarNombre').after(
                '<span class="clear-search" style="display:none; position:absolute; right:15px; top:50%; transform:translateY(-50%); cursor:pointer;">' +
                '<i class="bi bi-x-circle-fill text-secondary"></i>' +
                '</span>'
            );
            
            $('.clear-search').on('click', function() {
                $('#buscarNombre').val('').trigger('keyup');
                $(this).hide();
            });
        });
    </script>
    <script>
        $(document).on('click', '.btn-danger', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const nombre = $(this).closest('.card').find('.card-title').text();
            
            Swal.fire({
                title: '¿Eliminar ingeniero?',
                html: `Estás a punto de eliminar a <b>${nombre}</b>. Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });        
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</body>
</html>