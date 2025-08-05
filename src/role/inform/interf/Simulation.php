<?php require_once ("../tarif/Simul.php");
?>
<html><head><title>Simulação de Prêmio</title>
</head>
<body bgColor="#ffffcc">
<STRONG>

<TABLE border=0 cellPadding=12 cellSpacing=0 height=34 width="96%">
  <TBODY>
  <TR>
    <TD width="200" align="left"><IMG alt="" border=0 src="../../images/inf.gif"></TD>
    <TD width="100%" align="left">&nbsp;<!--<IMG alt="" border=0 src="images/44.gif">--></TD>
  </TR>
  <TR>
    <TD>&nbsp;</TD>
  </TR>
  </TBODY>
</TABLE>
<FONT face=arial size=2 color="#000066">
<P align=center><U>Sr.Cliente, Informamos</U>:</P>
<UL>
<li><u>Taxa de Prêmio - Indicativa : <?php echo number_format ($T5 * 100, 3, ",", ""); ?>%</u>
<li><u>Prêmio Mínimo - Indicativo  : US$ <?php echo number_format ($PM, 2, ",", "."); ?></u>
</UL>
<P>&nbsp;</P>
<P align=justify>&nbsp;&nbsp;&nbsp;Vale ressaltar que o referido valor é apenas uma mera
projeção "indicativa" do custo do prêmio do seguro, já que é
imprescindível uma análise posterior e criteriosa dos importadores,
para que na sequência à sua concordância, seja possível formular-se
uma proposta. Assim sendo e tão logo a análise seja finalizada,
estaremos remetendo-lhe uma cotação firme do Seguro de Crédito à
Exportação, para sua apreciação final e aguardaremos o seu "de acordo"
na expectativa de que possamos encaminhar-lhe os documentos originais
para assinatura e formalização da contratação em questão.
Desde já nos colocamos à sua disposição para quaisquer esclarecimentos 
que se fizerem necessários.
</P></FONT></STRONG>
<FORM action="<?php echo $root; ?>role/inform/Inform.php" method="post" name="">
<input type=hidden name="idInform" value="<?php echo $field->getField("idInform"); ?>">
<input type=hidden name="comm" value="open">
<input type=hidden name="idNotification" value="<?php echo $idNotification; ?>">
<input type=hidden name="volta" value="<?php echo $volta; ?>">
<P>&nbsp;</P>

<P align=center><input type=submit value="OK"></P>
</form>
</body></HTML>










