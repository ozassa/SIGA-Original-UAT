<?php 
  $i = 0;
  while(odbc_fetch_row($cur)) 
   {      
     $inform     = odbc_result($cur, 1); //chave de busca para linkar o informe
     $clientR    = odbc_result($cur, 2);
     $executiveR = odbc_result($cur, 3); 
     $stateR     = odbc_result($cur, 4); 
     $i++;
     echo "<tr ";
           ($i % 2 == 0) ? print("bgcolor=#e9e9e9"): print(""); 
     echo  " ><td class=texto><a href=../searchClient/ListClient.php?comm=view&idInform=$inform>$clientR</a></td>";

     switch ($stateR) {
      case 1:
        $stateR = "Novo";
        print  "<td  align=center>X</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>";
        break;
      case 2:
        $stateR = "Preenchido";
        print  "<td>&nbsp;</td>
		<td align=center>X</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>";
        break;
      case 3:
        $stateR = "Validado";
        print  "<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=center>X</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>";
        break;
      case 4:
        $stateR = "Analisado";
        print  "<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=center>X</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>";
        break;
      case 5:
        $stateR = "Tarifado";
        print  "<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=center>X</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>";
        break;
      case 6:
        $stateR = "Proposta";
        print  "<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=center>X</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>";
        break;
      case 7:
        $stateR = "Confirmado";
        print  "<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=center>X</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>";
        break;
      case 8:
        $stateR = "Alterado";
        print  "<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=center>X</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>";
        break;
      case 9:
        $stateR = "Cancelado";
        print  "<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=center>X</td>
		<td>&nbsp;</td>
		</tr>";
        break;
      case 10:
        $stateR = "Apólice";
        print  "<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=center>X</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>";
        break;
      case 11:
        $stateR = "Encerrado";
        print  "<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=center>X</td>
		</tr>";
        break;
     }
   }
        if ($i == 0) {
        print(" <TR bgcolor=#cccccc>
                <TD align=center><FONT size=2>Nenhum encontrado.</FONT>
                <td>&nbsp;</td>
        	<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
                </TD>
                </tr>");
      }
   

?> 
