<?php $title = '403 — ' . __('general.forbidden'); ?>

<section class="error-container" role="alert">
    <div class="error-card">
        <p class="error-code" aria-hidden="true">403</p>
        <h1><?= e(__('general.forbidden')) ?></h1>
        <p><?= e($message ?? __('general.forbidden_message')) ?></p>
        <a href="/" class="error-link"><?= e(__('general.go_home')) ?></a>
    </div>
</section>
