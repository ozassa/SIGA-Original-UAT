<?php 

require_once ("../rolePrefix.php");

$sql = "Insert Into Inform (idInsured) values ('$idInsured')";

if (odbc_exec($db, $sql)){
	$notif->doneRole($idNotication, $db);
	$title = "Notificações";
	$content = "../../../main.php";
    
}else{
	$msg = "Erro em gerar novo informe.";
	$content = "../../../main.php";
    
}

require_once("../../../home.php");
?>