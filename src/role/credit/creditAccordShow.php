<?php  $idInform = $field -> getField (idInform);

	$q = 	"Select Inform.name, Inform.contrat
		 FROM   Inform
		 Where  Inform.id = $idInform";

	$c =	odbc_exec($db, $q);

////////////////

	$qQ = "SELECT ChangeCredit.id, ChangeCredit.stateDate
		FROM ChangeCredit, Importer, Inform
		WHERE ChangeCredit.idImporter = Importer.id AND 
		    Importer.idInform = Inform.id AND Inform.id = $idInform
		ORDER BY ChangeCredit.stateDate DESC";

	$cC = odbc_exec ($db, $qQ);

	if (odbc_fetch_row ($cC)){
		$idChangeCredit = odbc_result ($cC, 1);
		$query = "SELECT Importer.przPag, Importer.periodicity, Importer.name, 
			    Importer.c_Coface_Imp, Importer.limCredit, Country.code, 
			    ChangeCredit.credit, Country.name, Importer.id
			FROM ChangeCredit, Importer, Country, Inform
			WHERE ChangeCredit.idImporter = Importer.id AND 
			    Importer.idCountry = Country.id AND 
			    Importer.idInform = Inform.id AND Inform.id = $idInform 
			AND (Importer.state <> 2 OR Importer.state <> 1 OR Importer.state <> 14 OR Importer.state <> 15) 
			GROUP BY Importer.przPag, Importer.periodicity, Importer.name, 
			    Importer.c_Coface_Imp, Importer.limCredit, Country.code, 
			    ChangeCredit.credit, Country.name, Importer.id";

		$cur = odbc_exec($db,$query);
	} else {
		$msg = "ERROR";
	}

?>
