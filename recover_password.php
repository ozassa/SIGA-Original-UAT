<?php
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/security_functions.php';
require_once __DIR__ . '/secure_password_recovery.php';
require_once __DIR__ . '/src/role/rolePrefix.php';

$error_message = '';
$success_message = '';

// Processar formulário de recuperação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_middleware();
    
    $email = safe_request('email', 'email');
    
    if (empty($email)) {
        $error_message = 'Email é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Email inválido.';
    } else {
        $result = request_password_recovery($email, $db);
        
        if ($result['success']) {
            $success_message = $result['message'];
        } else {
            $error_message = $result['message'];
        }
    }
}

$host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SIGA - Recuperar Senha</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/geral.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="Scripts/jquery.js"></script>
</head>

<body class="fundoimagem">
    <div id="entradageral">
        <div id="entrada">
            <p><img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/icone_usuario.png" alt="" width="36" height="44" /></p>
            <p><img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/texto_acessogprint.png" alt="" width="111" height="23" /></p>

            <?php if ($success_message): ?>
                <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; margin: 10px 0; border-radius: 4px;">
                    <?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <p>
                    <a href="index.php" class="linktexto">Voltar ao Login</a>
                </p>
            <?php else: ?>
                <span class="texto11">Digite seu email para recuperar a senha:</span><br /><br />

                <?php if ($error_message): ?>
                    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 4px;">
                        <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="recover_password.php" id="recoveryForm">
                    <?php echo csrf_token_field(); ?>
                    
                    <label>Email</label>
                    <input name="email" id="email" type="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>" />
                    <br />
                    
                    <div style="font-size: 11px; color: #666; margin: 10px 0;">
                        Enviaremos um link de recuperação para seu email.<br />
                        O link expira em 15 minutos.
                    </div>

                    <button class="botaoagm" type="submit" id="submitBtn">Enviar</button>
                </form>

                <p>
                    <a href="index.php" class="linktexto">Voltar ao Login</a>
                </p>
            <?php endif; ?>

        </div>
    </div>

    <script language="javascript" type="text/javascript">
        function validateForm() {
            var email = document.getElementById('email').value;
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email === '') {
                alert('Por favor, informe seu email.');
                document.getElementById('email').focus();
                return false;
            }

            if (!emailRegex.test(email)) {
                alert('Por favor, informe um email válido.');
                document.getElementById('email').focus();
                return false;
            }

            return true;
        }

        document.getElementById('recoveryForm').onsubmit = function() {
            return validateForm();
        };

        // Desabilitar botão após envio para evitar spam
        document.getElementById('submitBtn').onclick = function() {
            if (validateForm()) {
                this.disabled = true;
                this.innerHTML = 'Enviando...';
                document.getElementById('recoveryForm').submit();
            }
        };
    </script>
</body>
</html>