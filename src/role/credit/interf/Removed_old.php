<?php  // ALTERADO HICOM EM 28/04/2004
//Alterado HiCom mes 04
?>

<p>Cliente: <?php echo  $name;?>
<br>
<form action="<?php echo  $root;?>role/credit/Credit.php" method="post" name="coface_imp">
<input type=hidden name=comm value="removed">
<input type=hidden name=idNotification value="<?php echo  $idNotification;?>">
<input type=hidden name=idInform value="<?php echo  $idInform;?>">
<input type=hidden name=file value="<?php echo  $file;?>">
<input type=hidden name=remove value=1>

<TABLE width=100% cellspacing=0 cellpadding=2 border=0 align="center">
  <TR align=center class="bgAzul">
   <!-- ---alterado hicom em 28/04/2004------- -->
    <th align="left" class="bgAzul">Excluir</th>
   <!-- -------------------------------------- --> 
	<th class="bgAzul">Razão Social</th>
    <th class="bgAzul">Ci Importador</th>
    <th class="bgAzul">País</th>
 </TR>
<?php  $i = 1;
while(odbc_fetch_row($x)){
  $id = odbc_result($x, 1);
  $name = odbc_result($x, 2);
  $ci = odbc_result($x, 3);
  $pais = odbc_result($x, 4);
  echo "<input type=hidden name=idBuyer$i value=$id>\n";
  echo "<tr align=center ".
    ($i % 2 == 0 ? "class=bgCinza" : ""). ">\n";
 //alterado hicom em 28/04/2004-----------------------------------
  echo "<td align=left><input type=checkbox checked name=chkrem$i></td><td align=center>$name</td><td>$ci</td><td>$pais</td></tr>\n";
 //--------------------------------------------------------------- 
  $i++;
}
?>
</table>

<br>
<center>
<input type=button class=sair value=Voltar onClick="this.form.comm.value='notif'; this.form.submit()">
<input type=submit class=sair value=OK>
</center>
</form>
