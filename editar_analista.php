<?php
require_once("conexion.php");
$link = $mysqli;
if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
}
// Verificar si se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listado_analistas.php");
    exit();
}

$id_analista = intval($_GET['id']);
$error = '';
$success = '';

// Obtener datos del analista 
$analista = [];
$sql = "SELECT a.*, r.regional 
        FROM t_analista a
        LEFT JOIN t_regional r ON a.id_regional = r.id_regional
        WHERE a.id_analista = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id_analista);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: listado_analistas.php");
    exit();
}

$analista = $result->fetch_assoc();
$stmt->close();

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar los datos
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $cedula = htmlspecialchars(trim($_POST['cedula']));
    $id_regional = intval($_POST['id_regional']);
    $kilometros = floatval($_POST['kilometros']);
    $mantenimiento = intval($_POST['mantenimiento']);
    $redes = intval($_POST['redes']);
    $configuracion = intval($_POST['configuracion']);
    $correo_analista = !empty($_POST['correo_analista']) ? htmlspecialchars(trim($_POST['correo_analista'])) : null;
    
    // Validar que estén entre 1 y 5
    $mantenimiento = max(1, min(5, $mantenimiento));
    $redes = max(1, min(5, $redes));
    $configuracion = max(1, min(5, $configuracion));
    
    // Verificar si la cédula ya existe (excepto para el analista actual)
    $sql_verificar = "SELECT id_analista FROM t_analista WHERE cedula = ? AND id_analista != ?";
    $stmt_verificar = $mysqli->prepare($sql_verificar);
    $stmt_verificar->bind_param("si", $cedula, $id_analista);
    $stmt_verificar->execute();
    $stmt_verificar->store_result();
    
    if ($stmt_verificar->num_rows > 0) {
        $error = "Ya existe un analista con la cédula proporcionada.";
    } else {
        // Procesar la imagen si se subió una nueva
        $ruta_foto = $analista['foto'];
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $carpetaDestino = 'img_ingenieros/';
            $nombreArchivo = uniqid() . '_' . basename($_FILES['foto']['name']);
            $nueva_ruta = $carpetaDestino . $nombreArchivo;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $nueva_ruta)) {
                // Eliminar la foto anterior si existe y no es la imagen por defecto
                if ($ruta_foto && $ruta_foto != 'img/default-user.png' && file_exists($ruta_foto)) {
                    unlink($ruta_foto);
                }
                $ruta_foto = $nueva_ruta;
            } else {
                $error = "Error al subir la nueva imagen.";
            }
        }
        
        if (empty($error)) {
            // Consulta UPDATE corregida
            $sql_update = "UPDATE t_analista SET 
                          nombre = ?, 
                          id_regional = ?, 
                          foto = ?, 
                          mantenimiento = ?, 
                          redes = ?, 
                          configuracion = ?, 
                          kilometros = ?, 
                          cedula = ?,
                          correo_analista = ?
                          WHERE id_analista = ?";
            
            $stmt_update = $mysqli->prepare($sql_update);
            if ($stmt_update) {
                // CORRECCIÓN: Tipo de parámetros corregido - 10 parámetros
                // s = string, i = integer, d = double (decimal)
                $stmt_update->bind_param(
                    "sisiiidssi", // 10 caracteres para 10 parámetros
                    $nombre,           // s (string)
                    $id_regional,      // i (integer)
                    $ruta_foto,        // s (string)
                    $mantenimiento,    // i (integer)
                    $redes,            // i (integer)
                    $configuracion,    // i (integer)
                    $kilometros,       // d (double/decimal)
                    $cedula,           // s (string)
                    $correo_analista,  // s (string o null)
                    $id_analista       // i (integer)
                );
                
                if ($stmt_update->execute()) {
                    $success = "Analista actualizado correctamente.";
                    // Actualizar los datos mostrados
                    $analista = array_merge($analista, [
                        'nombre' => $nombre,
                        'cedula' => $cedula,
                        'id_regional' => $id_regional,
                        'kilometros' => $kilometros,
                        'mantenimiento' => $mantenimiento,
                        'redes' => $redes,
                        'configuracion' => $configuracion,
                        'foto' => $ruta_foto,
                        'correo_analista' => $correo_analista
                    ]);
                } else {
                    $error = "Error al actualizar el analista: " . $stmt_update->error;
                }
                $stmt_update->close();
            } else {
                $error = "Error preparando la consulta: " . $mysqli->error;
            }
        }
    }
    $stmt_verificar->close();
}

$regionales = [];
$sql_regionales = "SELECT id_regional, regional FROM t_regional ORDER BY regional";
$result_regionales = $mysqli->query($sql_regionales);
if ($result_regionales) {
    $regionales = $result_regionales->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Analista</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .img-preview {
            max-width: 200px;
            max-height: 200px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-select {
            margin-bottom: 15px;
        }
        .skill-label {
            font-weight: 500;
            margin-bottom: 5px;
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="interfaz_principal_listado_ingenieros.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Editar Analista</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?php echo htmlspecialchars($analista['nombre']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cedula" class="form-label">Cédula</label>
                            <input type="text" class="form-control" id="cedula" name="cedula" 
                                   value="<?php echo htmlspecialchars($analista['cedula']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="correo_analista" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo_analista" name="correo_analista" 
                                   value="<?php echo htmlspecialchars($analista['correo_analista'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="id_regional" class="form-label">Regional</label>
                            <select class="form-select" id="id_regional" name="id_regional" required>
                                <option value="">Seleccione una regional</option>
                                <?php foreach ($regionales as $regional): ?>
                                    <option value="<?php echo $regional['id_regional']; ?>"
                                        <?php if ($regional['id_regional'] == $analista['id_regional']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($regional['regional']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="kilometros" class="form-label">Kilómetros</label>
                            <input type="number" step="0.01" class="form-control" id="kilometros" name="kilometros" 
                                   value="<?php echo htmlspecialchars($analista['kilometros']); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Foto actual</label><br>
                            <img src="<?php echo htmlspecialchars($analista['foto']); ?>" 
                                 onerror="this.src='img/default-user.png'" 
                                 class="img-preview" id="imgPreview">
                        </div>
                        
                        <div class="mb-3">
                            <label for="foto" class="form-label">Cambiar foto</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Afinidad</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="mantenimiento" class="form-label">Mantenimiento</label>
                                    <select class="form-select" id="mantenimiento" name="mantenimiento" required>
                                        <option value="1" <?php echo $analista['mantenimiento'] == 1 ? 'selected' : ''; ?>>1 - Baja</option>
                                        <option value="2" <?php echo $analista['mantenimiento'] == 2 ? 'selected' : ''; ?>>2</option>
                                        <option value="3" <?php echo $analista['mantenimiento'] == 3 ? 'selected' : ''; ?>>3 - Media</option>
                                        <option value="4" <?php echo $analista['mantenimiento'] == 4 ? 'selected' : ''; ?>>4</option>
                                        <option value="5" <?php echo $analista['mantenimiento'] == 5 ? 'selected' : ''; ?>>5 - Alta</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="redes" class="form-label">Redes</label>
                                    <select class="form-select" id="redes" name="redes" required>
                                        <option value="1" <?php echo $analista['redes'] == 1 ? 'selected' : ''; ?>>1 - Baja</option>
                                        <option value="2" <?php echo $analista['redes'] == 2 ? 'selected' : ''; ?>>2</option>
                                        <option value="3" <?php echo $analista['redes'] == 3 ? 'selected' : ''; ?>>3 - Media</option>
                                        <option value="4" <?php echo $analista['redes'] == 4 ? 'selected' : ''; ?>>4</option>
                                        <option value="5" <?php echo $analista['redes'] == 5 ? 'selected' : ''; ?>>5 - Alta</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="configuracion" class="form-label">Configuración</label>
                                    <select class="form-select" id="configuracion" name="configuracion" required>
                                        <option value="1" <?php echo $analista['configuracion'] == 1 ? 'selected' : ''; ?>>1 - Baja</option>
                                        <option value="2" <?php echo $analista['configuracion'] == 2 ? 'selected' : ''; ?>>2</option>
                                        <option value="3" <?php echo $analista['configuracion'] == 3 ? 'selected' : ''; ?>>3 - Media</option>
                                        <option value="4" <?php echo $analista['configuracion'] == 4 ? 'selected' : ''; ?>>4</option>
                                        <option value="5" <?php echo $analista['configuracion'] == 5 ? 'selected' : ''; ?>>5 - Alta</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="interfaz_principal_listado_ingenieros.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al listado
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar vista previa de la imagen seleccionada
        document.getElementById('foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('imgPreview').src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>

<?php
$mysqli->close();
?>