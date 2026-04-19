<link rel="stylesheet" href="<?= asset('css/docs.css') ?>">

<div class="docs-layout">

    <!-- TOC Sidebar -->
    <nav class="docs-toc" aria-label="Tabla de contenidos">
        <ul class="toc">
            <li class="toc-title">Canvas LMS</li>
            <li><a href="#html-permitido" class="toc-link active"><i class="bi bi-code-slash" aria-hidden="true"></i> HTML Permitido</a></li>
            <li><a href="#html-restringido" class="toc-link"><i class="bi bi-slash-circle" aria-hidden="true"></i> HTML Restringido</a></li>
            <li><a href="#atributos" class="toc-link"><i class="bi bi-tag" aria-hidden="true"></i> Atributos</a></li>
            <li><a href="#css-permitido" class="toc-link"><i class="bi bi-palette" aria-hidden="true"></i> CSS Inline</a></li>
            <li><a href="#css-restringido" class="toc-link"><i class="bi bi-x-circle" aria-hidden="true"></i> CSS Restringido</a></li>
            <li><a href="#variables-css" class="toc-link"><i class="bi bi-palette2" aria-hidden="true"></i> Variables Canvas</a></li>
            <li><a href="#dark-mode" class="toc-link"><i class="bi bi-moon" aria-hidden="true"></i> Modo Oscuro</a></li>
            <li><a href="#dark-mode-web-canvas" class="toc-link"><i class="bi bi-moon-stars" aria-hidden="true"></i> Dark mode en Canvas Web</a></li>
            <li><a href="#high-contrast" class="toc-link"><i class="bi bi-circle-half" aria-hidden="true"></i> Alto Contraste</a></li>
            <li><a href="#tipografia" class="toc-link"><i class="bi bi-fonts" aria-hidden="true"></i> Tipografía</a></li>
            <li class="toc-title">Proyecto</li>
            <li><a href="#buenas-practicas" class="toc-link"><i class="bi bi-lightbulb" aria-hidden="true"></i> Buenas Prácticas</a></li>
            <li><a href="#flujo-trabajo" class="toc-link"><i class="bi bi-diagram-3" aria-hidden="true"></i> Flujo de Trabajo</a></li>
            <li><a href="#roles" class="toc-link"><i class="bi bi-shield-lock" aria-hidden="true"></i> Roles y Acceso</a></li>
            <li><a href="#fuentes" class="toc-link"><i class="bi bi-link-45deg" aria-hidden="true"></i> Fuentes</a></li>
        </ul>
    </nav>

    <!-- Content -->
    <main class="docs-body">

        <!-- ════════════ HTML PERMITIDO ════════════ -->
        <section id="html-permitido">
            <h2><i class="bi bi-code-slash" aria-hidden="true"></i> HTML Permitido</h2>
            <p>Canvas sanitiza todo el HTML usando <code>canvas_sanitize</code>. Solo las siguientes etiquetas sobreviven al guardado:</p>

            <h3>Estructura y semántica</h3>
            <div class="tag-grid">
                <span class="tag allowed">&lt;a&gt;</span><span class="tag allowed">&lt;article&gt;</span><span class="tag allowed">&lt;aside&gt;</span><span class="tag allowed">&lt;blockquote&gt;</span><span class="tag allowed">&lt;br&gt;</span><span class="tag allowed">&lt;dd&gt;</span><span class="tag allowed">&lt;del&gt;</span><span class="tag allowed">&lt;details&gt;</span><span class="tag allowed">&lt;div&gt;</span><span class="tag allowed">&lt;dl&gt;</span><span class="tag allowed">&lt;dt&gt;</span><span class="tag allowed">&lt;footer&gt;</span><span class="tag allowed">&lt;h2&gt;</span><span class="tag allowed">&lt;h3&gt;</span><span class="tag allowed">&lt;h4&gt;</span><span class="tag allowed">&lt;h5&gt;</span><span class="tag allowed">&lt;h6&gt;</span><span class="tag allowed">&lt;header&gt;</span><span class="tag allowed">&lt;hr&gt;</span><span class="tag allowed">&lt;ins&gt;</span><span class="tag allowed">&lt;li&gt;</span><span class="tag allowed">&lt;map&gt;</span><span class="tag allowed">&lt;mark&gt;</span><span class="tag allowed">&lt;nav&gt;</span><span class="tag allowed">&lt;ol&gt;</span><span class="tag allowed">&lt;p&gt;</span><span class="tag allowed">&lt;pre&gt;</span><span class="tag allowed">&lt;section&gt;</span><span class="tag allowed">&lt;small&gt;</span><span class="tag allowed">&lt;span&gt;</span><span class="tag allowed">&lt;summary&gt;</span><span class="tag allowed">&lt;ul&gt;</span>
            </div>

            <h3>Formato de texto</h3>
            <div class="tag-grid">
                <span class="tag allowed">&lt;b&gt;</span><span class="tag allowed">&lt;big&gt;</span><span class="tag allowed">&lt;cite&gt;</span><span class="tag allowed">&lt;code&gt;</span><span class="tag allowed">&lt;em&gt;</span><span class="tag allowed">&lt;i&gt;</span><span class="tag allowed">&lt;kbd&gt;</span><span class="tag allowed">&lt;strong&gt;</span><span class="tag allowed">&lt;sub&gt;</span><span class="tag allowed">&lt;sup&gt;</span><span class="tag allowed">&lt;u&gt;</span><span class="tag allowed">&lt;var&gt;</span>
            </div>

            <h3>Media</h3>
            <div class="tag-grid">
                <span class="tag allowed">&lt;audio&gt;</span><span class="tag allowed">&lt;embed&gt;</span><span class="tag allowed">&lt;iframe&gt;</span><span class="tag allowed">&lt;img&gt;</span><span class="tag allowed">&lt;object&gt;</span><span class="tag allowed">&lt;picture&gt;</span><span class="tag allowed">&lt;source&gt;</span><span class="tag allowed">&lt;track&gt;</span><span class="tag allowed">&lt;video&gt;</span>
            </div>

            <h3>Tablas</h3>
            <div class="tag-grid">
                <span class="tag allowed">&lt;table&gt;</span><span class="tag allowed">&lt;thead&gt;</span><span class="tag allowed">&lt;tbody&gt;</span><span class="tag allowed">&lt;tfoot&gt;</span><span class="tag allowed">&lt;tr&gt;</span><span class="tag allowed">&lt;th&gt;</span><span class="tag allowed">&lt;td&gt;</span><span class="tag allowed">&lt;col&gt;</span><span class="tag allowed">&lt;colgroup&gt;</span><span class="tag allowed">&lt;caption&gt;</span>
            </div>
        </section>

        <!-- ════════════ HTML RESTRINGIDO ════════════ -->
        <section id="html-restringido">
            <h2><i class="bi bi-slash-circle" aria-hidden="true"></i> HTML Restringido</h2>
            <p>Estas etiquetas son <strong>eliminadas silenciosamente</strong> al guardar contenido:</p>
            <div class="tag-grid">
                <span class="tag blocked">&lt;script&gt;</span><span class="tag blocked">&lt;style&gt;</span><span class="tag blocked">&lt;link&gt;</span><span class="tag blocked">&lt;meta&gt;</span><span class="tag blocked">&lt;head&gt;</span><span class="tag blocked">&lt;body&gt;</span><span class="tag blocked">&lt;html&gt;</span><span class="tag blocked">&lt;h1&gt;</span><span class="tag blocked">&lt;form&gt;</span><span class="tag blocked">&lt;input&gt;</span><span class="tag blocked">&lt;select&gt;</span><span class="tag blocked">&lt;textarea&gt;</span><span class="tag blocked">&lt;button&gt;</span>
            </div>
            <div class="callout warning">
                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                <div><strong>Importante:</strong> <code>&lt;h1&gt;</code> no está en la lista permitida de Canvas. Usa <code>&lt;h2&gt;</code> como encabezado principal. <code>&lt;style&gt;</code> tampoco se permite — solo estilos inline con <code>style=""</code>.</div>
            </div>
        </section>

        <!-- ════════════ ATRIBUTOS ════════════ -->
        <section id="atributos">
            <h2><i class="bi bi-tag" aria-hidden="true"></i> Atributos Permitidos</h2>
            <h3>Universales</h3>
            <div class="tag-grid">
                <span class="tag attr">style</span><span class="tag attr">class</span><span class="tag attr">id</span><span class="tag attr">title</span><span class="tag attr">role</span><span class="tag attr">lang</span><span class="tag attr">dir</span><span class="tag attr">data-*</span>
            </div>
            <h3>Por elemento</h3>
            <table class="docs-table">
                <thead><tr><th>Elemento</th><th>Atributos</th></tr></thead>
                <tbody>
                    <tr><td><code>&lt;a&gt;</code></td><td>href, target, name</td></tr>
                    <tr><td><code>&lt;img&gt;</code></td><td>src, alt, height, width, usemap, title, align</td></tr>
                    <tr><td><code>&lt;iframe&gt;</code></td><td>src, width, height, name, allowfullscreen</td></tr>
                    <tr><td><code>&lt;video&gt;</code></td><td>src, poster, width, height, controls, muted, playsinline</td></tr>
                    <tr><td><code>&lt;audio&gt;</code></td><td>src, controls, muted</td></tr>
                    <tr><td><code>&lt;table&gt;</code></td><td>summary, width, border, cellpadding, cellspacing</td></tr>
                </tbody>
            </table>
            <h3>Protocolos permitidos en URLs</h3>
            <table class="docs-table">
                <thead><tr><th>Contexto</th><th>Protocolos</th></tr></thead>
                <tbody>
                    <tr><td><code>a[href]</code></td><td>http, https, mailto, tel, ftp, skype</td></tr>
                    <tr><td><code>img[src]</code>, <code>iframe[src]</code></td><td>http, https, relativo</td></tr>
                    <tr><td><code>audio/video</code></td><td>data, http, https, relativo</td></tr>
                </tbody>
            </table>
            <div class="callout danger">
                <i class="bi bi-shield-exclamation" aria-hidden="true"></i>
                <div><strong>Bloqueado:</strong> El protocolo <code>javascript:</code> siempre es eliminado en cualquier atributo URL.</div>
            </div>
            <h3>Atributos data-* bloqueados</h3>
            <div class="tag-grid">
                <span class="tag blocked">data-xml</span><span class="tag blocked">data-method</span><span class="tag blocked">data-turn-into-dialog</span><span class="tag blocked">data-flash-message</span><span class="tag blocked">data-popup-within</span><span class="tag blocked">data-html-tooltip-title</span>
            </div>
        </section>

        <!-- ════════════ CSS PERMITIDO ════════════ -->
        <section id="css-permitido">
            <h2><i class="bi bi-palette" aria-hidden="true"></i> CSS Permitido (inline)</h2>
            <p>Solo se permiten estilos inline (<code>style=""</code>). Propiedades aceptadas:</p>
            <div class="css-grid">
                <div class="css-group"><h4>Layout</h4><ul><li><code>display</code> (flex, grid, block, inline, etc.)</li><li><code>position</code> (relative, absolute, fixed, sticky)</li><li><code>top</code>, <code>right</code>, <code>left</code></li><li><code>float</code>, <code>clear</code></li><li><code>z-index</code>, <code>visibility</code></li></ul></div>
                <div class="css-group"><h4>Flexbox</h4><ul><li><code>flex</code> y variantes</li><li><code>flex-direction</code>, <code>flex-wrap</code></li><li><code>justify-content</code>, <code>align-items</code></li><li><code>gap</code></li></ul></div>
                <div class="css-group"><h4>Grid</h4><ul><li><code>grid</code> y variantes</li><li><code>grid-template-columns</code>, <code>grid-template-rows</code></li><li><code>grid-row</code>, <code>grid-column</code></li><li><code>gap</code></li></ul></div>
                <div class="css-group"><h4>Dimensiones</h4><ul><li><code>width</code>, <code>height</code></li><li><code>max-width</code>, <code>max-height</code></li><li><code>min-width</code>, <code>min-height</code></li><li><code>overflow</code>, <code>overflow-x</code>, <code>overflow-y</code></li></ul></div>
                <div class="css-group"><h4>Espaciado</h4><ul><li><code>margin</code> y variantes</li><li><code>padding</code> y variantes</li></ul></div>
                <div class="css-group"><h4>Borde y fondo</h4><ul><li><code>border</code> y variantes</li><li><code>border-radius</code></li><li><code>background</code> y variantes</li><li><code>background-image</code> (solo http/https)</li></ul></div>
                <div class="css-group"><h4>Texto</h4><ul><li><code>color</code>, <code>font</code> y variantes</li><li><code>font-family</code> (solo del sistema)</li><li><code>font-size</code>, <code>font-weight</code></li><li><code>line-height</code>, <code>text-align</code></li><li><code>text-decoration</code>, <code>text-indent</code></li><li><code>white-space</code>, <code>direction</code></li></ul></div>
                <div class="css-group"><h4>Otros</h4><ul><li><code>cursor</code></li><li><code>list-style</code></li><li><code>table-layout</code></li><li><code>vertical-align</code></li></ul></div>
            </div>
        </section>

        <!-- ════════════ CSS RESTRINGIDO ════════════ -->
        <section id="css-restringido">
            <h2><i class="bi bi-x-circle" aria-hidden="true"></i> CSS Restringido</h2>
            <p>Propiedades <strong>no permitidas</strong> en estilos inline:</p>
            <div class="tag-grid">
                <span class="tag blocked">box-shadow</span><span class="tag blocked">text-shadow</span><span class="tag blocked">text-transform</span><span class="tag blocked">opacity</span><span class="tag blocked">transform</span><span class="tag blocked">transition</span><span class="tag blocked">animation</span><span class="tag blocked">@keyframes</span><span class="tag blocked">filter</span><span class="tag blocked">clip-path</span><span class="tag blocked">object-fit</span><span class="tag blocked">letter-spacing</span><span class="tag blocked">word-spacing</span><span class="tag blocked">user-select</span><span class="tag blocked">content</span><span class="tag blocked">--variables CSS</span>
            </div>
            <h3>At-rules no disponibles en contenido</h3>
            <div class="tag-grid">
                <span class="tag blocked">@import</span><span class="tag blocked">@font-face</span><span class="tag blocked">@keyframes</span><span class="tag blocked">@media</span><span class="tag blocked">@supports</span>
            </div>
            <div class="callout info">
                <i class="bi bi-info-circle" aria-hidden="true"></i>
                <div><strong>Excepción:</strong> Los administradores de Canvas pueden subir archivos CSS/JS personalizados a nivel de cuenta desde <strong>Admin > Temas > Subir CSS/JS</strong>. Estos archivos pueden usar cualquier propiedad CSS sin restricción.</div>
            </div>
        </section>

        <!-- ════════════ VARIABLES CSS ════════════ -->
        <section id="variables-css">
            <h2><i class="bi bi-palette2" aria-hidden="true"></i> Variables CSS de Canvas</h2>
            <p>Disponibles en archivos CSS subidos a nivel de admin. <strong>No funcionan en estilos inline.</strong></p>

            <h3>Variables de contenido</h3>
            <table class="docs-table">
                <thead><tr><th>Variable</th><th>Default</th><th>Qué afecta</th></tr></thead>
                <tbody>
                    <tr><td><code>--ic-brand-primary</code></td><td>#0374B5</td><td>Color primario, estados activos, acentos</td></tr>
                    <tr><td><code>--ic-brand-font-color-dark</code></td><td>#2D3B45</td><td>Texto del body en páginas</td></tr>
                    <tr><td><code>--ic-link-color</code></td><td>#0374B5</td><td>Enlaces en páginas, tareas, foros</td></tr>
                    <tr><td><code>--ic-brand-button--primary-bgd</code></td><td>#0374B5</td><td>Fondo de botones primarios</td></tr>
                    <tr><td><code>--ic-brand-button--primary-text</code></td><td>#FFFFFF</td><td>Texto de botones primarios</td></tr>
                </tbody>
            </table>

            <h3>Variables derivadas (auto-generadas por Canvas)</h3>
            <table class="docs-table">
                <thead><tr><th>Variable</th><th>Base</th><th>Transformación</th></tr></thead>
                <tbody>
                    <tr><td><code>--ic-brand-primary-darkened-5</code></td><td>primary</td><td>5% más oscuro</td></tr>
                    <tr><td><code>--ic-brand-primary-darkened-10</code></td><td>primary</td><td>10% más oscuro</td></tr>
                    <tr><td><code>--ic-brand-primary-lightened-15</code></td><td>primary</td><td>15% más claro</td></tr>
                    <tr><td><code>--ic-brand-font-color-dark-lightened-15</code></td><td>font-color-dark</td><td>15% más claro</td></tr>
                    <tr><td><code>--ic-link-color-darkened-10</code></td><td>link-color</td><td>10% más oscuro</td></tr>
                </tbody>
            </table>

            <h3>Variables de navegación</h3>
            <table class="docs-table">
                <thead><tr><th>Variable</th><th>Descripción</th></tr></thead>
                <tbody>
                    <tr><td><code>--ic-brand-global-nav-bgd</code></td><td>Fondo de la navegación lateral</td></tr>
                    <tr><td><code>--ic-brand-global-nav-ic-icon-svg-fill</code></td><td>Color de íconos</td></tr>
                    <tr><td><code>--ic-brand-global-nav-menu-item__text-color</code></td><td>Texto del menú</td></tr>
                    <tr><td><code>--ic-brand-global-nav-logo-bgd</code></td><td>Fondo del logo</td></tr>
                </tbody>
            </table>

            <h3>Estructura recomendada</h3>
            <pre><code>/* 1. Sobreescritura de variables Canvas */
:root {
  --ic-brand-primary: #E63946;
  --ic-link-color: #E63946;
}

/* 2. Variables propias del proyecto (--ct-*) */
:root {
  --ct-primary-base: #E63946;
  --ct-secondary-base: #457B9D;
}

/* 3. Dark mode para apps móviles */
@media (prefers-color-scheme: dark) {
  :root {
    --ct-primary: color-mix(in oklch, var(--ct-primary-base) 65%, white);
    --ct-bg: var(--ct-neutral-900);
  }
}

/* 4. Dark mode para ambiente de pruebas (se elimina al compilar) */
html[data-theme="dark"] {
  /* mismos valores que @media de arriba */
}</code></pre>

            <h3>API</h3>
            <pre><code>GET /api/v1/brand_variables</code></pre>
        </section>

        <!-- ════════════ DARK MODE ════════════ -->
        <section id="dark-mode">
            <h2><i class="bi bi-moon" aria-hidden="true"></i> Modo Oscuro</h2>
            <div class="callout warning">
                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                <div><strong>Estado actual:</strong> Canvas <strong>NO tiene modo oscuro nativo</strong> para la web (hasta 2026). Las apps móviles (iOS/Android) sí lo soportan.</div>
            </div>
            <h3>Cómo funciona en móvil</h3>
            <ul class="docs-list">
                <li>Invierte el esquema de colores claro a oscuro</li>
                <li>Usa tonos oscuros refinados (no negro puro)</li>
                <li>Transforma los colores institucionales</li>
                <li><strong>NO ajusta</strong> colores hardcodeados en contenido personalizado</li>
            </ul>
            <div class="practice-card good">
                <h4><i class="bi bi-check-circle" aria-hidden="true"></i> Hacer</h4>
                <ul>
                    <li>Heredar colores del contenedor padre</li>
                    <li>Asegurar contraste suficiente en ambos modos</li>
                    <li>Probar invirtiendo colores manualmente</li>
                </ul>
            </div>
            <div class="practice-card bad">
                <h4><i class="bi bi-x-circle" aria-hidden="true"></i> Evitar</h4>
                <ul>
                    <li><code>color: #000000</code> o <code>color: black</code></li>
                    <li><code>background-color: #ffffff</code> o <code>background: white</code></li>
                    <li>Depender de <code>prefers-color-scheme</code> en web</li>
                </ul>
            </div>
            <h3>Estrategia para proyectos</h3>
            <p>Usar variables de Canvas (<code>--ic-brand-*</code>) para que los diseños se adapten automáticamente:</p>
            <pre><code>/* Usar variables de Canvas con fallback */
.mi-titulo {
  color: var(--ic-brand-font-color-dark, #2D3B45);
}
.mi-link {
  color: var(--ic-link-color, #0374B5);
}
.mi-boton {
  background: var(--ic-brand-primary, #0374B5);
  color: #fff;
}</code></pre>
        </section>

        <!-- ════════════ DARK MODE EN CANVAS WEB ════════════ -->
        <section id="dark-mode-web-canvas">
            <h2><i class="bi bi-moon-stars" aria-hidden="true"></i> Dark mode en Canvas LMS Web</h2>

            <div class="callout warning">
                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                <div>
                    <strong>Caso reportado:</strong> Un usuario que ingresó al curso desde un navegador web vio los contenidos del curso en modo oscuro sin haber activado nada en Canvas. El resto de la interfaz de Canvas seguía en modo claro, generando una inconsistencia visual.
                </div>
            </div>

            <h3>¿Por qué ocurre?</h3>
            <p>El CSS del proyecto incluye una regla <code>@media (prefers-color-scheme: dark)</code> que aplica estilos oscuros automáticamente cuando el <strong>sistema operativo del usuario</strong> tiene modo oscuro habilitado. Esto lo resuelve el navegador y no puede desactivarse desde Canvas ni desde el CSS una vez la regla está presente.</p>
            <p>Como Canvas LMS web <strong>no tiene soporte nativo para dark mode</strong>, el resultado es una página donde:</p>
            <ul class="docs-list">
                <li>Los contenidos del curso (nuestro CSS) se ven en modo oscuro.</li>
                <li>La interfaz de Canvas (barra superior, menú lateral, gradebook, RCE, LTI) se mantiene en modo claro.</li>
                <li>Los recursos embebidos (imágenes, iframes, LTI) muchas veces no respetan el modo oscuro.</li>
            </ul>

            <h3>¿Es un error de Canvas?</h3>
            <p>No. Canvas simplemente <strong>no contempla dark mode en su versión web</strong> — solo ofrece un toggle de <em>alto contraste</em> en la configuración de usuario (que no es dark mode, es un contraste aumentado en modo claro).</p>

            <h3>¿Es un error del CSS generado?</h3>
            <p>Tampoco. La regla <code>@media (prefers-color-scheme: dark)</code> funciona como debe: respeta la preferencia del sistema operativo. El problema es que esa preferencia aplica <strong>solo al CSS del proyecto</strong>, no a la interfaz de Canvas que lo rodea.</p>

            <h3>Solución implementada en SkinLab</h3>
            <div class="callout info">
                <i class="bi bi-info-circle" aria-hidden="true"></i>
                <div>
                    <strong>El compilador desactiva el dark mode automático solo en desktop</strong>, manteniéndolo en móvil. Canvas tiene dos slots de CSS (uno desktop y otro móvil/app) y SkinLab los aprovecha.
                </div>
            </div>

            <h4>¿Cómo funciona?</h4>
            <ul class="docs-list">
                <li><strong>Desktop (<code>slug-desktop.css</code>):</strong> el compilador elimina automáticamente el bloque <code>@media (prefers-color-scheme: dark)</code>. Los usuarios en navegador de escritorio nunca verán dark mode automático, manteniendo consistencia con la interfaz clara de Canvas.</li>
                <li><strong>Móvil / App (<code>slug-mobile.css</code>):</strong> conserva el bloque <code>@media (prefers-color-scheme: dark)</code>. Los usuarios en la app Canvas Student ven dark mode cuando su sistema lo tiene habilitado, coherente con el resto de apps del dispositivo.</li>
                <li><strong>Ambiente de desarrollo:</strong> sigue disponible el toggle manual (<code>html[data-theme="dark"]</code>) en el dashboard de SkinLab para previsualizar dark mode durante el desarrollo. El compilador lo elimina siempre.</li>
            </ul>

            <h4>Dónde vive la lógica</h4>
            <p>El compilador (<code>app/Helpers/CssCompiler.php</code>) aplica dos reglas al generar el archivo desktop: una elimina el bloque cuando tiene el comentario <code>DARK MODE — Canvas real</code>, la otra (fallback) elimina cualquier <code>@media (prefers-color-scheme: dark)</code> sin comentario.</p>

            <h4>Verificación</h4>
            <p>Probado con dark mode del sistema activado:</p>
            <ul class="docs-list">
                <li>Navegador de escritorio → contenido del curso en modo claro (correcto, consistente con Canvas).</li>
                <li>App móvil con dark mode en el dispositivo → contenido del curso en modo oscuro.</li>
                <li>App móvil con modo claro → contenido del curso en modo claro.</li>
            </ul>

            <h3>Opciones descartadas</h3>
            <div class="practice-card bad">
                <h4><i class="bi bi-x-circle" aria-hidden="true"></i> Mantener dark mode automático también en desktop</h4>
                <ul>
                    <li>Era el estado original hasta que se reportó el caso.</li>
                    <li>Genera inconsistencia visual entre el contenido del curso (oscuro) y la interfaz de Canvas (clara).</li>
                </ul>
            </div>

            <div class="practice-card bad">
                <h4><i class="bi bi-x-circle" aria-hidden="true"></i> Dark mode opt-in con toggle en el contenido</h4>
                <ul>
                    <li>Requiere JavaScript que Canvas no permite en páginas wiki.</li>
                    <li>Tendría que subirse como JS personalizado en el Theme Editor a nivel de cuenta.</li>
                </ul>
            </div>
        </section>

        <!-- ════════════ ALTO CONTRASTE ════════════ -->
        <section id="high-contrast">
            <h2><i class="bi bi-circle-half" aria-hidden="true"></i> Alto Contraste</h2>
            <p>Canvas incluye un modo <strong>High Contrast UI</strong> que los usuarios activan desde <strong>Cuenta > Configuración</strong>.</p>
            <ul class="docs-list">
                <li>Aumenta el contraste en texto, botones y elementos UI</li>
                <li>Apunta a WCAG 2.1 Nivel AAA</li>
                <li>Puede sobreescribir <code>--ic-brand-primary</code> y <code>--ic-link-color</code></li>
            </ul>
        </section>

        <!-- ════════════ TIPOGRAFÍA ════════════ -->
        <section id="tipografia">
            <h2><i class="bi bi-fonts" aria-hidden="true"></i> Tipografía</h2>
            <h3>Font stack por defecto</h3>
            <pre><code>"LatoWeb", "Lato", "Helvetica Neue", Helvetica, Arial, sans-serif</code></pre>
            <h3>Uso de fuentes web</h3>
            <table class="docs-table">
                <thead><tr><th>Contexto</th><th>¿Fuentes web?</th><th>Detalle</th></tr></thead>
                <tbody>
                    <tr><td>Contenido de páginas</td><td><span class="badge-doc no">No</span></td><td>No se pueden cargar @font-face ni Google Fonts</td></tr>
                    <tr><td>CSS de admin</td><td><span class="badge-doc yes">Sí</span></td><td>@font-face y @import en archivos subidos a nivel de cuenta</td></tr>
                    <tr><td>Inline font-family</td><td><span class="badge-doc partial">Parcial</span></td><td>Solo fuentes del sistema ya instaladas</td></tr>
                </tbody>
            </table>
        </section>

        <!-- ════════════ BUENAS PRÁCTICAS ════════════ -->
        <section id="buenas-practicas">
            <h2><i class="bi bi-lightbulb" aria-hidden="true"></i> Buenas Prácticas</h2>
            <div class="practice-grid">
                <div class="practice-card good">
                    <h4><i class="bi bi-check-circle" aria-hidden="true"></i> Estructura</h4>
                    <ul>
                        <li>Usar <code>&lt;h2&gt;</code> como encabezado principal (no h1)</li>
                        <li>Prefijar selectores con <code>#content-body</code></li>
                        <li>Solo HTML sin html, head, body</li>
                        <li>Jerarquía semántica de encabezados</li>
                    </ul>
                </div>
                <div class="practice-card good">
                    <h4><i class="bi bi-check-circle" aria-hidden="true"></i> CSS</h4>
                    <ul>
                        <li>Usar <code>var(--ic-brand-font-color-dark)</code> para texto</li>
                        <li>Usar <code>var(--ic-link-color)</code> para enlaces</li>
                        <li>Usar <code>var(--ic-brand-primary)</code> para acentos</li>
                        <li>Unidades relativas: em, rem, %</li>
                    </ul>
                </div>
                <div class="practice-card good">
                    <h4><i class="bi bi-check-circle" aria-hidden="true"></i> Accesibilidad</h4>
                    <ul>
                        <li>Atributos <code>alt</code> en imágenes</li>
                        <li>Atributos <code>role</code> y <code>aria-*</code></li>
                        <li>Contraste mínimo 4.5:1 para texto</li>
                        <li>No depender solo del color</li>
                    </ul>
                </div>
                <div class="practice-card bad">
                    <h4><i class="bi bi-x-circle" aria-hidden="true"></i> Evitar</h4>
                    <ul>
                        <li>No usar <code>&lt;h1&gt;</code> — Canvas lo elimina</li>
                        <li>No usar <code>&lt;style&gt;</code> en contenido</li>
                        <li>No hardcodear colores de texto</li>
                        <li>No depender de box-shadow, transition, animation</li>
                        <li>No asumir fuentes web disponibles</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- ════════════ ROLES Y ACCESO ════════════ -->
        <section id="roles">
            <h2><i class="bi bi-shield-lock" aria-hidden="true"></i> Roles y Acceso</h2>
            <table class="docs-table">
                <thead><tr><th>Rol</th><th>Permisos</th></tr></thead>
                <tbody>
                    <tr><td><strong>Administrador</strong></td><td>Crear, editar, eliminar proyectos. Gestionar usuarios. Compilar CSS. Exportar.</td></tr>
                    <tr><td><strong>Editor</strong></td><td>Crear y editar proyectos propios. Compilar CSS. Exportar.</td></tr>
                    <tr><td><strong>Invitado</strong></td><td>Ver proyectos, código fuente y documentación. Solo lectura.</td></tr>
                </tbody>
            </table>
        </section>

        <!-- ════════════ FLUJO DE TRABAJO ════════════ -->
        <section id="flujo-trabajo">
            <h2><i class="bi bi-diagram-3" aria-hidden="true"></i> Flujo de Trabajo</h2>
            <h3>Estructura de archivos</h3>
            <table class="docs-table">
                <thead><tr><th>Archivo</th><th>Descripción</th></tr></thead>
                <tbody>
                    <tr><td><code>slug-master.css</code></td><td>Archivo de trabajo. Se edita aquí y se compila.</td></tr>
                    <tr><td><code>slug-mobile.css</code></td><td>Generado. Estilos hasta 992px. Conserva <code>@media (prefers-color-scheme: dark)</code>. Para Canvas móvil/tablet.</td></tr>
                    <tr><td><code>slug-desktop.css</code></td><td>Generado. Todos los estilos excepto <code>@media (prefers-color-scheme: dark)</code>. Para Canvas desktop.</td></tr>
                </tbody>
            </table>
            <h3>Proceso</h3>
            <ol class="docs-list">
                <li>Crear proyecto desde <strong>Admin > Proyectos > Nuevo</strong></li>
                <li>Editar <code>master.css</code> con los estilos del tema</li>
                <li>Editar las páginas HTML del proyecto</li>
                <li>Previsualizar en el dashboard (desktop y móvil)</li>
                <li>Compilar CSS desde el toolbar o Admin</li>
                <li>Copiar el código limpio desde el visor de código</li>
                <li>Subir a Canvas LMS</li>
            </ol>
            <h3>Variables del proyecto</h3>
            <p>Solo cambia los 2 colores <code>-base</code> al inicio del master. Todo lo demás se deriva automáticamente:</p>
            <ul class="docs-list">
                <li><code>--ct-primary-base</code>, <code>--ct-secondary-base</code> — colores principales</li>
                <li><code>--ct-primary</code>, <code>--ct-secondary</code>, etc. — colores de uso (se aclaran en dark mode)</li>
                <li><code>--ct-neutral-*</code> — paleta de neutros tintados con <code>color-mix()</code></li>
                <li><code>--ct-bg</code>, <code>--ct-text</code>, <code>--ct-link</code>, <code>--ct-border</code> — tokens semánticos</li>
            </ul>
        </section>

        <!-- ════════════ FUENTES ════════════ -->
        <section id="fuentes">
            <h2><i class="bi bi-link-45deg" aria-hidden="true"></i> Fuentes de Referencia</h2>
            <ul class="docs-list sources">
                <li><a href="https://community.instructure.com/t5/Canvas-Resource-Documents/Canvas-HTML-Editor-Allowlist/ta-p/387066" target="_blank" rel="noopener">Canvas HTML Editor Allowlist – Instructure Community</a></li>
                <li><a href="https://github.com/instructure/canvas-lms/blob/master/gems/canvas_sanitize/lib/canvas_sanitize/canvas_sanitize.rb" target="_blank" rel="noopener">canvas_sanitize.rb – GitHub Source</a></li>
                <li><a href="https://canvas.instructure.com/doc/api/brand_configs.html" target="_blank" rel="noopener">Brand Configs API Documentation</a></li>
                <li><a href="https://community.canvaslms.com/t5/Canvas-Basics-Guide/How-do-I-enable-the-high-contrast-user-interface-in-Canvas/ta-p/615334" target="_blank" rel="noopener">High Contrast UI – Canvas Guide</a></li>
                <li><a href="https://community.canvaslms.com/t5/Admin-Guide/How-do-I-upload-custom-JavaScript-and-CSS-files-to-an-account/ta-p/253" target="_blank" rel="noopener">Upload Custom CSS/JS – Admin Guide</a></li>
            </ul>
        </section>

    </main>
</div>
