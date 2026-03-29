<?php $title = __('auth.setup_title') . ' — ' . __('general.app_name'); ?>

<section class="auth-card auth-card-wide" aria-labelledby="setup-title">
    <header class="auth-logo">
        <i class="bi bi-brush" aria-hidden="true"></i>
        <h1 id="setup-title"><?= e(__('general.app_name')) ?></h1>
        <p><?= e(__('auth.setup_subtitle')) ?></p>
    </header>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error" role="alert">
            <i class="bi bi-exclamation-circle" aria-hidden="true"></i> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/setup" autocomplete="off" novalidate>
        <?= csrf_field() ?>

        <!-- Administrador -->
        <fieldset class="auth-section">
            <legend class="auth-section-legend">
                <i class="bi bi-shield-lock" aria-hidden="true"></i> <?= e(__('auth.setup_admin_section')) ?>
                <span class="badge badge-required"><?= e(__('auth.setup_required')) ?></span>
            </legend>
            <div class="form-group">
                <label for="admin_user"><?= e(__('auth.username')) ?></label>
                <input type="text" id="admin_user" name="admin_user" class="form-input"
                       placeholder="admin"
                       value="<?= e(flash('old_admin_user') ?? 'admin') ?>"
                       required aria-required="true">
                <p class="form-hint" id="admin-user-hint"><?= e(__('auth.setup_admin_hint')) ?></p>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="admin_password"><?= e(__('auth.password')) ?></label>
                    <input type="password" id="admin_password" name="admin_password" class="form-input"
                           placeholder="<?= e(__('auth.setup_password_hint')) ?>"
                           required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="admin_confirm"><?= e(__('auth.setup_confirm')) ?></label>
                    <input type="password" id="admin_confirm" name="admin_confirm" class="form-input"
                           placeholder="<?= e(__('auth.setup_confirm')) ?>"
                           required aria-required="true">
                </div>
            </div>
        </fieldset>

        <!-- Invitado -->
        <fieldset class="auth-section">
            <legend class="auth-section-legend">
                <i class="bi bi-person" aria-hidden="true"></i> <?= e(__('auth.setup_guest_section')) ?>
                <span class="badge badge-optional"><?= e(__('auth.setup_optional')) ?></span>
            </legend>
            <label class="checkbox-label">
                <input type="checkbox" name="create_guest" value="1">
                <span><?= e(__('auth.setup_guest_create')) ?></span>
            </label>
            <p class="form-hint mt-1"><?= e(__('auth.setup_guest_hint')) ?></p>
        </fieldset>

        <!-- Correo de recuperación -->
        <fieldset class="auth-section auth-section-last">
            <legend class="auth-section-legend">
                <i class="bi bi-envelope" aria-hidden="true"></i> <?= e(__('auth.setup_recovery_section')) ?>
                <span class="badge badge-optional"><?= e(__('auth.setup_optional')) ?></span>
            </legend>
            <div class="form-group">
                <label for="recovery_email"><?= e(__('auth.setup_recovery_section')) ?></label>
                <input type="email" id="recovery_email" name="recovery_email" class="form-input"
                       placeholder="tu@correo.com"
                       value="<?= e(flash('old_recovery_email') ?? '') ?>">
            </div>
            <p class="form-hint"><?= e(__('auth.setup_recovery_hint')) ?></p>
        </fieldset>

        <button type="submit" class="btn btn-primary btn-block">
            <i class="bi bi-rocket-takeoff" aria-hidden="true"></i> <?= e(__('auth.setup_btn')) ?>
        </button>
    </form>
</section>
