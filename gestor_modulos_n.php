<?php
// ============================================================
// FORMULARIO: Gestion de Subsistemas y Modulos
// ============================================================
// Proposito: Interfaz administrativa para CRUD de subsistemas
// y modulos del sistema TecnoPresta.
//
// Acceso: Solo Root (rol_id = 1)
//
// Dependencias:
//   - navegar.php (define ACCESO_SEGURO)
//   - auth.php (validacion de sesion y roles)
//   - usuarioAzure.php (datos de sesion Azure AD)
//   - sql/gestor_modulos.php (endpoint datos)
//   - actualizar_gestor_modulos_n.php (endpoint accion)
// ============================================================

// Verificar acceso seguro desde navegar.php
if (!defined('ACCESO_SEGURO')) {
    http_response_code(403);
    die('Acceso directo no permitido');
}

// Iniciar sesion si no existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que el usuario sea Root
require_once __DIR__ . '/usuarioAzure.php';
require_once __DIR__ . '/auth.php';

$usuario_azure = obtenerUsuarioSesion();
if (!$usuario_azure) {
    header('Location: index.html');
    exit;
}

// Redirigir si no es Root
if (!esUsuarioRoot()) {
    header('Location: navegar.php?ruta=formulario_menu_principal.php');
    exit;
}

// Construir ruta de regreso
$ruta_regreso = 'navegar.php?ruta=formulario_menu_principal.php';
if (isset($_GET['subsistema_id'], $_GET['modulo_id'])) {
    $ruta_regreso = 'navegar.php?ruta=formulario_sub_modulos.php'
        . '&subsistema_id=' . intval($_GET['subsistema_id'])
        . '&modulo_id=' . intval($_GET['modulo_id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor del Sistema - Gestion de Modulos</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Estilos institucionales MEP -->
    <link rel="stylesheet" href="assets/css/nueva-identidad.css?v=7">
    <!-- Estilos del menu principal y tablas MEP -->
    <link rel="stylesheet" href="css/formulario_menu_principal.css?v=7">
</head>
<body class="layout-page">

    <!-- Header institucional -->
    <?php include 'partials/header.php'; ?>

    <!-- ============================================================
    CONTENIDO PRINCIPAL
    ============================================================ -->
    <main class="container py-4 contenido-principal" id="appGestorModulos">

        <!-- Hero-box -->
        <div class="hero-box mb-4 fade-enter">
            <div class="row align-items-center">
                <div class="col-auto">
                    <i class="bi bi-diagram-3" style="font-size: 2.5rem; color: #C8A951;"></i>
                </div>
                <div class="col">
                    <h1 class="mb-0" style="color: #fff; font-weight: 600;">Gestion de Subsistemas y Modulos</h1>
                    <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                        Administre la estructura del menu del sistema
                    </p>
                </div>
            </div>
        </div>

        <!-- ============================================================
        SECCION 1: SUBSISTEMAS
        ============================================================ -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-collection me-2"></i>Subsistemas
                </h5>
                <button class="btn btn-mep-primary btn-sm" onclick="abrirModalSubsistema()">
                    <i class="bi bi-plus-lg"></i> Nuevo Subsistema
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table activos-table mb-0" id="tablaSubsistemas">
                        <thead>
                            <tr>
                                <th>Nombre del Subsistema</th>
                                <th>Descripcion</th>
                                <th class="th-estado">Estado</th>
                                <th class="th-acciones">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodySubsistemas">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Cargando subsistemas...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================
        SECCION 2: MODULOS
        ============================================================ -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-modem me-2"></i>Modulos
                </h5>
                <button class="btn btn-mep-primary btn-sm" onclick="abrirModalModulo()"
                        id="btnNuevoModulo">
                    <i class="bi bi-plus-lg"></i> Nuevo Modulo
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table activos-table mb-0" id="tablaModulos">
                        <thead>
                            <tr>
                                <th>Nombre del Modulo</th>
                                <th>Descripcion</th>
                                <th class="th-estado">Estado</th>
                                <th class="th-acciones">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyModulos">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Seleccione un subsistema para ver sus modulos
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Boton volver flotante -->
        <a href="<?= htmlspecialchars($ruta_regreso) ?>"
           class="btn-disponibilidad"
           style="bottom: 100px;"
           data-tooltip="Regresar">
            <i class="bi bi-arrow-left-circle-fill"></i>
        </a>

        <!-- ============================================================
        MODALES
        ============================================================ -->

        <!-- Modal Subsistema (Crear/Editar) -->
        <div class="modal fade gestor-overlay" id="modalSubsistema" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered gestor-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSubsistemaTitulo">
                            <i class="bi bi-collection"></i>Nuevo Subsistema
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editSubsistemaId" value="0">
                        <div class="mb-3">
                            <label for="subNombre" class="gestor-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="gestor-input form-control" id="subNombre" maxlength="70" required
                                   placeholder="Ej: Gestion de Inventario">
                        </div>
                        <div class="mb-3">
                            <label for="subDescripcion" class="gestor-label">Descripcion</label>
                            <textarea class="gestor-textarea form-control" id="subDescripcion" rows="2" maxlength="200"
                                      placeholder="Breve descripcion del subsistema"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="subImagen" class="gestor-label">Icono (SVG)</label>
                            <input type="file" class="gestor-input form-control" id="subImagen"
                                   accept=".svg" style="padding:0.375rem 0.75rem;">
                            <div class="form-text">
                                Subir archivo SVG. Se guardara en <code>assets/img/subsistemas/</code>
                            </div>
                            <div id="subImagenActual" class="mt-1 small text-muted" style="display:none;"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="subOrden" class="gestor-label">Orden</label>
                                <input type="number" class="gestor-input form-control" id="subOrden" value="0" min="0" max="999">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="subColor" class="gestor-label">Color</label>
                                <input type="color" class="gestor-input form-control form-control-color" id="subColor" value="#003876">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="gestor-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="gestor-btn-primary" onclick="guardarSubsistema()">
                            <i class="bi bi-check-lg"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Modulo (Crear/Editar) -->
        <div class="modal fade gestor-overlay" id="modalModulo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered gestor-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalModuloTitulo">
                            <i class="bi bi-modem"></i>Nuevo Modulo
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editModuloId" value="0">
                        <div class="mb-3">
                            <label for="modNombre" class="gestor-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="gestor-input form-control" id="modNombre" maxlength="70" required
                                   placeholder="Ej: Gestion de Activos">
                        </div>
                        <div class="mb-3">
                            <label for="modDescripcion" class="gestor-label">Descripcion</label>
                            <textarea class="gestor-textarea form-control" id="modDescripcion" rows="2" maxlength="200"
                                      placeholder="Breve descripcion del modulo"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="modSubsistema" class="gestor-label">Subsistema <span class="text-danger">*</span></label>
                            <select class="gestor-select form-select" id="modSubsistema">
                                <option value="">Seleccione un subsistema...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modRutaBase" class="gestor-label">Ruta Base</label>
                            <input type="text" class="gestor-input form-control" id="modRutaBase" maxlength="150"
                                   placeholder="/inventario/activos">
                            <div class="form-text">Prefijo de ruta para los formularios del modulo.</div>
                        </div>
                        <div class="mb-3">
                            <label for="modImagen" class="gestor-label">Icono (SVG)</label>
                            <input type="file" class="gestor-input form-control" id="modImagen"
                                   accept=".svg" style="padding:0.375rem 0.75rem;">
                            <div class="form-text">
                                Subir archivo SVG. Se guardara en <code>assets/img/modulos/</code>
                            </div>
                            <div id="modImagenActual" class="mt-1 small text-muted" style="display:none;"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modOrden" class="gestor-label">Orden</label>
                                <input type="number" class="gestor-input form-control" id="modOrden" value="0" min="0" max="999">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modColor" class="gestor-label">Color</label>
                                <input type="color" class="gestor-input form-control form-control-color" id="modColor" value="#003876">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="gestor-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="gestor-btn-primary" onclick="guardarModulo()">
                            <i class="bi bi-check-lg"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Confirmacion -->
        <div class="modal fade gestor-overlay" id="modalConfirmacion" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm gestor-modal">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="gestor-modal-icon warning">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold" id="modalConfirmacionTexto">¿Esta seguro?</p>
                    </div>
                    <div class="modal-footer centered border-0 pt-0">
                        <button type="button" class="gestor-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="gestor-btn-danger" id="btnConfirmarAccion">
                            <i class="bi bi-check-lg"></i> Si, continuar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Exito -->
        <div class="modal fade gestor-overlay" id="modalExito" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm gestor-modal">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="gestor-modal-icon success">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold" id="modalExitoTexto">Operacion exitosa</p>
                    </div>
                    <div class="modal-footer centered border-0 pt-0">
                        <button type="button" class="gestor-btn-primary" data-bs-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Error -->
        <div class="modal fade gestor-overlay" id="modalError" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm gestor-modal">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="gestor-modal-icon error">
                            <i class="bi bi-x-lg"></i>
                        </div>
                        <p class="mt-3 mb-0 fw-semibold" id="modalErrorTexto">Error al procesar la solicitud</p>
                    </div>
                    <div class="modal-footer centered border-0 pt-0">
                        <button type="button" class="gestor-btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer institucional -->
    <?php include 'partials/footer.php'; ?>

    <!-- ============================================================
    SCRIPTS
    ============================================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-3.7.1.min.js"></script>

    <script>
    // ============================================================
    // VARIABLES GLOBALES
    // ============================================================
    let modales = {};
    let subsistemasData = [];
    let modulosData = [];
    let subsistemaSeleccionado = null;

    // ============================================================
    // UTILIDADES
    // ============================================================

    /**
     * Escapa caracteres HTML para prevenir XSS
     */
    function escapeHtml(texto) {
        if (!texto) return '';
        return String(texto)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /**
     * Muestra un modal Bootstrap 5 por su ID
     */
    function mostrarModal(id) {
        const el = document.getElementById(id);
        if (el) {
            const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            modal.show();
        }
    }

    /**
     * Oculta un modal Bootstrap 5 por su ID
     */
    function ocultarModal(id) {
        const el = document.getElementById(id);
        if (el) {
            const modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        }
    }

    /**
     * Muestra modal de confirmacion con callback
     */
    function mostrarConfirmacion(mensaje, callbackAceptar) {
        document.getElementById('modalConfirmacionTexto').textContent = mensaje;
        const btnConfirmar = document.getElementById('btnConfirmarAccion');
        const nuevaAccion = function() {
            ocultarModal('modalConfirmacion');
            if (typeof callbackAceptar === 'function') callbackAceptar();
            btnConfirmar.removeEventListener('click', nuevaAccion);
        };
        btnConfirmar.addEventListener('click', nuevaAccion);
        mostrarModal('modalConfirmacion');
    }

    /**
     * Muestra modal de exito con mensaje
     */
    function mostrarExito(mensaje) {
        document.getElementById('modalExitoTexto').textContent = mensaje;
        mostrarModal('modalExito');
    }

    /**
     * Muestra modal de error con mensaje
     */
    function mostrarError(mensaje) {
        document.getElementById('modalErrorTexto').textContent = mensaje;
        mostrarModal('modalError');
    }

    // ============================================================
    // CARGA DE DATOS
    // ============================================================

    /**
     * Carga subsistemas y modulos desde el endpoint
     */
    async function cargarDatos() {
        try {
            const resp = await fetch('sql/gestor_modulos.php');
            const data = await resp.json();

            if (!data.success) {
                throw new Error(data.message || 'Error al cargar datos');
            }

            subsistemasData = data.subsistemas || [];
            modulosData = data.modulos || [];

            renderizarSubsistemas();
            llenarSelectSubsistemas();

            // Si habia un subsistema seleccionado, mantenerlo
            if (subsistemaSeleccionado) {
                renderizarModulos(subsistemaSeleccionado);
            } else if (subsistemasData.length > 0) {
                seleccionarSubsistema(subsistemasData[0].id);
            }

        } catch (e) {
            mostrarError('Error al cargar datos: ' + e.message);
        }
    }

    /**
     * Llena el select del modal de modulo con los subsistemas disponibles
     */
    function llenarSelectSubsistemas() {
        const select = document.getElementById('modSubsistema');
        select.innerHTML = '<option value="">Seleccione un subsistema...</option>';
        subsistemasData.forEach(function(s) {
            const opcion = document.createElement('option');
            opcion.value = s.id;
            opcion.textContent = s.nombre + (s.eliminado == 1 ? ' (Inactivo)' : '');
            select.appendChild(opcion);
        });
    }

    // ============================================================
    // RENDERIZADO: SUBSISTEMAS
    // ============================================================

    /**
     * Renderiza la tabla de subsistemas
     */
    function renderizarSubsistemas() {
        const tbody = document.getElementById('tbodySubsistemas');
        tbody.innerHTML = '';

        if (subsistemasData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No hay subsistemas registrados</td></tr>';
            return;
        }

        subsistemasData.forEach(function(s) {
            const activo = parseInt(s.eliminado) === 0;
            const esSeleccionado = subsistemaSeleccionado && parseInt(subsistemaSeleccionado) === parseInt(s.id);
            const tr = document.createElement('tr');
            tr.className = (activo ? '' : 'table-danger') + (esSeleccionado ? ' table-active' : '');
            tr.style.cursor = 'pointer';
            tr.onclick = function() { seleccionarSubsistema(s.id); };

            tr.innerHTML =
                '<td><strong>' + escapeHtml(s.nombre) + '</strong>' +
                    (esSeleccionado ? ' <i class="bi bi-chevron-right text-gold"></i>' : '') +
                '</td>' +
                '<td class="text-muted">' + escapeHtml(s.descripcion || '—') + '</td>' +
                '<td class="td-estado">' +
                    (activo
                        ? '<span class="badge bg-success">● Activo</span>'
                        : '<span class="badge bg-secondary">○ Inactivo</span>') +
                '</td>' +
                '<td class="td-acciones">' +
                    '<button class="btn btn-sm btn-outline-primary me-1" onclick="event.stopPropagation(); abrirModalSubsistema(' + s.id + ')">' +
                        '<i class="bi bi-pencil"></i>' +
                    '</button>' +
                    '<button class="btn btn-sm ' + (activo ? 'btn-outline-danger' : 'btn-outline-success') + '" onclick="event.stopPropagation(); toggleSubsistema(' + s.id + ', ' + (!activo) + ')">' +
                        '<i class="bi ' + (activo ? 'bi-x-circle' : 'bi-arrow-counterclockwise') + '"></i> ' +
                        (activo ? 'Desactivar' : 'Reactivar') +
                    '</button>' +
                '</td>';

            tbody.appendChild(tr);
        });
    }

    /**
     * Selecciona un subsistema y filtra los modulos
     */
    function seleccionarSubsistema(id) {
        subsistemaSeleccionado = id;
        renderizarSubsistemas();
        renderizarModulos(id);
    }

    // ============================================================
    // RENDERIZADO: MODULOS
    // ============================================================

    /**
     * Renderiza la tabla de modulos filtrados por subsistema
     */
    function renderizarModulos(subsistemaId) {
        const tbody = document.getElementById('tbodyModulos');
        tbody.innerHTML = '';

        const filtrados = modulosData.filter(function(m) {
            return parseInt(m.subsistema_id) === parseInt(subsistemaId);
        });

        if (filtrados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No hay modulos en este subsistema</td></tr>';
            return;
        }

        filtrados.forEach(function(m) {
            const activo = parseInt(m.eliminado) === 0;
            const tr = document.createElement('tr');
            tr.className = activo ? '' : 'table-danger';

            tr.innerHTML =
                '<td><strong>' + escapeHtml(m.nombre) + '</strong></td>' +
                '<td class="text-muted">' + escapeHtml(m.descripcion || '—') + '</td>' +
                '<td class="td-estado">' +
                    (activo
                        ? '<span class="badge bg-success">● Activo</span>'
                        : '<span class="badge bg-secondary">○ Inactivo</span>') +
                '</td>' +
                '<td class="td-acciones">' +
                    '<button class="btn btn-sm btn-outline-primary me-1" onclick="abrirModalModulo(' + m.id + ')">' +
                        '<i class="bi bi-pencil"></i>' +
                    '</button>' +
                    '<button class="btn btn-sm ' + (activo ? 'btn-outline-danger' : 'btn-outline-success') + '" onclick="toggleModulo(' + m.id + ', ' + (!activo) + ')">' +
                        '<i class="bi ' + (activo ? 'bi-x-circle' : 'bi-arrow-counterclockwise') + '"></i> ' +
                        (activo ? 'Desactivar' : 'Reactivar') +
                    '</button>' +
                '</td>';

            tbody.appendChild(tr);
        });
    }

    // ============================================================
    // CRUD: SUBSISTEMAS
    // ============================================================

    /**
     * Abre el modal para crear o editar un subsistema
     */
    function abrirModalSubsistema(id) {
        document.getElementById('modalSubsistemaTitulo').textContent = id ? 'Editar Subsistema' : 'Nuevo Subsistema';
        document.getElementById('editSubsistemaId').value = id || 0;

        // Limpiar file input
        document.getElementById('subImagen').value = '';
        document.getElementById('subImagenActual').style.display = 'none';

        if (id) {
            const s = subsistemasData.find(function(item) { return parseInt(item.id) === parseInt(id); });
            if (s) {
                document.getElementById('subNombre').value = s.nombre || '';
                document.getElementById('subDescripcion').value = s.descripcion || '';
                document.getElementById('subOrden').value = s.orden || 0;
                document.getElementById('subColor').value = s.color || '#003876';

                if (s.imagen) {
                    document.getElementById('subImagenActual').textContent = 'Actual: ' + s.imagen;
                    document.getElementById('subImagenActual').style.display = 'block';
                }
            }
        } else {
            document.getElementById('subNombre').value = '';
            document.getElementById('subDescripcion').value = '';
            document.getElementById('subOrden').value = '0';
            document.getElementById('subColor').value = '#003876';
        }

        mostrarModal('modalSubsistema');
    }

    /**
     * Guarda (crea o actualiza) un subsistema
     */
    async function guardarSubsistema() {
        const id = parseInt(document.getElementById('editSubsistemaId').value) || 0;
        const nombre = document.getElementById('subNombre').value.trim();
        const descripcion = document.getElementById('subDescripcion').value.trim();
        const orden = parseInt(document.getElementById('subOrden').value) || 0;
        const color = document.getElementById('subColor').value.trim();

        if (!nombre) {
            mostrarError('El nombre del subsistema es obligatorio');
            return;
        }

        const formData = new FormData();
        formData.append('action', id ? 'editar_subsistema' : 'crear_subsistema');
        formData.append('nombre', nombre);
        formData.append('descripcion', descripcion);
        formData.append('orden', orden);
        formData.append('color', color);
        if (id) formData.append('id', id);

        const fileInput = document.getElementById('subImagen');
        if (fileInput.files.length > 0) {
            formData.append('imagen', fileInput.files[0]);
        }

        try {
            const resp = await fetch('actualizar_gestor_modulos_n.php', {
                method: 'POST',
                body: formData
            });
            const data = await resp.json();

            if (data.success) {
                ocultarModal('modalSubsistema');
                mostrarExito(data.message);
                cargarDatos();
            } else {
                mostrarError(data.message);
            }
        } catch (e) {
            mostrarError('Error de conexion: ' + e.message);
        }
    }

    /**
     * Activa o desactiva un subsistema
     */
    function toggleSubsistema(id, activar) {
        const nombre = subsistemasData.find(function(s) { return parseInt(s.id) === parseInt(id); })?.nombre || '';
        const mensaje = activar
            ? '¿Reactivar el subsistema "' + nombre + '"?'
            : '¿Desactivar el subsistema "' + nombre + '"? Los modulos dentro de el deben estar inactivos.';

        mostrarConfirmacion(mensaje, async function() {
            try {
                const formData = new FormData();
                formData.append('action', 'toggle_subsistema');
                formData.append('id', id);
                formData.append('active', activar ? '1' : '0');

                const resp = await fetch('actualizar_gestor_modulos_n.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await resp.json();

                if (data.success) {
                    mostrarExito(data.message);
                    cargarDatos();
                } else {
                    mostrarError(data.message);
                }
            } catch (e) {
                mostrarError('Error de conexion: ' + e.message);
            }
        });
    }

    // ============================================================
    // CRUD: MODULOS
    // ============================================================

    /**
     * Abre el modal para crear o editar un modulo
     */
    function abrirModalModulo(id) {
        document.getElementById('modalModuloTitulo').textContent = id ? 'Editar Modulo' : 'Nuevo Modulo';
        document.getElementById('editModuloId').value = id || 0;

        // Limpiar file input
        document.getElementById('modImagen').value = '';
        document.getElementById('modImagenActual').style.display = 'none';

        if (id) {
            const m = modulosData.find(function(item) { return parseInt(item.id) === parseInt(id); });
            if (m) {
                document.getElementById('modNombre').value = m.nombre || '';
                document.getElementById('modDescripcion').value = m.descripcion || '';
                document.getElementById('modSubsistema').value = m.subsistema_id;
                document.getElementById('modRutaBase').value = m.ruta_base || '';
                document.getElementById('modOrden').value = m.orden || 0;
                document.getElementById('modColor').value = m.color || '#003876';

                if (m.imagen) {
                    document.getElementById('modImagenActual').textContent = 'Actual: ' + m.imagen;
                    document.getElementById('modImagenActual').style.display = 'block';
                }
            }
        } else {
            document.getElementById('modNombre').value = '';
            document.getElementById('modDescripcion').value = '';
            document.getElementById('modSubsistema').value = subsistemaSeleccionado || '';
            document.getElementById('modRutaBase').value = '';
            document.getElementById('modOrden').value = '0';
            document.getElementById('modColor').value = '#003876';
        }

        mostrarModal('modalModulo');
    }

    /**
     * Guarda (crea o actualiza) un modulo
     */
    async function guardarModulo() {
        const id = parseInt(document.getElementById('editModuloId').value) || 0;
        const nombre = document.getElementById('modNombre').value.trim();
        const descripcion = document.getElementById('modDescripcion').value.trim();
        const subsistema_id = parseInt(document.getElementById('modSubsistema').value) || 0;
        const ruta_base = document.getElementById('modRutaBase').value.trim();
        const orden = parseInt(document.getElementById('modOrden').value) || 0;
        const color = document.getElementById('modColor').value.trim();

        if (!nombre) {
            mostrarError('El nombre del modulo es obligatorio');
            return;
        }
        if (!subsistema_id) {
            mostrarError('Debe seleccionar un subsistema');
            return;
        }

        const formData = new FormData();
        formData.append('action', id ? 'editar_modulo' : 'crear_modulo');
        formData.append('nombre', nombre);
        formData.append('descripcion', descripcion);
        formData.append('subsistema_id', subsistema_id);
        formData.append('ruta_base', ruta_base);
        formData.append('orden', orden);
        formData.append('color', color);
        if (id) formData.append('id', id);

        const fileInput = document.getElementById('modImagen');
        if (fileInput.files.length > 0) {
            formData.append('imagen', fileInput.files[0]);
        }

        try {
            const resp = await fetch('actualizar_gestor_modulos_n.php', {
                method: 'POST',
                body: formData
            });
            const data = await resp.json();

            if (data.success) {
                ocultarModal('modalModulo');
                mostrarExito(data.message);
                cargarDatos();
            } else {
                mostrarError(data.message);
            }
        } catch (e) {
            mostrarError('Error de conexion: ' + e.message);
        }
    }

    /**
     * Activa o desactiva un modulo
     */
    function toggleModulo(id, activar) {
        const nombre = modulosData.find(function(m) { return parseInt(m.id) === parseInt(id); })?.nombre || '';
        const mensaje = activar
            ? '¿Reactivar el modulo "' + nombre + '"?'
            : '¿Desactivar el modulo "' + nombre + '"? Los formularios dentro de el deben estar inactivos.';

        mostrarConfirmacion(mensaje, async function() {
            try {
                const formData = new FormData();
                formData.append('action', 'toggle_modulo');
                formData.append('id', id);
                formData.append('active', activar ? '1' : '0');

                const resp = await fetch('actualizar_gestor_modulos_n.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await resp.json();

                if (data.success) {
                    mostrarExito(data.message);
                    cargarDatos();
                } else {
                    mostrarError(data.message);
                }
            } catch (e) {
                mostrarError('Error de conexion: ' + e.message);
            }
        });
    }

    // ============================================================
    // INICIALIZACION
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        cargarDatos();
    });
    </script>

</body>
</html>
