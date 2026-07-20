# Script para generar Documento de Requerimientos Funcionales (Word)
param(
    [string]$outputPath = "C:\xampp\htdocs\tecnopresta-yo\docs\Requerimientos_Funcionales_SGEM.docx"
)

try {
    $word = New-Object -ComObject Word.Application
    $word.Visible = $false
    $doc = $word.Documents.Add()
    $selection = $word.Selection

    # Colores MEP
    $mepBlue = [int]0x003876
    $mepGold = [int]0x51A9C8
    $mepDark = [int]0x2C3E50
    $mepWhite = [int]0xFFFFFF
    $mepLight = [int]0xF4F7FB

    # ============================================================
    # PORTADA
    # ============================================================
    $selection.ParagraphFormat.Alignment = 1  # Center

    # Espacio superior
    for ($i = 0; $i -lt 6; $i++) { $selection.TypeParagraph() }

    # Título principal
    $selection.Font.Size = 28
    $selection.Font.Bold = $true
    $selection.Font.Color = $mepBlue
    $selection.Font.Name = "Calibri"
    $selection.TypeText("Sistema Gestor de la Estructura del Menu")
    $selection.TypeParagraph()
    $selection.TypeParagraph()

    # Subtítulo
    $selection.Font.Size = 20
    $selection.Font.Bold = $false
    $selection.Font.Color = $mepDark
    $selection.TypeText("Documento de Requerimientos Funcionales")
    $selection.TypeParagraph()
    $selection.TypeParagraph()

    # Línea decorativa
    $selection.Font.Size = 14
    $selection.Font.Color = $mepGold
    $selection.TypeText("___________________________________________")
    $selection.TypeParagraph()
    $selection.TypeParagraph()
    $selection.TypeParagraph()

    # Metadatos
    $selection.Font.Size = 12
    $selection.Font.Color = $mepDark
    $selection.Font.Bold = $false
    $selection.TypeText("Version: 1.0")
    $selection.TypeParagraph()
    $selection.TypeText("Fecha: Julio 2026")
    $selection.TypeParagraph()
    $selection.TypeText("Sistema: TecnoPresta - Modulo Administracion del Sistema")
    $selection.TypeParagraph()

    # ============================================================
    # SALTAR A PÁGINA NUEVA
    # ============================================================
    $selection.InsertBreak(7)  # wdPageBreak

    # ============================================================
    # TABLA DE CONTENIDO (manual)
    # ============================================================
    $selection.ParagraphFormat.Alignment = 0  # Left
    $selection.Font.Size = 16
    $selection.Font.Bold = $true
    $selection.Font.Color = $mepBlue
    $selection.TypeText("Tabla de Contenido")
    $selection.TypeParagraph()
    $selection.TypeParagraph()

    $selection.Font.Size = 11
    $selection.Font.Bold = $false
    $selection.Font.Color = $mepDark

    $toc = @(
        "1. Proposito",
        "2. Alcance",
        "3. Roles de Acceso",
        "4. Requerimientos Funcionales",
        "    4.1 Formulario: Gestion de Modulos",
        "    4.2 Formulario: Gestion de Formularios",
        "    4.3 Formulario: Asignacion de Permisos a Roles",
        "5. Reglas de Negocio",
        "6. Arquitectura Tecnica",
        "7. Script de Inicializacion",
        "8. Criterios de Aceptacion"
    )
    foreach ($item in $toc) {
        $selection.TypeText($item)
        $selection.TypeParagraph()
    }

    $selection.InsertBreak(7)

    # ============================================================
    # 1. PROPÓSITO
    # ============================================================
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
        $selection.Font.Name = "Calibri"
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

    Add-Heading "1. Proposito" 1
    Add-Body "Proveer una interfaz administrativa para que el usuario Root pueda gestionar la estructura del menu del sistema TecnoPresta sin necesidad de ejecutar scripts SQL manualmente. Permite crear, editar y desactivar subsistemas, modulos, formularios, y asignar permisos a roles."

    Add-Separator

    # ============================================================
    # 2. ALCANCE
    # ============================================================
    Add-Heading "2. Alcance" 1
    Add-Body "El sistema comprende tres formularios funcionales:"
    Add-Bullet "Gestion de Modulos - administracion de subsistemas y modulos"
    Add-Bullet "Gestion de Formularios - administracion de formularios y sus permisos"
    Add-Bullet "Asignacion de Permisos a Roles - gestion de que roles tienen acceso a que formularios con que acciones"

    Add-Separator

    # ============================================================
    # 3. ROLES DE ACCESO
    # ============================================================
    Add-Heading "3. Roles de Acceso" 1
    Add-Body "El acceso a los tres formularios del Gestor del Sistema esta restringido exclusivamente al usuario Root (rol_id=1). Ningun otro rol tiene permisos para acceder a estas funcionalidades."

    # Tabla de roles
    $table = $doc.Tables.Add($selection.Range, 3, 3)
    $table.Borders.InsideLineStyle = 1
    $table.Borders.OutsideLineStyle = 1

    # Header row
    $table.Cell(1,1).Range.Font.Bold = $true
    $table.Cell(1,1).Range.Font.Color = $mepWhite
    $table.Cell(1,1).Shading.BackgroundPatternColor = $mepBlue
    $table.Cell(1,1).Range.Text = "Rol"
    $table.Cell(1,2).Range.Font.Bold = $true
    $table.Cell(1,2).Range.Font.Color = $mepWhite
    $table.Cell(1,2).Shading.BackgroundPatternColor = $mepBlue
    $table.Cell(1,2).Range.Text = "ID"
    $table.Cell(1,3).Range.Font.Bold = $true
    $table.Cell(1,3).Range.Font.Color = $mepWhite
    $table.Cell(1,3).Shading.BackgroundPatternColor = $mepBlue
    $table.Cell(1,3).Range.Text = "Acceso"

    $table.Cell(2,1).Range.Text = "Root"
    $table.Cell(2,2).Range.Text = "1"
    $table.Cell(2,3).Range.Text = "Acceso total a los 3 formularios"
    $table.Cell(3,1).Range.Text = "Cualquier otro rol"
    $table.Cell(3,2).Range.Text = "-"
    $table.Cell(3,3).Range.Text = "Sin acceso"

    $selection.TypeParagraph()
    $selection.TypeParagraph()
    Add-Separator

    # ============================================================
    # 4. REQUERIMIENTOS FUNCIONALES
    # ============================================================
    Add-Heading "4. Requerimientos Funcionales" 1

    # 4.1
    Add-Heading "4.1 Formulario: Gestion de Modulos" 2
    Add-Heading "4.1.1 Visualizacion" 3
    Add-Body "El formulario muestra dos secciones en una sola pagina:"
    Add-Bullet "Seccion Subsistemas: tabla con columnas id, nombre, descripcion, orden, estado (badge Activo/Inactivo), acciones [Editar] [Desactivar/Reactivar]."
    Add-Bullet "Seccion Modulos: tabla con columnas id, nombre, descripcion, subsistema_id (JOIN a subsistemas.nombre), ruta_base, orden, icono, color (swatch), estado, acciones."
    Add-Body "Al hacer clic en un subsistema, la seccion de modulos se filtra para mostrar solo los modulos de ese subsistema. Cada fila tiene botones [Editar] y [Desactivar] (o [Reactivar] si esta inactivo)."

    Add-Heading "4.1.2 Operaciones CRUD" 3
    Add-Body "Crear subsistema: Modal con campos: nombre (obligatorio), descripcion, imagen (ruta SVG), orden (numerico, defecto 0). INSERT INTO subsistemas. Imagen se deja NULL si no se especifica."
    Add-Body "Editar subsistema: Mismo modal precargado con datos actuales. UPDATE subsistemas SET... WHERE id=?"
    Add-Body "Desactivar subsistema: Validacion previa: SELECT COUNT(*) FROM modulos WHERE subsistema_id=? AND eliminado=0. Si hay modulos activos, modal de error. Si no, modal de confirmacion y UPDATE subsistemas SET eliminado=1."
    Add-Body "Reactivar subsistema: Modal de confirmacion. UPDATE subsistemas SET eliminado=0."
    Add-Body "Crear modulo: Modal con campos: nombre (obligatorio), descripcion, subsistema (select), ruta_base, imagen, orden, color. INSERT INTO modulos."
    Add-Body "Editar modulo: Mismo modal precargado."
    Add-Body "Desactivar modulo: Validacion: SELECT COUNT(*) FROM formularios WHERE modulo_id=? AND eliminado=0. Si hay formularios activos, error. Si no, UPDATE modulos SET eliminado=1."
    Add-Body "Reactivar modulo: UPDATE modulos SET eliminado=0. No afecta formularios."

    Add-Heading "4.1.3 Diseno UI" 3
    Add-Bullet "Hero-box con icono bi-diagram-3, titulo 'Gestion de Subsistemas y Modulos'"
    Add-Bullet "Boton [+ Nuevo Subsistema] sobre la tabla de subsistemas"
    Add-Bullet "Boton [+ Nuevo Modulo] sobre la tabla de modulos (visible solo si hay subsistema seleccionado)"
    Add-Bullet "Tablas con clase .activos-table (estilo MEP: thead gradient, hover, responsive)"
    Add-Bullet "Badge de estado: - Activo (#28a745) / o Inactivo (#dc3545)"
    Add-Bullet "Boton guardar flotante .btn-guardar-flotante con tooltip (aparece solo tras ediciones)"
    Add-Bullet "Boton volver .btn-disponibilidad con icono bi-arrow-left"
    Add-Bullet "Modales Bootstrap 5 con imagen de exito/error"

    Add-Heading "4.2 Formulario: Gestion de Formularios" 2
    Add-Heading "4.2.1 Visualizacion" 3
    Add-Body "Filtros en cascada: select de subsistema y select de modulo. Tabla de formularios con columnas: id, nombre, descripcion, ruta (badge 'Pendiente' si NULL), imagen (preview icon o '-'), orden, color (swatch), estado, acciones. Columna adicional 'Permisos' con badges compactos de las acciones asignadas (colores por tipo)."

    Add-Heading "4.2.2 Operaciones CRUD" 3
    Add-Body "Crear formulario: Modal con campos: nombre (obligatorio), descripcion, modulo (select), ruta, imagen, orden, color. Seccion de checkboxes para acciones (11 acciones del sistema). Checkbox [Seleccionar todas] / [Limpiar]. Transaccion: INSERT formulario + INSERT permisos."
    Add-Body "Editar formulario: Modal precargado con datos + checkboxes de permisos actuales. Transaccion: UPDATE formulario + DELETE permisos viejos + INSERT nuevos."
    Add-Body "Desactivar formulario: Sin validacion previa. UPDATE formularios SET eliminado=1."
    Add-Body "Reactivar formulario: UPDATE formularios SET eliminado=0."

    Add-Heading "4.2.3 Diseno UI" 3
    Add-Bullet "Hero-box con icono bi-file-earmark-text, titulo 'Gestion de Formularios y Permisos'"
    Add-Bullet "Filtros en fila con selects Bootstrap"
    Add-Bullet "Tabla .activos-table"
    Add-Bullet "Badges de permisos color-coded por tipo de accion"
    Add-Bullet "Boton [+ Nuevo Formulario] sobre la tabla"
    Add-Bullet "Modal con secciones: 'Datos del Formulario' y 'Permisos'"

    Add-Heading "4.3 Formulario: Asignacion de Permisos a Roles" 2
    Add-Heading "4.3.1 Visualizacion" 3
    Add-Body "Selector de rol. Resumen informativo: 'X modulos - Y formularios - Z permisos'. Arbol colapsable de 3 niveles: subsistema, modulo, formulario. Campo de busqueda para filtrar formularios por nombre."

    Add-Heading "4.3.2 Estados del checkbox de formulario" 3
    Add-Body "Cada formulario muestra un checkbox con tres estados: [v] (azul) = todas las acciones asignadas, [-] (gris) = algunas acciones asignadas, [.] (borde) = ninguna accion asignada. Ademas, badges toggle individuales para cada accion."

    Add-Heading "4.3.3 Operacion: Asignar/Revocar permisos" 3
    Add-Body "Toggle de accion individual: Clic en badge cambia entre activo (azul relleno) e inactivo (gris outline)."
    Add-Body "Toggle de formulario completo: Clic en checkbox [v] asigna todas las acciones. Clic en [-] o [.] desasigna todas."
    Add-Body "Guardado diferencial: Se envia solo el diferencial de cambios. Para cada cambio: activo=true = INSERT IGNORE INTO permisos + INSERT IGNORE INTO roles_permisos. activo=false = DELETE FROM roles_permisos + limpieza de permisos huerfanos."

    Add-Heading "4.3.4 Diseno UI" 3
    Add-Bullet "Hero-box con icono bi-shield-lock, titulo 'Asignacion de Permisos a Roles'"
    Add-Bullet "Selector de rol con badge informativo"
    Add-Bullet "Arbol con acordeon Bootstrap (details/summary HTML)"
    Add-Bullet "Campo de busqueda con filtro en tiempo real (keyup)"
    Add-Bullet "Badges de accion toggle con transicion de color"
    Add-Bullet "Boton guardar flotante con contador: 'Guardar (N cambios pendientes)'"
    Add-Bullet "Al cambiar de rol con cambios sin guardar, modal de confirmacion"

    Add-Separator

    # ============================================================
    # 5. REGLAS DE NEGOCIO
    # ============================================================
    Add-Heading "5. Reglas de Negocio" 1

    Add-Heading "5.1 Desactivacion en cascada (protegida)" 2
    Add-Body "Formulario: se puede desactivar siempre, sin condiciones."
    Add-Body "Modulo: solo se puede desactivar si todos sus formularios estan inactivos (eliminado=1)."
    Add-Body "Subsistema: solo se puede desactivar si todos sus modulos estan inactivos."

    Add-Heading "5.2 Reactivacion" 2
    Add-Body "Siempre permitida, sin validaciones. Reactivar un modulo NO reactiva automaticamente sus formularios. Reactivar un subsistema NO reactiva automaticamente sus modulos."

    Add-Heading "5.3 Integridad referencial" 2
    Add-Body "No se realizan DELETE fisicos. Solo soft-delete (UPDATE eliminado=1). Al desasignar un permiso, se limpian las entradas en roles_permisos y si ningun otro rol usa ese permiso, se elimina de permisos."

    Add-Separator

    # ============================================================
    # 6. ARQUITECTURA TÉCNICA
    # ============================================================
    Add-Heading "6. Arquitectura Tecnica" 1

    # Stack table
    $table2 = $doc.Tables.Add($selection.Range, 5, 2)
    $table2.Borders.InsideLineStyle = 1
    $table2.Borders.OutsideLineStyle = 1

    $headers = @("Componente", "Tecnologia")
    for ($i = 0; $i -lt 2; $i++) {
        $table2.Cell(1,$i+1).Range.Font.Bold = $true
        $table2.Cell(1,$i+1).Range.Font.Color = $mepWhite
        $table2.Cell(1,$i+1).Shading.BackgroundPatternColor = $mepBlue
        $table2.Cell(1,$i+1).Range.Text = $headers[$i]
    }

    $stack = @(
        @("Frontend", "PHP, HTML5, CSS3, Bootstrap 5, JavaScript (vanilla + jQuery 3.7)"),
        @("Backend", "PHP 8+, PDO, MariaDB/MySQL"),
        @("CSS framework", "Bootstrap 5 + CSS personalizado (identidad MEP)"),
        @("JS framework", "jQuery 3.7.1 para AJAX y manipulacion DOM"),
        @("Comunicacion", "JSON via fetch/AJAX")
    )
    for ($i = 0; $i -lt $stack.Count; $i++) {
        $table2.Cell($i+2,1).Range.Text = $stack[$i][0]
        $table2.Cell($i+2,2).Range.Text = $stack[$i][1]
    }

    $selection.TypeParagraph()

    Add-Heading "6.1 Estructura de archivos" 2
    Add-Body "La implementacion comprende 9 archivos nuevos: 3 formularios, 3 endpoints de datos y 3 endpoints de accion."

    $selection.TypeParagraph()
    Add-Separator

    # ============================================================
    # 7. SCRIPT DE INICIALIZACIÓN
    # ============================================================
    Add-Heading "7. Script de Inicializacion (Base de Datos)" 1
    Add-Body "El script SQL crea el modulo 'Gestor del Sistema', los 3 formularios, los permisos correspondientes y los asigna al rol Root. Debe ejecutarse una vez contra la base de datos ltecnopre."

    $selection.TypeParagraph()
    Add-Separator

    # ============================================================
    # 8. CRITERIOS DE ACEPTACIÓN
    # ============================================================
    Add-Heading "8. Criterios de Aceptacion" 1

    $criterios = @(
        "Root puede ver los 3 formularios en el menu bajo Administracion del Sistema > Gestor del Sistema",
        "Root puede crear, editar, desactivar y reactivar subsistemas",
        "Root puede crear, editar, desactivar y reactivar modulos (solo si no tienen formularios activos)",
        "Root puede crear, editar, desactivar y reactivar formularios con asignacion de permisos",
        "Root puede asignar y revocar permisos a roles mediante checkboxes",
        "Al desactivar un modulo con formularios activos, se muestra error indicando cuantos tiene",
        "Al desactivar un subsistema con modulos activos, se muestra error",
        "Reactivar un modulo no reactiva sus formularios automaticamente",
        "Todos los cambios se confirman mediante modales Bootstrap 5",
        "El codigo PHP usa prepared statements en todas las consultas",
        "Cada formulario incluye hero-box, tabla MEP, botones flotantes, modales y footer",
        "Los menus existentes del sistema no se ven afectados por el nuevo modulo"
    )
    $i = 1
    foreach ($c in $criterios) {
        $selection.Font.Size = 11
        $selection.Font.Bold = $false
        $selection.Font.Color = $mepDark
        $selection.TypeText("$i. $c")
        $selection.TypeParagraph()
        $i++
    }

    $selection.TypeParagraph()
    $selection.TypeParagraph()

    # Footer documental
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
