<?php   $var=odbc_exec($db, "SELECT name FROM Inform Where id = $idInform");
    $name = odbc_result($var, 1);
?>
<script language="JavaScript" src="<?php   echo $root;?>scripts/calendario.js"></script>
<form action="<?php   echo $root; ?>role/financ/Financ.php"  method="post" name="juros">
<input type=hidden name=usuario value="<?php   echo $userID; ?>">
<input type=hidden name=idInform value="<?php   echo $idInform;?>">
<input type=hidden name=idJuros value="<?php   echo $idJuros;?>">
<input type=hidden name=name value="<?php   echo $name;?>">
<input type=hidden name=idNotification value="<?php   echo $idNotification;?>">
<input type=hidden name="comm" value="confirmaSolicit">

<TABLE border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <TD align="center">&nbsp;</TD>
  </TR>
  <TR>
    <TD>
      <?php   echo $name; ?>
    </TD>
  </TR>
  <TR>
    <TD>Data de Recebimento: <input class="caixa" type="text" size="12" name="date">&nbsp;<A HREF="javascript:showCalendar(document.juros.date)"><img src="<?php   echo $root; ?>images/calendario.gif" width=24 height=20 border=0 alt=calendário></A></TD>
  </TR>
  <TR>
    <TD align="center">&nbsp;</TD>
  </TR>
</TABLE>


<P align="center"><input class="sair" type="button" value="Voltar" onClick="this.form.comm.value='voltar';this.form.submit()"> <input type="submit" value="Concluir" class="sair">
</form>
