<?php

$agencia  = $_REQUEST['agencia'];

if($tipoBanco != 3){
  $q = "SELECT Agencia.id, Agencia.name, Agencia.endereco, Agencia.cidade, Agencia.cnpj, Agencia.ie, Banco.codigo 
      FROM Agencia 
      INNER JOIN Banco ON Banco.id = Agencia.idBanco 
      WHERE Agencia.codigo = ? AND idBanco = ?";

$cur = odbc_prepare($db, $q);
odbc_execute($cur, [$agencia, $idBanco]);
  //print $q;
  
  if (odbc_fetch_row($cur)){
      $idAgencia = odbc_result($cur, 1);
      $agNome = odbc_result($cur, 2);
      $agEnd  = odbc_result($cur, 3);
      $agCid  = odbc_result($cur, 4);
      $agCNPJ  = odbc_result($cur, 5);
      $agIE = odbc_result($cur, 6);
      $codBanco = odbc_result($cur, 7);
      $idRegion = 0;
  } else {
      $msgAg = "A Agência informada não existe.";
  }
}

odbc_free_result($cur);

?>