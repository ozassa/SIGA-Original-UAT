<?php  

  $q = "UPDATE JurosMora SET dateVenc = '$vencimento' WHERE id = $idJuros";
  $cur = odbc_exec($db, $q);
  //echo "<pre>$q</pre>";

?>