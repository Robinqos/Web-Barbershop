<?php
/** @var string|null $error */
/** @var string|null $success */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
$view->setLayout('auth');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6">
            <div class="cb-dark-card my-5 shadow">
                <div class="card-body p-4">
                    <h2 class="cb-gold-text text-center mb-4">Vytvoriť nový účet</h2>

                    <form method="post" action="<?= $link->url("auth.register") ?>">
                        <div class="row g-3">
                            <!-- Osobné údaje -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="full_name" class="form-label cb-gold-text">
                                        Meno a priezvisko </label>
                                    <input type="text"
                                           name="full_name"
                                           id="full_name"
                                           class="form-control"
                                           placeholder="Janko Mrkvička">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label cb-gold-text">
                                        Telefónne číslo <span class="text-danger">*</span></label>
                                    <input type="tel"
                                           name="phone"
                                           id="phone"
                                           class="form-control"
                                           placeholder="+421 918 123 456"
                                           value = "<?= $_POST['phone'] ?? '' ?>"
                                           required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label cb-gold-text">
                                        E-mail <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           class="form-control"
                                           placeholder="vas@email.sk"
                                           value = "<?= $_POST['email'] ?? '' ?>"
                                           required>         <!-- required = povinne a vie ze tam ma byt @, MUSI TO BYT POSLEDNE toto je kontrola na strane servera -->
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label cb-gold-text">
                                        Heslo <span class="text-danger">*</span>
                                    </label>
                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="form-control"
                                           placeholder="vaše heslo"
                                           value = "<?= $_POST['password'] ?? '' ?>"
                                           required>
                                    <span><?= $errors['password'] ?? '' ?></span>
                                </div>
                            </div>

                            <!-- Bez pokročilých kontrol -->
                            <div class="col-12">
                                <div class="form-check mb-4">
                                    <input type="checkbox"
                                           name="terms"
                                           id="terms"
                                           class="form-check-input"
                                           required>
                                    <label for="terms" class="form-check-label">
                                        <a>Suhlasim so spracovanim osobnych udajov</a>
                                        <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>

                            <?php if (isset($success)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($success) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($error) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <!-- Tlačidlá -->
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                    <a href="<?= $link->url("auth.login") ?>" class="btn btn-outline-secondary">
                                        ← Späť na prihlásenie
                                    </a>
                                    <button type="submit" name="submit" class="btn btn-primary px-4">
                                        Vytvoriť účet
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>