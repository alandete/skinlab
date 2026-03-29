<?php $title = __('dashboard.title') . ' — ' . __('general.app_name'); ?>

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
                    <i class="bi bi-gear" aria-hidden="true"></i>
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
            </div>

            <span class="sl-toolbar-sep" aria-hidden="true"></span>

            <?php if (is_admin()): ?>
            <a href="/admin" class="sl-tool-link" title="<?= e(__('nav.admin')) ?>"
               aria-label="<?= e(__('nav.admin')) ?>">
                <i class="bi bi-sliders" aria-hidden="true"></i>
            </a>
            <?php endif; ?>

            <a href="/logout" class="sl-tool-link" title="<?= e(__('nav.logout')) ?>"
               aria-label="<?= e(__('nav.logout')) ?> – <?= e(auth_user()) ?>">
                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
            </a>
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
                <main class="sl-content-body" id="content-body">
                    <div class="sl-placeholder">
                        <i class="bi bi-easel" aria-hidden="true"></i>
                        <h2><?= e(__('dashboard.welcome_title')) ?></h2>
                        <p><?= __('dashboard.welcome_message') ?></p>
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

</div><!-- /.sl-dashboard -->
