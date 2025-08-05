<?php if (isset($congratulation)) { ?>
  <label>Usu&aacute;rio e Senha Cadastrados com Sucesso</label>
<?php }

if (!isset($tipo_apolice)) {
  $tipo_apolice = isset($_REQUEST['tipo_apolice']) ? $_REQUEST['tipo_apolice'] : '';
}

if (!isset($vig)) {
  $vig = isset($_REQUEST['vig']) ? $_REQUEST['vig'] : '';
}

if (!isset($sisseg)) {
  $sisseg = isset($_REQUEST['sisseg']) ? $_REQUEST['sisseg'] : '';
}


include_once('../../../navegacao.php');

if ($field->getField("idNotification")) {
  $_SESSION['idNotification'] = $field->getField("idNotification");
  $idNotification = $field->getField("idNotification");
} else {
  $idNotification = isset($_SESSION['idNotification']) ? $_SESSION['idNotification'] : null;
}

if (isset($_REQUEST['idInform'])) {
  $idInform = $_REQUEST['idInform'];
}


?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->

<div class="conteudopagina">
  <?php
  $qry = "SELECT a.i_Moeda, a.Nome, a.Sigla 
  FROM Moeda a 
  INNER JOIN Inform b ON b.currency = a.i_Moeda
  WHERE b.id = ?";

  $ResMoeda = odbc_prepare($db, $qry);
  odbc_execute($ResMoeda, [$idInform]);


  $moeda = odbc_result($ResMoeda, 'Nome');

  odbc_free_result($ResMoeda);
  if ($_SESSION['pefil'] == 'F') {
    $qry = "UPDATE Inform SET financState = ? WHERE id = ?";
    $updateQuery = odbc_prepare($db, $qry);
    odbc_execute($updateQuery, [3, $idInform]);
  }

  $qry = "SELECT generalState, volState, segState, financState, buyersState, lostState, bornDate, respName, 
        ocupation, name, idAnt, dist_lim_state, Periodo_Vigencia, det_Perda_Efetiva_State, i_Produto, dist_tipo_venda_state, dividas_vencidas_state, Departamento_Credito_State 
        FROM Inform WHERE id = ?";
  $cur = odbc_prepare($db, $qry);
  odbc_execute($cur, [$idInform]);


  if (odbc_fetch_row($cur)) {
    $idAnt = odbc_result($cur, 11);
    $infName = odbc_result($cur, 'name');
    $Periodo_Vigencia = odbc_result($cur, 'Periodo_Vigencia');
    $i_Produto = odbc_result($cur, 'i_Produto');

    /*
                               if ($vigencia == ""){
                                  $pvigencia ="12 meses";
                               }else if($vigencia == "1"){
                                   $pvigencia ="12 meses";
                               }else{
                                  $pvigencia ="24 meses";      
                               */

    $pvigencia = $Periodo_Vigencia . ' Meses';

    ?>

    <form action="<?php echo $root; ?>role/inform/Inform.php" method="post" name="formulario">
      <input type="hidden" name="comm" value="done">
      <input type="hidden" name="v">
      <input type="hidden" name="mot" value="">
      <input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="hc_cliente" value="N">
      <input type="hidden" name="idAnt" value="<?php echo $idAnt; ?>">
      <input type="hidden" name="idNotification" value="<?php echo $idNotification; ?>">
      <input type="hidden" name="tipo_apolice" value="<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">

      <?php if (isset($msg)) { ?>
        <label>
          <font color=red><?php echo ($msg); ?></font>
        </label>
        <div style="clear:both">&nbsp;</div>
      <?php } else if (isset($msgInc)) { ?>
          <label>
            <font color=red><?php echo ($msgInc); ?></font>
          </label>
          <div style="clear:both">&nbsp;</div>
      <?php } ?>

      <label>Question&aacute;rio de Seguro de Cr&eacute;dito</label>

      <?php if ($idAnt) { ?>
        <label>RENOVA&Ccedil;&Atilde;O DE AP&Oacute;LICE</label>
      <?php } ?>

      <div style="clear:both">&nbsp;</div>
      <ul>
        <li class="campo2colunas"><label>Clique nos links para preencher cada um dos t&oacute;picos, navegue pelas
            p&aacute;ginas utilizando os bot&otilde;es "Pr&oacute;xima Tela, Tela Anterior e Tela
            Principal".</label>
        </li>
      </ul>

      <!--
         Etapas 
        - Informações Gerais;
        - Distribuição de vendas por tipo de pagamento;
        - Distribuição de vendas por Tipo de pagamento e Canal;
        - Distribuição de Vendas a Prazo por Faixa de Limite de Crédito ;
        - Distribuição de Vendas a Prazo por País ;
        - Relação de Clientes para Análise Preliminar - Buyer Study;
        - Dívidas Vencidas;
        - Histórico de Perdas;
        - Detalhamento das perdas efetivas por faixa de valor.
        -->

      <?php
      $volta = isset($volta) ? $volta : '';
      ?>

      <table>
        <thead>
          <th>T&oacute;picos</th>
          <th>Status</th>
        </thead>

        <tbody>
          <!--Informações Gerais -->
          <tr>
            <td>
              <a
                href="<?php echo $root; ?>role/inform/Inform.php?comm=generalInformation&idInform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idNotification=<?php echo $idNotification; ?>&volta=<?php echo $volta; ?>&hc_cliente=N&tipo_apolice=<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">Informa&ccedil;&otilde;es
                Gerais</a>
            </td>

            <td>
              <?php $dataState = odbc_result($cur, 1);
              include("status.php");
              ?>
            </td>
          </tr>
          <!-- Organização do Departamento de Crédito -->
          <tr>
            <td>
              <a
                href="<?php echo $root; ?>role/inform/Inform.php?comm=organizacaoDepCred&idInform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idNotification=<?php echo $idNotification; ?>&volta=<?php echo $volta; ?>&hc_cliente=N&tipo_apolice=<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">Organiza&ccedil;&atilde;o
                do Departamento de Cr&eacute;dito</a>
            </td>

            <td>
              <?php
              $dataState = (odbc_result($cur, 'Departamento_Credito_State') ? odbc_result($cur, 'Departamento_Credito_State') : 1);
              include("status.php");
              ?>
            </td>
          </tr>


          <!--Distribuição de vendas por tipo de pagamento-->
          <tr>
            <td><a
                href="<?php echo $root; ?>role/inform/Inform.php?comm=volVendExt&idInform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idNotification=<?php echo $idNotification; ?>&volta=<?php echo $volta; ?>&hc_cliente=N&tipo_apolice=<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">Distribui&ccedil;&atilde;o
                de Vendas por Tipo de Pagamento</a>
            </td>

            <td>
              <?php $dataState = odbc_result($cur, 2);
              include("status.php");
              ?>
            </td>
          </tr>

          <!--Distribuição de vendas por Tipo de pagamento e Canal-->
          <tr>
            <td>
              <a
                href="<?php echo $root; ?>role/inform/Inform.php?comm=dist_tipo_vendas&idInform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idNotification=<?php echo $idNotification; ?>&volta=<?php echo $volta; ?>&hc_cliente=N&tipo_apolice=<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>"><?php if ($i_Produto == 1)
                           echo "Distribui&ccedil;&atilde;o de Vendas por Canal e Regi&otilde;es";
                         else
                           echo "Distribui&ccedil;&atilde;o de Vendas por Tipo de Pagamento e Canal "; ?></a>
            </td>
            <td>
              <?php $dataState = (odbc_result($cur, 'dist_tipo_venda_State') ? odbc_result($cur, 'dist_tipo_venda_State') : 1);
              include("status.php");
              ?>
            </td>
          </tr>

          <!--Distribuição de Vendas a Prazo por Faixa de Limite de Crédito-->
          <tr>

            <td>
              <a
                href="<?php echo $root; ?>role/inform/Inform.php?comm=dist_lim_cred&idInform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idNotification=<?php echo $idNotification; ?>&volta=<?php echo $volta; ?>&hc_cliente=N&tipo_apolice=<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">Distribui&ccedil;&atilde;o
                de Vendas a Prazo por Faixa de Limite de Cr&eacute;dito</a>
            </td>
            <td>
              <?php $dataState = (odbc_result($cur, 'dist_lim_state') ? odbc_result($cur, 'dist_lim_state') : 1);
              include("status.php");
              ?>
            </td>
          </tr>

          <!--Distribuição de Vendas a Prazo por País -->
          <tr>

            <td>
              <a
                href="<?php echo $root; ?>role/inform/Inform.php?comm=segVendExt&idInform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idNotification=<?php echo $idNotification; ?>&volta=<?php echo $volta; ?>&hc_cliente=N&tipo_apolice=<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">Distribui&ccedil;&atilde;o
                de Vendas a Prazo por Pa&iacute;s</a>
            </td>
            <td>
              <?php $dataState = odbc_result($cur, 3);
              include("status.php");
              ?>
            </td>
          </tr>

          <!--Relação de Clientes para Análise Preliminar - Buyer Study-->
          <tr>

            <td>
              <a
                href="<?php echo $root; ?>role/inform/Inform.php?comm=buyers&idInform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idNotification=<?php echo $idNotification; ?>&volta=<?php echo $volta; ?>&hc_cliente=N&tipo_apolice=<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">Rela&ccedil;&atilde;o
                de Clientes para An&aacute;lise Preliminar - Buyer Study</a>
            </td>
            <td>
              <?php $dataState = odbc_result($cur, 5);
              include("status.php");
              ?>
            </td>
          </tr>

          <!--Dívidas Vencidas-->
          <tr>
            <td>
              <a
                href="<?php echo $root; ?>role/inform/Inform.php?comm=dividas_vencidas&idInform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idNotification=<?php echo $idNotification; ?>&volta=<?php echo $volta; ?>&hc_cliente=N&tipo_apolice=<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">D&iacute;vidas
                Vencidas</a>
            </td>
            <td>
              <?php $dataState = (odbc_result($cur, 'dividas_vencidas_State') ? odbc_result($cur, 'dividas_vencidas_State') : 1);
              include("status.php");
              ?>
            </td>
          </tr>

          <!--Histórico de Perdas-->
          <tr>

            <td>
              <a
                href="<?php echo $root; ?>role/inform/Inform.php?comm=lost&idInform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idNotification=<?php echo $idNotification; ?>&volta=<?php echo $volta; ?>&hc_cliente=N&tipo_apolice=<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">Hist&oacute;rico
                de Perdas</a>
            </td>
            <td>
              <?php $dataState = odbc_result($cur, 6);
              include("status.php");
              ?>
            </td>
          </tr>

          <!--Detalhamento das perdas efetivas por faixa de valor-->
          <tr>
            <td>
              <a
                href="<?php echo $root; ?>role/inform/Inform.php?comm=det_Perda_Faixa_Valor&idInform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>&idNotification=<?php echo $idNotification; ?>&volta=<?php echo $volta; ?>&hc_cliente=N&tipo_apolice=<?php echo htmlspecialchars($tipo_apolice, ENT_QUOTES, 'UTF-8'); ?>">Detalhamento
                das perdas efetivas por faixa de valor</a>
            </td>
            <td>
              <?php $dataState = (odbc_result($cur, 'det_Perda_Efetiva_State') ? odbc_result($cur, 'det_Perda_Efetiva_State') : 1);
              include("status.php");
              ?>
            </td>
          </tr>



        </tbody>
      </table>
      <?php

      $data = odbc_result($cur, 7);
      $name = odbc_result($cur, 8);
      $ocupation = odbc_result($cur, 9);
      odbc_free_result($cur);

      $qry = "SELECT generalState, volState, segState, buyersState, lostState, dist_lim_state, idAnt 
            FROM Inform WHERE id = ?";
      $cur = odbc_prepare($db, $qry);
      odbc_execute($cur, [$idInform]);

      odbc_fetch_row($cur);


      $ok = 1;
      $i = 1;
      $test = 0;



      if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B')
        $test = 2;
      else
        $test = 3;


      for (; $i <= 6; $i++) {
        if (odbc_result($cur, $i) != $test) {
          $ok = 0;
        }

      }

      if ($i == 1)
        $ok = 0;

      if ($ok) {

      }
      ?>


      <div style="clear:both">&nbsp;</div>
      <label>Exportar as informa&ccedil;&otilde;es para PDF <a
          href="<?php echo $root ?>role/inform/interf/relatorio_informe.php?inform=<?php htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>"
          target="new"><img border="0" src="<?php echo $root ?>../images/pdf_icon.png"
            title="Exportar para PDF" /></a>.</label>

      <div style="clear:both">&nbsp;</div>

      <?php if ($ok && $infName != '') { ?>
        <div style="clear:both">&nbsp;</div>
        <label>* Preencha os campos abaixo e click no bot&atilde;o "Enviar Informe" para finalizar o seu
          cadastro.</label>

        <div style="clear:both">&nbsp;</div>
        <ul>
          <li class="campo2colunas">
            <label>Nome do respons&aacute;vel:</label>
            <input type="text" size="60" maxlength="100" name="respName" value="<?php echo $name; ?>">
          </li>
          <li class="campo2colunas">
            <label>Cargo:</label>
            <input type="text" size="30" maxlength="50" name="ocupation" value="<?php echo $ocupation; ?>">
          </li>
          <li class="campo2colunas">
            <label>Data:</label>
            <div class="formdescricao">
              <span><?php echo substr($data, 8, 2) . "/" . substr($data, 5, 2) . "/" . substr($data, 0, 4); ?></span>
            </div>
          </li>
        </ul>

      <?php } ?>

      <div style="clear:both">&nbsp;</div>

      <label>1. Preencher todos os campos obrigat&oacute;rios.
        <br>2. As informa&ccedil;&otilde;es solicitadas s&atilde;o imprescind&iacute;veis.
        <br>3. Os dados s&atilde;o tratados com total confidencialidade.
        <br>4. O envio deste "INFORME" n&atilde;o caracteriza compromisso de contrata&ccedil;&atilde;o do seguro.
        <br>5. A empresa segurada declara que as informa&ccedil;&otilde;es constantes deste "INFORME" s&atilde;o
        completas e verdadeiras e assume,
        sob as penas da lei, a responsabilidade por sua exatid&atilde;o.
      </label>
      <div style="clear:both">&nbsp;</div>

      <!--- Consultor -->
      <?php


      odbc_free_result($cur);
      $query = "SELECT idConsultor FROM Inform WHERE id = ?";
      $cur = odbc_prepare($db, $query);
      odbc_execute($cur, [$idInform]);


      while (odbc_fetch_row($cur)) {
        $idConsultor = odbc_result($cur, 1);
      }

      if (($idConsultor) == "") {

      } else {

        $query = "SELECT contato FROM consultor, Inform 
              WHERE Inform.id = ? AND Inform.idConsultor = consultor.idconsultor";

        $cur = odbc_prepare($db, $query);
        odbc_execute($cur, [$idInform]);


        while (odbc_fetch_row($cur)) {
          $consultor = odbc_result($cur, 1);
        }

      }

      ?>



      <div class="barrabotoes">
        <?php if (!$role["client"]) { ?>

          <?php
          //Criado Por Tiago V N - Elumini - 30/06/2006 - Botão Voltar
          if ($role["executive"]) {
            $v = "0";
          } else {
            $v = "1";
          }
          ?>

          <?php if ($volta) { ?>

            <?php if ($hc_cliente == "N") { ?>

              <input type="hidden" name="idNotification" value="<?php echo $idNotification; ?>">
              <button class="botaovgm" type="button"
                onClick="this.form.comm.value='goback';this.form.v.value='<?php echo $v; ?>';this.form.submit()">Voltar</button>

            <?php } else { ?>

              <input type="hidden" name="idNotification" value="<?php echo $idNotification; ?>">
              <button class="botaovgm" type="button"
                onClick="this.form.idInform.value=<?php echo $idAnt; ?>;this.form.comm.value='volta';this.form.submit()">Voltar</button>

            <?php } ?>

          <?php } else { ?>

            <?php if ($hc_cliente == "N") { ?>

              <input type=hidden name=idNotification value="<?php echo $idNotification; ?>">
              <button class="botaovgm" type="button"
                onClick="this.form.comm.value='goback';this.form.v.value='<?php echo $v; ?>';this.form.submit()">Voltar</button>

            <?php } else { ?>

              <button class="botaovgm" type="button"
                onClick="this.form.comm.value='back';this.form.v.value='<?php echo $v; ?>';this.form.submit()">Voltar</button>

            <?php } ?>
          <?php } ?>

        <?php } else { ?>


          <?php if (isset($renova)) { ?>

            <?php if ($renova) { ?>
              <?php $idAnt = odbc_result($cur, 6);
              odbc_free_result($cur);
              ?>

              <button class="botaovgm" type="button"
                onClick="this.form.comm.value='client';this.form.idInform.value='<?php echo $idAnt; ?>';this.form.submit()">Voltar</button>
            <?php } ?>

          <?php } else { ?>

            <?php if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') { ?>

              <?php if ($_SESSION['id_user'] > 0 || $_SESSION['idx'] > 0) { ?>
                <button class="botaovgm" type="button"
                  onClick="this.form.comm.value='open';this.form.v.value='<?php echo $v; ?>';this.form.submit()">Voltar</button>

              <?php } else { ?>
                <button class="botaovgm" type="button"
                  onClick="window.parent.location.href='http://www.coface.com.br'">Voltar</button>

              <?php } ?>

            <?php } else { ?>

              <button class="botaovgm" type="button" onClick="window.location = '../access/Access.php';">Voltar</button>

            <?php } ?>
          <?php } ?>
        <?php } ?>

        <?php if ($_SESSION['pefil'] != 'C' && $_SESSION['pefil'] != 'B') { ?>

          <?php if ($ok) { ?>
            <input type="hidden" name="vig" value="<?php echo htmlspecialchars($vig, ENT_QUOTES, 'UTF-8'); ?>" class="servicos">
            <input type="hidden" name="sisseg" value="<?php echo htmlspecialchars($sisseg, ENT_QUOTES, 'UTF-8'); ?>" class="servicos">

            <?php if (isset($state)) { ?>
              <?php if ($state == 1) { ?>
                <button name="ok" type="button" onClick="this.form.mot.value='OK'; this.form.submit()"
                  class="botaoagm">OK</button>
              <?php } ?>
            <?php } else { ?>
              <button name="aceitar" type="button" onClick="this.form.mot.value='Aceitar'; confvigencia(this.form); "
                class="botaoagm">Aceitar</button>
              <button name="recusar" type="button" onClick="this.form.mot.value='Recusar';confirma(this.form)"
                class="botaovgm">Recusar</button>
            <?php } ?>

          <?php } ?>

        <?php } else { ?>
          <button class="botaoagg" type="button" onClick="this.form.submit()">Enviar Informe</button>
        <?php } ?>

        <?php if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') { ?>
          <button type="button" class="botaoagg" onClick="this.form.comm.value='open';this.form.submit()">Ver
            Informes</button>
        <?php } ?>
      </div>


    </FORM>

    <?php require_once("../inform/interf/listainformes.php"); ?>
  <?php } else { ?>
    <label>
      <font color=red>Informe inv&aacute;lido</font>
    </label>
  <?php } ?>
</div>


<script language="javascript" type="text/javascript">

  function confvigencia(c) {
    if (confirm("Voc&ecirc; confirma a vig&ecirc;ncia de <?php echo $pvigencia; ?>\n"
      + 'e o tipo de moeda <?php echo $moeda; ?> ?')) {
      c.submit();
      return true;
    } else {
      return false;
    }
  }
  function confirma(f) {
    if (confirm('Confirma a recusa do Informe?')) {
      f.submit();
    }
  }
</script>