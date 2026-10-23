<?php
require __DIR__ . '/includes/bootstrap.php';

$currentUser = require_admin($connection);

$count = static function (string $table) use ($connection): int {
    return (int) $connection->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
};

$tiles = [
    ['count' => $count('books'),     'label' => t('Total_Books'),     'color' => 'bg-green',  'icon' => 'ion-ios-book',   'href' => 'books.php'],
    ['count' => $count('roomkeys'),  'label' => t('Total_Keys'),      'color' => 'bg-red',    'icon' => 'ion-key',        'href' => 'keys.php'],
    ['count' => $count('computers'), 'label' => t('Total_Computers'), 'color' => 'bg-blue',   'icon' => 'ion-laptop',     'href' => 'computers.php'],
    ['count' => $count('personnel'), 'label' => t('Total_Personnel'), 'color' => 'bg-yellow', 'icon' => 'ion-person-add', 'href' => 'personnel.php'],
];

$openLoanCount = (int) $connection->query('SELECT COUNT(*) FROM loans WHERE returnDate IS NULL')->fetchColumn();

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
                &mdash; <a href="loans.php"><?= e(t('Loans')) ?></a></p>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
