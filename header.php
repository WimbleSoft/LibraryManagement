<!DOCTYPE html>
<html>
<?php include("meta.php");?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
<header class="main-header">
    <a href="index.php" class="logo">
	<span class="logo-mini"><?=$lang["LS"];?></span><span class="logo-lg"><?=$lang["LIBRARY_RECORDS"];?></span>
	</a>
    <nav class="navbar navbar-static-top">
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
	<span class="sr-only"><?=$lang["Toggle_Nav"];?></span>
   </a>
   <div class="navbar-custom-menu">
   <ul class="nav navbar-nav">
   <li class="dropdown user user-menu">
   <a href="#" class="dropdown-toggle" data-toggle="dropdown">
   <i class="fa fa-gear"></i><span class="hidden-xs"><?=$_SESSION["enteredPerson"];?></span>
   </a>
   <ul class="dropdown-menu">
	   <li class="user-header"><p><?=$_SESSION["enteredPerson"];?><small><?=$_SESSION["enteredPersonEmail"];?></small></p></li>
	   <li class="user-footer">
		<div class="pull-left"><a href="settings.php" class="btn btn-default btn-flat"><i class="fa fa-gears"></i></a></div>
		<div class="pull-left"><a href="?lang=tr"  class="btn btn-default btn-flat">TR</a></div>
		<div class="pull-left"><a href="?lang=en"  class="btn btn-default btn-flat">EN</a></div>
		<div class="pull-right"><a href="logout.php" class="btn btn-default btn-flat"><i class="fa fa-power-off"></i></a></div>
	   </li>
   </ul>
   </li>
   </ul>
   </div>
   </nav>
</header>
<?php include("nav-left.php");?>


