<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

// Interaktiv - Pagina de atualização
require_once("versao_sistema.php");

$is_https = isset($_SERVER['HTTPS']) ? "https://" : "http://";
$req = explode('/', $_SERVER['REQUEST_URI']);

$host = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $req[1] . '/'; // endereços da localização das páginas na Web
?>

<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>SIGA Sistema Integrado de Gest&atilde;o de Ap&oacute;lice — Coface</title>
	<link href="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>css/reset.css" rel="stylesheet"
		type="text/css" />
	<link href="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>css/geral.css" rel="stylesheet"
		type="text/css" />
	<link href="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>css/jquery.lightbox-0.3.css" rel="stylesheet"
		type="text/css" />
	<script type="text/javascript"
		src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>Scripts/validation.js"></script>
	<script type="text/javascript"
		src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>Scripts/jquery.js"></script>

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
	<script type="text/javascript"
		src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>Scripts/tinybox.js"></script>
	<script type="text/javascript"
		src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>Scripts/jquery.lightbox-0.3.js"></script>






</head>

<body class="fundoimagem" onload="document.getElementById('login').focus();">
	<div id="entradageral">
		<div id="entrada">

			<p><img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/icone_usuario.png" alt=""
					width="36" height="44" /></p>
			<p><img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/texto_acessogprint.png"
					alt="" width="111" height="23" /></p>

			<span class="texto11">Digite seu nome de usu&aacute;rio e senha<br />nos campos abaixo:</span><br /><br />
			<form action="src/role/access/Access.php" name="frm" id="frm" method="post">

				<?php if (isset($_SESSION['browser'])) { ?>
					<?php if ($_SESSION['browser'] == 'IE') { ?>
						<input name="valido" id="valido" type="hidden" value="login" />
					<?php } ?>
				<?php } ?>

				<?php if (isset($_SESSION['erro'])) { ?>
					<?php if ($_GET['erro'] == 1) { ?>
						<label style="color:#C00">Usuário ou senha inválidos!</label>
					<?php } else if ($_GET['erro'] == 2) { ?>
							<label style="color:#C00">Sua seção expirou ou você não está logado!</label>
					<?php } ?>
				<?php } ?>


				<!-- Interaktiv 06/05/2015 -->
				<?php
				$loginUser = isset($_SESSION['tentativaLoginUsuario']) ? $_SESSION['tentativaLoginUsuario'] : '';

				if (isset($_SESSION['tentativaSenha_' . $loginUser])) {
					$tentativaSenha = $_SESSION['tentativaSenha_' . $loginUser];
					$tentativasValidas = 3 - $_SESSION['tentativaSenha_' . $loginUser];

					if ($tentativaSenha < 3) {
						$msg_tentativas = 'Você possui ' . $tentativasValidas . ' tentativa(s).';
					} /*else { 
																	   if ($_SESSION['tentativaPefilUsuario'] == 'F') {
																		   $msg_tentativas = 'Seu login foi bloqueado, por favor, entrar em contato com o Suporte Técnico.';
																		 } else { 
																			 $msg_tentativas = 'Seu login foi bloqueado, por favor, entrar em contato com Atendimento Coface.';
																		 } 
																	 }*/ ?>

					<script>
						str_tentativas = '<div style="position:relative; top:-20px; text-align: right;"><a href="javascript:TINY.box.hide()" class="linktexto"><img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/close.png" title="Fechar" border="0" width="15" height="15"></a></div>';
						TINY.box.show(str_tentativas + '<label id="Meng"><label><?php echo $msg_tentativas; ?></label></label><br>', 0, 0, 0, 2);
					</script>
				<?php } ?>

				<?php
				$mensagem = false;
				if (isset($_SESSION['mensagemSucess'])) {
					if ($_SESSION['mensagemSucess']) {
						$mensagem = $_SESSION['mensagemSucess'];
						$_SESSION['mensagemSucess'] = '';
					}
				} else if (isset($_GET['msg'])) {
					if ($_GET['msg']) {
						$mensagem = $_GET['msg'];
					}
				}

				if ($mensagem) { ?>
					<script>

						var message = '<?php echo '<label>' . htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') . '</label>'; ?>';
						// alert(document.getElementById('validar').value);
						string = '<div style="position:relative; top:-20px; text-align: right;"><a href="javascript:TINY.box.hide()" class="linktexto"><img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/close.png" title="Fechar" border="0" width="15" height="15"></a></div>';
						T$('valido').onclick = TINY.box.show(string + '<label id="Meng">' + encodeURIComponent(message) + '</label><br>', 0, 0, 0, 2);


						// T$('validar').onclick = TINY.box.show(message,0,0,0,0,3);

						window.location = 'index.php';
					</script>
					<?php
				} ?>

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
				<input name="validar" id="validar" type="hidden" value="login" />
				<br />
				<p><a href="#" class="linktexto" id="testclick1">Lembrar senha</a>&nbsp;&nbsp;&nbsp;<a href="#"
						class="linktexto" id="testclick2">Cadastre-se</a></p>
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
	<?php if (isset($_REQUEST['erro'])) { ?>
		<?php if ($_REQUEST['erro'] == '3') { ?>
			<script>
				T$('testclick3').onload = TINY.box.show('remember.php?user=<?php echo htmlspecialchars($_REQUEST['userID'], ENT_QUOTES, 'UTF-8'); ?>&rem=3&alter=<?php echo htmlspecialchars($_REQUEST['altersenha'], ENT_QUOTES, 'UTF-8'); ?>&indic=<?php echo htmlspecialchars($_GET['ind'], ENT_QUOTES, 'UTF-8'); ?>', 2, 540, 465, 2);
			</script>
		<?php } ?>
	<?php } ?>


	<script language="javascript" type="text/javascript">

		function validaform() {
			var message = '';

			if (document.getElementById('senhaAtual').value == '') {
				message = 'Por favor informe a sua senha atual.';
				document.getElementById('message').style.display = 'block';
				document.getElementById('message').innerText = message;
				document.getElementById('senhaAtual').focus();
				return false;
			} else if (document.getElementById('novaSenha').value == '') {
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
			} else if ((document.getElementById('senhaAtual').value == document.getElementById('novaSenha').value) || (document.getElementById('senhaAtual').value == document.getElementById('confsenha').value)) {
				message = 'A nova senha não pode ser igual à senha anterior.';
				document.getElementById('message').style.display = 'block';
				document.getElementById('message').innerText = message;
				document.getElementById('novaSenha').focus();
				return false;
			} else if (document.getElementById('novaSenha').value != document.getElementById('confsenha').value) {
				message = 'A senha de confirmação deve ser igual à nova senha.';
				document.getElementById('message').style.display = 'block';
				document.getElementById('message').innerText = message;
				document.getElementById('cofsenha').focus();
				return false;
			} else
				return true;
		}
	</script>

</body>

</html>