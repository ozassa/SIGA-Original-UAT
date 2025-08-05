<?php  require_once("../rolePrefix.php");

$searchCountry = $field -> getField(searchCountry);

$query ="SELECT Inform.contrat, Importer.name, Importer.c_Coface_Imp,
		    Importer.limCredit, Country.code, ChangeCredit.credit,
		    MAX (ChangeCredit.stateDate), Importer.id, ChangeCredit.state,
		    Country.name, Inform.name
		FROM ChangeCredit, Importer, Country, Inform
		WHERE ChangeCredit.idImporter = Importer.id AND
		    Importer.idCountry = Country.id AND
		    Importer.idInform = Inform.id AND
	 	    Country.name = LIKE '%".strtoupper($searchCountry)."%' AND
		    ChangeCredit.state <> 14
		GROUP BY Inform.contrat, Importer.name, Importer.c_Coface_Imp,
		    Importer.limCredit, Country.code, ChangeCredit.credit,
		    Importer.id, ChangeCredit.state, Country.name, Inform.name";

$country = odbc_exec($db,$query);

?>
