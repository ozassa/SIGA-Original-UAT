<?php require_once ("../tarif/Simul.php");
?>
<html><head><title>Simula��o de Pr�mio</title>
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
<li><u>Taxa de Pr�mio - Indicativa : <?php echo number_format ($T5 * 100, 3, ",", ""); ?>%</u>
<li><u>Pr�mio M�nimo - Indicativo  : US$ <?php echo number_format ($PM, 2, ",", "."); ?></u>
</UL>
<P>&nbsp;</P>
<P align=justify>&nbsp;&nbsp;&nbsp;Vale ressaltar que o referido valor � apenas uma mera
proje��o "indicativa" do custo do pr�mio do seguro, j� que �
imprescind�vel uma an�lise posterior e criteriosa dos importadores,
para que na sequ�ncia � sua concord�ncia, seja poss�vel formular-se
uma proposta. Assim sendo e t�o logo a an�lise seja finalizada,
estaremos remetendo-lhe uma cota��o firme do Seguro de Cr�dito �
Exporta��o, para sua aprecia��o final e aguardaremos o seu "de acordo"
na expectativa de que possamos encaminhar-lhe os documentos originais
para assinatura e formaliza��o da contrata��o em quest�o.
Desde j� nos colocamos � sua disposi��o para quaisquer esclarecimentos 
que se fizerem necess�rios.
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










