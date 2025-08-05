<?php  
// Alterado Hicom (Gustavo) - 28/12/04 - Alteração do processo de solicitação de cobertura para juros de mora -->

$q = "UPDATE JurosMora SET state=3 WHERE id=$idJuros";
$cur = odbc_exec($db, $q);

// alterado Hicom (Gustavo) - passei essa query para o arquivo role\financ\confirmaSolicit.php
// $q = "UPDATE Inform SET warantyInterest=1 WHERE id=$idInform";
// $cur = odbc_exec($db, $q);


$r = $notif->doneRole($idNotification, $db);

?>
