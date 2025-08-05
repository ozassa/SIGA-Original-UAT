<?php 
 if(! function_exists('renovacao')){
	  function renovacao($idInform, $db){
		$x = odbc_exec($db, "select idAnt from Inform where id=$idInform");
		$idAnt = odbc_result($x, 1);
		if(! $idAnt || $idAnt <= 0){
		  return 0;
		}
		$x = odbc_exec($db, "select state from Inform where id=$idAnt");
		$state = odbc_result($x, 1);
		if($state == 10){
		  return 1;
		}
		return 0;
	  }
}



$cur = odbc_exec($db, "SELECT notification, bornDate FROM NotificationR WHERE id = $idNotification");


if (!odbc_fetch_row($cur)){
  $title = "Notificação inválida";
}else{
  $title = odbc_result($cur,1);
  $notificationDate = odbc_result($cur,2);
  $notificationDate = substr($notificationDate,8,2)."/".substr($notificationDate,5,2)."/".substr($notificationDate,0,4);

  $x = odbc_exec($db, "select idAnt, sentOffer from Inform where id=$idInform");
  $idAnt = odbc_result($x, 1);
  $sentOffer = odbc_result($x, 2);
  if(renovacao($idInform, $db)){
    $title .= "<br><center>(Renovação)</center>";
  }
}


$cc = odbc_exec ($db,
		 "SELECT u.login FROM (Users u JOIN Insured i ON (u.id = i.idResp)) ".
		 "JOIN Inform inf ON (inf.idInsured = i.id)WHERE inf.id = $idInform");
if(odbc_fetch_row($cc)){
  $email = odbc_result($cc, 1);
}
?>