<?php $c = odbc_exec($db, "select name, startValidity, endValidity from Inform where id=$idInform");
$name = odbc_result($c, 1);
$start = ymd2dmy(odbc_result($c, 2));
$end = ymd2dmy(odbc_result($c, 3));

$c = odbc_exec($db, "select num from DVE where idInform=$idInform order by num");
?>
