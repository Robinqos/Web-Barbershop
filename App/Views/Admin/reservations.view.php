<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array $reservations */
/** @var string|null $filter */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="cb-gold-text">Všetky rezervácie</h1>

            <!-- Filter -->
            <div class="btn-group mb-3">
                <a href="<?= $link->url('admin.showReservations') ?>"
                   class="btn btn-outline-<?= !$filter ? 'warning' : 'secondary' ?>">
                    Všetky
                </a>
                <a href="<?= $link->url('admin.showReservations', ['filter' => 'today']) ?>"
                   class="btn btn-outline-<?= $filter === 'today' ? 'warning' : 'secondary' ?>">
                    Dnešné
                </a>
                <a href="<?= $link->url('admin.showReservations', ['filter' => 'upcoming']) ?>"
                   class="btn btn-outline-<?= $filter === 'upcoming' ? 'warning' : 'secondary' ?>">
                    Nadchádzajúce
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="cb-dark-card">
                <?php if (empty($reservations)): ?>
                    <p class="cb-text-muted">Žiadne rezervácie.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                            <tr>
                                <th>Dátum a čas</th>
                                <th>Zákazník</th>
                                <th>Služba</th>
                                <th>Status</th>
                                <th>Akcie</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td><?= $reservation->getFormattedReservationDate() ?></td>
                                    <td><?= htmlspecialchars($reservation->getCustomerName()) ?></td>
                                    <td>
                                        <?php
                                        $service = $reservation->getService();
                                        echo $service ? htmlspecialchars($service->getTitle()) : 'Neznáma služba';
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?=
                                        $reservation->getStatus() === 'pending' ? 'warning' :
                                            ($reservation->getStatus() === 'completed' ? 'success' : 'danger')
                                        ?>">
                                            <?= $reservation->getStatus() ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= $link->url('admin.reservation.edit', ['id' => $reservation->getId()]) ?>"
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i> Upraviť
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>