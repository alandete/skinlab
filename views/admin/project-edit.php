<?php
$cdnNames = [
    'bootstrap'       => __('admin.cdn_bootstrap'),
    'bootstrap-icons' => __('admin.cdn_bootstrap_icons'),
    'fontawesome'     => __('admin.cdn_fontawesome'),
    'animate'         => __('admin.cdn_animate'),
];
$p = $project ?? null;
$slug = $p ? $p['slug'] : '';
$activeCdns = $p ? (is_string($p['cdns']) ? json_decode($p['cdns'], true) : ($p['cdns'] ?? [])) : [];
$pages = $pages ?? ['organization' => [], 'custom' => [], 'activities' => []];
?>

<!-- Traducciones para JS -->
<div id="js-lang" class="hidden"
    data-confirm_delete_page="<?= e(__('admin.confirm_delete_page')) ?>"
    data-delete="<?= e(__('general.delete')) ?>"
    data-page_deleted="<?= e(__('admin.page_deleted')) ?>"
    data-pages_created="<?= e(__('admin.pages_created')) ?>"
    data-required="<?= e(__('general.field_required')) ?>"
></div>

<div id="toast-container" class="sl-toast-container" aria-live="polite"></div>

<!-- Header con acciones -->
<header class="panel-header">
    <h2>
        <i class="bi bi-<?= $isNew ? 'plus-lg' : 'pencil' ?>" aria-hidden="true"></i>
        <?= $isNew ? e(__('admin.create_project')) : e($p['name']) ?>
    </h2>
    <div class="panel-header-actions">
        <button id="btn-save-project" class="btn btn-primary" data-slug="<?= e($slug) ?>" data-is-new="<?= $isNew ? '1' : '0' ?>" data-project-id="<?= (int)($p['id'] ?? 0) ?>">
            <i class="bi bi-check-lg" aria-hidden="true"></i> <?= e(__('general.save')) ?>
        </button>
        <a href="/admin/projects" class="btn btn-secondary">
            <?= e(__('general.cancel')) ?>
        </a>
    </div>
</header>

<div class="project-edit-layout">

    <!-- ══════════ COLUMNA IZQUIERDA: Datos del proyecto ══════════ -->
    <section class="project-edit-main">

        <!-- Nombre -->
        <div class="form-group">
            <label class="form-label" for="project-name"><?= e(__('admin.project_name')) ?></label>
            <input type="text" id="project-name" class="form-input"
                   value="<?= $p ? e($p['name']) : '' ?>"
                   placeholder="<?= e(__('admin.project_name_hint')) ?>" autocomplete="off">
            <?php if ($isNew): ?>
            <p class="form-hint"><?= e(__('admin.project_folder')) ?>: <code id="slug-preview">---</code></p>
            <?php else: ?>
            <p class="form-hint"><code><?= e($slug) ?></code></p>
            <?php endif; ?>
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
                                <input type="color" id="color-primary-picker" value="<?= e($p['color_primary'] ?? '#0374B5') ?>">
                                <input type="text" id="color-primary" class="form-input form-input-sm color-hex" value="<?= e($p['color_primary'] ?? '#0374B5') ?>" maxlength="7">
                            </div>
                        </div>
                        <div class="color-field">
                            <small><?= e(__('admin.color_secondary')) ?></small>
                            <div class="color-input-row">
                                <input type="color" id="color-secondary-picker" value="<?= e($p['color_secondary'] ?? '#2D3B45') ?>">
                                <input type="text" id="color-secondary" class="form-input form-input-sm color-hex" value="<?= e($p['color_secondary'] ?? '#2D3B45') ?>" maxlength="7">
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
                                <input type="color" id="nav-bg-picker" value="<?= e($p['nav_bg_color'] ?? '#394B58') ?>">
                                <input type="text" id="nav-bg-color" class="form-input form-input-sm color-hex" value="<?= e($p['nav_bg_color'] ?? '#394B58') ?>" maxlength="7">
                            </div>
                        </div>
                        <div class="color-field">
                            <small><?= e(__('admin.nav_text_color')) ?></small>
                            <div class="color-input-row">
                                <input type="color" id="nav-text-picker" value="<?= e($p['nav_text_color'] ?? '#FFFFFF') ?>">
                                <input type="text" id="nav-text-color" class="form-input form-input-sm color-hex" value="<?= e($p['nav_text_color'] ?? '#FFFFFF') ?>" maxlength="7">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Organización (solo al crear) -->
        <?php if ($isNew): ?>
        <div class="form-group">
            <label class="form-label"><?= e(__('admin.organization')) ?></label>
            <div class="org-row">
                <select id="org-type" class="form-input">
                    <option value="none"><?= e(__('admin.org_none')) ?></option>
                    <option value="semanas"><?= e(__('admin.org_weeks')) ?></option>
                    <option value="modulos"><?= e(__('admin.org_modules')) ?></option>
                    <option value="unidades"><?= e(__('admin.org_units')) ?></option>
                </select>
                <input type="number" id="org-count" class="form-input form-input-sm"
                       min="1" max="30" value="4" disabled
                       placeholder="<?= e(__('admin.org_count')) ?>">
            </div>
            <p class="form-hint"><?= e(__('admin.org_add_hint')) ?></p>
        </div>
        <?php endif; ?>

        <!-- Actividades -->
        <div class="form-group">
            <label class="form-label"><?= e(__('admin.pages_section_act')) ?></label>
            <div class="cdn-inline">
                <?php
                $existingActivities = array_map(function ($a) { return $a['slug']; }, $pages['activities'] ?? []);
                $activityOptions = [
                    'tarea' => 'Tarea',
                    'foros' => 'Foros',
                    'quiz'  => 'Quiz',
                ];
                ?>
                <?php foreach ($activityOptions as $actSlug => $actName): ?>
                <label class="cdn-chip">
                    <input type="checkbox" name="activities[]" value="<?= e($actSlug) ?>"
                           <?= in_array($actSlug, $existingActivities) || $isNew ? 'checked' : '' ?>>
                    <span><?= e($actName) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Páginas adicionales -->
        <div class="form-group">
            <label class="form-label"><?= e(__('admin.custom_pages')) ?></label>
            <p class="form-hint"><?= e(__('admin.custom_pages_hint')) ?></p>
            <div class="form-row" style="max-width:20rem;">
                <div class="form-group">
                    <label class="form-label" for="custom-pages-count"><?= e(__('admin.org_count')) ?></label>
                    <input type="number" id="custom-pages-count" class="form-input form-input-sm" min="0" max="20" value="0" placeholder="0">
                </div>
            </div>
            <div id="new-pages-container" class="new-pages-container"></div>
            <?php if (!$isNew): ?>
            <button type="button" id="btn-save-pages" class="btn btn-primary btn-sm mt-1">
                <i class="bi bi-check-lg" aria-hidden="true"></i> <?= e(__('admin.add_pages')) ?>
            </button>
            <?php endif; ?>
        </div>

        <!-- Librerías -->
        <div class="form-group">
            <label class="form-label"><?= e(__('admin.external_libs')) ?></label>
            <div class="cdn-inline">
                <?php foreach ($cdns as $key => $url): ?>
                <label class="cdn-chip">
                    <input type="checkbox" name="cdns[]" value="<?= e($key) ?>"
                           <?= in_array($key, $activeCdns) ? 'checked' : '' ?>>
                    <span><?= e($cdnNames[$key] ?? $key) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

    </section>

    <!-- ══════════ COLUMNA DERECHA: Páginas existentes (solo edición) ══════════ -->
    <?php if (!$isNew): ?>
    <aside class="project-edit-sidebar">
        <h3><?= e(__('nav.projects')) ?> — <?= e($p['name']) ?></h3>

        <?php if (!empty($pages['organization'])): ?>
        <div class="pages-section">
            <h4><?= e(__('admin.pages_section_org')) ?></h4>
            <ul class="pages-list">
                <?php foreach ($pages['organization'] as $pg): ?>
                <li class="pages-list-item" data-slug="<?= e($pg['slug']) ?>">
                    <span><?= e($pg['name']) ?></span>
                    <button class="btn-icon btn-icon-sm btn-delete-page" data-slug="<?= e($pg['slug']) ?>" data-name="<?= e($pg['name']) ?>"
                            title="<?= e(__('admin.delete_page')) ?>" aria-label="<?= e(__('admin.delete_page')) ?> — <?= e($pg['name']) ?>">
                        <i class="bi bi-trash" aria-hidden="true"></i>
                    </button>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($pages['custom'])): ?>
        <div class="pages-section">
            <h4><?= e(__('admin.pages_section_custom')) ?></h4>
            <ul class="pages-list">
                <?php foreach ($pages['custom'] as $pg): ?>
                <li class="pages-list-item" data-slug="<?= e($pg['slug']) ?>">
                    <span><?= e($pg['name']) ?></span>
                    <button class="btn-icon btn-icon-sm btn-delete-page" data-slug="<?= e($pg['slug']) ?>" data-name="<?= e($pg['name']) ?>"
                            title="<?= e(__('admin.delete_page')) ?>" aria-label="<?= e(__('admin.delete_page')) ?> — <?= e($pg['name']) ?>">
                        <i class="bi bi-trash" aria-hidden="true"></i>
                    </button>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($pages['activities'])): ?>
        <div class="pages-section">
            <h4><?= e(__('admin.pages_section_act')) ?></h4>
            <ul class="pages-list">
                <?php foreach ($pages['activities'] as $pg): ?>
                <li class="pages-list-item" data-slug="<?= e($pg['slug']) ?>">
                    <span><?= e($pg['name']) ?></span>
                    <button class="btn-icon btn-icon-sm btn-delete-page" data-slug="<?= e($pg['slug']) ?>" data-name="<?= e($pg['name']) ?>"
                            title="<?= e(__('admin.delete_page')) ?>" aria-label="<?= e(__('admin.delete_page')) ?> — <?= e($pg['name']) ?>">
                        <i class="bi bi-trash" aria-hidden="true"></i>
                    </button>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (empty($pages['organization']) && empty($pages['custom']) && empty($pages['activities'])): ?>
        <p class="form-hint"><?= e(__('admin.no_projects')) ?></p>
        <?php endif; ?>
    </aside>
    <?php endif; ?>

</div>

<!-- Modal confirmación eliminar página -->
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
