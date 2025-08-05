<?php


if (!isset($_SESSION)) {
  session_start();
}

/*\
 * Criação: IT Coface
 * Desenvolvidmento: Elias Vaz
 * Empresa: Interaktiv
 * Data: 12/02/2010
 * Motivo: Liberar DVE`s em atraso ou vencidas
 */
$cquery = "Select
            DVE.id,
            Inf.id as Inform,
            Inf.n_Apolice,
            Inf.name,
            Case Inf.state When 10 Then
       'Apólice'
      When 11 Then
      'Encerrado'
      Else ''
      End as 'status',
            DVE.num as 'Sequencia',
            DVE.inicio as 'inicio',
            IsNull(DateAdd(Day, -1, D.inicio), Inf.endValidity)as Fim,
            DVE.LiberaAtraso,DVE.LiberaVencida,DVE.num,
      DVE.state  as StatusDVE
            From
                        Inform Inf
            Inner Join DVE DVE On
                        DVE.idInform = Inf.id
            Left Join DVE D On
                        D.idInform = Inf.id
                        And D.num = DVE.num + 1 ";


$criterio = '';
$clausula = ' where Inf.state in(10,11) and ';
$junta = '';
$param = []; // Array para os parâmetros

$Segurado = isset($_POST['Segurado']) ? $_POST['Segurado'] : null;
$Apolice = isset($_POST['Apolice']) ? $_POST['Apolice'] : null;
$LiberaAT = isset($_POST['LiberaAtraso']) ? $_POST['LiberaAtraso'] : null;
$LiberaVe = isset($_POST['LiberaVencida']) ? $_POST['LiberaVencida'] : null;
$stateDVE = isset($_POST['stateDVE']) ? $_POST['stateDVE'] : null;

//print '?'.$LiberaAT;


if (isset($_POST['EXECUTAR'])) {
  if ($_POST['EXECUTAR'] == 1) {
    if ($_POST['Segurado'] != "") {
      $criterio .= $junta . " Inf.name like ?";
      $param[] = '%' . strtoupper($_POST['Segurado']) . '%';
      $junta = " and ";
    }
    if ($_POST['Apolice'] != "") {
      $criterio .= $junta . " Inf.n_Apolice = ?";
      $param[] = $_POST['Apolice'];
      $junta = " and ";

    }
  }
}

$exec = null;
if ($criterio != '') {
  $cquery .= $clausula . $criterio . " order by Inf.name,Inf.n_Apolice,DVE.num ";
}


//print $cquery;





//print $cquery;
$and = '';
$and1 = '';

$liberar = isset($_POST['Liberar']) ? $_POST['Liberar'] : 0;
// as regras abaixo atualizam os status da liberação da DVE
if ($liberar == 1) {

  $resAt = '';
  foreach ($LiberaAT as $value) {
    $resAt .= $and . $value;
    $and = ',';
  }

  $resVe = '';
  foreach ($LiberaVe as $value2) {
    $resVe .= $and1 . $value2;
    $and1 = ',';
  }

// Atualiza LiberaAtraso = 1 para os IDs selecionados
if (!empty($LiberaAT)) {
    $upAt = 'update DVE set LiberaAtraso = 1 where id in(' . implode(',', array_fill(0, count($LiberaAT), '?')) . ')';
    $stmtAt = odbc_prepare($db, $upAt);
    if ($stmtAt) {
        odbc_execute($stmtAt, $LiberaAT);
        odbc_free_result($stmtAt);
    }
}

// Atualiza LiberaVencida = 1 para os IDs selecionados
if (!empty($LiberaVe)) {
    $upVe = 'update DVE set LiberaVencida = 1 where id in(' . implode(',', array_fill(0, count($LiberaVe), '?')) . ')';
    $stmtVe = odbc_prepare($db, $upVe);
    if ($stmtVe) {
        odbc_execute($stmtVe, $LiberaVe);
        odbc_free_result($stmtVe);
    }
}

// Atualiza o status da dve usando parâmetros
if (!empty($stateDVE)) {
    foreach ($stateDVE as $value3) {
        $resVe1 = explode('|', $value3);
        if (count($resVe1) == 2) {
            $upVe1 = 'update DVE set state = ? where id = ?';
            $stmtVe1 = odbc_prepare($db, $upVe1);
            if ($stmtVe1) {
                odbc_execute($stmtVe1, array($resVe1[0], $resVe1[1]));
                odbc_free_result($stmtVe1);
            }
        }
    }
}
  $_SESSION['messenger'] = 'Atualizações realizadas com sucesso.';

  //print $upAt.'<br>';
  //print  $upVe.'<br>';
  ?>
  <script type="text/javascript">
window.location = '<?php echo htmlspecialchars($root . 'role/dve/Dve.php?comm=libera_dve&segurado=' . urlencode($_POST['Segurado']) . '&Apolice=' . urlencode($_POST['Apolice']) . '&msge=1', ENT_QUOTES, 'UTF-8'); ?>';
  </script>
<?php }
if (isset($_POST['EXECUTAR'])) {
  if ($_POST['EXECUTAR'] == 1) {
    if (!empty($param)) {
      $result = odbc_prepare($db, $cquery);
      odbc_execute($result, $param) ? $result : false;
    } else {
      $result = odbc_exec($db, $cquery);
    }
    if ($result) {
      $exec = isset($_POST['EXECUTAR']) ? $_POST['EXECUTAR'] : null;
    }
  }
}

?>

<script language="javascript">
  /*----------------------------------------------------------------------------
  Formatação para qualquer mascara
  -----------------------------------------------------------------------------*/
  function formatar(src, mask) {
    var i = src.value.length;
    var saida = mask.substring(0, 1);
    var texto = mask.substring(i)
    if (texto.substring(0, 1) != saida) {
      src.value += texto.substring(0, 1);
    }
  }

  function exportarExcel() {
    document.getElementById("Exportar").value = 1;
    consultarDVE.submit();
  }

  function marcarckb() {
    document.getElementById('Liberar').value = 1;
    //submit();
  }


</script>

<?php require_once("../../../navegacao.php"); ?>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">
  <?php
  $msge = isset($_REQUEST['msge']) ? $_REQUEST['msge'] : 0;
  if ($msge == 1) {
    ?>
    <script>verErro('Atualiza&ccedil;&otilde;es realizadas com sucesso.');</script><?php

  }

  ?>
<form name="liberaDVE" id="liberaDVE" action="<?php echo htmlspecialchars($root . 'role/dve/Dve.php', ENT_QUOTES, 'UTF-8'); ?>" method="post">
    <ul>
      <li class="campo2colunas">
        <label>Segurado</label>
<input type="text" name="Segurado" id="Segurado" value="<?php echo htmlspecialchars($Segurado, ENT_QUOTES, 'UTF-8'); ?>">
      </li>

      <li class="campo2colunas"><label>Ap&oacute;lice</label>
<input type="text" name="Apolice" id="Apolice" value="<?php echo htmlspecialchars($Apolice, ENT_QUOTES, 'UTF-8'); ?>">
      </li>
      <li class="barrabotoes" style="list-style:none;">
        <button class="botaoagm" type="button" onClick="javascript: liberaDVE.submit();">Pesquisa</button>
        <button name="liberar" type="submit" class="botaoagg" onclick="marcarckb();" <?php if ($exec != 1)
          echo 'disabled'; ?>>Liberar Selecionados</button>
        <input type="hidden" name="Liberar" id="Liberar" value="">
      </li>
    </ul>
    <input type="hidden" name="comm" value="libera_dve">
    <input type="hidden" name="EXECUTAR" value="1">



    <table summary="" id="" class="tabela01">
      <thead>
        <tr>
          <th scope="col">Sequ&ecirc;ncia</th>
          <th scope="col">Per&iacute;odo DVN</th>
          <th scope="col" style="text-align:center">Libera em Atraso</th>
          <th scope="col" style="text-align:center">Libera Vencida</th>
          <th scope="col" style="text-align:center">Situa&ccedil;&atilde;o Per&iacute;odo</th>
        </tr>
      </thead>
      <?php
      $apoNum = '';
      $nomeSeg = '';
      $i = 0;

      ?>

      <tbody>

        <?php



        if (isset($result)) {
          while (odbc_fetch_row($result)) { ?>
            <?php

            if ($i % 2 == 0) {
              $cor = 'style="background-color:#FFF"';
            } else {
              $cor = '';
            }

            if (odbc_result($result, 'n_Apolice') != $apoNum || odbc_result($result, 'name') != $nomeSeg) {
              $apoNum = odbc_result($result, 'n_Apolice');
              $nomeSeg = odbc_result($result, 'name');
              ?>
              <tr style="background:#999">
                <td>
                  <font color="#FFFFFF"><strong>Ap&oacute;lice</strong></font>
                </td>
                <td>
                  <font color="#FFFFFF"><strong>Nome do segurado</strong></font>
                </td>
                <td>
                  <font color="#FFFFFF"><strong>Situa&ccedil;&atilde;o</strong></font>
                </td>
                <td>.&nbsp;</td>
                <td>.&nbsp;</td>
              </tr>

              <tr>
                <td><?php echo odbc_result($result, 'n_Apolice'); ?></td>
                <td><?php echo odbc_result($result, 'name'); ?></td>
                <td><?php echo (odbc_result($result, 'status')); ?></td>
                <td>.&nbsp;</td>
                <td>.&nbsp;</td>
              </tr>

            <?php
            }


            ?>
<tr <?php echo htmlspecialchars($cor, ENT_QUOTES, 'UTF-8'); ?>>
              <td><?php echo odbc_result($result, 'num'); ?></td>
              <td>
                <?php echo ymd2dmy(odbc_result($result, 'inicio')); ?>&nbsp;at&eacute;&nbsp;<?php echo ymd2dmy(odbc_result($result, 'Fim')); ?>
              </td>
              <td style="text-align:center">
                <div class="formopcao">
                  <input type="checkbox" <?php echo (odbc_result($result, 'LiberaAtraso') > 0 ? 'checked' : ''); ?>
                    name="LiberaAtraso[]" id="LiberaAtraso" value="<?php echo odbc_result($result, 'id'); ?>" onclick="">
                  <!--<input type="hidden" name="libAt[]" id="LibAt" value="">-->
                </div>
              </td>
              <td>
                <div class="formopcao">
                  <input type="checkbox" <?php echo (odbc_result($result, 'LiberaVencida') > 0 ? 'checked' : ''); ?>
                    name="LiberaVencida[]" id="LiberaVencida" value="<?php echo odbc_result($result, 'id'); ?>" onclick="">
                  <!--<input type="hidden" name="libVe[]" id="LibVe" value="">-->
                </div>
              </td>
              <td>
                <?php $sql = "select
                SituacaoDVN.i_Item As IDSituacao,
                SituacaoDVN.Descricao_Item As DescSituacao
              From
                Campo_Item SituacaoDVN
              Where
                SituacaoDVN.i_Campo = 600
                And SituacaoDVN.Situacao = 0
              Order By
                SituacaoDVN.i_Item
              ";
                $res = odbc_exec($db, $sql);
                ?>
                <select name="stateDVE[]" id="stateDVE" style="width:155px !important;">
                  <option value="">Selecione</option>
                  <?php while (odbc_fetch_row($res)) { ?>
                    <option value="<?php echo odbc_result($res, 'IDSituacao') . '|' . odbc_result($result, 'id'); ?>" <?php echo (odbc_result($res, 'IDSituacao') == odbc_result($result, 'StatusDVE') ? 'Selected' : ''); ?>>
                      <?php echo odbc_result($res, 'DescSituacao'); ?></option>
                  <?php } ?>
                </select>

              </td>
            </tr>
            <?php
            $i++;
          }
        }

        ?>
      </tbody>

      <tfoot>
        <tr>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
        </tr>
      </tfoot>
    </table>

  </form>

  <br clear="all" />
  <br clear="all" />
</div>