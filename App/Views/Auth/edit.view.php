<?php

/** @var \App\Models\User $user */
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
                    <h2 class="cb-gold-text text-center mb-4">Upravit účet</h2>

                    <?php if (isset($message) && isset($success) && $success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= $link->url("auth.update") ?>" id="editForm">
                        <div class="row g-3">
                            <!-- Osobné údaje -->
                            <div class="form-group mb-3">
                                <label for="full_name" class="form-label cb-gold-text">
                                    Meno a priezvisko
                                </label>
                                <input type="text"
                                       name="full_name"
                                       id="full_name"
                                       class="form-control text-center <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($user->getFullname() ?? '') ?>"
                                       placeholder="Zadajte vaše meno a priezvisko">
                                <?php if (isset($errors['full_name'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['full_name']) ?></div>
                                <?php else: ?>
                                    <div id="full_name_help" class="form-text text-muted"></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="form-label cb-gold-text">
                                    E-mail <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control text-center <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($user->getEmail() ?? '') ?>"
                                       placeholder="vas@email.sk"
                                       required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                                <?php else: ?>
                                    <div id="email_help" class="form-text text-muted"></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="phone" class="form-label cb-gold-text">
                                    Telefónne číslo <span class="text-danger">*</span>
                                </label>
                                <input type="tel"
                                       name="phone"
                                       id="phone"
                                       class="form-control text-center <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($user->getPhone() ?? '') ?>"
                                       placeholder="+421 XXX XXX XXX"
                                       required>
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div>
                                <?php else: ?>
                                    <div id="phone_help" class="form-text text-muted"></div>
                                <?php endif; ?>
                            </div>


                            <div class="form-group mb-3">
                                <label for="current_password" class="form-label cb-gold-text">
                                    Aktuálne heslo
                                </label>
                                <input type="password"
                                       name="current_password"
                                       id="current_password"
                                       class="form-control text-center <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>"
                                       placeholder="Zadajte aktuálne heslo (iba pri zmene hesla)">
                                <?php if (isset($errors['current_password'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['current_password']) ?></div>
                                <?php else: ?>
                                    <div id="current_password_help" class="form-text text-muted"></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="new_password" class="form-label cb-gold-text">
                                    Nové heslo
                                </label>
                                <input type="password"
                                       name="new_password"
                                       id="new_password"
                                       class="form-control text-center <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>"
                                       placeholder="Zadajte nové heslo (iba pri zmene hesla)">
                                <?php if (isset($errors['new_password'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['new_password']) ?></div>
                                <?php else: ?>
                                    <div id="new_password_help" class="form-text text-muted"></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="confirm_password" class="form-label cb-gold-text">
                                    Potvrdiť nové heslo
                                </label>
                                <input type="password"
                                       name="confirm_password"
                                       id="confirm_password"
                                       class="form-control text-center <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                       placeholder="Potvrďte nové heslo (iba pri zmene hesla)">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                                <?php else: ?>
                                    <div id="confirm_password_help" class="form-text text-muted"></div>
                                <?php endif; ?>
                            </div>
                            <!-- Tlačidlá -->
                            <div class="col-12">
                                <button type="submit" name="submit" class="btn btn-primary px-4" id="submitBtn">
                                    Upraviť účet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
