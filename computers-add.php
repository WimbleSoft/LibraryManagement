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

$manufacturer=$_POST['manufacturer'];
$model=$_POST['model'];
$serialNo=$_POST['serialNo'];

$situation=$connection->query("insert into computers  (manufacturer,model,serialNo,status) values ('$manufacturer','$model','$serialNo','0')") or die ("bir hata olustu...");


if($situation){
	$status="<h2>".$lang["Process_Successful"]."</h2>";
}else{
	$status="<h2>".$lang["Process_Failed"]."</h2>";
	}
echo"$status";
		
		echo" <meta http-equiv=\"refresh\" content=\"0;url=computers.php\"> ";

?>

</section>          
<?php include("footer.php"); ?>
<?php	} ?>