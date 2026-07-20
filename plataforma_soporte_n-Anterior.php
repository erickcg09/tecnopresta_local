<?php
if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

require_once("conexion.php");

require_once __DIR__ . '/usuarioAzure.php';
$usuario_azure = obtenerUsuarioSesion();
/*
echo '<pre>';
print_r($_GET);
echo '</pre>';
exit;
*/

// ==== CONSTRUIR RUTA DE REGRESO =====
$ruta_regreso ='navegar.php?ruta=formulario_menu_principal.php'; // Ruta por defecto si no vienen parámetros
//Validar que vengan los parámetros necesarios para construir la ruta de regreso a formulario_sub_modulos.php
if (isset($_GET['subsistema_id'], $_GET['modulo_id'])) {
    $ruta_regreso = 'navegar.php?ruta=formulario_sub_modulos.php'
    . '&subsistema_id=' . intval($_GET['subsistema_id'] ?? 0)
    . '&modulo_id=' . intval($_GET['modulo_id'] ?? 0);
}

// === Bloquear acceso directo ===
    if (!defined('ACCESO_SEGURO')) {
        http_response_code(403);
        exit('Acceso directo no permitido');
    }
//session_start();
/*$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4, 5, 7]);

if ($tienellave == false) {
    echo '<script language="javascript">
        alert("No tienes permisos");
        self.location = "index.html";
    </script>';
}
*/

$link = $mysqli;

if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

date_default_timezone_set('America/Costa_Rica'); // Configuro un nuevo timezone

/*$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$loginstitucion = $_SESSION['dependencia'];
$logcorreo = $_SESSION['correomep'];
$regionallog = $_SESSION['direccionreg'];
$circuitolog = $_SESSION['circuito'];
*/
$time = time();
$fecha = date("d-m-Y", $time); // fecha formato español
?>
<!doctype html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <!-- Nueva Identidad Gráfica Gobierno de Costa Rica CSS -->
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/nueva-identidad.css">
    <link rel="stylesheet" href="css/formulario_menu_principal.css" />
    <script src="bootstrap5/js/bootstrap.bundle.min.js"></script>

    <script defer src="js/cdn.min.js"></script>

    
    <title>Soporte T&eacute;cnico</title>

    <style>
        /* @font-face {
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
     */
    
        .soporte-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 30px;
            margin: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .soporte-svg {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
            transition: transform 0.3s ease;
        }
        
        .soporte-svg:hover {
            transform: scale(1.02);
        }
        
        /* Estilos para centrar el ticket */
        .ticket-centrado {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            min-height: 200px;
            padding: 20px 0;
        }
        
        .ticket-wrapper {
            position: relative;
            width: 490px;
            max-width: 100%;
        }
        
        /* IDEA 5: Estilos combinados */
        .texto-izquierda-ticket {
            /* Soporte Técnico - discreto */
            position: absolute;
            left: 40px;
            top: 42px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #666;
            background: #f0f0f0;
            padding: 4px 12px;
            border-radius: 20px;
            z-index: 2;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .texto-derecha-ticket {
            /* Crear Ticket - llamativo */
            position: absolute;
            right: 30px;
            top: 38px;
            font-family: Arial, sans-serif;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 8px 20px;
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(102,126,234,0.4);
            z-index: 2;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid rgba(255,255,255,0.2);
        }
        
        .texto-derecha-ticket:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102,126,234,0.6);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        /* Manita animada */
        .manita-flotante {
            position: absolute;
            right: 200px;
            top: 15px;
            font-size: 42px;
            animation: dance 2s infinite;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
            z-index: 5;
            transform-origin: 70% 70%;
        }
        
        @keyframes dance {
            0%, 100% { 
                transform: rotate(0deg) translateX(0); 
            }
            15% { 
                transform: rotate(15deg) translateX(8px); 
            }
            30% { 
                transform: rotate(-5deg) translateX(-2px); 
            }
            45% { 
                transform: rotate(10deg) translateX(5px); 
            }
            60% { 
                transform: rotate(0deg) translateX(0); 
            }
            100% { 
                transform: rotate(0deg) translateX(0); 
            }
        }
        
        /* Pequeño brillo alrededor de la manita */
        .manita-flotante::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 60px;
            height: 60px;
            background: rgba(102,126,234,0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            animation: glow 2s infinite;
        }
        
        @keyframes glow {
            0%, 100% { 
                transform: translate(-50%, -50%) scale(0.8);
                opacity: 0.5;
            }
            50% { 
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 0.8;
            }
        }
        
        /* Tooltip "¡Clic aquí!" */
        .tooltip-manita {
            position: absolute;
            right: 80px;
            top: 65px;
            background: #FFD700;
            color: #333;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 5;
            animation: pulse 2s infinite;
        }
        
        .tooltip-manita::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            transform: translateY(-50%);
            border-width: 6px 8px 6px 0;
            border-style: solid;
            border-color: transparent #FFD700 transparent transparent;
        }
        
        @keyframes pulse {
            0%, 100% { 
                transform: scale(1);
                opacity: 0.9;
            }
            50% { 
                transform: scale(1.05);
                opacity: 1;
            }
        }
        
        /* Responsive para móviles */
        @media (max-width: 576px) {
            .texto-izquierda-ticket {
                left: 20px;
                font-size: 12px;
                padding: 3px 8px;
            }
            
            .texto-derecha-ticket {
                right: 20px;
                font-size: 16px;
                padding: 6px 15px;
            }
            
            .manita-flotante {
                right: 150px;
                font-size: 32px;
                top: 20px;
            }
            
            .tooltip-manita {
                right: 50px;
                font-size: 12px;
                padding: 3px 8px;
            }
        }
        
        [x-cloak] {
            display: none !important;
        }
        
        .ticket-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 20px;
        }
        
        .ticket-svg {
            width: 100%;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.15));
            transition: all 0.3s ease;
        }
        
        /* Hover elegante */
        .ticket-card:hover .ticket-svg {
            transform: translateY(-8px) scale(1.02);
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.25));
        }
        
        /* Animación flotante */
        .ticket-card.admin {
            animation: float1 4s ease-in-out infinite;
        }
        
        .ticket-card.tecnico {
            animation: float2 4s ease-in-out infinite;
        }
        
        @keyframes float1 {
            0%,100%{transform: translateY(0);}
            50%{transform: translateY(-5px);}
        }
        
        @keyframes float2 {
            0%,100%{transform: translateY(0);}
            50%{transform: translateY(-8px);}
        }

/* Botón flotante para el dashboard de los tickets de los usuarios */
        
        .btn-mis-tickets {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn-mis-tickets:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
            color: white;
        }
        
        .btn-mis-tickets i {
            font-size: 28px;
        }
        
        .btn-mis-tickets .badge-tickets {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ff5722;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        /* Tooltip al pasar el mouse */
        .btn-mis-tickets::before {
            content: "Mis Tickets";
            position: absolute;
            right: 70px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            font-family: Arial, sans-serif;
        }
        
        .btn-mis-tickets:hover::before {
            opacity: 1;
            visibility: visible;
        }
        
        /* Responsive para móviles */
        @media (max-width: 768px) {
            .btn-mis-tickets {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
            }
            .btn-mis-tickets i {
                font-size: 24px;
            }
            .btn-mis-tickets::before {
                display: none;
            }
        }        
        
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    
    <?php include 'partials/header.php'; ?>
    
    <main class="flex-grow-1">

        <div class="container">
            <!-- <a class="btn btn-light mt-5" href="formulario_menu_principal.php" role="button"><i class="bi bi-arrow-left-circle me-1"></i> Regresar</a> -->
            <!-- Botón Flotante para regresar -->
            <!-- <a class="btn btn-light mt-5" href="navegar.php?ruta=formulario_menu_principal.php" role="button"><i class="bi bi-arrow-left-circle me-1"></i> Regresar</a> -->
            <a href="#" class="btn-mis-tickets" id="btnMisTickets" title="Ver mis tickets">
                <i class="bi bi-ticket-perforated-fill"></i>
                <span class="badge-tickets" id="contadorTickets">0</span>
            </a>

            <h1 class="mt-5 text-center">Centro de Soporte</h1>
            

            <div class="d-flex justify-content-center mt-5" x-data>
                
                <div class="row g-4 justify-content-center">
            
                    <!-- 🎟️ TICKET ADMINISTRATIVO -->
                    <div class="col-md-5">
                        <div class="ticket-card admin"
                            @click="new bootstrap.Modal(document.getElementById('modalAdmin')).show()">
            
                            <svg viewBox="0 0 400 160" class="ticket-svg">
                                
                                <!-- fondo -->
                                <rect x="0" y="0" width="400" height="160" rx="15" fill="#e3f2fd"/>
            
                                <!-- corte ticket -->
                                <circle cx="0" cy="80" r="20" fill="white"/>
                                <circle cx="400" cy="80" r="20" fill="white"/>
            
                                <!-- línea punteada -->
                                <line x1="200" y1="10" x2="200" y2="150"
                                    stroke="#90caf9"
                                    stroke-width="3"
                                    stroke-dasharray="5,8"/>
            
                                <!-- icono -->
                                <text x="70" y="90" font-size="50">💼</text>
            
                                <!-- texto -->
                                <text x="140" y="70" font-size="18" font-weight="bold" fill="#0d6efd">
                                    Ticket Administrativo
                                </text>
            
                                <text x="140" y="100" font-size="13" fill="#333">
                                    Trámites, solicitudes,
                                </text>
                                <text x="140" y="120" font-size="13" fill="#333">
                                    gestiones institucionales
                                </text>
            
                            </svg>
            
                        </div>
                    </div>
            
            
                    <!-- ⚙️ TICKET TECNICO -->
                    <div class="col-md-5">
                        <div class="ticket-card tecnico"
                            @click="new bootstrap.Modal(document.getElementById('modalTecnico')).show()">
            
                            <svg viewBox="0 0 400 160" class="ticket-svg">
                                
                                <!-- fondo -->
                                <rect x="0" y="0" width="400" height="160" rx="15" fill="#fff3cd"/>
            
                                <!-- corte ticket -->
                                <circle cx="0" cy="80" r="20" fill="white"/>
                                <circle cx="400" cy="80" r="20" fill="white"/>
            
                                <!-- línea punteada -->
                                <line x1="200" y1="10" x2="200" y2="150"
                                    stroke="#ffca28"
                                    stroke-width="3"
                                    stroke-dasharray="5,8"/>
            
                                <!-- icono -->
                                <text x="70" y="90" font-size="50">⚙️</text>
            
                                <!-- texto -->
                                <text x="140" y="70" font-size="18" font-weight="bold" fill="#e65100">
                                    Ticket Técnico
                                </text>
            
                                <text x="140" y="100" font-size="13" fill="#333">
                                    Equipos, sistemas,
                                </text>
                                <text x="140" y="120" font-size="13" fill="#333">
                                    fallas y soporte TI
                                </text>
            
                            </svg>
            
                        </div>
                    </div>
            
                </div>
            </div>



            
    
            <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">

            <svg width="440" height="340" viewBox="0 0 440 340" xmlns="http://www.w3.org/2000/svg">
            
            <style>
            
            @keyframes typing {
            0%{opacity:.2}
            50%{opacity:1}
            100%{opacity:.2}
            }
            
            @keyframes floatChat {
            0%{transform:translateY(0)}
            50%{transform:translateY(-6px)}
            100%{transform:translateY(0)}
            }
            
            .key{animation:typing 1.2s infinite}
            .key:nth-child(2){animation-delay:.2s}
            .key:nth-child(3){animation-delay:.4s}
            .key:nth-child(4){animation-delay:.6s}
            
            .chat{animation:floatChat 3s ease-in-out infinite}
            
            </style>
            
            <!-- OPERADORA -->
            
            <rect x="175" y="160" width="90" height="70" rx="20" fill="#6fa8dc"/>
            
            <rect x="205" y="135" width="24" height="20" fill="#f1c27d"/>
            
            <circle cx="217" cy="105" r="35" fill="#f1c27d"/>
            
            <!-- Cabello -->
            <path d="M182 100 Q217 50 252 100 L252 125 Q217 90 182 125 Z" fill="#f4d03f"/>
            
            <rect x="178" y="100" width="15" height="45" rx="7" fill="#f4d03f"/>
            <rect x="246" y="100" width="15" height="45" rx="7" fill="#f4d03f"/>
            
            <!-- Headset -->
            <path d="M182 95 Q217 65 252 95" stroke="#444" stroke-width="4" fill="none"/>
            
            <circle cx="182" cy="103" r="8" fill="#444"/>
            <circle cx="252" cy="103" r="8" fill="#444"/>
            
            <!-- Micrófono -->
            <path d="M252 105 Q272 120 247 135" stroke="#444" stroke-width="3" fill="none"/>
            
            <circle cx="245" cy="135" r="4" fill="red">
            <animate attributeName="opacity" values="1;.2;1" dur="1.2s" repeatCount="indefinite"/>
            </circle>
            
            <!-- Brazos -->
            <rect x="155" y="185" width="45" height="12" rx="6" fill="#f1c27d"/>
            <rect x="235" y="185" width="45" height="12" rx="6" fill="#f1c27d"/>
            
            <!-- LAPTOP -->
            
            <rect x="120" y="175" width="200" height="115" rx="10" fill="#2f2f2f"/>
            
            <rect x="115" y="290" width="210" height="12" rx="4" fill="#1f1f1f"/>
            
            <!-- Logo -->
            <text x="220" y="235" text-anchor="middle" font-family="Arial" font-size="18" fill="#00d4ff" font-weight="bold">
                TecnoPresta
            </text>
            
            <!-- Teclado -->
            <g transform="translate(150 270)">
            <rect class="key" x="0" y="0" width="6" height="3" fill="#888"/>
            <rect class="key" x="10" y="0" width="6" height="3" fill="#888"/>
            <rect class="key" x="20" y="0" width="6" height="3" fill="#888"/>
            <rect class="key" x="30" y="0" width="6" height="3" fill="#888"/>
            </g>
            
            <!-- CHATS / TICKETS -->
            
            <g class="chat">
            <rect x="330" y="70" width="70" height="35" rx="8" fill="#ffffff" stroke="#ddd"/>
            <circle cx="345" cy="88" r="3" fill="#00bcd4"/>
            <circle cx="355" cy="88" r="3" fill="#00bcd4"/>
            <circle cx="365" cy="88" r="3" fill="#00bcd4"/>
            </g>
            
            <g class="chat" style="animation-delay:1s">
            <rect x="40" y="90" width="70" height="35" rx="8" fill="#ffffff" stroke="#ddd"/>
            <circle cx="55" cy="108" r="3" fill="#00bcd4"/>
            <circle cx="65" cy="108" r="3" fill="#00bcd4"/>
            <circle cx="75" cy="108" r="3" fill="#00bcd4"/>
            </g>
            
            <g class="chat" style="animation-delay:2s">
            <rect x="330" y="130" width="70" height="35" rx="8" fill="#ffffff" stroke="#ddd"/>
            <circle cx="345" cy="148" r="3" fill="#00bcd4"/>
            <circle cx="355" cy="148" r="3" fill="#00bcd4"/>
            <circle cx="365" cy="148" r="3" fill="#00bcd4"/>
            </g>
            
            </svg>

            </div> 
            
        </div> <!-- Cierre del container -->
    </main>
<?php include 'partials/footer.php'; ?>
<!-- Botón flotante Volver al Dashboard -->
<a href="<?= htmlspecialchars($ruta_regreso) ?>" class="btn-disponibilidad" 
    style="bottom: 100px;" data-tooltip="Regresar">
    <i class="bi bi-arrow-left-circle-fill"></i>
</a>


    <!-- MODALES -->
    <!-- Modal Administrativo -->
    <div class="modal fade" id="modalAdmin" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #1565C0 0%, #1976D2 100%);">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="modal-icon-wrap">
                            <i class="bi bi-briefcase-fill fs-3 text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white mb-0 fw-bold">Ticket Administrativo</h5>
                            <small class="text-white opacity-75">Consultas y gestiones institucionales</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pt-4 pb-2">
                    <p class="text-muted mb-4">
                        Este tipo de ticket está diseñado para atender <strong>consultas de carácter administrativo</strong>
                        relacionadas con el uso de la plataforma TecnoPresta y sus procesos institucionales.
                    </p>
                    <div class="info-block admin-block mb-4">
                        <h6 class="info-block-title text-primary">
                            <i class="bi bi-check2-circle me-2"></i>¿Cuándo usar este ticket?
                        </h6>
                        <ul class="info-list mb-0">
                            <li><i class="bi bi-dot text-primary fs-5"></i>Solicitar la <strong>creación de usuarios, roles o accesos</strong></li>
                            <li><i class="bi bi-dot text-primary fs-5"></i>Resolver <strong>dudas sobre el funcionamiento</strong> de TecnoPresta</li>
                            <li><i class="bi bi-dot text-primary fs-5"></i>Consultar <strong>instrucciones o procedimientos</strong> administrativos</li>
                            <li><i class="bi bi-dot text-primary fs-5"></i>Reportar <strong>inconsistencias en datos</strong> o información registrada</li>
                        </ul>
                    </div>
                    <div class="alert alert-warning d-flex align-items-start gap-3 border-0 rounded-3">
                        <i class="bi bi-exclamation-triangle-fill fs-5 mt-1 text-warning"></i>
                        <div>
                            <strong>¿Tiene un problema con un equipo o sistema?</strong><br>
                            <span class="small text-muted">Seleccione el <strong>Ticket Técnico</strong> para reportar fallas de hardware o software.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Cerrar
                    </button>
                    <a href="ticket_administrativo_n.php" class="btn btn-primary btn-continuar px-4">
                        Continuar <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Técnico -->
    <div class="modal fade" id="modalTecnico" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #E65100 0%, #F57C00 100%);">
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="modal-icon-wrap">
                            <i class="bi bi-tools fs-3 text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white mb-0 fw-bold">Ticket Técnico</h5>
                            <small class="text-white opacity-75">Fallas en equipos, sistemas y soporte TI</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pt-4 pb-2">
                    <p class="text-muted mb-4">
                        Este tipo de ticket está diseñado para reportar <strong>incidencias de carácter técnico</strong>
                        que afecten el funcionamiento de equipos, sistemas o la conectividad.
                    </p>
                    <div class="info-block tecnico-block mb-4">
                        <h6 class="info-block-title text-warning">
                            <i class="bi bi-check2-circle me-2"></i>¿Cuándo usar este ticket?
                        </h6>
                        <ul class="info-list mb-0">
                            <li><i class="bi bi-dot text-warning fs-5"></i>Reportar <strong>fallos en equipos de cómputo</strong> (computadoras, impresoras, etc.)</li>
                            <li><i class="bi bi-dot text-warning fs-5"></i>Problemas con <strong>sistemas o aplicaciones</strong> de software</li>
                            <li><i class="bi bi-dot text-warning fs-5"></i>Incidencias de <strong>conectividad o red</strong></li>
                            <li><i class="bi bi-dot text-warning fs-5"></i>Solicitar <strong>reparación o revisión de hardware</strong></li>
                        </ul>
                    </div>
                    <div class="alert alert-info d-flex align-items-start gap-3 border-0 rounded-3">
                        <i class="bi bi-info-circle-fill fs-5 mt-1 text-info"></i>
                        <div>
                            <strong>En el siguiente paso podrá identificar el equipo afectado.</strong>
                            <span class="small text-muted d-block">Seleccione el activo o activos que presentan la incidencia.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Cerrar
                    </button>
                    <a href="ticket_tecnico_n.php" class="btn btn-warning btn-continuar px-4">
                        Continuar <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('btnMisTickets').addEventListener('click', function(e) {
            e.preventDefault();
            // Por ahora solo una alerta, luego redirigirá al dashboard
            alert('🚧 Funcionalidad en construcción\n\nPróximamente podrá ver el historial y estado de sus tickets.');
        });
        
        // Opcional: Mostrar un contador de tickets abiertos (si tienes datos)
        // Esto se puede activar después cuando tengamos el dashboard
        /*
        fetch('contar_tickets_abiertos.php')
            .then(response => response.json())
            .then(data => {
                if (data.total > 0) {
                    document.getElementById('contadorTickets').textContent = data.total;
                    document.getElementById('contadorTickets').style.display = 'flex';
                } else {
                    document.getElementById('contadorTickets').style.display = 'none';
                }
            });
        */
    </script>
</body>
</html>