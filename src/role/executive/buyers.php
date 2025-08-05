<?php  $c = odbc_exec($db, "select idAnt, state from Inform where id=$idInform");
$idAnt = odbc_result($c, 1);
$informState = odbc_result($c, 2);
?>
