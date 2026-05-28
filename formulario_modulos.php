<?php
// === INICIAR SESIÓN ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BLOQUEAR ACCESO DIRECTO

if (!defined('ACCESO_SEGURO')) {

    http_response_code(403);

    exit("Acceso directo no permitido");
}

/* VALIDAR SESIÓN */
/*if (!isset($_SESSION['funcionario'])) {

    header("Location:index.html");
    exit;
}
*/
?>

<!doctype html>
<html lang="es">
<head>
    <!-- === META CONFIGURACIÓN === -->
    <meta charset="utf-8"> <!-- Soporte de acentos -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive -->

    <title>TecnoPresta | Módulos</title>

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

<!-- <body> 
La clase layout-page es para que el body ocupe todo el espacio, junto con el header y el footer
pero al main también se debe ajustar se agrega la clase contenido-principal
-->
<body class="layout-page">
<!-- HEADER -->
<?php include 'partials/header.php'; ?>

<main class="container py-4 contenido-principal" x-data="modulosApp()" x-init="init()">
    <!-- HERO / ENCABEZADO -->
    <div class="hero-box mb-4 fade-enter">
        <div class="row align-items-center">

            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">

                    <div class="hero-icon">
                        <img
                            :src="subsistema.imagen"
                            width="80"
                            height="80"
                            class="card-img mt-0"
                            style="object-fit: cover"
                            alt="Imagen del subsistema"
                        />
                    </div> 

                    <div>
                        <h2 class="mb-1 fw-bold" x-text="subsistema.nombre"></h2>
                        <p class="mb-0 opacity-75" x-text="subsistema.descripcion">
                            <!-- Seleccione uno de los módulos habilitados para continuar. -->
                        </p>
                    </div>

                </div>
            </div>

            <!-- Buscador -->
            <div class="col-md-4 mt-3 mt-md-0">

                <input type="text"
                       class="form-control search-box"
                       placeholder="Buscar módulo | formulario"
                       x-model.debounce.350ms="buscar">
                    <!-- .debounce.350ms es para no buscar con cada tecla-->
            </div>

        </div>
    </div>

    <!-- LOADER -->
    <div class="text-center py-5" x-show="loading">
        <div class="spinner-border text-primary"></div>
        <p class="mt-3">Cargando módulos...</p>
    </div>

    <!-- ERROR -->
    <div class="alert alert-danger" x-show="error" x-text="error"></div>

    <!-- GRID DE MODULOS -->
    <div class="row" x-show="!loading">
  
        <template x-for="modulo in modulosFiltrados()" :key="modulo.id">
        <!-- <template x-for="modulo in modulos" :key="modulo.id"> -->

            <div class="col-md-6 col-xl-4 mb-4">
                <!-- Enlace al módulo -->
                <!-- <a :href="modulo.ruta"
                   class="text-decoration-none"> -->
                <a :href="'navegar.php?ruta=formulario_sub_modulos.php'
                        + '&subsistema_id=' + subsistema.id
                        + '&modulo_id=' + modulo.id"
                    class="card h-100 shadow-sm text-decoration-none d-block"
                    :aria-label="'Ingresar al módulo ' + modulo.nombre"
                >
                    <div class="card card-premium h-100 fade-enter">

                        <div class="card-body p-4">
                            <div class="img-container">
                                <img
                                    :src="modulo.imagen"
                                    class="card-img"
                                    alt="Imagen del modulo"
                                    loading="lazy" 
                                /> 
                            </div>
                            <!-- loading="lazy" ---- Permite cargar la imagen de forma diferida, 
                                mejorando el rendimiento de la página al cargar solo las imágenes visibles inicialmente. 
                                Las imágenes que no estén en el viewport se cargarán cuando el usuario se desplace hacia ellas. 
                            -->
                            <!-- Nombre del Módulo con resaltado -->
                            <h5 class="fw-bold text-dark mb-2"
                                x-html="highlightSeguro(modulo.nombre)">
                            </h5>

                            <!-- Descripción con resaltado -->
                             <p class="text-muted small mb-4" 
                                x-html="highlightSeguro(modulo.descripcion)">
                                !-- Ingresar al módulo y gestionar funciones disponibles. --
                            </p>

                            <!-- Pie del CARD -- FORMULARIOS -->                                    
                            <div class="border-top pt-3 mt-3">
                                            
                                <small class="text-muted d-block mb-2 fw-semibold">
                                    Formularios disponibles
                                </small>

                                <div class="d-flex flex-wrap gap-2">

                                    <template x-for="formulario in (modulo.formularios || [])" :key="formulario.id">
                                        <a :href="'navegar.php?ruta=' + formulario.ruta"

                                            class="badge badge-modulo text-decoration-none"
                                            :title="'Abrir formulario: ' + formulario.nombre"
                                        >
                                            <!-- Nombre del formulario -->
                                            <span x-html="highlightSeguro(formulario.nombre)"></span>

                                        </a>
                                    </template>

                                </div>

                            </div>
                        </div>

                    </div>

                </a>

            </div>

        </template>

    </div>

    <!-- MENSAJE VACÍO -->
    <div x-show="!loading && modulosFiltrados().length==0" class="empty-box">

        <i class="bi bi-search fs-1 text-muted"></i>

        <h5 class="mt-3">Sin resultados</h5>

        <p class="text-muted mb-0">
            No se encontraron módulos con ese criterio.
        </p>

    </div>

</main>

<?php include 'partials/footer.php'; ?>

<!-- Botón flotante Volver al Dashboard -->
<a href="navegar.php?ruta=formulario_menu_principal.php" class="btn-disponibilidad" 
    style="bottom: 100px;" title="Volver a Módulos del Sistema">
    <i class="bi bi-arrow-left-circle-fill"></i>
</a>

<!-- SCRIPT PRINCIPAL -->
<script>

window.modulosApp = function () {

    return {

        loading: true,
        error: "", //mensajes

        //Caja de búsqueda
        buscar: "",

        //Objeto principal
        subsistema: {
            nombre: "",
            descripcion:"",
            imagen:"",
            modulos:[]
        },

        /* ================= INIT ================= */
        async init() {
            //Leer parámetros URL
            const url = new URLSearchParams(window.location.search);
            
            //Lee el ID de Subsistema desde URL
            const subsistemaId = parseInt(url.get("subsistema_id"));
            
            //Validación
            if (!subsistemaId) {
                this.error = "Subsistema no válido";
                this.loading = false;
                return;
            }
            
            await this.cargarDatos(subsistemaId);
        },

        /* ================= Consulta a BACKEND --- FETCH ================= */
        //async cargarDatos(nombreSubsistema) {
        async cargarDatos(subsistemaId) {
            try {

                const response = await fetch("sql/formulario_principal.php", {
                    method: "GET",
                    credentials: "include"
                });

                const data = await response.json();

                /* Si backend devuelve error */
                if (data.error) {
                    this.error = data.error;
                    return;
                }

                //Lista general
                const lista = data.data || [];

                ///* Buscar subsistema por ID */
                const encontrado = lista.find(
                    item => item.id === subsistemaId
                );
                /*const encontrado = lista.find(
                    item => item.nombre === nombreSubsistema
                );*/


                //Si no existe
                if (!encontrado) {
                    this.error = "No se encontraron módulos.";
                    return;
                }

                //Guarda el objeto completo y no solo los módulos
                this.subsistema = encontrado;

            } catch (e) {
                console.error(e);
                this.error = "Error al cargar módulos.";
            } finally {
                this.loading = false;
            }
        },

        /* ================= NORMALIZAR :Evitar problemas de acento en las búsquedas ================= */
        normalizar(texto) {
            return (texto || "")
                .toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "");
        },

        /* ================= ESCAPE HTML ================= */
        escapeHtml(texto) {
            return String(texto || "")
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        },

        /* ================= RESALTADO SEGURO ================= */
        highlightSeguro(texto) {

            texto = String(texto || "");

            // Si no hay búsqueda → devuelve texto limpio
            if (!this.buscar || this.buscar.trim() === "") {
                return this.escapeHtml(texto);
            }

            const termino = this.buscar.trim();

            // Versiones normalizadas (sin tildes)
            const textoNorm = this.normalizar(texto);
            const terminoNorm = this.normalizar(termino);

            const index = textoNorm.indexOf(terminoNorm);

            // Si no hay coincidencia → devolver limpio
            if (index === -1) {
                return this.escapeHtml(texto);
            }

            // Obtener partes del texto ORIGINAL (con tildes)
            const inicio = texto.substring(0, index);
            const match = texto.substring(index, index + termino.length);
            const fin = texto.substring(index + termino.length);

            // Retornar con escape + resaltado
            return `
                ${this.escapeHtml(inicio)}
                <mark class="mep-highlight">${this.escapeHtml(match)}</mark>
                ${this.escapeHtml(fin)}
            `;
        },

        /* ================= FILTRO ================= */
        modulosFiltrados() {

            const texto = this.normalizar(this.buscar.trim());

            /*si caja esta vacía, devuelve todo */
            if (texto === "") {
                return this.subsistema.modulos;
            }

            return this.subsistema.modulos.filter(modulo => {

                //Busca por nombre del módulo
                const coincideModulo = 
                    this.normalizar(modulo.nombre || "").includes(texto) ||
                    this.normalizar(modulo.descripcion || "").includes(texto);

                // Busca dentro de los formularios
                const coincideFormulario = (modulo.formularios || []).some(f =>
                    this.normalizar(f.nombre || "").includes(texto) ||
                    this.normalizar(f.descripcion || "").includes(texto)
                );

                return coincideModulo || coincideFormulario;
            });
        }

    }
}
</script>
</body>
</html>