<?php
$roleIcons = ['admin' => 'bi-shield-lock', 'editor' => 'bi-pencil-square', 'guest' => 'bi-person'];
$rolePerms = [
    'admin'  => __('admin.perms_admin'),
    'editor' => __('admin.perms_editor'),
    'guest'  => __('admin.perms_guest'),
];
$currentUserId = (int) ($_SESSION['user_id'] ?? 0);
?>

<!-- Traducciones para JS -->
<script>
window.LANG = {
    confirm: '<?= e(__('general.confirm')) ?>',
    cancel: '<?= e(__('general.cancel')) ?>',
    delete: '<?= e(__('general.delete')) ?>',
    save: '<?= e(__('general.save')) ?>',
    create: '<?= e(__('general.create')) ?>',
    required: '<?= e(__('general.field_required')) ?>',
    password_min: '<?= e(__('auth.password_min')) ?>',
    password_max: '<?= e(__('auth.password_max')) ?>',
    copy_password: '<?= e(__('admin.copy_password')) ?>',
    credentials_created: '<?= e(__('admin.credentials_created')) ?>',
    confirm_toggle: '<?= e(__('admin.confirm_toggle')) ?>',
    confirm_toggle_deactivate: '<?= e(__('admin.confirm_toggle_deactivate')) ?>',
    activate: '<?= e(__('admin.activate')) ?>',
    deactivate: '<?= e(__('admin.deactivate')) ?>',
    current_password_hint: '<?= e(__('admin.current_password_hint')) ?>',
    no_users_found: '<?= e(__('admin.no_users_found')) ?>',
    confirm_delete: '<?= e(__('admin.confirm_delete_msg')) ?>',
    badge_inactive: '<?= e(__('admin.badge_inactive')) ?>'
};
</script>

<!-- Toast container -->
<div id="toast-container" class="toast-container" aria-live="polite"></div>

<!-- Panel header -->
<header class="panel-header">
    <h2><i class="bi bi-people" aria-hidden="true"></i> <?= e(__('admin.users_title')) ?></h2>
    <button id="btn-add-user" class="btn btn-primary">
        <i class="bi bi-person-plus" aria-hidden="true"></i> <?= e(__('admin.add_user')) ?>
    </button>
</header>

<!-- Barra de búsqueda y filtros -->
<section class="users-toolbar" aria-label="<?= e(__('admin.search_users')) ?>">
    <div class="users-search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="text" id="user-search" class="form-input" placeholder="<?= e(__('admin.search_users')) ?>">
    </div>
    <div class="users-filters">
        <button class="filter-btn active" data-filter="all"><?= e(__('admin.filter_all')) ?></button>
        <button class="filter-btn" data-filter="admin"><?= e(__('admin.role_admin')) ?></button>
        <button class="filter-btn" data-filter="editor"><?= e(__('admin.role_editor')) ?></button>
        <button class="filter-btn" data-filter="guest"><?= e(__('admin.role_guest')) ?></button>
        <span class="filter-sep" aria-hidden="true"></span>
        <button class="filter-btn" data-filter="active"><?= e(__('admin.filter_active')) ?></button>
        <button class="filter-btn" data-filter="inactive"><?= e(__('admin.filter_inactive')) ?></button>
    </div>
</section>

<!-- Formulario crear usuario (oculto) -->
<section id="user-form" class="card form-card hidden" aria-label="<?= e(__('admin.add_user')) ?>">
    <h3><?= e(__('admin.add_user')) ?></h3>
    <div class="form-row">
        <div class="form-group">
            <label for="new-username"><?= e(__('admin.username')) ?></label>
            <input type="text" id="new-username" class="form-input" placeholder="usuario"
                   autocomplete="off" aria-describedby="username-hint">
            <p class="form-hint" id="username-hint"><?= e(__('auth.setup_admin_hint')) ?></p>
        </div>
        <div class="form-group">
            <label for="new-email"><?= e(__('admin.email')) ?> <span class="text-danger">*</span></label>
            <input type="email" id="new-email" class="form-input" placeholder="correo@ejemplo.com" autocomplete="off" required>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="new-password"><?= e(__('admin.new_password')) ?></label>
            <input type="password" id="new-password" class="form-input"
                   placeholder="<?= e(__('auth.setup_password_hint')) ?>" autocomplete="new-password">
        </div>
        <div class="form-group">
            <label for="new-role"><?= e(__('admin.user_role')) ?></label>
            <select id="new-role" class="form-input">
                <option value="editor"><?= e(__('admin.role_editor')) ?></option>
                <option value="guest"><?= e(__('admin.role_guest')) ?></option>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <button id="btn-create-user" class="btn btn-primary">
            <i class="bi bi-check-lg" aria-hidden="true"></i> <?= e(__('general.create')) ?>
        </button>
        <button id="btn-cancel-user" class="btn btn-secondary">
            <i class="bi bi-x-lg" aria-hidden="true"></i> <?= e(__('general.cancel')) ?>
        </button>
    </div>

    <!-- Panel de credenciales post-creación -->
    <div id="credentials-panel" class="credentials-panel hidden">
        <h4><i class="bi bi-check-circle" aria-hidden="true"></i> <?= e(__('admin.credentials_created')) ?></h4>
        <div class="credentials-row">
            <span class="credentials-label"><?= e(__('admin.username')) ?></span>
            <code id="cred-username"></code>
        </div>
        <div class="credentials-row">
            <span class="credentials-label"><?= e(__('auth.password')) ?></span>
            <code id="cred-password"></code>
            <button id="btn-copy-password" class="btn-icon" title="<?= e(__('admin.copy_password')) ?>"
                    aria-label="<?= e(__('admin.copy_password')) ?>">
                <i class="bi bi-clipboard" aria-hidden="true"></i>
            </button>
        </div>
        <button id="btn-close-credentials" class="btn btn-secondary btn-sm">
            <?= e(__('general.close')) ?>
        </button>
    </div>
</section>

<!-- Lista de usuarios -->
<section class="users-grid" id="users-grid" aria-label="<?= e(__('admin.users_title')) ?>">
    <?php foreach ($users as $user): ?>
    <article class="user-card <?= !$user['is_active'] ? 'user-card-inactive' : '' ?>"
             data-user-id="<?= (int)$user['id'] ?>"
             data-role="<?= e($user['role']) ?>"
             data-active="<?= $user['is_active'] ? '1' : '0' ?>"
             data-username="<?= e($user['username']) ?>"
             aria-label="<?= e($user['username']) ?>">

        <header class="user-card-header">
            <div class="user-avatar user-avatar-<?= e($user['role']) ?>" aria-hidden="true">
                <i class="bi <?= $roleIcons[$user['role']] ?? 'bi-person' ?>"></i>
            </div>
            <div class="user-card-title">
                <h3><?= e($user['username']) ?></h3>
                <div class="user-card-badges">
                    <span class="role-badge role-badge-<?= e($user['role']) ?>">
                        <?= e(__('admin.role_' . $user['role'])) ?>
                    </span>
                    <?php if (!$user['is_active']): ?>
                        <span class="status-badge status-inactive"><?= e(__('admin.badge_inactive')) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Menú de acciones -->
            <?php if ((int)$user['id'] !== $currentUserId || $user['role'] !== 'admin'): ?>
            <div class="user-card-menu">
                <button class="btn-icon btn-dropdown-toggle"
                        aria-label="<?= e(__('admin.actions')) ?>" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                </button>
                <div class="dropdown-menu" role="menu">
                    <button class="dropdown-item btn-edit-user" role="menuitem"
                            data-user-id="<?= (int)$user['id'] ?>"
                            data-username="<?= e($user['username']) ?>"
                            data-email="<?= e($user['email'] ?? '') ?>"
                            data-role="<?= e($user['role']) ?>">
                        <i class="bi bi-pencil" aria-hidden="true"></i> <?= e(__('admin.edit_user')) ?>
                    </button>
                    <button class="dropdown-item btn-change-pw" role="menuitem"
                            data-user-id="<?= (int)$user['id'] ?>"
                            data-username="<?= e($user['username']) ?>">
                        <i class="bi bi-key" aria-hidden="true"></i> <?= e(__('admin.change_password')) ?>
                    </button>
                    <?php if ((int)$user['id'] !== $currentUserId): ?>
                    <button class="dropdown-item btn-toggle-user" role="menuitem"
                            data-user-id="<?= (int)$user['id'] ?>"
                            data-username="<?= e($user['username']) ?>"
                            data-active="<?= $user['is_active'] ? '1' : '0' ?>">
                        <i class="bi <?= $user['is_active'] ? 'bi-toggle-off' : 'bi-toggle-on' ?>" aria-hidden="true"></i>
                        <?= $user['is_active'] ? e(__('admin.deactivate')) : e(__('admin.activate')) ?>
                    </button>
                    <?php if ($user['role'] !== 'admin'): ?>
                    <button class="dropdown-item dropdown-item-danger btn-delete-user" role="menuitem"
                            data-user-id="<?= (int)$user['id'] ?>"
                            data-username="<?= e($user['username']) ?>">
                        <i class="bi bi-trash" aria-hidden="true"></i> <?= e(__('general.delete')) ?>
                    </button>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </header>

        <div class="user-card-body">
            <dl class="user-card-details">
                <?php if ($user['email']): ?>
                <div class="detail-row">
                    <dt><?= e(__('admin.email')) ?></dt>
                    <dd><?= e($user['email']) ?></dd>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <dt><?= e(__('admin.user_permissions')) ?></dt>
                    <dd><?= e($rolePerms[$user['role']] ?? '') ?></dd>
                </div>
                <div class="detail-row">
                    <dt><?= e(__('admin.last_login')) ?></dt>
                    <dd><?= $user['last_login_at']
                        ? e(date('d M Y, H:i', strtotime($user['last_login_at'])))
                        : '<span class="text-muted">' . e(__('admin.never_logged_in')) . '</span>' ?></dd>
                </div>
                <div class="detail-row">
                    <dt><?= e(__('admin.created')) ?></dt>
                    <dd><?= e(date('d M Y', strtotime($user['created_at']))) ?></dd>
                </div>
            </dl>
        </div>
    </article>
    <?php endforeach; ?>
</section>

<p id="no-users-msg" class="no-results hidden"><?= e(__('admin.no_users_found')) ?></p>

<!-- Correo de recuperación -->
<section class="card mt-3" aria-labelledby="recovery-title">
    <div class="recovery-section">
        <div class="recovery-info">
            <h3 id="recovery-title"><i class="bi bi-envelope" aria-hidden="true"></i> <?= e(__('admin.recovery_email')) ?></h3>
            <p class="form-hint"><?= e(__('admin.recovery_hint')) ?></p>
        </div>
        <div class="recovery-form">
            <label for="recovery-email" class="sr-only"><?= e(__('admin.recovery_email')) ?></label>
            <input type="email" id="recovery-email" class="form-input"
                   value="<?= e($recoveryEmail) ?>" placeholder="tu@correo.com">
            <button id="btn-save-email" class="btn btn-primary">
                <i class="bi bi-check-lg" aria-hidden="true"></i> <?= e(__('general.save')) ?>
            </button>
        </div>
        <?php if (empty($recoveryEmail)): ?>
        <div class="alert alert-warning mt-1" role="alert">
            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i> <?= e(__('admin.recovery_missing')) ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal genérico de confirmación -->
<dialog id="confirm-modal" class="overlay hidden" aria-labelledby="confirm-title">
    <div class="modal" role="alertdialog" aria-labelledby="confirm-title" aria-describedby="confirm-body">
        <div class="modal-icon" aria-hidden="true"><i class="bi bi-exclamation-triangle"></i></div>
        <h3 id="confirm-title"><?= e(__('general.confirm')) ?></h3>
        <p id="confirm-body"></p>
        <div class="modal-actions">
            <button id="btn-confirm-action" class="btn btn-danger"></button>
            <button id="btn-cancel-action" class="btn btn-secondary"><?= e(__('general.cancel')) ?></button>
        </div>
    </div>
</dialog>

<!-- Modal editar usuario -->
<dialog id="edit-modal" class="overlay hidden" aria-labelledby="edit-title">
    <div class="modal modal-form" role="dialog" aria-labelledby="edit-title">
        <h3 id="edit-title"><i class="bi bi-pencil" aria-hidden="true"></i> <?= e(__('admin.edit_user')) ?></h3>
        <input type="hidden" id="edit-user-id">
        <div class="form-group">
            <label for="edit-username"><?= e(__('admin.username')) ?></label>
            <input type="text" id="edit-username" class="form-input" autocomplete="off">
            <p class="form-hint"><?= e(__('auth.setup_admin_hint')) ?></p>
        </div>
        <div class="form-group">
            <label for="edit-email"><?= e(__('admin.email')) ?> <span class="text-danger">*</span></label>
            <input type="email" id="edit-email" class="form-input" placeholder="correo@ejemplo.com">
        </div>
        <div class="form-group">
            <label for="edit-role"><?= e(__('admin.user_role')) ?></label>
            <select id="edit-role" class="form-input">
                <option value="admin"><?= e(__('admin.role_admin')) ?></option>
                <option value="editor"><?= e(__('admin.role_editor')) ?></option>
                <option value="guest"><?= e(__('admin.role_guest')) ?></option>
            </select>
        </div>
        <div class="modal-actions">
            <button id="btn-save-edit" class="btn btn-primary">
                <i class="bi bi-check-lg" aria-hidden="true"></i> <?= e(__('general.save')) ?>
            </button>
            <button id="btn-cancel-edit" class="btn btn-secondary"><?= e(__('general.cancel')) ?></button>
        </div>
    </div>
</dialog>

<!-- Modal cambiar contraseña -->
<dialog id="password-modal" class="overlay hidden" aria-labelledby="pw-title">
    <div class="modal modal-form" role="dialog" aria-labelledby="pw-title">
        <h3 id="pw-title"><i class="bi bi-key" aria-hidden="true"></i> <?= e(__('admin.change_password')) ?></h3>
        <p class="modal-subtitle" id="pw-username"></p>
        <input type="hidden" id="pw-user-id">
        <div class="form-group">
            <label for="pw-current"><?= e(__('admin.current_password')) ?></label>
            <input type="password" id="pw-current" class="form-input"
                   placeholder="<?= e(__('admin.current_password_hint')) ?>" autocomplete="current-password">
        </div>
        <div class="form-group">
            <label for="pw-new"><?= e(__('admin.new_password')) ?></label>
            <input type="password" id="pw-new" class="form-input"
                   placeholder="<?= e(__('auth.setup_password_hint')) ?>" autocomplete="new-password">
        </div>
        <div class="modal-actions">
            <button id="btn-save-password" class="btn btn-primary">
                <i class="bi bi-check-lg" aria-hidden="true"></i> <?= e(__('general.save')) ?>
            </button>
            <button id="btn-cancel-password" class="btn btn-secondary"><?= e(__('general.cancel')) ?></button>
        </div>
    </div>
</dialog>
