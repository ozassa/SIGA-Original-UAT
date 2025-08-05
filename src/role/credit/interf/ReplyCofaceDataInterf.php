<?php 
      include_once("../../consultaCoface.php");
?>
<BODY bgColor=#ffffcc>
<form action="<?php echo $root;?>role/credit/Credit.php" method="post">
<input type="hidden" name="comm" value="acceptDATA">
<input type=hidden name=idBuyer value="<?php echo  $idBuyer;?>">
<input type=hidden name=idNotification value="<?php echo  $field->getField('idNotification');?>">
<input type=hidden name=type value="<?php echo  $type;?>">
<input type=hidden name=idInform value="<?php echo   $field->getField('idInform');?>">
<input type=hidden name=obs value="<?php echo  $field->getField('obs');?>">

<TABLE width="100%" cellspacing=0 cellpadding=0>
<HR>
<TABLE cellspacing=10 cellpadding=0>
<TR>
<td><br></td>
</TR>
</TABLE>
<HR>
<P></P>
<TABLE width="100%" cellspacing=0 cellpadding=5>

<?php  while(odbc_fetch_row($curInform)){
  $informContrat	= odbc_result($curInform, 1);
  $informName	    	= odbc_result($curInform, 2);
  $informState		= odbc_result($curInform, 3);
  $informSitCoface	= odbc_result($curInform, 4);

  if($informState >= 1 && $informState <= 8){
    $informState = "Prospect";
  }else if($informState == 9){
    $informState = "Cancelado";
  }else if($informState == 10){
    $informState = "Apólice";
  }else if($informState == 11){
    $informState = "Encerrado";
  }

?>

  <TR bgcolor=#cccccc><td colspan=4>Exportador: <?php echo  $informName;?></td></tr>
  <TR><td colspan=4>Ci <?php echo $nomeEmp; ?>: <?php echo  $informContrat;?></td></tr>
  <TR bgcolor=#eaeab4><td colspan=4>Situação SBCE: <?php echo  $informState;?></td></tr>
<!--
  <TR><td>Situação Coface: <?php echo  $informSitCoface;?></td></tr>
-->

<?php  } // while

$i = 0;
if(odbc_fetch_row($cur)){
  $importerAddress	= odbc_result($cur, 1);
  $importerTel	    	= odbc_result($cur, 2);
  $importerCity		= odbc_result($cur, 3);
  $nameBuyer		= odbc_result($cur, 4);
  $importerCoface	= odbc_result($cur, 5);
  $CountryCode		= odbc_result($cur, 6);
  $ChangeAddressCity	= odbc_result($cur, 7);
  $ChangeAddressAddress	= odbc_result($cur, 8);
  $ChangeAddressName	= odbc_result($cur, 9);
  $ChangeAddressTel	= odbc_result($cur, 10);
  $ChangeAddressDate	= odbc_result($cur, 11);
  $ChangeAddressState	= odbc_result($cur, 12);
  $CountryName		= odbc_result($cur, 13);
  $idBuyer		= odbc_result($cur, 14);
  $i++;

?>

    <TR bgcolor=#eaeab4>
       Importador:<!-- a href=Credit.php?comm=showBuyers&idBuyer=<?php echo $idBuyer;?> --><?php echo  $nameBuyer;?><!-- /a --></tr>
       </TR>

       <P>&nbsp;</P>

        <TR bgcolor=#cccccc>
          <th><FONT face=arial color=#000066 size=2><b>Dados</FONT></b></th>
          <th><FONT face=arial color=#000066 size=2><b>Nome</FONT></B></th>
          <th><FONT face=arial color=#000066 size=2><b>Endereço</FONT></B></th>
          <th><FONT face=arial color=#000066 size=2><b>País/Ci <?php echo $nomeEmp; ?></FONT></B></th>
        </TR>
        <TR  bgcolor=#eaeab4 align=middle><FONT face=arial color=#000066 size=2><b>
          <th>Antigos</th>
          <th><?php echo  $nameBuyer;?></th>
          <th><?php echo  $importerAddress == 'null' ? '&nbsp;' : $importerAddress;?> <?php echo  $importerCity == 'null' ? '&nbsp;' : $importerCity;?></th>
          <th><?php echo  $CountryCode;?>/<?php echo  $importerCoface;?></th>
        </TR>
        <TR align=middle><FONT face=arial color=#000066 size=2><b>
          <th>Novos</th>
          <th><?php echo  $ChangeAddressName;?></th>
          <th><?php echo  $ChangeAddressAddress;?> <?php echo  $ChangeAddressCity;?></th>
          <th><?php echo  $CountryCode;?>/<?php echo  $importerCoface;?></th></b></font>
        </TR>
<?php  } // if

?>

<TR>
<td>&nbsp;</td>
</TR>

<TR>
<td><FONT face=arial color=#000066 size=2><B>OBS:</B></FONT></td>
</TR>
<TR>
<td><?php echo  $obs;?></td>
</TR>
<TR>
<td>
<INPUT type=button value='Voltar' onClick="this.form.comm.value='open'; this.form.submit()">&nbsp;&nbsp;
<INPUT type=submit value='Aceitar'>
</td>
</TR>
</TABLE>
</BODY>

