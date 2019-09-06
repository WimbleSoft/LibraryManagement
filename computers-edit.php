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
$computerId=$_POST['computerId'];
$manufacturer=$_POST['manufacturer'];
$model=$_POST['model'];
$serialNo=$_POST['serialNo'];

$situation=$connection->query("UPDATE computers set model='$model',manufacturer='$manufacturer',serialNo='$serialNo' where computerId='$computerId'") or die ($lang["Something_happened_bad"]);


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