/**
 * SkinLab – Dashboard JavaScript
 * Navegación Canvas, toolbar, estado URL.
 */

(function () {
    'use strict';

    // ── DOM ──
    var dashboard = document.getElementById('sl-dashboard');

    // Toolbar
    var projectSelect = document.getElementById('project-select');
    var btnReload = document.getElementById('btn-reload');
    var btnCompile = document.getElementById('btn-compile');
    var btnExport = document.getElementById('btn-export');

    // Canvas nav
    var canvasNav = document.getElementById('canvas-nav');
    var btnToggleNav = document.getElementById('btn-toggle-nav');

    // Course nav (hamburger toggle) — usa #left-side como Canvas LMS
    var courseNav = document.getElementById('left-side');
    var projectPages = document.getElementById('project-pages');
    var btnToggleCourse = document.getElementById('btn-toggle-course');
    var courseOverlay = document.getElementById('course-overlay');

    // Content
    var pageTitle = document.getElementById('page-title');
    var contentBody = document.getElementById('content-body');
    var breadcrumbProject = document.getElementById('breadcrumb-project');
    var breadcrumbPage = document.getElementById('breadcrumb-page');

    // ── State ──
    var currentProject = null;
    var currentPageSlug = null;
    var currentPageName = null;
    var projectsData = [];
    var projectStyleEl = null;
    var projectDesktopStyleEl = null;
    var projectScriptEl = null;
    var courseNavVisible = true; // visible por defecto en desktop

    // ── Init ──
    // #left-side: display:block (visible) o display:none (oculto), como Canvas LMS
    if (window.innerWidth >= 768) {
        courseNav.style.display = 'block';
        btnToggleCourse.setAttribute('aria-expanded', 'true');
        courseNavVisible = true;
    } else {
        courseNav.style.display = 'none';
        courseNavVisible = false;
    }

    loadProjects().then(function () {
        // 1. Primero intentar desde la URL (/project/slug)
        var state = readState();
        // 2. Si no hay URL, intentar sessionStorage
        if (!state) {
            var saved = sessionStorage.getItem('sl_project');
            var savedPage = sessionStorage.getItem('sl_page');
            if (saved) {
                state = { project: saved, page: savedPage || 'index' };
            }
        }
        if (state && state.project) {
            var proj = projectsData.find(function (p) { return p.slug === state.project && parseInt(p.is_active) === 1; });
            if (proj) {
                selectProject(proj, state.page || 'index');
            }
        }
    });

    // ── Load projects into selector ──
    function loadProjects() {
        return api('/api/projects').then(function (data) {
            projectsData = data.projects || [];
            renderProjectSelect();
        }).catch(function () {
            projectsData = [];
        });
    }

    function renderProjectSelect() {
        while (projectSelect.options.length > 1) {
            projectSelect.remove(1);
        }
        var active = projectsData.filter(function (p) { return parseInt(p.is_active) === 1; });
        active.forEach(function (proj) {
            var opt = document.createElement('option');
            opt.value = proj.slug;
            opt.textContent = proj.name;
            projectSelect.appendChild(opt);
        });
    }

    // ── Project selection ──
    projectSelect.addEventListener('change', function () {
        var slug = projectSelect.value;
        if (!slug) {
            deselectProject();
            return;
        }
        var proj = projectsData.find(function (p) { return p.slug === slug; });
        if (proj) selectProject(proj);
    });

    function selectProject(proj, initialPage) {
        currentProject = proj;
        projectSelect.value = proj.slug;
        btnExport.classList.remove('hidden');

        // Aplicar colores institucionales al nav Canvas
        applyNavColors(proj.nav_bg_color, proj.nav_text_color);

        var pageSlug = initialPage || 'index';
        renderProjectPages(proj, pageSlug);
        loadPage(proj.slug, pageSlug);

        // Mostrar course nav si estaba oculto
        if (!courseNavVisible && window.innerWidth >= 768) {
            toggleCourseNav();
        }
    }

    function deselectProject() {
        currentProject = null;
        currentPageSlug = null;
        currentPageName = null;
        projectSelect.value = '';
        btnExport.classList.add('hidden');
        sessionStorage.removeItem('sl_project');
        sessionStorage.removeItem('sl_page');

        // Resetear colores del nav a los por defecto
        resetNavColors();

        unloadProjectAssets();

        projectPages.innerHTML = '<li class="sl-course-empty">' +
            escapeHtml(i18n('nav.no_active_projects')) + '</li>';
        contentBody.innerHTML =
            '<div class="sl-placeholder">' +
            '<i class="bi bi-easel" aria-hidden="true"></i>' +
            '<h2>' + escapeHtml(i18n('dashboard.welcome_title')) + '</h2>' +
            '<p>' + i18n('dashboard.welcome_message') + '</p></div>';

        breadcrumbProject.textContent = i18n('general.app_name');
        breadcrumbPage.textContent = i18n('nav.breadcrumb_home');
        pageTitle.textContent = '';
        switchView('home');
        history.replaceState(null, '', '/dashboard');
    }

    function renderProjectPages(proj, activeSlug) {
        projectPages.innerHTML = '';
        proj.pages.forEach(function (page) {
            // Separador visual antes de herramientas
            if (page.separator) {
                var sep = document.createElement('li');
                sep.className = 'sl-course-separator';
                sep.setAttribute('aria-hidden', 'true');
                var label = document.createElement('span');
                label.textContent = 'Herramientas';
                sep.appendChild(label);
                projectPages.appendChild(sep);
            }

            var li = document.createElement('li');
            li.className = 'sl-course-item';
            if (page.type === 'tool') li.classList.add('sl-course-item-tool');
            if (page.slug === activeSlug) li.classList.add('active');
            li.dataset.page = page.slug;

            var a = document.createElement('a');
            a.href = '#';
            a.textContent = page.name;
            a.addEventListener('click', function (e) {
                e.preventDefault();
                loadPage(proj.slug, page.slug, page.name);
                projectPages.querySelectorAll('.sl-course-item').forEach(function (item) {
                    item.classList.remove('active');
                });
                li.classList.add('active');
            });
            li.appendChild(a);
            projectPages.appendChild(li);
        });
    }

    // ── Load page content ──
    function loadPage(projectSlug, pageSlug, pageName) {
        return api('/api/content?project=' + encodeURIComponent(projectSlug) +
            '&page=' + encodeURIComponent(pageSlug)).then(function (data) {

            if (data.error) {
                contentBody.innerHTML = '<div class="sl-placeholder"><h2>Error</h2><p>' +
                    escapeHtml(data.error) + '</p></div>';
                return;
            }

            // Páginas de herramientas: renderizar dinámicamente
            if (data.toolPage) {
                if (pageSlug === 'colors' && window.renderColorsPage) {
                    window.renderColorsPage(contentBody, data.projectData);
                } else if (pageSlug === 'accessibility' && window.renderAccessibilityPage) {
                    window.renderAccessibilityPage(contentBody, projectSlug, data.contentPages || []);
                } else {
                    contentBody.innerHTML = '<div class="sl-placeholder"><p>Herramienta no disponible</p></div>';
                }
            } else {
                contentBody.innerHTML = data.html;
            }

            // Cargar assets del proyecto solo para páginas de contenido
            if (!data.toolPage) {
                var needsAssets = !currentPageSlug || projectStyleEl === null;
                if (needsAssets) {
                    unloadProjectAssets();
                    loadProjectAssets(data);
                } else {
                    document.dispatchEvent(new Event('contentLoaded'));
                }
            }

            var pageData = currentProject.pages.find(function (p) { return p.slug === pageSlug; });
            currentPageSlug = pageSlug;
            currentPageName = pageName || (pageData ? pageData.name : pageSlug);

            breadcrumbProject.textContent = currentProject.name;
            breadcrumbPage.textContent = currentPageName;
            pageTitle.textContent = currentPageName;

            switchView(pageSlug === 'index' ? 'home' : 'content');
            updateState();

            // Cerrar course nav en mobile después de seleccionar
            if (window.innerWidth < 768) {
                closeCourseNav();
            }

        }).catch(function () {
            contentBody.innerHTML = '<div class="sl-placeholder"><h2>Error</h2><p>' +
                escapeHtml(i18n('dashboard.error_loading')) + '</p></div>';
        });
    }

    function loadProjectAssets(data) {
        if (data.cssPath) {
            projectStyleEl = document.createElement('link');
            projectStyleEl.rel = 'stylesheet';
            projectStyleEl.id = 'project-style';
            projectStyleEl.href = data.cssPath + '?t=' + Date.now();
            document.head.appendChild(projectStyleEl);
        }
        if (data.cssDesktopPath) {
            projectDesktopStyleEl = document.createElement('link');
            projectDesktopStyleEl.rel = 'stylesheet';
            projectDesktopStyleEl.id = 'project-style-desktop';
            projectDesktopStyleEl.href = data.cssDesktopPath + '?t=' + Date.now();
            document.head.appendChild(projectDesktopStyleEl);
        }
        if (data.jsPath) {
            projectScriptEl = document.createElement('script');
            projectScriptEl.id = 'project-script';
            projectScriptEl.src = data.jsPath + '?t=' + Date.now();
            projectScriptEl.onload = function () {
                document.dispatchEvent(new Event('contentLoaded'));
            };
            document.body.appendChild(projectScriptEl);
        }
    }

    function unloadProjectAssets() {
        if (projectStyleEl) { projectStyleEl.remove(); projectStyleEl = null; }
        if (projectDesktopStyleEl) { projectDesktopStyleEl.remove(); projectDesktopStyleEl = null; }
        if (projectScriptEl) {
            if (window.__canvasProjectCleanup) {
                try { window.__canvasProjectCleanup(); } catch (e) { /* ignore */ }
                window.__canvasProjectCleanup = null;
            }
            projectScriptEl.remove();
            projectScriptEl = null;
        }
    }

    function switchView(view) {
        dashboard.classList.remove('view-home', 'view-content');
        dashboard.classList.add('view-' + view);
    }

    // ── Colores institucionales del nav Canvas ──
    function applyNavColors(bgColor, textColor) {
        if (!canvasNav) return;
        // Aplicar directamente al nav y como variables CSS
        if (bgColor) {
            canvasNav.style.background = bgColor;
            document.documentElement.style.setProperty('--sl-canvas-nav-bg', bgColor);
        }
        if (textColor) {
            canvasNav.style.color = textColor;
            document.documentElement.style.setProperty('--sl-canvas-nav-text', textColor);
            document.documentElement.style.setProperty('--sl-canvas-nav-active', textColor);
            // Aplicar a los íconos y labels
            canvasNav.querySelectorAll('.sl-canvas-nav-item a').forEach(function (a) {
                a.style.color = textColor;
            });
            canvasNav.querySelector('.sl-canvas-nav-toggle').style.color = textColor;
        }
    }

    function resetNavColors() {
        if (!canvasNav) return;
        canvasNav.style.background = '';
        canvasNav.style.color = '';
        canvasNav.querySelectorAll('.sl-canvas-nav-item a').forEach(function (a) {
            a.style.color = '';
        });
        canvasNav.querySelector('.sl-canvas-nav-toggle').style.color = '';
        document.documentElement.style.removeProperty('--sl-canvas-nav-bg');
        document.documentElement.style.removeProperty('--sl-canvas-nav-text');
        document.documentElement.style.removeProperty('--sl-canvas-nav-active');
    }

    // ── URL State + sessionStorage ──
    function updateState() {
        if (currentProject && currentPageSlug) {
            var url = '/project/' + encodeURIComponent(currentProject.slug);
            if (currentPageSlug !== 'index') {
                url += '#' + encodeURIComponent(currentPageSlug);
            }
            history.replaceState(null, '', url);
            sessionStorage.setItem('sl_project', currentProject.slug);
            sessionStorage.setItem('sl_page', currentPageSlug);
        }
    }

    function readState() {
        var pathMatch = location.pathname.match(/^\/project\/([a-z0-9\-]+)$/);
        if (pathMatch) {
            var page = location.hash ? decodeURIComponent(location.hash.replace('#', '')) : 'index';
            return { project: decodeURIComponent(pathMatch[1]), page: page };
        }
        return null;
    }

    // ── Canvas nav toggle ──
    btnToggleNav.addEventListener('click', function () {
        canvasNav.classList.toggle('nav-expanded');
        var icon = btnToggleNav.querySelector('i');
        icon.className = canvasNav.classList.contains('nav-expanded')
            ? 'bi bi-chevron-left' : 'bi bi-chevron-right';
    });

    // ── Course nav toggle (hamburger) ──
    function toggleCourseNav() {
        if (courseNavVisible) {
            closeCourseNav();
        } else {
            openCourseNav();
        }
    }

    function openCourseNav() {
        courseNav.style.display = 'block';
        courseNavVisible = true;
        btnToggleCourse.setAttribute('aria-expanded', 'true');
        if (window.innerWidth < 768) {
            courseOverlay.classList.add('visible');
        }
    }

    function closeCourseNav() {
        courseNav.style.display = 'none';
        courseNavVisible = false;
        btnToggleCourse.setAttribute('aria-expanded', 'false');
        courseOverlay.classList.remove('visible');
    }

    btnToggleCourse.addEventListener('click', toggleCourseNav);
    courseOverlay.addEventListener('click', closeCourseNav);

    // ── Toolbar: Reload ──
    btnReload.addEventListener('click', function () {
        if (!currentProject || !currentPageSlug) return;
        unloadProjectAssets();
        projectStyleEl = null;
        var icon = btnReload.querySelector('i');
        icon.className = 'bi bi-arrow-repeat';
        loadPage(currentProject.slug, currentPageSlug, currentPageName).then(function () {
            setTimeout(function () { icon.className = 'bi bi-arrow-clockwise'; }, 500);
            showToast('Contenido recargado', 'success');
        });
    });

    // ── Toolbar: Compile CSS ──
    if (btnCompile) {
        btnCompile.addEventListener('click', function (e) {
            e.preventDefault();
            if (!currentProject) return;
            btnCompile.disabled = true;
            var icon = btnCompile.querySelector('i');
            var origClass = icon.className;
            icon.className = 'bi bi-arrow-repeat';

            api('/api/projects/compile', {
                method: 'POST',
                body: JSON.stringify({ project: currentProject.slug })
            }).then(function (data) {
                icon.className = 'bi bi-check-lg';
                if (typeof showToast === 'function') {
                    showToast(data.message || 'CSS compilado correctamente', 'success');
                }
                setTimeout(function () {
                    icon.className = 'bi bi-box-seam';
                    btnCompile.disabled = false;
                }, 2000);
            }).catch(function (err) {
                if (typeof showToast === 'function') {
                    showToast(err.message, 'error');
                }
                icon.className = 'bi bi-box-seam';
                btnCompile.disabled = false;
            });
        });
    }

    // ── Toolbar: Code Viewer ──
    var btnEditor = document.getElementById('btn-editor');
    var codeViewer = document.getElementById('code-viewer');
    var btnCloseCode = document.getElementById('btn-close-code');
    var codeTabs = document.querySelectorAll('.code-tab');
    var codePanes = document.querySelectorAll('.code-pane');

    if (btnEditor) {
        btnEditor.addEventListener('click', function () {
            if (!currentProject || !currentPageSlug) {
                showToast('Selecciona un proyecto primero', 'error');
                return;
            }
            openCodeViewer();
        });
    }

    if (btnCloseCode) {
        btnCloseCode.addEventListener('click', closeCodeViewer);
    }

    if (codeViewer) {
        codeViewer.addEventListener('click', function (e) {
            if (e.target === codeViewer) closeCodeViewer();
        });
    }

    function closeCodeViewer() {
        codeViewer.classList.add('hidden');
    }

    // Tabs
    codeTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            codeTabs.forEach(function (t) { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
            codePanes.forEach(function (p) { p.classList.remove('active'); });
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');
            var pane = document.getElementById('pane-' + tab.dataset.tab);
            if (pane) pane.classList.add('active');
        });
    });

    // Copy buttons
    document.querySelectorAll('.code-copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var codeEl = document.getElementById(btn.dataset.target);
            if (!codeEl) return;
            var text = codeEl.textContent;
            if (!text || text === '(vacío)' || text === '(sin estilos)' || text === '(sin scripts)') return;

            copyToClipboard(text).then(function () {
                btn.classList.add('copied');
                var origHTML = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-lg" aria-hidden="true"></i> Copiado';
                setTimeout(function () {
                    btn.innerHTML = origHTML;
                    btn.classList.remove('copied');
                }, 2000);
            });
        });
    });

    function openCodeViewer() {
        codeViewer.classList.remove('hidden');
        var slug = currentProject.slug;

        // Filenames
        document.getElementById('filename-html').textContent = currentPageSlug === 'index' ? 'index.html' : 'pages/' + currentPageSlug + '.html';
        document.getElementById('filename-cssmaster').textContent = 'css/' + slug + '-master.css';
        document.getElementById('filename-css').textContent = 'css/' + slug + '-mobile.css';
        document.getElementById('filename-cssdesktop').textContent = 'css/' + slug + '-desktop.css';
        document.getElementById('filename-js').textContent = 'js/' + slug + '-scripts.js';

        // Cargar código fuente
        api('/api/source?project=' + encodeURIComponent(slug) + '&page=' + encodeURIComponent(currentPageSlug))
            .then(function (data) {
                document.getElementById('code-html').textContent = data.html || '(vacío)';
                document.getElementById('code-cssmaster').textContent = data.cssMaster || '(sin estilos)';
                document.getElementById('code-css').textContent = cleanCssForCanvas(data.css) || '(sin estilos)';
                document.getElementById('code-cssdesktop').textContent = cleanCssForCanvas(data.cssDesktop) || '(sin estilos)';
                document.getElementById('code-js').textContent = data.js || '(sin scripts)';
            })
            .catch(function (err) {
                document.getElementById('code-html').textContent = 'Error: ' + err.message;
            });
    }

    // Limpiar CSS para Canvas: quitar bloque dark mode de pruebas
    function cleanCssForCanvas(css) {
        if (!css) return css;
        return css
            .replace(/\/\*[\s\S]*?Ambiente de pruebas[\s\S]*?\*\/\s*/g, '')
            .replace(/html\[data-theme="dark"\]\s*\{[^}]*\}/g, '')
            .replace(/\n{3,}/g, '\n\n')
            .trim();
    }

    // ── Toolbar: Export ──
    btnExport.addEventListener('click', function () {
        if (!currentProject) return;
        window.location.href = '/api/export/' + encodeURIComponent(currentProject.slug);
    });

    // ── Toolbar: Mobile Simulator ──
    var btnMobile = document.getElementById('btn-mobile');
    var mobileFrame = document.getElementById('mobile-frame');
    var mobileIframe = document.getElementById('mobile-iframe');
    var mobileDevice = document.getElementById('mobile-device');
    // mobileSize se muestra en el select
    var btnOrient = document.getElementById('btn-orient');
    var orientIcon = document.getElementById('orient-icon');
    var btnDark = document.getElementById('btn-dark');
    var btnExitMobile = document.getElementById('btn-exit-mobile');
    var btnMobileReload = document.getElementById('btn-mobile-reload');
    var deviceSelect = document.getElementById('mobile-device-select');

    var mobileCurrentDevice = 'android-360';
    var mobileIsPortrait = true;
    var mobileIsDark = false;
    var devices = {
        'android-360':  { name: 'Android',        pw: 360, ph: 800,  type: 'phone' },
        'iphone-14':    { name: 'iPhone 14/15',   pw: 390, ph: 844,  type: 'phone' },
        'ipad-classic': { name: 'iPad Mini',      pw: 768, ph: 1024, type: 'tablet' },
        'ipad-10':      { name: 'iPad 10a gen',   pw: 810, ph: 1080, type: 'tablet' }
    };

    if (btnMobile) {
        btnMobile.addEventListener('click', function () {
            if (!currentProject || !currentPageSlug) {
                showToast('Selecciona un proyecto primero', 'error');
                return;
            }
            mobileFrame.classList.remove('hidden');
            btnMobile.classList.add('active');
            updateMobileDevice();
            syncMobileContent();
        });
    }

    if (btnExitMobile) {
        btnExitMobile.addEventListener('click', closeMobile);
    }

    function closeMobile() {
        mobileFrame.classList.add('hidden');
        if (btnMobile) btnMobile.classList.remove('active');
        mobileIsDark = false;
        if (btnDark) {
            btnDark.classList.remove('active');
            btnDark.querySelector('i').className = 'bi bi-moon';
        }
        // Cerrar panel info si está abierto
        var infoPanel = document.getElementById('mobile-info-panel');
        if (infoPanel) infoPanel.classList.add('hidden');
        var infoBtn = document.getElementById('btn-mobile-info');
        if (infoBtn) {
            infoBtn.classList.remove('active');
            infoBtn.setAttribute('aria-expanded', 'false');
        }
    }

    if (deviceSelect) {
        deviceSelect.addEventListener('change', function () {
            mobileCurrentDevice = deviceSelect.value;
            updateMobileDevice();
            syncMobileContent();
        });
    }

    if (btnOrient) {
        btnOrient.addEventListener('click', function () {
            mobileIsPortrait = !mobileIsPortrait;
            updateMobileDevice();
            syncMobileContent();
        });
    }

    if (btnMobileReload) {
        btnMobileReload.addEventListener('click', syncMobileContent);
    }

    // Info panel
    var btnMobileInfo = document.getElementById('btn-mobile-info');
    var mobileInfoPanel = document.getElementById('mobile-info-panel');
    var btnCloseInfo = document.getElementById('btn-close-info');

    if (btnMobileInfo) {
        btnMobileInfo.addEventListener('click', function () {
            var isOpen = !mobileInfoPanel.classList.contains('hidden');
            mobileInfoPanel.classList.toggle('hidden', isOpen);
            btnMobileInfo.classList.toggle('active', !isOpen);
            btnMobileInfo.setAttribute('aria-expanded', String(!isOpen));
        });
    }
    if (btnCloseInfo) {
        btnCloseInfo.addEventListener('click', function () {
            mobileInfoPanel.classList.add('hidden');
            if (btnMobileInfo) {
                btnMobileInfo.classList.remove('active');
                btnMobileInfo.setAttribute('aria-expanded', 'false');
            }
        });
    }

    if (btnDark) {
        btnDark.addEventListener('click', function () {
            mobileIsDark = !mobileIsDark;
            btnDark.classList.toggle('active', mobileIsDark);
            btnDark.querySelector('i').className = mobileIsDark ? 'bi bi-sun' : 'bi bi-moon';
            syncMobileContent();
        });
    }

    function updateMobileDevice() {
        var dev = devices[mobileCurrentDevice];
        if (!dev) return;

        mobileDevice.className = 'mobile-device ' + dev.type + ' ' + (mobileIsPortrait ? 'portrait' : 'landscape');

        // Tamaño dinámico con inline style
        var w = mobileIsPortrait ? dev.pw : dev.ph;
        var h = mobileIsPortrait ? dev.ph : dev.pw;
        mobileDevice.style.width = w + 'px';
        mobileDevice.style.height = h + 'px';

        if (dev.type === 'phone') {
            orientIcon.className = mobileIsPortrait ? 'bi bi-phone' : 'bi bi-phone-landscape';
        } else {
            orientIcon.className = mobileIsPortrait ? 'bi bi-tablet' : 'bi bi-tablet-landscape';
        }

        // Actualizar texto del select con dimensiones actuales (portrait/landscape)
        if (deviceSelect) {
            var opt = deviceSelect.querySelector('option[value="' + mobileCurrentDevice + '"]');
            if (opt) opt.textContent = dev.name + ' — ' + w + ' x ' + h;
        }
    }

    function syncMobileContent() {
        if (!currentProject || !currentPageSlug) return;
        var theme = mobileIsDark ? 'dark' : 'light';
        var url = '/api/preview?project=' + encodeURIComponent(currentProject.slug)
            + '&page=' + encodeURIComponent(currentPageSlug)
            + '&theme=' + theme
            + '&t=' + Date.now();
        mobileIframe.src = url;
    }

    // ── Keyboard shortcuts ──
    document.addEventListener('keydown', function (e) {
        // Ctrl+R — Recargar
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            btnReload.click();
        }
        // Ctrl+E — Toggle code viewer
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            if (codeViewer.classList.contains('hidden')) {
                if (currentProject && currentPageSlug) openCodeViewer();
            } else {
                closeCodeViewer();
            }
        }
        // Escape — Cerrar paneles
        if (e.key === 'Escape') {
            if (!codeViewer.classList.contains('hidden')) {
                closeCodeViewer();
            } else if (!mobileFrame.classList.contains('hidden')) {
                closeMobile();
            } else if (courseNavVisible && window.innerWidth < 768) {
                closeCourseNav();
            }
        }
    });

    // ── i18n helper ──
    var i18nCache = {};
    function i18n(key) {
        if (i18nCache[key]) return i18nCache[key];
        return key;
    }
    // Pre-populate from rendered page
    var mappings = {
        'nav.no_active_projects': '.sl-course-empty',
        'dashboard.welcome_title': '.sl-placeholder h2',
        'general.app_name': '.sl-toolbar-brand-text',
        'nav.breadcrumb_home': '#breadcrumb-page'
    };
    Object.keys(mappings).forEach(function (key) {
        var el = document.querySelector(mappings[key]);
        if (el) i18nCache[key] = el.textContent;
    });
    var welcomeP = document.querySelector('.sl-placeholder p');
    if (welcomeP) i18nCache['dashboard.welcome_message'] = welcomeP.innerHTML;
    i18nCache['dashboard.error_loading'] = 'Error loading page';

})();
