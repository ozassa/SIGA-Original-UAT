<?php
// VERIFICAÇÃO CRÍTICA: Se ODBC não estiver carregada, redirecionar para página de emergência
if (!extension_loaded('odbc')) {
    // Se for uma tentativa de login, mostrar página de emergência
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validar']) && $_POST['validar'] === 'login') {
        header("Location: emergency_login.php");
        exit();
    }
}

require_once __DIR__ . "/session_config.php";

// Incluir funções de segurança (com verificação)
if (file_exists(__DIR__ . "/security_functions.php")) {
    require_once(__DIR__ . "/security_functions.php");
} else {
    // Fallback sem segurança avançada por enquanto
    error_log("AVISO: security_functions.php não encontrado");
}

// Interaktiv - Pagina de atualização
// require_once ("versao_sistema.php");

$is_https = isset($_SERVER['HTTPS']) ? "https://" : "http://";
$req = explode('/', $_SERVER['REQUEST_URI']);

$host = 'http://' . $_SERVER['HTTP_HOST'] . '/'; // endereços da localização das páginas na Web
?>

<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>SIGA - Sistema de Integrado de Gestão de Apólice - COFACE</title>
  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/geral.css" rel="stylesheet" type="text/css" />
  <link href="css/jquery.lightbox-0.3.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="Scripts/validation.js"></script>
  <script type="text/javascript" src="Scripts/jquery.js"></script>

  <script>
    function mainmenu() {
      $(" #nav ul ").css({ display: "none" });
      $(" #nav li").hover(function () {
        $(this).find('ul:first').css({ visibility: "visible", display: "none" }).show(200);
      }, function () {
        $(this).find('ul:first').css({ visibility: "hidden" });
      });
    }

    $(document).ready(function () {
      mainmenu();
    });
  </script>

<script type="text/javascript" src="Scripts/tinybox.js"></script>

<script type="text/javascript" src="Scripts/jquery.lightbox-0.3.js"></script>

</head>

<body class="fundoimagem" onload="document.getElementById('login').focus();">
  <div id="entradageral">
    <div id="entrada">
      <p><img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/icone_usuario.png" alt=""
          width="36" height="44" /></p>
      <p><img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/texto_acessogprint.png" alt=""
          width="111" height="23" /></p>

      <span class="texto11">Digite seu nome de usu&aacute;rio e senha<br />nos campos abaixo:</span><br /><br />

      <form action="src/role/access/Access.php" name="frm" id="frm" method="post" autocomplete="off">
        <?php if (isset($_SESSION['browser'])) { ?>
          <?php if ($_SESSION['browser'] == 'IE') { ?>
            <input name="valido" id="valido" type="hidden" value="login" />
          <?php } ?>
        <?php } ?>

        <?php $v_erro = isset($_GET['erro']) ? (preg_match("/^[0-9]+$/", $_GET['erro']) ? $_GET['erro'] : 0) : 0; ?>

        <?php if ($v_erro != 0) { ?>
          <?php if ($v_erro == 1) { ?>
            <label style="color:#C00">Usuário ou senha inválidos!</label>
          <?php } else if ($v_erro == 2) { ?>
              <label style="color:#C00">Sua seção expirou ou você não está logado!</label>
          <?php } ?>
        <?php } ?>

        <!-- Interaktiv 06/05/2015 1 -->
        <?php
        $loginUser = isset($_SESSION['tentativaLoginUsuario']) ? $_SESSION['tentativaLoginUsuario'] : '';

        if (isset($_SESSION['tentativaSenha_' . $loginUser])) {
          $tentativaSenha = $_SESSION['tentativaSenha_' . $loginUser];
          $tentativasValidas = 3 - $_SESSION['tentativaSenha_' . $loginUser];

          // Matheus Fernandes - 28/07/2020 (regra de tentativa de senha bloqueada)
          $msg_tentativas = 'Usuário ou senha inválidos!';
          /*$msg_tentativas = '';
                                        if ($tentativaSenha < 3) {
                                          $msg_tentativas = 'Você possui ' . $tentativasValidas . ' tentativa(s).';
                                        } */
          ?>
          <script>
            str_tentativas = '<div style="position:relative; top:-20px; text-align: right;"><a href="javascript:TINY.box.hide()" class="linktexto"><img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/close.png" title="Fechar" border="0" width="15" height="15"></a></div>';
            TINY.box.show(str_tentativas + '<label id="Meng"><?php echo htmlspecialchars($msg_tentativas, ENT_QUOTES, 'UTF-8'); ?></label><br>', 0, 0, 0, 2);
          </script>
        <?php } ?>

        <?php
        $mensagem = false;

        if (isset($_SESSION['mensagemSucess']) && $_SESSION['mensagemSucess']) {
          $mensagem = $_SESSION['mensagemSucess'];
          $_SESSION['mensagemSucess'] = '';
        } elseif (isset($_GET['msg'])) {
          $mensagemRaw = $_GET['msg'];
          $mensagemSanitizada = strip_tags(trim($mensagemRaw));

          if (mb_strlen($mensagemSanitizada) > 200) {
            $mensagemSanitizada = mb_substr($mensagemSanitizada, 0, 200);
          }

          if (preg_match('/[<>\"\']/', $mensagemSanitizada)) {
            $mensagemSanitizada = '[mensagem inválida]';
          }

          $mensagem = $mensagemSanitizada;
        }
        ?>

        <?php if ($mensagem) { ?>
          <script>
            var message = <?php echo json_encode($mensagem); ?>;
            var string = '<div style="position:relative; top:-20px; text-align: right;">' +
              '<a href="javascript:TINY.box.hide()" class="linktexto">' +
              '<img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/close.png" title="Fechar" border="0" width="15" height="15"></a></div>';
            T$('valido').onclick = TINY.box.show(string + '<label id="Meng">' + message + '</label><br>', 0, 0, 0, 2);
            window.location = 'index.php';
          </script>
        <?php } ?>

        <?php if (isset($_SESSION['browser'])) { ?>
          <?php if ($_SESSION['browser'] != 'IE') { ?>
            <input name="valido" id="valido" type="hidden" value="login" />
          <?php } ?>
        <?php } ?>

        <label>Usu&aacute;rio</label>
        <input name="login" id="login" type="text" />
        <label>Senha</label>
        <input name="password" id="password" type="password"
          onkeypress="javascript:if (event.keyCode == 13) this.form.submit();" />
        <input name="validar" id="validarHid" type="hidden" value="login" />
        <?php echo csrf_token_field(); ?>
        
        <?php if (!extension_loaded('odbc')): ?>
        <div style="background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px 0; border-radius: 4px; color: #c62828; font-size: 12px;">
            ⚠️ <strong>AVISO DO SISTEMA:</strong> Extensão ODBC não detectada. O login pode falhar. 
            <a href="emergency_login.php" style="color: #1976d2;">Clique aqui para mais informações</a>
        </div>
        <?php endif; ?>
        
        <br />
        <p>
          <a href="#" class="linktexto" id="testclick1">Lembrar senha</a>&nbsp;&nbsp;&nbsp;
          <a href="#" class="linktexto" id="testclick2">Cadastre-se</a>
        </p>
        <a href="#" class="linktexto" id="testclick3"></a>

        <button class="botaoagm" type="button" id="validar" onclick="javascript: frm.submit();"
          onkeypress="javascript:if (event.keyCode == 13) frm.submit();">Entrar</button>
      </form>

    </div>
  </div>

  <script type="text/javascript">
    T$('testclick1').onclick = function () { TINY.box.show('remember.php?rem=1', 2, 300, 230, 2) }
    T$('testclick2').onclick = function () { TINY.box.show('remember.php?rem=2', 2, 340, 340, 2) }
  </script>

  <?php if (isset($_SESSION['resetSenha']['erroHiddenCode'])) { ?>
    <?php if ($_SESSION['resetSenha']['erroHiddenCode'] == '3') { ?>
      <script>
        T$('testclick3').onload = TINY.box.show('remember.php?user=<?php echo safe_output($_SESSION['resetSenha']['erroHiddenUser'], 'url') ?>&rem=3&alter=<?php echo safe_output($_SESSION['resetSenha']['alterSenha'], 'url'); ?>&indic=<?php echo safe_output($_SESSION['resetSenha']['erroHiddenMotivo'], 'url'); ?>', 2, 540, 465, 2);
      </script>
      <?php unset($_SESSION['resetSenha']); ?>
    <?php } ?>
  <?php } ?>

  <script language="javascript" type="text/javascript">
    function validaform() {
      var message = '';
      var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})");
      var novaSenha = document.getElementById('novaSenha').value;
      var senhaAtual = document.getElementById('senhaAtual').value;
      var confSenha = document.getElementById('confsenha').value

      if (senhaAtual == '') {
        message = 'Por favor informe a sua senha atual.';
        document.getElementById('message').style.display = 'block';
        document.getElementById('message').innerText = message;
        document.getElementById('senhaAtual').focus();
        return false;
      } else if (novaSenha == '') {
        message = 'Por favor informe a nova senha.';
        document.getElementById('message').style.display = 'block';
        document.getElementById('message').innerText = message;
        document.getElementById('novaSenha').focus();
        return false;
      } else if (document.getElementById('confsenha').value == '') {
        message = 'Por favor confirme a nova senha.';
        document.getElementById('message').style.display = 'block';
        document.getElementById('message').innerText = message;
        document.getElementById('confsenha').focus();
        return false;
      } else if ((senhaAtual == novaSenha) || (senhaAtual == confSenha)) {
        message = 'A nova senha não pode ser igual à senha anterior.';
        document.getElementById('message').style.display = 'block';
        document.getElementById('message').innerText = message;
        document.getElementById('novaSenha').focus();
        return false;
      } else if (novaSenha != confSenha) {
        message = 'A senha de confirmação deve ser igual à nova senha.';
        document.getElementById('message').style.display = 'block';
        document.getElementById('message').innerText = message;
        document.getElementById('cofsenha').focus();
        return false;
      } else if (!strongRegex.test(novaSenha)) {
        message = " A senha deve :<br> - Conter pelo menos 1 caractere alfabético minúsculo<br> - Conter pelo menos 1 caractere alfabético maiúsculo<br> - Conter pelo menos 1 caractere numérico<br> - Conter pelo menos um caractere especial<br> - Conter dez caracteres ou mais";
        document.getElementById('message').style.display = 'block';
        document.getElementById('message').innerHTML = message;
        document.getElementById('novaSenha').focus();
        return false;
      } else {
        return true;
      }
    }
  </script>

  <style type="text/css">
    #tinybox {
      overflow-y: scroll;
    }

    #tinycontent {
      padding-top: 10px;
    }
  </style>
</body>

</html>