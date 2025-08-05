<?php  $cookie = $field -> getField ("cookie");
	
	$cur = odbc_exec ($db, "SELECT idImporter FROM MailConfirm WHERE cookie = $cookie");

	if (odbc_fetch_row ($cur)) {
		$idImporter = odbc_result ($cur, 1);
	} else {
		$msg = "ERRO: Entre em contato com a SBCE.";
	}

	$cur = odbc_exec ($db, "SELECT Inform.name, Inform.contrat 
				FROM Inform, Importer 
				WHERE Inform.id = Importer.idInform 
				AND Importer.id = $idImporter");
	
	if (odbc_fetch_row ($cur)) {
		$nameCl = odbc_result ($cur, 1);
		$coface = odbc_result ($cur, 2);
	}

	$cur = odbc_exec($db, "SELECT Importer.name, Importer.address, Importer.tel, 
				Importer.city, Country.name, Importer.limCredit
				FROM Importer, Country
				WHERE Country.id = Importer.idCountry
				AND Importer.id = '$idImporter'");

	if (odbc_fetch_row ($cur)) {
		$address = odbc_result ($cur, 2);		
		$tel	 = odbc_result ($cur, 3);		
		$name    = odbc_result ($cur, 1);		
		$city	 = odbc_result ($cur, 4);		
		$country = odbc_result ($cur, 5);
		$limCred = odbc_result ($cur, 6);		
	}

	$cur = odbc_exec ($db, "SELECT ChangeAddress.address, ChangeAddress.tel,
					ChangeAddress.name, ChangeAddress.city, Country.name
				FROM ChangeAddress, Country
				WHERE idImporter = '$idImporter' 
				AND Country.id = ChangeAddress.idCountry
				ORDER BY ChangeAddress.stateDate DESC");

	if (odbc_fetch_row ($cur)) {
		$address = odbc_result($cur, 1);		
		$tel	 = odbc_result($cur, 2);		
		$name    = odbc_result($cur, 3);		
		$city	 = odbc_result($cur, 4);		
		$country = odbc_result($cur, 5);		
	}
		
	$cur = odbc_exec ($db, "SELECT credit, stateDate
				FROM ChangeCredit
				WHERE (state <> 14) AND idImporter = '$idImporter'
				ORDER BY stateDate DESC");

	if (odbc_fetch_row ($cur)) {
			$changeCredit = odbc_result ($cur, 1);
			$changeDate   = odbc_result ($cur, 2);
		}

?>