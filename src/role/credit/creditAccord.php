<?php  $query = "SELECT DISTINCT Importernform.name, Inform.id
		  FROM Inform	
		  WHERE Inform.state = '3' or 
		  ORDER BY Inform.id";

	$cursor = odbc_exec($db,$query);
	
	while (odbc_fetch_row ($cursor)) {
		$keyInform = odbc_result ($db, 1);

		$q = "SELECT COUNT(idInform) 
		      FROM Importer 
                      WHERE idInform = '$keyInform'";

		$c = odbc_exec ($db, $q);

		$control = true;

		if (odbc_fetch_row ($c)) { 
			$count	= odbc_result($c, 1);
		} else {
			$control = false; //error
		}

		$qQ =  "SELECT Inform.id
			FROM Importer, Inform
			WHERE Inform.id = Importer.idInform 
			AND (Importador.state <> 0 or Importer.state <> 2 OR Importer.state <> 1 OR Importer.state <> 14 OR Importer.state <> 15) 
			AND Importer.idInform = '$keyInform'";
            
            echo "<pre>$cC</pre>";       
		$cC =  odbc_exec($db, $cC);
		$i = 0;
		while (odbc_fetch_row ($cC)) {
			$i ++;
			$idInform = odbc_result ($cC, 1);
		}
 		if ($control) {
			if ($i == $count) {
				$cur = odbc_exec ($db, "SELECT Inform.name 
							FROM Inform 
							WHERE id = $idInform");

				
			}
		}
			
	}



	


?>