<?php
require __DIR__ . '/includes/bootstrap.php';

$currentUser = require_admin($connection);
$personnelId = (int) $currentUser['personnelId'];

if (is_post()) {
    verify_csrf();

    $fullName = post_string('fullName', 100);
    $username = post_string('username', 50);
    $email = post_string('email', 100);
    $phone = post_string('phone', 30);
    $currentPassword = is_string($_POST['currentPassword'] ?? null) ? $_POST['currentPassword'] : '';
    $newPassword = is_string($_POST['newPassword'] ?? null) ? $_POST['newPassword'] : '';

    $storedHash = $connection->prepare('SELECT password FROM personnel WHERE personnelId = ?');
    $storedHash->execute([$personnelId]);

    if ($fullName === '' || $username === '') {
        flash('danger', t('Fill_All_Fields'));
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('danger', t('Invalid_Email'));
    } elseif (!verify_password($currentPassword, (string) $storedHash->fetchColumn())) {
        // Re-entering the password stops someone at an unattended screen from taking over the account.
        flash('danger', t('Current_Password_Wrong'));
    } elseif ($newPassword !== '' && !is_valid_password($newPassword)) {
        flash('danger', t('Password_Too_Short'));
    } else {
        try {
            $connection
                ->prepare('UPDATE personnel SET fullName = ?, username = ?, email = ?, phone = ? WHERE personnelId = ?')
                ->execute([$fullName, $username, $email, $phone, $personnelId]);

            if ($newPassword !== '') {
                $connection
                    ->prepare('UPDATE personnel SET password = ? WHERE personnelId = ?')
                    ->execute([hash_password($newPassword), $personnelId]);
                session_regenerate_id(true);
            }
            flash('success', t('Process_Successful'));
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            flash('danger', t('Username_Taken'));
        }
    }

    redirect('settings.php');
}

$activePage = 'settings';
$pageTitle = t('Settings');
require __DIR__ . '/includes/header.php';
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?= e(t('Account_Settings')) ?></h3>
    </div>
    <form method="post" action="settings.php">
        <?= csrf_field() ?>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label><?= e(t('Full_Name')) ?></label>
                    <input name="fullName" type="text" class="form-control" maxlength="100" required value="<?= e($currentUser['fullName']) ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label><?= e(t('Username')) ?></label>
                    <input name="username" type="text" class="form-control" maxlength="50" required autocomplete="username" value="<?= e($currentUser['username']) ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label><?= e(t('Email')) ?></label>
                    <input name="email" type="email" class="form-control" maxlength="100" value="<?= e($currentUser['email']) ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label><?= e(t('Phone')) ?></label>
                    <input name="phone" type="tel" class="form-control" maxlength="30" value="<?= e($currentUser['phone']) ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label><?= e(t('New_Password')) ?></label>
                    <input name="newPassword" type="password" class="form-control" minlength="8" autocomplete="new-password" placeholder="<?= e(t('Leave_Blank_To_Keep_Password')) ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label><?= e(t('Current_Password')) ?></label>
                    <input name="currentPassword" type="password" class="form-control" required autocomplete="current-password" placeholder="<?= e(t('Current_Password_Required')) ?>">
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary"><?= e(t('Save')) ?></button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
