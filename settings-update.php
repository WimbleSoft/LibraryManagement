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
<?php include("menu.php") ?>
<?php include("database.php") ?>

<section id="content">
<?php
$bookId=$_POST['bookId'];
$nameSurname=$_POST['nameSurname'];
$passWord=md5($_POST['nameSurname']);
$eMail=$_POST['eMail'];
$userName=$_POST['userName'];
$phone=$_POST['phone'];

$situation=$connection->query("UPDATE personnel set nameSurname='$nameSurname',passWord='$passWord',eMail='$eMail',userName='$userName',phone='$phone' where bookId='$bookId'") or die ($lang["Something_bad_happened"]);


if($situation){
	$status="<h2>".$lang["Process_Successful"]."</h2>";
}else{
	$status="<h2>".$lang["Process_Failed"]."</h2>";
	}
echo"$status";
echo" <meta http-equiv=\"refresh\" content=\"0;url=settings.php\"> ";
?>

</section>

<!-- GİRİŞ KONTROL -->          
<?php include("ayak.php"); ?>
<?php	} ?>