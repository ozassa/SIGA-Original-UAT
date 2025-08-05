<!-- Alterado Hicom (Gustavo) 30/12/04 - bloqueio de envio de proposta se algum dado for alterado -->
<!-- Alterado Hicom (Gustavo) 03/01/05 - exibir botão de reestudo antes de enviar a oferta -->
<!-- Alterado Hicom (Gustavo) 06/01/05 - opção para selecionar o vencimento das demais parcelas  -->

<div align="center">
<script language="JavaScript" src="<?php   echo $root;?>scripts/utils.js"></script>

<?php  $cur=odbc_exec(
    $db,
    "SELECT  id, respName, ocupation, bornDate, prMin, txMin, txRise, idRegion, numParc,
     periodMaxCred, currency, limPagIndeniz, validCot, txAnalize, currencyAnalize,
     txMonitor, currencyMonitor, prodUnit, percCoverage, sentOffer, tarifDate, warantyInterest, idUser, emailContact,
     pvigencia, txMonitor, mModulos, tipoDve, perBonus, perPart0, perPart1, pLucro, nas, Ga,Periodo_Vigencia FROM Inform WHERE id = $idInform"
  );
  if (odbc_fetch_row($cur)) {
    $date = odbc_result($cur,4);
    $limPagIndeniz = odbc_result($cur, 'limPagIndeniz');
    $cobertura = odbc_result($cur, 'percCoverage');
    $tarifDate = odbc_result($cur,21);
    $tarifDate = substr($tarifDate,8,2)."/".substr($tarifDate,5,2)."/".substr($tarifDate,0,4);
    $field->setDB ($cur);
    $numParc=$field->getDBField("numParc", 9);
    //echo "Número de parcelas [".$numParc."]";
    //echo "<br>".($field->getDBField ("warantyInterest",22) == 1 ? 1.04 : 1)."<br>";
    $idUser = odbc_result($cur,"idUser");
    $emailContact = odbc_result($cur,"emailContact");
    $vigencia    = odbc_result($cur, "pvigencia");
	$dataOferta  = odbc_result($cur, "dataOferta");
	$dataPreench = odbc_result($cur, "dataPreench");
	$mBonus      = odbc_result($cur, "mModulos");
	//$mPart       = odbc_result($cur, "mPart");
	$tipoDve     = odbc_result($cur, "tipoDve");
	$perBonus    = odbc_result($cur, "perBonus");
	$perPart0    = odbc_result($cur, "perPart0");
    $perPart1    = odbc_result($cur, "perPart1");
    $pLucro      = odbc_result($cur, "pLucro");
    $nas         = odbc_result($cur, "nas");
    $ga          = odbc_result($cur, "Ga");
    $moeda       = odbc_result($cur, "currency");
	$jurosmora	 = odbc_result($cur, "warantyInterest");
	$Periodo_Vigencia  = odbc_result($cur, "Periodo_Vigencia");
    


    if ($nas == "" Or $nas == "0.00"){
      if ($moeda == "2") {
         $nas = "500";
      }elseif ($moeda == "6"){
         $nas = "400";
      }elseif ($moeda == "0"){
         $nas = "500";
      }
    }


    if ($mBonus == "1") {
       $bonus = "checked";
    }

    if ($mBonus == "2") {
       $part = "checked";
    }
    
    if ($mBonus == "0") {
       $nenhum = "checked";
       $part = "";
       $bonus = "";
    }else{
       $nenhum = "checked";
       $part = "";
       $bonus = "";
    }

    if ($pLucro == "F13") {
       $f13 = "checked";
    }else if ($pLucro == "F14") {
       $f14 = "checked";
    }else if ($pLucro == "F15") {
       $f15 = "checked";
    }

    if ( $vigencia == "") {
       $pvigencia = "12 Meses";
    }else if ( $vigencia == "1" ) {
       $pvigencia = "12 Meses";
    }else{
       $pvigencia = "24 Meses";
    }
	
	if($Periodo_Vigencia){
	   	$pvigencia = $Periodo_Vigencia ." Meses";
	}

    if ($moeda == "2") {
       $tmoeda = "Dolár";
       $extMoeda = "US$";
       $ext = "Dolar Norte Americano";
    }else if($moeda == "6"){
       $tmoeda = "Euro";
       $extMoeda = "€";
       $ext = "Euro";
    }else if ($moeda == "0") {
       $tmoeda = "Dolár";
       $extMoeda = "US$";
       $ext = "Dolar Norte Americano";
    }

?>
<table cellspacing=2 cellspacing="0" width="100%" align="center">
<tr><td class="textoBold" width="30%">Nome do Responsável:</td><td class="texto" width="70%"><?php   echo odbc_result($cur,2);?></td></tr>
<tr><td class="textoBold">Cargo:</td><td class="texto"><?php   echo odbc_result($cur,3);?></td></tr>
<tr><td class="textoBold">Data de Cadastro:</td><td class="texto"><?php   echo substr($date,8,2)."/".substr($date,5,2)."/".substr($date,0,4);?></td></tr>
<tr><td class="textoBold">Data de Tarifação:</td><td class="texto"><?php   echo $tarifDate;?></td></tr>
<tr><td class="textoBold">Email para enviar Oferta/Proposta:</td><td class="texto"><?php   echo $emailContact;?></td></tr>
<tr><td class="textoBold">Período de Vigência Selecionado:</td><td class="texto"><?php  echo $pvigencia;?></td></tr>
<tr><td class="textoBold">Tipo de Moeda Selecionado:</td><td class="texto"><?php  echo $tmoeda;?></td></tr>
</table>

<script>

function exibir_nenhum(){

  if (document.all.opt[0].checked == true) {
   p1.style.display = 'none';
   p2.style.display = 'none';
   f1.style.display = 'none';
   f2.style.display = 'none';
   f3.style.display = 'none';
   document.all.per1.value = '';
   document.all.per2.value = '';
   document.all.plucro[0].checked = false;
   document.all.plucro[1].checked = false;
   document.all.plucro[2].checked = false;
   document.all.opt[1].value = '0';
   p0.style.display = 'none';
   p3.style.display = 'none'
   document.all.per.value = '';
   document.all.opt[0].value = '0';

  }

}


function exibir_bonus(){

  if (document.all.opt[1].checked == true) {
   p0.style.display = 'block';
   p3.style.display = 'block';
   document.all.opt[1].value = '1';

   p1.style.display = 'none';
   p2.style.display = 'none';
   f1.style.display = 'none';
   f2.style.display = 'none';
   f3.style.display = 'none';
   document.all.per1.value = '';
   document.all.per2.value = '';
   document.all.plucro[0].checked = false;
   document.all.plucro[1].checked = false;
   document.all.plucro[2].checked = false;
   document.all.opt[2].value = '0';
  }else{
   p0.style.display = 'none';
   p3.style.display = 'none'
   document.all.per.value = '';
   document.all.opt[1].value = '0';
  }

}

function exibir_part(){

  if (document.all.opt[2].checked == true) {
   p1.style.display = 'block';
   p2.style.display = 'block';
   f1.style.display = 'block';
   f2.style.display = 'block';
   f3.style.display = 'block';
   document.all.opt[2].value = '2';

   p0.style.display = 'none';
   p3.style.display = 'none'
   document.all.per.value = '';
   document.all.opt[1].value = '0';
  }else{
   p1.style.display = "none";
   p2.style.display = "none";
   f1.style.display = 'none';
   f2.style.display = 'none';
   f3.style.display = 'none';
   document.all.per1.value = '';
   document.all.per2.value = '';
   document.all.plucro[0].checked = false;
   document.all.plucro[1].checked = false;
   document.all.plucro[2].checked = false;
   document.all.opt[2].value = '0';
  }

}

function tipoApl(){
   if (document.all.tipoapolice.value == '0') { <!--Apólice Antiga-->
      document.all.opt[0].click();
      document.all.opt[0].disabled = true;
      document.all.opt[1].disabled = true;
      document.all.opt[2].disabled = true;

   }else{
      document.all.opt[0].disabled = false;
      document.all.opt[1].disabled = false;
      document.all.opt[2].disabled = false;
   }
}

function nParc (form) {

  <?php  if ($vigencia != "2") {
  ?>
  if (form.numParc[0].checked) {
    numParc=1;
  } else if (form.numParc[1].checked) {
    numParc=2;
  } else if (form.numParc[2].checked) {
    numParc=4;
  } else if (form.numParc[3].checked) {
    numParc=7;
  } else if (form.numParc[4].checked) {
    numParc=10;
  }
  return numParc;
  <?php  }else if ($vigencia == "2"){
  ?>
  if (form.numParc[0].checked) {
    numParc=1;
  } else if (form.numParc[1].checked) {
    numParc=4;
  } else if (form.numParc[2].checked) {
    numParc=7;
  } else if (form.numParc[3].checked) {
    numParc=8;
  }
    return numParc;
  <?php  }
  ?>
}

function calc (form) {
  nP = nParc (form);
  /*
  Tiago V N - Elumini
  Aqui o sistema faz um arredondamento utilizando a função(Math.floor)
  */
  premio =  Math.floor(<?php   echo  $field->getDBField ("prMin", 5) * ($field->getDBField ("warantyInterest",22) == 1 ? 1.04 : 1);?> * (1 + (numVal(form.txRise.value)/100)) / nP) * nP;
  form.prDisplay.value = premio;
  checkDecimals (form.prDisplay, dot2comma(form.prDisplay.value));
  form.txDisplay.value = <?php   echo  $field->getDBField ("txMin", 6) ?> * (1 + (numVal(form.txRise.value)/100)) * 100;
  //form.txDisplay.value = <?php   echo  100 * $field->getDBField ("txMin", 6);?> * (1 + (numVal(form.txRise.value /100)));
  checkDecimals (form.txDisplay, dot2comma(form.txDisplay.value));
 
}

function consist (form) {
  msg = "";
  nP = nParc (form);
  calc(form);
  if (
    nP > 1 &&
    numVal(form.prDisplay.value) / nP < 1000
  ) {
    msg += "Valor da parcela abaixo de <?php   echo $extMoeda;?> 1.000,00";
  }


  if (document.all.comm.value=='viewProp'){
  
  }else{
        if (document.all.nas.value == '0,00') {
             verErro('Informe um valor para Notificação de Ameaça de Sinistro.');
             return(false);
        }
  }
  
  if (document.all.opt[1].checked == true) {
     msgb = "";
     if (document.all.per.value == "" ||
                                document.all.per.value == "0") {
        verErro('Campo percentual esta vazio ou zerado.');
        return(false);
     }else{
        msgb = "\n e " + document.all.per.value + "% Bônus por Ausência de Sinistro\n" +
        "vinculado a renovação da apólice por mais um período de seguro";
     }
  }else{
       msgb = "";
  }

  if (document.all.opt[2].checked == true) {
     msgp = "";
     msgp = "\n e Participação nos lucros com ";
     if (document.all.per1.value == "" ||
                document.all.per1.value == "0" ) {
        verErro('Campo de percentagem de dedução esta vazio ou zerado.');
        return(false);
     }else{
        msgp += "\n Percentagem de Dedução : " + document.all.per1.value + "%";
     }
     

     if (document.all.per2.value == "" ||
                                 document.all.per2.value == "0") {
        verErro('Campo de Participação nos lucros esta vazio ou zerado.');
        return(false);
     }else {
        msgp += "\n e Participação nos Lucros : " + document.all.per2.value + "%";
     }

     if (!document.all.plucro[0].checked &&
                !document.all.plucro[1].checked &&
                         !document.all.plucro[2].checked ){
         verErro('Selecione um tipo de Participação nos Lucros.');
         return(false);
     }
  }else{
     msgp = "";
  }

 if (form.txAnalize.value == '0,00'){
   verErro("Informe um valor para a Taxa de Análise e Monitoramento");
   form.txAnalize.focus();
   return (false);
 }

  if (msg != "") {
    verErro("Favor corrigir o número de parcelas:\n"+ msg);
  } else {
    if (form.tipoapolice.value==1 && form.jurosmora.value==1){
        if (!confirm ("O cliente selecionou cobertura de juros de mora," +
                     "caso continue\n com apólice tipo GA o sistema automaticamente "+
					 "cancelará a\n cobertura de juros de mora.\n Deseja continuar ?")){	
		}else{
	        if (confirm ("Confirma o valor de Análise = R$ " + form.txAnalize.value+ "\n" +
                     "e Monitoramento = R$ " + form.txMonitor.value + msgb + msgp + "?"))
   		       form.submit();
		
		}			 
	}else{
        if (confirm ("Confirma o valor de Análise = R$ " + form.txAnalize.value+ "\n" +
                     "e Monitoramento = R$ " + form.txMonitor.value + msgb + msgp + "?"))
   	       form.submit();
	}	   
  }
}
</script>

<form action="<?php   echo $root;?>role/executive/Executive.php" method=post name=f>
<input type=hidden name="comm" value="sendOffer">
<input type=hidden name="idInform" value="<?php   echo $idInform;?>">
<input type=hidden name="idNotification" value="<?php   echo $idNotification;?>">
<input type=hidden name="idRegion" value="<?php   echo $field->getDBField('idRegion', 8);?>">
<input type="hidden" name="jurosmora" value="<?php   echo $jurosmora;?>">

<TABLE WIDTH="100%" cellspacing=0 cellpadding="2" align="center" border="0">
  <TR class="bgCinza">
    <td class="bgCinza" align="center">&nbsp;</td>
    <td class="bgCinza" align="center">Valores</td>
    <td class="bgCinza" align="center">Aumentar</td>
    <td class="bgCinza" align="center">Novo Valor</td> 
  </TR>
  <TR>
    <td class="textoBold">Prêmio Mínimo:</td>
<?php  $premio_minimo = odbc_result($cur, 5) * ($field->getDBField("warantyInterest", 22) == 1 ? 1.04 : 1);
?>
    <td align=center class="texto"><?php   echo number_format ($premio_minimo, 2, ",", ".");?></td> 
    <td align=center rowspan=2><INPUT class="caixa" size=5 onFocus="select()" name="txRise" value="<?php   echo number_format(100 * odbc_result($cur, 7), 2, ',', '.');?>" onBlur="checkDecimals(this, this.value); calc(this.form)">%</td>
    <?php  //echo ($field->getDBField('warantyInterest', 22) == 1 ? 1.04 : 1);
    //echo (odbc_result($cur, 5) * ($field->getDBField('warantyInterest', 22) == 1 ? 1.04 : 1) * (1 + odbc_result($cur, 7)));
    ?>
    <td align=center><input class="caixa" name="prDisplay" onFocus="blur()" value="<?php   echo number_format((odbc_result($cur, 5) * ($field->getDBField('warantyInterest', 22) == 1 ? 1.04 : 1) * (1 + odbc_result($cur, 7))), 2, ',', '.');?>"></td>
  </TR>
  <TR>
    <td class="textoBold">Taxa Mínima:</td>
    <td align=center class="texto"><?php   echo round(100 * odbc_result($cur, 6), 2)//number_format(100 * odbc_result($cur, 6), 2, ",", ".");?>%</td>
    <td align=center>&nbsp;&nbsp;<input class="caixa" name="txDisplay" onFocus="blur()" value="<?php   echo number_format ((100 * odbc_result($cur, 6) * (1 + odbc_result($cur, 7))),2,",",".");?>">%</td>
  </TR>
  <TR>
    <th colspan = 4>&nbsp;</th>
  </TR>
  <TR class="bgCinza">
    <th colspan="4" class="bgCinza">Parâmetros da Proposta</th>
  </TR>
  <TR>
    <th colspan = 4>&nbsp;</th>
  </TR>
</TABLE> 

<table width="100%" align="center" cellpadding="2" cellspacing=0 border=0>
<tr>
<td width=100% colspan=4>
<table width="100%">
<tr>
<td align=center class="texto">
  <input type="radio" onClick="calc(this.form)" name = "numParc" value="1" checked><br> À vista
</td>
<?php  if ($vigencia !="2") {
?>
<td align=center class="texto">
<input type="radio" onClick="calc(this.form)" name = "numParc" value="2"<?php   echo $numParc == 2 ? " checked" : "";?>>02 Parcelas <br> À vista/90 dias</td>
</td>
<?php  }
?>
<?php  if ($vigencia !="2") {
?>
<td align=center class="texto">
<input type="radio" onClick="calc(this.form)" name = "numParc" value="4"<?php   echo $numParc == 4 ? " checked" : "";?>>04 Parcelas <br> À vista/90/180/270 dias</td>
<?php  }else{
?>
<td align=center class="texto">
<input type="radio" onClick="calc(this.form)" name = "numParc" value="4"<?php   echo $numParc == 4 ? " checked" : "";?>>04 Parcelas <br> Trimestrais</td>
<?php  } ?>

<td align=center class="texto">
<input type="radio" onClick="calc(this.form)" name = "numParc" value="7"<?php   echo $numParc == 7 ? " checked" : "";?>>07 Parcelas <br> mensais</td>

<?php  if ($vigencia == "2"){
?>
<td align=center class="texto">
<input type="radio" onClick="calc(this.form)" name = "numParc" value="8"<?php   echo $numParc == 8 ? " checked" : "";?>>08 Parcelas <br> Trimestrais</td>
<?php  }
?>
<!--
<td align=center class="texto">
<input type="radio" onClick="calc(this.form)" name = "numParc" value="6"<?php   echo $numParc == 6 ? " checked" : "";?>>06 Parcelas <br> À vista/60/120/180/240/300 dias</td>
<td align=center class="texto">
<input type="radio" onClick="calc(this.form)" name = "numParc" value="10"<?php   echo $numParc == 10 ? " checked" : "";?>>10 Parcelas <br> À vista e demais parcelas <br> a cada 30 dias</td>
-->

</tr>
</table>
</td>
</tr>
<tr>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class="textoBold">Duração de Crédito:</td><td class="texto"><input type=hidden name=periodMaxCred value="180">180</td>
  <td class="textoBold">* Vencimento da 1ª parcela:</td><td class="texto">
  	<select name=vencFirst>
<?php  /*
$dia = date('d');
$mes = date('m');
$ano = date('Y');
    for($j = $dia + 1; $j <= $dia + 15; $j++){
      $tmp = date('d/m/Y', mktime(0, 0, 0, $mes, $j, $ano));
      echo "<option value=\"$tmp\"> $tmp\n";
    }
*/
$dia = date('d');
$mes = date('m');
$ano = date('Y');
for($j = 1; $j <= 28; $j++){
 	if ($j >= $dia){
   	$tmp = date('d/m/Y', mktime(0, 0, 0, $mes, $j, $ano));
	   echo "<option value=\"$tmp\"> $tmp\n";
 	}
 }
for($j = 1; $j <= 28; $j++){
 	if ($j < $dia){
   	$tmp = date('d/m/Y', mktime(0, 0, 0, $mes + 1, $j, $ano));
	   echo "<option value=\"$tmp\"> $tmp\n";
 	}
 }

?>
    </select>
  </td>
</tr>

<!-- Alterado Hicom (Gustavo) -->
<?php  // somente o executivo responsável pode escolher a data
if (false) { // não é mais usado (não apaguei pq se mudarem de idéia...)
//if ($userID == $idUser) {
?>
<tr>
  <td class="textoBold"></td><td class="texto"></td>
  <td class="textoBold">Dia das demais parcelas:</td>
  <td class="texto">
  	<select name=diaFaturas>
  		<OPTION value="1" selected>1</OPTION>
  		<OPTION value="2">2</OPTION>
  		<OPTION value="3">3</OPTION>
<!--		... -->
	</select>
  </td>
</tr>
<?php  }
?>
<!-- fim -->

<tr>
  <td class="textoBold">Moeda:</td><td class="texto"><input class="caixa" type=hidden name=currency value="<?php   echo $field->getDBField('currency', 11);?>"><?php   echo $ext;?></td>
  <td class="textoBold">Lim. Máx Pag. Indeniz.:</td><td class="texto"><input class="caixa" name=limPagIndeniz value=<?php   echo $limPagIndeniz ? $limPagIndeniz : '30';?> onFocus="blur()"></td>
</tr>
<tr>
  <td class="textoBold">Taxa de Análise:</td><td class="texto"><input class="caixa" name=txAnalize style="width : 60px;" onBlur="checkDecimals(this, this.value)" value="<?php   echo number_format(odbc_result($cur,'txAnalize'), 2, ',', '.');?>">&nbsp;R$</td>
  <!--number_format($field->getDBField('txAnalize', 14), 2, ',', '.')-->
  <td class="textoBold">Cobertura:</td><td><input class="caixa" name=percCoverage value="<?php   echo number_format($cobertura ? $cobertura : 85, 0, '', '');?>" onFocus="blur()" size=4>%</td>
</tr>
<tr>
  <td class="textoBold">Taxa de Monitoramento:</td><td class="texto"><input name=txMonitor class="caixa" style="width : 60px;" onBlur="checkDecimals(this, this.value)" value="<?php   echo number_format(odbc_result($cur,'txMonitor'), 2, ',', '.');?>">&nbsp;R$</td>
  <!--number_format($field->getDBField('txMonitor', 16), 2, ',', '.')-->
  <td class="textoBold">Unidade de Produção:</td>
  <td>
    <SELECT name=prodUnit class="caixa">
	<option value="62"<?php   echo $field->getDBField("prodUnit", 18) == 62 ? " selected" : "";?>>Matriz-062</option>
        <option value="202"<?php   echo $field->getDBField("prodUnit", 18) == 202 ? " selected" : "";?>>Filial SP-202</option>
        <option value="204"<?php   echo $field->getDBField("prodUnit", 18) == 204 ? " selected" : "";?>>Filial RS-204</option>
    </SELECT>
  </td>
</tr>
<tr>
  <td class="textoBold">Validade da Cotação:</td><td class="texto"><input class="caixa" name=validCot style="width : 60px;" value="<?php   echo $field->getDBField('validCot', 13);?>">&nbsp;dias</td>
  <td class="textoBold">Dve:</td>
  <td class="texto">
  <select name="tipoDve" class="caixa" style="width : 100px">
          <?php  if ($tipoDve == "0") {
               $selmensal = "selected";
            }else if ($tipoDve == "1") {
               $seltrimestral = "selected";
            }else{
            }
          ?>
          <option value="0" <?php   echo $selmensal;?> >Mensal</option>
          <option value="1" <?php   echo $seltrimestral;?> >Trimestral</option>
  </select>
  </td>
</tr>
<tr>
   <td class="textoBold">Limite Mínimo para<br>Notificação de<br>Ameaça de Sinistro</td>
   <td class="texto"><input name="nas" class="caixa" style="width : 60px;" onBlur="checkDecimals(this, this.value)" value="<?php echo number_format($nas, 2, ',','.');?>">&nbsp;<?php   echo $extMoeda;?></td>
   <td class="textoBold">Tipo Apólice</td>
   <td class="texto">
   <select name="tipoapolice" class="caixa" style="width: 100px;" onFocus="tipoApl()" onChange="tipoApl()">
   <?php  if ($ga!=""){
      if ($ga=="0"){
        $ap = "Selected";
      }else{
        $g  = "Selected";
      }
   }else{
        $g  = "Selected";
   }

   ?>
   <option value="1" <?php   echo $g;?> >GA</option>
   <option value="0" <?php   echo $ap;?> >Apol. Antiga</option>
   </select>
   </td>
</tr>
</table>
<!--Alterador por Tiago - Elumini - 29/12/2005-->
<?php  if ( $userID == "456"  ||
         $userID == "1953" ||
         $userID == "40" || $role["policy"] ) {
          if ($userID== "40" And
                          $premio_minimo >= 30000) { //Nice
?>
<table width="100%" border="0">
<tr>
   <td colspan="3" class="bgCinza" align="center">Parâmetros de Modulos Especiais</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" name="opt" class="caixa"  onclick="exibir_nenhum()" <?php   echo $nenhum;?> value="0" >Nenhum</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" name="opt" class="caixa"  onclick="exibir_bonus()" <?php   echo $bonus;?> value="1" >Bônus por Ausência de sinistro</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p3" style="display:none" class="textoBold">&nbsp;&nbsp;vinculado a renovação da apólice por mais um período de seguro.</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p0" style="display:none">Percentual&nbsp;<input type="text" name="per" class="caixa" style="width:60px;" value="<?php   echo $perBonus ?>" >%</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" name="opt" class="caixa" onclick="exibir_part()" <?php   echo $part;?> value="2" >Participação nos lucros</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p1" style="display:none">Percentagem de Dedução&nbsp;<input type="text" name="per1" class="caixa" style="width:60px;" value="<?php   echo $perPart0;?>" >%</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p2" style="display:none">Participação nos Lucros&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="per2" class="caixa" style="width:60px;" value="<?php   echo $perPart1;?>" >%</td>
</tr>
<tr>
    <td colspan="2"><img src="dot.gif" border="0" height="10"></td>
</tr>
<tr id="f1" style="display : none">
    <td><input type="radio" name="plucro" class="caixa" value="13" <?php   echo $f13;?>> </td>
    <td>F13.2 - Ao Termino de cada Período de Seguro...</td>
</tr>
<tr id="f2" style="display : none">
    <td><input type="radio" name="plucro" class="caixa" value="14" <?php   echo $f14;?>></td>
    <td>F14.2 - Caso a presente apólice se mantenha vigente durante 2 (dois) Períodos de Seguro...</td>
</tr>
<tr id="f3" style="display : none">
    <td><input type="radio" name="plucro" class="caixa" value="15" <?php   echo $f15;?>></td>
    <td>F15.2 - Caso a presente apólice se mantenha vigente durante 3 (três) Períodos de Seguro...</td>
</tr>
</table>
<?php  /* Habilita os checkbox após ter recebido o valor da consulta*/
    if ($mBonus=="0") {
       echo "<script>".
            "p0.style.display = 'block';".
            "p3.style.display = 'block';".
            "</script>";
       echo "<script>p1.style.display = 'block'</script>";
       echo "<script>p2.style.display = 'block'</script>";
       echo "<script>".
            "f1.style.display = 'block';".
            "f2.style.display = 'block';".
            "f3.style.display = 'block';".
            "</script>";
    }
    
    if ($mBonus=="1") {
       echo "<script>".
            "p0.style.display = 'block';".
            "p3.style.display = 'block';".
            "</script>";
	}

	if ($mBonus =="2") {
       echo "<script>p1.style.display = 'block'</script>";
       echo "<script>p2.style.display = 'block'</script>";
       echo "<script>".
            "f1.style.display = 'block';".
            "f2.style.display = 'block';".
            "f3.style.display = 'block';".
            "</script>";
	}
	
	if ($ga == "0" || $ga == "") {
      echo "<script>
      document.all.opt[0].click();
      document.all.opt[0].disabled = true;
      document.all.opt[1].disabled = true;
      document.all.opt[2].disabled = true;</script>";
	}else{
      echo "<script>
      document.all.opt[0].disabled = false;
      document.all.opt[1].disabled = false;
      document.all.opt[2].disabled = false;</script>";
	}
}else if ($userID != "40"){
?>

<table width="100%" border="0">
<tr>
   <td colspan="3" class="bgCinza" align="center">Parâmetros de Modulos Especiais</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" disabled id="opt1" name="opt" class="caixa"  onclick="exibir_nenhum()" <?php   echo $nenhum;?> value="0" >Nenhum</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" disabled name="opt" class="caixa"  onclick="exibir_bonus()" <?php   echo $bonus;?> value="1" >Bônus por Ausência de sinistro</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p3" style="display:none" class="textoBold">&nbsp;&nbsp;vinculado a renovação da apólice por mais um período de seguro.</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p0" style="display:none">Percentual&nbsp;<input type="text" name="per" class="caixa" style="width:60px;" value="<?php   echo $perBonus;?>" >%</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" disabled name="opt" class="caixa" onclick="exibir_part()" <?php   echo $part;?> value="2" >Participação nos lucros</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p1" style="display:none">Percentagem de Dedução&nbsp;<input type="text" name="per1" class="caixa" style="width:60px;" value="<?php   echo $perPart0;?>" >%</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p2" style="display:none">Participação nos Lucros&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="per2" class="caixa" style="width:60px;" value="<?php   echo $perPart1;?>" >%</td>
</tr>
<tr>
    <td colspan="2"><img src="dot.gif" border="0" height="10"></td>
</tr>
<tr id="f1" style="display : none">
    <td><input type="radio" name="plucro" class="caixa" value="13" <?php   echo $f13;?>> </td>
    <td>F13.2 - Ao Termino de cada Período de Seguro...</td>
</tr>
<tr id="f2" style="display : none">
    <td><input type="radio" name="plucro" class="caixa" value="14" <?php   echo $f14;?>></td>
    <td>F14.2 - Caso a presente apólice se mantenha vigente durante 2 (dois) Períodos de Seguro...</td>
</tr>
<tr id="f3" style="display : none">
    <td><input type="radio" name="plucro" class="caixa" value="15" <?php   echo $f15;?>></td>
    <td>F15.2 - Caso a presente apólice se mantenha vigente durante 3 (três) Períodos de Seguro...</td>
</tr>
</table>
<?php  /* Habilita os checkbox após ter recebido o valor da consulta*/
    if ($mBonus=="0") {
       echo "<script>".
            "p0.style.display = 'none';".
            "p3.style.display = 'none';".
            "</script>";

       echo "<script>p1.style.display = 'none'</script>";
       echo "<script>p2.style.display = 'none'</script>";
       echo "<script>".
            "f1.style.display = 'none';".
            "f2.style.display = 'none';".
            "f3.style.display = 'none';".
            "</script>";
    }

    if ($mBonus=="1") {
       echo "<script>".
            "p0.style.display = 'block';".
            "p3.style.display = 'block';".
            "</script>";
	}

	if ($mBonus =="2") {
       echo "<script>p1.style.display = 'block'</script>";
       echo "<script>p2.style.display = 'block'</script>";
       echo "<script>".
            "f1.style.display = 'block';".
            "f2.style.display = 'block';".
            "f3.style.display = 'block';".
            "</script>";
	}

    if ($ga == "0" || $ga == "") {
      echo "<script>
      document.all.opt[0].click();
      document.all.opt[0].disabled = true;
      document.all.opt[1].disabled = true;
      document.all.opt[2].disabled = true;</script>";
	}else{
      echo "<script>
      document.all.opt[0].disabled = false;
      document.all.opt[1].disabled = false;
      document.all.opt[2].disabled = false;</script>";
	}

} //Validação para o valor do premio minimo maior que 30.000 e Usuário Nice
else{
?>
<table width="100%" border="0">
<tr>
   <td colspan="3" class="bgCinza" align="center">Parâmetros de Modulos Especiais</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" disabled name="opt" class="caixa"  onclick="exibir_nenhum()" <?php   echo $nenhum;?> value="0" >Nenhum</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" disabled name="opt" class="caixa"  onclick="exibir_bonus()" <?php   echo $bonus;?> value="1" >Bônus por Ausência de sinistro</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p3" style="display:none" class="textoBold">&nbsp;&nbsp;vinculado a renovação da apólice por mais um período de seguro.</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p0" style="display:none">Percentual&nbsp;<input type="text" readonly name="per" class="caixa" style="width:60px;" value="<?php   echo $perBonus;?>" >%</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" disabled name="opt" class="caixa" onclick="exibir_part()" <?php   echo $part;?> value="2" >Participação nos lucros</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p1" style="display:none">Percentagem de Dedução&nbsp;<input type="text" readonly name="per1" class="caixa" style="width:60px;" value="<?php   echo $perPart0;?>" >%</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p2" style="display:none">Participação nos Lucros&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" readonly name="per2" class="caixa" style="width:60px;" value="<?php   echo $perPart1;?>" >%</td>
</tr>
<tr>
    <td colspan="2"><img src="dot.gif" border="0" height="10"></td>
</tr>
<tr id="f1" style="display : none">
    <td><input type="radio" disabled name="plucro" class="caixa" value="13" <?php   echo $f13;?>> </td>
    <td>F13.2 - Ao Termino de cada Período de Seguro...</td>
</tr>
<tr id="f2" style="display : none">
    <td><input type="radio" disabled name="plucro" class="caixa" value="14" <?php   echo $f14;?>></td>
    <td>F14.2 - Caso a presente apólice se mantenha vigente durante 2 (dois) Períodos de Seguro...</td>
</tr>
<tr id="f3" style="display : none">
    <td><input type="radio" disabled name="plucro" class="caixa" value="15" <?php   echo $f15;?>></td>
    <td>F15.2 - Caso a presente apólice se mantenha vigente durante 3 (três) Períodos de Seguro...</td>
</tr>
</table>
<?php  /* Habilita os checkbox após ter recebido o valor da consulta*/
    if ($mBonus=="0") {
       echo "<script>".
            "p0.style.display = 'none';".
            "p3.style.display = 'none';".
            "</script>";

       echo "<script>p1.style.display = 'none'</script>";
       echo "<script>p2.style.display = 'none'</script>";
       echo "<script>".
            "f1.style.display = 'none';".
            "f2.style.display = 'none';".
            "f3.style.display = 'none';".
            "</script>";
    }

    if ($mBonus=="1") {
       echo "<script>".
            "p0.style.display = 'block';".
            "p3.style.display = 'block';".
            "</script>";
	}

	if ($mBonus =="2") {
       echo "<script>p1.style.display = 'block'</script>";
       echo "<script>p2.style.display = 'block'</script>";
       echo "<script>".
            "f1.style.display = 'block';".
            "f2.style.display = 'block';".
            "f3.style.display = 'block';".
            "</script>";
	}
	
	if ($ga == "0" || $ga == "") {
      echo "<script>
      document.all.opt[0].click();
      document.all.opt[0].disabled = true;
      document.all.opt[1].disabled = true;
      document.all.opt[2].disabled = true;</script>";
	}else{
      echo "<script>
      document.all.opt[0].disabled = false;
      document.all.opt[1].disabled = false;
      document.all.opt[2].disabled = false;</script>";
	}

 }
}else{
?>
<table width="100%" border="0">
<tr>
   <td colspan="3" class="bgCinza" align="center">Parâmetros de Modulos Especiais</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" disabled name="opt" class="caixa"  onclick="exibir_nenhum()" <?php   echo $nenhum;?> value="0" >Nenhum</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" disabled name="opt" class="caixa"  onclick="exibir_bonus()" <?php   echo $bonus;?> value="1" >Bônus por Ausência de sinistro</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p3" style="display:none" class="textoBold">&nbsp;&nbsp;vinculado a renovação da apólice por mais um período de seguro.</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p0" style="display:none">Percentual&nbsp;<input type="text" readonly name="per" class="caixa" style="width:60px;" value="<?php   echo $perBonus;?>" >%</td>
</tr>
<tr>
   <td colspan="2"><input type="radio" disabled name="opt" class="caixa" onclick="exibir_part()" <?php   echo $part;?> value="2" >Participação nos lucros</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p1" style="display:none">Percentagem de Dedução&nbsp;<input type="text" readonly name="per1" class="caixa" style="width:60px;" value="<?php   echo $perPart0;?>" >%</td>
</tr>
<tr>
   <td><img src="dot.gif" border="0" height="3" width="7"></td>
   <td id="p2" style="display:none">Participação nos Lucros&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" readonly name="per2" class="caixa" style="width:60px;" value="<?php   echo $perPart1;?>" >%</td>
</tr>
<tr>
    <td colspan="2"><img src="dot.gif" border="0" height="10"></td>
</tr>
<tr id="f1" style="display : none">
    <td><input type="radio" disabled name="plucro" class="caixa" value="13" <?php   echo $f13;?>> </td>
    <td>F13.2 - Ao Termino de cada Período de Seguro...</td>
</tr>
<tr id="f2" style="display : none">
    <td><input type="radio" disabled name="plucro" class="caixa" value="14" <?php   echo $f14;?>></td>
    <td>F14.2 - Caso a presente apólice se mantenha vigente durante 2 (dois) Períodos de Seguro...</td>
</tr>
<tr id="f3" style="display : none">
    <td><input type="radio" disabled name="plucro" class="caixa" value="15" <?php   echo $f15;?>></td>
    <td>F15.2 - Caso a presente apólice se mantenha vigente durante 3 (três) Períodos de Seguro...</td>
</tr>
</table>
<?php  /* Habilita os checkbox após ter recebido o valor da consulta*/
    if ($mBonus=="0") {
       echo "<script>".
            "p0.style.display = 'none';".
            "p3.style.display = 'none';".
            "</script>";

       echo "<script>p1.style.display = 'none'</script>";
       echo "<script>p2.style.display = 'none'</script>";
       echo "<script>".
            "f1.style.display = 'none';".
            "f2.style.display = 'none';".
            "f3.style.display = 'none';".
            "</script>";
    }

    if ($mBonus=="1") {
       echo "<script>".
            "p0.style.display = 'block';".
            "p3.style.display = 'block';".
            "</script>";
	}

	if ($mBonus =="2") {
       echo "<script>p1.style.display = 'block'</script>";
       echo "<script>p2.style.display = 'block'</script>";
       echo "<script>".
            "f1.style.display = 'block';".
            "f2.style.display = 'block';".
            "f3.style.display = 'block';".
            "</script>";
	}
	if ($ga == "0" || $ga == "") {
      echo "<script>
      document.all.opt[0].click();
      document.all.opt[0].disabled = true;
      document.all.opt[1].disabled = true;
      document.all.opt[2].disabled = true;</script>";
	}else{
      echo "<script>
      document.all.opt[0].disabled = false;
      document.all.opt[1].disabled = false;
      document.all.opt[2].disabled = false;</script>";
	}

} //Outro Usuário (Modo de Visualização)
//Validação para os usuario : Cristina, Marcele L, Nice e Role policy
?>
<br><br><br>

<input class="servicos" type=hidden name=currencyAnalize value="<?php   echo $field->getDBField('currencyAnalize', 15);?>">
<input class="servicos" type=hidden name=currencyMonitor value="<?php   echo $field->getDBField('currencyMonitor', 17);?>">
<?php  if ($msg != "") {?><p><font color="red"><?php   echo $msg ?></font></p><?php  } ?>
<input class="servicos" type=button value="Voltar" onClick="this.form.comm.value='notif';this.form.submit()">
<!--Alterado por Tiago V N - 03/11/2005-->
<!--<input class="servicos" type=button value="Cancelar" onClick="this.form.comm.value='cancelInf';this.form.submit()">-->
<input class="servicos" type=button value="Cancelar Informe" onClick="this.form.comm.value='cancelInf';confirma(this.form);">
<INPUT class="servicos" type=button value="Retarifação" onClick="this.form.comm.value='retarifar'; this.form.submit()">
<br>
<?php  if (odbc_result($cur, 20) != 0 || $idAnt)  { ?>
<INPUT class="servicos" type=button value="Enviar Nova Oferta" onClick="this.form.comm.value='sendOffer';consist(this.form);">
<input class="servicos" type=button value="Enviar Proposta" onClick="enviar(this.form)">
<?php  } else { ?>
<INPUT class="servicos" type=button value="Enviar Oferta" onClick="this.form.comm.value='sendOffer';consist(this.form);">
<?php  }
// alterado Hicom (Gustavo) - tirei esse teste para exibir opção antes de enviar a oferta e adicionei outro
// para exibir apenas para o executivo responsável ou para o perfil creditManager
//    if($sentOffer){
		  
    if ($userID == $idUser || $role["creditManager"]) {
?>
<INPUT class="servicos" type=button value="Reestudo" onClick="this.form.comm.value='devolve';this.form.submit();">
<INPUT class="servicos" type=button value="Analis. credito" onClick="this.form.comm.value='devcredito';this.form.submit();">
<?php  //  }  
    }
?>
</P>
<P>
* Todas as parcelas vencerão sempre no mesmo dia do mês, beseadas no dia do vencimento da primeira parcela.
</P>
</form>
<?php  } else {
?>
<p class="verm">Inform inválido</p>
<?php  }
?>
</div>

<script language=javascript>
function confirma(c){
 if (confirm('Tem certeza que deseja cancelar?')){
     c.submit();
    return true;
 }else{
   return
 }
}

calc(document.f);

<!-- Alterado Hicom (Gustavo) -->
function enviar(f) {
	
	ok = true;
	
	wtxRise = '<?php   echo  number_format(100 * odbc_result($cur, 7), 2, ',', '.');?>';
    if (wtxRise != f.txRise.value){
		ok = false;
	}
	wnumParc = '<?php   echo  $numParc ?>';
	if (wnumParc != nParc(f)){
		ok = false;
	}
	wlimPagIndeniz = '<?php   echo  $limPagIndeniz ? $limPagIndeniz : '30';?>';
    if (wlimPagIndeniz != f.limPagIndeniz.value){
		ok = false;
	}
	wtxAnalize = '<?php   echo  number_format(odbc_result($cur, 'txAnalize'), 2, ',', '.');?>';
    if (wtxAnalize != f.txAnalize.value){
		ok = false;
	}

   	wtxMonitor = '<?php   echo  number_format(odbc_result($cur,'txMonitor'), 2, ',', '.');?>';
   	<!--number_format($field->getDBField('txMonitor', 14), 2, ',', '.')-->
    if (wtxMonitor != f.txMonitor.value){
		ok = false;
	}

	wpercCoverage = '<?php   echo  number_format($cobertura ? $cobertura : 85, 0, '', '');?>';
    if (wpercCoverage != f.percCoverage.value){
		ok = false;
	}
	wvalidCot = '<?php   echo  $field->getDBField('validCot', 13);?>';
    if (wvalidCot != f.validCot.value){
		ok = false;
	}
	
    wmModulos = '<?php   echo  $field->getDBField('mModulos', 27);?>';
    if (f.opt[1].checked){
      var mo = '1';
    }else if (f.opt[2].checked){
      var mo = '2';
    }else if (f.opt[0].checked){
      if (wmModulos == '') {
         var mo = wmModulos;
      }else{
         var mo = '0';
      }
    }
    if (wmModulos != mo){
      ok = false;
    }

    if (f.opt[1].checked){
        wperBonus = '<?php   echo  $field->getDBField('perBonus', 29);?>';
        if (wperBonus != f.per.value){
           ok = false;
        }
	}

    if (f.opt[2].checked){
        wperPart1 = '<?php   echo  $field->getDBField('perPart0', 30);?>';
        if (wperPart1 != f.per1.value){
           ok = false;
        }
        wperPart2 = '<?php   echo  $field->getDBField('perPart1', 31);?>';
        if (wperPart2 != f.per2.value){
           ok = false;
        }
        
        wpLucro = '<?php   echo  $field->getDBField('pLucro', 32);?>';
        if (f.plucro[0].checked) {
          var pl = 'F13';
        }else if (f.plucro[1].checked){
          var pl = 'F14';
        }else if (f.plucro[2].checked){
          var pl = 'F15';
        }
        if (wpLucro != pl ){
           ok = false;
        }
	}

    wnas = '<?php   echo  number_format($field->getDBField('nas', 33), 2, ',', '.');?>';
    if (wnas == ''){
       var na = '0,00';
    }else{
       var na = wnas;
    }
    if (na != f.nas.value){
       ok = false;
	}
	if (!ok) {
		verErro('Dados da oferta foram alterados. Somente são permitidas alterações na data de vencimento e unidade de produção.');
	}
	else {
		f.comm.value='viewProp';
		consist(f);
	}
}


</script>
