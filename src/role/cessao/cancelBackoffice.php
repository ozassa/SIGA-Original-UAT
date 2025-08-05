<?php  

if($tipo == "BB"){

  $q = "UPDATE CDBB SET status=3 WHERE id=$idCDBB";
  $cur = odbc_exec($db, $q);

}else if($tipo == "OB"){

  $q = "UPDATE CDOB SET status=3 WHERE id=$idCDBB";
  $cur = odbc_exec($db, $q);

}else{

  $q = "UPDATE CDParc SET status=3 WHERE id=$idCDBB";
  $cur = odbc_exec($db, $q);

}

  $msg = "Cessão de Direito Cancelada";

  $r = $notif->doneRole($idNotification, $db);

?>
