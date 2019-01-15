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
document.getElementById("oduncler").className = "active";
</script>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Ödünç Verilen Bilgisi
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="#"><i class="fa fa-dashboard"></i> Anasayfa</a></li>
        <li><a href="#">Ödünçler</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
			  <div class="box box-default collapsed-box" style="border:1px solid #00a65a;">
				<div class="box-header with-border">
				  <h3 class="box-title">Yeni Ödünç Girdisi</h3>

				  <div class="box-tools">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
					</button>
				  </div>
				  <!-- /.box-tools -->
				</div>
				<!-- /.box-header -->
				
				
				<div class="box-body">
				<div class="col-md-12">
					  <!-- Custom Tabs -->
					  <div class="nav-tabs-custom">
						<ul class="nav nav-tabs">
						  <li class="active"><a href="#tab_1" data-toggle="tab">Bilgisayar</a></li>
						  <li><a href="#tab_2" data-toggle="tab">Anahtar</a></li>
						  <li><a href="#tab_3" data-toggle="tab">Kitap</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<form role="form" method="post" enctype="multipart/form-data" action="oduncler-ekle.php">
									<div class="col-md-4">
									  <div class="box-body">
										<div class="form-group">
										<label>Bilgisayar</label>
										<input  hidden="hidden" name="tur" id="tur" type="text" value="Bilgisayar">
										<select id="id" name="id" class="form-control select2" style="width: 100%;">
										  <option selected="selected">Lütfen Listeden Bir Bilgisayar Seçiniz</option>
										   <?php
											$bilgisayarcek=$connection->query("select * from bilgisayarlar WHERE durum='0' ")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($bilgisayarcek as $bcek)
											{
											?>
											<option value="<?=$bcek['id'];?>">
											<?=$bcek['marka'];?> | <?=$bcek['model'];?> | <?=$bcek['serino'];?>
										  </option>
										  <?php } ?>
										</select>
										<label>Ödünç Alan</label>
										<select name="oduncalan" id="oduncalan"class="form-control select2" style="width: 100%;">
										  <option selected="selected">Personel Seçiniz</option>
										   <?php
											$percek=$connection->query("select * from personel")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($percek as $pcek)
											{
											?>
											<option value="<?=$pcek['id'];?>"><?=$pcek['adsoyad'];?></option>
										  <?php } ?>
										</select>
										</div>
									  <!-- /.form-group -->
									  </div>
									  <!-- /.box-body -->
									</div>
									<div class="col-md-4">
									  <div class="box-body">
										<div class="form-group">
										<label>Ödünç Veren</label>
										<input readonly="readonly" type="text" class="form-control" placeholder="<?=$_SESSION['girenkisi'];?>">
										<input  hidden="hidden" name="oduncveren" id="oduncveren" type="text" value="<?=$_SESSION['girenid'];?>">
										<label>Ödünç Verilme Tarihi:</label>
										<div class="input-group date">
										  <div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										  </div>
										  <input type="text" name="odunctarihi"class="form-control pull-right" id="datepicker" value="<?=$tarih = date("d-m-Y");?>">
										</div>
										</div>
									  <!-- /.form-group -->
									  </div>
									  <!-- /.box-body -->
									</div>
									
									<div class="col-md-2">
									  <div class="box-body">
										<div class="form-group">
										<label>Lütfen Son Bir Kez Kontrol Edin...</label>										
										<input type="submit" class="form-control btn btn-block btn-warning btn-lg" value="Kaydet">
										</div>
									  <!-- /.form-group -->
									  </div>
									  <!-- /.box-body -->
									</div>
								</form>
								<!-- /.form -->
								<!-- /.box-body -->
							
							</div>
							<!-- /.tab-pane -->
							<div class="tab-pane" id="tab_2">
							<form role="form" method="post" enctype="multipart/form-data" action="oduncler-ekle.php">
									<div class="col-md-4">
									  <div class="box-body">
										<div class="form-group">
										<label>Anahtar</label>
										<input  hidden="hidden" name="tur" id="tur" type="text" value="Anahtar">
										<select name="id" class="form-control select2" style="width: 100%;">
										  <option selected="selected">Lütfen Listeden Bir Anahtar Seçiniz</option>
										   <?php
											$anahtarcek=$connection->query("select * from anahtarlar WHERE durum='0' ")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($anahtarcek as $acek)
											{
											?>
											<option value="<?=$acek['id'];?>">
											<?=$acek['numara'];?> | <?=$acek['icerigi'];?>
 											</option>
										  <?php } ?>
										</select>
										<label>Ödünç Alan</label>
										<select name="oduncalan" id="oduncalan" class="form-control select2" style="width: 100%;">
										  <option selected="selected">Personel Seçiniz</option>
										   <?php
											$percek=$connection->query("select * from personel")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($percek as $pcek)
											{
											?>
											<option value="<?=$pcek['id'];?>"><?=$pcek['adsoyad'];?></option>
										  <?php } ?>
										</select>
										</div>
									  <!-- /.form-group -->
									  </div>
									  <!-- /.box-body -->
									</div>
									<div class="col-md-4">
									  <div class="box-body">
										<div class="form-group">
										<label>Ödünç Veren</label>
										<input readonly="readonly" type="text" class="form-control" placeholder="<?=$_SESSION['girenkisi'];?>">
										<input  hidden="hidden" name="oduncveren" id="oduncveren" type="text" value="<?=$_SESSION['girenid'];?>">
										<label>Ödünç Verilme Tarihi:</label>
										<div class="input-group date">
										  <div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										  </div>
										  <input type="text" name="odunctarihi"class="form-control pull-right" id="datepicker2" value="<?=$tarih = date("d-m-Y");?>">
										</div>
										</div>
									  <!-- /.form-group -->
									  </div>
									  <!-- /.box-body -->
									</div>
									
									<div class="col-md-2">
									  <div class="box-body">
										<div class="form-group">
										<label>Lütfen Son Bir Kez Kontrol Edin...</label>										
										<input type="submit" class="form-control btn btn-block btn-warning btn-lg" value="Kaydet">
										</div>
									  <!-- /.form-group -->
									  </div>
									  <!-- /.box-body -->
									</div>
								</form>
								<!-- /.form -->
								<!-- /.box-body -->
							</div>
							<!-- /.tab-pane -->
							<div class="tab-pane" id="tab_3">
							<form role="form" method="post" enctype="multipart/form-data" action="oduncler-ekle.php">
									<div class="col-md-4">
									  <div class="box-body">
										<div class="form-group">
										<label>Kitap</label>
										<input  hidden="hidden" name="tur" id="tur" type="text" value="Kitap">
										<select name="id"class="form-control select2" style="width: 100%;">
										  <option selected="selected">Lütfen Listeden Bir Kitap Seçiniz</option>
										   <?php
											$kitapcek=$connection->query("select * from kitaplar WHERE durum='0' ")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($kitapcek as $kcek)
											{
											?>
											<option value="<?=$kcek['id'];?>">
											<?=$kcek['kitapadi'];?> | <?=$kcek['yazari'];?>
 											</option>
										  <?php } ?>
										</select>
										<label>Ödünç Alan</label>
										<select name="oduncalan" class="form-control select2" style="width: 100%;">
										  <option disabled selected="selected">Personel Seçiniz</option>
										   <?php
											$percek=$connection->query("select * from personel")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($percek as $pcek)
											{
											?>
											<option value="<?=$pcek['id'];?>"><?=$pcek['adsoyad'];?></option>
										  <?php } ?>
										</select>
										</div>
									  <!-- /.form-group -->
									  </div>
									  <!-- /.box-body -->
									</div>
									<div class="col-md-4">
									  <div class="box-body">
										<div class="form-group">
										<label>Ödünç Veren</label>
										<input readonly="readonly" type="text" class="form-control" placeholder="<?=$_SESSION['girenkisi'];?>">
										<input  hidden="hidden" name="oduncveren" id="oduncveren" type="text" value="<?=$_SESSION['girenid'];?>">
										<label>Ödünç Verilme Tarihi:</label>
										<div class="input-group date">
										  <div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										  </div>
										  <input type="text" name="odunctarihi"class="form-control pull-right" id="datepicker3" value="<?=$tarih = date("d-m-Y");?>">
										</div>
										</div>
									  <!-- /.form-group -->
									  </div>
									  <!-- /.box-body -->
									</div>
									
									<div class="col-md-2">
									  <div class="box-body">
										<div class="form-group">
										<label>Lütfen Son Bir Kez Kontrol Edin...</label>										
										<input type="submit" class="form-control btn btn-block btn-warning btn-lg" value="Kaydet">
										</div>
									  <!-- /.form-group -->
									  </div>
									  <!-- /.box-body -->
									</div>
								</form>
								<!-- /.form -->
								<!-- /.box-body -->
							</div>
						  <!-- /.tab-pane -->
						</div>
						<!-- /.tab-content -->
					  </div>
					  <!-- nav-tabs-custom -->
					</div>
					<!-- /.col -->
				</div>
	
            </div>
			
			
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
				  <tr>
					<th>Tür</th>
					<th>Adı/Marka/Model</th>
					<th>Ödünç Tarihi</th>
					<th>Ödünç Veren</th>
					<th>Ödünç Alan</th>
					<th>İade Tarihi</th>
					<th>İade Alan</th>
					<th>İşlemler</th>
				  </tr>
				</thead>

				<tbody>
					<?php
					$vericek=$connection->query("select * from oduncler")->fetchAll(PDO::FETCH_ASSOC);
					foreach ($vericek as $vcek)
					{
					?>
				  <tr>
					<td><?=$vcek['tur'];?></td>
					<td>
					<?php
					if($vcek['tur']=="Kitap")
					{
						$kitapcek=$connection->query("select * from kitaplar where id=".$vcek['urunid']."")->fetchAll(PDO::FETCH_ASSOC);
						foreach ($kitapcek as $kcek)
						{
						echo $kcek['kitapadi'];
						}
					}
					else if($vcek['tur']=="Bilgisayar")
					{
						$bilgisayarcek=$connection->query("select * from bilgisayarlar where id=".$vcek['urunid']."")->fetchAll(PDO::FETCH_ASSOC);
						foreach ($bilgisayarcek as $bcek)
						{
						echo $bcek['marka'].' '.$bcek['model'];
						}
					}
					else if($vcek['tur']=="Anahtar")
					{
						$anahtarcek=$connection->query("select * from anahtarlar where id=".$vcek['urunid']."")->fetchAll(PDO::FETCH_ASSOC);
						foreach ($anahtarcek as $acek)
						{
						echo $acek['numara'];
						}
					}
					
					?>
					
					</td>
					<td><?=(new DateTime($vcek['odunctarihi']))->format('d-m-Y');?></td>
					<td>
						<?php
							
							$percek=$connection->query("select * from personel where id=".$vcek['oduncveren']."")->fetchAll(PDO::FETCH_ASSOC);
							foreach ($percek as $pcek)
							{
							echo $pcek['adsoyad'];
							}
						?>
					</td>
					<td>
						<?php
							$percek=$connection->query("select * from personel where id=".$vcek['oduncalan']."")->fetchAll(PDO::FETCH_ASSOC);
							foreach ($percek as $pcek)
							{
							echo $pcek['adsoyad'];
							}
						?>
					</td>
					<td>
						<?php
						if($vcek['iadetarihi']=="0000-00-00"){echo 'İade Edilmedi';}
						else{echo (new DateTime($vcek['iadetarihi']))->format('d-m-Y');}
						?>
					</td>
					<td>
						<?php
						if($vcek['iadealan']==NULL)
						{
							echo 'İade Edilmedi'; 
							
						}
						else 
						{
							$percek=$connection->query("select * from personel where id=".$vcek['iadealan']."")->fetchAll(PDO::FETCH_ASSOC);
							foreach ($percek as $pcek)
							{
							echo $pcek['adsoyad'];
							} 
							
						}
						?>
						
					</td>
					<td>
					  <span class="button-group">
						<a href="oduncler-iade-al.php?id=<?=$vcek['id'];?>&tur=<?=$vcek['tur'];?>&urunid=<?=$vcek['urunid'];?>" onclick="alert('Tarih ve iade alan kişi otomatik atanacaktır. Eğer daha önce iade alınmışsa, işlem geçersiz olacaktır.')"class="fa fa-check"></a>
						<a href="oduncler-duzenle.php?id=<?=$vcek['id'];?>" class="fa fa-pencil"></a>
						<a href="oduncler-sil.php?id=<?=$vcek['id'];?>&tur=<?=$vcek['tur'];?>&urunid=<?=$vcek['urunid'];?>" onclick="alert('Bu ürüne ait bu ödünç bilgisi sistemden silinecek. Eğer ürünü henüz iade almadıysanız, silme işlemi gerçekleşmeyecek.')" class="fa fa-trash"></a>
					  </span>
					</td>
				  </tr>
				  <?php } ?>
				</tbody>
              </table>
			  <?php 
				if(isset($_GET['iadealinmis']))
				{
					if($_GET['iadealinmis']!=NULL)
					{
						echo "<script>alert('İade alınmış bir ürünü tekrar iade edemezsiniz');</script>";
					}
				}
				
				if(isset($_GET['iadealinmamis']))
				{
					if($_GET['iadealinmamis']!=NULL)
					{
						echo "<script>alert('İade alınmayan ürünün ödünç bilgisi silinemez. Lütfen önce iade alınız.');</script>";
					}
				}
				?>
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

  <!-- PAGE CONTENT END -->
  <!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- bootstrap datepicker -->
<script src="plugins/datepicker/bootstrap-datepicker.js"></script>
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
<script src="plugins/select2/select2.full.min.js"></script>
<script src="plugins/input-mask/jquery.inputmask.js"></script>
<script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="plugins/input-mask/jquery.inputmask.extensions.js"></script>

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
	
    $(".select2").select2();
	   //Datemask dd/mm/yyyy
    $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    //Datemask2 mm/dd/yyyy
    $("#datemask2").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    //Money Euro
    $("[data-mask]").inputmask();
    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    });
	$('#datepicker2').datepicker({
      autoclose: true
    });
	$('#datepicker3').datepicker({
      autoclose: true
    });
  });
</script>
 <?php include("footer.php") ?>


 <!-- Giriş KONTROL -->         
<?php	} ?>