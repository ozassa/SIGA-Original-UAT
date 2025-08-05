<?php

  if(! $notif->doneRole($idNotification, $db)){
    $msg = 'Erro ao encerrar notificação';
    odbc_rollback($db);
    return;
  }

?>