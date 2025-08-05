<?php

  $idUser = isset($_SESSION['userID']) ? $_SESSION['userID'] : false;
  $id_Cessao = isset($_REQUEST['id_Cessao']) ? $_REQUEST['id_Cessao'] : false;
  $id_Parametro = '10030';

  $rsSql = odbc_prepare($db, "EXEC SPR_BB_Consulta_Cessao_Direito ?, ?, ?, NULL");
odbc_execute($rsSql, ['150', $idUser, $id_Cessao]);

  $dados = array();
  while(odbc_fetch_row($rsSql)) {
    $idInform = odbc_result($rsSql, 'Id_Inform');
    $n_Apolice = odbc_result($rsSql, "n_Apolice");
    $Segurado = odbc_result($rsSql, "Segurado");
    $Banco = odbc_result($rsSql, "Banco");
    $Agencia = odbc_result($rsSql, "Agencia");
    $Cod_Cessao = odbc_result($rsSql, "Cod_Cessao");
    $Data_Solic = Convert_Data_Geral(substr(odbc_result($rsSql, "Data_Solic"), 0, 10));
    $Cod_Comprador = odbc_result($rsSql, "Cod_Comprador");
    $Comprador = odbc_result($rsSql, "Comprador");
    $Pais = odbc_result($rsSql, "Pais");

    $dados[] = array(
      "n_Apolice"       => $n_Apolice,
      "Segurado"        => $Segurado,
      "Banco"           => $Banco,
      "Agencia"         => $Agencia,
      "Cod_Cessao"      => $Cod_Cessao,
      "Data_Solic"      => $Data_Solic,
      "Cod_Comprador"   => $Cod_Comprador,
      "Comprador"       => $Comprador,
      "Pais"            => $Pais
    );
  }

  odbc_free_result($rsSql);

  

  require_once("../../../navegacao.php");
  require_once("../consultaCertificacao.php");
      
?>

<div class="conteudopagina">

  <li class="campo3colunas" style="width: 220px;"> 
    <label>Ap&oacute;lice</label>
    <?php echo $n_Apolice; ?>
  </li>

  <li class="campo3colunas" style="width: 465px;"> 
    <label>Nome do Segurado</label>
    <?php echo $Segurado; ?>
  </li>

  <li class="campo3colunas" style="width: 220px;"> 
    <label>Banco</label>
    <?php echo $Banco; ?>
  </li>

  <li class="campo3colunas" style="width: 285px;"> 
    <label>Ag&ecirc;ncia</label>
    <?php echo $Agencia; ?>
  </li>

  <li class="campo3colunas" style="width: 190px;"> 
    <label>Cod. Cess&atilde;o</label>
    <?php echo $Cod_Cessao; ?>
  </li>

  <li class="campo3colunas" style="width: 190px;"> 
    <label>Data Solicita&ccedil;&atilde;o</label>
    <?php echo $Data_Solic; ?>
  </li>

  <br clear="all">

  <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">  
    <label>
      <h2>Compradores</h2>
    </label>

    <table summary="" id="">
      <thead>
        <tr>
          <th></th>
          <th>Cod. Comprador</th>
          <th>Nome Comprador</th>
          <th>Pa&iacute;s</th>
        </tr>
      </thead>  

      <?php if(empty($dados)){ ?>
        <tbody><tr><td valign="top" colspan="10" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
      <?php 
        } else { 
          for ($a=0; $a < count($dados); $a++) { ?>
            <tr>
              <td><?php echo ($a+1); ?></td>
              <td><?php echo $dados[$a]['Cod_Comprador']; ?></td>
              <td><?php echo $dados[$a]['Comprador']; ?></td>
              <td><?php echo $dados[$a]['Pais']; ?></td>
            </tr>
        <?php } ?>
      <?php } ?>    
    </table>
  </li>

  <div class="barrabotoes">
    <button class="botaovgm" type="button" onClick="window.location = '<?php echo $host;?>src/role/cessao/Cessao.php?comm=emiteCessaoDireito';">Voltar</button>
    <button class="botaovgm <?php echo $perm_cert ? 'js-recusar_cessao' : 'js-certificado'; ?>" type="button">Recusar</button>
    <button class="botaoagm <?php echo $perm_cert ? 'js-aceitar_cessao' : 'js-certificado'; ?>" type="button">Aceitar</button>
  </div>  
</div>

<script>
  $(document).ready(function(){
    $(".js-certificado").on("click", function(){
      $(".modal-certificado").show();
    });

    $(".js-recusar_cessao").on("click", function(){
      $(".modal-recusar").show();
    });

    $(".js-aceitar_cessao").on("click", function(){
      $(".modal-aceitar").show();
    });
    
    $("#close_modal_certificado").on("click", function(){
      $(".modal-certificado").hide();
    });
    
    $("#close_modal_recusar").on("click", function(){
      $(".modal-recusar").hide();
    });
    
    $("#close_modal_aceitar").on("click", function(){
      $(".modal-aceitar").hide();
    });
  });
</script>

<!-- Modal Certificado -->
  <div class="modal-certificado" style="display:none">
    <div class="bg-black"></div>

    <div class='modal-int'>
      <h1>Aten&ccedil;&atilde;o</h1>
      <div class="divisoriaamarelo"></div>

        <li class="campo2colunas" style="width: 690px;">
          <label>&nbsp;</label>
          <p>Para aceitar/recusar a Cess&atilde;o de Direito, &eacute; obrigat&oacute;rio a Certifica&ccedil;&atilde;o Digital.</p>
        </li>

        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
          <button type="button" class="botaovgm" id="close_modal_certificado">Fechar</button>
          <button type="button" class="botaoagg" onClick="window.location = '../../../auth_cert/index.php';">Usar certificado</button>
        </li>

    </div>
  </div>
<!-- Fim modal -->

<!-- Modal Recusar -->
  <div class="modal-recusar" style="display:none">
    <div class="bg-black"></div>

    <div class='modal-int'>
      <h1>Aten&ccedil;&atilde;o</h1>
      <div class="divisoriaamarelo"></div>

        <li class="campo2colunas" style="width: 690px;">
          <label>&nbsp;</label>
          <p>Deseja recusar a Cess&atilde;o de Direito?</p>
        </li>

        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
          <button type="button" class="botaovgm" id="close_modal_recusar">N&atilde;o</button>
          <button type="button" class="botaoagm" onClick="window.location = '<?php echo $host;?>src/role/cessao/Cessao.php?comm=recusaCessaoDireito&id_Cessao=<?php echo htmlspecialchars($id_Cessao, ENT_QUOTES, 'UTF-8'); ?>';">Sim</button>
        </li>

    </div>
  </div>
<!-- Fim modal -->

<!-- Modal Aceitar -->
  <div class="modal-aceitar" style="display:none">
    <div class="bg-black"></div>

    <div class='modal-int'>
      <h1>Aten&ccedil;&atilde;o</h1>
      <div class="divisoriaamarelo"></div>

        <li class="campo2colunas" style="width: 690px;">
          <label>&nbsp;</label>
          <p>Deseja aceitar a Cess&atilde;o de Direito?</p>
        </li>

        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
          <button type="button" class="botaovgm" id="close_modal_aceitar">N&atilde;o</button>
          <button type="button" class="botaoagm" onClick="window.location = '<?php echo $host;?>src/role/cessao/Cessao.php?comm=aceitaCessaoDireito&id_Cessao=<?php echo htmlspecialchars($id_Cessao, ENT_QUOTES, 'UTF-8'); ?>
';">Sim</button>
        </li>

    </div>
  </div>
<!-- Fim modal -->