<?php
/** @var \App\Models\Reservation $reservation */
/** @var \App\Models\Service $service */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Barber|null $barber */
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="cb-dark-card text-center">
                <h2 class="cb-gold-text mb-4">Rezervácia bola vytvorená</h2>

                <div class="alert alert-success">
                    <h4>Ďakujeme za vašu rezerváciu!</h4>
                    <p>Číslo rezervácie: <strong>#<?= $reservation->getId() ?></strong></p>
                </div>

                <div class="p-4 bg-dark rounded border border-secondary mb-4">
                    <h5 class="cb-gold-text mb-3">Detaily rezervácie</h5>

                    <div class="row text-center mb-3">
                        <!-- 1.riadok -->
                        <div class="col-md-3 col-6 mb-3">
                            <strong class="cb-gold-text d-block">Dátum a čas</strong>
                            <span><?= $reservation->getFormattedReservationDate() ?></span>
                        </div>

                        <div class="col-md-3 col-6 mb-3">
                            <strong class="cb-gold-text d-block">Barber</strong>
                            <?php if ($barber): ?>
                                <span><?= htmlspecialchars($barber->getName()) ?></span>
                            <?php else: ?>
                                <span class="cb-text-muted">Nepriradené</span>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3 col-6 mb-3">
                            <strong class="cb-gold-text d-block">Služba</strong>
                            <span><?= htmlspecialchars($service->getTitle()) ?></span>
                        </div>

                        <div class="col-md-3 col-6 mb-3">
                            <strong class="cb-gold-text d-block">Trvanie</strong>
                            <span><?= $service->getDuration() ?> minút</span>
                        </div>
                    </div>

                    <!-- 2.riadok -->
                    <div class="text-center border-top pt-3">
                        <strong class="cb-gold-text d-block mb-2 fs-5">Celková cena</strong>
                        <div class="cb-price fs-1"><?= $service->getPrice() ?>€</div>
                    </div>

                    <hr class="cb-border-secondary mt-4">

                    <div class="text-start mt-3">
                        <p><strong>Meno:</strong> <?= htmlspecialchars($reservation->getCustomerName()) ?></p>
                        <p><strong>Status:</strong>
                            <span class="badge bg-warning"><?= $reservation->getStatus() ?></span>
                        </p>
                        <p><strong>Vytvorené:</strong> <?= date('d.m.Y H:i', strtotime($reservation->getCreatedAt())) ?></p>

                        <?php if ($reservation->getNote()): ?>
                            <p><strong>Poznámka:</strong> <?= htmlspecialchars($reservation->getNote()) ?></p>
                        <?php endif; ?>
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