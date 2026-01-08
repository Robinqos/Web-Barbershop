<?php
/** @var \Framework\Support\LinkGenerator $link */
?>

<!-- zvacseny obrazok  -->
<section class="cb-hero-section" style="min-height: 80vh;">
    <div class="container">
        <!-- HLAVIČKA PROFILU - PEVNÁ ČASŤ -->
        <div class="text-center mb-4">
            <h1 class="display-4 cb-gold-text">Môj profil</h1>

            <div class="mt-4">
                <a class="btn cb-btn-gold btn-lg me-3" href="<?= $link->url('auth.edit') ?>">
                    Upraviť profil
                </a>
                <a class="btn cb-btn-gold btn-lg me-3" href="<?= $link->url('auth.confirmDelete') ?>">
                    Zmazať účet
                </a>
            </div>
        </div>

        <!-- REZERVACIE -->
        <div class="mt-4">
            <h3 class="cb-gold-text mb-3 text-center">Moje rezervácie</h3>

            <?php if (empty($reservations)) : ?>
                <div class="text-center py-4">
                    <p class="cb-text-muted">Zatiaľ nemáte žiadne rezervácie.</p>
                    <a href="<?= $link->url('reservation.create') ?>" class="btn cb-btn-gold mt-2">
                        Vytvoriť prvú rezerváciu
                    </a>
                </div>
            <?php else : ?>
                <!-- WRAPPER (fix vyska) -->
                <div class="border border-secondary rounded p-1" style="max-height: 400px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                            <tr>
                                <th>Dátum a čas</th>
                                <th>Služba</th>
                                <th>Status</th>
                                <th>Akcie</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reservations as $reservation) :
                                $service = $reservation->getService();
                                ?>
                                <tr>
                                    <td><?= $reservation->getFormattedReservationDate() ?></td>
                                    <td>
                                        <?php if ($service) : ?>
                                            <?= htmlspecialchars($service->getTitle()) ?>
                                            <small class="cb-text-muted d-block"><?= $service->getPrice() ?>€</small>
                                        <?php else : ?>
                                            <em>Služba neexistuje</em>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($reservation->isPending()) : ?>
                                            <span class="badge bg-warning">Čakajúca</span>
                                        <?php elseif ($reservation->isCompleted()) : ?>
                                            <span class="badge bg-success">Dokončená</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($reservation->isPending()) : ?>
                                            <form method="POST" action="<?= $link->url('reservation.cancel', ['id' => $reservation->getId()]) ?>"
                                                  class="d-inline">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Naozaj chcete zrušiť túto rezerváciu?')">
                                                    Zrušiť
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- button -->
                <div class="text-center mt-3">
                    <a href="<?= $link->url('reservation.create') ?>" class="btn cb-btn-gold">
                        <i class="fas fa-plus"></i> Nová rezervácia
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>