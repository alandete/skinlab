/**
 * SkinLab – Tool: Accesibilidad
 * Evalúa páginas del proyecto con axe-core.
 * Genera reporte detallado por página en tabla.
 */

(function () {
    'use strict';

    var axeLoaded = false;
    var impactES = { critical: 'Crítico', serious: 'Grave', moderate: 'Moderado', minor: 'Menor' };
    var impactOrder = { critical: 0, serious: 1, moderate: 2, minor: 3 };

    window.renderAccessibilityPage = function (container, projectSlug, contentPages) {
        var html =
            '<section class="tool-a11y">' +
                '<div class="tool-section">' +
                    '<h2>Evaluación de Accesibilidad</h2>' +
                    '<p class="tool-desc">Selecciona una página para evaluar su accesibilidad (WCAG 2.1 AA). ' +
                    'El reporte muestra errores, advertencias y cómo corregirlos.</p>' +
                '</div>' +
                '<div class="tool-section">' +
                    '<div class="a11y-pages" id="a11y-pages">';

        contentPages.forEach(function (page) {
            html += '<button class="a11y-page-btn" data-slug="' + escapeHtml(page.slug) + '">' +
                '<i class="bi bi-file-earmark" aria-hidden="true"></i> ' +
                escapeHtml(page.name) +
                '<span class="a11y-status" id="a11y-status-' + escapeHtml(page.slug) + '"></span>' +
            '</button>';
        });

        html += '</div></div>' +
            '<div class="tool-section">' +
                '<div id="a11y-report-area" class="a11y-report-area">' +
                    '<p class="tool-desc">Selecciona una página para ver el resultado.</p>' +
                '</div>' +
            '</div>' +
        '</section>';

        container.innerHTML = html;

        loadAxe(function () {
            bindPageButtons(projectSlug);
        });
    };

    function loadAxe(callback) {
        if (axeLoaded || typeof axe !== 'undefined') {
            axeLoaded = true;
            callback();
            return;
        }

        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/axe-core@4.9.1/axe.min.js';
        script.onload = function () {
            axeLoaded = true;
            callback();
        };
        script.onerror = function () {
            var area = document.getElementById('a11y-report-area');
            if (area) area.innerHTML = '<p class="tool-error">Error al cargar axe-core.</p>';
        };
        document.body.appendChild(script);
    }

    function bindPageButtons(projectSlug) {
        document.querySelectorAll('.a11y-page-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.a11y-page-btn').forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
                evaluatePage(projectSlug, btn.dataset.slug);
            });
        });
    }

    function evaluatePage(projectSlug, pageSlug) {
        var area = document.getElementById('a11y-report-area');
        var statusEl = document.getElementById('a11y-status-' + pageSlug);
        area.innerHTML = '<p class="tool-loading"><i class="bi bi-arrow-repeat" aria-hidden="true"></i> Cargando y evaluando...</p>';

        api('/api/content?project=' + encodeURIComponent(projectSlug) + '&page=' + encodeURIComponent(pageSlug))
            .then(function (data) {
                if (data.error) {
                    area.innerHTML = '<p class="tool-error">' + escapeHtml(data.error) + '</p>';
                    return;
                }

                // Crear contenedor oculto para evaluación
                var evalContainer = document.getElementById('a11y-eval-container');
                if (!evalContainer) {
                    evalContainer = document.createElement('div');
                    evalContainer.id = 'a11y-eval-container';
                    evalContainer.style.cssText = 'position:absolute;left:-9999px;top:-9999px;width:1024px;';
                    document.body.appendChild(evalContainer);
                }
                evalContainer.innerHTML = data.html || '';

                // Cargar CSS del proyecto
                loadProjectCss(data);

                // Esperar que el CSS cargue
                setTimeout(function () {
                    runAxeCheck(evalContainer, area, pageSlug, statusEl);
                }, 500);
            })
            .catch(function (err) {
                area.innerHTML = '<p class="tool-error">' + escapeHtml(err.message) + '</p>';
            });
    }

    function loadProjectCss(data) {
        var prev = document.getElementById('a11y-eval-css');
        if (prev) prev.remove();

        if (data.cssPath) {
            var link = document.createElement('link');
            link.id = 'a11y-eval-css';
            link.rel = 'stylesheet';
            link.href = data.cssPath + '?t=' + Date.now();
            document.head.appendChild(link);
        }
    }

    function runAxeCheck(target, reportArea, pageSlug, statusEl) {
        axe.run(target, {
            runOnly: ['wcag2a', 'wcag2aa', 'best-practice'],
            rules: { 'frame-tested': { enabled: false } }
        }).then(function (results) {
            renderReport(reportArea, results, pageSlug);
            updateStatus(statusEl, results.violations.length);
        }).catch(function (err) {
            reportArea.innerHTML = '<p class="tool-error">Error: ' + escapeHtml(err.message) + '</p>';
        });
    }

    function renderReport(area, results, pageSlug) {
        var v = results.violations.length;
        var p = results.passes.length;
        var inc = results.incomplete.length;

        var html = '<div class="a11y-report">';

        // Resumen
        html += '<div class="a11y-summary">';
        if (v === 0) {
            html += '<div class="a11y-badge-pass"><i class="bi bi-check-circle" aria-hidden="true"></i> Sin errores</div>';
        } else {
            html += '<div class="a11y-badge-fail"><i class="bi bi-exclamation-circle" aria-hidden="true"></i> ' + v + ' error' + (v > 1 ? 'es' : '') + '</div>';
        }
        html += '<div class="a11y-badge-info"><i class="bi bi-check" aria-hidden="true"></i> ' + p + ' reglas aprobadas</div>';
        if (inc > 0) {
            html += '<div class="a11y-badge-warn"><i class="bi bi-question-circle" aria-hidden="true"></i> ' + inc + ' revisión manual</div>';
        }
        html += '</div>';

        // Errores
        if (v > 0) {
            // Ordenar por impacto
            var sorted = results.violations.slice().sort(function (a, b) {
                return (impactOrder[a.impact] || 4) - (impactOrder[b.impact] || 4);
            });

            html += '<h3 class="a11y-section-title">Errores encontrados</h3>';
            sorted.forEach(function (vi) {
                var impact = impactES[vi.impact] || vi.impact;
                html += '<details class="a11y-detail">' +
                    '<summary>' +
                        '<span class="a11y-impact a11y-impact-' + vi.impact + '">' + impact + '</span> ' +
                        escapeHtml(vi.help) +
                        ' <small class="a11y-count">(' + vi.nodes.length + ')</small>' +
                    '</summary>' +
                    '<div class="a11y-detail-body">' +
                        '<p class="a11y-description">' + escapeHtml(vi.description) + '</p>' +
                        '<p class="a11y-tags">';

                vi.tags.forEach(function (t) {
                    html += '<span class="a11y-tag">' + escapeHtml(t) + '</span> ';
                });

                html += '</p>';

                // Elementos afectados
                html += '<table class="a11y-nodes-table">' +
                    '<thead><tr><th>Elemento</th><th>Corrección</th></tr></thead><tbody>';

                vi.nodes.forEach(function (n) {
                    var fix = (n.failureSummary || '')
                        .replace('Fix any of the following:', 'Corregir alguno:')
                        .replace('Fix all of the following:', 'Corregir todos:')
                        .replace('Element has insufficient color contrast', 'Contraste insuficiente')
                        .replace('Expected contrast ratio of', 'Ratio esperado:')
                        .replace('foreground color:', 'texto:')
                        .replace('background color:', 'fondo:')
                        .replace('font-size:', 'fuente:')
                        .replace('font-weight:', 'peso:')
                        .replace('Element does not have an alt attribute', 'Falta atributo alt')
                        .replace('Element has no title attribute', 'Falta atributo title')
                        .replace('Heading order invalid', 'Orden de encabezados inválido')
                        .replace(/\n/g, '<br>');

                    html += '<tr>' +
                        '<td><code>' + escapeHtml(n.html.substring(0, 120)) + (n.html.length > 120 ? '...' : '') + '</code></td>' +
                        '<td class="a11y-fix">' + fix + '</td>' +
                    '</tr>';
                });

                html += '</tbody></table></div></details>';
            });
        }

        // Revisión manual
        if (inc > 0) {
            html += '<h3 class="a11y-section-title">Requiere revisión manual</h3>';
            results.incomplete.forEach(function (vi) {
                var impact = impactES[vi.impact] || vi.impact;
                html += '<details class="a11y-detail a11y-detail-warn">' +
                    '<summary>' +
                        '<span class="a11y-impact a11y-impact-' + (vi.impact || 'minor') + '">' + impact + '</span> ' +
                        escapeHtml(vi.help) +
                    '</summary>' +
                    '<div class="a11y-detail-body">' +
                        '<p class="a11y-description">' + escapeHtml(vi.description) + '</p>' +
                    '</div></details>';
            });
        }

        html += '</div>';
        area.innerHTML = html;
    }

    function updateStatus(statusEl, violationCount) {
        if (!statusEl) return;
        if (violationCount === 0) {
            statusEl.innerHTML = '<i class="bi bi-check-circle" aria-hidden="true"></i>';
            statusEl.className = 'a11y-status a11y-status-pass';
        } else {
            statusEl.textContent = violationCount;
            statusEl.className = 'a11y-status a11y-status-fail';
        }
    }

})();
