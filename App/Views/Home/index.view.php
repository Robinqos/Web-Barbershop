<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Service[] $services */
/** @var array $barbers Pole s údajmi o barberoch */
?>

<!-- HERO SEKCIA -->
<section class="cb-hero-section">
    <div class="container text-center">
        <h1 class="display-5 cb-gold-text">CROWN BARBER</h1>
        <p class="lead cb-gold-subtitle">Královský prístup k vášmu štýlu</p>
        <a href="#sluzby" class="btn cb-btn-gold btn-lg mt-3">Pozrieť služby</a>
    </div>
</section>

<!-- SLUZBY -->
<section id="sluzby" class="cb-dark-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 cb-gold-text">NAŠE SLUŽBY</h2>
                <p class="lead cb-text-muted">Ponúkame špičkové služby pre moderných mužov</p>
            </div>
        </div>

        <?php foreach ($services as $service): ?>
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <div class="cb-dark-card mb-4">
                        <h3 class="cb-gold-text"><?= htmlspecialchars($service->getTitle()) ?></h3>
                        <p class="cb-text-muted"><?= htmlspecialchars($service->getDescription()) ?></p>
                        <span class="cb-price"><?= $service->getPrice() ?>€</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- BARBERI -->
<section id="barberi" class="cb-dark-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 cb-gold-text">NAŠI BARBERI</h2>
                <p class="lead cb-text-muted">Profesionáli s láskou k remeslu</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <?php foreach ($barbers as $barberData):
                $barber = $barberData['barber'];
                $user = $barberData['user'];
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="cb-dark-card text-center h-100 d-flex flex-column">
                        <!-- MENO -->
                        <h3 class="cb-gold-text mb-3"><?= htmlspecialchars($user->getFullName()) ?></h3>

                        <!-- FOTKA -->
                        <div class="mb-3">
                            <?php
                            $photoPath = $barber->getPhotoPath();
                            if ($photoPath && file_exists($_SERVER['DOCUMENT_ROOT'] . $photoPath)):
                                ?>
                                <img src="<?= htmlspecialchars($photoPath) ?>"
                                     alt="<?= htmlspecialchars($user->getFullName()) ?>"
                                     class="img-fluid rounded"
                                     style="width: 100%; max-width: 250px; height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <!-- Fallback -->
                                <div class="rounded d-inline-flex align-items-center justify-content-center"
                                     style="width: 250px; height: 250px; background-color: #d4af37; color: #1a1a1a;">
                                    <span style="font-size: 3rem;"><?= substr(htmlspecialchars($user->getFullName()), 0, 1) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- BIO -->
                        <div class="flex-grow-1">
                            <p class="cb-text-muted mb-4"><?= htmlspecialchars($barber->getBio()) ?></p>
                        </div>

                        <!-- HODNOTENIE -->
                        <div class="mt-3">
                            <?php if ($barberData['reviewCount'] > 0): ?>
                                <div class="cb-gold-text fs-5">
                                    <?= $barberData['starRating'] ?>
                                </div>
                                <div class="cb-text-muted small">
                                    <?= $barberData['formattedRating'] ?> / 5
                                    (<?= $barberData['reviewCount'] ?> recenzií)
                                </div>
                            <?php else: ?>
                                <div class="cb-text-muted small">
                                    <em>Zatiaľ žiadne hodnotenia</em>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($barbers)): ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="cb-dark-card text-center">
                        <p class="cb-text-muted mb-0">Momentálne nie sú k dispozícii žiadni barberi.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>