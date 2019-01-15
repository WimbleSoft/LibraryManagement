<?php
session_start(); 
if((!isset($_SESSION["login"]))||($_SESSION["yetki"]=="0")){
?>
<meta http-equiv="refresh" content="0;URL=giris.php">
<?php
} else
{
?>
<!-- GİRİŞ KONTROL -->
<?php include("header.php") ?>
<?php include("kontrol/veritabani.php") ?>
<section id="content">
<?php
$id=$_GET['id'];
$tur=$_GET['tur'];
$urunid=$_GET['urunid'];


	if($tur=="Bilgisayar"){
		$bilgisayarcek=$connection->query("select * from bilgisayarlar where id='$urunid'")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($bilgisayarcek as $bcek)
		{
			if($bcek['durum']=='0')
			{
			$ayarguncelle1=$connection->query("UPDATE bilgisayarlar set durum='0' where id='$urunid'") or die ("Bir Hata Oluştu");
			$delete = $connection->query("DELETE from oduncler WHERE id='$id' AND tur='$tur'") or die("HATA!");
			echo" <meta http-equiv=\"refresh\" content=\"0;url=oduncler.php\"> ";
			}
			else
			{
			echo" <meta http-equiv=\"refresh\" content=\"0;url=oduncler.php?iadealinmamis=true\"> ";
			}
		}
	}
	if($tur=="Anahtar"){
		$anahtarcek=$connection->query("select * from anahtarlar where id='$urunid'")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($anahtarcek as $acek)
		{
			if($acek['durum']=='0')
			{
			$ayarguncelle2=$connection->query("UPDATE anahtarlar set durum='0' where id='$urunid'") or die ("Bir Hata Oluştu");
			$delete = $connection->query("DELETE from oduncler WHERE id='$id'  AND tur='$tur'") or die("HATA!");
			echo" <meta http-equiv=\"refresh\" content=\"0;url=oduncler.php\"> ";
			}
			else
			{
			echo" <meta http-equiv=\"refresh\" content=\"0;url=oduncler.php?iadealinmamis=true\"> ";
			}
		}
	}

	if($tur=="Kitap"){
		$kitapcek=$connection->query("select * from kitaplar where id='$urunid'")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($kitapcek as $kcek)
		{
			if($kcek['durum']=='0')
			{
			$ayarguncelle3=$connection->query("UPDATE kitaplar set durum='0' where id='$urunid'") or die ("Bir Hata Oluştu");
			$delete = $connection->query("DELETE from oduncler WHERE id='$id' AND tur='$tur' ") or die("HATA!");
			echo" <meta http-equiv=\"refresh\" content=\"0;url=oduncler.php\"> ";
			}
			else
			{
			echo" <meta http-equiv=\"refresh\" content=\"0;url=oduncler.php?iadealinmamis=true\"> ";
			}
		}
	}


?>
</section> </div>
<!-- GİRİŞ KONTROL -->          
<?php include("footer.php"); ?>
<?php	} ?>