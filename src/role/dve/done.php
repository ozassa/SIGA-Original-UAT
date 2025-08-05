<?php
if(! $notif->doneRole($idNotification, $db)){
  $msg = 'Erro ao revisar DVE';
}else{
  $msg = 'DVE revisada';
}
?>
