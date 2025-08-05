<?php  //
//	$idInform = "";
//
//	$control = "<th>A COFACE não tem Pendências.</th>";
//
//	$cCoface = odbc_exec ($db, "SELECT DISTINCT Importer.idInform
//		                    FROM Inform, Importer
//				    WHERE Importer.state = 3 or Importer.state = 4 AND Inform.id = Importer.idInform
//				    ORDER BY Importer.idInform");	
//
//	while (odbc_fetch_row ($cCoface)) {
//		$idInform = odbc_result ($cCoface, 1);
//	} 
//
//	$count = "";
//	$total = "";
//	
////	if ($idInform != "") {
////		$control  = ""; 
////		$cInform  = odbc_exec ($db, "SELECT DISTINCT Inform.name, Inform.contrat, Inform.id
////					     FROM Inform INNER JOIN
////					          Importer ON Inform.id = Importer.idInform
////					     WHERE Inform.id = $idInform");
////	}
//
//  $control  = ""; 
//  $cInform  = odbc_exec (
//			 $db,
//			 "SELECT DISTINCT Inform.name, Inform.contrat, Inform.id
//			  FROM Inform
//                            JOIN Importer ON Inform.id = Importer.idInform
//			  WHERE Importer.state = 3 or Importer.state=4"
//                          );
//	
?>