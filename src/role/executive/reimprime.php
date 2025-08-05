<?php  // pega as segundas vias
$x = odbc_exec($db, "select name, segundaVia, state from Inform where id=$idInform");
$name = odbc_result($x, 1);
$key = odbc_result($x, 2);
$state = odbc_result($x, 3);

?>
