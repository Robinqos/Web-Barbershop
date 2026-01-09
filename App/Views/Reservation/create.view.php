<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var string|null $error */
/** @var \Framework\Auth\AppUser $user */
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="cb-dark-card">
                <h2 class="cb-gold-text text-center mb-4">Rezervácia termínu</h2>

                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= $link->url('reservation.store') ?>" id="reservationForm">
                    <div class="row g-3">
                        <!-- DÁTUM -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label cb-gold-text fw-bold">Dátum *</label>
                                <label for="reservationDate"></label>
                                <input type="date"
                                       id="reservationDate"
                                       name="date"
                                       class="form-control bg-dark text-white border-secondary"
                                       min="<?= date('Y-m-d') ?>"
                                       max="<?= date('Y-m-d', strtotime('+60 days')) ?>"
                                       required>
                                <small class="cb-text-muted">Rezervácie možné na 60 dní dopredu</small>
                            </div>
                        </div>

                        <!-- ČAS -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label cb-gold-text fw-bold">Čas *</label>
                                <label for="timeSelect"></label>
                                <select name="time" id="timeSelect" class="form-control bg-dark text-white border-secondary" required>
                                    <option value="">Najprv vyberte dátum</option>
                                    <!-- Časy sa naplnia dynamicky -->
                                </select>
                                <small class="cb-text-muted" id="openingHoursNote"></small>
                            </div>
                        </div>

                        <!-- SLUŽBA -->
                        <div class="col-12">
                            <div class="mb-4">
                                <label class="form-label cb-gold-text fw-bold">Služba *</label>
                                <div class="row g-3">
                                    <?php
                                    foreach (\App\Models\Service::getAll() as $service) :
                                        ?>
                                        <div class="col-md-6">
                                            <div class="form-check p-3 bg-dark rounded border border-secondary service-option">
                                                <input class="form-check-input"
                                                       type="radio"
                                                       name="service_id"
                                                       id="service_<?= $service->getId() ?>"
                                                       value="<?= $service->getId() ?>"
                                                       required
                                                       data-price="<?= $service->getPrice() ?>"
                                                       data-name="<?= htmlspecialchars($service->getTitle()) ?>"
                                                       data-duration="<?= $service->getDuration() ?>">
                                                <label class="form-check-label w-100" for="service_<?= $service->getId() ?>">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-2 cb-gold-text"><?= htmlspecialchars($service->getTitle()) ?></h6>
                                                            <small class="cb-text-muted"><?= $service->getDuration() ?> min</small>
                                                        </div>
                                                        <div>
                                                            <span class="cb-price fs-4"><?= $service->getPrice() ?>€</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- KONTAKTNÉ ÚDAJE -->
                        <div class="col-12">
                            <div class="mb-4 p-3 bg-dark rounded border border-secondary">
                                <h5 class="cb-gold-text mb-3">Kontaktné údaje</h5>

                                <div class="row g-3">
                                    <!-- MENO - celá šírka -->
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label cb-gold-text">Meno a priezvisko *</label>
                                            <label for="customerName"></label>
                                            <input type="text"
                                                   name="guest_name"
                                                   id="customerName"
                                                   class="form-control bg-dark text-white border-secondary"
                                                   value="<?= htmlspecialchars($user->isLoggedIn() ? $user->getFullname() : '') ?>"
                                                   placeholder="Zadajte vaše meno"
                                                   <?= $user->isLoggedIn() ? 'readonly' : '' ?>
                                                   required>
                                            <div id="customerName_help" class="form-text text-danger"></div>
                                        </div>
                                    </div>

                                    <!-- TELEFÓN - celá šírka -->
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label cb-gold-text">Telefón *</label>
                                            <label for="phone"></label>
                                            <input type="tel"
                                                   name="guest_phone"
                                                   id="phone"
                                                   class="form-control bg-dark text-white border-secondary"
                                                   value="<?= htmlspecialchars($user->isLoggedIn() ? $user->getPhone() : '') ?>"
                                                   pattern="[0-9]{9,15}"
                                                   placeholder="+421 918 123 456"
                                                   <?= $user->isLoggedIn() ? 'readonly' : '' ?>
                                                   required>
                                            <div id="phone_help" class="form-text text-danger"></div>
                                        </div>
                                    </div>

                                    <!-- EMAIL - celá šírka -->
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label cb-gold-text">Email *</label>
                                            <label for="email"></label>
                                            <input type="email"
                                                   name="guest_email"
                                                   id="email"
                                                   class="form-control bg-dark text-white border-secondary"
                                                   value="<?= htmlspecialchars($user->isLoggedIn() ? ($user->getEmail() ?? '') : '') ?>"
                                                   placeholder="vasemail@domena.sk"
                                                   <?= $user->isLoggedIn() ? 'readonly' : '' ?>
                                                   required>
                                            <div id="email_help" class="form-text text-danger"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- POZNÁMKA - taktiež celá šírka -->
                                <div class="col-12">
                                    <div class="mt-3">
                                        <label class="form-label cb-gold-text">Poznámka (voliteľné)</label>
                                        <textarea name="note"
                                                  id="note"
                                                  class="form-control bg-dark text-white border-secondary"
                                                  rows="2"
                                                  placeholder="Špeciálne požiadavky..."
                                                  maxlength="70"></textarea>
                                        <small class="cb-text-muted">Zostávajúce znaky: 70/<span id="noteCounter">70</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ZHRNUTIE -->
                        <div class="col-12">
                            <div class="mb-4 p-3 bg-dark rounded border border-secondary">
                                <h5 class="cb-gold-text mb-3 text-center">Zhrnutie rezervácie</h5>
                                <div class="row text-center">
                                    <div class="col-md-3 mb-2">
                                        <strong class="cb-gold-text d-block mb-1">Dátum</strong>
                                        <span id="summaryDate" class="cb-text-muted">Nie je vybrané</span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong class="cb-gold-text d-block mb-1">Čas</strong>
                                        <span id="summaryTime" class="cb-text-muted">Nie je vybraný</span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong class="cb-gold-text d-block mb-1">Služba</strong>
                                        <span id="summaryService" class="cb-text-muted">Nie je vybraná</span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong class="cb-gold-text d-block mb-1">Cena</strong>
                                        <span id="summaryPrice" class="cb-price">0€</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TLAČIDLÁ -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= $link->url('home.index')  ?>" class="btn btn-secondary">
                                    ← Späť na domov
                                </a>
                                <div>
                                    <button type="submit" name="submit" class="btn cb-btn-gold">
                                        Potvrdiť rezerváciu
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>