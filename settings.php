<?php session_start(); if((!isset($_SESSION["login"]))||($_SESSION["auth"]=="0")){?>
<meta http-equiv="refresh" content="0;URL=login.php">
<?php} else{?>
<?php include("header.php") ?>
<?php include("database.php") ?>
<?php $enteredPersonId=$_SESSION["enteredPersonId"];?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        <?=$lang["Settings"];?>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="#"><i class="fa fa-dashboard"></i> <?=$lang["Homepage"];?></a></li>
        <li><a href="#"><?=$lang["Settings"];?></a></li>
      </ol>
    </section>
    <section class="content">
  <div class="row">
	<div class="col-xs-12">
	  <div class="box">
		<div class="box-header">
		  <h3 class="box-title"><?=$lang["Personnel_Settings_Screen"];?></h3>
		</div>
<div class="box-body">
<?php
$pullData=$connection->query("select * from personnel where personnelId='$enteredPersonId'")->fetchAll(PDO::FETCH_ASSOC);
foreach ($pullData as $pulledData)
{
?>
<form role="form" method="post" enctype="multipart/form-data" action="settings-update.php">
<div class="box-body">
<div class="form-group">
<div class="col-md-2">
<input name="nameSurname" type="text" id="nameSurname" class="form-control"  placeholder="<?=$lang["Enter_Name_Surname"];?>" value="<?=$pulledData['nameSurname'];?>">
</div>
<div class="col-md-2">
<input name="<?=$lang["phone"];?>" type="text" id="<?=$lang["phone"];?>" class="form-control"  placeholder="<?=$lang["Enter_Phone"];?>" value="<?=$pulledData['<?=$lang["phone"];?>'];?>">
</div>
<div class="col-md-2">
<input name="eMail" type="text" id="eMail" class="form-control" placeholder="<?=$lang["Enter_eMail"];?>" value="<?=$pulledData['eMail'];?>">
</div>
<div class="col-md-2">
<input name="userName" type="text" id="userName" class="form-control" placeholder="<?=$lang["User_Name"];?> Girin" value="<?=$pulledData['userName'];?>">
</div>
<div class="col-md-2">
<input name="passWord" type="password" id="passWord" class="form-control" placeholder="<?=$lang["Password"];?> Girin" value="<?=$pulledData['passWord'];?>">
</div>
<div class="col-md-1"><button type="submit" class="btn btn-primary"><?=$lang["Save"];?></button></div>
</div>
</div>
</form>
 <?php } ?>
		</div>
	  </div>
	</div>
  </div>
    </section>
  </div>
 <?php include("footer.php") ?>
<?php	} ?>



