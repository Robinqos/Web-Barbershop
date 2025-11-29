<?php

/** @var string $contentHTML */
/** @var \Framework\Auth\AppUser $user */
/** @var \Framework\Support\LinkGenerator $link */
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <title><?= App\Configuration::APP_NAME ?></title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $link->asset('favicons/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $link->asset('favicons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $link->asset('favicons/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= $link->asset('favicons/site.webmanifest') ?>">
    <link rel="shortcut icon" href="<?= $link->asset('favicons/favicon.ico') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= $link->asset('css/styl.css') ?>">
    <script src="<?= $link->asset('js/script.js') ?>"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Navbar logo -->
        <a class="navbar-brand-me-4" href="<?= $link->url('home.index') ?>">     <!-- margin end-->
            <img src="<?= $link->asset('images/crownbarber_logo.png') ?>" title="<?= App\Configuration::APP_NAME ?>" alt="CrownBarber Logo">
        </a>

        <!-- Mobilne tlacidlo -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link->url('home.index') ?>">Domov</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#sluzby">Služby</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#barberi">Barberi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#rezervacia">Rezervácia</a>
                </li>
            </ul>

            <!-- Prihlasenie vpravo-->
            <ul class="navbar-nav ms-auto">
                <?php if ($user->isLoggedIn()) { ?>
                    <li class="nav-item">
                        <span class="navbar-text me-3">Prihlásený: <b><?= $user->getName() ?></b></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $link->url('auth.logout') ?>">Odhlásiť</a>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $link->url('auth.login') ?>">Prihlásiť</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid mt-3">
    <div class="web-content">
        <?= $contentHTML ?>
    </div>
</div>
</body>
</html>