<?php 

	require_once ("../../../config.php");

	$comm = isset($_GET["comm"]) ? $_GET["comm"] : "index";
	$userID = $_SESSION['userID'];

	if($comm == "index"){
		$page = "ListReport.php";
	} elseif ($comm == "policyReport") {
		$page = "ListPolicyReport.php";
	} elseif ($comm == "policyReportDetail") {
		$page = "ListPolicyReportDetail.php";
	}	elseif ($comm == "fullPolicyReport") {
		$page = "ListFullPolicyReport.php";
	} elseif ($comm == "fullPolicyReportDetail") {
		$page = "ListFullPolicyReport.php";
	}

	require_once($page);
			
	require_once("../../../home.php"); 
?>