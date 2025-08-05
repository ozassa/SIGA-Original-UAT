<?php  $idNotification = $field->getField("idNotification");
$idInform       = $field->getField("idInform");
$idBuyer        = $field->getField("idBuyer");
$invoice        = $field->getField("invoice");
$type           = $field->getField("type");
$limTemp        = $field->getField("limTemp");
$flag           = $field->getField("flag"); // indica se veio de um NotificationU

$credit       = $field->getField("credit");
$creditReq    = $field->getField("creditReq");
$creditTemp   = $field->getField("creditTemp");

if(! $flag){ // não é NotificationU
  $resultado = odbc_exec($db, "select notification, cookie from NotificationR where id=$idNotification and state=1");
  if(odbc_fetch_row($resultado)){
    $u_notification = odbc_result($resultado, 'notification');
    $u_cookie = odbc_result($resultado, 'cookie');
    $notif->doneRole($idNotification, $db);
  }
}

// gera outra notificacao apenas para este usuario
if($u_notification){
  // verifica se já foi gerada uma notificação para este usuario
  $resultado = odbc_exec($db, "select * from NotificationU where cookie='$u_cookie'");
  if(! odbc_fetch_row($resultado)){ // notificacao ainda nao foi gerada
    $q = "insert into NotificationU (notification, state, cookie) values ".
      "('$u_notification', '1', '$u_cookie')";
    if(! odbc_exec($db, $q)){
      die("$q <br>". odbc_errormsg($db));
    }
    $r = odbc_exec($db, "select max(id) from NotificationU where cookie='$u_cookie'");
    if(odbc_fetch_row($r)){
      $u_id = odbc_result($r, 1);
      // monta o super-link
      $u_link = "../credit/Credit.php?comm=acceptDATA&idBuyer=$idBuyer&idNotification=$u_id".
	"&type=$type&idInform=$idInform&invoice=$invoice&flag=1&obs=".
	urlencode($obs). "&credit=$credit&creditReq=$creditReq&decision_date=$decision_date";
      if($creditTemp > 0){
	$u_link .= "&creditTemp=$creditTemp&limTemp=". urlencode($limTemp);
      }
      odbc_exec($db, "update NotificationU set link='$u_link' where id=$u_id");
    }
    odbc_exec($db, "insert into UserNotification (idUser, idNotification) values ($userID, $u_id)");
    $idNotification = $u_id;
  }
}

$query = "SELECT Importer.address, Importer.tel, Importer.city,
	    Importer.name, Importer.c_Coface_Imp, Country.code,
	    ChangeAddress.city, ChangeAddress.address,
	    ChangeAddress.name, ChangeAddress.tel,
	    MAX(ChangeAddress.stateDate) as stateDate,
            ChangeAddress.state, Country.name, Importer.id
	  FROM ChangeAddress, Importer, Country
	  WHERE ChangeAddress.idImporter = Importer.id AND
	    Importer.idCountry = Country.id AND Importer.id = $idBuyer AND
	    ChangeAddress.userIdChangeAddress = '13' AND
            ChangeAddress.id = (select max(id) from ChangeAddress where idImporter=$idBuyer)
	  GROUP BY Importer.name, Importer.c_Coface_Imp, Country.code,
	    ChangeAddress.city, ChangeAddress.address,
	    ChangeAddress.name, ChangeAddress.tel,
	    ChangeAddress.state, Country.name, Importer.id,
	    Importer.address, Importer.tel, Importer.city
          ORDER BY stateDate DESC";
$cur = odbc_exec($db, $query);

$q = "SELECT Inform.contrat, Inform.name, Inform.state
      FROM Inform, Importer
      WHERE Inform.id = Importer.idInform AND
      Importer.id = $idBuyer";
$curInform = odbc_exec($db, $q);

?>
