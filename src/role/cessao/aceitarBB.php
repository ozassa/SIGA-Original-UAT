<?php 


if($tipoBanco == 3){

	$q = "UPDATE CDOB SET status = 2, dateBackoffice = getdate() WHERE id = $idCDOB";
	$cur = odbc_exec($db, $q);
       // echo "<pre>$q</pre>";

} else if($tipoBanco == 1){

	$q = "UPDATE CDBB SET status = 2, dateBackoffice = getdate() WHERE id = $idCDBB";
	$cur = odbc_exec($db, $q);
       // echo "<pre>$q</pre>";

}else{

	$q = "UPDATE CDParc SET status = 2, dateBackoffice = getdate() WHERE id = $idCDParc";
	$cur = odbc_exec($db, $q);
       // echo "<pre>$q</pre>";

}

        $r = $notif->doneRole($idNotification, $db);
        $msg = "Cessão de Direitos Aceita";
?>