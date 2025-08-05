<TABLE  width="100%" cellspacing=0 cellpadding=0>
<TR bgcolor=#cccccc>
          <th align=left><FONT size=3>Exportador</FONT></th>
          <th align=center><FONT size=3>Ci Exportador</FONT></th>
</TR>      
		
<?php  //objetivo: listar quem tem todos os importadores prontos
	$j = 0;
	$cur = odbc_exec ($db, "SELECT DISTINCT idInform
				FROM Importer
				WHERE (state <> 18)");// quem está em analise de crédito?

	while (odbc_fetch_row ($cur)) {
		$idInform = odbc_result ($cur, 1);

		$count = odbc_exec ($db, "SELECT (idInform)
					FROM Importer
					WHERE ((state = 11) OR
					    (state = 3) OR
					    (state = 4) OR 
					    (state = 5) OR
					    (state = 6) OR
					    (state = 10) OR
					    (state = 12) OR
					    (state = 13) OR
					    (state = 7) OR
					    (state = 8) OR
					    (state = 9)) AND idInform = $idInform"); //quantos esão em analise de crédito?
		
		while (odbc_fetch_row ($count)) {
			$Ccount ++;
		}
                       
		$total = odbc_exec ($db, "SELECT (idInform)
					FROM Importer
					WHERE (state <> 18) AND idInform = $idInform"); //qual o total?
            
		while (odbc_fetch_row ($total)) {
			$Ctotal ++;
		}
		
		if ($Ctotal == $Ccount) {
			$cur = odbc_exec ($db, "SELECT name, contrat, id FROM Inform WHERE id = $idInform");

			while (odbc_fetch_row ($cur)) { // imprime que tá pronto
				$name		= odbc_result ($cur, 1);
				$contrat	= odbc_result ($cur, 2);
				$idInform 	= odbc_result ($cur, 3);
?>
				<tr align=left <?php echo  $j % 2 ? "bgcolor=#eaeab4" : "";?>> 

				<th><a href="Credit.php?comm=creditAccordShow&idInform=<?php echo $idInform;?>"><?php echo $name;?></a>
				<th><?php echo $contrat;?></th>
				</tr>
<?php  }
		
		}
	}
?>
	

