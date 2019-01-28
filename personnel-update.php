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
$personnelId=$_POST['personnelId'];
$nameSurname=$_POST['nameSurname'];
$passWord=md5($_POST['passWord']);
$eMail=$_POST['eMail'];
$userName=$_POST['userName'];
$phone=$_POST['phone'];
if(isset($_POST['auth'])) { 
    $auth=1;
} else { 
    $auth=0;
}
if($_POST['passWord']=='')
{
$situation=$connection->query("UPDATE personnel set nameSurname='$nameSurname',eMail='$eMail',userName='$userName',auth='$auth',phone='$phone' where personnelId='$personnelId'") or die ($lang["Something_bad_happened"]);
}
else
{
$situation2=$connection->query("UPDATE personnel set nameSurname='$nameSurname',passWord='$passWord',eMail='$eMail',userName='$userName',auth='$auth',phone='$phone' where personnelId='$personnelId'") or die ($lang["Something_bad_happened"]);
}
echo" <meta http-equiv=\"refresh\" content=\"0;url=personnel.php\"> ";
?>

</section>

<!-- GİRİŞ KONTROL -->          
<?php include("footer.php"); ?>
<?php	} ?>