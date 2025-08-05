<?php
  include_once('src/dbOpen.php');
  
  // Incluir sistemas de segurança
  require_once(__DIR__ . "/session_config.php");
  require_once(__DIR__ . "/security_functions.php");
  require_once(__DIR__ . "/secure_password_recovery.php");
  require_once(__DIR__ . "/hybrid_auth.php");
 
  require_once("src/role/consultaCoface.php");
  require_once("src/role/rolePrefix.php"); 
 
  $op_num = isset($_POST['operacao']) ? (preg_match("/^[0-9]+$/", $_POST['operacao']) ? $_POST['operacao'] : 0) : 0;

	if ($op_num != 0) {
    // Inicializa as vari�veis
    $userREmail = '';
    $userRLogin = '';

    // Valida e limpa o email
    if (isset($_POST['email'])) {
        $userREmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if (!filter_var($userREmail, FILTER_VALIDATE_EMAIL)) {
            $userREmail = preg_replace("/[^0-9a-zA-Z._]/", "", $_POST['email']);
        }
    }

    // Valida e limpa o login
    if (isset($_POST['login'])) {
        $userRLogin = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_EMAIL);
        if (!filter_var($userRLogin, FILTER_VALIDATE_EMAIL)) {
            $userRLogin = preg_replace("/[^0-9a-zA-Z._]/", "", $_POST['login']);
        }
    }
}

 
  if ($op_num == 1) {
	  // Usar sistema seguro de recuperação apenas com email
	  if ($userREmail != ''){
		   
		// Usar novo sistema de recuperação segura
		$recovery_result = request_password_recovery($userREmail, $db);
		
		if ($recovery_result['success']) {
			$msg = $recovery_result['message'];
		} else {
			$msg = $recovery_result['message'];
		}
	  } else {
		$msg = "Email é obrigatório para recuperação de senha.";
	  } ?>
		  <script> window.location = 'index.php?msg=<?php echo urlencode($msg);?>';</script>
		
		<?php
  } else if($op_num == 2) {
    $msg = '';
    if($userREmail != '' && $_POST['senha'] != '' && $_POST['confsenha'] != ''){
			//Alterado por Michel Saddock  29/09/2006
			if (!is_valid_email($userREmail)) {
			   $msg = "E-Mail Incorreto.";
			   $forward="error";
			}else if (strlen($_POST['senha']) < '10'){
			   $msg = "Senha deve ter pelo menos 10 caracteres.";
			   $forward="error";
			}else if ($_POST['senha'] == $userREmail ) {
			   $msg = "Senha não pode ser igual ao email.";
			   $forward="error";
			}else if ($_POST['senha'] <> $_POST['confsenha']) {
			   $msg = "Senhas não coincidem.";
			   $forward="error";
			}else {
			   // Validar força da senha
			   $strongRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})/";
			   if (!preg_match($strongRegex, $_POST['senha'])) {
			      $msg = "A senha deve conter pelo menos: 1 letra minúscula, 1 maiúscula, 1 número, 1 caractere especial e ter 10+ caracteres.";
			      $forward="error";
			}else{
				$sql = "SELECT id 
								FROM Users 
								WHERE login = ? And perfil = ?"; 

		    $sqlPrep 	= odbc_prepare($db, $sql);
		    $sqlRs 		= odbc_execute($sqlPrep, array($userREmail, 'C'));
		    $sqlCur		= odbc_fetch_array($sqlPrep);

				$idUser  	= $sqlCur ? $sqlCur['id'] : '';

				if ($idUser) {
				  $msg = "Este login j� consta em nossa base";
				  $forward="error";
				}else{
				  odbc_autocommit ($db, FALSE);
				  // Usar novo sistema de hash seguro
				  $pwd = hybrid_password_hash($_POST['senha']);
				  // Gerar cookie único
				  $key = uniqid('user_', true);
				  
				  //odbc_exec($db,"INSERT INTO Users (cookie, name, login, email, password, state, perfil) VALUES ('".$key."','". utf8_decode($_POST["nome"]) ."','".$userREmail."','".$userREmail."','".$pwd."','0', 'C')");

				  $query = "INSERT INTO Users (cookie, name, login, email, password, state, perfil) 
          VALUES (?, ?, ?, ?, ?, ?, ?)";

				// Prepara a query
				$stmt = odbc_prepare($db, $query);

				// Par�metros da query
				$params = [
				    $key,                  // cookie
				    utf8_decode($_POST['nome']), // name
				    $userREmail,           // login
				    $userREmail,           // email
				    $pwd,                  // password
				    '0',                   // state
				    'C'                    // perfil
				];

				// Executa a query com par�metros
				odbc_execute($stmt, $params);


					$sql = "SELECT id 
									FROM Users 
									WHERE cookie = ?"; 

			    $sqlPrep 	= odbc_prepare($db, $sql);
			    $sqlRs 		= odbc_execute($sqlPrep, array($key));
			    $sqlCur		= odbc_fetch_array($sqlPrep);

					$idUser  	= $sqlCur ? $sqlCur['id'] : '';
			
				  // associa o papel cliente a este usu�rio			
				  $r = odbc_do($db,	"INSERT INTO UserRole (idUser, idRole) VALUES (".$idUser.",1)");			
			
				  if ($r != FALSE)
					 odbc_commit($db);
				  else
					 odbc_rollback($db);
				  
				  odbc_autocommit ($db, TRUE);
			
				  $congratulation = true;

				  $msg = "Cadastro efetuado com sucesso";

	        /*
					  if ($comm == "setCreateLog" ) {
						 $per = 'C';
					  }
			
					  // efetua o login do usu�rio
					  
					  require_once("../../entity/user/User.php");
					  $u = new User($field->getField("login"),$field->getField("password1"), $per, $db);
					  $user = $u->getUserView();
					  $_SESSION["user"] = $user;
				
					  if ($user == NULL || !$role["client"]) {
						 $msg = "N�o consegui efetuar o login do usu�rio";
						 $forward = "error";
					  }
					*/
				}
			}
		  }

			$sql = "SELECT id, login, name, password, email
							FROM Users 
							WHERE login = ? And perfil = ? and password = ?"; 

	    $sqlPrep 	= odbc_prepare($db, $sql);
	    $sqlRs 		= odbc_execute($sqlPrep, array($userREmail, 'C', $_POST['senha']));
	    $sqlCur		= odbc_fetch_array($sqlPrep);

			$id     	= $sqlCur ? $sqlCur['id'] : '';
			$login    = $sqlCur ? $sqlCur['login'] : '';
		  $name     = $sqlCur ? $sqlCur['name'] : '';
		  $password = $sqlCur ? $sqlCur['password'] : '';
		  $email    = $sqlCur ? $sqlCur['email'] : '';
				 
		  if($login != "" && $msg == ""){
			  /*			   
				require_once("src/role/MailSend.php"); 

				$message  = "<p><b>".$name.'</b>,</p>'
				.'Segue abaixo o seu acesso ao Sistema SIGA:<br> '
				."<b>Login:</b> ".$login."<br>"
				."<b>Senha:</b> ".$password."<br><br>"
				."Atenciosamente<br><br>"
				."COFACE do Brasil Seguro de Cr�dito SA.<br>"
				."www.coface.com.br";	

				$mail->From = "coface@coface.com.br"; // Seu e-mail
				$mail->FromName = "Sistema Siex"; // Seu nome 
				// Define os destinat�rio(s)

				$mail->AddAddress($email);

				$mail->IsHTML(false); // Define que o e-mail ser� enviado como HTML
				$mail->Subject  = "Acesso de usu�rio"; // Assunto da mensagem
				$mail->Body = $message;
				$enviado = $mail->Send();   // envia o email
				$mail->ClearAllRecipients();
				$mail->ClearAttachments();
				*/

				$enviado = true;	

				//  Exibe uma mensagem de resultado
				if ($enviado) {
					//$msg = "Foi enviado um e-mail para o seguinte endere�o: ".$email."";

					//$_SESSION['mensagemSucess'] = "Cadastro efetuado com sucesso";
					$msg = "Cadastro efetuado com sucesso";

					odbc_commit ($db);

					$forward = "success";
				} else {
					//$msg = "Problemas no envio do e-mail, verifique este endere�o: ".$userREmail." ou tente novamente mais tarde.<br>";

					$msg = "Problemas no cadastro, por favor tente novamente";
				} 
				
				//print $msg;
				
				//die($_SESSION['mensagemSucess']);
				
				header("location: index.php?msg=". $msg); ?>
		    <script> //window.location = 'index.php?msg=<?php echo $msg;?>';</script>
		    <?php 
		  } else { 
				//$_SESSION['mensagemSucess'] =  $msg;

				header("location: index.php?msg=". $msg); 
		  }
	  }
	} else { ?>
		  <script> window.location = 'index.php?msg=Informe os dados corretamente!';</script>
		  <?php 
	  }
	}  
?>

<p align="right"><a href="javascript:TINY.box.hide()" class="linktexto">Fechar</a></p>
<?php 
	$rem = isset($_REQUEST['rem']) ? (preg_match("/^[0-9]+$/", $_REQUEST['rem']) ? $_REQUEST['rem'] : '') : '';

	if ($rem == 1){ ?>
		<form action="remember.php" method="post">
		   <label>Informe os seguintes dados:</label>
		   
		   <label>E-mail</label>
		   <input type="text" name="email" id="email" value="">
		   
		   <label>Login</label>
		   <input type="text" name="login" id="login" value="">
		   
		   <input type="hidden" name="operacao" id="operacao" value="1">
		   
		   <button class="botaoagm" type="button" id="validar" onclick="javascript: this.form.submit();">OK</button>	

		</form>
 <?php 
	} else if ($rem == 2){ ?>   
		<form action="remember.php" method="post">
			<label>Informe os seguintes dados:</label>

			<label>Nome </label>
			<input type="text" name="nome" id="nome" value="">

			<label>E-mail - Ser� utilizado como login</label>
			<input type="text" name="email" id="email" value="">

			<label>Senha</label>
			<input type="password" name="senha" id="senha">

			<label>Confirme a senha</label>
			<input type="password" name="confsenha" id="confsenha">

			<input type="hidden" name="operacao" id="operacao" value="2">

			<button class="botaoagm" type="button" id="validar" onclick="javascript: this.form.submit();">OK</button>
		</form>
  <?php  
		} else if ($rem == 3) { 
			$user = isset($_GET['user']) ? (preg_match("/^[0-9]+$/", $_GET['user']) ? $_GET['user'] : 0) : 0;

			if ($user != 0) { 

				$sql = "SELECT id, login 
								FROM Users 
								WHERE id = ?"; 

		    $sqlPrep 	= odbc_prepare($db, $sql);
		    $sqlRs 		= odbc_execute($sqlPrep, array($user));
		    $sqlCur		= odbc_fetch_array($sqlPrep);

				$id     	= $sqlCur ? $sqlCur['id'] : '';
				$login    = $sqlCur ? $sqlCur['login'] : ''; ?>
	      
	      <form action="src/role/access/Access.php" method="post" id='newPassForm'>
					<h2>ATEN��O</h2>
					<label>Prezado usu�rio,</label>
				
					<label>por motivos de seguran�a informamos que sua senha de acesso
					<?php
					 if($_REQUEST['alter'] == "" && $_REQUEST['indic'] == 'dentroPrazo'){
						echo " expira em 7 dias.";
					 }else if($_REQUEST['alter'] != "" && $_REQUEST['indic'] == 'dentroPrazo'){
						echo ' expira em ' . htmlspecialchars($_REQUEST['alter'], ENT_QUOTES, 'UTF-8') . '.';
					 }else if($_REQUEST['indic'] == 'foraPrazo'){
					    echo ' expirou. ';
					 }
					?>

			   	<br>
			   	<br>
						Sendo assim, pedimos a gentileza de cadastrar a sua nova senha, importante que ter no m�nimo 8 caracteres.<br><br>	
						Esta medida visa manter a confidencialidade dos seus dados. <br><br>	
						Atenciosamente, <br>
						<?php echo $nomeEmp; ?>
			    <br>
	        </label>		   
	                         
					<label><div id="message" style="display:none; color:#F00;"></div></label>
					<label>Senha Atual</label>
					<input type="password" name="senhaAtual" id="senhaAtual" style="width:250px">

					<label>Nova Senha</label>
					<input type="password" name="novaSenha" id="novaSenha" style="width:250px">

					<label>Confirme a nova senha</label>
					<input type="password" name="confsenha" id="confsenha" style="width:250px">

					<input type="hidden" name="login" id="login" value="<?php echo $login;?>">
					<input type="hidden" name="id" id="id" value="<?php echo $id;?>">
					<input type="hidden" name="operacao" id="operacao" value="3">

					<div style="clear:both">&nbsp;</div>
					<button class="botaoagg" type="button" onClick="javascript: if (validaform())this.form.submit();">Alterar Senha</button>
					<?php if($_REQUEST['indic'] != 'foraPrazo'){ ?>
						<button class="botaoagg" type="button" onClick="this.form.submit()">N�o alterar senha</button>
					<?php } ?>
				</form>
 		<?php  } else { ?> 
 			<script> window.location = 'index.php';</script>
 		<?php  } ?> 
 	<?php  } else { ?> 
 		<script> window.location = 'index.php';</script>
 	<?php  } ?> 
