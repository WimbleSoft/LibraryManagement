<?php
/**
 * Availability badge for one item in a list.
 *
 * Expects: $loan (array of the item's open loan, or null).
 */
?>
<?php if ($loan === null): ?>
    <span class="label label-success"><?= e(t('In_Inventory')) ?></span>
<?php else: ?>
    <span class="label label-warning"><?= e(t('Lent')) ?></span>
    <small class="text-muted">
        <?= e(sprintf(
            t('Lent_Description'),
            $loan['borrowerName'] ?? t('Deleted_Personnel'),
            format_date($loan['loanDate']),
            $loan['lenderName'] ?? t('Deleted_Personnel')
        )) ?>
    </small>
<?php endif; ?>
