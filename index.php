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




<script>
document.getElementById("homepage").className = "active";
</script>

  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        <?=$lang["Control_Panel"];?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> <?=$lang["Homepage"];?></a></li>
        
      </ol>
    </section>

    <section class="content">
      
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?=$bookCount=$connection->query("select * from books")->rowCount();?><sup style="font-size: 20px"></sup></h3>

              <p><?=$lang["Total_Books"];?></p>
            </div>
            <div class="icon">
              <i class="ion ion-ios-book"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?=$keyCount=$connection->query("select * from computers")->rowCount();?><sup style="font-size: 20px"></sup></h3>

              <p><?=$lang["Total_Keys"];?></p>
            </div>
            <div class="icon">
              <i class="ion ion-key"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          
          <div class="small-box bg-blue">
            <div class="inner">
              <h3><?=$computerCount=$connection->query("select * from computers")->rowCount();?><sup style="font-size: 20px"></sup></h3>

              <p><?=$lang["Total_Computers"];?></p>
            </div>
            <div class="icon">
              <i class="ion ion-laptop"></i>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?=$personnelCount=$connection->query("select * from personnel")->rowCount();?></h3>

              <p><?=$lang["Total_Personnel"];?></p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
           
          </div>
        </div>
        
       
      </div>
      
    </section>
    
  </div>
 <?php include("footer.php"); ?> 
 <?php include("scripts.php"); ?> 
<?php	} ?>