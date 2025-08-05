<?php  $i = 0;
	$idCountry = $field -> getField("id");

	$query = "SELECT DISTINCT Country.name
		  FROM  Country  
		  WHERE Country.id = $idCountry "; 
			

	$country = odbc_exec ($db,$query);


	if (odbc_fetch_row ($country)) { 
		$nameCountry	= odbc_result ($country, 1);
	} else {
		$msg = "erro não determinado";
	}

	$sum = odbc_exec ($db, "SELECT SUM(limCredit)
				FROM Country, Importer
				WHERE Country.id = $idCountry AND Importer.idCountry = Country.id");

	if (odbc_fetch_row ($sum)) {	
		$Csum = odbc_result ($sum, 1);
	}
	$Csum = number_format($Csum,  2, ",", ".");

//cabeca

?>

<BODY bgColor=#ffffcc>

 <TABLE width="100%" cellspacing=0 cellpadding=0 border=0><FONT face=arial color=#000066 size=2><b>

  	<tr>
    		<th><br><th>
  	</tr>
  	<tr>
	    	<td><?php echo $nameCountry;?></td>
	</tr>
	<tr>
    		<th><br><th>
  	</tr>
	<tr>
		<td>Exposição Máxima: US$ <?php echo $Csum;?>    </td>    
	</tr>
	<tr>
    		<th><br><th>
  	</tr>
<?php  $curInform = odbc_exec ($db, "SELECT Inform.id, Inform.name, Inform.contrat, Importer.id
					FROM Country INNER JOIN
						Importer ON Country.id = Importer.idCountry INNER JOIN
						Inform ON Importer.idInform = Inform.id
					WHERE (Country.id = $idCountry)
					GROUP BY Inform.id, Inform.name, Inform.contrat");

			      
	while (odbc_fetch_row ($curInform)) {
		$idInform 	 = odbc_result ($curInform, 1);
		$nameInform	 = odbc_result ($curInform, 2);
		$contratInform   = odbc_result ($curInform, 3);
		$idBuyer	 = odbc_result ($curInform, 4);

?>
	<tr>
		<td>Exportador: <?php echo $nameInform;?> </td>
		<td>Ci Exportador:<?php echo $contratInform;?> </td>
	</tr>
	<TR align=center bgcolor=#cccccc><FONT face=arial size=2 color=#000066>
	     <td>Importador</td>
	     <td>Ci Importador</td>
	     <td>Crédito Solicitado US$</td>
	     <td>Crédito Concedido US$</td>
	</TR>
<?php  $curID = odbc_exec($db, "SELECT Importer.id
					FROM Importer, Country, ChangeCredit
					WHERE Importer.idInform = 1893 AND 
					    Importer.id = ChangeCredit.idImporter AND 
					    Importer.idCountry = Country.id AND Country.id = 8
					ORDER BY Importer.id");

			

			$curImporter = odbc_exec ($db, "SELECT Inform.id, Inform.contrat, Importer.name, 
							    Importer.c_Coface_Imp, Importer.limCredit, 
							    ChangeCredit.credit
							FROM Country INNER JOIN
							    Importer ON Country.id = Importer.idCountry INNER JOIN
							    Inform ON Importer.idInform = Inform.id INNER JOIN
							    ChangeCredit ON 
							    Importer.id = ChangeCredit.idImporter
							WHERE (Country.id = $idCountry) AND Inform.id = $idInform
							GROUP BY Inform.id, Inform.contrat, Importer.name, 
							    Importer.c_Coface_Imp, Importer.limCredit, ChangeCredit.credit");

print "SELECT Inform.id, Inform.contrat, Importer.name, 
						    Importer.c_Coface_Imp, Importer.limCredit, 
						    ChangeCredit.credit
						FROM Country INNER JOIN
						    Importer ON Country.id = Importer.idCountry INNER JOIN
						    Inform ON Importer.idInform = Inform.id INNER JOIN
						    ChangeCredit ON 
						    Importer.id = ChangeCredit.idImporter
						WHERE (Country.id = $idCountry) AND Inform.id = $idInform
						GROUP BY Inform.id, Inform.contrat, Importer.name, 
						    Importer.c_Coface_Imp, Importer.limCredit, ChangeCredit.credit";

		while (odbc_fetch_row ($curImporter)) {
			$nameImporter     = odbc_result ($curImporter, 3);
			$cofaceImporter   = odbc_result ($curImporter, 4);
			$importlimCredit  = odbc_result ($curImporter, 5);
			$creditChange	  = odbc_result ($curImporter, 6);		

		
			$importlimCredit  = number_format($importerlimCredit,  2, ",", ".");
			$creditChange	  = number_format($creditChange,  2, ",", "."); 
			$i ++;

?>
	<tr <?php echo($i % 2 == 0) ?  "bgcolor=#eaeab4": "";?> align=left></font face=arial size=2>
		<td><?php echo $nameImporter;?></td>
		<td><?php echo $cofaceImporter;?></td>
		<td><?php echo $importlimCredit;?></td>
		<td><?php echo $creditChange;?></td>
	<tr>   

	
<?php  }
	}
?>

</table>
<h1><?php  $msg;?></h1>
