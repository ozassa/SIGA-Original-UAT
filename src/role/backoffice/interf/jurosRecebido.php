<?php
$v = odbc_exec($db, "SELECT name FROM Inform Where id = $idInform");
$name = odbc_result($v, 1);
?>

<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD align="center">&nbsp;</TD>
  </TR>
  <TR>
    <TD>
      Exportador: <?php echo $name;?>
    </TD>
  </TR>
  <TR>
    <TD align="center">&nbsp;</TD>
  </TR>
</TABLE>


<form action="<?php echo $root;?>role/backoffice/Backoffice.php"  method="get">
<input type=hidden name=idInform value="<?php echo $idInform;?>">
<input type=hidden name=idNotification value="<?php echo $idNotification;?>">
<input type=hidden name="comm" value="confirmaSolicit">
<P align="center"><input class="sair" type="button" value="Voltar" onClick="this.form.comm.value='back';this.form.submit()"> <input type="submit" value="Concluir" class="sair">
</form>
