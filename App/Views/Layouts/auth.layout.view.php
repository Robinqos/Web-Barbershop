<?php

/** @var string $contentHTML */
/** @var \Framework\Core\IAuthenticator $auth */
/** @var \Framework\Support\LinkGenerator $link */
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <title><?= App\Configuration::APP_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">      <!-- responzivita -->

    <!-- Favicons - Crown Barber -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $link->asset('favicons/appletouchicon-180x180.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $link->asset('favicons/faviconlogo-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $link->asset('favicons/faviconlogo-16x16.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192"
          href="<?= $link->asset('favicons/faviconlogochrome-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512"
          href="<?= $link->asset('favicons/faviconlogochrome-512x512.png') ?>">
    <link rel="shortcut icon" href="<?= $link->asset('favicons/faviconlogo-16x16.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="<?= $link->asset('css/styl.css') ?>">
    <script src="<?= $link->asset('js/userValidation.js') ?>"></script>
    <script src="<?= $link->asset('js/adminEdit.js') ?>"></script>
</head>

<body class="cb-dark-theme">
<!-- Logo  -->
<header class="py-3">
    <div class="container text-center">
        <a href="<?= $link->url('home.index') ?>">
            <img src="<?= $link->asset('images/crownbarber_logo.png') ?>"
                 alt="Crown Barber Logo"
                 style="max-height: 60px;"
                 class="img-fluid">
        </a>
    </div>
</header>

<!-- Hlavny obsah -->
<main class="container-fluid py-4 flex-grow-1">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-12">
            <?= $contentHTML ?>
        </div>
    </div>
</main>

<!-- Pata -->
<footer class="py-4 text-center border-top border-secondary">
    <div class="container">
        <p class="cb-text-muted mb-2">
            <a href="<?= $link->url('home.index') ?>" class="cb-gold-text text-decoration-none">
                ← Späť na domovskú stránku
            </a>
        </p>
        <p class="cb-text-muted mb-0">&copy; 2025 Crown Barber</p>
    </div>
</footer>
</body>
</html>
