<script>
	function gerarPdf(f){
		f.comm.value = "pdf";
		f.submit();
	}
</script>
<?php 
  //Alterado por Tiago V N - Elumini - 10/04/2006
  $cur=odbc_exec(
    $db,
    "SELECT * FROM Inform WHERE id = ".$field->getField("idInform")
  );

  if (odbc_result($cur, 'currency') == 2) {
     $ext = "US$";
  }else if (odbc_result($cur, 'currency') == 6) {
     $ext = "€";
  }
?>
<script language="JavaScript" src="<?php echo $root; ?>scripts/utils.js"></script>
<form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post">

<TABLE border="0" cellSpacing=0 cellpadding="2" width="100%" align="center">
  <TR class="bgCinza">
    <TD colspan="7" align="center">QUADRO III - Principais Compradores</TD>
  </TR>
  <TR>
    <TD colspan="7">&nbsp;</TD>
  </TR>
  <TR>
    <TD align="justify" colspan="7">Atenção: O limite de crédito é rotativo.  Deve ser calculado para cobrir a exposição máxima do segurado em relação a cada importador.  Depende do valor e da frequência dos embarques, assim como do prazo de pagamento.  Para cadastrar a Razão Social do Importador, não utilize apóstrofo ( ' ).  Em caso de dúvida, entre em contato conosco:  <ul><li>São Paulo, ligue (11) 3284-3132; <!-- <li>Rio Grande do Sul, ligue (51) 3249-7154; --> <li>Demais localidades, ligue (21) 2510-5000.</TD>
  </TR>
  <TR>
    <TD colspan="7">&nbsp;</TD>
  </TR>
  <!-- início de um importador ' -->
  <TR class="bgAzul">
    <TD colspan=7 align="center">Importadores Incluídos</TD>
  </tr>
<?php 
  $cur = odbc_exec($db,
		   "SELECT imp.name, address, risk, city, c.name, tel, prevExp12, limCredit, numShip12, periodicity, ".
		   "przPag, imp.id, imp.hold, cep, fax, contact, relation, seasonal FROM Importer imp JOIN Country c ON ".
		   "(idCountry = c.id) WHERE idInform = $idInform AND state <> 7 AND state <> 15 AND state <> 8 and ".
		   "state <> 9 and imp.id not in (select distinct idImporter from ImporterRem) ORDER BY imp.id");
  $i = 0;
  $count = 0;
  while (odbc_fetch_row($cur)) {
    $i++;
?>
  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td align=center rowspan=6 width="5%">
    <?php echo $i ?>
    <?php  if (($role['executive'] || $role['executiveLow']) && odbc_result ($cur, 'hold')) { $count ++; ?>
    <br>
    <input type=hidden name="buyId<?php echo $count; ?>" value="<?php echo odbc_result ($cur, 'id'); ?>">
    <input type=checkbox name="free<?php echo $count; ?>" value = 1 <?php echo odbc_result($cur,'hold') == 0 ? ' checked' : ''; ?>>
    <?php  } ?>
    </td>
    <td><font color=#4169e1>Razão:</font></td><td colspan=2><?php echo odbc_result($cur,1); ?></td>
    <td><font color=#4169e1>Endereço:</font></td><td colspan=4><?php echo odbc_result($cur,2); ?>&nbsp;</td>
    <!--<td><font color=#4169e1>Riscos:</font></td><td colspan=2><?php  $r = odbc_result($cur ,3); echo ($r == 1 ? "RC" : ($r == 2 ? "RP" : "RC/RP")); ?></td>-->
  </tr>
  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td><font color=#4169e1 width="30%">Cidade:</font></td><td width="15%"><?php echo odbc_result($cur,4); ?>&nbsp;</td>
    <td><font color=#4169e1 width="10%">CEP:</font></td><td width="15%"><?php echo odbc_result($cur,14); ?>&nbsp;</td>
    <td><font color=#4169e1 width="5%">País:</font></td><td width="20%"><?php echo odbc_result($cur,5); ?>&nbsp;</td>
  </tr>
  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td><font color=#4169e1>Telefone:</font></td><td><?php echo odbc_result($cur,6); ?>&nbsp;</td>
    <td><font color=#4169e1>Fax:</font></td><td colspan="3"><?php echo odbc_result($cur,15); ?>&nbsp;</td>
  </tr>
  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td><font color=#4169e1>Contato:</font></td><td><?php echo odbc_result($cur,16); ?>&nbsp;</td>
    <td><font color=#4169e1>Vendas Sazonais:</font></td><td><?php echo odbc_result($cur,18) ? "Sim" : "Não"; ?></td>
    <td><font color=#4169e1>Relação Comercial:</font></td><td> desde o ano de <?php echo odbc_result($cur,17); ?></td>
  </tr>
  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <td><font color=#4169e1>Volume <?php echo $ext;?>:</font></td><td><?php echo number_format(odbc_result($cur,7),2,",","."); ?></td>
    <td><font color=#4169e1>N.º Emb./Ano:</font></td><td><?php echo odbc_result($cur,9); ?></td>
    <td><font color=#4169e1>Exp. Máx. <?php echo $ext;?> (Mil):</font></td><td><?php  if (odbc_result ($cur, 'hold')) { ?><input name="limCredit<?php echo $count; ?>" value="<?php echo odbc_result($cur,8)/1000 ?>" onBlur="checkDecimalsMil(this, this.value)" size=5><?php  } else {?><?php echo odbc_result($cur,8)/1000; ?><?php  } ?></td>
  </tr>
  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
    <!--<td><font color=#4169e1>Crédito US$:</font></td><td><?php echo number_format(odbc_result($cur,8),2,",","."); ?></td>-->
    <td><font color=#4169e1>Per/Emb(dias):</font></td><td><?php echo odbc_result($cur,10); ?></td>
    <td><font color=#4169e1>Prz./Pag.(dias):</font></td><td colspan="3"><?php echo odbc_result($cur,11); ?></td>
  </tr>

<?php 
  }
  if ($i == 0) {
?>
  <TR bgcolor="#a4a4a4">
    <TD align="center" colspan=7>Nenhum importador cadastrado</TD>
  </TR>
<?php 
  }
?>
</TABLE>
<P>&nbsp;</P>
<P>
<?php  if ($msg != "") {?>
  <p><font color="red"><?php echo $msg; ?></font></p>
<?php  } ?>

<div align="center">
<input type=hidden name=idInform value="<?php echo $field->getField("idInform"); ?>">
<input type="hidden" name="reltipo" value="informIII">
<input type=hidden name=comm value="buySubmit">
<P><input type=button class="servicos" value="Voltar" onClick="this.form.comm.value='open';this.form.submit()"> <INPUT type=submit class="servicos" value="OK"> 
<input type="button" class="servicos" value=" Versão PDF" name="pdf" id="pdf" onclick="gerarPdf(this.form)"></P>

</form>

</DIV> 
