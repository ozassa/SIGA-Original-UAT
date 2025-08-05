<?php

$date = $_POST['date'];
$i_Contrato_Resseguro = $_POST['i_Contrato_Resseguro'];
$idInform = $_POST['idInform'];

function Convert_Data($data)
{
	if (strstr($data, "/")) {//verifica se tem a barra /
		$d = explode("/", $data);//tira a barra
		$invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mês etc...
		return $invert_data;
	} elseif (strstr($data, "-")) {
		$d = explode("-", $data);
		$invert_data = "$d[2]/$d[1]/$d[0]";
		return $invert_data;
	}

}


function getTimeStamp($date, $dias = 0)
{
	if (preg_match('@^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})@', $date, $res)) {
		return mktime(0, 0, 0, $res[2], $res[1] + $dias, $res[3]);
	}
	// ano com 2 digitos
	if (preg_match('@^([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})@', $date, $res)) {
		return mktime(0, 0, 0, $res[2], $res[1] + $dias, $res[3] + 2000);
	}
}

if (!function_exists('ymd2dmy')) {
	// converte a data de yyyy-mm-dd para dd/mm/yyyy
	function ymd2dmy($d)
	{
		if (preg_match("@([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})@", $d, $v)) {
			return "$v[3]/$v[2]/$v[1]";
		}
		return $d;
	}
}


/*
 sobre a data de aceitacao:
1 - nao pode ser antes da data do pagamento
2 - tem q ser uma data do mes atual..
3 - pode ser no maximo até 15 dias atras
*/
$dataH = date("d/m/Y"); //formata data de hoje
$dateHoje = getTimeStamp($dataH);
if (preg_match('@^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})@', $dataH, $res)) {
	$dia = $res[1];
	$mes = $res[2];
	$ano = $res[3];
}

// Casos permitidos
// 1-) dtPg = hj e dtAceit = hj
// 2-) dtPg = hj e dtAceit = amanhã
// 3-) dtPg = mês passd. e dtAceit = hj
// Casos não permitidos:
// 1-) dtPg = hj e dtAceit = ontem (ñ)
// 2-) dtPg = mês passd.

$dateB = getTimeStamp($date); // data backoffice

// if ($dateB < $dateHoje - (15 * 24 * 3600)){ // nao pode ser antes de 15 dias atras
//   $msgData = "Data de aceitação inválida";
// } else {
$ok = true;
$forward = "error";
odbc_autocommit($db, false);
$query = "SELECT idRegion, name, pgOk, i_Seg, idAnt, dateFinanc FROM Inform WHERE id = ?";
$cur = odbc_prepare($db, $query);
odbc_execute($cur, [$idInform]);

odbc_fetch_row($cur);

$idRegion = odbc_result($cur, 1);
$name = odbc_result($cur, 2);
$pgOk = odbc_result($cur, 3);
$idSeg = odbc_result($cur, 4);
$idAnt = odbc_result($cur, 5);
$datePag = ymd2dmy(odbc_result($cur, 6));
if (preg_match('@^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})@', $date, $res)) {
	$diaB = $res[1];
	$mesB = $res[2];
	$anoB = $res[3];
}

odbc_free_result($cur);

/*
   Data alteração: 20/04/2009
   Alterado por: Elias Vaz
   Motivo: Alterar período de aceite para 45 anteriores à data do recebimento do documento aceito.
 */
// Neste trecho verifa-se a quantidade de dias após a data de Aceite

$cargo = isset($role['executive']) ? $role['executive'] : false; //retorna verdadeiro ou falso (0,1)

$totaldias = (($dateHoje - $dateB) / 3600) / 24;

//if (($totaldias > 45) && ($cargo == 0)){
//	  $msgData = "Data de aceitação inválida. Data retroativa maior do que 45 dias.";
//	  return;
//  }

/*
   if($anoB != $ano || $mesB != $mes){ // tem q ser no mesmo mes
	 $msgData = "Data de aceitação inválida";
	 return;
   }
  */
/*
	 Alterado por Tiago V N - Elumini 31/08/2005
   */
/*
   if($dateB < getTimeStamp($datePag) - (15 * 24 * 3600)){ // credo!!!! (dateFinanc - 15)
	 $msgData = "Data de aceitação inválida";
	 return;
   }
   */

$r = 0;
//if($mot == "Aceita"){
if ($idAnt) {
	//Query antiga (até 07/07) Andréa---------------------------------------------------------------
	// "select endValidity from Inform where id=$idAnt and endValidity <= '".
	//	     date("Y-m-d 00:00:00.000", $dateB). "'";
	//----------------------------------------------------------------------------------------------

	//Alterado por Tiago V N - 07/11/2005
	$query = "SELECT endValidity, state FROM Inform WHERE id = ? AND endValidity >= ?";
	$x = odbc_prepare($db, $query);
	odbc_execute($x, [$idAnt, date("Y-m-d 00:00:00.000", $dateB)]);


	odbc_fetch_row($x);

	if (odbc_result($x, 2) == "10") {
		if (odbc_result($x, 1) != "") {
			$data_errada = true;
		}
	}

	odbc_free_result($x);
	//if(odbc_fetch_row($x) ){ // a data nao está correta
	//$data_errada = true;
	//  }
}

$data_errada = isset($data_errada) ? $data_errada : false;

if (!$data_errada) {
	$qry = "SELECT CR.i_Contrato_Resseguro, CR.Desc_Contrato, CR.Inicio_Vigencia, CR.Fim_Vigencia
        FROM Contrato_Resseguro CR
        WHERE CR.i_Contrato_Resseguro = ? 
          AND ? BETWEEN CR.Inicio_Vigencia AND CR.Fim_Vigencia";

	$res = odbc_prepare($db, $qry);
	odbc_execute($res, [$i_Contrato_Resseguro, Convert_Data($date)]);


	$dtIni = substr(odbc_result($res, 'Inicio_Vigencia'), 0, 10);
	$dtFim = substr(odbc_result($res, 'Fim_Vigencia'), 0, 10);


	odbc_free_result($res);
	if ($dtIni != '') {


		$query1 = "UPDATE Inform SET
            dateAceit = ?,
            mailOk = ?,
            i_Contrato_Resseguro = ?
           WHERE id = ?";

		$r = odbc_prepare($db, $query1);
		odbc_execute($r, [date("Y-m-d 00:00:00.000", $dateB), 1, $i_Contrato_Resseguro, $idInform]);

		//echo $query1;
		if ($pgOk) { // mudou de status
			$querySelect = "SELECT id, fim FROM AnaliseInform WHERE idInform = ?";
			$stmtSelect = odbc_prepare($db, $querySelect);
			odbc_execute($stmtSelect, [$idInform]);

			if (odbc_fetch_row($stmtSelect)) {
				$id = odbc_result($stmtSelect, 1);
				$fim = odbc_result($stmtSelect, 2);
				if (!$fim) {
					$queryUpdate = "UPDATE AnaliseInform SET fim = GETDATE() WHERE id = ?";
					$stmtUpdate = odbc_prepare($db, $queryUpdate);
					odbc_execute($stmtUpdate, [$id]);
				}
			}
		}


	} else {
		$msgData = "Atenção! A data de início de vigência está fora do perído de vigência do contrato de resseguro.";
	}

} else {
	$msgData = "A data de aceitação deve ser maior que a data de vigência do Inform anterior.";
}
odbc_free_result($x);
//Criado Por Tiago V N - 26/10/2005
//Log Aceitação de Proposta
// Inserindo no Log
$sql = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) 
        VALUES (?, ?, ?, ?, ?)";
$stmtLog = odbc_prepare($db, $sql);
$logDate = date("Y-m-d");
$logTime = date("H:i:s");
if (odbc_execute($stmtLog, ['23', $userID, $idInform, $logDate, $logTime])) {
    // Obtendo o último ID inserido
    $sql_id = "SELECT @@IDENTITY AS id_Log";
    $stmtId = odbc_exec($db, $sql_id);
    $cur = odbc_result($stmtId, 1);

    // Inserindo no Log_Detalhes
    $sqlDetails = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) 
                   VALUES (?, ?, ?, ?)";
    $stmtDetails = odbc_prepare($db, $sqlDetails);
    odbc_execute($stmtDetails, [$cur, 'Aceita Prop', 'Aceita', 'Alteração']);
} else {
    $msg = "Erro no incluir do Log";
}

odbc_autocommit($db, true);

?>