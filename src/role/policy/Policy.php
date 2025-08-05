<?php
session_start();
$userID = $_SESSION['userID'];
// extract($_REQUEST);


if (isset($log_query)) {
	$log_query .= "";
} else {
	$log_query = "";
}
ini_set('max_execution_time', 600);

function SomarData($data, $Operacao, $dias, $meses, $ano)
{
	//passe a data no formato dd/mm/yyyy 
	$data = explode("/", $data);

	if ($Operacao == '+') {
		$newData = date("d/m/Y", mktime(0, 0, 0, $data[1] + $meses, $data[0] + $dias, $data[2] + $ano));
	} else {
		$newData = date("d/m/Y", mktime(0, 0, 0, $data[1] - $meses, $data[0] - $dias, $data[2] - $ano));
	}

	return $newData;
}

function getTimeStamp($date)
{
	if (preg_match('@^([0-9]{4})-([0-9]{2})-([0-9]{2})@', $date, $res)) {
		return mktime(0, 0, 0, $res[2], $res[3], $res[1]);
	}
}

function faltam($fim, $dias, $emission)
{
	$secs = ($dias - 1) * 24 * 3600;

	return getTimeStamp($fim) - getTimeStamp($emission) <= $secs;
}

require_once("../rolePrefix.php");

$idInform = isset($_REQUEST["idInform"]) ? $_REQUEST["idInform"] : 0;
$password = isset($_REQUEST["password"]) ? $_REQUEST["password"] : 0;
$usuario = isset($_REQUEST["usuario"]) ? $_REQUEST["usuario"] : 0;
$comm = $_REQUEST['comm'];

 if (!preg_match('/^[a-zA-Z0-9_&=]+$/', $comm)) {
       
        //die('Input inválido!');
    }

     $comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');

$idNotification = $field->getField("idNotification");

if (count($_POST) > 2) {
	// armazena dados do log
	$idImporter = isset($_REQUEST['idImporter']) ? $_REQUEST['idImporter'] : 0;
	$tem = $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 1, '', 'Inform', '');
	$tem1 = $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 1, '', 'Importer', $idImporter);
}

if ($comm == "back") {
	$title = "Notificações";
	$content = "../../../main.php";
} else if ($comm == "cancela") {
	require_once("cancela.php");
	$title = "Notificações";
	$content = "../../../main.php";
} else if ($comm == "view") {
	$title = "Emitir Ap&oacute;lice";
	$content = "../policy/interf/inputDate.php";
} else if ($comm == "gravaData") {
	$title = "Emitir Ap&oacute;lice";
	$content = "../policy/interf/issuePolicy.php";
} else if ($comm == "emite") {

	//Alterado por Tiago V N - Elumini - 31/08/2005
	$query = "SELECT state FROM Inform WHERE state < ? AND id = ?";
	$cur = odbc_prepare($db, $query);
	odbc_execute($cur, [9, $idInform]);

	if (odbc_fetch_row($cur)) {
		odbc_free_result($cur);
		$query = "SELECT idAnt, dateEmission, GetDate() FROM Inform WHERE id = ?";
		$r = odbc_prepare($db, $query);
		odbc_execute($r, [$idInform]);

		if (odbc_fetch_row($r)) {
			$idAnt = odbc_result($r, 1);
			$dateEmission = odbc_result($r, 2);
			$DataEmissaoApolice = odbc_result($r, 3);
			odbc_free_result($r);
			if ($idAnt) {
				$query = "SELECT endValidity FROM Inform WHERE id = ? AND state = ?";
				$stmt = odbc_prepare($db, $query);
				odbc_execute($stmt, [$idAnt, 10]);
				$finalVigencia = odbc_result($stmt, 1);
				odbc_free_result($stmt);
			}
		}

		// 2018/09/11 - AIP: SR220163 - Validação de "30 dias antes do fim de vigência" foi desativada
		//If($idAnt > 0 && !faltam($finalVigencia, 30, $DataEmissaoApolice)){
		//	$msgData = "A apólice só pode ser emitida com menos de 30 dias antes do fim de vigência da apólice renovada.";
		//	$title = "Emitir Apólice";
		//	$content = "../policy/interf/inputDate.php";
		//}else{
		$userID = $_SESSION['userID'];

		require_once("segProp.php");   	// verificar		
		require_once("../vigencia.php"); // verifica			   	   
		require_once("view.php"); // verificar 	

		if ($return_flag) {
			//require_once ("../rolePrefix.php");
			$msg .= $erroq;
			$title = "Notificações";
			$content = "../../../main.php";
		} else {
			require_once("done.php");
			$title = "Apólice Emitida";
			$content = "../policy/interf/Policy.php";
		}
		//}

		//Criado Por Tiago V N - 26/10/2005
		//Log Emissão de Apólice
		$sql = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) VALUES (?, ?, ?, ?, ?)";
		$result = odbc_prepare($db, $sql);
		odbc_execute($result, [
			'13',
			$userID,
			$idInform,
			date("Y") . "-" . date("m") . "-" . date("d"),
			date("H") . ":" . date("i") . ":" . date("s")
		]);


		if ($result) {
			odbc_free_result($result);
			$sql_id = "SELECT @@IDENTITY AS 'id_Log'";

			$cur = odbc_result(odbc_exec($db, $sql_id), 1);

			$sql = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) VALUES (?, ?, ?, ?)";
			$rs = odbc_prepare($db, $sql);
			odbc_execute($rs, [
				$cur,
				'Emitir Apólice',
				'Apólice',
				'Alteração'
			]);

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//CRIADO POR WAGNER
			// ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			if ($rs) {
				odbc_free_result($rs);
				$sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
				$cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);

				$sql = "INSERT INTO Log_Detalhes_Query (id_detalhes, query) VALUES (?, ?)";
				$result = odbc_prepare($db, $sql);
				odbc_execute($result, [
					$cur,
					str_replace("'", "", $log_query)
				]);

			}//fim if
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		} else {
			$msg = "Erro no incluir do Log";
		}//Log
	} else {
		$msg = "Ap&oacute;lice j&aacute; Emitida";
		$title = "Notificações";
		$content = "../../../main.php";
	}

	//$title = "Apólice Emitida";
	//$content = "../policy/interf/Policy.php";

} else if ($comm == "emitec") {
	$qry = "SELECT a.id, a.name, c.login 
        FROM Role a
        INNER JOIN UserRole b ON b.idRole = a.id
        INNER JOIN Users c ON c.id = b.idUser
        WHERE c.id = ? AND c.perfil = ?
        ORDER BY a.name, c.login";

	$cur = odbc_prepare($db, $qry);
	odbc_execute($cur, [$_SESSION['userID'], $_SESSION['pefil']]);

	while (odbc_fetch_row($cur)) {
		$x = $x + 1;
		$name = odbc_result($cur, 'name');
		$id = odbc_result($cur, 'id');
		$role[$name] = $id . '<br>';
	}
	odbc_free_result($cur);
	if ($role["liberaApolice"]) {
		//Alterado por Tiago V N - Elumini - 31/08/2005
		$query = "SELECT state FROM Inform WHERE state < ? AND id = ?";
		$cur = odbc_prepare($db, $query);
		odbc_execute($cur, [9, $idInform]);

		if (odbc_fetch_row($cur)) {
			odbc_free_result($cur);
			$query = "SELECT idAnt, dateEmission, GetDate() FROM Inform WHERE id = ?";
			$r = odbc_prepare($db, $query);
			odbc_execute($r, [$idInform]);

			if (odbc_fetch_row($r)) {
				$idAnt = odbc_result($r, 1);
				$dateEmission = odbc_result($r, 2);
				$DataEmissaoApolice = odbc_result($r, 3);

				odbc_free_result($r);
				if ($idAnt) {
					$query = "SELECT endValidity FROM Inform WHERE state = ? AND id = ?";
					$stmt = odbc_prepare($db, $query);
					odbc_execute($stmt, [10, $idAnt]);
					$finalVigencia = odbc_result($stmt, 1);
					odbc_free_result($stmt);
				}
			}

			if ($idAnt > 0 && !faltam($finalVigencia, 30, $dateEmission)) {
				$msgData = "A apólice só pode ser emitida com menos de 30 dias antes do fim de vigência da apólice renovada.";
				$title = "Emitir Apólice";
				$content = "../policy/interf/inputDate.php";
			} else {
				require_once("segProp.php");
				require_once("../vigencia.php");
				require_once("view.php");

				if ($return_flag) {
					//require_once ("../rolePrefix.php");
					$title = "Notificações";
					$content = "../../../main.php";
				} else {
					require_once("done.php");
					$title = "Apólice Emitida";
					$content = "../policy/interf/Policy.php";
				}
			}

			//Criado Por Tiago V N - 26/10/2005
			//Log Emissão de Apólice
			$sql = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) VALUES (?, ?, ?, ?, ?)";
			$result = odbc_prepare($db, $sql);
			odbc_execute($result, [
				'13',
				$userID,
				$idInform,
				date("Y") . "-" . date("m") . "-" . date("d"),
				date("H") . ":" . date("i") . ":" . date("s")
			]);


			if ($result) {
				$sql_id = "SELECT @@IDENTITY AS 'id_Log'";

				$cur = odbc_result(odbc_exec($db, $sql_id), 1);

				$sql = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) VALUES (?, ?, ?, ?)";
				$rs = odbc_prepare($db, $sql);
				odbc_execute($rs, [
					$cur,
					'Emitir Apólice',
					'Apólice',
					'Alteração'
				]);


				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//CRIADO POR WAGNER
				// ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

				if ($rs) {
					// Obter o ID gerado pela última inserção
					$sql_id_detalhes = "SELECT @@IDENTITY AS id_detalhes";
					$stmt_id_detalhes = odbc_prepare($db, $sql_id_detalhes);
					odbc_execute($stmt_id_detalhes, []);
					$cur = odbc_result($stmt_id_detalhes, 1);

					// Inserir na tabela Log_Detalhes_Query
					$sql = "INSERT INTO Log_Detalhes_Query (id_detalhes, query) VALUES (?, ?)";
					$stmt_log_query = odbc_prepare($db, $sql);
					odbc_execute($stmt_log_query, [
						$cur,
						str_replace("'", "", $log_query)
					]);
				}
				//fim if
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
			} else {
				$msg = "Erro no incluir do Log";
			}//Log   
		} else {
			$msg = "Apólice já Emitida";
			$title = "Notificações";
			$content = "../../../main.php";
		}
	} else {
		if ($_REQUEST['password'] != '' || $_REQUEST['usuario'] != '') {
			$password = $_REQUEST['password'];
			$usuario = $_REQUEST['usuario'];
		}

		if (empty($password) || empty($usuario)) {
			$nomeEmp = $nome;
			$par = "password";
			$title = "Emitir Apólice";
			$content = "../policy/interf/issuePolicy.php";
			$msg = "Usuário ou Senha não pode ser vazio.";
		} else {

			$sql = "SELECT 1 
			FROM dbo.Users 
			INNER JOIN dbo.UserRole ON dbo.Users.id = dbo.UserRole.idUser 
			INNER JOIN dbo.Role ON dbo.UserRole.idRole = dbo.Role.id
			WHERE dbo.Role.name = ? 
			AND dbo.Users.login = ? 
			AND dbo.Users.password = ?";

			$cur = odbc_prepare($db, $sql);
			odbc_execute($cur, ['liberaApolice', $usuario, $password]);


			// var_dump($_SESSION);
			// var_dump($_POST);


			/*
																																									 $sql = "Select password from Users where id = '3215'";
																																								 
																																									 $pass = odbc_result($cur, 1);
																																									 */
			//if ( $pass == $password ) {
			if (odbc_result($cur, 1)) {
				odbc_free_result($cur);
				//Alterado por Tiago V N - Elumini - 31/08/2005
				$query = "SELECT state FROM Inform WHERE state < ? AND id = ?";
				$cur = odbc_prepare($db, $query);
				odbc_execute($cur, [9, $idInform]);

				if (odbc_fetch_row($cur)) {
					odbc_free_result($cur);
					$query = "SELECT idAnt, dateEmission, GetDate() FROM Inform WHERE id = ?";
					$r = odbc_prepare($db, $query);
					odbc_execute($r, [$idInform]);

					if (odbc_fetch_row($r)) {
						$idAnt = odbc_result($r, 1);
						$dateEmission = odbc_result($r, 2);
						$DataEmissaoApolice = odbc_result($r, 3);

						if ($idAnt)
							$query = "SELECT endValidity FROM Inform WHERE id = ?";
						$stmt = odbc_prepare($db, $query);
						odbc_execute($stmt, [$idAnt]);
						$finalVigencia = odbc_result($stmt, 1);
					}

					if ($idAnt > 0 && !faltam($finalVigencia, 30, $dateEmission)) {
						$msgData = "A apólice só pode ser emitida com menos de 30 dias antes do fim de vigência da apólice renovada.";
						$title = "Emitir Apólice";
						$content = "../policy/interf/inputDate.php";
					} else {
						require_once("segProp.php");
						require_once("../vigencia.php");
						require_once("view.php");

						if ($return_flag) {
							//require_once ("../rolePrefix.php");
							$title = "Notificações";
							$content = "../../../main.php";
						} else {
							require_once("done.php");
							$title = "Apólice Emitida";
							$content = "../policy/interf/Policy.php";
						}
					}
				} else {
					$msg = "Ap&oacute;lice j&aacute; Emitida";
					$title = "Notificações";
					$content = "../../../main.php";
				}
			} else {

				$nomeEmp = $nome;
				$par = "password";
				$title = "Emitir Apólice";
				$msg = "Senha inválida ou Usuário não tem premissão.";
				$content = "../policy/interf/issuePolicy.php";
			}
		}
	}
} else if ($comm == "done") {
	if ($field->getField("mot") != "Voltar")
		require_once("done.php");

	if ($forward == "success") {
		$title = "Notificações";
		$content = "../../../main.php";
	} else {
		$content = "../policy/interf/Policy.php";
		require_once("view.php");
	}
} else if ($comm == "allPolicies") {
	$content = "../policy/interf/AllPolicies.php";
	$title = 'Apólices emitidas';
} else if ($comm == "Esm") {
	$content = "../policy/interf/Esm.php";
	$title = "Endosso S/Prêmio Movimento/Cancelamento";
} else if ($comm == "EEsm") {
	$content = "../policy/interf/EEsm.php";
	$title = "Endosso S/Prêmio Movimento/Cancelamento";
} else if ($comm == "teste_apolice") {

	$idInform = $_GET['idInform'];
	$userID = $_SESSION['userID'];

	require_once("view_test.php");

	odbc_close($db);

	$title = "Apólice Emitida";
	$content = "../policy/interf/Policy_test.php";

}

if (count($_POST) > 2) {
	// adiciona dados do log após alterações no inform
	$idImporter = isset($_REQUEST['idImporter']) ? $_REQUEST['idImporter'] : 0;
	$notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 2, $tem, 'Inform', '');
	$notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 2, $tem1, 'Importer', $idImporter);
}



require_once("../../../home.php");

?>