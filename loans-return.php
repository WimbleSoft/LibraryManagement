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

$productId=$_GET['productId'];
$type=$_GET['type'];
$loanId=$_GET['loanId'];

$situation=$connection->query("UPDATE loans set returnAccepterId='".$_SESSION['enteredPersonId']."',returnDate='".date("Y-m-d")."' where loanId='$loanId'") or die ($lang["Something_bad_happened"]);

if($type==1){
	$pullBook=$connection->query("select * from books where bookId='$productId' ")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($pullBook as $pulledBook)
	{
		if($pulledBook['status']=='1')
		{
		$situation3=$connection->query("UPDATE books set status='0' where bookId='$productId'") or die ($lang["Something_bad_happened"]);
		}
		else
		{
		echo" <meta http-equiv=\"refresh\" content=\"0;url=loans.php?returned=true\"> ";
		}
	}
}
if($type==2){
	$pullComputer=$connection->query("select * from computers where computerId='$productId'")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($pullComputer as $pulledComputer)
	{
		if($pulledComputer['status']=='1')
		{
		$situation1=$connection->query("UPDATE computers set status='0' where computerId='$productId'") or die ($lang["Something_bad_happened"]);
		}
		else
		{
		echo" <meta http-equiv=\"refresh\" content=\"0;url=loans.php?returned=true\"> ";
		}
	}
}
if($type==3){
	$pullKey=$connection->query("select * from keys where keyId='$productId'")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($pullKey as $pulledKey)
	{
		if($pulledKey['status']=='1')
		{
		$situation2=$connection->query("UPDATE keys set status='0' where keyId='$productId'") or die ($lang["Something_bad_happened"]);
		}
		else
		{
		echo" <meta http-equiv=\"refresh\" content=\"0;url=loans.php?returned=true\"> ";
		}
	}
}




if($situation){
	$status="<h2>".$lang["Process_Successful"]."</h2>";
}else{
	$status="<h2>".$lang["Process_Failed"]."</h2>";
	}

echo"$status";		

?>

</section>          
<?php include("footer.php"); ?>
<?php	} ?>