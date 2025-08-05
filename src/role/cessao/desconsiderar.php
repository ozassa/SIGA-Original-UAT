<?php  

if($tipo == "BB"){

  $q = "UPDATE CDBB SET status=2 WHERE id=$idCDBB";
  $cur = odbc_exec($db, $q);

}else if($tipo == "OB"){

  $q = "UPDATE CDOB SET status=2 WHERE id=$idCDBB";
  $cur = odbc_exec($db, $q);

}else{

  $q = "UPDATE CDParc SET status=2 WHERE id=$idCDBB";
  $cur = odbc_exec($db, $q);

}

  $msg = "Cancelamento de Cessão de Direito Desconsiderada";

  $r = $notif->doneRole($idNotification, $db);

?>
