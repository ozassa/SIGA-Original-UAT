<?php
//Alterado HiCom mes 04
//Alterado HiCom 19/10/04 (Gustavo - adicionei um item de menu para Parcela de Ajuste)

// converte a data de yyyy-mm-dd para dd/mm/yyyy

session_start();
$perfilUsuario = $_SESSION['pefil'];
$userID = $_SESSION['userID'];



//extract($_REQUEST);
//error_log("Conteúdo de listclient:");
//error_log(print_r($_REQUEST, true));

$comm = isset($_REQUEST['comm']) ? trim($_REQUEST['comm']) : null;
$comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');

$comm_allowed = [
  'view',
  'open',
  'done',
  'entregavenciada',
  'entregavenciadaok',
  'comite',
  'include',
  'exclude',
  'modalidade',
  'salvatotal',
  'send',
  'editImporter',
  'consultadve',
  'libera_dve',
  'consultadveBanco',
  'DVEConsulta',
  'editDveBanco',
  'consultaDveEmitidaBanco',
  'NotificacaoNPC',
  'consultaPa',
  'calculaPa',
  'calculaPaDet',
  'atualizaDetPa',
  'atualizaDetPaOK',
  'parcelaPA',
  'exibeDve',
  'exibeDveDet',
  'impdve',
  'dImpDve',
  'importDve',
  'voltar',
  'atualizar_unblock_dve',
  'DVELiquidacao',
  'alterDVELiquidacao'
];

if (!in_array($comm, $comm_allowed, true)) {
  #http_response_code(403);
  #exit('Acesso negado: comm inválido.');
}

$t_Usuario = isset($_REQUEST['t_Usuario']) ? trim($_REQUEST['t_Usuario']) : null;
$t_Usuario = $t_Usuario !== null ? htmlspecialchars($t_Usuario, ENT_QUOTES, 'UTF-8') : null;

$perfil = isset($_REQUEST['perfil']) ? trim($_REQUEST['perfil']) : null;
$perfil = $perfil !== null ? htmlspecialchars($perfil, ENT_QUOTES, 'UTF-8') : null;

$_REQUEST['idInform'] = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
$idInform = preg_replace('/\D/', '', $idInform);
$idInform = (int) $idInform;

if (!function_exists('ymd2dmy')) {
  function ymd2dmy($d)
  {
    if (preg_match("@([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})@", $d ?? '', $v)) {
      return "$v[3]/$v[2]/$v[1]";
    }

    return $d;
  }
}

// converte a data de yyyy-mm-dd para dd/mm/yy
if (!function_exists('ymd2dmy2')) {
  function ymd2dmy2($d)
  {
    if (preg_match("@([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})@", $d ?? '', $v)) {
      return "$v[3]/$v[2]/" . sprintf("%02d", ($v[1] - 2000));
    }

    return $d;
  }
}

// converte a data de  dd/mm/yyyy para yyyy-mm-dd 00:00:00.000
if (!function_exists('dmy2ymd')) {
  function dmy2ymd($d)
  {
    global $msg;
    if (preg_match("@([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})@", $d, $v)) {
      return "$v[3]-$v[2]-$v[1] 00:00:00.000";
    } else if (preg_match("@([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})@", $d, $v)) {
      return ($v[3] + 2000) . "-$v[2]-$v[1] 00:00:00.000";
    } else {
      $msg = 'Data em formato inválido (deve ser dd/mm/yyyy ou dd/mm/yy): ' . $d;
      return '';
    }
  }
}

if (!function_exists('getTimeStamp')) {
  function getTimeStamp($date)
  {
    if (preg_match('@^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})@', $date, $res)) {
      return mktime(0, 0, 0, $res[2], $res[1], $res[3]);
    } else if (preg_match('@^([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})@', $date, $res)) {
      return mktime(0, 0, 0, $res[2], $res[1], $res[3] + 2000);
    }
  }
}

if (!function_exists('check_dates')) {
  function check_dates($embarque, $vencimento)
  {
    // verifica se as datas estao corretas
    global $idDVE, $msg, $db;

    if (!$embarque) {
      $msg = "A data de embarque é obrigatória";
      return false;
    }

    if (!$vencimento) {
      $msg = "A data de vencimento é obrigatória";
      return false;
    }

    $emb = getTimeStamp($embarque);
    $venc = getTimeStamp($vencimento);

    if ($emb > time()) {
      $msg = "Erro: A data de embarque não pode ser maior que a data atual.";
      return false;
    }

    if ($emb > $venc) {
      $msg = "Erro: A data de vencimento deve ser posterior a data de embarque";
      return false;
    }

    if ($venc > $emb + (720 * 24 * 3601)) {
      $msg = "Erro: A data de vencimento deve ser, no máximo, 180 dias após o embarque";
      return false;
    }

    $query = "SELECT inicio, periodo, idInform FROM DVE WHERE id = ?";
    $r = odbc_prepare($db, $query);
    odbc_execute($r, array($idDVE));
    if (!odbc_fetch_row($r)) {
      $msg = "DVN inexistente: $idDVE";
      return false;
    }

    $inicio = odbc_result($r, 1);
    $periodo = odbc_result($r, 2);
    $time_inicio = getTimeStamp(ymd2dmy($inicio));
    $idInform = odbc_result($r, "idInform");
    odbc_free_result($r);


    //Alterado por Tiago V N - 06/07/2007
    //time_fim = getTimeStamp(getEndDate(ymd2dmy($inicio), $periodo));

    $queryInform = "SELECT * FROM Inform WHERE id = ?";
    $rs = odbc_prepare($db, $queryInform);
    odbc_execute($rs, array($idInform));

    if (odbc_fetch_row($rs)) {
      $tipoDve = odbc_result($rs, "tipoDve");
    } else {
      $msg = "Erro: Identificador segurado não existente";
      return false;
    }

    odbc_free_result($rs);

    if ($tipoDve == 3) { //Tipo DVE Anual
      $time_fim = getTimeStamp(getEndDate(ymd2dmy($inicio ?? ''), 12, 1));
    } else if ($tipoDve == 2) { //Tipo DVE Trimestral
      $time_fim = getTimeStamp(getEndDate(ymd2dmy($inicio ?? ''), 3, 1));
    } else { // Tipo DVE Mensal
      $time_fim = getTimeStamp(getEndDate(ymd2dmy($inicio ?? ''), 1, 1));
    }

    if (!($time_inicio <= $emb && $emb <= $time_fim)) {
      $msg = "Erro: A data de embarque deve estar dentro do período de referência da DVE";
      return false;
    }

    return true;
  }
}

if (!function_exists('correct')) {
  function correct($d)
  {
    // corrige o formato da data
    if (preg_match("@^([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})$@", $d, $v)) {
      if ($v[3] >= 70) {
        $ano = 1900 + $v[3];
      } else {
        $ano = 2000 + $v[3];
      }
      return "$v[1]/$v[2]/$ano";
    }
    return $d;
  }
}

if (!function_exists('getEndDate')) {
  function getEndDate($d, $n, $c = 0)
  {
    global $idDVE, $db, $idInform;

    $stmtNum = odbc_prepare($db, "SELECT num FROM DVE WHERE id = ?");
    odbc_execute($stmtNum, array($idDVE));
    $num = odbc_fetch_row($stmtNum) ? odbc_result($stmtNum, 1) : null;
    odbc_free_result($stmtNum);

    $stmtStart = odbc_prepare($db, "SELECT startValidity FROM Inform WHERE id = ?");
    odbc_execute($stmtStart, array($idInform));
    $start = odbc_fetch_row($stmtStart) ? ymd2dmy(odbc_result($stmtStart, 1)) : null;
    odbc_free_result($stmtStart);

    $stmtNumDves = odbc_prepare($db, "SELECT max(num) FROM DVE WHERE idInform = ?");
    odbc_execute($stmtNumDves, array($idInform));
    $num_dves = odbc_fetch_row($stmtNumDves) ? odbc_result($stmtNumDves, 1) : null;
    odbc_free_result($stmtNumDves);

    if (preg_match("@([0-9]{2})/([0-9]{2})/([0-9]{4})@", $start, $v)) {
      $dia_inicial = $v[1];
    }
    /*if($dia_inicial == 1){
      $num_dves = 12;
    }else{
      $num_dves = 13;
    }  */

    if ($num != $num_dves) {
      if (preg_match("@([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})@", $d, $v)) {
        //return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $v[1] - 1 + $c, $v[3]));
        return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
      } else if (preg_match("@([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})@", $d, $v)) {
        //return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $v[1] - 1 + $c, $v[3] + 2000));
        return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
      }
    } else {
      $stmtEnd = odbc_prepare($db, "SELECT endValidity FROM Inform WHERE id = ?");
      odbc_execute($stmtEnd, array($idInform));
      $end = odbc_fetch_row($stmtEnd) ? odbc_result($stmtEnd, 1) : null;
      odbc_free_result($stmtEnd);
      return ymd2dmy($end);
    }
  }
}

if (!function_exists('conserta')) {
  function conserta($n)
  {
    $s = ereg_replace(',00', '', $n);
    $s = ereg_replace('\.', '', $s);
    return $s;
  }
}

require_once("../rolePrefix.php");


if (count($_POST) > 3) {
  // armazena dados do log
  $idImporter = isset($idImporter) ? $idImporter : 0;
  //   $tem = $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db,1,'','Inform','');  
  //   $tem1 = $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db,1,'','Importer',$idImporter); 
}

$comm = isset($_REQUEST['comm']) ? $_REQUEST['comm'] : 'view';


//$flag    = $_REQUEST['primeira_tela'];
$flag = isset($_REQUEST['flag']) ? $_REQUEST['flag'] : "";
$client = isset($_REQUEST['client']) ? $_REQUEST['client'] : "";

if (!ctype_digit($client)) {
  #http_response_code(403);
  #exit('Acesso negado: comm inválido.');
}

$client = preg_replace('/\D/', '', $client);
$client = (int) $client;

$numDVE = isset($_REQUEST['numDVE']) ? $_REQUEST['numDVE'] : "";
$numDVE = preg_replace('/\D/', '', $numDVE);
$numDVE = (int) $numDVE;



if (!isset($modalidade)) {
  $modalidade = isset($_REQUEST['modalidade']) ? $_REQUEST['modalidade'] : "";
}

$tipobanco = isset($_REQUEST['tipoBanco']) ? $_REQUEST['tipoBanco'] : "";
$cessao = isset($_REQUEST['cessao']) ? $_REQUEST['cessao'] : "";
$agencia = isset($_REQUEST['agencia']) ? $_REQUEST['agencia'] : "";
$primeira_tela = isset($_REQUEST['primeira_tela']) ? $_REQUEST['primeira_tela'] : "";
$primeira_tela = preg_replace('/\D/', '', $primeira_tela);
$primeira_tela = (int) $primeira_tela;

$exibe = isset($_REQUEST['exibe']) ? $_REQUEST['exibe'] : "";
$dveInicio = isset($_REQUEST['dveInicio']) ? $_REQUEST['dveInicio'] : "";



$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : "";

$idDVE = isset($_REQUEST['idDVE']) ? $_REQUEST['idDVE'] : "";
$idDVE = preg_replace('/\D/', '', $idDVE);
$idDVE = (int) $idDVE;

$idNotification = isset($_REQUEST['idNotification']) ? $_REQUEST['idNotification'] : "";
$client = isset($_REQUEST['client']) ? $_REQUEST['client'] : "";
$fieldfocus = isset($_REQUEST['fieldfocus']) ? $_REQUEST['fieldfocus'] : "";
$formfocus = isset($_REQUEST['formfocus']) ? $_REQUEST['formfocus'] : "";
$viewflag = isset($_REQUEST['viewflag']) ? $_REQUEST['viewflag'] : "";
$importerName = isset($_REQUEST['importerName']) ? $_REQUEST['importerName'] : "";
$dve_action = isset($_REQUEST['dve_action']) ? $_REQUEST['dve_action'] : "";
$idBuyer = isset($_REQUEST['idBuyer']) ? $_REQUEST['idBuyer'] : "";
$newdve = isset($_REQUEST['newdve']) ? $_REQUEST['newdve'] : "";
$idDetail = isset($_REQUEST['idDetail']) ? $_REQUEST['idDetail'] : "";
$dataEmb = isset($_REQUEST['dataEmb']) ? $_REQUEST['dataEmb'] : "";
$dataVenc = isset($_REQUEST['dataVenc']) ? $_REQUEST['dataVenc'] : "";
$idCountry = isset($_REQUEST['idCountry']) ? $_REQUEST['idCountry'] : "";
$registro = isset($_REQUEST['registro']) ? $_REQUEST['registro'] : "";
$idBuyer = isset($_REQUEST['idBuyer']) ? $_REQUEST['idBuyer'] : "";
$cicoface = isset($_REQUEST['cicoface']) ? $_REQUEST['cicoface'] : "";
$fatura = isset($_REQUEST['fatura']) ? $_REQUEST['fatura'] : "";
$dataEmbDia = isset($_REQUEST['dataEmbDia']) ? $_REQUEST['dataEmbDia'] : "";
$dataEmbMes = isset($_REQUEST['dataEmbMes']) ? $_REQUEST['dataEmbMes'] : "";
$dataEmbAno = isset($_REQUEST['dataEmbAno']) ? $_REQUEST['dataEmbAno'] : "";
$LiberaVencida = isset($_REQUEST['LiberaVencida']) ? $_REQUEST['LiberaVencida'] : "";
$Valida = isset($_REQUEST['Valida']) ? $_REQUEST['Valida'] : "";
$DataCadastro = isset($_REQUEST['DataCadastro']) ? $_REQUEST['DataCadastro'] : "";
$dataVencDia = isset($_REQUEST['dataVencDia']) ? $_REQUEST['dataVencDia'] : "";
$dataVencMes = isset($_REQUEST['dataVencMes']) ? $_REQUEST['dataVencMes'] : "";
$dataVencAno = isset($_REQUEST['dataVencAno']) ? $_REQUEST['dataVencAno'] : "";
$valorEmb = isset($_REQUEST['valorEmb']) ? $_REQUEST['valorEmb'] : "";
$totalEmb = isset($_REQUEST['totalEmb']) ? $_REQUEST['totalEmb'] : "";
$proex = isset($_REQUEST['proex']) ? $_REQUEST['proex'] : "";
$totalProex = isset($_REQUEST['totalProex']) ? $_REQUEST['totalProex'] : "";
$ace = isset($_REQUEST['ace']) ? $_REQUEST['ace'] : "";
$totalAce = isset($_REQUEST['totalAce']) ? $_REQUEST['totalAce'] : "";
$show = isset($_REQUEST['show']) ? $_REQUEST['show'] : "";
$pode_remover = isset($_REQUEST['pode_remover']) ? $_REQUEST['pode_remover'] : "";




//print '?'. $flag;

if ($comm == 'view' || $comm == 'open') {
  //if($flag != 1)

  require_once("viewDve.php");

  if ($flag == 1) {
    require_once('includeImporter.php');
    $title = 'Inclusão de Importador';
    $content = '../dve/interf/IncludeImporter.php';

  } else if ($flag == 2) {
    if ($client) {
      require_once("../client/query.php");
      require_once("../client/verifyAnt.php");
      $content = "../client/interf/ViewClient.php";
    } else {
      //require_once("../notification/BoxInput.php");
      //$title = "Notifica&ccedil;&otilde;es";
      //$content = "../../../main.php";
    }

  } else {
    //$title = "DVE - Declaração do Volume de Negócios Riscos Comerciais - Negócios até 180 dias";    
    $title = "DVN – Declaração de Volume de Negócio";

    if ($comm == 'view') {
      $content = "../dve/interf/view.php";
    } else {
      $content = "../dve/interf/viewDVE.php";
    }
    //dve_header();
    //viewdve_header();
    //viewdve_footer();
    //viewdve_body();

  }



} else if ($comm == 'done') {
  require_once('done.php');
  $title = "Notifica&ccedil;&otilde;es";
  $content = "../../../main.php";

  //-------------------------------------------------------------------------------

} else if ($comm == 'entregavenciada') {

  //require('comite.php');
  $title = 'DVN com entrega vencida';
  $content = "../dve/interf/DveVencida.php";

  //-------------------------------------------------------------------------------

} else if ($comm == 'entregavenciadaok') {
  //require('comite.php');
  $hc_r = $notif->doneRole($idNotification, $db);
  $title = "Notifica&ccedil;&otilde;es";
  $content = "../../../main.php";

  //-------------------------------------------------------------------------------

} else if ($comm == 'comite') {
  require_once('comite.php');
  $title = 'Comitê de Cancelamento';
  $content = "../dve/interf/Comite.php";

} else if ($comm == 'include') {
  require_once('includeImporter.php');
  require_once('input.php');

  $title = "DVN - Declaração do Volume de Negócios Riscos Comerciais - Negócios até 180 dias";

  $content = '../dve/interf/Input.php';

} else if ($comm == 'exclude') {
  require_once('excludeImporter.php');
  require_once('input.php');
  $title = "DVN - Declaração do Volume de Negócios<br>Riscos Comerciais - Negócios até 180 dias";
  $content = '../dve/interf/Input.php';

} else if ($comm == 'modalidade') {
  $title = "DVN - Declaração do Volume de Negócios Riscos Comerciais - Negócios até 180 dias";

  if ($modalidade == 3) {
    require_once("embarcado.php");
    $content = '../dve/interf/Embarcado.php';
  } else if ($modalidade == 1 || $modalidade == 2) {
    require_once('input.php');
    $content = '../dve/interf/Input.php';
  }

} else if ($comm == 'salvatotal') {
  require_once('salvatotal.php');
  require_once('embarcado.php');
  $content = '../dve/interf/Embarcado.php';

} else if ($comm == 'send') {
  require_once('sendDVE.php');

  $link = "../client/Client.php?comm=open&idInform=$idInform&msg=" . urlencode($msg);

  header("Location: " . $link);
  exit;

} else if ($comm == 'editImporter') {
  require_once('editImporter.php');
  require_once('input.php');
  $title = "DVN - Declaração do Volume de Negócios<br>Riscos Comerciais - Negócios até 180 dias";
  $content = '../dve/interf/Input.php';

} else if ($comm == 'consultadve' && $perfilUsuario == 'F') {
  //require('editImporter.php');
  //require('input.php');
  $title = "DVN - Consultas";
  $content = '../dve/interf/consultadve.php';

} else if ($comm == "libera_dve" && $perfilUsuario == 'F') {
  //require("viewDve.php");
  $title = "DVN - Liberação de DVN em atraso ou Vencida";
  $content = '../dve/interf/liberacao_Dve.php';
  // Elias Vaz - Interaktiv - 12/02/2009
} else if ($comm == 'consultadveBanco') {
  //require('editImporter.php');
  //require('input.php');
  $title = "DVE - Consulta";
  $content = '../dve/interf/consultadveBanco.php';

} else if ($comm == 'DVEConsulta') {

  $title = "DVE - Consulta";
  $content = '../dve/interf/DVEConsulta.php';

} else if ($comm == 'editDveBanco') {
  require_once('interf/control_consulta_dve_banco.php');

} else if ($comm == 'consultaDveEmitidaBanco' && $perfilUsuario == 'F') { // Esta comm, abrirá o histórico de consultas de DVE emitidos pelo banco
  $title = "Análise de consultas de DVN emitidas pelo banco";
  $content = '../dve/interf/doc_emitido_dve_banco.php';

  // Elias Interakyiv - 11-02-2010
} else if ($comm == 'NotificacaoNPC') {
  $title = "Consulta de Notificação N.P.C";
  $content = '../dve/interf/notificacaoNPC.php';

  // Elias Interakyiv - 14-05-2010

} else if ($comm == 'consultaPa') {
  $title = "Parcela de Ajuste - Consulta";
  $content = '../dve/interf/consultaPa.php';

} else if ($comm == 'calculaPa') {
  $title = "Parcela de Ajuste - Cálculo";
  $content = '../dve/interf/calculaPa.php';

} else if ($comm == 'calculaPaDet') {
  $title = "Parcela de Ajuste - Detalhe";
  $content = '../dve/interf/calculaPaDet.php';

} else if ($comm == 'atualizaDetPa') {
  $title = "Parcela de Ajuste - Detalhe";
  require_once("../dve/atualizaDetPa.php");
  $content = '../dve/interf/calculaPaDet.php';

} else if ($comm == 'atualizaDetPaOK') {
  $title = "Parcela de Ajuste - Cálculo";
  require_once("../dve/atualizaDetPa.php");
  $content = '../dve/interf/calculaPa.php';

} else if ($comm == 'parcelaPA') {
  $title = "Parcela de Ajuste - Faturamento";
  $content = '../dve/interf/parcelaPa.php';

} else if ($comm == 'exibeDve') {
  $title = "Declaração de Volume de Negócios";
  $content = '../dve/interf/exibeDve.php';

} else if ($comm == 'exibeDveDet') {
  //int 'oioio';    
  $title = "Declaração de Volume de Negócios - Detalhamento";
  $content = '../dve/interf/exibeDveDet.php';

} else if ($comm == 'impdve') {
  require_once("viewDve.php");
  $title = "DVN - Declaração do Volume de Negócios<br>Riscos Comerciais - Negócios até 180 dias";
  $content = "../dve/interf/impDve.php";

} else if ($comm == 'dImpDve') {
  require_once("../dve/dImpDve.php");
  if ($erro) {
    $numDVE = $num;
    $idDVE = "";
    require_once("viewDve.php");
    $title = "DVN - Declaração do Volume de Negócios<br>Riscos Comerciais - Negócios até 180 dias";
    $content = "../dve/interf/impDve.php";
    $client = 1;
  } else {
    $title = "DVN - Declaração do Volume de Negócios<br>Riscos Comerciais - Negócios até 180 dias";
    $content = "../dve/interf/viewImp.php";
    $client = 1;
  }

} else if ($comm == 'importDve') {
  require_once("processardve.php");
  require_once("viewDve.php");
  $client = 1;
  $title = "DVN - Declaração do Volume de Negócios<br>Riscos Comerciais - Negócios até 180 dias";
  $content = "../dve/interf/view.php";

} else if ($comm == 'voltar') {
  require_once("viewDve.php");
  //excluir os importadores da tabela temporaria
  // $SQL = "DELETE FROM tb_Temp_Dve WHERE idInform='$idInform' AND idDve='$idDVE' AND numDve='".intval($numDVE)."'";
  //odbc_exec($db, $SQL);
  $query = "DELETE FROM tb_Temp_Dve WHERE idInform = ? AND idDve = ? AND numDve = ?";
  $stmt = odbc_prepare($db, $query);

  // Substituindo os parâmetros para garantir que os dados sejam tratados de forma segura
  odbc_execute($stmt, array($idInform, $idDVE, intval($numDVE)));

  $client = 1;
  $title = "DVN - Declaração do Volume de Negócios<br>Riscos Comerciais - Negócios até 180 dias";
  $content = "../dve/interf/view.php";

} else if ($comm == 'atualizar_unblock_dve') {


  /*
  //####### ini ####### adicionado por eliel vieira - elumini - em 02/04/2008
  // referente a demanda 1374 - SAD
  */

  //efetua atualizacao no campo unblock_dve em inform
  require_once("../dve/atualizar_unblock_dve.php");

  //redireciona para informacoes gerais
  $comm = "view";
  $content = "../searchClient/interf/InformRes.php";
  require_once("../searchClient/interf/nameClient.php");

  //####### end ####### adicionado por eliel vieira - elumini - em 02/04/2008

} else if ($comm == 'DVELiquidacao') {

  $title = "DVE - Conclusão da Liquidação de Faturamento";
  $content = '../dve/interf/DVELiquidacao.php';

} else if ($comm == 'alterDVELiquidacao') {

  require_once('interf/alterDVELiquidacao.php');
}

if (count($_POST) > 3) {
  // adiciona dados do log após alterações no inform
  $idImporter = isset($idImporter) ? $idImporter : 0;
  // $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 2, $tem,'Inform',''); 
  // $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 2, $tem1,'Importer',$idImporter); 
}

require_once("../../../home.php");

?>