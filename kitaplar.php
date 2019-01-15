<?php
session_start(); 
if((!isset($_SESSION["login"]))||($_SESSION["yetki"]=="0")){
?>
<meta http-equiv="refresh" content="0;URL=giris.php">
<?php
} else
{
?>

<!-- Giriş KONTROL -->
<?php include("header.php") ?>
<?php include("kontrol/veritabani.php") ?>

<!-- PAGE CONTENT -->


<script>
document.getElementById("kitaplar").className = "active";
</script>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Kitaplar
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="#"><i class="fa fa-dashboard"></i> Anasayfa</a></li>
        <li><a href="#">Kitaplar</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              
			 

        <div class="col-md-6">
          <div class="box box-default collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Yeni Kitap Ekle</h3>

              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            
            <div class="box-body">
			
			<!-- form -->
			
			<form role="form" method="post" enctype="multipart/form-data" action="kitaplar-ekle.php">
              <div class="box-body">
                <div class="form-group">
					<div class="col-md-5">
						<input  name="kitapadi" type="text" class="form-control" id="kitapadi" placeholder="Kitap Adını Girin">
					</div>
					<div class="col-md-5">
						<input  name="yazari" type="text" class="form-control" id="yazari" placeholder="Kitabın Yazarını Girin">
					</div>
					<div class="col-md-2">
						<button type="submit" class="btn btn-primary">Kitabı Ekle</button>
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
					<th>Kitap Adı</th>
					<th>Yazarı</th>
					<th>Durumu</th>	
					<th>İşlemler</th>
				  </tr>
				</thead>

				<tbody>
					<?php
					$vericek=$connection->query("select * from kitaplar")->fetchAll(PDO::FETCH_ASSOC);
					foreach ($vericek as $vcek)
					{
					?>
				  <tr>
					<td><?=$vcek['kitapadi'];?></td>
					<td><?=$vcek['yazari'];?></td>
					<td>
					<div class="box box-default collapsed-box" style="border:1px solid #00a65a;">
						<div class="box-header with-border">
						  <h4 class="box-title"><?php if($vcek['durum']=='0')echo'Dolapta'; else echo'Ödünç Verildi';?></h4>
						  <div class="box-tools">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
							</button>
						  </div> 
						</div>
						<div class="box-body">
							<?php
							if($vcek['durum']=='1')
							{
								$id=$vcek['id'];
								$odunccek=$connection->query("select * from oduncler where tur='Kitap' AND urunid='$id' AND iadealan=''")->fetchAll(PDO::FETCH_ASSOC);
								foreach ($odunccek as $ocek)
								{
									echo 'Bu '.$ocek['tur'].', '.$ocek['odunctarihi'].' tarihinde ';
									
									$percek=$connection->query("select * from personel where id=".$ocek['oduncalan']."")->fetchAll(PDO::FETCH_ASSOC);
									foreach ($percek as $pcek)
									{
									echo $pcek['adsoyad'];
									}
									echo ' tarafından ';
									$percek2=$connection->query("select * from personel where id=".$ocek['oduncalan']."")->fetchAll(PDO::FETCH_ASSOC);
									foreach ($percek2 as $pcek2)
									{
									echo $pcek2['adsoyad'];
									}
									echo ' adlı personele emanet edilmiştir.';
									if ($ocek['iadetarihi']!="0000-00-00") 
									{
									//echo $ocek['iadetarihi'].' tarihinde, '.$ocek['iadealan'].' tarafından iade alınmıştır.';
									}
									else echo 'Bu kitap henüz iade alınmamıştır.';
								}
							}
							?>
						</div>
					</div>
					</td>
					
					
					<!-- Small modal -->
					<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
							<form role="form" method="post" enctype="multipart/form-data" action="kitaplar-duzenle.php">
							  <div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel">Kitap Düzenle</h4>
							  </div>
							  <div class="modal-body">
								<div class="form-group">
									<div class="col-md-6">
									<label>Kitap Adı</label>
										<input name="kitapadi" type="text" id="kitapadi" class="form-control"  placeholder="Kitap Adı Girin" value="<?=$vcek['kitapadi'];?>">
										<input name="id" type="text" id="id" hidden="hidden" value="<?=$vcek['id'];?>">
									</div>
									
									<div class="col-md-6">
									<label>Kitap Yazarı</label>
										<input name="yazari" type="text" id="yazari" class="form-control"  placeholder="-Kitap Yazarını Girin" value="<?=$vcek['yazari'];?>">
									</div>
									
								</div>
							  </div>
							  <div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
								<button type="submit" class="btn btn-warning">Kaydet</button>
							  </div>
							</form>
						</div>
					  </div>
					</div>
					<td>
					  <span class="button-group">
						<button type="button" class="btn btn-warning fa fa-pencil" data-toggle="modal" data-target="#myModal"></button>
						<a href="kitaplar-sil.php?id=<?=$vcek['id'];?>" onclick="return confirm('İçeriği silmek istediğinize emin misiniz? Bu işlem geri alınamaz ve bu kitapa ait ödünç bilgileri de silinecektir!');" class="fa fa-trash btn btn-danger"></a>
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