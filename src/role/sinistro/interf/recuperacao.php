<?php
  include_once("../consultaCoface.php");
?>
<script language="JavaScript" src="<?php echo $root;?>scripts/calendario.js"></script>
<script>
function checkDecimals2(fieldName, fieldValue) {

  if (fieldValue == "0,00") {
    verErro("Preenchimento obrigatório.");
    fieldName.value='';
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
          fieldName.value='0,00';
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
          v += "";
        } else {
          c = fieldValue.substring (i, i+1);
          if (isNaN(c)) {
            verErro("Este não é um número válido.");
            fieldName.value='0,00';
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
            fieldName.value='0,00';
            fieldName.focus();
            err = true;
          } else {
            v += c;
          }
        }
      }	  
	if(fieldValue.match(/^\d+$/)){
	  fieldName.value = fieldValue + ',00';
	}else if(fieldValue.match(/^(\d+)(,|.)\d\d/)){
	  fieldName.value = fieldValue.replace(/^(\d+)(,|.)(\d\d)\d*$/, '$1' + ',' + '$3');
	}else{
	  fieldName.value = fieldValue.replace(/\./, ',');
	  fieldName.value += '';
	}
      }
    }
  }

</script>
<script>
function verify(f){
   if(f.valor.value == ''){
     verErro('Preencha o valor recuperado');
     f.valor.focus();
     return false;
   }
  if(f.date.value == ''){
    verErro('Preencha a Data do Pagamento');
    f.date.focus();
    return false;
  }
  return true;
}
</script>

<table border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <td colspan=7 class="bgCinza" align="center">Aviso de Sinistro</td>
  </TR>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
<?php $query = "SELECT inf.name, imp.name FROM Inform inf JOIN Importer imp ON (imp.idInform = inf.id) WHERE imp.id = $idImporter";
    $cur = odbc_exec($db,$query);
?>
  <TR>
    <td colspan=7>Exportador: <span class="texto"><?php echo odbc_result($cur, 1);?></span></td>
  </TR>
  <TR>
    <td colspan=7>Importador: <span class="texto"><?php echo odbc_result($cur, 2);?></span></td>
  </TR>
<?php if($nSinistro){ ?>
  <TR>
    <td colspan=7>Número do Sinistro: <span class="texto"><?php echo $nSinistro; ?></span></td>
  </TR>
<?php } ?>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=7 class="bgCinza" align="center">Faturas Recuperadas</td>
  </TR>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
  <TR>
    <td class=bgAzul align=center>Nº Sinistro</td>
    <td class=bgAzul align=center>Nº Fatura</td>
    <td class=bgAzul align=center>Data de Embarque</td>
    <td class=bgAzul align=center>Data de Vencimento</td>
    <td class=bgAzul align=center>Valor da Fatura</td>
    <td class=bgAzul align=center>Valor Pago</td>
    <td class=bgAzul align=center>Valor em Aberto</td>
  </TR>

<?php $query = "SELECT * FROM SinistroDetails WHERE idSinistro = $idSinistro ORDER BY dateEmb";
    $cur = odbc_exec($db,$query);
    $i = 0;
    $valueTotal = 0;
    $recup = 0;
    $var = odbc_exec($db,"SELECT sum(value) FROM Recuperacao WHERE idSinistro = $idSinistro");
    if(odbc_fetch_row($var)){
      $recup = odbc_result($var, 1);
    }

    while (odbc_fetch_row($cur)) {
      $i++;
      $numSin = odbc_result($cur, 2); 
      $dateEmb = odbc_result($cur, 5); 
      $dateVenc = odbc_result($cur, 6);
      $valuePag = odbc_result($cur, 7);
      $valueFat = odbc_result($cur, 8);
      $valueAbt = odbc_result($cur, 9);
      $valueTotal = $valueTotal + $valueAbt;
      if ($recup >= $valueTotal){
?>
  <TR <?php echo ((($i % 2) != 0) ? " bgcolor=\"#ffffff\"" : " bgcolor=\"#e9e9e9\"");?>>
    <td align=center><?php echo $numSin; ?></td>
    <td align=center><?php echo odbc_result($cur, 4); ?></td>
    <td align=center><?php echo substr($dateEmb, 8, 2). "/". substr($dateEmb, 5, 2). "/". substr($dateEmb, 2, 2); ?></td>
    <td align=center><?php echo substr($dateVenc, 8, 2). "/". substr($dateVenc, 5, 2). "/". substr($dateVenc, 2, 2); ?></td>
    <td align=right><?php echo number_format($valueFat, 2, ",", "."); ?> &nbsp;</td>
    <td align=right><?php echo number_format($valuePag, 2, ",", "."); ?> &nbsp;</td>
    <td align=right><?php echo number_format($valueAbt, 2, ",", "."); ?> &nbsp;</td>
  </TR>
<?php } // if
    } // while

if ($i == 0){
?>
  <TR class="bgCinza">
    <TD align="center" colspan=7 class="bgCinza">Nenhum Dado Cadastrado</TD>
  </TR>
<?php }
?>

  <TR>
    <td class=bgAzul align=right colspan=6>Total (em aberto):</td>
    <td align=right><?php echo number_format($valueTotal, 2, ",", "."); ?> &nbsp;</td>
  </TR>
  <TR>
    <td class=bgAzul align=right colspan=6>Total Recuperado:</td>
    <td align=right><?php echo number_format($recup, 2, ",", "."); ?> &nbsp;</td>
  </TR>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
</table>

<form action="<?php echo $root;?>role/sinistro/Sinistro.php" method="post" name="recup" onSubmit="return verify(this)">
<input type="hidden" name="comm" value="recuperacaoSQL">
<input type="hidden" name="total" value="<?php echo $valueTotal; ?>">
<input type="hidden" name="idInform" value="<?php echo $idInform;?>">
<input type="hidden" name="idImporter" value="<?php echo $idImporter;?>">
<input type="hidden" name="idSinistro" value="<?php echo $idSinistro;?>">
<input type="hidden" name="idNotification" value="<?php echo $idNotification;?>">

<TABLE width ="98%" cellspacing=0 cellpadding=3 border=0 align="center">
  <TR>
    <td colspan=3>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=3 class="bgCinza" align="center">Recuperações</td>
  </TR>
  <TR>
    <td colspan=3>&nbsp;</td>
  </TR>
<?php $s = odbc_exec($db,"SELECT status FROM Sinistro WHERE id = $idSinistro");
  $status = odbc_result($s, 1);
  if(($status != 7) && ($status != 8)){
?>
  <TR>
    <td width="30%">Valor Recuperado:</td>
    <td width="70%" colspan=2><input class="caixa" type="text" name="valor" onBlur="checkDecimals2(this, this.value);"></td>
  </TR>
  <TR>
    <td width="30%">Data do Pagamento:</td>
    <td width="70%" colspan=2><INPUT class=caixa size=11 name="date" onFocus="blur()"> <A HREF="javascript:showCalendar(document.recup.date)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"></A></td>
  </TR>
  <TR>
    <td colspan="3"><input type="radio" name="custo" value="1" checked> Com Custo <?php echo $nomeEmp; ?>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="custo" value="0"> Sem Custo <?php echo $nomeEmp; ?> </td>
  </TR>
  <TR>
    <td colspan=3>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=3 align="center"><input type="submit" value="Recuperar" class="servicos"></td>
  </TR>
  <TR>
    <td colspan=3>&nbsp;</td>
  </TR>
<?php } ?>
  <TR>
    <td class=bgAzul align=center>Valor</td>
    <td class=bgAzul align=center>Data da Recuperação</td>
    <td class=bgAzul align=center>Com Custo <?php echo $nomeEmp; ?></td>
  </TR>
<?php $query = "SELECT date, value, custoCoface FROM Recuperacao WHERE idSinistro = $idSinistro ORDER BY date";
    $c = odbc_exec($db, $query);
    $i = 0;
    while (odbc_fetch_row($c)){
       $i++;
       $dateRecup = odbc_result($c, 'date');
       $dateRecup = substr($dateRecup, 8, 2). "/". substr($dateRecup, 5, 2). "/". substr($dateRecup, 2, 2);
       $valueRecup = number_format(odbc_result($c, 'value'), 2, ",", ".");
       $custoCoface = odbc_result($c, 'custoCoface');
       if($custoCoface == 1){
         $custoCoface = "sim";
       } else {
         $custoCoface = "não";
       }
?>
  <TR <?php echo ((($i % 2) != 0) ? " bgcolor=\"#ffffff\"" : " bgcolor=\"#e9e9e9\"");?>>
    <td align=center><?php echo $valueRecup; ?></td>
    <td align=center><?php echo $dateRecup; ?></td>
    <td align=center><?php echo $custoCoface; ?></td>
  </TR>
<?php } /* while */ ?>
  <TR>
    <td colspan=3>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=3 align="center"><INPUT class=servicos onclick="this.form.comm.value='view';this.form.submit()" type=button value="Voltar">
</td>
  </TR>
</TABLE>

</form>
