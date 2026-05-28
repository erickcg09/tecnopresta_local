<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
            .card-custom {
                background-color: #f0f4f8; /* Color frío y suave */
                border: none;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }
            .card-body {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .card-img {
                width: 50px;
                height: 50px;
                object-fit: cover;
                border-radius: 50%;
                align-self: flex-end;
            }
            .btn-custom {
                background-color: #007bff; /* Color frío */
                border: none;
                border-radius: 5px;
            }
            .icon-large {
            font-size: 2em; /* Ajusta el tamaño del ícono */
            }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md bg-dark navbar-dark">
        <img src="img/logodelgobierno.png" width="35" height="30" alt="" loading="lazy">
        <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="menu_inventario_nacional.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-all" viewBox="0 0 16 16">
                <path d="M8.098 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.7 8.7 0 0 0-1.921-.306 7 7 0 0 0-.798.008h-.013l-.005.001h-.001L8.8 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L4.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028zM9.3 10.386q.102 0 .223.006c.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96z"/>
                <path d="M5.232 4.293a.5.5 0 0 0-.7-.106L.54 7.127a1.147 1.147 0 0 0 0 1.946l3.994 2.94a.5.5 0 1 0 .593-.805L1.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028 4.012-2.954a.5.5 0 0 0 .106-.699"/>
                </svg> Principal</a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" href="gameover.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open" viewBox="0 0 16 16">
                <path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/>
                <path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117M11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5M4 1.934V15h6V1.077z"/>
                </svg> Cerrar Sesión</a>
            </li>  
            </ul>
        </div>  
    </nav>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-file-earmark-pdf-fill icon-large"></i> Adjuntar actas de recepción de equipos</h5>
                            <p class="card-text">
                              Las instituciones educativas deberán adjuntar, en formato PDF, las actas de recepción de todos los recursos tecnológicos (computadoras y tabletas). Estas actas deben estar clasificadas según su origen presupuestario. Es fundamental que cada documento incluya la siguiente información:
                            </p>
                            <ul>
                              <li><strong>Descripción del recurso:</strong> Detalle del tipo y cantidad de dispositivos recibidos.</li>
                              <li><strong>Fecha de recepción:</strong> Indicación precisa del día, mes y año en que se recibieron los recursos.</li>
                              <li><strong>Origen presupuestario:</strong> Clasificación clara del origen de los fondos utilizados para la adquisición de los recursos (por ejemplo, FONATEL SUTEL, PRONIE, PNTM, donaciones, etc.).</li>
                              <li><strong>Firma y sello:</strong> Firma del responsable de la recepción y el sello oficial de la institución.</li>
                            </ul>
                            <p class="card-text">
                              Agradecemos su colaboración y cumplimiento con estas directrices para asegurar una gestión transparente y eficiente de los recursos tecnológicos.
                            </p>
                        <a href="in_formulario_recibir_acta_recepcion.php" class="btn btn-custom">Ingresar</a>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-file-earmark-text-fill icon-large"></i> Generar Certificación de Dispositivos Tecnológicos</h5>
                        <p class="card-text">      En este apartado, podrá generar los informes pertinentes sobre los inventarios de su institución, clasificados según la fuente de financiamiento. Haga clic en el botón a continuación para acceder a la herramienta de generación de informes. Complete los campos necesarios para obtener un reporte detallado y preciso que refleje las adquisiciones realizadas.<br><br>
      Este proceso es esencial para asegurar la transparencia y la correcta administración de los recursos. Agradecemos su colaboración y compromiso con la gestión eficiente de los inventarios.
    </p>
                        <a href="formulario_inventario_reporte_por_fuente_financiamiento.html" class="btn btn-custom">Ingresar</a>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-mailbox2 icon-large"></i> Adjuntar Certificación de Dispositivos Tecnológicos Firmados</h5>
                        <p class="card-text">En este apartado, podrá subir sus reportes de informe de inventarios de manera rápida y segura. Por favor, haga clic en el botón a continuación para acceder al formulario de entrega. Asegúrese de completar todos los campos requeridos y adjuntar el archivo correspondiente. Su colaboración es fundamental para mantener nuestros registros actualizados y precisos.

Gracias por su cooperación.</p>
                        <a href="in_formulario_recibir_reportes.php" class="btn btn-custom">Ingresar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<footer class="bg-dark text-white pt-4 pb-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0">Por favor, asegúrese de ingresar la información solicitada en cada instancia.</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <div class="border border-light p-3">
                    <p class="mb-0">© 2024 Ministerio de Educación Pública. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
