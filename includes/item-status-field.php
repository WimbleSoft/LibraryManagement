<?php
/**
 * Status drop-down for an item's edit form.
 *
 * Expects: $itemStatus (the item's ITEM_STATUS_* value) and $loan (the
 * item's open loan, or null). An item on loan must stay active.
 */
?>
<div class="form-group">
    <label><?= e(t('Status')) ?></label>
    <select name="status" class="form-control" <?= $loan !== null ? 'disabled' : '' ?>>
        <?php foreach ([ITEM_STATUS_ACTIVE, ITEM_STATUS_IN_REPAIR, ITEM_STATUS_RETIRED, ITEM_STATUS_LOST] as $option): ?>
            <option value="<?= e($option) ?>" <?= $option === $itemStatus ? 'selected' : '' ?>><?= e(item_status_name($option)) ?></option>
        <?php endforeach; ?>
    </select>
    <?php if ($loan !== null): ?>
        <input type="hidden" name="status" value="<?= e(ITEM_STATUS_ACTIVE) ?>">
        <p class="help-block"><?= e(t('Status_Locked_While_Lent')) ?></p>
    <?php else: ?>
        <p class="help-block"><?= e(t('Status_Help')) ?></p>
    <?php endif; ?>
</div>
