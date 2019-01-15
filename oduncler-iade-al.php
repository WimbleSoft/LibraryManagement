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

$ayarguncelle=$connection->query("UPDATE oduncler set iadealan='".$_SESSION['girenid']."',iadetarihi='".date("Y-m-d")."' where id='$id'") or die ("Bir Hata Oluştu");


if($tur=="Bilgisayar"){
	$bilgisayarcek=$connection->query("select * from bilgisayarlar where id='$urunid'")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($bilgisayarcek as $bcek)
	{
		if($bcek['durum']=='1')
		{
		$ayarguncelle1=$connection->query("UPDATE bilgisayarlar set durum='0' where id='$urunid'") or die ("Bir Hata Oluştu");
		}
		else
		{
		echo" <meta http-equiv=\"refresh\" content=\"0;url=oduncler.php?iadealinmis=true\"> ";
		}
	}
}
if($tur=="Anahtar"){
	$anahtarcek=$connection->query("select * from anahtarlar where id='$urunid'")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($anahtarcek as $acek)
	{
		if($acek['durum']=='1')
		{
		$ayarguncelle2=$connection->query("UPDATE anahtarlar set durum='0' where id='$urunid'") or die ("Bir Hata Oluştu");
		}
		else
		{
		echo" <meta http-equiv=\"refresh\" content=\"0;url=oduncler.php?iadealinmis=true\"> ";
		}
	}
}

if($tur=="Kitap"){
	$kitapcek=$connection->query("select * from kitaplar where id='$urunid' ")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($kitapcek as $kcek)
	{
		if($kcek['durum']=='1')
		{
		$ayarguncelle3=$connection->query("UPDATE kitaplar set durum='0' where id='$urunid'") or die ("Bir Hata Oluştu");
		}
		else
		{
		echo" <meta http-equiv=\"refresh\" content=\"0;url=oduncler.php?iadealinmis=true\"> ";
		}
	}
}


if($ayarguncelle){
	$durum="<h2>İşlem Başarılı</h2>";
}else{
	$durum="<h2>Hiç veri girmediniz..</h2>";
	}

echo"$durum";		

?>

</section>
<!-- GİRİŞ KONTROL -->          
<?php include("footer.php"); ?>
<?php	} ?>