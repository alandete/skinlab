# SkinLab — Design System

Basado en [Primer](https://primer.style) (GitHub Design System), adaptado a SkinLab.

---

## 1. Tipografía

### Fuente
```
--sl-font: 'Lato', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
--sl-font-mono: 'Consolas', 'Monaco', 'Courier New', monospace;
```

### Escala de tamaños

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-text-xs` | 0.75rem (12px) | Badges, tags, hints, labels uppercase |
| `--sl-text-sm` | 0.8125rem (13px) | Hints, form hints, metadata |
| `--sl-text-base` | 0.875rem (14px) | Body text, inputs, botones, tablas |
| `--sl-text-md` | 1rem (16px) | Body principal, párrafos |
| `--sl-text-lg` | 1.25rem (20px) | Títulos de sección (h3) |
| `--sl-text-xl` | 1.5rem (24px) | Títulos de página (h2) |
| `--sl-text-2xl` | 2rem (32px) | Títulos hero (h1, solo SkinLab UI) |

### Pesos

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-weight-normal` | 400 | Texto body |
| `--sl-weight-medium` | 500 | Labels, énfasis suave |
| `--sl-weight-semibold` | 600 | Botones, nav items activos |
| `--sl-weight-bold` | 700 | Títulos, secciones |

### Line heights

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-leading-tight` | 1.25 | Títulos, encabezados |
| `--sl-leading-normal` | 1.5 | Body text, párrafos |
| `--sl-leading-relaxed` | 1.7 | Textos largos, documentación |

---

## 2. Espaciado

Escala basada en múltiplos de 4px (0.25rem).

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-space-1` | 0.25rem (4px) | Gaps mínimos, separadores internos |
| `--sl-space-2` | 0.5rem (8px) | Padding interno de badges, chips |
| `--sl-space-3` | 0.75rem (12px) | Padding de inputs, gap de form-row |
| `--sl-space-4` | 1rem (16px) | Padding estándar, margin entre elementos |
| `--sl-space-5` | 1.25rem (20px) | Padding de cards, secciones |
| `--sl-space-6` | 1.5rem (24px) | Separación entre secciones de formulario |
| `--sl-space-8` | 2rem (32px) | Padding de página, margen de layout |
| `--sl-space-10` | 2.5rem (40px) | Spacing de hero, separaciones grandes |
| `--sl-space-12` | 3rem (48px) | Padding de modales, secciones mayores |

### Uso por contexto

| Contexto | Padding | Gap |
|----------|---------|-----|
| Inputs | `--sl-space-3` vertical, `--sl-space-3` horizontal | — |
| Buttons | `--sl-space-3` vertical, `--sl-space-4` horizontal | `--sl-space-2` (icon-text) |
| Cards | `--sl-space-5` | — |
| Form groups | — | `--sl-space-4` margin-bottom |
| Form sections | — | `--sl-space-6` margin-bottom + border-bottom |
| Page content | `--sl-space-4` mobile, `--sl-space-6` desktop | — |
| Modal | `--sl-space-8` | — |

---

## 3. Colores

### Tokens base (ya existentes, estandarizar nomenclatura)

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-primary` | #0374B5 | Acciones principales, enlaces, acentos |
| `--sl-primary-hover` | #0587D4 | Hover de primary |
| `--sl-primary-dark` | #025E91 | Active de primary |
| `--sl-secondary` | #2D3B45 | Fondos oscuros, toolbar, sidebar admin |
| `--sl-secondary-light` | #394B58 | Nav Canvas |

### Fondos

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-bg` | #F5F5F5 | Fondo general de la app |
| `--sl-bg-white` | #FFFFFF | Cards, modales, contenido |
| `--sl-bg-dark` | #1A1A2E | Fondo de auth pages |

### Texto

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-text` | #2D3B45 | Texto principal, títulos |
| `--sl-text-light` | #566069 | Texto secundario, labels |
| `--sl-text-muted` | #717B84 | Hints, metadata, placeholders |

### Estado

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-success` | #0B874B | Confirmaciones, checks |
| `--sl-danger` | #C62828 | Errores, eliminar, alertas |
| `--sl-warning` | #F57C00 | Advertencias |
| `--sl-success-bg` | #E8F5E9 | Fondo de alertas success |
| `--sl-danger-bg` | #FFEBEE | Fondo de alertas danger |
| `--sl-warning-bg` | #FFF3E0 | Fondo de alertas warning |

### Bordes

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-border` | #C7CDD1 | Bordes visibles, hover |
| `--sl-border-light` | #E0E0E0 | Bordes sutiles, separadores |

### Reglas de uso

- **Texto sobre blanco**: usar `--sl-text` (principal) o `--sl-text-light` (secundario)
- **Texto sobre oscuro**: usar `--sl-white` o `color-mix(in srgb, var(--sl-white) 85%, transparent)` mínimo
- **Fondos de interacción**: usar `color-mix` con el color base al 8-12% para hover
- **Nunca** hardcodear colores — siempre variables
- **Opacidades**: siempre con `color-mix(in srgb, var(--variable) X%, transparent)`

---

## 4. Bordes y Radios

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-radius` | 0.375rem (6px) | Inputs, botones, badges |
| `--sl-radius-lg` | 0.75rem (12px) | Cards, modales, paneles |
| `--sl-radius-full` | 9999px | Pills, avatares |
| `--sl-border-width` | 1px | Bordes estándar |
| `--sl-border-width-thick` | 2px | Focus rings, bordes activos |

---

## 5. Sombras

| Token | Valor | Uso |
|-------|-------|-----|
| `--sl-shadow-sm` | 0 1px 3px ... 8% | Cards hover, dropdowns |
| `--sl-shadow` | 0 4px 12px ... 10% | Modales pequeños, paneles flotantes |
| `--sl-shadow-lg` | 0 20px 60px ... 30% | Modales grandes, overlay |

---

## 6. Componentes

### Botones

| Variante | Fondo | Texto | Hover fondo | Uso |
|----------|-------|-------|-------------|-----|
| Primary | `--sl-primary` | `--sl-white` | `--sl-primary-hover` | Acción principal |
| Secondary | `--sl-border-light` | `--sl-text` | `--sl-border` | Acción secundaria, cancelar |
| Danger | `--sl-danger` | `--sl-white` | #B71C1C | Eliminar, acciones destructivas |

**Tamaños:**

| Tamaño | Padding | Font size | Uso |
|--------|---------|-----------|-----|
| Default | `--sl-space-3` / `--sl-space-4` | `--sl-text-base` | Acciones principales |
| Small | `--sl-space-2` / `--sl-space-3` | `--sl-text-sm` | Acciones inline, secundarias |
| Icon | 2.25rem x 2.25rem | 1rem | Acciones de solo ícono |

**Estados:**
- `:hover` — Fondo más oscuro, sin underline
- `:focus-visible` — Outline 2px solid primary, offset 2px
- `:disabled` — Opacity 0.6, cursor not-allowed
- Links como botones (`a.btn`) — Nunca underline

### Formularios

**Labels:**
- Font size: `--sl-text-base` (0.875rem)
- Font weight: `--sl-weight-semibold` (600)
- Color: `--sl-text`
- Margin bottom: `--sl-space-2`

**Inputs:**
- Padding: `--sl-space-3` vertical y horizontal
- Border: 1px solid `--sl-border-light`
- Border radius: `--sl-radius`
- Font size: `--sl-text-base`
- Focus: border `--sl-primary` + box-shadow 3px primary 15%

**Hints:**
- Font size: `--sl-text-sm` (0.8125rem)
- Color: `--sl-text-muted`
- Margin top: `--sl-space-1`
- Siempre después del input, nunca antes

**Form groups:**
- Margin bottom: `--sl-space-4`
- En secciones separadas: `--sl-space-6` + border-bottom

### Cards

- Background: `--sl-bg-white`
- Border: 1px solid `--sl-border-light`
- Border radius: `--sl-radius-lg`
- Padding: `--sl-space-5`
- Hover: border `--sl-border` + `--sl-shadow-sm`

### Alertas / Toasts

- Border radius: `--sl-radius` (alertas) / `--sl-radius-full` (toasts)
- Padding: `--sl-space-3` / `--sl-space-4`
- Font size: `--sl-text-sm`
- Font weight: `--sl-weight-semibold`
- Variantes: success (verde), error (rojo), warning (naranja)

### Dropdowns

- Background: `--sl-bg-white`
- Border: 1px solid `--sl-border-light`
- Border radius: `--sl-radius-lg`
- Shadow: `--sl-shadow`
- Items padding: `--sl-space-2` / `--sl-space-4`
- Items hover: `--sl-bg`
- Danger items: color `--sl-danger`

### Tablas

- Header: bg `--sl-bg`, text `--sl-text-muted`, uppercase, `--sl-text-xs`
- Cells: padding `--sl-space-2` / `--sl-space-3`, border-bottom `--sl-border-light`
- Font size: `--sl-text-sm`

---

## 7. Layout

### Breakpoints (Bootstrap 5)

| Nombre | Min-width | Uso |
|--------|-----------|-----|
| sm | 576px | Form-row 2 columnas, auth card padding |
| md | 768px | Sidebar admin visible, nav Canvas visible, grids 2+ cols |
| lg | 992px | — |
| xl | 1200px | Sidebar estado del curso visible |
| xxl | 1400px | — |

### Reglas

- Mobile First siempre
- Un solo `@media` por breakpoint al final del archivo
- Nunca `max-width` en media queries
- Flexbox o Grid para layout, nunca floats
- Medidas responsivas (rem, %, vw, vh, clamp), nunca px para layout

---

## 8. Accesibilidad

- `:focus-visible` con outline 2px solid primary, offset 2px
- `aria-label` en todos los botones de solo ícono
- `aria-hidden="true"` en íconos decorativos
- `role="alert"` en errores, `role="status"` en confirmaciones
- `.sr-only` para labels de screen reader
- `.sr-only-focusable` para skip links
- Contraste mínimo: 4.5:1 texto, 3:1 elementos grandes
- `--sl-text-light` (#566069) y `--sl-text-muted` (#717B84) verificados WCAG AA sobre blanco
