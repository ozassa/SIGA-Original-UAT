<?php
// VERSÃO DE EMERGÊNCIA - SEM CORREÇÕES DE SEGURANÇA
require_once __DIR__ . "/session_config.php";

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

        <label>Usu&aacute;rio</label>
        <input name="login" id="login" type="text" />
        <label>Senha</label>
        <input name="password" id="password" type="password"
          onkeypress="javascript:if (event.keyCode == 13) this.form.submit();" />
        <input name="validar" id="validarHid" type="hidden" value="login" />
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

</body>
</html>