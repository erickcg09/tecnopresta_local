<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Entregas Fonatel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center header-title mb-4">Consulta de Entregas Fonatel</h1>
                
                <!-- Card para formulario de búsqueda -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Búsqueda por Código</h5>
                    </div>
                    <div class="card-body">
                        <form id="searchForm" method="GET" action="">
                            <div class="row g-3">
                                <div class="col-md-8 offset-md-2">
                                    <label for="codigo" class="form-label">Código</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" 
                                           value="<?php echo isset($_GET['codigo']) ? htmlspecialchars($_GET['codigo']) : ''; ?>" 
                                           placeholder="Ingrese el código a buscar" autofocus>
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
                            // Verificar si la conexión se estableció correctamente
                            if (!$link) {
                                echo "<div class='alert alert-danger'>Error de conexión a MySQL: No se pudo conectar a la base de datos</div>";
                            } else {
                                // Verificar si hay errores de conexión
                                if (mysqli_connect_errno()) {
                                    echo "<div class='alert alert-danger'>Error de conexión a MySQL: " . mysqli_connect_error() . "</div>";
                                } else {
                                    // Establecer charset
                                    if (!mysqli_set_charset($link, "utf8")) {
                                        echo "<div class='alert alert-warning'>Error cargando el conjunto de caracteres utf8</div>";
                                    }
                                    
                                    // Construir consulta SQL basada en el filtro de código
                                    $sql = "SELECT * FROM t_entrega_fonatel WHERE 1=1";
                                    $params = array();
                                    $types = "";
                                    
                                    if (isset($_GET['codigo']) && !empty($_GET['codigo'])) {
                                        $sql .= " AND codigo LIKE ?";
                                        $params[] = '%' . $_GET['codigo'] . '%';
                                        $types .= "s";
                                    }
                                    
                                    $sql .= " ORDER BY id_entrega DESC";
                                    
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
                                            echo "<table class='table table-striped table-bordered table-hover'>";
                                            echo "<thead class='table-dark'><tr>";
                                            echo "<th>ID Entrega</th>";
                                            echo "<th>Placa</th>";
                                            echo "<th>Serie</th>";
                                            echo "<th>Código</th>";
                                            echo "<th>Recibido</th>";
                                            echo "</tr></thead>";
                                            echo "<tbody>";
                                            
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                echo "<td>" . (isset($row['id_entrega']) ? htmlspecialchars($row['id_entrega']) : '') . "</td>";
                                                echo "<td>" . (isset($row['placa']) ? htmlspecialchars($row['placa']) : '') . "</td>";
                                                echo "<td>" . (isset($row['serie']) ? htmlspecialchars($row['serie']) : '') . "</td>";
                                                echo "<td>" . (isset($row['codigo']) ? htmlspecialchars($row['codigo']) : '') . "</td>";
                                                echo "<td>" . (isset($row['recibido']) ? htmlspecialchars($row['recibido']) : '') . "</td>";
                                                echo "</tr>";
                                            }
                                            
                                            echo "</tbody></table>";
                                        } else {
                                            if (isset($_GET['codigo']) && !empty($_GET['codigo'])) {
                                                echo "<div class='alert alert-info'>No se encontraron resultados para el código: " . htmlspecialchars($_GET['codigo']) . "</div>";
                                            } else {
                                                echo "<div class='alert alert-info'>Ingrese un código para realizar la búsqueda.</div>";
                                            }
                                        }
                                        
                                        // Cerrar statement
                                        mysqli_stmt_close($stmt);
                                    } else {
                                        echo "<div class='alert alert-danger'>Error en la preparación de la consulta: " . mysqli_error($link) . "</div>";
                                    }
                                    
                                    // Cerrar conexión
                                    mysqli_close($link);
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Enfocar el campo de código al cargar la página
            $('#codigo').focus();
            
            // Limpiar el formulario
            $('.btn-clear').on('click', function() {
                $('#codigo').val('').focus();
            });
        });
    </script>
</body>
</html>