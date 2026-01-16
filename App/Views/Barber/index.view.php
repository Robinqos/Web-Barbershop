<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\User $user */
/** @var \App\Models\Barber $barber */
/** @var array $todayReservations */
/** @var array $upcomingReservations */
/** @var array $allReservations */
/** @var int $totalReservations */
/** @var int $todayReservationsCount */
?>

<div class="container-fluid mt-4">
    <!-- head -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="cb-gold-text">Barber panel</h1>
            <p class="cb-text-muted">Vitajte, <strong><?= htmlspecialchars($barber->getName()) ?></strong></p>

            <?php if ($barber->getIsActive()): ?>
                <div class="alert alert-dark">
                    <i class="bi bi-scissors"></i> Ste prihlásený ako <strong>Barber</strong>
                    <div class="mt-2">
                        <!-- deaktivacia -->
                        <form method="POST" action="<?= $link->url('barber.toggleActivation') ?>"
                              class="d-inline-block"
                              onsubmit="return confirm('Naozaj sa chcete deaktivovať? Po deaktivácii nebudete môcť prijímať nové rezervácie.')">
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="bi bi-power"></i> Deaktivovať účet
                            </button>
                        </form>
                        <small class="ms-2 cb-text-muted">(napr. ak idete na dovolenku)</small>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> Váš účet je <strong>neaktívny</strong>.
                    <div class="mt-2">
                        <!-- aktivacia -->
                        <form method="POST" action="<?= $link->url('barber.toggleActivation') ?>"
                              class="d-inline-block"
                              onsubmit="return confirm('Naozaj sa chcete aktivovať?')">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-power"></i> Aktivovať účet
                            </button>
                        </form>
                        <small class="ms-2 cb-text-muted">Váš účet bude opäť viditeľný pre zákazníkov</small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- statistiky -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="cb-dark-card text-center">
                <h3 class="cb-gold-text"><?= $totalReservations ?></h3>
                <p>Celkové rezervácie</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="cb-dark-card text-center">
                <h3 class="cb-gold-text"><?= $todayReservationsCount ?></h3>
                <p>Dnešné rezervácie</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="cb-dark-card text-center">
                <h3 class="cb-gold-text">
                    <?php
                    $completed = array_filter($allReservations, function($r) {
                        return $r->getStatus() === 'completed';
                    });
                    echo count($completed);
                    ?>
                </h3>
                <p>Dokončené</p>
            </div>
        </div>
    </div>

    <!-- dnesne rezervacie -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="cb-dark-card">
                <h3 class="cb-gold-text mb-3">Dnešné rezervácie</h3>

                <?php if (empty($todayReservations)): ?>
                    <p class="cb-text-muted">Dnes nemáte žiadne rezervácie.</p>
                <?php else: ?>
                    <?php foreach ($todayReservations as $reservation): ?>
                        <div class="mb-3 pb-3 border-bottom border-secondary">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong><?= date('H:i', strtotime($reservation->getReservationDate())) ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <?= htmlspecialchars($reservation->getCustomerName()) ?>
                                </div>
                                <div class="col-md-3">
                                    <?php $service = $reservation->getService(); ?>
                                    <?= $service ? htmlspecialchars($service->getTitle()) : 'Neznáma služba' ?>
                                </div>
                                <div class="col-md-3 text-end">
                                    <?php if ($reservation->isPending()): ?>
                                        <form method="POST" action="<?= $link->url('barber.completeReservation', ['id' => $reservation->getId()]) ?>"
                                              class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-success me-2"
                                                    onclick="return confirm('Označiť rezerváciu ako dokončenú?')">
                                                <i class="bi bi-check-circle"></i> Dokončiť
                                            </button>
                                        </form>
                                        <form method="POST" action="<?= $link->url('barber.cancelReservation', ['id' => $reservation->getId()]) ?>"
                                              class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Naozaj chcete zrušiť túto rezerváciu?')">
                                                <i class="bi bi-x-circle"></i> Zrušiť
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-<?=
                                        $reservation->getStatus() === 'pending' ? 'warning' :
                                            ($reservation->getStatus() === 'completed' ? 'success' : 'danger')
                                        ?>">
                                            <?= $reservation->getStatus() === 'pending' ? 'Čakajúca' :
                                                ($reservation->getStatus() === 'completed' ? 'Dokončená' : 'Zrušená') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($reservation->getNote()): ?>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <small class="cb-text-muted">
                                            <i class="bi bi-chat-text"></i>
                                            <?= htmlspecialchars($reservation->getNote()) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- nadchadzajuce -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="cb-dark-card">
                <h3 class="cb-gold-text mb-3">Nadchádzajúce rezervácie</h3>

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
                                    <?= $reservation->getStatus() === 'pending' ? 'Čakajúca' :
                                        ($reservation->getStatus() === 'completed' ? 'Dokončená' : 'Zrušená') ?>
                                </span>
                            </div>
                            <div>
                                <?php if ($reservation->isPending()): ?>
                                    <form method="POST" action="<?= $link->url('barber.cancelReservation', ['id' => $reservation->getId()]) ?>"
                                          class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Naozaj chcete zrušiť túto rezerváciu?')">
                                            <i class="bi bi-x-circle"></i> Zrušiť
                                        </button>
                                    </form>
                                <?php endif; ?>
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

    <!-- vsetky -->
    <div class="row">
        <div class="col-12">
            <div class="cb-dark-card">
                <h3 class="cb-gold-text mb-3">Všetky moje rezervácie</h3>

                <?php if (empty($allReservations)): ?>
                    <p class="cb-text-muted">Nemáte žiadne rezervácie.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover text-center align-middle mb-0">
                            <thead>
                            <tr>
                                <th>Dátum a čas</th>
                                <th>Zákazník</th>
                                <th>Služba</th>
                                <th>Status</th>
                                <th>Poznámka</th>
                                <th>Akcie</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($allReservations as $reservation): ?>
                                <?php $service = $reservation->getService(); ?>
                                <tr>
                                    <td><?= $reservation->getFormattedReservationDate() ?></td>
                                    <td><?= htmlspecialchars($reservation->getCustomerName()) ?></td>
                                    <td>
                                        <?= $service ? htmlspecialchars($service->getTitle()) : 'Neznáma služba' ?>
                                        <small class="cb-text-muted d-block">
                                            <?= $service ? $service->getPrice() . '€' : '' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?=
                                        $reservation->getStatus() === 'pending' ? 'warning' :
                                            ($reservation->getStatus() === 'completed' ? 'success' : 'danger')
                                        ?>">
                                            <?= $reservation->getStatus() === 'pending' ? 'Čakajúca' :
                                                ($reservation->getStatus() === 'completed' ? 'Dokončená' : 'Zrušená') ?>
                                        </span>
                                    </td>
                                    <td style="max-width: 200px;">
                                        <?= $reservation->getNote() ? htmlspecialchars($reservation->getNote()) : '-' ?>
                                    </td>
                                    <td>
                                        <?php if ($reservation->isPending()): ?>
                                            <form method="POST" action="<?= $link->url('barber.completeReservation', ['id' => $reservation->getId()]) ?>"
                                                  class="d-inline">
                                                <button type="submit" class="btn btn-sm btn-success me-1"
                                                        onclick="return confirm('Označiť rezerváciu ako dokončenú?')">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="<?= $link->url('barber.cancelReservation', ['id' => $reservation->getId()]) ?>"
                                                  class="d-inline">
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Naozaj chcete zrušiť túto rezerváciu?')">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
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