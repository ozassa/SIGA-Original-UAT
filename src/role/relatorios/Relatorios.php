<?php

//Alterado HiCom mes 04
session_set_cookie_params([
  'secure' => true,
  'httponly' => true
]);
session_start();


if (empty($_SESSION['userID'])) {
  header("Location: https://siga.coface.com/3");
}
$userID = $_SESSION['userID'];

//extract($_REQUEST);

//print '?'.$status;

require_once("../rolePrefix.php");

$idBanco = isset($_REQUEST['idBanco']) ? $_REQUEST['idBanco'] : false;
$tipoBanco = isset($_REQUEST['tipoBanco']) ? $_REQUEST['tipoBanco'] : false;

$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
$idCDBB = isset($_REQUEST['idCDBB']) ? $_REQUEST['idCDBB'] : false;
$idCDOB = isset($_REQUEST['idCDOB']) ? $_REQUEST['idCDOB'] : false;
$idCDParc = isset($_REQUEST['idCDParc']) ? $_REQUEST['idCDParc'] : false;
$idNotification = isset($_REQUEST['idNotification']) ? $_REQUEST['idNotification'] : false;
$msgAg = isset($msgAg) ? $msgAg : false;

if ($comm == 'cessaoBB') {
  $q = "SELECT tipo FROM Banco WHERE id = ?";
  $stmt = odbc_prepare($db, $q);
  odbc_execute($stmt, [$idBanco]);

  $tipoBanco = odbc_fetch_array($stmt)['tipo'] ?? null;

  odbc_free_result($stmt);


  if (($tipoBanco == 1) || ($tipoBanco == 2)) {
    $title = "Cessão de Direito - Emissão de Cessão de Direito";
    $content = "../client/interf/cessao.php";
  } else if ($idBanco == 0) {
    $title = "Cessão de Direito - Emissão de Cessão de Direito";
    $msgAg = "Por favor, selecione um Banco";
    $content = "../client/interf/cessao.php";
  } else {
    $content = "../cessao/interf/cessaoBB.php";
  }


} else if ($comm == "cessao") {
  $title = 'Cessão de Direito - Emissão de Cessão de Direito';
  $content = "../client/interf/cessao.php";

} else if ($comm == 'selImp') {
  require_once('confAg.php');
  if ($msgAg) {
    if ($tipoBanco != 3) {
      $title = "Cessão de Direito - Emissão de Cessão de Direito";
      $content = "../client/interf/cessao.php";
    } else {
      $title = "Cessão de Direito";
      $content = "../cessao/interf/cessaoBB.php";
    }
  } else {
    $title = "Cessão de Direito - Emissão de Cessão de Direito - Compradores";
    $content = "../cessao/interf/selImp.php";
  }

} else if ($comm == 'consImpCliente') {

  require_once('confAg.php');
  //echo "<pre>status:$status</pre>";
  if ($status == 0) {
    $title = "Cessão de Direito";
    $content = "../cessao/interf/selImp.php";
  } else {
    $title = "Cessão de Direito";
    $content = "../cessao/interf/consImpCliente.php";
  }


} else if ($comm == 'cessaoOB') {
  $title = "Cessão de Direito";
  $content = "../cessao/interf/cessaoOB.php";

} else if ($comm == 'selImpOB') {
  $title = "Cessão de Direito";
  $content = "../cessao/interf/selImpOB.php";

} else if ($comm == 'imprBB') {
  $title = "Cessão de Direito";
  $content = "../cessao/interf/imprBB.php";

} else if ($comm == 'view') {
  $title = "Cessão de Direito";

  $content = "../cessao/interf/view.php";

} else if ($comm == 'viewCancel') {
  $title = "Cessão de Direito";
  $content = "../cessao/interf/cancelBackoffice.php";

} else if ($comm == 'aceitarBB') {
  require_once('aceitarBB.php');
  $title = 'Notificações';
  $content = "../../../main.php";

} else if ($comm == 'cancelarBB') {
  require_once('cancelarBB.php');
  $title = 'Notificações';
  $content = "../../../main.php";

} else if ($comm == 'gravaBB') {
  require_once('gravaBB.php');
  $title = 'Cessão de Direito - Emissão de Cessão de Direito - Confirmação';
  //$content = "../cessao/interf/selImp.php";

} else if ($comm == 'consultaCessao') {
  $title = 'Cessão de Direito';
  $content = "../cessao/interf/consultaCessao.php";

} else if ($comm == 'cancelCessaoBB') {
  $title = 'Cancelar Cessão de Direito';
  $content = "../cessao/interf/cancelCessaoBB.php";

} else if ($comm == 'cancel') {
  $title = 'Cancelar Cessão de Direito';
  $content = "../cessao/interf/cancelBB.php";

} else if ($comm == 'cancelBB') {
  require_once('cancelBB.php');
  $title = 'Cancelar Cessão de Direito';
  $content = "../cessao/interf/cancelCessaoBB.php";

} else if ($comm == 'cancelBackoffice') {
  require_once('cancelBackoffice.php');
  $title = 'Notificações';
  $content = "../../../main.php";

} else if ($comm == 'desconsiderar') {
  require_once('desconsiderar.php');
  $title = 'Notificações';
  $content = "../../../main.php";

} else if ($comm == 'consultaCessaoExp') {
  $title = 'Cessão de Direito';
  $content = "../cessao/interf/consultaCessaoExp.php";

} else if ($comm == 'consultaImp') {
  $title = 'Cessão de Direito';
  $content = "../cessao/interf/consultaImp.php";

} else if ($comm == 'consultaClientBB') {
  $title = 'Cessão de Direito';
  $content = "../cessao/interf/consultaClientBB.php";
  if ($role["client"]) {

  } else {

  }

  //}else if($comm == 'cessoesdireito'){
  //  //require_once('geraNotif.php');
  //  $content = "../client/interf/ViewClient.php";
  //  require_once("../../../../site/informe/index.php");

} else if ($comm == 'concluiBB') {
  require('geraNotif.php');
  $content = "../client/interf/ViewClient.php";

} else if ($comm == 'donenotif') {
  $notif->doneRole($idNotification, $db);
  $title = 'Notificações';
  $content = "../../../main.php";

} else if ($comm == 'cadBanco') {
  $title = 'Banco';
  $content = "../cessao/interf/cadBanco.php";

} else if ($comm == 'situacaoFinanceira') {
  $title = 'Situação Financeira';
  $content = "../relatorios/interf/situacaoFinanceira.php";

} else if ($comm == 'calculoClCofSbce') {
  $title = 'Cálculo Loss Ratio - Coface & SBCE';
  $content = "../relatorios/interf/calculoClCofSbce.php";

} else if ($comm == 'malusBonus') {
  $title = 'Controle de Malus e Bonificação - COFACE & SBCE';
  $content = "../relatorios/interf/malusBonus.php";

} else if ($comm == 'CAPRI') {
  $title = 'CAPRI - Limites pro segurado';
  $content = "../relatorios/interf/CAPRI.php";

} else if ($comm == 'TOD') {
  $title = 'Cálculo Loss Ratio - Coface & SBCE';
  $content = "../relatorios/interf/TOD.php";

} else if ($comm == 'cadBancoSQL') {
  require_once('cadBanco.php');
  $title = 'Cessão de Direito';
  $content = "../cessao/interf/cadBanco.php";


} else if ($comm == 'voltar') {
  $title = 'Notificações';
  $content = "../../../main.php";

} else if ($comm == 'cadAg') {
  $title = 'Agência';
  $content = "../cessao/interf/cadAgencia.php";

} else if ($comm == 'atuAg') {
  require_once('atuAg.php');
  $title = 'Agência';
  $content = "../cessao/interf/cadAgencia.php";

} else if ($comm == 'cadAgSQL') {
  require_once('cadAg.php');
  $title = 'Cessão de Direito';
  $content = "../cessao/interf/cadAgencia.php";

} else if ($comm == 'buscaAg') {
  $title = 'Cessão de Direito';
  $content = "../cessao/interf/cadAgencia.php";

} else if ($comm == 'cancelBackofficeOutros') {
  require_once('cancelBackofficeOutros.php');
  $title = 'Cancelar Cessão de Direito';
  $content = "../cessao/interf/consultaIndex.php";

} else if ($comm == 'consultaIndex') {
  $title = 'Cessão de Direito';
  $content = "../cessao/interf/consultaindex.php";

} else if ($comm == 'DetAg') {
  $title = 'Detalhes da Agência';
  $content = "../cessao/interf/DetAg.php";

} else if ($comm == 'consultaDoc') {
  $title = 'Documentação da Apólice';
  $content = "../cessao/interf/consultaDoc.php";

} else if ($comm == 'consultaSituacaoFinanc') {
  $title = 'Cessão de Direito - Situação Financeira';
  $content = "../cessao/interf/consultaSituacaoFinanc.php";

} else if ($comm == 'consultaSinistro') {
  $title = 'Cessão de Direito - Acompanhamento de Sinistros';
  $content = "../cessao/interf/consultaSinistro.php";

} else if ($comm == 'emiteCessaoDireito') {
  $title = 'Cessão de Direito - Emissão de Cessão de Direito';
  $content = "../cessao/interf/emiteCessaoDireito.php";

} else if ($comm == 'viewEmiteCessaoDireito') {
  $title = 'Cessão de Direito - Emissão de Cessão de Direito - Detalhes';
  $content = "../cessao/interf/viewEmiteCessaoDireito.php";

} else if ($comm == 'recusaCessaoDireito') {
  require_once('alterCessaoDireito.php');

} else if ($comm == 'aceitaCessaoDireito') {
  require_once('alterCessaoDireito.php');

} else if ($comm == 'emiteCessaoDireitoSeguradora') {
  $title = 'Cessão de Direito - Emissão de Cessão de Direito';
  $content = "../cessao/interf/emiteCessaoDireitoSeguradora.php";

} else if ($comm == 'viewEmiteCessaoDireitoSeguradora') {
  $title = 'Cessão de Direito - Emissão de Cessão de Direito - Detalhes';
  $content = "../cessao/interf/viewEmiteCessaoDireitoSeguradora.php";

} else if ($comm == 'recusaCessaoDireitoSeguradora') {
  require_once('alterCessaoDireitoSeguradora.php');

} else if ($comm == 'aceitaCessaoDireitoSeguradora') {
  require_once('alterCessaoDireitoSeguradora.php');

} else if ($comm == 'cancelaCessaoDireito') {
  $title = 'Cessão de Direito - Cancelamento de Cessão de Direito';
  $content = "../cessao/interf/cancelaCessaoDireito.php";

} else if ($comm == 'viewCancelaCessaoDireito') {
  $title = 'Cessão de Direito - Cancelamento de Cessão de Direito - Detalhes';
  $content = "../cessao/interf/viewCancelaCessaoDireito.php";

} else if ($comm == 'cancelarCessaoDireito') {
  require_once('cancelarCessaoDireito.php');

} else if ($comm == 'cancelaCessaoDireitoBB') {
  $title = 'Cessão de Direito - Cancelamento de Cessão de Direito';
  $content = "../cessao/interf/cancelaCessaoDireitoBB.php";

} else if ($comm == 'viewCancelaCessaoDireitoBB') {
  $title = 'Cessão de Direito - Cancelamento de Cessão de Direito - Detalhes';
  $content = "../cessao/interf/viewCancelaCessaoDireitoBB.php";

} else if ($comm == 'cancelarCessaoDireitoBB') {
  require_once('cancelarCessaoDireitoBB.php');

} else if ($comm == 'cancelaCessaoDireitoSeguradora') {
  $title = 'Cessão de Direito - Cancelamento de Cessão de Direito';
  $content = "../cessao/interf/cancelaCessaoDireitoSeguradora.php";

} else if ($comm == 'viewCancelaCessaoDireitoSeguradora') {
  $title = 'Cessão de Direito - Cancelamento de Cessão de Direito - Detalhes';
  $content = "../cessao/interf/viewCancelaCessaoDireitoSeguradora.php";

} else if ($comm == 'cancelarCessaoDireitoSeguradora') {
  require_once('cancelarCessaoDireitoSeguradora.php');

}



if (!isset($content)) {
  $content = false;
}

if (!$content) {
  $content = "../../../main.php";
}

require_once("../../../home.php");
?>