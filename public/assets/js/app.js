/**
 * SkinLab – Base JavaScript
 * Utilidades globales y configuración.
 */

(function () {
    'use strict';

    // ── CSRF Token para peticiones AJAX ──
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var CSRF_TOKEN = csrfMeta ? csrfMeta.getAttribute('content') : '';

    /**
     * Wrapper para fetch con CSRF automático.
     * @param {string} url
     * @param {object} options
     * @returns {Promise<Response>}
     */
    window.api = function (url, options) {
        options = options || {};
        options.headers = Object.assign({
            'Content-Type': 'application/json',
            'X-CSRF-Token': CSRF_TOKEN,
            'Accept': 'application/json'
        }, options.headers || {});

        return fetch(url, options).then(function (resp) {
            if (!resp.ok) {
                return resp.json().then(function (data) {
                    throw new Error(data.error || 'Error ' + resp.status);
                });
            }
            return resp.json();
        });
    };

    /**
     * Copiar texto al portapapeles.
     * @param {string} text
     * @returns {Promise<void>}
     */
    window.copyToClipboard = function (text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }

        // Fallback
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0;pointer-events:none;';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        return Promise.resolve();
    };

    /**
     * Escapar HTML.
     * @param {string} str
     * @returns {string}
     */
    window.escapeHtml = function (str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

})();
