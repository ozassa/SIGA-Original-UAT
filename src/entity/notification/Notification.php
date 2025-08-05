<?php

//####### ini ####### adicionado por eliel vieira - elumini - 13/03/2008
/*
// adicionado no insert das notificacoes, o idInform
*/
//####### end ####### adicionado por eliel vieira - elumini - 13/03/2008


//ALTERADO HICOM mes 04
//ALTERADO HICOM 27/10/2004 (Gustavo)
//Alterado Hicom (Gustavo) - 23/12/04 - Altera��o do processo de solicita��o de cobertura para juros de mora

class Notification
{
  
  // Função auxiliar para validar e sanitizar cookies/keys
  private function validateKey($key) {
    if (empty($key) || !is_string($key)) {
      return false;
    }
    // Remover caracteres perigosos
    $key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
    return strlen($key) > 0 ? $key : false;
  }
  
  // Função auxiliar para executar queries parametrizadas seguras
  private function executeSecureQuery($db, $query, $params = []) {
    $stmt = odbc_prepare($db, $query);
    if (!$stmt) {
      error_log("Erro ao preparar query: " . $query);
      return false;
    }
    
    if (!empty($params)) {
      if (!odbc_execute($stmt, $params)) {
        error_log("Erro ao executar query parametrizada: " . $query);
        return false;
      }
    } else {
      if (!odbc_execute($stmt)) {
        error_log("Erro ao executar query: " . $query);
        return false;
      }
    }
    
    return $stmt;
  }

  function existsRole($notification, $db)
  {
    $sql = "SELECT notification, id FROM NotificationR WHERE notification = ? AND state = 1";
    $cur = odbc_prepare($db, $sql);
    odbc_execute($cur, [$notification]);

    if (odbc_fetch_row($cur)) {
      $id = odbc_result($cur, 'id');
      odbc_free_result($cur);
      $updateSql = "UPDATE NotificationR SET state = 2 WHERE id = ?";
      $updateStmt = odbc_prepare($db, $updateSql);
      odbc_execute($updateStmt, [$id]);
      odbc_free_result($updateStmt);
      return true;
    }

    return false;
  }


  function existsRolePlus($notification, $hc_id, $db)
  {
    $sql = "SELECT notification, id FROM NotificationR WHERE notification = ? AND state = 1 AND idInform = ?";
    $cur = odbc_prepare($db, $sql);
    odbc_execute($cur, [$notification, $hc_id]);

    if (odbc_fetch_row($cur)) {
      $id = odbc_result($cur, 'id');
      odbc_free_result($cur);

      $updateSql = "UPDATE NotificationR SET state = 2 WHERE id = ?";
      $updateStmt = odbc_prepare($db, $updateSql);
      odbc_execute($updateStmt, [$id]);
      odbc_free_result($updateStmt);

      return true;
    }

    odbc_free_result($cur);
    return false;
  }


  // Insere uma linha de log na tabela de transa��es banco de dados
  function log($u, $d, $db)
  {
    $sql = "INSERT INTO TransactionLog (idUser, description) VALUES (?, ?)";
    $r = odbc_prepare($db, $sql);
    odbc_execute($r, [$u, $d]);
    odbc_free_result($r);
  }


  // Indicar como efetuada uma notifica��o de papel (Role)
  function doneRole($idNotification, $db)
  {
    $sql = "UPDATE NotificationR SET state = 2 WHERE id = ?";
    $r = odbc_prepare($db, $sql);
    $result = odbc_execute($r, [$idNotification]);
    odbc_free_result($r);

    return $result;
  }


  // Indicar como efetuada uma notifica��o de papel (User)
  function doneUser($idNotification, $db)
  {
    $r = odbc_exec(
      $db,
      "UPDATE NotificationU SET state = 2 WHERE id = $idNotification"
    );
    return $r;
  }

  // Gera��o de notifica��es de cobertura Juros Mora - Financeiro e BackOffice
  function jurosMoraF($u, $idInform, $n, $db, $idJuros)
  {
    // Insere log
    $notification = "Solicita��o de Juros de Mora [$n]";

    if ($this->existsRole($notification, $db)) {
      return false;
    }

    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time() . '2';

    // Inser��o na tabela NotificationR
    $sqlInsert = "INSERT INTO NotificationR (cookie, notification, idInform, tp_notification_id) VALUES (?, ?, ?, 36)";
    $stmtInsert = odbc_prepare($db, $sqlInsert);
    if (!odbc_execute($stmtInsert, [$key, $notification, $idInform])) {
      odbc_free_result($stmtInsert);
      return false;
    }
    odbc_free_result($stmtInsert);

    // Recupera o ID da notifica��o
    $sqlSelect = "SELECT id FROM NotificationR WHERE cookie = ?";
    $stmtSelect = odbc_prepare($db, $sqlSelect);
    odbc_execute($stmtSelect, [$key]);

    if (!odbc_fetch_row($stmtSelect)) {
      odbc_free_result($stmtSelect);
      return false;
    }

    $idNotification = odbc_result($stmtSelect, 1);
    odbc_free_result($stmtSelect);

    // Atualiza a notifica��o com o link correto
    $sqlUpdate = "UPDATE NotificationR SET link = ? WHERE id = ?";
    $stmtUpdate = odbc_prepare($db, $sqlUpdate);
    $link = "../financ/Financ.php?comm=juros&idInform=$idInform&idJuros=$idJuros&idNotification=$idNotification";

    if (!odbc_execute($stmtUpdate, [$link, $idNotification])) {
      odbc_free_result($stmtUpdate);
      return false;
    }
    odbc_free_result($stmtUpdate);

    // Insere na tabela RoleNotification
    $sqlRoleNotification = "INSERT INTO RoleNotification (idRole, idNotification) VALUES (8, ?)";
    $stmtRoleNotification = odbc_prepare($db, $sqlRoleNotification);

    if (!odbc_execute($stmtRoleNotification, [$idNotification])) {
      odbc_free_result($stmtRoleNotification);
      return false;
    }
    odbc_free_result($stmtRoleNotification);

    return $ok;
  }


  // Gera��o de notifica��es (Informe Cancelado)
  function newInfCredito($u, $n, $idInform, $db)
  {
    if ($idInform != "") {
      $this->hidden_notifications($u, $n, $idInform, $db);
    }

    $notification = "Informe Cancelado [$n]";

    if ($this->existsRole($notification, $db)) {
      return false;
    }

    // Insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    // Inser��o na tabela NotificationR
    $sqlInsert = "INSERT INTO NotificationR (cookie, notification, idInform, tp_notification_id) VALUES (?, ?, ?, 26)";
    $stmtInsert = odbc_prepare($db, $sqlInsert);
    if (!odbc_execute($stmtInsert, [$key, $notification, $idInform])) {
      odbc_free_result($stmtInsert);
      return false;
    }
    odbc_free_result($stmtInsert);

    // Recupera o ID da notifica��o
    $sqlSelect = "SELECT id FROM NotificationR WHERE cookie = ?";
    $stmtSelect = odbc_prepare($db, $sqlSelect);
    odbc_execute($stmtSelect, [$key]);

    if (!odbc_fetch_row($stmtSelect)) {
      odbc_free_result($stmtSelect);
      return false;
    }

    $idNotification = odbc_result($stmtSelect, 1);
    odbc_free_result($stmtSelect);

    // Atualiza a notifica��o com o link correto
    $sqlUpdate = "UPDATE NotificationR SET link = ? WHERE id = ?";
    $stmtUpdate = odbc_prepare($db, $sqlUpdate);
    $link = "../searchClient/SearchApolice.php?comm=viewImportador&idInform=$idInform&idNotification=$idNotification";

    if (!odbc_execute($stmtUpdate, [$link, $idNotification])) {
      odbc_free_result($stmtUpdate);
      return false;
    }
    odbc_free_result($stmtUpdate);

    // Insere na tabela RoleNotification
    $sqlRoleNotification = "INSERT INTO RoleNotification (idRole, idNotification) VALUES (10, ?)";
    $stmtRoleNotification = odbc_prepare($db, $sqlRoleNotification);

    if (!odbc_execute($stmtRoleNotification, [$idNotification])) {
      odbc_free_result($stmtRoleNotification);
      return false;
    }
    odbc_free_result($stmtRoleNotification);

    return $ok;
  }


  //--------------------------------------------------------------------------------------
  function hidden_notifications($u, $n, $idInform, $db)
  {
    $ok = true;

    // Define hist�rico do log
    $notification = "Notifica��es canceladas [$n]";

    // Insere log
    $this->log($u, $notification, $db);

    // Verifica se h� notifica��es ativas
    $sqlSelect = "SELECT id FROM NotificationR WHERE state = 1 AND idInform = ?";
    $stmtSelect = odbc_prepare($db, $sqlSelect);
    odbc_execute($stmtSelect, [$idInform]);

    if (odbc_fetch_row($stmtSelect)) {
      odbc_free_result($stmtSelect);

      // Atualiza as notifica��es para state = 2
      $sqlUpdate = "UPDATE NotificationR SET state = 2 WHERE idInform = ?";
      $stmtUpdate = odbc_prepare($db, $sqlUpdate);
      odbc_execute($stmtUpdate, [$idInform]);
      odbc_free_result($stmtUpdate);
    } else {
      odbc_free_result($stmtSelect);
    }

    return $ok;
  }


  // Altera��o por Tiago V N - 25/10/2005
// Gera��o de notifica��es (Informe sem executivo)
  function newInfExcutivo($u, $n, $idInform, $db)
  {
    $notification = "Informe sem Executivo Respons�vel [$n]";

    if ($this->existsRole($notification, $db)) {
      return false;
    }

    // Insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    // Inser��o na tabela NotificationR
    $sqlInsert = "INSERT INTO NotificationR (cookie, notification, idInform, tp_notification_id) VALUES (?, ?, ?, 37)";
    $stmtInsert = odbc_prepare($db, $sqlInsert);
    if (!odbc_execute($stmtInsert, [$key, $notification, $idInform])) {
      odbc_free_result($stmtInsert);
      return false;
    }
    odbc_free_result($stmtInsert);

    // Recupera o ID da notifica��o
    $sqlSelect = "SELECT id FROM NotificationR WHERE cookie = ?";
    $stmtSelect = odbc_prepare($db, $sqlSelect);
    odbc_execute($stmtSelect, [$key]);

    if (!odbc_fetch_row($stmtSelect)) {
      odbc_free_result($stmtSelect);
      return false;
    }

    $idNotification = odbc_result($stmtSelect, 1);
    odbc_free_result($stmtSelect);

    // Atualiza a notifica��o com o link correto
    $sqlUpdate = "UPDATE NotificationR SET link = ? WHERE id = ?";
    $stmtUpdate = odbc_prepare($db, $sqlUpdate);
    $link = "../searchClient/ListClient.php?comm=mudaRelacao&idInform=$idInform&idNotification=$idNotification";

    if (!odbc_execute($stmtUpdate, [$link, $idNotification])) {
      odbc_free_result($stmtUpdate);
      return false;
    }
    odbc_free_result($stmtUpdate);

    // Insere na tabela RoleNotification
    $sqlRoleNotification = "INSERT INTO RoleNotification (idRole, idNotification) VALUES (24, ?)";
    $stmtRoleNotification = odbc_prepare($db, $sqlRoleNotification);

    if (!odbc_execute($stmtRoleNotification, [$idNotification])) {
      odbc_free_result($stmtRoleNotification);
      return false;
    }
    odbc_free_result($stmtRoleNotification);

    return $ok;
  }


  //--------------------------------------------------------------------------------------

  // Alterado HICOM 27/10/2004 (Gustavo)

  // Gera��o de notifica��o de Grava��o de PA - Financeiro e BackOffice
  function gravaPA($u, $idInform, $n, $db)
  {
    // Insere log
    $notification = "Emiss�o de Parcela de Ajuste [$n]";

    if ($this->existsRole($notification, $db)) {
      return false;
    }

    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time() . '2';

    // Inser��o na tabela NotificationR
    $sqlInsert = "INSERT INTO NotificationR (cookie, notification, idInform, tp_notification_id) VALUES (?, ?, ?, 38)";
    $stmtInsert = odbc_prepare($db, $sqlInsert);
    if (!odbc_execute($stmtInsert, [$key, $notification, $idInform])) {
      odbc_free_result($stmtInsert);
      return false;
    }
    odbc_free_result($stmtInsert);

    // Recupera o ID da notifica��o
    $sqlSelect = "SELECT id FROM NotificationR WHERE cookie = ?";
    $stmtSelect = odbc_prepare($db, $sqlSelect);
    odbc_execute($stmtSelect, [$key]);

    if (!odbc_fetch_row($stmtSelect)) {
      odbc_free_result($stmtSelect);
      return false;
    }

    $idNotification = odbc_result($stmtSelect, 1);
    odbc_free_result($stmtSelect);

    // Atualiza a notifica��o com o link correto
    $sqlUpdate = "UPDATE NotificationR SET link = ? WHERE id = ?";
    $stmtUpdate = odbc_prepare($db, $sqlUpdate);
    $link = "../financ/Financ.php?comm=emitePa&idInform=$idInform&idNotification=$idNotification";

    if (!odbc_execute($stmtUpdate, [$link, $idNotification])) {
      odbc_free_result($stmtUpdate);
      return false;
    }
    odbc_free_result($stmtUpdate);

    // Insere na tabela RoleNotification
    $sqlRoleNotification = "INSERT INTO RoleNotification (idRole, idNotification) VALUES (8, ?)";
    $stmtRoleNotification = odbc_prepare($db, $sqlRoleNotification);

    if (!odbc_execute($stmtRoleNotification, [$idNotification])) {
      odbc_free_result($stmtRoleNotification);
      return false;
    }
    odbc_free_result($stmtRoleNotification);

    return $ok;
  }
  // FIM Alterado HICOM


  function jurosMoraB($u, $idInform, $n, $db, $idJuros)
  {
    // insere log
    $notification = "Cobertura de Juros de Mora Solicitada [$n]";

    if ($this->existsRole($notification, $db))
      return false;

    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $key = $this->validateKey($key);
    if (!$key) {
      error_log("Invalid key provided to Notification INSERT");
      return false;
    }
    $r = $this->executeSecureQuery($db, "INSERT INTO NotificationR (cookie, notification,idInform,tp_notification_id) VALUES (?, ?, ?, 39)", [$key, $notification, $idInform]);

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $query = "UPDATE NotificationR SET link = ? WHERE id = ?";
        $link = "../backoffice/Backoffice.php?comm=juros&idInform=$idInform&idJuros=$idJuros&idNotification=$idNotification";


        $r = $this->executeSecureQuery($db, $query, [$link, $idNotification]);

        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (17, $idNotification)"
          );
          if (!$r)
            $ok = false;
          // alterado Hicom (Gustavo) - Enviar notifica��o para o perfil creditManager tb.
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (11, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // Gera��o de notifica��es de cobertura Juros Mora - BackOffice
  // alterado Hicom (Gustavo) - adicionei o par�metro $n_Endosso
  function jurosMoraRecebido($u, $idInform, $n, $db, $idJuros, $n_Endosso)
  {
    // insere log
    $notification = "Confirma��o de Pagamento da fatura de Juros de Mora [$n]";

    if ($this->existsRole($notification, $db))
      return false;

    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $key = $this->validateKey($key);
    if (!$key) {
      error_log("Invalid key provided to Notification INSERT");
      return false;
    }
    $r = $this->executeSecureQuery($db, "INSERT INTO NotificationR (cookie, notification,idInform,tp_notification_id) VALUES (?, ?, ?, 40)", [$key, $notification, $idInform]);

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../backoffice/Backoffice.php?comm=recebimento&idInform=$idInform" .
          "&idJuros=$idJuros&idNotification=$idNotification&n_Endosso=$n_Endosso'" .
          " WHERE id = $idNotification"
        );

        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (8, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // Gera��o de notifica��es de tarifa��o
  function newTarif($u, $n, $idInform, $db, $idEndosso = 0)
  {
    // Insere log
    $notification = "Solicita��o de Tarifa��o [$n]";

    if ($this->existsRole($notification, $db)) {
      return false;
    }

    $this->log($u, $notification, $db);
    $ok = true;
    sleep(1);
    $key = session_id() . time();

    // Inser��o na tabela NotificationR
    $sqlInsert = "INSERT INTO NotificationR (cookie, notification, tp_notification_id, idInform) VALUES (?, ?, 35, ?)";
    $stmtInsert = odbc_prepare($db, $sqlInsert);
    if (!odbc_execute($stmtInsert, [$key, $notification, $idInform])) {
      odbc_free_result($stmtInsert);
      return false;
    }
    odbc_free_result($stmtInsert);

    // Recupera o ID da notifica��o
    $sqlSelect = "SELECT id FROM NotificationR WHERE cookie = ?";
    $stmtSelect = odbc_prepare($db, $sqlSelect);
    odbc_execute($stmtSelect, [$key]);

    if (!odbc_fetch_row($stmtSelect)) {
      odbc_free_result($stmtSelect);
      return false;
    }

    $idNotification = odbc_result($stmtSelect, 1);
    odbc_free_result($stmtSelect);

    // Atualiza a notifica��o com o link correto
    $sqlUpdate = "UPDATE NotificationR SET link = ? WHERE id = ?";
    $stmtUpdate = odbc_prepare($db, $sqlUpdate);
    $link = "../tariffer/Tariffer.php?comm=view&idInform=$idInform&idNotification=$idNotification&idEndosso=$idEndosso";

    if (!odbc_execute($stmtUpdate, [$link, $idNotification])) {
      odbc_free_result($stmtUpdate);
      return false;
    }
    odbc_free_result($stmtUpdate);

    // Insere na tabela RoleNotification
    $sqlRoleNotification = "INSERT INTO RoleNotification (idRole, idNotification) VALUES (5, ?)";
    $stmtRoleNotification = odbc_prepare($db, $sqlRoleNotification);

    if (!odbc_execute($stmtRoleNotification, [$idNotification])) {
      odbc_free_result($stmtRoleNotification);
      return false;
    }
    odbc_free_result($stmtRoleNotification);

    return $ok;
  }



  // Gera��o de notifica��o de endosso para tarifa��o:
  function newTarifEndosso($u, $n, $idInform, $db, $idEndosso, $idPremio, $c)
  {
    // insere log
    $notification = "($c) Endosso de Pr�mio M�nimo [$n]";

    if ($this->existsRole($notification, $db))
      return false;

    $this->log($u, $notification, $db);
    $ok = true;
    sleep(1);
    $key = session_id() . time();

    $key = $this->validateKey($key);
    if (!$key) {
      error_log("Invalid key provided to Notification INSERT");
      return false;
    }
    $r = $this->executeSecureQuery($db, "INSERT INTO NotificationR (cookie, notification,idInform,tp_notification_id) VALUES (?, ?, ?, 41)", [$key, $notification, $idInform]);

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../tariffer/Tariffer.php?comm=view&idInform=$idInform" .
          "&idNotification=$idNotification&idEndosso=$idEndosso&idPremio=$idPremio#endosso'" .
          " WHERE id = $idNotification"
        );

        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (5, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }




  // Gera��o de notifica��es de tarifa��o
  function newTarifN($u, $n, $idInform, $db, $tipo, $c, $idEndosso = 0)
  {
    // insere log
    $notification = "($c) Endosso de Natureza de Opera��o [$n]";

    if ($this->existsRole($notification, $db))
      return false;

    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $key = $this->validateKey($key);
    if (!$key) {
      error_log("Invalid key provided to Notification INSERT");
      return false;
    }
    $r = $this->executeSecureQuery($db, "INSERT INTO NotificationR (cookie, notification,idInform,tp_notification_id) VALUES (?, ?, ?, 20)", [$key, $notification, $idInform]);

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../tariffer/Tariffer.php?comm=view&idInform=$idInform" .
          "&idNotification=$idNotification&idEndosso=$idEndosso&tipo=$tipo#endosso'" .
          " WHERE id = $idNotification"
        );

        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (5, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // Gera��o de notifica��es de ao Analista de cr�dito.
  function newCredit($u, $n, $idInform, $db, $roleType)
  {
    $notification = "Solicita��o de An�lise de Cr�dito [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    //21/05/2010 Interaktiv - Elias Vaz
    //Altera��o: inclus�o do id da notifica��o e id do inform
    //           Alterada a linha onde se atualiza o link, agora est� apontando
    //           para a tela de solicita��o de cr�dido.
    //*************************************************************************************************************

    $sql = "Select id from NotificationR where tp_notification_id = 31 and idInform  = " . $idInform;
    $rr = odbc_exec($db, $sql);

    if (odbc_fetch_row($rr)) {
      $idNotification = odbc_result($rr, 1);

      $r = odbc_exec($db, "Update NotificationR Set state = 1 Where id = $idNotification");

      if (!$r) {
        $ok = false;
      }
    } else {
      $key = $this->validateKey($key);
    if (!$key) {
      error_log("Invalid key provided to Notification INSERT");
      return false;
    }
    $r = $this->executeSecureQuery($db, "INSERT INTO NotificationR (state,cookie, notification,idInform, tp_notification_id) VALUES (1, ?, ?, ?, 31)", [$key, $notification, $idInform]);

      if (!$r) {
        $ok = false;
      } else {
        $key = $this->validateKey($key);
        if (!$key) {
          error_log("Invalid key provided to Notification");
          return false;
        }
        $cur = $this->executeSecureQuery($db, "SELECT NR.id, Inf.state FROM NotificationR NR Inner Join Inform Inf On Inf.id = NR.idInform WHERE NR.cookie = ?", [$key]);

        if (!odbc_fetch_row($cur)) {
          $ok = false;
        } else {
          $idNotification = odbc_result($cur, 1);
          $stateInform = odbc_result($cur, 2);

          $r = odbc_exec($db, " UPDATE NotificationR SET link ='../credit/Credit.php?comm=view&idInform=$idInform&idNotification=$idNotification' " .
            " WHERE id = $idNotification");

          //if ($stateInform == 10 || $stateInform == 11){
          //      $r = odbc_exec ($db," UPDATE NotificationR SET link ='../credit/Credit.php?comm=PendencyCoface&idInform=$idInform&idNotification=$idNotification' ".
          //                         " WHERE id = $idNotification");
          //  }else {
          //      
          //  }

          if (!$r) {
            $ok = false;
          } else {
            $r = odbc_exec($db, "INSERT INTO RoleNotification (idRole, idNotification) VALUES ($roleType, $idNotification)");

            if (!$r)
              $ok = false;
          }
        }
      }
    }

    return $ok;
  }

  //--------------------------------------------------------------------------------------

  //Alterado por Tiago V N - Elumini - 06/09/2005
  // Gerar notifa��o do cancelamento do informe para area de credito Cancelar
  function newCreditCanc($u, $n, $idInform, $db, $roleType)
  {
    $notification = "Solicita��o de Cancelamento da COFACE [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();
    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, notification,idInform,tp_notification_id) VALUES ('$key', '$notification',$idInform,42)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../credit/Credit.php?comm=view&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES ($roleType, $idNotification)"
          );

          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }
  //----------------------------------------------------------------------//

  //semDVE16
  function semDVE16($u, $n, $idInform, $db, $roleType, $num, $idDVE)
  {
    $notification = "Entrega da [" . $num . "�] DVE vencida [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();
    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, notification,idInform,tp_notification_id) VALUES ('$key', '$notification',$idInform,23)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../dve/Dve.php?comm=entregavenciada&idInform=$idInform&idNotification=$idNotification&idDVE=$idDVE'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES ($roleType, $idNotification)"
          );

          if (!$r)
            $ok = false;
        }
      }
    }
    if ($ok) {
      $ok = $idNotification;
    }
    return $ok;
  }




  //-------------------------------------------------------------------------------------

  // Gera��o de notifica��es inclus�o de endereco.
  function novoEndereco($u, $n, $idInform, $db, $roleType)
  {
    $notification = "Solicita��o de inclus�o de endere�o adicional [$n]";

    if ($this->existsRole($notification, $db)) {
      return false;
    }

    // Insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    // Inser��o na tabela NotificationR
    $sqlInsert = "INSERT INTO NotificationR (cookie, notification, idInform, tp_notification_id) VALUES (?, ?, ?, 34)";
    $stmtInsert = odbc_prepare($db, $sqlInsert);
    if (!odbc_execute($stmtInsert, [$key, $notification, $idInform])) {
      odbc_free_result($stmtInsert);
      return false;
    }
    odbc_free_result($stmtInsert);

    // Recupera o ID da notifica��o
    $sqlSelect = "SELECT id FROM NotificationR WHERE cookie = ?";
    $stmtSelect = odbc_prepare($db, $sqlSelect);
    odbc_execute($stmtSelect, [$key]);

    if (!odbc_fetch_row($stmtSelect)) {
      odbc_free_result($stmtSelect);
      return false;
    }

    $idNotification = odbc_result($stmtSelect, 1);
    odbc_free_result($stmtSelect);

    // Atualiza a notifica��o com o link correto
    $sqlUpdate = "UPDATE NotificationR SET link = ? WHERE id = ?";
    $stmtUpdate = odbc_prepare($db, $sqlUpdate);
    $link = "../credit/Credit.php?comm=solicEnderecoAdicional&idInform=$idInform&idNotification=$idNotification";

    if (!odbc_execute($stmtUpdate, [$link, $idNotification])) {
      odbc_free_result($stmtUpdate);
      return false;
    }
    odbc_free_result($stmtUpdate);

    // Insere na tabela RoleNotification
    $sqlRoleNotification = "INSERT INTO RoleNotification (idRole, idNotification) VALUES (?, ?)";
    $stmtRoleNotification = odbc_prepare($db, $sqlRoleNotification);

    if (!odbc_execute($stmtRoleNotification, [$roleType, $idNotification])) {
      odbc_free_result($stmtRoleNotification);
      return false;
    }
    odbc_free_result($stmtRoleNotification);

    return $ok;
  }






  //--------------------------------------------------------------------------------------

  function clientChangeImporter(
    $u,
    $n,
    $idInform,
    $db,
    $roleNumber,
    $t,
    $idBuyer,
    $renovacao = 0,
    $includeOld = 0
  ) {
    $notification = "Cliente altera lista de Importadores [$n]" . ($renovacao ? ' (Renova��o)' : '');
    if ($t == 'r') {
      $notification = "Cliente remove importador [$n]";
    }

    if ($this->existsRolePlus($notification, $idInform, $db)) {
      return false;
    }

    // Insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    // Inser��o na tabela NotificationR
    $sqlInsert = "INSERT INTO NotificationR (cookie, notification, idInform, tp_notification_id) VALUES (?, ?, ?, 9)";
    $stmtInsert = odbc_prepare($db, $sqlInsert);
    if (!odbc_execute($stmtInsert, [$key, $notification, $idInform])) {
      odbc_free_result($stmtInsert);
      return false;
    }
    odbc_free_result($stmtInsert);

    // Recupera o ID da notifica��o
    $sqlSelect = "SELECT id FROM NotificationR WHERE cookie = ? AND idInform = ?";
    $stmtSelect = odbc_prepare($db, $sqlSelect);
    odbc_execute($stmtSelect, [$key, $idInform]);

    if (!odbc_fetch_row($stmtSelect)) {
      odbc_free_result($stmtSelect);
      return false;
    }

    $idNotification = odbc_result($stmtSelect, 1);
    odbc_free_result($stmtSelect);

    // Define o link correto com base no tipo da notifica��o
    if ($t == "r") {
      $link = "../credit/Credit.php?comm=clientChangeImporterRemove&idInform=$idInform&idNotification=$idNotification&idBuyer=$idBuyer&origem=4" . ($includeOld ? "&includeOld=1" : '');
    } else {
      $link = "../credit/Credit.php?comm=clientChangeImporterInsert&idInform=$idInform&idNotification=$idNotification&idBuyer=$idBuyer&origem=4&flag_renovacao=$renovacao" . ($includeOld ? "&includeOld=1" : '');
    }

    // Atualiza a notifica��o com o link correto
    $sqlUpdate = "UPDATE NotificationR SET link = ? WHERE id = ?";
    $stmtUpdate = odbc_prepare($db, $sqlUpdate);
    if (!odbc_execute($stmtUpdate, [$link, $idNotification])) {
      odbc_free_result($stmtUpdate);
      return false;
    }
    odbc_free_result($stmtUpdate);

    // Insere na tabela RoleNotification
    $sqlRoleNotification = "INSERT INTO RoleNotification (idRole, idNotification) VALUES (?, ?)";
    $stmtRoleNotification = odbc_prepare($db, $sqlRoleNotification);
    if (!odbc_execute($stmtRoleNotification, [$roleNumber, $idNotification])) {
      odbc_free_result($stmtRoleNotification);
      return false;
    }
    odbc_free_result($stmtRoleNotification);

    return $ok;
  }


  function clientChangeImporterExecutive($u, $n, $idInform, $idRegion, $idBuyer, $nameImp, $db, $includeOld = 0)
  {
    $notification = "Cliente [$n] altera lista de importadores [$nameImp]";

    if ($this->existsRole($notification, $db)) {
      return false;
    }

    // Insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    // Inser��o na tabela NotificationR
    $sqlInsert = "INSERT INTO NotificationR (cookie, notification, idRegion, idInform, tp_notification_id) VALUES (?, ?, ?, ?, 9)";
    $stmtInsert = odbc_prepare($db, $sqlInsert);
    if (!odbc_execute($stmtInsert, [$key, $notification, $idRegion, $idInform])) {
      odbc_free_result($stmtInsert);
      return false;
    }
    odbc_free_result($stmtInsert);

    // Recupera o ID da notifica��o
    $sqlSelect = "SELECT id FROM NotificationR WHERE cookie = ?";
    $stmtSelect = odbc_prepare($db, $sqlSelect);
    odbc_execute($stmtSelect, [$key]);

    if (!odbc_fetch_row($stmtSelect)) {
      odbc_free_result($stmtSelect);
      return false;
    }

    $idNotification = odbc_result($stmtSelect, 1);
    odbc_free_result($stmtSelect);

    // Define o link correto
    $link = "../executive/Executive.php?comm=clientAddImporter&idInform=$idInform&idNotification=$idNotification&idBuyer=$idBuyer" . ($includeOld ? "&includeOld=1" : '');

    // Atualiza a notifica��o com o link correto
    $sqlUpdate = "UPDATE NotificationR SET link = ? WHERE id = ?";
    $stmtUpdate = odbc_prepare($db, $sqlUpdate);
    if (!odbc_execute($stmtUpdate, [$link, $idNotification])) {
      odbc_free_result($stmtUpdate);
      return false;
    }
    odbc_free_result($stmtUpdate);

    // Define o n�mero do role
    $roleNumber = ($idRegion == 0) ? 6 : 2;

    // Insere na tabela RoleNotification
    $sqlRoleNotification = "INSERT INTO RoleNotification (idRole, idNotification) VALUES (?, ?)";
    $stmtRoleNotification = odbc_prepare($db, $sqlRoleNotification);
    if (!odbc_execute($stmtRoleNotification, [$roleNumber, $idNotification])) {
      odbc_free_result($stmtRoleNotification);
      return false;
    }
    odbc_free_result($stmtRoleNotification);

    return $ok;
  }


  function clientChangeCredit($u, $n, $idInform, $db, $roleNumber)
  {
    $notification = "Cliente modifica Limite de Cr�dito [$n]";

    //if ($this->existsRole ($notification,$db)) return false;
    if ($this->existsRolePlus($notification, $idInform, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, notification, idInform,tp_notification_id) VALUES ('$key', '$notification', '$idInform',10)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = odbc_exec(
        $db,
        "SELECT id FROM NotificationR WHERE cookie = ? AND idInform = ?"
      );
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../credit/Credit.php?comm=clientChangeLimit&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES ($roleNumber, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }






  // Gera��o de notifica��es de emiss�o de oferta
  function newOffer($u, $r, $n, $idInform, $db)
  {

    $notification = "Emitir Oferta [$n]";

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();
    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, idRegion, notification,idInform,tp_notification_id) VALUES ('$key', $r, '$notification',$idInform,19)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../executive/Executive.php?comm=offer&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (2, $idNotification) "
          );
          //Andr�a 07/07/04 --------------------------------------------------------------------------------
          // Se for renova��o, envia notifica��o de Enviar Oferta tb para o cr�dito
          $idAnt = odbc_result(odbc_exec($db, "select idAnt from Inform where id=$idInform"), 1);
          if ($idAnt > 0) {
            $r = odbc_exec(
              $db,
              "INSERT INTO RoleNotification (idRole, idNotification) VALUES (11, $idNotification) "
            );
          }
          //---------------------------------------------------------------------------------------------

          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }




  // Gera��o de notifica��es de emiss�o de oferta para EXECUTIVELOW!
  function newOfferLow($u, $n, $idInform, $db)
  {

    $notification = "Emitir Oferta [$n]";

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();
    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, notification,idInform,tp_notification_id) VALUES ('$key', '$notification',$idInform,19)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../executive/Executive.php?comm=offer&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (6, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }





  // aguardando o resultado da oferta
  function waitOffer($u, $r, $n, $idInform, $db)
  {
    $notification = "Aguardando Retorno (Oferta) [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();
    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, idRegion, notification,idInform,tp_notification_id) VALUES ('$key', $r, '$notification',$idInform,4)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../executive/Executive.php?comm=offer&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (2, $idNotification)"
          );
          //Andr�a 07/07/04 ---------------------------------------------------------------------------------------
          // Se for renova��o, cria a notifica��o de Enviar Proposta tb para o cr�dito
          $idAnt = odbc_result(odbc_exec($db, "select idAnt from Inform where id=$idInform"), 1);
          if ($idAnt > 0) {
            $r = odbc_exec(
              $db,
              "INSERT INTO RoleNotification (idRole, idNotification) VALUES (11, $idNotification) "
            );
          }
          //------------------------------------------------------------------------------------------------


          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // aguardando o resultado da oferta  para o EXECUTIVELOW
  function waitOfferLow($u, $n, $idInform, $db)
  {
    $notification = "Aguardando Retorno (Oferta)  [$n]";

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();
    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, notification,idInform,tp_notification_id) VALUES ('$key', '$notification',$idInform,4)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../executive/Executive.php?comm=offer&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (6, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }


  //insert NotificationR (state, notification, link)
//      Values (1, 'In�cio do per�odo de renova��o [SOCIEDADE FABRICANTE DE PE�AS LTDA.]', '../executive/Executive.php?comm=renovacao&idInform=1647&idNotification=34967')

  // Gera��o de notifica��es de inicio de renovacao
  // $r regi�o, $n

  ///  INCLUIDO PELA HICOM GPC

  function periodoRenova($u, $n, $idInform, $idRegion, $db, $erro)
  {

    $ok = true;
    //Alterado Por Tiago V N - Elumini - 08/03/2006
    $idAnt = odbc_result(odbc_exec($db, "select id from Inform where idAnt=$idInform"), 1);
    //$cur = odbc_exec($db, "select id, idRegion from Inform where idAnt=$idInform");
    //$idAnt = odbc_result($cur, 1);
    //$idRegion = odbc_result($cur, 2);

    if ($idAnt > 0) {
      // J� tem.....  n�o faz nada,,,
      $erro = "J� tem informe de renova��o!";
    } else {
      $notification = "In�cio do per�odo de renova��o [$n]";

      if ($this->existsRole($notification, $db)) {
        $erro = "Existe role para notifica��o!";
        return false;
      }
      // insere log
      //$this->log ($u, $notification, $db);

      $ok = true;
      $key = session_id() . time();

      $r = odbc_exec(
        $db,
        "INSERT INTO NotificationR (cookie,  notification, idInform, idRegion,id_notification_id) VALUES ('$key',  '$notification', $idInform, $idRegion,28)"
      );

      if (!$r) {
        $erro = "N�o incluiu notifica��o!";
        $ok = false;
      } else {
        $cur = odbc_exec(
          $db,
          "SELECT id FROM NotificationR WHERE cookie = ?"
        );
        if (!odbc_fetch_row($cur)) {

          $erro = "N�o selecionou id da notifica��o!";
          $ok = false;
        } else {
          $idNotification = odbc_result($cur, 1);

          $r = odbc_exec(
            $db,
            " UPDATE NotificationR" .
            "   SET link =" .
            "   '../executive/Executive.php?comm=renovacao&idInform=$idInform&idNotification=$idNotification'" .
            " WHERE id = $idNotification"
          );

          // '../executive/Executive.php?comm=renovacao&idInform=1647&idNotification=34967'

          if (!$r) {
            $erro = "N�o alterou notifica��o!";
            $ok = false;
          } else {
            /*
             * Solicitado por Marcele Lemos para retirar a notifica��o da Tela do Credit.
             * Alterado por Tiago V N - 09/04/2007
             */
            /*
           $r = odbc_exec (
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (11, $idNotification)"
           );
            */

            //Alterado por Tiago V N - Elumini - 08/03/2006
            $r = odbc_exec(
              $db,
              "INSERT INTO RoleNotification (idRole, idNotification) VALUES (2, $idNotification)"
            );

            if (!$r) {
              $erro = "N�o incluiu a role notification!";
              $ok = false;
            }
          }

          if ($ok) {
            // Atualiza o informe...
            $r = odbc_exec($db, " update Inform set notificaRenova = 'S' where id = $idInform ");
          }

        }
      }

    }

    return $ok;
  }




  // Gera��o de notifica��es de cria��o de informe
  // $r regi�o, $n



  function newInform($u, $r, $n, $idInform, $db)
  {
    $tp_notification_id = '';
    $idAnt = odbc_result(odbc_exec($db, "select idAnt from Inform where id=$idInform"), 1);
    if ($idAnt > 0) {
      $notification = "Informe de Renova��o [$n]";
    } else {
      $notification = "Novo Informe [$n]";
      $tp_notification_id = 29; // Novo informe;
    }

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    // 22-07-2010 Elias Vaz - Interaktiv
    // Altera��o conforme solicita��o do Ricardo Turatto
    // Objetivo:  inserir o id da notifica��o quando o informe for NOVO.
    if ($tp_notification_id) {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification INSERT");
        return false;
      }
      $rx = $this->executeSecureQuery($db, "INSERT INTO NotificationR (cookie, idRegion, notification,idInform, tp_notification_id) VALUES (?, ?, ?, ?, ?)", [$key, $r, $notification, $idInform, $tp_notification_id]);
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification INSERT");
        return false;
      }
      $rx = $this->executeSecureQuery($db, "INSERT INTO NotificationR (cookie, idRegion, notification,idInform, tp_notification_id) VALUES (?, ?, ?, ?, 27)", [$key, $r, $notification, $idInform]);
    }


    if (!$rx) {
      $ok = false;

    } else {

      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);

      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);
        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../executive/Executive.php?comm=view&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );

        if (!$r)
          $ok = false;
        else {
          if ($idAnt > 0) {
            // vai direto para o credito...


            $r = odbc_exec(
              $db,
              "INSERT INTO RoleNotification (idRole, idNotification) VALUES (11, $idNotification)"
            );


            //Alterado por Tiago V N - Elumini - 08/03/2006
            $r = odbc_exec(
              $db,
              "INSERT INTO RoleNotification (idRole, idNotification) VALUES (2, $idNotification)"
            );

          } else {
            $r = odbc_exec(
              $db,
              "INSERT INTO RoleNotification (idRole, idNotification) VALUES (2, $idNotification)"
            );
          }


          if (!$r)
            $ok = false;


        }
      }
    }
    return $ok;


  }





  function retInform($u, $r, $n, $idInform, $db)
  {

    $notification = "Retorno de Informe [$n]";

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();


    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, idRegion, notification,idInform, tp_notification_id) VALUES ('$key', $r, '$notification',$idInform,30)"
    );



    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../executive/Executive.php?comm=view&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (2, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // Gera��o de notifica��o de novo informe para valores baixos
  function newLowInform($u, $n, $idInform, $db)
  {
    $idAnt = odbc_result(odbc_exec($db, "select idAnt from Inform where id=$idInform"), 1);
    if ($idAnt > 0) {
      $notification = "Informe de Renova��o [$n]";
    } else {
      $notification = "Novo Informe [$n]";
    }

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    if ($idAnt > 0) {
      $r = odbc_exec(
        $db,
        "INSERT INTO NotificationR (cookie, notification,idInform, tp_notification_id) VALUES ('$key', '$notification',$idInform,27)"
      );
    } else {
      $r = odbc_exec(
        $db,
        "INSERT INTO NotificationR (cookie, notification,idInform, tp_notification_id) VALUES ('$key', '$notification',$idInform,29)"
      );

    }
    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../executive/Executive.php?comm=view&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (6, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // Gera��o de notifica��o de novo informe para valores baixos
  function retLowInform($u, $n, $idInform, $db)
  {

    $notification = "Retorno de Informe  [$n]";

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, notification,idInform, tp_notification_id) VALUES ('$key', '$notification',$idInform,30)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../executive/Executive.php?comm=view&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (6, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // Gera��o de notifica��es de aguardando correio (BackOffice)
  function waitMail($u, $r, $n, $idInform, $db)
  {
    $notification = "Aguardando Proposta [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, idRegion, notification,idInform, tp_notification_id) VALUES ('$key', $r, '$notification',$idInform,3)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../backoffice/BackOffice.php?comm=view&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (7, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }


  // Gera��o de notifica��es de aguardando correio (BackOffice) para o EXECUTIVELOW
  function waitMailLow($u, $n, $idInform, $db)
  {
    $notification = "Aguardando Proposta  [$n]";

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, notification,idInform, tp_notification_id) VALUES ('$key', '$notification',$idInform,3)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../backoffice/BackOffice.php?comm=view&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (7, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }


  // Gera��o de notifica��es de aguardando pagamento
  function waitPgto($u, $r, $n, $idInform, $db)
  {
    $notification = "Aguardando Pagamento [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time() . "P";

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, idRegion, notification,idInform, tp_notification_id) VALUES ('$key', $r, '$notification',$idInform,2)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../financ/Financ.php?comm=view&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (8, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }



  // Gera��o de notifica��es de aguardando pagamento EXECUTIVELOW
  function waitPgtoLow($u, $n, $idInform, $db)
  {
    $notification = "Aguardando Pagamento  [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time() . "P";

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, notification,idInform, tp_notification_id) VALUES ('$key', '$notification',$idInform,2)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../financ/Financ.php?comm=view&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (8, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // Comanda a emiss�o da ap�lice

  function EmitPolicy($u, $n, $idInform, $db)
  {
    $notification = "Emitir ap�lice [$n]";

    //echo "<pre>$notification</pre>";
    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time() . '1';

    $query = "INSERT INTO NotificationR (cookie, notification,idInform, tp_notification_id) VALUES ('$key', '$notification',$idInform,18)";

    //echo "<pre>$query</pre>";
    $r = odbc_exec($db, $query);

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $query =
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../policy/Policy.php?comm=view&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification";

        //echo "<pre>$query</pre>";

        $r = odbc_exec($db, $query);
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (9, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // cria nova notificacao para o role Backoffice - Cess�o de Direitos BB
  function cdbb($u, $idCDBB, $idInform, $db, $n, $c)
  {
    $notification = "($c) Cess�o de Direito Banco do Brasil [$n]";
    $key = session_id() . time();
    $this->log($u, $notification, $db);
    $ok = true;

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,7)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../cessao/Cessao.php?comm=view&idInform=$idInform&idCDBB=$idCDBB&idNotification=$idNotification&tipoBanco=1'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (22, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  function cdbbCancela($u, $idCDBB, $idInform, $db, $n, $c, $tipo)
  {
    $notification = "($c) Cancelamento de Cess�o de Direito [$n]";
    $key = session_id() . time();

    // Insere log
    $this->log($u, $notification, $db);
    $ok = true;

    // Inser��o na tabela NotificationR
    $sqlInsert = "INSERT INTO NotificationR (state, cookie, notification, idInform, tp_notification_id) VALUES (1, ?, ?, ?, 6)";
    $stmtInsert = odbc_prepare($db, $sqlInsert);
    if (!odbc_execute($stmtInsert, [$key, $notification, $idInform])) {
      odbc_free_result($stmtInsert);
      return false;
    }
    odbc_free_result($stmtInsert);

    // Recupera o ID da notifica��o
    $sqlSelect = "SELECT id FROM NotificationR WHERE cookie = ?";
    $stmtSelect = odbc_prepare($db, $sqlSelect);
    odbc_execute($stmtSelect, [$key]);

    if (!odbc_fetch_row($stmtSelect)) {
      odbc_free_result($stmtSelect);
      return false;
    }

    $idNotification = odbc_result($stmtSelect, 1);
    odbc_free_result($stmtSelect);

    // Define o link correto
    $link = "../cessao/Cessao.php?comm=viewCancel&idInform=$idInform&idCDBB=$idCDBB&idNotification=$idNotification&tipo=$tipo";

    // Atualiza a notifica��o com o link correto
    $sqlUpdate = "UPDATE NotificationR SET link = ? WHERE id = ?";
    $stmtUpdate = odbc_prepare($db, $sqlUpdate);
    if (!odbc_execute($stmtUpdate, [$link, $idNotification])) {
      odbc_free_result($stmtUpdate);
      return false;
    }
    odbc_free_result($stmtUpdate);

    // Insere na tabela RoleNotification
    $sqlRoleNotification = "INSERT INTO RoleNotification (idRole, idNotification) VALUES (22, ?)";
    $stmtRoleNotification = odbc_prepare($db, $sqlRoleNotification);
    if (!odbc_execute($stmtRoleNotification, [$idNotification])) {
      odbc_free_result($stmtRoleNotification);
      return false;
    }
    odbc_free_result($stmtRoleNotification);

    return $ok;
  }


  // cria nova notificacao para o role Backoffice - Cess�o de Direitos Bancos Parceiros
  function cdparc($u, $idCDParc, $idInform, $db, $n, $c)
  {
    $notification = "($c) Cess�o de Direito de Bancos Parceiros [$n]";
    $key = session_id() . time();
    $this->log($u, $notification, $db);
    $ok = true;

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,43)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../cessao/Cessao.php?comm=view&idInform=$idInform&idCDParc=$idCDParc&idNotification=$idNotification&tipoBanco=2'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (22, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // cria nova notificacao para o role Backoffice - Cess�o de Direitos Outros Bancos
  function cdob($u, $idCDOB, $idInform, $db, $n, $c, $idBanco, $bancoName)
  {
    $notification = "($c) Cess�o de Direito $bancoName [$n]";
    $key = session_id() . time();
    $this->log($u, $notification, $db);
    $ok = true;

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,44)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../cessao/Cessao.php?comm=view&idInform=$idInform&idCDOB=$idCDOB&idBanco=$idBanco&idNotification=$idNotification&tipoBanco=3'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (22, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }


  // cria nova notificacao para o role Sinistro
  function newSinistro($u, $n, $idInform, $db, $idImporter, $idSinistro, $ni)
  {
    $notification = "Aviso de Sinistro [$n] -> [$ni]";
    $key = session_id() . time();
    $this->log($u, $notification, $db);
    $ok = true;

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,45)"
    );

    if (!$r)
      $ok = false;
    else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur))
        $ok = false;
      else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../sinistro/Sinistro.php?comm=view&idInform=$idInform&idImporter=$idImporter&idSinistro=$idSinistro&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (18, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // cria aviso de Sinistro
  function sinistro($idNotification, $idImporter, $idInform, $u, $db, $n, $idSinistro, $ni)
  {
    $notification = "Sinistro Criado [$n] -> [$ni] ";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idImporter,46)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../sinistro/Sinistro.php?comm=view&idInform=$idInform&idNotification=$idNotification&idImporter=$idImporter&idSinistro=$idSinistro'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (18, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // cria aviso de Sinistro
  function sinistroSuspenso($idNotification, $idImporter, $idInform, $u, $db, $n, $idSinistro, $ni)
  {
    $notification = "Sinistro Suspenso [$n] -> [$ni]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,47)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../sinistro/Sinistro.php?comm=view&idInform=$idInform&idNotification=$idNotification&idImporter=$idImporter&idSinistro=$idSinistro'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (18, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // cria aviso de Sinistro
  function sinistroCancelado($idNotification, $idImporter, $idInform, $u, $db, $n, $idSinistro, $ni)
  {
    $notification = "Sinistro Cancelado [$n] -> [$ni]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,48)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../sinistro/Sinistro.php?comm=view&idInform=$idInform&idNotification=$idNotification&idImporter=$idImporter&idSinistro=$idSinistro'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (18, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // cria aviso de Sinistro
  function sinistroNaoAceito($idNotification, $idImporter, $idInform, $u, $db, $n, $idSinistro, $ni)
  {
    $notification = "Sinistro N�o Aceito [$n] -> [$ni]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification', $idInform,49)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../sinistro/Sinistro.php?comm=view&idInform=$idInform&idNotification=$idNotification&idImporter=$idImporter&idSinistro=$idSinistro'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (18, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }


  // cria aviso de Sinistro
  function pagInd($idNotification, $idImporter, $idInform, $u, $db, $n, $idSinistro, $ni)
  {
    $notification = "Indeniza��o Aprovada [$n] -> [$ni]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,51)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../sinistro/Sinistro.php?comm=view&idInform=$idInform&idNotification=$idNotification&idImporter=$idImporter&idSinistro=$idSinistro'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (18, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // cria aviso de Sinistro
  function sinistroRecuperado($idNotification, $idImporter, $idInform, $u, $db, $n, $idSinistro, $ni)
  {
    $notification = "Sinistro Recuperado [$n] -> [$ni]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,50)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../sinistro/Sinistro.php?comm=view&idInform=$idInform&idNotification=$idNotification&idImporter=$idImporter&idSinistro=$idSinistro'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (18, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // cria nova notificacao para o role DVE
  function newDVE($u, $idDVE, $idInform, $n, $db)
  {
    $num = odbc_result(odbc_exec($db, "select num from DVE where id=$idDVE"), 1);
    $notification = $num . "� DVE Enviada [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,17)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../dve/Dve.php?comm=view&idInform=$idInform&idNotification=$idNotification&idDVE=$idDVE&client=0&newdve=0'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (16, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // cria notificacao para creditInform com os importadores excluidos durante a renovacao
  function apoliceEmitida($u, $idInform, $n, $db)
  {
    $notification = "Ap�lice emitida [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,5)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../credit/Credit.php?comm=emitida&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = $this->executeSecureQuery($db, "INSERT INTO RoleNotification (idRole, idNotification) VALUES (12, ?)", [$idNotification]);
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  function renovacao_reduzidos($u, $idInform, $n, $db)
  {
    $notification = "Importadores com cr�dito reduzido durante a renova��o [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,52)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../credit/Credit.php?comm=renovacao_reduzidos&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (10, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  // cria notificacao para creditInform com os importadores q tiveram credito reduzido no reestudo
  function reestudo_reduzido($u, $idInform, $n, $db, $old, $new, $idBuyer)
  {
    $notification = "Importador com cr�dito reduzido no reestudo [$n]";

    //if ($this->existsRole ($notification, $db)) return false;
    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,24)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);
        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          " SET link =" .
          " '../credit/Credit.php?comm=reduzido&idInform=$idInform&idNotification=$idNotification&idBuyer=$idBuyer&oldCredit=$old&newCredit=$new'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = $this->executeSecureQuery($db, "INSERT INTO RoleNotification (idRole, idNotification) VALUES (12, ?)", [$idNotification]);
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  function newEndossoDados($idInform, $idEndosso, $db, $c)
  {
    global $user;
    $name = odbc_result(odbc_exec($db, "select name from Inform where id=$idInform"), 1);
    $notification = "($c) Solicita��o de Endosso de Dados Cadastrais [$name]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($user, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,32)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../endosso/Endosso.php?comm=dados&idEndosso=$idEndosso&idInform=$idInform&idNotification=$idNotification#endosso'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (17, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  function newEndossoNatureza($idInform, $idEndosso, $db, $c)
  {
    global $user;
    $name = odbc_result(odbc_exec($db, "select name from Inform where id=$idInform"), 1);
    $notification = "($c) Solicita��o de Endosso de Natureza da Opera��o [$name]";

    if ($this->existsRole($notification, $db))
      return false;
    // insere log
    $this->log($user, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,idInform, tp_notification_id) VALUES (1, '$key', '$notification',$idInform,33)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../endosso/Endosso.php?comm=natureza&idEndosso=$idEndosso&" .
          "idInform=$idInform&idNotification=$idNotification#endosso'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (17, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  function newEndossoPrMin($idInform, $name, $db, $idEndosso, $id, $c, $idRole = 17)
  {
    global $user;
    $notification = "($c) Endosso de Pr�mio M�nimo [$name]";

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($user, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $idRegion = odbc_result(
      odbc_exec(
        $db,
        "select idRegion from UserRegion where idUser=1"
      ),
      1
    );
    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification, idRegion,idInform, tp_notification_id) VALUES (1, '$key', '$notification', $idRegion,$idInform,41)"
    );

    if (!$r)
      $ok = false;
    else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../endosso/Endosso.php?comm=prMin&idInform=$idInform&idNotification=$idNotification" .
          "&idEndosso=$idEndosso&idPremio=$id&propSent=1#endosso'" .
          //($idRole != 17 ? '&propSent=1' : '').
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES ($idRole, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  function newPrMinNatOper($idInform, $name, $db, $idEndosso, $id, $c, $idRole = 17)
  {
    global $user;
    $notification = "($c) Endosso de Natureza de Opera��o e Altera��o de Pr�mio M�nimo [$name]";

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($user, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $idRegion = odbc_result(
      odbc_exec(
        $db,
        "select idRegion from UserRegion where idUser=1"
      ),
      1
    );
    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification, idRegion,idInform, tp_notification_id) VALUES (1, '$key', '$notification', $idRegion,$idInform,21)"
    );

    if (!$r)
      $ok = false;
    else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../endosso/Endosso.php?comm=natOperPrMin&idInform=$idInform&idNotification=$idNotification" .
          ($idRole == 17 ? '' : '&propSent=1') .
          "&idEndosso=$idEndosso&idPremio=$id#endosso'" .
          " WHERE id = $idNotification"
        );
        if (!$r)
          $ok = false;
        else {
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES ($idRole, $idNotification)"
          );
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }

  function mudouDados($db, $idInform, $fields_changed, $idBuyer, $name, $importer)
  {
    global $user;
    $notification = "Cliente muda dados do Importador $importer [$name]";

    if ($this->existsRole($notification, $db))
      return false;

    // insere log
    $this->log($user, $notification, $db);
    $ok = true;
    $key = session_id() . time();
    $idRegion = odbc_result(
      odbc_exec(
        $db,
        "select idRegion from UserRegion where idUser=1"
      ),
      1
    );
    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification, idRegion,idInform, tp_notification_id) VALUES (1, '$key', '$notification', $idRegion,$idInform,11)"
    );

    if (!$r)
      $ok = false;
    else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT max(id) FROM NotificationR WHERE cookie = ?", [$key]);
      $idNotification = odbc_result($cur, 1);

      $r = odbc_exec(
        $db,
        " UPDATE NotificationR" .
        "   SET link =" .
        "   '../credit/Credit.php?comm=mudou&idInform=$idInform&idNotification=$idNotification&idBuyer=$idBuyer&fields_changed=" .
        base64_encode($fields_changed) . "'" .
        " WHERE id = $idNotification"
      );
      if (!$r)
        $ok = false;
      else {
        $state = odbc_result(odbc_exec($db, "select state from Inform where id=$idInform"), 1);
        if ($state == 10)
          $idRole = 10;
        else
          $idRole = 12;
        $r = odbc_exec(
          $db,
          "INSERT INTO RoleNotification (idRole, idNotification) VALUES ($idRole, $idNotification)"
        );
        if (!$r)
          $ok = false;
      }
    }
    return $ok;
  }

  function removedImporters($u, $db, $idInform, $file)
  {
    $n = trim(odbc_result(odbc_exec($db, "select name from Inform where id=$idInform"), 1));
    $notification = "Cliente remove importadores [$n]";
    if ($this->existsRole($notification, $db)) {
      return false;
    }

    // insere log
    $this->log($u, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (cookie, notification,idInform, tp_notification_id) VALUES ('$key', '$notification',$idInform,12)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);
        $qQ1 = "UPDATE NotificationR SET link = ? WHERE id = ?";
        $linkRemoved = "../credit/Credit.php?comm=removed&idInform=$idInform&idNotification=$idNotification&file=$file";
        $r = $this->executeSecureQuery($db, $qQ1, [$linkRemoved, $idNotification]);
        if (!$r) {
          $ok = false;
        } else {
          $r = $this->executeSecureQuery($db, "INSERT INTO RoleNotification (idRole, idNotification) VALUES (12, ?)", [$idNotification]);
          if (!$r)
            $ok = false;
        }
      }
    }
    return $ok;
  }


  /*
  Desenvolvido por Tiago V N - 25/01/2008
  Fun��o para notificar sobre Pagamento de Bonus que ser� enviada
  pelo servi�o de monitoramento.
  */

  function notifica_bonus($idInform, $n, $db)
  {
    $notification = "Previs�o de Pagamento de B�nus [$n]";

    if ($this->existsRole($notification, $db))
      return false;
    $ok = true;
    $key = session_id() . time();

    // insere log
    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification, idInform,tp_notification_id) VALUES (1, '$key', '$notification', '$idInform',55)"
    );
    if (!$r) {
      $ok = false;
    } else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT id FROM NotificationR WHERE cookie = ?", [$key]);
      if (!odbc_fetch_row($cur)) {
        $ok = false;
      } else {
        $idNotification = odbc_result($cur, 1);

        $r = odbc_exec(
          $db,
          " UPDATE NotificationR" .
          "   SET link =" .
          "   '../credit/Credit.php?comm=notificaBonus&idInform=$idInform&idNotification=$idNotification'" .
          " WHERE id = $idNotification"
        );
        if (!$r) {
          $ok = false;
        } else {
          //Role Credito
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (10, $idNotification)"
          );
          if (!$r)
            $ok = false;
          //Role Financeiro
          $r = odbc_exec(
            $db,
            "INSERT INTO RoleNotification (idRole, idNotification) VALUES (8, $idNotification)"
          );
          if (!$r)
            $ok = false;

        }
      }
    }
    return $ok;
  }

  /*
    ##############################33
    Cria a notifica��o Cliente divulga o nome do importador
    Criado por Tiago V N -Elumini - 18/02/2008
  */

  function ClienteDivulgaNome($db, $idInform, $idBuyer, $name, $importer)
  {
    global $user;
    $notification = "A $name autoriza a SBCE a entrar em contato com o importador";

    //if ($this->existsRole ($notification, $db)) return false;

    // insere log
    $this->log($user, $notification, $db);
    $ok = true;
    $key = session_id() . time();

    $r = odbc_exec(
      $db,
      "INSERT INTO NotificationR (state, cookie, notification,  tp_notification_id,idInform) VALUES (1, '$key', '$notification', 54,$idInform)"
    );

    if (!$r)
      $ok = false;
    else {
      $key = $this->validateKey($key);
      if (!$key) {
        error_log("Invalid key provided to Notification");
        return false;
      }
      $cur = $this->executeSecureQuery($db, "SELECT max(id) FROM NotificationR WHERE cookie = ?", [$key]);
      $idNotification = odbc_result($cur, 1);

      $r = odbc_exec(
        $db,
        " UPDATE NotificationR" .
        "   SET link =" .
        "   '../credit/Credit.php?comm=ClienteDivulgaNome&idBuyer=$idBuyer&idNotification=$idNotification'" .
        " WHERE id = $idNotification"
      );
      if (!$r)
        $ok = false;

      $r = odbc_exec(
        $db,
        "INSERT INTO RoleNotification (idRole, idNotification) VALUES (10, $idNotification)"
      );
      if (!$r)
        $ok = false;
    }
    return $ok;
  }

  // Gera o Log


  function verifMasc($vlr, $tipo)
  {
    $masq = str_replace(".", "", $vlr);
    $masq = str_replace("-", "", $masq);
    $masq = str_replace("/", "", $masq);

    return $masq;

  }
  function Convert_Data_Geral($data)
  {
    if (strstr($data, "/")) {//verifica se tem a barra /
      $d = explode("/", $data);//tira a barra
      $invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = m�s etc...
      return $invert_data;
    } elseif (strstr($data, "-")) {
      $d = explode("-", $data);
      $invert_data = "$d[2]/$d[1]/$d[0]";
      return $invert_data;
    }

  }

  function historicolog($idInform, $userID, $db, $tipo, $temx, $nomeTabela, $idImporter)
  {
    $j = 0;

    if (!$idInform) {
      return null;
    }

    $nomeTabela = ($nomeTabela != '') ? $nomeTabela : 'Inform';

    // Obt�m as colunas da tabela
    $sqlColumns = "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = ?";
    $stmtColumns = odbc_prepare($db, $sqlColumns);
    odbc_execute($stmtColumns, [$nomeTabela]);

    $arr = [];

    while ($rowColumns = odbc_fetch_array($stmtColumns)) {
      $column_name = $rowColumns['column_name'];

      if ($nomeTabela == 'Importer') {
        $sqlImporter = "SELECT $column_name, id FROM Importer WHERE idInform = ? AND id = ?";
        $stmtImporter = odbc_prepare($db, $sqlImporter);
        odbc_execute($stmtImporter, [$idInform, $idImporter]);

        while ($rowImporter = odbc_fetch_array($stmtImporter)) {
          if ($tipo == 1) {
            $arr[] = $rowImporter[$column_name] . '|' . $rowImporter['id'];
          } else {
            $vlr = explode('|', $temx[$j]);

            if ($vlr[0] != $rowImporter[$column_name] && $column_name != 'id') {
              $sqlInsert = "INSERT INTO historico_inform (
                            idInform, userID, DataReg, NomeTabela, NomeCampo, ValorAnterior, ValorAtual, Acao
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, '1')";
              $stmtInsert = odbc_prepare($db, $sqlInsert);
              odbc_execute($stmtInsert, [
                $idInform,
                $userID,
                date('Y-m-d H:i:s'),
                $nomeTabela,
                $column_name,
                $vlr[0],
                $rowImporter[$column_name]
              ]);
              odbc_free_result($stmtInsert);
            }
          }
          $j++;
        }
        odbc_free_result($stmtImporter);
      } else {
        $sqlInform = "SELECT $column_name FROM Inform WHERE id = ?";
        $stmtInform = odbc_prepare($db, $sqlInform);
        odbc_execute($stmtInform, [$idInform]);

        while ($rowInform = odbc_fetch_array($stmtInform)) {
          if ($tipo == 1) {
            $arr[] = $rowInform[$column_name];
          } else {
            if ($temx[$j] != $rowInform[$column_name] && $column_name != 'id') {
              $sqlInsert = "INSERT INTO historico_inform (
                            idInform, userID, DataReg, NomeTabela, NomeCampo, ValorAnterior, ValorAtual, Acao
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, '1')";
              $stmtInsert = odbc_prepare($db, $sqlInsert);
              odbc_execute($stmtInsert, [
                $idInform,
                $userID,
                date('Y-m-d H:i:s'),
                $nomeTabela,
                $column_name,
                $temx[$j],
                $rowInform[$column_name]
              ]);
              odbc_free_result($stmtInsert);
            }
          }
          $j++;
        }
        odbc_free_result($stmtInform);
      }
    }
    odbc_free_result($stmtColumns);

    return ($tipo == 1) ? (isset($arr) ? $arr : null) : null;
  }


}
?>