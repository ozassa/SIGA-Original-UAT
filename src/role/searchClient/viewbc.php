<?php
/*
   Criado por Tiago V N -(Elumini)
   Atualizado por Michel E.C.Saddock de Sá - (Elumini) 15/01/2007
   23/03/2016 - Ricardo - Alteração Do nome Do responsável apresentado na tela pelo nome Do segurado, e não o nome Do responsável pelo preenchimento Do informe
   */

require_once("../../dbOpen.php");

$id = $_REQUEST['idclient'];
$tipo = $_REQUEST['tipoclient'];

include_once('../../../navegacao.php');
?>


<div class="conteudopagina">
  <script type="text/javascript">
  function validaForm(frm) {
    if (frm.login.value == "") {
    verErro('O Login dever ser preenchido!');
    return false;
    } else {
    return true;
    }
  }
  </script>

  <?php
  if ($tipo == 1) { // CLIENTES BANCOS 
    $sql = "SELECT id, name, login, password FROM Users WHERE id = ?";
    $cur = odbc_prepare($db, $sql);
    odbc_execute($cur, [$id]);


    odbc_fetch_row($cur); ?>

    <li class="campo2colunas">
      <label>Nome Empresa</label>
      <?php echo odbc_result($cur, 2); ?>
    </li>

    <li class="campo2colunas">
      <label>Login atual</label>
      <?php echo odbc_result($cur, 3); ?>
    </li>

    <form name="formbanco" action="searchSenha.php">
      <li class="campo3colunas">
      <label>Novo Login</label>
      <input type="text" size="50" name="login">
      <input type="hidden" value="<?php echo $idclient; ?>" name="idcliente" id="idcliente">
      <input type="hidden" value="alteraLogin" name="comm">
      <input type="hidden" value="<?php echo $tipoclient; ?>" name="tipoclient">
      </li>

      <div class="barrabotoes">
      <button class="botaoagm" type="button"
        onClick="if (validaForm(this.form)) this.form.submit();">Alterar</button>
      <?php if ($id) { ?>
          <button class="botaoagg" type="button" id="js-modal_pass">Alterar Senha</button>
      <?php } ?>
      </div>
    </form>

    <form id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/searchSenha.php" method="post">
      <div class="barrabotoes">
      <input type="hidden" value="1" name="tipoclient">
      <input type="hidden" value="true" name="envia">
      <button class="botaovgm" type="button" onClick="this.form.submit()">Voltar</button>
      </div>
    </form>

    <?php
    odbc_free_result($cur);
  } else if ($tipo == 2) { // CLIENTES PROSPECTIVOS 
    $sql = "SELECT i.name, u.login, u.password, u.id 
        FROM Insured s, Users u, Inform i 
        WHERE i.idInsured = s.id AND u.id = s.idResp AND i.state IN (1,2,3,4,5,6,7) AND i.id IN (?)";

    $cur = odbc_prepare($db, $sql);
    odbc_execute($cur, [$id]);

    odbc_fetch_row($cur); ?>

        <li class="campo2colunas">
        <label>Nome Empresa</label>
      <?php echo odbc_result($cur, 1); ?>
        </li>

        <li class="campo2colunas">
        <label>Login atual</label>
      <?php echo odbc_result($cur, 2); ?>
        </li>

        <form name="formprospectivo" action="searchSenha.php">
        <li class="campo3colunas">
          <label>Novo Login</label>
          <input type="text" size="50" name="login">
          <input type="hidden" value="<?php echo odbc_result($cur, 4); ?>" name="idcliente" id="idcliente">
          <input type="hidden" value="<?php echo $idclient; ?>" name="idInform">
          <input type="hidden" value="alteraLogin" name="comm">
          <input type="hidden" value="<?php echo $tipoclient; ?>" name="tipoclient">
        </li>

        <div class="barrabotoes">
          <button class="botaoagm" type="button"
          onClick="if (validaForm(this.form)) this.form.submit();">Alterar</button>
      <?php if ($id) { ?>
            <button class="botaoagg" type="button" id="js-modal_pass">Alterar Senha</button>
      <?php } ?>
        </div>
        </form>

        <form id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/searchSenha.php" method="post">
        <div class="barrabotoes">
          <input type="hidden" value="2" name="tipoclient">
          <input type="hidden" value="true" name="envia">
          <button class="botaovgm" type="button" onClick="this.form.submit()">Voltar</button>
        </div>
        </form>

        <?php

        odbc_free_result($cur);
  } else if ($tipo == 3) { // CLIENTES VIGENTES 
    $sql = "SELECT i.name AS SEGURADO, u.name AS RESPONSAVEL, u.login AS LOGIN, u.password, i.codProd, i.startValidity, u.id, u.email AS EMAIL
        FROM Inform i
        JOIN Insured s ON i.idInsured = s.id
        JOIN Users u ON s.idResp = u.id
        WHERE i.state IN (10) AND i.id IN (?)
        ORDER BY i.name";

    $cur = odbc_prepare($db, $sql);
    odbc_execute($cur, [$id]);

    odbc_fetch_row($cur); ?>

          <li class="campo2colunas">
            <label>Segurado</label>
      <?php echo odbc_result($cur, "SEGURADO"); ?>
          </li>

          <li class="campo2colunas">
            <label>Responsável</label>
      <?php echo odbc_result($cur, "RESPONSAVEL"); ?>
          </li>

          <li class="campo2colunas">
            <label>Login</label>
      <?php echo odbc_result($cur, "LOGIN"); ?>
          </li>

          <li class="campo2colunas">
            <label>E-mail</label>
      <?php echo odbc_result($cur, "EMAIL"); ?>
          </li>

          <form name="formvigente" action="searchSenha.php">
            <li class="campo2colunas">
            <label>Novo Login</label>
            <input type="text" size="50" name="login">
            <label>Novo E-mail</label>
            <input type="email" size="50" name="email">
            <input type="hidden" value="<?php echo odbc_result($cur, 7); ?>" name="idcliente" id="idcliente">
            <input type="hidden" value="<?php echo $idclient; ?>" name="idInform">
            <input type="hidden" value="alteraLogin" name="comm">
            <input type="hidden" value="<?php echo $tipoclient; ?>" name="tipoclient">
            </li>

            <div class="barrabotoes">
            <button class="botaoagm" type="button"
              onClick="if (validaForm(this.form)) this.form.submit();">Alterar</button>
      <?php if ($id) { ?>
                <button class="botaoagg" type="button" id="js-modal_pass">Alterar Senha</button>
      <?php } ?>
            </div>
          </form>

          <form id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/searchSenha.php" method="post">
            <div class="barrabotoes">
            <input type="hidden" value="3" name="tipoclient">
            <input type="hidden" value="true" name="envia">
            <button class="botaovgm" type="button" onClick="this.form.submit()">Voltar</button>
            </div>
          </form>

        <?php
        odbc_free_result($cur);
  } else if ($tipo == 4) { // FUNCIONÁRIO - APARENTEMENTE INATIVO 
    $sql = "SELECT DISTINCT u.id, u.name, u.login, u.email, u.state, u.password
        FROM Users u
        JOIN UserRole ur ON u.id = ur.idUser
        WHERE ur.idRole <> 1 AND u.id IN (?) AND u.perfil = 'F'
        ORDER BY u.name";

    $cur = odbc_prepare($db, $sql);
    odbc_execute($cur, [$id]);


    odbc_fetch_row($cur); ?>

              <li class="campo2colunas">
              <label>Nome Funcion&aacute;rio</label>
      <?php echo odbc_result($cur, 2); ?>
              </li>

              <li class="campo2colunas">
              <label>Login atual</label>
      <?php echo odbc_result($cur, 3); ?>
              </li>

              <form id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/searchSenha.php" method="post">
              <input type="hidden" value="4" name="tipoclient">
              <input type="hidden" value="true" name="envia">

              <div class="barrabotoes">
                <button class="botaovgm" type="button" onClick="this.form.submit()">Voltar</button>
              </div>
              </form>
        <?php
        odbc_free_result($cur);
  } else if ($tipo == 5) { // CLIENTES ENCERRADOS/CANCELADOS 
  
    $sql = "SELECT i.name, u.name, u.login, u.password, i.codProd, i.startValidity, u.id 
        FROM Insured s
        JOIN Inform i ON i.idInsured = s.id
        JOIN Users u ON u.id = s.idResp
        WHERE i.state IN (9, 11) AND i.id IN (?)";

    $cur = odbc_prepare($db, $sql);
    odbc_execute($cur, [$id]);

    odbc_fetch_row($cur); ?>

                <li class="campo2colunas">
                  <label>Nome Empresa</label>
      <?php echo odbc_result($cur, 1); ?>
                </li>

                <li class="campo2colunas">
                  <label>Login atual</label>
      <?php echo odbc_result($cur, 3); ?>
                </li>

        <?php
        $separa0 = explode("[ ]", odbc_result($cur, 6));
        list($ano, $mes, $dia) = explode("[-/]", $separa0[0]);
        ?>

                <form name="formencerrado" action="searchSenha.php">
                  <li class="campo3colunas">
                  <label>Novo Login</label>
                  <input type="text" size="50" name="login">
                  <input type="hidden" value="<?php echo odbc_result($cur, 7); ?>" name="idcliente" id="idcliente">
                  <input type="hidden" value="<?php echo $idclient; ?>" name="idInform">
                  <input type="hidden" value="alteraLogin" name="comm">
                  <input type="hidden" value="<?php echo $tipoclient; ?>" name="tipoclient">
                  </li>

                  <div class="barrabotoes">
                  <button class="botaoagm" type="button"
                    onClick="if (validaForm(this.form)) this.form.submit();">Alterar</button>
      <?php if ($id) { ?>
                      <button class="botaoagg" type="button" id="js-modal_pass">Alterar Senha</button>
      <?php } ?>
                  </div>
                </form>

                <form id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/searchSenha.php" method="post">
                  <input type="hidden" value="5" name="tipoclient">
                  <input type="hidden" value="true" name="envia">

                  <div class="barrabotoes">
                  <button class="botaovgm" type="button" onClick="this.form.submit()">Voltar</button>
                  </div>
                </form>
    <?php
  }
  odbc_close($db);
  ?>
</div>

<?php
$funct = explode("/searchClient/", $_SERVER["REQUEST_URI"]);
$url_funct = $funct[0] . '/user/ajax_change_password.php';
?>

<script>
  function validaform() {
  var message = '';
  var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})");
  var password = $('#password').val();
  var confirm_password = $('#confirm_password').val();

  if (password == '') {
    message = 'Por favor informe a nova senha.';
    $('#message').html(message);
    $('#password').focus();
    return false;
  }

  if (confirm_password == '') {
    message = 'Por favor confirme a nova senha.';
    $('#message').html(message);
    $('#confirm_password').focus();
    return false;
  }

  if (password != confirm_password) {
    message = 'A senha de confirmação deve ser igual à nova senha.';
    $('#message').html(message);
    $('#confirm_password').focus();
    return false;
  }

  if (!strongRegex.test(password)) {
    message = " A senha deve :<br> - Conter pelo menos 1 caractere alfabético minúsculo<br> - Conter pelo menos 1 caractere alfabético maiúsculo<br> - Conter pelo menos 1 caractere numérico<br> - Conter pelo menos um caractere especial<br> - Conter dez caracteres ou mais";
    $('#message').html(message);
    $('#password').focus();
    return false;
  }

  return true;
  }

  $(document).ready(function () {
  $("#js-modal_pass").on("click", function () {
    $(".modal-ext").show();
    $("#js-title").hide();
  });

  $("#close_modal").on("click", function () {
    $(".modal-ext").hide();
  });

  $("#change_pass").on("click", function (event) {

    if (validaform()) {
    var userID = $("#idcliente").val();
    var password = $("#password").val();

    $.ajax({
    type: "POST",
    url: '<?php echo htmlspecialchars($url_funct, ENT_QUOTES, 'UTF-8'); ?>',
    data: { userID: userID, password: password },
    success: function (data) {
        verErro('Senha alterada com sucesso');
    }
    });


    $(".modal-ext").hide();
    event.preventDefault();
    }
  })
  });
</script>

<!-- Modal -->
<div class="modal-ext" style="display:none">
  <div class="bg-black"></div>

  <div class='modal-int'>
  <h1>Alterar Senha</h1>
  <div class="divisoriaamarelo"></div>

  <input type="hidden" name="userID" id="userID" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
  <div id="message" style="color: #f00;font-family: Arial;font-size: 12px; margin: 10px 0px;"></div>

  <li class="campo2colunas">
    <label>Nova Senha</label>
    <input type="password" name="password" id="password" size="20" maxlength="100">
  </li>

  <li class="campo2colunas">
    <label>Confirmar Senha</label>
    <input type="password" name="confirm_password" id="confirm_password" size="20" maxlength="100">
  </li>

  <li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
    <button type="button" class="botaovgm" id="close_modal">Voltar</button>
    <button type="button" class="botaoagg" id="change_pass">Salvar</button>
  </li>

  </div>
</div>
<!-- Fim modal -->