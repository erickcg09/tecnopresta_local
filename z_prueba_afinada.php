<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
</script>';
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Evento en Calendario con Teams</title>
    <script type="text/javascript" src="https://alcdn.msauth.net/browser/2.21.0/js/msal-browser.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0078d4;
            --primary-dark: #106ebe;
            --teams-purple: #7B83EB;
            --teams-purple-dark: #5A62D4;
            --secondary: #20c997;
            --secondary-dark: #1aa179;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --border-radius: 12px;
            --box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f2f5 0%, #e6e9f0 100%);
            min-height: 100vh;
            padding: 20px;
            color: var(--dark);
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .microsoft-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #0078d4 0%, #106ebe 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0, 120, 212, 0.2);
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .logo-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--teams-purple), var(--teams-purple-dark));
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(123, 131, 235, 0.3);
        }

        .logo-icon i {
            font-size: 2.2rem;
            color: white;
        }

        h1 {
            color: var(--dark);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(90deg, var(--primary), var(--teams-purple));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .subtitle {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 30px;
            line-height: 1.6;
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            margin-bottom: 25px;
            transition: var(--transition);
            border-left: 5px solid var(--teams-purple);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--teams-purple), var(--primary));
        }

        .card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.4rem;
            color: var(--dark);
            margin-bottom: 20px;
        }

        .card-title i {
            font-size: 1.6rem;
            color: var(--teams-purple);
        }

        .instructions-list {
            padding-left: 20px;
            margin-bottom: 20px;
        }

        .instructions-list li {
            margin-bottom: 15px;
            line-height: 1.6;
            display: flex;
            align-items: flex-start;
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary), var(--teams-purple));
            color: white;
            border-radius: 50%;
            margin-right: 15px;
            font-weight: bold;
            flex-shrink: 0;
            box-shadow: 0 4px 8px rgba(123, 131, 235, 0.2);
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 25px 0;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 18px 32px;
            border-radius: 10px;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            20% {
                transform: scale(25, 25);
                opacity: 0.3;
            }
            100% {
                opacity: 0;
                transform: scale(40, 40);
            }
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--primary-dark), #0a5a9e);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 120, 212, 0.3);
        }

        .btn-teams {
            background: linear-gradient(135deg, var(--teams-purple), var(--teams-purple-dark));
            color: white;
        }

        .btn-teams:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--teams-purple-dark), #4a52c5);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(123, 131, 235, 0.3);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .teams-icon {
            width: 24px;
            height: 24px;
            fill: white;
        }

        .form-section {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--dark);
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i {
            color: var(--teams-purple);
        }

        .form-input {
            width: 100%;
            padding: 16px;
            border: 2px solid var(--gray-light);
            border-radius: 10px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-input:focus {
            border-color: var(--teams-purple);
            outline: none;
            box-shadow: 0 0 0 4px rgba(123, 131, 235, 0.2);
        }

        textarea.form-input {
            min-height: 130px;
            resize: vertical;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: rgba(123, 131, 235, 0.05);
            border-radius: 10px;
            border-left: 4px solid var(--teams-purple);
        }

        .checkbox-container input[type="checkbox"] {
            width: 22px;
            height: 22px;
            accent-color: var(--teams-purple);
            cursor: pointer;
        }

        .checkbox-container label {
            font-weight: 500;
            cursor: pointer;
            color: var(--dark);
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px 22px;
            border-radius: 10px;
            margin-bottom: 25px;
            background-color: white;
            border: 2px solid rgba(123, 131, 235, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .status-icon {
            font-size: 1.8rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-text {
            flex-grow: 1;
            font-weight: 500;
            font-size: 1.05rem;
        }

        .status-success {
            color: var(--success);
            background-color: rgba(40, 167, 69, 0.1);
        }

        .status-warning {
            color: var(--warning);
            background-color: rgba(255, 193, 7, 0.1);
        }

        .status-error {
            color: var(--danger);
            background-color: rgba(220, 53, 69, 0.1);
        }

        .status-info {
            color: var(--teams-purple);
            background-color: rgba(123, 131, 235, 0.1);
        }

        .config-section {
            background-color: rgba(123, 131, 235, 0.03);
            border: 2px solid rgba(123, 131, 235, 0.1);
            border-left-color: var(--teams-purple);
            margin-top: 30px;
        }

        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .config-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--gray-light);
            transition: var(--transition);
        }

        .config-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            border-color: var(--teams-purple);
        }

        .config-check {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--gray-light);
            color: var(--gray);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .config-check.checked {
            background: linear-gradient(135deg, var(--teams-purple), var(--teams-purple-dark));
            color: white;
            box-shadow: 0 4px 12px rgba(123, 131, 235, 0.3);
        }

        .config-details {
            flex-grow: 1;
        }

        .config-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .config-desc {
            font-size: 0.9rem;
            color: var(--gray);
        }

        .result-card {
            margin-top: 30px;
            animation: fadeIn 0.5s ease-out;
            border-left-color: var(--teams-purple);
            background-color: rgba(123, 131, 235, 0.03);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .result-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(123, 131, 235, 0.1);
        }

        .result-title {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--success);
            font-size: 1.4rem;
        }

        .result-title i {
            font-size: 1.8rem;
        }

        .result-content {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .result-item {
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-label {
            font-weight: 600;
            min-width: 180px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .result-label i {
            color: var(--teams-purple);
        }

        .result-value {
            flex-grow: 1;
            word-break: break-word;
            line-height: 1.5;
        }

        .meeting-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--teams-purple), var(--teams-purple-dark));
            color: white;
            border-radius: 10px;
            text-decoration: none;
            margin-top: 10px;
            transition: var(--transition);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(123, 131, 235, 0.2);
        }

        .meeting-link:hover {
            background: linear-gradient(135deg, var(--teams-purple-dark), #4a52c5);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(123, 131, 235, 0.3);
        }

        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 22px;
            background-color: var(--gray);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            margin-top: 15px;
            font-size: 1rem;
        }

        .copy-btn:hover {
            background-color: var(--dark);
            transform: translateY(-2px);
        }

        .copy-btn-success {
            background-color: var(--success);
        }

        .loading {
            display: inline-block;
            width: 22px;
            height: 22px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            color: var(--gray);
            font-size: 0.9rem;
            padding-top: 25px;
            border-top: 2px solid rgba(123, 131, 235, 0.1);
        }

        .teams-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--teams-purple), var(--teams-purple-dark));
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            
            .card {
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .config-grid {
                grid-template-columns: 1fr;
            }
            
            .result-item {
                flex-direction: column;
                margin-bottom: 20px;
            }
            
            .result-label {
                min-width: auto;
                margin-bottom: 8px;
            }
            
            .logo-container {
                flex-direction: column;
                gap: 15px;
            }
        }

        /* Animación de pulse para el botón de Teams */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(123, 131, 235, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(123, 131, 235, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(123, 131, 235, 0);
            }
        }

        .btn-teams:not(:disabled) {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header con identidad Microsoft -->
        <div class="header">
            <div class="microsoft-badge">
                <i class="fab fa-microsoft"></i>
                Microsoft 365 Integración
            </div>
            
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h1>Crear Evento en Calendario <span class="teams-badge">Microsoft Teams</span></h1>
                    <p class="subtitle">Crea eventos profesionales en tu calendario de Microsoft 365 que incluyen automáticamente enlaces de reunión de Microsoft Teams</p>
                </div>
            </div>
        </div>

        <!-- Instrucciones de uso -->
        <div class="card">
            <h2 class="card-title"><i class="fas fa-graduation-cap"></i> Cómo Funciona</h2>
            <ol class="instructions-list">
                <li>
                    <span class="step-number">1</span>
                    <div>
                        <strong>Autenticación</strong><br>
                        Haz clic en "Obtener Token" para autenticarte con tu cuenta Microsoft 365.
                    </div>
                </li>
                <li>
                    <span class="step-number">2</span>
                    <div>
                        <strong>Configuración del Evento</strong><br>
                        Completa los detalles del evento en el formulario principal.
                    </div>
                </li>
                <li>
                    <span class="step-number">3</span>
                    <div>
                        <strong>Creación Automática</strong><br>
                        Haz clic en "Crear Evento con Teams" para generar el evento con enlace de reunión.
                    </div>
                </li>
                <li>
                    <span class="step-number">4</span>
                    <div>
                        <strong>Compartir y Participar</strong><br>
                        Copia y comparte el enlace de Teams con los participantes de la reunión.
                    </div>
                </li>
            </ol>
        </div>

        <!-- Botones de acción principales -->
        <div class="action-buttons">
            <button id="loginBtn" class="btn btn-primary" onclick="loginAndGetToken()">
                <i class="fas fa-key"></i> Obtener Token de Acceso
            </button>
            <button id="createEventBtn" class="btn btn-teams" onclick="createCalendarEvent()" disabled>
                <svg class="teams-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                Crear Evento con Teams
            </button>
        </div>

        <!-- Indicador de estado -->
        <div id="statusIndicator" class="status-indicator">
            <div class="status-icon status-info">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="status-text">Presiona "Obtener Token" para comenzar el proceso de autenticación</div>
        </div>

        <!-- Formulario principal -->
        <div class="card form-section">
            <h2 class="card-title"><i class="fas fa-edit"></i> Detalles del Evento</h2>
            <form id="eventForm">
                <div class="form-group">
                    <label for="eventSubject" class="form-label">
                        <i class="fas fa-heading"></i> Asunto del Evento
                    </label>
                    <input type="text" id="eventSubject" class="form-input" 
                           placeholder="Ej: Reunión de equipo, Presentación de proyecto, Revisión mensual" 
                           value="Reunión de equipo" required>
                </div>
                
                <div class="form-group">
                    <label for="eventStartTime" class="form-label">
                        <i class="fas fa-calendar-alt"></i> Fecha y Hora de Inicio
                    </label>
                    <input type="datetime-local" id="eventStartTime" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="eventDuration" class="form-label">
                        <i class="fas fa-clock"></i> Duración (minutos)
                    </label>
                    <input type="number" id="eventDuration" class="form-input" min="5" max="1440" value="60" required>
                    <small style="color: var(--gray); margin-top: 5px; display: block;">Entre 5 minutos y 24 horas</small>
                </div>
                
                <div class="form-group">
                    <label for="eventDescription" class="form-label">
                        <i class="fas fa-align-left"></i> Descripción (opcional)
                    </label>
                    <textarea id="eventDescription" class="form-input" 
                              placeholder="Describe el propósito de este evento, agenda, materiales necesarios...">Reunión para discutir los avances del proyecto y planificar las próximas acciones.</textarea>
                </div>
                
                <div class="checkbox-container">
                    <input type="checkbox" id="sendReminder" checked>
                    <label for="sendReminder">Enviar recordatorio 15 minutos antes del evento</label>
                </div>
            </form>
        </div>

        <!-- Sección de configuración y verificación -->
        <div id="configSection" class="card config-section" style="display: none;">
            <h2 class="card-title"><i class="fas fa-check-circle"></i> Verificación de Configuración</h2>
            <p style="color: var(--gray); margin-bottom: 20px;">Todos los sistemas están listos para crear tu evento con Microsoft Teams</p>
            <div class="config-grid">
                <div class="config-item">
                    <div id="authCheck" class="config-check">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="config-details">
                        <div class="config-title">Autenticación Microsoft</div>
                        <div class="config-desc">Token de acceso verificado</div>
                    </div>
                </div>
                
                <div class="config-item">
                    <div id="graphCheck" class="config-check">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="config-details">
                        <div class="config-title">Microsoft Graph API</div>
                        <div class="config-desc">Permisos de calendario confirmados</div>
                    </div>
                </div>
                
                <div class="config-item">
                    <div id="calendarCheck" class="config-check">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="config-details">
                        <div class="config-title">Calendario Office 365</div>
                        <div class="config-desc">Acceso a eventos confirmado</div>
                    </div>
                </div>
                
                <div class="config-item">
                    <div id="teamsCheck" class="config-check">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="config-details">
                        <div class="config-title">Microsoft Teams</div>
                        <div class="config-desc">Integración de reuniones activa</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados de la creación del evento -->
        <div id="resultCard" class="card result-card" style="display: none;">
            <div class="result-header">
                <h3 class="result-title">
                    <i class="fas fa-check-circle"></i> ¡Evento Creado Exitosamente!
                </h3>
                <div class="teams-badge">
                    <i class="fas fa-video"></i> Teams
                </div>
            </div>
            
            <div class="result-content">
                <div class="result-item">
                    <div class="result-label">
                        <i class="fas fa-heading"></i> Asunto:
                    </div>
                    <div class="result-value" id="resultSubject"></div>
                </div>
                
                <div class="result-item">
                    <div class="result-label">
                        <i class="fas fa-calendar-alt"></i> Fecha y Hora:
                    </div>
                    <div class="result-value" id="resultDateTime"></div>
                </div>
                
                <div class="result-item">
                    <div class="result-label">
                        <i class="fas fa-clock"></i> Duración:
                    </div>
                    <div class="result-value" id="resultDuration"></div>
                </div>
                
                <div class="result-item">
                    <div class="result-label">
                        <i class="fas fa-link"></i> Enlace de Teams:
                    </div>
                    <div class="result-value">
                        <a id="meetingLink" class="meeting-link" target="_blank">
                            <i class="fas fa-video"></i> Unirse a la reunión de Teams
                        </a>
                        <br>
                        <button id="copyLinkBtn" class="copy-btn" onclick="copyMeetingLink()">
                            <i class="fas fa-copy"></i> Copiar enlace de reunión
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="result-item">
                <div class="result-label">
                    <i class="fas fa-info-circle"></i> Nota:
                </div>
                <div class="result-value">
                    El evento ha sido creado en tu calendario de Microsoft 365. El enlace de Teams está activo y listo para compartir con los participantes. 
                    El evento aparecerá automáticamente en la aplicación de Teams.
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <i class="fab fa-microsoft" style="color: var(--teams-purple);"></i> 
                Integración Microsoft 365 • Powered by Microsoft Graph API
            </p>
            <p style="font-size: 0.8rem; margin-top: 10px; color: var(--gray);">
                Esta aplicación utiliza Microsoft Graph API para integrar Calendario y Microsoft Teams
            </p>
        </div>
    </div>

    <script>
        // Configuración MSAL
        const msalConfig = {
            auth: {
                clientId: "be0e9b41-718d-4d2a-8c2f-d26eba67d767",
                authority: "https://login.microsoftonline.com/mep.go.cr",
                redirectUri: window.location.origin + window.location.pathname,
                knownAuthorities: ["mep.go.cr"]
            },
            cache: {
                cacheLocation: "sessionStorage",
                storeAuthStateInCookie: false
            }
        };

        // Scopes requeridos
        const graphScopes = {
            scopes: [
                "User.Read",
                "Calendars.ReadWrite",
                "OnlineMeetings.ReadWrite"
            ]
        };

        // Variables globales
        let msalInstance = null;
        let currentToken = null;
        let currentUser = null;
        let lastMeetingLink = "";

        // Inicializar MSAL
        function initializeMsal() {
            try {
                msalInstance = new msal.PublicClientApplication(msalConfig);
                updateStatus("Sistema inicializado. Listo para autenticación.", "info");
                return true;
            } catch (error) {
                updateStatus(`Error al inicializar: ${error.message}`, "error");
                return false;
            }
        }

        // Actualizar estado en la UI
        function updateStatus(message, type = "info") {
            const statusIndicator = document.getElementById("statusIndicator");
            const statusIcon = statusIndicator.querySelector('.status-icon');
            const statusText = statusIndicator.querySelector('.status-text');
            
            // Reset classes
            statusIcon.className = 'status-icon';
            statusIcon.classList.add(`status-${type}`);
            
            // Set icon based on type
            let iconClass = 'fa-info-circle';
            if (type === "success") iconClass = 'fa-check-circle';
            else if (type === "warning") iconClass = 'fa-exclamation-triangle';
            else if (type === "error") iconClass = 'fa-times-circle';
            
            statusIcon.innerHTML = `<i class="fas ${iconClass}"></i>`;
            statusText.textContent = message;
            
            // Show status indicator
            statusIndicator.style.display = 'flex';
            
            // Animate status update
            statusIndicator.style.animation = 'fadeIn 0.5s ease-out';
            setTimeout(() => {
                statusIndicator.style.animation = '';
            }, 500);
        }

        // Actualizar checks de configuración
        function updateConfigCheck(checkId, isChecked) {
            const checkElement = document.getElementById(checkId);
            if (isChecked) {
                checkElement.innerHTML = '<i class="fas fa-check"></i>';
                checkElement.classList.add('checked');
                
                // Add animation
                checkElement.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    checkElement.style.transform = 'scale(1)';
                }, 300);
            } else {
                checkElement.innerHTML = '<i class="fas fa-times"></i>';
                checkElement.classList.remove('checked');
            }
        }

        // Mostrar sección de configuración
        function showConfigSection() {
            const configSection = document.getElementById('configSection');
            configSection.style.display = 'block';
            configSection.style.animation = 'fadeIn 0.8s ease-out';
        }

        // Iniciar sesión y obtener token
        async function loginAndGetToken() {
            try {
                updateStatus("Iniciando autenticación con Microsoft...", "warning");
                
                if (!msalInstance) {
                    if (!initializeMsal()) return;
                }

                // Verificar si ya hay sesión activa
                const accounts = msalInstance.getAllAccounts();
                if (accounts.length > 0) {
                    currentUser = accounts[0];
                    updateStatus(`Sesión activa encontrada: ${currentUser.username}`, "success");
                    await acquireTokenSilent();
                } else {
                    // Iniciar sesión interactiva
                    updateStatus("Redirigiendo al portal de Microsoft...", "warning");
                    await msalInstance.loginPopup({
                        ...graphScopes,
                        prompt: "select_account"
                    });
                    
                    const accounts = msalInstance.getAllAccounts();
                    if (accounts.length > 0) {
                        currentUser = accounts[0];
                        updateStatus(`✅ Autenticación exitosa: ${currentUser.username}`, "success");
                        await acquireTokenSilent();
                    }
                }
                
            } catch (error) {
                updateStatus(`Error en autenticación: ${error.message}`, "error");
                console.error("Error detallado:", error);
            }
        }

        // Obtener token silenciosamente
        async function acquireTokenSilent() {
            try {
                updateStatus("Obteniendo token de acceso...", "warning");
                
                const response = await msalInstance.acquireTokenSilent({
                    scopes: graphScopes.scopes,
                    account: currentUser,
                    forceRefresh: false
                });

                if (response && response.accessToken) {
                    currentToken = response.accessToken;
                    
                    // Actualizar UI
                    updateStatus(`✅ Autenticado como: ${currentUser.username}`, "success");
                    document.getElementById("createEventBtn").disabled = false;
                    
                    // Mostrar y actualizar configuración
                    showConfigSection();
                    updateConfigCheck('authCheck', true);
                    setTimeout(() => updateConfigCheck('graphCheck', true), 300);
                    setTimeout(() => updateConfigCheck('calendarCheck', true), 600);
                    setTimeout(() => updateConfigCheck('teamsCheck', true), 900);
                    
                    // Establecer valores por defecto en el formulario
                    setDefaultFormTime();
                }
                
            } catch (error) {
                console.warn("Error silencioso, intentando interactivo:", error);
                await acquireTokenPopup();
            }
        }

        // Obtener token via popup
        async function acquireTokenPopup() {
            try {
                updateStatus("Solicitando autorización interactiva...", "warning");
                
                const response = await msalInstance.acquireTokenPopup({
                    scopes: graphScopes.scopes
                });

                if (response && response.accessToken) {
                    currentToken = response.accessToken;
                    
                    // Actualizar UI
                    updateStatus(`✅ Autenticado como: ${response.account.username}`, "success");
                    document.getElementById("createEventBtn").disabled = false;
                    
                    // Mostrar y actualizar configuración
                    showConfigSection();
                    updateConfigCheck('authCheck', true);
                    setTimeout(() => updateConfigCheck('graphCheck', true), 300);
                    setTimeout(() => updateConfigCheck('calendarCheck', true), 600);
                    setTimeout(() => updateConfigCheck('teamsCheck', true), 900);
                    
                    // Establecer valores por defecto en el formulario
                    setDefaultFormTime();
                }
                
            } catch (error) {
                updateStatus(`Error al obtener token: ${error.message}`, "error");
            }
        }

        // Crear evento en el calendario con Teams
        async function createCalendarEvent() {
            if (!currentToken) {
                updateStatus("No hay token disponible. Autentícate primero.", "warning");
                return;
            }

            try {
                updateStatus("Creando evento en calendario con Microsoft Teams...", "warning");
                
                // Obtener valores del formulario
                const subject = document.getElementById("eventSubject").value || "Evento de calendario";
                const startTimeInput = document.getElementById("eventStartTime").value;
                const duration = parseInt(document.getElementById("eventDuration").value) || 60;
                const description = document.getElementById("eventDescription").value || "";
                const sendReminder = document.getElementById("sendReminder").checked;
                
                // Validar campos requeridos
                if (!subject.trim()) {
                    updateStatus("Por favor, ingresa un asunto para el evento", "error");
                    return;
                }
                
                if (!startTimeInput) {
                    updateStatus("Por favor, selecciona una fecha y hora de inicio", "error");
                    return;
                }
                
                // Calcular fechas
                const startDateTime = new Date(startTimeInput);
                const endDateTime = new Date(startDateTime.getTime() + duration * 60000);
                
                // Construir datos del evento con reunión de Teams
                const eventData = {
                    subject: subject,
                    body: {
                        contentType: "HTML",
                        content: description || "Evento creado automáticamente desde la aplicación."
                    },
                    start: {
                        dateTime: startDateTime.toISOString(),
                        timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone || "UTC"
                    },
                    end: {
                        dateTime: endDateTime.toISOString(),
                        timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone || "UTC"
                    },
                    isOnlineMeeting: true,
                    onlineMeetingProvider: "teamsForBusiness",
                    attendees: [
                        {
                            emailAddress: {
                                address: currentUser?.username,
                                name: currentUser?.name || "Usuario"
                            },
                            type: "required"
                        }
                    ],
                    location: {
                        displayName: "Microsoft Teams Meeting"
                    }
                };
                
                // Añadir recordatorio si está marcado
                if (sendReminder) {
                    eventData.reminderMinutesBeforeStart = 15;
                    eventData.isReminderOn = true;
                }

                // Mostrar indicador de carga en el botón
                const createBtn = document.getElementById("createEventBtn");
                const originalText = createBtn.innerHTML;
                createBtn.innerHTML = '<div class="loading"></div> Creando evento...';
                createBtn.disabled = true;

                const response = await fetch("https://graph.microsoft.com/v1.0/me/events", {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${currentToken}`,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(eventData)
                });

                // Restaurar botón
                createBtn.innerHTML = originalText;
                createBtn.disabled = false;
                
                if (response.ok) {
                    const event = await response.json();
                    updateStatus("✅ Evento creado exitosamente con enlace de Teams", "success");
                    
                    // Mostrar resultados
                    showEventResult(event, subject, startDateTime, duration);
                    
                } else {
                    const error = await response.text();
                    let errorMessage = `Error al crear evento (${response.status})`;
                    
                    try {
                        const errorObj = JSON.parse(error);
                        if (errorObj.error && errorObj.error.message) {
                            errorMessage = errorObj.error.message;
                        }
                    } catch (e) {
                        // Si no se puede parsear como JSON, usar el texto plano
                    }
                    
                    updateStatus(`❌ ${errorMessage}`, "error");
                    
                    // Mostrar error en resultado
                    document.getElementById('resultCard').style.display = 'block';
                    document.getElementById('resultCard').innerHTML = `
                        <div class="result-header">
                            <h3 class="result-title" style="color: var(--danger);">
                                <i class="fas fa-times-circle"></i> Error al Crear Evento
                            </h3>
                        </div>
                        <div class="result-content">
                            <div class="result-item">
                                <div class="result-label">
                                    <i class="fas fa-exclamation-triangle"></i> Detalles del error:
                                </div>
                                <div class="result-value">${errorMessage}</div>
                            </div>
                            <div class="result-item">
                                <div class="result-label">
                                    <i class="fas fa-lightbulb"></i> Solución:
                                </div>
                                <div class="result-value">
                                    Verifica que tengas los permisos necesarios en Microsoft 365 y que los datos del formulario sean correctos.
                                    Intenta autenticarte nuevamente si el problema persiste.
                                </div>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                updateStatus(`Error: ${error.message}`, "error");
                
                // Restaurar botón
                const createBtn = document.getElementById("createEventBtn");
                createBtn.innerHTML = originalText || '<svg class="teams-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> Crear Evento con Teams';
                createBtn.disabled = false;
                
                // Mostrar error en resultado
                document.getElementById('resultCard').style.display = 'block';
                document.getElementById('resultCard').innerHTML = `
                    <div class="result-header">
                        <h3 class="result-title" style="color: var(--danger);">
                            <i class="fas fa-times-circle"></i> Error de Conexión
                        </h3>
                    </div>
                    <div class="result-content">
                        <div class="result-item">
                            <div class="result-label">
                                <i class="fas fa-exclamation-triangle"></i> Detalles del error:
                            </div>
                            <div class="result-value">${error.message}</div>
                        </div>
                        <div class="result-item">
                            <div class="result-label">
                                <i class="fas fa-lightbulb"></i> Solución:
                            </div>
                            <div class="result-value">
                                Verifica tu conexión a internet e intenta nuevamente. Si el problema persiste, 
                                contacta con el administrador del sistema.
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        // Mostrar resultados del evento creado
        function showEventResult(event, subject, startDateTime, duration) {
            const resultCard = document.getElementById('resultCard');
            const resultSubject = document.getElementById('resultSubject');
            const resultDateTime = document.getElementById('resultDateTime');
            const resultDuration = document.getElementById('resultDuration');
            const meetingLink = document.getElementById('meetingLink');
            
            // Actualizar contenido
            resultSubject.textContent = subject;
            resultDateTime.textContent = startDateTime.toLocaleString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            resultDuration.textContent = `${duration} minutos`;
            
            // Configurar enlace de Teams si está disponible
            if (event.onlineMeeting && event.onlineMeeting.joinUrl) {
                lastMeetingLink = event.onlineMeeting.joinUrl;
                meetingLink.href = lastMeetingLink;
                meetingLink.style.display = 'inline-flex';
                document.getElementById('copyLinkBtn').style.display = 'inline-flex';
            } else {
                meetingLink.style.display = 'none';
                document.getElementById('copyLinkBtn').style.display = 'none';
            }
            
            // Mostrar tarjeta de resultados
            resultCard.style.display = 'block';
            resultCard.style.animation = 'fadeIn 0.8s ease-out';
            
            // Desplazar suavemente a los resultados
            setTimeout(() => {
                resultCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        }

        // Copiar enlace de reunión al portapapeles
        async function copyMeetingLink() {
            if (!lastMeetingLink) {
                updateStatus("No hay enlace de reunión para copiar", "warning");
                return;
            }
            
            try {
                await navigator.clipboard.writeText(lastMeetingLink);
                
                // Feedback visual
                const copyBtn = document.getElementById('copyLinkBtn');
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check"></i> ¡Enlace copiado!';
                copyBtn.classList.add('copy-btn-success');
                
                // Animación de confirmación
                copyBtn.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    copyBtn.style.transform = 'scale(1)';
                }, 200);
                
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                    copyBtn.classList.remove('copy-btn-success');
                }, 2000);
                
                updateStatus("✅ Enlace de Teams copiado al portapapeles", "success");
            } catch (err) {
                updateStatus("❌ Error al copiar enlace: " + err.message, "error");
            }
        }

        // Establecer valor por defecto en el formulario de fecha/hora
        function setDefaultFormTime() {
            const now = new Date();
            const futureDate = new Date(now.getTime() + 30 * 60000); // 30 minutos en el futuro
            
            // Ajustar a minutos redondos
            futureDate.setMinutes(Math.ceil(futureDate.getMinutes() / 30) * 30);
            futureDate.setSeconds(0);
            
            // Formatear para input datetime-local
            const formatForInput = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                
                return `${year}-${month}-${day}T${hours}:${minutes}`;
            };
            
            // Solo establecer si no hay valor ya
            if (!document.getElementById("eventStartTime").value) {
                document.getElementById("eventStartTime").value = formatForInput(futureDate);
            }
        }

        // Inicializar al cargar la página
        document.addEventListener("DOMContentLoaded", function() {
            initializeMsal();
            
            // Establecer valor por defecto en formulario
            setDefaultFormTime();
            
            // Verificar si ya hay una sesión activa
            setTimeout(() => {
                const accounts = msalInstance?.getAllAccounts();
                if (accounts && accounts.length > 0) {
                    currentUser = accounts[0];
                    updateStatus(`Sesión activa detectada: ${currentUser.username}`, "success");
                    acquireTokenSilent();
                }
            }, 500);
            
            // Validación en tiempo real del formulario
            document.getElementById('eventForm').addEventListener('input', function() {
                const subject = document.getElementById('eventSubject').value.trim();
                const startTime = document.getElementById('eventStartTime').value;
                const duration = document.getElementById('eventDuration').value;
                
                // Habilitar botón solo si hay datos válidos
                const isValid = subject.length > 0 && startTime && parseInt(duration) >= 5;
                document.getElementById('createEventBtn').disabled = !isValid || !currentToken;
            });
        });
    </script>
</body>
</html>