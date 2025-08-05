<?php 
      include_once("../../consultaCoface.php");
?>
<BODY bgColor=#ffffcc>

	<table  width="100%" cellspacing=0 cellpadding=5> 
        <TR>
          <FONT size=3>Alteração de Dados</FONT>
        </TR>

      <TR class=bgAzul>
      <th align=middle><FONT color=#000066 size=2>Exportador</FONT></th>
      <th align=middle><FONT color=#000066 size=2>Nome <br>(antigo)</FONT></th>
      <th align=middle><FONT color=#000066 size=2>Endereço <br>(antigo)</FONT></th>
      <th align=middle><FONT color=#000066 size=2>Ci <?php echo $nomeEmp; ?> <br>(antigo)</FONT></th>
      <th align=middle><FONT color=#000066 size=2>Nome <br>(novo)</FONT></th>
      <th align=middle><FONT color=#000066 size=2>Endereço <br>(novo)</FONT></th>
      <th align=middle><FONT color=#000066 size=2>Ci <?php echo $nomeEmp; ?><br>(novo)</FONT></th>
      <th align=middle><FONT color=#000066 size=2>Situação SBCE</FONT></th>
      <th align=middle><FONT color=#000066 size=2>Situação <?php echo $nomeEmp; ?></FONT></th>
      </TR>


<?php  $i = 0;
	while(odbc_fetch_row($curData)){       
 	     $idBuyer            = odbc_result($curData, 1); //chave de busca para linkar o informe	
	     $nameInform         = odbc_result($curData, 2);
	     $nameBuyer          = odbc_result($curData, 3); 
	     $importerAddress    = odbc_result($curData, 4); 
	     $importerCity       = odbc_result($curData, 5); 
             $importerCoface     = odbc_result($curData, 6); 
	     $importerLimCredit  = odbc_result($curData, 7); 
	     $changeName         = odbc_result($curData, 8); 
	     $changeAddress      = odbc_result($curData, 9); 
             $changeCity         = odbc_result($curData, 10); 
	     $changeCoface       = odbc_result($curData, 11); 
	     $importerState      = odbc_result($curData, 12); //determina se muda dados ou credito.
	     $importerCountry    = odbc_result($curData, 13); 

	     $i++;

     $importerLimCredit = number_format($importerLimCredit, 2, ",", ".");


     print "<tr align=center";
     if($i % 2 == 0) print "bgcolor=#eaeab4"; else print" >"; 

         print "<th align=middle><FONT color=#000066 size=2> $nameInform </FONT></th>".
         " <th align=middle><FONT color=#000066 size=2>".
              "<a href=Credit.php?comm=showBuyers&idBuyer=$idBuyer>$nameBuyer</a></FONT></th>".
          "<th align=middle><FONT color=#000066 size=2>$importerAddress $importerCity</FONT></th>".
          "<th align=middle><FONT color=#000066 size=2>$importerCountry $importerCoface</FONT></th>".
          "<th align=middle><FONT color=#000066 size=2>$changeName</FONT></th>".
          "<th align=middle><FONT color=#000066 size=2>$changeAddress $changeCity</FONT></th>".
          "<th align=middle><FONT color=#000066 size=2>$changeCoface</FONT></th>".
          "<th align=middle><FONT color=#000066 size=2>?</FONT></th>".
          "<th align=middle><FONT color=#000066 size=2>?</FONT></th>".
        "</TR>";


  }
?>
	<tr><br></tr>
	</table>
	<table   width="100%" cellspacing=0> 
	<TR>
          <FONT size=3 ><br>Alteração de Limite de Crédito</FONT>
        </TR>



	<TR bgcolor=#cccccc>
        <th align=middle><FONT color=#000066 size=2>Exportador</FONT></th>
        <th align=middle><FONT color=#000066 size=2>Importador</FONT></th>
        <th align=middle><FONT color=#000066 size=2>Data</FONT></th>
        <th align=middle><FONT color=#000066 size=2>Ci <?php echo $nomeEmp; ?></FONT></th>
        <th align=middle><FONT color=#000066 size=2>Créd.<br>Solicitado</FONT></th>
        <th align=middle><FONT color=#000066 size=2>Créd.<br>Anterior</FONT></th>
        <th align=middle><FONT color=#000066 size=2>Créd.<br>Atual</FONT></th>
        <th align=middle><FONT color=#000066 size=2>Situação SBCE</FONT></th>
        <th align=middle><FONT color=#000066 size=2>Situação <?php echo $nomeEmp; ?></FONT></th>
        </TR>




<?php  $i = 0;
	while(odbc_fetch_row($curCredit)){       
 	     $idBuyerC            = odbc_result($curCredit, 1); //chave de busca para linkar o informe
	     $nameInformC         = odbc_result($curCredit, 2);
	     $nameBuyerC          = odbc_result($curCredit, 3); 
	     $importerLimCreditC  = odbc_result($curCredit, 4); 
	     $importerStateC      = odbc_result($curCredit, 5); //determina se muda dados ou credito.
             $importerContryC     = odbc_result($curCredit, 6); 
             $importerCofaceC     = odbc_result($curCredit, 7); 
	     $importerStateDateC  = odbc_result($curCredit, 8); 

	     $i++;
$importerStateDateC  = substr($importerStateDateC, 8, 2)."/".substr($importerStateDateC, 5, 2)."/".substr($importerStateDateC, 0, 4);
	     $importerLimCreditC  = number_format($importerLimCreditC, 2, ",", ".");

?>
     <tr align=center <?php echo  $i % 2 ? "bgcolor=#eaeab4" : "";?>> 
         <th align=middle><FONT color=#000066 size=2><?php echo $nameInformC;?></FONT></th>
          <th align=middle><FONT color=#000066 size=2>
            <a href=Credit.php?comm=showBuyers&idBuyer=<?php echo $idBuyerC;?>><?php echo $nameBuyerC;?></a></FONT></th>

          <th align=middle><FONT color=#000066 size=2><?php echo $importerStateDateC;?></FONT></th>
          <th align=middle><FONT color=#000066 size=2><?php echo $importerCountryC;?> <?php echo $importerCofaceC;?></FONT></th>
          <th align=middle><FONT color=#000066 size=2><?php echo $importerLimCreditC;?></FONT></th>


      
          <th align=middle><FONT color=#000066 size=2>?</FONT></th>
          <th align=middle><FONT color=#000066 size=2>?</FONT></th>
          <th align=middle><FONT color=#000066 size=2>?</FONT></th>
          <th align=middle><FONT color=#000066 size=2>?</FONT></th>
        </TR>
<?php  }

?>


        <TR>
         
         </TR>
</FONT></FONT>    






