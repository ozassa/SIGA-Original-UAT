<?php 
      include_once("../../consultaCoface.php");
?>
<p>&nbsp;&nbsp;&nbsp;Cliente: <?php   echo $name;?>
<br>&nbsp;&nbsp;&nbsp;CI <?php echo $nomeEmp; ?>: <?php   echo $ci;?>

<hr noshade>

<br>
<TABLE border="0" cellSpacing=0 cellpadding="2" width="98%" align="center">
<tr class=bgAzul>
<th align=left>Importador</th>
<th>CI <?php echo $nomeEmp; ?></th>
<th>País</th>
<th>Data do<br>Crédito</th>
</tr>

<?php  $i = 0;
while(odbc_fetch_row($x)){
  $idBuyer = odbc_result($x, 1);
  $nameBuyer = trim(odbc_result($x, 2));
  $ciCoface = odbc_result($x, 3);
  $pais = odbc_result($x, 4);
  $y = odbc_exec($db,
		 "select creditDate from ChangeCredit where idImporter=$idBuyer and credit is not null ".
		 "and id in (select max(id) from ChangeCredit where idImporter=$idBuyer and credit is not null) ".
		 "and creditDate <= getdate() - 150"); # credito concedido ha mais de 5 meses
  if(odbc_fetch_row($y)){
    $date = ymd2dmy(odbc_result($y, 1));
    echo "<tr". (($i % 2) ? " class=bgCinza" : ""). ">\n";
    echo "<td align=left>$nameBuyer</td><td align=center>$ciCoface</td>".
      "<td align=center>$pais</td><td align=center>$date</td></tr>\n";
    $i++;
  }
}
?>

</table>

<br>
<center>
<form action="<?php   echo $root;?>role/executive/Executive.php" method=post>
<input type=hidden name=comm value="notif">
<input type=hidden name=idInform value=<?php   echo $idInform;?>>
<input type=hidden name=idNotification value=<?php   echo $idNotification;?>>
<input type=hidden name=done value="">
<input type=submit class=sair value=Voltar>
<input type=button value=OK class=sair onClick="this.form.comm.value='cinco_meses';this.form.done.value=1;this.form.submit()">
</form>
</center>
