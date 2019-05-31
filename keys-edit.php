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
<?php include("database.php") ?>

<section id="content">
<?php
$keyId=$_POST['keyId'];
$keyNumber=$_POST['keyNumber'];
$keyContent=$_POST['keyContent'];

$situation=$connection->query("UPDATE keys set keyNumber='$keyNumber',keyContent='$keyContent' where keyId='$keyId'") or die ($lang["Something_happened_bad"]);


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
