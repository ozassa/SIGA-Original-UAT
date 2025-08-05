<?php 

$s = odbc_exec($db, "UPDATE Inform SET state = 9  WHERE id = $idInform");
$x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");
if(odbc_fetch_row($x)){
  $id = odbc_result($x, 1);
  $fim = odbc_result($fim, 2);
  if(! $fim){
    odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");
  }
}

?>
