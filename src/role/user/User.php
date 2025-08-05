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
		$page = "ListUser.php";
	} else if ($comm == "addUser") {
		$page = "AddUser.php";
	} else if ($comm == "new") {
		$page = "CadUser.php";
	} else if ($comm == "editUser") {
		$page = "AddUser.php";
	} else if ($comm == "alter") {
		$page = "CadUser.php";
	}else if ($comm == "delUser") {
		$page = "DelUser.php";
	}

	require_once($page);
			
	require_once("../../../home.php"); 
?>