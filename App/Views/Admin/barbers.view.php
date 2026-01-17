<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Barber[] $barbers */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="cb-gold-text">Barberi</h1>
            <a href="<?= $link->url('admin.createBarber') ?>" class="btn btn-warning">
                <i class="bi bi-plus-circle"></i> Pridať nového barbera
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="cb-dark-card">
                <?php if (empty($barbers)): ?>
                    <p class="cb-text-muted text-center">Žiadni barberi.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover text-center align-middle mb-0">
                            <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Meno</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Telefón</th>
                                <th class="text-center">Bio</th>
                                <th class="text-center">URL fotky</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Dátum pridania</th>
                                <th class="text-center">Akcie</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($barbers as $barber): ?>
                                <?php $user = $barber->getUser(); ?>
                                <tr>
                                    <td class="align-middle"><?= $barber->getId() ?></td>
                                    <!-- meno -->
                                    <td class="align-middle">
                                        <div class="editable-cell"
                                             data-id="<?= $barber->getId() ?>"
                                             data-field="name"
                                             data-type="text"
                                             data-entity="barber"
                                             title="Kliknite pre úpravu">
                                            <?= htmlspecialchars($barber->getName()) ?>
                                        </div>
                                    </td>
                                    <!-- email -->
                                    <td class="align-middle">
                                        <div class="editable-cell"
                                             data-id="<?= $barber->getId() ?>"
                                             data-field="email"
                                             data-type="email"
                                             data-entity="barber"
                                             title="Kliknite pre úpravu">
                                            <?= htmlspecialchars($barber->getEmail()) ?>
                                        </div>
                                    </td>
                                    <!-- cislo -->
                                    <td class="align-middle">
                                        <div class="editable-cell"
                                             data-id="<?= $barber->getId() ?>"
                                             data-field="phone"
                                             data-type="text"
                                             data-entity="barber"
                                             title="Kliknite pre úpravu">
                                            <?= htmlspecialchars($barber->getPhone()) ?>
                                        </div>
                                    </td>
                                    <!-- bio -->
                                    <td class="align-middle">
                                        <div class="editable-cell"
                                             data-id="<?= $barber->getId() ?>"
                                             data-field="bio"
                                             data-type="textarea"
                                             data-entity="barber"
                                             data-original-value="<?= htmlspecialchars($barber->getBio() ?? '') ?>"
                                             title="Kliknite pre úpravu">
                                            <?= htmlspecialchars($barber->getBio() ?? '') ?>
                                        </div>
                                    </td>
                                    <!-- fotka path -->
                                    <td class="align-middle">
                                        <div class="editable-cell"
                                             data-id="<?= $barber->getId() ?>"
                                             data-field="photo_path"
                                        data-type="text"
                                        data-entity="barber"
                                        title="Kliknite pre úpravu">
                                        <?php if ($barber->getPhotoPath()): ?>
                                            <a href="<?= htmlspecialchars($barber->getPhotoPath()) ?>"
                                               target="_blank"
                                               class="text-warning text-decoration-none"
                                               onclick="event.stopPropagation()">
                                                Fotka
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Nezadané</span>
                                        <?php endif; ?>
                                        </div>
                                    </td>
                                    <!-- status -->
                                    <td class="align-middle">
                                        <div class="editable-cell"
                                             data-id="<?= $barber->getId() ?>"
                                             data-field="is_active"
                                             data-type="select"
                                             data-entity="barber"
                                             data-options='<?= json_encode([
                                                 ['value' => '1', 'text' => 'Aktívny'],
                                                 ['value' => '0', 'text' => 'Neaktívny']
                                             ]) ?>'
                                             data-render="badge"
                                             title="Kliknite pre úpravu">
                                            <?php
                                            $status = $barber->getIsActive() ? 'Aktívny' : 'Neaktívny';
                                            $badgeClass = $barber->getIsActive() ? 'success' : 'danger';
                                            ?>
                                            <span class="badge bg-<?= $badgeClass ?> text-dark">
                                                <?= $status ?>
                                            </span>
                                        </div>
                                    </td>
                                    <!-- datum pridania(readonly) -->
                                    <td class="align-middle">
                                        <?= date('d.m.Y H:i', strtotime($barber->getCreatedAt())) ?>
                                    </td>
                                    <!-- AKCIE -->
                                    <td class="align-middle">
                                        <a href="<?= $link->url('admin.deleteBarber', ['id' => $barber->getId()]) ?>"
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('Naozaj chcete vymazať tohto barbera?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
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