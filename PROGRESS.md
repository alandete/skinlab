# SkinLab — Progreso del Proyecto

Recreación limpia y modular de CanvasThemes para desarrollo de temas Canvas LMS.

---

## FASE 1: Estructura Base y Configuración ✅
> Completada: 2026-03-28

### Tareas completadas

| # | Tarea | Fecha | Detalles |
|---|-------|-------|----------|
| 1.1 | Análisis de CanvasThemes | 2026-03-28 | Revisión completa del proyecto original: estructura, funcionalidades, errores de seguridad y arquitectura |
| 1.2 | Plan de desarrollo por fases | 2026-03-28 | Definición de 6 fases incrementales con alcance detallado |
| 1.3 | Estructura de carpetas | 2026-03-28 | app/, config/, database/, lang/, public/, storage/, templates/, views/ |
| 1.4 | Front controller y autoloader | 2026-03-28 | Entry point único (public/index.php), autoloader PSR-4, .htaccess con URL limpias |
| 1.5 | Core del framework | 2026-03-28 | App, Router (con parámetros dinámicos), Request, Response, Database (MySQL PDO), View (con layouts), Lang (i18n con fallback) |
| 1.6 | Sistema de middleware | 2026-03-28 | SecurityMiddleware (CSP, headers), CsrfMiddleware (auto en POST), AuthMiddleware (sesión + roles), RoleMiddleware (jerarquía admin>editor>guest), RateLimitMiddleware (por IP en BD) |
| 1.7 | Helpers globales | 2026-03-28 | __(), e(), csrf_field(), csrf_token(), url(), asset(), flash(), auth_check(), is_admin(), has_role(), to_slug(), is_hex_color(), is_valid_slug() |
| 1.8 | Base de datos MySQL | 2026-03-28 | Schema con 4 tablas (users, projects, settings, rate_limits). Usuario dedicado skinlab_user. Evento automático de limpieza de rate_limits |
| 1.9 | Sistema i18n | 2026-03-28 | 10 archivos de idioma (5 ES + 5 EN): general, auth, nav, dashboard, admin. Todos los textos del frontend cubiertos |
| 1.10 | Configuración | 2026-03-28 | app.php (roles, CDNs, rate limits, session), database.php (MySQL), routes.php (definición por fases) |
| 1.11 | Layouts y vistas base | 2026-03-28 | 3 layouts (app, auth, error), vista home, páginas de error (403, 404, 500) |
| 1.12 | CSS base con variables | 2026-03-28 | Variables CSS (--sl-*), reset, formularios, botones, alertas, utilidades, breakpoints Mobile First |
| 1.13 | JS base | 2026-03-28 | api() wrapper con CSRF automático, copyToClipboard(), escapeHtml() |
| 1.14 | Seguridad base | 2026-03-28 | CSRF automático en POST, CSP headers, sesión HttpOnly+SameSite, rate limiting por IP, prepared statements, usuario MySQL dedicado |

---

## FASE 2: Autenticación y Control de Usuarios ✅
> Completada: 2026-03-28

### Tareas completadas

| # | Tarea | Fecha | Detalles |
|---|-------|-------|----------|
| 2.1 | Modelo User | 2026-03-28 | CRUD completo (find, findByUsername, create, updatePassword, toggleActive, delete, count, countByRole, usernameExists). Helpers para settings (getSetting, setSetting) |
| 2.2 | AuthController | 2026-03-28 | showLogin, login (con rate limit), logout, showSetup (con validaciones), setup (transacción), showResetPassword, resetPassword (verificación email + username) |
| 2.3 | Vista Setup | 2026-03-28 | Formulario: admin (usuario+contraseña+confirmar), invitado (checkbox), email recuperación. Sin CSS inline. Todos los textos i18n |
| 2.4 | Vista Login | 2026-03-28 | Formulario con CSRF, flash messages (error/success), link a reset-password. Rate limiting: 5 intentos/minuto |
| 2.5 | Vista Reset Password | 2026-03-28 | Verificación por email + username + nueva contraseña + confirmar. Rate limited |
| 2.6 | AdminController + Vista usuarios | 2026-03-28 | Listado con cards por usuario (avatar, rol, badge, permisos). Cambio de contraseña inline. Toggle activo. Eliminar con modal. Crear usuario (editor/guest). Correo recuperación. Layout admin con tabs |
| 2.7 | API de usuarios | 2026-03-28 | POST /api/users/create, /api/users/password, /api/users/toggle, /api/users/delete, /api/settings/email. Protecciones: no self-deactivate, no self-delete, no delete last admin |
| 2.8 | Rutas activadas | 2026-03-28 | 12 rutas activas (7 auth + 2 admin + 5 API). Middleware auth + role:admin en admin/API |
| 2.9 | Tests de seguridad | 2026-03-28 | CSRF validado en todos los POST. Rate limit 429 al 6to intento. Guest bloqueado de admin (403). Self-protection. Prepared statements en todas las queries |

### Archivos creados/modificados

- `app/Models/User.php` — Modelo de usuario con helpers de settings
- `app/Controllers/AuthController.php` — Login, logout, setup, reset password
- `app/Controllers/DashboardController.php` — Placeholder hasta Fase 3
- `app/Controllers/AdminController.php` — Gestión de usuarios
- `app/Controllers/Api/UserApiController.php` — API REST de usuarios
- `views/auth/login.php`, `setup.php`, `reset-password.php` — Vistas de auth
- `views/dashboard/index.php` — Placeholder dashboard
- `views/layouts/admin.php` — Layout admin con tabs
- `views/admin/users.php` — Panel de gestión de usuarios
- `public/assets/css/admin.css` — Estilos del panel admin
- `public/assets/js/admin.js` — JS para CRUD de usuarios vía API
- `app/Core/View.php` — Fix: propagación de variables vista→layout
- `app/Core/Database.php` — Fix: isSchemaReady() carga config primero
- `app/Middleware/CsrfMiddleware.php` — Fix: evitar redirect loop en falla CSRF

---

## FASE 3: Dashboard Canvas LMS ✅
> Completada: 2026-03-29

### Tareas completadas

| # | Tarea | Fecha | Detalles |
|---|-------|-------|----------|
| 3.1 | Toolbar SkinLab (barra superior) | 2026-03-29 | Selector de proyecto, herramientas (reload, compile, mobile, code, export), admin link, logout, usuario con badge de rol. Herramientas extendidas ocultas en mobile |
| 3.2 | Nav Canvas global (Col 1) | 2026-03-29 | Réplica fiel de Canvas LMS: Cuenta, Tablero, Cursos, Calendario, Bandeja. Separada de herramientas de SkinLab. Colapsable. Oculta en mobile |
| 3.3 | Nav páginas del proyecto (Col 2) | 2026-03-29 | Lista dinámica de páginas del proyecto seleccionado. Toggle con overlay en mobile. Header con nombre del proyecto |
| 3.4 | Contenido principal (Col 3) | 2026-03-29 | Breadcrumbs dinámicos, body con carga de HTML/CSS/JS del proyecto. Placeholder de bienvenida |
| 3.5 | Sidebar estado del curso (Col 4) | 2026-03-29 | Calificación, tareas pendientes con fechas, próximos eventos. Visible solo desde xl (1200px) y solo en vista home |
| 3.6 | Dashboard JS | 2026-03-29 | Carga de proyectos vía API, selección por dropdown, renderizado de páginas, toggle navs, reload, compile, export, URL state, atajos de teclado (Ctrl+R, Esc) |
| 3.7 | CSS dashboard Mobile First | 2026-03-29 | Variables dedicadas (--sl-toolbar-*, --sl-nav-*, --sl-canvas-*), todas las medidas en rem, breakpoints Bootstrap. Mobile: toolbar + contenido. md: + navs laterales. xl: + sidebar |
| 3.8 | Layout dashboard dedicado | 2026-03-29 | Layout separado (layouts/dashboard.php) con CSS y JS específicos del dashboard |
| 3.9 | Ruta /project/{slug} | 2026-03-29 | URL limpia para proyectos con hash para páginas. DashboardController con index() y project() |
| 3.10 | Rediseño: separación nav/tools | 2026-03-29 | Corrección del error de CanvasThemes: herramientas de desarrollo en toolbar superior, nav Canvas como preview fiel, selector de proyecto en toolbar |

### Archivos creados

- `views/layouts/dashboard.php` — Layout dedicado del dashboard
- `views/dashboard/index.php` — Vista completa con toolbar + 4 columnas
- `public/assets/css/dashboard.css` — CSS Mobile First del dashboard
- `public/assets/js/dashboard.js` — JS: proyectos, navegación, estado, atajos
- `public/assets/img/canvas-logo.svg` — Logo de Canvas para nav

---

## FASE 4: Gestión de Proyectos ⬜
> Pendiente

| # | Tarea | Estado |
|---|-------|--------|
| 4.1 | Modelo Project (CRUD con metadata en BD) | ⬜ |
| 4.2 | API REST proyectos (listar, crear, editar, eliminar, toggle) | ⬜ |
| 4.3 | Templates para nuevos proyectos (HTML, CSS, JS) | ⬜ |
| 4.4 | Sistema de compilación CSS (master → mobile + desktop) | ⬜ |
| 4.5 | Panel admin de proyectos (formularios crear/editar, lista con acciones) | ⬜ |
| 4.6 | Selección de colores con paleta auto-generada | ⬜ |
| 4.7 | Organización por semanas/módulos/unidades | ⬜ |
| 4.8 | Selección de CDNs | ⬜ |
| 4.9 | Permisos por rol (admin todo, editor proyectos propios, guest lectura) | ⬜ |

---

## FASE 5: Visualización y Herramientas ⬜
> Pendiente

| # | Tarea | Estado |
|---|-------|--------|
| 5.1 | Carga dinámica de páginas de proyecto | ⬜ |
| 5.2 | Visor de código fuente (HTML, CSS master/mobile/desktop, JS) | ⬜ |
| 5.3 | Simulador móvil (phone/tablet, portrait/landscape) | ⬜ |
| 5.4 | Modo oscuro para preview | ⬜ |
| 5.5 | Exportación a ZIP | ⬜ |
| 5.6 | Atajos de teclado (Ctrl+E, Ctrl+R, Escape) | ⬜ |
| 5.7 | Sincronización de estado por URL limpia | ⬜ |
| 5.8 | API de contenido y código fuente | ⬜ |

---

## FASE 6: Documentación y Pulido ⬜
> Pendiente

| # | Tarea | Estado |
|---|-------|--------|
| 6.1 | Sección de documentación integrada | ⬜ |
| 6.2 | Tema oscuro completo para dashboard | ⬜ |
| 6.3 | Optimización de rendimiento (lazy loading, cache) | ⬜ |
| 6.4 | Revisión de accesibilidad (ARIA, contraste) | ⬜ |
| 6.5 | Testing de seguridad (XSS, CSRF, path traversal, injection) | ⬜ |

---

## Notas Técnicas

- **Stack:** PHP 8.3, MySQL 8.0, Bootstrap 5, Bootstrap Icons, Lato font
- **BD:** skinlab (usuario: skinlab_user)
- **URL:** https://skinlab.test (Laragon con auto SSL)
- **Entorno:** Laragon en Windows 11
