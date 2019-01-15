<!DOCTYPE html>
<html>
<?php include("meta.php");?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
<header class="main-header">
    <a href="index2.html" class="logo">
	<span class="logo-mini"><b>F</b>S</span><span class="logo-lg"><b>FATURA</b>SİS</span>
	</a>
    <nav class="navbar navbar-static-top">
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
	<span class="sr-only">Toggle navigation</span>
   </a>
   <div class="navbar-custom-menu">
   <ul class="nav navbar-nav">
   <li class="dropdown user user-menu">
   <a href="#" class="dropdown-toggle" data-toggle="dropdown">
   <i class="fa fa-gear"></i><span class="hidden-xs"><?=$_SESSION["girenkisi"];?></span>
   </a>
   <ul class="dropdown-menu">
	   <li class="user-header"><p><?=$_SESSION["girenkisi"];?><small><?=$_SESSION["gireneposta"];?></small></p></li>
	   <li class="user-footer">
		<div class="pull-left"><a href="ayarlar.php" class="btn btn-default btn-flat"><i class="fa fa-gears"></i></a></div>
		<div class="pull-right"><a href="cikis.php" class="btn btn-default btn-flat"><i class="fa fa-power-off"></i></a></div>
	   </li>
   </ul>
   </li>
   </ul>
   </div>
   </nav>
</header>
<?php include("sol_menu.php");?>


