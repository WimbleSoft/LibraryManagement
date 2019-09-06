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

<script>
	document.getElementById("loans").className = "active";
</script>

<div class="content-wrapper">

<section class="content-header">
	<h1>
		<?=$lang["Lent_Information"];?>
	</h1>
	<ol class="breadcrumb">
		<li class="active"><a href="#"><i class="fa fa-dashboard"></i> <?=$lang["Homepage"];?></a></li>
		<li><a href="#"><?=$lang["Loans"];?></a></li>
	</ol>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<div class="box box-default collapsed-box" style="border:1px solid #00a65a;">
						<div class="box-header with-border">
							<h3 class="box-title"><?=$lang["New_Loan_Entry"];?></h3>
							<div class="box-tools">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
								</button>
							</div>
							
						</div>
	
						<div class="box-body">
							<div class="col-md-12">
								
								<div class="nav-tabs-custom">
									<ul class="nav nav-tabs">
										<li class="active"><a href="#tab_1" data-toggle="tab"><?=$lang["Book"];?></a></li>
										<li><a href="#tab_2" data-toggle="tab"><?=$lang["Computer"];?></a></li>
										<li><a href="#tab_3" data-toggle="tab"><?=$lang["Key"];?></a></li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane" id="tab_1">
											<form role="form" method="post" enctype="multipart/form-data" action="loans-add.php">
												<div class="col-md-4">
													<div class="box-body">
														<div class="form-group">
															<label><?=$lang["Book"];?></label>
															<input  hidden="hidden" name="type" id="type" type="text" value="0">
															<select name="id"class="form-control select2" style="width: 100%;">
																<option selected="selected"><?=$lang["Please_Select_a_Book_From_List"];?></option>
															<?php
																$pullBook=$connection->query("select * from books WHERE bookStatus='0' ")->fetchAll(PDO::FETCH_ASSOC);
																foreach ($pullBook as $pulledBook)
																{
																?>
																	<option value="<?=$pulledBook['bookId'];?>">
																		<?=$pulledBook['bookName'];?> | <?=$pulledBook['bookWriter'];?>
																	</option>
															<?php 
																} 
																?>
															</select>
															<label><?=$lang["Loaner"];?></label>
															<select name="loanerId" class="form-control select2" style="width: 100%;">
																<option disabled selected="selected"><?=$lang["Select_Personnel"];?></option>
															<?php
																$pullPersonnel=$connection->query("select * from personnel")->fetchAll(PDO::FETCH_ASSOC);
																foreach ($pullPersonnel as $pulledPersonnel)
																{
																?>
																<option value="<?=$pulledPersonnel['personnelId'];?>"><?=$pulledPersonnel['nameSurname'];?></option>
															<?php 
																}
																?>
															</select>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="box-body">
														<div class="form-group">
															<label><?=$lang["Lender"];?></label>
															<input readonly="readonly" type="text" class="form-control" placeholder="<?=$_SESSION['enteredPerson'];?>">
															<input  hidden="hidden" name="lenderId" id="lenderId" type="text" value="<?=$_SESSION['personnelId'];?>">
															<label><?=$lang["Loan_Date"];?>:</label>
															<div class="input-group date">
																<div class="input-group-addon">
																	<i class="fa fa-calendar"></i>
																</div>
																<input type="text" name="loanDate"class="form-control pull-right" id="datepicker3" value="<?=$tarih = date("d-m-Y");?>">
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-2">
													<div class="box-body">
														<div class="form-group">
															<label><?=$lang["Please_check_once_more"];?></label>
															<input type="submit" class="form-control btn btn-block btn-warning btn-lg" value="<?=$lang["Save"];?>">
														</div>
													</div>
												</div>
											</form>
										</div>
										<div class="tab-pane active" id="tab_2">
											<form role="form" method="post" enctype="multipart/form-data" action="loans-add.php">
												<div class="col-md-4">
													<div class="box-body">
														<div class="form-group">
															<label><?=$lang["Computer"];?></label>
															<input  hidden="hidden" name="type" id="type" type="text" value="1">
															<select id="productId" name="productId" class="form-control select2" style="width: 100%;">
																<option selected="selected"><?=$lang["Please_Select_a_Computer_From_List"];?></option>
															<?php
																$pullComputer=$connection->query("select * from computers WHERE computerStatus='0' ")->fetchAll(PDO::FETCH_ASSOC);
																foreach ($pullComputer as $pulledComputer)
																{
																?>
																<option value="<?=$pulledComputer['computerId'];?>">
																	<?=$pulledComputer['manufacturer'];?> | <?=$pulledComputer['model'];?> | <?=$pulledComputer['serialNo'];?>
																</option>
															<?php 
																}
																?>
															</select>
															<label><?=$lang["Loaner"];?></label>
															<select name="loanerId" id="loanerId"class="form-control select2" style="width: 100%;">
																<option selected="selected"><?=$lang["Select_Personnel"];?></option>
																<?php
																	$pullPersonnel=$connection->query("select * from personnel")->fetchAll(PDO::FETCH_ASSOC);
																	foreach ($pullPersonnel as $pulledPersonnel)
																	{
																	?>
																<option value="<?=$pulledPersonnel['personnelId'];?>"><?=$pulledPersonnel['nameSurname'];?></option>
																<?php } ?>
															</select>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="box-body">
														<div class="form-group">
															<label><?=$lang["Lender"];?></label>
															<input readonly="readonly" type="text" class="form-control" placeholder="<?=$_SESSION['enteredPerson'];?>">
															<input  hidden="hidden" name="lenderId" id="lenderId" type="text" value="<?=$_SESSION['personnelId'];?>">
															<label><?=$lang["Loan_Date"];?>:</label>
															<div class="input-group date">
																<div class="input-group-addon">
																	<i class="fa fa-calendar"></i>
																</div>
																<input type="text" name="loanDate"class="form-control pull-right" id="datepicker" value="<?=$tarih = date("d-m-Y");?>">
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-2">
													<div class="box-body">
														<div class="form-group">
															<label><?=$lang["Please_check_once_more"];?></label>
															<input type="submit" class="form-control btn btn-block btn-warning btn-lg" value="<?=$lang["Save"];?>">
														</div>
													</div>
												</div>
											</form>
										</div>
										<div class="tab-pane" id="tab_3">
											<form role="form" method="post" enctype="multipart/form-data" action="loans-add.php">
												<div class="col-md-4">
													<div class="box-body">
														<div class="form-group">
															<label><?=$lang["Key"];?></label>
															<input  hidden="hidden" name="type" id="type" type="text" value="2">
															<select name="productId" class="form-control select2" style="width: 100%;">
																	<option selected="selected"><?=$lang["Please_Select_a_Key_From_List"];?></option>
															<?php
																$pullKey=$connection->query("select * from roomkeys WHERE keyStatus='0' ")->fetchAll(PDO::FETCH_ASSOC);
																foreach ($pullKey as $pulledKey)
																{
																?>
																	<option value="<?=$pulledKey['keyId'];?>">
																		<?=$pulledKey['keyNumber'];?> | <?=$pulledKey['keyContent'];?>
																	</option>
															<?php
																}
																?>
															</select>
															<label><?=$lang["Loaner"];?></label>
															<select name="loanerId" id="loanerId" class="form-control select2" style="width: 100%;">
																	<option selected="selected"><?=$lang["Select_Personnel"];?></option>
															<?php
																$pullPersonnel=$connection->query("select * from personnel")->fetchAll(PDO::FETCH_ASSOC);
																foreach ($pullPersonnel as $pulledPersonnel)
																{
																?>
																	<option value="<?=$pulledPersonnel['personnelId'];?>"><?=$pulledPersonnel['nameSurname'];?></option>
																<?php 
																}
																?>
															</select>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="box-body">
														<div class="form-group">
															<label><?=$lang["Lender"];?></label>
															<input readonly="readonly" type="text" class="form-control" placeholder="<?=$_SESSION['enteredPerson'];?>">
															<input  hidden="hidden" name="lenderId" id="lenderId" type="text" value="<?=$_SESSION['personnelId'];?>">
															<label><?=$lang["Loan_Date"];?>:</label>
															<div class="input-group date">
																<div class="input-group-addon">
																	<i class="fa fa-calendar"></i>
																</div>
																<input type="text" name="loanDate"class="form-control pull-right" id="datepicker2" value="<?=$tarih = date("d-m-Y");?>">
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-2">
													<div class="box-body">
														<div class="form-group">
															<label><?=$lang["Please_check_once_more"];?></label>
															<input type="submit" class="form-control btn btn-block btn-warning btn-lg" value="<?=$lang["Save"];?>">
														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-body">
						<table id="example1" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th><?=$lang["Type"];?></th>
									<th><?=$lang["Name"];?>/<?=$lang["Manufacturer"];?>/<?=$lang["Model"];?></th>
									<th><?=$lang["Loan_Date"];?></th>
									<th><?=$lang["Lender"];?></th>
									<th><?=$lang["Loaner"];?></th>
									<th><?=$lang["Return_Date"];?></th>
									<th><?=$lang["Return_Accepter"];?></th>
									<th><?=$lang["Operations"];?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$pullData=$connection->query("select * from loans")->fetchAll(PDO::FETCH_ASSOC);
									foreach ($pullData as $pulledData)
									{
									?>
								<tr>
									<td><?=$pulledData['type'];?></td>
									<td>
									<?php
										if($pulledData['type']==0)
										{
											$pullBook=$connection->query("select * from books where bookId=".$pulledData['loanId']."")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($pullBook as $pulledBook)
											{
												echo $pulledBook['bookName'];
											}
										}
										else if($pulledData['type']==1)
										{
											$pullComputer=$connection->query("select * from computers where computerId=".$pulledData['loanId']."")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($pullComputer as $pulledComputer)
											{
												echo $pulledComputer['manufacturer'].' '.$pulledComputer['model'];
											}
										}
										else if($pulledData['type']==2)
										{
											$pullKey=$connection->query("select * from roomkeys where keyId=".$pulledData['loanId']."")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($pullKey as $pulledKey)
											{
												echo $pulledKey['keyNumber'];
											}
										}
										?>
									</td>
									<td><?=(new DateTime($pulledData['loanDate']))->format('d-m-Y');?></td>
									<td>
										<?php
											$pullPersonnel=$connection->query("select * from personnel where personnelId=".$pulledData['lenderId']."")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($pullPersonnel as $pulledPersonnel)
											{
											echo $pulledPersonnel['nameSurname'];
											}
											?>
									</td>
									<td>
										<?php
											$pullPersonnel=$connection->query("select * from personnel where personnelId=".$pulledData['loanerId']."")->fetchAll(PDO::FETCH_ASSOC);
											foreach ($pullPersonnel as $pulledPersonnel)
											{
											echo $pulledPersonnel['nameSurname'];
											}
											?>
									</td>
									<td>
										<?php
											if($pulledData['returnDate']=="0000-00-00"){echo 'İade Edilmedi';}
											else{echo (new DateTime($pulledData['returnDate']))->format('d-m-Y');}
											?>
									</td>
									<td>
										<?php
											if($pulledData['returnAccepterId']==NULL)
											{
												echo $lang["Not_Returned_Yet"];
											
											}
											else
											{
												$pullPersonnel=$connection->query("select * from personnel where personnelId=".$pulledData['returnAccepterId']."")->fetchAll(PDO::FETCH_ASSOC);
												foreach ($pullPersonnel as $pulledPersonnel)
												{
													echo $pulledPersonnel['nameSurname'];
												}
											
											}
											?>
									</td>
									<td>
										<span class="button-group">
										<a href="loans-return.php?productId=<?=$pulledData['loanId'];?>&type=<?=$pulledData['type'];?>&loanId=<?=$pulledData['loanId'];?>" onclick="alert('<?=$lang["Date_and_Repossesser_will_be_assigned_automatically"];?>')"class="fa fa-check"></a>
										<a href="loans-edit.php?productId=<?=$pulledData['loanId'];?>" class="fa fa-pencil"></a>
										<a href="loans-delete.php?productId=<?=$pulledData['loanId'];?>&type=<?=$pulledData['type'];?>&loanId=<?=$pulledData['loanId'];?>" onclick="alert('<?=$lang["This_loan_information_for_this_product_will_be_deleted_from_the_system"];?>')" class="fa fa-trash"></a>
										</span>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<?php
							if(isset($_GET['returned']))
							{
								if($_GET['returned']!=NULL)
								{
									echo "<script>alert('"+$lang["You_cannot_return_a_returned_product_again"]+"');</script>";
								}
							}
							
							if(isset($_GET['notReturned']))
							{
								if($_GET['notReturned']!=NULL)
								{
									echo "<script>alert('"+$lang["Loan_information_of_the_non_returned_product_cannot_be_deleted_Please_return_it_first"]+"');</script>";
								}
							}
						?>
					</div>
					
				</div>
				
			</div>
			
		</div>
		
</section>

</div>


<?php 
	include("footer.php");
	include("scripts.php"); 
?>
<?php	} ?>
