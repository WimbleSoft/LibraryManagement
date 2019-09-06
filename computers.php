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
document.getElementById("computers").className = "active";
</script>


  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        <?=$lang["Computers"];?>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="#"><i class="fa fa-dashboard"></i> <?=$lang["Homepage"];?></a></li>
        <li><a href="#"><?=$lang["Computers"];?></a></li>
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
              <h3 class="box-title"><?=$lang["Add_New_Computer"];?></h3>

              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
              
            </div>
            

            <div class="box-body">

			

			<form role="form" method="post" enctype="multipart/form-data" action="computers-add.php">
              <div class="box-body">
                <div class="form-group">
					<div class="col-md-3">
						<input name="manufacturer" type="text" id="manufacturer" class="form-control"  placeholder="<?=$lang["Enter_Manufacturer"];?>">
					</div>
					<div class="col-md-3">
						<input name="model" type="text" id="model" class="form-control"  placeholder="<?=$lang["Enter_Model"];?>">
					</div>
					<div class="col-md-4">
						<input name="serialNo" type="text" id="serialNo" class="form-control" placeholder="<?=$lang["Enter_Serial_Number"];?>">
					</div>
					<div class="col-md-1">
						<button type="submit" class="btn btn-primary"><?=$lang["Add_the_Computer"];?></button>
					</div>
				</div>
              </div>
              

            </form>
			

            </div>
            
          </div>
          
        </div>

            </div>
			<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModal">
			  <div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
				  ...
				</div>
			  </div>
			</div>
            
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
				  <tr>
					<th><?=$lang["Manufacturer"];?></th>
					<th><?=$lang["Model"];?></th>
					<th><?=$lang["Serial_Number"];?></th>
					<th><?=$lang["Status"];?></th>
					<th><?=$lang["Operations"];?></th>
				  </tr>
				</thead>

				<tbody>
					<?php
					$pullComputer=$connection->query("select * from computers")->fetchAll(PDO::FETCH_ASSOC);
					foreach ($pullComputer as $pulledComputer)
					{
					?>
				  <tr>
					<td><?=$pulledComputer['manufacturer'];?></td>
					<td><?=$pulledComputer['model'];?></td>
					<td><?=$pulledComputer['serialNo'];?></td>
					<td>
					<div class="box box-default collapsed-box" style="border:1px solid #00a65a;">
						<div class="box-header with-border">
						  <h4 class="box-title"><?php if($pulledComputer['computerStatus']=='0')echo $lang["In_inventory"]; else echo $lang["Lent"];?></h4>
						  <div class="box-tools">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
							</button>
						  </div>
						</div>
						<div class="box-body">
							<?php
							if($pulledComputer['computerStatus']=='1')
							{
								$computerId=$pulledComputer['computerId'];
								$pullLoan=$connection->query("select * from loans where type='1' AND productId='$computerId' AND returnAccepterId=''")->fetchAll(PDO::FETCH_ASSOC);
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
									else echo 'Bu bilgisayar henüz iade alınmamıştır.';
								}
							}
							?>
						</div>
					</div>
					</td>

					
					<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
							<form role="form" method="post" enctype="multipart/form-data" action="computers-edit.php">
							  <div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel"><?=$lang["Edit_Computer"];?></h4>
							  </div>
							  <div class="modal-body">
								<div class="form-group">
									<div class="col-md-3">
									<label><?=$lang["Manufacturer"];?></label>
										<input name="manufacturer" type="text" id="manufacturer" class="form-control"  placeholder="<?=$lang["Enter_Manufacturer"];?>" value="<?=$pulledComputer['manufacturer'];?>">
										<input name="computerId" type="text" id="computerId" hidden="hidden" value="<?=$pulledComputer['computerId'];?>">
									</div>

									<div class="col-md-3">
									<label><?=$lang["Model"];?></label>
										<input name="model" type="text" id="model" class="form-control"  placeholder="<?=$lang["Enter_Model"];?>" value="<?=$pulledComputer['model'];?>">
									</div>

									<div class="col-md-4">
									<label><?=$lang["Serial_Number"];?></label>
										<input name="serialNo" type="text" id="serialNo" class="form-control" placeholder="<?=$lang["Enter_Serial_Number"];?>" value="<?=$pulledComputer['serialNo'];?>">
									</div>
								</div>
							  </div>
							  <div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal"><?=$lang["Cancel"];?></button>
								<button type="submit" class="btn btn-warning"><?=$lang["Save"];?></button>
							  </div>
							</form>
						</div>
					  </div>
					</div>
					<td>
					  <span class="button-group">
						<button type="button" class="btn btn-warning fa fa-pencil" data-toggle="modal" data-target="#myModal"></button>
						<a href="computer-delete.php?computerId=<?=$pulledComputer['computerId'];?>" onclick="return confirm('<?=$lang["Are_you_sure_to_delete"];?>');" class="fa fa-trash btn btn-danger"></a>
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
