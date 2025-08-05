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
.verm {
	COLOR: #aa0000; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}
</STYLE>
<script>

function checkDecimals(fieldName, fieldValue) {

  if (fieldValue == "") {
    verErro("Preenchimento obrigatório.");
    fieldName.select();
    fieldName.focus();
  } else {
    err = false;
    dec = ",";
    mil = ".";
    v = "";
    c = "";
    len = fieldValue.length;
    for (i = 0; i < len; i++) {
      c = fieldValue.substring (i, i+1);
      if (c == dec) { break; }
      if (c != mil) {
        if (isNaN(c)) {
          err = true;
          verErro("Este não é um número válido.");
          fieldName.select();
          fieldName.focus();
          break;
        } else {
          v += c;
        }
      }
    }
    if (!err) {
      if (i == len) {
        v += "00";
      } else {
        if (c == dec) i++;
        if (i == len) {
          v += "00";
        } else {
          c = fieldValue.substring (i, i+1);
          if (isNaN(c)) {
            verErro("Este não é um número válido.");
            fieldName.select();
            fieldName.focus();
            err = true;
          } else {
            v += c;
          }
        }
        i++;
        if (!err && i == len) {
          v += "0";
        } else {
          c = fieldValue.substring (i, i+1);
          if (isNaN(c)) {
            verErro("Este não é um número válido.");
            fieldName.select();
            fieldName.focus();
            err = true;
          } else {
            v += c;
          }
        }
      }
      fieldValue = "," + v.substring (v.length - 2, v.length);
      v = v.substring (0, v.length - 2);
      while (v.length > 0) {
        t = v.substring (v.length >= 3 ? v.length - 3 : 0, v.length);
        v = v.substring (0, v.length >= 3 ? v.length - 3 : 0);
        fieldValue = (v.length > 0 ? "." : "") + t + fieldValue;
      }
      fieldName.value = fieldValue;
    }
  }
}

</script>

<script>
      function consist (form) {
      msg = "";
      if (form.name.value == "") { msg += "Razão Social Completa\n"; }
      if (form.address.value == "") { msg += "Endereço\n"; }
      if (form.city.value == "") { msg += "Cidade\n"; }
      if (form.tel.value == "") { msg += "Telefone/FAX\n"; }
      if (msg != "") {
      verErro("Favor preencher as seguintes informações:\n"+ msg);
      } else  {
        form.submit();
      }
      }
</script>
</HEAD>
<BODY bgColor="#ffffcc" leftMargin="5" topMargin="5" marginheight="5" marginwidth="5">
<DIV align=center>
<TABLE border=0 cellPadding=0 cellSpacing=0 height=34 width="96%">
  <TBODY>
  <TR>
    <TD width="200" align="left"><IMG alt="" border=0 src="../../images/inf.gif"></TD>
    <TD width="100%" align="left">&nbsp;<!--<IMG alt="" border=0 src="images/44.gif">--></TD>
  </TR>
  </TBODY>
</TABLE>
<FORM action="<?php  echo $root;?>role/inform/Inform.php" method="post" name="">
<input type=hidden name="comm" value="buySubmit">
<input type=hidden name="id" value="<?php  echo $id;?>">
<TABLE border="0" borderColor="#00ccff" cellSpacing=0 cellpadding="5" width="100%">
  <TBODY>
  <TR bgcolor="#cccccc">
    <TD colspan="10" align="center">Inclusão de importadores</TD>
  </TR>
  <TR>
    <TD colspan="10">&nbsp;</TD>
  </TR>
  <TR>
    <TD colspan="10">&nbsp;</TD>
  </TR>
  <!-- início de um importador -->
  <TR bgcolor="#00ccff">
    <TD colspan=10 align="center">Importadores Incluídos</TD>
  </tr>
<?php  $cur = odbc_exec($db,
		   "SELECT imp.name, address, risk, city, c.name, tel, prevExp12, limCredit, numShip12, periodicity, przPag, imp.id
                    FROM Importer imp JOIN Country c ON (idCountry = c.id) WHERE idInform = $idInform ORDER BY imp.id");
  $i = 0;
  while (odbc_fetch_row($cur)) {
    $i ++;
?>
  <tr <?php  echo ((($i % 2) != 0) ? " bgcolor=\"#eaeab4\"" : "");?>>
    <td rowspan=3><?php  echo $i;?></td>
    <td><font color="#4169e1">Razão:</font></td><td colspan=2><?php  echo odbc_result($cur,1);?></td>
    <td><font color="#4169e1">Endereço:</font></td><td colspan=2><?php  echo odbc_result($cur,2);?></td>
    <td><font color="#4169e1">Riscos:</font></td><td colspan=2><?php  $r = odbc_result($cur ,3); echo ($r == 1 ? "RC" : ($r == 2 ? "RP" : "RC/RP"));?></td>
  </tr>
  <tr <?php  echo ((($i % 2) != 0) ? " bgcolor=\"#eaeab4\"" : "");?>>
    <td><font color="#4169e1">Cidade:</font></td><td><?php  echo odbc_result($cur,4);?></td>
    <td><font color="#4169e1">País:</font></td><td><?php  echo odbc_result($cur,5);?></td>
    <td><font color="#4169e1">Tel/Fax:</font></td><td><?php  echo odbc_result($cur,6);?></td>
    <td><font color="#4169e1">Volume US$ Mil:</font></td><td colspan=2><?php  echo number_format(odbc_result($cur,7),2,",",".");?></td>
  </tr>
  <tr <?php  echo ((($i % 2) != 0) ? " bgcolor=\"#eaeab4\"" : "");?>>
    <td><font color="#4169e1">Crédito US$ Mil:</font></td><td><?php  echo number_format(odbc_result($cur,8),2,",",".");?></td>
    <td><font color="#4169e1">N.º Emb./Ano:</font></td><td><?php  echo odbc_result($cur,9);?></td>
    <td><font color="#4169e1">Per/Emb(dias):</font></td><td><?php  echo odbc_result($cur,10);?></td>
    <td><font color="#4169e1">Prz./Pag.(dias):</font></td><td><?php  echo odbc_result($cur,11);?></td>
    <td align=right><a href="<?php  echo $root;?>role/inform/Inform.php?comm=remBuy&idBuy=<?php  echo odbc_result($cur,12);?>&idInform=<?php  echo $idInform;?>"><font color="#4169e1">remover</a></font></td>
  </tr>
<?php  }
  if ($i == 0) {
?>
  <TR bgcolor="#cccccc">
    <TD align="center" colspan=10>Nenhum importador cadastrado</TD>
  </TR>
<?php  }
?>
  </TBODY>
</TABLE>
<P>&nbsp;</P>
<P>&nbsp;</P>
<TABLE border=0 width="96%">
  <TBODY>
  <TR>
    <TD colspan="4" align="center" class="verm">Relacionar nesta fase, apenas os 10 importadores mais representativos</TD>
  </TR>
  <TR>
    <TD colspan="4">&nbsp;</TD>
  </TR>
  <TR>
    <TD align="left">Razão Social Completa</TD>
    <TD align="left" colspan="2"><input type="text" size="60" name="name" value="<?php  echo ($msg == "" ? "" : $field->getField("name"));?>"></TD>
  </TR>
  <TR>
    <TD align="left">Endereço</TD>
    <TD align="left" colspan="2"><input type="text" size="60" name="address" value="<?php  echo ($msg == "" ? "" : $field->getField("address"));?>"></TD>
  </TR>
   <TR>
    <TD align="left">Cidade</TD>
    <TD align="left" colspan="2"><input type="text" size="30" name="city" value="<?php  echo ($msg == "" ? "" : $field->getField("city"));?>"></TD>
  </TR>
  <TR>
    <TD align="left">País</TD>
    <TD align="left" colspan="2">
      <?php  // Monta a lista de países
        $sql = "SELECT id, name FROM Country ORDER BY name";
        $sel = $field->getField("idCountry");
	$name = "idCountry";
        require_once("../../interf/Select.php");
      ?>
    </TD>
  </TR>
  <TR>
    <TD align="left">Telefone / FAX (incluir DDI e DDD)</TD>
    <TD align="left" colspan=2><input type="text" size="20" name="tel" value="<?php  echo ($msg == "" ? "" : $field->getField("tel"));?>"></TD>
  </TR>
  <TR>
    <TD align="left">Previsão Vol. Export próx. 12 meses </TD>
    <TD align="left"><input onFocus="select()" onBlur="checkDecimals(this, this.value)" type="text" size="20" name="prevExp12" value="<?php  echo number_format($field->getNumField("prevExp12"),2,",",".");?>"></TD>
    <TD align="left">Limite de Crédito Necessário (Exposição Máxima)</TD>
    <TD align="left"><input onFocus="select()" onBlur="checkDecimals(this, this.value)" type="text" size="20" name="limCredit" value="<?php  echo number_format($field->getNumField("limCredit"),2,",",".");?>"></TD>
  </TR>
  <TR>
    <TD align="left">Nº de Embarques por ano </TD>
    <TD align="left"><input onFocus="select()" type="text" size="20" name="numShip12" value="<?php  echo $field->getNumField("numShip12");?>"></TD>
    <TD align="left">Periodicidade de Embarques (Dias) </TD>
    <TD align="left"><input onFocus="select()" type="text" size="20" name="periodicity" value="<?php  echo $field->getNumField("periodicity");?>"></TD>
  </TR>
  <TR>
    <TD align="left">Prazo de Pagamento (Dias) </TD>
    <TD align="left"><input onFocus="select()" type="text" size="20" name="przPag" value="<?php  echo $field->getNumField("przPag");?>"></TD>
  </TR>
  <TR>
    <TD align="left">Indicar Riscos</TD>
    <?php  $risk = $field->getField("risk") ?>
    <TD align="left" colspan="2"><input type="radio" name="risk" value="1"<?php  echo $risk == "1" ? " checked" : "";?>> RC- Comercial<br>
                                 <input type="radio" name="risk" value="2"<?php  echo $risk == "2" ? " checked" : "";?>> RP- Político<br>
                                 <input type="radio" name="risk" value="3"<?php  echo $risk == "3" ? " checked" : "";?>> Ambos</TD>
  </TR>
  
  </TBODY>
</TABLE>
<?php  if ($msg != "") {?><p><font color="red"><?php  echo $msg;?></font></p><?php  } ?>

<P>&nbsp;</P>

<P><font class="verm">Para cadastrar os principais compradores, preencha o formulário e clique em "Incluir"</font></P>
<P><input type=button value="Voltar" onClick="this.form.comm.value='open';this.form.submit()"> <INPUT type=button value="Incluir" onClick="consist(this.form);this.form.comm.value='insBuy';this.form.submit()"> <INPUT type=submit value="OK" > <INPUT name=Reset type=reset value=Cancelar></P>
</form>
</DIV>
</BODY>