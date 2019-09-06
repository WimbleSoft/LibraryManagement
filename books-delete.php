<?php
session_start(); 
if((!isset($_SESSION["login"]))||($_SESSION["auth"]=="0"))
{
?>
    <meta http-equiv="refresh" content="0;URL=login.php">
<?php
} 
else
{
    include("database.php")
?>
    <section id="content">
<?php
    $bookId=$_GET['bookId'];
    $delete = $connection->query("DELETE from books WHERE bookId='$bookId") or die("HATA!");
    $delete2 = $connection->query("DELETE from loans WHERE productId='$bookId' and type='0'") or die("HATA!");

    if($delete)
    {
        $status="<h2>".$lang["Record_Deleted"]."</h2>";
    }
    else
    {
        $status="<h2>".$lang["Record_couldnt_Deleted"]."<h2>";
    }
        echo    "$status";
        echo    " <meta http-equiv=\"refresh\" content=\"0;url=books.php\"> ";
    ?>
    </section>   
<?php	
} 
?>