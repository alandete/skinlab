/**
 * SkinLab – Base JavaScript
 * Utilidades globales, dropdowns, toasts.
 */

(function () {
    'use strict';

    // ── CSRF Token para peticiones AJAX ──
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var CSRF_TOKEN = csrfMeta ? csrfMeta.getAttribute('content') : '';

    /**
     * Wrapper para fetch con CSRF automático.
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
                    if (data._csrf) updateCsrfToken(data._csrf);
                    throw new Error(data.error || 'Error ' + resp.status);
                });
            }
            return resp.json().then(function (data) {
                if (data._csrf) updateCsrfToken(data._csrf);
                return data;
            });
        });
    };

    /**
     * Copiar texto al portapapeles.
     */
    window.copyToClipboard = function (text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
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
     */
    window.escapeHtml = function (str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    /**
     * Leer traducciones desde #js-lang data attributes.
     */
    window.getLang = function () {
        var el = document.getElementById('js-lang');
        return el ? el.dataset : {};
    };

    /**
     * Toast notification.
     */
    window.showToast = function (message, type) {
        var container = document.getElementById('toast-container');
        if (!container) return;
        var toast = document.createElement('div');
        toast.className = 'sl-toast sl-toast-' + (type || 'success');
        toast.innerHTML = '<i class="bi ' + (type === 'error' ? 'bi-x-circle' : 'bi-check-circle') + '" aria-hidden="true"></i> ' + escapeHtml(message);
        container.appendChild(toast);
        setTimeout(function () {
            toast.style.animation = 'toastOut 0.35s ease forwards';
            setTimeout(function () { toast.remove(); }, 350);
        }, 3000);
    };

    function updateCsrfToken(newToken) {
        CSRF_TOKEN = newToken;
        if (csrfMeta) csrfMeta.setAttribute('content', newToken);
    }

    // ── Dropdown menus (global) ──
    document.addEventListener('click', function (e) {
        var toggleBtn = e.target.closest('.btn-dropdown-toggle');

        // Cerrar todos los dropdowns que no contienen el target
        document.querySelectorAll('.dropdown-menu.open').forEach(function (menu) {
            if (!menu.parentElement.contains(e.target)) {
                menu.classList.remove('open');
                var tb = menu.parentElement.querySelector('.btn-dropdown-toggle');
                if (tb) tb.setAttribute('aria-expanded', 'false');
            }
        });

        // Toggle dropdown del botón clickeado
        if (toggleBtn) {
            e.preventDefault();
            e.stopPropagation();
            var menu = toggleBtn.nextElementSibling;
            if (!menu) return;
            var isOpen = menu.classList.contains('open');
            // Cerrar todos primero
            document.querySelectorAll('.dropdown-menu.open').forEach(function (m) {
                m.classList.remove('open');
            });
            if (!isOpen) {
                menu.classList.add('open');
                toggleBtn.setAttribute('aria-expanded', 'true');
            } else {
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        }
    });

})();
