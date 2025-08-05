<body>
      <TABLE width=100% cellspacing=0 cellpadding=5 border=0>
        <TR>
          <td>RAFAND COMÉRCIO E INDÚSTRIA S/A. - Ci Exportador=82321</td>
        </TR>
        <TR align=center bgcolor=#cccccc><FONT face=arial size=2 color=#000066>

          <td>Razão Social</td>
          <td>País</td>
          <td>Cod. País</td>
          <td>Limite de Crédito</td>
          <td>&nbsp;</td>
          </font>

        </TR>
        <TR align=center><FONT face=arial size=2 color=#000066>


        <form action="<?php echo  $root;?>role/credit/Credit.php?idNotification<?php echo $idNotification;?>" method="post"> 


<?php  $i = 0;
  while(odbc_fetch_row($cur)) 
  {      
     $idBuyer      = odbc_result($cur, 1); //chave de busca para linkar o informe
     $nameBuyer    = odbc_result($cur, 2);
     $nameCountry  = odbc_result($cur, 3); 
     $idCountry    = odbc_result($cur, 4); 
     $limiteCredit = odbc_result($cur, 5); 
     $i++;

     $limiteCredit = "US$".number_format($limiteCredit, 2, ",", ".");

     print("<tr align=center ");
     if ($i % 2 == 0)  print("bgcolor = #eaeab4 >");
     else print(" > "); 

     print "<td><a href=Credit.php?comm=showBuyers&idBuyer=$idBuyer&idNotification=$idNotification>$nameBuyer</a></td>".
           "<td>$nameCountry</td>".
           "<td>$idCountry</td>".
           "<td>$limiteCredit</td>".
           "<td><input type=hidden name=idImporter$i value=$idBuyer>".
           "<input type=hidden name=idNotification value=$idNotification></td>\n";
   }

?>
<?php  if ($i == 0) {
        print(" <TR bgcolor=#eaeab4>
                <TD align=center><FONT size=2>Nenhum encontrado.</FONT>
                <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
		</TD>
                </tr>");
      }
?>  

        </tr>
	<br>     
        <tr>
	<td colspan="5" align="center"> 

               	<input type=submit name=back value="Voltar"> 
		<input type=submit name=submit value="Demanda OK">
                <input type=hidden name=comm value=c_Coface_Imp> 
                <input type=hidden name=i value=<?php echo $i;?>> 

<?php  /*
  $cont = 1;
  $continue = 0;

  if($submit){
    while($cont <= $i){
      $name = "$".$cont;
      if($name) 
         $continue = 1;
      else
         $continue = 0;
      $cont++; 
    }
    if($continue == 1) 
}
*/  


?>

	</form>
        </td></tr>	

      </TABLE>
	
</body>

     














