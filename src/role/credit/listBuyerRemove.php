<?php  $idInform = $field->getField("idInform");
$idBuyer = $field->getField("idBuyer");

$query  = "SELECT Importer.id, Importer.name, Country.name, Country.code,
	   	Importer.limCredit, Importer.c_Coface_Imp
	   FROM Importer, Country
	   WHERE Importer.idInform = $idInform AND
		Importer.idCountry = Country.id AND
		(Importer.state = 7 or Importer.state = 9 or
                 Importer.id in (select idImporter from ImporterRem))
                and Importer.id=$idBuyer
	   ORDER BY Importer.id";
$cur = odbc_exec($db, $query);

$q = "SELECT Inform.contrat, Inform.name  FROM Inform where Inform.id = $idInform";
$d = odbc_exec($db, $q);

if(odbc_fetch_row($d)){
  $nameExpo = odbc_result($d, 2);
  $ciExpo   = odbc_result($d, 1);
}else{
  $nameExpo = "ERRO";
  $ciExpo   = "ERRO";
}
?>
