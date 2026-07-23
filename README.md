<p align="center">
  <img src="icon.png" alt="TecnoPresta Logo" width="120"/>
</p>

<h1 align="center">TecnoPresta</h1>

<p align="center">
  <strong>Sistema de Préstamo e Inventario de Equipos Tecnológicos</strong><br>
  Ministerio de Educación Pública — Costa Rica
</p>

<p align="center">
  <a href="https://tecnopresta.mep.go.cr/">Producción</a> •
  <a href="#características">Características</a> •
  <a href="#instalación">Instalación</a> •
  <a href="#configuración">Configuración</a> •
  <a href="#tecnologías">Tecnologías</a>
</p>

---

## Descripción

**TecnoPresta** es una plataforma web diseñada para la gestión integral del préstamo, inventario y soporte técnico de equipos tecnológicos del Ministerio de Educación Pública (MEP) de Costa Rica. Originalmente concebida como parte del **Programa Nacional de Tecnologías Móviles (PNTM)**, ha evolucionado hasta convertirse en el sistema central para:

- Controlar el inventario nacional de equipos (laptops, impresoras, proyectores, parlantes, tabletas)
- Gestionar solicitudes de préstamo, aprobaciones, préstamos y devoluciones
- Operar un sistema de soporte técnico con tickets administrativos y técnicos
- Programar citas integradas con Microsoft Teams
- Generar reportes e informes para toma de decisiones a nivel institucional y nacional

**Producción:** [https://tecnopresta.mep.go.cr/](https://tecnopresta.mep.go.cr/)

---

## Capturas

<!-- Agregar capturas de pantalla aquí -->

---

## Características

### Inventario
- Registro de activos con placa, serial, alias, marca, modelo, color y ubicación
- Importación por lotes de equipos
- Consulta y búsqueda avanzada de activos
- Estados del equipo (Bueno / Regular / Malo)
- Control por fuente de financiamiento (FONATEL, Pronie, donaciones)

### Préstamos y Devoluciones
- Flujo completo: Solicitud → Aprobación → Préstamo → Devolución → Reporte
- Carrito de compras para solicitudes con múltiples equipos
- Control de disponibilidad en tiempo real
- Boletas de servicio asociadas al préstamo
- Registro de irregularidades en devoluciones

### Soporte Técnico
- Tickets administrativos y técnicos
- Dashboard de seguimiento
- Escalación a servicio técnico
- Cierre y valoración de casos

### Citas con Microsoft Teams
- Solicitudes de cita integradas con el calendario de Outlook
- Creación automática de reuniones en línea vía Microsoft Graph API
- Panel de administración de citas

### Reportes e Informes
- Inventario por fuente de financiamiento
- Activos donados y hurtados
- Vencimiento de licencias de software
- Exportación a Excel y PDF
- Gráficos y dashboards interactivos

### Software y Licencias
- Catálogo de software educativo
- Asignación de licencias a equipos
- Control de vencimiento

### Portal de Beneficiarios
- Consulta de inventario por usuario
- Formulario de contacto
- Calendario y planificador

### PWA (Progressive Web App)
- Instalable desde el navegador
- Funcionamiento offline con página de respaldo
- Notificaciones push

---

## Arquitectura del Sistema

TecnoPresta utiliza una arquitectura modular y dinámica basada en base de datos:

```
Subsistemas → Módulos → Formularios → Acciones (Permisos)
```

- **Subsistemas:** Categorías de alto nivel (Inventario, Préstamo, Soporte, etc.)
- **Módulos:** Agrupaciones funcionales dentro de cada subsistema
- **Formularios:** Páginas o interfaces del sistema
- **Acciones:** Permisos granulares (ver, crear, editar, eliminar, exportar, asignar, auditar)

Los menús, módulos y permisos son **totalmente configurables desde la interfaz de administración** sin necesidad de modificar código.

### Roles del Sistema

| ID | Rol | Descripción |
|----|-----|-------------|
| 1 | Root | Acceso total al sistema |
| 2 | Administrador | Gestión administrativa completa |
| 3 | Prestador | Gestión de préstamos de equipos |
| 4 | Inventariador | Gestión del inventario |
| 5 | Solicitante | Solicita equipos (rol por defecto) |
| 7 | Consultor | Solo consulta (solo lectura) |
| 8 | Mesa de Servicios | Atención en mesa de servicio |
| 9 | Soporte Virtual | Soporte técnico remoto |
| 10 | Coordinador en Sitio | Coordinación presencial |
| 11 | Soporte en Sitio | Soporte técnico presencial |

---

## Tecnologías

### Backend
- **PHP 8.1**
- **MySQL / MariaDB 10.1+**
- **Composer** para gestión de dependencias PHP

### Frontend
- **Bootstrap 5.3** — Framework CSS
- **Alpine.js 3.x** — Interactividad reactiva
- **jQuery 3.7** — Manipulación DOM (páginas heredadas)
- **Chart.js / ECharts** — Gráficos y dashboards
- **SweetAlert2** — Diálogos modales
- **FullCalendar** — Vista de calendario
- **Gijgo** — Componentes de fecha
- **Tom Select** — Selectores mejorados
- **Dropzone.js** — Subida de archivos
- **MSAL.js 2.21** — Autenticación Microsoft Azure AD

### Dependencias PHP (Composer)

| Paquete | Propósito |
|---------|-----------|
| `league/oauth2-client` | Autenticación OAuth2 (Azure AD) |
| `microsoft/microsoft-graph` | Integración con Microsoft Graph API |
| `phpoffice/phpspreadsheet` | Exportación a Excel |
| `minishlink/web-push` | Notificaciones push (VAPID) |
| `mpdf/mpdf` | Generación de PDF |

### Librerías Adicionales
- **PHPMailer** — Envío de correos vía SMTP (Office 365)
- **dompdf** — Generación de PDF alternativa

---

## Integraciones Externas

### Microsoft Azure AD (Autenticación SSO)
- Inicio de sesión único mediante MSAL.js
- Tenant: `mep.go.cr`
- Permisos: OpenID, Calendario, OnlineMeetings

### Microsoft Graph API
- Perfil de usuario y foto
- Creación de eventos en calendario de Outlook
- Creación de reuniones en línea de Microsoft Teams

### Correo Electrónico (PHPMailer)
- Servidor SMTP: `smtp.office365.com:587` (TLS)
- Notificaciones de solicitud, préstamo, devolución, rechazo y boletas

### Telegram Bot
- Bot: `@tecnopresta_bot`
- Comandos: `/start`, `/ayuda`

### Web Push (VAPID)
- Notificaciones push del navegador
- Service Worker con soporte offline

### Servicio SOAP del MEP
- URL: `https://apps.mep.go.cr/wstecnopresta/servicio.asmx`
- Validación de credenciales y consulta de funcionarios

---

## Requisitos del Sistema

| Componente | Versión mínima |
|------------|----------------|
| PHP | 8.1 |
| MySQL / MariaDB | 10.1 |
| Apache | 2.4+ |
| Composer | 2.x |

### Extensiones PHP requeridas
`PDO`, `mysqli`, `SOAP`, `mbstring`, `openssl`, `curl`, `json`, `mbstring`

### Configuración del servidor
```
memory_limit = 128M
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 30
max_input_time = 300
```

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/erickcg09/tecnopresta_local.git
cd tecnopresta_local
```

### 2. Configurar la base de datos

Crear la base de datos `ltecnopre` en MySQL/MariaDB y ejecutar el esquema inicial (disponible en `sql/gestor_sistema_init.sql` o en el dump `pntm.sql`).

```bash
mysql -u root -p ltecnopre < pntm.sql
```

### 3. Instalar dependencias PHP

```bash
composer install
```

### 4. Configurar el servidor web

Si usas **XAMPP**:
- Mover la carpeta del proyecto a `C:\xampp\htdocs\`
- Iniciar Apache y MySQL desde el panel de control
- Acceder a `http://localhost/tecnopresta-yo/`

Si usas **cPanel (producción)**:
- Subir los archivos al directorio del dominio
- Configurar el document root apuntando a la carpeta del proyecto
- Habilitar PHP 8.1 desde MultiPHP Manager

### 5. Configurar las credenciales

Ver la sección de [Configuración](#configuración) a continuación.

---

## Configuración

### Base de datos

**Archivo:** `config.php`

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ltecnopre');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### Azure AD (Autenticación)

**Archivo:** `authConfig.js`

```javascript
const msalConfig = {
    auth: {
        clientId: "TU_CLIENT_ID_DE_AZURE",
        authority: "https://login.microsoftonline.com/TU_TENANT_ID",
        redirectUri: "https://tecnopresta.mep.go.cr/"
    }
};
```

**Requisitos en Azure Portal:**
1. Crear un registro de aplicación en Azure AD
2. Configurar URI de redirección
3. Habilitar tokens de ID y de acceso
4. Asignar permisos de API: `Calendars.ReadWrite`, `OnlineMeetings.ReadWrite`

### Microsoft Graph

**Archivo:** `graphConfig.js`

```javascript
const graphConfig = {
    graphMeEndpoint: "https://graph.microsoft.com/v1.0/me",
    graphPhotoEndpoint: "https://graph.microsoft.com/v1.0/me/photo/$value"
};
```

### Correo Electrónico (SMTP)

**Archivo:** `sql/conexion.php`

```php
define('SMTP_HOST', 'smtp.office365.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tecnopresta@mep.go.cr');
define('SMTP_PASS', 'tu_contraseña_smtp');
```

### Telegram Bot

**Archivo:** `enviar_telegram.php`

```php
$bot_token = "TU_TOKEN_DE_TELEGRAM";
$chat_id = "TU_CHAT_ID";
```

### Web Push (VAPID)

Generar las llaves VAPID:

```bash
npx web-push generate-vapid-keys
```

Configurar en el archivo correspondiente y en `service-worker.js`.

---

## Estructura del Proyecto

```
tecnopresta-yo/
├── ajax/                    # Endpoints AJAX para operaciones dinámicas
├── assets/                  # Archivos CSS y recursos estáticos
├── avatar/                  # Fotos de perfil de usuario
├── bootstrap/               # Bootstrap 5 (archivos locales)
├── bootstrap5/              # Bootstrap 5 (bundle)
├── ccss/                    # Hojas de cálculo de estilos (CCSS)
├── css/                     # Estilos personalizados
├── dompdf/                  # Librería dompdf para generación de PDF
├── fpdf/                    # Librería FPDF
├── fonts/                   # Fuentes tipográficas
├── global/                  # Funciones globales y utilidades
├── ico/                     # Iconos e imágenes
├── images/                  # Imágenes del sistema
├── img/                     # Imágenes adicionales
├── js/                      # Scripts JavaScript
├── js2/                     # Scripts JavaScript (versión 2)
├── login/                   # Módulo de autenticación
├── menu/                    # Componentes del menú
├── partials/                # Componentes parciales reutilizables
├── PHPMailer/               # Librería PHPMailer
├── popup/                   # Ventanas emergentes
├── scripts/                 # Scripts auxiliares
├── select2/                 # Componente Select2
├── sql/                     # Consultas SQL y conexión a BD
├── subidos/                 # Archivos subidos por usuarios
├── svg/                     # Iconos SVG
├── sweetalert2/             # Librería SweetAlert2
├── ttf/                     # Fuentes TrueType
├── webfonts/                # Fuentes web (Font Awesome)
├── composer.json            # Dependencias PHP
├── config.php               # Configuración de base de datos
├── index.html               # Página de inicio / login
├── manifest.json            # Manifest PWA
├── service-worker.js        # Service Worker para PWA
├── navegar.php              # Dispatcher central de rutas
├── auth.php                 # Sistema de autenticación y autorización
└── formulario_menu_principal.php  # Menú principal dinámico
```

---

## Desarrollo

### Convenciones de nomenclatura

- Los archivos terminados en **`_n`** son las versiones nuevas/actualizadas (ej: `plataforma_soporte_n.php`)
- Los archivos sin `_n` son versiones anteriores que se mantienen por compatibilidad
- Archivos con **`copia`** en el nombre son respaldos (ignorados por `.gitignore`)

### Autenticación en desarrollo local

El sistema detecta automáticamente si se está ejecutando en `localhost` y desactiva la autenticación de Azure AD, utilizando un usuario de prueba predeterminado.

### Base de datos

- **Producción:** `ltecnopre`
- **Desarrollo local:** `ltecnopre` (XAMPP, usuario root sin contraseña)

---

## Producción

| Propiedad | Valor |
|-----------|-------|
| **URL** | [https://tecnopresta.mep.go.cr/](https://tecnopresta.mep.go.cr/) |
| **Servidor** | cPanel (Linux, Apache) |
| **PHP** | 8.1 |
| **Base de datos** | MySQL/MariaDB |
| **Dominio** | tecnopresta.mep.go.cr |
| **SSL** | Habilitado (HTTPS) |

---

## Licencia

Proyecto desarrollado para el **Ministerio de Educación Pública de Costa Rica**. Todos los derechos reservados.

---

<p align="center">
  <sub>TecnoPresta v1.1 — MEP Costa Rica</sub>
</p>
