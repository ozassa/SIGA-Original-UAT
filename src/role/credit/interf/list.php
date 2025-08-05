<?php  $i = 0;
  while(odbc_fetch_row($cur)) 
  {      

     $nameBuyer         = odbc_result($cur, 1);
     $addressBuyer      = odbc_result($cur, 2);
     $riskBuyer         = odbc_result($cur, 3);    
     $cityBuyer         = odbc_result($cur, 4);
     $countryBuyer      = odbc_result($cur, 5);
     $telBuyer          = odbc_result($cur, 6);
     $limCreditBuyer    = odbc_result($cur, 7);
     $numShip12Buyer    = odbc_result($cur, 8);
     $periodicityBuyer  = odbc_result($cur, 9);
     $przPagBuyer       = odbc_result($cur, 10);
     $idCountryBuyer    = odbc_result($cur, 11); 

     $limCreditBuyer    = number_format($limCreditBuyer, 2, ",", ".");

     $i++;

     print("<tr ");
      ($i % 2 == 0) ?  print("bgcolor = #eaeab4"): print(" "); 
     print(" align=left></font face=arial size=2>");   


     if ($riskBuyer == 1)
        $riskBuyer = "RC - Risco Comercial";
     else if($riskBuyer == 2)
        $riskBuyer = "RP - Risco Político";
     else 
        $riskBuyer = "RP & RC - Risco Político & Risco Comercial";

     
     print ("<td width=15%><font color=#4169e1>Razão:</font></td><td><font color=#000066><b>$nameBuyer</b></font></td>");
     print ("</font></tr>");
     print ("<td width=15%><font color=#4169e1>Endereço:</font></td><td><font color=#000066><b>$addressBuyer</b></font></td>");
     print ("</font></tr>");
     print ("<td width=15%><font color=#4169e1>Risco:</font></td><td><font color=#000066><b>$riskBuyer</b></font></td>");
     print ("</font></tr>");
     print ("<td width=15%><font color=#4169e1>Cidade:</font></td><td><font color=#000066><b>$cityBuyer</b></font></td>");     
     print ("</font></tr>");
     print ("<td width=15%><font color=#4169e1>País:</font></td><td><font color=#000066><b>$countryBuyer</b></font></td>");
     print ("</font></tr>");
     print ("<td width=15%><font color=#4169e1>Tel/Fax:</font></td><td><font color=#000066><b>$telBuyer</b></font></td>");
     print ("</font></tr>");
     print ("<td width=15%><font color=#4169e1>Crédito US$:</font></td><td><font color=#000066><b>$limCreditBuyer</b></font></td>");
     print ("</font></tr>");
     print ("<td width=15%><font color=#4169e1>N.º Emb./Ano:</font></td><td><font color=#000066><b>$numShip12Buyer</b></font></td>");
     print ("</font></tr>");
     print ("<td width=15%><font color=#4169e1>Per/Emb(dias):</font></td><td><font color=#000066><b>$periodicityBuyer</b></font></td>");
     print ("</font></tr>");
     print ("<td width=15%><font color=#4169e1>Prz./Pag.(dias):</font></td><td><font color=#000066><b>$przPagBuyer</b></font></td>");
     print ("</font></tr>");

   }

?>
<?php  if ($i == 0) {
        print(" <TR bgcolor=#eaeab4>
                <TD align=center><FONT size=3>Nenhum encontrado.</FONT>
                <td>&nbsp;</td>
        	<td>&nbsp;</td>
		</TD>
                </tr>");
      }
?>  