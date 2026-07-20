<?php
session_start();
require_once("funciones_tickets.php");

// Verificar permisos
if (!isset($_SESSION['cedula'])) {
    header("Location: index.html");
    exit();
}

$mensaje_error = '';
$mensaje_exito = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asunto = trim($_POST['asunto'] ?? '');
    $detalle = trim($_POST['detalle'] ?? '');
    
    if (empty($asunto) || empty($detalle)) {
        $mensaje_error = 'Todos los campos son obligatorios';
    } elseif (strlen($asunto) < 3 || strlen($asunto) > 150) {
        $mensaje_error = 'El asunto debe tener entre 3 y 150 caracteres';
    } elseif (strlen($detalle) < 5) {
        $mensaje_error = 'La descripción debe tener al menos 5 caracteres';
    } else {
        // Obtener usuario_id
        $usuario_id = obtenerOCrearUsuario(
            $_SESSION['cedula'],
            $_SESSION['nombre'],
            $_SESSION['correomep']
        );
        
        if (!$usuario_id) {
            $mensaje_error = 'No se pudo identificar al usuario';
        } else {
            // Obtener ID real de la regional
            $dre_id = obtenerIdRegional($_SESSION['idregional']);
            
            if (!$dre_id) {
                $mensaje_error = 'No se pudo determinar su dirección regional';
            } else {
                // Preparar datos del ticket
                $datosTicket = [
                    'usuario_id' => $usuario_id,
                    'dre_id'     => $dre_id,
                    'circuito'   => $_SESSION['circuito']    ?? null,
                    'dependencia'=> $_SESSION['dependencia'] ?? null,
                    'tipo'       => 0,
                    'asunto'     => $asunto,
                    'descripcion'=> $detalle,
                    'estado_id'  => 1,
                    'eliminado'  => 0
                ];
                
                $ticket_id = insertarTicket($datosTicket);
                
                if ($ticket_id) {
                    $codigo = generarCodigoTicket(0, $ticket_id);
                    actualizarCodigoTicket($ticket_id, $codigo);
                    $mensaje_exito = "Ticket creado exitosamente. Su código es: {$codigo}";
                    
                    // Limpiar campos
                    $asunto = '';
                    $detalle = '';
                } else {
                    $mensaje_error = 'Error al guardar el ticket';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/nueva-identidad.css">
    <style>
            @font-face {
            font-family: 'Henderson Sans';
            src: url('assets/fuentes/HendersonSansW00-BasicLight.woff2') format('woff2'),
                 url('assets/fuentes/HendersonSansW00-BasicLight.woff') format('woff');
            font-weight: 300;
            font-style: normal;
            font-display: swap;
        }
        
        @font-face {
            font-family: 'Henderson Sans';
            src: url('assets/fuentes/HendersonSansW00-BasicSmBd.woff2') format('woff2'),
                 url('assets/fuentes/HendersonSansW00-BasicSmBd.woff') format('woff');
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }
        
        @font-face {
            font-family: 'Henderson Sans';
            src: url('assets/fuentes/HendersonSansW00-BasicBold.woff2') format('woff2'),
                 url('assets/fuentes/HendersonSansW00-BasicBold.woff') format('woff');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        
        .mep-logo-box {
            background: transparent;
            padding: 0;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: none;
        }
        
        .mep-logo-icon {
            width: 48px;
            height: auto;
            display: block;
        }
    </style>
</head>
<body>

    <?php include 'partials/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">

                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-briefcase-fill me-2"></i>
                            Ticket Administrativo
                        </h4>
                    </div>

                    <div class="card-body">

                        <?php if ($mensaje_error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= htmlspecialchars($mensaje_error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($mensaje_exito): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?= htmlspecialchars($mensaje_exito) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="asunto" class="form-label">
                                    Asunto <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="asunto"
                                       name="asunto"
                                       value="<?= htmlspecialchars($asunto ?? '') ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="detalle" class="form-label">
                                    Detalle <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control"
                                          id="detalle"
                                          name="detalle"
                                          rows="6"
                                          required><?= htmlspecialchars($detalle ?? '') ?></textarea>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Información que se registrará automáticamente:</strong><br>
                                Usuario: <?= htmlspecialchars($_SESSION['nombre']) ?><br>
                                Cédula: <?= htmlspecialchars($_SESSION['cedula']) ?><br>
                                Centro: <?= htmlspecialchars($_SESSION['codigo']) ?><br>
                                Regional: <?= htmlspecialchars($_SESSION['direccionreg']) ?>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="plataforma_soporte.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send-check me-2"></i>Enviar Ticket
                                </button>
                            </div>
                        </form>

                    </div><!-- /card-body -->
                </div><!-- /card -->
            </div>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>

    <script src="bootstrap5/js/bootstrap.bundle.min.js"></script>
</body>
</html>