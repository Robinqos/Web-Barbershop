<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array $reservations */
/** @var string|null $filter */
/** @var \App\Models\Service[] $services */

use App\Models\Barber;

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
                                <th class="text-center">Barber</th>
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
                                             data-entity="reservation"
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
                                                 data-entity="reservation"
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
                                                 data-entity="reservation"
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
                                                 data-entity="reservation"
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
                                    <!-- barber -->
                                    <td class="align-middle">
                                        <?php
                                        $barberOptions = [];
                                        foreach (Barber::getAll() as $barberModel) {
                                            $barberOptions[] = [
                                                'value' => $barberModel->getId(),
                                                'text' => $barberModel->getName()
                                            ];
                                        }
                                        ?>
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $reservation->getId() ?>"
                                             data-field="barber_id"
                                             data-type="select"
                                             data-entity="reservation"
                                             data-render="barber"
                                             data-options='<?= json_encode($barberOptions) ?>'
                                             title="Kliknite pre výber barbera">
                                            <div class="text-truncate">
                                                <?php
                                                $barber = $reservation->getBarber();
                                                echo $barber ? htmlspecialchars($barber->getName()) : 'Nie je priradený';
                                                ?>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- sluzba -->
                                    <td class="align-middle">
                                        <?php
                                        $serviceOptions = [];
                                        foreach ($services as $service) {
                                            $serviceOptions[] = [
                                                'value' => $service->getId(),
                                                'text' => $service->getTitle()];
                                        }
                                        ?>
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $reservation->getId() ?>"
                                             data-field="service_id"
                                             data-type="select"
                                             data-entity="reservation"
                                             data-render="service"
                                             data-options='<?= json_encode($serviceOptions) ?>'
                                             title="Kliknite pre úpravu služby">
                                            <div class="text-truncate">
                                                <?= $reservation->getService() ? htmlspecialchars($reservation->getService()->getTitle()) : 'Neznáma služba' ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="align-middle">
                                        <?php
                                        $statusOptions = [
                                            ['value' => 'pending', 'text' => 'Čakajúca'],
                                            ['value' => 'completed', 'text' => 'Dokončená'],
                                            ['value' => 'cancelled', 'text' => 'Zrušená']
                                        ];
                                        ?>
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $reservation->getId() ?>"
                                             data-field="status"
                                             data-type="select"
                                             data-entity="reservation"
                                             data-render="status"
                                             data-options='<?= json_encode($statusOptions) ?>'
                                             title="Kliknite pre zmenu">
                                            <span class="badge bg-<?=
                                            $reservation->getStatus() === 'pending' ? 'warning' :
                                                ($reservation->getStatus() === 'completed' ? 'success' : 'danger')
                                            ?> text-dark">
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
                                             data-entity="reservation"
                                             title="Kliknite pre úpravu">
                                            <div class="text-truncate text-start">
                                                <?= htmlspecialchars($reservation->getNote() ?? '') ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- akcie -->
                                    <td class="align-middle">
                                        <div class="mx-auto w-100 d-flex justify-content-center align-items-center" style="min-height: 40px;">
                                            <?php if ($reservation->getStatus() === 'cancelled'): ?>
                                                <a href="<?= $link->url('admin.deleteReservation', ['id' => $reservation->getId()]) ?>"
                                                   class="btn btn-outline-danger btn-sm"
                                                   title="Vymazať natrvalo"
                                                   onclick="return confirm('Naozaj chcete natrvalo vymazať túto zrušenú rezerváciu? Táto akcia je nevratná!')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
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