<?php include_once("../../../navegacao.php");?>

<style>
  p {
    margin-bottom: 7px;
  }
  
  thead th {
    font-size: 10px;
  }

  .p_left {
    width: 10%;
    float: left;
    font-weight: bold;
  }

  .p_right {
    float: left;
  }
</style>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">  

  <div class="p_left">
    <?php if ($Nome_Banco) { ?>
      <p>Banco</p>
    <?php } ?>
    <?php if ($Estado) { ?>
      <p>Estado</p>
    <?php } ?>
    <?php if ($Regiao) { ?>
      <p>Regi&atilde;o</p>
    <?php } ?>
    <?php if ($Nome_Agencia) { ?>
      <p>Ag&ecirc;ncia</p>
    <?php } ?>
    <?php if ($Situacao) { ?>
      <p>Situa&ccedil;&atilde;o</p>
    <?php } ?>
  </div>

  <div class="p_right">
    <?php if ($Nome_Banco) { ?>
      <p><?php echo $Nome_Banco; ?></p>
    <?php } ?>
    <?php if ($Estado) { ?>
      <p><?php echo $Estado; ?></p>
    <?php } ?>
    <?php if ($Regiao) { ?>
      <p><?php echo $Regiao; ?></p>
    <?php } ?>
    <?php if ($Nome_Agencia) { ?>
      <p><?php echo $Nome_Agencia; ?></p>
    <?php } ?>
    <?php if ($Situacao) { ?>
      <p><?php echo $Situacao; ?></p>
    <?php } ?>
  </div>

  <li class="<?php echo !empty($dados) ? "barrabotoes" : ""; ?>" style="list-style:none;*margin-left:-15px;">

    <label>
      <h2>Rela&ccedil;&atilde;o de ap&oacute;lices</h2>
    </label>

    <table summary="" id="" style="font-size: 9px;">
      <thead>
        <tr>
          <th>N&ordm; da Ap&oacute;lice</th>
          <th>Segurado</th>
          <th>Ini. Vig&ecirc;ncia</th>
          <th>Fim. Vig&ecirc;ncia</th>
          <th>Valor Total de Pr&ecirc;mio Emitido</th>
          <th>Valor Total de Pr&ecirc;mio Pago</th>
          <th>Valor Total de Pr&ecirc;mio Vencido</th>
          <th>Valor Total dos Sinistros Pagos</th>
          <th>Valor Total de Sinistro em Reserva</th>
          <th>Valor do Limite M&aacute;ximo de Indeniza&ccedil;&atilde;o da Ap&oacute;lice</th>
          <th>Saldo Dispon&iacute;vel do Limite M&aacute;ximo de Indeniza&ccedil;&atilde;o da Ap&oacute;lice</th>
          <th>Qtde. Compradores Cedidos</th>
        </tr>
      </thead>  

      <?php if(empty($dados)){ ?>
        <tbody><tr><td valign="top" colspan="20" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
      <?php 
        } else { 
          for ($a=0; $a < count($dados); $a++) { ?>
            <tr>
              <td><?php echo $dados[$a]['n_Apolice']; ?></td>
              <td><?php echo $dados[$a]['Segurado']; ?></td>
              <td><?php echo $dados[$a]['d_Inicio_Vigencia']; ?></td>
              <td><?php echo $dados[$a]['d_Fim_Vigencia']; ?></td>
              <td><?php echo $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_Premio_Emitido']; ?></td>
              <td><?php echo $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_Premio_Pago']; ?></td>
              <td><?php echo $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_Premio_Vencido']; ?></td>
              <td><?php echo $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_Sinistro_Pago']; ?></td>
              <td><?php echo $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_Sinistro_Pendente']; ?></td>
              <td><?php echo $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_LMI']; ?></td>
              <td><?php echo $dados[$a]['Sigla_Moeda'].' '.$dados[$a]['v_LMI_Disponivel']; ?></td>
              <td><?php echo $dados[$a]['Qtde_Compradores_Cedidos']; ?></td>
            </tr>
        <?php } ?>
      <?php } ?>    
    </table>
  </li>
  
  <div class="barrabotoes">
    <button class="botaovgm" type="button" onClick="window.location = '<?php echo $host;?>src/role/report/Report.php?comm=policyReport';">Voltar</button>
  </div>      
</div>