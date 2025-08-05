<?php  $idBuyer = $field->getField("idBuyer");
$idInform = $field->getField("idInform");
$action = $field->getField("action");
$name = ereg_replace("'", "''", $name);

$query =  "UPDATE Importer  SET name = '$name', address = '$address', city = '$city', tel = '$tel', cep = '$cep', contact = '$contact', prevExp12 = '$prevExp12', risk = '$risk', numShip12 = '$numShip12', limCredit = '$limCredit', periodicity = '$periodicity', przPag = '$przPag', fax = '$fax' WHERE id = $idImporter")";

?>

