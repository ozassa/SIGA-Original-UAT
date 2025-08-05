  <?php
    require_once("../../../navegacao.php");
    
    $namecliente = isset($_POST['nameclient']) ? trim($_POST['nameclient']) : '';
     
    $where = '';
    
    /*if ($namecliente != ''){ 
      $where = " WHERE startValidity >='01-01-2017' and upper(name) LIKE '%". strtoupper($namecliente). "%'";
      $qry = "SELECT policyKey, name, id, segundaVia, i_Produto, dateEmissionP, numParc, Num_Parcelas, warantyInterest
                FROM Inform 
                ".$where." 
                ORDER BY startValidity DESC ";
      $cur = odbc_exec($db,$qry);
    }    */      

    if ($namecliente != '') {
    $where = " WHERE startValidity >= ? AND upper(name) LIKE ?";
    $qry = "SELECT policyKey, name, id, segundaVia, i_Produto, dateEmissionP, numParc, Num_Parcelas, warantyInterest
            FROM Inform
            $where
            ORDER BY startValidity DESC";

    // Preparação da consulta
    $cur = odbc_prepare($db, $qry);

    // Conversão e formatação segura do parâmetro
    $startValidity = '2017-01-01';
    $nameclienteFormatted = '%' . strtoupper($namecliente) . '%';

    // Execução da consulta com os parâmetros
    odbc_execute($cur, [$startValidity, $nameclienteFormatted]);

}

  ?>

    <div class="conteudopagina" >
         <div style="height:65px;">
      <form action="../policy/Policy.php?comm=allPolicies&menu=1" name="formc" method="post">
          <li class="campo3colunas"> 
                <label>Ap&oacute;lices emitidas para:</label>
<input type="text" name="nameclient" id="nameclient" value="<?php echo htmlspecialchars($namecliente, ENT_QUOTES, 'UTF-8'); ?>">
              </li>
              
              <li class="campo3colunas" style="height:40px;"> 
                  <label>&nbsp;</label>
                  <button class="botaoagm" type="button" onClick="formc.submit();">OK</button>
              </li>   
               <div style="clear:both" style="height:1px;">&nbsp;</div>   
         </form>
       </div> 
  <?php   
    $i = 0;

    while(odbc_fetch_row($cur)){
      $pKey         = odbc_result($cur, 1);
      $empresa      = odbc_result($cur, 2);
      $idInform     = odbc_result($cur, 3);
      $pKeyVia      = odbc_result($cur, 4);
      $i_Produto    = odbc_result($cur, 5);
      $DataEmissao  = odbc_result($cur, 6);
      $numParc      = odbc_result($cur, 7);
      $Num_Parcelas = odbc_result($cur, 8);
      $interest     = odbc_result($cur, 9);

      $num_parcelas = ($Num_Parcelas ? $Num_Parcelas : $numParc);

      if($pKey != ""){
        $pFile = $pdfDir.$pKey;

        $i++;
        ?>

        <li class="campo2colunas" style="height:45px; width:660px;"> 
          <labeL><h2><?php echo $empresa; ?> (emitida em <?php echo Convert_Data_Geral(substr($DataEmissao,0,10));?>)</h2></labeL> 
        </li>
        <br>

        <!-- Apólice -->
        <div style="clear:both; height:1px;">&nbsp;</div> 

        <li class="campo2colunas" style="height:30px">
          <label><a class=textoBold href="<?php echo $root.'role/documents/view_files.php?document=apolice&idInform='.$idInform;?>" target=_blank>Ap&oacute;lice</a></label>
        </li>
        <!-- Apólice -->

        <!-- Condições Especiais Juros de Mora -->
        <?php if ($interest) { ?>
          <div style="clear:both; height:1px;">&nbsp;</div> 

          <li class="campo2colunas" style="height:30px">
            <label><a class=textoBold href="<?php echo $root.'role/documents/view_files.php?document=cond_juros&idInform='.$idInform;?>" target=_blank>Condi&ccedil;&otilde;es Especiais Juros de Mora</a></label>
          </li>
        <?php } ?>
        <!-- Condições Especiais Juros de Mora -->

        <!-- Carta -->
        <div style="clear:both; height:1px;">&nbsp;</div> 

        <li class="campo2colunas" style="height:30px">
          <label><a class=textoBold href="<?php echo $root.'role/documents/view_files.php?document=carta&idInform='.$idInform;?>" target=_blank>Carta</a></label>
        </li>
        <!-- Carta -->

        <?php if ($num_parcelas > 0 && $i_Produto == 2) { ?>
          <div style="clear:both; height:1px;">&nbsp;</div> 
          <li class="campo2colunas" style="height:30px">
              <label><h2>Parcelas:</h2></label>
          </li>  

          <?php $i = 1; ?>
          <?php while ($i <= $num_parcelas) { ?>
            <div style="clear:both; height:1px;">&nbsp;</div> 
            <li class="campo2colunas" style="height:30px">
              <label><a class=textoBold href="<?php echo $root.'role/documents/view_files.php?document=parcela&parc='.$i.'&idInform='.$idInform;?>" target=_blank>Parcela <?php echo $i; ?></a></label>
            </li>
            <?php $i++; ?>
          <?php } ?>
        <?php } ?>
        <div style="clear:both">&nbsp;</div> 

      <?php } ?>
    <?php } ?>


  <?php if($i > 0) { ?>
    <div style="clear:both">&nbsp;</div> 
    <form action="../policy/Policy.php" name="form" method="post">
       <input type=hidden name="comm" value="done">
       <input type=hidden name="mot" value="Voltar">
          <li class="campo2colunas">
              <button type="button" class="botaoagm" onClick="this.form.submit();">Voltar</button>
        </li>
    </form>
  <?php } ?>

  <div style="clear:both">&nbsp;</div> 
  </div>
