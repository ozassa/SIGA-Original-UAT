<?php include_once("../../../navegacao.php");?>

<style>
  .report_table {
    margin-bottom: 25px;
  }

  .report_table_left {
    width: 48%;
    float: left;
  }

  .report_table_right {
    width: 48%;
    float: right;
  }
</style>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">  
  <div class="report_table_left">
    <div class="report_table">
      <h2>Ap&oacute;lices agrupadas por Estado</h2>
      <table summary="" id="">
        <thead>
          <tr>
            <th>UF</th>
            <th>Estado</th>
            <th>Ap&oacute;lices Vigentes</th>
            <th>Ap&oacute;lices Encerradas</th>
          </tr>
        </thead>  

        <?php if(empty($dados_estado)){ ?>
          <tbody><tr><td valign="top" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
        <?php 
          } else { 
            $totalEstVig = 0;
            $totalEstEnc = 0;
            for ($a=0; $a < count($dados_estado); $a++) { 
              $totalEstVig += $dados_estado[$a]['ContVig'];
              $totalEstEnc += $dados_estado[$a]['ContEnc'];
              ?>
              <tr>
                <td><?php echo $dados_estado[$a]['UF']; ?></td>
                <td><?php echo $dados_estado[$a]['Estado']; ?></td>
                <td><a href="../report/Report.php?comm=policyReportDetail&uf=<?php echo $dados_estado[$a]['UF'] ?>&situacao=10"><?php echo $dados_estado[$a]['ContVig']; ?></a></td>
                <td><a href="../report/Report.php?comm=policyReportDetail&uf=<?php echo $dados_estado[$a]['UF'] ?>&situacao=11"><?php echo $dados_estado[$a]['ContEnc']; ?></a></td>        
              </tr>
          <?php } ?>
          <tfoot>
            <tr>
              <th colspan="2">TOTAL</th>
              <th><?php echo $totalEstVig; ?></th>
              <th><?php echo $totalEstEnc; ?></th>
            </tr>
          </tfoot>
        <?php } ?>    
      </table>
    </div>  

    <div class="report_table">
      <h2>Ap&oacute;lices agrupadas por Regi&atilde;o</h2>
      <table summary="" id="">
        <thead>
          <tr>
            <th>Regi&atilde;o</th>
            <th>Ap&oacute;lices Vigentes</th>
            <th>Ap&oacute;lices Encerradas</th>
          </tr>
        </thead>  

        <?php if(empty($dados_regiao)){ ?>
          <tbody><tr><td valign="top" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
        <?php 
          } else { 
            $totalRegVig = 0;
            $totalRegEnc = 0;
            for ($a=0; $a < count($dados_regiao); $a++) { 
              $totalRegVig += $dados_regiao[$a]['ContVig'];
              $totalRegEnc += $dados_regiao[$a]['ContEnc'];
              ?>
              <tr>
                <td><?php echo $dados_regiao[$a]['Regiao']; ?></td>
                <td><a href="../report/Report.php?comm=policyReportDetail&Id_Regiao=<?php echo $dados_regiao[$a]['i_Regiao'] ?>&situacao=10"><?php echo $dados_regiao[$a]['ContVig']; ?></a></td>
                <td><a href="../report/Report.php?comm=policyReportDetail&Id_Regiao=<?php echo $dados_regiao[$a]['i_Regiao'] ?>&situacao=11"><?php echo $dados_regiao[$a]['ContEnc']; ?></a></td>        
              </tr>
          <?php } ?>
          <tfoot>
            <tr>
              <td>TOTAL</td>
              <td><?php echo $totalRegVig; ?></td>
              <td><?php echo $totalRegEnc; ?></td>
            </tr>
          </tfoot>
        <?php } ?> 
      </table>
    </div>
  </div>
  
  <div class="report_table_right">
    <div class="report_table">
      <h2>Ap&oacute;lices agrupadas por Ag&ecirc;ncia</h2>
      <table summary="" id="" class="no-sort">
        <thead>
          <tr>
            <th>C&oacute;digo</th>
            <th>Ag&ecirc;ncia</th>
            <th>Ap&oacute;lices Vigentes</th>
            <th>Ap&oacute;lices Encerradas</th>
          </tr>
        </thead>  

        <?php if(empty($dados_agencia)){ ?>
          <tbody><tr><td valign="top" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
        <?php 
          } else { 
            $totalAgVig = 0;
            $totalAgEnc = 0;
            for ($a=0; $a < count($dados_agencia); $a++) { 
              $totalAgVig += $dados_agencia[$a]['ContVig'];
              $totalAgEnc += $dados_agencia[$a]['ContEnc'];
              ?>
              <tr>
                <td><?php echo $dados_agencia[$a]['Cod_Agencia']; ?></td>
                <td><?php echo $dados_agencia[$a]['Agencia']; ?></td>
                <td><a href="../report/Report.php?comm=policyReportDetail&Id_Agencia=<?php echo $dados_agencia[$a]['i_Agencia'] ?>&situacao=10"><?php echo $dados_agencia[$a]['ContVig']; ?></a></td>
                <td><a href="../report/Report.php?comm=policyReportDetail&Id_Agencia=<?php echo $dados_agencia[$a]['i_Agencia'] ?>&situacao=11"><?php echo $dados_agencia[$a]['ContEnc']; ?></a></td>        
              </tr>
          <?php } ?>
          <tfoot>
            <tr>
              <td colspan="2">TOTAL</td>
              <td><?php echo $totalAgVig; ?></td>
              <td><?php echo $totalAgEnc; ?></td>
            </tr>
          </tfoot>
        <?php } ?> 
      </table>
    </div>
  </div>

  <div class="barrabotoes">
    <button class="botaovgm" type="button" onClick="window.location = '<?php echo $host;?>src/role/report/Report.php?comm=index';">Voltar</button>
  </div>  
</div>