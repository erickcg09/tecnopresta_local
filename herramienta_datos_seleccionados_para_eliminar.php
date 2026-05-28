<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];

if (isset($_POST['fondos'])) {
    $fondos = $_POST['fondos'];
    $codigo_centro = $_POST['codigo_centro'];

    $consulta = mysqli_query($link, "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
        FROM t_activo Ta
        INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
        INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
        INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
        INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
        WHERE Tp.codigo = '$codigo_centro' AND Tp.id_fondos = '$fondos'
        ORDER BY Tp.placa ASC") or die(mysqli_error($link));

    $totalRegistros = mysqli_num_rows($consulta);

    if ($totalRegistros > 0) {
        // Contador de registros
        echo '<div class="contador-registros-ajax mb-3" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 10px 15px; border-radius: 8px; font-weight: bold; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <i class="fas fa-database me-2"></i>
                Registros encontrados: <strong>' . $totalRegistros . '</strong> ' . ($totalRegistros == 1 ? 'registro' : 'registros') . '
              </div>';

        echo '<table class="table table-hover">
            <thead>
                <tr>
                    <th>Seleccione</th>
                    <th>Activo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Color</th>
                    <th>Placa</th>
                    <th>Serial</th>
                    <th colspan="4">
                        <button class="btn btn-dark btn-lg btn-block" type="submit" name="btnMarcar" id="btnMarcar">
                            <i class="bi bi-trash3"></i> Eliminar
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody class="BusquedaRapida">';

        while ($activos = mysqli_fetch_array($consulta)) {
            echo '<tr>
                <td><input type="checkbox" class="selectall" name="idsplacas[]" value="' . $activos['id_placa'] . '"/></td>
                <td>' . $activos['clase'] . '</td>
                <td>' . $activos['marca'] . '</td>
                <td>' . $activos['modelo'] . '</td>
                <td>' . $activos['color'] . '</td>
                <td>' . $activos['placa'] . '</td>
                <td>' . $activos['serial'] . '</td>
            </tr>';
        }

        echo '</tbody></table>';

        // Script para actualizar contador de seleccionados
        echo '<script>
        $(document).ready(function() {
            // Contador de checkboxes seleccionados
            $(".selectall").change(function() {
                const seleccionados = $(".selectall:checked").length;
                $(".contador-seleccionados").remove();
                $(".contador-registros-ajax").after(\'<div class="contador-seleccionados mb-2" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; padding: 8px 12px; border-radius: 6px; font-weight: bold; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"><i class="fas fa-check-circle me-2"></i>Registros seleccionados: <strong>\' + seleccionados + \'</strong> \' + (seleccionados == 1 ? "registro" : "registros") + \'</div>\');
                
                // Habilitar/deshabilitar botón de eliminar
                $("#btnMarcar").prop("disabled", seleccionados === 0);
            });

            // Inicializar estado del botón
            $("#btnMarcar").prop("disabled", true);
        });
        </script>';
    } else {
        echo '<div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i> No se encontraron registros con los criterios seleccionados.
              </div>';
    }

    mysqli_close($link);
}
?>