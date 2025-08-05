<?php  $query = "select i1.idAnt, i1.state from Inform i1 JOIN Inform i2 ON (i2.id = i1.idAnt) where i1.id=$idInform AND i2.state = 10";
//echo "<pre>$query</pre>";
$cc = odbc_exec($db, $query);
if(odbc_fetch_row($cc)){
  if(odbc_result($cc, 1) > 0){
    $renova = 1;
    $state = odbc_result($cc, 2);
  }
}

?>
