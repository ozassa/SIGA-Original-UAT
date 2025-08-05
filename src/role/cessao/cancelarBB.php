<?php 
if($tipoBanco == 3){

$q = "UPDATE CDOB SET status=3, dateBackoffice=getdate() WHERE id=$idCDOB";
$cur = odbc_exec($db, $q);

}else if($tipoBanco == 1){

$q = "UPDATE CDBB SET status=3, dateBackoffice=getdate() WHERE id=$idCDBB";
$cur = odbc_exec($db, $q);

}else{

$q = "UPDATE CDParc SET status=3, dateBackoffice=getdate() WHERE id=$idCDParc";
$cur = odbc_exec($db, $q);

}

$r = $notif->doneRole($idNotification, $db);
$msg = "Cessão de Direitos Cancelada";

?>
