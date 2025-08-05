<?php 
      include_once("../../consultaCoface.php");
?>
<p>&nbsp;&nbsp;&nbsp;Cliente: <?php echo  $nameCl;?>
<br>&nbsp;&nbsp;&nbsp;CI <?php echo $nomeEmp; ?>: <?php echo  $contrat;?>
<br>&nbsp;&nbsp;&nbsp;Final da Vigência: <?php echo  $endValidity;?>

<br><br>
<TABLE border="0" cellSpacing=0 cellpadding="2" width="98%" align="center">
<tr class=bgAzul>
<th align=left>Importador</th>
<th>País</th>
<th>CI <?php echo $nomeEmp; ?></th>
<th>Crédito<br>(US$ Mil)</th>
</tr>

<?php  $i = 0;
while(odbc_fetch_row($c)){
  $name = trim(odbc_result($c, 1));
  $pais = odbc_result($c, 2);
  $ciCoface = odbc_result($c, 3);
  $credit = number_format(odbc_result($c, 4) / 1000, 0);
  echo "<tr". (($i % 2) ? " class=bgCinza" : ""). ">\n";
  echo "<td align=left>$name</td><td align=center>$pais</td><td align=center>$ciCoface</td><td align=center>$credit</td>\n";
  echo "</tr>\n";
  $i++;
}
?>
</table>

<?php  if ($msg != "") { ?>
<br><p><font color="red"><?php echo  $msg;?></font></p>
<?php  } ?>

<br>
<center>
<form action="<?php echo  $root;?>role/credit/Credit.php" method=post>
<input type=hidden name=comm value=open>
<input type=hidden name=idNotification value=<?php echo  $idNotification;?>>
<input type=hidden name=done value="">
<input type=submit class=sair value=Voltar>
<input type=button value=OK class=sair onClick="this.form.comm.value='renovacao_reduzidos';this.form.done.value=1;this.form.submit()">
</form>
</center>
