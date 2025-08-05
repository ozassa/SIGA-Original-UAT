<?php  /* 
Motor
*/

	$q = 	"SELECT Importer.name, Country.name, Country.code, 
		    Importer.c_Coface_Imp, Importer.limCredit, Importer.id
		FROM Importer, Country
		WHERE Importer.idCountry = Country.id AND 
		    Importer.name LIKE '%$importconsult%'";


	$import = odbc_exec($db, $q);


?>