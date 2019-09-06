<?php
session_start(); 
if((!isset($_SESSION["login"]))||($_SESSION["auth"]=="0")){
?>
<meta http-equiv="refresh" content="0;URL=login.php">
<?php
} else
{
?>
<?php include("header.php") ?>


<section id="content">
<?php

$keyContent=$_POST['keyContent'];
$keyNumber=$_POST['keyNumber'];

$situation=$connection->query("insert into roomkeys  (keyContent,keyNumber,keyStatus) values ('$keyContent','$keyNumber','2')") or die ("bir hata olustu...");


if($situation){
	$status="<h2>".$lang["Process_Successful"]."</h2>";
}else{
	$status="<h2>".$lang["Process_Failed"]."</h2>";
	}
echo"$status";
		
		echo" <meta http-equiv=\"refresh\" content=\"0;url=keys.php\"> ";

?>

</section>          
<?php include("footer.php"); ?>
<?php	} ?>