/**
 * SkinLab – Sa11y Toolbar Integration
 * Carga Sa11y bajo demanda y evalúa #content-body.
 * Fallback a axe-core si Sa11y no carga.
 */

(function () {
    'use strict';

    var btnA11y = document.getElementById('btn-a11y');
    if (!btnA11y) return;

    var sa11yActive = false;
    var sa11yInstance = null;
    var sa11yLoaded = false;
    var axeLoaded = false;

    btnA11y.addEventListener('click', function () {
        if (sa11yActive) {
            deactivateSa11y();
        } else {
            activateSa11y();
        }
    });

    // Escuchar cambios de página para desactivar Sa11y
    document.addEventListener('contentLoaded', function () {
        if (sa11yActive) {
            deactivateSa11y();
        }
    });

    function activateSa11y() {
        var target = document.getElementById('content-body');
        if (!target || !target.innerHTML.trim()) {
            showToast('Carga una página del proyecto primero', 'error');
            return;
        }

        btnA11y.classList.add('active');
        sa11yActive = true;

        if (sa11yLoaded) {
            initSa11y();
            return;
        }

        showToast('Cargando Sa11y...', 'success');

        // Cargar CSS
        if (!document.getElementById('sa11y-css')) {
            var link = document.createElement('link');
            link.id = 'sa11y-css';
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/gh/ryersondmp/sa11y@4.4.1/dist/css/sa11y.min.css';
            document.head.appendChild(link);
        }

        // Cargar JS (español + core)
        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/combine/gh/ryersondmp/sa11y@4.4.1/dist/js/lang/es.umd.js,gh/ryersondmp/sa11y@4.4.1/dist/js/sa11y.umd.min.js';
        script.onload = function () {
            sa11yLoaded = true;
            initSa11y();
        };
        script.onerror = function () {
            showToast('Sa11y no disponible. Usando axe-core...', 'error');
            loadAxeFallback();
        };
        document.body.appendChild(script);
    }

    function initSa11y() {
        if (typeof Sa11y === 'undefined' || typeof Sa11yLangEs === 'undefined') {
            showToast('Error al inicializar Sa11y', 'error');
            deactivateSa11y();
            return;
        }

        requestAnimationFrame(function () {
            try {
                // Solo crear instancia una vez (custom elements no se pueden re-registrar)
                if (!sa11yInstance) {
                    Sa11y.Lang.addI18n(Sa11yLangEs.strings);
                    sa11yInstance = new Sa11y.Sa11y({
                        checkRoot: '#content-body',
                        panelPosition: 'bottom-left',
                        detectSPArouting: false,
                    });
                }
                // Mostrar todos los elementos de Sa11y
                document.querySelectorAll(
                    'sa11y-control-panel, sa11y-toggle, sa11y-annotation, sa11y-tooltips, ' +
                    '[id^="sa11y-"], [class*="sa11y-"]'
                ).forEach(function (el) {
                    el.style.display = '';
                });

                showToast('Sa11y activado', 'success');
            } catch (e) {
                showToast('Error: ' + e.message, 'error');
                deactivateSa11y();
            }
        });
    }

    function deactivateSa11y() {
        sa11yActive = false;
        btnA11y.classList.remove('active');
        hideSa11yUI();
    }

    function hideSa11yUI() {
        // Ocultar TODOS los elementos generados por Sa11y
        document.querySelectorAll(
            'sa11y-control-panel, sa11y-toggle, sa11y-annotation, sa11y-tooltips, ' +
            'sa11y-dismiss-tooltip, sa11y-heading-label, sa11y-heading-anchor, ' +
            '[id^="sa11y-"], [class*="sa11y-"]'
        ).forEach(function (el) {
            el.style.display = 'none';
        });
    }

    // ── Fallback: axe-core ──

    function loadAxeFallback() {
        if (axeLoaded) {
            runAxe();
            return;
        }

        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/axe-core@4.9.1/axe.min.js';
        script.onload = function () {
            axeLoaded = true;
            runAxe();
        };
        script.onerror = function () {
            showToast('No se pudo cargar ninguna herramienta de accesibilidad', 'error');
            deactivateSa11y();
        };
        document.body.appendChild(script);
    }

    function runAxe() {
        var target = document.getElementById('content-body');
        if (!target) return;

        showToast('Evaluando con axe-core...', 'success');

        axe.run(target, {
            runOnly: ['wcag2a', 'wcag2aa', 'best-practice'],
            rules: { 'frame-tested': { enabled: false } }
        }).then(function (results) {
            var v = results.violations.length;
            var p = results.passes.length;

            if (v === 0) {
                showToast('Sin errores de accesibilidad (' + p + ' reglas aprobadas)', 'success');
            } else {
                showToast(v + ' error' + (v > 1 ? 'es' : '') + ' de accesibilidad encontrado' + (v > 1 ? 's' : ''), 'error');

                // Mostrar resumen en consola
                console.group('axe-core: ' + v + ' errores de accesibilidad');
                results.violations.forEach(function (vi) {
                    console.warn('[' + vi.impact + '] ' + vi.help);
                    vi.nodes.forEach(function (n) {
                        console.log('  →', n.html);
                        if (n.failureSummary) console.log('   ', n.failureSummary);
                    });
                });
                console.groupEnd();
            }

            btnA11y.classList.remove('active');
            sa11yActive = false;
        }).catch(function (err) {
            showToast('Error axe-core: ' + err.message, 'error');
            btnA11y.classList.remove('active');
            sa11yActive = false;
        });
    }

})();
