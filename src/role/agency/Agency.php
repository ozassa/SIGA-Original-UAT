<?php 

	require_once ("../../../config.php");

	$comm = isset($_GET["comm"]) ? $_GET["comm"] : "index";
	$userID = $_SESSION['userID'];

	if($comm == "index"){
		$page = "ListAgency.php";
	} 

	require_once($page);
			
	require_once("../../../home.php"); 
?>