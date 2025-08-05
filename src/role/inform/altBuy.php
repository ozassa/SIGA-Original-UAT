<?php 

  $idBuy = $field->getNumField("idBuy");
  $rAlter = odbc_exec(
    $db,
    " SELECT name, address, idCountry, tel, prevExp12, limCredit,
	 numShip12, periodicity, risk, city, przPag, expMax,limCredit
	 FROM Importer WHERE id = $idBuy");
	 
	 
  if (odbc_fetch_row($rAlter)) {
    $name 	= odbc_result($rAlter, 1);
    $address	= odbc_result($rAlter, 2);
    $idCountry	= odbc_result($rAlter, 3);
    $tel	= odbc_result($rAlter, 4);
    $preevExp12	= odbc_result($rAlter, 5);
    $limCredit	= odbc_result($rAlter, 6);
    $numShip12	= odbc_result($rAlter, 7);
    $periodicity= odbc_result($rAlter, 8);
    $risk	= odbc_result($rAlter, 9);
    $city	= odbc_result($rAlter, 10);
    $przPag	= odbc_result($rAlter, 11);
	$expMax = odbc_result($rAlter, 12);
	$limCredit = odbc_result($rAlter, 13);
  } else{
      $msg = "Problemas na Leitura dos Dados para Alteração";
  }
  

?>
