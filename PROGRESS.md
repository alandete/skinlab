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

## Rediseño Módulo de Usuarios ✅
> Completado: 2026-03-29

### Tareas completadas

| # | Tarea | Fecha | Detalles |
|---|-------|-------|----------|
| U.1 | Col 2 con ID Canvas | 2026-03-29 | ID cambiado a `#left-side`, toggle con `display:block/none` inline como Canvas LMS. CSS adapta mobile (overlay) y desktop (inline) |
| U.2 | Rate limiting en API | 2026-03-29 | `RateLimitMiddleware::check` aplicado en create, update, changePassword, toggle, delete |
| U.3 | Máximo contraseña 72 chars | 2026-03-29 | Validación en `User::validatePassword()`, traducción en ambos idiomas |
| U.4 | Verificación usuario activo | 2026-03-29 | `AuthMiddleware::checkStillActive()` consulta BD cada 60s. Si desactivado, destruye sesión |
| U.5 | Re-autenticación | 2026-03-29 | Cambio de contraseña requiere `current_password` del admin. `AuthMiddleware::verifyCurrentPassword()` |
| U.6 | Dropdown de acciones | 2026-03-29 | Menú tres puntos por tarjeta: editar, cambiar contraseña, activar/desactivar, eliminar. Reemplaza campos inline |
| U.7 | Toast notifications | 2026-03-29 | Sistema global de toasts (success/error) con animación. Elimina alert() y location.reload() |
| U.8 | DOM updates sin reload | 2026-03-29 | Toggle y delete actualizan tarjeta directamente. Delete con fade out |
| U.9 | Confirmación en toggle | 2026-03-29 | Modal de confirmación con mensaje contextual y advertencia de consecuencia |
| U.10 | i18n en JS | 2026-03-29 | `window.LANG` con traducciones desde PHP. Cero strings hardcodeados en JS |
| U.11 | Cambiar rol | 2026-03-29 | Endpoint POST /api/users/update + modal editar. Protecciones: no cambiar propio, no quitar último admin |
| U.12 | Editar email | 2026-03-29 | Campo email en modal editar y en formulario de creación |
| U.13 | Last login tracking | 2026-03-29 | Columna `last_login_at` en BD. Se actualiza en AuthMiddleware::login(). Visible en tarjeta |
| U.14 | Búsqueda y filtros | 2026-03-29 | Barra de búsqueda por username + filtros por rol (admin/editor/guest) + estado (activo/inactivo). Client-side |
| U.15 | Fechas en tarjetas | 2026-03-29 | Último acceso y fecha de creación en cada tarjeta |
| U.16 | Credenciales post-creación | 2026-03-29 | Panel verde con username + password + botón copiar después de crear usuario |

### Archivos modificados

- `app/Models/User.php` — Nuevos métodos: findWithPassword, update, updateLastLogin, isStillActive, validatePassword, validateUsername. all() con filtros
- `app/Middleware/AuthMiddleware.php` — checkStillActive (cada 60s), verifyCurrentPassword, updateLastLogin en login
- `app/Controllers/Api/UserApiController.php` — Rate limiting, re-auth, max password, nuevo endpoint update
- `views/admin/users.php` — Rediseño completo: dropdowns, search, filters, modales (edit, password, confirm), toasts, credentials panel
- `public/assets/css/admin.css` — Dropdown, search bar, filters, toast, credentials, modal-form
- `public/assets/js/admin.js` — Reescrito: toasts, DOM updates, dropdowns, search/filter, modales, i18n
- `config/routes.php` — Nueva ruta POST /api/users/update
- `database/schema.sql` — Columna last_login_at
- `lang/es/admin.php` + `lang/en/admin.php` — 18 nuevas traducciones
- `lang/es/auth.php` + `lang/en/auth.php` — password_max

---

## FASE 4: Gestión de Proyectos ✅
> Completada: 2026-03-29

### Tareas completadas

| # | Tarea | Fecha | Detalles |
|---|-------|-------|----------|
| 4.1 | Modelo Project | 2026-03-29 | CRUD completo, allWithPages() escanea filesystem, scanPages() detecta organización, slugExists() |
| 4.2 | API REST proyectos | 2026-03-29 | 8 endpoints: list, create, edit, delete, toggle, compile, content, source. Rate limiting en mutaciones |
| 4.3 | Templates | 2026-03-29 | index, tarea, quiz, foros, organization, palette, snippets, CSS master con paleta derivada (color-mix oklch), JS con cleanup |
| 4.4 | Compilación CSS | 2026-03-29 | CssCompiler: master → mobile (sin @media ≥1200px) + desktop (sin dark mode test). Elimina html[data-theme="dark"] |
| 4.5 | Panel admin proyectos | 2026-03-29 | Grid con cards, dropdown acciones (abrir, editar, compilar, toggle, eliminar), formulario crear con nombre/slug preview/colores/org/CDNs |
| 4.6 | Colores con paleta | 2026-03-29 | Color pickers sincronizados con hex input. Master CSS usa --ct-primary-base y --ct-secondary-base con paleta derivada automática |
| 4.7 | Organización | 2026-03-29 | Semanas/módulos/unidades con cantidad configurable. Crear y editar. Solo agrega páginas nuevas, no elimina existentes |
| 4.8 | CDNs en crear y editar | 2026-03-29 | Bootstrap, Bootstrap Icons, Font Awesome, Animate.css. Pre-carga en editar. Actualiza @import en master CSS |
| 4.9 | Dashboard conectado | 2026-03-29 | Selector de proyectos filtra activos (is_active=1). Persistencia con sessionStorage al navegar a Admin. H1 título de página separado |
| 4.10 | CSP fix | 2026-03-29 | Eliminados scripts inline, traducciones vía data-* attributes (#js-lang). Dropdown y toast movidos a app.js global |

### Archivos creados

- `app/Models/Project.php` — Modelo con CRUD, scanPages, allWithPages
- `app/Controllers/Api/ProjectApiController.php` — 8 endpoints API
- `app/Controllers/AdminController.php` — Ruta projects con vista
- `app/Helpers/CssCompiler.php` — Compilación master → mobile + desktop
- `views/admin/projects.php` — Panel completo con crear/editar/grid
- `public/assets/js/admin-projects.js` — JS para CRUD de proyectos
- `templates/` — 9 archivos template (HTML, CSS master, JS)

### Archivos modificados

- `config/routes.php` — 8 rutas de proyectos + admin/projects
- `views/layouts/admin.php` — Tab Proyectos activa, admin-projects.js
- `public/assets/js/app.js` — Dropdown global, showToast global, getLang()
- `public/assets/js/admin.js` — Limpiado: usa funciones globales, guard para página usuarios
- `public/assets/js/dashboard.js` — Filtro is_active, sessionStorage, pageTitle
- `public/assets/css/admin.css` — Estilos proyectos, modal-form-lg, color inputs, CDN grid
- `public/assets/css/dashboard.css` — sl-page-title, sl-content-main
- `views/dashboard/index.php` — H1 page-title separado del contenido
- `views/admin/users.php` — js-lang data attributes (sin script inline)
- `.htaccess` — Acceso a storage/projects CSS/JS/img
- `lang/es/admin.php` + `lang/en/admin.php` — Traducciones de proyectos

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
