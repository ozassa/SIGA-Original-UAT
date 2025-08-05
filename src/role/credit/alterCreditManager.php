<?php  require_once("../rolePrefix.php");
$i = $field->getField("i");
$idInform = $field->getField("informId");
$count = 0;
while ($count <= $i) {
  $editCredit = $field->getField("editCode".$count);     //valor
  $idBuyer    = $field->getField("importerCode".$count); //importador

  $query = "SELECT name, idAnt FROM Inform WHERE id = $inform";
  $cur = odbc_exec($db, $query);

  if(odbc_fetch_row($cur)) {
    $nameCl = odbc_result($cur, 1);
    $idAnt = odbc_result($cur, 2);
  }
  $cur = odbc_exec($db, "select c_Coface_Imp, idTwin from Importer where id=$idBuyer");
  if (odbc_fetch_row ($cur)){
    $ci = odbc_result($cur, 'c_Coface_Imp');
    $idOther = odbc_result($cur, 'idTwin');
  }
  if(! $idOther){
    $y = odbc_exec($db, "select id from Importer where idTwin=$idBuyer");
    $idOther = odbc_result($y, 1);
  }

  if ($editCredit && $idBuyer) {
    $query = "INSERT INTO ChangeCredit (credit, state, userIdChangeCredit, idImporter) 
	      VALUES ($editCredit, 9, $userID, $idBuyer)";
    $cur = odbc_exec($db, $query);
    if($idOther){
      $query = "INSERT INTO ChangeCredit (credit, state, userIdChangeCredit, idImporter) 
	        VALUES ($editCredit, 9, $userID, $idOther)";
      $cur = odbc_exec($db, $query);
    }
    $ok  = "true";
  }
  $count++;
}

?>
