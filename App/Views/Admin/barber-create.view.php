<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array $errors */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="cb-gold-text">Pridať nového barbera</h1>
            <a href="<?= $link->url('admin.barbers') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Späť na zoznam
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="cb-dark-card">
                <form action="<?= $link->url('admin.createBarber') ?>" method="POST" id="createBarberForm">
                    <h3 class="cb-gold-text">Základné informácie</h3>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Meno a priezvisko *</label>
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                                   id="name" name="name" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= $errors['name'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                   id="email" name="email" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Telefón *</label>
                            <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                                   id="phone" name="phone" required>
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Heslo *</label>
                            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                   id="password" name="password" required>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= $errors['password'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h3 class="cb-gold-text mt-4">Barber informácie</h3>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="bio" class="form-label">Bio *</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3" required></textarea>
                            <div id="bio_help" class="form-text text-danger"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="photo_url" class="form-label">URL fotky *</label>
                            <input type="text" class="form-control" id="photo_url" name="photo_url" required>
                            <div id="photo_url_help" class="form-text text-danger"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-control" id="is_active" name="is_active">
                                <option value="1">Aktívny</option>
                                <option value="0">Neaktívny</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-warning">Vytvoriť barbera</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>