<DIV align=center>
<head>
<script>
function calc (form) {
   ace = numVal (form.ace.value);
   proex = numVal (form.proex.value);
   tot = ace/1 + proex/1;
   b = form.b.value;
   c = form.c.value;
   perc = 0;
   if (b/1 + c/1 > 0) {
     perc = (tot / (b/1 + c/1)) * 100;
   }
   form.tot.value = tot;
   checkDecimals(form.tot,dot2comma(form.tot.value));
   form.perc.value = perc;
   checkDecimals(form.perc,dot2comma(form.perc.value));
}

</script>
</head>
<?php $cur=odbc_exec(
    $db,
    "SELECT ace, proex, vol3 AS B, vol4 AS C FROM Inform i Join Volume v ON (i.id = v.idInform) WHERE i.id = ".$field->getField("idInform")
  );
  if (odbc_fetch_row($cur)) {
    $field->setDB ($cur);
    $ace = $field->getDBField ("ace",1);
    $proex = $field->getDBField ("proex",2);
    $tot = $ace + $proex;
    $b = $field->getDBField ("B",3);
    $c = $field->getDBField ("C",4);
    if ($b + $c > 0) {
      $perc = number_format(( $tot / ($b+$c))*100,2,",",".")."%";
    } else {
      $perc = 0;
    }
	
	$idNotification = $field->getField("idNotification");
	$volta          = $field->getField("volta");
	$tipo_apolice   = $field->getField("tipo_apolice");
?>
<FORM action="<?php echo $root; ?>role/inform/Inform.php" method="post" name="">
<input type=hidden name="comm" value="prevSubmit">
<input type=hidden name="idInform" value="<?php echo $field->getField("idInform"); ?>">
<input type=hidden name="idNotification" value="<?php echo $idNotification; ?>">
<input type=hidden name="volta" value="<?php echo $volta; ?>">
<input type=hidden name="b" value="<?php echo $b; ?>">
<input type=hidden name="c" value="<?php echo $c; ?>">

<?php //####### ini ####### adicionado por eliel vieira - elumini - em 31/08/2007
echo "<input type=hidden name=tipo_apolice value='$tipo_apolice'>";
?>

<TABLE border="0" cellSpacing=0 cellpadding="2" width="96%">
  <TR class="bgCinza">
    <TD colspan="4" class="bgCinza" align="center">QUADRO III - Previsão de Financiamento para o Volume de Vendas Externas - Campos "B" e "C" do Quadro I</TD>
  </TR>
  <TR>
    <TD colspan="4" >&nbsp;</TD>
  </TR>
  <TR class="bgAzul">
    <TD  align="center" class="bgAzul">ACE - US$ </TD>
    <TD  align="center" class="bgAzul">PROEX - US$</TD>
    <TD  align="center" class="bgAzul">Total - US$</TD>
    <TD  align="center" class="bgAzul">Percentual (Total Financiado / B + C)</TD>
  </TR>
  <TR>
    <TD  align="center"><input onFocus="select()" type="text" class="caixa" size="15" value="<?php echo number_format($ace,2,",","."); ?>" onBlur="checkDecimals(this, this.value);calc(this.form)" name="ace"></TD>
    <TD  align="center"><input onFocus="select()" type="text" class="caixa" size="15" value="<?php echo number_format($proex,2,",","."); ?>" onBlur="checkDecimals(this, this.value);calc(this.form)" name="proex"></TD>
    <TD  align="center"><input type="text" class="caixa" size="15" value="<?php echo number_format($tot,2,",","."); ?>" onFocus="blur()" name="tot"></TD>
    <TD  align="center"><input type="text" class="caixa" size="15" value="<?php echo number_format($perc,2,",","."); ?>" onFocus="blur()" name="perc"><b>%</b></TD>
  </TR>
</TABLE>
<P>&nbsp;</P>
<P>&nbsp;</P>
<P><input type=button value="Voltar" onClick="this.form.comm.value='open';this.form.submit()" class="servicos"> <INPUT type=submit value="OK" class="servicos"> <INPUT name=Reset type=reset value=Cancelar class="servicos"> </P>
</form>
<?php }
?>
</DIV>





