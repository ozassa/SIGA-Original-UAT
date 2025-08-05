<?php 

 session_start();
 $userID = $_SESSION['userID'];  
 //extract($_REQUEST);
		
require_once ("../rolePrefix.php");

$idInform = $field->getField ("idInform");
$idNotification = $field->getField ("idNotification");
$nomeSegurado = odbc_result(odbc_exec($db,'select name from Inform where id = '.$idInform),'name');

if (!$idInform || !$idNotification) {
  	$title = "Erro : Informe inválido";
  	$content = "../../interf/Error.php";
}else if ($comm == "view") {
  	require_once ("view.php");
  	$title = "Solicitação de Tarifação: ".$nomeSegurado;
  	$content = "../tariffer/interf/Tariff.php";
} else if ($comm == "done") {
  	if ($field->getField("mot") != "Voltar")
    		require_once ("done.php");

  	if ($forward == "success") {
    		$title = "Notificações";
     		$content = "../../../main.php";
  	} else {
		$title = "Solicitação de Tarifação ".$nomeSegurado; 
    		$content = "../tariffer/interf/Tariff.php";
    		require_once ("view.php");
  	}
}

require_once("../../../home.php");
?>