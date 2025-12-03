<?php

/** @var string $contentHTML */
/** @var \Framework\Auth\AppUser $user */
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
    <link rel="icon" type="image/png" sizes="192x192" href="<?= $link->asset('favicons/faviconlogochrome-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= $link->asset('favicons/faviconlogochrome-512x512.png') ?>">
    <link rel="shortcut icon" href="<?= $link->asset('favicons/faviconlogo-16x16.png') ?>">

    <!--Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>


    <!--Custom CSS and JS -->
    <link rel="stylesheet" href="<?= $link->asset('css/styl.css') ?>">
    <script src="<?= $link->asset('js/script.js') ?>"></script>

</head>
<body class="d-flex flex-column min-vh-100 cb-dark-theme">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <!-- Navbar logo -->
        <a class="navbar-brand ms-0 ms-lg-3" href="<?= $link->url('home.index') ?>">     <!-- margin end-->
            <img src="<?= $link->asset('images/crownbarber_logo.png') ?>"
                 title="<?= App\Configuration::APP_NAME ?>"
                 alt="CrownBarber Logo"
                 class="img-fluid"
                 style="max-height: 50px;">
        </a>

        <!-- Mobilne tlacidlo -->
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav"> <!-- margin x os -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link->url('home.index') ?>">Domov</a>
                </li>
                <li class="nav-item">
                    <!-- Pridaj link na homepage so sekciou #sluzby -->
                    <a class="nav-link" href="<?= $link->url('home.index') ?>#sluzby">Služby</a>
                </li>
                <li class="nav-item">
                    <!-- Pridaj link na homepage so sekciou #barberi -->
                    <a class="nav-link" href="<?= $link->url('home.index') ?>#barberi">Barberi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link->url('reservation.create') ?>">Rezervácia</a>
                </li>
            </ul>

            <!-- Prihlasenie vpravo-->
            <ul class="navbar-nav ms-lg-auto me-lg-3 text-lg-end"> <!-- margin start & end -->
                <?php if ($user->isLoggedIn()) { ?>
                    <li class="nav-item">
                        <span class="navbar-text me-3">Prihlásený: <b><?= htmlspecialchars($user->getEmail()) ?></b></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $link->url('user.index') ?>">Môj profil</a>
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
<!-- PÄTA -->
<footer class="cb-footer-section mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0 text-center">
                <h5 class="cb-gold-text mb-3">Kontakt</h5>
                <p class="cb-text-muted mb-1">📞 +421 918 165 642</p>
                <p class="cb-text-muted mb-0">✉️ cedzorobo@gmail.com</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0 text-center">
                <h5 class="cb-gold-text mb-3">Sídlo barbershopu</h5>
                <p class="cb-text-muted mb-0">📍 Hvozdnica 204, 013 56</p>
            </div>
            <div class="col-md-4 text-center">
                <h5 class="cb-gold-text mb-3">Otváracie hodiny</h5>
                <p class="cb-text-muted mb-1">Pon-Pia: 9:00 - 20:00</p>
                <p class="cb-text-muted mb-1">Sobota: 8:00 - 2:00</p>
                <p class="cb-text-muted mb-0">Nedeľa: 8:00 - 2:00</p>
            </div>
        </div>
        <div class="row mt-4 pt-3 border-top border-secondary">
            <div class="col text-center">
                <p class="cb-text-muted mb-0">&copy; 2025 Crown Barber</p>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
