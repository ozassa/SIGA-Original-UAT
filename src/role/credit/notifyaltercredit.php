<?php  /*
	
Motor de busca    

*/

   //print $idInform;
 
	$idBuyer = $_REQUEST["idBuyer"];
    
	
	//echo $idBuyer;

	$query = "SELECT Inform.name, Inform.contrat, Inform.startValidity, Inform.endValidity
		  FROM Inform
		  WHERE Inform.id=$idInform";


    $notifyaltercredit = odbc_exec($db, $query);



    $q = "SELECT Importer.name, Country.code, Importer.c_Coface_Imp, Importer.limCredit, ch.credit, ch.creditSolic, Importer.id
	     FROM Importer
               JOIN Inform ON (Importer.idInform = Inform.id)
               JOIN Country ON (Importer.idCountry = Country.id)
               LEFT JOIN ChangeCredit ch ON (ch.idImporter = Importer.id)
	     WHERE Importer.state=2 AND Inform.id = $idInform AND ch.creditSolic > 0
             ORDER BY Importer.name, ch.id DESC
            ";
       //echo "<pre>$q</pre>\n";

    $notifyalterImporter = odbc_exec($db,$q);
	
	// HICom: Vamos ver se tem tem inform de renovação
	
	$ren_idInform = 0;
	
	$query = "SELECT Inform.name, Inform.contrat, Inform.startValidity, Inform.endValidity, Inform.id, Inform.state 
		  FROM Inform
		  WHERE Inform.idAnt=$idInform";

    $ren_notifyaltercredit = odbc_exec($db, $query);
    if (odbc_fetch_row($ren_notifyaltercredit)) {
	
	   $ren_idInform = odbc_result($ren_notifyaltercredit, 5);
	   
	   $ren_status   = odbc_result($ren_notifyaltercredit, 6); 

	   $query = "SELECT Inform.name, Inform.contrat, Inform.startValidity, Inform.endValidity, Inform.id 
		         FROM Inform
		         WHERE Inform.idAnt=$idInform";

       $ren_notifyaltercredit = odbc_exec($db, $query);
	   
	   
	   
       $q = "SELECT Importer.name, Country.code, Importer.c_Coface_Imp, Importer.limCredit, ch.credit, ch.creditSolic, Importer.id
	         FROM Importer
               JOIN Inform ON (Importer.idInform = Inform.id)
               JOIN Country ON (Importer.idCountry = Country.id)
               LEFT JOIN ChangeCredit ch ON (ch.idImporter = Importer.id)
	         WHERE Importer.state=2 AND Inform.id = $ren_idInform AND ch.creditSolic > 0
             ORDER BY Importer.name, ch.id DESC
            ";
           //echo "<pre>$q</pre>\n";
        
        $ren_notifyalterImporter = odbc_exec($db,$q);
    }

 // echo "<pre>$q</pre>\n";

?>
