<!-- Panel header -->
<header class="panel-header">
    <h2><i class="bi bi-people" aria-hidden="true"></i> <?= e(__('admin.users_title')) ?></h2>
    <button id="btn-add-user" class="btn btn-primary" aria-controls="user-form">
        <i class="bi bi-person-plus" aria-hidden="true"></i> <?= e(__('admin.add_user')) ?>
    </button>
</header>

<!-- Formulario crear usuario (oculto por defecto) -->
<section id="user-form" class="card form-card hidden" aria-label="<?= e(__('admin.add_user')) ?>">
    <h3><?= e(__('admin.add_user')) ?></h3>
    <div class="form-group">
        <label for="new-username"><?= e(__('admin.username')) ?></label>
        <input type="text" id="new-username" class="form-input" placeholder="usuario"
               autocomplete="off" aria-describedby="username-hint">
        <p class="form-hint" id="username-hint"><?= e(__('auth.setup_admin_hint')) ?></p>
    </div>
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
    <div class="form-actions">
        <button id="btn-create-user" class="btn btn-primary">
            <i class="bi bi-check-lg" aria-hidden="true"></i> <?= e(__('general.create')) ?>
        </button>
        <button id="btn-cancel-user" class="btn btn-secondary">
            <i class="bi bi-x-lg" aria-hidden="true"></i> <?= e(__('general.cancel')) ?>
        </button>
    </div>
    <div id="user-form-msg" class="alert hidden" role="status" aria-live="polite"></div>
</section>

<!-- Lista de usuarios -->
<section class="users-grid" aria-label="<?= e(__('admin.users_title')) ?>">
    <?php
    $roleIcons = ['admin' => 'bi-shield-lock', 'editor' => 'bi-pencil-square', 'guest' => 'bi-person'];
    $rolePerms = [
        'admin'  => __('admin.perms_admin'),
        'editor' => __('admin.perms_editor'),
        'guest'  => __('admin.perms_guest'),
    ];
    ?>
    <?php foreach ($users as $user): ?>
    <article class="user-card <?= !$user['is_active'] ? 'user-card-inactive' : '' ?>"
             data-user-id="<?= (int)$user['id'] ?>"
             aria-label="<?= e($user['username']) ?>">
        <header class="user-card-header">
            <div class="user-avatar user-avatar-<?= e($user['role']) ?>" aria-hidden="true">
                <i class="bi <?= $roleIcons[$user['role']] ?? 'bi-person' ?>"></i>
            </div>
            <div>
                <h3><?= e($user['username']) ?></h3>
                <span class="role-badge role-badge-<?= e($user['role']) ?>">
                    <?= e(__('admin.role_' . $user['role'])) ?>
                </span>
                <?php if (!$user['is_active']): ?>
                    <span class="status-badge status-inactive"><?= e(__('admin.badge_inactive')) ?></span>
                <?php endif; ?>
            </div>
        </header>

        <div class="user-card-info">
            <dl class="info-row">
                <dt class="info-label"><?= e(__('admin.user_permissions')) ?></dt>
                <dd><?= e($rolePerms[$user['role']] ?? '') ?></dd>
            </dl>
        </div>

        <footer class="user-card-actions">
            <!-- Cambiar contraseña -->
            <div class="pw-row">
                <label for="pw-<?= (int)$user['id'] ?>" class="sr-only"><?= e(__('admin.new_password')) ?></label>
                <input type="password" id="pw-<?= (int)$user['id'] ?>"
                       class="form-input form-input-sm pw-input"
                       placeholder="<?= e(__('admin.new_password')) ?>"
                       data-user-id="<?= (int)$user['id'] ?>"
                       autocomplete="new-password">
                <button class="btn-icon btn-change-pw"
                        data-user-id="<?= (int)$user['id'] ?>"
                        title="<?= e(__('admin.change_password')) ?>"
                        aria-label="<?= e(__('admin.change_password')) ?> – <?= e($user['username']) ?>">
                    <i class="bi bi-key" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Toggle activo + Eliminar -->
            <?php if ((int)$user['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
            <div class="action-row">
                <button class="btn-icon btn-toggle-user"
                        data-user-id="<?= (int)$user['id'] ?>"
                        data-active="<?= $user['is_active'] ? '1' : '0' ?>"
                        title="<?= $user['is_active'] ? e(__('admin.deactivate')) : e(__('admin.activate')) ?>"
                        aria-label="<?= $user['is_active'] ? e(__('admin.deactivate')) : e(__('admin.activate')) ?> – <?= e($user['username']) ?>">
                    <i class="bi <?= $user['is_active'] ? 'bi-toggle-on' : 'bi-toggle-off' ?>" aria-hidden="true"></i>
                </button>
                <?php if ($user['role'] !== 'admin'): ?>
                <button class="btn-icon btn-icon-danger btn-delete-user"
                        data-user-id="<?= (int)$user['id'] ?>"
                        data-username="<?= e($user['username']) ?>"
                        title="<?= e(__('general.delete')) ?>"
                        aria-label="<?= e(__('general.delete')) ?> – <?= e($user['username']) ?>">
                    <i class="bi bi-trash" aria-hidden="true"></i>
                </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </footer>
    </article>
    <?php endforeach; ?>
</section>

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
        <div id="email-msg" class="alert hidden mt-1" role="status" aria-live="polite"></div>
        <?php if (empty($recoveryEmail)): ?>
        <div class="alert alert-warning mt-1" role="alert">
            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i> <?= e(__('admin.recovery_missing')) ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal confirmar eliminación -->
<dialog id="delete-overlay" class="overlay hidden" aria-labelledby="delete-title">
    <div class="modal" role="alertdialog" aria-labelledby="delete-title" aria-describedby="delete-message">
        <div class="modal-icon" aria-hidden="true"><i class="bi bi-exclamation-triangle"></i></div>
        <h3 id="delete-title"><?= e(__('general.confirm')) ?></h3>
        <p id="delete-message"></p>
        <div class="modal-actions">
            <button id="btn-confirm-delete" class="btn btn-danger">
                <i class="bi bi-trash" aria-hidden="true"></i> <?= e(__('general.delete')) ?>
            </button>
            <button id="btn-cancel-delete" class="btn btn-secondary"><?= e(__('general.cancel')) ?></button>
        </div>
    </div>
</dialog>
