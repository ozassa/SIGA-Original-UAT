<?php   //  $cur=odbc_exec(
//    $db,
//    "SELECT notification FROM NotificationR WHERE id = $idNotification"
//  );


$cur=odbc_exec(
$db,
"SELECT id, name, contrat, dateEmission, (prMin*(1+txRise)/numParc) as parcela FROM Inform where state=6 AND pgOk=0 AND codProd >0 AND  mailOk=0  ORDER BY dateEmission"
);


if(! $msg){
  $state = odbc_exec($db, "UPDATE Inform SET state = 7 WHERE (id = $idInform)");
}
//  if (!odbc_fetch_row($cur)) $title = "Notificação inválida";
//  else "valeu";

//$title = odbc_result($cur,1);
?>