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

                    <form method="post" action="<?= $link->url("auth.update") ?>">
                        <div class="row g-3">
                            <!-- Osobné údaje -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <input type="hidden"
                                           name="id"
                                           id="id"
                                           class="form-control"
                                           value="<?= $user->getId() ?>">
                                    <label for="full_name" class="form-label cb-gold-text">
                                        Meno a priezvisko </label>
                                    <input type="text"
                                           name="full_name"
                                           id="full_name"
                                           class="form-control"
                                           value="<?= $user->getFullname() ?>">
                                    <span><?= $message ?? '' ?></span>
                                </div>
                            </div>

                            <!-- Tlačidlá -->
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                    <button type="submit" name="submit" class="btn btn-primary px-4">
                                        Upraviť účet
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