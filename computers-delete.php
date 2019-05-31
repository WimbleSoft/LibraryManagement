<?php
session_start(); 
if((!isset($_SESSION["login"]))||($_SESSION["auth"]=="0")){
?>
<meta http-equiv="refresh" content="0;URL=login.php">
<?php
} else
{
?>

<?php include("database.php") ?>
<section id="content">
<?php
$computerId=$_GET['computerId'];
$delete = $connection->query("DELETE from computers WHERE computerId='$computerId'") or die("HATA!");
$delete2 = $connection->query("DELETE from loans WHERE productId='$computerId' and type='2'") or die("HATA!");

if($delete)
{
$status="<h2>".$lang["Record_Deleted"]."</h2>";
}
else
{
    $status="<h2>".$lang["Record_couldnt_Deleted"]."<h2>";
}
echo "$status";
echo '<meta http-equiv=\"refresh\" content=\"0;url=computers.php\"> ';
?>
</section> </div>          
<?php	} ?>