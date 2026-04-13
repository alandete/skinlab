<!DOCTYPE html>
<html lang="<?= e(App\Core\Lang::locale()) ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? __('admin.title')) ?></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">

    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
</head>
<body class="admin-page">
    <a href="#admin-main" class="sr-only sr-only-focusable">Ir al contenido principal</a>

    <div class="admin-layout">

        <!-- Sidebar -->
        <aside class="admin-sidebar" id="admin-sidebar" aria-label="<?= e(__('admin.title')) ?>">
            <header class="admin-sidebar-header">
                <a href="/dashboard" class="admin-sidebar-brand" title="<?= e(__('general.back')) ?>">
                    <i class="bi bi-brush" aria-hidden="true"></i>
                    <span><?= e(__('general.app_name')) ?></span>
                </a>
            </header>

            <nav class="admin-sidebar-nav">
                <ul>
                    <li>
                        <a href="/dashboard" class="<?= ($activeTab ?? '') === 'dashboard' ? 'active' : '' ?>">
                            <i class="bi bi-speedometer2" aria-hidden="true"></i>
                            <span><?= e(__('nav.dashboard')) ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/users" class="<?= ($activeTab ?? '') === 'users' ? 'active' : '' ?>">
                            <i class="bi bi-people" aria-hidden="true"></i>
                            <span><?= e(__('admin.tab_users')) ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/projects" class="<?= ($activeTab ?? '') === 'projects' ? 'active' : '' ?>">
                            <i class="bi bi-folder" aria-hidden="true"></i>
                            <span><?= e(__('admin.tab_projects')) ?></span>
                        </a>
                    </li>
                    <li>
                        <span class="admin-nav-disabled">
                            <i class="bi bi-plug" aria-hidden="true"></i>
                            <span>API</span>
                        </span>
                    </li>
                    <li>
                        <a href="/admin/docs" class="<?= ($activeTab ?? '') === 'docs' ? 'active' : '' ?>">
                            <i class="bi bi-book" aria-hidden="true"></i>
                            <span><?= e(__('admin.tab_docs')) ?></span>
                        </a>
                    </li>
                </ul>
            </nav>

            <footer class="admin-sidebar-footer">
                <div class="admin-sidebar-user">
                    <div class="admin-sidebar-avatar">
                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    </div>
                    <div class="admin-sidebar-user-info">
                        <strong><?= e(auth_user()) ?></strong>
                        <small><?= e(__('admin.role_' . auth_role())) ?></small>
                    </div>
                </div>
            </footer>
        </aside>

        <!-- Contenido principal -->
        <div class="admin-content-wrapper">

            <!-- Header mobile + breadcrumb -->
            <header class="admin-topbar">
                <button class="admin-menu-toggle" id="admin-menu-toggle"
                        aria-label="Menu" aria-expanded="false" aria-controls="admin-sidebar">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <nav class="admin-breadcrumb" aria-label="Breadcrumb">
                    <a href="/dashboard"><?= e(__('general.app_name')) ?></a>
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    <a href="/admin"><?= e(__('admin.title')) ?></a>
                    <?php if (!empty($breadcrumb)): ?>
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    <span aria-current="page"><?= e($breadcrumb) ?></span>
                    <?php endif; ?>
                </nav>
            </header>

            <!-- Main -->
            <main class="admin-main" id="admin-main">
                <?= $content ?>
            </main>
        </div>

    </div>

    <!-- Overlay mobile -->
    <div class="admin-overlay" id="admin-overlay" aria-hidden="true"></div>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script src="<?= asset('js/admin.js') ?>"></script>
    <script src="<?= asset('js/admin-projects.js') ?>"></script>
</body>
</html>
