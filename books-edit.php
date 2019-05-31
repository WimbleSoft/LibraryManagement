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
$bookId=$_POST['bookId'];
$bookName=$_POST['bookName'];
$bookWriter=$_POST['bookWriter'];

$situation=$connection->query("UPDATE books set bookName='$bookName',bookWriter='$bookWriter' where bookId='$bookId'") or die ($lang["Something_bad_happened"]);


if($situation){
	$status="<h2>".$lang["Process_Successful"]."</h2>";
}else{
	$status="<h2>".$lang["Process_Failed"]."</h2>";
	}
echo"$status";
echo" <meta http-equiv=\"refresh\" content=\"0;url=books.php\"> ";
?>

</section>
          
<?php include("footer.php"); ?>
<?php	} ?>