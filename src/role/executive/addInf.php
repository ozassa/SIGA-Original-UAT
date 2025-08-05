<?php  $cur=odbc_exec(
    $db,
    "SELECT i.id FROM Inform i JOIN Insured ins ON (ins.id = i.idInsured) WHERE i.state = 1 AND ins.idResp = $userID"
  );

  // tenta encontrar o informe corrente
  if (odbc_fetch_row($cur)) $idInform = odbc_result($cur,1);
  else {
    
    $ok = true;

    // inicia a transaчуo
    odbc_autocommit ($db, FALSE);

    $r = odbc_exec(
      $db,
      "INSERT INTO Insured (idResp) VALUES ($userID)"
    );

    if (!$r) $ok = false;
    else {
      $cur=odbc_exec(
        $db,
        "SELECT id FROM Insured WHERE idResp = $userID"
      );
      if (!odbc_fetch_row($cur)) $ok = false;
      else {
        $idInsured = odbc_result($cur,1);
        $r = odbc_exec(
          $db,
          "INSERT INTO Inform (idInsured, idRegion) VALUES ($idInsured, 1)"
        );

        if (!$r) $ok = false;
        else {
          $cur=odbc_exec(
            $db,
            "SELECT i.id FROM Inform i JOIN Insured ins ON (ins.id = i.idInsured) WHERE i.state = 1 AND ins.idResp = $userID"
          );
          if (!odbc_fetch_row($cur)) $ok = false;
          else {
            $idInform = odbc_result($cur,1);
            odbc_exec(
              $db,
              "INSERT INTO Volume (idInform) VALUES ($idInform)"
            );
            odbc_exec(
              $db,
              "INSERT INTO Lost (idInform) VALUES ($idInform)"
            );
          }
        }
      }
    }
    if ($ok)
      odbc_commit();
    else
      odbc_rollback();

    odbc_autocommit ($db, TRUE);
  }
?>