<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Review[] $reviews */
?>

<section class="cb-hero-section">
    <div class="container">
        <h1 class="cb-gold-text text-center mb-4">Moje hodnotenia</h1>

        <?php if (empty($reviews)) : ?>
            <div class="text-center py-5">
                <p class="cb-text-muted">Zatiaľ ste neuviedli žiadne hodnotenia.</p>
                <a href="<?= $link->url('auth.index') ?>" class="btn cb-btn-gold mt-3">
                    Späť na profil
                </a>
            </div>
        <?php else : ?>
            <div class="row">
                <?php foreach ($reviews as $review) :
                    $barber = $review->getBarber();
                    $reservation = $review->getReservation();
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="cb-dark-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="cb-gold-text mb-1">
                                        <?= htmlspecialchars($barber ? $barber->getName() : 'Neznámy barber') ?>
                                    </h5>
                                    <?php if ($reservation && $service = $reservation->getService()) : ?>
                                        <p class="mb-1 cb-text-muted">
                                            <small><?= htmlspecialchars($service->getTitle()) ?></small>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="cb-gold-text">
                                    <?= $review->getStarRating() ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="cb-text-muted">
                                    <?= $review->getFormattedCreatedAt() ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4">
                <a href="<?= $link->url('auth.index') ?>" class="btn cb-btn-gold">
                    Späť na profil
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>