<?php // include 'templates/cabecera.php'; ?> 
<?php include 'partials/header.php'; ?>

    <div x-data="menuApp()" x-init="init()">
        <!-- BARRA DE CONTEXTO -- Se muestra cuando el usuario tiene una institución asignada-->
        <div class="bg-white border-bottom py-1 px-3"
            x-show="usuario.institucion">

            <div class="container d-flex align-items-center justify-content-between">
            <!-- <div class="barra-contexto d-flex align-items-center justify-content-between"> -->
                
                <nav aria-label="Migas de pan">

                    <ol class="breadcrumb mb-0 small"> 
                        <!-- Breadcrumb para mostrar contexto de navegación -->
                        <li class="breadcrumb-item"> <a href="formulario_menu_principal.html" class="text-decoration-none text-secondary"> Inicio </a> </li>
                        <!-- Enlace al inicio del menú -->
                        <li class="breadcrumb-item active text-secondary"> Menú Principal </li>
                    </ol>
                </nav>
                    <!-- INSTITUCION Pindora -->
                    <span class="badge rounded-pill d-flex align-items-center gap-1"
                        style=" background-color: var(--mep-blue);                                
                                font-size: 0.75rem;">
                        <i class="bi bi-building"></i>
                        <!-- se inserta el texto del campo usuario.institucion -->
                        <span x-text="usuario.institucion"></span>
                    </span>
            </div>
        </div>
        <!--Pildora del Centro Educativo-->

        <!-- MENSAJES -->
        <div class="container mt-3">
            <div x-show="error" class="alert alert-danger" x-text="error"></div>
        </div>

        <!-- LOADER -->
        <div class="text-center mt-5" x-show="loading">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2">Cargando módulos...</p>
        </div>
        <!-- Saludo de Bienvenida-->
        <!-- <div class="contenido-principal mb-4"> -->
        <div class="container mb-4"> 
                <h4 class="fw-semibold mb-1" style="color: var(--mep-blue);">
                    Bienvenido/a, <span x-text="usuario.nombre.split(' ')[0]"></span>   <!-- Solo muestra el primer nombre -->
                </h4>
                <!-- retorna la fecha formateada en español-->
                <p class="text-muted small mb-0" x-text="fechaHoy()"></p>
                <hr>
                <p class="text-uppercase text-muted small fw-semibold mb-3 ls-wide"
                    style="letter-spacing: 6px;">
                    Módulos del Sistema
                </p>
            <!-- </div> -->
        </div>
        <!-- Contenedor Principal -->
        <div class="container mt-4" x-show="!loading">
            <div class="row">
                <template x-for="subsistema in subsistemas" :key="subsistema.nombre">
                    <!-- Se agrega cada subsistema como un elemento de lista -->
                    <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-4" role="listitem">
                        <a :href="subsistema.modulos.length ? subsistema.modulos[0].ruta : '#'"
                            class="card h-100 shadow-sm text-decoration-none d-block"
                            :aria-label="'Ingresar al módulo ' + subsistema.nombre"
                            >
                            <div class="card-body d-flex flex-column p-4">
                                <img
                                    :src="subsistema.imagen"
                                    width="180"
                                    height="180"
                                    class="card-img mb-3"
                                    alt="Imagen del subsistema"
                                />
                                <!-- Nombre del Modulo -->
                                <h5
                                    class="card-title fw-semibold mb-2"
                                    style="font-size: 1 rem; color: #0f1f3d;"
                                    x-text="subsistema.nombre"
                                ></h5>
                                <!-- Descripción del Módulo -->
                                <p class="card-text text-muted small flex-grow-1" 
                                    style="line-height: 1.6;"
                                    x-text="subsistema.descripcion">
                                </p>
                                <!-- Pie del CARD-->
                                <div class="d-flex align-items-center justify-content-between
                                    border-top pt-3 mt-3">
                                    <span class="small fw-semibold"
                                        x-text="subsistema.modulos[0].nombre || 'Ingresar'">
                                    </span>
                                    <!-- :style="color: var(--mep-blue);" -->
                                    <span class="d-flex align-items-center justify-content-center rounded-circle"
                                            style="width:26px;height:26px;">
                                        <i class="bi bi-arrow-right small">
                                        </i>
                                    </span>
                                </div>
                            </div>
                        </a>   
                    </div> 
                </template>
            </div>
        </div> 
    </div>
                <!-- Este Script es para tener acceso a la función de OBTENER FOTO DE AZURE -->
        <script src="js/formulario_login.js"></script>
        <!-- FIN OBTNER ACCESO A función de OBTENER FOTO DE AZURE -->

        <!-- SCRIPT -->
        <script>
            // Función para devolver la fecha actual en español
            function fechaHoy() {
                const opciones = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };

                return new Date().toLocaleDateString('es-ES', opciones);
            }

            //function menuApp() {
            window.menuApp = function () {
                return {
                //variables reactivas
                usuario: "",
                loading: true,
                error: "",
                subsistemas: [], //datos del menú
                fotoPerfil: "", //Aquí se guarda la URL de la foto o imagen de Perfil

                //INIT se ejecuta al cargar
                async init() {
                    console.log("INIT Ejecutando");

                    // Verifica que exite sesión en FrontEnd
                    const sesion = sessionStorage.getItem("sesion");

                    if (!sesion) {
                    console.log("No hay sesión.");
                    window.location.href = "index.html";
                    return;
                    }
                    // 👤 Obtiene la foto del usuario desde Microsoft Graph
                    this.fotoPerfil = await window.obtenerFotoPerfil();

                    //Carga módulos desde el backend
                    await this.cargaModulos();
            },

            async cargaModulos() {
                console.log("Iniciando CargaModulos");
                //const sesion = sessionStorage.getItem('sesion'); // simulación de verificación de sesión

                try {
                const response = await fetch("sql/formulario_principal.php", {
                    method: "GET",
                    credentials: "include", // nesario para la sesión en PHP
                });

                const data = await response.json();
                console.log("Respuesta BackEnd: ", data);

                if (data.error) {
                    this.error = data.error;
                    return;
                }

                this.subsistemas = data.data || [];

                //Cargar usuario desde sessionStorage
                const sesion = JSON.parse(sessionStorage.getItem("sesion"));
                this.usuario = {
                    nombre: sesion?.Nombre || "Usuario",
                    institucion: sesion?.Dependencia || "",
                };

                //this.usuario = sesion || {}; //Carga los datos de la sesión en usuario
                } catch (e) {
                this.error = "Error al cargar módulos";
                console.error(e);
                } finally {
                //Ocultar Loader
                this.loading = false;
                }
            },
            };
        };
        </script>
<?php include 'templates/pie.php'; ?>