<?php 
      include_once("../../consultaCoface.php");
?>
<p>&nbsp;&nbsp;&nbsp;Cliente: <?php echo  $nameCl;?>
<br><br>
<TABLE border="0" cellSpacing=1 cellpadding="2" width="98%" align="center">
<tr bgcolor="#cccccc">
<th>Importador</th>
<th>CI <?php echo $nomeEmp; ?></th>
</tr>

<?php  $i = 0;
while(odbc_fetch_row($c)){
  $name = odbc_result($c, 1);
  $ciCoface = odbc_result($c, 2);
  echo "<tr". (($i % 2) ? " bgcolor=#eaeab4" : ""). ">\n";
  echo "<td align=left>$name</td><td align=center>$ciCoface</td>\n";
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
<input type=submit value=Voltar>
<input type=button value=OK onClick="this.form.comm.value='excluidos';this.form.done.value=1;this.form.submit()">
</form>
</center>
