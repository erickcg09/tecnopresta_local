
<?php 

/*
================================================================
TecnoPresta — formulario_menu_principal.php
Propósito: Página principal del sistema. Muestra los módulos
           disponibles según el rol del usuario.

FLUJO DE LA FOTO DE PERFIL:
  1. Login en index.html → Graph API devuelve foto
  2. fotoAzure.php guarda foto en $_SESSION['funcionario']['fotoPerfil']
  3. usuarioAzure.php lee la sesión y expone los datos
  4. header.php usa $fotoPerfil directamente en PHP
  5. No se necesita JavaScript para la foto en el header
================================================================
*/

/*
  PASO 1: Incluir usuarioAzure.php PRIMERO, antes del HTML.
  Este archivo inicia la sesión y define obtenerUsuarioSesion().
*/
//require_once 'usuarioAzure.php';
require_once __DIR__ . '/usuarioAzure.php';
/*
  PASO 2: Obtener datos del usuario desde la sesión.
  Si no hay sesión válida, redirige al login.
*/
$usuario_azure = obtenerUsuarioSesion();
echo "<script>console.log('Datos del usuario desde formulario_menu_principal.php (JS):', " . json_encode($usuario_azure) . ");</script>";
/*
if (!$usuario_azure) {
    header('Location: index.html');
    exit();
}*/

/*
  PASO 3: Preparar variables para header.php.
  header.php usa estas variables directamente.
  Se definen ANTES del include para que estén disponibles.
*/
$fotoPerfil = $usuario_azure['fotoPerfil'];

$nombreCompleto = trim(
    ($usuario_azure['nombre']    ?? '') . ' ' .
    ($usuario_azure['apellidos'] ?? '')
);
if (empty($nombreCompleto)) {
    $nombreCompleto = 'Usuario';
}

$dependencia = $usuario_azure['dependencia'] ?? 'Dependencia no disponible';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="manifest" href="manifest.json" />

    <!-- Bootstrap 5 CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/formulario_menu_principal.css" />
    <link rel="stylesheet" href="assets/css/nueva-identidad.css"/>

    <!--
    ORDEN DE SCRIPTS — CRÍTICO:
    1. MSAL primero, sin defer (disponible para toda la página)
    2. Alpine con defer (obligatorio en v3)
    3. Bootstrap y jQuery al final del body
    4. formulario_login_e.js al final del body, antes del componente Alpine
    -->
    <script src="https://alcdn.msauth.net/browser/2.38.3/js/msal-browser.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="layout-page">

    <!--
    PASO 4: Incluir header.php DESPUÉS de definir las variables.
    header.php usa $fotoPerfil, $nombreCompleto y $dependencia
    directamente en PHP. No necesita JavaScript.
    -->
    <?php include 'partials/header.php'; ?>

    <main class="contenido-principal">
        <div x-data="menuApp()" x-init="init()">

            <!-- Mensajes de error -->
            <div class="container mt-3">
                <div x-show="error" class="alert alert-danger" x-text="error"></div>
            </div>

            <!-- Loader -->
            <div class="text-center mt-5" x-show="loading">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Cargando módulos...</p>
            </div>

            <!-- Saludo y etiqueta de sección -->
            <div class="container mb-4">
                <h4 class="fw-semibold mb-1" style="color: var(--mep-blue);">
                    <!--
                    El nombre también se puede mostrar aquí desde PHP
                    para que sea visible inmediatamente sin esperar
                    a que Alpine cargue los datos del sessionStorage.
                    -->
                    Bienvenido/a,
                    <?= htmlspecialchars(
                            explode(' ', trim($usuario_azure['nombre'] ?? 'Usuario'))[0],
                            ENT_QUOTES, 'UTF-8'
                        ) ?>
                </h4>
                <p class="text-muted small mb-0" x-text="fechaHoy()"></p>
                <hr>
                <p class="text-uppercase text-muted small fw-semibold mb-3"
                   style="letter-spacing:6px;">
                    Módulos del Sistema
                </p>
            </div>

            <!-- Grid de módulos -->
            <div class="container mt-4" x-show="!loading">
                <div class="row">
                    <template x-for="subsistema in subsistemas" :key="subsistema.nombre">
                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-4" role="listitem">
                            <a :href="'formulario_modulos.php?subsistema_id=' + subsistema.id"
                               class="card h-100 shadow-sm text-decoration-none d-block"
                               :aria-label="'Ingresar al módulo ' + subsistema.nombre">
                                <div class="card card-premium h-100 d-flex flex-column p-4 fade-enter">
                                    <div class="img-container">
                                        <img :src="subsistema.imagen"
                                             class="img-fluid"
                                             loading="lazy"
                                             alt="Imagen del subsistema" />
                                    </div>
                                    <h5 class="card-title fw-semibold mb-2"
                                        style="font-size:1rem;color:#0f1f3d;"
                                        x-text="subsistema.nombre">
                                    </h5>
                                    <p class="card-text text-muted small flex-grow-1"
                                       style="line-height:1.6;"
                                       x-text="subsistema.descripcion">
                                    </p>
                                    <div class="border-top pt-3 mt-3">
                                        <small class="text-muted d-block mb-2 fw-semibold">
                                            Módulos disponibles
                                        </small>
                                        <div class="d-flex flex-wrap gap-2">
                                            <template x-for="modulo in subsistema.modulos" :key="modulo.id">
                                                <span class="badge badge-modulo"
                                                      x-text="modulo.nombre">
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </template>
                </div>
            </div>

        </div><!-- /x-data -->
    </main>

    <?php include 'partials/footer.php'; ?>

    <!-- Bootstrap JS y jQuery al final del body -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!--
    formulario_login_e.js se carga aquí, al final del body,
    DESPUÉS de que MSAL ya está en memoria (se cargó en <head>).
    Solo se necesita para funciones que usan MSAL desde JS
    (no para la foto, que ya viene de PHP).
    -->
    <script> window.tipoLoginActivo = "especial"; </script>
    <script src="js/formulario_login_e.js"></script>

    <!-- Componente Alpine.js -->
    <script>
    /*
      fechaHoy() — global para Alpine y PHP pueden usarla
    */
    function fechaHoy() {
        return new Date().toLocaleDateString('es-ES', {
            weekday: 'long', year: 'numeric',
            month: 'long',   day: 'numeric'
        });
    }

    window.menuApp = function () {
        return {
            /*
              Estado del componente.
              La foto NO se maneja aquí porque ya viene de PHP
              en el header. Alpine solo maneja los módulos.
            */
            usuario: {
                nombre:      '<?= addslashes(htmlspecialchars($usuario_azure['nombre'] ?? '', ENT_QUOTES)) ?>',
                institucion: '<?= addslashes(htmlspecialchars($dependencia, ENT_QUOTES)) ?>',
            },
            loading:     true,
            error:       '',
            subsistemas: [],

            async init() {
                console.log("INIT Ejecutando");

                /*
                  Verificación de sesión en frontend.
                  Si no hay sesión JS, redirige al login.
                  Nota: la verificación principal ya la hizo PHP arriba
                  (obtenerUsuarioSesion() → header Location).
                  Esta es una segunda capa de seguridad del lado cliente.
                */
                const sesion = sessionStorage.getItem("sesion");
                if (!sesion) {
                    console.log("No hay sesión en sessionStorage.");
                    window.location.href = "index.html";
                    return;
                }

                /*
                  Solo carga los módulos. La foto ya la manejó PHP.
                */
                await this.cargaModulos();
            },

            async cargaModulos() {
                console.log("Iniciando CargaModulos");
                try {
                    const response = await fetch("sql/formulario_principal.php", {
                        method: "GET",
                        credentials: "include",
                    });

                    const data = await response.json();
                    console.log("Respuesta BackEnd:", data);

                    if (data.error) {
                        this.error = data.error;
                        return;
                    }

                    this.subsistemas = data.data || [];

                } catch (e) {
                    this.error = "Error al cargar módulos";
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            fechaHoy() {
                return fechaHoy();
            }
        };
    };
    </script>

</body>
</html>
