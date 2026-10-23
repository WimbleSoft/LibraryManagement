<?php
/**
 * Page header, top bar and side navigation.
 *
 * Expects: $currentUser (array), $activePage (string), $pageTitle (string).
 */

$menuItems = [
    'index'     => ['href' => 'index.php',     'icon' => 'fa-home',     'label' => t('Homepage')],
    'loans'     => ['href' => 'loans.php',     'icon' => 'fa-calendar', 'label' => t('Loans')],
    'books'     => ['href' => 'books.php',     'icon' => 'fa-book',     'label' => t('Books')],
    'computers' => ['href' => 'computers.php', 'icon' => 'fa-laptop',   'label' => t('Computers')],
    'keys'      => ['href' => 'keys.php',      'icon' => 'fa-key',      'label' => t('Keys')],
    'personnel' => ['href' => 'personnel.php', 'icon' => 'fa-user',     'label' => t('Personnel')],
];

$alertIcons = ['success' => 'fa-check', 'danger' => 'fa-ban', 'warning' => 'fa-warning', 'info' => 'fa-info'];
?>
<!DOCTYPE html>
<html lang="<?= e($GLOBALS['languageCode']) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(t('Page_Title')) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <a href="index.php" class="logo">
            <span class="logo-mini"><?= e(t('App_Short_Name')) ?></span>
            <span class="logo-lg"><?= e(t('App_Name_Upper')) ?></span>
        </a>
        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only"><?= e(t('Toggle_Navigation')) ?></span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-gear"></i>
                            <span class="hidden-xs"><?= e($currentUser['fullName']) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <p>
                                    <?= e($currentUser['fullName']) ?>
                                    <small><?= e($currentUser['email']) ?></small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="settings.php" class="btn btn-default btn-flat" title="<?= e(t('Settings')) ?>"><i class="fa fa-gears"></i></a>
                                    <a href="?lang=en" class="btn btn-default btn-flat">EN</a>
                                    <a href="?lang=tr" class="btn btn-default btn-flat">TR</a>
                                </div>
                                <div class="pull-right">
                                    <form method="post" action="logout.php">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-default btn-flat" title="<?= e(t('Sign_Out')) ?>"><i class="fa fa-power-off"></i></button>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <?php foreach ($menuItems as $key => $item): ?>
                    <li class="<?= $key === $activePage ? 'active' : '' ?>">
                        <a href="<?= e($item['href']) ?>"><i class="fa <?= e($item['icon']) ?>"></i> <span><?= e($item['label']) ?></span></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <h1><?= e($pageTitle) ?></h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-dashboard"></i> <?= e(t('Homepage')) ?></a></li>
                <?php if ($activePage !== 'index'): ?>
                    <li class="active"><?= e($pageTitle) ?></li>
                <?php endif; ?>
            </ol>
        </section>

        <section class="content">
            <?php foreach (take_flashes() as $flashMessage): ?>
                <div class="alert alert-<?= e($flashMessage['type']) ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="<?= e(t('Close')) ?>">&times;</button>
                    <i class="icon fa <?= e($alertIcons[$flashMessage['type']] ?? 'fa-info') ?>"></i>
                    <?= e($flashMessage['message']) ?>
                </div>
            <?php endforeach; ?>
