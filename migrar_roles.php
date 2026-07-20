<?php
require_once("conexion.php");
$link = $mysqli;

if (mysqli_connect_errno()) {
    die("Error de conexion: " . mysqli_connect_error());
}

mysqli_set_charset($link, "utf8");

echo "=== MIGRACI\u00d3N: t_lista_blanca \u2192 usuarios + usuarios_roles ===\n\n";

$query = mysqli_query($link, "SELECT * FROM t_lista_blanca WHERE id_rol IN (2,3,4)") or die(mysqli_error($link));

$insertados = 0;
$omitidos = 0;
$errores = 0;

while ($row = mysqli_fetch_array($query)) {
    $cedula = $row['cedula'];
    $nombre = $row['nombre'];
    $codigo = $row['codigo'];
    $id_rol = $row['id_rol'];

    $res = mysqli_query($link, "SELECT id FROM usuarios WHERE cedula = '$cedula' LIMIT 1");
    if (mysqli_num_rows($res) > 0) {
        $u = mysqli_fetch_array($res);
        $usuario_id = $u['id'];
    } else {
        $resCorreo = mysqli_query($link, "SELECT id FROM usuarios WHERE correo = '$nombre' LIMIT 1");
        if (mysqli_num_rows($resCorreo) > 0) {
            $u = mysqli_fetch_array($resCorreo);
            $usuario_id = $u['id'];
            echo "~ Usuario ya existe con correo $nombre (cédula distinta: $cedula), reusando id=$usuario_id\n";
        } else {
            $insert = "INSERT INTO usuarios (cedula, nombre, correo, azure_id, sexo, created_at, updated_at)
                        VALUES ('$cedula', '$nombre', '$nombre', '', 'N', NOW(), NOW())";
            if (mysqli_query($link, $insert)) {
                $usuario_id = mysqli_insert_id($link);
            } else {
                echo "ERROR creando usuario c\u00e9dula $cedula: " . mysqli_error($link) . "\n";
                $errores++;
                continue;
            }
        }
    }

    $res2 = mysqli_query($link, "SELECT id FROM usuarios_roles
                                  WHERE usuario_id = $usuario_id
                                  AND codigo_presu = '$codigo'
                                  AND eliminado = 0
                                  LIMIT 1");

    if (mysqli_num_rows($res2) == 0) {
        $insert2 = "INSERT INTO usuarios_roles (usuario_id, rol_id, subsistema_id, codigo_presu, created_at)
                     VALUES ($usuario_id, $id_rol, 1, '$codigo', NOW())";
        if (mysqli_query($link, $insert2)) {
            echo "\u2713 Insertado: c\u00e9dula=$cedula, rol=$id_rol, codigo=$codigo\n";
            $insertados++;
        } else {
            echo "ERROR insertando rol para c\u00e9dula $cedula: " . mysqli_error($link) . "\n";
            $errores++;
        }
    } else {
        $omitidos++;
    }
}

echo "\n=== RESUMEN ===\n";
echo "Insertados: $insertados\n";
echo "Omitidos (ya exist\u00edan): $omitidos\n";
echo "Errores: $errores\n";
echo "Total procesados: " . ($insertados + $omitidos + $errores) . "\n";

mysqli_close($link);
?>
