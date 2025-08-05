<?php

//extract($_POST);
//extract($_GET);

$idInform = isset($idInform) ? (int)preg_replace('/\D/', '', $idInform) : 0;
    $idInform = htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');
    $idInform = preg_replace('/\D/', '', $idInform);
    $idInform = (int)$idInform;

function creditoAnterior($idInform, $importerName, $db) {
    $qCrediAnte = "
        SELECT TOP 1 
            Importer.name, 
            Country.code, 
            Importer.c_Coface_Imp, 
            Importer.limCredit, 
            ch.stateDate, 
            ch.credit, 
            ch.creditSolic, 
            Importer.id
        FROM Importer
        JOIN Inform ON (Importer.idInform = Inform.id)
        JOIN Country ON (Importer.idCountry = Country.id)
        LEFT JOIN ChangeCredit ch ON (ch.idImporter = Importer.id)
        WHERE Importer.name = ? AND ch.state = 6 AND Inform.id = ? AND ch.creditSolic > 0
        ORDER BY ch.stateDate DESC";

    $stmt = odbc_prepare($db, $qCrediAnte);
    odbc_execute($stmt, [$importerName, $idInform]);

    $credAnterio = [];
    while (odbc_fetch_row($stmt)) {
        $credAnterio[] = odbc_result($stmt, 7);
    }

    return $credAnterio[0] ?? null;
}


function getStrDate($str){
  if(trim($str) == ''){return '';}
  $row = explode('-', $str);
  return $row[2]. "/". $row[1] ."/". $row[0];
}

$idInform      = $_REQUEST['idInform'];
   $idInform = htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');
    $idInform = preg_replace('/\D/', '', $idInform);
    $idInform = (int)$idInform;
//print '??'.$_REQUEST['origem'];
$origem        = isset($_REQUEST['origem']) ? $_REQUEST['origem'] : 0;

if(!$contrat){
    $contrat = isset($_REQUEST['contrat']) ? $_REQUEST['contrat'] : null; 
}

include_once('../../../navegacao.php'); 

include_once("../consultaCoface.php");
?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->

<div class="conteudopagina">

<?php  if($origem == 2) { ?>
  <form action="<?php  echo $root;?>role/searchClient/ListClient.php" method="post">
  <input type="hidden" name="comm" value="view">
<?php  }else { ?>
  <form action="<?php echo $root;?>role/cessao/Cessao.php?comm=consultaCessao" method="post">
  <input type="hidden" name="comm" value="alterCreditImporter">
<?php  } ?>

<script language="JavaScript" src="<?php  echo $root;?>scripts/utils.js"></script>

<table width="100%" style="font-size: 16pt;">
  <tr>
    <td width="12%" style="font-size: 12pt;"><b>Segurado:</b></td>  
    <td width="88%" colspan=6 style="font-size: 12pt;"><?php echo $nameCl;?></td>
  </tr>
  <tr>
    <td width="12%" style="font-size: 12pt;"><b>DPP:</b></td>    
    <td width="22%" style="font-size: 12pt;"><?php echo htmlspecialchars($contrat, ENT_QUOTES, 'UTF-8'); ?></td>
    <td width="14%" style="font-size: 12pt;"><b>Prazo M&aacute;ximo:</b></td>    
    <td width="20%" style="font-size: 12pt;"><?php echo htmlspecialchars($PeriodoMaxCred, ENT_QUOTES, 'UTF-8'); ?> dias</td>
    <td width="12%" style="font-size: 12pt;"><b>Valores em:</b></td>    
    <td width="20%" style="font-size: 12pt;"><?php echo htmlspecialchars($ext, ENT_QUOTES, 'UTF-8'); ?> Mil</td>
</tr>

</table>

<div style="clear:both">&nbsp;</div>

<table summary="Submitted table designs" id="example">
  <thead>
    <tr>
                  <th width="3%" scope="col">&nbsp;</th>
                  <th width="6%" scope="col" style="text-align:center;">CRS</th>
                  <th width="25%" scope="col" style="text-align:left;">Comprador</th>
                  <th width="8%" scope="col" style="text-align:center;">V&aacute;lido a<br>partir de</th>
                  <th width="10%" scope="col" style="text-align:left;">Pa&iacute;s</th>
                  <th width="10%" scope="col" style="text-align:center;">Cr&eacute;dito Solicitado<br></th>
                  <th width="10%" scope="col" style="text-align:center;">Cr&eacute;dito Concedido<br></th>
                  <th width="10%" scope="col" style="text-align:center;">Cr&eacute;dito Tempor&aacute;rio<br></th>
                  <th width="8%" scope="col" style="text-align:center;">Validade</th>

                  <?php  if ($comm != "ficha") { ?>
                    <th scope="col">Solicitar<br><?php  echo $ext;?> Mil</th>
                  <?php  } ?>

                  <th width="10%" scope="col" style="text-align:center;">C&oacute;digo de aprova&ccedil;&atilde;o</th>
          </tr>
        </thead>

  <tbody>
    <?php  
    $table = "";
    $i = 0;
    $obs = 0;

    $queryxx = "
    SELECT 
        inf.contrat, 
        imp.name, 
        imp.c_Coface_Imp,
        imp.limCredit, 
        c.name, 
        ch.credit,
        ISNULL(imp.validityDate, imp.stateDate) AS creditDate, 
        imp.id,
        ch.creditTemp, 
        ch.limTemp, 
        c.code, 
        imp.city,
        imp.address, 
        imp.cep,
        imp.idAprov,
        imp.state
    FROM 
        Inform inf
        INNER JOIN Importer imp ON imp.idInform = inf.id
        INNER JOIN Country c ON c.id = imp.idCountry
        INNER JOIN (
            SELECT MAX(id) AS ID, idImporter 
            FROM ChangeCredit 
            GROUP BY idImporter
        ) CC ON CC.idImporter = imp.id
        INNER JOIN ChangeCredit ch ON ch.id = CC.ID
    WHERE inf.id = ? AND
        (imp.state = 6 OR ((imp.state = 2 " . (!$alter ? " OR imp.state = 4" : '') . ") AND NOT ch.credit IS NULL))
    ORDER BY imp.name";

$cur = odbc_prepare($db, $queryxx);
odbc_execute($cur, [$idInform]);

$importerCreditTotal = 0;
$changeCreditTotal = 0;

    while (odbc_fetch_row($cur)) {
        $importerName = odbc_result($cur, 2);
        $importerCoface       = odbc_result($cur, 3);
        $importerCredit       = odbc_result($cur, 4);//limite solicitado
        $importerCountry      = odbc_result($cur, 5);
        $changeCredit         = odbc_result($cur, 6);
        $changeDate           = odbc_result($cur, 7);
        $importerId           = odbc_result($cur, 8);
        $creditTemp           = odbc_result($cur, 9);
        $limTemp              = odbc_result($cur, 10);
      $countryCode          = odbc_result($cur, 11);
      $Cidade           = odbc_result($cur, 12);
      $Endereco           = odbc_result($cur, 13);
      $CEP              = odbc_result($cur, 14);
        $state                = odbc_result($cur, 16);//pega o status do importador, para poder resgatar o valor anterior solicitado de crédito
        $today      = date("Y", time())."-".date("m", time())."-".date("d", time());
        $tempValid    = false;
  
        if ($i==0) {
        $table .= "
            <table>
              <tr>
                <td><font>Cr&eacute;dito solicitado</font></td><td><font>Cr&eacute;dito concedido</font></td><td><font>Cr&eacute;dito tempor&aacute;rio</font></td>
              </tr>
          ";
        }
  
        $idAprov  = odbc_result($cur, "idAprov");
  
        if ($idAprov != "") {
    $strSQL = "SELECT * FROM tb_Aprovacao WHERE id = ?";
    $stmt = odbc_prepare($db, $strSQL);
    odbc_execute($stmt, [$idAprov]);

    if (odbc_fetch_row($stmt)) {
        $codAprov = odbc_result($stmt, "codigo");
    }

    // Free the result resource
    odbc_free_result($stmt);
  
        if ($limTemp >= $today){
        $tempValid = true;
        }
  
        $limTemp = getStrDate(substr($limTemp ?? '', 2, 8));
  
        //Credito solicitado
        $importerCredit = number_format($importerCredit/1000, 0, ",", ".");
  
        //verifica se o status do importar é pendente com o numero 4
        if($state!=6){
        $importerCredit = number_format(creditoAnterior($idInform,$importerName,$db)/1000, 0, ",", ".");//pega o valor anterior solicitado e atribui ao creido atual
        }else{
        $importerCredit = $importerCredit;
        }
  
        //Credito concedido
        $changeCredit   = number_format($changeCredit/1000, 0, ",", ".");
  
        //Credito temporario
        $creditTemp     = number_format($creditTemp/1000, 0, ",", ".");
  
        $changeDate      = substr($changeDate, 8, 2)."/".substr($changeDate, 5, 2)."/".substr($changeDate, 2, 2);

        if ($changeDate == "//")
        $changeDate = "";

        if ($limTemp == "//")
        $limTemp = "";

        $i++;
        $tmp_array = array();

        // pega as observações deste importador
        $strSQL = "SELECT comment FROM ImpComment WHERE idImporter = ? AND hide = 0";
$stmt = odbc_prepare($db, $strSQL);
odbc_execute($stmt, [$importerId]);

$observacoes = [];
while (odbc_fetch_row($stmt)) {
    $observacoes[$obs] = odbc_result($stmt, 'comment');
    $tmp_array[] = $obs;
    $obs++;
}

// Free the result resource
odbc_free_result($stmt);

      ?>

            <tr>
              <td style="text-align:center;"><?php echo $i;?></td>
              <td style="text-align:center;"><?php echo $countryCode . substr($importerCoface, -6);?></td>
              <td style="text-align:left;"><?php echo $importerName;?>&nbsp;&nbsp;
              <td style="text-align:center;"><?php  echo $changeDate;?></td>
              <td style="text-align:left;"><?php echo $importerCountry;?></td>

          <?php  
                
                        for($j = 0; $j < count($tmp_array); $j++){
                            echo "(". ($tmp_array[$j] + 1). ") ";
                        }
            
                    ?>
              </td>

              <td style="text-align:right;"><?php echo $importerCredit;?></td>
              <td style="text-align:right;"><?php echo $changeCredit;?></td>
              <td style="text-align:right;"><?php echo $tempValid ? $creditTemp : "";?>&nbsp;</td>
              <td style="text-align:center;"><?php echo $tempValid ? $limTemp : "";?>&nbsp;</td>
        <?php
            
                    //Credito solicitado
                    $importerCredit = str_replace(".", "", $importerCredit);
                    $importerCreditTotal += $importerCredit;
            
                    //Credito concedido
                    $changeCredit = str_replace(".", "", $changeCredit);
                    $changeCreditTotal += $changeCredit;
            
                    //Credito temporario
                    $creditTemp = str_replace(".", "", $creditTemp);

                    if ($tempValid) {
                      $creditTemptotal+=$creditTemp;
                    }
            
                    if ($comm != "ficha") {  
          ?>
                      <td>
                          <input type="text" size="10" onBlur="checkDecimalsMil(this,this.value)" name="<?php  echo "edit".$i;?>" value="" class="caixa">
                          <input type="hidden" name="<?php  echo "importer".$i;?>" value="<?php  echo $importerId;?>">
                      </td>
        <?php  }   ?>

              <td style="text-align:center;"><?php  echo $codAprov;?></td>
            </tr>
            
            <tr>
              <td style="text-align:left;" colspan="2"><?php  echo "Endere&ccedil;o:";?></td>
              <td style="text-align:left;" colspan="4"><?php  echo $Endereco;?></td>
              <td style="text-align:left;" ><?php  echo "Cidade:";?></td>
              <td style="text-align:left;" colspan="3"><?php  echo $Cidade;?></td>
            </tr>

      <?php  
            $table .= "
                    <tr>
                      <td><font>$importerCredit</font></td><td><font>$changeCredit</font></td><td><font>$creditTemp</font></td>
                    </tr>
                    ";

        } //fim while (odbc_fetch_row($cur)) {

      $table .= "</table>";
      ?>

  <input type="hidden" name="i" value=<?php  echo $i;?>>
        <?php  if ($i == 0) { ?>
    <tr>
      <td colspan=9>Nenhum importador pode ter o cr&eacute;dito alterado no momento</td>
        </tr>
        <?php  } ?>

  <tr>
    <td colspan="10">
      <?php
            if( (!$role["client"]) ) {
        
              //percentual de aprovacao
                $resultado = ( ($changeCreditTotal + $creditTemptotal) / $importerCreditTotal)*100;
                $resultado = round($resultado, 2);
        
              ?>
        
              <!--Credito solicitado--><b>Total cr&eacute;dito Solicitado: </b><?php  echo $importerCreditTotal;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <!--Credito concedido--><b>Total cr&eacute;dito Concedido: </b><?php  echo $changeCreditTotal;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <!--Credito temporário--><b>Total cr&eacute;dito Tempor&aacute;rio: </b><?php  echo $creditTemptotal;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <b>Percentual de aprova&ccedil;&atilde;o:</b> <?php  echo $resultado;?>%<br><br><br>
        
              <?php
            }
          ?>

        <?php  if ($comm == "ficha" ) { 
                    echo ('"Encontram-se acima relacionados os limites de cr&eacute;dito solicitados por Vsa. quando da assinatura da Ap&oacute;lice de Seguro de Cr&eacute;dito &agrave; Exporta&ccedil;&atilde;o, e os limites de cr&eacute;dito efetivamente aprovados pela '.$nomeEmpSBCE.'. O presente documento revoga as decis&otilde;es de limite de cr&eacute;dito previamente concedidas aos compradores aqui elencados."');
            } else { ?>

        <?php  }      ?>
      </td>
       </tr>
        
</tbody>
</table>
  
<div class="barrabotoes">

  <?php
  //print $comm;
  
       if ($origem == 2) { ?>
        <button class="botaoagm" type="button" onClick="this.form.submit();">Voltar</button>
  <?php  } else { ?>
        <button class="botaoagm" type="button" onClick="this.form.comm.value='open';this.form.submit()" >Voltar</button>
  <?php  } ?>
<input type="hidden" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>" name="idInform" class="sair">
      
  <?php  if ($i > 0 && $comm != "ficha") { ?>
        <button class="botaoagm" type="button" onClick="this.form.submit();">Voltar</button>
  <?php  } ?>
</div>

<div style="clear:both">&nbsp;</div>
  <table>
          <thead>
              <tr>
                  <th>&nbsp;</th>
                  <th>Observa&ccedil;&otilde;es</th>
              </tr>
          </thead>

          <tbody>
      <?php  
        $cnt = isset($observacoes) ? count($observacoes) : 0;
        for($j = 0; $j < $cnt; $j++){
          echo "<tr>";
          echo "<td><font>". ($j + 1). "</font></td>\n";
          echo "<td><font>$observacoes[$j]</font></td></tr>\n";
        }
      ?>
          </tbody>

  </table>
<!--FIM PÁGINA - -->
</div>