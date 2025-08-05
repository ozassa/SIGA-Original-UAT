<?php //----------------------------------------------------------------------------------------------------
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

$cur=odbc_exec($db, "SELECT txRise, prMTotal, startValidity, endValidity, warantyInterest, limPagIndeniz, contrat, name, state FROM Inform WHERE id = $idInform");

  $nameExp = odbc_result($cur, 'name');
  $statusInf = odbc_result($cur, 'state');
  $prMin = odbc_result($cur, 'prMTotal');
  $taxa_rise = odbc_result($cur, 'txRise');
  $waranty = odbc_result($cur, 'warantyInterest');
  $premio_min = $prMin;
  $ini_vig = odbc_result($cur, 'startValidity');
  $fim_vig = odbc_result($cur, 'endValidity');
  $limPag = odbc_result($cur, 'limPagIndeniz');
  $apol = odbc_result($cur, 'contrat');
  $vmi = $prMin * $limPag; 

$cur=odbc_exec($db, "SELECT Importer.c_Coface_Imp, Importer.name, Country.name, Importer.credit FROM Importer, Country WHERE Importer.id = $idImporter AND Country.id = Importer.idCountry");

  $ciImp = odbc_result($cur, 1);
  $nameImp = odbc_result($cur, 2);
  $pais = odbc_result($cur, 3);
  $limCred = odbc_result($cur, 4);

$cur=odbc_exec($db, "SELECT id, status, date, indenizacao, juro, nSinistro FROM Sinistro WHERE id = $idSinistro");

  $numSinistro = odbc_result($cur, 'id');
  $nSinistro = odbc_result($cur, 'nSinistro');
  $status = odbc_result($cur, 'status');
  $date = odbc_result($cur, 'date');
  $indeniz = odbc_result($cur, 'indenizacao');
  $juros = odbc_result($cur, 'juro');

$cur = odbc_exec($db,"SELECT valueAbt FROM SinistroDetails WHERE idSinistro = $idSinistro");
$i = 0;
$valueTotal = 0;
while (odbc_fetch_row($cur)) {
  $i++;
  $valueAbt = odbc_result($cur, 'valueAbt');
  $valueTotal = $valueTotal + $valueAbt;
}

$cur = odbc_exec($db,"SELECT value FROM Recuperacao WHERE idSinistro = $idSinistro");
$i = 0;
$totalRecup = 0;
while (odbc_fetch_row($cur)) {
  $i++;
  $valueR = odbc_result($cur, 'value');
  $totalRecup = $totalRecup + $valueR;
}

$perdas = $valueTotal + $juros - $totalRecup;

?>

<TABLE cellSpacing=0 cellPadding=3 width="98%" align=center border=0>
<a name=sinistro></a>
  <TR>
    <th align=middle colspan=8><h3>Detalhes do Sinistro</h3></TD>
  </TR></FONT>
  <TR>
    <td colspan=8>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=8 class=bgAzul align=center>Sinistro</td>
  </TR>
  <!--TR>
    <td colspan=2 width=30% align=left>Segurado:</td>
    <td colspan=6 align=left><?php echo $nameExp; ?></td>
  </TR>
  <TR>
    <td colspan=2 width=30% align=left>N.º Aviso:</td>
    <td colspan=6 align=left></td>
  </TR> 
  <TR>
    <td colspan=8 class=bgAzul align=center>Importador</td>
  </TR-->
  <TR>
    <td colspan=2 width=30% align=left>N.º Sinistro:</td>
    <td colspan=6 align=left><?php echo $n_Sinistro; ?></td>
  </TR>    
  <TR>
    <td colspan=2 width=25% align=left>Importador:</td>
    <td colspan=6 align=left><?php echo $nameImp; ?></td>
  </TR>
  <TR>
    <td colspan=2 width=25% align=left>País:</td>
    <td colspan=6 align=left><?php echo $pais; ?></td>
  </TR>    
  <TR>
    <td colspan=2 width=25% align=left>Ci Importador:</td>
    <td colspan=6 align=left><?php echo $ciImp; ?></td>
  </TR> 
  <TR>
    <td colspan=2 width=30% align=left>Situação do Sinistro:</td>
    <td colspan=6 align=left>
<?php switch($status){
            case 2 : echo "Env. Análise"; break;
            case 3 : echo "Avisado"; break;
            case 4 : echo "Suspenso"; break;
            case 5 : echo "Cancelado"; break;
            case 6 : echo "Não Aceito"; break;
            case 7 : echo "Recuperado"; break;
            case 8 : echo "Inden. Aprovada"; break;
          }
?>
    </td>
  </TR>
  <!--TR>
    <td colspan=8 class=bgAzul align=center>Sinistro</td>
  </TR-->
  <TR>
    <td colspan=2 width=30% align=left>Valor do Sinistro:</td>
    <td colspan=6 align=left><?php echo number_format($valueTotal,2,",","."); ?></td>
  </TR>
  <TR>
    <td colspan=2 width=30% align=left>Valor da Indenização:</td>
    <td colspan=6 align=left><?php echo number_format($indeniz,2,",","."); ?></td>
  </TR>    
  <!--TR>
    <td colspan=2 width=30% align=left>Valor das Perdas:</td>
    <td colspan=6 align=left><?php echo number_format($perdas,2,",","."); ?></td>
  </TR> 
  <TR>
    <td colspan=8 class=bgAzul align=center>Aviso do Sinistro</td>
  </TR-->
  <TR>
    <td colspan=2 width=30% align=left>Data do Aviso:</td>
    <td colspan=6 align=left><?php echo substr($date,8,2)."/".substr($date,5,2)."/".substr($date,0,4);?></td>
  </TR>

<?php if ($role["sinistro"]){ ?>
  <TR>
    <td colspan=2 width=30% align=left>&nbsp;</td>
    <td colspan=6 align=right><a href="<?php echo $root;?>role/sinistro/Sinistro.php?comm=histSinistro&idInform=<?php echo $idInform;?>&idSinistro=<?php echo $idSinistro; ?>&idImporter=<?php echo $idImporter; ?>&vol=1">Histórico</a></td>
  </TR> 
<?php }?>
  <TR>
    <td colspan=8 class=bgAzul align=center>Apólice</td>
  </TR>
  <TR>
    <td colspan=2 width=25% align=left>Nº:</td>
    <td colspan=6 align=left><?php echo $apol; ?></td>
  </TR>
  <TR>
    <td colspan=2 width=25% align=left>Vigência:</td>
    <td colspan=6 align=left><?php echo substr($ini_vig,8,2)."/".substr($ini_vig,5,2)."/".substr($ini_vig,0,4);?> a <?php echo substr($fim_vig,8,2)."/".substr($fim_vig,5,2)."/".substr($fim_vig,0,4);?></td>
  </TR>
  <TR>
    <td colspan=2 width=25% align=left>Situação:</td>
    <td colspan=6 align=left>
<?php switch($statusInf){
            case 9  : echo "Cancelado"; break;
            case 10 : echo "Vigente"; break;
            case 11 : echo "Encerrado"; break;
          }
?>
    </td>
  </TR>    
  <!--TR>
    <td colspan=2 width=25% align=left>VMI:</td>
    <td colspan=6 align=left><?php echo number_format($vmi,2,",","."); ?></td>
  </TR--> 
<?php if ($role["sinistro"]){ ?>
  <!--TR>
    <td colspan=8 class=bgAzul align=center>Histórico de Crédito</td>
  </TR>
  <TR>
    <td colspan=8>&nbsp;</td>
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
     <tr<?php echo ($count % 2) ? "" : " bgcolor=#e9e9e9" ?>>
       <td align=center class="texto"><?php echo $dataCred ?>&nbsp;</td>
       <td align=center class="texto"><?php echo $creditSolic == '' ? '' : number_format ($creditSolic,0,',','.') ?>&nbsp;</td>
       <td align=center class="texto"><?php echo $credit == '' ? '0' : number_format ($credit,0,",",".") ?>&nbsp;</td>
       <td align=center class="texto"><?php echo $dataTemp ?>&nbsp;</td>
       <td align=center class="texto"><?php echo $creditTemp == '' ? '' : number_format ($creditTemp,0,",",".") ?>&nbsp;</td>
       <td align=center class="texto"><?php echo odbc_result ($cur, "analysis") == 1 ? "Cobrar" : "&nbsp;" ?></td>
       <td align=center class="texto"><?php echo odbc_result ($cur, "monitor") == 1 ? "Cobrar" : "&nbsp;" ?></td>
       <td align=center class="texto"><?php echo $stateString ?></td>
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
  <TR>
    <td colspan=8 class=bgAzul align=center>Parcelas</td>
  </TR-->
<?php }?>
</TABLE>
<br><br>
<table border="0" cellSpacing=0 cellpadding="1" width="98%" align="center">
  <TR>
    <td colspan=9>&nbsp;</td>
  </TR>
  <TR>
    <td class=bgAzul align=center>Nº Fatura</td>
    <td class=bgAzul align=center>DVE</td>
    <td class=bgAzul align=center>Data de Emb.</td>
    <td class=bgAzul align=center>Data de Venc.</td>
    <td class=bgAzul align=center>Recup</td>
    <td class=bgAzul align=center>Valor da Fatura</td>
    <td class=bgAzul align=center>Valor Pago</td>
    <td class=bgAzul align=center>Valor Coberto</td>
    <td class=bgAzul align=center>Valor em Aberto</td>
  </TR>
<?php $query = "SELECT * FROM SinistroDetails WHERE idSinistro = $idSinistro ORDER BY dateEmb";
    //echo $query;
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
    <td align=center class="texto"><?php if($modalidade == 2) echo "*";?><?php echo odbc_result($cur,4); ?></td>
    <td align=center class="texto"><?php if($dve != 0) echo "sim"; else echo "não"; ?></td>
    <td align=center class="texto"><?php echo substr($dateEmb,8,2)."/".substr($dateEmb,5,2)."/".substr($dateEmb,2,2); ?></td>
    <td align=center class="texto"><?php echo substr($dateVenc,8,2)."/".substr($dateVenc,5,2)."/".substr($dateVenc,2,2); ?></td>
    <td align=center class="texto"><?php echo $recup ?></td>
    <td align=right class="texto"><?php echo number_format($valueFat,2,",","."); ?> &nbsp;</td>
    <td align=right class="texto"><?php echo number_format($valuePag,2,",","."); ?> &nbsp;</td>
    <td align=center class="texto">
       <?php if($dve != 0){ 
          $vc = 1;
          $cob++;
       ?>
          <?php echo $cob;?>
       <?php }else{?>-<?php }?>
    </td>
    <td align=right class="texto"><?php echo number_format($valueAbt,2,",","."); ?> &nbsp;</td>
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
    <td class=bgAzul align=right colspan=8>Total (em aberto):</td>
    <td align=right class="texto"><?php echo number_format($valueTotal,2,",","."); ?> &nbsp;</td>
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
    <td class=bgAzul align=right colspan=8>Total Recuperado:</td>
    <td align=right class="texto"><?php echo number_format($valueT,2,",","."); ?> &nbsp;</td>
    <td align=center>&nbsp;</td>
  </TR>

</table>

<P>&nbsp;</P>
<DIV align=center>
<FORM action="<?php echo $root;?>role/sinistro/Sinistro.php" method="post">
<input type=hidden name="comm">
<input type=hidden name="idInform" value="<?php echo $idInform; ?>">
<?php if ($role["client"]){ ?>
<P><INPUT class=servicos onclick="this.form.comm.value='consultaSinistro';this.form.submit()" type=button value="Voltar">
<?php }else{?> 
<P><INPUT class=servicos onclick="this.form.comm.value='consultaSinistroFunc';this.form.submit()" type=button value="Voltar">
<?php }?>
</form>
</DIV>