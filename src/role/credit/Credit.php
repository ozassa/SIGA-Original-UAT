<?php  //Alterado HiCom mes 04
if (!isset($_SESSION)) {
	session_set_cookie_params([
		'secure' => true,
		'httponly' => true
	]);
	session_start();
}
$userID = $_SESSION['userID'];
// extract($_REQUEST);

require_once("../rolePrefix.php");

// Incluir funções de segurança
require_once("../../security_functions.php");

// Verificar CSRF para requisições POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_middleware();
}

//require("control.php");
$type = $field->getField('type');

$refusal = $field->getNumField('refusal');
$finish = isset($finish) ? $finish : false;
if (!$finish)
	$finish = $field->getField('finish');

$done = isset($done) ? $done : false;
if (!$done) {
	$done = isset($_REQUEST['done']) ? $_REQUEST['done'] : false;
}

if (!$comm) {
	$comm = $_REQUEST['comm'];

	// Validando que o par�metro 'comm' s� pode conter caracteres alfanum�ricos e alguns caracteres especiais
	if (!preg_match('/^[a-zA-Z0-9_&=]+$/', $comm)) {
		// Valor inv�lido, rejeitar
		die('Input inv�lido!');
	}

	// Escapando caracteres especiais para prevenir inje��o
	//$comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');
}

$allowed_comms = [
	'notif',
	'return',
	'setCobr',
	'setCobrNovo',
	'c_Coface_Imp',
	'resMonitor',
	'fechamentoMensal',
	'statistics',
	'geraDemonstrativo',
	'reportImporter',
	'viewReportImport',
	'IncompleteInform',
	'cancelIncomplete',
	'RemoveNotif',
	'searchClient',
	'import',
	'changeManager',
	'done',
	'goTarif',
	'goOk',
	'ImportConsult',
	'importconsult',
	'alterCreditManager',
	'open',
	'replyCoface',
	'accept',
	'acceptDATA',
	'sendMail',
	'SearchReplyCoface',
	'CountryConsult',
	'countryConsultInterf',
	'countryConsult',
	'searchContact',
	'insertContact',
	'InsertContactDB',
	'AlterContact',
	'DeleteContact',
	'AlterationContact',
	'showBuyers',
	'addAddress',
	'setBuyer',
	'view',
	'solicEnderecoAdicional',
	'trataEndereco',
	'rejeitar',
	'rejeitarChange',
	'PendencyCoface',
	'changeCredit',
	'PendChangeCredit',
	'PendReject',
	'PendenciesCoface',
	'ClientNotConfirmed',
	'clientChangeImporterInsert',
	'clientChangeImporterRemove',
	'c_Coface_ImpRemove',
	'clientChangeLimit',
	'ClientChangeCredit_Ret',
	'ClientChangeCredit',
	'ReplyConsult',
	'SendReply',
	'SendReplyDados',
	'CreditAccord',
	'creditAccordShow',
	'NotifyInsertImport',
	'ImportLimits',
	'obs',
	'emitida',
	'renovacao_reduzidos',
	'original',
	'showdifsisseg',
	'reduzido',
	'mudou',
	'removed',
	'estudo',
	'cancelar',
	'notificaBonus',
	'view_rel_importer_vmi',
	'OcultarNotif',
	'ClienteDivulgaNome'
];

if (!in_array($comm, $allowed_comms, true)) {
	die('Comando inv�lido!');
}

$comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');

error_log($comm);


$idNotification = isset($idNotification) ? $idNotification : false;
if (!$idNotification) {
	$idNotification = isset($_REQUEST['idNotification']) ? $_REQUEST['idNotification'] : false;
}

$idInform = isset($idInform) ? $idInform : false;
if (!$idInform) {
	$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
}

$idBuyer = isset($idBuyer) ? $idBuyer : false;
if (!$idBuyer) {
	$idBuyer = isset($_REQUEST['idBuyer']) ? $_REQUEST['idBuyer'] : false;
}

//error_reporting(E_ALL); ini_set('display_errors', '1');
ini_set('max_execution_time', 600);

$namePrint = '';


if (!function_exists('getInterf')) {
	function getInterf($user)
	{
		if (!$role['client']) {
			return "../../../home.php";
		} else {
			return "../../../home.php";
		}
	}
}

if (!function_exists('ymd2dmy')) {
	// converte a data de yyyy-mm-dd para dd/mm/yyyy
	function ymd2dmy($d)
	{
		if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)) {
			return "$v[3]/$v[2]/$v[1]";
		}

		return $d;
	}
}

if (!function_exists('renovacao')) {
	function renovacao($idInform, $db)
	{
		$query = "SELECT idAnt FROM Inform WHERE id = ?";
		$stmt = odbc_prepare($db, $query);
		odbc_execute($stmt, [$idInform]);
		$idAnt = odbc_result($stmt, 1);

		odbc_free_result($stmt);

		if (!$idAnt || $idAnt <= 0) {
			return 0;
		}

		$query = "SELECT state FROM Inform WHERE id = ?";
		$stmt = odbc_prepare($db, $query);
		odbc_execute($stmt, [$idAnt]);
		$state = odbc_result($stmt, 1);

		odbc_free_result($stmt);

		if ($state == 10) {
			return 1;
		}

		return 0;
	}
}


if (count($_POST) > 2) {
	// armazena dados do log
	$tem = $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 1, '', 'Inform', '');
	$tem1 = $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 1, '', 'Importer', (isset($_REQUEST['idImporter']) ? $_REQUEST['idImporter'] : 0));
}

// notificacoes
if ($comm == "notif") {
	//require_once("../notification/BoxInput.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// Retorno de informe
} else if ($comm == "return") {
	require_once("return.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// Cobran�a An�lise e Monitoramento
} else if ($comm == "setCobr") {
	require_once("setCobr.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// Controla o c_Coface_Imp
} else if ($comm == "setCobrNovo") {
	require_once("setCobrNovo.php");
	$title = $msg . "<br><br><input type='button' value='Voltar' onclick=\"location.href='" . $root . "role/credit/Credit.php?comm=fechamentoMensal&ano=" . $_REQUEST['ano'] . "&mes=" . $_REQUEST['mes'] . "' \" name='Voltar' class='servicos'>";
	//$content = "../../../main.php";

	// Controla o c_Coface_Imp
} else if ($comm == "c_Coface_Imp") {
	require_once("cCofaceImp.php"); //motor
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// Teste de cobran�a de an�lise/monitoramento
} else if ($comm == "resMonitor") {
	$title = "Resumo do Faturamento de An�lise e Monitoramento";
	$content = "../credit/interf/ReportMonitor.php";

	// Estat�sticas
} else if ($comm == "fechamentoMensal") {
	$title = "Resumo do Faturamento de An�lise e Monitoramento";
	$content = "../credit/interf/fechamentoMensal.php";

	// Estat�sticas
} else if ($comm == "statistics") {
	if ($act == "prospects") {
		$title = "Estat�sticas - Tempo de an�lise - Prospectivos";
	} else if ($act == "month") {
		$title = "Estat�sticas - Consulta de Prospectivos por m�s";
	} else if ($act == "country") {
		$title = "Estat�sticas - Tempo de an�lise - Segurados";
	} else if ($act == "relProducao") {
		$title = "Estat�sticas - Relat�rio de Produ��o";
	} else if ($act == "relBonus") {
		$title = "Relat�rio de Pagamento de B�nus";
	} else if ($act == "relVMI") {
		$title = "Relat�rio de cr�dito maior que VMI";
	} else if ($act == "apoliceBB") {
		$title = "Relat�rio de Ap�lices cedidas para o Banco do Brasil";
	} else if ($act == "apoliceBP") {
		$title = "Relat�rio de Ap�lices cedidas para Bancos parceiros";
	} else if ($act == "apoliceOB") {
		$title = "Relat�rio de Ap�lices cedidas para outros Bancos";
	} else {
		$title = "Estat�sticas";
	}

	$content = "../credit/interf/statistics.php";

	// gera o pdf do Demonstrativo de faturamento e analise/monitoramento
} else if ($comm == "geraDemonstrativo") {


} else if ($comm == "reportImporter") {
	$title = "Demonstrativo do Faturamento de An&aacute;lise e Monitoramento";
	$content = "../credit/interf/ReportImporter.php";
} else if ($comm == "viewReportImport") {
	//Analise e Monitoramento
	if ($voltar != "1") {
		$title = "An&aacute;lise e Monitoramento";
		$content = "../credit/interf/viewReportImport.php";
	} else {
		$title = "Demonstrativo do Faturamento de An&aacute;lise e Monitoramento";
		$content = "../credit/interf/ReportImporter.php";
	}

	// inform incompleto
} else if ($comm == "IncompleteInform") {
	$title = "Informes Incompletos";
	$content = "../executive/interf/incompleteInform.php";

	require_once("../../../home.php");

} else if ($comm == "cancelIncomplete") {
	$query = "UPDATE Inform SET state = 9 WHERE id = ?";
	$stmt = odbc_prepare($db, $query);
	odbc_execute($stmt, [$idInform]);

	$query = "SELECT id, fim FROM AnaliseInform WHERE idInform = ?";
	$stmt = odbc_prepare($db, $query);
	odbc_execute($stmt, [$idInform]);

	if (odbc_fetch_row($stmt)) {
		$id = odbc_result($stmt, 1);
		$fim = odbc_result($stmt, 2);

		if (!$fim) {
			$query = "UPDATE AnaliseInform SET fim = GETDATE() WHERE id = ?";
			$stmt = odbc_prepare($db, $query);
			odbc_execute($stmt, [$id]);
		}
	}


	odbc_exec($db, $query);
	$msg = "Infome Cancelado";
	$title = "Informes Incompletos";
	$content = "../executive/interf/incompleteInform.php";

} else if ($comm == "RemoveNotif") {
	require_once("RemoveNotif.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// volta para search client
} else if ($comm == "searchClient") {
	require_once("../searchClient/SearchClient.php");

	// CONSULTA DE Compradores
} else if ($comm == "import") {
	require_once("importer.php"); // motor de altera��o
	$title = "Consulta de Compradores";
	$content = "../credit/interf/Importer.php";

} else if ($comm == "changeManager") {
	require_once("changeCredit.php");
	//die();
	require_once("importer.php"); // motor de altera��o
	$title = "Consulta de Compradores";
	$content = "../credit/interf/Importer.php";

} else if ($comm == "done") {
	require_once("done.php");
	//require_once("../notification/BoxInput.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// CONSULTA DE Compradores
} else if ($comm == "goTarif" || $comm == "goOk") {
	require_once("done.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// CONSULTA DE Compradores
} else if ($comm == "ImportConsult") {
	$title = "Consulta de Compradores";
	$content = "../credit/interf/ImportConsult.php";

	// CONSULTA DE Compradores
} else if ($comm == "importconsult") {
	$title = "Consulta de Compradores";
	$content = "../credit/interf/ImportConsult.php";

	// AlterCreditManager
} else if ($comm == "alterCreditManager") {
	require_once("alterCreditManager.php");
	$idInform = $field->getField('idInform');
	require_once("importer.php"); // motor de altera��o
	$title = "Consulta de Compradores";
	$content = "../credit/interf/Importer.php";

	// notificaoes
} else if ($comm == "open") {
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// RESPOSTA DA COFACE tipo 4, 5, 6 ou 7
} else if ($comm == "replyCoface" && ($type == 4 || $type == 5 || $type == 6 || $type == 7)) {
	require_once("replycofacelim.php"); // motor de busca

	if ($refusal) {
		$title = "Resposta COFACE - Recusa de Limite de Cr�dito" . $namePrint;
	} else {
		$title = "Resposta COFACE - Altera&ccedil;&atilde;o de Limite de Cr�dito" . $namePrint;
	}

	$content = "../credit/interf/replycofaceInterf.php";

	// credit aceita notifica��o credit
} else if ($comm == "accept") {
	require_once("replycofacelim.php");
	$title = "Resposta COFACE - Altera&ccedil;&atilde;o de Limite de Cr�dito" . $namePrint;
	$content = "../credit/interf/replycofacelim.php";

	// credit aceita notificacao de dados
} else if ($comm == "acceptDATA") {
	require_once("accept.php");
	$title = "Resposta COFACE - Altera&ccedil;&atilde;o de Dados" . $namePrint;
	$content = "../credit/interf/ReplyCofaceData.php";

	// send mail
} else if ($comm == "sendMail") {
	$mail = 1;
	require("execMail.php");

	if (!$address_flag) {
		require_once("changeCredit.php");
	}

	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// RESPOSTA DA COFACE tipo 3
} else if ($comm == "replyCoface" && $type == 3) {
	require_once("replyCofaceData.php"); // motor de busca
	$title = "Resposta COFACE - Altera&ccedil;&atilde;o de Dados" . $namePrint;
	$content = "../credit/interf/ReplyCofaceDataInterf.php";

	// CONSULTA DE RESPOSTAS
} else if ($comm == "SearchReplyCoface") {
	require_once("SearchReplyCoface.php"); // motor de busca
	$title = "Consulta de Resposta Coface" . $namePrint;
	$content = "../credit/interf/SearchReplyCoface.php";

	// CONSULTA DE PAISES
} else if ($comm == "CountryConsult") {
	$title = "Consulta de Pa�ses";
	$content = "../credit/interf/countryConsult.php";

} else if ($comm == "countryConsultInterf") {
	$title = "Consulta de Pa�ses";
	$content = "../credit/interf/countryConsultInterf.php";

	// Consulta Paises
} else if ($comm == "countryConsult") {
	require_once("countryConsult.php"); //motor de busca
	$title = "Consulta de Pa�ses";
	$content = "../credit/interf/countryConsultInterf.php";

	// BUSCA DE CONTATOS
} else if ($comm == "searchContact") {
	$title = "Busca de Contatos" . $namePrint;
	$content = "../credit/interf/searchcontact.php";

	// INCLUS�O DE CONTATOS
} else if ($comm == "insertContact") {
	$title = "Inclus�o de Contatos" . $namePrint;
	$content = "../credit/interf/InsertContact.php";

	// Busca de contatos
} else if ($comm == "InsertContactDB") {
	require_once("insertcontact.php"); // motor de altera��o
	$title = "Busca de Contatos" . $namePrint;
	$content = "../credit/interf/searchContact.php";

	// Alterar contato
} else if ($comm == "AlterContact") {
	$title = "Altera&ccedil;&atilde;o de Contatos" . $namePrint;
	$content = "../credit/interf/altercontact.php";

	// Excluir contato
} else if ($comm == "DeleteContact") {
	require_once("interf/DeleteContact.php");
	$title = $mensagem;
	$content = "../credit/interf/searchContact.php";

} else if ($comm == "AlterationContact") {
	require_once("alterationContact.php");
	$title = "Busca de Contatos" . $namePrint;
	$content = "../credit/interf/searchContact.php";

	//  monta tela com detalhes de um importador
} else if ($comm == "showBuyers") {
	require_once("showBuyers.php"); //motor de exibica��o
	$title = "Visualizar Comprador " . $namePrint;
	$content = "../credit/interf/ShowBuyers.php" . $namePrint;

} else if ($comm == "addAddress") {
	require_once("addAddress.php");
	$title = "Acrescentar Endere�os Adicionais";
	$content = "../credit/interf/AddAddress.php";

	// monta tela do recebimento da notifica��o
} else if ($comm == "setBuyer") {
	require_once("setBuyer.php"); //motor de exibica��o

	if ($erro) {
		require_once("showBuyers.php"); //motor de exibica��o
		$title = "" . $namePrint;
		$content = "../credit/interf/ShowBuyers.php" . $namePrint;
	} else {
		$origem = $field->getField("origem");

		if ($origem == 1) {
			require_once("importer.php"); // motor de altera��o
			$title = "Consulta de Compradores";
			$content = "../credit/interf/Importer.php";
		} else if ($origem == 2) {
			require_once("listBuyer.php"); //motor de notifica��o
			$title = "Principais Compradores" . $namePrint;

			if (renovacao($idInform, $db)) {
				$title .= "<br>(Renova��o)";
			}

			$content = "../credit/interf/ListBuyers.php" . $namePrint;
		} else if ($origem == 3) {
			require_once("PendenciesCoface.php");
			$title = "Pend�ncias na Coface";
			$content = "../credit/interf/PendenciesCoface.php";
		} else if ($origem == 4) {
			$comm = 'clientChangeImporterInsert';
			require_once("listBuyer.php"); //motor de notifica��o
			$title = "Principais Compradores" . $namePrint;

			if (renovacao($idInform, $db)) {
				$title .= "<br>(Renova��o)";
			}

			$content = "../credit/interf/ListBuyers.php" . $namePrint;
		}
	}
	// monta tela do recebimento da notifica��o
} else if ($comm == "view") {
	require_once("listBuyer.php"); //motor de notifica��o
	$namePrint = isset($namePrint) ? $namePrint : "";
	$title = "Solicita��o de An�lise de Cr�dito " . $namePrint;

	if (renovacao($idInform, $db)) {
		$title .= " (Renova��o)";
	} else if ($dateEmission > 0) {
		$title .= " (Reestudo)";
	}

	$content = "../credit/interf/ListBuyers.php" . $namePrint;

} else if ($comm == "solicEnderecoAdicional") {
	require_once("solEnder.php"); //motor de notifica��o
	$title = "Solicita��o de Inclus�o de endere�os adicionais";
	$content = "../credit/interf/solEnder.php";


} else if ($comm == "trataEndereco") {
	require_once("trataEndereco.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// Recusar importador
} else if ($comm == "rejeitar") {
	require_once("reject.php");

	if ($vazio) {
		$title = "Notifica&ccedil;&otilde;es";
		$content = "../../../main.php";
	} else {
		$comm = 'view';
		require_once("listBuyer.php"); //motor de notifica��o
		$title = "Principais Compradores" . $namePrint;

		if (renovacao($idInform, $db)) {
			$title .= "<br>(Renova��o)";
		}

		$content = "../credit/interf/ListBuyers.php" . $namePrint;
	}

} else if ($comm == "rejeitarChange") { //Rejeitar Alterar Limite do Importador
	require_once("rejeitaChange.php");

	if ($vazio) {
		$title = "Notifica&ccedil;&otilde;es";
		$content = "../../../main.php";
	} else {
		//$comm = 'clientChangeLimit';
		require_once("notifyaltercredit.php"); // motor de altera��o
		$title = "Altera&ccedil;&atilde;o de Cr�dito" . $namePrint;
		$content = "../credit/interf/NotifyAlterCredit.php";
	}

	// Pendencias Coface
} else if ($comm == "PendencyCoface") {
	require_once("PendenciesCoface.php");
	$title = "Pend�ncias na Coface";
	$content = "../credit/interf/PendenciesCoface.php";

	// Pendencias Coface
} else if ($comm == "changeCredit") {
	require_once("changeCredit.php");

	if ($comm == "changeCredit") {
		require_once("PendenciesCoface.php");
		$content = "../credit/interf/PendenciesCoface.php";
		$title = "Pend�ncias na Coface";
	} else {
		$title = "Notifica&ccedil;&otilde;es";
		$content = "../../../main.php";
	}

	//Criado pendencias para Coface
} else if ($comm == "PendChangeCredit") {
	require_once("PendChangeCredit.php");



	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	if ($comm == "PendChangeCredit") {
		//require_once("PendenciesCoface.php")

		$title = "Pend�ncias na Coface";
		$comm = "PendenciesCoface";
		$content = "../credit/interf/PendenciesCoface.php";

	} else {
		$_SESSION['msg'] = $msg;
		header('location: ' . $host . '../access/Access.php?comm=openBanco&msg=' . $msg);

		// print '?'.$msg;
		$title = "Notifica&ccedil;&otilde;es";
		$content = "../../../main.php";
	}

} else if ($comm == "PendReject") {
	require_once("PendReject.php");


	if ($comm == "PendChangeCredit") {
		require_once("PendenciesCoface.php");
		$title = "Pend�ncias na Coface";
		$content = "../credit/interf/PendenciesCoface.php";
	} else {
		$title = "Notifica&ccedil;&otilde;es";
		//$content = "../../../main.php";
		header('location: ../access/Access.php?comm=openBanco&msg=' . $msg);

	}

	// Pendencias Coface
} else if ($comm == "PendenciesCoface") {
	require_once("PendencyCoface.php");
	$title = "Pend�ncias na Coface";
	$content = "../credit/interf/PendencyCoface.php";

	// CLIENTES N�O CONFIRMADOS
} else if ($comm == "ClientNotConfirmed") {
	require_once("clientNotConfirmed.php"); //motor de SQL
	$title = "Clientes N�o Confirmados" . $namePrint;
	$content = "../credit/interf/ClientNotConfirmed.php";

	// Solicita��o de inclus�o de Compradores CLIENTE
} else if ($comm == "clientChangeImporterInsert") {
	require_once("listBuyer.php"); //motor de notifica��o
	$title = "Principais Compradores";

	if (renovacao($idInform, $db)) {
		$title .= "(Renova��o)";
	}

	$content = "../credit/interf/ListBuyers.php";

	// SOLICITA��O DE EXCLUS�O DE Compradores CLIENTE
} else if ($comm == "clientChangeImporterRemove") {
	require_once("listBuyerRemove.php"); //motor de notifica��o
	$title = "Compradores Removidos";
	$content = "../credit/interf/ListBuyersRemove.php";

} else if ($comm == "c_Coface_ImpRemove") {
	require_once("clientChangeCredit.php"); //motor
	$title = "Notifica&ccedil;&otilde;es";
	// $content = "../../../main.php";
	$content = "../../../main.php";

	// monta a tela da notifica��o que envolve a mudanca de credito feita pelo cliente.
} else if ($comm == "clientChangeLimit") {
	require_once("notifyaltercredit.php"); // motor de altera��o
	$title = "Altera&ccedil;&atilde;o de Cr�dito" . $namePrint;
	$content = "../credit/interf/NotifyAlterCredit.php";

	//executa a retirada da notificacao da tela de visualizacao
} else if ($comm == "ClientChangeCredit_Ret") {
	/*
		  //####### ini ####### adicionado por eliel vieira - elumini - em 17/04/2008
		  // referente a demanda 1485 - SAD
		  */
	//procedimento para update da notificacao
	require_once("clientChangeCredit_Ret.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

	// executa o aceite da notificacao
} else if ($comm == "ClientChangeCredit") {
	require_once("clientChangeCredit.php"); // motor de altera��o
	//require ("../notification/BoxInput.php");

	if ($vazio) {
		$title = "Notifica&ccedil;&otilde;es";
		$content = "../../../main.php";
	} else {
		require_once("notifyaltercredit.php"); // motor de altera��o
		$title = "Altera&ccedil;&atilde;o de Cr�dito" . $namePrint;
		$content = "../credit/interf/NotifyAlterCredit.php";
	}

	// CONSULTA DE RESPOSTAS
} else if ($comm == "ReplyConsult") {
	//require ("ReplyConsult.php"); motor de busca
	$title = "Consulta de Respostas" . $namePrint;
	$content = "../credit/interf/ReplyConsult.php";

	// REENVIAR RESPOSTA
} else if ($comm == "SendReply") {
	//require ("SendReply.php"); motor de busca
	$title = "Reenviar Resposta COFACE - Altera&ccedil;&atilde;o de Limite de Cr�dito" . $namePrint;
	$content = "../credit/interf/SendReply.php";

	// REENVIAR RESPOSTA DADOS
} else if ($comm == "SendReplyDados") {
	//require ("SendReplyDados.php"); motor de busca
	$title = "Reenviar Resposta COFACE - Altera&ccedil;&atilde;o de Dados";
	$content = "../credit/interf/SendReplyDados.php";

	// CR�DITO CONCEDIDO lista geral
} else if ($comm == "CreditAccord") {
	$title = "Cr�dito Concedido";
	$content = "../credit/interf/CreditAccord.php";

	// CR�DITO CONCEDIDO lista detalhe
} else if ($comm == "creditAccordShow") {
	require_once("creditAccordShow.php"); //motor de altera��o
	$title = "Cr�dito Concedido";
	$content = "../credit/interf/CreditAccordShow.php";

	// SOLICITA��O DE INCLUS�O DE Compradores
} else if ($comm == "NotifyInsertImport") {
	//require ("NotifyInsertImport.php"); motor de altera��o
	$title = "Solicita&ccedil;&atilde;o de Inclus�o de Compradores";
	$content = "../credit/interf/NotifyInsertImport.php";

	// CONSULTA DE Compradores - DETALHES
} else if ($comm == "ImportLimits") {
	//require ("ImportLimits.php"); motor de altera��o
	$title = "Consulta de Compradores" . $namePrint;
	$content = "../credit/interf/ImportLimits.php";

} else if ($comm == "obs") {
	if ($_POST['idComment']) {
		$idComment = $_POST['idComment'];
		$val = $_POST['val'];
	}

	if ($idComment) {
		$query = "UPDATE ImpComment SET hide = ? WHERE id = ?";
		$stmt = odbc_prepare($db, $query);
		odbc_execute($stmt, [$val, $idComment]);

	}

	require_once("obs.php");
	$title = "Observa&ccedil;&otilde;es";
	$content = "../credit/interf/Obs.php";
} else if ($comm == 'emitida') {
	require_once("emitida.php");

	if ($done) {
		$title = "Notica��es";
		$content = "../../../main.php";
	} else {
		$title = "Notica��es";
		$content = "../credit/interf/Emitida.php";
	}
} else if ($comm == 'renovacao_reduzidos') {
	require_once("renovacao_reduzidos.php");

	if ($done) {
		$title = "Notifica&ccedil;&otilde;es";
		$content = "../../../main.php";
	} else {
		$title = "Compradores com cr�dito reduzido automaticamente na renova��o";
		$content = "../credit/interf/Renovacao_reduzidos.php";
	}

} else if ($comm == 'original') {
	$title = "Cadastro original";
	$content = "../credit/interf/original.php";

} else if ($comm == 'showdifsisseg') {
	$title = "Diferen�as Siex / Sisseg";
	$content = "../credit/interf/showdifsisseg.php";

} else if ($comm == 'reduzido') {
	require_once("reduzido.php");

	if ($finish) {
		//require_once("../notification/BoxInput.php");
		$title = "Notifica&ccedil;&otilde;es";
		$content = "../../../main.php";
	} else {
		$title = "Comprador teve cr�dito reduzido durante o reestudo";
		$content = "../credit/interf/Reduzido.php";
	}

} else if ($comm == 'mudou') {
	require_once("mudou.php");

	if ($done) {
		$title = "Notifica&ccedil;&otilde;es";
		$content = "../../../main.php";
	} else {
		$title = "Cliente mudou dados do Comprador";
		$content = "../credit/interf/mudou.php";
	}

} else if ($comm == 'removed') {
	require_once("removed.php");

	if ($remove) {
		$msg = 'Compradores removidos';
		//require_once("../notification/BoxInput.php");
		$title = "Notifica&ccedil;&otilde;es";
		$content = "../../../main.php";
		//return;
	} else {
		$title = 'Compradores removidos';
		$content = '../credit/interf/Removed.php';
	}

} else if ($comm == 'estudo') {
	require_once('estudo.php');
	$comm = 'import';
	require('Credit.php');
	exit;

} else if ($comm == 'cancelar') {
	require_once("cancelainforme.php");
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

} else if ($comm == 'notificaBonus') {
	//Criado por Tiago V N - 25/01/2008
	if ($ocultar) {
		$notif->doneRole($idNotification, $db);
		$title = "Notifica&ccedil;&otilde;es";
		$content = "../../../main.php";
	} else {
		$title = "Previs�o de Pagamento de B�nus";
		$content = "../credit/interf/viewPagBonus.php";
	}

} else if ($comm == 'view_rel_importer_vmi') {
	$title = "Relat�rio de cr�dito maior que VMI";
	$content = "../credit/interf/view_rel_importer_vmi.php";

} else if ($comm == "OcultarNotif") {
	$notif->doneRole($idNotification, $db);
	$title = "Notifica&ccedil;&otilde;es";
	$content = "../../../main.php";

} else if ($comm == "ClienteDivulgaNome") {
	$title = "Comprador divulga nome";
	$content = '../credit/interf/ViewClienteDivulgaNome.php';

} else {
	$content = "";
	$title = "<H1>SERVI�O INEXISTENTE: $comm</h1>";
}

//require_once(getInterf($user));

if ($content == "") {
	$title = "Notifica��es";
	$content = "../../../main.php";
}

if (count($_POST) > 2) {
	// adiciona dados do log ap�s altera��es no inform
	$notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 2, $tem, 'Inform', '');
	$notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 2, $tem1, 'Importer', (isset($_REQUEST['idImporter']) ? $_REQUEST['idImporter'] : false));
}

require_once("../../../home.php");

?>