<?php
session_start(); 
if((!isset($_SESSION["login"]))||($_SESSION["auth"]=="0")){
?>
<meta http-equiv="refresh" content="0;URL=login.php">
<?php
} else
{
?>

<!-- Giriş KONTROL -->
<?php include("header.php") ?>
<?php include("database.php") ?>

<!-- PAGE CONTENT -->
<script>
document.getElementById("personnel").className = "active";
</script>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=$lang["Personnel"];?>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="#"><i class="fa fa-dashboard"></i> <?=$lang["Homepage"];?></a></li>
        <li><a href="#"><?=$lang["Personnel"];?></a></li>
      </ol>
    </section>

    <!-- Main content -->
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
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            
            <div class="box-body">
			
			<!-- form -->
			
			<form role="form" method="post" enctype="multipart/form-data" action="personnel-add.php">
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
						<input name="userName" type="text" id="userName" class="form-control" placeholder="<?=$lang["User_Name"];?> Girin">
					</div>
					<div class="col-md-2">
						<input name="passWord" type="password" id="passWord" class="form-control" placeholder="<?=$lang["Password"];?> Girin">
					</div>
					<div class="col-md-1">
						<div class="checkbox">
						<label>
						  <input id="auth" name="auth" type="checkbox">
						  <?=$lang["Is_Admin"];?>
						</label>
						</div>
					</div>
                  
					<div class="col-md-1">
						<button type="submit" class="btn btn-primary"><?=$lang["Add_the_Personnel"];?></button>
					</div>
				</div>
              </div>
              <!-- /.box-body -->

            </form>
			<!-- /.form -->
			
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
		
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
				  <tr>
					<th><?=$lang["Name_Surname"];?></th>
					<th><?=$lang["User_Name"];?></th>
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
						<a href="personnel-edit.php?personnelId=<?=$pulledData['personnelId'];?>" class="fa fa-pencil"></a>
						<a href="personnel-delete.php?personnelId=<?=$pulledData['personnelId'];?>" onclick="return confirm('<?=$lang['Are_you_sure_to_delete'];?>');" class="fa fa-trash"></a>
					  </span>
					</td>
				  </tr>
				  <?php } ?>
				</tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- page script -->
<script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>
  <!-- PAGE CONTENT END -->
 <?php include("footer.php") ?>
 
 
 <!-- Giriş KONTROL -->         
<?php	} ?>