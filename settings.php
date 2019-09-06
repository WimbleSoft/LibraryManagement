<?php 
	session_start(); 
	if((!isset($_SESSION["login"]))||($_SESSION["auth"]=="0")){
	?>
<meta http-equiv="refresh" content="0;URL=login.php">
<?php 
} 
else
{ 
  include("header.php");
  $personnelId=$_SESSION["personnelId"];
  if(isset($_POST["update"]) && isset($_POST["bookId"]) && isset($_POST["nameSurname"]) && isset($_POST["eMail"]) && isset($_POST["userName"]) && isset($_POST["phone"])  )
  {

    $bookId=$_POST['bookId'];
    $nameSurname=$_POST['nameSurname'];
    $eMail=$_POST['eMail'];
    $userName=$_POST['userName'];
    $phone=$_POST['phone'];

    $situation="";
    if(isset($_POST['passWord']) && $_POST['passWord']!="") 
    {
      $passWord=md5($_POST['passWord']);
      $situation=$connection->query("UPDATE personnel set nameSurname='$nameSurname',passWord='$passWord',eMail='$eMail',userName='$userName',phone='$phone' where bookId='$bookId'") or die ($lang["Something_bad_happened"]);
    }
    else
    {
      $situation=$connection->query("UPDATE personnel set nameSurname='$nameSurname',eMail='$eMail',userName='$userName',phone='$phone' where bookId='$bookId'") or die ($lang["Something_bad_happened"]);
    }
      
    if($situation)
    {
      $status="<h2>".$lang["Process_Successful"]."</h2>";
    }else
    {
      $status="<h2>".$lang["Process_Failed"]."</h2>";
    }
  }

?>
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
							$personnels=$connection->query("select * from personnel where personnelId='$personnelId'")->fetchAll(PDO::FETCH_ASSOC);
							foreach ($personnels as $personnel)
							{
							?>
                <form role="form" method="post" enctype="multipart/form-data" action="">
                  <div class="box-body">
                    <div class="form-group">
                      <div class="col-md-2">
                        <input name="nameSurname" type="text" id="nameSurname" class="form-control"  placeholder="<?=$lang["Enter_Name_Surname"];?>" value="<?=$personnel['nameSurname'];?>">
                      </div>
                      <div class="col-md-2">
                        <input name="phone" type="text" id="phone" class="form-control"  placeholder="<?=$lang["Enter_Phone"];?>" value="<?=$personnel['phone'];?>">
                      </div>
                      <div class="col-md-2">
                        <input name="eMail" type="text" id="eMail" class="form-control" placeholder="<?=$lang["Enter_eMail"];?>" value="<?=$personnel['eMail'];?>">
                      </div>
                      <div class="col-md-2">
                        <input name="userName" type="text" id="userName" class="form-control" placeholder="<?=$lang["Enter_Username"];?>" value="<?=$personnel['userName'];?>">
                      </div>
                      <div class="col-md-2">
                        <input name="passWord" type="password" id="passWord" class="form-control" placeholder="<?=$lang["Enter_Password"];?>" value="<?=$personnel['passWord'];?>">
                      </div>
                      <div class="col-md-1"><button type="submit" name="update" class="btn btn-primary"><?=$lang["Save"];?></button></div>
                    </div>
                  </div>
                </form>
            <?php 
              }
            ?>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php 
  include("footer.php");
  include("scripts.php");
}
?>
