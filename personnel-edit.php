<?php
session_start(); 
if((!isset($_SESSION["login"]))||($_SESSION["auth"]=="0")){
?>
<meta http-equiv="refresh" content="0;URL=login.php">
<?php
} else
{
?>


<?php include("header.php") ?>
<?php include("database.php") ?>



<?php $personelid=$_GET["id"];?>

  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        <?=$lang["Settings"];?>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="#"><i class="fa fa-dashboard"></i> <?=$lang["Homepage"];?></a></li>
        <li><a href="#"><?=$lang["Personnel"];?></a></li>
        <li><?=$lang["Edit"];?></li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?=$lang["Personnel_Edit_Panel"];?></h3>
            </div>
            
            <div class="box-body">
			<?php
				$pullPersonnel=$connection->query("select * from personnel where personnelId='$personelid'")->fetchAll(PDO::FETCH_ASSOC);
				foreach ($pullPersonnel as $pulledPersonnel)
				{
				?>
				<form role="form" method="post" enctype="multipart/form-data" action="personnel-update.php">
				  <div class="box-body">
					<div class="form-group">
						<div class="col-md-2">
							<input name="nameSurname" type="text" id="nameSurname" class="form-control"  placeholder="<?=$lang["Enter_Name_Surname"];?>" value="<?=$pulledPersonnel['nameSurname'];?>">
							<input name="personnelId" type="text" id="personnelId" hidden="hidden" value="<?=$pulledPersonnel['personnelId'];?>">
						</div>
						<div class="col-md-2">
							<input name="<?=$lang["phone"];?>" type="text" id="<?=$lang["phone"];?>" class="form-control"  placeholder="<?=$lang["Enter_Phone"];?>" value="<?=$pulledPersonnel['phone'];?>">
						</div>
						<div class="col-md-2">
							<input name="eMail" type="text" id="eMail" class="form-control" placeholder="<?=$lang["Enter_eMail"];?>" value="<?=$pulledPersonnel['eMail'];?>">
						</div>
						<div class="col-md-1">
							<input name="userName" type="text" id="userName" class="form-control" placeholder="<?=$lang["User_Name"];?> Girin" value="<?=$pulledPersonnel['userName'];?>">
						</div>
						<div class="col-md-2">
							<input name="passWord" type="password" id="passWord" class="form-control" min-lenght="4" placeholder="<?=$lang["If_you_dont_enter_password_doesnt_change"];?>">
						</div>
						<div class="col-md-1">
							<div class="checkbox">
							<label>
							  <input id="auth" name="auth" type="checkbox" <?php if($pulledPersonnel['auth']=='1'){echo 'checked="checked"';}?>>
							  <?=$lang["Is_Admin"];?>
							</label>
							</div>
						</div>
					  
						<div class="col-md-1">
							<button type="submit" class="btn btn-primary"><?=$lang["Save"];?></button>
						</div>
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