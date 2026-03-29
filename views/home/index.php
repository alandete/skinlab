<?php $title = __('general.app_name') . ' — ' . __('general.welcome'); ?>

<div class="welcome-container">
    <div class="welcome-card">
        <div class="welcome-logo">
            <i class="bi bi-brush"></i>
        </div>
        <h1><?= e(__('general.app_name')) ?></h1>
        <p><?= e(__('general.app_description')) ?></p>
        <p class="welcome-version">v<?= e(App\Core\App::config('version', '1.0.0')) ?></p>
    </div>
</div>
