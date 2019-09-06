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
$productId=$_GET['productId'];
$type=$_GET['type'];
$loanId=$_GET['loanId'];

	if($type==1){
		$pullBook=$connection->query("select * from books where bookId='$productId'")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($pullBook as $pulledBook)
		{
			if($pulledBook['status']=='0')
			{
				$situation3=$connection->query("UPDATE books set status='0' where bookId='$productId'") or die ($lang["Something_bad_happened"]);
				$delete = $connection->query("DELETE from loans WHERE loanId='$loanId' AND type='$type' ") or die($lang["Something_bad_happened"]);
				echo" <meta http-equiv=\"refresh\" content=\"0;url=loans.php\"> ";
			}
			else
			{
				echo" <meta http-equiv=\"refresh\" content=\"0;url=loans.php?notReturned=true\"> ";
			}
		}
	}
	if($type==2){
		$pullComputer=$connection->query("select * from computers where computerId='$productId'")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($pullComputer as $pulledComputer)
		{
			if($pulledComputer['status']=='0')
			{
			$situation1=$connection->query("UPDATE computers set status='0' where computerId='$productId'") or die ($lang["Something_bad_happened"]);
			$delete = $connection->query("DELETE from loans WHERE loanId='$loanId' AND type='$type'") or die($lang["Something_bad_happened"]);
			echo" <meta http-equiv=\"refresh\" content=\"0;url=loans.php\"> ";
			}
			else
			{
			echo" <meta http-equiv=\"refresh\" content=\"0;url=loans.php?notReturned=true\"> ";
			}
		}
	}
	if($type==3){
		$pullKey=$connection->query("select * from roomkeys where keyId='$productId'")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($pullKey as $pulledKey)
		{
			if($pulledKey['status']=='0')
			{
			$situation2=$connection->query("UPDATE roomkeys set status='0' where keyId='$productId'") or die ($lang["Something_bad_happened"]);
			$delete = $connection->query("DELETE from loans WHERE loanId='$loanId'  AND type='$type'") or die($lang["Something_bad_happened"]);
			echo" <meta http-equiv=\"refresh\" content=\"0;url=loans.php\"> ";
			}
			else
			{
			echo" <meta http-equiv=\"refresh\" content=\"0;url=loans.php?notReturned=true\"> ";
			}
		}
	}

	


?>
</section> </div>          
<?php include("footer.php"); ?>
<?php	} ?>