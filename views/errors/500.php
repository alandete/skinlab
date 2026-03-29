<?php $title = '500 — ' . __('general.server_error'); ?>

<section class="error-container" role="alert">
    <div class="error-card">
        <p class="error-code" aria-hidden="true">500</p>
        <h1><?= e(__('general.server_error')) ?></h1>
        <p><?= e($message ?? __('general.server_error_message')) ?></p>
        <a href="/" class="error-link"><?= e(__('general.go_home')) ?></a>
    </div>
</section>
