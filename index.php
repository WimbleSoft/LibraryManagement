<?php
require __DIR__ . '/includes/bootstrap.php';

$currentUser = require_admin($connection);

// Retired and lost items, and staff who have left, are not counted.
$count = static function (string $table, string $where) use ($connection): int {
    return (int) $connection->query("SELECT COUNT(*) FROM {$table} WHERE {$where}")->fetchColumn();
};
$inCollection = 'status IN (' . ITEM_STATUS_ACTIVE . ', ' . ITEM_STATUS_IN_REPAIR . ')';

$tiles = [
    ['count' => $count('books', $inCollection),     'label' => t('Total_Books'),     'color' => 'bg-green',  'icon' => 'ion-ios-book',   'href' => 'books.php'],
    ['count' => $count('roomkeys', $inCollection),  'label' => t('Total_Keys'),      'color' => 'bg-red',    'icon' => 'ion-key',        'href' => 'keys.php'],
    ['count' => $count('computers', $inCollection), 'label' => t('Total_Computers'), 'color' => 'bg-blue',   'icon' => 'ion-laptop',     'href' => 'computers.php'],
    ['count' => $count('personnel', 'isActive = 1'), 'label' => t('Total_Personnel'), 'color' => 'bg-yellow', 'icon' => 'ion-person-add', 'href' => 'personnel.php'],
];

$openLoanCount = (int) $connection->query('SELECT COUNT(*) FROM loans WHERE returnDate IS NULL')->fetchColumn();

$overdue = $connection->prepare(
    'SELECT l.itemType, l.loanDate, l.dueDate,
            CASE l.itemType
                WHEN 0 THEN b.bookName
                WHEN 1 THEN CONCAT(c.manufacturer, \' \', c.model)
                WHEN 2 THEN k.keyNumber
            END AS itemName,
            borrower.fullName AS borrowerName, borrower.email, borrower.phone
       FROM loans l
       LEFT JOIN books b ON l.itemType = 0 AND b.bookId = l.itemId
       LEFT JOIN computers c ON l.itemType = 1 AND c.computerId = l.itemId
       LEFT JOIN roomkeys k ON l.itemType = 2 AND k.keyId = l.itemId
       LEFT JOIN personnel borrower ON borrower.personnelId = l.borrowerId
      WHERE l.returnDate IS NULL AND l.dueDate < ?
      ORDER BY l.dueDate'
);
$overdue->execute([today()->format('Y-m-d')]);
$overdue = $overdue->fetchAll();

$activePage = 'index';
$pageTitle = t('Control_Panel');
require __DIR__ . '/includes/header.php';
?>
<div class="row">
    <?php foreach ($tiles as $tile): ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box <?= e($tile['color']) ?>">
                <div class="inner">
                    <h3><?= e($tile['count']) ?></h3>
                    <p><?= e($tile['label']) ?></p>
                </div>
                <div class="icon"><i class="ion <?= e($tile['icon']) ?>"></i></div>
                <a href="<?= e($tile['href']) ?>" class="small-box-footer"><?= e(t('More_Info')) ?> <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="callout callout-info">
            <p><i class="fa fa-calendar"></i> <?= e(t('Open_Loans')) ?>: <b><?= e($openLoanCount) ?></b>
                &mdash; <a href="loans.php?show=open"><?= e(t('Loans')) ?></a></p>
        </div>
    </div>
</div>

<div class="box <?= $overdue ? 'box-danger' : 'box-success' ?>">
    <div class="box-header with-border">
        <h3 class="box-title"><?= e(t('Overdue_Loans')) ?>: <?= e(count($overdue)) ?></h3>
        <?php if ($overdue): ?>
            <div class="box-tools pull-right">
                <a href="loans.php?show=overdue" class="btn btn-box-tool"><?= e(t('More_Info')) ?> <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        <?php endif; ?>
    </div>
    <div class="box-body table-responsive">
        <?php if (!$overdue): ?>
            <p class="text-muted"><?= e(t('No_Overdue_Loans')) ?></p>
        <?php else: ?>
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th><?= e(t('Item')) ?></th>
                    <th><?= e(t('Borrower')) ?></th>
                    <th><?= e(t('Contact')) ?></th>
                    <th><?= e(t('Due_Date')) ?></th>
                    <th><?= e(t('Days_Late')) ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($overdue as $loan): ?>
                    <tr>
                        <td><?= e(item_type_name((int) $loan['itemType'])) ?>: <?= e($loan['itemName'] ?? t('Deleted_Item')) ?></td>
                        <td><?= e($loan['borrowerName'] ?? t('Deleted_Personnel')) ?></td>
                        <td><?= e(trim(($loan['email'] ?? '') . ' ' . ($loan['phone'] ?? ''))) ?></td>
                        <td><?= e(format_date($loan['dueDate'])) ?></td>
                        <td><span class="label label-danger"><?= e(days_overdue($loan['dueDate'])) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
