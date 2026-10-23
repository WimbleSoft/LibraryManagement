<?php
require __DIR__ . '/includes/bootstrap.php';

$currentUser = require_admin($connection);

if (is_post()) {
    verify_csrf();

    $action = $_POST['action'] ?? '';
    $keyId = post_int('keyId');
    $keyNumber = post_string('keyNumber', 20);
    $keyDescription = post_string('keyDescription', 255);

    if ($action === 'add' || $action === 'update') {
        if ($keyNumber === '' || $keyDescription === '') {
            flash('danger', t('Fill_All_Fields'));
        } elseif ($action === 'add') {
            $connection
                ->prepare('INSERT INTO roomkeys (keyNumber, keyDescription) VALUES (?, ?)')
                ->execute([$keyNumber, $keyDescription]);
            flash('success', t('Process_Successful'));
        } elseif ($keyId !== null) {
            $connection
                ->prepare('UPDATE roomkeys SET keyNumber = ?, keyDescription = ? WHERE keyId = ?')
                ->execute([$keyNumber, $keyDescription, $keyId]);
            flash('success', t('Process_Successful'));
        }
    } elseif ($action === 'delete' && $keyId !== null) {
        delete_item($connection, 'roomkeys', 'keyId', ITEM_KEY, $keyId);
    }

    redirect('keys.php');
}

$keys = $connection->query('SELECT keyId, keyNumber, keyDescription FROM roomkeys ORDER BY keyNumber')->fetchAll();
$openLoans = open_loans_by_item($connection, ITEM_KEY);

$activePage = 'keys';
$pageTitle = t('Keys');
require __DIR__ . '/includes/header.php';
?>
<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= e(t('Add_New_Key')) ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <form method="post" action="keys.php" class="row">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <div class="col-md-4 form-group">
                <input name="keyNumber" type="text" class="form-control" maxlength="20" required placeholder="<?= e(t('Enter_Key_Number')) ?>">
            </div>
            <div class="col-md-6 form-group">
                <input name="keyDescription" type="text" class="form-control" maxlength="255" required placeholder="<?= e(t('Enter_Key_Description')) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block"><?= e(t('Add_Key')) ?></button>
            </div>
        </form>
    </div>
</div>

<div class="box">
    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped data-table">
            <thead>
            <tr>
                <th><?= e(t('Key_Number')) ?></th>
                <th><?= e(t('Description')) ?></th>
                <th><?= e(t('Status')) ?></th>
                <th><?= e(t('Operations')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($keys as $key): ?>
                <?php $loan = $openLoans[(int) $key['keyId']] ?? null; ?>
                <tr>
                    <td><?= e($key['keyNumber']) ?></td>
                    <td><?= e($key['keyDescription']) ?></td>
                    <td><?php require __DIR__ . '/includes/loan-status.php'; ?></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editKey<?= e($key['keyId']) ?>" title="<?= e(t('Edit')) ?>">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <form method="post" action="keys.php" class="inline-form" data-confirm="<?= e(t('Confirm_Delete')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="keyId" value="<?= e($key['keyId']) ?>">
                            <button type="submit" class="btn btn-danger btn-sm" title="<?= e(t('Delete')) ?>"><i class="fa fa-trash"></i></button>
                        </form>

                        <div class="modal fade" id="editKey<?= e($key['keyId']) ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form method="post" action="keys.php" class="modal-content">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="keyId" value="<?= e($key['keyId']) ?>">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="<?= e(t('Close')) ?>">&times;</button>
                                        <h4 class="modal-title"><?= e(t('Edit_Key')) ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label><?= e(t('Key_Number')) ?></label>
                                            <input name="keyNumber" type="text" class="form-control" maxlength="20" required value="<?= e($key['keyNumber']) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?= e(t('Description')) ?></label>
                                            <input name="keyDescription" type="text" class="form-control" maxlength="255" required value="<?= e($key['keyDescription']) ?>">
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
