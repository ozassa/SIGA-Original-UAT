<?php  $queryData = "SELECT Importer.id, Inform.name, Importer.name, Importer.address, 
	    Importer.city, Importer.c_Coface_Imp, Importer.limCredit, 
	    ChangeAddress.name, ChangeAddress.address, 
	    ChangeAddress.city, ChangeAddress.c_Coface_Imp, 
	    Importer.state, Importer.idCountry
	FROM Importer, Inform, ChangeAddress
	WHERE Importer.stateDate = '10/10/10' AND 
	    Importer.idInform = Inform.id AND 
	    Importer.id = ChangeAddress.idImporter
	    Importer.state ='3'";


   $curData = odbc_exec($db,$queryData);


   $queryCredit = "SELECT Importer.id, Inform.name, Importer.name, Importer.limCredit,".
		  "Importer.state, Importer.idCountry, Importer.c_Coface_Imp, Importer.stateDate".
	          "FROM   Importer, Inform, ChangeAddress, ChangeCredit".
	          "WHERE  Importer.stateDate = '10/10/10'".
	          "AND    (Importer.state = 4 OR Importer.state = 5 OR Importer.state = 6  OR Importer.state = 7)".
                  "AND    Inform.id = Importer.idInform". 
                  "AND    ChangeCredit.idImporter = Importer.id";

   $curCredit = odbc_exec($db,$queryCredit);




?>