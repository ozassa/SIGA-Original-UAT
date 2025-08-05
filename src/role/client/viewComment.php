<?php  $x = odbc_exec($db,
	       "select u.name, i.texto, i.date
                from InformComment i join Users u on i.idUser=u.id
                where i.id=$idComment");
$autor = odbc_result($x, 1);
$texto = odbc_result($x, 2);
$data = ymd2dmy(odbc_result($x, 3));

$x = odbc_exec($db, "select name from Inform where id=$idInform");
$name = odbc_result($x, 1);
?>
