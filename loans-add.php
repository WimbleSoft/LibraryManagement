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

$productId=$_POST['productId'];
$type=$_POST['type'];
$loanDate = (new DateTime($_POST['loanDate']))->format('Y-m-d');
$lenderId=$_POST['lenderId'];
$loanerId=$_POST['loanerId'];


$situation=$connection->query("insert into loans  (productId,type,loanDate,lenderId,loanerId) values ('$productId','$type','$loanDate','$lenderId','$loanerId')") or die ($lang["Something_bad_happened"]);
if($type==1)
{
$situation3=$connection->query("UPDATE roomkeys set status='1' where keyId='$productId'") or die ($lang["Something_bad_happened"]);
}
else if($type==2)
{
$situation2=$connection->query("UPDATE books set status='1' where bookId='$productId'") or die ($lang["Something_bad_happened"]);
}
else if($type==3)
{
$situation1=$connection->query("UPDATE computers set status='1' where computerId='$productId'") or die ($lang["Something_bad_happened"]);
}

if($situation){
	$status="<h2>".$lang["Process_Successful"]."</h2>";
}else{
	$status="<h2>".$lang["Process_Failed"]."</h2>";
	}
echo"$status";
		
		echo" <meta http-equiv=\"refresh\" content=\"0;url=loans.php\"> ";

?>

</section>          
<?php include("footer.php"); ?>
<?php	} ?>