<?php  $address = strtoupper($address);
$city = strtoupper($city);
$nameImporter = ereg_replace("'", "''", strtoupper($nameImporter));

$msg = "Erro!";

$msg = $idInform.";".$nameImporter.";".$address.";".$idCountry.";".$tel.";".$prevExp12.";".$limCredit
.";".$numShip12.";".$periodicity.";".$risk.";".$city.";".$przPag.";".$cur;


$q = "INSERT INTO Importer (idInform, name, address, idCountry, tel, prevExp12, limCredit,
      numShip12, periodicity, risk, city, przPag)
      VALUES ('$Inform', '$nameImporter', '$address', '$idCountry', '$tel', '$prevExp12', '$limCredit',
      '$numShip12', '$periodicity', '$risk', '$city', '$przPag')";
		 
$cur = odbc_exec($db, $q);
		
if (!$cur) {
  $msg = "Problemas na atualização da base";
} else {
  $msg = "";
}

?>
