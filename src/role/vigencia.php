<?php
session_start();

$userID = $_SESSION['userID'];

//extract($_REQUEST);

if (!$idInform)
	$idInform = $_POST['idInform'];

$query = "SELECT dateAceit, idAnt, state, pvigencia, Periodo_Vigencia 
          FROM Inform 
          WHERE id = ?";
$r = odbc_prepare($db, $query);
odbc_execute($r, [$idInform]);

$dAceit = odbc_result($r, 1);
$idAnt = odbc_result($r, 2);
$state = odbc_result($r, 3);
//Alterado por Tiago V N - 23/09/2005
$vigencia = odbc_result($r, 4);
$Periodo_Vigencia = odbc_result($r, 'Periodo_Vigencia');

if ($vigencia == "") {
	$pvigencia = "1";
} else if ($vigencia == "1") {
	$pvigencia = "1";
} else {
	$pvigencia = "2";
}
// Trata-se de periodo em qtde de meses
if ($Periodo_Vigencia) {
	$pvigencia = $Periodo_Vigencia;
	$anox = ($pvigencia > 12 ? 2 : 1);
}


list($ano, $mes, $dia) = explode('-', $dAceit);
$dia = substr($dia, 0, 2);

$dInicio = "";
$dFim = "";

//  if ($dia <= 15)
//    $dia = 15;
//  else {
//    $mes = $mes + 1;
//    $dia = 1;
//  }

$dInicio = date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano));
//$dFim = date ("Y-m-d", mktime (0,0,0, $mes,  $dia - 1, $ano + 1));
//$dFim = date ("Y-m-d", mktime (0,0,0, $mes,  $dia - 1, $ano + 2));
//Alterado por Tiago V N - Elumini - 19/09/2005

//$dFim = date ("Y-m-d", mktime (0,0,0, $mes,  $dia - 1, $ano + $pvigencia));
$anox = isset($anox) ? $anox : false;
$dFim = date("Y-m-d", mktime(0, 0, 0, $mes, $dia - 1, $ano + ($anox ? $anox : $pvigencia)));

//  $dOntem = date ("Y-m-d", mktime (0,0,0, date('m'),  date("d") - 1 ,  date("Y")));

//  echo "dInicio [$dInicio] dFim [$dFim], $idSeg";


odbc_free_result($r);
$query = "UPDATE Proposta
          SET d_Inicio = ?, d_Fim = ?, d_Aceitacao = ?
          WHERE i_Seg = ? AND
                n_Prop IN (SELECT MAX(n_Prop) FROM Proposta WHERE i_Seg = ?)";
$r = odbc_prepare($dbSisSeg, $query);
odbc_execute($r, [$dInicio, $dFim, $dAceit, $idSeg, $idSeg]);


if ($r) {
	odbc_free_result($r);
	$query = "UPDATE Base_Calculo
          SET d_Ini_Vig = ?, d_Fim_Vig = ?, d_Aceitacao = ?
          WHERE i_Seg = ? AND
                n_Prop IN (SELECT MAX(n_Prop) FROM Proposta WHERE i_Seg = ?)";
	$r = odbc_prepare($dbSisSeg, $query);
	odbc_execute($r, [$dInicio, $dFim, $dAceit, $idSeg, $idSeg]);

	if (!$r) {
		$msg = "Não consegui acertar a vigência na Base de Cálculo";
		odbc_free_result($r);
	}

	$query = "UPDATE Inform
          SET startValidity = ?, endValidity = ?, dateEmissionP = getDate()
          WHERE id = ?";
	$r = odbc_prepare($db, $query);
	odbc_execute($r, [$dInicio, $dFim, $idInform]);

	if (!$r) {
		$msg = "Não consegui acertar as datas de vigência no Inform";
		odbc_free_result($r);
	}
} else {
	odbc_free_result($r);
	$msg = "Não consegui acertar a vigência na Proposta";
}

odbc_free_result($r);
$query = "SELECT id FROM Importer WHERE idInform = ? AND state = 1 AND hold <> 0";
$r = odbc_prepare($db, $query);
odbc_execute($r, [$idInform]);

while (odbc_fetch_row($r)) {
	$id = odbc_result($r, 1);
	$x = odbc_exec($db, "select * from AnaliseImporter where idImporter=$id and fim is null");
	if (!odbc_fetch_row($x)) {
		if ($state == 10) {
			$x = odbc_exec(
				$db,
				"insert into AnaliseImporter (idImporter, inicio) values ($id, getdate())"
			);
		}
	}
}
odbc_free_result($r);

// Selecionar id do Importer
$querySelectImporter = "SELECT id FROM Importer WHERE idInform = ? AND state = 1 AND hold <> 0";
$r = odbc_prepare($db, $querySelectImporter);
odbc_execute($r, [$idInform]);

if (odbc_fetch_row($r)) {
	$n = 1;
	odbc_free_result($r);

	// Atualizar hold do Importer
	$queryUpdateHold = "UPDATE Importer SET hold = 0 WHERE idInform = ? AND state = 1 AND hold <> 0";
	$updateHold = odbc_prepare($db, $queryUpdateHold);
	odbc_execute($updateHold, [$idInform]);
} else {
	$n = 0;
	odbc_free_result($r);
}

// Ajustar as datas de validade dos Importer
$queryUpdateValidity = "UPDATE Importer SET validityDate = ? WHERE idInform = ? AND (state = 6 OR state = 5)";
$updateValidity = odbc_prepare($db, $queryUpdateValidity);
odbc_execute($updateValidity, [$dInicio, $idInform]);

// Selecionar nome do Inform
$querySelectName = "SELECT name FROM Inform WHERE id = ?";
$cur = odbc_prepare($db, $querySelectName);
odbc_execute($cur, [$idInform]);

$nameCl = 'NONO';
if (odbc_fetch_row($cur)) {
	$nameCl = odbc_result($cur, 'name');
}
odbc_free_result($cur);

// Verificar renovação ou notificação
if ($idAnt > 0) { // renovação
	$notif->clientChangeImporter($userID, $nameCl, $idInform, $db, 10, "i", 0, 1);
} else if ($n > 0) {
	$notif->clientChangeImporter($userID, $nameCl, $idInform, $db, 10, "i", 0);
}
