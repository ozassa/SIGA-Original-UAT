<?php

 session_start();
 $userID = $_SESSION['userID'];  
 //extract($_REQUEST);
    
require_once("../rolePrefix.php");

if ($comm == "view") {
	$title = "Aceita&ccedil;&atilde;o de Propostas"; 
	require_once("view.php");
	$content = "../backoffice/interf/BackOffice.php";
} else if ($comm == "back") {
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";
} else if ($comm == "done") {
	$title = "Aceita&ccedil;&atilde;o de Propostas";
	require_once("done.php");
	$content = "../backoffice/interf/BackOffice.php";
	require_once("view.php");
} else if ($comm == "DateBack") {
	require_once("DateBack.php");
	$title = "Aceita&ccedil;&atilde;o de Propostas";
	$content = "../backoffice/interf/BackOffice.php";
	require_once("view.php");
} else if ($comm == "juros") {
	$title = "Aviso de Solicita&ccedil;&atilde;o de Juros de Mora";
	$content = "../backoffice/interf/jurosRecebido.php";
} else if ($comm == "confirmaSolicit") {
	require_once("confirmaSolicit.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";
} else if ($comm == "concluiJuros") {
	require_once("concluiJuros.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";
} else if ($comm == "recebimento") {
	$title = "Confirma&ccedil;&atilde;o de Pagamento da fatura de Juros de Mora";
	$content = "../backoffice/interf/recebJuros.php";
}

require_once("../../../home.php");
?>