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
<script>
document.getElementById("keys").className = "active";
</script>

  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        <?=$lang["Keys"];?>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="#"><i class="fa fa-dashboard"></i> <?=$lang["Homepage"];?></a></li>
        <li><a href="#"><?=$lang["Keys"];?></a></li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <div class="col-md-6">
				  <div class="box box-default collapsed-box">
					<div class="box-header with-border">
					  <h3 class="box-title"><?=$lang["Add_a_Key"];?></h3>

					  <div class="box-tools">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
						</button>
					  </div>

					</div>

					
					<div class="box-body">
					

					
					<form role="form" method="post" enctype="multipart/form-data" action="keys-add.php">
					  <div class="box-body">
						<div class="form-group">
							<div class="col-md-5">
								<input name="keyNumber" type="text"class="form-control" id="keyNumber" placeholder="<?=$lang["Enter_Key_Number"];?>">
							</div>
							<div class="col-md-5">
								<input name="keyContent" type="text" class="form-control" id="keyContent" placeholder="<?=$lang["Enter_Key_Content"];?>">
							</div>
							<div class="col-md-2">
								<button type="submit" class="btn btn-primary"><?=$lang["Add_the_Key"];?></button>
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
									<th><?=$lang["Number"];?></th>
									<th><?=$lang["Content"];?></th>
									<th><?=$lang["Status"];?></th>
									<th><?=$lang["Operations"];?></th>
									</tr>
								</thead>

								<tbody>
									<?php
									$pullKey=$connection->prepare('select * from roomkeys');
									$pullKey->execute();
									foreach ($pullKey->fetchAll(PDO::FETCH_ASSOC) as $pulledKey)
									{
									?>
									<tr>
									<td><?=$pulledKey['keyNumber'];?></td>
									<td><?=$pulledKey['keyContent'];?></td>
									<td>
									<div class="box box-default collapsed-box" style="border:1px solid #00a65a;">
										<div class="box-header with-border">
											<h4 class="box-title"><?php if($pulledKey['keyStatus']=='0')echo $lang["In_Inventory"]; else echo $lang["Lent"];?></h4>
											<div class="box-tools">
											<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
											</button>
											</div> 
										</div>
										<div class="box-body">
											<?php
											if($pulledKey['keyStatus']=='1')
											{
												$id=$pulledKey['keyId'];
												$pullLoan=$connection->query("select * from loans where type='3' AND productId='$keyId' AND returnAccepterId=''")->fetchAll(PDO::FETCH_ASSOC);
												foreach ($pullLoan as $pulledLoan)
												{
													echo 'Bu '.$pulledLoan['type'].', '.$pulledLoan['loanDate'].' tarihinde ';
													
													$pullPersonnel=$connection->query("select * from personnel where personnelId=".$pulledLoan['loanerId']."")->fetchAll(PDO::FETCH_ASSOC);
													foreach ($pullPersonnel as $pulledPersonnel)
													{
													echo $pulledPersonnel['nameSurname'];
													}
													echo ' tarafından ';
													$pullPersonnel2=$connection->query("select * from personnel where personnelId=".$pulledLoan['loanerId']."")->fetchAll(PDO::FETCH_ASSOC);
													foreach ($pullPersonnel2 as $pulledPersonnel2)
													{
													echo $pulledPersonnel2['nameSurname'];
													}
													echo ' adlı personele emanet edilmiştir.';
													if ($pulledLoan['returnDate']!="0000-00-00") 
													{
													//echo $pulledLoan['returnDate'].' tarihinde, '.$pulledLoan['returnAccepterId'].' tarafından iade alınmıştır.';
													}
													else echo 'Bu anahtar henüz iade alınmamıştır.';
												}
											}
											?>
										</div>
									</div>
									</td>
									
									<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
										<div class="modal-dialog" role="document">
										<div class="modal-content">
											<form role="form" method="post" enctype="multipart/form-data" action="keys-edit.php">
												<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title" id="myModalLabel"><?=$lang["Key"];?> <?=$lang["Edit"];?></h4>
												</div>
												<div class="modal-body">
												<div class="form-group">
													<div class="col-md-6">
													<label><?=$lang["Key_Number"];?></label>
														<input name="keyNumber" type="text" id="keyNumber" class="form-control"  placeholder="<?=$lang["Enter_Key_Number"];?>" value="<?=$pulledKey['keyNumber'];?>">
														<input name="keyId" type="text" id="keyId" hidden="hidden" value="<?=$pulledKey['keyId'];?>">
													</div>
													
													<div class="col-md-6">
													<label><?=$lang["Key_Content"];?></label>
														<input name="keyContent" type="text" id="keyContent" class="form-control"  placeholder="<?=$lang["Enter_Key_Content"];?>" value="<?=$pulledKey['keyContent'];?>">
													</div>
													
												</div>
												</div>
												<div class="modal-footer">
												<button type="button" class="btn btn-default " data-dismiss="modal"><?=$lang["Cancel"];?></button>
												<button type="submit" class="btn btn-warning"><?=$lang["Save"];?></button>
												</div>
											</form>
										</div>
										</div>
									</div>
									<td>
										<span class="button-group">
										<button type="button" class="btn btn-warning fa fa-pencil" data-toggle="modal" data-target="#myModal"></button>
										<a href="keys-delete.php?keyId=<?=$pulledKey['keyId'];?>" onclick="return confirm('<?=$lang["Are_you_sure_to_delete"];?>');" class="fa fa-trash btn btn-danger"></a>
										</span>
									</td>
									</tr>
									<?php } ?>
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