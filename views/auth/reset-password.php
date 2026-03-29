<?php $title = __('auth.reset_title') . ' — ' . __('general.app_name'); ?>

<section class="auth-card" aria-labelledby="reset-title">
    <header class="auth-logo">
        <i class="bi bi-brush" aria-hidden="true"></i>
        <h1 id="reset-title"><?= e(__('general.app_name')) ?></h1>
        <p><?= e(__('auth.reset_subtitle')) ?></p>
    </header>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-error" role="alert">
            <i class="bi bi-exclamation-circle" aria-hidden="true"></i> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" role="status">
            <i class="bi bi-check-circle" aria-hidden="true"></i> <?= e($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/reset-password" autocomplete="off" novalidate>
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email"><?= e(__('auth.reset_email')) ?></label>
            <input type="email" id="email" name="email" class="form-input"
                   placeholder="tu@correo.com" required autofocus
                   aria-required="true">
        </div>

        <div class="form-group">
            <label for="username"><?= e(__('auth.username')) ?></label>
            <input type="text" id="username" name="username" class="form-input"
                   placeholder="admin" required
                   aria-required="true">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="new_password"><?= e(__('auth.reset_new_password')) ?></label>
                <input type="password" id="new_password" name="new_password" class="form-input"
                       placeholder="<?= e(__('auth.setup_password_hint')) ?>"
                       required aria-required="true">
            </div>
            <div class="form-group">
                <label for="confirm_password"><?= e(__('auth.reset_confirm')) ?></label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input"
                       placeholder="<?= e(__('auth.reset_confirm')) ?>"
                       required aria-required="true">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block"><?= e(__('auth.reset_btn')) ?></button>
    </form>

    <footer class="auth-footer">
        <a href="/login"><i class="bi bi-arrow-left" aria-hidden="true"></i> <?= e(__('auth.login_title')) ?></a>
    </footer>
</section>
