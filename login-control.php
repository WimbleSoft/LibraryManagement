<?php

session_start();
include("database.php");
$userName	= $_POST['userName'];
$passWord 	= md5($_POST['passWord']);
	if ((!$userName =="") and (!$passWord =="")) {
		
		$sql = $connection->prepare("select * from personnel where userName='$userName' and passWord='$passWord' ");
		$query = $connection->query("select * from personnel where userName='$userName' and passWord='$passWord'  ")->fetchAll(PDO::FETCH_ASSOC);
		$sql->execute();
		
		
		if($sql->rowCount()){
			$_SESSION["login"] = TRUE;
		
			foreach ($query as $pulledData)
			{
			$_SESSION["userName"] = $pulledData['userName'];
			$_SESSION["personnelId"] = $pulledData['personnelId'];
			}
			
			@header ("Location: index.php");
		}
		else {
			@header ("Location: login.php");
		}
	} else {
		@header ("Location: login.php");
	}
	
ob_end_flush();
?>