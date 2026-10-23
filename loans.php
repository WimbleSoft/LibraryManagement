<?php
require __DIR__ . '/includes/bootstrap.php';

$currentUser = require_admin($connection);

// Where each item type lives, used to check that a loaned item exists.
$itemTables = [
    ITEM_BOOK     => ['table' => 'books',     'id' => 'bookId'],
    ITEM_COMPUTER => ['table' => 'computers', 'id' => 'computerId'],
    ITEM_KEY      => ['table' => 'roomkeys',  'id' => 'keyId'],
];

if (is_post()) {
    verify_csrf();

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $itemType = post_int('itemType');
        $itemId = post_int('itemId');
        $borrowerId = post_int('borrowerId');
        $loanDate = DateTime::createFromFormat('!Y-m-d', post_string('loanDate', 10));

        if ($itemType === null || !isset($itemTables[$itemType]) || $itemId === null || $borrowerId === null || !$loanDate) {
            flash('danger', t('Fill_All_Fields'));
            redirect('loans.php');
        }

        $item = $itemTables[$itemType];

        $connection->beginTransaction();

        // Lock the item row so two people cannot lend the same item at once.
        $itemExists = $connection->prepare("SELECT 1 FROM {$item['table']} WHERE {$item['id']} = ? FOR UPDATE");
        $itemExists->execute([$itemId]);

        $borrowerExists = $connection->prepare('SELECT 1 FROM personnel WHERE personnelId = ?');
        $borrowerExists->execute([$borrowerId]);

        if (!$itemExists->fetchColumn() || !$borrowerExists->fetchColumn()) {
            $connection->rollBack();
            flash('danger', t('Process_Failed'));
        } elseif (is_item_on_loan($connection, $itemType, $itemId)) {
            $connection->rollBack();
            flash('danger', t('Item_Already_Lent'));
        } else {
            $connection
                ->prepare('INSERT INTO loans (itemType, itemId, loanDate, lenderId, borrowerId) VALUES (?, ?, ?, ?, ?)')
                ->execute([$itemType, $itemId, $loanDate->format('Y-m-d'), $currentUser['personnelId'], $borrowerId]);
            $connection->commit();
            flash('success', t('Process_Successful'));
        }
    } elseif ($action === 'return') {
        $statement = $connection->prepare(
            'UPDATE loans SET returnDate = CURDATE(), receivedById = ? WHERE loanId = ? AND returnDate IS NULL'
        );
        $statement->execute([$currentUser['personnelId'], post_int('loanId')]);

        $statement->rowCount() > 0
            ? flash('success', t('Process_Successful'))
            : flash('warning', t('Already_Returned'));
    } elseif ($action === 'delete') {
        // Only returned loans may be deleted, so an item can never vanish from the records while lent out.
        $statement = $connection->prepare('DELETE FROM loans WHERE loanId = ? AND returnDate IS NOT NULL');
        $statement->execute([post_int('loanId')]);

        $statement->rowCount() > 0
            ? flash('success', t('Record_Deleted'))
            : flash('warning', t('Return_Before_Delete'));
    }

    redirect('loans.php');
}

$availableItems = [
    ITEM_BOOK => $connection->query(
        'SELECT bookId AS id, CONCAT(bookName, \' | \', bookAuthor) AS label FROM books
          WHERE bookId NOT IN (SELECT itemId FROM loans WHERE itemType = 0 AND returnDate IS NULL)
          ORDER BY bookName'
    )->fetchAll(),
    ITEM_COMPUTER => $connection->query(
        'SELECT computerId AS id, CONCAT(manufacturer, \' \', model, \' | \', serialNumber) AS label FROM computers
          WHERE computerId NOT IN (SELECT itemId FROM loans WHERE itemType = 1 AND returnDate IS NULL)
          ORDER BY manufacturer, model'
    )->fetchAll(),
    ITEM_KEY => $connection->query(
        'SELECT keyId AS id, CONCAT(keyNumber, \' | \', keyDescription) AS label FROM roomkeys
          WHERE keyId NOT IN (SELECT itemId FROM loans WHERE itemType = 2 AND returnDate IS NULL)
          ORDER BY keyNumber'
    )->fetchAll(),
];

$personnel = $connection->query('SELECT personnelId, fullName FROM personnel ORDER BY fullName')->fetchAll();

$loans = $connection->query(
    'SELECT l.loanId, l.itemType, l.loanDate, l.returnDate,
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
      ORDER BY (l.returnDate IS NULL) DESC, l.loanDate DESC, l.loanId DESC'
)->fetchAll();

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
                        <form method="post" action="loans.php" class="row">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="itemType" value="<?= e($type) ?>">
                            <div class="col-md-4 form-group">
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
                                <input type="date" name="loanDate" class="form-control" required value="<?= e(date('Y-m-d')) ?>">
                            </div>
                            <div class="col-md-1 form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-warning btn-block"><?= e(t('Save')) ?></button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="box">
    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped data-table">
            <thead>
            <tr>
                <th><?= e(t('Type')) ?></th>
                <th><?= e(t('Item')) ?></th>
                <th><?= e(t('Loan_Date')) ?></th>
                <th><?= e(t('Lender')) ?></th>
                <th><?= e(t('Borrower')) ?></th>
                <th><?= e(t('Return_Date')) ?></th>
                <th><?= e(t('Received_By')) ?></th>
                <th><?= e(t('Operations')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($loans as $loan): ?>
                <?php $isReturned = $loan['returnDate'] !== null; ?>
                <tr>
                    <td><?= e(item_type_name((int) $loan['itemType'])) ?></td>
                    <td><?= e($loan['itemName'] ?? t('Deleted_Item')) ?></td>
                    <td data-order="<?= e($loan['loanDate']) ?>"><?= e(format_date($loan['loanDate'])) ?></td>
                    <td><?= e($loan['lenderName'] ?? t('Deleted_Personnel')) ?></td>
                    <td><?= e($loan['borrowerName'] ?? t('Deleted_Personnel')) ?></td>
                    <?php if ($isReturned): ?>
                        <td data-order="<?= e($loan['returnDate']) ?>"><?= e(format_date($loan['returnDate'])) ?></td>
                        <td><?= e($loan['receiverName'] ?? t('Deleted_Personnel')) ?></td>
                    <?php else: ?>
                        <td data-order=""><span class="label label-warning"><?= e(t('Not_Returned_Yet')) ?></span></td>
                        <td></td>
                    <?php endif; ?>
                    <td class="text-nowrap">
                        <?php if (!$isReturned): ?>
                            <form method="post" action="loans.php" class="inline-form" data-confirm="<?= e(t('Confirm_Return')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="return">
                                <input type="hidden" name="loanId" value="<?= e($loan['loanId']) ?>">
                                <button type="submit" class="btn btn-success btn-sm" title="<?= e(t('Mark_Returned')) ?>"><i class="fa fa-check"></i></button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="loans.php" class="inline-form" data-confirm="<?= e(t('Confirm_Delete_Loan')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="loanId" value="<?= e($loan['loanId']) ?>">
                                <button type="submit" class="btn btn-danger btn-sm" title="<?= e(t('Delete')) ?>"><i class="fa fa-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
