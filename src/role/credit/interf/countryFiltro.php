
<?php  $searchCountry = $field -> getField ("searchCountry");

	$query ="SELECT DISTINCT Country.name, Country.code, Country.id
		FROM  Country
		WHERE Country.name LIKE '%$searchCountry%'";

	$cur = odbc_exec ($db, $query);

?>

<TABLE  width="100%" cellspacing=0 cellpadding=0>
	<TR bgcolor=#cccccc>
		<td>Nome</th>
		<td>Quantidade de Importadores</th>
	</tr>
<?php  $i = 0;	
	while (odbc_fetch_row ($cur)) {
		$nameCountry	= odbc_result ($cur, 1);
		$codeCountry	= odbc_result ($cur, 2);
		$idCountry	= odbc_result ($cur, 3);


	
		$Ccount = 0;
		$count = odbc_exec ($db, "select id from Importer where idCountry = $idCountry");
		
		while (odbc_fetch_row ($count)) {
			$Ccount ++;
		}	
		
		if ($Ccount != 0) {
			$i ++;
?>
	<tr <?php echo  ($i % 2 == 0) ?  "bgcolor = #eaeab4": "";?> align=left></font face=arial size=2>
	        <td><a href="Credit.php?comm=countryConsultInterf&id=<?php echo $idCountry;?>"><?php echo $nameCountry;?></a></td>
		<td><?php echo $Ccount;?></td>
	</tr>

<?php  }
	}
?>

</table>
