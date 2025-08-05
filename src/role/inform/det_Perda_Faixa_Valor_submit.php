<?php
function formatVlr($n_Valor)
{
	$n_Valor1 = str_replace('.', '', $n_Valor);
	$n_Valor2 = str_replace(',', '.', $n_Valor1);
	return $n_Valor2;
}

$idInform = $_POST['idInform'];
$i_Perda_Efetiva = $_POST['i_Perda_Efetiva'];

$Ano1 = $_POST['Ano1'];
$Ano2 = $_POST['Ano2'];
$Ano3 = $_POST['Ano3'];
$Ano4 = $_POST['Ano4'];

$clientes1 = formatVlr($_POST['clientes1']);
$valor1 = formatVlr($_POST['valor1']);
$clientes2 = formatVlr($_POST['clientes2']);
$valor2 = formatVlr($_POST['valor2']);
$clientes3 = formatVlr($_POST['clientes3']);
$valor3 = formatVlr($_POST['valor3']);
$clientes4 = formatVlr($_POST['clientes4']);
$valor4 = formatVlr($_POST['valor4']);




$i = 0;

$qry = "DELETE FROM Inform_Detalhamento_Perda_Efetiva WHERE i_Inform = ?";
$cur = odbc_prepare($db, $qry);
odbc_execute($cur, [$idInform]);


foreach ($i_Perda_Efetiva as $faixa) {
	$Perda_Efetiva[$i] = $faixa;
	if ($clientes1[$i] > 0 || $valor1[$i] > 0) {
		$qryDelete = "DELETE FROM Inform_Detalhamento_Perda_Efetiva WHERE i_Inform = ? AND Ano = ? AND i_Perda_Efetiva = ?";
		$stmtDelete = odbc_prepare($db, $qryDelete);
		odbc_execute($stmtDelete, [$idInform, $Ano1, $Perda_Efetiva[$i]]);

		$qryInsert = "INSERT INTO Inform_Detalhamento_Perda_Efetiva (i_Inform, i_Perda_Efetiva, Ano, n_Clientes, v_Valor) 
					  VALUES (?, ?, ?, ?, ?)";
		$stmtInsert = odbc_prepare($db, $qryInsert);
		odbc_execute($stmtInsert, [$idInform, $Perda_Efetiva[$i], $Ano1, $clientes1[$i], $valor1[$i]]);
	}

	if ($clientes2[$i] > 0 || $valor2[$i] > 0) {
		$qryDelete = "DELETE FROM Inform_Detalhamento_Perda_Efetiva WHERE i_Inform = ? AND Ano = ? AND i_Perda_Efetiva = ?";
		$stmtDelete = odbc_prepare($db, $qryDelete);
		odbc_execute($stmtDelete, [$idInform, $Ano2, $Perda_Efetiva[$i]]);

		$qryInsert = "INSERT INTO Inform_Detalhamento_Perda_Efetiva (i_Inform, i_Perda_Efetiva, Ano, n_Clientes, v_Valor) 
					  VALUES (?, ?, ?, ?, ?)";
		$stmtInsert = odbc_prepare($db, $qryInsert);
		odbc_execute($stmtInsert, [$idInform, $Perda_Efetiva[$i], $Ano2, $clientes2[$i], $valor2[$i]]);
	}

	if ($clientes3[$i] > 0 || $valor3[$i] > 0) {
		$qryDelete = "DELETE FROM Inform_Detalhamento_Perda_Efetiva WHERE i_Inform = ? AND Ano = ? AND i_Perda_Efetiva = ?";
		$stmtDelete = odbc_prepare($db, $qryDelete);
		odbc_execute($stmtDelete, [$idInform, $Ano3, $Perda_Efetiva[$i]]);

		$qryInsert = "INSERT INTO Inform_Detalhamento_Perda_Efetiva (i_Inform, i_Perda_Efetiva, Ano, n_Clientes, v_Valor) 
					  VALUES (?, ?, ?, ?, ?)";
		$stmtInsert = odbc_prepare($db, $qryInsert);
		odbc_execute($stmtInsert, [$idInform, $Perda_Efetiva[$i], $Ano3, $clientes3[$i], $valor3[$i]]);
	}

	if ($clientes4[$i] > 0 || $valor4[$i] > 0) {
		$qryDelete = "DELETE FROM Inform_Detalhamento_Perda_Efetiva WHERE i_Inform = ? AND Ano = ? AND i_Perda_Efetiva = ?";
		$stmtDelete = odbc_prepare($db, $qryDelete);
		odbc_execute($stmtDelete, [$idInform, $Ano4, $Perda_Efetiva[$i]]);

		$qryInsert = "INSERT INTO Inform_Detalhamento_Perda_Efetiva (i_Inform, i_Perda_Efetiva, Ano, n_Clientes, v_Valor) 
					  VALUES (?, ?, ?, ?, ?)";
		$stmtInsert = odbc_prepare($db, $qryInsert);
		odbc_execute($stmtInsert, [$idInform, $Perda_Efetiva[$i], $Ano4, $clientes4[$i], $valor4[$i]]);
	}




	$i++;


}


if ($hc_cliente == "N" && ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B')) {
	$novo_estatus = "2";
} else {
	if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') {
		$novo_estatus = "2";
	} else {
		$novo_estatus = "3";
	}
}

if ($i > 0) {
	$sql = "UPDATE Inform SET det_Perda_Efetiva_State = ? WHERE id = ?";
	$cur = odbc_prepare($db, $sql);
	odbc_execute($cur, [$novo_estatus, $idInform]);
}


?>