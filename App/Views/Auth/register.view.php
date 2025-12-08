<?php
/** @var array $errors */
/** @var array $formData */
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

                    <?php if (!empty($errors)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Opravte nasledujúce chyby:</strong>
                            <ul class="mb-0">
                                <?php foreach ($errors as $field => $error) : ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= $link->url("auth.register") ?>" id="registerForm">
                        <div class="row g-3">
                            <!-- Osobné údaje -->
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="full_name" class="form-label cb-gold-text">
                                        Meno a priezvisko </label>
                                    <input type="text"
                                           name="full_name"
                                           id="full_name"
                                           class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                                           placeholder="Zadajte vaše meno a priezvisko"
                                           value="<?= htmlspecialchars($formData['full_name'] ?? '') ?>">
                                    <?php if (isset($errors['full_name'])) : ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['full_name']) ?></div>
                                    <?php else : ?>
                                        <div id="full_name_error" class="invalid-feedback"></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label cb-gold-text">
                                        Telefónne číslo <span class="text-danger">*</span></label>
                                    <input type="tel"
                                           name="phone"
                                           id="phone"
                                           class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                                           placeholder="+421 XXX XXX XXX"
                                           value="<?= htmlspecialchars($formData['phone'] ?? '') ?>"
                                           required>
                                    <?php if (isset($errors['phone'])) : ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div>
                                    <?php else : ?>
                                        <div id="phone_error" class="invalid-feedback"></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label cb-gold-text">
                                        E-mail <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                           placeholder="vas@email.sk"
                                           value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
                                           required>
                                    <?php if (isset($errors['email'])) : ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                                    <?php else : ?>
                                        <div id="email_error" class="invalid-feedback"></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label cb-gold-text">
                                        Heslo <span class="text-danger">*</span>
                                    </label>
                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                           placeholder="Vaše heslo"
                                           value="<?= htmlspecialchars($formData['password'] ?? '') ?>"
                                           required>
                                    <?php if (isset($errors['password'])) : ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                                    <?php else : ?>
                                        <div id="password_error" class="invalid-feedback"></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="password_confirm" class="form-label cb-gold-text">
                                        Potvrdenie hesla <span class="text-danger">*</span>
                                    </label>
                                    <input type="password"
                                           name="password_confirm"
                                           id="password_confirm"
                                           class="form-control
                                           <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                                           placeholder="Zopakujte heslo"
                                           value="<?= htmlspecialchars($formData['password_confirm'] ?? '') ?>"
                                           required>
                                    <?php if (isset($errors['password_confirm'])) : ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['password_confirm']) ?></div>
                                    <?php else : ?>
                                        <div id="password_confirm_error" class="invalid-feedback"></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Bez pokročilých kontrol -->
                            <div class="col-12">
                                <div class="form-check mb-4">
                                    <input type="checkbox"
                                           name="terms"
                                           id="terms"
                                           class="form-check-input <?= isset($errors['terms']) ? 'is-invalid' : '' ?>"
                                        <?= isset($formData['terms']) && $formData['terms'] === 'on' ? 'checked' : '' ?>
                                           required>
                                    <label for="terms" class="form-check-label">
                                        <a>Súhlasím so spracovaním osobných údajov</a>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php if (isset($errors['terms'])) : ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['terms']) ?></div>
                                    <?php else : ?>
                                        <div id="terms_error" class="invalid-feedback"></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Tlačidlá -->
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                    <a href="<?= $link->url("auth.login") ?>" class="btn btn-outline-secondary">
                                        ← Späť na prihlásenie
                                    </a>
                                    <button type="submit" name="submit" class="btn btn-primary px-4" id="submitBtn">
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
