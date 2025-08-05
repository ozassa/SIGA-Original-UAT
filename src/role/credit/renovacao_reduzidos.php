<?
if($done){
  $notif->doneRole($idNotification, $db);
}else{
  $c = odbc_exec($db, "select name, contrat, idAnt from Inform where id=$idInform");
  if(odbc_fetch_row($c)){
    $nameCl = odbc_result($c, 1);
    $contrat = odbc_result($c, 2);
    $idAnt = odbc_result($c, 3);
  }
  if(! $idAnt){
    $msg = 'Esse não é um informe de renovação';
    return;
  }
  $x = odbc_exec($db, "select endValidity from Inform where id=$idAnt");
  $endValidity = ymd2dmy(odbc_result($x, 1));
  $query =
     "select i.name, c.name, i.c_Coface_Imp, i.limCredit ".
     "from Importer i join Country c on c.id=i.idCountry ".
     "where i.idInform=$idInform and i.creditAut=1 order by i.name";
  $c = odbc_exec($db, $query);
}
?>
