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

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="cb-dark-card">
                <form action="<?= $link->url('admin.createUser') ?>" method="POST">
                    <!-- meno -->
                    <div class="mb-3">
                        <label for="name" class="form-label cb-text-muted">Meno a priezvisko *</label>
                        <input type="text" class="form-control cb-input" id="name" name="name" required>
                    </div>

                    <!-- email -->
                    <div class="mb-3">
                        <label for="email" class="form-label cb-text-muted">Email *</label>
                        <input type="email" class="form-control cb-input" id="email" name="email" required>
                    </div>

                    <!-- cislo -->
                    <div class="mb-3">
                        <label for="phone" class="form-label cb-text-muted">Telefón</label>
                        <input type="text" class="form-control cb-input" id="phone" name="phone">
                    </div>

                    <!-- heslo -->
                    <div class="mb-3">
                        <label for="password" class="form-label cb-text-muted">Heslo *</label>
                        <input type="password" class="form-control cb-input" id="password" name="password" required>
                    </div>

                    <!-- rola -->
                    <div class="mb-3">
                        <label for="permissions" class="form-label cb-text-muted">Rola *</label>
                        <select class="form-select cb-input" id="permissions" name="permissions" required>
                            <option value="0">Zákazník</option>
                            <option value="1">Barber</option>
                            <option value="2">Admin</option>
                        </select>
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