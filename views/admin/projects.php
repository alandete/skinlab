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
    <button id="btn-new-project" class="btn btn-primary">
        <i class="bi bi-plus-lg" aria-hidden="true"></i> <?= e(__('admin.new_project')) ?>
    </button>
</header>

<!-- Formulario crear proyecto (oculto) -->
<section id="project-form" class="card form-card hidden" aria-label="<?= e(__('admin.create_project')) ?>">
    <h3><?= e(__('admin.create_project')) ?></h3>

    <div class="form-group">
        <label for="project-name"><?= e(__('admin.project_name')) ?></label>
        <input type="text" id="project-name" class="form-input"
               placeholder="<?= e(__('admin.project_name_hint')) ?>" autocomplete="off">
        <p class="form-hint"><?= e(__('admin.project_folder')) ?>: <code id="slug-preview">---</code></p>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><?= e(__('admin.brand_colors')) ?></label>
            <p class="form-hint mb-1"><?= e(__('admin.brand_colors_hint')) ?></p>
            <div class="color-inputs">
                <div class="color-field">
                    <input type="color" id="color-primary-picker" value="#0374B5">
                    <input type="text" id="color-primary" class="form-input form-input-sm color-hex" value="#0374B5" maxlength="7">
                    <small><?= e(__('admin.color_primary')) ?></small>
                </div>
                <div class="color-field">
                    <input type="color" id="color-secondary-picker" value="#2D3B45">
                    <input type="text" id="color-secondary" class="form-input form-input-sm color-hex" value="#2D3B45" maxlength="7">
                    <small><?= e(__('admin.color_secondary')) ?></small>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label><?= e(__('admin.nav_colors')) ?></label>
            <p class="form-hint mb-1"><?= e(__('admin.nav_colors_hint')) ?></p>
            <div class="color-inputs">
                <div class="color-field">
                    <input type="color" id="nav-bg-picker" value="#394B58">
                    <input type="text" id="nav-bg-color" class="form-input form-input-sm color-hex" value="#394B58" maxlength="7">
                    <small><?= e(__('admin.nav_bg_color')) ?></small>
                </div>
                <div class="color-field">
                    <input type="color" id="nav-text-picker" value="#FFFFFF">
                    <input type="text" id="nav-text-color" class="form-input form-input-sm color-hex" value="#FFFFFF" maxlength="7">
                    <small><?= e(__('admin.nav_text_color')) ?></small>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label><?= e(__('admin.organization')) ?></label>
            <div class="org-row">
                <select id="org-type" class="form-input">
                    <option value="none"><?= e(__('admin.org_none')) ?></option>
                    <option value="semanas"><?= e(__('admin.org_weeks')) ?></option>
                    <option value="modulos"><?= e(__('admin.org_modules')) ?></option>
                    <option value="unidades"><?= e(__('admin.org_units')) ?></option>
                </select>
                <input type="number" id="org-count" class="form-input form-input-sm" min="1" max="30" value="4" disabled placeholder="<?= e(__('admin.org_count')) ?>">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label><?= e(__('admin.external_libs')) ?></label>
        <div class="cdn-grid">
            <?php foreach ($cdns as $key => $url): ?>
            <label class="cdn-option">
                <input type="checkbox" name="cdns[]" value="<?= e($key) ?>">
                <span class="cdn-option-label">
                    <strong><?= e($cdnNames[$key] ?? $key) ?></strong>
                    <small><?= e($cdnDescs[$key] ?? '') ?></small>
                </span>
            </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-actions">
        <button id="btn-create-project" class="btn btn-primary">
            <i class="bi bi-check-lg" aria-hidden="true"></i> <?= e(__('general.create')) ?>
        </button>
        <button id="btn-cancel-project" class="btn btn-secondary">
            <i class="bi bi-x-lg" aria-hidden="true"></i> <?= e(__('general.cancel')) ?>
        </button>
    </div>
</section>

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
                    <button class="dropdown-item btn-edit-project" role="menuitem"
                            data-project-id="<?= (int)$proj['id'] ?>"
                            data-name="<?= e($proj['name']) ?>"
                            data-primary="<?= e($proj['color_primary']) ?>"
                            data-secondary="<?= e($proj['color_secondary']) ?>"
                            data-org-type="<?= e($proj['org_type']) ?>"
                            data-org-count="<?= (int)$proj['org_count'] ?>"
                            data-cdns="<?= e(is_array($proj['cdns']) ? implode(',', $proj['cdns']) : '') ?>"
                            data-nav-bg="<?= e($proj['nav_bg_color']) ?>"
                            data-nav-text="<?= e($proj['nav_text_color']) ?>">
                        <i class="bi bi-pencil" aria-hidden="true"></i> <?= e(__('general.edit')) ?>
                    </button>
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

<!-- Modal editar proyecto -->
<div id="edit-project-modal" class="overlay hidden" aria-labelledby="edit-project-title">
    <div class="modal modal-form modal-form-lg" role="dialog" aria-labelledby="edit-project-title">
        <h3 id="edit-project-title"><i class="bi bi-pencil" aria-hidden="true"></i> <?= e(__('admin.edit_project')) ?></h3>
        <input type="hidden" id="edit-project-id">

        <!-- Nombre -->
        <div class="form-group">
            <label for="edit-project-name"><?= e(__('admin.project_name')) ?></label>
            <input type="text" id="edit-project-name" class="form-input" autocomplete="off">
        </div>

        <!-- Colores -->
        <div class="form-group">
            <label class="form-label"><?= e(__('admin.brand_colors')) ?></label>
            <div class="edit-colors-grid">
                <div class="edit-color-group">
                    <span class="edit-color-context"><?= e(__('admin.brand_colors')) ?></span>
                    <div class="edit-color-pair">
                        <div class="color-field">
                            <small><?= e(__('admin.color_primary')) ?></small>
                            <div class="color-input-row">
                                <input type="color" id="edit-color-primary-picker" value="#0374B5">
                                <input type="text" id="edit-color-primary" class="form-input form-input-sm color-hex" value="#0374B5" maxlength="7">
                            </div>
                        </div>
                        <div class="color-field">
                            <small><?= e(__('admin.color_secondary')) ?></small>
                            <div class="color-input-row">
                                <input type="color" id="edit-color-secondary-picker" value="#2D3B45">
                                <input type="text" id="edit-color-secondary" class="form-input form-input-sm color-hex" value="#2D3B45" maxlength="7">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="edit-color-group">
                    <span class="edit-color-context"><?= e(__('admin.nav_colors')) ?></span>
                    <div class="edit-color-pair">
                        <div class="color-field">
                            <small><?= e(__('admin.nav_bg_color')) ?></small>
                            <div class="color-input-row">
                                <input type="color" id="edit-nav-bg-picker" value="#394B58">
                                <input type="text" id="edit-nav-bg-color" class="form-input form-input-sm color-hex" value="#394B58" maxlength="7">
                            </div>
                        </div>
                        <div class="color-field">
                            <small><?= e(__('admin.nav_text_color')) ?></small>
                            <div class="color-input-row">
                                <input type="color" id="edit-nav-text-picker" value="#FFFFFF">
                                <input type="text" id="edit-nav-text-color" class="form-input form-input-sm color-hex" value="#FFFFFF" maxlength="7">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Organización -->
        <div class="form-group">
            <label class="form-label"><?= e(__('admin.organization')) ?></label>
            <div class="org-row">
                <select id="edit-org-type" class="form-input">
                    <option value="none"><?= e(__('admin.org_none')) ?></option>
                    <option value="semanas"><?= e(__('admin.org_weeks')) ?></option>
                    <option value="modulos"><?= e(__('admin.org_modules')) ?></option>
                    <option value="unidades"><?= e(__('admin.org_units')) ?></option>
                </select>
                <input type="number" id="edit-org-count" class="form-input form-input-sm" min="1" max="30" value="4" disabled placeholder="<?= e(__('admin.org_count')) ?>">
            </div>
            <p class="form-hint"><?= e(__('admin.org_add_hint')) ?></p>
        </div>

        <!-- Librerías externas -->
        <div class="form-group">
            <label class="form-label"><?= e(__('admin.external_libs')) ?></label>
            <div class="cdn-inline" id="edit-cdn-grid">
                <?php foreach ($cdns as $key => $url): ?>
                <label class="cdn-chip">
                    <input type="checkbox" name="edit_cdns[]" value="<?= e($key) ?>">
                    <span><?= e($cdnNames[$key] ?? $key) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="modal-actions">
            <button id="btn-save-project" class="btn btn-primary">
                <i class="bi bi-check-lg" aria-hidden="true"></i> <?= e(__('general.save')) ?>
            </button>
            <button id="btn-cancel-edit-project" class="btn btn-secondary"><?= e(__('general.cancel')) ?></button>
        </div>
    </div>
</div>

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
