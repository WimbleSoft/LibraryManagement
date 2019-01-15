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

$urunid=$_POST['id'];
$tur=$_POST['tur'];
$odunctarihi = (new DateTime($_POST['odunctarihi']))->format('Y-m-d');
$oduncveren=$_POST['oduncveren'];
$oduncalan=$_POST['oduncalan'];


$ayarguncelle=$connection->query("insert into oduncler  (urunid,tur,odunctarihi,oduncveren,oduncalan) values ('$urunid','$tur','$odunctarihi','$oduncveren','$oduncalan')") or die ("bir hata olustu...");
if($tur=="Bilgisayar")
{
$ayarguncelle1=$connection->query("UPDATE bilgisayarlar set durum='1' where id='$urunid'") or die ("Bir Hata Oluştu");
}
else if($tur=="Kitap")
{
$ayarguncelle2=$connection->query("UPDATE kitaplar set durum='1' where id='$urunid'") or die ("Bir Hata Oluştu");
}
else if($tur=="Anahtar")
{
$ayarguncelle3=$connection->query("UPDATE anahtarlar set durum='1' where id='$urunid'") or die ("Bir Hata Oluştu");
}

if($ayarguncelle){
	$durum="<h2>İşlem Başarılı</h2>";
}else{
	$durum="<h2>Hiç veri girmediniz..</h2>";
	}
echo"$durum";
		
		echo" <meta http-equiv=\"refresh\" content=\"0;url=oduncler.php\"> ";

?>

</section>
<!-- GİRİŞ KONTROL -->          
<?php include("footer.php"); ?>
<?php	} ?>