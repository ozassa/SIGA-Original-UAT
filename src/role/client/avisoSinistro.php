<?php  // checa se já existe um sinistro pra este importador 
  $var = odbc_exec($db, "SELECT id, status FROM Sinistro WHERE idImporter = $idImporter");
  if (!$var) {
      $status = 0;
  } else {
      $status = odbc_result($var, 2);
  }

  if ($status == 1){
      $idSinistro = odbc_result($var, 1); 
  } else {
      $q = "INSERT INTO Sinistro (idImporter) VALUES ($idImporter)";
      $cur = odbc_exec($db, $q);
      $var = odbc_exec($db, "SELECT id FROM Sinistro WHERE idImporter = $idImporter AND status = 1");
      $idSinistro = odbc_result($var, 1);
  }

//echo $idSinistro;		

?>