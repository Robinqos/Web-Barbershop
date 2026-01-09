<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\User $user */
/** @var array $todayReservations */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col pt-5">
            <div class="pt-5">
                <h1 class="cb-gold-text">Admin sekcia</h1>
                <p>Ahoj, <strong><?= $user->getEmail() ?></strong>!</p>

                <?php if ($user->getPermissions() >= 2): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-shield-check"></i> Máš <strong>Admin</strong> oprávnenia.
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <h3 class="cb-gold-text">Dnešné rezervácie</h3>
                    <?php if (empty($todayReservations)): ?>
                        <p class="cb-text-muted">Dnes niesú žiadne rezervácie.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($todayReservations as $reservation): ?>
                                <li><?= $reservation->getFormattedReservationDate() ?> -
                                    <?= $reservation->getCustomerName() ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>