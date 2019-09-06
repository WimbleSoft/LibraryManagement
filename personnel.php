<?php
	session_start(); 
	if((!isset($_SESSION["login"]))||($_SESSION["auth"]=="0")){
	?>
<meta http-equiv="refresh" content="0;URL=login.php">
<?php
	} else
	{
	?>

<?php 
  include("header.php");
  if(isset($_POST["add"]) && isset($_POST['nameSurname']) && isset($_POST['passWord']) && isset($_POST['eMail']) && isset($_POST['phone']) && isset($_POST['userName']))
  {
    $nameSurname=$_POST['nameSurname'];
    $passWord=$_POST['passWord'];
    $eMail=$_POST['eMail'];
    $phone=$_POST['phone'];
    $userName=$_POST['userName'];
    if(isset($_POST['auth']) && $_POST['auth'] == "on")
    { 
        $auth=1;
    } 
    else
    { 
        $auth=0;
    }
    
    $situation=$connection->query("insert into personnel  (userName,passWord,eMail,phone,nameSurname,auth) values ('$userName','$passWord','$eMail','$phone','$nameSurname','$auth')") or die ($lang["Something_bad_happened"]);
  
  
    if($situation)
    {
      $status="<h2>".$lang["Process_Successful"]."</h2>";
    }
    else
    {
      $status="<h2>".$lang["Process_Failed"]."</h2>";
    }
  }
  if(isset($_POST["update"]) && isset($_POST['personnelId']) && isset($_POST['nameSurname']) && isset($_POST['passWord']) && isset($_POST['eMail']) && isset($_POST['phone']) && isset($_POST['userName']))
  {
    $personnelId=$_POST['personnelId'];
    $nameSurname=$_POST['nameSurname'];
    $passWord=md5($_POST['passWord']);
    $eMail=$_POST['eMail'];
    $userName=$_POST['userName'];
    $phone=$_POST['phone'];
    if(isset($_POST['auth'])) 
    { 
        $auth=1;
    } 
    else 
    { 
        $auth=0;
    }
    if($_POST['passWord']=='')
    {
      $situation=$connection->query("UPDATE personnel set nameSurname='$nameSurname',eMail='$eMail',userName='$userName',auth='$auth',phone='$phone' where personnelId='$personnelId'") or die ($lang["Something_bad_happened"]);
    }
    else
    {
      $situation2=$connection->query("UPDATE personnel set nameSurname='$nameSurname',passWord='$passWord',eMail='$eMail',userName='$userName',auth='$auth',phone='$phone' where personnelId='$personnelId'") or die ($lang["Something_bad_happened"]);
    }
  }

?>
<script>
	document.getElementById("personnel").className = "active";
</script>
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			<?=$lang["Personnel"];?>
		</h1>
		<ol class="breadcrumb">
			<li class="active"><a href="#"><i class="fa fa-dashboard"></i> <?=$lang["Homepage"];?></a></li>
			<li><a href="#"><?=$lang["Personnel"];?></a></li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<div class="col-md-12">
							<div class="box box-default collapsed-box">
								<div class="box-header with-border">
									<h3 class="box-title"><?=$lang["Add_New_Personnel"];?></h3>
									<div class="box-tools">
										<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
										</button>
									</div>
								</div>
								<div class="box-body">
									<form role="form" method="post" enctype="multipart/form-data" action="">
										<div class="box-body">
											<div class="form-group">
												<div class="col-md-2">
													<input name="nameSurname" type="text" id="nameSurname" class="form-control"  placeholder="<?=$lang["Enter_Name_Surname"];?>">
												</div>
												<div class="col-md-2">
													<input name="phone" type="text" id="phone" class="form-control"  placeholder="<?=$lang["Enter_Phone"];?>">
												</div>
												<div class="col-md-2">
													<input name="eMail" type="text" id="eMail" class="form-control" placeholder="<?=$lang["Enter_eMail"];?>">
												</div>
												<div class="col-md-1">
													<input name="userName" type="text" id="userName" class="form-control" placeholder="<?=$lang["Enter_Username"];?>">
												</div>
												<div class="col-md-2">
													<input name="passWord" type="password" id="passWord" class="form-control" placeholder="<?=$lang["Enter_Password"];?>">
												</div>
												<div class="col-md-2">
													<div class="checkbox">
														<label>
														<input id="auth" name="auth" type="checkbox">
														<?=$lang["Is_Admin"];?>
														</label>
													</div>
												</div>
												<div class="col-md-1">
													<button type="submit" name="action" value="add" class="btn btn-primary"><?=$lang["Add_the_Personnel"];?></button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<div class="box-body">
						<table id="example1" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th><?=$lang["Name_Surname"];?></th>
									<th><?=$lang["Username"];?></th>
									<th><?=$lang["eMail"];?></th>
									<th><?=$lang["Phone"];?></th>
									<th><?=$lang["Operations"];?></th>
								</tr>
							</thead>
							<tbody>
							<?php
								$pullData=$connection->query("select * from personnel")->fetchAll(PDO::FETCH_ASSOC);
								foreach ($pullData as $pulledData)
								{
								?>
								<tr>
									<td><?=$pulledData['nameSurname'];?></td>
									<td><?=$pulledData['userName'];?></td>
									<td><?=$pulledData['eMail'];?></td>
									<td><?=$pulledData['phone'];?></td>
									<td>
										<span class="button-group">
										<a href="#" data-target="personnelEdit_<?=$pulledData['personnelId'];?>" date-toggle="modal" class="fa fa-pencil"></a>
										<a href="personnel.php?action=delete&personnelId=<?=$pulledData['personnelId'];?>" onclick="return confirm('<?=$lang['Are_you_sure_to_delete'];?>');" class="fa fa-trash"></a>
										</span>
									</td>
								</tr>
							<?php 
							}
							?>
							</tbody>
						</table>
					</div>
					
				</div>
				
			</div>
			
		</div>
		
	</section>
	
</div>


<?php 
	include("footer.php"); 
	include("scripts.php"); 
	?>

<?php	} ?>
