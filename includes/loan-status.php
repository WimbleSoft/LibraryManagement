<?php
/**
 * Availability badge for one item in a list.
 *
 * Expects: $loan (array of the item's open loan, or null) and
 * $itemStatus (the item's ITEM_STATUS_* value).
 */
$statusLabels = [
    ITEM_STATUS_IN_REPAIR => 'label-info',
    ITEM_STATUS_RETIRED   => 'label-default',
    ITEM_STATUS_LOST      => 'label-danger',
];
?>
<?php if ($loan !== null): ?>
    <?php if (is_overdue($loan['dueDate'], null)): ?>
        <span class="label label-danger"><?= e(t('Overdue')) ?></span>
    <?php else: ?>
        <span class="label label-warning"><?= e(t('Lent')) ?></span>
    <?php endif; ?>
    <small class="text-muted">
        <?= e(sprintf(
            t('Lent_Description'),
            $loan['borrowerName'] ?? t('Deleted_Personnel'),
            format_date($loan['loanDate']),
            $loan['lenderName'] ?? t('Deleted_Personnel')
        )) ?>
        <?php if ($loan['dueDate'] !== null): ?>
            <?= e(sprintf(t('Due_On'), format_date($loan['dueDate']))) ?>
        <?php endif; ?>
    </small>
<?php elseif (isset($statusLabels[$itemStatus])): ?>
    <span class="label <?= e($statusLabels[$itemStatus]) ?>"><?= e(item_status_name($itemStatus)) ?></span>
<?php else: ?>
    <span class="label label-success"><?= e(t('In_Inventory')) ?></span>
<?php endif; ?>
