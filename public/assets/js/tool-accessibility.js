/**
 * SkinLab – Tool: Accesibilidad
 * Evalúa páginas del proyecto con Sa11y (tooltips visuales + panel).
 */

(function () {
    'use strict';

    var sa11yInstance = null;
    var sa11yLoaded = false;

    window.renderAccessibilityPage = function (container, projectSlug, contentPages) {
        var html =
            '<section class="tool-a11y">' +
                '<div class="tool-section">' +
                    '<h2>Evaluación de Accesibilidad</h2>' +
                    '<p class="tool-desc">Selecciona una página para evaluar su accesibilidad con Sa11y (WCAG 2.1 AA). ' +
                    'Los errores se mostrarán directamente sobre el contenido con tooltips visuales.</p>' +
                '</div>' +
                '<div class="tool-section">' +
                    '<div class="a11y-pages" id="a11y-pages">';

        contentPages.forEach(function (page) {
            html += '<button class="a11y-page-btn" data-slug="' + escapeHtml(page.slug) + '">' +
                '<i class="bi bi-file-earmark" aria-hidden="true"></i> ' +
                escapeHtml(page.name) +
            '</button>';
        });

        html += '</div></div>' +
            '<div class="tool-section">' +
                '<div id="a11y-content-area" class="a11y-content-area">' +
                    '<p class="tool-desc">Selecciona una página para cargar su contenido y evaluarlo.</p>' +
                '</div>' +
            '</div>' +
        '</section>';

        container.innerHTML = html;

        loadSa11y(function () {
            bindPageButtons(projectSlug);
        });
    };

    function loadSa11y(callback) {
        if (sa11yLoaded) {
            callback();
            return;
        }

        // CSS
        if (!document.getElementById('sa11y-css')) {
            var link = document.createElement('link');
            link.id = 'sa11y-css';
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/gh/ryersondmp/sa11y@4.4.1/dist/css/sa11y.min.css';
            document.head.appendChild(link);
        }

        // JS: español + core
        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/combine/gh/ryersondmp/sa11y@4.4.1/dist/js/lang/es.umd.js,gh/ryersondmp/sa11y@4.4.1/dist/js/sa11y.umd.min.js';
        script.onload = function () {
            sa11yLoaded = true;
            callback();
        };
        script.onerror = function () {
            var area = document.getElementById('a11y-content-area');
            if (area) area.innerHTML = '<p class="tool-error">Error al cargar Sa11y desde CDN.</p>';
        };
        document.body.appendChild(script);
    }

    function bindPageButtons(projectSlug) {
        document.querySelectorAll('.a11y-page-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.a11y-page-btn').forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
                loadAndEvaluate(projectSlug, btn.dataset.slug);
            });
        });
    }

    function loadAndEvaluate(projectSlug, pageSlug) {
        var area = document.getElementById('a11y-content-area');
        area.innerHTML = '<p class="tool-loading"><i class="bi bi-arrow-repeat" aria-hidden="true"></i> Cargando contenido...</p>';

        destroySa11y();

        api('/api/content?project=' + encodeURIComponent(projectSlug) + '&page=' + encodeURIComponent(pageSlug))
            .then(function (data) {
                if (data.error) {
                    area.innerHTML = '<p class="tool-error">' + escapeHtml(data.error) + '</p>';
                    return;
                }

                area.innerHTML =
                    '<div id="sa11y-check-area" class="a11y-check-area">' +
                        (data.html || '<p>Página sin contenido.</p>') +
                    '</div>';

                // CSS del proyecto para evaluar con estilos reales
                loadProjectCssForEval(data);

                // Iniciar Sa11y
                initSa11y();
            })
            .catch(function (err) {
                area.innerHTML = '<p class="tool-error">' + escapeHtml(err.message) + '</p>';
            });
    }

    function loadProjectCssForEval(data) {
        var prev = document.getElementById('a11y-project-css');
        if (prev) prev.remove();

        if (data.cssPath) {
            var link = document.createElement('link');
            link.id = 'a11y-project-css';
            link.rel = 'stylesheet';
            link.href = data.cssPath + '?t=' + Date.now();
            document.head.appendChild(link);
        }
    }

    function initSa11y() {
        if (typeof Sa11y === 'undefined' || typeof Sa11yLangEs === 'undefined') {
            return;
        }

        requestAnimationFrame(function () {
            try {
                Sa11y.Lang.addI18n(Sa11yLangEs.strings);
                sa11yInstance = new Sa11y.Sa11y({
                    checkRoot: '#sa11y-check-area',
                    panelPosition: 'top-left',
                    detectSPArouting: false,
                });
            } catch (e) {
                console.error('Sa11y init error:', e);
            }
        });
    }

    function destroySa11y() {
        if (sa11yInstance && typeof sa11yInstance.destroy === 'function') {
            try { sa11yInstance.destroy(); } catch (e) { /* ignore */ }
            sa11yInstance = null;
        }
        document.querySelectorAll('.sa11y-annotation, #sa11y-panel, #sa11y-panel-container, [class*="sa11y-"]').forEach(function (el) {
            el.remove();
        });
        var prevCss = document.getElementById('a11y-project-css');
        if (prevCss) prevCss.remove();
    }

})();
