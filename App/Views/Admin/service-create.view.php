<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array $errors */
/** @var array $formData */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="cb-gold-text">Pridať novú službu</h1>
            <a href="<?= $link->url('admin.services') ?>" class="btn btn-outline-warning">
                <i class="bi bi-arrow-left"></i> Späť na služby
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
                <form action="<?= $link->url('admin.createService') ?>" method="POST" id="createServiceForm">
                    <!-- nazov -->
                    <div class="mb-3">
                        <label for="title" class="form-label cb-text-muted">Názov služby *</label>
                        <input type="text"
                               class="form-control cb-input <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                               id="title"
                               name="title"
                               value="<?= htmlspecialchars($formData['title'] ?? '') ?>"
                               required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- popis -->
                    <div class="mb-3">
                        <label for="description" class="form-label cb-text-muted">Popis *</label>
                        <textarea class="form-control cb-input <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                                  id="description"
                                  name="description"
                                  rows="3"
                                  required><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- cena -->
                    <div class="mb-3">
                        <label for="price" class="form-label cb-text-muted">Cena (€) *</label>
                        <div class="input-group">
                            <input type="number"
                                   class="form-control cb-input <?= isset($errors['price']) ? 'is-invalid' : '' ?>"
                                   id="price"
                                   name="price"
                                   min="0"
                                   value="<?= htmlspecialchars($formData['price'] ?? '') ?>"
                                   required>
                            <span class="input-group-text">€</span>
                        </div>
                        <?php if (isset($errors['price'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['price']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- trvanie -->
                    <div class="mb-3">
                        <label for="duration" class="form-label cb-text-muted">Trvanie (minúty) *</label>
                        <div class="input-group">
                            <input type="number"
                                   class="form-control cb-input <?= isset($errors['duration']) ? 'is-invalid' : '' ?>"
                                   id="duration"
                                   name="duration"
                                   min="1"
                                   value="<?= htmlspecialchars($formData['duration'] ?? '') ?>"
                                   required>
                            <span class="input-group-text">min</span>
                        </div>
                        <?php if (isset($errors['duration'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['duration']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= $link->url('admin.services') ?>" class="btn btn-outline-secondary">
                            Zrušiť
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Vytvoriť službu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>