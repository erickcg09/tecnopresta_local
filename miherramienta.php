<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Placas</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }
        .table-container {
            overflow-x: auto;
        }
        .btn-search {
            background-color: #0d6efd;
            color: white;
        }
        .btn-clear {
            background-color: #6c757d;
            color: white;
        }
        .header-title {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center header-title mb-4">Sistema de Consulta de Placas</h1>
                
                <!-- Card para formulario de búsqueda -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Filtros de Búsqueda</h5>
                    </div>
                    <div class="card-body">
                        <form id="searchForm" method="GET" action="">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="codigo" class="form-label">Código</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" 
                                           value="<?php echo isset($_GET['codigo']) ? htmlspecialchars($_GET['codigo']) : ''; ?>" 
                                           placeholder="Ej: 0328">
                                </div>
                                <div class="col-md-3">
                                    <label for="placa" class="form-label">Placa</label>
                                    <input type="text" class="form-control" id="placa" name="placa" 
                                           value="<?php echo isset($_GET['placa']) ? htmlspecialchars($_GET['placa']) : ''; ?>" 
                                           placeholder="Ej: ABC123">
                                </div>
                                <div class="col-md-3">
                                    <label for="serial" class="form-label">Serial</label>
                                    <input type="text" class="form-control" id="serial" name="serial" 
                                           value="<?php echo isset($_GET['serial']) ? htmlspecialchars($_GET['serial']) : ''; ?>" 
                                           placeholder="Ej: S123456">
                                </div>
                                <div class="col-md-3">
                                    <label for="id_fondos" class="form-label">ID Fondos</label>
                                    <input type="number" class="form-control" id="id_fondos" name="id_fondos" 
                                           value="<?php echo isset($_GET['id_fondos']) ? htmlspecialchars($_GET['id_fondos']) : ''; ?>" 
                                           placeholder="Ej: 123">
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-search me-2">
                                        <i class="bi bi-search"></i> Buscar
                                    </button>
                                    <a href="?" class="btn btn-clear">
                                        <i class="bi bi-x-circle"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card para resultados -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Resultados de la Búsqueda</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <?php
                            // Incluir archivo de conexión
                            require_once("conexion.php");
                            $link = $mysqli;
                            // Verificar conexión
                            if (mysqli_connect_errno()) {
                                echo "<div class='alert alert-danger'>Error de conexión a MySQL: " . mysqli_connect_error() . "</div>";
                            } else {
                                // Establecer charset
                                if (!mysqli_set_charset($link, "utf8")) {
                                    echo "<div class='alert alert-warning'>Error cargando el conjunto de caracteres utf8</div>";
                                }
                                
                                // Construir consulta SQL basada en los filtros
                                $sql = "SELECT * FROM t_placa WHERE 1=1";
                                $params = array();
                                $types = "";
                                
                                if (isset($_GET['codigo']) && !empty($_GET['codigo'])) {
                                    $sql .= " AND codigo LIKE ?";
                                    $params[] = '%' . $_GET['codigo'] . '%';
                                    $types .= "s";
                                }
                                
                                if (isset($_GET['placa']) && !empty($_GET['placa'])) {
                                    $sql .= " AND placa LIKE ?";
                                    $params[] = '%' . $_GET['placa'] . '%';
                                    $types .= "s";
                                }
                                
                                if (isset($_GET['serial']) && !empty($_GET['serial'])) {
                                    $sql .= " AND serial LIKE ?";
                                    $params[] = '%' . $_GET['serial'] . '%';
                                    $types .= "s";
                                }
                                
                                if (isset($_GET['id_fondos']) && !empty($_GET['id_fondos'])) {
                                    $sql .= " AND id_fondos = ?";
                                    $params[] = $_GET['id_fondos'];
                                    $types .= "i";
                                }
                                
                                $sql .= " ORDER BY id_placa DESC";
                                
                                // Preparar la consulta
                                if ($stmt = mysqli_prepare($link, $sql)) {
                                    // Vincular parámetros si existen
                                    if (!empty($params)) {
                                        mysqli_stmt_bind_param($stmt, $types, ...$params);
                                    }
                                    
                                    // Ejecutar consulta
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    
                                    // Verificar si hay resultados
                                    if (mysqli_num_rows($result) > 0) {
                                        echo "<table id='resultsTable' class='table table-striped table-bordered table-hover'>";
                                        echo "<thead class='table-dark'><tr>";
                                        echo "<th>ID</th>";
                                        echo "<th>Código</th>";
                                        echo "<th>Placa</th>";
                                        echo "<th>Serial</th>";
                                        echo "<th>ID Activo</th>";
                                        echo "<th>ID Estado</th>";
                                        echo "<th>ID Fondos</th>";
                                        echo "<th>Acciones</th>";
                                        echo "</tr></thead>";
                                        echo "<tbody>";
                                        
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['id_placa']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['codigo']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['placa']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['serial']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['id_activo']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['id_estado']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['id_fondos']) . "</td>";
                                            echo "<td>
                                                    <button class='btn btn-sm btn-info view-details' data-id='" . $row['id_placa'] . "'>Ver Detalles</button>
                                                  </td>";
                                            echo "</tr>";
                                        }
                                        
                                        echo "</tbody></table>";
                                    } else {
                                        echo "<div class='alert alert-info'>No se encontraron resultados con los filtros aplicados.</div>";
                                    }
                                    
                                    // Cerrar statement
                                    mysqli_stmt_close($stmt);
                                } else {
                                    echo "<div class='alert alert-danger'>Error en la preparación de la consulta: " . mysqli_error($link) . "</div>";
                                }
                                
                                // Cerrar conexión
                                mysqli_close($link);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para detalles -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailsModalLabel">Detalles Completo del Registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDetailsContent">
                    <!-- Los detalles se cargarán aquí mediante AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTables
            $('#resultsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50]
            });
            
            // Manejar clic en botón de ver detalles
            $('.view-details').on('click', function() {
                var id = $(this).data('id');
                
                // Realizar solicitud AJAX para obtener detalles completos
                $.ajax({
                    url: 'get_details.php',
                    type: 'GET',
                    data: {id: id},
                    success: function(response) {
                        $('#modalDetailsContent').html(response);
                        $('#detailsModal').modal('show');
                    },
                    error: function() {
                        $('#modalDetailsContent').html('<div class="alert alert-danger">Error al cargar los detalles.</div>');
                        $('#detailsModal').modal('show');
                    }
                });
            });
        });
    </script>
</body>
</html>