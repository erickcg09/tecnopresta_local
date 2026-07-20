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
$advertencia_activos = [];

// Obtener datos para filtros
$tipos = getTiposActivos();
$marcas = getMarcas();

// Cargar activos directamente desde PHP (sin AJAX)
$codigoCentro = $_SESSION['codigo'] ?? '';
$activos = getActivosByCentro($codigoCentro);

// =============================================
// ASEGURAR UTF-8 SIN ELIMINAR ACENTOS
// =============================================
// Convertir a UTF-8 si es necesario, pero mantener los acentos
foreach ($activos as &$activo) {
    foreach ($activo as &$valor) {
        if (is_string($valor)) {
            // Asegurar que sea UTF-8 válido, pero conservar acentos
            if (!mb_check_encoding($valor, 'UTF-8')) {
                $valor = mb_convert_encoding($valor, 'UTF-8', 'ISO-8859-1');
            }
            // Escapar para JSON, pero NO eliminar caracteres
            $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        }
    }
}

$activos_json = json_encode($activos, JSON_UNESCAPED_UNICODE);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log('Error JSON en activos: ' . json_last_error_msg());
    $activos_json = '[]';
}
// Eliminar saltos de línea y tabulaciones
$activos_json = preg_replace('/\s+/', ' ', $activos_json);
// =============================================

$tipos_json = json_encode($tipos);
$marcas_json = json_encode($marcas);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asunto = trim($_POST['asunto'] ?? '');
    $detalle = trim($_POST['detalle'] ?? '');
    $activosPost = isset($_POST['activos']) ? json_decode($_POST['activos'], true) : [];
    $observaciones = isset($_POST['observaciones']) ? json_decode($_POST['observaciones'], true) : [];
    $confirmado = isset($_POST['confirmado']) ? (int)$_POST['confirmado'] : 0;
    
    if (empty($asunto) || empty($detalle)) {
        $mensaje_error = 'Todos los campos son obligatorios';
    } elseif (empty($activosPost) || count($activosPost) == 0) {
        $mensaje_error = 'Debe seleccionar al menos un activo afectado';
    } elseif (strlen($asunto) < 3 || strlen($asunto) > 150) {
        $mensaje_error = 'El asunto debe tener entre 3 y 150 caracteres';
    } elseif (strlen($detalle) < 5) {
        $mensaje_error = 'La descripción debe tener al menos 5 caracteres';
    } else {
        // Verificar tickets abiertos (solo si no está confirmado)
        if (!$confirmado) {
            $ticketsExistentes = verificarActivosConTicketsAbiertos($activosPost);
            if (!empty($ticketsExistentes)) {
                // Guardar datos en sesión para confirmación
                $_SESSION['pendiente_tecnico'] = [
                    'asunto' => $asunto,
                    'detalle' => $detalle,
                    'activos' => $activosPost,
                    'observaciones' => $observaciones
                ];
                
                $advertencia_activos = $ticketsExistentes;
            } else {
                // Proceder con el guardado
                $usuario_id = obtenerOCrearUsuario(
                    $_SESSION['cedula'],
                    $_SESSION['nombre'],
                    $_SESSION['correomep']
                );
                
                if (!$usuario_id) {
                    $mensaje_error = 'No se pudo identificar al usuario';
                } else {
                    $dre_id = obtenerIdRegional($_SESSION['idregional']);
                    
                    if (!$dre_id) {
                        $mensaje_error = 'No se pudo determinar su dirección regional';
                    } else {
                        $datosTicket = [
                            'usuario_id' => $usuario_id,
                            'dre_id' => $dre_id,
                            'circuito' => $_SESSION['circuito'] ?? null,
                            'dependencia' => $_SESSION['dependencia'] ?? null,
                            'tipo' => 1,
                            'asunto' => $asunto,
                            'descripcion' => $detalle,
                            'estado_id' => 1,
                            'eliminado' => 0
                        ];
                        
                        $ticket_id = insertarTicket($datosTicket);
                        
                        if ($ticket_id) {
                            $codigo = generarCodigoTicket(1, $ticket_id);
                            actualizarCodigoTicket($ticket_id, $codigo);
                            guardarActivosTicket($ticket_id, $activosPost, $observaciones, $detalle);
                            $mensaje_exito = "Ticket técnico creado exitosamente. Se reportaron " . count($activosPost) . " activos. Código: {$codigo}";
                            
                            // Limpiar datos pendientes
                            unset($_SESSION['pendiente_tecnico']);
                            
                            // Redirigir después de 2 segundos
                            echo "<meta http-equiv='refresh' content='2;url=plataforma_soporte.php'>";
                        } else {
                            $mensaje_error = 'Error al guardar el ticket';
                        }
                    }
                }
            }
        } else {
            // Confirmado - proceder con el guardado
            $usuario_id = obtenerOCrearUsuario(
                $_SESSION['cedula'],
                $_SESSION['nombre'],
                $_SESSION['correomep']
            );
            
            if (!$usuario_id) {
                $mensaje_error = 'No se pudo identificar al usuario';
            } else {
                $dre_id = obtenerIdRegional($_SESSION['idregional']);
                
                if (!$dre_id) {
                    $mensaje_error = 'No se pudo determinar su dirección regional';
                } else {
                    $datosTicket = [
                        'usuario_id' => $usuario_id,
                        'dre_id' => $dre_id,
                        'circuito' => $_SESSION['circuito'] ?? null,
                        'dependencia' => $_SESSION['dependencia'] ?? null,
                        'tipo' => 1,
                        'asunto' => $asunto,
                        'descripcion' => $detalle,
                        'estado_id' => 1,
                        'eliminado' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $ticket_id = insertarTicket($datosTicket);
                    
                    if ($ticket_id) {
                        $codigo = generarCodigoTicket(1, $ticket_id);
                        actualizarCodigoTicket($ticket_id, $codigo);
                        guardarActivosTicket($ticket_id, $activosPost, $observaciones, $detalle);
                        $mensaje_exito = "Ticket técnico creado exitosamente. Se reportaron " . count($activosPost) . " activos. Código: {$codigo}";
                        
                        unset($_SESSION['pendiente_tecnico']);
                        echo "<meta http-equiv='refresh' content='2;url=plataforma_soporte.php'>";
                    } else {
                        $mensaje_error = 'Error al guardar el ticket';
                    }
                }
            }
        }
    }
}

// Recuperar datos pendientes si existen
$datosPendientes = $_SESSION['pendiente_tecnico'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Técnico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/nueva-identidad.css">
    <script defer src="js/cdn.min.js"></script>
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
        
        [x-cloak] { display: none !important; }
        .list-group-item:hover { background-color: #fff8e1; cursor: pointer; }
        .filter-card { background: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include 'partials/header.php'; ?>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Barra de navegación -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="plataforma_soporte.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                    <div>
                        <span class="text-muted">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= htmlspecialchars($_SESSION['nombre']) ?>
                        </span>
                    </div>
                </div>

                <!-- Encabezado -->
                <div class="text-center mb-4">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="bi bi-tools fs-1 text-warning"></i>
                    </div>
                    <h2>Ticket Técnico</h2>
                    <p class="text-muted">Reporte de incidencias en equipos y sistemas</p>
                </div>

                <!-- Mensajes -->
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

                <!-- Modal de advertencia -->
                <?php if (!empty($advertencia_activos)): ?>
                <div class="modal fade" id="modalAdvertencia" tabindex="-1" data-bs-backdrop="static">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Atención: Equipos con tickets abiertos
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Los siguientes equipos ya tienen tickets abiertos:</p>
                                <ul>
                                <?php foreach ($advertencia_activos as $id_placa => $ticket): ?>
                                    <?php
                                    // Obtener información del activo
                                    $info = null;
                                    foreach ($activos as $a) {
                                        if ($a['id_placa'] == $id_placa) {
                                            $info = $a;
                                            break;
                                        }
                                    }
                                    ?>
                                    <li>
                                        <strong><?= htmlspecialchars($info['placa'] ?? $id_placa) ?></strong> - 
                                        <?= htmlspecialchars($info['modelo'] ?? '') ?> (<?= htmlspecialchars($info['tipo_activo'] ?? '') ?>)<br>
                                        <span class="text-muted">
                                            Ticket abierto: <strong><?= htmlspecialchars($ticket['codigo_tkt']) ?></strong><br>
                                            Asunto: <?= htmlspecialchars($ticket['asunto']) ?><br>
                                            Creado: <?= date('d/m/Y', strtotime($ticket['created_at'])) ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                                <p>¿Desea continuar creando este nuevo ticket de todas formas?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-2"></i>Cancelar
                                </button>
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="confirmado" value="1">
                                    <input type="hidden" name="asunto" value="<?= htmlspecialchars($datosPendientes['asunto'] ?? '') ?>">
                                    <input type="hidden" name="detalle" value="<?= htmlspecialchars($datosPendientes['detalle'] ?? '') ?>">
                                    <input type="hidden" name="activos" value='<?= json_encode($datosPendientes['activos'] ?? []) ?>'>
                                    <input type="hidden" name="observaciones" value='<?= json_encode($datosPendientes['observaciones'] ?? []) ?>'>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-check-circle me-2"></i>Continuar de todas formas
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        new bootstrap.Modal(document.getElementById('modalAdvertencia')).show();
                    });
                </script>
                <?php endif; ?>

                <!-- Formulario principal -->
                <div x-data="ticketTecnicoApp()" x-init="init()" x-cloak>
                    <form method="POST" id="formTicketTecnico">
                        <input type="hidden" name="confirmado" value="0">
                        <input type="hidden" name="activos" id="activosInput">
                        <input type="hidden" name="observaciones" id="observacionesInput">
                        
                        <!-- Información general -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-info-circle me-2 text-warning"></i>
                                    Información del reporte
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="asunto" class="form-label">Asunto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="asunto" name="asunto" 
                                           value="<?= htmlspecialchars($datosPendientes['asunto'] ?? '') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="detalle" class="form-label">Descripción del problema <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="detalle" name="detalle" rows="4" required><?= htmlspecialchars($datosPendientes['detalle'] ?? '') ?></textarea>
                                    <div class="form-text">Describa detalladamente el problema que presenta(n) el/los equipo(s)</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selección de activos -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-pc-display me-2 text-warning"></i>
                                    Equipos afectados <span class="text-danger">*</span>
                                </h5>
                                
                                <!-- Filtros -->
                                <div class="filter-card">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small">Tipo de activo</label>
                                            <select class="form-select" x-model="filtros.tipo">
                                                <option value="">Todos</option>
                                                <?php foreach ($tipos as $tipo): ?>
                                                    <option value="<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($tipo) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Marca</label>
                                            <select class="form-select" x-model="filtros.marca">
                                                <option value="">Todas</option>
                                                <?php foreach ($marcas as $marca): ?>
                                                    <option value="<?= htmlspecialchars($marca) ?>"><?= htmlspecialchars($marca) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Buscar</label>
                                            <input type="text" class="form-control" placeholder="Placa, modelo..." x-model="filtros.busqueda">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Contador de equipos -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="seleccionarTodos" x-model="seleccionarTodos">
                                        <label class="form-check-label" for="seleccionarTodos">
                                            Seleccionar todos los equipos mostrados
                                        </label>
                                    </div>
                                    <span class="badge bg-secondary" x-text="activosSeleccionados.length + ' equipo(s) seleccionado(s)'"></span>
                                </div>
                                
                                <!-- Lista de activos -->
                                <div x-show="activosFiltrados().length === 0" class="text-center py-4 text-muted">
                                    <i class="bi bi-search fs-1"></i>
                                    <p>No se encontraron equipos con los filtros seleccionados</p>
                                </div>
                                
                                <div class="list-group" x-show="activosFiltrados().length > 0">
                                    <template x-for="activo in activosFiltrados()" :key="activo.id_placa">
                                        <div class="list-group-item">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="d-flex align-items-start gap-3">
                                                    <input type="checkbox" class="form-check-input mt-1" 
                                                           x-model="activosSeleccionados" :value="activo.id_placa">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                                            <span class="badge bg-secondary" x-text="activo.placa"></span>
                                                            <span class="badge bg-info" x-text="activo.tipo_activo"></span>
                                                            <span class="badge bg-light text-dark" x-text="activo.nombre_marca || 'Sin marca'"></span>
                                                        </div>
                                                        <div class="fw-semibold mb-2" x-text="activo.modelo"></div>
                                                        <div x-show="activo.serial" class="small text-muted mb-2">
                                                            Serial: <span x-text="activo.serial"></span>
                                                        </div>
                                                        <div>
                                                            <label class="form-label small text-muted">Problema específico (opcional):</label>
                                                            <textarea class="form-control form-control-sm" rows="2"
                                                              x-model="observaciones[activo.id_placa]"
                                                              placeholder="Ej: No enciende, pantalla rota, no tiene red..."
                                                              :class="{'bg-light': !activosSeleccionados.includes(activo.id_placa)}"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información del usuario -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Información que se registrará automáticamente:</strong><br>
                                    Usuario: <?= htmlspecialchars($_SESSION['nombre']) ?><br>
                                    Cédula: <?= htmlspecialchars($_SESSION['cedula']) ?><br>
                                    Centro: <?= htmlspecialchars($_SESSION['codigo']) ?><br>
                                    Regional: <?= htmlspecialchars($_SESSION['direccionreg']) ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="plataforma_soporte.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning" :disabled="activosSeleccionados.length === 0">
                                <i class="bi bi-send-check me-2"></i>
                                <span>Enviar reporte técnico</span>
                            </button>
                        </div>
                        <div x-show="activosSeleccionados.length === 0" class="text-danger small text-end mt-2">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Debe seleccionar al menos un equipo afectado
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php include 'partials/footer.php'; ?>
    <script>
        function ticketTecnicoApp() {
            return {
                // Verificar que los datos sean válidos
                activos: (function() {
                    try {
                        const data = <?= $activos_json ?>;
                        return Array.isArray(data) ? data : [];
                    } catch(e) {
                        console.error('Error parsing activos:', e);
                        return [];
                    }
                })(),
                filtros: {
                    tipo: '',
                    marca: '',
                    busqueda: ''
                },
                activosSeleccionados: [],
                observaciones: {},
                
                get seleccionarTodos() {
                    const filtrados = this.activosFiltrados();
                    return filtrados.length > 0 && filtrados.every(a => this.activosSeleccionados.includes(a.id_placa));
                },
                
                set seleccionarTodos(value) {
                    const filtrados = this.activosFiltrados();
                    if (value) {
                        filtrados.forEach(a => {
                            if (!this.activosSeleccionados.includes(a.id_placa)) {
                                this.activosSeleccionados.push(a.id_placa);
                            }
                        });
                    } else {
                        filtrados.forEach(a => {
                            const index = this.activosSeleccionados.indexOf(a.id_placa);
                            if (index !== -1) this.activosSeleccionados.splice(index, 1);
                        });
                    }
                    // 👇 Forzar actualización de observaciones después de seleccionar/deseleccionar
                    this.observaciones = {...this.observaciones};
                },
                
                // 👇 NUEVO MÉTODO: Verifica si un activo está seleccionado
                estaSeleccionado(id_placa) {
                    return this.activosSeleccionados.includes(id_placa);
                },
                
                init() {
                    console.log('Activos cargados:', this.activos.length);
                    console.log('Primer activo:', this.activos[0]);
                    
                    // 👇 Usar x-effect en lugar de $watch (más confiable)
                    this.$effect(() => {
                        // Esto se ejecuta cada vez que cambia activosSeleccionados
                        // Forzar la reactividad
                        const temp = [...this.activosSeleccionados];
                        // Pequeño truco para forzar actualización
                        this.observaciones = {...this.observaciones};
                    });
                    
                    <?php if ($datosPendientes && isset($datosPendientes['activos'])): ?>
                    this.activosSeleccionados = <?= json_encode($datosPendientes['activos']) ?>;
                    this.observaciones = <?= json_encode($datosPendientes['observaciones']) ?>;
                    <?php endif; ?>
                },
                
                activosFiltrados() {
                    if (!this.activos || !Array.isArray(this.activos) || this.activos.length === 0) {
                        return [];
                    }
                    return this.activos.filter(activo => {
                        if (!activo) return false;
                        if (this.filtros.tipo && activo.tipo_activo !== this.filtros.tipo) return false;
                        if (this.filtros.marca && activo.nombre_marca !== this.filtros.marca) return false;
                        if (this.filtros.busqueda) {
                            const busqueda = this.filtros.busqueda.toLowerCase();
                            return (activo.placa && activo.placa.toLowerCase().includes(busqueda)) ||
                                   (activo.modelo && activo.modelo.toLowerCase().includes(busqueda)) ||
                                   (activo.serial && activo.serial.toLowerCase().includes(busqueda));
                        }
                        return true;
                    });
                }
            }
        }
        
        // Esperar a que el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Preparar datos antes de enviar
            const form = document.getElementById('formTicketTecnico');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Buscar el componente Alpine
                    const alpineDiv = document.querySelector('[x-data]');
                    if (alpineDiv && alpineDiv._x_dataStack && alpineDiv._x_dataStack.length > 0) {
                        const alpineData = alpineDiv._x_dataStack[0];
                        
                        // 👇 Filtrar observaciones: solo las de activos seleccionados
                        const observacionesFiltradas = {};
                        if (alpineData.activosSeleccionados && alpineData.activosSeleccionados.length > 0) {
                            alpineData.activosSeleccionados.forEach(id => {
                                if (alpineData.observaciones && alpineData.observaciones[id]) {
                                    observacionesFiltradas[id] = alpineData.observaciones[id];
                                }
                            });
                        }
                        
                        const activosInput = document.getElementById('activosInput');
                        const observacionesInput = document.getElementById('observacionesInput');
                        
                        if (activosInput) {
                            activosInput.value = JSON.stringify(alpineData.activosSeleccionados);
                        }
                        if (observacionesInput) {
                            observacionesInput.value = JSON.stringify(observacionesFiltradas);
                        }
                        
                        console.log('Enviando activos:', alpineData.activosSeleccionados);
                        console.log('Enviando observaciones filtradas:', observacionesFiltradas);
                    }
                });
            }
        });
    </script>
    <script src="bootstrap5/js/bootstrap.bundle.min.js"></script>
</body>
</html>