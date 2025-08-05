<?php 

//Alterado HiCom mes 04
// Alterado Hicom (Gustavo) - 15/12/04 - colspan = 5

$sql = "SELECT currency, i_Sub_Produto FROM Inform WHERE id = ?";
$cur = odbc_prepare($db, $sql);
odbc_execute($cur, [$idInform]);

$moeda = odbc_result($cur, "currency");
$iSubProduto = odbc_result($cur, "i_Sub_Produto");

If ($moeda == "1") {
   $ext = "R$";
}else if ($moeda == "2") {
   $ext = "US$";
}else if ($moeda == "6") {
   $ext = "";
}

odbc_free_result($cur);

if(! function_exists('getStrDate')){
  function getStrDate($str){
    $row = explode('-', $str);
    $ret = $row[2]. "/". $row[1] ."/". $row[0];
    if ($ret == '//')
      return '';
    return $ret;
  }
}


$idCDOB   = $_REQUEST['idCessao'];
$idCDBB   = $_REQUEST['idCessao'];
$idCDParc = $_REQUEST['idCessao'];
$idNotification = isset($_REQUEST['idNotification']) ? $_REQUEST['idNotification'] : '';

$tipoBanco = $_REQUEST['tipoBanco'];
//echo $idCessao . "<br>";
//echo $tipoBanco . "<br>";

if($tipoBanco == 3){
  $query = "SELECT inf.name, cdob.agencia, bc.name AS banco_nome, cdob.status, cdob.codigo, cdob.dateClient, bc.id 
          FROM Inform inf 
          JOIN CDOB cdob ON cdob.idInform = inf.id
          JOIN Banco bc ON cdob.idBanco = bc.id 
          WHERE inf.id = ? AND cdob.id = ?";

$cur = odbc_prepare($db, $query);
odbc_execute($cur, [$idInform, $idCDOB]);
  $nameInf = odbc_result($cur, 1);
  $agencia = odbc_result($cur, 2);
  $banco = odbc_result($cur, 3);
  $status = odbc_result($cur, 4);
  $idBanco = odbc_result($cur, 7);

  $hc_codigo  = odbc_result ($cur, 5);
  $dateEnv = odbc_result($cur, 6);
  list($ano, $mes, $dia) = explode('-', $dateEnv);

odbc_free_result($cur);
} else if($tipoBanco == 1){
  $query = "SELECT inf.name, cdbb.idAgencia, ag.name AS agencia_nome, cdbb.status, cdbb.codigo, 
                 cdbb.dateClient, ag.codigo AS agencia_codigo, ag.idBanco
          FROM Inform inf 
          JOIN CDBB cdbb ON cdbb.idInform = inf.id 
          JOIN Agencia ag ON cdbb.idAgencia = ag.id 
          WHERE inf.id = ? AND cdbb.id = ?";

$cur = odbc_prepare($db, $query);
odbc_execute($cur, [$idInform, $idCDBB]);
  $nameInf = odbc_result($cur, 1);
  $agencia = odbc_result($cur, 3);
  $idAgencia  = odbc_result($cur,'idAgencia');
  $numeroagencia  = odbc_result($cur, 7);
  $status = odbc_result($cur, 4);
  $idBanco = odbc_result($cur, 8);

  $hc_codigo  = odbc_result ($cur, 5);
  $dateEnv = odbc_result($cur, 6);
  list($ano, $mes, $dia) = explode('-', $dateEnv);
  odbc_free_result($cur);
}else{
  $query = "SELECT inf.name, cdparc.idAgencia, ag.name AS agencia_nome, cdparc.status, 
                 bc.name AS banco_nome, cdparc.codigo, cdparc.dateClient, 
                 ag.codigo AS agencia_codigo, bc.id AS banco_id
          FROM Inform inf 
          JOIN CDParc cdparc ON cdparc.idInform = inf.id 
          JOIN Agencia ag ON cdparc.idAgencia = ag.id 
          JOIN Banco bc ON cdparc.idBanco = bc.id 
          WHERE inf.id = ? AND cdparc.id = ?";

$cur = odbc_prepare($db, $query);
odbc_execute($cur, [$idInform, $idCDParc]);
  $nameInf = odbc_result($cur, 1);
  $agencia = odbc_result($cur, 3);
  $idAgencia  = odbc_result($cur,'idAgencia');
  $numeroagencia  = odbc_result($cur, 8);
  $status = odbc_result($cur, 4);
  $banco = odbc_result($cur, 5);
  $idBanco = odbc_result($cur, 9);

  $hc_codigo  = odbc_result ($cur, 6);
  $dateEnv = odbc_result($cur, 7);
  list($ano, $mes, $dia) = explode('-', $dateEnv);
  odbc_free_result($cur);
}


//####### ini ####### adicionado por eliel vieira - elumini - 05/05/2008
//debug: Detalhe da Cesso (segurado)
//echo "(".$query.")<br>";


?>

<div class="conteudopagina">
    <h2><?php if(isset($banco)){ echo $banco; }else{ echo "Banco do Brasil";}?></h2>
      <li class="campo2colunas">
        <label>Segurado</label>
        <?php echo ($nameInf); ?>
      </li>
      <li class="campo2colunas">
        <label>Ag&ecirc;ncia</label>
        <?php echo ($agencia); ?>
      </li>
      <li class="campo2colunas">
        <label>N&ordm; Ag&ecirc;ncia</label>
        <?php echo $numeroagencia; ?>
      </li>
      <li class="campo2colunas">
        <label>Cess&atilde;o</label>
        <?php  echo $hc_codigo . "/" . $ano; ?>
      </li>
    <div class="divisoria01"></div>
      <table>
          <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Raz&atilde;o</th>
                <th>Pa&iacute;s</th>
                <th>Cr&eacute;dito Concedido (<?php echo $ext;?> Mil)</th>
                <th>Cr&eacute;dito Tempor&aacute;rio (<?php echo $ext;?> Mil)</th>
            </tr>
          </thead>
    <?php 
    //print $tipoBanco;
    if ($tipoBanco == 3) {
      $query = "SELECT imp.name AS impName, c.name AS cName, imp.id, cb.codigo, cb.dateClient
                FROM Importer imp
                JOIN Country c ON imp.idCountry = c.id
                JOIN CDOBDetails cd ON imp.id = cd.idImporter
                JOIN CDOB cb ON cd.idCDOB = cb.id
                WHERE imp.idInform = ?
                AND cb.status <> 10
                AND cb.id = ?
                AND imp.state NOT IN (7, 8, 9)
                ORDER BY imp.name";
      $params = [$idInform, $idCDOB];
  
  } elseif ($tipoBanco == 1) {
      $query = "SELECT imp.name AS impName, c.name AS cName, imp.id, cb.codigo, cb.dateClient
                FROM Importer imp
                JOIN Country c ON imp.idCountry = c.id
                JOIN CDBBDetails cd ON imp.id = cd.idImporter
                JOIN CDBB cb ON cd.idCDBB = cb.id
                WHERE imp.idInform = ?
                AND cb.status <> 10
                AND cb.id = ?
                AND imp.state NOT IN (7, 8, 9)
                ORDER BY imp.name";
      $params = [$idInform, $idCDBB];
  
  } else {
      $query = "SELECT imp.name AS impName, c.name AS cName, imp.id, cb.codigo, cb.dateClient
                FROM Importer imp
                JOIN Country c ON imp.idCountry = c.id
                JOIN CDParcDetails cd ON imp.id = cd.idImporter
                JOIN CDParc cb ON cd.idCDParc = cb.id
                WHERE imp.idInform = ?
                AND cb.status <> 10
                AND cb.id = ?
                AND imp.state NOT IN (7, 8, 9)
                ORDER BY imp.name";
      $params = [$idInform, $idCDParc];
  }
  
  $cur = odbc_prepare($db, $query);
  odbc_execute($cur, $params);
  
  // Exemplo de processamento dos resultados
  $i = 0;
  
            while (odbc_fetch_row($cur)) {
              $i++;
              // $idImporter = odbc_result($cur,1);
              $idImporter = odbc_result($cur,3);
              $dateEnv = odbc_result($cur, 5);
              list($ano, $mes, $dia) = explode('-', $dateEnv);
        
              // busca dados de crdito
        //echo $idImporter;
              $wsql = "select case when limTemp < getdate() then 0 else creditTemp end creditTemp, limTemp, credit, getdate() as hoje from ChangeCredit where id=(select max(id) from ChangeCredit where idImporter= $idImporter) ";
                //echo $wsql;
        
              $y = odbc_exec($db, $wsql);
              $hc_creditTemp_imp = odbc_result($y, 1);
              $hc_limTemp_imp = odbc_result($y, 2);
              $hc_credit_imp = odbc_result($y, 3);
                
              
              if ($hc_limTemp_imp)
              {
                if($hc_creditTemp_imp > 0) 
                    $hc_creditTemp_imp = number_format($hc_creditTemp_imp/1000, 0, ",", ".") . "<br>at:&nbsp;" . getStrDate(substr($hc_limTemp_imp, 0, 10));
                else
                    $hc_creditTemp_imp = "0";
                 //if(getTimeStamp(getStrDate(substr($hc_limTemp_imp, 0, 10))) >= time())
                 //{
        
                 //}
                 //else
                 //{
        
                 //}
              }
              else
              {
                 $hc_creditTemp_imp = number_format(0, 0, ",", ".");
              }
        
        
        
              // fim busca dados de crdito
        
    ?>
        <tbody>
              <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo odbc_result($cur,'impName'); ?></td>
                <td><?php echo (odbc_result($cur,'cName')); ?></td>
                <td style="text-align:right"><?php echo number_format($hc_credit_imp/1000, 0, ",", "."); ?></td>
                <td style="text-align:right"><?php echo $hc_creditTemp_imp; ?></td>
              </tr>
        </tbody>
    <?php 





    } // while
  odbc_free_result($cur);
    if ($i == 0) {
  
  ?>
       <tfoot>
          <tr>
            <td colspan="5">Nenhum Importador Cadastrado</td>
          </tr>
       </tfoot>


  <?php 
      }
    ?>
</table>

<?php 
  $sqlCDBB = "SELECT Chave_Documento FROM CDBB WHERE id = ?";
  $rsSqlCDBB = odbc_prepare($db, $sqlCDBB);
  odbc_execute($rsSqlCDBB, [$idCDBB]);
    
  $Chave_Documento = odbc_result($rsSqlCDBB, "Chave_Documento");

  odbc_free_result($rsSqlCDBB);


 ?>

<form action="<?php echo $root;?>role/cessao/Cessao.php" name="frm1" method="post">
<input type="hidden" name="comm" value="">
  <input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="idCDBB" value="<?php echo htmlspecialchars($idCDBB, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="idCDOB" value="<?php echo htmlspecialchars($idCDOB, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="idCDParc" value="<?php echo htmlspecialchars($idCDParc, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="idBanco" value="<?php echo htmlspecialchars($idBanco, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="tipoBanco" value="<?php echo htmlspecialchars($tipoBanco, ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="idNotification" value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">
  
  <div class="barrabotoes">
    <button class="botaovgm" type="button" onclick="window.location = '<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8');?>role/searchCessao/SearchCessao.php'">Voltar</button>
    
    <?php if($role["bancoBB"] || $role["bancoParc"]){ ?>
      <button class="botaoagg" type="button" onclick="window.open('<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8');?>role/cessao/nadaconsta.php?idInform=<?php echo urlencode($idInform);?>&codigo=<?php echo urlencode($hc_codigo);?>&idCessao=<?php echo urlencode($_REQUEST['idCessao']);?>&tipoBanco=<?php echo urlencode($_REQUEST['tipoBanco']);?>&total=<?php echo urlencode($i);?>');" >Declarao de Regul.</button>
    <?php } ?>
    
    <?php if($role["Emite_Clausula_beneficiaria"]){ ?>
      <button type="button" class="botaoagg" onClick="imprime(this.form)">Cl&aacute;usula Benefici&aacute;ria</button>
    <?php } ?>

    <?php if($iSubProduto > 10){ ?>
      <button type="button" class="botaoagg" onClick="imprimeCarta(this.form)">Esclarecimentos</button>
    <?php } ?>
    
    <?php if ($Chave_Documento) { ?>
      <button type="button" class="botaoagg" onClick="window.open('<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8').'src/download/'.htmlspecialchars($Chave_Documento, ENT_QUOTES, 'UTF-8').'Distrato.pdf'; ?>', '_blank');">Distrato da Cess&atilde;o</button>      
    <?php } ?>
  </div>

</form>

<script language=javascript>
  function imprime(f) {

<?php if (!in_array($iSubProduto, [11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28])) { ?>

    <?php if ($iSubProduto >= 16 && $iSubProduto <= 30) { ?>

var str = '../cessao/cond_espnovo.php?consulta=1&idInform=<?php echo urlencode($idInform); ?>&agencia=<?php echo urlencode($agencia); ?>'+
'&idAgencia=<?php echo urlencode($idAgencia); ?>&idBanco=<?php echo urlencode($idBanco); ?>&tipoBanco=<?php echo urlencode($tipoBanco); ?>&idCDBB=<?php echo urlencode($idCDBB); ?>&idCDParc=<?php echo urlencode($idCDParc); ?>&idCDOB=<?php echo urlencode($idCDOB); ?>&total=<?php echo urlencode($i); ?>&totalR='+
'&idImporterR1=<?php echo urlencode(""); ?>&comm=gerapdf';

imprimeCarta();

    <?php } else { ?>

var str = '../cessao/cond_esp.php?consulta=1&idInform=<?php echo urlencode($idInform); ?>&agencia=<?php echo urlencode($agencia); ?>'+
'&idAgencia=<?php echo urlencode($idAgencia); ?>&idBanco=<?php echo urlencode($idBanco); ?>&tipoBanco=<?php echo urlencode($tipoBanco); ?>&idCDBB=<?php echo urlencode($idCDBB); ?>&idCDParc=<?php echo urlencode($idCDParc); ?>&idCDOB=<?php echo urlencode($idCDOB); ?>&total=<?php echo urlencode($i); ?>&totalR='+
'&idImporterR1=<?php echo urlencode(""); ?>&comm=gerapdf';

    <?php } ?>

<?php } else { ?>

var str = '../cessao/cond_esp_banco_brasil.php?consulta=1&idInform=<?php echo urlencode($idInform); ?>&agencia=<?php echo urlencode($agencia); ?>'+
'&idAgencia=<?php echo urlencode($idAgencia); ?>&idBanco=<?php echo urlencode($idBanco); ?>&tipoBanco=<?php echo urlencode($tipoBanco); ?>&idCDBB=<?php echo urlencode($idCDBB); ?>&idCDParc=<?php echo urlencode($idCDParc); ?>&idCDOB=<?php echo urlencode($idCDOB); ?>&total=<?php echo urlencode($i); ?>&totalR='+
'&idImporterR1=<?php echo urlencode(""); ?>&comm=gerapdf';

imprimeCarta();

<?php } ?>

window.open(str);

}


function imprimeCarta(f) {

console.log(<?php echo htmlspecialchars($iSubProduto, ENT_QUOTES, 'UTF-8'); ?>);

<?php if (!in_array($iSubProduto, [11, 12, 13, 14, 15, 16])) { ?>

    <?php if ($iSubProduto >= 17 && $iSubProduto <= 30) { ?>

var str2 = '../cessao/carta2.php?consulta=1&idInform=<?php echo urlencode($idInform); ?>&agencia=<?php echo urlencode($agencia); ?>'+
'&idAgencia=<?php echo urlencode($idAgencia); ?>&idBanco=<?php echo urlencode($idBanco); ?>&tipoBanco=<?php echo urlencode($tipoBanco); ?>&idCDBB=<?php echo urlencode($idCDBB); ?>&idCDParc=<?php echo urlencode($idCDParc); ?>&idCDOB=<?php echo urlencode($idCDOB); ?>&total=<?php echo urlencode($i); ?>&totalR='+ 
'&idImporterR1=<?php echo urlencode(""); ?>&comm=gerapdf';

    <?php } ?>

<?php } else { ?>

var str2 = '../cessao/carta.php?consulta=1&idInform=<?php echo urlencode($idInform); ?>&agencia=<?php echo urlencode($agencia); ?>'+
'&idAgencia=<?php echo urlencode($idAgencia); ?>&idBanco=<?php echo urlencode($idBanco); ?>&tipoBanco=<?php echo urlencode($tipoBanco); ?>&idCDBB=<?php echo urlencode($idCDBB); ?>&idCDParc=<?php echo urlencode($idCDParc); ?>&idCDOB=<?php echo urlencode($idCDOB); ?>&total=<?php echo urlencode($i); ?>&totalR='+
'&idImporterR1=<?php echo urlencode(""); ?>&comm=gerapdf';

<?php } ?>
  
window.open(str2);
}


</script>
</div>