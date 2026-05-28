<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
</script>';
}
// Definir los valores de las regionales
$r1 = "1";
$r2 = "2";
$r3 = "3";
$r4 = "4";
$r5 = "5";
$r6 = "6";
$r7 = "7"; 
$r8 = "8";
$r9 = "9";
$r10 = "10";
$r11 = "11";
$r12 = "12";
$r13 = "13";
$r14 = "14"; 
$r15 = "15";
$r16 = "16";
$r17 = "17";
$r18 = "18";
$r19 = "19";
$r20 = "20";
$r21 = "21"; 
$r22 = "22";
$r23 = "23";
$r24 = "24";
$r25 = "25";
$r26 = "26";
$r27 = "27";

// Arreglo asociativo con los nombres de las regionales y sus valores
$regionales = [
    "San José Central" => $r1,
    "San José Norte" => $r2,
    "San José Suroeste" => $r3,
    "Desamparados" => $r4,
    "Los Santos" => $r5,
    "Puriscal" => $r6,
    "Pérez Zeledón" => $r7,
    "Alajuela" => $r8,
    "Occidente" => $r9,
    "San Carlos" => $r10,
    "Zona Norte Norte" => $r11,
    "Cartago" => $r12,
    "Turrialba" => $r13,
    "Heredia" => $r14,
    "Sarapiquí" => $r15,
    "Liberia" => $r16,
    "Cañas" => $r17,
    "Nicoya" => $r18,
    "Santa Cruz" => $r19,
    "Puntarenas" => $r20,
    "Peninsular" => $r21,
    "Aguirre" => $r22,
    "Grande de Térraba" => $r23,
    "Coto" => $r24,
    "Limón" => $r25,
    "Sula" => $r26,
    "Guápiles" => $r27
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Analista de Sistemas</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Estilos personalizados para las estrellas -->
    <style>
        .rating {
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        .rating input {
            display: none;
        }
        .rating label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
        }
        .rating input:checked ~ label {
            color: #ffc107; /* Color amarillo para las estrellas seleccionadas */
        }
        .rating label:hover,
        .rating label:hover ~ label {
            color: #ffc107; /* Color amarillo al hacer hover */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="formulario_menu_principal.html">
                <i class="bi bi-laptop"></i> Tecnopresta
            </a>
            <!-- Botón para móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Contenido colapsable -->
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="administracion_plataforma_soporte.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>   
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h1 class="text-center">Registro de Ingeniero de Sistemas</h1>
        <form action="guardar_analista.php" method="POST" enctype="multipart/form-data">
            <!-- Nombre y Cédula -->
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control w-50" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula</label>
                <input type="text" class="form-control w-25" id="cedula" name="cedula" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control w-50" id="email" name="email" required>
            </div>

            <!-- Foto -->
            <div class="mb-3">
                <label for="foto" class="form-label">Foto del Funcionario</label>
                <input type="file" class="form-control w-75" id="foto" name="foto" accept="image/*" required>
            </div>

            <!-- Regional y Kilómetros -->
            <div class="mb-3">
                <label for="id_regional" class="form-label">Regional</label>
                <select class="form-select w-50" id="id_regional" name="id_regional" required>
                    <option value="">Seleccione una regional</option>
                    <?php
                    // Generar las opciones del select
                    foreach ($regionales as $nombre => $valor) {
                        echo "<option value='$valor'>$nombre</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="kilometros" class="form-label">Distancia en Kilómetros del DRE a la Instituci&oacute;n</label>
                <input type="number" class="form-control w-25" id="kilometros" name="kilometros" required>
            </div>

            <h4 class="text-center my-3 text-secondary">Afinidad con las &aacute;reas</h4>
            <!-- Calificación por Áreas -->
            <div class="mb-3">
                <label class="form-label">Soporte y Mantenimiento</label>
                <div class="rating">
                    <input type="radio" id="mantenimiento5" name="mantenimiento" value="5"><label for="mantenimiento5">★</label>
                    <input type="radio" id="mantenimiento4" name="mantenimiento" value="4"><label for="mantenimiento4">★</label>
                    <input type="radio" id="mantenimiento3" name="mantenimiento" value="3"><label for="mantenimiento3">★</label>
                    <input type="radio" id="mantenimiento2" name="mantenimiento" value="2"><label for="mantenimiento2">★</label>
                    <input type="radio" id="mantenimiento1" name="mantenimiento" value="1"><label for="mantenimiento1">★</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Redes y Telecomunicaciones</label>
                <div class="rating">
                    <input type="radio" id="redes5" name="redes" value="5"><label for="redes5">★</label>
                    <input type="radio" id="redes4" name="redes" value="4"><label for="redes4">★</label>
                    <input type="radio" id="redes3" name="redes" value="3"><label for="redes3">★</label>
                    <input type="radio" id="redes2" name="redes" value="2"><label for="redes2">★</label>
                    <input type="radio" id="redes1" name="redes" value="1"><label for="redes1">★</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Sistemas y Servidores</label>
                <div class="rating">
                    <input type="radio" id="configuracion5" name="configuracion" value="5"><label for="configuracion5">★</label>
                    <input type="radio" id="configuracion4" name="configuracion" value="4"><label for="configuracion4">★</label>
                    <input type="radio" id="configuracion3" name="configuracion" value="3"><label for="configuracion3">★</label>
                    <input type="radio" id="configuracion2" name="configuracion" value="2"><label for="configuracion2">★</label>
                    <input type="radio" id="configuracion1" name="configuracion" value="1"><label for="configuracion1">★</label>
                </div>
            </div>

            <!-- Botón de Envío -->
            <button type="submit" class="btn btn-primary">Guardar Analista</button>
        </form>
    </div>

    <!-- Bootstrap 5 JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        // Validación de formato de email
        function validarEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }    
    </script>
</body>
</html>