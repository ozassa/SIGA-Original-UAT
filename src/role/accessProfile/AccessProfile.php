<?php 

	require_once ("../../../config.php");

	if (isset($_GET["comm"]) || isset($_REQUEST["comm"])) {
		if (isset($_GET["comm"])) {
			$comm = $_GET["comm"];
		} else {
			$comm = $_REQUEST["comm"];
		}
	} else {
		$comm = "index";
	}

	if($comm == "index" || $comm == ""){
		$page = "ListAccessProfile.php";
	} else if ($comm == "addAccessProfile") {
		$page = "AddAccessProfile.php";
	} else if ($comm == "new") {
		$page = "CadAccessProfile.php";
	} else if ($comm == "editAccessProfile") {
		$page = "AddAccessProfile.php";
	} else if ($comm == "alter") {
		$page = "CadAccessProfile.php";
	}

	require_once($page);
			
	require_once("../../../home.php"); 
?>