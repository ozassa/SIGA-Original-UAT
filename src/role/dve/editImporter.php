<?php $x = odbc_exec($db, "select id from DVEDetails where idDVE=$idDVE and modalidade=$modalidade and state=1 order by id");
$i = 1;
while(odbc_fetch_row($x)){
  if(odbc_result($x, 1) == $idDetail){
    $registro = $i;
    break;
  }
  $i++;
}
?>
