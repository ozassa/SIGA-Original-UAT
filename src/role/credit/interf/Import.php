<?php 
      include_once("../../consultaCoface.php");
?>

<BODY bgColor=#ffffcc>

  <TABLE> 


<?php  while(odbc_fetch_row($import)){
     $nameImporter      = odbc_result($import, 1); 
     $importerLimCred   = odbc_result($import, 2);
     $idcountry         = odbc_result($import, 3);
     $cimportador       = odbc_result($import, 4);
     $countryName       = odbc_result($import, 5);
     $idImporter        = odbc_result($import, 6);



     $importerLimCredPrint = number_format($importerLimCred, 2, ",", ".");

	$q = "select ChangeCredit.credit, MAX(ChangeCredit.stateDate)
	FROM ChangeCredit, Importer
	WHERE Importer.id = ChangeCredit.idImporter AND 
	    ChangeCredit.state = '13' And idImporter = $idImporter
	GROUP BY ChangeCredit.credit";
	$c =  odbc_exec($db,$q);
	
	if ($c) {
		$creditCoface = odbc_result($c, 1);
	}

	if ($creditCoface == 0) {
		$creditCofacePrint = "Esperando reposta da ".$nomeEmp;
		$div = 0;
	} else  {
		$creditCofacePrint = "US$".number_format($creditCoface, 2, ",", ".");
		$div =  ($importerLimCred/$creditCoface)*100 ;
	}
?>
  
   <TR><td>Total de crédito Solicitado : US$<?php echo $importerLimCred;?></td></tr>
   <tr><td>Total de Crédito Concedido (Coface): <?php echo $creditCofafePrint;?> </td></tr>
   <tr><td>Perc. (%) entre Créditos: <?php echo $div;?>       </td></tr>
	        
   <TR> <td>&nbsp;</td> </TR>
	</table>
         <TABLE width=96% cellspacing=0 cellpadding=3 border=0 align="center">
        <TR align=center bgcolor=#cccccc><FONT face=arial size=2 color=#000066>

        </TR>
        <TR align=center><FONT face=arial size=2 color=#000066>


        <form action="<?php echo  $root;?>role/credit/Credit.php?idNotification<?php echo $idNotification;?>" method="post"> 



	 <tr align=center <?php echo  $i % 2 ? "bgcolor=#eaeab4" : "";?>> 
    
     <td><a href="Credit.php?comm=showBuyers&idBuyer=<?php echo $id;?>"><?php echo $name;?></a></td>
     <td><?php echo $idpais;?>/<?php echo $cimportador;?></td>
     <td><?php echo $pais;?></td>
<?php  }
?>         


</font>
</form>
</tr>
</table>

</BODY>
