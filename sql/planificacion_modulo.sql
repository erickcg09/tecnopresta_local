-- ============================================================
-- Script de Inicializacion: Modulo Planificacion
-- Subsistema: Gestion de Servicios (id=2)
-- Sistema: TecnoPresta
-- Fecha: Julio 2026
-- ============================================================
-- Este script crea el modulo "Planificacion" bajo el
-- subsistema "Gestion de Servicios" (id=2), con sus 2
-- formularios (badges) y permisos asignados a 5 roles:
--   Root(1), Mesa de servicios(8), Soporte virtual(9),
--   Coordinador en sitio(10), Soporte en sitio(11).
--
-- INSTRUCCIONES:
--   Ejecutar desde phpMyAdmin o consola MySQL:
--   SOURCE sql/planificacion_modulo.sql;
-- ============================================================

START TRANSACTION;

-- ============================================================
-- 1. CREAR EL MODULO "PLANIFICACION"
-- ============================================================
-- Descripcion: Gestion de planificacion de giras y disponibilidad
-- de soportistas en sitio para atencion presencial en centros
-- educativos.
-- ============================================================
INSERT INTO modulos (
    nombre,
    descripcion,
    subsistema_id,
    imagen,
    ruta_base,
    orden,
    color,
    eliminado
) VALUES (
    'Planificacion',
    'Gestion de planificacion de giras y disponibilidad de soportistas en sitio',
    2,
    'assets/img/modulos/planificacion.svg',
    '/servicios/planificacion',
    1,
    '#003876',
    0
);

SET @modulo_planificacion_id = LAST_INSERT_ID();

-- ============================================================
-- 2. CREAR LOS 2 FORMULARIOS (BADGES)
-- ============================================================

-- Formulario 1: Disponibilidad de Dias
-- Muestra los dias disponibles y no disponibles de cada
-- funcionario (soportista) para planificar giras en sitio.
INSERT INTO formularios (
    modulo_id,
    nombre,
    descripcion,
    ruta,
    imagen,
    orden,
    color,
    eliminado
) VALUES (
    @modulo_planificacion_id,
    'Disponibilidad de Dias',
    'Consulta de dias disponibles y no disponibles de cada soportista para planificar giras en sitio',
    'formulario_disponibilidad_dias.php',
    NULL,
    1,
    '#003876',
    0
);

SET @form_disponibilidad_id = LAST_INSERT_ID();

-- Formulario 2: Estimacion de Giras en Sitio
-- Registro y planificacion de giras con datos del funcionario,
-- nombre de la gira, fecha/hora de salida y regreso, punto de
-- movilidad, destino principal y costo de hospedaje por noche.
INSERT INTO formularios (
    modulo_id,
    nombre,
    descripcion,
    ruta,
    imagen,
    orden,
    color,
    eliminado
) VALUES (
    @modulo_planificacion_id,
    'Estimacion de Giras en Sitio',
    'Registro de giras: funcionario, destino, fechas de salida y regreso, punto de movilidad, destino principal y costo de hospedaje por noche',
    'formulario_estimacion_giras.php',
    NULL,
    2,
    '#003876',
    0
);

SET @form_estimacion_id = LAST_INSERT_ID();

-- ============================================================
-- 3. CREAR PERMISOS (COMBINACIONES formulario_id x accion_id)
-- ============================================================

-- Formulario 1: Disponibilidad de Dias
--   ver(1), crear(2), editar(3), eliminar(4), auditar(11)
INSERT INTO permisos (formulario_id, accion_id) VALUES
(@form_disponibilidad_id, 1),
(@form_disponibilidad_id, 2),
(@form_disponibilidad_id, 3),
(@form_disponibilidad_id, 4),
(@form_disponibilidad_id, 11);

-- Formulario 2: Estimacion de Giras en Sitio
--   ver(1), crear(2), editar(3), eliminar(4), exportar(5), auditar(11)
INSERT INTO permisos (formulario_id, accion_id) VALUES
(@form_estimacion_id, 1),
(@form_estimacion_id, 2),
(@form_estimacion_id, 3),
(@form_estimacion_id, 4),
(@form_estimacion_id, 5),
(@form_estimacion_id, 11);

-- ============================================================
-- 4. ASIGNAR PERMISOS A LOS 5 ROLES CON ACCESO
-- ============================================================
-- Roles: Root(1), Mesa de servicios(8), Soporte virtual(9),
--        Coordinador en sitio(10), Soporte en sitio(11)
-- ============================================================

INSERT INTO roles_permisos (rol_id, permiso_id)
SELECT r.rol_id, p.id
FROM permisos p
CROSS JOIN (
    SELECT 1 AS rol_id UNION ALL
    SELECT 8 UNION ALL
    SELECT 9 UNION ALL
    SELECT 10 UNION ALL
    SELECT 11
) r
WHERE p.formulario_id IN (@form_disponibilidad_id, @form_estimacion_id)
  AND NOT EXISTS (
      SELECT 1 FROM roles_permisos rp
      WHERE rp.rol_id = r.rol_id AND rp.permiso_id = p.id
  );

-- ============================================================
-- 5. CONFIRMAR TRANSACCION
-- ============================================================
COMMIT;

-- ============================================================
-- Mensaje de confirmacion (visible si se ejecuta desde CLI)
-- ============================================================
SELECT 'Script ejecutado exitosamente' AS resultado,
       CONCAT('Modulo creado: Planificacion (ID: ', @modulo_planificacion_id, ')') AS modulo_info,
       CONCAT('Formularios: Disponibilidad de Dias (ID: ', @form_disponibilidad_id,
              '), Estimacion de Giras en Sitio (ID: ', @form_estimacion_id, ')') AS formularios_info,
       CONCAT('Permisos asignados a 5 roles: Root(1), Mesa de servicios(8), ',
              'Soporte virtual(9), Coordinador en sitio(10), Soporte en sitio(11)') AS permisos_info;
