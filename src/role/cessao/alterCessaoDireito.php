<?php
	$comm = isset($_REQUEST['comm']) ? $_REQUEST['comm'] : false;
	$id_Cessao = isset($_REQUEST['id_Cessao']) ? $_REQUEST['id_Cessao'] : false;

	if ($comm) {
		$data = date("Y/m/d H:i:s");

		if ($comm == 'recusaCessaoDireito') {
			// Preparar o SQL usando parâmetros
			$sql = "UPDATE CDBB SET status = 3, dateCancel = ?, d_Aceite_Cancelamento_Banco = ? WHERE id = ?";
			$stmt = odbc_prepare($db, $sql);
			// Executar o SQL com os dados
			odbc_execute($stmt, array($data, $data, $id_Cessao));
		} else {
			// Preparar o SQL usando parâmetros
			$sql = "UPDATE CDBB SET d_Aceite_Banco = ? WHERE id = ?";
			$stmt = odbc_prepare($db, $sql);
			// Executar o SQL com os dados
			odbc_execute($stmt, array($data, $id_Cessao));
		}
	}

	// Log Certificado Digital
	// $id_Parametro = '10030';
	// require_once("../logCertificado.php");

	$location = "Location: ../cessao/Cessao.php?comm=emiteCessaoDireito";
	header($location);
?>
