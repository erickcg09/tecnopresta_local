<?php
// Archivo para comprobar los datos recibidos por POST

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Datos recibidos:</h2>";

    // Mostrar todos los datos recibidos por POST
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Mostrar detalles específicos
    echo "<h3>Detalles de los campos:</h3>";
    echo "<ul>";
    echo "<li><strong>Clase General del Activo (myDropdown2):</strong> " . htmlspecialchars($_POST['myDropdown2']) . "</li>";
    echo "<li><strong>Clase (hidden):</strong> " . htmlspecialchars($_POST['clase']) . "</li>";
    echo "<li><strong>Marca de Fabricante (myDropdown):</strong> " . htmlspecialchars($_POST['myDropdown']) . "</li>";
    echo "<li><strong>Marca (hidden):</strong> " . htmlspecialchars($_POST['marca']) . "</li>";
    echo "<li><strong>Modelo del Activo:</strong> " . htmlspecialchars($_POST['modelo']) . "</li>";
    echo "<li><strong>Color Predominante (myDropdown3):</strong> " . htmlspecialchars($_POST['myDropdown3']) . "</li>";
    echo "<li><strong>Color (hidden):</strong> " . htmlspecialchars($_POST['color']) . "</li>";
    echo "</ul>";
} else {
    echo "<p>No se han recibido datos por POST.</p>";
}
?>