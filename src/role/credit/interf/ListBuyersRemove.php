<body>
   <TABLE width=100% cellspacing=0 cellpadding=2 border=0 align="center">
<tr>
  <td class="textoBold">Exportador:</td>
  <td class="texto"><?php echo  $nameExpo;?></td>
</tr>
<tr>
  <td class="textoBold">Ci Exportador :</td>
  <td class="texto"><?php echo $ciExpo;?></td>
</tr>
</table>
      <TABLE width=100% cellspacing=0 cellpadding=2 border=0 align="center">
        <TR class="bgAzul">
          <th>Razão Social</th>
          <th>País</th>
          <th>Cod. País</th>
          <th align=center>Limite de Crédito<br>US$ Mil</th>
          <th>Ci Importador</th>
        </TR>
        <TR align=left>
        <form action="<?php echo  $root;?>role/credit/Credit.php" method="post">
        <input type=hidden name=idNotification value=<?php echo  $idNotification;?>>
<?php  $i = 1;
  if(odbc_fetch_row($cur)) {
     $idBuyer      = odbc_result($cur, 1); //chave de busca para linkar o informe
     $nameBuyer    = odbc_result($cur, 2);
     $nameCountry  = odbc_result($cur, 3);
     $idCountry    = odbc_result($cur, 4);
     $limiteCredit = odbc_result($cur, 5) / 1000;
     $cofaceBuyer  = odbc_result($cur, 6);
     $i++;

     $limiteCredit = number_format($limiteCredit, 0, ",", ".");
?>
        <input type=hidden name=idBuyer value=<?php echo  $idBuyer;?>>
     <tr align=center <?php echo $i % 2 == 0 ? "bgcolor=#e9e9e9" : "";?>>
<!--
	     <td><a href=Credit.php?comm=showBuyers&idBuyer=<?php echo $idBuyer;?>><?php echo $nameBuyer;?></a></td>
-->
	     <td class="texto"><?php echo $nameBuyer;?></td>
	     <td class="texto"><?php echo $nameCountry;?></td>
	     <td align=center class="texto"><?php echo $idCountry;?></td>
	     <td align=center class="texto"><?php echo $limiteCredit;?></td>
	     <td align=center class="texto"><?php echo $cofaceBuyer;?></td>
     </tr>
<?php  } // while

  if ($i == 0) {
?>
   <TR bgcolor=#a4a4a4>
	<TD align=left><FONT size=3>Nenhum encontrado.</FONT>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
	</TD>
   </tr>
<?php  } // fecha o if ($i == 0)
?>
	<br>
<!--
      <tr>
	<td colspan=5 align=center>
	<input type=checkbox name=cobrar checked>Cobrar análise
	</td>
      </tr>
-->
      <tr>
	<td colspan="5" align="center">
                <input type=hidden name=comm value=RemoveNotif>
		<input type=button class="servicos" onClick="this.form.comm.value='open';this.form.submit()" value="Voltar">
		<input type=hidden name=idInform value="<?php echo $idInform;?>">
                <input type=hidden name=i value=<?php echo $i;?>>
		<input type=submit  class="servicos" value="OK">
	</form>
        </td>
     </tr>

    </TABLE>

</body>

