<script>
  function seleciona(obj) {
    form = obj.form;

    if (obj.selectedIndex != 1) {
      form.formfocus.value = '';
      form.fieldfocus.value = '';
    }

    form.submit();
  }
</script>

<?php

function getStatus($key)
{
  $value = 'indefinido';

  switch ($key) {
    case 0:
      $value = 'Em cadastramento';
      break;
    case 1:
      $value = 'Aguard. Aprova&ccedil;&atilde;o';
      break;
    case 2:
      $value = 'V&aacute;lido';
      break;
    case 3:
      $value = 'Cancelado';
      break;
    case 4:
      $value = 'Em Cancelamento';
      break;
  }

  return $value;
}

$idInform = $_REQUEST['idInform'];
$idBanco = isset($_REQUEST['idBanco']) ? $_REQUEST['idBanco'] : 0;
$codBanco = isset($_REQUEST['codBanco']) ? $_REQUEST['codBanco'] : 0;
$tipoBanco = isset($_REQUEST['tipoBanco']) ? $_REQUEST['tipoBanco'] : 0;

$sql = "SELECT Inf.Ga, PME.nu_banco FROM Inform Inf INNER JOIN ParModEsp PME ON PME.idInform = Inf.id WHERE Inf.id = ?";
$ban = odbc_prepare($db, $sql);
odbc_execute($ban, [$idInform]);

$ga = odbc_result($ban, "Ga");
$idBanco = odbc_result($ban, "Ga");

odbc_free_result($ban);

$q = "SELECT tipo FROM Banco WHERE id = ?";
$ban = odbc_prepare($db, $q);
odbc_execute($ban, [$idBanco]);
$tipoBanco = odbc_result($ban, 1);

odbc_free_result($ban);


require_once("../../../navegacao.php");
?>

<div class="conteudopagina">
  <?php
  if (isset($msgAg) && $msgAg != '') {
    echo "<script>verErro('" . $msgAg . "')</script>";
  }
  ?>

  <form action="<?php echo $root; ?>role/cessao/Cessao.php#cessao" method="post" name="bancom">
    <input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="idBanco" value="<?php echo htmlspecialchars($idBanco, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="comm" value="cessaoBB">
    <input type="hidden" name="codBanco" value="<?php echo htmlspecialchars($codBanco, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="tipoBanco" value="<?php echo htmlspecialchars($tipoBanco, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="formfocus" value="bancom">
    <input type="hidden" name="fieldfocus" value="agencia">
    <input type="hidden" name="ga" value="<?php echo $ga; ?>">

    <?php
    $sql = "SELECT nu_banco FROM ParModEsp WHERE idInform = ?";
    $cur3 = odbc_prepare($db, $sql);
    odbc_execute($cur3, [$idInform]);

    if ($linha = odbc_fetch_array($cur3)) {
      $tp_banco = $linha['nu_banco'];
    }
    odbc_free_result($cur3);

    ?>

    <li class="campo3colunas">
      <label>Banco</label>
      <?php
      $name = "idBanco";
      $acao = "onChange=seleciona(this)";
      $empty = "Selecione o Banco";

      $sql = "Select B.id As Id, Upper(B.name) As Nome_Banco, B.codigo as Cod_Banco, B.idUser, B.tipo 
    From Banco B Where B.tipo = $tp_banco";

      $sel = '';
      if ($tipoBanco) {
        $sel = $idBanco;
      }

      require_once("../../interf/Select.php");
      ?>
    </li>

    <?php
    if ($tipoBanco == 1) { ?>
      <li class="campo3colunas">
        <label>C&oacute;digo da Ag&ecirc;ncia (sem DV)</label>
        <input type="text" name="agencia" size="6" class="caixa" maxlength="4" onkeypress="keyhandler"
          onkeyup="proximo(this, 4, this.form.botao)">
      </li>

      <li class="barrabotoes" style="list-style:none;*margin-left:-15px;*margin-top:20px;">
        <!--Alterador por Tiago V N - Elumini - 01/09/2006-->
        <button type="button" class="botaoagm" onClick="
      if (this.form.agencia.value == '') {
        verErro('Campo Ag&ecirc;ncia n&atilde;o pode ser vazio.'); 
      } else {
        this.form.comm.value = 'selImp';
        this.form.submit();
      }">Continuar</button>
      </li>
      <?php
    } else if ($tipoBanco == 2) { ?>
        <li class="campo3colunas">
          <label>C&oacute;digo da Ag&ecirc;ncia (sem DV)</label>
          <input type="text" name="agencia" class="caixa" maxlength="4" onkeypress="keyhandler"
            onkeyup="proximo(this, 4, this.form.botao)">
        </li>

        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;*margin-top:20px;">
          <button type="button" class="botaoagm" onClick="
      if (this.form.agencia.value == '') {
        verErro('Campo Ag&ecirc;ncia n&atilde;o pode ser vazio.'); 
      } else {
        this.form.comm.value = 'selImp';
        this.form.submit();
      }">Continuar</button>
        </li>
      <?php
    }
    ?>
  </form>

  <?php if (!$tipoBanco) { ?>
    <br clear="all">
  <?php } ?>

  <?php


  ?>

  <label>
    <h2>Rela&ccedil;&atilde;o de cess&otilde;es de direito</h2>
  </label>
  <table class="tabela01">
    <thead>
      <tr>
        <th>C&oacute;d. Cess&atilde;o</th>
        <th>Banco</th>
        <th>Ag&ecirc;ncia</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      //echo $query;
      $query = "
    SELECT DISTINCT cdbb.codigo,
                    ag.codigo          AS agencia,
                    cdbb.status,
                    ag.name            AS nameAgencia,
                    cdbb.dateClient,
                    cdbb.id,
                    'Banco do Brasil S/A' AS name,
                    1                  AS idBanco,
                    1                  AS tipo,
                    ag.id              AS idAgencia
      FROM CDBB cdbb
      JOIN Agencia ag ON ag.id = cdbb.idAgencia
      LEFT JOIN CDBBDetails cdd ON cdd.idCDBB = cdbb.id
      LEFT JOIN Importer imp ON imp.id = cdd.idImporter
      WHERE cdbb.idInform = ?

    UNION

    SELECT DISTINCT cdob.codigo,
                    cdob.agencia,
                    cdob.status,
                    cdob.name          AS nameAgencia,
                    cdob.dateClient,
                    cdob.id,
                    bc.name,
                    bc.id              AS idBanco,
                    3                  AS tipo,
                    0                  AS idAgencia
      FROM CDOB cdob
      JOIN Banco bc ON cdob.idBanco = bc.id
      LEFT JOIN CDOBDetails cdd ON cdd.idCDOB = cdob.id
      LEFT JOIN Importer imp ON imp.id = cdd.idImporter
      WHERE cdob.idInform = ?

    UNION

    SELECT DISTINCT cdpc.codigo,
                    ag.codigo          AS agencia,
                    cdpc.status,
                    ag.name            AS nameAgencia,
                    cdpc.dateClient,
                    cdpc.id,
                    bc.name,
                    cdpc.idBanco,
                    bc.tipo            AS tipo,
                    ag.id              AS idAgencia
      FROM CDParc cdpc
      JOIN Banco bc ON cdpc.idBanco = bc.id
      JOIN Agencia ag ON ag.id = cdpc.idAgencia
      LEFT JOIN CDParcDetails cdp ON cdp.idCDParc = cdpc.id
      LEFT JOIN Importer imp ON imp.id = cdp.idImporter
      WHERE cdpc.idInform = ?

    ORDER BY codigo
";

$cur = odbc_prepare($db, $query);
odbc_execute($cur, [$idInform, $idInform, $idInform]);


      $i = 0;
      while (odbc_fetch_row($cur)) {
        $i++;
        //$idImporter = odbc_result($cur,'id');
        $status = odbc_result($cur, 3);
        $agencia = odbc_result($cur, 2);
        $codigo = odbc_result($cur, 1);
        $idBanco = odbc_result($cur, 8);
        $tipoBanco = odbc_result($cur, 9);
        $idAgencia = odbc_result($cur, 10);
        if ($codigo == '') {
          $codigo = "sem cdigo";
        } else {
          $dateEnv = odbc_result($cur, 5);
          list($ano, $mes, $dia) = explode('-', $dateEnv);
          $codigo = ($codigo . "/" . $ano);
        }
        ?>
        <tr <?php echo ((($i % 2) != 0) ? "" : 'class="odd"'); ?>>
          <?php  //Alterado por Tiago V N - Elumini - 13/02/2006
            if ($tipoBanco == 1) { //Banco do Brasil
              $idCDBB = odbc_result($cur, 6);

              if ($status != "1") { ?>
              <td width="15%">
                <a
                  href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . "/role/cessao/Cessao.php?comm=consImpCliente&idInform=" . urlencode($idInform) . "&agencia=" . urlencode($agencia) . "&idAgencia=" . urlencode($idAgencia) . "&banco=1&idCDBB=" . urlencode($idCDBB) . "&idBanco=" . urlencode($idBanco) . "&status=" . urlencode($status) . "&tipoBanco=" . urlencode($tipoBanco) . "#cessao"; ?>">
                  <?php echo htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8'); ?>
                </a>
              </td>
            <?php } else { ?>
              <td width="15%">
                <a
                  href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . "role/cessao/Cessao.php?comm=consImpCliente&idInform=" . urlencode($idInform) . "&agencia=" . urlencode($agencia) . "&idAgencia=" . urlencode($idAgencia) . "&banco=1&idCDBB=" . urlencode($idCDBB) . "&idBanco=" . urlencode($idBanco) . "&status=" . urlencode($status) . "&tipoBanco=" . urlencode($tipoBanco) . "#cessao"; ?>">
                  <?php echo htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8'); ?>
                </a>
              </td>
            <?php }
            } else if ($tipoBanco == 2) {  // Banco Parceiros
              $idCDParc = odbc_result($cur, 6);
              ?>
              <td width="15%">
                <a
                  href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . "/role/cessao/Cessao.php?comm=consImpCliente&idInform=" . urlencode($idInform) . "&agencia=" . urlencode($agencia) . "&idAgencia=" . urlencode($idAgencia) . "&banco=1&idCDParc=" . urlencode($idCDParc) . "&idBanco=" . urlencode($idBanco) . "&status=" . urlencode($status) . "&tipoBanco=" . urlencode($tipoBanco) . "#cessao"; ?>">
                <?php echo htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8'); ?>
                </a>
              </td>
          <?php } else {
              $idCDOB = odbc_result($cur, 6);
              ?>
              <td width="15%">
                <a
                  href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . "/role/cessao/Cessao.php?comm=consImpCliente&idInform=" . urlencode($idInform) . "&agencia=" . urlencode($agencia) . "&banco=1&idCDOB=" . urlencode($idCDOB) . "&idBanco=" . urlencode($idBanco) . "&status=" . urlencode($status) . "&tipoBanco=" . urlencode($tipoBanco) . "#cessao"; ?>">
                <?php echo htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8'); ?>
                </a>
              </td>
          <?php } ?>

          <td><?php echo odbc_result($cur, 7); ?></td>
          <!--td><?php if ($status == 0) { ?><a href=<?php echo htmlspecialchars("$root/role/cessao/Cessao.php?comm=selImp&idInform=$idInform&agencia=$agencia&banco=1#cessao", ENT_QUOTES, 'UTF-8'); ?>><?php } ?><?php echo odbc_result($cur, 1); ?><?php if ($status == 0) { ?></a><?php } ?></td-->
          <!--td><?php echo odbc_result($cur, 2); ?></td-->
          <td>(<?php echo $agencia; ?>) <?php echo odbc_result($cur, 4); ?></td>
          <td><?php echo getStatus($status); ?></td>
        </tr>
      <?php } // while desse código lixo
      odbc_free_result($cur);
      if ($i == 0) {
        ?>
        <TR class="">
          <TD align="center" colspan=4>Nenhuma Cess&atilde;o Cadastrada</TD>
        </TR>

      <?php }
      $total = $i;
      ?>
    </tbody>
  </table>


  <form action="<?php echo $root; ?>role/client/Client.php" method="get">
    <input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="comm">
    <div class="barrabotoes">
      <button type="button" class="botaovgm"
        onClick="this.form.comm.value='open';this.form.submit()">Voltar</button>
    </div>
  </form>


  <script language="javascript">
    var keypressed;

    function keyhandler(e) {
      if (document.layers)
        keypressed = e.which;
      else
        keypressed = window.event.keyCode;
    }

    function proximo(atual, size, prox) {
      if (atual.value.length == size) {
        prox.focus();
      }
    }
  </script>

</div>