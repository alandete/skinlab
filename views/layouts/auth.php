<!DOCTYPE html>
<html lang="<?= e(App\Core\Lang::locale()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? __('general.app_name')) ?></title>

    <!-- Fuentes -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SkinLab CSS -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="auth-page">
    <?= $content ?>
</body>
</html>
