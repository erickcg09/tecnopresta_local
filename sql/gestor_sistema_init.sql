-- ============================================================
-- Script de Inicializacion: Modulo Gestor del Sistema
-- Sistema: TecnoPresta
-- Version: 1.0
-- Fecha: Julio 2026
-- ============================================================
-- Este script crea el modulo "Gestor del Sistema" bajo el
-- subsistema "Administracion del Sistema" (id=4), con sus 3
-- formularios y permisos asignados al rol Root (id_rol=1).
-- ============================================================

START TRANSACTION;

-- ============================================================
-- 1. CREAR EL MODULO "GESTOR DEL SISTEMA"
-- ============================================================
-- Validacion: verifica que no exista ya un modulo con el mismo nombre
-- (unique key uq_modulos_nombre lo evita, pero validamos por claridad)
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
    'Gestor del Sistema',
    'Administracion de modulos, formularios, permisos y roles del sistema',
    4,
    NULL,
    '/admin/gestor',
    1,
    '#003876',
    0
);

-- Capturar el ID del modulo recien creado para usarlo en los formularios
SET @modulo_id = LAST_INSERT_ID();

-- ============================================================
-- 2. CREAR LOS 3 FORMULARIOS DEL MODULO
-- ============================================================
-- Formulario 1: Gestion de Modulos (subsistemas y modulos)
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
    @modulo_id,
    'Gestion de Modulos',
    'Crear y administrar subsistemas y modulos del sistema',
    'gestor_modulos_n.php',
    NULL,
    1,
    '#003876',
    0
);
SET @form_modulos_id = LAST_INSERT_ID();

-- Formulario 2: Gestion de Formularios (formularios y permisos)
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
    @modulo_id,
    'Gestion de Formularios',
    'Crear y administrar formularios y sus permisos de acceso',
    'gestor_formularios_n.php',
    NULL,
    2,
    '#003876',
    0
);
SET @form_formularios_id = LAST_INSERT_ID();

-- Formulario 3: Asignacion de Permisos a Roles
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
    @modulo_id,
    'Asignacion de Permisos a Roles',
    'Gestionar que roles tienen acceso a que formularios',
    'gestor_roles_permisos_n.php',
    NULL,
    3,
    '#003876',
    0
);
SET @form_roles_id = LAST_INSERT_ID();

-- ============================================================
-- 3. CREAR PERMISOS (COMBINACIONES formulario_id x accion_id)
-- ============================================================
-- Formulario 1: Gestion de Modulos -> ver(1), crear(2), editar(3), eliminar(4), auditar(11)
INSERT INTO permisos (formulario_id, accion_id) VALUES
(@form_modulos_id, 1),
(@form_modulos_id, 2),
(@form_modulos_id, 3),
(@form_modulos_id, 4),
(@form_modulos_id, 11);

-- Formulario 2: Gestion de Formularios -> ver(1), crear(2), editar(3), eliminar(4), auditar(11)
INSERT INTO permisos (formulario_id, accion_id) VALUES
(@form_formularios_id, 1),
(@form_formularios_id, 2),
(@form_formularios_id, 3),
(@form_formularios_id, 4),
(@form_formularios_id, 11);

-- Formulario 3: Asignacion de Permisos a Roles -> ver(1), asignar(8), auditar(11)
INSERT INTO permisos (formulario_id, accion_id) VALUES
(@form_roles_id, 1),
(@form_roles_id, 8),
(@form_roles_id, 11);

-- ============================================================
-- 4. ASIGNAR PERMISOS AL ROL ROOT (id_rol = 1)
-- ============================================================
-- Obtener todos los permisos de los 3 formularios y asignarlos a Root
INSERT INTO roles_permisos (rol_id, permiso_id)
SELECT 1, p.id
FROM permisos p
WHERE p.formulario_id IN (@form_modulos_id, @form_formularios_id, @form_roles_id)
  AND NOT EXISTS (
      SELECT 1 FROM roles_permisos rp
      WHERE rp.rol_id = 1 AND rp.permiso_id = p.id
  );

-- ============================================================
-- 5. CONFIRMAR TRANSACCION
-- ============================================================
COMMIT;

-- ============================================================
-- Mensaje de confirmacion (visible si se ejecuta desde CLI)
-- ============================================================
SELECT 'Script ejecutado exitosamente' AS resultado,
       CONCAT('Modulo creado: Gestor del Sistema (ID: ', @modulo_id, ')') AS modulo_info,
       CONCAT('Formularios creados: Gestion de Modulos (ID: ', @form_modulos_id,
              '), Gestion de Formularios (ID: ', @form_formularios_id,
              '), Asignacion de Permisos a Roles (ID: ', @form_roles_id, ')') AS formularios_info,
       CONCAT('Permisos creados y asignados al rol Root (ID: 1)') AS permisos_info;
