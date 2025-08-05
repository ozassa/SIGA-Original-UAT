<?php  $address = strtoupper($address);
$city = strtoupper($city);
$nameImporter = ereg_replace("'", "''", strtoupper($nameImporter);

$q = "INSERT INTO Importer (idInform, name, address, idCountry, tel, prevExp12, limCredit, numShip12, periodicity, risk, city, przPag) VALUES ($idInform, $nameImporter, $address, $idCountry, $tel, $prevExp12, $limCredit, $numShip12, $periodicity, $risk, $city, $przPag)";

$cur = odbc_exec($db,$q);
$msg = $idInform.";".$nameImporter.";".$address.";".$idCountry.";".$tel.";".$prevExp12.";".
      $limCredit.";".$numShip12.";".$periodicity.";".$risk.";".$city.";".$przPag;
?>

