/**
 * SkinLab – Tool: Colores
 * Genera página de colores con The Color API + validación de contraste.
 */

(function () {
    'use strict';

    var API_URL = 'https://www.thecolorapi.com/scheme';

    window.renderColorsPage = function (container, projectData) {
        var primary = projectData.color_primary || '#0374B5';
        var secondary = projectData.color_secondary || '#2D3B45';

        container.innerHTML =
            '<section class="tool-colors">' +
                '<div class="tool-section">' +
                    '<h2>Colores del proyecto</h2>' +
                    '<div class="color-swatches" id="project-swatches"></div>' +
                '</div>' +
                '<div class="tool-section">' +
                    '<h2>Propuesta Light</h2>' +
                    '<div class="color-swatches" id="light-palette"></div>' +
                '</div>' +
                '<div class="tool-section">' +
                    '<h2>Propuesta Dark</h2>' +
                    '<div class="color-swatches" id="dark-palette"></div>' +
                '</div>' +
                '<div class="tool-section">' +
                    '<h2>Tipografía y Botones</h2>' +
                    '<div class="tool-row">' +
                        '<div class="tool-preview-card" id="preview-light"></div>' +
                        '<div class="tool-preview-card tool-preview-dark" id="preview-dark"></div>' +
                    '</div>' +
                '</div>' +
                '<div class="tool-section">' +
                    '<h2>Validación de contraste</h2>' +
                    '<div id="contrast-results"></div>' +
                '</div>' +
            '</section>';

        // Colores del proyecto
        renderProjectSwatches(primary, secondary);

        // Consultar API para propuestas
        fetchPalette(primary, 'monochrome-light', 6, 'light-palette');
        fetchPalette(secondary, 'monochrome-dark', 6, 'dark-palette');

        // Preview tipografía y botones
        renderPreviewCards(primary, secondary);

        // Validación de contraste
        renderContrastResults(primary, secondary);
    };

    function renderProjectSwatches(primary, secondary) {
        var el = document.getElementById('project-swatches');
        if (!el) return;
        el.innerHTML =
            swatch(primary, 'Primario') +
            swatch(secondary, 'Secundario') +
            swatch(lighten(primary, 0.3), 'Primario Light') +
            swatch(darken(primary, 0.3), 'Primario Dark') +
            swatch(lighten(secondary, 0.3), 'Secundario Light') +
            swatch(darken(secondary, 0.3), 'Secundario Dark');
    }

    function fetchPalette(hexColor, mode, count, targetId) {
        var hex = hexColor.replace('#', '');
        var el = document.getElementById(targetId);
        if (!el) return;
        el.innerHTML = '<p class="tool-loading">Consultando The Color API...</p>';

        fetch(API_URL + '?hex=' + hex + '&mode=' + mode + '&count=' + count + '&format=json')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var html = '';
                if (data.colors) {
                    data.colors.forEach(function (c) {
                        html += swatch(c.hex.value, c.name.value);
                    });
                }
                el.innerHTML = html || '<p>Sin resultados</p>';
            })
            .catch(function () {
                el.innerHTML = '<p class="tool-error">Error al consultar la API</p>';
            });
    }

    function renderPreviewCards(primary, secondary) {
        var lightBg = '#FFFFFF';
        var darkBg = '#1E1E2E';
        var lightText = '#2D3B45';
        var darkText = '#E0E0E0';

        document.getElementById('preview-light').innerHTML =
            '<h3>Modo Light</h3>' +
            '<div class="tool-preview-body" style="background:' + lightBg + ';color:' + lightText + ';">' +
                '<h4 style="color:' + primary + ';">Título con color primario</h4>' +
                '<p>Texto del cuerpo sobre fondo claro. Este es un ejemplo de cómo se verá la tipografía.</p>' +
                '<p><a href="#" style="color:' + primary + ';">Enlace de ejemplo</a> con color primario.</p>' +
                '<div class="tool-preview-buttons">' +
                    '<span class="tool-btn" style="background:' + primary + ';color:#fff;">Botón Primario</span>' +
                    '<span class="tool-btn" style="background:' + secondary + ';color:#fff;">Botón Secundario</span>' +
                    '<span class="tool-btn tool-btn-outline" style="border-color:' + primary + ';color:' + primary + ';">Outline</span>' +
                '</div>' +
                '<div class="tool-preview-card-sample" style="border-color:' + lighten(secondary, 0.7) + ';">' +
                    '<h5 style="color:' + secondary + ';">Tarjeta de ejemplo</h5>' +
                    '<p style="color:' + lightText + ';opacity:0.7;">Contenido de la tarjeta con texto secundario.</p>' +
                '</div>' +
            '</div>';

        document.getElementById('preview-dark').innerHTML =
            '<h3>Modo Dark</h3>' +
            '<div class="tool-preview-body" style="background:' + darkBg + ';color:' + darkText + ';">' +
                '<h4 style="color:' + lighten(primary, 0.3) + ';">Título con color primario</h4>' +
                '<p>Texto del cuerpo sobre fondo oscuro. Este es un ejemplo de cómo se verá la tipografía.</p>' +
                '<p><a href="#" style="color:' + lighten(primary, 0.3) + ';">Enlace de ejemplo</a> con color primario.</p>' +
                '<div class="tool-preview-buttons">' +
                    '<span class="tool-btn" style="background:' + lighten(primary, 0.15) + ';color:#1E1E2E;">Botón Primario</span>' +
                    '<span class="tool-btn" style="background:' + lighten(secondary, 0.15) + ';color:#1E1E2E;">Botón Secundario</span>' +
                    '<span class="tool-btn tool-btn-outline" style="border-color:' + lighten(primary, 0.3) + ';color:' + lighten(primary, 0.3) + ';">Outline</span>' +
                '</div>' +
                '<div class="tool-preview-card-sample" style="border-color:rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);">' +
                    '<h5 style="color:' + lighten(secondary, 0.4) + ';">Tarjeta de ejemplo</h5>' +
                    '<p style="color:' + darkText + ';opacity:0.7;">Contenido de la tarjeta con texto secundario.</p>' +
                '</div>' +
            '</div>';
    }

    function renderContrastResults(primary, secondary) {
        var el = document.getElementById('contrast-results');
        if (!el) return;

        var combos = [
            { name: 'Primario sobre blanco', fg: primary, bg: '#FFFFFF' },
            { name: 'Secundario sobre blanco', fg: secondary, bg: '#FFFFFF' },
            { name: 'Blanco sobre primario', fg: '#FFFFFF', bg: primary },
            { name: 'Blanco sobre secundario', fg: '#FFFFFF', bg: secondary },
            { name: 'Primario (light) sobre dark bg', fg: lighten(primary, 0.3), bg: '#1E1E2E' },
            { name: 'Secundario (light) sobre dark bg', fg: lighten(secondary, 0.3), bg: '#1E1E2E' },
        ];

        var html = '<table class="tool-contrast-table">' +
            '<thead><tr><th>Combinación</th><th>Muestra</th><th>Ratio</th><th>AA</th><th>AAA</th></tr></thead><tbody>';

        combos.forEach(function (c) {
            var ratio = contrastRatio(c.fg, c.bg);
            var passAA = ratio >= 4.5;
            var passAAA = ratio >= 7;
            html += '<tr>' +
                '<td>' + escapeHtml(c.name) + '</td>' +
                '<td><span class="tool-contrast-sample" style="background:' + c.bg + ';color:' + c.fg + ';">Texto</span></td>' +
                '<td><strong>' + ratio.toFixed(2) + ':1</strong></td>' +
                '<td><span class="tool-badge ' + (passAA ? 'tool-badge-pass' : 'tool-badge-fail') + '">' + (passAA ? 'Pasa' : 'Falla') + '</span></td>' +
                '<td><span class="tool-badge ' + (passAAA ? 'tool-badge-pass' : 'tool-badge-fail') + '">' + (passAAA ? 'Pasa' : 'Falla') + '</span></td>' +
                '</tr>';
        });

        html += '</tbody></table>';
        el.innerHTML = html;
    }

    // ── Utilidades de color ──

    function hexToRgb(hex) {
        hex = hex.replace('#', '');
        return {
            r: parseInt(hex.substring(0, 2), 16),
            g: parseInt(hex.substring(2, 4), 16),
            b: parseInt(hex.substring(4, 6), 16)
        };
    }

    function rgbToHex(r, g, b) {
        return '#' + [r, g, b].map(function (v) {
            var h = Math.max(0, Math.min(255, Math.round(v))).toString(16);
            return h.length === 1 ? '0' + h : h;
        }).join('');
    }

    function lighten(hex, amount) {
        var rgb = hexToRgb(hex);
        return rgbToHex(
            rgb.r + (255 - rgb.r) * amount,
            rgb.g + (255 - rgb.g) * amount,
            rgb.b + (255 - rgb.b) * amount
        );
    }

    function darken(hex, amount) {
        var rgb = hexToRgb(hex);
        return rgbToHex(
            rgb.r * (1 - amount),
            rgb.g * (1 - amount),
            rgb.b * (1 - amount)
        );
    }

    function luminance(hex) {
        var rgb = hexToRgb(hex);
        var a = [rgb.r, rgb.g, rgb.b].map(function (v) {
            v = v / 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2];
    }

    function contrastRatio(fg, bg) {
        var l1 = luminance(fg);
        var l2 = luminance(bg);
        var lighter = Math.max(l1, l2);
        var darker = Math.min(l1, l2);
        return (lighter + 0.05) / (darker + 0.05);
    }

    function swatch(color, name) {
        return '<div class="tool-swatch">' +
            '<div class="tool-swatch-color" style="background:' + color + ';"></div>' +
            '<div class="tool-swatch-info">' +
                '<code>' + color.toUpperCase() + '</code>' +
                '<small>' + escapeHtml(name) + '</small>' +
            '</div>' +
        '</div>';
    }

})();
