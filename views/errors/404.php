<?php $title = '404 — ' . __('general.not_found'); ?>

<section class="error-container" role="alert">
    <div class="error-card">
        <p class="error-code" aria-hidden="true">404</p>
        <h1><?= e(__('general.not_found')) ?></h1>
        <p><?= e(__('general.not_found_message')) ?></p>
        <a href="/" class="error-link"><?= e(__('general.go_home')) ?></a>
    </div>
</section>
