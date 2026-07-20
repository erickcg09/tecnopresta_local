# Script para generar Documento de Plan de Implementacion (Word)
param(
    [string]$outputPath = "C:\xampp\htdocs\tecnopresta-yo\docs\Plan_Implementacion_SGEM.docx"
)

try {
    $word = New-Object -ComObject Word.Application
    $word.Visible = $false
    $doc = $word.Documents.Add()
    $selection = $word.Selection

    $mepBlue = [int]0x003876
    $mepGold = [int]0x51A9C8
    $mepDark = [int]0x2C3E50
    $mepWhite = [int]0xFFFFFF

    function Add-Heading {
        param($text, $level)
        $selection.Font.Size = @{1=18;2=14;3=12}[$level]
        $selection.Font.Bold = $true
        $selection.Font.Color = @{1=$mepBlue;2=$mepBlue;3=$mepDark}[$level]
        $selection.Font.Name = "Calibri"
        $selection.TypeText($text)
        $selection.TypeParagraph()
        $selection.TypeParagraph()
    }

    function Add-Body {
        param($text)
        $selection.Font.Size = 11
        $selection.Font.Bold = $false
        $selection.Font.Color = $mepDark
        $selection.Font.Name = "Calibri"
        $selection.TypeText($text)
        $selection.TypeParagraph()
        $selection.TypeParagraph()
    }

    function Add-Bullet {
        param($text)
        $selection.Font.Size = 11
        $selection.Font.Bold = $false
        $selection.Font.Color = $mepDark
        $selection.TypeText("     - $text")
        $selection.TypeParagraph()
    }

    function Add-Separator {
        $selection.Font.Size = 8
        $selection.Font.Color = $mepGold
        $selection.TypeText("___________________________________________")
        $selection.TypeParagraph()
        $selection.TypeParagraph()
    }

    # ============================================================
    # PORTADA
    # ============================================================
    $selection.ParagraphFormat.Alignment = 1
    for ($i = 0; $i -lt 6; $i++) { $selection.TypeParagraph() }

    $selection.Font.Size = 28
    $selection.Font.Bold = $true
    $selection.Font.Color = $mepBlue
    $selection.Font.Name = "Calibri"
    $selection.TypeText("Sistema Gestor de la Estructura del Menu")
    $selection.TypeParagraph()
    $selection.TypeParagraph()

    $selection.Font.Size = 20
    $selection.Font.Bold = $false
    $selection.Font.Color = $mepDark
    $selection.TypeText("Plan de Implementacion Detallado")
    $selection.TypeParagraph()
    $selection.TypeParagraph()

    $selection.Font.Size = 14
    $selection.Font.Color = $mepGold
    $selection.TypeText("___________________________________________")
    $selection.TypeParagraph()
    $selection.TypeParagraph()
    $selection.TypeParagraph()

    $selection.Font.Size = 12
    $selection.Font.Color = $mepDark
    $selection.TypeText("Version: 1.0")
    $selection.TypeParagraph()
    $selection.TypeText("Fecha: Julio 2026")
    $selection.TypeParagraph()
    $selection.TypeText("Total archivos: 9 PHP + 1 SQL")
    $selection.TypeParagraph()
    $selection.TypeText("Tiempo estimado: ~24 horas de desarrollo")

    $selection.InsertBreak(7)

    # ============================================================
    # 1. RESUMEN
    # ============================================================
    $selection.ParagraphFormat.Alignment = 0

    Add-Heading "1. Resumen del Proyecto" 1
    Add-Body "Creacion del modulo 'Gestor del Sistema' bajo el subsistema 'Administracion del Sistema' (id=4), compuesto por 3 formularios que permiten al usuario Root administrar la estructura completa del menu del sistema TecnoPresta: subsistemas, modulos, formularios, permisos y roles. Todo sin intervencion manual en la base de datos."

    Add-Separator

    # ============================================================
    # 2. ESTRUCTURA DE ARCHIVOS
    # ============================================================
    Add-Heading "2. Estructura de Archivos" 1
    Add-Body "La implementacion comprende 9 archivos PHP nuevos organizados en 3 grupos funcionales, mas 1 script SQL de inicializacion:"

    $selection.TypeParagraph()

    $tableFiles = $doc.Tables.Add($selection.Range, 10, 3)
    $tableFiles.Borders.InsideLineStyle = 1
    $tableFiles.Borders.OutsideLineStyle = 1

    $headers = @("Archivo", "Tipo", "Proposito")
    for ($i = 0; $i -lt 3; $i++) {
        $tableFiles.Cell(1,$i+1).Range.Font.Bold = $true
        $tableFiles.Cell(1,$i+1).Range.Font.Color = $mepWhite
        $tableFiles.Cell(1,$i+1).Shading.BackgroundPatternColor = $mepBlue
        $tableFiles.Cell(1,$i+1).Range.Text = $headers[$i]
    }

    $files = @(
        @("sql/gestor_sistema_init.sql", "SQL", "Script de inicializacion: modulo + 3 formularios + permisos Root"),
        @("sql/gestor_modulos.php", "PHP (datos)", "Endpoint JSON: lista subsistemas y modulos"),
        @("actualizar_gestor_modulos_n.php", "PHP (accion)", "Endpoint JSON: CRUD subsistemas/modulos"),
        @("gestor_modulos_n.php", "PHP (formulario)", "Formulario 1: Gestion de Subsistemas y Modulos"),
        @("sql/gestor_formularios.php", "PHP (datos)", "Endpoint JSON: lista formularios, permisos, acciones"),
        @("actualizar_gestor_formularios_n.php", "PHP (accion)", "Endpoint JSON: CRUD formularios/permisos"),
        @("gestor_formularios_n.php", "PHP (formulario)", "Formulario 2: Gestion de Formularios y Permisos"),
        @("sql/gestor_roles.php", "PHP (datos)", "Endpoint JSON: arbol completo permisos por rol"),
        @("actualizar_gestor_roles_permisos_n.php", "PHP (accion)", "Endpoint JSON: toggle permisos por rol"),
        @("gestor_roles_permisos_n.php", "PHP (formulario)", "Formulario 3: Asignacion de Permisos a Roles")
    )
    for ($i = 0; $i -lt $files.Count; $i++) {
        $tableFiles.Cell($i+2,1).Range.Text = $files[$i][0]
        $tableFiles.Cell($i+2,2).Range.Text = $files[$i][1]
        $tableFiles.Cell($i+2,3).Range.Text = $files[$i][2]
    }

    $selection.TypeParagraph()
    $selection.TypeParagraph()
    Add-Separator

    # ============================================================
    # 3. ORDEN DE IMPLEMENTACIÓN
    # ============================================================
    Add-Heading "3. Orden de Implementacion" 1
    Add-Body "Cada fase tiene una dependencia clara de la fase anterior. Se recomienda seguir este orden estrictamente para evitar errores de referencias cruzadas."

    $selection.TypeParagraph()

    $tableFases = $doc.Tables.Add($selection.Range, 8, 5)
    $tableFases.Borders.InsideLineStyle = 1
    $tableFases.Borders.OutsideLineStyle = 1

    $headers = @("Fase", "Archivos", "Dependencia", "Esfuerzo", "Descripcion")
    for ($i = 0; $i -lt 5; $i++) {
        $tableFases.Cell(1,$i+1).Range.Font.Bold = $true
        $tableFases.Cell(1,$i+1).Range.Font.Color = $mepWhite
        $tableFases.Cell(1,$i+1).Shading.BackgroundPatternColor = $mepBlue
        $tableFases.Cell(1,$i+1).Range.Text = $headers[$i]
    }

    $fases = @(
        @("0", "sql/gestor_sistema_init.sql", "Ninguna", "15 min", "Script SQL: modulo + 3 formularios + permisos Root"),
        @("1", "sql/gestor_modulos.php + actualizar_gestor_modulos_n.php", "Fase 0", "2 h", "Endpoints datos y accion para Modulos"),
        @("2", "gestor_modulos_n.php", "Fase 0, 1", "5 h", "Formulario: Gestion de Subsistemas y Modulos"),
        @("3", "sql/gestor_formularios.php + actualizar_gestor_formularios_n.php", "Fase 0", "2 h", "Endpoints datos y accion para Formularios"),
        @("4", "gestor_formularios_n.php", "Fase 0, 3", "5 h", "Formulario: Gestion de Formularios y Permisos"),
        @("5", "sql/gestor_roles.php + actualizar_gestor_roles_permisos_n.php", "Fase 0", "2.5 h", "Endpoints datos y accion para Roles"),
        @("6", "gestor_roles_permisos_n.php", "Fase 0, 5", "5 h", "Formulario: Asignacion de Permisos a Roles"),
        @("7", "Pruebas integrales", "Todas", "2 h", "Pruebas de integracion y ajustes finales")
    )
    for ($i = 0; $i -lt $fases.Count; $i++) {
        for ($j = 0; $j -lt 5; $j++) {
            $tableFases.Cell($i+2,$j+1).Range.Text = $fases[$i][$j]
        }
    }

    $selection.TypeParagraph()
    $selection.TypeParagraph()
    Add-Separator

    # ============================================================
    # 4. ESPECIFICACIONES TÉCNICAS
    # ============================================================
    Add-Heading "4. Especificaciones Tecnicas por Archivo" 1

    # 4.0 SQL
    Add-Heading "4.0 sql/gestor_sistema_init.sql" 2
    Add-Body "Script SQL que debe ejecutarse una vez contra la base de datos ltecnopre. Crea el modulo 'Gestor del Sistema' (subsistema_id=4), los 3 formularios con sus datos basicos, los permisos (acciones) correspondientes, y asigna todos los permisos al rol Root (id_rol=1). Todo dentro de una transaccion."
    Add-Bullet "INSERT INTO modulos: nombre, descripcion, subsistema_id=4, ruta_base=/admin/gestor, orden=1"
    Add-Bullet "3 INSERT INTO formularios: modulo_id, nombre, descripcion, ruta, imagen=NULL, orden, color=#003876"
    Add-Bullet "Permisos: Formulario 1 (ver, crear, editar, eliminar, auditar), Formulario 2 (ver, crear, editar, eliminar, auditar), Formulario 3 (ver, asignar, auditar)"
    Add-Bullet "roles_permisos: todos los permisos nuevos asignados a rol_id=1 (Root)"

    # 4.1
    Add-Heading "4.1 sql/gestor_modulos.php - Endpoint de datos" 2
    Add-Body "Endpoint JSON que devuelve la lista completa de subsistemas y modulos. Metodo GET. Consulta las tablas subsistemas y modulos con JOIN para obtener el nombre del subsistema en cada modulo. Filtra por eliminado=0. Respuesta incluye array 'subsistemas', 'modulos' y flag 'success'."

    # 4.2
    Add-Heading "4.2 actualizar_gestor_modulos_n.php - Endpoint de accion" 2
    Add-Body "Endpoint JSON que recibe POST con body JSON. Soporta 6 acciones: crear_subsistema, editar_subsistema, toggle_subsistema, crear_modulo, editar_modulo, toggle_modulo. La accion toggle_modulo valida primero que no haya formularios activos (eliminado=0) antes de desactivar. Todas las operaciones usan prepared statements. Las operaciones de toggle incluyen validacion de integridad."

    # 4.3
    Add-Heading "4.3 gestor_modulos_n.php - Formulario" 2
    Add-Body "Formulario principal que carga via fetch los datos del endpoint sql/gestor_modulos.php y renderiza dos tablas MEP: una para subsistemas (seleccionables via clic) y otra para modulos (filtrados por subsistema seleccionado). Usa modales Bootstrap 5 para crear/editar entidades y para confirmar desactivaciones. Implementa el patron hero-box, tabla activos-table, botones flotantes, y validaciones tanto en frontend como backend."
    Add-Bullet "PHP: session_start, require usuarioAzure, validar ACCESO_SEGURO, verificar esUsuarioRoot()"
    Add-Bullet "HTML: head con CDN Bootstrap 5 + iconos, includes header/footer"
    Add-Bullet "JS: fetch GET para carga inicial, fetch POST para CRUD, mostrarModal/mostrarConfirmacion"
    Add-Bullet "Validacion: un subsistema no se puede desactivar si tiene modulos activos"
    Add-Bullet "Validacion: un modulo no se puede desactivar si tiene formularios activos"

    # 4.4
    Add-Heading "4.4 sql/gestor_formularios.php - Endpoint de datos" 2
    Add-Body "Endpoint JSON que devuelve subsistemas, modulos, formularios y acciones. Los formularios incluyen un array 'acciones' con los IDs de las acciones asignadas via tabla permisos. Soporta filtro opcional por modulo_id. Respuesta completa incluye todos los datos necesarios para renderizar el formulario sin llamadas adicionales."

    # 4.5
    Add-Heading "4.5 actualizar_gestor_formularios_n.php - Endpoint de accion" 2
    Add-Body "Endpoint JSON que recibe POST con body JSON. Soporta acciones: crear, editar y toggle. La accion 'crear' y 'editar' reciben un array 'acciones' con los IDs de las acciones a asignar. La accion 'editar' reemplaza completamente los permisos (DELETE + INSERT en transaccion). La accion 'toggle' solo cambia el flag eliminado."

    # 4.6
    Add-Heading "4.6 gestor_formularios_n.php - Formulario" 2
    Add-Body "Formulario con filtros en cascada (subsistema y modulo) que carga formularios del modulo seleccionado. Cada fila muestra los permisos como badges color-coded. Modal de creacion/edicion incluye seccion de checkboxes para las 11 acciones del sistema con botones [Seleccionar todas] y [Limpiar]. Implementa el mismo patron MEP de diseno."

    # 4.7
    Add-Heading "4.7 sql/gestor_roles.php - Endpoint de datos" 2
    Add-Body "Endpoint JSON que devuelve la estructura completa del arbol de permisos: roles, subsistemas, modulos, formularios, acciones, permisos y roles_permisos. El frontend utiliza estos datos para construir el arbol colapsable y determinar los estados de los checkboxes y badges."

    # 4.8
    Add-Heading "4.8 actualizar_gestor_roles_permisos_n.php - Endpoint de accion" 2
    Add-Body "Endpoint JSON que recibe POST con {rol_id, cambios: [{formulario_id, accion_id, activo}]}. Procesa cada cambio diferencial: si activo=true, INSERT IGNORE INTO permisos + INSERT IGNORE INTO roles_permisos. Si activo=false, DELETE FROM roles_permisos + DELETE FROM permisos (solo si huerfano). Todo en una transaccion."

    # 4.9
    Add-Heading "4.9 gestor_roles_permisos_n.php - Formulario" 2
    Add-Body "Formulario mas complejo de los tres. Presenta un selector de rol, un arbol colapsable de subsistemas/modulos/formularios, y un campo de busqueda. Cada formulario muestra checkbox de estado (todos/parcial/ninguno) mas badges toggle por accion. Los cambios se acumulan en un array diferencial y se envian al guardar. El boton flotante muestra contador de cambios pendientes."

    Add-Heading "4.10 Patrones de codigo transversales" 2
    Add-Body "Todos los archivos implementan los siguientes patrones de forma consistente:"
    Add-Bullet "Seguridad: validacion de ACCESO_SEGURO, sesion Azure, y esUsuarioRoot()"
    Add-Bullet "Prepared statements en todas las consultas SQL"
    Add-Bullet "Transacciones en operaciones multi-tabla"
    Add-Bullet "Respuestas JSON consistentes: {success, message, data}"
    Add-Bullet "Fetch API para comunicacion asincrona"
    Add-Bullet "Modales Bootstrap 5 para exito, error y confirmacion"
    Add-Bullet "Hero-box con icono, titulo y descripcion"
    Add-Bullet "Tabla MEP con clase activos-table"

    Add-Separator

    # ============================================================
    # 5. PATRONES DE CÓDIGO
    # ============================================================
    Add-Heading "5. Patrones de Codigo" 1

    Add-Heading "5.1 Seguridad (aplicado en los 9 archivos PHP)" 2
    Add-Body "Cada formulario verifica ACCESO_SEGURO (definido por navegar.php) para evitar acceso directo. Cada endpoint verifica sesion Azure y que el usuario sea Root mediante esUsuarioRoot(). Las respuestas de error retornan JSON con codigo HTTP apropiado."

    Add-Heading "5.2 Prepared Statements" 2
    Add-Body "Todas las consultas SQL utilizan prepared statements con parametros posicionales (?). Ninguna consulta concatena valores directamente."

    Add-Heading "5.3 Transacciones" 2
    Add-Body "Las operaciones que modifican multiples tablas (crear formulario + permisos, editar formulario + permisos, toggle permisos) se ejecutan dentro de START TRANSACTION / COMMIT con rollback en caso de error."

    Add-Heading "5.4 Estructura de respuesta JSON" 2
    Add-Body "Endpoints de datos: {success: bool, ...datos}. Endpoints de accion: {success: bool, message: string, code: string}. Codigos de error: DUPLICATE (nombre duplicado), HAS_ACTIVE_FORMS (modulo con forms activos), HAS_ACTIVE_MODULES (subsistema con modulos activos)."

    Add-Heading "5.5 Patron AJAX" 2
    Add-Body "Los formularios usan fetch() para GET (carga de datos) y POST (envio de acciones). Todas las llamadas son async/await con manejo de errores try/catch. La respuesta se valida con data.success antes de mostrar exito o error."

    Add-Heading "5.6 Patron Modales Bootstrap 5" 2
    Add-Body "Tres modales reutilizables: modalExito (icono check + mensaje + boton aceptar), modalError (icono alerta + mensaje + boton cerrar), modalConfirmacion (mensaje + botones cancelar/confirmar con callback). Inicializados en DOMContentLoaded."

    Add-Heading "5.7 Patron Tabla con datos dinamicos" 2
    Add-Body "Las tablas se renderizan desde JavaScript creando elementos TR/TD. Cada fila tiene clases condicionales (table-danger si eliminado=1). Los botones de accion usan onclick con funciones nombradas. El escape de texto se hace via funcion escapeHtml()."

    Add-Heading "5.8 Patron Hero-box" 2
    Add-Body "Estructura: div.hero-box > div.row.align-items-center > div.col-auto (icono Bootstrap) + div.col (h1 + p). Icono en color dorado MEP (#C8A951), texto blanco con opacidad 0.8 en subtitulo."

    Add-Heading "5.9 Patron Tabla MEP" 2
    Add-Body "Tabla con clase .table.activos-table: thead con gradient background MEP, filas con hover, columnas con clases .th-id, .th-orden, .th-estado, .th-acciones para anchos fijos. Badges con clase .badge bg-success (activo) o .badge bg-secondary (inactivo)."

    Add-Separator

    # ============================================================
    # 6. MANEJO DE ERRORES
    # ============================================================
    Add-Heading "6. Manejo de Errores" 1

    $tableErr = $doc.Tables.Add($selection.Range, 7, 3)
    $tableErr.Borders.InsideLineStyle = 1
    $tableErr.Borders.OutsideLineStyle = 1

    $headers = @("Escenario", "Codigo HTTP", "Respuesta")
    for ($i = 0; $i -lt 3; $i++) {
        $tableErr.Cell(1,$i+1).Range.Font.Bold = $true
        $tableErr.Cell(1,$i+1).Range.Font.Color = $mepWhite
        $tableErr.Cell(1,$i+1).Shading.BackgroundPatternColor = $mepBlue
        $tableErr.Cell(1,$i+1).Range.Text = $headers[$i]
    }

    $errors = @(
        @("Sesion invalida", "401", "{success:false, message:'Sesion invalida'}"),
        @("No es Root", "403", "{success:false, message:'Acceso no autorizado'}"),
        @("Nombre duplicado", "200", "{success:false, message:'...', code:'DUPLICATE'}"),
        @("Modulo con forms activos", "200", "{success:false, message:'...', code:'HAS_ACTIVE_FORMS'}"),
        @("Subsistema con modulos activos", "200", "{success:false, message:'...', code:'HAS_ACTIVE_MODULES'}"),
        @("Error SQL", "500", "{success:false, message:'Error de BD: ...'}"),
        @("Error de conexion", "0", "Modal de error en frontend")
    )
    for ($i = 0; $i -lt $errors.Count; $i++) {
        for ($j = 0; $j -lt 3; $j++) {
            $tableErr.Cell($i+2,$j+1).Range.Text = $errors[$i][$j]
        }
    }

    $selection.TypeParagraph()
    $selection.TypeParagraph()
    Add-Separator

    # ============================================================
    # 7. PRUEBAS
    # ============================================================
    Add-Heading "7. Pruebas" 1

    Add-Heading "7.1 Pruebas por endpoint" 2
    Add-Bullet "Crear subsistema con datos validos -> success:true"
    Add-Bullet "Crear subsistema sin nombre -> success:false"
    Add-Bullet "Crear subsistema con nombre duplicado -> success:false, code:DUPLICATE"
    Add-Bullet "Desactivar modulo sin formularios -> success:true"
    Add-Bullet "Desactivar modulo con formularios activos -> success:false, code:HAS_ACTIVE_FORMS"
    Add-Bullet "Asignar permiso a rol -> success:true"
    Add-Bullet "Revocar permiso de rol -> success:true"
    Add-Bullet "Reactivar entidad -> success:true"
    Add-Bullet "Acceso sin Root -> success:false, HTTP 403"

    Add-Heading "7.2 Pruebas de integracion" 2
    Add-Bullet "Flujo completo: crear subsistema -> crear modulo -> crear formulario -> asignar permisos"
    Add-Bullet "Verificar que el menu del sistema refleja los cambios"
    Add-Bullet "Verificar que formularios desactivados no aparecen en el menu"
    Add-Bullet "Verificar que reactivar modulo no afecta formularios"
    Add-Bullet "Verificar que reactivar subsistema no afecta modulos"

    Add-Separator

    # ============================================================
    # 8. POST-IMPLEMENTACIÓN
    # ============================================================
    Add-Heading "8. Post-Implementacion" 1

    $tablePost = $doc.Tables.Add($selection.Range, 5, 2)
    $tablePost.Borders.InsideLineStyle = 1
    $tablePost.Borders.OutsideLineStyle = 1

    $headers = @("Tarea", "Descripcion")
    for ($i = 0; $i -lt 2; $i++) {
        $tablePost.Cell(1,$i+1).Range.Font.Bold = $true
        $tablePost.Cell(1,$i+1).Range.Font.Color = $mepWhite
        $tablePost.Cell(1,$i+1).Shading.BackgroundPatternColor = $mepBlue
        $tablePost.Cell(1,$i+1).Range.Text = $headers[$i]
    }

    $postItems = @(
        @("Cache busting", "Incrementar ?v=N en CSS incluido en los 3 formularios nuevos"),
        @("Limpieza", "Verificar que no hay archivos temporales o de prueba"),
        @("Backup", "Respaldo de BD antes del script de inicializacion"),
        @("Monitoreo", "Verificar logs de error de PHP despues del despliegue"),
        @("Documentacion", "Actualizar diagrama de arquitectura del sistema")
    )
    for ($i = 0; $i -lt $postItems.Count; $i++) {
        $tablePost.Cell($i+2,1).Range.Text = $postItems[$i][0]
        $tablePost.Cell($i+2,2).Range.Text = $postItems[$i][1]
    }

    $selection.TypeParagraph()
    $selection.TypeParagraph()

    # Footer
    $selection.Font.Size = 9
    $selection.Font.Color = [int]0x999999
    $selection.Font.Italic = $true
    $selection.TypeText("--- Fin del Documento ---")

    # ============================================================
    # GUARDAR
    # ============================================================
    $doc.SaveAs([ref]$outputPath)
    $word.Quit()

    Write-Host "Documento creado exitosamente: $outputPath"
}
catch {
    Write-Host "Error: $_"
    if ($word) { $word.Quit() }
    exit 1
}
