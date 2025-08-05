<?php  if($done){
  $notif->doneRole($idNotification, $db);
}else{
  $c = odbc_exec($db, "select name, idAnt from Inform where id=$idInform");
  if(odbc_fetch_row($c)){
    $nameCl = odbc_result($c, 1);
    $idAnt = odbc_result($c, 2);
  }
  if(! $idAnt){
    $msg = 'Esse não é um informe de renovação';
    return;
  }

  $query = "select name, c_Coface_Imp from Importer where idInform=$idAnt
            and c_Coface_Imp not in
            (select c_Coface_Imp from Importer where idInform=$idInform)
            order by name";
  $c = odbc_exec($db, $query);
}

?>
