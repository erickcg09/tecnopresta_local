<?php
session_start();
?>

<!doctype html>
<html lang="es">
<head>
    <!-- ========= META CONFIGURACIÓN ========= -->
    <meta charset="utf-8"> <!-- Soporte de acentos -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/> <!-- Responsive -->
    
    <title>TecnoPresta | Formualarios </title>

    <!-- Bootstrap 5 CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ICONOS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    
    <link rel="stylesheet" href="css/formulario_menu_principal.css" />

    <!-- Alpine js -- Defer es para utilizar alpine antes de definrlo -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ESTILOS INSTITUCIONALES -->
    <link rel="stylesheet" href="assets\css\nueva-identidad.css"/>

</head>

<!-- <body> 
La clase layout-page es para que el body ocupe todo el espacio, junto con el header y el footer
pero al main también se debe ajustar se agrega la clase contenido-principal
-->
<body class="layout-page">
<!-- HEADER -->
<?php include 'partials/header.php'; ?>
<!-- ===== CONTENIDO PRINCIPAL ===== -->
<main class="container py-4 contenido-principal" 
        x-data="subModulosApp()" 
        x-init="init()">
    <!-- HERO / ENCABEZADO -->
    <div class="hero-box mb-4 fade-enter">
        <div class="row align-items-center">

            <!-- <div class="col-md-8"> -->
                <div class="d-flex align-items-center gap-3">

                    <!-- <div class="hero-icon"> -->
                        <!-- <i class="bi bi-grid-1x2-fill"></i> -->
                        <img
                            :src="modulo.imagen || 'assets/img/default-modulo.svg'"
                            width="80"
                            height="80"
                            class="rounded"
                            style="object-fit: cover"
                            alt="Imagen del modulo"
                        />
                    <!-- </div>  -->
                    <!-- ===== INFORMACIÓN DEL MODULO ===== -->
                    <div>
                        <h2 class="mb-1 fw-bold" x-text="modulo.nombre"></h2>
                        <p class="mb-0 opacity-75" x-text="modulo.descripcion">
                            <!-- Seleccione uno de los módulos habilitados para continuar. -->
                        </p>
                    </div>

                </div>
            <!-- </div> -->

            <!-- ========= BUSCADOR ======= -->
            <div class="col-md-4 mt-3 mt-md-0">

                <input type="text"
                       class="form-control search-box"
                       placeholder="Buscar formulario"
                       x-model.debounce.350ms="buscar">
                    <!-- .debounce.350ms es para no buscar con cada tecla-->
            </div>

        </div>
    </div>

    <!-- LOADER -->
    <div class="text-center py-5" x-show="loading">
        <div class="spinner-border text-primary"></div>
        <p class="mt-3">Cargando Formularios...</p>
    </div>

    <!-- ERROR -->
    <div class="alert alert-danger" x-show="error" x-text="error"></div>

    <!-- ====== GRID DE FORMULARIOS ======= -->
    <div class="row" x-show="!loading">
        <!-- ====== RECORRE CADA FORMULARIO ========= -->
        <template x-for="formulario in formulariosFiltrados()" :key="formulario.id">

            <div class="col-md-6 col-xl-4 mb-4">
                <!-- Enlace al módulo -->
                <a :href="formulario.ruta"
                   class="text-decoration-none">

                    <div class="card card-premium h-100 fade-enter">

                        <div class="card-body p-4">
                            <img
                                :src="formulario.imagen || 'assets/img/default-form.svg'"
                                width="150"
                                height="150"
                                class="card-img"
                                alt="Imagen del formulario"
                                style="object-fit: cover"
                                loading="lazy" 
                            /> 
                            <!-- loading="lazy" ---- Permite cargar la imagen de forma diferida, 
                                mejorando el rendimiento de la página al cargar solo las imágenes visibles inicialmente. 
                                Las imágenes que no estén en el viewport se cargarán cuando el usuario se desplace hacia ellas. 
                            -->
                            <!-- Nombre del Módulo con resaltado -->
                            <h5 class="fw-bold text-dark mb-2"
                                x-html="highlightSeguro(formulario.nombre)">
                            </h5>
                            <!-- <h5 class="fw-bold text-dark mb-2"
                                x-text="modulo.nombre">
                            </h5>  -->

                            <!-- Descripción con resaltado -->
                             <p class="text-muted small mb-4" 
                                x-html="highlightSeguro(formulario.descripcion)">
                                !-- Ingresar al módulo y gestionar funciones disponibles. --
                            </p>
                            <!-- <p class="text-muted small mb-4" x-text="modulo.descripcion">
                                !-- Ingresar al módulo y gestionar funciones disponibles. --
                            </p> -->

                            <!-- Pie del CARD -- FORMULARIOS -->                                    
                            <!-- <div class="border-top pt-3 mt-3">
                                            
                                <small class="text-muted d-block mb-2 fw-semibold">
                                    Formularios disponibles
                                </small>

                                <div class="d-flex flex-wrap gap-2">

                                    <template x-for="formulario in (modulo.formularios || [])" :key="formulario.id">

                                        !-- <span class="badge badge-modulo"                                                        
                                                x-text="formulario.nombre">
                                        </span>  --
                                        !-- Nombre formulario con resaltado --
                                        <span class="badge badge-modulo" 
                                            x-html="highlightSeguro(formulario.nombre)"> 
                                        </span>
                                        
                                    </template>

                                </div>

                            </div> -->
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
<a href="formulario_menu_principal.php" class="btn-disponibilidad" 
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
        subsistema: {},
        mmdolo: {},
        formularios: [],

        /* ================= INIT ================= */
        async init() {
            //Leer parámetros URL
            const url = new URLSearchParams(window.location.search);
            //Lee IDs desde URL
            const subsistemaId = parseInt(params.get("subsistema_id"));
            const moduloId = parseInt(params.get("modulo_id"));

            //Validación
            if (!subsistemaId || !moduloId) {
                this.error = "Parámetros no válidos"; //this es cuando es una variable global, no está declarada con const
                this.loading = false;
                return;
            }

            //Cargar datos
            await this.cargarDatos(subsistemaId, moduloId);
        },

        /* ================= Consulta a BACKEND --- FETCH ================= */
        async cargarDatos(subsistemaId, moduloId) {
            try {

                /*const resp = await fetch("sql/formulario_principal.php", {
                    method: "GET",
                    credentials: "include"
                });*/
                const resp = await fetch("sql/formulario_principal.php");

                const data = await resp.json();

                /* Si backend devuelve error */
                if (data.error) {
                    this.error = data.error;
                    return;
                }

                //Lista general
                const lista = data.data || [];

                ///* Buscar subsistema */
                /*const encontrado = lista.find(
                    item => item.nombre === nombreSubsistema
                );*/
                // === Busca el Subsistema por ID
                const subsistema = lista.find(
                    s => s.id === subsistemaId
                );

                if (!subsistema) {
                    this.error = "Subsistema no encontrado";
                    return;
                }

                // === Busca Módulo por ID
                const modulo = subsistema.modulos.find(
                    m => m.id === moduloId
                );

                if (!modulo) {
                    this.error = "Módulo no encontrado";
                    return;
                }

                //Guarda el objeto completo y no solo los módulos
                this.subsistema = subsistema;
                this.modulo = modulo;
                this.formularios = (modulo.formularios || [])
                    .sort((a, b) => a.orden - b.orden);

            } catch (e) {
                console.error(e);
                this.error = "Error al cargar datos.";
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