<TABLE border="0" cellSpacing=0 cellpadding="5" width="98%" align="center">
  <TR>
    <TD class="bgCinza" align="center">Cessão de Direito - Outros Bancos</TD>
  </TR>
   <TR>
    <TD>&nbsp;</TD>
  </TR>
  <TR class="bgAzul">
    <TD align="center" class="bgAzul">Selecionar Banco</TD>
  </TR>
   <TR>
    <TD>&nbsp;</TD>
  </TR>
   <TR>
    <TD> 
<form action="<?php echo $root;?>role/cessao/Cessao.php" method="post">
<input type=hidden name="comm"     value="selImpOB">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">
<p align=center>Banco:
<select name="banco">
<?php  $cur = odbc_exec (
		    $db,
		    "SELECT name, codigo FROM Banco order by name"
		    );
  $count = 0;
  while (odbc_fetch_row($cur)) {
    $name=odbc_result($cur,1);
    $cod=odbc_result($cur,2);
?>
<option value=<?php echo $cod ?>><?php echo $name;?></option>
<?php  $count ++;
  }
?>
</select>
      </TD>
   </TR>
   <TR>
    <TD>&nbsp;</TD>
  </TR>
  <TR class="bgAzul">
    <TD align="center" class="bgAzul">Selecionar Agência</TD>
  </TR>
   <TR>
    <TD>&nbsp;</TD>
  </TR>
   <TR>
    <TD align="center">Agência: <input type="text" name="agencia" size="10"></TD>
  </TR>
   <TR>
    <TD>&nbsp;</TD>
  </TR>
   <TR>
    <TD align="center"><INPUT class=servicos onclick="this.form.comm.value='cessao';this.form.submit()" type=button value="Voltar">  <input type="submit" value="Continuar" class="servicos"></TD>
  </TR>
</form>
   <TR>
    <TD><br><br>&nbsp;</TD>
  </TR>
</TABLE>
