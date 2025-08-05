<?php  require_once("../rolePrefix.php");

$i = $field->getField("i");
$count = 1;
$ok = true;
$changed = 0;
odbc_autocommit ($db, false);
while($count <= $i && $ok) {
  $editCreditTxt = $field->getField("edit".$count);
  $editCredit = 1000 * $field->getNumField("edit".$count);   //valor
  $idBuyer    = $field->getField("importer".$count);  //importador
  $creditTemp = $field->getNumField("creditTemp$count"); //credito temporario
  $limTemp    = $field->getField("limTemp$count");    //limite do credito temporario
  $idAnt = odbc_result(odbc_exec($db, "select idAnt from Inform where id=$idInform"), 1);

  $query = "SELECT inf.name, imp.limCredit, ch.credit, ch.analysis, ch.monitor,
                   ch.limTemp, ch.creditTemp, imp.c_Coface_Imp, imp.idCountry, imp.idTwin
		 FROM (
                   SELECT idImporter, credit, analysis, monitor, limTemp, creditTemp
                   FROM   ChangeCredit ch
                   WHERE  id IN
                     (
                       SELECT max (id)
                       FROM   ChangeCredit
                       GROUP BY idImporter
                     )
                 ) ch RIGHT JOIN Importer imp ON (ch.idImporter = imp.id)
                            JOIN Inform   inf ON (imp.idInform  = inf.id)
		 WHERE imp.id = $idBuyer
                ";

  $cur = odbc_exec($db,$query);
  if(odbc_fetch_row($cur)) {
    $oldConcCredit  = odbc_result($cur, "credit");
    $analysis   = odbc_result($cur, "analysis");
    $monitor    = odbc_result($cur, "monitor");
    $creditTemp = odbc_result($cur, "creditTemp");
    $limTemp = odbc_result($cur, "limTemp");
    $ci = odbc_result($cur, 'c_Coface_Imp');
    $idCountry = odbc_result($cur, 'idCountry');
    $idOther = odbc_result($cur, 'idTwin');
    if ($analysis == '') $analysis = 0;
    if ($monitor == '') $monitor = 0;
    if ($oldConcCredit == "") $oldConcCredit = 0;
    $oldSolicCredit = odbc_result($cur, "limCredit");
    $nameCl = odbc_result($cur, "name");
    if(! $idOther){
      $y = odbc_exec($db, "select id from Importer where idTwin=$idBuyer");
      $idOther = odbc_result($y, 1);
    }

    if ($editCreditTxt != "") {
      $r = odbc_exec ($db,
		      "UPDATE Importer SET limCredit = $editCredit, state = 2 WHERE id = $idBuyer"
		      );
      if ($r == 0) $ok = false;

      $query =
	"INSERT INTO ChangeCredit (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state)
         VALUES ($oldConcCredit, $editCredit, $userID, $idBuyer, $analysis, $monitor, 2)";
      if ($limTemp != '')
	$query =
	  "INSERT INTO ChangeCredit (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state, creditTemp, limTemp)
           VALUES ($oldConcCredit, $editCredit, $userID, $idBuyer, $analysis, $monitor, 2, $creditTemp, '$limTemp')";
      $r = odbc_exec ($db, $query);
      if ($r == 0) $ok= false;

      if($idOther){
	$query =
	  "INSERT INTO ChangeCredit (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state)
           VALUES ($oldConcCredit, $editCredit, $userID, $idOther, $analysis, $monitor, 2)";
	if ($limTemp != '')
	  $query =
	    "INSERT INTO ChangeCredit
             (credit, creditSolic, userIdChangeCredit, idImporter, analysis, monitor, state, creditTemp, limTemp)
             VALUES($oldConcCredit, $editCredit, $userID, $idOther, $analysis, $monitor, 2, $creditTemp, '$limTemp')";
	$r = odbc_exec ($db, $query);
	if ($r == 0) $ok= false;
      }
      if ($ok){
        $changed++;
	odbc_commit ($db);
      }else{
	odbc_rollback ($db);
      }
    }
  }
  $count ++;
} // while

if ($ok) {
  $msg = "Solicitação Finalizada com Sucesso para $changed Importador(es).";
  if ($changed > 0)
    $r = $notif->clientChangeCredit ($userID, $nameCl, $idInform, $db, 10);
} else {
  $msg = "problemas na atualização dos limites solicitados.";
}
odbc_autocommit ($db, true);

$comm = "open";

?>
