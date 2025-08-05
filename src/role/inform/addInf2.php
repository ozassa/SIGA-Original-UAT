<?php 

$n = 0;
$query = '';
if ($idInform) {
  $query = "SELECT id, state, name FROM Inform WHERE id = $idInform";
  $cur = odbc_exec($db, $query);
  
} else {
  $query = 
	 "SELECT count(i.id)
          FROM Inform i
            JOIN Insured ins ON
                 (ins.id = i.idInsured)
         WHERE ins.idResp = $userID
           AND ((startValidity <= getdate()
           AND endValidity >= getdate() AND i.state <> 9) OR i.state < 9)";

  //echo "<pre>$query</pre>";
  
  $cur = odbc_exec($db, $query);

  if (odbc_fetch_row ($cur)) {
    if (odbc_result ($cur, 1) > 1)
      $n = 1;
  }

  //echo "<pre>$n</pre>";
  $query = "SELECT i.id, i.state, i.name
            FROM Inform i
              JOIN Insured ins ON (ins.id = i.idInsured)
            WHERE ins.idResp = $userID 
            ORDER BY i.id DESC";

  //echo "<pre>$query</pre>";

  $cur = odbc_exec($db, $query);

}

//echo "<pre>$query</pre>";

// tenta encontrar o informe corrente
if(odbc_fetch_row($cur)){
  $state = odbc_result($cur,2);
  $idInform = odbc_result($cur,1);
  $informName = odbc_result($cur,3);
  //echo "<br>idInform [$idInform] state [$state]<br>";
  if($n == 1){
  //echo "<pre>$n</pre>";
    odbc_fetch_row($cur);
    $state = odbc_result($cur,2);
    $idInform = odbc_result($cur,1);
    $informName = odbc_result($cur,3);
    //echo "<br>idInform [$idInform] state [$state]<br>";
  }
  if($state == 1 || $state == 8) {
    $r = odbc_exec($db, "UPDATE Inform SET state = 1 WHERE id = $idInform");
    if (!$r) $msg = "Problemas na Recuperação do Informe";
    $x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");
    if(odbc_fetch_row($x)){
      $id = odbc_result($x, 1);
      $fim = odbc_result($fim, 2);
      if(! $fim){
	odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");
      }
    }
  } else if($state == 7 || $state == 10 || $state == 3) {
    $comm = "changeImporter";
    if ($state == 3) {
      $msg = "Informe em Análise de Crédito";
    } else if ($state == 7) {
      $msg = "Apólice a ser Emitida";
    } else if ($state == 10) {
      $msg = "Apólice Vigente";
    }
  } else if ($state == 2) {
    $msg = "Alterando Informe Submetido";
  } else if ($state == 4) {
    $comm = "changeImporter";
    $msg = "Informe em Tarifação";
  } else if ($state == 5) {
    $comm = "changeImporter";
    $msg = "Informe Tarifado";
  } else if ($state == 6) {
    $comm = "changeImporter";
    $msg = "Proposta Enviada";
  } else if ($state == 9) {
    $canc = true;
    $msg = "Seu Processo foi Cancelado";
  } else if ($state == 11) {
    $canc = true;
    $msg = "Apólice Encerrada";
  } else {
    $msg = "Sua Proposta já foi Emitida";
  }
} else {
  $ok = true;

  // inicia a transação
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
	"INSERT INTO Inform (idInsured) VALUES ($idInsured)"
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
  else {
    odbc_rollback();
    $msg = "problemas na criação do informe";
  }
  odbc_autocommit ($db, TRUE);
}

?>
