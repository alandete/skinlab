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
$orgType = $p ? ($p['org_type'] ?? 'none') : 'none';
$orgCount = $p ? (int) ($p['org_count'] ?? 0) : 0;
$existingActivities = array_map(fn($a) => $a['slug'], $pages['activities'] ?? []);
$activityOptions = ['tarea' => 'Tarea', 'foros' => 'Foros', 'quiz' => 'Quiz'];
?>

<!-- Traducciones para JS -->
<div id="js-lang" class="hidden"
    data-confirm_delete_page="<?= e(__('admin.confirm_delete_page')) ?>"
    data-delete="<?= e(__('general.delete')) ?>"
    data-page_name="<?= e(__('admin.page_name')) ?>"
    data-required="<?= e(__('general.field_required')) ?>"
    data-rename_page="<?= e(__('admin.rename_page')) ?>"
    data-invalid_slug="<?= e(__('admin.invalid_slug')) ?>"
    data-slug_collision="<?= e(__('admin.slug_collision')) ?>"
    data-add_org_semanas="<?= e(__('admin.add_org_semanas')) ?>"
    data-add_org_modulos="<?= e(__('admin.add_org_modulos')) ?>"
    data-add_org_unidades="<?= e(__('admin.add_org_unidades')) ?>"
    data-confirm_delete="<?= e(__('admin.confirm_delete')) ?>"
    data-project_deleted="<?= e(__('admin.project_deleted')) ?>"
></div>

<div id="toast-container" class="sl-toast-container" aria-live="polite"></div>

<!-- Header con acciones -->
<header class="panel-header">
    <h2>
        <i class="bi bi-<?= $isNew ? 'plus-lg' : 'pencil' ?>" aria-hidden="true"></i>
        <?= $isNew ? e(__('admin.create_project')) : e($p['name']) ?>
    </h2>
    <div class="panel-header-actions">
        <?php if (!$isNew): ?>
        <button id="btn-delete-project" class="btn btn-danger-ghost"
                data-project-id="<?= (int)($p['id'] ?? 0) ?>"
                data-name="<?= e($p['name']) ?>"
                <?= !empty($p['is_protected']) ? 'hidden' : '' ?>>
            <i class="bi bi-trash" aria-hidden="true"></i> <?= e(__('admin.delete_project')) ?>
        </button>
        <?php endif; ?>
        <button id="btn-save-project" class="btn btn-primary"
                data-slug="<?= e($slug) ?>"
                data-is-new="<?= $isNew ? '1' : '0' ?>"
                data-project-id="<?= (int)($p['id'] ?? 0) ?>">
            <i class="bi bi-check-lg" aria-hidden="true"></i> <?= e(__('general.save')) ?>
        </button>
        <a href="/admin/projects" class="btn btn-secondary">
            <?= e(__('general.cancel')) ?>
        </a>
    </div>
</header>

<!-- Tabs -->
<nav class="project-tabs" role="tablist" aria-label="<?= e(__('admin.project_sections')) ?>">
    <button type="button" class="project-tab active"
            role="tab" id="tab-config" aria-controls="panel-config" aria-selected="true" tabindex="0">
        <i class="bi bi-gear" aria-hidden="true"></i>
        <span><?= e(__('admin.tab_config')) ?></span>
    </button>
    <button type="button" class="project-tab"
            role="tab" id="tab-content" aria-controls="panel-content" aria-selected="false" tabindex="-1">
        <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
        <span><?= e(__('admin.tab_content')) ?></span>
    </button>
</nav>

<div class="project-edit-form">

<!-- ═══════════════════ PESTAÑA 1: CONFIGURACIÓN ═══════════════════ -->
<section class="project-panel active" id="panel-config" role="tabpanel" aria-labelledby="tab-config">

    <!-- Identificación -->
    <fieldset class="form-section">
        <legend class="form-section-title"><?= e(__('admin.section_identification')) ?></legend>

        <div class="form-group">
            <label class="form-label" for="project-name"><?= e(__('admin.project_name')) ?></label>
            <input type="text" id="project-name" class="form-input"
                   value="<?= $p ? e($p['name']) : '' ?>"
                   placeholder="<?= e(__('admin.project_name_hint')) ?>" autocomplete="off"
                   maxlength="100">
            <?php if ($isNew): ?>
            <p class="form-hint"><?= e(__('admin.project_folder')) ?>: <code id="slug-preview">---</code></p>
            <?php else: ?>
            <p class="form-hint"><?= e(__('admin.project_folder')) ?>: <code><?= e($slug) ?></code></p>
            <?php endif; ?>
        </div>
    </fieldset>

    <!-- Colores de marca -->
    <section class="color-row" role="group" aria-labelledby="colors-brand-title">
        <div class="color-row-head">
            <h3 id="colors-brand-title"><?= e(__('admin.brand_colors')) ?></h3>
            <p class="form-hint"><?= e(__('admin.brand_colors_hint')) ?></p>
        </div>
        <div class="color-field">
            <label for="color-primary"><?= e(__('admin.color_primary')) ?></label>
            <div class="color-input-row">
                <input type="color" id="color-primary-picker" value="<?= e($p['color_primary'] ?? '#0374B5') ?>"
                       aria-label="<?= e(__('admin.color_primary')) ?>">
                <input type="text" id="color-primary" class="form-input color-hex"
                       value="<?= e($p['color_primary'] ?? '#0374B5') ?>" maxlength="7">
            </div>
        </div>
        <div class="color-field">
            <label for="color-secondary"><?= e(__('admin.color_secondary')) ?></label>
            <div class="color-input-row">
                <input type="color" id="color-secondary-picker" value="<?= e($p['color_secondary'] ?? '#2D3B45') ?>"
                       aria-label="<?= e(__('admin.color_secondary')) ?>">
                <input type="text" id="color-secondary" class="form-input color-hex"
                       value="<?= e($p['color_secondary'] ?? '#2D3B45') ?>" maxlength="7">
            </div>
        </div>
    </section>

    <!-- Colores Nav Canvas -->
    <section class="color-row" role="group" aria-labelledby="colors-nav-title">
        <div class="color-row-head">
            <h3 id="colors-nav-title"><?= e(__('admin.nav_colors')) ?></h3>
            <p class="form-hint"><?= e(__('admin.nav_colors_hint')) ?></p>
        </div>
        <div class="color-field">
            <label for="nav-bg-color"><?= e(__('admin.nav_bg_color')) ?></label>
            <div class="color-input-row">
                <input type="color" id="nav-bg-picker" value="<?= e($p['nav_bg_color'] ?? '#394B58') ?>"
                       aria-label="<?= e(__('admin.nav_bg_color')) ?>">
                <input type="text" id="nav-bg-color" class="form-input color-hex"
                       value="<?= e($p['nav_bg_color'] ?? '#394B58') ?>" maxlength="7">
            </div>
        </div>
        <div class="color-field">
            <label for="nav-text-color"><?= e(__('admin.nav_text_color')) ?></label>
            <div class="color-input-row">
                <input type="color" id="nav-text-picker" value="<?= e($p['nav_text_color'] ?? '#FFFFFF') ?>"
                       aria-label="<?= e(__('admin.nav_text_color')) ?>">
                <input type="text" id="nav-text-color" class="form-input color-hex"
                       value="<?= e($p['nav_text_color'] ?? '#FFFFFF') ?>" maxlength="7">
            </div>
        </div>
    </section>

    <!-- Librerías externas -->
    <fieldset class="form-section">
        <legend class="form-section-title"><?= e(__('admin.external_libs')) ?></legend>

        <div class="cdn-inline">
            <?php foreach ($cdns as $key => $url): ?>
            <label class="cdn-chip">
                <input type="checkbox" name="cdns[]" value="<?= e($key) ?>"
                       <?= in_array($key, $activeCdns) ? 'checked' : '' ?>>
                <span><?= e($cdnNames[$key] ?? $key) ?></span>
            </label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <!-- Protección (solo admin, en edición) -->
    <?php if (!$isNew && is_admin()): ?>
    <fieldset class="form-section">
        <legend class="form-section-title"><?= e(__('admin.protection')) ?></legend>
        <label class="toggle-row">
            <input type="checkbox" id="is-protected" <?= !empty($p['is_protected']) ? 'checked' : '' ?>>
            <span class="toggle-row-text">
                <strong><?= e(__('admin.protect_label')) ?></strong>
                <small class="form-hint"><?= e(__('admin.protect_hint')) ?></small>
            </span>
        </label>
    </fieldset>
    <?php endif; ?>
</section>

<!-- ═══════════════════ PESTAÑA 2: CONTENIDO ═══════════════════ -->
<section class="project-panel" id="panel-content" role="tabpanel" aria-labelledby="tab-content" hidden>

    <!-- Organización -->
    <fieldset class="form-section">
        <legend class="form-section-title"><?= e(__('admin.organization')) ?></legend>
        <p class="form-hint"><?= e(__('admin.organization_hint')) ?></p>

        <div class="org-row">
            <select id="org-type" class="form-input">
                <option value="none" <?= $orgType === 'none' ? 'selected' : '' ?>><?= e(__('admin.org_none')) ?></option>
                <option value="semanas" <?= $orgType === 'semanas' ? 'selected' : '' ?>><?= e(__('admin.org_weeks')) ?></option>
                <option value="modulos" <?= $orgType === 'modulos' ? 'selected' : '' ?>><?= e(__('admin.org_modules')) ?></option>
                <option value="unidades" <?= $orgType === 'unidades' ? 'selected' : '' ?>><?= e(__('admin.org_units')) ?></option>
            </select>
            <input type="number" id="org-count" class="form-input form-input-sm"
                   min="0" max="30"
                   value="<?= $orgCount > 0 ? (int)$orgCount : '' ?>"
                   <?= $orgType === 'none' ? 'disabled' : '' ?>
                   placeholder="<?= e(__('admin.org_count')) ?>">
        </div>

        <ul class="page-list" id="org-list" data-section="organization">
            <?php foreach ($pages['organization'] as $pg): ?>
            <li class="page-list-item" data-slug="<?= e($pg['slug']) ?>" data-old-slug="<?= e($pg['slug']) ?>">
                <span class="page-list-name"><?= e($pg['name']) ?></span>
                <div class="page-list-actions">
                    <button type="button" class="btn-icon btn-icon-sm btn-delete-page"
                            data-slug="<?= e($pg['slug']) ?>" data-name="<?= e($pg['name']) ?>"
                            title="<?= e(__('admin.delete_page')) ?>"
                            aria-label="<?= e(__('admin.delete_page')) ?> — <?= e($pg['name']) ?>">
                        <i class="bi bi-trash" aria-hidden="true"></i>
                    </button>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>

        <button type="button" id="btn-add-org-page" class="btn btn-secondary btn-sm"
                <?= $orgType === 'none' ? 'hidden' : '' ?>>
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            <span id="btn-add-org-label"></span>
        </button>
    </fieldset>

    <!-- Páginas adicionales -->
    <fieldset class="form-section">
        <legend class="form-section-title"><?= e(__('admin.custom_pages')) ?></legend>
        <p class="form-hint"><?= e(__('admin.custom_pages_hint')) ?></p>

        <ul class="page-list" id="custom-list" data-section="custom">
            <?php foreach ($pages['custom'] as $pg): ?>
            <li class="page-list-item" data-slug="<?= e($pg['slug']) ?>" data-old-slug="<?= e($pg['slug']) ?>">
                <span class="page-list-name"><?= e($pg['name']) ?></span>
                <div class="page-list-actions">
                    <button type="button" class="btn-icon btn-icon-sm btn-rename-page"
                            title="<?= e(__('admin.rename_page')) ?>"
                            aria-label="<?= e(__('admin.rename_page')) ?> — <?= e($pg['name']) ?>">
                        <i class="bi bi-pencil" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn-icon btn-icon-sm btn-delete-page"
                            data-slug="<?= e($pg['slug']) ?>" data-name="<?= e($pg['name']) ?>"
                            title="<?= e(__('admin.delete_page')) ?>"
                            aria-label="<?= e(__('admin.delete_page')) ?> — <?= e($pg['name']) ?>">
                        <i class="bi bi-trash" aria-hidden="true"></i>
                    </button>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>

        <button type="button" id="btn-add-custom-page" class="btn btn-secondary btn-sm">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            <?= e(__('admin.add_custom_page')) ?>
        </button>
    </fieldset>

    <!-- Actividades -->
    <fieldset class="form-section">
        <legend class="form-section-title"><?= e(__('admin.pages_section_act')) ?></legend>
        <p class="form-hint"><?= e(__('admin.activities_hint')) ?></p>

        <div class="cdn-inline">
            <?php foreach ($activityOptions as $actSlug => $actName): ?>
            <label class="cdn-chip">
                <input type="checkbox" name="activities[]" value="<?= e($actSlug) ?>"
                       <?= in_array($actSlug, $existingActivities) || $isNew ? 'checked' : '' ?>>
                <span><?= e($actName) ?></span>
            </label>
            <?php endforeach; ?>
        </div>
    </fieldset>
</section>

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
