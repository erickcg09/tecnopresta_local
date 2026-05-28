<?php
session_start();
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    
   

    <title>TecnoPresta | Módulos</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    
    <link rel="stylesheet" href="css/formulario_menu_principal.css" />
    <!-- <link rel="stylesheet" href="assets\css\nueva-identidad.css"/> -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script> -->

    <!-- Alpine js -- Defer es para utilizar alpine antes de definrlo -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- jQuery requerido por Bootstrap 4 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Popper requerido por dropdown -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

    <!-- Bootstrap JS -->
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->

 
    <link rel="stylesheet" href="assets\css\nueva-identidad.css"/>

</head>

<!-- <body> 
La clase layout-page es para que el body ocupe todo el espacio, junto con el header y el footer
pero al main también se debe ajustar se agrega la clase contenido-principal
-->
<body class="layout-page">
<!-- HEADER -->
<?php include 'partials/header.php'; ?>

<main class="container py-4 contenido-principal" x-data="modulosApp()" x-init="init()">

    <!-- BREADCRUMB -->
    <!-- <div class="breadcrumb mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="formulario_menu_principal.php">Inicio</a>
                </li>
                <li class="breadcrumb-item active" x-text="subsistema.nombre"></li>
            </ol>
        </nav>
    </div> -->

    <!-- HERO / ENCABEZADO -->
    <div class="hero-box mb-4 fade-enter">
        <div class="row align-items-center">

            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">

                    <div class="hero-icon">
                        <!-- <i class="bi bi-grid-1x2-fill"></i> -->
                        <img
                            :src="subsistema.imagen"
                            width="80"
                            height="80"
                            class="card-img"
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
                <a :href="modulo.ruta"
                   class="text-decoration-none">

                    <div class="card card-premium h-100 fade-enter">

                        <div class="card-body p-4">
                            <img
                                :src="modulo.imagen"
                                width="150"
                                height="150"
                                class="card-img"
                                alt="Imagen del modulo"
                                loading="lazy" 
                            /> 
                            <!-- loading="lazy" ---- Permite cargar la imagen de forma diferida, 
                                mejorando el rendimiento de la página al cargar solo las imágenes visibles inicialmente. 
                                Las imágenes que no estén en el viewport se cargarán cuando el usuario se desplace hacia ellas. 
                            -->
                            <!-- Nombre del Módulo con resaltado -->
                            <!-- <h5 class="fw-bold text-dark mb-2"
                                x-html="highlightSeguro(modulo.nombre)"> -->
                            <h5 class="fw-bold text-dark mb-2"
                                x-text="modulo.nombre">
                            </h5> 

                            <!-- Descripción con resaltado -->
                             <!-- <p class="text-muted small mb-4" 
                                x-html="highlightSeguro(modulo.descripcion)">
                                !-- Ingresar al módulo y gestionar funciones disponibles. --
                            </p> -->
                            <p class="text-muted small mb-4" x-text="modulo.descripcion">
                                <!-- Ingresar al módulo y gestionar funciones disponibles. -->
                            </p>

                            <!-- <div class="d-flex justify-content-between align-items-center">

                                <small class="text-muted">
                                    Disponible
                                </small>

                                <i class="bi bi-arrow-right-circle-fill text-primary fs-4"></i>

                            </div> -->
                            <!-- Pie del CARD -- FORMULARIOS -->                                    
                            <div class="border-top pt-3 mt-3">
                                            
                                <small class="text-muted d-block mb-2 fw-semibold">
                                    Formularios disponibles
                                </small>

                                <div class="d-flex flex-wrap gap-2">

                                    <template x-for="formulario in (modulo.formularios || [])" :key="formulario.id">

                                        <span class="badge badge-modulo"                                                        
                                                x-text="formulario.nombre">
                                        </span> 
                                        <!-- Nombre formulario con resaltado -->
                                        <!-- <span class="badge badge-modulo" 
                                            x-html="highlightSeguro(formulario.nombre)"> 
                                        </span> -->
                                        
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
        subsistema: {
            nombre: "",
            descripcion:"",
            imagen:"",
            modulos:[]
        },

        // ================= Al iniciar la página ========================
        async init() {
            //Leer parámetros URL
            const url = new URLSearchParams(window.location.search);
            
            //Lee el nombre de Subsistema desde URL
            // this.subsistema = url.get("subsistema");
            const nombreSubsistema = url.get("subsistema"); 
            
            //Validación
            if (!nombreSubsistema) { //if (!this.nombreSubsistema) -- this es cuando es una variable global {
                this.error = "Subsistema no válido";
                this.loading = false;
                return;
            }
            //Cargar datos
            await this.cargarDatos(nombreSubsistema);
            //await this.cargarDatos();
        },

        //Consulta a Backend 
        async cargarDatos(nombreSubsistema) {

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

                ///* Buscar subsistema */
                const encontrado = lista.find(
                    item => item.nombre === nombreSubsistema
                    //item => item.nombre === this.nombreSubsistema
                );
                //Si no existe
                if (!encontrado) {
                    this.error = "No se encontraron módulos.";
                    return;
                }

                //this.modulos = encontrado.modulos;
                //Guarda el objeto completo y no solo los módulos
                this.subsistema = encontrado; 

            } catch (e) {

                console.error(e);

                this.error = "Error al cargar módulos.";

            } finally {

                this.loading = false;

            }

        },

        // ========== NORMALIZAR TEXTO : Evitar problemas de acento en las búsquedas =========
        normalizar(texto) {
            return (texto || "")
                .toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "");
        },
        
        /* ========================= ESCAPE HTML ========================= */ 
        /*
        escapeHtml(texto) { 
            return texto 
                .replace(/&/g, "&amp;") 
                .replace(/</g, "&lt;") 
                .replace(/>/g, "&gt;") 
                .replace(/"/g, "&quot;") 
                .replace(/'/g, "&#039;"); 
            },
        */
        /* ========================================= RESALTADO DE TEXTO ========================================= */ 
        /*highlightSeguro(texto) {

            // 1. Asegurar string válido SIEMPRE
            texto = texto ? String(texto) : "";

            // 2. Si no hay búsqueda → devolver limpio
            if (!this.buscar || this.buscar.trim() === "") {
                return this.escapeHtml(texto);
            }

            const termino = this.buscar.trim();

            try {

                // 3. Escapar HTML base (MUY IMPORTANTE)
                let textoSeguro = this.escapeHtml(texto);

                // 4. Escapar término para regex
                const terminoEscapado = termino.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

                const regex = new RegExp(terminoEscapado, "gi");

                // 5. Aplicar resaltado
                return textoSeguro.replace(regex, match =>
                    `<mark class="mep-highlight">${match}</mark>`
                );

            } catch (e) {
                return this.escapeHtml(texto);
            }
        },
        */
        //Filtro "Buscador dinámico"
        modulosFiltrados() {

            //const texto = this.buscar.trim().toLowerCase();
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
                const coincideFormulario = (modulo.formularios || []).some(formulario => {
                    return (
                        this.normalizar(formulario.nombre || "").includes(texto) ||
                        this.normalizar(formulario.descripcion || "").includes(texto)
                    );
                });

                return coincideModulo || coincideFormulario;
            });
        }

    }

}      

</script>
</body>
</html>