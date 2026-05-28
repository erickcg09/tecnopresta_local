<!doctype html>
<html lang="es">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <link rel="manifest" href="manifest.json" />

        <!-- Alpine js -- Defer es para utilizar alpine antes de definrlo -->
        <script
        defer
        src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
        ></script>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        <!-- <link rel="stylesheet" type="text/css" href="/css/style.css"> -->
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/formulario_menu_principal.css" />
        <!-- <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css"> -->
        <link rel="stylesheet" href="css_reportes/ico_reportes.css" />
        <link rel="stylesheet" href="css/loader.css" />

        <title>TecnoPresta// Menú Principal</title>
        <link rel="icon" href="icons/favicon.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" href="icons/apple-touch-icon.png" />
        <link rel="stylesheet" href="carrusel/css/font-awesome.min.css" />
        <!-- Latest compiled and minified CSS -->
        <link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        />

        <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <style>
        a {
            color: #ffffff;
            text-decoration: none;
        } /* CSS link color */
        </style>
    </head>
    <body>
    <!-- x-data="menuApp()" Esto es para asegurar que que menuAPP exista -->
    
    <!-- <body x-data="menuApp()" x-init="init()"> -->
        <!-- *** NAVBAR **** -->
        <!-- <div x-text="'Alpine Funcionando'"></div> -->
        <!-- <nav class="navbar navbar-expand-md bg-dark navbar-dark"> -->
        <nav class="navbar navbar-expand-md navbar-dark navbar-mep">
            <!-- Logo + título -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="img/logomep2020.png" width="45" height="30" class="mr-2" />
                <span style="color: #fff"
                >Tecno<span style="color: var(--mep-gold)">Presta</span></span>
                <span class="d-none d-md-inline text-white-50 small ms-1">
                    | Ministerio de Educación Pública
                </span>
            </a>

            <!-- Botón responsive -->
            <button 
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#menuNav"
                aria-controls="menuNav"
                aria-expanded="false"
                aria-label="Abrir menú de navegación"
                >
                <!-- data-target="#collapsibleNavbar" -->
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- MENU --- Contenido colapsable -->
            <div class="collapse navbar-collapse" id="menuNav">
                
                <ul class="navbar-nav">
                    <!-- <li class="nav-item">
                                    <a class="nav-link" href="portal_perfil.php">
                                        <span class="icon icon-profile"></span><b>Mi Perfil</b>
                                    </a>
                                </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="portal_soporte_tecnico.php">
                            <span class="icon icon-envelop"></span>
                            <b>Contáctenos</b>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tarjetero.php">
                            <span class="icon icon-lifebuoy"></span>
                            <b> Ayuda </b>
                        </a>
                    </li>
                    <!-- CERRAR SESION -->
                    <!-- <li class="nav-item">
                                <a class="nav-link" href="gameover.php"
                                ><span class="icon icon-enter"></span>
                                <b>Cerrar Sesi&oacute;n </b></a
                                >
                            </li> -->
                </ul>

                <!-- DROPDOWN DEL USUARIO -->
                <div class="ml-auto dropdown mr-5">
                    <a href="#"
                        class="d-flex align-items-center text-white dropdown-toggle"  
                        data-toggle="dropdown"  
                        aria-haspopup="true"
                        aria-expanded="false"                    
                        data-boundary="viewport"
                    >
                        <!-- Nombre -->
                        <span class="mr-2" x-text="usuario.nombre"></span>

                        <!-- FOTO Avatar -->
                        <img :src="fotoPerfil"
                            class="rounded-circle"
                            width="40"
                            height="40"
                            style="object-fit: cover; border: 2px solid #fff">
    
                    </a>

                    <!-- MENÚ DESPLEGABLE -->
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="portal_perfil.php">👤 Mi Perfil </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item text-danger" href="gameover.php">🚪 Cerrar sesión </a>
                    </div>
                </div>
            </div>
        </nav>
        <!-- FIN DE AGREGA IMAGEN DE PERFIL -->