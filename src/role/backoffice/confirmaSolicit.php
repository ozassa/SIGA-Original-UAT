<?php

  if(! $notif->doneRole($idNotification, $db)){
    $msg = 'Erro ao encerrar notifica��o';
    odbc_rollback($db);
    return;
  }

?>