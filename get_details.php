<?php
require_once("conexion.php");
$link = $mysqli;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "SELECT * FROM t_placa WHERE id_placa = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="row">';
            echo '<div class="col-md-6"><strong>ID Placa:</strong> ' . htmlspecialchars($row['id_placa']) . '</div>';
            echo '<div class="col-md-6"><strong>Código:</strong> ' . htmlspecialchars($row['codigo']) . '</div>';
            echo '</div><div class="row mt-2">';
            echo '<div class="col-md-6"><strong>Placa:</strong> ' . htmlspecialchars($row['placa']) . '</div>';
            echo '<div class="col-md-6"><strong>Serial:</strong> ' . htmlspecialchars($row['serial']) . '</div>';
            echo '</div><div class="row mt-2">';
            echo '<div class="col-md-6"><strong>ID Activo:</strong> ' . htmlspecialchars($row['id_activo']) . '</div>';
            echo '<div class="col-md-6"><strong>ID Estado:</strong> ' . htmlspecialchars($row['id_estado']) . '</div>';
            echo '</div><div class="row mt-2">';
            echo '<div class="col-md-6"><strong>Prestar:</strong> ' . htmlspecialchars($row['prestar']) . '</div>';
            echo '<div class="col-md-6"><strong>Activo:</strong> ' . htmlspecialchars($row['activo']) . '</div>';
            echo '</div><div class="row mt-2">';
            echo '<div class="col-md-6"><strong>ID Fondos:</strong> ' . htmlspecialchars($row['id_fondos']) . '</div>';
            echo '<div class="col-md-6"><strong>Alias ID:</strong> ' . htmlspecialchars($row['alias_id']) . '</div>';
            echo '</div><div class="row mt-2">';
            echo '<div class="col-md-6"><strong>Número Activo:</strong> ' . htmlspecialchars($row['numero_activo']) . '</div>';
            echo '<div class="col-md-6"><strong>Revisado:</strong> ' . htmlspecialchars($row['revisado']) . '</div>';
            echo '</div><div class="row mt-2">';
            echo '<div class="col-md-6"><strong>ID Lugar:</strong> ' . htmlspecialchars($row['id_lugar']) . '</div>';
            echo '<div class="col-md-6"><strong>En Uso:</strong> ' . ($row['enuso'] ? 'Sí' : 'No') . '</div>';
            echo '</div><div class="row mt-2">';
            echo '<div class="col-md-6"><strong>Donar:</strong> ' . ($row['donar'] ? 'Sí' : 'No') . '</div>';
            echo '<div class="col-md-6"><strong>Marcado:</strong> ' . ($row['marcado'] ? 'Sí' : 'No') . '</div>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning">No se encontraron detalles para el ID proporcionado.</div>';
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo '<div class="alert alert-danger">Error en la consulta: ' . mysqli_error($link) . '</div>';
    }
    
    mysqli_close($link);
} else {
    echo '<div class="alert alert-danger">ID no válido.</div>';
}
?>