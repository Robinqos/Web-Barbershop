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
                                <th class="text-center">Status</th>
                                <th class="text-center">Akcie</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($barbers as $barber): ?>
                                <?php $user = $barber->getUser(); ?>rrrrr
                                <tr>
                                    <td class="align-middle"><?= $barber->getId() ?></td>
                                    <td class="align-middle">
                                        <?= htmlspecialchars($barber->getName()) ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= htmlspecialchars($barber->getEmail()) ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= htmlspecialchars($barber->getPhone()) ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= htmlspecialchars(substr($barber->getBio() ?? '', 0, 50)) ?>
                                        <?php if (strlen($barber->getBio() ?? '') > 50): ?>...<?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-<?= $barber->getIsActive() ? 'success' : 'danger' ?>">
                                            <?= $barber->getIsActive() ? 'Aktívny' : 'Neaktívny' ?>
                                        </span>
                                    </td>
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