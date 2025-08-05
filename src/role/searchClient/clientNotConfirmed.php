<?php

$qry = "SELECT Inform.name, Inform.contrat, Importer.stateDate, 
			Importer.state
		FROM Importer, Inform
		WHERE (Importer.state = 10) OR
			(Importer.state = 12) OR
			(Importer.state = 13) AND (Importer.idInform = Inform.id) ";
	
?>