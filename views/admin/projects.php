<?php
$currentUserId = (int) ($_SESSION['user_id'] ?? 0);
$cdnNames = [
    'bootstrap'       => __('admin.cdn_bootstrap'),
    'bootstrap-icons' => __('admin.cdn_bootstrap_icons'),
    'fontawesome'     => __('admin.cdn_fontawesome'),
    'animate'         => __('admin.cdn_animate'),
];
$cdnDescs = [
    'bootstrap'       => __('admin.cdn_bootstrap_desc'),
    'bootstrap-icons' => __('admin.cdn_bootstrap_icons_desc'),
    'fontawesome'     => __('admin.cdn_fontawesome_desc'),
    'animate'         => __('admin.cdn_animate_desc'),
];
?>

<!-- Traducciones para JS -->
<div id="js-lang" class="hidden"
    data-project_created="<?= e(__('admin.project_created', ['name' => ':name'])) ?>"
    data-project_updated="<?= e(__('admin.project_updated')) ?>"
    data-project_deleted="<?= e(__('admin.project_deleted')) ?>"
    data-project_activated="<?= e(__('admin.project_activated')) ?>"
    data-project_deactivated="<?= e(__('admin.project_deactivated')) ?>"
    data-compile_success="<?= e(__('admin.compile_success')) ?>"
    data-confirm_delete_project="<?= e(__('admin.confirm_delete')) ?>"
    data-no_projects="<?= e(__('admin.no_projects')) ?>"
    data-delete="<?= e(__('general.delete')) ?>"
    data-required="<?= e(__('general.field_required')) ?>"
></div>

<!-- Toast container (si no existe ya) -->
<div id="toast-container" class="sl-toast-container" aria-live="polite"></div>

<!-- Panel header -->
<header class="panel-header">
    <h2><i class="bi bi-folder" aria-hidden="true"></i> <?= e(__('admin.tab_projects')) ?></h2>
    <a href="/admin/projects/new" class="btn btn-primary">
        <i class="bi bi-plus-lg" aria-hidden="true"></i> <?= e(__('admin.new_project')) ?>
    </a>
</header>

<!-- Grid de proyectos -->
<section id="projects-grid" class="projects-grid" aria-label="<?= e(__('admin.tab_projects')) ?>">
    <?php if (empty($projects)): ?>
    <div class="empty-state" id="empty-state">
        <i class="bi bi-folder-plus" aria-hidden="true"></i>
        <p><?= e(__('admin.no_projects')) ?></p>
        <p class="form-hint"><?= __('admin.no_projects_hint') ?></p>
    </div>
    <?php else: ?>
    <?php foreach ($projects as $proj): ?>
    <article class="project-card <?= !$proj['is_active'] ? 'project-card-inactive' : '' ?>"
             data-project-id="<?= (int)$proj['id'] ?>"
             data-slug="<?= e($proj['slug']) ?>">
        <header class="project-card-header">
            <div class="project-color-dot" style="background: <?= e($proj['color_primary']) ?>" aria-hidden="true"></div>
            <div class="project-card-title">
                <h3><?= e($proj['name']) ?></h3>
                <code>projects/<?= e($proj['slug']) ?>/</code>
            </div>
            <div class="project-card-menu">
                <button class="btn-icon btn-dropdown-toggle" aria-label="<?= e(__('admin.actions')) ?>" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                </button>
                <div class="dropdown-menu" role="menu">
                    <a href="/project/<?= e($proj['slug']) ?>" class="dropdown-item" role="menuitem" target="_blank">
                        <i class="bi bi-eye" aria-hidden="true"></i> <?= e(__('admin.open')) ?>
                    </a>
                    <a href="/admin/projects/<?= e($proj['slug']) ?>/edit" class="dropdown-item" role="menuitem">
                        <i class="bi bi-pencil" aria-hidden="true"></i> <?= e(__('general.edit')) ?>
                    </a>
                    <button class="dropdown-item btn-compile-project" role="menuitem"
                            data-slug="<?= e($proj['slug']) ?>">
                        <i class="bi bi-gear" aria-hidden="true"></i> <?= e(__('admin.compile_css')) ?>
                    </button>
                    <button class="dropdown-item btn-toggle-project" role="menuitem"
                            data-project-id="<?= (int)$proj['id'] ?>"
                            data-active="<?= $proj['is_active'] ? '1' : '0' ?>"
                            data-name="<?= e($proj['name']) ?>">
                        <i class="bi <?= $proj['is_active'] ? 'bi-toggle-off' : 'bi-toggle-on' ?>" aria-hidden="true"></i>
                        <?= $proj['is_active'] ? e(__('admin.deactivate')) : e(__('admin.activate')) ?>
                    </button>
                    <?php if (!$proj['is_protected']): ?>
                    <button class="dropdown-item dropdown-item-danger btn-delete-project" role="menuitem"
                            data-project-id="<?= (int)$proj['id'] ?>"
                            data-name="<?= e($proj['name']) ?>">
                        <i class="bi bi-trash" aria-hidden="true"></i> <?= e(__('general.delete')) ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="project-card-meta">
            <span><i class="bi bi-file-earmark" aria-hidden="true"></i> <?= count($proj['pages']) ?> <?= count($proj['pages']) === 1 ? 'página' : 'páginas' ?></span>
            <?php if ($proj['hasCss']): ?><span><i class="bi bi-palette" aria-hidden="true"></i> CSS</span><?php endif; ?>
            <?php if ($proj['hasJs']): ?><span><i class="bi bi-code-slash" aria-hidden="true"></i> JS</span><?php endif; ?>
            <?php if (!$proj['is_active']): ?><span class="status-badge status-inactive"><?= e(__('admin.badge_inactive')) ?></span><?php endif; ?>
        </div>

        <div class="project-card-colors">
            <span class="color-swatch" style="background: <?= e($proj['color_primary']) ?>" title="<?= e(__('admin.color_primary')) ?>"></span>
            <span class="color-swatch" style="background: <?= e($proj['color_secondary']) ?>" title="<?= e(__('admin.color_secondary')) ?>"></span>
        </div>
    </article>
    <?php endforeach; ?>
    <?php endif; ?>
</section>

<!-- Modal confirmación -->
<div id="confirm-modal" class="overlay hidden" aria-labelledby="confirm-title">
    <div class="modal" role="alertdialog" aria-labelledby="confirm-title" aria-describedby="confirm-body">
        <div class="modal-icon" aria-hidden="true"><i class="bi bi-exclamation-triangle"></i></div>
        <h3 id="confirm-title"><?= e(__('general.confirm')) ?></h3>
        <p id="confirm-body"></p>
        <div class="modal-actions">
            <button id="btn-confirm-action" class="btn btn-danger"></button>
            <button id="btn-cancel-action" class="btn btn-secondary"><?= e(__('general.cancel')) ?></button>
        </div>
    </div>
</div>
