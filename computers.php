<?php
require __DIR__ . '/includes/bootstrap.php';

$currentUser = require_admin($connection);

if (is_post()) {
    verify_csrf();

    $action = $_POST['action'] ?? '';
    $computerId = post_int('computerId');
    $manufacturer = post_string('manufacturer', 50);
    $model = post_string('model', 50);
    $serialNumber = post_string('serialNumber', 50);

    if ($action === 'add' || $action === 'update') {
        if ($manufacturer === '' || $model === '' || $serialNumber === '') {
            flash('danger', t('Fill_All_Fields'));
        } elseif ($action === 'add') {
            $connection
                ->prepare('INSERT INTO computers (manufacturer, model, serialNumber) VALUES (?, ?, ?)')
                ->execute([$manufacturer, $model, $serialNumber]);
            flash('success', t('Process_Successful'));
        } elseif ($computerId !== null) {
            $connection
                ->prepare('UPDATE computers SET manufacturer = ?, model = ?, serialNumber = ? WHERE computerId = ?')
                ->execute([$manufacturer, $model, $serialNumber, $computerId]);
            flash('success', t('Process_Successful'));
        }
    } elseif ($action === 'delete' && $computerId !== null) {
        delete_item($connection, 'computers', 'computerId', ITEM_COMPUTER, $computerId);
    }

    redirect('computers.php');
}

$computers = $connection
    ->query('SELECT computerId, manufacturer, model, serialNumber FROM computers ORDER BY manufacturer, model')
    ->fetchAll();
$openLoans = open_loans_by_item($connection, ITEM_COMPUTER);

$activePage = 'computers';
$pageTitle = t('Computers');
require __DIR__ . '/includes/header.php';
?>
<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= e(t('Add_New_Computer')) ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <form method="post" action="computers.php" class="row">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <div class="col-md-3 form-group">
                <input name="manufacturer" type="text" class="form-control" maxlength="50" required placeholder="<?= e(t('Enter_Manufacturer')) ?>">
            </div>
            <div class="col-md-3 form-group">
                <input name="model" type="text" class="form-control" maxlength="50" required placeholder="<?= e(t('Enter_Model')) ?>">
            </div>
            <div class="col-md-4 form-group">
                <input name="serialNumber" type="text" class="form-control" maxlength="50" required placeholder="<?= e(t('Enter_Serial_Number')) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block"><?= e(t('Add_Computer')) ?></button>
            </div>
        </form>
    </div>
</div>

<div class="box">
    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped data-table">
            <thead>
            <tr>
                <th><?= e(t('Manufacturer')) ?></th>
                <th><?= e(t('Model')) ?></th>
                <th><?= e(t('Serial_Number')) ?></th>
                <th><?= e(t('Status')) ?></th>
                <th><?= e(t('Operations')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($computers as $computer): ?>
                <?php $loan = $openLoans[(int) $computer['computerId']] ?? null; ?>
                <tr>
                    <td><?= e($computer['manufacturer']) ?></td>
                    <td><?= e($computer['model']) ?></td>
                    <td><?= e($computer['serialNumber']) ?></td>
                    <td><?php require __DIR__ . '/includes/loan-status.php'; ?></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editComputer<?= e($computer['computerId']) ?>" title="<?= e(t('Edit')) ?>">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <form method="post" action="computers.php" class="inline-form" data-confirm="<?= e(t('Confirm_Delete')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="computerId" value="<?= e($computer['computerId']) ?>">
                            <button type="submit" class="btn btn-danger btn-sm" title="<?= e(t('Delete')) ?>"><i class="fa fa-trash"></i></button>
                        </form>

                        <div class="modal fade" id="editComputer<?= e($computer['computerId']) ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form method="post" action="computers.php" class="modal-content">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="computerId" value="<?= e($computer['computerId']) ?>">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="<?= e(t('Close')) ?>">&times;</button>
                                        <h4 class="modal-title"><?= e(t('Edit_Computer')) ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label><?= e(t('Manufacturer')) ?></label>
                                            <input name="manufacturer" type="text" class="form-control" maxlength="50" required value="<?= e($computer['manufacturer']) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?= e(t('Model')) ?></label>
                                            <input name="model" type="text" class="form-control" maxlength="50" required value="<?= e($computer['model']) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?= e(t('Serial_Number')) ?></label>
                                            <input name="serialNumber" type="text" class="form-control" maxlength="50" required value="<?= e($computer['serialNumber']) ?>">
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
