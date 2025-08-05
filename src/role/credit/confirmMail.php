<HTML><HEAD><TITLE>SBCE: Informe</TITLE>

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
<script language="JavaScript">
<!--
function CloseWindow(){
window.close();
}
//-->
</SCRIPT>
</HEAD>

<BODY bgColor=#ffffcc leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
  <TR>
    <TD colspan=11 width="100%" height="85" bgColor="#000066" align="center"><img src="../../images/logob1.gif"></TD>
  </tr>
  <tr>
    <TD width="100%">	


<DIV align=center>

<P>&nbsp;</P>
<P>&nbsp;</P>
<P><FONT size=5>

<?php  require_once("../rolePrefix.php");
$h = base64_decode($hash);
list($id, $cookie) = explode("\|", $h);
$c = odbc_exec($db, "update MailConfirm set state=2, confirmDate=getdate() where id=$id and cookie='$cookie'");
if(!$c){
  $msg = "Erro ao atualizar dados do email: ". odbc_errormsg($db);
}else{
  $msg = "Confirmação de email recebido OK";
}
echo $msg;

?>
</font></p>
<P>&nbsp;</P>
<P>&nbsp;</P>
<p align="center"> <a href="javascript:CloseWindow();" title="Fechar essa janela!"> Fechar</a></p>
<P>&nbsp;</P>
<P>&nbsp;</P>
<P>&nbsp;</P>

<P>Desenvolvido pela Gerência de Informática da SBCE</P>
<P><FONT size=1>Todos os Direitos Reservados</FONT></P>

</DIV>


</BODY>
</HTML>
