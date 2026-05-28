<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte el administrador");
    window.location.href = "formulario_corregir_modelo.php";
    </script>';
    exit();
}
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

// Inicializar variables
$id_activo = "";
$activo = null;
$error = "";
$success = "";

// Procesar búsqueda del activo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar'])) {
    $id_activo = trim($_POST['id_activo']);
    
    if (empty($id_activo)) {
        $error = "Por favor, ingrese un ID de activo.";
    } else {
        // Buscar el activo en la base de datos
        $query = "SELECT * FROM t_activo WHERE id_activo = ?";
        $stmt = $link->prepare($query);
        $stmt->bind_param("i", $id_activo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $activo = $result->fetch_assoc();
        } else {
            $error = "No se encontró un activo con el ID proporcionado.";
        }
        $stmt->close();
    }
}

// Procesar actualización del activo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $id_activo = $_POST['id_activo'];
    $id_ag = $_POST['id_ag'];
    $id_marca = $_POST['id_marca'];
    $modelo = trim($_POST['modelo']);
    $id_color = $_POST['id_color'];
    
    if (empty($modelo)) {
        $error = "El campo modelo es obligatorio.";
    } else {
        $query = "UPDATE t_activo SET id_ag = ?, id_marca = ?, modelo = ?, id_color = ? WHERE id_activo = ?";
        $stmt = $link->prepare($query);
        $stmt->bind_param("iisii", $id_ag, $id_marca, $modelo, $id_color, $id_activo);
        
        if ($stmt->execute()) {
            $success = "Activo actualizado correctamente.";
            // Volver a cargar los datos actualizados
            $activo = [
                'id_activo' => $id_activo,
                'id_ag' => $id_ag,
                'id_marca' => $id_marca,
                'modelo' => $modelo,
                'id_color' => $id_color
            ];
        } else {
            $error = "Error al actualizar el activo: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar modelo del activo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        .card-custom {
            background-color: #f0f4f8; /* Color frío y suave */
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            align-self: flex-end;
        }
        .btn-custom {
            background-color: #007bff; /* Color frío */
            border: none;
            border-radius: 5px;
        }
        .icon-large {
            font-size: 2em; /* Ajusta el tamaño del ícono */
        }
        .form-section {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md bg-dark navbar-dark">
        <img src="img/logodelgobierno.png" width="35" height="30" alt="" loading="lazy">
        <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="formulario_corregir_modelo.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-all" viewBox="0 0 16 16">
                <path d="M8.098 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.7 8.7 0 0 0-1.921-.306 7 7 0 0 0-.798.008h-.013l-.005.001h-.001L8.8 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L4.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028zM9.3 10.386q.102 0 .223.006c.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96z"/>
                <path d="M5.232 4.293a.5.5 0 0 0-.7-.106L.54 7.127a1.147 1.147 0 0 0 0 1.946l3.994 2.94a.5.5 0 1 0 .593-.805L1.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028 4.012-2.954a.5.5 0 0 0 .106-.699"/>
                </svg> Regresar</a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" href="gameover.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open" viewBox="0 0 16 16">
                <path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/>
                <path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117M11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5M4 1.934V15h6V1.077z"/>
                </svg> Cerrar Sesión</a>
            </li>  
            </ul>
        </div>  
    </nav>

<div class="container mt-5">
    <h2>Usuario: <?php echo $lognombre." ".$logcodigo;?></h2><br>
    <h4>Formulario para editar modelo del activo.</h4>

    <!-- Mostrar mensajes de error o éxito -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Formulario de búsqueda -->
    <div class="form-section">
        <h5>Buscar activo por ID</h5>
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="id_activo" class="form-label">ID del Activo</label>
                        <input type="number" class="form-control w-50" id="id_activo" name="id_activo" 
                               value="<?php echo htmlspecialchars($id_activo); ?>" required>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" name="buscar" class="btn btn-primary w-100">Buscar Activo</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Formulario de edición (solo visible si se encontró el activo) -->
    <?php if ($activo): ?>
    <div class="form-section">
        <h5>Editar información del activo</h5>
        <form method="POST" action="">
            <input type="hidden" name="id_activo" value="<?php echo $activo['id_activo']; ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_ag" class="form-label">ID Ag</label>
                        <input type="number" class="form-control" id="id_ag" name="id_ag" 
                               value="<?php echo htmlspecialchars($activo['id_ag']); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_marca" class="form-label">ID Marca</label>
                        <input type="number" class="form-control" id="id_marca" name="id_marca" 
                               value="<?php echo htmlspecialchars($activo['id_marca']); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" 
                               value="<?php echo htmlspecialchars($activo['modelo']); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_color" class="form-label">ID Color</label>
                        <input type="number" class="form-control" id="id_color" name="id_color" 
                               value="<?php echo htmlspecialchars($activo['id_color']); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" name="actualizar" class="btn btn-success">Actualizar Activo</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-eraser-fill" viewBox="0 0 16 16">
          <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm.66 11.34L3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z"/>
        </svg>
    </div>
</div>

<footer class="bg-dark text-white pt-4 pb-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0">Por favor, asegúrese de ingresar la información solicitada en cada instancia.</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <div class="border border-light p-3">
                    <p class="mb-0">© 2024 Ministerio de Educación Pública. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>