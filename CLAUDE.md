# SkinLab — Instrucciones del Proyecto

## Qué es SkinLab

Entorno de desarrollo de temas gráficos para Canvas LMS. Dos ambientes:

- **Ambiente de desarrollo (SkinLab UI)**: Sin restricciones. Bootstrap, JS, cualquier CSS moderno.
- **Ambiente de temas (contenido del proyecto)**: Debe respetar las limitaciones de Canvas LMS.

---

## Políticas de Diseño

### Mobile First (obligatorio)

- CSS base para móvil, escalar con `min-width` media queries.
- Nunca usar `max-width` en media queries.
- Breakpoints exclusivos de Bootstrap 5: sm(576), md(768), lg(992), xl(1200), xxl(1400).
- Un solo bloque `@media` por breakpoint al final de cada archivo CSS.

### Medidas responsivas

- Nunca px fijos para dimensiones de layout.
- Usar `%`, `rem`, `em`, `vw`, `vh`, `clamp()`, `min()`, `max()`.
- Px solo para bordes (1px), sombras y detalles decorativos mínimos.

### CSS limpio

- Nunca colores hardcodeados — siempre variables CSS.
- Usar `color-mix(in srgb, var(--variable) X%, transparent)` para opacidades.
- Minimizar variables y tokens a su máxima expresión para eficiencia.
- Media queries unitarios y organizados al final del archivo.
- No duplicar media queries — consolidar reglas en un solo bloque por breakpoint.

### HTML semántico

- Usar `header`, `nav`, `main`, `section`, `article`, `aside`, `footer`, `fieldset`, `legend`, `dl/dt/dd`.
- Nunca `<div>` genéricos cuando existe una etiqueta semántica apropiada.

### Accesibilidad

- `aria-label` en todos los botones de solo ícono.
- `aria-hidden="true"` en íconos decorativos.
- `role="alert"` en errores, `role="status"` en confirmaciones.
- `aria-live="polite"` en contenedores de mensajes dinámicos.
- `:focus-visible` con outline visible para navegación por teclado.
- Clase `.sr-only` para labels de screen reader.

### Layout

- Flexbox o CSS Grid siempre que sea posible.
- Evitar floats y posicionamientos absolutos para layout.

### Componentes

- Bootstrap 5 como base en el ambiente de desarrollo.
- Bootstrap Icons para iconografía.
- Cada módulo/componente debe seguir mejores prácticas de UX.

---

## Restricciones de Canvas LMS

**SIEMPRE revisar la documentación (`/admin/docs`) antes de crear contenido para proyectos.**

### HTML permitido en contenido de Canvas

Solo las etiquetas documentadas en la sección "HTML Permitido". Importante:
- `<h1>` NO está permitido — usar `<h2>` como encabezado principal.
- `<script>`, `<style>`, `<link>`, `<form>`, `<input>`, `<button>` son eliminados.

### CSS en contenido de Canvas

- Solo estilos inline (`style=""`) con las propiedades documentadas.
- NO se permiten inline: `box-shadow`, `text-shadow`, `text-transform`, `opacity`, `transform`, `transition`, `animation`, `filter`, `clip-path`, `object-fit`, `letter-spacing`, `word-spacing`.
- NO se permiten variables CSS (`--mi-variable`) en inline.

### CSS en archivos subidos a Canvas (Admin > Temas)

- Sin restricciones de propiedades — cualquier CSS válido funciona.
- Usar variables de Canvas (`--ic-brand-*`) con fallback.
- Al compilar: eliminar bloque `html[data-theme="dark"]` (solo para pruebas).
- Separar en mobile (hasta 992px) y desktop (todo).

### JavaScript

- NO se permite JS en contenido de páginas de Canvas.
- Solo en archivos subidos a nivel de cuenta (Admin > Temas).

---

## Compilación CSS

Al compilar `master.css` → `mobile.css` + `desktop.css`:

1. Eliminar bloque `html[data-theme="dark"]` (ambiente de pruebas).
2. Mobile: eliminar `@media (min-width: 1200px)` y superiores.
3. Desktop: mantener todo excepto el bloque de pruebas.
4. Los archivos compilados son los que se suben a Canvas.

---

## Internacionalización

- Todo texto visible en el frontend debe venir de archivos de idioma (`lang/es/`, `lang/en/`).
- Cero strings hardcodeados en vistas, controladores o JS.
- JS usa `data-*` attributes en `#js-lang` o funciones globales como `getLang()`.
- Nunca `<script>` inline — CSP lo bloquea.

---

## Seguridad

- CSRF automático en todos los POST.
- Prepared statements en todas las queries SQL.
- Rate limiting por IP en endpoints sensibles.
- `e()` para escapar HTML en todas las salidas.
- Content-Security-Policy estricta (sin `unsafe-inline` en scripts).
- Asset cache-busting automático via `asset()` helper con filemtime.

---

## Git

- Repositorio: https://github.com/alandete/skinlab
- Email para commits: `25472104+alandete@users.noreply.github.com`
- Usar `-c user.email="25472104+alandete@users.noreply.github.com"` en cada commit.
- Branch principal: `main`.

---

## Stack

- PHP 8.3+ con `declare(strict_types=1)`
- MySQL 8.0 (BD: skinlab, usuario: skinlab_user)
- Bootstrap 5 + Bootstrap Icons vía CDN
- Fuente: Lato (Google Fonts)
- Laragon en Windows 11 (https://skinlab.test)
