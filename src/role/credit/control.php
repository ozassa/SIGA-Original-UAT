<?php  /*
	$idInform = $field->getField(idInform);
	$idBuyer  = $field->getField(idBuyer);


	if ($idBuyer && $idInform) {
		$query   = "SELECT Inform.name, Importer.name
			    FROM   Inform, Importer
			    WHERE  Inform.id   = Importer.idInform
			    AND    Importer.id = $idBuyer
			    AND    Inform.id   = $idInform";
		$control = 1;
	} else if ($idInform) {
		$query   = "SELECT Inform.nameme
			    FROM   Inform
			    WHERE  Inform.id = $idInform";
		$control = 2;
	} else if ($idBuyer) {
		$query   = "SELECT Inform.name, Importer.name
			    FROM   Importer, Inform
		      	    WHERE  Importer.id = $idBuyer
			    AND    Importer.idInform = Inform.id";
		$control = 1;
	}
	
	$cur = odbc_exec ($db, $query);
	

	if (odbc_fetch_row($cur) && control == 1){
		$nameInform = " [".odbc_result($cur, 1)."] ";
		$nameBuyer  = " [".odbc_result($cur, 2)."] ";	
	} else if (odbc_fetch_row($cur) && control == 2){
		$nameInform = " [".odbc_result($cur, 1)."] ";
		$nameBuyer  = "";
	}
	
	$namePrint = $nameInform.$nameBuyer;
*/
?>