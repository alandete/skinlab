# SkinLab — Características y Cualidades del Proyecto

Entorno profesional de desarrollo de temas gráficos para Canvas LMS.

---

## Visión General

SkinLab es una plataforma web diseñada para crear, previsualizar y exportar temas CSS personalizados para Canvas LMS. Ofrece un dashboard que replica la experiencia visual de Canvas, permitiendo a diseñadores y desarrolladores ver en tiempo real cómo lucirán sus temas dentro del aula virtual.

---

## Arquitectura Técnica

### Stack Tecnológico

| Componente | Tecnología |
|------------|------------|
| Backend | PHP 8.3+ con tipado estricto (`declare(strict_types=1)`) |
| Base de datos | MySQL 8.0 con InnoDB, charset utf8mb4 |
| Frontend | HTML5 semántico, CSS3 con custom properties, JavaScript vanilla |
| Íconos | Bootstrap Icons 1.11 |
| Fuentes | Google Fonts (Lato) |
| Servidor local | Laragon (Apache + MySQL) en Windows |

### Patrón de Arquitectura

- **Front Controller**: todas las peticiones pasan por un único punto de entrada (`public/index.php`)
- **MVC simplificado**: Controllers, Models, Views con layouts
- **Autoloader PSR-4**: carga automática de clases por namespace
- **Routing declarativo**: rutas definidas en archivo de configuración con soporte para parámetros dinámicos y middleware

### Estructura del Proyecto

```
skinlab/
├── app/
│   ├── Core/           Clases fundamentales del framework
│   │   ├── App.php         Bootstrapping, configuración, sesión
│   │   ├── Router.php      Enrutador con parámetros y middleware
│   │   ├── Request.php     Abstracción del request HTTP
│   │   ├── Response.php    Respuestas JSON, redirects, errores
│   │   ├── Database.php    Conexión PDO MySQL (singleton)
│   │   ├── View.php        Motor de templates con layouts
│   │   └── Lang.php        Sistema de internacionalización
│   ├── Controllers/    Controladores web y API
│   ├── Middleware/      Capas de seguridad y validación
│   ├── Models/          Modelos de datos
│   └── Helpers/         Funciones globales de utilidad
├── config/             Configuración centralizada
├── database/           Schema SQL y migraciones
├── lang/               Archivos de idioma (ES + EN)
├── public/             Document root (assets + entry point)
├── storage/            Proyectos y logs (excluido de git)
├── templates/          Plantillas para nuevos proyectos
└── views/              Vistas organizadas por módulo
```

---

## Seguridad

### Protecciones Implementadas

| Protección | Implementación |
|------------|---------------|
| **CSRF** | Token automático en todas las peticiones POST. Validación vía campo hidden en formularios y header `X-CSRF-Token` en AJAX |
| **SQL Injection** | Prepared statements en todas las consultas a base de datos vía PDO |
| **XSS** | Función `e()` de escape HTML en todas las salidas. Content-Security-Policy restrictiva |
| **Rate Limiting** | Por dirección IP almacenado en base de datos. No por sesión, evitando bypass por creación de sesiones nuevas. Configurable por acción (login: 5/min, API: 60/min) |
| **Session Security** | Cookies HttpOnly + SameSite=Lax. Regeneración periódica del ID de sesión. Nombre de sesión personalizado |
| **HTTP Headers** | X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy, Content-Security-Policy |
| **Autenticación** | Contraseñas hasheadas con bcrypt (`password_hash` + `password_verify`). Validación de longitud mínima |
| **Autorización** | Sistema de roles jerárquico con middleware en rutas. Protecciones contra auto-desactivación y auto-eliminación |
| **Base de datos** | Usuario MySQL dedicado con permisos limitados (no usa root). Evento automático de limpieza de datos temporales |
| **Acceso a archivos** | Carpetas sensibles bloqueadas por `.htaccess` (app/, config/, database/, storage/, lang/, views/) |

### Roles y Permisos

| Rol | Permisos |
|-----|----------|
| **Administrador** | Gestión completa: usuarios, proyectos, compilación, exportación, configuración global |
| **Editor** | Crear y editar proyectos propios, compilar CSS, exportar |
| **Invitado** | Solo lectura: ver proyectos, ver código fuente, documentación |

La jerarquía es estricta: Admin > Editor > Guest. Un middleware verifica el nivel mínimo requerido en cada ruta.

---

## Internacionalización (i18n)

### Diseño del Sistema

- Todos los textos visibles en el frontend provienen de archivos de idioma
- Cero textos hardcodeados en vistas o controladores
- Archivos segmentados por módulo para facilitar mantenimiento
- Soporte de idioma de fallback (si falta una clave en el idioma actual, busca en el idioma base)
- Soporte de reemplazos dinámicos: `__('auth.welcome', ['name' => 'Juan'])` → `Bienvenido, :name`

### Idiomas Disponibles

| Idioma | Archivos |
|--------|----------|
| Español (es) | general, auth, nav, dashboard, admin |
| English (en) | general, auth, nav, dashboard, admin |

### Cobertura por Módulo

| Archivo | Contenido |
|---------|-----------|
| `general.php` | Nombre de la app, acciones comunes, mensajes de error, validaciones |
| `auth.php` | Login, setup, reset password, validaciones de formulario |
| `nav.php` | Navegación global Canvas, herramientas, breadcrumbs |
| `dashboard.php` | Títulos, placeholder, panel de estado, visor de código, vista móvil |
| `admin.php` | Gestión de proyectos, usuarios, roles, permisos, CDNs, compilación, exportación |

---

## Políticas de Diseño Frontend

### Mobile First

- El CSS base está escrito para dispositivos móviles
- Las pantallas más grandes se escalan con media queries `min-width`
- Se utilizan exclusivamente los breakpoints de Bootstrap 5:

| Breakpoint | Mínimo |
|------------|--------|
| sm | 576px |
| md | 768px |
| lg | 992px |
| xl | 1200px |
| xxl | 1400px |

### Medidas Responsivas

- Nunca se usan valores fijos en píxeles para dimensiones de layout
- Unidades permitidas: `%`, `rem`, `em`, `vw`, `vh`, `clamp()`, `min()`, `max()`
- Píxeles solo para bordes (1px), sombras y detalles decorativos mínimos
- Layouts construidos con Flexbox y CSS Grid para adaptación natural al ancho del dispositivo

### HTML Semántico

Se utilizan las etiquetas HTML5 apropiadas en lugar de `<div>` genéricos:

| Elemento | Uso |
|----------|-----|
| `<section>` | Bloques de contenido con título (cards, paneles, grids) |
| `<article>` | Unidades de contenido independientes (user cards, project cards) |
| `<header>` | Cabeceras de sección (logos, panel headers) |
| `<footer>` | Pies de sección (links, acciones secundarias) |
| `<nav>` | Bloques de navegación |
| `<main>` | Contenido principal de la página |
| `<fieldset>` + `<legend>` | Grupos de campos en formularios |
| `<dialog>` | Modales de confirmación |
| `<dl>`, `<dt>`, `<dd>` | Listas de definición (permisos, metadatos) |

### Accesibilidad (WCAG)

| Práctica | Implementación |
|----------|---------------|
| **ARIA labels** | `aria-label` en todos los botones de solo ícono, con contexto del elemento relacionado |
| **ARIA roles** | `role="alert"` en errores, `role="status"` en confirmaciones, `role="alertdialog"` en modales |
| **ARIA live** | `aria-live="polite"` en contenedores de mensajes dinámicos (AJAX) |
| **ARIA hidden** | `aria-hidden="true"` en todos los íconos decorativos |
| **Focus visible** | Outline de 2px en color primario para navegación por teclado (`:focus-visible`) |
| **Screen readers** | Clase `.sr-only` para labels invisibles que dan contexto a lectores de pantalla |
| **Campos requeridos** | `aria-required="true"` en inputs obligatorios |
| **Descripciones** | `aria-describedby` para vincular hints con sus campos |
| **Controles** | `aria-controls` para botones que muestran/ocultan secciones |
| **Checkbox** | `accent-color` para mantener coherencia visual con el tema |

### Variables CSS

Todas las propiedades de diseño se gestionan desde variables CSS centralizadas con prefijo `--sl-`:

```css
/* Colores principales */
--sl-primary, --sl-primary-hover, --sl-primary-dark
--sl-secondary, --sl-secondary-light

/* Fondos */
--sl-bg, --sl-bg-white, --sl-bg-dark

/* Texto */
--sl-text, --sl-text-light, --sl-text-muted

/* Bordes */
--sl-border, --sl-border-light

/* Estados */
--sl-success, --sl-danger, --sl-warning (+ variantes -bg)

/* Transiciones, radios, sombras, fuente */
--sl-transition, --sl-radius, --sl-shadow, --sl-font
```

Adicionalmente se declaran las variables de Canvas LMS (`--ic-brand-*`) para compatibilidad con los temas generados.

---

## Sistema de Autenticación

### Flujo de Primer Uso

1. El usuario accede al sitio por primera vez
2. El sistema detecta que no está instalado (`app_installed = 0`)
3. Redirige automáticamente a `/setup`
4. El formulario de setup permite:
   - Crear la cuenta de administrador (obligatorio)
   - Crear un usuario invitado de solo lectura (opcional)
   - Configurar un correo de recuperación (opcional)
5. Al completar, marca la app como instalada y redirige al login

### Flujo de Login

1. Formulario con protección CSRF automática
2. Rate limiting: máximo 5 intentos por minuto por IP
3. Verificación de credenciales con bcrypt
4. Verificación de cuenta activa
5. Regeneración del ID de sesión al autenticar
6. Redirección al dashboard

### Recuperación de Contraseña

1. Verificación del correo de recuperación configurado
2. Verificación del nombre de usuario
3. Establecimiento de nueva contraseña con confirmación
4. Protegido por rate limiting

### Gestión de Usuarios (Admin)

- Crear usuarios con rol Editor o Invitado
- Cambiar contraseña de cualquier usuario
- Activar/desactivar cuentas
- Eliminar usuarios (con protecciones)
- Actualizar correo de recuperación global

### Protecciones de Integridad

- Un admin no puede desactivar su propia cuenta
- Un admin no puede eliminar su propia cuenta
- No se puede eliminar el último administrador del sistema
- Los usuarios desactivados no pueden iniciar sesión

---

## API REST

Todas las operaciones AJAX utilizan endpoints API con las siguientes características:

- Content-Type: `application/json`
- Autenticación por sesión
- Token CSRF vía header `X-CSRF-Token`
- Respuestas JSON estandarizadas con `success` o `error`
- Códigos HTTP apropiados (200, 400, 401, 403, 404, 409, 429)

### Endpoints Disponibles

| Método | Ruta | Rol Mínimo | Descripción |
|--------|------|------------|-------------|
| POST | `/api/users/create` | Admin | Crear usuario |
| POST | `/api/users/password` | Admin | Cambiar contraseña |
| POST | `/api/users/toggle` | Admin | Activar/desactivar usuario |
| POST | `/api/users/delete` | Admin | Eliminar usuario |
| POST | `/api/settings/email` | Admin | Actualizar correo de recuperación |

*Endpoints de proyectos se incorporan en fases posteriores.*

---

## Dos Ambientes de Trabajo

### Ambiente de Desarrollo (SkinLab UI)

El entorno propio de SkinLab donde se gestionan los proyectos. Puede utilizar:
- Bootstrap 5 completo (grid, componentes, utilidades)
- JavaScript modular segmentado por funcionalidad
- Todas las herramientas y técnicas modernas de desarrollo web
- CSS Grid y Flexbox para layouts

### Ambiente de Temas Gráficos (Proyectos Canvas)

Los temas que se crean y previsualizan dentro de SkinLab deben respetar las limitaciones de Canvas LMS:
- Sin JavaScript personalizado
- CSS limitado a lo que Canvas permite inyectar
- Selectores compatibles con la estructura DOM de Canvas
- Variables CSS del sistema Canvas (`--ic-brand-*`)

---

## Funcionalidades por Fase

### Fase 1 — Estructura Base ✅

Framework propio con router, autoloader, base de datos, sistema de vistas con layouts, internacionalización, middleware de seguridad y configuración centralizada.

### Fase 2 — Autenticación ✅

Sistema completo de usuarios con 3 roles, login con rate limiting, setup inicial, reset de contraseña, gestión de usuarios vía API REST, y todas las protecciones de seguridad.

### Fase 3 — Dashboard Canvas LMS *(En desarrollo)*

Interfaz de 4 columnas que replica el aula virtual de Canvas: navegación global, navegación del curso, área de contenido principal y sidebar de estado.

### Fase 4 — Gestión de Proyectos *(Pendiente)*

CRUD de proyectos con metadata en base de datos, selección de colores y CDNs, organización por semanas/módulos/unidades, compilación CSS y templates.

### Fase 5 — Visualización y Herramientas *(Pendiente)*

Carga dinámica de páginas, visor de código fuente, simulador móvil, modo oscuro, exportación ZIP y atajos de teclado.

### Fase 6 — Documentación y Pulido *(Pendiente)*

Documentación integrada, tema oscuro completo, optimización de rendimiento, revisión de accesibilidad y testing de seguridad.
