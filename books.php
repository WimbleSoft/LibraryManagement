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
document.getElementById("books").className = "active";
</script>

  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        <?=$lang["Books"];?>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="#"><i class="fa fa-dashboard"></i> <?=$lang["Homepage"];?></a></li>
        <li><a href="#"><?=$lang["Books"];?></a></li>
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
              <h3 class="box-title"><?=$lang["Add_New_Book"];?></h3>

              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
              
            </div>
            
            
            <div class="box-body">
			
			
			
			<form role="form" method="post" enctype="multipart/form-data" action="books-add.php">
              <div class="box-body">
                <div class="form-group">
					<div class="col-md-5">
						<input  name="bookName" type="text" class="form-control" id="bookName" placeholder="<?=$lang["Enter_Book_Name"];?>">
					</div>
					<div class="col-md-5">
						<input  name="bookWriter" type="text" class="form-control" id="bookWriter" placeholder="<?=$lang["Enter_Book_Writer"];?>">
					</div>
					<div class="col-md-2">
						<button type="submit" class="btn btn-primary"><?=$lang["Add_the_Book"];?></button>
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
					<th><?=$lang["Book_Name"];?></th>
					<th><?=$lang["Writer"];?></th>
					<th><?=$lang["Status"];?></th>	
					<th><?=$lang["Operations"];?></th>
				  </tr>
				</thead>

				<tbody>
					<?php
					$pullBooks=$connection->query("select * from books")->fetchAll(PDO::FETCH_ASSOC);
					foreach ($pullBooks as $pulledBook)
					{
					?>
				  <tr>
					<td><?=$pulledBook['bookName'];?></td>
					<td><?=$pulledBook['bookWriter'];?></td>
					<td>
					<div class="box box-default collapsed-box" style="border:1px solid #00a65a;">
						<div class="box-header with-border">
						  <h4 class="box-title"><?php if($pulledBook['bookStatus']=='0')echo $lang["In_Inventory"]; else echo $lang["Lent"];?></h4>
						  <div class="box-tools">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
							</button>
						  </div> 
						</div>
						<div class="box-body">
							<?php
							if($pulledBook['bookStatus']=='1')
							{
								$bookId=$pulledBook['bookId'];
								$pullLoan=$connection->query("select * from loans where type='0' AND productId='$bookId' AND returnAccepterId=''")->fetchAll(PDO::FETCH_ASSOC);
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
									else echo 'Bu <?=$lang["Book"];?> henüz iade alınmamıştır.';
								}
							}
							?>
						</div>
					</div>
					</td>
					
					
					
					<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
							<form role="form" method="post" enctype="multipart/form-data" action="books-edit.php">
							  <div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel"><?=$lang["Book"];?> <?=$lang["Edit"];?></h4>
							  </div>
							  <div class="modal-body">
								<div class="form-group">
									<div class="col-md-6">
									<label><?=$lang["Book_Name"];?></label>
										<input name="bookName" type="text" id="bookName" class="form-control"  placeholder="<?=$lang["Book_Name"];?>" value="<?=$pulledBook['bookName'];?>">
										<input name="bookId" type="text" id="bookId" hidden="hidden" value="<?=$pulledBook['bookId'];?>">
									</div>
									
									<div class="col-md-6">
									<label><?=$lang["Book_Writer"];?></label>
										<input name="bookWriter" type="text" id="bookWriter" class="form-control"  placeholder="<?=$lang["Enter_Book_Writer"];?>" value="<?=$pulledBook['bookWriter'];?>">
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
						<a href="books-delete.php?bookId=<?=$pulledBook['bookId'];?>" onclick="return confirm('<?=$lang["Are_you_sure_to_delete"];?>');" class="fa fa-trash btn btn-danger"></a>
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