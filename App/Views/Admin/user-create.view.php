<?php
/** @var \Framework\Support\LinkGenerator $link */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="cb-gold-text">Pridať nového používateľa</h1>
            <a href="<?= $link->url('admin.users') ?>" class="btn btn-outline-warning">
                <i class="bi bi-arrow-left"></i> Späť na používateľov
            </a>
        </div>
    </div>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="row mb-3">
            <div class="col-md-6 offset-md-3">
                <div class="alert alert-danger">
                    <h5 class="alert-heading">Opravte nasledujúce chyby:</h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $field => $error): ?>
                            <li><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="cb-dark-card">
                <form action="<?= $link->url('admin.createUser') ?>" method="POST" id="createUserForm">
                    <!-- meno -->
                    <div class="mb-3">
                        <label for="name" class="form-label cb-text-muted">Meno a priezvisko *</label>
                        <input type="text"
                               class="form-control cb-input <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                               id="name"
                               name="name"
                               value="<?= htmlspecialchars($formData['name'] ?? '') ?>"
                               required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- email -->
                    <div class="mb-3">
                        <label for="email" class="form-label cb-text-muted">Email *</label>
                        <input type="email"
                               class="form-control cb-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                               id="email"
                               name="email"
                               value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- cislo -->
                    <div class="mb-3">
                        <label for="phone" class="form-label cb-text-muted">Telefón *</label>
                        <input type="text"
                               class="form-control cb-input <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                               id="phone"
                               name="phone"
                               value="<?= htmlspecialchars($formData['phone'] ?? '') ?>"
                               required>
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- heslo -->
                    <div class="mb-3">
                        <label for="password" class="form-label cb-text-muted">Heslo *</label>
                        <input type="password"
                               class="form-control cb-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                               id="password"
                               name="password"
                               required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- rola -->
                    <div class="mb-3">
                        <label for="permissions" class="form-label cb-text-muted">Rola *</label>
                        <select class="form-select cb-input <?= isset($errors['permissions']) ? 'is-invalid' : '' ?>"
                                id="permissions"
                                name="permissions"
                                required>
                            <option value="0" <?= (($formData['permissions'] ?? '') == '0') ? 'selected' : '' ?>>Zákazník</option>
                            <option value="2" <?= (($formData['permissions'] ?? '') == '2') ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <?php if (isset($errors['permissions'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['permissions']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= $link->url('admin.users') ?>" class="btn btn-outline-secondary">
                            Zrušiť
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Vytvoriť používateľa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>