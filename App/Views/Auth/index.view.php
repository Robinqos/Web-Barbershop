<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Reservation[] $reservations */
/** @var array $reviewMap Mapa reservation_id => Review objekt */
?>

<section class="cb-hero-section" style="min-height: 80vh;">
    <div class="container">
        <!-- HLAVIČKA PROFILU -->
        <div class="text-center mb-4">
            <h1 class="display-4 cb-gold-text">Môj profil</h1>
            <div class="mt-4">
                <a class="btn cb-btn-gold btn-lg me-3" href="<?= $link->url('auth.edit') ?>">
                    Upraviť profil
                </a>
                <a class="btn cb-btn-gold btn-lg me-3" href="<?= $link->url('review.index') ?>">
                    Moje hodnotenia
                </a>
                <a class="btn cb-btn-gold btn-lg me-3" href="<?= $link->url('auth.confirmDelete') ?>">
                    Zmazať účet
                </a>
            </div>
        </div>

        <!-- REZERVÁCIE -->
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
                <!-- WRAPPER (fix výška) -->
                <div class="border border-secondary rounded p-1" style="max-height: 400px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                            <tr>
                                <th>Dátum a čas</th>
                                <th>Služba</th>
                                <th>Barber</th>
                                <th>Status</th>
                                <th>Poznámka</th>
                                <th>Hodnotenie</th>
                                <th>Akcie</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reservations as $reservation) :
                                $service = $reservation->getService();
                                $barber = $reservation->getBarber();

                                // ziskanie recenzie z mapp
                                $review = $reviewMap[$reservation->getId()] ?? null;
                                $hasReview = $review !== null;
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
                                        <?php if ($barber) : ?>
                                            <?= htmlspecialchars($barber->getName()) ?>
                                        <?php else : ?>
                                            <span class="cb-text-muted">Nepriradené</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($reservation->isPending()) : ?>
                                            <span class="badge bg-warning text-dark">Čakajúca</span>
                                        <?php elseif ($reservation->isCompleted()) : ?>
                                            <span class="badge bg-success text-dark">Dokončená</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger text-dark">Zrušená</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="max-width: 200px; overflow:hidden;">
                                        <?php $note = $reservation->getNote(); ?>
                                        <?= trim((string)$note) !== ''
                                            ? htmlspecialchars($note)
                                            : '<span class="cb-text-muted">-</span>' ?>
                                    </td>
                                    <td>
                                        <?php if ($reservation->isCompleted()) : ?>
                                            <?php if ($hasReview && $review) : ?>
                                                <!-- ZOBRAZ HODNOTENIE -->
                                                <div>
                                                    <span class="cb-gold-text fs-5">
                                                        <?= $review->getStarRating() ?>
                                                    </span>
                                                    <br>
                                                    <small class="cb-text-muted">
                                                        (<?= $review->getRating() ?>/5)
                                                    </small>
                                                </div>
                                            <?php else : ?>
                                                <!-- FORMULAR PRE HODNOTENIE -->
                                                <form method="POST" action="<?= $link->url('review.store') ?>"
                                                      class="d-inline-block" style="min-width: 160px;">
                                                    <input type="hidden" name="reservation_id"
                                                           value="<?= $reservation->getId() ?>">
                                                    <div class="input-group input-group-sm">
                                                        <label>
                                                            <select name="rating" class="form-control form-control-sm"
                                                                    style="width: 100px;" required>
                                                                <option value="">Vyberte</option>
                                                                <option value="1">1 hviezdička</option>
                                                                <option value="2">2 hviezdičky</option>
                                                                <option value="3">3 hviezdičky</option>
                                                                <option value="4">4 hviezdičky</option>
                                                                <option value="5">5 hviezdičiek</option>
                                                            </select>
                                                        </label>
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            ✓
                                                        </button>
                                                    </div>
                                                </form>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <span class="cb-text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($reservation->isPending()) : ?>
                                            <form method="POST"
                                                  action="<?= $link->url('reservation.cancel', ['id' => $reservation->getId()]) ?>"
                                                  class="d-inline">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Naozaj chcete zrušiť túto rezerváciu?')">
                                                    Zrušiť
                                                </button>
                                            </form>
                                        <?php elseif ($reservation->isCompleted() && $hasReview) : ?>
                                            <span class="badge bg-info text-dark">Už ste ohodnotili</span>
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