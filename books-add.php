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

$bookName=$_POST['bookName'];
$bookWriter=$_POST['bookWriter'];

$situation=$connection->query("insert into books  (bookName,bookWriter,status) values ('$bookName','$bookWriter','0')") or die ($lang["Something_bad_happened"]);


if($situation){
	$status="<h2>".$lang["Process_Successful"]."</h2>";
}else{
	$status="<h2>Hiç veri girmediniz..</h2>";
	}
echo"$status";
		
		echo" <meta http-equiv=\"refresh\" content=\"0;url=books.php\"> ";

?>

</section>
<!-- GİRİŞ KONTROL -->          
<?php include("footer.php"); ?>
<?php	} ?>