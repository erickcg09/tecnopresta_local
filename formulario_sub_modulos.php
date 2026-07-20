<?php
// === INICIO DE SESIÓN ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === BLOQUEAR ACCESO DIRECTO ====
if (!defined('ACCESO_SEGURO')) {

    http_response_code(403);

    exit("Acceso directo no permitido");
}
?>

<!doctype html>
<html lang="es">
<head>

    <!-- === META CONFIGURACIÓN === -->
    <meta charset="utf-8"> <!-- Soporte de acentos -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive -->

    <title>TecnoPresta | Formularios</title>

    <!-- ====  BOOTSTRAP 5 ===  -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- ICONOS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">

    <!-- ESTILOS INSTITUCIONALES -->
    <link rel="stylesheet" href="assets/css/nueva-identidad.css">

    <link rel="stylesheet" href="css/formulario_menu_principal.css" />

    <!-- ALPINE JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- BOOTSTRAP JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="bootstrap5/js/bootstrap.bundle.min.js"></script>

</head>

<!-- layout-page permite sticky footer = el body ocupe todo el espacio, junto con el header y el footer-->
<body class="layout-page">

<!-- === HEADER === -->
<?php include 'partials/header.php'; ?>

<!-- ===== CONTENIDO PRINCIPAL ===== -->
<main class="container py-4 contenido-principal"
      x-data="subModulosApp()"  
      x-init="init()">          <!-- Ejecuta función init -->

    <!--==== HERO / ENCABEZADO ===== -->  
    <div class="hero-box mb-4 fade-enter">
        <div class="row align-items-center">
            <div class="col-md-8">        
                <div class="d-flex align-items-center gap-3">

                    <div class="hero-icon">
                        <!-- IMAGEN DEL MÓDULO -->
                        <img 
                            :src="modulo.imagen || 'assets/img/default-modulo.svg'" 
                            class="card-img"                            
                            alt="Imagen del módulo"
                        >
 
                    </div>
                    <!-- INFORMACIÓN DEL MÓDULO -->
                    <div>

                        <!-- Nombre del módulo -->
                        <h2 class="fw-bold" x-text="modulo.nombre"></h2>

                        <!-- Descripción -->
                        <p class="opacity-75" x-text="modulo.descripcion"></p>

                    </div>

                </div>
            </div>

            <!-- === BUSCADOR ==== -->
            <div class="col-md-4 mt-3 mt-md-0">
                <input type="text"
                    class="form-control search-box"
                    placeholder="Buscar formulario..."
                    x-model.debounce.300ms="buscar"> <!-- debounce evita buscar en cada tecla -->
            </div>
        </div>
    </div>

    <!-- ==== LOADER ==== -->
    <div x-show="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-3">Cargando formularios...</p>
    </div>

    <!-- ==== ERROR ==== -->
    <div class="alert alert-danger"
         x-show="error"
         x-text="error">
    </div>

    <!-- ==== GRID DE FORMULARIOS ===  -->
    <div class="row" x-show="!loading">

        <!-- Recorre cada formulario -->
        <template x-for="formulario in formulariosFiltrados()" :key="formulario.id">

            <div class="col-md-6 col-xl-4 mb-4">
                <!-- llama al archivo navegar.php y le agrega la ruta del formulario que es el nombre del archivo -->
                <a :href="
                        'navegar.php?ruta=' + formulario.ruta
                        + '&subsistema_id=' + subsistema.id
                        + '&modulo_id=' + modulo.id
                        + '&formulario_id=' + formulario.id"
                    class="card h-100 shadow-sm text-decoration-none d-block"
                    :aria-label="'Ingresar al formulario ' + formulario.nombre"
                >
                <!-- CARD -->
                <div class="card card-premium h-100 fade-enter">
                <!-- <div class="card card-premium card-animada"> -->
                
                    <div class="card-body pt-3 pb-4 px-4"> <!-- pt-3 espacio arriba | pb-4 = espacio abajo | px-4 = espacio lados -->
                        <div class="img-container">
                            <!-- IMAGEN -->
                            <img 
                                :src="formulario.imagen || 'assets/img/default-form.svg'"
                                class="img-fluid"
                                loading="lazy"
                                alt="Imagen del Formulario"
                            >  
                        </div>

                    <!-- NOMBRE -->
                    <h5 class="fw-bold"
                        x-html="highlightSeguro(formulario.nombre)">
                    </h5>

                    <!-- DESCRIPCIÓN -->
                    <p class="text-muted small"
                       x-html="highlightSeguro(formulario.descripcion)">
                    </p>

                    <!-- Pie del CARD -- FORMULARIOS -->                                    
                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex flex-wrap gap-2">
                            <!-- ACCIONES -->
                            <div class="mt-2">

                                <!-- Itera acciones del formulario -->
                                <template x-for="accion in (formulario.acciones || [])" :key="accion.id">

                                    <span class="badge bg-light text-dark me-1"
                                        x-text="accion.nombre">
                                    </span>
                                    
                                </template>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </template>

    </div>

    <!-- === MENSAJE SIN RESULTADOS === -->
    <div x-show="!loading && formulariosFiltrados().length === 0"
         class="text-center py-5">

        <i class="bi bi-search fs-1 text-muted"></i>
        <h5 class="mt-3">Sin resultados</h5>
        <p class="text-muted">No se encontraron formularios.</p>

    </div>

    <!-- Botón flotante Volver al Dashboard  / :href los : es para que utilice alpine-->
    <a :href="volverUrl" 
        class="btn-disponibilidad"
        style="bottom: 100px;" 
        data-tooltip="Regresar">

        <i class="bi bi-arrow-left-circle-fill"></i>
    </a>

</main>

<!-- ==== FOOTER ==== -->
<?php include 'partials/footer.php'; ?>

<!-- ==== SCRIPT PRINCIPAL ==== -->
<script>

window.subModulosApp = function () {

    return {

        // ====== VARIABLES ====
        loading: true,   // Control de carga
        error: "",       // Mensajes de error
        buscar: "",      // Texto del buscador

        subsistema: {},  // Objeto subsistema
        modulo: {},      // Objeto módulo
        formularios: [], // Lista de formularios

        volverUrl: "",   // URL de regreso a módulos (se construye dinámicamente)

        // === INIT === 
        async init() {

            // Leer parámetros URL
            const params = new URLSearchParams(window.location.search);

            // Obtener IDs (IMPORTANTE: usar ID, no nombre)
            const subsistemaId = parseInt(params.get("subsistema_id"));
            const moduloId = parseInt(params.get("modulo_id"));

            // Validación
            if (!subsistemaId || !moduloId) {
                this.error = "Parámetros inválidos";
                this.loading = false;
                return;
            }

            // ====  SE CONSTRUYE LA URL DE REGRESO ====
            //this.volverUrl = "formulario_modulos.php?subsistema_id=" + subsistemaId;
            this.volverUrl = "navegar.php?ruta=formulario_modulos.php&subsistema_id=" + subsistemaId;
            // Cargar datos
            await this.cargarDatos(subsistemaId, moduloId);
        },

        // ==== FETCH DATOS ====
        async cargarDatos(subsistemaId, moduloId) {

            try {

                const response = await fetch("sql/formulario_principal.php");

                const data = await response.json();

                // Validación backend
                if (!data.Ok) {
                    this.error = "Error en backend";
                    return;
                }

                const lista = data.data || [];

                // Buscar subsistema por ID
                const subsistema = lista.find(
                    s => s.id === subsistemaId
                );

                if (!subsistema) {
                    this.error = "Subsistema no encontrado";
                    return;
                }

                // Buscar módulo por ID
                const modulo = subsistema.modulos.find(
                    m => m.id === moduloId
                );

                if (!modulo) {
                    this.error = "Módulo no encontrado";
                    return;
                }

                // Ordenar formularios por campo "orden"
                const formulariosOrdenados = (modulo.formularios || [])
                    .sort((a, b) => a.orden - b.orden);

                // Asignar datos
                this.subsistema = subsistema;
                this.modulo = modulo;
                this.formularios = formulariosOrdenados;

            } catch (e) {

                console.error(e);
                this.error = "Error al cargar datos";

            } finally {

                this.loading = false;

            }
        },

        // ==== NORMALIZAR TEXTO (sin tildes) ====
        normalizar(texto) {
            return (texto || "")
                .toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "");
        },

        // === ESCAPE HTML (seguridad) ====
        escapeHtml(texto) {
            return String(texto || "")
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;");
        },

        // === HIGHLIGHT SEGURO ====
        highlightSeguro(texto) {

            if (!this.buscar) return this.escapeHtml(texto);

            const textoNorm = this.normalizar(texto);
            const buscarNorm = this.normalizar(this.buscar);

            const index = textoNorm.indexOf(buscarNorm);

            if (index === -1) return this.escapeHtml(texto);

            return texto.substring(0, index)
                + "<mark class= mep-highlight>" + texto.substring(index, index + this.buscar.length) + "</mark>"
                + texto.substring(index + this.buscar.length);
        },
        // === FILTRO DE FORMULARIOS ===
        formulariosFiltrados() {

            const texto = this.normalizar(this.buscar);

            if (!texto) return this.formularios;

            return this.formularios.filter(f =>
                this.normalizar(f.nombre).includes(texto) ||
                this.normalizar(f.descripcion).includes(texto)
            );
        }

    }

}

</script>

</body>
</html>