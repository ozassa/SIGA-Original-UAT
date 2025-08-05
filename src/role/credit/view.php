<?php  $cur=odbc_exec(
    $db,
    "SELECT notification, bornDate FROM NotificationR WHERE id = $idNotification"
  );
  if (!odbc_fetch_row($cur)) $title = "Notificação inválida";
  else {
    $title = odbc_result($cur,1);
    $notificationDate = odbc_result($cur,2);
    $notificationDate = substr($notificationDate,8,2)."/".substr($notificationDate,5,2)."/".substr($notificationDate,0,4);
  }
?>