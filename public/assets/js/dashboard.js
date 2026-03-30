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
            var li = document.createElement('li');
            li.className = 'sl-course-item';
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

            contentBody.innerHTML = data.html;

            var needsAssets = !currentPageSlug || projectStyleEl === null;
            if (needsAssets) {
                unloadProjectAssets();
                loadProjectAssets(data);
            } else {
                document.dispatchEvent(new Event('contentLoaded'));
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
        });
    });

    // ── Toolbar: Compile CSS ──
    if (btnCompile) {
        btnCompile.addEventListener('click', function () {
            if (!currentProject) return;
            btnCompile.disabled = true;
            var icon = btnCompile.querySelector('i');
            icon.className = 'bi bi-arrow-repeat';

            api('/api/compile', {
                method: 'POST',
                body: JSON.stringify({ project: currentProject.slug })
            }).then(function () {
                icon.className = 'bi bi-check-lg';
                btnCompile.classList.add('active');
                unloadProjectAssets();
                projectStyleEl = null;
                loadPage(currentProject.slug, currentPageSlug, currentPageName);
                setTimeout(function () {
                    icon.className = 'bi bi-gear';
                    btnCompile.classList.remove('active');
                    btnCompile.disabled = false;
                }, 2000);
            }).catch(function (err) {
                alert(err.message);
                icon.className = 'bi bi-gear';
                btnCompile.disabled = false;
            });
        });
    }

    // ── Toolbar: Export ──
    btnExport.addEventListener('click', function () {
        if (!currentProject) return;
        window.location.href = '/api/export/' + encodeURIComponent(currentProject.slug);
    });

    // ── Keyboard shortcuts ──
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            btnReload.click();
        }
        if (e.key === 'Escape') {
            if (courseNavVisible && window.innerWidth < 768) {
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
