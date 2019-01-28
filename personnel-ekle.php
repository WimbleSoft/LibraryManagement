<?php
session_start(); 
if((!isset($_SESSION["login"]))||($_SESSION["auth"]=="0")){
?>
<meta http-equiv="refresh" content="0;URL=login.php">
<?php
} else
{
?>
<!-- GİRİŞ KONTROL -->
<?php include("header.php") ?>
<?php include("database.php") ?>


<section id="content">
<?php

$nameSurname=$_POST['nameSurname'];
$passWord=$_POST['passWord'];
$eMail=$_POST['eMail'];
$phone=$_POST['phone'];
$userName=$_POST['userName'];
if(isset($_POST['auth'])) { // checkbox seçilmişse "on" değeri gönderiliyor
    $auth=1;
} else { // seçilmemişse bu değer sayfaya hiç gönderilmiyor
    $auth=0;
}
$situation=$connection->query("insert into personnel  (userName,passWord,eMail,phone];?>,nameSurname,auth) values ('$userName','$passWord','$eMail','$phone','$nameSurname','$auth')") or die ($lang["Something_bad_happened"]);


if($situation){
	$status="<h2>".$lang["Process_Successful"]."</h2>";
}else{
	$status="<h2>".$lang["Process_Failed"]."</h2>";
	}
echo"$status";
		
		echo" <meta http-equiv=\"refresh\" content=\"0;url=personnel.php\"> ";

?>

</section>
<!-- GİRİŞ KONTROL -->          
<?php include("footer.php"); ?>
<?php	} ?>