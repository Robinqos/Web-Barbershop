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
        <a href="<?= $link->url('reservation.create') ?>" class="btn cb-btn-gold btn-lg mt-3">
            Rezervovať termín
        </a>
    </div>
</section>

<!-- O NAS -->
<section id="onas" class="cb-dark-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 cb-gold-text">O NÁS</h2>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="cb-dark-card p-4">
                    <div class="text-center">
                        <p class="cb-text-muted fs-5 mb-4">
                            Crown Barber je viac než len barbershop. Sme miesto, kde sa tradičné remeslo stretáva
                            s moderným dizajnom a každý klient sa cíti príjemne.
                        </p>

                        <p class="cb-text-muted mb-4">
                            Naši skúsení barberi sa venujú každému detailu. Pomôžeme vám nájsť a zdôrazniť váš
                            jedinečný štýl v uvoľnenej atmosfére.
                        </p>

                        <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                            <span class="badge bg-warning text-dark p-2">
                                <i class="bi bi-person-check"></i> Profesionáli
                            </span>
                            <span class="badge bg-warning text-dark p-2">
                                <i class="bi bi-award"></i> Kvalita
                            </span>
                            <span class="badge bg-warning text-dark p-2">
                                <i class="bi bi-emoji-smile"></i> Individuálny prístup
                            </span>
                            <span class="badge bg-warning text-dark p-2">
                                <i class="bi bi-calendar-check"></i> Online rezervácie
                            </span>
                        </div>

                        <p class="cb-text-muted fst-italic">
                            "Dobrý strih nie je len o vzhľade. Je o sebavedomí, postoji a o tom, ako sa cítiš."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SLUZBY -->
<section id="sluzby" class="cb-dark-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 cb-gold-text">NAŠE SLUŽBY</h2>
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

        <!-- HLAVNA FOTKA - zobrazí sa len jedna -->
        <?php if (!empty($galleryItems)):
            // 1.fotkau
            $mainGalleryItem = $galleryItems[0];
            $item = $mainGalleryItem['item'];
            $canDelete = $mainGalleryItem['canDelete'];
            $photoPath = $mainGalleryItem['photoPath'];
            $exists = $mainGalleryItem['exists'];
            ?>
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="cb-dark-card p-3">
                        <!-- Hlavna, klikatelne cez modal -->
                        <?php if ($exists): ?>
                            <div class="text-center mb-3">
                                <a href="#"
                                   data-bs-toggle="modal"
                                   data-bs-target="#galleryCarouselModal">
                                    <img src="<?= htmlspecialchars($photoPath) ?>"
                                         class="img-fluid rounded"
                                         alt="Galéria"
                                         style="width: 100%; max-height: 500px; object-fit: cover; cursor: pointer;">
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="rounded d-flex align-items-center justify-content-center"
                                 style="width: 100%; height: 300px; background-color: #d4af37;">
                                <i class="bi bi-image" style="font-size: 5rem; color: #1a1a1a;"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Popis hlavnej fotky -->
                        <?php if ($item->getServices()): ?>
                            <div class="text-center mt-3">
                                <p class="cb-price mb-2" style="font-size: 1.6rem;">
                                    <?= htmlspecialchars($item->getServices()) ?>
                                </p>
                                <!- meno barbera -->
                                <p class="cb-text-muted small">
                                    <i class="bi bi-person"></i> <?= htmlspecialchars($mainGalleryItem['barberName']) ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <!-- ak nieje popois, tak meno -->
                            <div class="text-center mt-3">
                                <p class="cb-text-muted small">
                                    <i class="bi bi-person"></i> <?= htmlspecialchars($mainGalleryItem['barberName']) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <button class="btn btn-outline-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryCarouselModal">
                            <i class="bi bi-arrows-fullscreen"></i> Otvoriť galériu
                        </button>

                        <!-- zmazanie, ak je opravneny -->
                        <?php if ($canDelete): ?>
                            <div class="mt-3">
                                <form action="<?= $link->url('gallery.delete') ?>" method="POST"
                                      onsubmit="return confirm('Naozaj chcete odstrániť túto fotku?');">
                                    <input type="hidden" name="id" value="<?= $item->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="bi bi-trash"></i> Odstrániť túto fotku
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- ak ziadne fotku -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="cb-dark-card text-center p-5">
                        <i class="bi bi-images" style="font-size: 4rem; color: #d4af37;"></i>
                        <p class="cb-text-muted mt-3">Galéria je momentálne prázdna.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Upload fotkt (iba pre admin/barbera) -->
        <?php if ($showUploadForm): ?>
            <div class="row justify-content-center mt-5">
                <div class="col-md-8">
                    <div class="cb-dark-card p-4">
                        <h3 class="cb-gold-text mb-4">Pridať novú fotku do galérie</h3>
                        <form action="<?= $link->url('gallery.store') ?>" method="POST" enctype="multipart/form-data">
                            <!-- Admin vyberie barbera -->
                            <?php if ($loggedUser && $loggedUser->getPermissions() === \App\Models\User::ROLE_ADMIN): ?>
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
                            <?php elseif ($loggedUser && $loggedUser->getPermissions() === \App\Models\User::ROLE_BARBER): ?>
                                <?php
                                $loggedBarber = \App\Models\Barber::getByUserId($loggedUser->getId());
                                if ($loggedBarber): ?>
                                    <input type="hidden" name="barber_id" value="<?= $loggedBarber->getId() ?>">
                                    <div class="mb-3">
                                        <label class="form-label cb-text-muted">Barber</label>
                                        <div class="form-control bg-dark text-white" style="border: 1px solid #333; cursor: not-allowed;">
                                            <?= htmlspecialchars($loggedUser->getFullName()) ?> (Vy)
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

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
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-upload"></i> Nahrať fotku
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- MODAL S BOOTSTRAP CAROUSEL -->
<div class="modal fade" id="galleryCarouselModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title cb-gold-text">Galéria <span class="badge bg-warning text-dark"><?= count($galleryItems) ?> fotiek</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Bootstrap Carousel -->
                <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php foreach ($galleryItems as $index => $galleryItemData): ?>
                            <button type="button"
                                    data-bs-target="#galleryCarousel"
                                    data-bs-slide-to="<?= $index ?>"
                                    class="<?= $index === 0 ? 'active' : '' ?>"
                                    aria-label="Slide <?= $index + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Carousel itemy -->
                    <div class="carousel-inner">
                        <?php foreach ($galleryItems as $index => $galleryItemData):
                            $item = $galleryItemData['item'];
                            $photoPath = $galleryItemData['photoPath'];
                            $exists = $galleryItemData['exists'];
                            $canDeleteModal = $galleryItemData['canDelete'];
                            ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <div class="text-center" style="min-height: 70vh;">
                                    <?php if ($exists): ?>
                                        <img src="<?= htmlspecialchars($photoPath) ?>"
                                             class="d-block mx-auto img-fluid"
                                             alt="Galéria obrázok"
                                             style="max-height: 60vh; max-width: 100%; object-fit: contain;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center mx-auto"
                                             style="height: 60vh; width: 100%; background-color: #d4af37;">
                                            <i class="bi bi-image" style="font-size: 5rem; color: #1a1a1a;"></i>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Popis pod fotkou -->
                                    <div class="mt-3 px-4">
                                        <?php if ($item->getServices()): ?>
                                            <p class="cb-price h4 mb-2 text-center"><?= htmlspecialchars($item->getServices()) ?></p>
                                        <?php endif; ?>

                                        <!-- Meno barbera -->
                                        <p class="cb-text-muted text-center small mb-2">
                                            <i class="bi bi-person"></i> <?= htmlspecialchars($galleryItemData['barberName']) ?>
                                        </p>

                                        <!-- cislovanie -->
                                        <p class="text-muted text-center small mt-2">
                                            Fotka <?= $index + 1 ?> z <?= count($galleryItems) ?>
                                        </p>

                                        <!-- na zmazanie v modale -->
                                        <?php if ($canDeleteModal): ?>
                                            <div class="text-center mt-2">
                                                <form action="<?= $link->url('gallery.delete') ?>" method="POST"
                                                      onsubmit="return confirm('Naozaj chcete odstrániť túto fotku?');">
                                                    <input type="hidden" name="id" value="<?= $item->getId() ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Odstrániť
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Carousel Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
            </div>
        </div>
    </div>
</div>