<?php

if (!isset($_SESSION)) {
  session_start();
}
$userID = $_SESSION['userID'];
//extract($_REQUEST);
//error_log("Conte�do de listclient:");
//error_log(print_r($_REQUEST, true));


$comm = isset($_REQUEST['comm']) ? trim($_REQUEST['comm']) : null;
$comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');

$idInform = isset($_REQUEST['idInform']) ? trim($_REQUEST['idInform']) : null;
$idInform = htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');

$idNotification = isset($_REQUEST['idNotification']) ? trim($_REQUEST['idNotification']) : null;
$idNotification = htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8');


// Alterado Hicom (Gustavo) - 21/12/04 - adicionei itens re_oferta e re_tarifacao para bot�es de reenvio
$log_query = "";

ini_set('max_execution_time', 600);
// error_reporting(E_ALL);
// ini_set("display_errors", 1 );


if (!function_exists("unregister")) {
  function unregister()
  {
    session_unregister('login');
    session_unregister('pdfDir');
    session_unregister('key');
    session_unregister('keyParc');
    session_unregister('keyProp');
    session_unregister('downParc');
    session_unregister('downProp');
    session_unregister('msg1');
    session_unregister('msg2');
    session_unregister('msg3');
    session_unregister('msg4');
    session_unregister('msg5');
    session_unregister('msg6');
    session_unregister('msg7');
    session_unregister('msg8');
    session_unregister('msg9');
    session_unregister('msg10');
    session_unregister('msg11a');
    session_unregister('msg11b');
    session_unregister('msg99x');
    session_unregister('msg11c');
    session_unregister('msg12');
    session_unregister('msg13');
    session_unregister('msg14');
    session_unregister('msg15');
    session_unregister('msg16');
    session_unregister('msg17');
    session_unregister('msg18a');
    session_unregister('msg18b');
    session_unregister('msg19');
    session_unregister('msg20');
    session_unregister('totasseg');
    session_unregister('contract');
    session_unregister('fatNum');
    session_unregister('apoNum');
    session_unregister('contract');
    session_unregister('end');
    session_unregister('cep');
    session_unregister('name');
    session_unregister('cnpj');
    session_unregister('valPar');
    session_unregister('valParExt');
    session_unregister('prPrint');
    session_unregister('valPreExt');
    session_unregister('numPre');
    session_unregister('five');
    session_unregister('today');
    session_unregister('txRise');
    session_unregister('periodMaxCred');
    session_unregister('currency');
    session_unregister('limPagIndeniz');
    session_unregister('txAnalize');
    session_unregister('txMonitor');
    session_unregister('currencyAnalize');
    session_unregister('prodUnit');
    session_unregister('percCoverage');
    session_unregister('validCot');
    session_unregister('sisSegQueries');
    session_unregister('idSeg');
    session_unregister('address');
  }

  function getTimeStamp($date)
  {
    if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})/', $date, $res)) {
      return mktime(0, 0, 0, $res[2], $res[3], $res[1]);
    }
    return 0;
  }

  function faltam($fim, $dias)
  {
    $secs = ($dias - 1) * 24 * 3600;
    return getTimeStamp($fim) - time() <= $secs;
  }

}

require_once("../rolePrefix.php");


$idInform = $field->getField("idInform");

$idNotification = $field->getField("idNotification");

if (count($_POST) > 2) {
  // armazena dados do log
  $tem = $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 1, '', 'Inform', '');
  $tem1 = $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 1, '', 'Importer', (isset($_REQUEST['idImporter']) ? $_REQUEST['idImporter'] : false));
}

if ($comm == "historico_transacao") {
  $title = 'Hist�rico das Transa��es do Informe e Compradores';
  $content = '../executive/interf/historico_transacao.php';
} else if (!$idInform || !$idNotification) {
  $title = "Erro : Informe inv�lido";
  $content = "../../interf/Error.php";

} else if ($comm == "view" || $comm == "open") {
  $title = "Novo Informe";
  $content = "../inform/interf/InformRes.php";
  //$content = "../executive/interf/InformRes.php";
  require_once("view.php");

} else if ($comm == "clientAddImporter") {
  $title = "Notifica��es";
  $content = "../../../main.php";
  /*$w = odbc_exec($db, "UPDATE NotificationR SET state = 2, i_Usuario = " . $_SESSION["userID"] . ", d_Encerramento = GETDATE() WHERE id = $idNotification");
            $msg = "Aviso de Inclus�o de Importador Recebido";

            //criado por Wagner 29/08/2008
            if ($w) {
              $log_query .= "UPDATE NotificationR SET state = 2, i_Usuario = " . $_SESSION["userID"] . ", d_Encerramento = GETDATE() WHERE id = $idNotification";
            }

            if ($includeOld) {
              $msg .= "<br>O importador ser� inclu�do na ap�lice vigente e na renova��o";
            }*/

  // Valida��o de vari�veis
  $idNotification = intval($idNotification); // Garantir que $idNotification seja um n�mero inteiro
  $userID = intval($_SESSION["userID"]); // Garantir que $_SESSION["userID"] seja um n�mero inteiro

  // Prepara��o da consulta com par�metros
  $query = "UPDATE NotificationR SET state = ?, i_Usuario = ?, d_Encerramento = GETDATE() WHERE id = ?";

  // Prepara a consulta
  $stmt = odbc_prepare($db, $query);

  if ($stmt) {
    // Executa a consulta com os par�metros
    $params = [2, $userID, $idNotification];
    $w = odbc_execute($stmt, $params);

    if ($w) {
      // Registro do log da query
      $log_query = "UPDATE NotificationR SET state = 2, i_Usuario = " . $userID . ", d_Encerramento = GETDATE() WHERE id = " . $idNotification;
    }
  }

  // Mensagem de inclus�o
  $msg = "Aviso de Inclus�o de Importador Recebido";

  if ($includeOld) {
    $msg .= "<br>O importador ser� inclu�do na ap�lice vigente e na renova��o";
  }

  odbc_free_result($stmt);

} else if ($comm == "generalInformation") {
  $title = "Informa��es Gerais";
  $content = "../executive/interf/GeneralInf.php";

  // Guarda no banco as informa��es gerais do segurado
} else if ($comm == "generalSubmit") {
  require_once("generalSubmit.php");
  if ($forward == "success") {
    $title = "Distribui��o de Vendas por Tipo de Pagamento";
    $content = "../executive/interf/VolVendExt.php";
  } else {
    $title = "Informa��es Gerais";
    $content = "../executive/interf/GeneralInf.php";
  }

  // Monta a tela de entrada de volume de exporta��o
} else if ($comm == "volVendExt") {
  $title = "Distribui��o de Vendas por Tipo de Pagamento";
  $content = "../executive/interf/VolVendExt.php";

  // Guarda no banco as informa��es de volume de exporta��o
} else if ($comm == "volSubmit") {
  require_once("volSubmit.php");
  if ($forward == "success") {
    $title = " Distribui��o de Vendas a Prazo por Pa�s";
    $content = "../executive/interf/SegVendExt.php";
  } else {
    $title = "Distribui��o de Vendas por Tipo de Pagamento";
    $content = "../executive/interf/VolVendExt.php";
  }

  // Monta a tela de segmenta��o de vendas
} else if ($comm == "segVendExt") {
  $title = "  Distribui��o de Vendas a Prazo por Pa�s";
  $content = "../executive/interf/SegVendExt.php";

  // voltar para tela anterior
} else if ($comm == "back") {
  $title = " Informa��es Gerais";
  $content = "../executive/interf/GeneralInf.php";

  // voltar para tela volvend 
} else if ($comm == "voltar") {
  $title = " Distribui��o de Vendas por Tipo de Pagamento";
  $content = "../executive/interf/VolVendExt.php";

  // voltar para tela segvend 
} else if ($comm == "anterior") {
  $title = "  Distribui��o de Vendas a Prazo por Pa�s";
  $content = "../executive/interf/SegVendExt.php";

  // voltar para tela buyers 
} else if ($comm == "volte") {
  require_once("buyers.php");
  $content = "../executive/interf/Buyers.php";
  $title = ($idAnt ? '' : 'Principais ') . "Compradores";

  // Guarda no banco as informa��es de segmenta��o de exporta��es
} else if ($comm == "segSubmit") {
  require_once("segSubmit.php");
  if ($forward == "success") {
    require_once("buyers.php");
    $content = "../executive/interf/Buyers.php";
    $title = ($idAnt ? '' : 'Principais ') . "Compradores";
  }

  // Monta a tela de previs�o de financiamento
} else if ($comm == "prevFinanc") {
  $title = "Previs�o de Financiamento";
  $content = "../executive/interf/PrevFinanc.php";

  // Guarda no banco as informa��es da previs�o de financiamento
} else if ($comm == "prevSubmit") {
  require_once("prevSubmit.php");
  if ($forward == "success") {
    require_once("buyers.php");
    $content = "../executive/interf/Buyers.php";
    $title = ($idAnt ? '' : 'Principais ') . "Compradores";
  } else {
    $title = "Previs�o de Financiamento";
    $content = "../executive/interf/PrevFinanc.php";
  }

  // Monta a tela de rela��o de compradores
} else if ($comm == "buyers") {
  require_once("buyers.php");
  $content = "../executive/interf/Buyers.php";
  $title = ($idAnt ? '' : 'Principais ') . "Compradores";

  // Guarda no banco as informa��es dos compradores
} else if ($comm == "buySubmit") {
  require_once("buySubmit.php");
  if ($forward == "success") {
    $title = "Hist�rico de Perdas";
    $content = "../executive/interf/Lost.php";
  } else {
    require_once("buyers.php");
    $content = "../executive/interf/Buyers.php";
    $title = ($idAnt ? '' : 'Principais ') . "Compradores";
  }

  // Guarda no banco as informa��es dos compradores
} else if ($comm == "lost") {
  $title = "Hist�rico de Perdas";
  $content = "../executive/interf/Lost.php";
} else if ($comm == "lostSubmit") {
  require_once("lostSubmit.php");
  if ($forward == "success") {
    $title = "Simula��o de Pr�mio";
    $content = "../executive/interf/Simulation.php";
  } else {
    $title = "Hist�rico de Perdas";
    $content = "interf/Lost.php";
  }

  // Apresenta��o da simula��o de pr�mio
} else if ($comm == "simul") {
  $title = "Simula��o de Pr�mio";
  $content = "../executive/interf/Simulation.php";

  // Conclus�o do informe
} else if ($comm == "done") {
  $vig = false;
  if ($field->getField("mot") != "Voltar") {
    require_once("done.php");
  }

  if ($forward == "success") {
    $title = "Notifica��es";
    $content = "../../../main.php";
  } else {
    $content = "../executive/interf/InformRes.php";
    require_once("view.php");
  }
} else if ($comm == "offer") {



  require_once("view.php");

  $content = "../executive/interf/EnvOffer.php";

  // require_once("../../../home.php");


} else if ($comm == "notif") {
  //unregister();


  $title = "Notifica��es";
  //$content = "../../../main.php";die


  // Se a n�o for emitida a proposta os arquivos ser�o deletados
  if (!empty($_REQUEST['key'])) {
    include_once("../../../config.php");

    $key = basename($_REQUEST['key']); // Remove poss�veis caminhos relativos

    $arq_parcela = $key . 'parcela.pdf';
    $arq_proposta = $key . 'prop.pdf';

    // Caminhos completos e seguros
    $caminho_parcela = realpath($pdfDir . DIRECTORY_SEPARATOR . $arq_parcela);
    $caminho_proposta = realpath($pdfDir . DIRECTORY_SEPARATOR . $arq_proposta);

    // Verifica se os arquivos realmente pertencem ao diret�rio esperado antes de deletar
    if ($caminho_parcela !== false && strpos($caminho_parcela, realpath($pdfDir)) === 0) {
        unlink($caminho_parcela);
    }

    if ($caminho_proposta !== false && strpos($caminho_proposta, realpath($pdfDir)) === 0) {
        unlink($caminho_proposta);
    }
}


  $content = "../../../main.php";
  //     echo "fub�~ufs�oidfh�suofhodsufusdbfubsudfbsdufnosdmnfosjf[pojsd[p";die();

} else if ($comm == "cancelInf") {
  /*$w = odbc_exec($db, "UPDATE Inform SET state = 9 WHERE id = $idInform");

        //criado por Wagner 29/08/2008
        if ($w) {
          $log_query .= "UPDATE Inform SET state = 9 WHERE id = $idInform";
        }

        $x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");
        if (odbc_fetch_row($x)) {
          $id = odbc_result($x, 1);
          $fim = odbc_result($fim, 2);
          if (!$fim) {
            $w = odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");

            //criado por Wagner 29/08/2008
            if ($w) {
              $log_query .= "update AnaliseInform set fim=getdate() where id=$id";
            }
          }
        }
        //Criado Por Tiago V N - 19/10/2005
        //Log do Cancelamento ( Informe cancelado em oferta )
        $sql = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('6'," .
          "'$userID', '$idInform','" . date("Y") . "-" . date("m") . "-" . date("d") .
          "','" . date("H") . ":" . date("i") . ":" . date("s") . "')";
        if (odbc_exec($db, $sql)) {
          $sql_id = "SELECT @@IDENTITY AS 'id_Log'";
          $cur = odbc_result(odbc_exec($db, $sql_id), 1);
          $sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) " .
            "values ('$cur', 'Cancelamento', 'Oferta', 'Altera��o')";
          $rs = odbc_exec($db, $sql);

          ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
          //CRIADO POR WAGNER
          // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA A��O REGISTRADA NA TABELA Log_Detalhes
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

          if ($rs) {
            $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
            $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
            $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) " .
              "values ('$cur', '" . str_replace("'", "", $log_query) . "')";

            //echo $sql;
            odbc_exec($db, $sql);
          }//fim if 
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




        } else {
          $msg = "Erro no incluir do Log";
        }
        $sql = "select name from Inform where id='$idInform'";
        $cur = odbc_exec($db, $sql);
        odbc_fetch_row($cur);
        $clientR = odbc_result($cur, 1);

        $notif->newInfCredito($userID, $clientR, $idInform, $db);
        $notif->doneRole($idNotification, $db);
        unregister();
        $title = "Notifica��es";
        //$content = "../../../main.php";
        $content = "../../../main.php"; */

  // Valida��o de vari�veis
  $idInform = intval($idInform); // Garante que $idInform seja um n�mero inteiro
  $userID = intval($userID); // Garante que $userID seja um n�mero inteiro

  // Atualiza��o de Inform
  $query = "UPDATE Inform SET state = ? WHERE id = ?";
  $stmt = odbc_prepare($db, $query);
  $w = odbc_execute($stmt, [9, $idInform]);
  if ($stmt)
    odbc_free_result($stmt);

  if ($w) {
    $log_query .= sprintf("UPDATE Inform SET state = 9 WHERE id = %d", $idInform);
  }

  // Verifica��o de AnaliseInform
  $query = "SELECT id, fim FROM AnaliseInform WHERE idInform = ?";
  $stmt = odbc_prepare($db, $query);
  odbc_execute($stmt, [$idInform]);

  if (odbc_fetch_row($stmt)) {
    $id = odbc_result($stmt, 1);
    $fim = odbc_result($stmt, 2);
    if (!$fim) {
      $query = "UPDATE AnaliseInform SET fim = GETDATE() WHERE id = ?";
      $stmtUpdate = odbc_prepare($db, $query);
      $w = odbc_execute($stmtUpdate, [$id]);
      if ($stmtUpdate)
        odbc_free_result($stmtUpdate);

      if ($w) {
        $log_query .= sprintf("UPDATE AnaliseInform SET fim = GETDATE() WHERE id = %d", $id);
      }
    }
  }
  if ($stmt)
    odbc_free_result($stmt);

  // Inser��o no Log
  $query = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) VALUES (?, ?, ?, ?, ?)";
  $stmt = odbc_prepare($db, $query);
  $date = date("Y-m-d");
  $time = date("H:i:s");
  $w = odbc_execute($stmt, [6, $userID, $idInform, $date, $time]);

  if ($w) {
    $query = "SELECT @@IDENTITY AS id_Log";
    $stmt = odbc_exec($db, $query);
    $idLog = odbc_result($stmt, 1);
    if ($stmt)
      odbc_free_result($stmt);

    $query = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) VALUES (?, ?, ?, ?)";
    $stmt = odbc_prepare($db, $query);
    odbc_execute($stmt, [$idLog, 'Cancelamento', 'Oferta', 'Altera��o']);
    if ($stmt)
      odbc_free_result($stmt);

    $query = "SELECT @@IDENTITY AS id_detalhes";
    $stmt = odbc_exec($db, $query);
    $idDetalhes = odbc_result($stmt, 1);
    if ($stmt)
      odbc_free_result($stmt);

    $query = "INSERT INTO Log_Detalhes_Query (id_detalhes, query) VALUES (?, ?)";
    $stmt = odbc_prepare($db, $query);
    odbc_execute($stmt, [$idDetalhes, str_replace("'", "", $log_query)]);
    if ($stmt)
      odbc_free_result($stmt);
  } else {
    $msg = "Erro no incluir do Log";
  }

  // Obten��o do nome do cliente
  $query = "SELECT name FROM Inform WHERE id = ?";
  $stmt = odbc_prepare($db, $query);
  odbc_execute($stmt, [$idInform]);
  odbc_fetch_row($stmt);
  $clientR = odbc_result($stmt, 1);
  if ($stmt)
    odbc_free_result($stmt);

  // Notifica��es
  $notif->newInfCredito($userID, $clientR, $idInform, $db);
  $notif->doneRole($idNotification, $db);

  // Finaliza��o
  unregister();
  $title = "Notifica��es";
  $content = "../../../main.php";


} else if ($comm == "sendOffer") {
  require_once("sendOffer.php");

  if ($forward == "success") {
    $title = "Notifica��es";
    //$content = "../../../main.php";
    $content = "../../../main.php";
  } else {
    require_once("view.php");
    $content = "../executive/interf/EnvOffer.php";
  }
} else if ($comm == "sendProp") {

  if ($_REQUEST['idInform'] != '') {
    $idInform = $_REQUEST['idInform'];
    $idNotification = $_REQUEST['idNotification'];
    $key = $_REQUEST['key'];

    $userID = $_REQUEST['ids'];
    $_SESSION['userID'] = $userID;

  }



  //Vericar este include 
  require_once("sendProp.php");


  if ($forward == "success") {
    $title = "Notifica��es";
    //$content = "../../../main.php";
    $content = "../../../main.php";
  } else {
    require_once("view.php");
    $content = "../executive/interf/EnvOffer.php";
  }
  //unregister();

} else if ($comm == "viewProp") { // Mostra os PDFs na tela

  // Valida��o de vari�veis
  $idInform = intval($idInform); // Garante que $idInform seja um n�mero inteiro

  // Consulta para buscar state e idAnt
  $query = "SELECT state, idAnt FROM Inform WHERE id = ?";
  $stmt = odbc_prepare($db, $query);
  odbc_execute($stmt, [$idInform]);

  if (odbc_fetch_row($stmt)) {
    $status = odbc_result($stmt, 1);
    $idAnt = odbc_result($stmt, 2);
  }
  if ($stmt)
    odbc_free_result($stmt);

  // Consulta para buscar endValidity, se necess�rio
  if ($idAnt) {
    $query = "SELECT endValidity FROM Inform WHERE id = ? AND state <> 9";
    $stmt = odbc_prepare($db, $query);
    odbc_execute($stmt, [$idAnt]);

    if (odbc_fetch_row($stmt)) {
      $finalVigencia = odbc_result($stmt, 1);
    }
    if ($stmt)
      odbc_free_result($stmt);
  }

  // Defini��o de $file
  $file = isset($file) ? $file : 'Prop';

  // Defini��o de $key
  $key = isset($key) ? $key : session_id() . time();

  // Inclui o arquivo de visualiza��o
  require_once("viewProp.php");
  $title = "Proposta";
  require_once("../executive/interf/indexViewProp.php");
} else if ($comm == 'retarifar') {
  require_once('retarifar.php');
  $title = "Notifica��es";
  $content = "../../../main.php";
} else if ($comm == 'devolve') {
  require_once('devolver.php');
  $title = "Notifica��es";
  $content = "../../../main.php";
} else if ($comm == 'devcredito') {
  require_once('reanalise.php');
  $title = "Notifica��es";
  $content = "../../../main.php";

} else if ($comm == 'renovacao') {

  /*if (isset($done)) {

        if ($notif->doneRole($idNotification, $db)) {
          $log_query .= "UPDATE NotificationR SET state = 2, i_Usuario = " . $_SESSION["userID"] . ", d_Encerramento = GETDATE() WHERE id = $idNotification";
        }

        //Registrar no Log (Notifica��o foi ocultada) - Criado Por Tiago V N - 16/03/2006
        $sql = "Insert Into Log (tipoLog, id_User, Inform, data, hora) Values
          ('30', '$userID', '$idInform','" . date("Y") . "-" . date("m") . "-" . date("d") . "'," .
          "'" . date("H") . ":" . date("i") . ":" . date("s") . "')";

        $result = odbc_exec($db, $sql);

        if ($result) {
          $sql_id = "SELECT @@IDENTITY AS 'id_Log'";
          $cur = odbc_result(odbc_exec($db, $sql_id), 1);

          $sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) values " .
            "('$cur', 'notifica��o', '2', 'In�cio do per�odo de Renova��o')";
          $rs = odbc_exec($db, $sql);

          if ($rs) {
            $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
            $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);

            $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) " .
              "values ('$cur', '" . str_replace("'", "", $log_query) . "')";

            odbc_exec($db, $sql);
          }//fim if    
        }

        $title = "Notifica��es";
        $content = "../../../main.php";

      }*/
  if (isset($done)) {
    if ($notif->doneRole($idNotification, $db)) {
      $log_query = sprintf(
        "UPDATE NotificationR SET state = 2, i_Usuario = %d, d_Encerramento = GETDATE() WHERE id = %d",
        intval($_SESSION["userID"]),
        intval($idNotification)
      );
    }

    // Registrar no Log (Notifica��o foi ocultada) - Criado Por Tiago V N - 16/03/2006
    $query = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) VALUES (?, ?, ?, ?, ?)";
    $stmt = odbc_prepare($db, $query);
    $result = odbc_execute($stmt, [30, $userID, $idInform, date("Y-m-d"), date("H:i:s")]);
    if ($stmt)
      odbc_free_result($stmt);

    if ($result) {
      $query = "SELECT @@IDENTITY AS id_Log";
      $stmt = odbc_exec($db, $query);
      $cur = odbc_result($stmt, 1);
      if ($stmt)
        odbc_free_result($stmt);

      $query = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) VALUES (?, ?, ?, ?)";
      $stmt = odbc_prepare($db, $query);
      odbc_execute($stmt, [$cur, 'notifica��o', '2', 'In�cio do per�odo de Renova��o']);
      if ($stmt)
        odbc_free_result($stmt);

      $query = "SELECT @@IDENTITY AS id_detalhes";
      $stmt = odbc_exec($db, $query);
      $cur = odbc_result($stmt, 1);
      if ($stmt)
        odbc_free_result($stmt);

      $query = "INSERT INTO Log_Detalhes_Query (id_detalhes, query) VALUES (?, ?)";
      $stmt = odbc_prepare($db, $query);
      odbc_execute($stmt, [$cur, str_replace("'", "", $log_query)]);
      if ($stmt)
        odbc_free_result($stmt);
    }

    $title = "Notifica��es";
    $content = "../../../main.php";
  } /*elseif (isset($cancelar)) {
      $sql = "Update Inform set state='9' where id = '$idInform'";
      $exec = odbc_exec($db, $sql);

      $sql = "Insert Into Log (tipoLog, id_User, Inform, data, hora) Values
          ('28', '$userID', '$idInform','" . date("Y") . "-" . date("m") . "-" . date("d") . "'," .
        "'" . date("H") . ":" . date("i") . ":" . date("s") . "')";

      $result = odbc_exec($db, $sql);

      if ($result) {
        $log_query .= $sql;
      }

      if ($result) {
        $sql_id = "SELECT @@IDENTITY AS 'id_Log'";
        $cur = odbc_result(odbc_exec($db, $sql_id), 1);

        $sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) values " .
          "('$cur', 'Informe', '-', 'In�cio de Remova��o')";
        $rs = odbc_exec($db, $sql);

        if ($rs) {
          $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
          $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);

          $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) " .
            "values ('$cur', '" . str_replace("'", "", $log_query) . "')";

          odbc_exec($db, $sql);
        }//fim if 

      } else {
        $msg = "Erro na inclus�o do Log";
      }

      $notif->doneRole($idNotification, $db);
      $title = "Notifica��es";
      $content = "../../../main.php";

    }*/ elseif (isset($cancelar)) {
    // Atualizar o estado de Inform
    $query = "UPDATE Inform SET state = ? WHERE id = ?";
    $stmt = odbc_prepare($db, $query);
    $exec = odbc_execute($stmt, [9, intval($idInform)]);
    if ($stmt)
      odbc_free_result($stmt);

    // Inserir registro no Log
    $query = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) VALUES (?, ?, ?, ?, ?)";
    $stmt = odbc_prepare($db, $query);
    $result = odbc_execute($stmt, [28, intval($userID), intval($idInform), date("Y-m-d"), date("H:i:s")]);
    if ($stmt)
      odbc_free_result($stmt);

    if ($result) {
      // Adicionar a query no log
      $log_query = sprintf(
        "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) VALUES (28, %d, %d, '%s', '%s')",
        intval($userID),
        intval($idInform),
        date("Y-m-d"),
        date("H:i:s")
      );

      // Obter o ID do Log rec�m-criado
      $query = "SELECT @@IDENTITY AS id_Log";
      $stmt = odbc_exec($db, $query);
      $idLog = odbc_result($stmt, 1);
      if ($stmt)
        odbc_free_result($stmt);

      // Inserir detalhes no Log_Detalhes
      $query = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) VALUES (?, ?, ?, ?)";
      $stmt = odbc_prepare($db, $query);
      $rs = odbc_execute($stmt, [$idLog, 'Informe', '-', 'In�cio de Remo��o']);
      if ($stmt)
        odbc_free_result($stmt);

      if ($rs) {
        // Obter o ID do Log_Detalhes rec�m-criado
        $query = "SELECT @@IDENTITY AS id_detalhes";
        $stmt = odbc_exec($db, $query);
        $idDetalhes = odbc_result($stmt, 1);
        if ($stmt)
          odbc_free_result($stmt);

        // Inserir query no Log_Detalhes_Query
        $query = "INSERT INTO Log_Detalhes_Query (id_detalhes, query) VALUES (?, ?)";
        $stmt = odbc_prepare($db, $query);
        odbc_execute($stmt, [$idDetalhes, str_replace("'", "", $log_query)]);
        if ($stmt)
          odbc_free_result($stmt);
      }
    } else {
      $msg = "Erro na inclus�o do Log";
    }

    // Finalizar notifica��o
    $notif->doneRole($idNotification, $db);

    // Definir t�tulo e conte�do
    $title = "Notifica��es";
    $content = "../../../main.php";
  } else {

    //require_once('renovacao.php');
    require('renovacao.php');

    //die('?');
    $title = "Cliente em per�odo de renova��o";
    $content = "../executive/interf/Renovacao.php";
  }


} else if ($comm == 'notif') {
  exit();
  $title = "Notifica��es";
  $content = "../../../main.php";
} else if ($comm == 'reimprime') { // reimprime a proposta
  require_once('reimprime.php');
  $title = 'Segunda via da Proposta';
  $content = '../executive/interf/SegundaVia.php';
} else if ($comm == 'reestudo') { // envia para reestudo
  require_once('reestudo.php');
  $title = "Notifica��es";
  $content = "../../../main.php";
} else if ($comm == 'reenviarEmail') {
  $por_email = 1;
  require_once('reenviar.php');
  $comm = 'view';
  require_once('../searchClient/ListClient.php');
  return;
} else if ($comm == 'reenviar') {
  require_once('reenviar.php');
  $title = 'Notifica��es';
  $content = '../notification/interf/ViewBox.php';

  // alterado Hicom (Gustavo)  
} else if ($comm == 're_oferta') {
  require_once('reenviarOferta.php');
  $title = 'Notifica��es';
  $content = '../notification/interf/ViewBox.php';
} else if ($comm == 're_tarifacao') {
  require_once('reenviarTarifacao.php');
  $title = 'Notifica��es';
  $content = '../notification/interf/ViewBox.php';
  // fim alterado Hicom  

} else if ($comm == 'cinco_meses') {
  require_once('cinco_meses.php');
  if (!$done) {
    $title = 'Importadores com cr�dito concedido h� mais de 5 meses';
    $content = '../executive/interf/Cinco_meses.php';
  } else {
    $title = "Notifica��es";
    $content = "../../../main.php";
  }
} else {
  $title = "Servi�o inexistente";
  $content = "../../interf/Error.php";
}


if (count($_POST) > 2) {
  // adiciona dados do log ap�s altera��es no inform
  $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 2, $tem, 'Inform', '');
  $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 2, $tem1, 'Importer', (isset($_REQUEST['idImporter']) ? $_REQUEST['idImporter'] : false));
}
require_once("../../../home.php");

?>