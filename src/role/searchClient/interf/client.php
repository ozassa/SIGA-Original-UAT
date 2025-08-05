<script>
  $(document).ready(function(){
    $("#js-modal_parametros").on("click", function(){
      $(".modal-ext").show();
      $("#js-title").hide();
    });
    
    $("#close_modal").on("click", function(){
      $(".modal-ext").hide();
    });
  });
</script>

<?php 
  $hc_renova = false;

  if ($hc_idAnt) {
    $wsql = " SELECT state FROM Inform WHERE id = $hc_idAnt ";
    $aux = odbc_exec($db, $wsql); 
    $hc_state = odbc_result($aux, "state");
    
    if ($hc_state != 9)
      $hc_renova = true;
  }

  $Sql = "Select Top 1 id From NotificationR where idInform = $idInform and tp_notification_id = 27 order by bornDate Desc ";
  $aux = odbc_exec($db, $Sql);  

  $idNotification = odbc_result($aux, "id"); 
  ?>

  <div style="padding-bottom: 170px;">
    <li class="campo2colunas"> 
      <label><h2>Informe</h2></label>
      <div style="margin:0 0 0 10px;">
        <ul>
          <?php
            if (odbc_result($cur, "state") == 1 && $hc_idAnt > 0) { ?>
              <li><a href="<?php echo $root; ?>role/inform/Inform.php?comm=open&idInform=<?php echo $idInform; ?>&idNotification=<?php echo $idNotification; ?>&volta=1&hc_cliente=N">Ver Informe de renova&ccedil;&atilde;o</a></li>
          <?php } ?>
          <li><a href="<?php echo $root; ?>role/searchClient/ListClient.php?comm=generalInformation&idInform=<?php echo $idInform; ?>">Informa&ccedil;&otilde;es Gerais</a></li>
          <li><a href="<?php echo $root; ?>role/searchClient/ListClient.php?comm=cadastro_endosso&idInform=<?php echo $idInform; ?>">Endosso</a></li>
          <li>
            <a href="<?php echo $root; ?>role/client/Client.php?comm=ficha&idInform=<?php echo $idInform; ?>&origem=2">
              <?php  if(odbc_result($cur, "state") == 10) { ?>
                Ficha de Aprova&ccedil;&atilde;o de Limites de Cr&eacute;dito
              <?php } else {?>
                Ficha Indicativa de Limites de Cr&eacute;dito
              <?php  } ?>
            </a>
          </li>
          <li><a href="<?php echo $root; ?>role/cessao/Cessao.php?comm=consultaSituacaoFinanc&idInform=<?php echo $idInform;?>">Situa&ccedil;&atilde;o Financeira</a></li>
          <li><a href="<?php echo $root; ?>role/cessao/Cessao.php?comm=consultaSinistro&idInform=<?php echo $idInform;?>">Acompanhamento de Sinistros</a></li>
          <li><a href="<?php echo $root; ?>role/searchCessao/SearchCessao.php?exportadorFixo=<?php echo $idInform;?>">Consultar Cess&atilde;o de Direitos</a></li>
          <li><a href="<?php echo $root; ?>role/client/Client.php?comm=comments&idInform=<?php echo $idInform; ?>">Coment&aacute;rios</a></li>
        </ul>
      </div>
    </li>
    
    <li class="campo2colunas"> 
      <ul>
        <li>
          <label><h2>Par&acirc;metros</h2></label>
          <div style="margin:0 0 0 10px;">
            <ul>
              <li><a href="#" id="js-modal_parametros">Certifica&ccedil;&atilde;o Digital</a></li>
            </ul>
          </div>
        </li>
      </ul>

      <?php 
        if ($role["dve"] || $role["executive"] || $role["executiveLow"] || $role["credit"] || $role["creditInform"] || $role["creditManager"] || $role["viewCredit"] || $role["sinistro"]){ ?>
          <ul>
            <li><label><h2>Contatos</h2></label>
              <div style="margin:0 0 0 10px;">
                <ul>      
                  <li><a href="<?php echo $root; ?>role/credit/Credit.php?comm=insertContact&idInform=<?php echo $idInform; ?>">Inclus&atilde;o</a></li>
                  <li><a href="<?php echo $root; ?>role/credit/Credit.php?comm=searchContact&idInform=<?php echo $idInform; ?>">Consulta/Altera&ccedil;&atilde;o</a></li>
                </ul>
              </div>
            </li>
          </ul>
      <?php } ?>

      <?php 
        if ($role["executive"] || $role["clientAdmin"]) {
          if (($state <> 10) && ($state <> 11) && ($state <> 9) || $role["clientAdmin"]) { ?>
            <ul>
              <li>
                <label><h2>&Aacute;rea do Cliente</h2></label>
                <div style="margin:0 0 0 10px;">
                  <ul>      
                    <li><a href="<?php echo $root;?>role/inform/Inform.php?comm=open&idInform=<?php echo $idInform; ?>&acessointerno=1" class="textoBold">Clique aqui para acessar a &aacute;rea do cliente</a></li>
                  </ul>
                </div>
              </li>
            </ul>
            <?php 
          }
        } ?>
        <div class="divisoria01"></div>

    </li>
  </div>

  <!-- Modal -->
  <div class="modal-ext" style="display:none">
    <div class="bg-black"></div>

    <div class='modal-int'>
      <h1>Par&acirc;metros da Certifica&ccedil;&atilde;o Digital</h1>
      <div class="divisoriaamarelo"></div>
      
      <form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="idInform" id="idInform" value="<?php echo $idInform; ?>">
        <input type="hidden" name="comm" id="comm" value="paramCertif">

        <?php require_once('../executive/interf/includes/certificacao_digital.php'); ?>

        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
          <button type="button" class="botaovgm" id="close_modal">Voltar</button>
          <button type="submit" class="botaoagg">Salvar</button>
        </li>
      </form>

    </div>
  </div>
  <!-- Fim modal -->