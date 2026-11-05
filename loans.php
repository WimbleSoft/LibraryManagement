<?php
require __DIR__ . '/includes/bootstrap.php';

$currentUser = require_admin($connection);

$rules = lending_rules($connection);
$today = today();

if (is_post()) {
    verify_csrf();

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $itemType = post_int('itemType');
        $itemId = post_int('itemId');
        $borrowerId = post_int('borrowerId');
        $loanDate = post_date('loanDate');
        $dueDate = post_date('dueDate');
        $override = isset($_POST['override']);

        if ($itemType === null || !isset(ITEM_TABLES[$itemType]) || $itemId === null || $borrowerId === null || !$loanDate) {
            flash('danger', t('Fill_All_Fields'));
            redirect('loans.php');
        }

        // An empty due date means the standard loan period for this item type.
        $dueDate = $dueDate ?? $loanDate->modify('+' . loan_days($rules, $itemType) . ' days');

        if ($loanDate > $today) {
            flash('danger', t('Loan_Date_In_Future'));
            redirect('loans.php');
        }

        if ($dueDate < $loanDate) {
            flash('danger', t('Due_Date_Before_Loan'));
            redirect('loans.php');
        }

        if (!$override && ($reason = borrowing_limit_reason($connection, $rules, $borrowerId)) !== null) {
            flash('warning', $reason . ' ' . t('Tick_Override'));
            redirect('loans.php');
        }

        $item = ITEM_TABLES[$itemType];

        $connection->beginTransaction();

        // Lock the item row so two people cannot lend the same item at once.
        $itemStatus = $connection->prepare("SELECT status FROM {$item['table']} WHERE {$item['id']} = ? FOR UPDATE");
        $itemStatus->execute([$itemId]);
        $itemStatus = $itemStatus->fetchColumn();

        $borrowerActive = $connection->prepare('SELECT isActive FROM personnel WHERE personnelId = ?');
        $borrowerActive->execute([$borrowerId]);
        $borrowerActive = $borrowerActive->fetchColumn();

        if ($itemStatus === false || $borrowerActive === false) {
            $connection->rollBack();
            flash('danger', t('Process_Failed'));
        } elseif ((int) $itemStatus !== ITEM_STATUS_ACTIVE) {
            $connection->rollBack();
            flash('danger', sprintf(t('Item_Not_Lendable'), item_status_name((int) $itemStatus)));
        } elseif ((int) $borrowerActive !== 1) {
            $connection->rollBack();
            flash('danger', t('Borrower_Inactive'));
        } elseif (is_item_on_loan($connection, $itemType, $itemId)) {
            $connection->rollBack();
            flash('danger', t('Item_Already_Lent'));
        } else {
            $connection
                ->prepare('INSERT INTO loans (itemType, itemId, loanDate, dueDate, lenderId, borrowerId) VALUES (?, ?, ?, ?, ?, ?)')
                ->execute([$itemType, $itemId, $loanDate->format('Y-m-d'), $dueDate->format('Y-m-d'), $currentUser['personnelId'], $borrowerId]);
            $connection->commit();
            flash('success', sprintf(t('Loan_Saved_Due'), format_date($dueDate->format('Y-m-d'))));
        }
    } elseif ($action === 'return' || $action === 'lost' || $action === 'extend') {
        $loan = $connection->prepare('SELECT loanId, itemType, itemId, loanDate FROM loans WHERE loanId = ? AND returnDate IS NULL');
        $loan->execute([post_int('loanId')]);
        $loan = $loan->fetch();

        if (!$loan) {
            flash('warning', t('Already_Returned'));
            redirect('loans.php');
        }

        $loanDate = new DateTimeImmutable($loan['loanDate']);
        $item = ITEM_TABLES[(int) $loan['itemType']] ?? null;
        $note = post_string('note', 255);

        if ($action === 'extend') {
            $dueDate = post_date('dueDate');

            if (!$dueDate || $dueDate < $loanDate) {
                flash('danger', t('Due_Date_Before_Loan'));
            } else {
                $connection
                    ->prepare('UPDATE loans SET dueDate = ? WHERE loanId = ? AND returnDate IS NULL')
                    ->execute([$dueDate->format('Y-m-d'), $loan['loanId']]);
                flash('success', sprintf(t('Loan_Saved_Due'), format_date($dueDate->format('Y-m-d'))));
            }
            redirect('loans.php');
        }

        if ($action === 'return') {
            $returnDate = post_date('returnDate');
            $condition = post_int('condition');

            if (!$returnDate || $returnDate < $loanDate || $returnDate > $today) {
                flash('danger', t('Return_Date_Invalid'));
                redirect('loans.php');
            }

            if (!in_array($condition, [RETURN_GOOD, RETURN_DAMAGED, RETURN_INCOMPLETE], true)) {
                flash('danger', t('Fill_All_Fields'));
                redirect('loans.php');
            }

            $cost = null;
            $newItemStatus = $condition === RETURN_DAMAGED ? ITEM_STATUS_IN_REPAIR : null;
        } else {
            $returnDate = $today;
            $condition = RETURN_LOST;
            $costText = str_replace(',', '.', post_string('replacementCost', 12));

            if ($costText !== '' && (!is_numeric($costText) || (float) $costText < 0)) {
                flash('danger', t('Cost_Invalid'));
                redirect('loans.php');
            }

            $cost = $costText === '' ? null : round((float) $costText, 2);
            $newItemStatus = ITEM_STATUS_LOST;
        }

        $connection->beginTransaction();
        $statement = $connection->prepare(
            'UPDATE loans SET returnDate = ?, receivedById = ?, returnCondition = ?, returnNote = ?, replacementCost = ?
              WHERE loanId = ? AND returnDate IS NULL'
        );
        $statement->execute([$returnDate->format('Y-m-d'), $currentUser['personnelId'], $condition, $note, $cost, $loan['loanId']]);

        if ($statement->rowCount() === 0) {
            $connection->rollBack();
            flash('warning', t('Already_Returned'));
            redirect('loans.php');
        }

        if ($newItemStatus !== null && $item !== null) {
            $connection
                ->prepare("UPDATE {$item['table']} SET status = ? WHERE {$item['id']} = ?")
                ->execute([$newItemStatus, $loan['itemId']]);
        }
        $connection->commit();

        if ($action === 'lost') {
            flash('success', t('Loan_Closed_Lost'));
        } elseif ($newItemStatus === ITEM_STATUS_IN_REPAIR) {
            flash('success', t('Returned_Damaged'));
        } else {
            flash('success', t('Process_Successful'));
        }
    }

    redirect('loans.php');
}

$availableItems = [
    ITEM_BOOK => $connection->query(
        'SELECT bookId AS id, CONCAT(bookName, \' | \', bookAuthor) AS label FROM books
          WHERE status = 0 AND bookId NOT IN (SELECT itemId FROM loans WHERE itemType = 0 AND returnDate IS NULL)
          ORDER BY bookName'
    )->fetchAll(),
    ITEM_COMPUTER => $connection->query(
        'SELECT computerId AS id, CONCAT(manufacturer, \' \', model, \' | \', serialNumber) AS label FROM computers
          WHERE status = 0 AND computerId NOT IN (SELECT itemId FROM loans WHERE itemType = 1 AND returnDate IS NULL)
          ORDER BY manufacturer, model'
    )->fetchAll(),
    ITEM_KEY => $connection->query(
        'SELECT keyId AS id, CONCAT(keyNumber, \' | \', keyDescription) AS label FROM roomkeys
          WHERE status = 0 AND keyId NOT IN (SELECT itemId FROM loans WHERE itemType = 2 AND returnDate IS NULL)
          ORDER BY keyNumber'
    )->fetchAll(),
];

$personnel = $connection->query('SELECT personnelId, fullName FROM personnel WHERE isActive = 1 ORDER BY fullName')->fetchAll();

// Which loans to list: all, only open ones, or only overdue ones.
$filters = [
    'all'     => ['label' => t('Filter_All'),     'where' => ''],
    'open'    => ['label' => t('Filter_Open'),    'where' => 'WHERE l.returnDate IS NULL'],
    'overdue' => ['label' => t('Filter_Overdue'), 'where' => 'WHERE l.returnDate IS NULL AND l.dueDate < :today'],
];
$filter = is_string($_GET['show'] ?? null) && isset($filters[$_GET['show']]) ? $_GET['show'] : 'all';

$loans = $connection->prepare(
    'SELECT l.loanId, l.itemType, l.loanDate, l.dueDate, l.returnDate, l.returnCondition, l.returnNote, l.replacementCost,
            CASE l.itemType
                WHEN 0 THEN b.bookName
                WHEN 1 THEN CONCAT(c.manufacturer, \' \', c.model)
                WHEN 2 THEN k.keyNumber
            END AS itemName,
            lender.fullName AS lenderName,
            borrower.fullName AS borrowerName,
            receiver.fullName AS receiverName
       FROM loans l
       LEFT JOIN books b ON l.itemType = 0 AND b.bookId = l.itemId
       LEFT JOIN computers c ON l.itemType = 1 AND c.computerId = l.itemId
       LEFT JOIN roomkeys k ON l.itemType = 2 AND k.keyId = l.itemId
       LEFT JOIN personnel lender ON lender.personnelId = l.lenderId
       LEFT JOIN personnel borrower ON borrower.personnelId = l.borrowerId
       LEFT JOIN personnel receiver ON receiver.personnelId = l.receivedById
      ' . $filters[$filter]['where'] . '
      ORDER BY (l.returnDate IS NULL) DESC, l.loanDate DESC, l.loanId DESC'
);
$loans->execute($filter === 'overdue' ? ['today' => $today->format('Y-m-d')] : []);
$loans = $loans->fetchAll();

$itemTabs = [
    ITEM_BOOK     => ['id' => 'loanBook',     'placeholder' => t('Select_Book')],
    ITEM_COMPUTER => ['id' => 'loanComputer', 'placeholder' => t('Select_Computer')],
    ITEM_KEY      => ['id' => 'loanKey',      'placeholder' => t('Select_Key')],
];

$activePage = 'loans';
$pageTitle = t('Loans');
require __DIR__ . '/includes/header.php';
?>
<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= e(t('New_Loan')) ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <?php foreach ($itemTabs as $type => $tab): ?>
                    <li class="<?= $type === ITEM_BOOK ? 'active' : '' ?>">
                        <a href="#<?= e($tab['id']) ?>" data-toggle="tab"><?= e(item_type_name($type)) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="tab-content">
                <?php foreach ($itemTabs as $type => $tab): ?>
                    <div class="tab-pane <?= $type === ITEM_BOOK ? 'active' : '' ?>" id="<?= e($tab['id']) ?>">
                        <form method="post" action="loans.php">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="itemType" value="<?= e($type) ?>">
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label><?= e(item_type_name($type)) ?></label>
                                    <select name="itemId" class="form-control" required>
                                        <option value=""><?= e($tab['placeholder']) ?></option>
                                        <?php foreach ($availableItems[$type] as $item): ?>
                                            <option value="<?= e($item['id']) ?>"><?= e($item['label']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label><?= e(t('Borrower')) ?></label>
                                    <select name="borrowerId" class="form-control" required>
                                        <option value=""><?= e(t('Select_Personnel')) ?></option>
                                        <?php foreach ($personnel as $person): ?>
                                            <option value="<?= e($person['personnelId']) ?>"><?= e($person['fullName']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label><?= e(t('Lender')) ?></label>
                                    <input type="text" class="form-control" readonly value="<?= e($currentUser['fullName']) ?>">
                                </div>
                                <div class="col-md-2 form-group">
                                    <label><?= e(t('Loan_Date')) ?></label>
                                    <input type="date" name="loanDate" class="form-control" required max="<?= e($today->format('Y-m-d')) ?>" value="<?= e($today->format('Y-m-d')) ?>">
                                </div>
                                <div class="col-md-2 form-group">
                                    <label><?= e(t('Due_Date')) ?></label>
                                    <input type="date" name="dueDate" class="form-control">
                                    <p class="help-block"><?= e(sprintf(t('Due_Date_Help'), loan_days($rules, $type))) ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="override" value="1"> <?= e(t('Override_Limits')) ?></label>
                                    </div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <button type="submit" class="btn btn-warning btn-block"><?= e(t('Save')) ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="box">
    <div class="box-header">
        <div class="btn-group">
            <?php foreach ($filters as $key => $option): ?>
                <a href="loans.php?show=<?= e($key) ?>" class="btn btn-sm <?= $key === $filter ? 'btn-primary' : 'btn-default' ?>"><?= e($option['label']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped data-table">
            <thead>
            <tr>
                <th><?= e(t('Type')) ?></th>
                <th><?= e(t('Item')) ?></th>
                <th><?= e(t('Borrower')) ?></th>
                <th><?= e(t('Loan_Date')) ?></th>
                <th><?= e(t('Lender')) ?></th>
                <th><?= e(t('Due_Date')) ?></th>
                <th><?= e(t('Return_Date')) ?></th>
                <th><?= e(t('Received_By')) ?></th>
                <th><?= e(t('Operations')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($loans as $loan): ?>
                <?php
                $isReturned = $loan['returnDate'] !== null;
                $isOverdue = is_overdue($loan['dueDate'], $loan['returnDate']);
                $loanId = (int) $loan['loanId'];
                ?>
                <tr class="<?= $isOverdue ? 'danger' : '' ?>">
                    <td><?= e(item_type_name((int) $loan['itemType'])) ?></td>
                    <td><?= e($loan['itemName'] ?? t('Deleted_Item')) ?></td>
                    <td><?= e($loan['borrowerName'] ?? t('Deleted_Personnel')) ?></td>
                    <td data-order="<?= e($loan['loanDate']) ?>"><?= e(format_date($loan['loanDate'])) ?></td>
                    <td><?= e($loan['lenderName'] ?? t('Deleted_Personnel')) ?></td>
                    <td data-order="<?= e($loan['dueDate'] ?? '') ?>">
                        <?= e(format_date($loan['dueDate'])) ?>
                        <?php if ($isOverdue): ?>
                            <br><span class="label label-danger"><?= e(sprintf(t('Overdue_Days'), days_overdue($loan['dueDate']))) ?></span>
                        <?php endif; ?>
                    </td>
                    <?php if ($isReturned): ?>
                        <?php $condition = (int) $loan['returnCondition']; ?>
                        <td data-order="<?= e($loan['returnDate']) ?>">
                            <?= e(format_date($loan['returnDate'])) ?>
                            <?php if ($condition !== RETURN_GOOD): ?>
                                <br><span class="label <?= $condition === RETURN_LOST ? 'label-danger' : 'label-warning' ?>"><?= e(return_condition_name($condition)) ?></span>
                            <?php endif; ?>
                            <?php if ($loan['replacementCost'] !== null): ?>
                                <br><small><?= e(t('Replacement_Cost')) ?>: <?= e(number_format((float) $loan['replacementCost'], 2)) ?></small>
                            <?php endif; ?>
                            <?php if ($loan['returnNote'] !== ''): ?>
                                <br><small class="text-muted"><?= e($loan['returnNote']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= e($loan['receiverName'] ?? t('Deleted_Personnel')) ?></td>
                        <td></td>
                    <?php else: ?>
                        <td data-order=""><span class="label label-warning"><?= e(t('Not_Returned_Yet')) ?></span></td>
                        <td></td>
                        <td class="text-nowrap">
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#returnLoan<?= e($loanId) ?>" title="<?= e(t('Mark_Returned')) ?>"><i class="fa fa-check"></i></button>
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#extendLoan<?= e($loanId) ?>" title="<?= e(t('Change_Due_Date')) ?>"><i class="fa fa-calendar"></i></button>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#lostLoan<?= e($loanId) ?>" title="<?= e(t('Mark_Lost')) ?>"><i class="fa fa-exclamation-triangle"></i></button>

                            <div class="modal fade" id="returnLoan<?= e($loanId) ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <form method="post" action="loans.php" class="modal-content">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="return">
                                        <input type="hidden" name="loanId" value="<?= e($loanId) ?>">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="<?= e(t('Close')) ?>">&times;</button>
                                            <h4 class="modal-title"><?= e(t('Mark_Returned')) ?>: <?= e($loan['itemName'] ?? t('Deleted_Item')) ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label><?= e(t('Return_Date')) ?></label>
                                                <input type="date" name="returnDate" class="form-control" required
                                                       min="<?= e($loan['loanDate']) ?>" max="<?= e($today->format('Y-m-d')) ?>" value="<?= e($today->format('Y-m-d')) ?>">
                                            </div>
                                            <div class="form-group">
                                                <label><?= e(t('Condition')) ?></label>
                                                <select name="condition" class="form-control">
                                                    <?php foreach ([RETURN_GOOD, RETURN_DAMAGED, RETURN_INCOMPLETE] as $option): ?>
                                                        <option value="<?= e($option) ?>"><?= e(return_condition_name($option)) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <p class="help-block"><?= e(t('Damaged_Help')) ?></p>
                                            </div>
                                            <div class="form-group">
                                                <label><?= e(t('Note')) ?></label>
                                                <input type="text" name="note" class="form-control" maxlength="255" placeholder="<?= e(t('Return_Note_Placeholder')) ?>">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?= e(t('Cancel')) ?></button>
                                            <button type="submit" class="btn btn-success"><?= e(t('Save')) ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="modal fade" id="extendLoan<?= e($loanId) ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <form method="post" action="loans.php" class="modal-content">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="extend">
                                        <input type="hidden" name="loanId" value="<?= e($loanId) ?>">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="<?= e(t('Close')) ?>">&times;</button>
                                            <h4 class="modal-title"><?= e(t('Change_Due_Date')) ?>: <?= e($loan['itemName'] ?? t('Deleted_Item')) ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label><?= e(t('Due_Date')) ?></label>
                                                <input type="date" name="dueDate" class="form-control" required
                                                       min="<?= e($loan['loanDate']) ?>" value="<?= e($loan['dueDate'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?= e(t('Cancel')) ?></button>
                                            <button type="submit" class="btn btn-info"><?= e(t('Save')) ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="modal fade" id="lostLoan<?= e($loanId) ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <form method="post" action="loans.php" class="modal-content" data-confirm="<?= e(t('Confirm_Lost')) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="lost">
                                        <input type="hidden" name="loanId" value="<?= e($loanId) ?>">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="<?= e(t('Close')) ?>">&times;</button>
                                            <h4 class="modal-title"><?= e(t('Mark_Lost')) ?>: <?= e($loan['itemName'] ?? t('Deleted_Item')) ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <p><?= e(t('Lost_Explanation')) ?></p>
                                            <div class="form-group">
                                                <label><?= e(t('Replacement_Cost')) ?></label>
                                                <input type="text" name="replacementCost" class="form-control" inputmode="decimal" maxlength="12" placeholder="0.00">
                                            </div>
                                            <div class="form-group">
                                                <label><?= e(t('Note')) ?></label>
                                                <input type="text" name="note" class="form-control" maxlength="255">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?= e(t('Cancel')) ?></button>
                                            <button type="submit" class="btn btn-danger"><?= e(t('Mark_Lost')) ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
