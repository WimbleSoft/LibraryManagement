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

<?php include("database.php") ?>
<section id="content">
<?php
$personnelId = $_GET['personnelId'];
$delete = $connection->query("DELETE from personnel WHERE personnelId='$personnelId'") or die($lang["Something_happened_bad"]);
$delete2 = $connection->query("DELETE from loans WHERE returnAccepterId='$personnelId' OR lenderId='$personnelId' OR loanerId='$personnelId'") or die($lang["Something_happened_bad"]);

if($delete){
$status="<h2>".$lang["Record_Deleted"]."</h2>";
}else{
$status="<h2>".$lang["Record_couldnt_Deleted"]."</h2>";
}
<?php
echo"$status";
echo " <meta http-equiv=\"refresh\" content=\"0;url=personnel.php\"> ";
?>
</section> </div>
<!-- GİRİŞ KONTROL -->          
<?php	} ?>