<?php //echo "Inicio";
?>

<script>
<!--
function seleciona (obj) {
//  verErro(obj.selectedIndex);
  form = obj.form;
 form.submit();
}
// -->
</script>

<script>
function calc (form) {
   form.valueAbt.value = numVal (form.valueFat.value)/1 - numVal(form.valuePag.value)/1;
   checkDecimals(form.valueAbt,dot2comma(form.valueAbt.value));
}
</script>


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

<script language="JavaScript" src="<?php echo $root;?>scripts/calendario.js"></script>
<?php $query = "SELECT inf.name, imp.name, inf.limPagIndeniz, inf.prMin, inf.warantyInterest, inf.percCoverage, inf.txRise, imp.credit, inf.prMTotal, imp.c_Coface_Imp FROM Inform inf JOIN Importer imp ON (imp.idInform = inf.id) WHERE imp.id = $idImporter";
    $cur = odbc_exec($db,$query);
    $limPag = odbc_result($cur, 3);
    $jurosOk = odbc_result($cur, 5);
    $percCo = odbc_result($cur, 6);
    $txRise = odbc_result($cur, 7);
    $credit = odbc_result($cur, 8);
    $premio_minimo = odbc_result($cur, 9);
    $c_Coface_Imp = odbc_result($cur, 10);
    
//----------------------------------------------------------------------------------------------------
// pega o numero do sinistro do SisSeg 
$cur = odbc_exec($db,
		 "SELECT Inform.i_Seg, Inform.idAnt
                  FROM Inform, Importer Where Inform.id=$idInform AND Importer.id=$idImporter");
$i_Seg = odbc_result($cur, 1);
$idAnt = odbc_result($cur, 2);
if($idAnt && ! $i_Seg){
  $i_Seg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
}
$x = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$i_Seg");
$n_Apolice = odbc_result($x, 1);
$x = odbc_exec($dbSisSeg, "select n_Sucursal, n_Ramo from Apolice where n_Apolice=$n_Apolice");
$n_Sucursal = odbc_result($x, 1);
$n_Ramo = odbc_result($x, 2);
$n_Imp = 0;
$ci_coface = odbc_result(odbc_exec($db, "select c_Coface_Imp from Importer where id=$idImporter"), 1);
if($ci_coface){
  $n_Imp = odbc_result(odbc_exec($dbSisSeg,
				 "select n_Imp from Importador where c_Coface_Imp='$ci_coface' and i_Seg=$i_Seg"),
		       1);
}
$x = odbc_exec($dbSisSeg,
	       "select n_Sinistro from Aviso_Sinistro
                where n_Apolice=$n_Apolice and i_Seg=$i_Seg
                and n_Sucursal=$n_Sucursal and n_Ramo=$n_Ramo and n_Imp=$n_Imp");
$n_Sinistro = odbc_result($x, 1);
//----------------------------------------------------------------------------------------------------

  $cur=odbc_exec($db, "SELECT id, status, date, value, juro, indenizacao, nSinistro, limCredit FROM Sinistro WHERE id = $idSinistro");

//$numSinistro = odbc_result($cur, 'nSinistro');
  $numSinistro = $n_Sinistro;
  $status = odbc_result($cur, 'status');
  $date = odbc_result($cur, 'date');
  $sinist = odbc_result($cur, 'value');
  $juros = odbc_result($cur, 'juro');
  //$inden = odbc_result($cur, 'indenizacao');
  $nSinistro = odbc_result($cur, 'nSinistro');
  $limCredit = odbc_result($cur, 'limCredit');

  $cur=odbc_exec($db,  "SELECT SUM(r.value)
FROM
  Inform inf1
  JOIN Insured ins ON (inf1.idInsured = ins.id)
  JOIN Inform inf2 ON (inf2.idInsured = ins.id)
  JOIN Importer imp ON (imp.idInform = inf2.id)
  JOIN Sinistro s ON (s.idImporter = imp.id)
  JOIN Ressarcimento r ON (r.idSinistro = s.id)
WHERE
  inf1.id = $idInform");
  $ressarcimento = odbc_result($cur, 1);

  $query = "
     SELECT SUM (s.indenizacao) FROM
     Sinistro s
     JOIN Importer imp ON (imp.id = s.idImporter)
     JOIN Inform inf ON (inf.id = imp.idInform)
     WHERE inf.id = $idInform
  ";
  $ind = odbc_exec($db, $query);
  //echo "<pre>$query</pre>";


  $inden = odbc_result($ind, 1);

if($status == 8){
  $vmi = odbc_exec($db, "SELECT vmi FROM Sinistro WHERE id = $idSinistro");
  $valorVMI = odbc_result($vmi, 1);
}else{
  if ($inden){
    $valorVMI = (($premio_minimo * $limPag) - $inden + $ressarcimento);       // valor indenizavel
  } else {
    $valorVMI = (($premio_minimo * $limPag) + $ressarcimento);       // valor indenizavel
  }
}

$cur = odbc_exec($db,"SELECT valueAbt, valueCob FROM SinistroDetails WHERE idSinistro = $idSinistro");
$i = 0;
$valueTotal = 0;
$valueCobTotal = 0;
while (odbc_fetch_row($cur)) {
  $i++;
  $valueAbt = odbc_result($cur, 'valueAbt');
  $valueTotal = $valueTotal + $valueAbt;
  $valueCob1 = odbc_result($cur, 'valueCob');
  $valueCobTotal = $valueCobTotal + $valueCob1;
}

$cur = odbc_exec($db,"SELECT value FROM Recuperacao WHERE idSinistro = $idSinistro");
$i = 0;
$totalRecup = 0;
while (odbc_fetch_row($cur)) {
  $i++;
  $valueR = odbc_result($cur, 'value');
  $totalRecup = $totalRecup + $valueR;
}

$valorPago = $inden + $totalRecup;
$perdas = $valueTotal + $juros - $totalRecup;
$valorAvisado = $valueTotal - $totalRecup;

$q = "SELECT max (cc.credit)
FROM
  Inform inf1
  JOIN Insured ins ON (inf1.idInsured = ins.id)
  JOIN Inform inf2 ON (inf2.idInsured = ins.id)
  JOIN Importer imp ON (imp.idInform = inf2.id)
  JOIN ChangeCredit cc ON (cc.idImporter = imp.id)
WHERE
  inf1.id = $idInform AND
  imp.c_Coface_Imp = '$c_Coface_Imp'";
$cur = odbc_exec($db, $q);
$limiteC = odbc_result($cur, 1);
?>

<table border="0" cellSpacing=0 cellpadding="2" width="98%" align="center">
<FORM action="<?php echo $root;?>role/sinistro/Sinistro.php" method="post" name="teste">
<input type=hidden name="comm" value="atualizaDados">
<input type=hidden name="idInform" value="<?php echo $idInform; ?>">
<input type=hidden name="idImporter" value="<?php echo $idImporter; ?>">
<input type=hidden name="idSinistro" value="<?php echo $idSinistro; ?>">
<input type=hidden name="nSinistro" value="<?php echo $numSinistro; ?>">
  <TR>
    <td colspan=4 align="center"><h3>Aviso de Sinistro</h3></td>
  </TR>
  <!--TR>
    <td colspan=4>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=4 align="center">
<?php switch($status){
            case 2 : echo "Aviso de Sinistro"; break;
            case 3 : echo "Sinistro"; break;
            case 4 : echo "Sinistro Suspenso"; break;
            case 5 : echo "Sinistro Cancelado"; break;
            case 6 : echo "Sinistro Não Aceito"; break;
            case 7 : echo "Sinistro Recuperado"; break;
            case 8 : echo "Indenização Aprovada"; break;
          }
?>
    </td>
  </TR-->
  <TR>
    <td colspan=4>&nbsp;</td>
  </TR>
<?php if($msgAtualiza){?>
  <TR>
    <td colspan=4 class="verm" align="center"><?php echo $msgAtualiza;?></td>
  </TR>
  <TR>
    <td colspan=4>&nbsp;</td>
  </TR>
<?php }
    $query = "SELECT inf.name, imp.name FROM Inform inf JOIN Importer imp ON (imp.idInform = inf.id) WHERE imp.id = $idImporter";
    $cur = odbc_exec($db,$query);
?>
  <TR>
    <td width="30%">Exportador:</td>
    <td colspan=3><span class="texto"><?php echo odbc_result($cur, 1);?></span></td>
  </TR>
  <TR>
    <td>Importador: </td>
    <td colspan=3><span class="texto"><?php echo odbc_result($cur, 2);?></span></td>
  </TR>
<?php if($numSinistro && $status != 2){?>
  <TR>
    <td>Número do Sinistro: </td>
    <td colspan=3><span class="texto"><?php echo $numSinistro; ?></span></td>
  </TR>
<?php }?>
  <TR>
    <td>Status: </td>
    <td colspan=3><span class="texto">
<?php switch($status){
            case 2 : echo "Aviso"; break;
            case 3 : echo "Sinistro"; break;
            case 4 : echo "Suspenso"; break;
            case 5 : echo "Cancelado"; break;
            case 6 : echo "Não Aceito"; break;
            case 7 : echo "Recuperado"; break;
            case 8 : echo "Inden. Aprovada"; break;
          }
?>
</span></td>
  </TR>
  <!--TR>
    <td colspan=3>Valor das Perdas: </td>
    <td colspan=5><span class="texto"><?php echo number_format(($perdas),2,",","."); ?></span></td>
  </TR-->
  <TR>
    <td width="10%">Valor Avisado: </td>
    <td width="40%"><span class="texto"><?php echo number_format(($valorAvisado),2,",","."); ?></span></td>
    <td width="20%">Limite de Crédito: </td>
    <input type="hidden" name="limiteCredit" value="<?php echo number_format(($limiteC),2,",","."); ?>">
    <?php if ($limCredit){?>
    <td width="30%"><input type="text" value="<?php echo number_format(($limCredit),2,",","."); ?>" name="limiteC" onBlur="checkDecimals(this, this.value);" class="caixa"></td>
    <?php }else{?>
    <td width="30%"><input type="text" value="<?php echo number_format(($limiteC),2,",","."); ?>" name="limiteC" onBlur="checkDecimals(this, this.value);" class="caixa"></td>
    <?php }?>
  </TR>
  <TR>
    <td>Valor Coberto: </td>
    <td colspan=3><span class="texto">
<?php if($valueCobTotal > $credit){ 
   echo number_format(($credit),2,",",".");
}else{ 
   echo number_format(($valueCobTotal),2,",",".");
}
?></span></td>
  </TR>
<?php if($jurosOk == 1 ){?>
  <TR>
    <td>Valor dos Juros: </td>
    <td colspan=><input type="text" value="<?php echo number_format(($juros),2,",","."); ?>" name="juro" onBlur="checkDecimals(this, this.value);" class="caixa"></td>
  </TR>
<?php }else{?>
  <input type=hidden name="juro" value="0,00">
<?php }?>
  <TR>
    <td>Valor do Sinistro: </td>
    <td colspan=3><span class="texto">
<?php
  $valorSinistro = $valueCobTotal + $juros;
  
  if(($valorSinistro > $credit) || ($valorSinistro > $valorVMI)){ 
     if($valorVMI < $credit){
        echo number_format(($valorVMI),2,",",".");
     }else{
        echo number_format(($credit),2,",",".");
     }
  }else{
     echo number_format(($valorSinistro),2,",",".");
  }

//else{
//   if ($credit < $valueTotal AND $credit != ""){
//      echo number_format(($credit),2,",",".");
//      $valorInd = (($credit * 85) / 100);
//   }else{
//      echo number_format(($valueTotal),2,",",".");
//      $valorInd = (($valueTotal * 85) / 100);
//   }
//}
?> 
     </span></td>
  </TR>
  <TR>
    <td nowrap>Valor Indenizável: </td>
    <td colspan=3><span class="texto">
<?php if($juros){
   $valorInd = ((($valueCobTotal + $juros) * 85) / 100);
}else{
   $valorInd = (($valueCobTotal * 85) / 100);
}
if(($valorInd > $credit) || ($valorInd > $valorVMI)){ 
   if($valorVMI < $credit){
     echo number_format(($valorVMI),2,",",".");
   }else{
     echo number_format(($credit),2,",",".");
   }
}else{
   echo number_format(($valorInd),2,",",".");
}
?></span></td>
  </TR>
<?php if($status == 8){?>
  <TR>
    <td>Indenização Aprovada: </td>
    <td colspan=3><span class="texto"><?php echo number_format(($inden),2,",","."); ?></span></td>
  </TR>
<?php }?>
  <TR>
    <td>VMI: </td>
    <td colspan=3><span class="texto"><?php echo number_format(($valorVMI),2,",","."); ?></span></td>
  </TR>
  <TR>
    <td colspan=4>&nbsp;</td>
  </TR>
<?php if (($status != 5) && ($status != 6) && ($status != 7) && ($status != 8)){ ?>
  <TR>
    <td colspan=4 align="center"><input type="submit" value="Atualizar" class="servicos"></td>
  </TR>
<?php }?>
</form>
<?php $sql = "SELECT sd.id, sd.id, imp.c_Coface_Imp FROM Importer imp
        JOIN Sinistro sd ON (sd.idImporter = imp.id)
        WHERE imp.c_Coface_Imp = $c_Coface_Imp AND imp.id <> $idImporter";
  //echo $sql;
  $cof = odbc_exec($db, $sql);
  if (odbc_fetch_row($cof)) {
?>
<FORM action="<?php echo $root;?>role/sinistro/Sinistro.php" method="post" name="cobertos">
<input type=hidden name="comm" value="detalhesSinistro">
<input type=hidden name="idInform" value="<?php echo $idInform; ?>">
<input type=hidden name="idImporter" value="<?php echo $idImporter; ?>">
  <TR>
    <td colspan=4>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=4 align="center">Histórico de Sinistro:
      <?php // Monta a lista de Sinistros
        $sel = 0;
	$name = "idSinistro";
        $empty = "Selecione um Sinistro";
        $acao = "onChange=seleciona(this)";
        require ("../../interf/Select.php");
      ?>
    </td>
  </TR>
</form>
<?php }?>
</table>


<?php if (($status != 5) && ($status != 6) && ($status != 8) && ($status != 7)){ ?>
<FORM action="<?php echo $root;?>role/sinistro/Sinistro.php" method="post" name="cobertos">
<input type=hidden name="comm" value="valoresCobertos">
<input type=hidden name="idInform" value="<?php echo $idInform; ?>">
<input type=hidden name="idImporter" value="<?php echo $idImporter; ?>">
<input type=hidden name="idSinistro" value="<?php echo $idSinistro; ?>">
<input type=hidden name="nSinistro" value="<?php echo $numSinistro; ?>">
<table border="0" cellSpacing=0 cellpadding="2" width="98%" align="center">
  <TR>
    <td colspan=10>&nbsp;</td>
  </TR>
<?php if($erro){?>
  <TR>
    <td colspan=10 class="verm" align="center"><span class="textoBold">Problema na(s) seguinte(s) Fatura(s):</span> <?php echo $erro;?><span class="textoBold">.</span> <br>O Valor Coberto deve ser menor ou igual ao Valor em Aberto.</td>
  </TR>
  <TR>
    <td colspan=10>&nbsp;</td>
  </TR>
<?php }?>
<?php if($msgA){
?>
  <TR>
    <td colspan=10 class="verm" align="center"><br><?php echo $msgA; ?></td>
  </TR>
  <TR>
    <td colspan=10>&nbsp;</td>
  </TR>
<?php } ?>
  <TR>
    <td colspan=10 align="center">Faturas Não Pagas</td>
  </TR>
  <TR>
    <td class=bgAzul align=center>Nº Fatura</td>
    <td class=bgAzul align=center>DVE</td>
    <td class=bgAzul align=center>Data de <br>Emb.</td>
    <td class=bgAzul align=center>Data de <br>Venc.</td>
    <td class=bgAzul align=center>R</td>
    <td class=bgAzul align=center>Valor da <br>Fatura</td>
    <td class=bgAzul align=center>Valor Pago</td>
    <td class=bgAzul align=center>Valor em Aberto</td>
    <td class=bgAzul align=center>Valor <br>Coberto</td>
    <td class=bgAzul align=center>&nbsp;</td>
  </TR>
<?php $query = "SELECT * FROM SinistroDetails WHERE idSinistro = $idSinistro ORDER BY dateEmb";
    $cur = odbc_exec($db,$query);
    $i = 0;
    $valueTotal = 0;
    $cob = 0;
    while (odbc_fetch_row($cur)) {
      $i++;
      $idSinDet = odbc_result($cur, 1); 
      $dve = odbc_result($cur,3); 
      $mod = odbc_exec($db, "SELECT modalidade FROM DVEDetails WHERE id = $dve");
      $modalidade = odbc_result($mod, 1);
      $dateEmb = odbc_result($cur,5); 
      $dateVenc = odbc_result($cur,6);
      $valuePag = odbc_result($cur, 7);
      $valueFat = odbc_result($cur, 8);
      $valueAbt = odbc_result($cur, 9);
      $valueCoberto = odbc_result($cur, 11);
      $valueTotal = $valueTotal + $valueAbt;
      if ($totalRecup >= $valueTotal){
         $recup = "<img src=../../../src/images/yes.gif alt=\"Recuperado\">";
      }else{
         $recup = "<img src=../../../src/images/not.gif alt=\"Não Recuperado\">";
      }
?>
  <TR <?php echo ((($i % 2) != 0) ? " bgcolor=\"#ffffff\"" : " bgcolor=\"#e9e9e9\"")?>>
    <td align=center class="texto"><?php if($modalidade == 2) echo "*"; $coligada = 1;?><?php echo odbc_result($cur,4); ?></td>
    <td align=center class="texto"><?php if($dve != 0) echo "sim"; else echo "não"; ?></td>
    <td align=center class="texto"><?php echo substr($dateEmb,8,2)."/".substr($dateEmb,5,2)."/".substr($dateEmb,2,2); ?></td>
    <td align=center class="texto"><?php echo substr($dateVenc,8,2)."/".substr($dateVenc,5,2)."/".substr($dateVenc,2,2); ?></td>
    <td align=center class="texto"><?php echo $recup; ?></td>
    <td align=right class="texto"><?php echo number_format($valueFat,2,",","."); ?> &nbsp;</td>
    <td align=right class="texto"><?php echo number_format($valuePag,2,",","."); ?> &nbsp;</td>
    <td align=right class="texto"><?php echo number_format($valueAbt,2,",","."); ?> &nbsp;</td>
    <td align=center class="texto">
       <?php if($dve != 0){ 
          $vc = 1;
          $cob++;
       ?>
          <input type="hidden" name="numFat<?php echo $cob;?>" value="<?php echo odbc_result($cur,4); ?>">
          <input type="hidden" name="idSinDet<?php echo $cob;?>" value="<?php echo $idSinDet;?>">
          <input type="hidden" name="valoraberto<?php echo $cob;?>" value="<?php echo $valueAbt; ?>">
          <?php if(!$valueCoberto){?>
          <input type="text" size="15" class="caixa" name="valueCob<?php echo $cob;?>" value="<?php echo number_format($valueAbt,2,",",".");?>" onBlur="checkDecimals(this, this.value);">
          <?php }else{?>
          <input type="text" size="15" class="caixa" name="valueCob<?php echo $cob;?>" value="<?php echo number_format($valueCoberto,2,",",".");?>" onBlur="checkDecimals(this, this.value);">
          <?php }?>            
       <?php }else{?>-<?php }?>
    </td>
    <td class="texto" align=center nowrap><a href="<?php echo $root;?>role/sinistro/Sinistro.php?comm=view&idSinDetA=<?php echo $idSinDet;?>&idInform=<?php echo $idInform; ?>&idImporter=<?php echo $idImporter; ?>&idSinistro=<?php echo $idSinistro; ?>&alter=1" title="Alterar essa Fatura?">A</a> | <a href="javascript:onClick=exclui(<?php echo $idSinDet; ?>)" title="Excluir essa Fatura?"><!--a href="<?php echo $root;?>role/client/Client.php?comm=RemFatura&idSinDet=<?php echo $idSinDet;?>&idInform=<?php echo $idInform; ?>&idImporter=<?php echo $idImporter; ?>&idSinistro=<?php echo $idSinistro; ?>" title="Excluir?"-->E</a></td>
  </TR>
<?php } // while
  if ($i == 0) {

?>

  <TR class="bgCinza">
    <TD align="center" colspan=10 class="bgCinza">Nenhum Dado Cadastrado</TD>
  </TR>

<?php }
?>
  <TR>
    <td class=bgAzul align=right colspan=7>Total (em aberto):</td>
    <td align=right class="texto"><?php echo number_format($valueTotal,2,",","."); ?> &nbsp;</td>
    <td align=center>&nbsp;</td>
    <td align=center>&nbsp;</td>
  </TR>
<?php $cor = odbc_exec($db, "SELECT value FROM Recuperacao WHERE idSinistro = $idSinistro");
      $i = 0;
      $valueT = 0;
      while (odbc_fetch_row($cor)) {
        $i++;
        $valueR = odbc_result($cor,1); 
        $valueT = $valueT + $valueR;
      }  
?>
  <TR>
    <td class=bgAzul align=right colspan=7>Total Recuperado:</td>
    <td align=right class="texto"><?php echo number_format($valueT,2,",","."); ?> &nbsp;</td>
    <td align=center>&nbsp;</td>
    <td align=center>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=7 class="texto">
R = Fatura Recuperada?<br>
<img src=../../../src/images/yes.gif alt="Recuperado"> Valor Recuperado<br>
<img src=../../../src/images/not.gif alt="Não Recuperado"> Valor Não Recuperado<br>
<?php if($coligada){?><br>* Exportação via Coligada<?php }?>
    </td>
    <td class="texto" align="right">
A = <br>
E = 
    </td>
    <td colspan=2 class="texto">
Alterar Fatura<br>
Excluir Fatura
    </td>
  </TR>
  <TR>
    <td colspan=10>&nbsp;</td>
  </TR>
<?php //if($msgA){
?>
  <!--TR>
    <td colspan=10 class="verm" align="center"><br><?php echo $msgA; ?></td>
  </TR-->
<?php //} ?>
<?php if ($vc){
?>
  <input type="hidden" name="cob" value="<?php echo $cob; ?>">
  <TR>
    <td colspan=10 align="center"><input type="submit" value="Atualizar Valores Cobertos" class="servicos"><br><br></td>
  </TR>
<?php }?>
</form>
</table>
<table border="0" cellSpacing=0 cellpadding="2" width="98%" align="center">
  <TR>
    <td colspan=6 align="center">Faturas declaradas na DVE (não avisadas)</td>
  </TR>
  <!-- início da DVE -->
  <TR>
    <td class=bgAzul align=center WIDTH="10%">Nº Fatura</td>
    <td class=bgAzul align=center>Data de Embarque</td>
    <td class=bgAzul align=center>Data de Vencimento</td>
    <td class=bgAzul align=center>Valor da Fatura</td>
    <td class=bgAzul align=center>Valor Pago</td>
    <td class=bgAzul align=center>&nbsp;</td>
  </TR>
<?php $cur=odbc_exec($db,
	"SELECT d.inicio, d.periodo,
             dd.embDate, dd.vencDate, dd.fatura, dd.totalEmbarcado, dd.id
	FROM DVE d JOIN DVEDetails dd ON (dd.idDVE = d.id)
	WHERE d.idInform = $idInform AND dd.idImporter = $idImporter");
  $i = 0;
  while (odbc_fetch_row($cur)) {
    $i++;
    $dateEmb = odbc_result($cur,3); 
    $dateVenc = odbc_result($cur,4);
    $valor = odbc_result($cur, 6);
    $numFat = odbc_result($cur,5);
    $idDVE = odbc_result($cur,7);

    $query = "SELECT valuePag, valueAbt FROM SinistroDetails WHERE idDVE = $idDVE";
    $sol = odbc_exec($db,$query);
    if (!odbc_fetch_row($sol)) {
       $aparece = 1;
?>
<FORM action="<?php echo $root;?>role/sinistro/Sinistro.php#sinistro" method="post" name="gera_aviso<?php echo $i; ?>">
<input type=hidden name="comm" value="inserirFatura">
<input type=hidden name="idInform" value="<?php echo $idInform; ?>">
<input type=hidden name="idImporter" value="<?php echo $idImporter; ?>">
<input type=hidden name="idSinistro" value="<?php echo $idSinistro; ?>">
<input type=hidden name="idDVE" value="<?php echo $idDVE; ?>">
<input type=hidden name="action" value="valor">
<input type=hidden name="i" value="<?php echo $i; ?>">
  <TR <?php echo ((($i % 2) != 0) ? " bgcolor=\"#ffffff\"" : " bgcolor=\"#e9e9e9\"")?>>
    <input type="hidden" name="valueFat" value="<?php echo number_format($valor,2,",","."); ?>">
    <input type=hidden name="dateEmb" value="<?php echo $dateEmb; ?>">
    <input type=hidden name="dateVenc" value="<?php echo $dateVenc; ?>">
    <input type=hidden name="numFat" value="<?php echo $numFat; ?>">
    <td align=center class="texto"><?php echo $numFat; ?></td>
    <td align=center class="texto"><?php echo substr($dateEmb,8,2)."/".substr($dateEmb,5,2)."/".substr($dateEmb,0,4); ?></td>
    <td align=center class="texto"><?php echo substr($dateVenc,8,2)."/".substr($dateVenc,5,2)."/".substr($dateVenc,0,4); ?></td>
    <td align=center class="texto"><?php echo number_format($valor,2,",","."); ?></td>
    <td align=center class="texto"><INPUT class=caixa size=15 name=valuePag onBlur="checkDecimals(this, this.value);"></td>
    <td align=center class="texto"><INPUT class=servicos type="submit" value="OK" name=button1></td>
  </TR>
</form>

<?php } // if
  } // while
  if ($i == 0) {
?>

  <TR class="bgCinza">
    <TD align="center" colspan=6 class="bgCinza">Não Existem Faturas</TD>
  </TR>
<?php }?>
</table>


<table border="0" cellSpacing=0 cellpadding="2" width="98%" align="center">
  <TR>
    <td colspan=3>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=6>Faturas não declaradas na DVE</td>
  </TR>
<?php
if($alter == 1){

   $q = "SELECT dateEmb, dateVenc, valueFat, valuePag, valueAbt, numFat FROM SinistroDetails WHERE id = $idSinDetA";
   //echo $q;
   $alt = odbc_exec($db, $q);
   $dateEmbA = odbc_result($alt, 1);
   $dateVencA = odbc_result($alt, 2);
   $valueFatA = odbc_result($alt, 3);
   $valuePagA = odbc_result($alt, 4);
   $valueAbtA = odbc_result($alt, 5);
   $numFatA = odbc_result($alt, 6);
?>
<FORM action="<?php echo $root;?>role/sinistro/Sinistro.php" method="post" name="incluir">
<input type=hidden name="comm">
<input type=hidden name="dateEmbA" value="<?php echo $dateEmbA; ?>">
<input type=hidden name="dateVencA" value="<?php echo $dateVencA; ?>">
<input type=hidden name="valueFatA" value="<?php echo $valueFatA; ?>">
<input type=hidden name="valuePagA" value="<?php echo $valuePagA; ?>">
<input type=hidden name="valueAbtA" value="<?php echo $valueAbtA; ?>">
<input type=hidden name="numFatA" value="<?php echo $numFatA; ?>">
<input type=hidden name="idInform" value="<?php echo $idInform; ?>">
<input type=hidden name="idImporter" value="<?php echo $idImporter; ?>">
<input type=hidden name="idSinistro" value="<?php echo $idSinistro; ?>">
<input type=hidden name="nSinistro" value="<?php echo $numSinistro; ?>">
<input type=hidden name="idSinDetA" value="<?php echo $idSinDetA; ?>">
<input type=hidden name="action" value="alterar">
<input type=hidden name=fieldfocus value="numFat">
<input type=hidden name=formfocus value="formulario">
<input type=hidden name=dateEmb value="">
<input type=hidden name=dateVenc value="">
<input type=hidden name="Ndve" value="1">
  <TR>
    <td align=center>Nº Fatura<br><INPUT class=caixa size=4 name="numFat" value="<?php echo $numFatA;?>"></td>
    <td align=center>Data de Embarque<br>
<input type=text class=caixa size=2 name=dataEmbDia value="<?php echo substr($dateEmbA,8,2);?>" maxlength=2 onkeyup="proximo(this, 2, this.form.dataVencMes, 31)"> / 
<input type=text class=caixa size=2 name=dataEmbMes value="<?php echo substr($dateEmbA,5,2);?>" maxlength=2 onkeyup="proximo(this, 2, this.form.dataVencAno, 12)"> / 
<input type=text class=caixa size=4 name=dataEmbAno value="<?php echo substr($dateEmbA,0,4);?>" maxlength=4 onkeyup="proximo(this, 4, this.form.valorEmb, 9999)">
<!--INPUT class=caixa size=10 name="dateEmb" value="<?php echo substr($dateEmbA,8,2)."/".substr($dateEmbA,5,2)."/".substr($dateEmbA,2,2); ?>" onFocus="blur()"> <A HREF="javascript:showCalendar(document.incluir.dateEmb)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"></A--></td>
    <td align=center>Data de Vencimento<br>
<input type=text class=caixa size=2 name=dataVencDia value="<?php echo substr($dateVencA,8,2);?>" maxlength=2 onkeyup="proximo(this, 2, this.form.dataVencMes, 31)"> / 
<input type=text class=caixa size=2 name=dataVencMes value="<?php echo substr($dateVencA,5,2);?>" maxlength=2 onkeyup="proximo(this, 2, this.form.dataVencAno, 12)"> / 
<input type=text class=caixa size=4 name=dataVencAno value="<?php echo substr($dateVencA,0,4);?>" maxlength=4 onkeyup="proximo(this, 4, this.form.valueFat, 9999)">
<!--INPUT class=caixa size=10 name="dateVenc" value="<?php echo substr($dateVencA,8,2)."/".substr($dateVencA,5,2)."/".substr($dateVencA,2,2); ?>" onFocus="blur()"> <A HREF="javascript:showCalendar(document.incluir.dateVenc)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"></A--></td>
  </TR>
  <TR>
    <td align=center>Valor da Fatura<br><INPUT class=caixa size=10 name="valueFat" value="<?php echo number_format($valueFatA,2,",","."); ?>" onBlur="checkDecimals(this, this.value);calc(this.form)"></td>
    <td align=center>Valor Pago<br><INPUT class=caixa size=10 name="valuePag" value="<?php echo number_format($valuePagA,2,",","."); ?>" onBlur="checkDecimals(this, this.value);calc(this.form)"></td>
    <td align=center>Valor em Aberto<br><INPUT class=caixa size=10 name="valueAbt" value="<?php echo number_format($valueAbtA,2,",","."); ?>" onBlur="checkDecimals(this, this.value)" onFocus="blur()"></td>
  </TR>
  <TR>
    <td align=center colspan="3"><INPUT class=servicos type="button" value="Alterar" name=button1 onClick="manda(this.form, 'AltFatura')"></td>
  </TR>
</form>
<?php }else{ 
?>
<FORM action="<?php echo $root;?>role/sinistro/Sinistro.php" method="post" name="formulario">
<input type=hidden name="comm">
<input type=hidden name="idInform" value="<?php echo $idInform; ?>">
<input type=hidden name="idImporter" value="<?php echo $idImporter; ?>">
<input type=hidden name="idSinistro" value="<?php echo $idSinistro; ?>">
<input type=hidden name="nSinistro" value="<?php echo $numSinistro; ?>">
<input type=hidden name="idDVE" value="0">
<input type=hidden name="action" value="incluir">
<input type=hidden name=fieldfocus value="numFat">
<input type=hidden name=formfocus value="formulario">
<input type=hidden name=dateEmb value="">
<input type=hidden name=dateVenc value="">
<input type=hidden name="Ndve" value="1">
  <TR>
    <td align=center>Nº Fatura<br><INPUT class=caixa size=4 name="numFat"></td>
    <td align=center>Data de Embarque<br>
<input type=text class=caixa size=2 name=dataEmbDia maxlength=2 onkeyup="proximo(this, 2, this.form.dataEmbMes, 31)"> / 
<input type=text class=caixa size=2 name=dataEmbMes maxlength=2 onkeyup="proximo(this, 2, this.form.dataEmbAno, 12)"> / 
<input type=text class=caixa size=4 name=dataEmbAno maxlength=4 onkeyup="proximo(this, 4, this.form.dataVencDia, 9999)">
<!--INPUT class=caixa size=10 name="dateEmb" onFocus="blur()"> <A HREF="javascript:showCalendar(document.incluir.dateEmb)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"--></A></td>
    <td align=center>Data de Vencimento<br>
<input type=text class=caixa size=2 name=dataVencDia maxlength=2 onkeyup="proximo(this, 2, this.form.dataVencMes, 31)"> / 
<input type=text class=caixa size=2 name=dataVencMes maxlength=2 onkeyup="proximo(this, 2, this.form.dataVencAno, 12)"> / 
<input type=text class=caixa size=4 name=dataVencAno maxlength=4 onkeyup="proximo(this, 4, this.form.valueFat, 9999)">
<!--INPUT class=caixa size=10 name="dateVenc" onFocus="blur()"> <A HREF="javascript:showCalendar(document.incluir.dateVenc)"><img src="../../../src/images/calendario.gif" width="24" height="20" border="0" alt="Clique para Incluir uma Data"></A--></td>
  </TR>
  <TR>
    <td align=center>Valor da Fatura<br><INPUT class=caixa size=10 name="valueFat" onBlur="checkDecimals(this, this.value);calc(this.form)"></td>
    <td align=center>Valor Pago<br><INPUT class=caixa size=10 name="valuePag" onBlur="checkDecimals2(this, this.value);calc(this.form)"></td>
    <td align=center>Valor em Aberto<br><INPUT class=caixa size=10 name="valueAbt" onBlur="checkDecimals(this, this.value)" onFocus="blur()"> <INPUT class=servicos type="button" value="OK" name=button1 onClick="manda(this.form, 'inserirFatura')"></td>
  </TR>
</form>
<?php }?>
  <TR>
    <td colspan=3>&nbsp;</td>
  </TR>
</table>
<?php }else{?>
<table border="0" cellSpacing=0 cellpadding="2" width="98%" align="center">
  <TR>
    <td colspan=9>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=9 align="center">Faturas Não Pagas</td>
  </TR>
  <TR>
    <td class=bgAzul align=center>Nº Fatura</td>
    <td class=bgAzul align=center>DVE</td>
    <td class=bgAzul align=center>Data de Emb.</td>
    <td class=bgAzul align=center>Data de Venc.</td>
    <td class=bgAzul align=center>R</td>
    <td class=bgAzul align=center>Valor da Fatura</td>
    <td class=bgAzul align=center>Valor Pago</td>
    <td class=bgAzul align=center>Valor em Aberto</td>
    <td class=bgAzul align=center>Valor Coberto</td>
  </TR>
<?php $query = "SELECT * FROM SinistroDetails WHERE idSinistro = $idSinistro ORDER BY dateEmb";
    $cur = odbc_exec($db,$query);
    $i = 0;
    $valueTotal = 0;
    $cob = 0;
    while (odbc_fetch_row($cur)) {
      $i++;
      $idSinDet = odbc_result($cur, 1); 
      $dve = odbc_result($cur,3); 
      $mod = odbc_exec($db, "SELECT modalidade FROM DVEDetails WHERE id = $dve");
      $modalidade = odbc_result($mod, 1);
      $dateEmb = odbc_result($cur,5); 
      $dateVenc = odbc_result($cur,6);
      $valuePag = odbc_result($cur, 7);
      $valueFat = odbc_result($cur, 8);
      $valueAbt = odbc_result($cur, 9);
      $valueCoberto = odbc_result($cur, 11);
      $valueTotal = $valueTotal + $valueAbt;
      if ($totalRecup > $valueTotal){
         $recup = "<img src=../../../src/images/yes.gif alt=\"Recuperado\">";
      }else{
         $recup = "<img src=../../../src/images/not.gif alt=\"Não Recuperado\">";
      }
?>
  <TR <?php echo ((($i % 2) != 0) ? " bgcolor=\"#ffffff\"" : " bgcolor=\"#e9e9e9\"");?>>
    <td align=center class="texto"><?php if($modalidade == 2) echo "*"; $coligada = 1;?><?php echo odbc_result($cur,4); ?></td>
    <td align=center class="texto"><?php if($dve != 0) echo "sim"; else echo "não"; ?></td>
    <td align=center class="texto"><?php echo substr($dateEmb,8,2)."/".substr($dateEmb,5,2)."/".substr($dateEmb,2,2); ?></td>
    <td align=center class="texto"><?php echo substr($dateVenc,8,2)."/".substr($dateVenc,5,2)."/".substr($dateVenc,2,2); ?></td>
    <td align=center class="texto"><?php echo $recup; ?></td>
    <td align=right class="texto"><?php echo number_format($valueFat,2,",","."); ?> &nbsp;</td>
    <td align=right class="texto"><?php echo number_format($valuePag,2,",","."); ?> &nbsp;</td>
    <td align=right class="texto"><?php echo number_format($valueAbt,2,",","."); ?> &nbsp;</td>
    <td align=center class="texto">
       <?php if($dve != 0){ 
          $vc = 1;
          $cob++;
       ?>
          <input type="hidden" name="idSinDet<?php echo $cob;?>" value="<?php echo $idSinDet;?>">
          <? if(!$valueCoberto){?>
            <?php echo number_format($valueAbt,2,",","."); ?>
          <?php }else{?>
            <?php echo number_format($valueCoberto,2,",",".");?>
          <?php }?>            
       <?php }else{ ?>-<?php } ?>
    </td>
  </TR>
<?php } // while
  if ($i == 0) {

?>

  <TR class="bgCinza">
    <TD align="center" colspan=9 class="bgCinza">Nenhum Dado Cadastrado</TD>
  </TR>

<?php }
?>
  <TR>
    <td class=bgAzul align=right colspan=7>Total (em aberto):</td>
    <td align=right class="texto"><?php echo number_format($valueTotal,2,",","."); ?> &nbsp;</td>
    <td align=right class="texto">&nbsp;</td>
  </TR>
<?php $cor = odbc_exec($db, "SELECT value FROM Recuperacao WHERE idSinistro = $idSinistro");
      $i = 0;
      $valueT = 0;
      while (odbc_fetch_row($cor)) {
        $i++;
        $valueR = odbc_result($cor,1); 
        $valueT = $valueT + $valueR;
      }  
?>
  <TR>
    <td class=bgAzul align=right colspan=7>Total Recuperado:</td>
    <td align=right class="texto"><?php echo number_format($valueT,2,",","."); ?> &nbsp;</td>
    <td align=right class="texto">&nbsp;</td>
  </TR>
  <TR>
    <td colspan=9 class="texto">
R = Fatura Recuperada?<br>
<img src=../../../src/images/yes.gif alt="Recuperado"> Valor Recuperado<br>
<img src=../../../src/images/not.gif alt="Não Recuperado"> Valor Não Recuperado<br>
<?php if($coligada){?><br>* Exportação via Coligada<?php }?>
    </td>
  </TR>
  <TR>
    <td colspan=9>&nbsp;</td>
  </TR>
<?php if($msgA){
?>
  <TR>
    <td colspan=9 class="verm" align="center"><br><?php echo $msgA; ?></td>
  </TR>
</table>
<?php } ?>
<?php }?>

<TABLE width ="98%" cellspacing=0 cellpadding=2 border=0 align="center">
  <TR>
    <td colspan=8>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=8 align="center">Histórico de Crédito</td>
  </TR>
    <TR class="bgAzul">
      <th class="textoBold">Data de Validade</th>
      <th class="textoBold" align=center>Solicitado<br>US$ Mil</th>
      <th class="textoBold" align=center>Concedido<br>US$ Mil</th>
      <th class="textoBold" align=center>Venc. <br> Crédito Temp</th>
      <th class="textoBold" align=center>Crédito Temp.<br>US$ Mil</th>
      <th class="textoBold" align=center>Análise</th>
      <th class="textoBold" align=center>Monit.</th>
      <th class="textoBold" align=center>Status</th>
    </tr>
  <?php $cur = odbc_exec (
	$db,
	" SELECT creditDate, creditSolic, credit, creditTemp, limTemp, analysis, monitor, state".
	" FROM ChangeCredit".
	" WHERE idImporter = $idImporter".
	" ORDER BY stateDate"
	);
  $count = 0;
  $v_Sinistro = 0;
  while (odbc_fetch_row ($cur)) {

    $state = odbc_result ($cur, 'state');
    //echo "<br>>>> [$state] <<<";
    $stateString = "Nulo";
    if ($state == 1)
      $stateString = "Novo";
    else if ($state == 2)
      $stateString = "Solicitado";
    else if ($state == 3)
      $stateString = "Demanda Novo";
    else if ($state == 4)
      $stateString = "Reativado";
    else if ($state == 6)
      $stateString = "Concedido";
    else if ($state == 7)
      $stateString = "Inativado";
    else if ($state == 8)
      $stateString = "Recusado";
    

    $count ++;

    $data = odbc_result($cur, 1);
    $data = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,2,2);

    $dataCred = $data;

    $data = odbc_result($cur, 5);
    $dataTemp = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,2,2);
    if ($dataTemp == "//") $dataTemp = "";

    $creditSolic = odbc_result ($cur, "creditSolic");
    if ($creditSolic != '' && $creditSolic != 0)
      $creditSolic /= 1000;
    else $creditSolic='';

    $credit = odbc_result ($cur, "credit");
    if ($credit != '' && $credit != 0)
      $credit /= 1000;
    else $credit='';

    $creditTemp = odbc_result ($cur, "creditTemp");
    if ($creditTemp != '' && $creditTemp != 0)
      $creditTemp /= 1000;
    else $creditTemp='';
  ?>
     <tr<?php echo ($count % 2) ? "" : " bgcolor=#e9e9e9"; ?>>
       <td align=center class="texto"><?php echo $dataCred; ?>&nbsp;</td>
       <td align=center class="texto"><?php echo $creditSolic == '' ? '' : number_format ($creditSolic,0,',','.'); ?>&nbsp;</td>
       <td align=center class="texto"><?php echo $credit == '' ? '0' : number_format ($credit,0,",","."); ?>&nbsp;</td>
       <td align=center class="texto"><?php echo $dataTemp; ?>&nbsp;</td>
       <td align=center class="texto"><?php echo $creditTemp == '' ? '' : number_format ($creditTemp,0,",","."); ?>&nbsp;</td>
       <td align=center class="texto"><?php echo odbc_result ($cur, "analysis") == 1 ? "Cobrar" : "&nbsp;"; ?></td>
       <td align=center class="texto"><?php echo odbc_result ($cur, "monitor") == 1 ? "Cobrar" : "&nbsp;"; ?></td>
       <td align=center class="texto"><?php echo $stateString; ?></td>
     </tr>
  <?php $tmp = $credit == '' ? '0' : number_format($credit, 0, ",", ".") * 1000;
    if($tmp > $v_Sinistro){
      $v_Sinistro = $tmp;
    }
  } // while
  if ($count == 0){
  ?>
    <tr><th colspan=8 bgcolor=#a4a4a4>Histórico Inexistente</th></tr>
  <?php } // if
  ?>
</table>

<br>
<br>
<br>
<p align="center">
<form action="<?php echo $root;?>role/sinistro/Sinistro.php" method="post">
<input type="hidden" name="comm">
<input type="hidden" name="idInform" value="<?php echo $idInform;?>">
<input type="hidden" name="idImporter" value="<?php echo $idImporter;?>">
<input type="hidden" name="idSinistro" value="<?php echo $idSinistro;?>">
<input type="hidden" name="nSinistro" value="<?php echo $numSinistro;?>">
<input type="hidden" name="idNotification" value="<?php echo $idNotification;?>">
<input type="hidden" name="v_Sinistro" value="<?php echo $v_Sinistro; ?>">
<input type="hidden" name="v_Perda" value="<?php echo $valueTotal; ?>">
<input type="hidden" name="v_Indenizacao" value="<?php echo $v_Sinistro * 0.85; ?>">

<input type=button value="Voltar" onClick="this.form.comm.value='voltar';this.form.submit()" class="servicos">
<?php $q = "SELECT status FROM Sinistro WHERE id = $idSinistro";
    $cur = odbc_exec($db,$q);
    $status = odbc_result($cur, 1);
    //echo $status;

if ($status == 2){ ?>
   <input class="servicos" type=button value="Criar Sinistro" onClick="this.form.comm.value='criarSinistro';this.form.submit()">
   <input class="servicos" type=button value="Não Aceito" onClick="this.form.comm.value='naoAceito';this.form.submit()">
   <input class="servicos" type=button value="Recuperações" onClick="this.form.comm.value='recuperacao';this.form.submit()">
<?php } else if ($status == 3){ ?>
   <input class="servicos" type=button value="Cancelar" onClick="cancela(<?php echo $idSinistro; ?>,<?php echo $idNotification; ?>)">
   <input class="servicos" type=button value="Suspender" onClick="this.form.comm.value='suspenso';this.form.submit()">
   <?php if($valueCobTotal != 0){?>
      <!--input class="servicos" type=button value="Indenização Aprovada" onClick="indeniza(<?php echo $idSinistro; ?>,<?php echo $idNotification; ?>)"-->
      <input class="servicos" type=button value="Indenização Aprovada" onClick="this.form.comm.value='pagInd';this.form.submit()">
   <?php }?>
   <input class="servicos" type=button value="Recuperações" onClick="this.form.comm.value='recuperacao';this.form.submit()">
<?php } else if ($status == 4){ ?>
   <input class="servicos" type=button value="Cancelar" onClick="cancela(<?php echo $idSinistro; ?>,<?php echo $idNotification; ?>)">
   <input class="servicos" type=button value="Término da Suspensão" onClick="this.form.comm.value='criarSinistro';this.form.submit()">
   <?php if($valueCobTotal != 0){?>
     <input class="servicos" type=button value="Indenização Aprovada" onClick="this.form.comm.value='pagInd';this.form.submit()">
   <?php }?>
   <input class="servicos" type=button value="Recuperações" onClick="this.form.comm.value='recuperacao';this.form.submit()">
<?php } else if ($status == 6){ ?>
   <input class="servicos" type=button value="Aviso" onClick="this.form.comm.value='avisoSinistro';this.form.submit()">
<?php } else if ($status == 7){ ?>
   <input class="servicos" type=button value="Recuperações" onClick="this.form.comm.value='recuperacao';this.form.submit()">
<?php } else if ($status == 8){ ?>
   <input class="servicos" type=button value="Recuperações" onClick="this.form.comm.value='recuperacao';this.form.submit()">
<?php } ?>
   <input class="servicos" type=button value="Histórico" onClick="this.form.comm.value='histSinistro';this.form.submit()">

</form> 


<!--
STATUS
1 = criado
2 = enviado
3 = aviso criado
4 = suspenso
5 = cancelado
6 = não aceito
7 = recuperado
8 = indenizado 
-->





<form name="exclui" action="<?php echo $root;?>role/client/Client.php">
<input type=hidden name="comm" value="RemFatura">
<input type=hidden name="idSinDet" value="">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">
<input type=hidden name="idSinistro" value="<?php echo $idSinistro;?>">
<input type=hidden name="idImporter" value="<?php echo $idImporter;?>">
</form>



<script>
function exclui(myIdSinDet) { 
 if (confirm ("Deseja Realmente Excluir essa Fatura?")) {
   document.forms["exclui"].idSinDet.value=myIdSinDet;
   document.forms["exclui"].submit();
 }
}

var f = document.formulario;

function manda(f, c){
  if(confirma(f)){
    f.comm.value = c;
    f.submit();
  }
}

function confirma(f){
  if(f.dataEmbDia.value == ''){
    verErro("Favor preencher a data de embarque");
    f.dataEmbDia.focus();
    return false;  
  }
  if(f.dataEmbMes.value == ''){
    verErro("Favor preencher a data de embarque");
    f.dataEmbMes.focus();
    return false;
  }
  if(f.dataEmbAno.value == ''){
    verErro("Favor preencher a data de embarque");
    f.dataEmbAno.focus();
    return false;
  }
  if(f.dataVencDia.value == ''){
    verErro("Favor preencher a data de vencimento");
    f.dataVencDia.focus();
    return false;
  }
  if(f.dataVencMes.value == ''){
    verErro("Favor preencher a data de vencimento");
    f.dataVencMes.focus();
    return false;
  }
  if(f.dataVencAno.value == ''){
    verErro("Favor preencher a data de vencimento");
    f.dataVencAno.focus();
    return false;
  }

  f.dateEmb.value = f.dataEmbDia.value + '/' + f.dataEmbMes.value + '/' + f.dataEmbAno.value;
  f.dateVenc.value = f.dataVencDia.value + '/' + f.dataVencMes.value + '/' + f.dataVencAno.value;
  return true;
}

function proximo(atual, size, prox, max){
  if(atual.value.length == size){
    if(checknumber(atual, max))
      prox.focus();
  }
}

function checknumber(f, n){
  if(f.value > 0){
    if(f.value > n){
      verErro("Valor inválido: " + f.value);
      f.value = '';
      f.focus();
      return false;
    }
  }else{
    verErro("Valor inválido: " + f.value);
    f.value = '';
    f.focus();
    return false;
  }
  return true;
}

</script>


<form name="cancel" action="<?php echo $root;?>role/sinistro/Sinistro.php">
<input type=hidden name="comm" value="cancelado">
<input type=hidden name="idSinistro" value="">
<input type=hidden name="idNotification" value="">
</form>

<script>
function cancela(myIdSinistro,myIdNotification) { 
if (confirm ("Deseja Realmente Cancelar esse Aviso de Sinistro?")) {
   document.forms["cancel"].idSinistro.value=myIdSinistro;
   document.forms["cancel"].idNotification.value=myIdNotification;
   document.forms["cancel"].submit();
}
}
</script>
