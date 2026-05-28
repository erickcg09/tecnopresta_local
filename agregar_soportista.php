<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte con el administrador.");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];

// Inicializar array de soportista con valores por defecto para horarios
$soportista = [
    'nombre' => '',
    'correo' => '',
    'hora_inicio' => '07:00:00',
    'hora_fin' => '15:00:00',
    'activo' => 1
];
$edicion = false;

// Procesar edición si hay ID
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $edicion = true;
    $id = mysqli_real_escape_string($link, $_GET['id']);
    $query = "SELECT * FROM soportistas WHERE id = '$id'";
    $result = mysqli_query($link, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $soportista = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['mensaje'] = [
            'tipo' => 'danger', 
            'texto' => 'Soportista no encontrado'
        ];
        header("Location: index.html");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edicion ? 'Editar' : 'Agregar' ?> Soportista</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .time-input-group {
            display: flex;
            align-items: center;
        }
        .time-input-group .form-control {
            max-width: 120px;
            margin-right: 10px;
        }
        .time-separator {
            margin: 0 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
                        <a class="nav-link" href="citas_soportistas.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>   
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="bi bi-person-plus"></i> <?= $edicion ? 'Editar Soportista' : 'Agregar Nuevo Soportista' ?></h4>
                    </div>
                    <div class="card-body">
                        <form action="guardar_soportista.php" method="POST" id="form-soportista">
                            <?php if ($edicion): ?>
                                <input type="hidden" name="id" value="<?= $soportista['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($soportista['nombre']) ?>" required
                                       placeholder="Ej: Juan Pérez López">
                                <div class="invalid-feedback">Por favor ingrese el nombre del soportista</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" 
                                       value="<?= htmlspecialchars($soportista['correo']) ?>" required
                                       placeholder="Ej: usuario@mep.go.cr">
                                <div class="invalid-feedback">Por favor ingrese un correo válido</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Horario de atención</label>
                                <div class="time-input-group">
                                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" 
                                           value="<?= substr($soportista['hora_inicio'], 0, 5) ?>" required
                                           step="900"> <!-- 15 minutos steps -->
                                    <span class="time-separator">a</span>
                                    <input type="time" class="form-control" id="hora_fin" name="hora_fin" 
                                           value="<?= substr($soportista['hora_fin'], 0, 5) ?>" required
                                           step="900">
                                </div>
                                <small class="text-muted">Seleccione el horario laboral del soportista</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Días de atención</label>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dias_atiende[]" value="1" id="lunes" 
                                           <?= isset($soportista['dias_atiende']) && strpos($soportista['dias_atiende'], '1') !== false ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="lunes">Lunes</label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dias_atiende[]" value="2" id="martes"
                                           <?= isset($soportista['dias_atiende']) && strpos($soportista['dias_atiende'], '2') !== false ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="martes">Martes</label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dias_atiende[]" value="3" id="miercoles"
                                           <?= isset($soportista['dias_atiende']) && strpos($soportista['dias_atiende'], '3') !== false ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="miercoles">Miércoles</label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dias_atiende[]" value="4" id="jueves"
                                           <?= isset($soportista['dias_atiende']) && strpos($soportista['dias_atiende'], '4') !== false ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="jueves">Jueves</label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dias_atiende[]" value="5" id="viernes"
                                           <?= isset($soportista['dias_atiende']) && strpos($soportista['dias_atiende'], '5') !== false ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="viernes">Viernes</label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dias_atiende[]" value="6" id="sabado"
                                           <?= isset($soportista['dias_atiende']) && strpos($soportista['dias_atiende'], '6') !== false ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="sabado">Sábado</label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dias_atiende[]" value="7" id="domingo"
                                           <?= isset($soportista['dias_atiende']) && strpos($soportista['dias_atiende'], '7') !== false ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="domingo">Domingo</label>
                                </div>
                                
                                <small class="text-muted">Seleccione los días que atiende este soportista</small>
                            </div>
                            
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="activo" name="activo" 
                                       <?= $soportista['activo'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activo">Soportista activo</label>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="citas_soportistas.php" class="btn btn-secondary">
                                    <i class="bi bi-x-lg"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('form-soportista').addEventListener('submit', function(e) {
            let valid = true;
            const nombre = document.getElementById('nombre').value.trim();
            const correo = document.getElementById('correo').value.trim();
            const horaInicio = document.getElementById('hora_inicio').value;
            const horaFin = document.getElementById('hora_fin').value;
            
            // Validar nombre
            if (nombre.length < 3) {
                document.getElementById('nombre').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('nombre').classList.remove('is-invalid');
            }
            
            // Validar correo
            if (!correo.includes('@') || correo.length < 5) {
                document.getElementById('correo').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('correo').classList.remove('is-invalid');
            }
            
            // Validar horarios
            if (horaInicio >= horaFin) {
                alert('La hora de inicio debe ser anterior a la hora de fin');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });

        // Validación en tiempo real para horarios
        document.getElementById('hora_fin').addEventListener('change', function() {
            const inicio = document.getElementById('hora_inicio').value;
            const fin = this.value;
            
            if (inicio && fin && inicio >= fin) {
                alert('La hora de fin debe ser posterior a la hora de inicio');
                this.value = '';
            }
        });
    </script>
</body>
</html>