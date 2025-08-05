<HTML><HEAD><TITLE>SBCE: Informe</TITLE>
<META content="text/html; charset=windows-1252" http-equiv=Content-Type>
<STYLE type=text/css>TD {
	COLOR: #000066; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}
BODY {
	COLOR: #000066; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}

P {
	COLOR: #000066; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}

A {
	COLOR: #000066; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}

A:active {
	COLOR: #ff0000; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}

</STYLE>

</HEAD>
<BODY bgColor=#ffffcc leftMargin="5" topMargin="5" marginheight="5" marginwidth="5">
<DIV align=center>
<TABLE border=0 cellPadding=0 cellSpacing=0 height=34 width="96%">
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
<?php 
  $cur=odbc_exec(
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
?>
<TABLE border="0" borderColor=#00ccff cellSpacing=0 cellpadding="2" width="96%">
  <TBODY>
  <TR bgcolor="#cccccc">
    <TD colspan="4"  align="center">QUADRO III - Previsão de Financiamento para o Volume de Vendas Externas - Campos "B" e "C" do Quadro I</TD>
  </TR>
  <TR>
    <TD colspan="4" >&nbsp;</TD>
  </TR>
  <TR bgColor=#00ccff>
    <TD  align="center">ACE - US$ mil</TD>
    <TD  align="center">PROEX - US$ mil</TD>
    <TD  align="center">Total - US$ mil</TD>
    <TD  align="center">Percentual (Total Financiado / B + C)</TD>
  </TR>
  <TR>
    <TD  align="center"><?php echo number_format($ace,2,",","."); ?></TD>
    <TD  align="center"><?php echo number_format($proex,2,",","."); ?></TD>
    <TD  align="center"><?php echo number_format($tot,2,",","."); ?></TD>
    <TD  align="center"><?php echo $perc ?></TD>
  </TR>
  </TBODY>
</TABLE>
<P>&nbsp;</P>
<P>&nbsp;</P>


<form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post">
<input type=hidden name=idInform value="<?php echo $field->getField("idInform"); ?>">

<input type=hidden name=comm value="buyers">
<P><input type=button value="Voltar" onClick="this.form.comm.value='open';this.form.submit()"> <INPUT type=submit value="OK"> </P>

</form>

<?php 
  }
?>
</DIV>
</BODY>
</HTML>


