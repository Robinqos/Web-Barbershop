<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\User $user */
/** @var array $todayReservations */
/** @var array $upcomingReservations */
/** @var int $totalReservations */
/** @var int $totalServices */
/** @var int $totalUsers */
/** @var int $totalBarbers */
?>

<div class="container-fluid mt-4">
    <!-- head -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="cb-gold-text">Admin sekcia</h1>
            <p class="cb-text-muted">Vitajte, <strong><?= htmlspecialchars($user->getEmail()) ?></strong></p>

            <?php if ($user->getPermissions() >= \App\Models\User::ROLE_ADMIN): ?>
                <div class="alert alert-dark">
                    <i class="bi bi-shield-check"></i> Ste prihlásený ako <strong>Admin</strong>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- rychle info a tlacidla -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="cb-dark-card text-center">
                <h3 class="cb-gold-text"><?= $totalReservations ?></h3>
                <p>Celkové rezervácie</p>
                <a href="<?= $link->url('admin.showReservations') ?>" class="btn btn-warning btn-sm mt-2">
                    <i class="bi bi-list"></i> Zobraziť
                </a>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="cb-dark-card text-center">
                <h3 class="cb-gold-text"><?= $totalServices ?></h3>
                <p>Služby</p>
                <a href="<?= $link->url('admin.services') ?>" class="btn btn-warning btn-sm mt-2">
                    <i class="bi bi-list"></i> Zobraziť
                </a>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="cb-dark-card text-center">
                <h3 class="cb-gold-text"><?= $totalBarbers ?></h3>
                <p>Barberi</p>
                <a href="<?= $link->url('admin.barbers') ?>" class="btn btn-warning btn-sm mt-2">
                    <i class="bi bi-list"></i> Zobraziť
                </a>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="cb-dark-card text-center">
                <h3 class="cb-gold-text"><?= $totalUsers ?></h3>
                <p>Používatelia</p>
                <a href="<?= $link->url('admin.users') ?>" class="btn btn-warning btn-sm mt-2">
                    <i class="bi bi-list"></i> Zobraziť
                </a>
            </div>
        </div>
    </div>

    <!-- rtchle akcie -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="cb-dark-card">
                <h3 class="cb-gold-text mb-3">Rýchle akcie</h3>
                <div class="btn-group" role="group">
                    <a href="<?= $link->url('admin.createService') ?>" class="btn btn-outline-warning">
                        <i class="bi bi-plus-circle"></i> Pridať službu
                    </a>
                    <a href="<?= $link->url('admin.createBarber') ?>" class="btn btn-outline-warning">
                        <i class="bi bi-plus-circle"></i> Pridať barbera
                    </a>
                    <a href="<?= $link->url('admin.createUser') ?>" class="btn btn-outline-warning">
                        <i class="bi bi-plus-circle"></i> Pridať používateľa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- dnesnee rezervacie -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="cb-dark-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="cb-gold-text mb-0">Dnešné rezervácie</h3>
                    <a href="<?= $link->url('admin.showReservations', ['filter' => 'today']) ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-eye"></i> Zobraziť
                    </a>
                </div>

                <?php if (empty($todayReservations)): ?>
                    <p class="cb-text-muted">Dnes nie sú žiadne rezervácie.</p>
                <?php else: ?>
                    <?php foreach ($todayReservations as $reservation): ?>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= date('H:i', strtotime($reservation->getReservationDate())) ?></strong>
                                - <?= htmlspecialchars($reservation->getCustomerName()) ?>
                                <span class="badge bg-<?=
                                $reservation->getStatus() === 'pending' ? 'warning' :
                                    ($reservation->getStatus() === 'completed' ? 'success' : 'danger')
                                ?> ms-2">
                                    <?= $reservation->getStatus() ?>
                                </span>
                            </div>
                            <div>
                                <a href="<?= $link->url('admin.reservation.edit', ['id' => $reservation->getId()]) ?>"
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i> Upraviť
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- nasledujuce rezervacie -->
    <div class="row">
        <div class="col-12">
            <div class="cb-dark-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="cb-gold-text mb-0">Nadchádzajúce rezervácie</h3>
                    <a href="<?= $link->url('admin.showReservations', ['filter' => 'upcoming']) ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-eye"></i> Zobraziť
                    </a>
                </div>

                <?php if (empty($upcomingReservations)): ?>
                    <p class="cb-text-muted">Žiadne nadchádzajúce rezervácie.</p>
                <?php else: ?>
                    <?php foreach (array_slice($upcomingReservations, 0, 5) as $reservation): ?>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= $reservation->getFormattedReservationDate() ?></strong>
                                - <?= htmlspecialchars($reservation->getCustomerName()) ?>
                                <span class="badge bg-<?=
                                $reservation->getStatus() === 'pending' ? 'warning' :
                                    ($reservation->getStatus() === 'completed' ? 'success' : 'danger')
                                ?> ms-2">
                                    <?= $reservation->getStatus() ?>
                                </span>
                            </div>
                            <div>
                                <a href="<?= $link->url('admin.reservation.edit', ['id' => $reservation->getId()]) ?>"
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i> Upraviť
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($upcomingReservations) > 5): ?>
                        <p class="cb-text-muted mt-2">
                            + <?= count($upcomingReservations) - 5 ?> ďalších...
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>