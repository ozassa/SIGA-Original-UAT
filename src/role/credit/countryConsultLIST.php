<?php  $idCountry = $field -> getField(idCountry);

/////////// credit's sum for country

	
	$a = odbc_exec ($db, "SELECT DISTINCT Inform.id, Inform.name, Inform.contrat
				FROM Importer, Inform
				WHERE idCountry = $idCountry 
				AND Importer.state <> 15
				AND Inform.id = Importer.idInform");

	while (odbc_fetch_row ($a)) { // escopo exportador
		$idInform1 = odbc_result ($a, 1);

		$d = odbc_exec ($db, "SELECT ChangeCredit.credit, Importer.id, Importer.idInform,
						 ChangeCredit.state
					FROM ChangeCredit, Importer, Inform
					WHERE Inform.id = $idInform1
					AND Inform.id = Importer.idInform 
					AND Importer.id = ChangeCredit.idImporter
					AND Importer.state <> 15
					AND ChangeCredit.state <> 14
					ORDER BY ChangeCredit.stateDate DESC");

		if (odbc_fetch_row ($d)) {
			$creditS1 = odbc_result ($d, 1);

		} else {
			$creditS1 = 0;
		}
		
		$creditSum = $creditSum + $creditS;

		$b = odbc_exec ($db, "SELECT Importer.id, Importer.name, Importer.limCredit, 
					    Importer.c_Coface_Imp
					FROM Importer
					WHERE Importer.idInform = '$idInform1'
					AND Importer.state <> '15'
					AND Importer.idCountry = '$idCountry'");
		$i = 0;

		while (odbc_fetch_row($b)) { // escopo importador
			$idBuyer1   = odbc_result ($b, 1);

			$i++;
	
			$c = odbc_exec ($db, "SELECT ChangeCredit.credit
						FROM ChangeCredit, Importer
						WHERE ChangeCredit.idImporter = $idBuyer1
						AND ChangeCredit.state <> 14
						AND Importer.id = ChangeCredit.idImporter
						AND Importer.state <> 15
						ORDER BY ChangeCredit.stateDate DESC");

			if (odbc_fetch_row ($c)) { // escopo importador
				$credit1 = odbc_result ($c, 1);

			} else {
				$credit1 = 0;
			}

			$creditCountry = $credit1 + $creditCountry;

		}
	}
?>
	
