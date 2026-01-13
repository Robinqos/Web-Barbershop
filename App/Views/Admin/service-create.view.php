<?php
/** @var \Framework\Support\LinkGenerator $link */
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

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="cb-dark-card">
                <form action="<?= $link->url('admin.createService') ?>" method="POST">
                    <!-- nazov -->
                    <div class="mb-3">
                        <label for="title" class="form-label cb-text-muted">Názov služby *</label>
                        <input type="text" class="form-control cb-input" id="title" name="title" required>
                    </div>

                    <!-- popis -->
                    <div class="mb-3">
                        <label for="description" class="form-label cb-text-muted">Popis</label>
                        <textarea class="form-control cb-input" id="description" name="description" rows="3"></textarea>
                    </div>

                    <!-- cena -->
                    <div class="mb-3">
                        <label for="price" class="form-label cb-text-muted">Cena (€) *</label>
                        <div class="input-group">
                            <input type="number" class="form-control cb-input" id="price" name="price"
                                   min="0" required>
                            <span class="input-group-text">€</span>
                        </div>
                    </div>

                    <!-- trvanie -->
                    <div class="mb-3">
                        <label for="duration" class="form-label cb-text-muted">Trvanie (minúty) *</label>
                        <div class="input-group">
                            <input type="number" class="form-control cb-input" id="duration" name="duration"
                                   min="1" required>
                            <span class="input-group-text">min</span>
                        </div>
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