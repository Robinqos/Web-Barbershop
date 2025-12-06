<?php
/** @var \App\Models\User $user */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6">
            <div class="cb-dark-card my-5 shadow">
                <div class="card-body p-4">
                    <h2 class="cb-gold-text text-center mb-4">Potvrdenie zmazania účtu</h2>

                    <div class="bg-dark border-start border-danger p-2 mb-2 text-center">
                        <h5 class="text-danger fw-bold mb-3">
                            <i class="bi bi-exclamation-circle me-2"></i>Konečné varovanie
                        </h5>
                        <p class="text-light mb-3">Naozaj chcete zmazať svoj účet? <strong class="text-danger">Táto akcia je nevratná!</strong></p>

                        <div class="d-inline-block text-start">
                            <ul class="text-light ps-3 mb-0">
                                <li class="mb-1">Všetky vaše údaje budú vymazané</li>
                                <li class="mb-1">Túto akciu nie je možné vrátiť späť</li>
                                <li>Účet bude deaktivovaný</li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center">
                        <p class="mb-4">Ste si istý, že chcete zmazať účet <strong><?= htmlspecialchars($user->getEmail()) ?></strong>?</p>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="<?= $link->url("auth.index") ?>" class="btn btn-outline-secondary me-3">
                                Nie, späť na profil
                            </a>
                            <form method="post" action="<?= $link->url("auth.delete") ?>" class="d-inline">
                                <input type="hidden" name="id" value="<?= $user->getId() ?>">
                                <input type="hidden" name="confirm" value="yes">
                                <button type="submit" name="submit" class="btn btn-danger px-4">
                                    Áno, zmazať účet
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>