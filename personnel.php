<?php
require __DIR__ . '/includes/bootstrap.php';

$currentUser = require_admin($connection);

if (is_post()) {
    verify_csrf();

    $action = $_POST['action'] ?? '';
    $personnelId = post_int('personnelId');
    $isSelf = $personnelId === (int) $currentUser['personnelId'];

    if ($action === 'add' || $action === 'update') {
        $fullName = post_string('fullName', 100);
        $username = post_string('username', 50);
        $email = post_string('email', 100);
        $phone = post_string('phone', 30);
        $password = $_POST['password'] ?? '';
        $password = is_string($password) ? $password : '';
        // Administrators cannot remove their own admin rights and lock themselves out.
        $isAdmin = ($isSelf || isset($_POST['isAdmin'])) ? 1 : 0;

        if ($fullName === '' || $username === '' || ($action === 'add' && $password === '')) {
            flash('danger', t('Fill_All_Fields'));
        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('danger', t('Invalid_Email'));
        } elseif ($password !== '' && !is_valid_password($password)) {
            flash('danger', t('Password_Too_Short'));
        } else {
            try {
                if ($action === 'add') {
                    $connection
                        ->prepare('INSERT INTO personnel (username, password, fullName, email, phone, isAdmin) VALUES (?, ?, ?, ?, ?, ?)')
                        ->execute([$username, hash_password($password), $fullName, $email, $phone, $isAdmin]);
                } elseif ($personnelId !== null) {
                    $connection
                        ->prepare('UPDATE personnel SET username = ?, fullName = ?, email = ?, phone = ?, isAdmin = ? WHERE personnelId = ?')
                        ->execute([$username, $fullName, $email, $phone, $isAdmin, $personnelId]);

                    if ($password !== '') {
                        $connection
                            ->prepare('UPDATE personnel SET password = ? WHERE personnelId = ?')
                            ->execute([hash_password($password), $personnelId]);
                    }
                }
                flash('success', t('Process_Successful'));
            } catch (PDOException $exception) {
                if ($exception->getCode() !== '23000') {
                    throw $exception;
                }
                flash('danger', t('Username_Taken'));
            }
        }
    } elseif ($action === 'delete' && $personnelId !== null) {
        $openLoans = $connection->prepare('SELECT 1 FROM loans WHERE borrowerId = ? AND returnDate IS NULL LIMIT 1');
        $openLoans->execute([$personnelId]);

        if ($isSelf) {
            flash('danger', t('Cannot_Delete_Self'));
        } elseif ($openLoans->fetchColumn()) {
            flash('danger', t('Personnel_Has_Open_Loans'));
        } else {
            // Loan history is kept; the person is shown as "deleted personnel" there.
            $connection->prepare('DELETE FROM personnel WHERE personnelId = ?')->execute([$personnelId]);
            flash('success', t('Record_Deleted'));
        }
    }

    redirect('personnel.php');
}

$personnel = $connection
    ->query('SELECT personnelId, username, fullName, email, phone, isAdmin FROM personnel ORDER BY fullName')
    ->fetchAll();

$activePage = 'personnel';
$pageTitle = t('Personnel');
require __DIR__ . '/includes/header.php';
?>
<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= e(t('Add_New_Personnel')) ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <form method="post" action="personnel.php" class="row">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <div class="col-md-2 form-group">
                <input name="fullName" type="text" class="form-control" maxlength="100" required placeholder="<?= e(t('Enter_Full_Name')) ?>">
            </div>
            <div class="col-md-2 form-group">
                <input name="phone" type="tel" class="form-control" maxlength="30" placeholder="<?= e(t('Enter_Phone')) ?>">
            </div>
            <div class="col-md-2 form-group">
                <input name="email" type="email" class="form-control" maxlength="100" placeholder="<?= e(t('Enter_Email')) ?>">
            </div>
            <div class="col-md-2 form-group">
                <input name="username" type="text" class="form-control" maxlength="50" required autocomplete="off" placeholder="<?= e(t('Enter_Username')) ?>">
            </div>
            <div class="col-md-2 form-group">
                <input name="password" type="password" class="form-control" minlength="8" required autocomplete="new-password" placeholder="<?= e(t('Enter_Password')) ?>">
            </div>
            <div class="col-md-1 form-group">
                <div class="checkbox">
                    <label><input name="isAdmin" type="checkbox" value="1"> <?= e(t('Admin')) ?></label>
                </div>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-block"><?= e(t('Add')) ?></button>
            </div>
        </form>
    </div>
</div>

<div class="box">
    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped data-table">
            <thead>
            <tr>
                <th><?= e(t('Full_Name')) ?></th>
                <th><?= e(t('Username')) ?></th>
                <th><?= e(t('Email')) ?></th>
                <th><?= e(t('Phone')) ?></th>
                <th><?= e(t('Admin')) ?></th>
                <th><?= e(t('Operations')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($personnel as $person): ?>
                <?php $isSelf = (int) $person['personnelId'] === (int) $currentUser['personnelId']; ?>
                <tr>
                    <td><?= e($person['fullName']) ?></td>
                    <td><?= e($person['username']) ?></td>
                    <td><?= e($person['email']) ?></td>
                    <td><?= e($person['phone']) ?></td>
                    <td><?= (int) $person['isAdmin'] === 1 ? '<i class="fa fa-check text-green"></i>' : '' ?></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editPersonnel<?= e($person['personnelId']) ?>" title="<?= e(t('Edit')) ?>">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <?php if (!$isSelf): ?>
                            <form method="post" action="personnel.php" class="inline-form" data-confirm="<?= e(t('Confirm_Delete')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="personnelId" value="<?= e($person['personnelId']) ?>">
                                <button type="submit" class="btn btn-danger btn-sm" title="<?= e(t('Delete')) ?>"><i class="fa fa-trash"></i></button>
                            </form>
                        <?php endif; ?>

                        <div class="modal fade" id="editPersonnel<?= e($person['personnelId']) ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form method="post" action="personnel.php" class="modal-content">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="personnelId" value="<?= e($person['personnelId']) ?>">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="<?= e(t('Close')) ?>">&times;</button>
                                        <h4 class="modal-title"><?= e(t('Edit_Personnel')) ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label><?= e(t('Full_Name')) ?></label>
                                            <input name="fullName" type="text" class="form-control" maxlength="100" required value="<?= e($person['fullName']) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?= e(t('Phone')) ?></label>
                                            <input name="phone" type="tel" class="form-control" maxlength="30" value="<?= e($person['phone']) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?= e(t('Email')) ?></label>
                                            <input name="email" type="email" class="form-control" maxlength="100" value="<?= e($person['email']) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?= e(t('Username')) ?></label>
                                            <input name="username" type="text" class="form-control" maxlength="50" required autocomplete="off" value="<?= e($person['username']) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?= e(t('Password')) ?></label>
                                            <input name="password" type="password" class="form-control" minlength="8" autocomplete="new-password" placeholder="<?= e(t('Leave_Blank_To_Keep_Password')) ?>">
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input name="isAdmin" type="checkbox" value="1" <?= (int) $person['isAdmin'] === 1 ? 'checked' : '' ?> <?= $isSelf ? 'disabled' : '' ?>>
                                                <?= e(t('Is_Admin')) ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= e(t('Cancel')) ?></button>
                                        <button type="submit" class="btn btn-warning"><?= e(t('Save')) ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
