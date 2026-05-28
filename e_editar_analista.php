<?php
    
    //session_start();
    include_once 'sql/bd.php';

    if (!isset($_SESSION['id_visita'])) {
        die("Sesión de visita no encontrada.");
    }

    //$id_visita = $_SESSION['id_visita'];
    $id_visita = $_SESSION['id_visita'] ?? null;

    if (empty($id_visita)) {
    header("Location: formulario_panel_visitas_sitio.html");
    exit;
}
    
    $conexionBD=BD::crearInstancia();    //Se crea la instancia de la conexión

    //session_start();
    $mensajeError = $_SESSION['error'] ?? null;
    unset($_SESSION['error']);

    $mensaje = $_SESSION['msg'] ?? null;
    unset($_SESSION['msg']);

    $datosViejos = $_SESSION['datosA'] ?? null;
    unset($_SESSION['datosA']);

    $accion= $_POST['accion'] ?? '';

    //print_r($_POST);

    $sql = "SELECT * FROM visitas_sitio WHERE id_visita = ? LIMIT 1"; 
    $consulta=$conexionBD->prepare($sql);
    $consulta->bindValue(1, $id_visita, PDO::PARAM_INT);
    $consulta->execute();
    //$consulta->execute([$id_visita]);
    $visita = $consulta->fetch(PDO::FETCH_ASSOC); //Devuelve solo uno

    $codigo_institucion = $visita['codigo_institucion'];
    $t_estado_visitas_id = $visita['estado'];

    if ($datosViejos){
        $asunto = $datosViejos['asunto'] ?? '';
        $problema = $datosViejos['problema'] ?? '';
        $profesionalesSeleccionados = $datosViejos['profesionales'] ?? [];
    } else {
        $asunto = $visita['titulo_problema'];
        $problema = trim($visita['descripcion_problema']);
        $profesionalesSeleccionados = [];
    }

    // *** Consulta con la lista con todos los profesionales que se pueden asignar.
    $sql = "SELECT id_analista, nombre
            FROM t_analista";
    
    $consulta=$conexionBD->prepare($sql);
    $consulta->execute();
    $listaProf=$consulta->fetchAll();
    
    // *** Consulta con los profesionales Asignados
    $sql = "SELECT t.id_analista_visita, v.codigo_institucion, v.nombre_institucion, v.titulo_problema, v.descripcion_problema, a.id_analista as analista_cdg, a.nombre as analista_nmb
            FROM visitas_sitio v, t_analista a, analistas_visita t WHERE v.id_visita = t.id_visita AND a.id_analista = t.id_analista and t.id_visita = ?";
    
    $consulta=$conexionBD->prepare($sql);
    $consulta->execute([$id_visita]);
    $listaProfAsig=$consulta->fetchAll();

    if (!$datosViejos) {
        $profesionalesSeleccionados=[];
        foreach ($listaProfAsig as $profAsig) {
            $profesionalesSeleccionados[] = $profAsig['analista_cdg'];
    }
}

    if ($accion!="") {
        switch ($accion) {
            case 'guardar':
                try {
                    $profesionales = $_POST['profesionales'] ?? []; //si no hay, carga vació
                    if (count($profesionales) > 0 && !empty($_POST['asunto']) && !empty($_POST['problema'])) {
                
                        $conexionBD->beginTransaction();
                        //Actualizar la tabla visita
                        //$sql = $db->prepare()
                        $sql = "UPDATE visitas_sitio SET titulo_problema = ?, descripcion_problema = ?
                                WHERE id_visita = ?";
                        $consulta=$conexionBD->prepare($sql);
                        $consulta->execute([trim($_POST['asunto']), trim($_POST['problema']), $id_visita]);

                        //Borra las asignaciones previas
                        $sql = "DELETE FROM analistas_visita WHERE id_visita = ?";
                        $consulta=$conexionBD->prepare($sql);
                        $consulta->execute([$id_visita]);

                        //Inserta los nuevos profesionales
                    
                        $sql = "INSERT INTO analistas_visita (id_analista_visita, id_visita, id_analista, fecha_asignacion) VALUES (NULL, ?, ?, ?)";
                        $consulta=$conexionBD->prepare($sql);
                        $fecha = date('Y-m-d H:i:s'); 
                        foreach ($profesionales as $idprofesional) {                    
                            $consulta->execute([$id_visita, $idprofesional,$fecha]);
                        }

                        $conexionBD->commit();                        
                        //header("Location: e_v_editar_analista.php?id=$id_visita");
                        $_SESSION['id_visita'] = $id_visita;
                        /*
                        $_SESSION['msg'] = "Datos actualizados satisfactoriamente.";
                        header("Location: formulario_hoja_trabajo_visitas_sitio.html");
                        exit;*/
                        echo '
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <meta charset="UTF-8">
                                <title>Guardando...</title>
                                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                            </head>
                            <body class="bg-light">

                            <div class="container mt-5">
                                <div class="alert alert-success shadow">
                                    Datos actualizados correctamente. Redirigiendo...
                                </div>
                            </div>
                            
                            <script>
                                setTimeout(function(){
                                    window.location.href = "formulario_hoja_trabajo_visitas_sitio.html";
                                }, 2000);
                            </script>
                            </body>
                            </html>
                            ';
                            exit;

                        //header("Location: formulario_hoja_trabajo_visitas_sitio.html?id=$id_visita");
                    } elseif (count($profesionales) == 0) {                        
                        $_SESSION['error'] = "Debe asignar al menos un profesional.";
                        // Guardar lo que el usuario escribió
                        $_SESSION['datosA'] = $_POST;
                        header("Location: e_v_editar_analista.php?id=$id_visita");
                        exit;
                    } elseif (empty($_POST['asunto'])){                        
                        $_SESSION['error'] = "Debe indicar el asunto de la gira.";
                        // Guardar lo que el usuario escribió
                        $_SESSION['datosA'] = $_POST;
                        header("Location: e_v_editar_analista.php?id=$id_visita");
                        exit;
                    } elseif (empty($_POST['problema'])){                        
                        $_SESSION['error'] = "Debe indicar la descripción de problema.";
                        // Guardar lo que el usuario escribió
                        $_SESSION['datosA'] = $_POST;
                        header("Location: e_v_editar_analista.php?id=$id_visita");
                        //header("Location: formulario_hoja_trabajo_visitas_sitio.html?id=$id_visita");
                        
                        exit;
                    }
                    
                    } catch (Exception $e) {
                        $conexionBD->rollBack();
                        throw $e;
                    }
                break;
            
            case 'cancelar':
                $_SESSION['id_visita'] = $id_visita;
                header("Location: e_v_editar_analista.php");
                exit;
                break;
            
            case 'regresar':
                //header("Location: formulario_hoja_trabajo_visitas_sitio.html?id=$id_visita");
                $_SESSION['id_visita'] = $id_visita;
                header("Location: formulario_hoja_trabajo_visitas_sitio.html");
                exit;
                break;
        }
        
    }
///id_visita
  //  formulario_panel_visitas_sitio.html

?>