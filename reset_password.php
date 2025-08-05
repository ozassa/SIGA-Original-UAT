<?php
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/security_functions.php';
require_once __DIR__ . '/secure_password_recovery.php';
require_once __DIR__ . '/src/role/rolePrefix.php';

$token = isset($_GET['token']) ? $_GET['token'] : '';
$error_message = '';
$success_message = '';

// Processar formulário de redefinição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_middleware();
    
    $token = safe_request('token', 'string');
    $new_password = safe_request('new_password', 'string');
    $confirm_password = safe_request('confirm_password', 'string');
    
    if (empty($new_password) || empty($confirm_password)) {
        $error_message = 'Todos os campos são obrigatórios.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'As senhas não coincidem.';
    } elseif (strlen($new_password) < 10) {
        $error_message = 'A senha deve ter pelo menos 10 caracteres.';
    } else {
        // Validar força da senha (mesmo padrão do frontend)
        $strongRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})/";
        if (!preg_match($strongRegex, $new_password)) {
            $error_message = "A senha deve conter pelo menos: 1 letra minúscula, 1 maiúscula, 1 número, 1 caractere especial e ter 10+ caracteres.";
        } else {
            $result = reset_password_with_token($token, $new_password, $db);
            
            if ($result['success']) {
                $success_message = $result['message'];
                $token = ''; // Limpar token após uso
            } else {
                $error_message = $result['message'];
            }
        }
    }
}

// Validar token se presente
$token_valid = false;
if (!empty($token)) {
    $token_data = validate_recovery_token($token);
    $token_valid = ($token_data !== false);
    
    if (!$token_valid) {
        $error_message = 'Link de recuperação inválido ou expirado.';
    }
}

$host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SIGA - Redefinir Senha</title>
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
            <?php elseif ($token_valid): ?>
                <span class="texto11">Digite sua nova senha nos campos abaixo:</span><br /><br />

                <?php if ($error_message): ?>
                    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 4px;">
                        <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="reset_password.php" id="resetForm">
                    <?php echo csrf_token_field(); ?>
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>" />
                    
                    <label>Nova Senha</label>
                    <input name="new_password" id="new_password" type="password" required />
                    
                    <label>Confirmar Nova Senha</label>
                    <input name="confirm_password" id="confirm_password" type="password" required />
                    <br />
                    
                    <div id="password_requirements" style="font-size: 11px; color: #666; margin: 10px 0;">
                        A senha deve conter:
                        <ul style="margin-left: 20px;">
                            <li>Pelo menos 10 caracteres</li>
                            <li>1 letra minúscula</li>
                            <li>1 letra maiúscula</li>
                            <li>1 número</li>
                            <li>1 caractere especial (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <button class="botaoagm" type="submit" id="submitBtn">Redefinir Senha</button>
                </form>

                <p>
                    <a href="index.php" class="linktexto">Voltar ao Login</a>
                </p>

            <?php else: ?>
                <span class="texto11">Link de recuperação inválido ou expirado</span><br /><br />
                
                <?php if ($error_message): ?>
                    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 4px;">
                        <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <p>
                    <a href="index.php" class="linktexto">Voltar ao Login</a><br />
                    <a href="recover_password.php" class="linktexto">Solicitar Nova Recuperação</a>
                </p>
            <?php endif; ?>

        </div>
    </div>

    <script language="javascript" type="text/javascript">
        function validateForm() {
            var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})");
            var newPassword = document.getElementById('new_password').value;
            var confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword === '') {
                alert('Por favor, informe a nova senha.');
                document.getElementById('new_password').focus();
                return false;
            }

            if (confirmPassword === '') {
                alert('Por favor, confirme a nova senha.');
                document.getElementById('confirm_password').focus();
                return false;
            }

            if (newPassword !== confirmPassword) {
                alert('As senhas não coincidem.');
                document.getElementById('confirm_password').focus();
                return false;
            }

            if (!strongRegex.test(newPassword)) {
                alert("A senha deve conter:\n- Pelo menos 1 letra minúscula\n- Pelo menos 1 letra maiúscula\n- Pelo menos 1 número\n- Pelo menos 1 caractere especial\n- Pelo menos 10 caracteres");
                document.getElementById('new_password').focus();
                return false;
            }

            return true;
        }

        document.getElementById('resetForm').onsubmit = function() {
            return validateForm();
        };

        // Indicador visual de força da senha
        document.getElementById('new_password').onkeyup = function() {
            var password = this.value;
            var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})");
            
            if (strongRegex.test(password)) {
                this.style.borderColor = 'green';
            } else {
                this.style.borderColor = 'red';
            }
        };
    </script>
</body>
</html>