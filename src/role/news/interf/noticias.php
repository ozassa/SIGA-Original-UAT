<?php 

$cur=odbc_exec(
$db,
"SELECT id, titulo, data FROM Noticias"
);

?>


<TABLE border="0" cellSpacing="0" cellpadding="2" width="100%" align="center">
  <TR>
    <TD colspan="3" >&nbsp;</TD>
  </TR>
  <TR>
    <TD colspan="3" align="center"><a href="../news/News.php?comm=incluir">Criar Nova Notícia</TD>
  </TR>
  <TR>
    <TD colspan="3">&nbsp;</TD>
  </TR>
  <TR bgcolor="#cccccc">
    <TD align="left" class="bgAzul" width="70%">Notícia</TD>
    <TD align="center" class="bgAzul" width="20%">Data</TD>
    <TD class="bgAzul" width="10%">&nbsp;</TD>
  </TR>

<?php $i = 0;
  while (odbc_fetch_row($cur)) {
    $i ++;
?>
<form action="../news/News.php" method="post">
<input type=hidden name="comm" value="action">
<input type=hidden name="id" value="<?php echo  odbc_result($cur,1); ?>">

  <tr <?php echo  ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "")?>>
    <TD class="texto"><?php echo  odbc_result($cur,2); ?>&nbsp;</TD>
    <TD align="center" class="texto"><?php echo  odbc_result($cur,3); ?>&nbsp;</TD>
    <TD width="10%"><input class="servicos" type="submit" value="Alterar"></TD>
 </TR>

</form>				     
<?php }
  if ($i == 0) {
?>
  <TR bgcolor="#a4a4a4">
    <TD align="center" colspan="3">Nenhuma Notícia Cadastrada</TD>
  </TR>
<?php }
?>
</TABLE>

