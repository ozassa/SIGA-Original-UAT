<?php 
  // Alterado Hicom 10/01/05 (Gustavo) - inclusão de um novo perfil (viewCredit) igual ao crédito, sem permissão de alteração

  /*
     *******************************************
     Data ção: 07/05/2009
     Alterado por: Interaktiv (Elias Vaz)
     Motivo: Alterar a forma de calcular os valores com litígio.
  */

  if(! function_exists('getEndDate')){
    function getEndDate($d, $n, $idInform, $c = 0){
        if($n != 12){
            if(preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $d, $v)){
          //return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $v[1] - 1 + $c, $v[3]));
              return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
            }else if(preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})/", $d, $v)){
          //return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $v[1] - 1 + $c, $v[3] + 2000));
              return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
            }
        }else{
            $sql = "SELECT endValidity FROM Inform WHERE id = ?";

            $stmt = odbc_prepare($db, $sql);

            $result = odbc_execute($stmt, array($idInform));
            
            $row = odbc_fetch_array($stmt);
            $end = $row['endValidity'];
            odbc_free_result($stmt);
            return dmy2ymd($end);
        }
      }
  }

  // converte a data de yyyy-mm-dd para dd/mm/yyyy
  if(! function_exists('ymd2dmy')){
      function ymd2dmy($d){
        if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
            return "$v[3]/$v[2]/$v[1]";
        }
        
        return $d;
      }
  }

  // //Adicionado por Michel Saddock 10/08/2006
  //Quase 10 anos depois e tenho que arrumar erro do Checheu, tnc corno manso
  $sql = "SELECT *, IsNull(prAux,'0') as pxX2 FROM Inform WHERE id = ?";

  $cur = odbc_prepare($db, $sql);

  $result = odbc_execute($cur, array($idInform));

  if ($result && odbc_fetch_row($cur)) {
    $ok = 1;
    $i = 1;
    $test = 0;
    $field->setDB ($cur);
    $state = odbc_result($cur,"state");
    $sameAddress = odbc_result($cur, 'sameAddress');
    $addressNumber = odbc_result($cur, 'addressNumber');
    $chargeAddressNumber = odbc_result($cur, 'chargeAddressNumber');
    $addressComp = odbc_result($cur, 'addressComp');
    $chargeAddressComp = odbc_result($cur, 'chargeAddressComp');
    $chargeCity = odbc_result($cur, 'chargeCity');
    $chargeAddress = odbc_result($cur, 'chargeAddress');
    $chargeCep = odbc_result($cur, 'chargeCep');
    $vigencia          = odbc_result($cur, 'pvigencia');
    $unblock_dve       = odbc_result($cur, 'unblock_dve');
    $prMinAuxX2X = odbc_result($cur, 'pxX2'); // valor campo prAux
    $ini_vig = odbc_result($cur, 'startValidity');
    $fim_vig = odbc_result($cur, 'endValidity');
    $num_parc = odbc_result($cur, 'numParc');
    $sent = odbc_result($cur, 'sentOffer');
    $moeda = odbc_result($cur, 'currency');
    $bornDate = odbc_result($cur, 'bornDate');
    $prMTotal = odbc_result($cur, 'prMTotal');
    $txMTotal = odbc_result($cur, 'txMTotal');
    $Periodo_Vigencia   =  odbc_result($cur, 'Periodo_Vigencia');
    
    $hc_idAnt = trim ( "" . odbc_result($cur, 'idAnt'));

    if ($vigencia == "") {
      $pvigencia = "12 Meses";
    } else if ($vigencia == "1") {
      $pvigencia = "12 Meses";
    } else {
      $pvigencia = "24 Meses";
    }
          
    if ($Periodo_Vigencia) {
      $pvigencia = $Periodo_Vigencia . " Meses";
    }

    $limPagIndeniz = odbc_result($cur, 'limPagIndeniz');
    $percCoverage  = odbc_result($cur, 'percCoverage');
    $numParc       = odbc_result($cur, 'numParc');
    $txAux         = odbc_result($cur, 'txAux');
    $Ga = odbc_result($cur, 'Ga');
    $endo = odbc_result($cur, 'endosso');
    $state = odbc_result($cur, 'state');
    $taxa_min = odbc_result($cur, 'txMin');
    $taxa_Aux = odbc_result($cur, 'txAux');
    $taxa_rise = odbc_result($cur, 'txRise');
    $waranty = odbc_result($cur, 'warantyInterest');

    /*Inicia Cálculos do CAEX
     19/05/2009 - Interaktiv (Elias Vaz)
                  Alteração na exibição do cálculo do premio mínimo
                  este valor na variável $Ur: 0.0384616 é resultado do seguinte cálculo
                  =(0,04 * 0,04) = 0,0016
                  = 0,04 - 0,0016 = 0,0384616
                  o resultado do cálculo é um percentual para retonar o valor do premio mínimo anterior sem os juros mora
                  logo abaixo ele simplesmente irá ser utilizado na exibição do valor Adicional de juros.
    */

    $premio_min = $prMTotal;
    $tx = $txMTotal;
    $caex = 0;
    if($tx != 0){
      $caex = number_format ((($premio_min / $tx)*100), 2, ',','.');
    }
    
      
    if(isset($action)){
      odbc_exec($db, "update Inform set endosso=$val where id=$idInform");
    }

    $prospectJaAtivo = false;
    $Ur = 0.0384616;
    $premio = odbc_result($cur, 'prMin');
    $premio = $premio - number_format(($premio * ($waranty > 0 ? $Ur:0)),1,'','');
    $premio_min_total = $prMTotal;

    $adicional_juros = $premio_min_total - ($premio_min_total / ($waranty ? 1.04:1));
    //$adicional_juros = $premio * ($waranty ? 0.04:0);
    $parc = number_format ($premio_min / $num_parc, 0, '', '');
      
    /*
      
      $Ur = 0.0384616;

      $premio_min = odbc_result($cur, 'prMin') * (1 + $taxa_rise);
      $tx = number_format($taxa_min,5,'.','') * (1 + $taxa_rise);
      
    $caex = number_format (($premio_min / $tx), 2, ',','.');

    if($action){
      odbc_exec($db, "update Inform set endosso=$val where id=$idInform");
    }

      $prospectJaAtivo = false;
      $premio_min = $premio_min - number_format(($premio_min * ($waranty > 0 ? $Ur:0)),1,'','');
      //$adicional_juros = $premio_min * ($waranty ? $Ur:0);
      $adicional_juros = $premio_min * ($waranty ? 0.04:0);
      //$premio_min = $premio_min - $adicional_juros;
      $premio_min_total = $premio_min  + $adicional_juros;
      $parc = number_format ($premio_min / $num_parc, 0, '', '');

    */
      
    if ($hc_idAnt!="" && $state < 9 ) {
      // Tem anterior e é prospectivo
      // Vamos verificar se idAnt é 11, 10 ou 9 e tem endValidity

      $hc_count = 0;

      // Prepara a consulta com placeholders ?
      $wstr = "SELECT count(state) as cc 
               FROM Inform 
               WHERE id = ? 
               AND (state = 11 OR state = 10 OR (state = 9 AND endValidity IS NOT NULL))";
      $hc_cur = odbc_prepare($db, $wstr);

      $result = odbc_execute($hc_cur, array($hc_idAnt));

      if ($result && odbc_fetch_row($hc_cur)) {
          $hc_count = odbc_result($hc_cur, 'cc');
      }


      if ($hc_count > 0) {
        $prospectJaAtivo = true;
      }
    }
      
    $iSeg = odbc_result($cur, 'i_Seg');
    $prod = odbc_result($cur, 'prodUnit');

    //Alterado por Tiago V N - Elumini - 09/11/2005
    $sql = "SELECT n_Apolice FROM Inform WHERE id = ?";
    #odbc_free_result($hc_cur);
    $y = odbc_prepare($db, $sql);

    $result = odbc_execute($y, array($idInform));

    if ($result && odbc_fetch_row($y)) {
        $apolice = sprintf("062%06d", odbc_result($y, 1));

        if ($prod != 62) {
            $apolice .= "/$prod";
        }
    }


    if ($role["client"]) {
       $test = 2;
    } else {
      $test = 3;
    }

    for(; $i <= 6; $i++){
      if(odbc_result($cur, $i) != $test){
        $ok = 0;
      }
    }
         
    if($i == 1){
      $ok = 0;
    }
    
    //continua na condição  "odbc_fetch_row($cur)"
?>

<script language="javascript">
   function muda_endosso(b, inform, session){
     if(b.checked){
       location = "/src/role/searchClient/ListClient.php?comm=view&idInform=" + inform + "&action=1&val=1";
     }else{
       location = "/src/role/searchClient/ListClient.php?comm=view&idInform=" + inform + "&action=1&val=0";
     }
   }
</script>
<?php include_once("../../../navegacao.php");?>

<div class="conteudopagina">
  <?php  require_once("client.php"); ?>
</div>

<?php 


if ($prospectJaAtivo || $role["executive"]  || $role["tariffer"] || $role["credit"] || $state == 10 || $state == 11)
{
?>
<div class="conteudopagina">

<table summary="Submitted table designs">
  <thead>
  <tr>
      <th colspan="2">Informa&ccedil;&otilde;es da Ap&oacute;lice</th>
  </tr>
  </thead>
  <tbody>

<?php 
   if($apolice){
     echo "<tr>
      <td>N&ordm; da Ap&oacute;lice:</td>
      <td>$apolice</td>
  </tr>";
   }
?>

  <tr>
      <td>Tipo de Ap&oacute;lice:</td>
      <td><?php
      if( (($Ga)=="") && ($state !=6 && $state !=10 && $state !=5) )
      {
         echo "--";
      }
      elseif( (($Ga)=="") && ($state ==6 || $state ==10 || $state ==5))
      {
        echo "CP/RC/05-01";
      }
      elseif(($Ga)=="1")
      {
         echo "CP/GA/05-01";
      }
      else
      {
         echo "CP/RC/05-01";
      }
      ?></td>
  </tr>

  <tr>
      <td>Data de Cadastro:</td>
      <td>
      <?php  $data = odbc_result($cur,'bornDate'); ?><?php echo $dat = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4); ?>
      </td>
  </tr>
  <tr>
      <td>Nome do Respons&aacute;vel:</td>
      <td><?php echo   (odbc_result($cur, 'respName')); ?></td>
  </tr>
  <tr>
      <td>Cargo:</td>
      <td><?php echo  (odbc_result ($cur, 'ocupation')); ?></td>
  </tr>
  
  <tr>
      <td>CAEX:</td>
      <td><?php echo $caex; ?></td>
  </tr>
  <tr>
      <td>Taxa de Pr&ecirc;mio:</td>
      <td><?php echo number_format($tx ?? 0, 3, ',', '.'); ?> %</td>
  </tr>
  <?php 
      $ext = '';
      if($premio_min > 0){
        if ($moeda == "2"){
           $ext = "US$";
        }else if ($moeda == "6") {
           $ext = "€";
        }else if ($moeda == "0") {
           $ext = "US$";
        }else if ($moeda == "1") {
           $ext = "R$";
        }
    ?>
  <tr>
      <td>Pr&ecirc;mio M&iacute;nimo: </td>
      <?php  $premio_minimo = number_format(($prMTotal - $adicional_juros), 0, '.', ''); ?>
      <td><?php echo $ext?> <?php echo number_format($premio_minimo, 2, ',', '.'); ?></td>
  </tr>
  <?php 
    
    } 

 if($adicional_juros > 0){ ?>
  <tr>
      <td>Adicional de Juros de Mora:</td>
      <td><?php echo $ext;?> <?php echo number_format($adicional_juros, 2, ',', '.'); ?></td>
  </tr>
  <tr>
      <td>Pr&ecirc;mio M&iacute;nimo Total:</td>
      <td><?php echo $ext;?> <?php echo number_format($premio_min_total, 2, ',', '.'); ?></td>
  </tr>
<?php  } ?>

  <tr>
      <td>Data de Preenchimento:</td>
      <td><?php  $dataPreench = odbc_result($cur, "dataPreench") ?? ''; ?><?php echo substr($dataPreench, 8, 2). "/". substr($dataPreench, 5, 2). "/". substr($dataPreench, 0, 4); ?></td>
  </tr>
  <tr>
      <td>Data de Envio Oferta:</td>
      <td><?php  $dataOferta = odbc_result($cur, "dataOferta") ?? ''; ?><?php echo substr($dataOferta, 8, 2). "/". substr($dataOferta, 5, 2). "/". substr($dataOferta, 0, 4); ?></td>
  </tr>
  <tr>
      <td>Per&iacute;odo de Vig&ecirc;ncia: </td>
      <td>
      <?php echo substr($ini_vig ?? '', 8, 2). "/". substr($ini_vig ?? '', 5, 2). "/". substr($ini_vig ?? '', 0, 4); ?>
      &agrave; <?php echo substr($fim_vig ?? '', 8, 2). "/". substr($fim_vig ?? '', 5, 2). "/". substr($fim_vig ?? '', 0, 4); ?>
      </td>
  </tr>

  <tr>
      <td>N&uacute;mero de Parcelas:</td>
      <td><?php echo $num_parc;?> </td>
  </tr>

</tbody>
</table>
</div>

<?php 
}  

if ($role["viewCredit"]) {
?>
<div class="conteudopagina" style="padding-top: 0px;">
            <form action="<?php echo $root; ?>role/credit/Credit.php" method="post">
                <input type=hidden name="comm"     value="reportImporter" />
                <input type=hidden name="idInform" value="<?php echo $idInform; ?>" />
                <input type=hidden name="origem"   value="2" />
                <ul>
                <li class="campo2colunas">
                <label>Demonstrativo do Faturamento de An&aacute;lise e Monitoramento</label>
                <div class="formselect">
                <select name="anoMes" style="width:120px;">
          <?php       // Acesso à nova estrutura de análise e monitoramento 
                      $sql = "SELECT ano, mes FROM resFatAnaliseMonitor WHERE idInform = ? ORDER BY ano, mes";
                      odbc_free_result($y);
                      // Prepara a query
                      $cur = odbc_prepare($db, $sql);

                      // Executa a query passando o parâmetro de forma segura
                      $result = odbc_execute($cur, array($idInform));

                      $count = 0;
                      // Itera sobre os resultados
                      while ($result && odbc_fetch_row($cur)) {
                          $ano = odbc_result($cur, 1);
                          $mes = odbc_result($cur, 2);
                    ?>
                    <option value=<?php echo "$ano-$mes"; ?>><?php echo "$mes/$ano"; ?></option>
                    <?php 
                        $count++;
                      }
                    ?>
                </select>
                </div>
                <div class="formdescricao"><span> 
        <?php  if ($count > 0) { ?>
                <button name="resumo" type="submit" class="botaoapm">Ver Resumo</button>
                <?php  } else { ?>
                Nenhum Demonstrativo Dispon&iacute;vel
                <?php  } ?>
                </span></div>
              </li>
              </ul>
            </form>
            
</div>
<?php 
}
?>
<div class="divisoria01"></div>
<div class="conteudopagina" style="padding-top: 0px;">
            <form action="<?php echo $root; ?>role/dve/DVE.php" method="post" style="min-height:auto!important;">
                <input type="hidden" name="comm" value="view">
                <input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
                <input type="hidden" name="client" value="0">
                <input type="hidden" name="newdve" value="0">
        <input type="hidden" name="primeira_tela" id="primeira_tela" value="1" />
                <ul>
                <li class="campo2colunas">
                <label>DVN   - Declara&ccedil;&atilde;o do Volume de Negócios</label>
                <div class="formselect" style="width:auto;">
                <select name="idDVE" style="width:auto;">
        <?php 
                  require_once("../client/query.php");

                    // Prepara a consulta SQL com placeholders
                    odbc_free_result($cur);
                    
                    
                    $sql = "SELECT * FROM DVE WHERE idInform = ? AND state = 2 ORDER BY num";

                    // Prepara a query
                    $dves = odbc_prepare($db, $sql);

                    // Executa a query passando os parâmetros
                    $result = odbc_execute($dves, array($idInform));

                    $count = 0;

                    // Itera sobre os resultados
                    while ($result && odbc_fetch_row($dves)) {
                        $num = odbc_result($dves, 'num');
                        $inicio = ymd2dmy(odbc_result($dves, 'inicio'));

                        if ($tipoDve == 3) { // Tipo DVN Anual
                            $fim = getEndDate($inicio, 12, 0, $idInform);
                        } else if ($tipoDve == 2) { // Tipo DVN Trimestral
                            $fim = getEndDate($inicio, 3, 0, $idInform);
                        } else { // Tipo DVN Mensal
                            $fim = getEndDate($inicio, 1, 0, $idInform);
                        }
        
                
                ?>
                <option value=<?php echo odbc_result($dves, 'id'); ?>><?php echo "$inicio a $fim";/* " ($num". "ª DVE)"*/ ?></option>
                <?php 
                    $count++;
                  }
                ?>
                </select>
                </div>
                <div class="formdescricao"><span> 
        <?php  if ($count > 0) { ?>
                             <button name="dve"  id="dve" type="submit" class="botaoapm">Ver DVN</button>
                <?php  } else { ?>
                             Nenhuma DVN Dispon&iacute;vel
                <?php  } 
             $chk_unb = "";
             
             if ($unblock_dve==1) {
              $chk_unb = "checked";
             }
        ?>
                </span></div>
              </li>
              </ul>
            </form>
            <?php
            $remove = $field->getField("remove");
              if($remove == "ok") {
                require_once("removeIncomplete.php");
              }
            }
            ?>            

</div>

