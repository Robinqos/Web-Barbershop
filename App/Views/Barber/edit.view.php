<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\User $user */
/** @var \App\Models\Barber $barber */
/** @var array $errors */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="cb-gold-text">Upraviť profil</h1>
            <a href="<?= $link->url('barber.index') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Späť na dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- fotka -->
            <div class="cb-dark-card text-center p-4">
                <?php if ($barber->getPhotoPath()): ?>
                    <img src="<?= htmlspecialchars($barber->getPhotoPath()) ?>"
                         alt="<?= htmlspecialchars($barber->getName()) ?>"
                         class="img-fluid rounded-circle mb-3"
                         style="width: 150px; height: 150px; object-fit: cover; border: 3px solid var(--cb-gold);">
                <?php else: ?>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto"
                         style="width: 150px; height: 150px; background-color: #444; border: 3px solid var(--cb-gold);">
                        <i class="bi bi-person" style="font-size: 4rem; color: #666;"></i>
                    </div>
                <?php endif; ?>

                <h4 class="cb-gold-text mb-2"><?= htmlspecialchars($barber->getName()) ?></h4>

                <!-- button na fotku -->
                <button class="btn btn-outline-primary btn-sm mt-2"
                        data-bs-toggle="modal"
                        data-bs-target="#barberUploadPhotoModal">
                    <i class="bi bi-upload"></i> Zmeniť profilovú fotku
                </button>
            </div>
        </div>

        <div class="col-md-8">
            <div class="cb-dark-card p-4">
                <form method="POST" action="<?= $link->url('barber.updateProfile') ?>">
                    <h3 class="cb-gold-text mb-4">Osobné informácie</h3>

                    <!-- Meno a priezvisko -->
                    <div class="mb-3">
                        <label for="fullname" class="form-label cb-text-muted">Meno a priezvisko *</label>
                        <input type="text"
                               class="form-control <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>"
                               id="fullname"
                               name="fullname"
                               value="<?= htmlspecialchars($user->getFullName()) ?>"
                               required>
                        <?php if (isset($errors['fullname'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['fullname']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label cb-text-muted">Email *</label>
                        <input type="email"
                               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                               id="email"
                               name="email"
                               value="<?= htmlspecialchars($user->getEmail()) ?>"
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['email']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Cislo -->
                    <div class="mb-3">
                        <label for="phone" class="form-label cb-text-muted">Telefónne číslo</label>
                        <input type="tel"
                               class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                               id="phone"
                               name="phone"
                               value="<?= htmlspecialchars($user->getPhone() ?? '') ?>">
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['phone']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text cb-text-muted">
                            Formát: +421 123 456 789 alebo 0912 345 678
                        </div>
                    </div>

                    <!-- Bio -->
                    <div class="mb-4">
                        <label for="bio" class="form-label cb-text-muted">O mne</label>
                        <textarea class="form-control <?= isset($errors['bio']) ? 'is-invalid' : '' ?>"
                                  id="bio"
                                  name="bio"
                                  rows="4"
                                  placeholder="Napíšte niečo o sebe..."><?= htmlspecialchars($barber->getBio() ?? '') ?></textarea>
                        <?php if (isset($errors['bio'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['bio']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text cb-text-muted">
                            Maximálne 500 znakov. Tento text sa zobrazí zákazníkom.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= $link->url('barber.index') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Zrušiť
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Uložiť zmeny
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal pre nahratie fotky -->
<div class="modal fade" id="barberUploadPhotoModal" tabindex="-1" aria-labelledby="barberUploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content cb-dark-card">
            <div class="modal-header">
                <h5 class="modal-title cb-gold-text" id="barberUploadPhotoModalLabel">Zmeniť profilovú fotku</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= $link->url('barber.uploadPhoto') ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="photo" class="form-label">Vyberte novú fotku</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                        <div class="form-text cb-text-muted">
                            Podporované formáty: JPG, PNG, GIF, WebP.<br>
                            Maximálna veľkosť: 2MB.<br>
                            Minimálne rozlíšenie: 200x200px.<br>
                            Odporúčané: Štvorcový formát pre najlepší vzhľad.
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-upload"></i> Nahrať fotku
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>