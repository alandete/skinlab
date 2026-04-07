<?php $title = __('dashboard.title') . ' — ' . __('general.app_name'); ?>

<!-- Toast notifications (fuera del dashboard para z-index correcto) -->
<div id="toast-container" class="sl-toast-container" aria-live="polite"></div>

<div class="sl-dashboard view-home" id="sl-dashboard">

    <!-- ==================== TOOLBAR SKINLAB ==================== -->
    <header class="sl-toolbar" role="banner">
        <span class="sl-toolbar-brand" aria-label="<?= e(__('general.app_name')) ?>">
            <i class="bi bi-brush" aria-hidden="true"></i>
            <span class="sl-toolbar-brand-text"><?= e(__('general.app_name')) ?></span>
        </span>

        <!-- Selector de proyecto -->
        <div class="sl-toolbar-project">
            <label for="project-select" class="sr-only"><?= e(__('nav.projects')) ?></label>
            <select id="project-select" class="sl-project-select" aria-label="<?= e(__('nav.select_project')) ?>">
                <option value=""><?= e(__('nav.select_project')) ?></option>
            </select>
        </div>

        <!-- Herramientas principales -->
        <nav class="sl-toolbar-tools" aria-label="<?= e(__('dashboard.dev_environment')) ?>">
            <button id="btn-reload" class="sl-tool-btn" title="<?= e(__('nav.reload')) ?> (Ctrl+R)"
                    aria-label="<?= e(__('nav.reload')) ?>">
                <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
            </button>

            <!-- Herramientas extendidas (ocultas en mobile) -->
            <div class="sl-toolbar-tools-extended">
                <?php if (has_role('editor')): ?>
                <button id="btn-compile" class="sl-tool-btn" title="<?= e(__('nav.compile')) ?>"
                        aria-label="<?= e(__('nav.compile')) ?>">
                    <i class="bi bi-box-seam" aria-hidden="true"></i>
                </button>
                <?php endif; ?>

                <button id="btn-mobile" class="sl-tool-btn" title="<?= e(__('nav.mobile_view')) ?>"
                        aria-label="<?= e(__('nav.mobile_view')) ?>">
                    <i class="bi bi-phone" aria-hidden="true"></i>
                </button>

                <button id="btn-editor" class="sl-tool-btn" title="<?= e(__('nav.code_view')) ?> (Ctrl+E)"
                        aria-label="<?= e(__('nav.code_view')) ?>">
                    <i class="bi bi-code-slash" aria-hidden="true"></i>
                </button>

                <button id="btn-export" class="sl-tool-btn hidden" title="<?= e(__('nav.export')) ?>"
                        aria-label="<?= e(__('nav.export')) ?>">
                    <i class="bi bi-file-earmark-zip" aria-hidden="true"></i>
                </button>

                <button id="btn-a11y" class="sl-tool-btn" title="Accesibilidad (Sa11y)"
                        aria-label="Evaluar accesibilidad">
                    <i class="bi bi-universal-access" aria-hidden="true"></i>
                </button>
            </div>

            <span class="sl-toolbar-sep" aria-hidden="true"></span>

            <?php if (is_admin()): ?>
            <a href="/admin" class="sl-tool-link" title="<?= e(__('nav.admin')) ?>"
               aria-label="<?= e(__('nav.admin')) ?>">
                <i class="bi bi-sliders" aria-hidden="true"></i>
            </a>
            <?php endif; ?>

            <form method="POST" action="/logout" style="display:inline;">
                <?= csrf_field() ?>
                <button type="submit" class="sl-tool-link" title="<?= e(__('nav.logout')) ?>"
                        aria-label="<?= e(__('nav.logout')) ?> – <?= e(auth_user()) ?>">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                </button>
            </form>
        </nav>

        <!-- Usuario (visible desde md) -->
        <span class="sl-toolbar-user">
            <span class="user-badge user-badge-<?= e(auth_role()) ?>"><?= e(auth_role()) ?></span>
            <span><?= e(auth_user()) ?></span>
        </span>
    </header>

    <!-- ==================== CANVAS AREA ==================== -->
    <div class="sl-canvas-area">

        <!-- COL 1: Nav global Canvas (preview fiel) -->
        <nav class="sl-canvas-nav" id="canvas-nav" aria-label="Canvas LMS">
            <div class="sl-canvas-nav-logo">
                <img src="<?= asset('img/canvas-lms.png') ?>" alt="Canvas LMS">
            </div>
            <ul class="sl-canvas-nav-items">
                <li class="sl-canvas-nav-item active">
                    <a href="#" aria-current="page">
                        <i class="bi bi-person" aria-hidden="true"></i>
                        <span class="sl-canvas-nav-label"><?= e(__('nav.account')) ?></span>
                    </a>
                </li>
                <li class="sl-canvas-nav-item">
                    <a href="#">
                        <i class="bi bi-speedometer2" aria-hidden="true"></i>
                        <span class="sl-canvas-nav-label"><?= e(__('nav.dashboard')) ?></span>
                    </a>
                </li>
                <li class="sl-canvas-nav-item">
                    <a href="#">
                        <i class="bi bi-book" aria-hidden="true"></i>
                        <span class="sl-canvas-nav-label"><?= e(__('nav.courses')) ?></span>
                    </a>
                </li>
                <li class="sl-canvas-nav-item">
                    <a href="#">
                        <i class="bi bi-calendar3" aria-hidden="true"></i>
                        <span class="sl-canvas-nav-label"><?= e(__('nav.calendar')) ?></span>
                    </a>
                </li>
                <li class="sl-canvas-nav-item">
                    <a href="#">
                        <i class="bi bi-inbox" aria-hidden="true"></i>
                        <span class="sl-canvas-nav-label"><?= e(__('nav.inbox')) ?></span>
                    </a>
                </li>
            </ul>
            <button class="sl-canvas-nav-toggle" id="btn-toggle-nav"
                    title="Toggle nav" aria-label="Toggle Canvas navigation">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
        </nav>

        <!-- Wrapper: header + contenido + sidebar -->
        <div class="sl-content-wrapper">

            <!-- Barra: hamburguesa + breadcrumb -->
            <div class="sl-content-header">
                <button class="sl-hamburger" id="btn-toggle-course"
                        title="<?= e(__('nav.projects')) ?>"
                        aria-label="<?= e(__('nav.projects')) ?>"
                        aria-expanded="false" aria-controls="left-side">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <nav class="sl-content-breadcrumb" aria-label="Breadcrumb">
                    <a href="#" id="breadcrumb-project"><?= e(__('general.app_name')) ?></a>
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    <span id="breadcrumb-page" aria-current="page"><?= e(__('nav.breadcrumb_home')) ?></span>
                </nav>
            </div>

            <!-- Área de contenido: Col 2 + Col 3 + Col 4 -->
            <div class="sl-content">

                <!-- COL 2: Páginas del proyecto -->
                <aside class="sl-course-nav" id="left-side" aria-label="<?= e(__('nav.projects')) ?>">
                    <ul class="sl-course-items" id="project-pages" role="navigation"
                        aria-label="<?= e(__('nav.projects')) ?>">
                        <li class="sl-course-empty"><?= e(__('nav.no_active_projects')) ?></li>
                    </ul>
                </aside>

                <!-- Overlay para cerrar course nav en mobile -->
                <div class="sl-course-overlay" id="course-overlay" aria-hidden="true"></div>

                <!-- COL 3: Contenido principal -->
                <main class="sl-content-main" id="content-main">
                    <h1 class="sl-page-title" id="page-title"></h1>
                    <div class="sl-content-body" id="content-body">
                        <div class="sl-placeholder">
                            <i class="bi bi-easel" aria-hidden="true"></i>
                            <h2><?= e(__('dashboard.welcome_title')) ?></h2>
                            <p><?= __('dashboard.welcome_message') ?></p>
                        </div>
                    </div>
                </main>

                <!-- COL 4: Sidebar estado del curso -->
                <aside class="sl-sidebar" id="course-sidebar" aria-label="<?= e(__('dashboard.course_status')) ?>">
                    <section class="sl-sidebar-section">
                        <h3><i class="bi bi-bar-chart-line" aria-hidden="true"></i> <?= e(__('dashboard.course_status')) ?></h3>
                        <div class="sl-sidebar-item">
                            <i class="bi bi-graph-up" aria-hidden="true"></i>
                            <div>
                                <strong><?= e(__('dashboard.current_grade')) ?></strong>
                                <small><?= e(__('dashboard.no_grade')) ?></small>
                            </div>
                        </div>
                    </section>

                    <section class="sl-sidebar-section">
                        <h3><i class="bi bi-check2-square" aria-hidden="true"></i> <?= e(__('dashboard.todo')) ?></h3>
                        <div class="sl-sidebar-item">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                            <div>
                                <a href="#">Tarea 1 – Introducción</a>
                                <small><?= __('dashboard.due_date', ['date' => '25 mar 2026']) ?></small>
                            </div>
                        </div>
                        <div class="sl-sidebar-item">
                            <i class="bi bi-chat-dots" aria-hidden="true"></i>
                            <div>
                                <a href="#">Foro – Presentación</a>
                                <small><?= __('dashboard.due_date', ['date' => '27 mar 2026']) ?></small>
                            </div>
                        </div>
                        <div class="sl-sidebar-item">
                            <i class="bi bi-question-circle" aria-hidden="true"></i>
                            <div>
                                <a href="#">Quiz – Diagnóstico</a>
                                <small><?= __('dashboard.due_date', ['date' => '30 mar 2026']) ?></small>
                            </div>
                        </div>
                    </section>

                    <section class="sl-sidebar-section">
                        <h3><i class="bi bi-calendar-event" aria-hidden="true"></i> <?= e(__('dashboard.upcoming_events')) ?></h3>
                        <div class="sl-sidebar-item">
                            <i class="bi bi-camera-video" aria-hidden="true"></i>
                            <div>
                                <a href="#">Clase en vivo</a>
                                <small>22 mar 2026 – 10:00 AM</small>
                            </div>
                        </div>
                    </section>
                </aside>

            </div><!-- /.sl-content -->

        </div><!-- /.sl-content-wrapper -->

    </div><!-- /.sl-canvas-area -->

    <!-- ==================== CODE VIEWER ==================== -->
    <div id="code-viewer" class="code-viewer hidden" aria-label="<?= e(__('dashboard.code_viewer')) ?>">
        <header class="code-viewer-header">
            <h3><i class="bi bi-code-slash" aria-hidden="true"></i> <?= e(__('dashboard.code_viewer')) ?></h3>
            <button id="btn-close-code" class="code-viewer-close" title="<?= e(__('general.close')) ?>"
                    aria-label="<?= e(__('general.close')) ?>">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </header>
        <nav class="code-viewer-tabs" role="tablist">
            <button class="code-tab active" data-tab="html" role="tab" aria-selected="true"><?= e(__('dashboard.tab_html')) ?></button>
            <button class="code-tab" data-tab="cssmaster" role="tab"><?= e(__('dashboard.tab_css_master')) ?></button>
            <button class="code-tab" data-tab="css" role="tab"><?= e(__('dashboard.tab_css_mobile')) ?></button>
            <button class="code-tab" data-tab="cssdesktop" role="tab"><?= e(__('dashboard.tab_css_desktop')) ?></button>
            <button class="code-tab" data-tab="js" role="tab"><?= e(__('dashboard.tab_js')) ?></button>
        </nav>
        <div class="code-viewer-body">
            <div class="code-pane active" id="pane-html">
                <div class="code-pane-bar">
                    <span class="code-filename" id="filename-html">index.html</span>
                    <button class="code-copy-btn" data-target="code-html" aria-label="<?= e(__('dashboard.copy')) ?>">
                        <i class="bi bi-clipboard" aria-hidden="true"></i> <?= e(__('dashboard.copy')) ?>
                    </button>
                </div>
                <pre><code id="code-html"></code></pre>
            </div>
            <div class="code-pane" id="pane-cssmaster">
                <div class="code-pane-bar">
                    <span class="code-filename" id="filename-cssmaster"></span>
                    <button class="code-copy-btn" data-target="code-cssmaster" aria-label="<?= e(__('dashboard.copy')) ?>">
                        <i class="bi bi-clipboard" aria-hidden="true"></i> <?= e(__('dashboard.copy')) ?>
                    </button>
                </div>
                <pre><code id="code-cssmaster"></code></pre>
            </div>
            <div class="code-pane" id="pane-css">
                <div class="code-pane-bar">
                    <span class="code-filename" id="filename-css"></span>
                    <button class="code-copy-btn" data-target="code-css" aria-label="<?= e(__('dashboard.copy')) ?>">
                        <i class="bi bi-clipboard" aria-hidden="true"></i> <?= e(__('dashboard.copy')) ?>
                    </button>
                </div>
                <pre><code id="code-css"></code></pre>
            </div>
            <div class="code-pane" id="pane-cssdesktop">
                <div class="code-pane-bar">
                    <span class="code-filename" id="filename-cssdesktop"></span>
                    <button class="code-copy-btn" data-target="code-cssdesktop" aria-label="<?= e(__('dashboard.copy')) ?>">
                        <i class="bi bi-clipboard" aria-hidden="true"></i> <?= e(__('dashboard.copy')) ?>
                    </button>
                </div>
                <pre><code id="code-cssdesktop"></code></pre>
            </div>
            <div class="code-pane" id="pane-js">
                <div class="code-pane-bar">
                    <span class="code-filename" id="filename-js"></span>
                    <button class="code-copy-btn" data-target="code-js" aria-label="<?= e(__('dashboard.copy')) ?>">
                        <i class="bi bi-clipboard" aria-hidden="true"></i> <?= e(__('dashboard.copy')) ?>
                    </button>
                </div>
                <pre><code id="code-js"></code></pre>
            </div>
        </div>
    </div>

    <!-- ==================== MOBILE SIMULATOR ==================== -->
    <div id="mobile-frame" class="mobile-frame hidden" aria-label="<?= e(__('nav.mobile_view')) ?>">
        <header class="mobile-toolbar">
            <select id="mobile-device-select" class="mobile-device-select" aria-label="Dispositivo">
                <option value="android-360" selected>Android — 360 x 800</option>
                <option value="iphone-14">iPhone 14/15 — 390 x 844</option>
                <option value="ipad-classic">iPad Mini — 768 x 1024</option>
                <option value="ipad-10">iPad 10a gen — 810 x 1080</option>
            </select>
            <span class="mobile-sep" aria-hidden="true"></span>
            <button id="btn-orient" class="mobile-tool-btn" title="<?= e(__('dashboard.rotate')) ?>"
                    aria-label="<?= e(__('dashboard.rotate')) ?>">
                <i class="bi bi-phone" id="orient-icon" aria-hidden="true"></i>
            </button>
            <span class="mobile-sep" aria-hidden="true"></span>
            <button id="btn-mobile-reload" class="mobile-tool-btn" title="<?= e(__('nav.reload')) ?>"
                    aria-label="<?= e(__('nav.reload')) ?>">
                <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
            </button>
            <span class="mobile-sep" aria-hidden="true"></span>
            <button id="btn-dark" class="mobile-tool-btn" title="<?= e(__('nav.dark_mode')) ?>"
                    aria-label="<?= e(__('nav.dark_mode')) ?>">
                <i class="bi bi-moon" aria-hidden="true"></i>
            </button>
            <span class="mobile-spacer" aria-hidden="true"></span>
            <button id="btn-mobile-info" class="mobile-tool-btn" title="Estadísticas de viewports"
                    aria-label="Estadísticas de viewports" aria-expanded="false" aria-controls="mobile-info-panel">
                <i class="bi bi-info-circle" aria-hidden="true"></i>
            </button>
            <span class="mobile-sep" aria-hidden="true"></span>
            <button id="btn-exit-mobile" class="mobile-tool-btn mobile-exit" title="<?= e(__('dashboard.exit_mobile')) ?>"
                    aria-label="<?= e(__('dashboard.exit_mobile')) ?>">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </header>

        <!-- Panel de estadísticas -->
        <aside id="mobile-info-panel" class="mobile-info-panel hidden" aria-label="Estadísticas de viewports">
            <header class="mobile-info-header">
                <h4><i class="bi bi-bar-chart-line" aria-hidden="true"></i> Viewports más usados (2026)</h4>
                <button id="btn-close-info" class="mobile-tool-btn" aria-label="Cerrar">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </header>
            <div class="mobile-info-body">
                <section class="mobile-info-section">
                    <h5><i class="bi bi-phone" aria-hidden="true"></i> Móviles</h5>
                    <table class="mobile-info-table">
                        <thead><tr><th>Viewport</th><th>Dispositivos</th><th>Tráfico</th></tr></thead>
                        <tbody>
                            <tr><td><strong>360 x 800</strong></td><td>Samsung Galaxy S21/S22/S23, Xiaomi, Motorola</td><td>~25%</td></tr>
                            <tr><td><strong>390 x 844</strong></td><td>iPhone 14, iPhone 15, iPhone 16</td><td>~18%</td></tr>
                            <tr><td>393 x 873</td><td>iPhone 14 Pro, Pixel 7/8</td><td>~10%</td></tr>
                            <tr><td>430 x 932</td><td>iPhone 14/15/16 Pro Max</td><td>~8%</td></tr>
                            <tr><td>412 x 915</td><td>Samsung Galaxy S24, Pixel 9</td><td>~7%</td></tr>
                        </tbody>
                    </table>
                </section>
                <section class="mobile-info-section">
                    <h5><i class="bi bi-tablet" aria-hidden="true"></i> Tablets</h5>
                    <table class="mobile-info-table">
                        <thead><tr><th>Viewport</th><th>Dispositivos</th><th>Tráfico</th></tr></thead>
                        <tbody>
                            <tr><td><strong>768 x 1024</strong></td><td>iPad Mini, iPad 9a gen</td><td>~45%</td></tr>
                            <tr><td><strong>810 x 1080</strong></td><td>iPad 10a gen</td><td>~15%</td></tr>
                            <tr><td>820 x 1180</td><td>iPad Air</td><td>~12%</td></tr>
                            <tr><td>1024 x 1366</td><td>iPad Pro 12.9"</td><td>~8%</td></tr>
                        </tbody>
                    </table>
                </section>
                <section class="mobile-info-section">
                    <h5><i class="bi bi-lightbulb" aria-hidden="true"></i> Recomendaciones</h5>
                    <ul class="mobile-info-list">
                        <li>Probar siempre en <strong>360px</strong> (móvil más común) y <strong>768px</strong> (tablet más común)</li>
                        <li>Los breakpoints de Bootstrap (576, 768, 992, 1200) cubren el 95% de los dispositivos</li>
                        <li>Canvas LMS oculta <code>#left-side</code> con <code>max-width: 768px</code> — no se ve en móviles ni en tablets en portrait</li>
                    </ul>
                </section>
                <section class="mobile-info-section">
                    <h5><i class="bi bi-link-45deg" aria-hidden="true"></i> Fuentes</h5>
                    <ul class="mobile-info-list">
                        <li><a href="https://gs.statcounter.com/screen-resolution-stats/mobile-tablet/worldwide/" target="_blank" rel="noopener">StatCounter — Screen Resolution Stats</a></li>
                        <li><a href="https://www.browserstack.com/guide/common-screen-resolutions" target="_blank" rel="noopener">BrowserStack — Common Screen Resolutions 2026</a></li>
                        <li><a href="https://phone-simulator.com/blog/most-popular-mobile-screen-resolutions-in-2026" target="_blank" rel="noopener">Phone Simulator — Popular Resolutions 2026</a></li>
                    </ul>
                </section>
            </div>
        </aside>
        <div id="mobile-device" class="mobile-device portrait phone">
            <div class="mobile-status-bar">
                <span>9:41</span>
                <span><i class="bi bi-reception-4" aria-hidden="true"></i> <i class="bi bi-wifi" aria-hidden="true"></i> <i class="bi bi-battery-full" aria-hidden="true"></i></span>
            </div>
            <iframe id="mobile-iframe" title="Mobile preview" sandbox="allow-scripts" referrerpolicy="no-referrer"></iframe>
            <nav class="mobile-bottom-nav" aria-label="Canvas mobile">
                <span class="mobile-tab active"><i class="bi bi-speedometer2" aria-hidden="true"></i><small><?= e(__('nav.dashboard')) ?></small></span>
                <span class="mobile-tab"><i class="bi bi-book" aria-hidden="true"></i><small><?= e(__('nav.courses')) ?></small></span>
                <span class="mobile-tab"><i class="bi bi-calendar3" aria-hidden="true"></i><small><?= e(__('nav.calendar')) ?></small></span>
                <span class="mobile-tab"><i class="bi bi-inbox" aria-hidden="true"></i><small><?= e(__('nav.inbox')) ?></small></span>
                <span class="mobile-tab"><i class="bi bi-bell" aria-hidden="true"></i><small>Alertas</small></span>
            </nav>
        </div>
    </div>

</div><!-- /.sl-dashboard -->
