<?php
/** @var \App\Models\Reservation $reservation */
/** @var \App\Models\Service $service */
/** @var \Framework\Support\LinkGenerator $link */
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="cb-dark-card text-center">
                <h2 class="cb-gold-text mb-4">✅ Rezervácia bola vytvorená</h2>

                <div class="alert alert-success">
                    <h4>Ďakujeme za vašu rezerváciu!</h4>
                    <p>Číslo rezervácie: <strong>#<?= $reservation->getId() ?></strong></p>
                </div>

                <div class="p-4 bg-dark rounded border border-secondary mb-4">
                    <h5 class="cb-gold-text mb-3">Detaily rezervácie</h5>

                    <div class="row text-center">
                        <div class="col-md-6 mb-3">
                            <strong class="cb-gold-text d-block">Dátum a čas</strong>
                            <span><?= $reservation->getFormattedReservationDate() ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong class="cb-gold-text d-block">Služba</strong>
                            <span><?= htmlspecialchars($service->getTitle()) ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong class="cb-gold-text d-block">Trvanie</strong>
                            <span><?= $service->getDuration() ?> minút</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong class="cb-gold-text d-block">Cena</strong>
                            <span class="cb-price"><?= $service->getPrice() ?>€</span>
                        </div>
                    </div>

                    <hr class="cb-border-secondary">

                    <div class="text-start">
                        <p><strong>Meno:</strong> <?= htmlspecialchars($reservation->getCustomerName()) ?></p>
                        <p><strong>Status:</strong>
                            <span class="badge bg-warning"><?= $reservation->getStatus() ?></span>
                        </p>
                        <p><strong>Vytvorené:</strong> <?= date('d.m.Y H:i', strtotime($reservation->getCreatedAt())) ?></p>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <?php if ($reservation->isUserReservation()) : ?>
                        <a href="<?= $link->url('auth.index') ?>" class="btn cb-btn-gold">
                            Moje rezervácie
                        </a>
                    <?php endif; ?>
                    <a href="<?= $link->url('home.index') ?>" class="btn btn-secondary">
                        Domov
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>