<?php $title = __('auth.login_title') . ' — ' . __('general.app_name'); ?>

<section class="auth-card" aria-labelledby="login-title">
    <header class="auth-logo">
        <i class="bi bi-brush" aria-hidden="true"></i>
        <h1 id="login-title"><?= e(__('general.app_name')) ?></h1>
        <p><?= e(__('auth.login_subtitle')) ?></p>
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

    <form method="POST" action="/login" autocomplete="on" novalidate>
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="username"><?= e(__('auth.username')) ?></label>
            <input type="text" id="username" name="username" class="form-input"
                   autocomplete="username" required autofocus
                   aria-required="true">
        </div>

        <div class="form-group">
            <label for="password"><?= e(__('auth.password')) ?></label>
            <input type="password" id="password" name="password" class="form-input"
                   autocomplete="current-password" required
                   aria-required="true">
        </div>

        <button type="submit" class="btn btn-primary btn-block"><?= e(__('auth.login_btn')) ?></button>
    </form>

    <footer class="auth-footer">
        <a href="/reset-password"><?= e(__('auth.forgot_password')) ?></a>
    </footer>
</section>
