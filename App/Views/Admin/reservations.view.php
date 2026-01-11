<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array $reservations */
/** @var string|null $filter */
/** @var \App\Models\Service[] $services */
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
                    <p class="cb-text-muted text-center">Žiadne rezervácie.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover text-center align-middle mb-0">
                            <thead>
                            <tr>
                                <th class="text-center">Dátum a čas</th>
                                <th class="text-center">Zákazník</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Telefón</th>
                                <th class="text-center">Služba</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 20%;">Poznámka</th>
                                <th class="text-center">Akcie</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reservations as $reservation):
                                $isGuest = $reservation->isGuestReservation();
                                ?>
                                <tr>
                                    <!-- datum a cas -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $reservation->getId() ?>"
                                             data-field="reservation_date"
                                             data-type="datetime"
                                             title="Kliknite pre úpravu">
                                            <?= $reservation->getFormattedReservationDate() ?>
                                        </div>
                                    </td>

                                    <!-- zakaznik -->
                                    <td class="align-middle">
                                        <?php if ($isGuest): ?>
                                            <!-- host=edit -->
                                            <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                                 style="min-height: 40px;"
                                                 data-id="<?= $reservation->getId() ?>"
                                                 data-field="guest_name"
                                                 data-type="text"
                                                 title="Kliknite pre úpravu">
                                                <?= htmlspecialchars($reservation->getCustomerName()) ?>
                                            </div>
                                        <?php else: ?>
                                            <!-- logged=readonly -->
                                            <div class="mx-auto w-100 d-flex justify-content-center align-items-center" style="min-height: 40px;">
                                                <div class="text-truncate" title="Údaj z účtu používateľa">
                                                    <?= htmlspecialchars($reservation->getCustomerName()) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Email -->
                                    <td class="align-middle">
                                        <?php if ($isGuest): ?>
                                            <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                                 style="min-height: 40px;"
                                                 data-id="<?= $reservation->getId() ?>"
                                                 data-field="guest_email"
                                                 data-type="email"
                                                 title="Kliknite pre úpravu">
                                                <?= htmlspecialchars($reservation->getGuestEmail() ?? '') ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="mx-auto w-100 d-flex justify-content-center align-items-center" style="min-height: 40px;">
                                                <div class="text-truncate" title="Údaj z účtu používateľa">
                                                    <?php
                                                    $user = $reservation->getUser();
                                                    echo $user ? htmlspecialchars($user->getEmail()) : 'N/A';
                                                    ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- cislo -->
                                    <td class="align-middle">
                                        <?php if ($isGuest): ?>
                                            <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                                 style="min-height: 40px;"
                                                 data-id="<?= $reservation->getId() ?>"
                                                 data-field="guest_phone"
                                                 data-type="text"
                                                 title="Kliknite pre úpravu">
                                                <?= htmlspecialchars($reservation->getGuestPhone() ?? '') ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="mx-auto w-100 d-flex justify-content-center align-items-center" style="min-height: 40px;">
                                                <div class="text-truncate" title="Údaj z účtu používateľa">
                                                    <?php
                                                    $user = $reservation->getUser();
                                                    echo $user ? htmlspecialchars($user->getPhone()) : 'N/A';
                                                    ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- sluzba -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $reservation->getId() ?>"
                                             data-field="service_id"
                                             data-type="select_service"
                                             data-current-service-id="<?= $reservation->getServiceId() ?>"
                                             title="Kliknite pre úpravu služby">
                                            <div class="text-truncate">
                                                <?= $reservation->getService() ? htmlspecialchars($reservation->getService()->getTitle()) : 'Neznáma služba' ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- status -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $reservation->getId() ?>"
                                             data-field="status"
                                             data-type="select"
                                             title="Kliknite pre zmenu">
                                            <span class="badge bg-<?=
                                            $reservation->getStatus() === 'pending' ? 'warning' :
                                                ($reservation->getStatus() === 'completed' ? 'success' : 'danger')
                                            ?>">
                                                <?= $reservation->getStatus() === 'pending' ? 'Čakajúca' :
                                                    ($reservation->getStatus() === 'completed' ? 'Dokončená' : 'Zrušená') ?>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- note -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $reservation->getId() ?>"
                                             data-field="note"
                                             data-type="textarea"
                                             title="Kliknite pre úpravu">
                                            <div class="text-truncate text-start">
                                                <?= htmlspecialchars($reservation->getNote() ?? '') ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- akcie -->
                                    <td class="align-middle">
                                        <div class="mx-auto w-100 d-flex justify-content-center align-items-center" style="min-height: 40px;">
                                            <div class="btn-group btn-group-sm">
                                                <?php if ($reservation->isPending()): ?>
                                                    <a href="<?= $link->url('admin.completeReservation', ['id' => $reservation->getId()]) ?>"
                                                       class="btn btn-outline-success btn-sm"
                                                       title="Dokončiť"
                                                       onclick="return confirm('Naozaj chcete dokončiť túto rezerváciu?')">
                                                        <i class="bi bi-check"></i>
                                                    </a>
                                                    <a href="<?= $link->url('admin.cancelReservation', ['id' => $reservation->getId()]) ?>"
                                                       class="btn btn-outline-danger btn-sm"
                                                       title="Zrušiť"
                                                       onclick="return confirm('Naozaj chcete zrušiť túto rezerváciu?')">
                                                        <i class="bi bi-x"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
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