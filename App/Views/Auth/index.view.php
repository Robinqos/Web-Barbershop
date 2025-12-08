<?php

/** @var \Framework\Support\LinkGenerator $link */
?>

<section class="cb-hero-section">
    <div class="container text-center">
        <h1 class="display-4 cb-gold-text">Môj profil</h1>

        <div class="mt-4">
            <a class="btn cb-btn-gold btn-lg me-3" href="<?= $link->url('auth.edit') ?>">
                Upraviť profil
            </a>
            <a class="btn cb-btn-gold btn-lg me-3" href="<?= $link->url('auth.confirmDelete') ?>">
                Zmazať účet
            </a>
        </div>

        <div class="mt-5">
            <h3 class="cb-gold-text mb-3">Moje rezervácie</h3>
            <!-- Tu bude zoznam rezervacii -->
            <p class="cb-text-muted">Zatiaľ nemáte žiadne rezervácie.</p>
        </div>
    </div>
</section>
