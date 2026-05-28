<?php
/*
================================================================
TecnoPresta — partials/header.php
Propósito: Encabezado institucional MEP con foto de perfil,
           nombre del usuario y menú de opciones.

CÓMO OBTIENE LA FOTO:
  La foto viene de $_SESSION['funcionario']['fotoPerfil']
  que fue guardada por fotoAzure.php durante el login.
  NO se necesita JavaScript ni llamadas a Graph API aquí.
  Todo es PHP puro del lado del servidor.

DEPENDENCIA:
  Requiere que usuarioAzure.php ya haya sido incluido
  en la página padre (formulario_menu_principal.php).
  Las variables $fotoPerfil, $nombreCompleto y $dependencia
  deben estar definidas en el archivo padre antes del include.

  Si por alguna razón no están definidas (inclusión directa),
  este archivo las define con valores por defecto.
================================================================
*/

/*
  Verificación defensiva: si las variables no están definidas
  en el scope del archivo padre, las definimos aquí con
  valores seguros para evitar warnings de PHP.

  Esto ocurre si header.php se incluye sin haber procesado
  antes usuarioAzure.php en la página padre.
*/
if (!isset($fotoPerfil)) {
    /*
      Intenta obtener la foto directamente de la sesión
      como último recurso.
    */
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $fotoPerfil = $_SESSION['funcionario']['fotoPerfil']
                  ?? 'assets/img/avatarH.svg';
}

if (!isset($nombreCompleto) || empty(trim($nombreCompleto))) {
    $nombre    = $_SESSION['funcionario']['Nombre']    ?? '';
    $apellidos = $_SESSION['funcionario']['Apellidos'] ?? '';
    $nombreCompleto = trim("$nombre $apellidos") ?: 'Usuario';
}

if (!isset($dependencia)) {
    $dependencia = $_SESSION['funcionario']['Dependencia']
                   ?? 'Dependencia no disponible';
}

/*
  htmlspecialchars con ENT_QUOTES previene XSS en todos los
  campos que vienen de la sesión y se muestran en el HTML.
  Siempre usar en datos que provienen de fuentes externas.
*/
$nombreSeguro    = htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8');
$dependenciaSegura = htmlspecialchars($dependencia,  ENT_QUOTES, 'UTF-8');

/*
  La foto puede ser:
  a) Una ruta relativa:  "assets/img/avatarH.svg"
  b) Un base64 inline:  "data:image/jpeg;base64,..."

  En ambos casos se usa directamente en src="" sin escapar
  porque base64 no contiene caracteres peligrosos para HTML,
  y las rutas son generadas por el propio sistema.

  LÍMITE DE TAMAÑO: las fotos base64 de Microsoft Graph
  típicamente pesan entre 5KB y 50KB. No hay riesgo de
  que el HTML sea excesivamente grande.
*/
?>
<!-- ================== HEADER MEP ================== -->
<div class="mep-header">
    <div class="container">
        <div class="row align-items-center">

            <!-- ================== LOGO + NOMBRE SISTEMA ================== -->
            <div class="col-md-8">
                <div class="mep-logo">
                    <div class="mep-logo-box">
                        <img src="assets/img/logo-mep.svg"
                             alt="Logo del Ministerio de Educación Pública"
                             class="mep-logo-icon" />
                    </div>
                    <!-- Texto institucional -->
                    <div class="ms-3">
                        <div>MINISTERIO DE EDUCACI&Oacute;N P&Uacute;BLICA</div>
                        <div class="gov-text">GOBIERNO DE COSTA RICA</div>
                        <div class="gov-text" style="font-size: medium;">
                            <span style="color:#fff;">
                                Tecno<span style="color:var(--mep-gold)">Presta</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================== USUARIO + DROPDOWN ================== -->
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="dropdown position-relative">

                    <!--
                    BOTÓN DROPDOWN — muestra nombre + foto
                    La foto se carga directamente desde PHP.
                    Si la foto es base64, el navegador la muestra inline.
                    Si es una ruta SVG, se carga como archivo normal.

                    onerror: si la imagen falla por cualquier motivo
                    (base64 corrupto, archivo no encontrado), muestra
                    el avatar por defecto automáticamente.
                    -->
                    <a href="#"
                       class="d-inline-flex align-items-center text-white dropdown-toggle"
                       data-bs-toggle="dropdown"
                       data-bs-display="static"
                       aria-expanded="false">

                        <!-- Nombre del usuario -->
                        <span class="me-2 text-white-50">
                            <?= $nombreSeguro ?>
                        </span>

                        <!--
                        FOTO DE PERFIL — fuente principal: sesión PHP
                        No requiere JavaScript, no requiere MSAL,
                        no requiere llamadas a Graph API.
                        La foto viene de $_SESSION['funcionario']['fotoPerfil']
                        que se guardó en fotoAzure.php durante el login.
                        -->
                        <img
                            src="<?= $fotoPerfil ?>"
                            alt="Foto de perfil de <?= $nombreSeguro ?>"
                            class="rounded-circle"
                            width="48"
                            height="48"
                            style="object-fit:cover; border:2px solid rgba(255,255,255,.6);"
                            onerror="this.onerror=null; this.src='assets/img/avatarH.svg';"
                        />
                    </a>

                    <!-- ── MENÚ DESPLEGABLE ─────────────────────── -->
                    <div class="dropdown-menu dropdown-menu-end user-dropdown shadow animate">

                        <!-- Información del usuario en la parte superior del menú -->
                        <div class="dropdown-user-info d-flex align-items-center px-3 py-2">
                            <!--
                            Mini avatar en el menú desplegable.
                            Misma fuente que el avatar del botón.
                            -->
                            <img
                                src="<?= $fotoPerfil ?>"
                                alt="Avatar de <?= $nombreSeguro ?>"
                                class="user-mini-avatar me-2"
                                onerror="this.onerror=null; this.src='assets/img/avatarH.svg';"
                            />
                            <div>
                                <div class="user-name fw-semibold">
                                    <?= $nombreSeguro ?>
                                </div>
                                <small class="text-muted">
                                    <?= $dependenciaSegura ?>
                                </small>
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        <!-- Opciones del menú -->
                        <a class="dropdown-item user-item" href="portal_perfil.php">
                            <i class="bi bi-person-circle me-2"></i>
                            <span>Mi Perfil</span>
                        </a>

                        <a class="dropdown-item user-item logout-item" href="gameover.php">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            <span>Cerrar Sesi&oacute;n</span>
                        </a>

                    </div><!-- /dropdown-menu -->
                </div><!-- /dropdown -->
            </div><!-- /col usuario -->

        </div><!-- /row -->
    </div><!-- /container -->
</div><!-- /mep-header -->

<?php
/*
================================================================
DIAGNÓSTICO (solo en entorno de desarrollo)
Descomente el bloque siguiente TEMPORALMENTE para verificar
qué valores tienen las variables en tiempo de ejecución.
Nunca dejar activo en producción.
================================================================
if (defined('APP_ENV') && APP_ENV === 'development') {
    echo '<div style="background:#fff3cd;padding:8px;font-size:11px;font-family:monospace;">';
    echo '<strong>DEBUG header.php:</strong><br>';
    echo 'nombreCompleto: ' . $nombreSeguro . '<br>';
    echo 'dependencia: '    . $dependenciaSegura . '<br>';
    echo 'fotoPerfil tipo: ' . (str_starts_with($fotoPerfil, 'data:') ? 'base64 (' . strlen($fotoPerfil) . ' chars)' : 'ruta: ' . $fotoPerfil) . '<br>';
    echo 'SESSION keys: ' . implode(', ', array_keys($_SESSION['funcionario'] ?? [])) . '<br>';
    echo '</div>';
}
*/
?>
