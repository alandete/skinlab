<!DOCTYPE html>
<html lang="<?= e(App\Core\Lang::locale()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Error') ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="error-page">
    <?= $content ?>
</body>
</html>
