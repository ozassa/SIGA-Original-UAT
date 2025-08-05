<?php 

require_once ("../../../config.php");

$comm = isset($_GET["comm"]) ? $_GET["comm"] : "index";


if($comm == "index"){
	$page = "CadModule.php";
} elseif($comm == "ordem"){
	$page = "OrderModule.php";
} elseif($comm == "cad_ordem"){
	$page = "InserirOrdemModule.php";
} elseif($comm == "cad_mod"){
	$page = "InserirModule.php";
} elseif($comm == "mod_edit"){
	$page = "EditModule.php";
} elseif($comm == "exc_mod"){
	$page = "ExcModule.php";
} elseif($comm == "relacionamento"){
	$page = "RelacionaModule.php";
}  elseif($comm == "insere_relac"){
	$page = "InserirRelac.php";
}




require_once($page);
		
require_once("../../../home.php"); 
?>