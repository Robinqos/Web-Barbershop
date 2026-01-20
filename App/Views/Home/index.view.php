<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Service[] $services */
/** @var array $barbers  */
/** @var array $galleryItems  */
/** @var \App\Models\User|null $loggedUser */
/** @var bool $showUploadForm */
/** @var array $allBarbersForAdmin */
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

<!-- Gallery -->
<section id="galeria" class="cb-dark-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 cb-gold-text">GALÉRIA</h2>
                <p class="lead cb-text-muted">Naše najlepšie práce</p>
            </div>
        </div>

        <!-- fotky -->
        <div class="row justify-content-center">
            <?php foreach ($galleryItems as $galleryItemData):
                $item = $galleryItemData['item'];
                $canDelete = $galleryItemData['canDelete'];
                $photoPath = $galleryItemData['photoPath'];
                $exists = $galleryItemData['exists'];
                ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="cb-dark-card p-2 h-100 d-flex flex-column">
                        <!-- Fotka -->
                        <?php if ($exists): ?>
                            <a href="<?= htmlspecialchars($photoPath) ?>"
                               data-bs-toggle="modal"
                               data-bs-target="#galleryModal"
                               onclick="setGalleryImage('<?= htmlspecialchars($photoPath) ?>')">
                                <img src="<?= htmlspecialchars($photoPath) ?>"
                                     class="img-fluid rounded"
                                     alt="Galéria obrázok"
                                     style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;">
                            </a>
                        <?php else: ?>
                            <div class="rounded d-flex align-items-center justify-content-center"
                                 style="width: 100%; height: 200px; background-color: #d4af37;">
                                <i class="bi bi-image" style="font-size: 3rem; color: #1a1a1a;"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Popis -->
                        <?php if ($item->getServices()): ?>
                            <div class="mt-2 flex-grow-1">
                                <p class="cb-text-muted small mb-2">
                                    <?= htmlspecialchars($item->getServices()) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <!-- tlacidlo an zmazanie -->
                        <?php if ($canDelete): ?>
                            <div class="mt-2">
                                <form action="<?= $link->url('gallery.delete') ?>" method="POST"
                                      onsubmit="return confirm('Naozaj chcete odstrániť túto fotku?');">
                                    <input type="hidden" name="id" value="<?= $item->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="bi bi-trash"></i> Odstrániť
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!--na pridanie admin -->
        <?php if ($showUploadForm): ?>
            <div class="row justify-content-center mt-5">
                <div class="col-md-8">
                    <div class="cb-dark-card p-4">
                        <h3 class="cb-gold-text mb-4">Pridať novú fotku do galérie</h3>
                        <form action="<?= $link->url('gallery.store') ?>" method="POST" enctype="multipart/form-data">
                            <!-- Admin vyberie aj barbera -->
                            <div class="mb-3">
                                <label for="barber_id" class="form-label cb-text-muted">Barber</label>
                                <select class="form-control" id="barber_id" name="barber_id" required>
                                    <option value="">Vyberte barbera</option>
                                    <?php foreach ($allBarbersForAdmin as $barber): ?>
                                        <?php $barberUser = \App\Models\User::getOne($barber->getUserId()); ?>
                                        <option value="<?= $barber->getId() ?>">
                                            <?= htmlspecialchars($barberUser->getFullName()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="photo" class="form-label cb-text-muted">Fotka</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                                <div class="form-text cb-text-muted">
                                    Podporované formáty: JPG, PNG, GIF, WebP. Maximálna veľkosť: 5MB.
                                </div>
                            </div>

                            <!-- Popis -->
                            <div class="mb-3">
                                <label for="services" class="form-label cb-text-muted">Popis (voliteľné)</label>
                                <input type="text" class="form-control" id="services" name="services"
                                       placeholder="Napríklad: Pánsky strih, úprava brady...">
                                <div class="form-text cb-text-muted">
                                    Môžete uviesť, aké služby sú na fotke.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-upload"></i> Nahrať fotku
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($galleryItems)): ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="cb-dark-card text-center">
                        <p class="cb-text-muted mb-0">Galéria je momentálne prázdna.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>