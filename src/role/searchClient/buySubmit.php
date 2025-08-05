<?php 
  $forward = "success";
  $changed = 0;
  $hasNotif = 0;
  $cur = odbc_exec ($db, "SELECT id FROM Importer WHERE idInform = $idInform AND hold = 0 AND state = 1");
  if (odbc_fetch_row ($cur))
    $hasNotif = 1;

  for ($i = 1; $field->getField ("buyId$i") != ''; $i++) {
    $h   = $field->getField ("free$i");
    $lim = $field->getNumField ("limCredit$i") * 1000;
    //echo "<br>$i, $h, $lim<br>";
    if ($h && $lim == 0) {
      $h = 0;
      $err = true;
    }
    $x = odbc_exec($db, "select state from Importer WHERE id = ".$field->getField ("buyId$i"));
    $status = odbc_result($x, 1);
    $query = "UPDATE Importer SET limCredit = $lim, hold = ".
       ($h == 1 ? 0 : 1).
       ($status != 1 && $status != 2 ? ", state = 1" : '').
       " WHERE id = ".$field->getField ("buyId$i");
//    echo "<br>$query<br>";
    $r = odbc_exec ($db, $query);
    $query = "UPDATE ChangeCredit SET creditSolic = $lim WHERE idImporter = ".$field->getField ("buyId$i");
//    echo "<br>$query<br>";
    odbc_exec ($db, $query);
    if ($r) $changed ++;
  }

  $cur = odbc_exec ($db, "SELECT name, state FROM Inform WHERE id = $idInform");
  $nameCl = 'NONO';
  $state = 1;
  if (odbc_fetch_row ($cur)) {
    $nameCl = odbc_result ($cur, 'name');
    $state = odbc_result ($cur, 'state');
  }
  if ($err) {
    $msg = "Favor Preencher a Exposição Máxima dos Importadores Selecionados";
    $forward = "error";
  } else {
    if ($changed > 0)
      if ($state < 10 && $state != 9 && !$hasNotif)
        $r = $notif->clientChangeImporter ($userID, $nameCl, $idInform, $db, 12, "i", 0);
      else if ($state == 10)
        $r = $notif->clientChangeImporter ($userID, $nameCl, $idInform, $db, 10, "i", 0);
  }
?>
