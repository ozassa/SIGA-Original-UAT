<?php  $idCountry = $field -> getField("idCountry");


	$query ="SELECT DISTINCT Country.name, Country.code 
		FROM  Country, Inform
		WHERE Country.id = $idCountry"; 
			

	$Pais = odbc_exec($db,$query);

		while (odbc_fetch_row($Pais)) { 
			$nameCountry   = odbc_result ($Pais, 1);
?>


<BODY bgColor=#ffffcc>

 <TABLE width="100%" cellspacing=0 cellpadding=0 border=0><FONT face=arial color=#000066 size=2><b>

  	<tr>
    	<th><br><th>
  	</tr>
  	<tr><FONT face=arial color=#000066>
    	<td><?php echo $nameCountry;?></td>
	</tr>
	<tr>
    	<th><br><th>
  	</tr>
	<tr>
	<td>Exposição Máxima: US$  ???   </td>    
	</tr>
	<tr>
    	<th><br><th>
  	</tr>
	<tr>
    <td> Ci Exportador = <?php echo $idInform;?>&nbsp;&nbsp; US$<?php echo number_format ($creditSum, 2, ",", ".");?></font></td>
  </tr>

  <tr>
    <th><br><th>
  </tr>
  <TR align=center bgcolor=#cccccc><FONT face=arial size=2 color=#000066>
     <td>Importador</td>
     <td>Ci Importador</td>
     <td>Crédito Solicitado US$</td>
     <td>Crédito Concedido US$</td>
     <TD> Alterar para </td></font>
  </TR>

?> 

<?php  /*

		$b = odbc_exec ($db, "SELECT Importer.id, Importer.name, Importer.limCredit, 
					    Importer.c_Coface_Imp
					FROM Importer
					WHERE Importer.idInform = '$idInform'
					AND Importer.state <> '15'
					AND Importer.idCountry = '$idCountry'");
		$i = 0;


		while (odbc_fetch_row($b)) { // escopo importador
			$idBuyer   = odbc_result ($b, 1);
			$nameBuyer = odbc_result ($b, 2);	
			$ciBuyer   = odbc_result ($b, 4);
			$limCredit = odbc_result ($b, 3);

			$limCredit = number_format ($limCredit, 2, ",", ".");			
	
			$i++;
	
			$c = odbc_exec ($db, "SELECT ChangeCredit.credit
						FROM ChangeCredit, Importer
						WHERE ChangeCredit.idImporter = $idBuyer
						AND ChangeCredit.state <> 14
						AND Importer.id = ChangeCredit.idImporter
						AND Importer.state <> 15
						ORDER BY ChangeCredit.stateDate DESC");

			if (odbc_fetch_row ($c)) { // escopo importador
				$credit = odbc_result ($c, 1);
			} else {
				$credit = 0;
			}

			if ($credit != 0){
				$credit  = number_format ($credit, 2, ",", ".");	
			} else {
				$credit = "Aguardando resposta da COFACE.";
			}
*/
?>			

<?php  //		}
	

?>

	<tr <?php echo  $i % 2 == 0 ?  "bgcolor=#eaeab4" : " ";?> align=center><font face=arial size=2 color=#000066>
    	   <td><a href="Credit.php?comm=showBuyers&idBuyer=<?php echo $idBuyer;?>"><?php echo $nameBuyer;?></a></td>
           <td><?php echo $idBuyer;?></td>
           <td><?php echo $limCredit;?></td>
           <td><?php echo $ciBuyer;?></td>
	   <td><input type=text name=text size=10> </td>
	</font></TR>


 
</b>
</table>
</body>
