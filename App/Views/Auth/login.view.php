<?php

/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');
?>

<div class="container">
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="cb-dark-card">
                <div class="card-body">
                    <h5 class="cb-gold-text text-center">Prihlásenie</h5>
                    <div class="text-center text-danger mb-3">
                        <?= @$message ?>
                    </div>
                    <form class="form-signin" method="post" action="<?= $link->url("login") ?>">
                        <div class="form-label-group mb-3">
                            <label for="email" class="form-label cb-gold-text">E-mail</label>
                            <input name="email" type="email" id="email"
                                   class="form-control"
                                   placeholder="Emailová adresa"
                                   required autofocus>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="password" class="form-label cb-gold-text">Heslo</label>
                            <input name="password" type="password" id="password"
                                   class="form-control"
                                   placeholder="Heslo"
                                   required>
                        </div>


                        <div class="text-center">
                            <button class="btn cb-btn-gold" type="submit" name="submit" >Prihlásiť
                            </button>
                            <button onclick="window.location.href = '<?= $link->url("auth.register")?>'"
                                    class="btn cb-btn-gold cb-gold-text"
                                    type="button"
                                    name="submit">Registrácia
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>