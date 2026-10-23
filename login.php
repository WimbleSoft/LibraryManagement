<?php
require __DIR__ . '/includes/bootstrap.php';

if (isset($_SESSION['personnelId'])) {
    redirect('index.php');
}

$error = null;
$username = '';

if (is_post()) {
    verify_csrf();

    $username = post_string('username', 50);
    $password = $_POST['password'] ?? '';
    $password = is_string($password) ? $password : '';

    if ($username === '' || $password === '') {
        $error = t('Enter_Username_And_Password');
    } elseif (is_login_locked($connection, $username, $config)) {
        $error = t('Too_Many_Login_Attempts');
    } else {
        $statement = $connection->prepare(
            'SELECT personnelId, password, isAdmin FROM personnel WHERE username = ?'
        );
        $statement->execute([$username]);
        $user = $statement->fetch();

        if (!$user) {
            // Spend the same time as a real check so usernames cannot be probed by timing.
            password_verify($password, '$2y$10$LFUTb9JxdMPwlt2bsflqgecQ/WaOj.U93TVprk3t0V1Cdher8F4E.');
        }

        $needsRehash = false;
        if ($user && verify_password($password, $user['password'], $needsRehash)) {
            if ($needsRehash) {
                $connection
                    ->prepare('UPDATE personnel SET password = ? WHERE personnelId = ?')
                    ->execute([hash_password($password), $user['personnelId']]);
            }

            if ((int) $user['isAdmin'] === 1) {
                clear_failed_logins($connection, $username);
                sign_in($user);
                redirect('index.php');
            }

            $error = t('No_Panel_Access');
        } else {
            record_failed_login($connection, $username);
            $error = t('Invalid_Credentials');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= e($languageCode) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(t('Login_Title')) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b><?= e(t('App_Name')) ?></b>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg"><?= e(t('Login_Prompt')) ?></p>

        <?php if ($error !== null): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="login.php" method="post">
            <?= csrf_field() ?>
            <div class="form-group has-feedback">
                <input name="username" type="text" class="form-control" autocomplete="username" required
                       placeholder="<?= e(t('Enter_Username')) ?>" value="<?= e($username) ?>">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input name="password" type="password" class="form-control" autocomplete="current-password" required
                       placeholder="<?= e(t('Enter_Password')) ?>">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <a href="?lang=en">EN</a> | <a href="?lang=tr">TR</a>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?= e(t('Login')) ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
