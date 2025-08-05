<?php  /*
  Motor de busca    
*/
$q = "SELECT ChangeCredit.credit, ChangeCredit.stateDate
	      FROM ChangeCredit, Importer
	      WHERE Importer.id = " . $field->getField("idBuyer") . " AND 
	      ChangeCredit.idImporter = Importer.id
	      ORDER BY ChangeCredit.stateDate DESC";

$cur = odbc_exec ($db, $q);
$credCons = 0;


if (odbc_fetch_row($cur)) {
  $credCons = odbc_result ($cur, 1);
}

if($field->getField("idBuyer"))
   $idBuyer = $field->getField("idBuyer");
	
	

$query  = "SELECT Importer.name, Importer.address, Importer.risk, Importer.city, Country.name,
       	          Importer.tel, Importer.limCredit, Importer.numShip12, Importer.periodicity,
               	  Importer.przPag, Importer.idCountry, Importer.c_Coface_Imp, Importer.prevExp12,
		  Importer.fax, Importer.cep, Importer.contact, Importer.divulgaNome, Importer.emailContato,
          Importer.cnpj, Importer.relation
           FROM Importer, Country
           WHERE Importer.id = $idBuyer AND Importer.idCountry = Country.id";

$cur = odbc_exec($db,$query);



$head = odbc_exec($db, "SELECT Inform.name, Inform.contrat, Inform.id, Inform.currency
			FROM Inform, Importer
			WHERE Inform.id = Importer.idInform AND Importer.id = $idBuyer");


?>
