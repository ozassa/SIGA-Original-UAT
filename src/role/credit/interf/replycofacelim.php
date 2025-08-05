<?php 
      include_once("../../consultaCoface.php");
?>
<form action="<?php echo $root;?>role/credit/Credit.php" method="post">
<input type="hidden" name="comm" value="sendMail">
<HR>
<TABLE cellspacing=10 cellpadding=0>
<TR>
<TD><input type="checkbox" name="email" value="1" style="LEFT: 6px; TOP: 4px"><FONT face=arial size=2 color=#000066><b>E-mail Segurado</b></td>
<!--
<TD><input type="checkbox" name="emailBB" value="1" style="LEFT: 6px; TOP: 4px" checked><FONT face=arial size=2 color=#000066><b>E-mail BB</b></FONT></TD>
-->
<?php  if(odbc_fetch_row($curInform)){
  $informContrat   = odbc_result($curInform, 1);
  $informName      = odbc_result($curInform, 2);
  $informState	   = odbc_result($curInform, 3);
  $informSitCoface = odbc_result($curInform, 4);

  if($informState >= 1 && $informState <= 8){
    $informState = "Prospect";
  }elseif($informState == 9){
    $informState = "Cancelado";
  }elseif($informState == 10){
    $informState = "Apólice";
  }elseif($informState == 11){
    $informState = "Encerrado";
  }
}

if($informState != 10){
?>
<TD><input type="checkbox" name="enviarTarif" value="1" style="LEFT: 6px; TOP: 4px"><FONT face=arial size=2 color=#000066><b>Enviar para tarifação</b></td>
<?php  } // if
?>
</TR>
</TABLE>
<HR>
<TABLE width=100% ><font face=arial size=2><b>

<?php  if($informState >= 1 && $informState <= 8){
  $informState = "Prospect";
}else if($informState == 9){
  $informState = "Cancelado";
}else if($informState == 10){
  $informState = "Apólice";
}else if($informState == 11){
  $informState = "Encerrado";
}

?>

  <TR bgcolor=#cccccc><td>Exportador: <?php echo  $informName;?></td></tr>
  <TR><td>Ci <?php echo $nomeEmp; ?>: <?php echo  $informContrat;?></td></tr>
  <TR bgcolor=#eaeab4><td>Situação SBCE: <?php echo  $informState;?></td></tr>
<!--
  <TR><td>Situação Coface: <?php echo  $informSitCoface;?></td></tr>
-->
  <TR bgcolor=#eaeab4><td>Situação Faturamento: <?php echo  $sitFat;?></FONT></B></td></TR>
  <Input type=hidden name=invoice value="<?php echo  $invoice;?>">

<TR>
<td>&nbsp;</td>
</TR>
<?php  if(odbc_fetch_row($cur)){
  $nameBuyer         = odbc_result($cur, 3);
  //$idBuyer           = odbc_result($cur, 10);
  $CodeCountry       = odbc_result($cur, 6);
  $ImporterCoface    = odbc_result($cur, 4);
?>
	<tr align=left bgcolor=#eaeab4 >
	<Td>Importador:<a href=Credit.php?comm=showBuyers&idBuyer=<?php echo  $idBuyer;?>><?php echo  $nameBuyer;?></a></Td></tr>
	<tr align=left >
	<Td>País/Ci Importador: <?php echo  $CodeCountry ?>/<?php echo  $ImporterCoface ?></Td></tr>
	<tr align=left bgcolor="#eaeab4">
	<Td>Validade a partir de: <?php echo  $decision_date;?></td></tr>
	<tr align=left>
	<Td>Crédito Solicitado: <?php echo  ($creditReq == 0 ? '-' : "US$". number_format($creditReq, 2, ',', '.'));?></Td></tr>
	<tr align=left bgcolor="#eaeab4">
	<td>Crédito Concedido: <?php echo  'US$'. ($type == 4 ? '0,00' : number_format($credit, 2, ',', '.'));?></td>
<?php  if($creditTemp > 0){
?>

	<tr align=left>
	<Td>Crédito Temporário: US$<?php echo  number_format($creditTemp, 2, ',', '.');?></Td></tr>
	<tr align=left bgcolor="#eaeab4">
	<Td>Validade do crédito temp: <?php echo  $limTemp;?></Td></tr>

<?php  }
}

/* while(odbc_fetch_row($cur)){ */
/*   $nameBuyer  		= odbc_result($cur, 3); */
/*   $idBuyer	    	= odbc_result($cur, 10); */
/*   $CodeCountry		= odbc_result($cur, 6); */
/*   $ImporterCoface	= odbc_result($cur, 4); */
/*   $ChangeStateDate	= odbc_result($cur, 8); */
/*   $ImporterLimCredit	= odbc_result($cur, 5); */
/*   $ChangeCredit		= odbc_result($cur, 7); */

/*   $ChangeCredit		= number_format($ChangeCredit, 2, ",", "."); */
/*   $ImporterLimCredit	= number_format($ImporterLimCredit, 2, ",", "."); */

/*   $ChangeStateDate = substr($ChangeStateDate, 8, 2)."/".substr($ChangeStateDate, 5, 2)."/".substr($ChangeStateDate, 0, 4); */

/*   $i++; */

/* ?> */

/*     <tr align=left bgcolor=#eaeab4> */
/*        <td>Importador:<a href=Credit.php?comm=showBuyers&idBuyer=<?php echo  $idBuyer ?>><?php echo  $nameBuyer ?></a></td> */
/*        </tr> */
/*        <tr align=left> */
/*        <td>País/Ci Coface Importador: <?php echo  $CodeCountry ?>/<?php echo  $ImporterCoface ?></td> */
/*        </tr> */
/*        <tr align=left bgcolor=#eaeab4> */
/*        <td>Validade a partir de: <?php echo  $ChangeStateDate ?></td> */
/*        </tr> */
/*        <tr align=left> */
/*        <td>Crédito Solicitado: US$<?php echo  $ImporterLimCredit ?></td> */
/*        </tr> */
/*        <tr align=left bgcolor=#eaeab4> */
/*        <td>Crédito Concedido:  US$<?php echo  $ChangeCredit ?></td> */
/*        <input type=hidden name=idImporter value=<?php echo  $idBuyer ?>> */

/* <?php  */

/* } // while */

?>

</TR>
<TR>
<td>&nbsp;</td>
</TR>

<?php  $q = "SELECT text FROM Comment WHERE state='1' AND idImporter=$idBuyer";
$c = odbc_exec($db, $q);
if(odbc_fetch_row($c)){
  $obs = odbc_result($c, 1);
}else{
  $obs = $field->getField('obs');
}

?>

<TR bgcolor=#cccccc><td><B>OBS:</td>
</TR>
<TR>
<td><INPUT type=text style="WIDTH: 483px; HEIGHT: 47px" size=62 name="obs" value="<?php echo  $obs;?>"></td>
</TR>
<TR>
<td>
</td></tr>

<h1><?php echo  $msg;?><h1>

</TABLE>
<p>
<input type=hidden name=idBuyer value="<?php echo  $idBuyer;?>">
<input type=hidden name=idNotification value="<?php echo  $idNotification;?>">
<input type=hidden name=decision_date value="<?php echo  $decision_date;?>">
<input type=hidden name=type value="<?php echo  $type;?>">
<input type=hidden name=table_to_update value="NotificationR">
<input type=hidden name=idInform value="<?php echo   $field->getField('idInform');?>">
<input type=hidden name=obs value="<?php echo  $field->getField('obs');?>">
<input type=hidden name=credit value="<?php echo  $field->getField('credit');?>">
<input type=hidden name=creditReq value="<?php echo  $field->getField('creditReq');?>">
<input type=hidden name=creditTemp value="<?php echo  $field->getField('creditTemp');?>">
<input type=hidden name=limTemp value="<?php echo  $field->getField('limTemp');?>">
<input type=button onClick="this.form.comm.value='open';this.form.submit()" value="Voltar">
<input type=hidden name=id_da_coface value="<?php echo  $id_da_coface;?>">
<input type=submit value='Enviar' >
</p>
</form>
