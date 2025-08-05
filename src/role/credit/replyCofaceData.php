<?php  $query = "SELECT Importer.address, Importer.tel, Importer.city, 
	    Importer.name, Importer.c_Coface_Imp, Country.code, 
	    ChangeAddress.city, ChangeAddress.address, 
	    ChangeAddress.name, ChangeAddress.tel, 
	    MAX(ChangeAddress.stateDate) as date, ChangeAddress.state, 
	    Country.name, Importer.id
	  FROM ChangeAddress, Importer, Country
	  WHERE ChangeAddress.idImporter = Importer.id AND 
	    Importer.idCountry = Country.id AND Importer.id = $idBuyer AND
            ChangeAddress.id = (select max(id) from ChangeAddress where idImporter=$idBuyer)
	  GROUP BY Importer.name, Importer.c_Coface_Imp, Country.code, 
	    ChangeAddress.city, ChangeAddress.address, 
	    ChangeAddress.name, ChangeAddress.tel, 
	    ChangeAddress.state, Country.name, Importer.id, 
	    Importer.address, Importer.tel, Importer.city
          ORDER BY date desc";
$cur = odbc_exec($db,$query);


$q = "SELECT Inform.contrat, Inform.name, Inform.state
	FROM Inform, Importer
	WHERE Inform.id = Importer.idInform AND 
	Importer.id = $idBuyer";
$curInform = odbc_exec($db,$q);

?>
