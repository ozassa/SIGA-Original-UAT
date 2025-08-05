<?php  // Alterado Hicom (Gustavo) - 23/12/04 - Alteração do processo de solicitação de cobertura para juros de mora
// antes:
/*
    $var=odbc_exec($db, "SELECT name FROM Inform Where id = $idInform");
    $name = odbc_result($var, 1);

    $r = $notif->jurosMoraF($usuario, $idInform, $name, $db, $idJuros, "financ");
    if (!$r) {
       	$msg = "problemas na criação da notificação pro Financeiro";
      	$ok = false;
    } else {
        $r = $notif->jurosMoraB($usuario, $idInform, $name, $db, $idJuros, "backof");
        if (!$r) {
       	    $msg = "problemas na criação da notificação pro Endosso";
      	    $ok = false;
        }
    }
*/

    $var=odbc_exec($db, "SELECT name FROM Inform Where id = $idInform");
    $name = odbc_result($var, 1);

    $r = $notif->jurosMoraB($usuario, $idInform, $name, $db, $idJuros, "backof");
    if (!$r) {
 	    $msg = "problemas na criação da notificação pro Endosso";
	    $ok = false;
    }
    $msg = "Juros de Mora Solicitado";
    
?>
