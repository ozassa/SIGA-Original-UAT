<?php

$idUser = isset($_SESSION['userID']) ? $_SESSION['userID'] : false;
$id_Cessao = isset($_REQUEST['id_Cessao']) ? $_REQUEST['id_Cessao'] : false;
$id_Parametro = '10090';


$sql = "EXEC SPR_BB_Consulta_Cessao_Direito ?, ?, ?, ?";
$rsSql = odbc_prepare($db, $sql);
odbc_execute($rsSql, ['450', $idUser, $id_Cessao, null]);

$dados = array();
while (odbc_fetch_row($rsSql)) {
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
    "n_Apolice" => $n_Apolice,
    "Segurado" => $Segurado,
    "Banco" => $Banco,
    "Agencia" => $Agencia,
    "Cod_Cessao" => $Cod_Cessao,
    "Data_Solic" => $Data_Solic,
    "Cod_Comprador" => $Cod_Comprador,
    "Comprador" => $Comprador,
    "Pais" => $Pais
  );
}

odbc_free_result($rsSql);

odbc_close($db);

require_once("../../../navegacao.php");
// require_once("../consultaCertificacao.php");

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
    <label>Data Solicita&ccedil;&atilde;o Cancelamento</label>
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

      <?php if (empty($dados)) { ?>
        <tbody>
          <tr>
            <td valign="top" colspan="10" class="dataTables_empty">Nenhum dado retornado na tabela</td>
          </tr>
        </tbody>
      <?php
      } else {
        for ($a = 0; $a < count($dados); $a++) { ?>
          <tr>
            <td><?php echo ($a + 1); ?></td>
            <td><?php echo $dados[$a]['Cod_Comprador']; ?></td>
            <td><?php echo $dados[$a]['Comprador']; ?></td>
            <td><?php echo $dados[$a]['Pais']; ?></td>
          </tr>
        <?php } ?>
      <?php } ?>
    </table>
  </li>
  <? ?>
  <div class="barrabotoes">
    <button class="botaovgm" type="button"
      onClick="window.location = '<?php echo $host; ?>src/role/cessao/Cessao.php?comm=cancelaCessaoDireitoBB';">Voltar</button>
    <button class="botaoagg js-cancelar_cessao" type="button">Cancelar Cess&atilde;o</button>
  </div>
</div>

<script>
  $(document).ready(function () {
    <?php if (isset($_GET['show_modal'])) { ?>
      $(".modal-cancelar").show();
    <?php } ?>

    $(".js-certificado").on("click", function () {
      $(".modal-certificado").show();
    });

    $(".js-cancelar_cessao").on("click", function () {
      $(".modal-cancelar").show();
    });

    $("#close_modal_certificado").on("click", function () {
      $(".modal-certificado").hide();
    });

    $("#close_modal_cancelar").on("click", function () {
      $(".modal-cancelar").hide();
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
      <p>Para cancelar a Cess&atilde;o de Direito, &eacute; obrigat&oacute;rio a Certifica&ccedil;&atilde;o Digital.</p>
    </li>

    <li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
      <button type="button" class="botaovgm" id="close_modal_certificado">Fechar</button>
      <button type="button" class="botaoagg"
        onClick="window.location = '../../../auth_cert/index.php?url=<?php echo urlencode($_SERVER['REQUEST_URI'] . '&show_modal=modal-cancelar'); ?>';">Usar
        certificado</button>
    </li>

  </div>
</div>
<!-- Fim modal -->

<!-- Modal Cancelar -->
<div class="modal-cancelar" style="display:none">
  <div class="bg-black"></div>

  <div class='modal-int'>
    <h1>Aten&ccedil;&atilde;o</h1>
    <div class="divisoriaamarelo"></div>

    <li class="campo2colunas" style="width: 690px;">
      <label>&nbsp;</label>
      <p>Deseja confirmar o cancelamento da Cess&atilde;o de Direito?</p>
    </li>

    <li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
      <button type="button" class="botaovgm" id="close_modal_cancelar">N&atilde;o</button>
      <button type="button" class="botaoagm"
        onClick="window.location = '<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>src/role/cessao/Cessao.php?comm=cancelarCessaoDireitoBB&id_Cessao=<?php echo htmlspecialchars($id_Cessao, ENT_QUOTES, 'UTF-8'); ?>';">Sim</button>

    </li>

  </div>
</div>
<!-- Fim modal -->