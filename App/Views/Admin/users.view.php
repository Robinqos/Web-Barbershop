<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\User[] $users */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="cb-gold-text">Používatelia</h1>
            <a href="<?= $link->url('admin.createUser') ?>" class="btn btn-warning">
                <i class="bi bi-plus-circle"></i> Pridať nového používateľa
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="cb-dark-card">
                <?php if (empty($users)): ?>
                    <p class="cb-text-muted text-center">Žiadni používatelia.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover text-center align-middle mb-0">
                            <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Meno</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Telefón</th>
                                <th class="text-center">Rola</th>
                                <th class="text-center">Dátum registrácie</th>
                                <th class="text-center">Akcie</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <!-- ID -->
                                    <td class="align-middle">
                                        <?= $user->getId() ?>
                                    </td>

                                    <!-- meno -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $user->getId() ?>"
                                             data-field="name"
                                             data-type="text"
                                             data-entity="user"
                                             title="Kliknite pre úpravu">
                                            <?= htmlspecialchars($user->getFullName() ?? 'Nezadané') ?>
                                        </div>
                                    </td>

                                    <!-- email -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $user->getId() ?>"
                                             data-field="email"
                                             data-type="email"
                                             data-entity="user"
                                             title="Kliknite pre úpravu">
                                            <?= htmlspecialchars($user->getEmail()) ?>
                                        </div>
                                    </td>

                                    <!-- cislo -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $user->getId() ?>"
                                             data-field="phone"
                                             data-type="text"
                                             data-entity="user"
                                             title="Kliknite pre úpravu">
                                            <?= htmlspecialchars($user->getPhone() ?? 'Nezadané') ?>
                                        </div>
                                    </td>

                                    <!-- rola -->
                                    <td class="align-middle">
                                        <div class="editable-cell mx-auto w-100 d-flex justify-content-center align-items-center"
                                             style="min-height: 40px;"
                                             data-id="<?= $user->getId() ?>"
                                             data-field="permissions"
                                             data-type="select"
                                             data-entity="user"
                                             data-options='<?= json_encode([
                                                 ['value' => '0', 'text' => 'Zákazník'],
                                                 ['value' => '1', 'text' => 'Barber'],
                                                 ['value' => '2', 'text' => 'Admin']
                                             ]) ?>'
                                             data-render="badge"
                                             title="Kliknite pre úpravu">
                                            <?php
                                            $roleText = [
                                                '0' => 'Zákazník',
                                                '1' => 'Barber',
                                                '2' => 'Admin'
                                            ];
                                            $permissions = (string)$user->getPermissions();
                                            ?>
                                            <span class="badge bg-<?=
                                            $permissions === '2' ? 'warning' :
                                                ($permissions === '1' ? 'primary' : 'info')
                                            ?> text-dark">
                                                <?= $roleText[$permissions] ?? 'Neznáma' ?>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- registracia -->
                                    <td class="align-middle">
                                        <?= date('d.m.Y H:i', strtotime($user->getCreatedAt())) ?>
                                    </td>

                                    <!-- akcie -->
                                    <td class="align-middle">
                                        <div class="mx-auto w-100 d-flex justify-content-center align-items-center gap-2" style="min-height: 40px;">
                                            <a href="<?= $link->url('admin.deleteUser', ['id' => $user->getId()]) ?>"
                                               class="btn btn-outline-danger btn-sm"
                                               title="Vymazať používateľa"
                                               onclick="return confirm('Naozaj chcete vymazať tohto používateľa? Táto akcia je nevratná!')">
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