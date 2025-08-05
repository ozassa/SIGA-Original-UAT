<?php

require_once("../rolePrefix.php");

//extract($_REQUEST);
//error_log("Conteњdo de searchclient:");
//error_log(print_r($_REQUEST, true));



$log_query = "";
$idInform = isset($_REQUEST["idInform"]) ? $_REQUEST["idInform"] : false;

//require_once ("../../../../site/func/index.php");

$acao = isset($acao) ? $acao : false;
if (!$acao)
	$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : false;

if ($acao == "cancelar") {
	// Preparaчуo da query para atualizar o estado
	$sql = "UPDATE Inform SET state = ? WHERE id = ?";
	$stmt = odbc_prepare($db, $sql);
	odbc_execute($stmt, ['9', $idInform]);

	if ($stmt) {
		$log_query .= "UPDATE Inform SET state = 9 WHERE id = $idInform"; // Log simplificado
	}

	// Preparaчуo da query para inserir no log
	$sql = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) 
				VALUES (?, ?, ?, ?, ?)";
	$stmt = odbc_prepare($db, $sql);
	odbc_execute($stmt, [
		'6',
		$userID,
		$idInform,
		date("Y-m-d"),
		date("H:i:s")
	]);

	if ($stmt) {
		$sql_id = "SELECT @@IDENTITY AS id_Log";
		$result = odbc_exec($db, $sql_id);
		$cur = odbc_result($result, 1);

		$sql = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) 
					VALUES (?, ?, ?, ?)";
		$stmt = odbc_prepare($db, $sql);
		odbc_execute($stmt, [$cur, 'state', $status_at, 'Cancelamento']);

		if ($stmt) {
			$sql_id_detalhes = "SELECT @@IDENTITY AS id_detalhes";
			$result = odbc_exec($db, $sql_id_detalhes);
			$cur = odbc_result($result, 1);

			$sql = "INSERT INTO Log_Detalhes_Query (id_detalhes, query) 
						VALUES (?, ?)";
			$stmt = odbc_prepare($db, $sql);
			odbc_execute($stmt, [$cur, str_replace("'", "", $log_query)]);
		}
	}

	// Gera Notificaчуo de Cancelamento
	$geraNot = "SELECT name FROM Inform WHERE id = ?";
	$stmt = odbc_prepare($db, $geraNot);
	odbc_execute($stmt, [$idInform]);

	if (odbc_fetch_row($stmt)) {
		$clientR = odbc_result($stmt, 1);
		$notif->newInfCredito($userID, $clientR, $idInform, $db);
	}
}



$title = "Pesquisa de Clientes ";
$content = "../searchClient/interf/ViewClient.php";

require_once("../../../home.php");
?>