<?php
   session_start();
   // Alterado HICOM - 27/10/2004 (Gustavo)
	require_once ("../rolePrefix.php");
  	//$idInform = $field->getField ("idInform");
  	//$idNotification = $field->getField ("idNotification");
  	//if (!$idInform || !$idNotification) {
  	//  $title = "Erro : Informe inv�lido";
  	//  $content = "../../interf/Error.php";} else 
	
//	extract($_REQUEST);

	if ($comm == "view") {
		$title = "Propostas Emitidas e n�o Pagas";
		require_once ("view.php");
		$content = "../financ/interf/Financ.php";
	} else if ($comm == "voltar"){
		$title = "Notifica��es";
		$content = "../../../main.php";
	} else if ($comm == "done") {
		//if($mot != "Voltar")
      		require_once ("done.php");
      		$title = "Propostas Emitidas e n�o Pagas";
      		$content = "../financ/interf/Financ.php";
      		require_once ("view.php");
	} else if ($comm == "juros") {
		$title = "Solicita��o de Juros de Mora";
		$content = "../financ/interf/jurosMora.php";
	} else if ($comm == "confirmaSolicit") {
		require_once ("confirmaSolicit.php");
		$title = "Notifica��es";
		$content = "../../../main.php";
		// Alterado HICOM - 27/10/2004 (Gustavo)
  	} else if ($comm == "emitePa") {
		// require ("confirmaSolicit.php");
    		$title = "Parcela de Ajuste - C�lculo";
  		$content = '../dve/interf/calculaPa.php';
  	} else if ($comm == "Esusep") {
		$content = "../financ/interf/Esusep.php";
  		$title   = "Exporta��o Ap�lice/Endosso para SUSEP";
  	}

	require_once("../../../home.php");
?>