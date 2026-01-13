<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Service[] $services */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="cb-gold-text">Služby</h1>
            <a href="<?= $link->url('admin.createService') ?>" class="btn btn-warning">
                <i class="bi bi-plus-circle"></i> Pridať novú službu
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="cb-dark-card">
                <?php if (empty($services)): ?>
                    <p class="cb-text-muted text-center">Žiadne služby.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover text-center align-middle mb-0">
                            <thead>
                            <tr>
                                <th class="text-center">Názov</th>
                                <th class="text-center">Popis</th>
                                <th class="text-center">Cena</th>
                                <th class="text-center">Trvanie (min)</th>
                                <th class="text-center">Akcie</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <!-- nazov -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $service->getId() ?>"
                                             data-field="title"
                                             data-type="text"
                                             data-entity="service"
                                             title="Kliknite pre úpravu">
                                            <?= htmlspecialchars($service->getTitle()) ?>
                                        </div>
                                    </td>

                                    <!-- popis -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $service->getId() ?>"
                                             data-field="description"
                                             data-type="textarea"
                                             data-entity="service"
                                             title="Kliknite pre úpravu">
                                            <div class="text-truncate text-start">
                                                <?= htmlspecialchars($service->getDescription() ?? '') ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- cena -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $service->getId() ?>"
                                             data-field="price"
                                             data-type="number"
                                             data-entity="service"
                                             data-render="price"
                                             title="Kliknite pre úpravu">
                                            <?= number_format($service->getPrice()) ?> €
                                        </div>
                                    </td>

                                    <!-- trvanie -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $service->getId() ?>"
                                             data-field="duration"
                                             data-type="number"
                                             data-entity="service"
                                             data-render="duration"
                                             title="Kliknite pre úpravu">
                                            <?= $service->getDuration() ?> min
                                        </div>
                                    </td>

                                    <!-- akcie -->
                                    <td class="align-middle">
                                        <div class="mx-auto w-100 d-flex justify-content-center align-items-center" style="min-height: 40px;">
                                            <a href="<?= $link->url('admin.deleteService', ['id' => $service->getId()]) ?>"
                                               class="btn btn-outline-danger btn-sm"
                                               title="Vymazať službu"
                                               onclick="return confirm('Naozaj chcete vymazať túto službu? Táto akcia je nevratná a môže ovplyvniť existujúce rezervácie!')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>