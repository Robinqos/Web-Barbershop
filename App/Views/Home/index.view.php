<?php

/** @var \Framework\Support\LinkGenerator $link */
?>

<!-- HERO SEKCIA -->
<section class="cb-hero-section">
    <div class="container text-center">
        <h1 class="display-4 cb-gold-text">CROWN BARBER</h1>
        <p class="lead cb-gold-subtitle">Královský prístup k vášmu štýlu</p>
        <a href="#sluzby" class="btn cb-btn-gold btn-lg mt-4">Pozrieť služby</a>
    </div>
</section>

<!-- SLUZBY -->
<section id="sluzby" class="cb-dark-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 cb-gold-text">NAŠE SLUŽBY</h2>
                <p class="lead cb-text-muted">Ponúkame špičkové služby pre moderných mužov</p>
            </div>
        </div>

        <?php foreach (\App\Models\Service::getAll() as $service): ?>    <!-- foreach do listu ich da -->
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="cb-service-card mb-4">
                    <h3 class="cb-gold-text"><?= $service->getTitle() ?></h3>
                    <p class="cb-text-muted"><?= $service->getDescription() ?></p>
                    <span class="cb-price"><?= $service->getPrice() ?>€</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!--<div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="cb-service-card mb-4">
                    <h3 class="cb-gold-text">Pansky strih</h3>
                    <p class="cb-text-muted">Profesionálne strihanie vlasov podľa vášho štýlu</p>
                    <span class="cb-price">15€</span>
                </div>

                <div class="cb-service-card mb-4">
                    <h3 class="cb-gold-text">Úprava brady</h3>
                    <p class="cb-text-muted">Tvarovanie a úprava brady podľa aktuálnych trendov a vášho osobitného štýlu</p>
                    <span class="cb-price">10€</span>
                </div>
            </div>
        </div>-->
    </div>
</section>