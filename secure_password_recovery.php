<?php
/**
 * Sistema Seguro de Recuperação de Senha SIGA
 * 
 * Implementa recuperação de senha com:
 * - Tokens seguros time-based
 * - Rate limiting
 * - Validação de email
 * - Expiração automática
 */

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/hybrid_auth.php';

// Configurações de recuperação
define('RECOVERY_TOKEN_LIFETIME', 900); // 15 minutos
define('MAX_RECOVERY_ATTEMPTS_PER_IP', 3);
define('MAX_RECOVERY_ATTEMPTS_PER_EMAIL', 2);
define('RECOVERY_COOLDOWN', 300); // 5 minutos entre tentativas

/**
 * Gera token seguro para recuperação de senha
 * 
 * @param string $email Email do usuário
 * @param int $user_id ID do usuário
 * @return string Token gerado
 */
function generate_recovery_token($email, $user_id) {
    $token_data = [
        'email' => $email,
        'user_id' => $user_id,
        'timestamp' => time(),
        'random' => bin2hex(random_bytes(16))
    ];
    
    return base64_encode(json_encode($token_data));
}

/**
 * Valida token de recuperação
 * 
 * @param string $token Token a ser validado
 * @return array|false Dados do token ou false se inválido
 */
function validate_recovery_token($token) {
    try {
        $token_data = json_decode(base64_decode($token), true);
        
        if (!$token_data || !isset($token_data['timestamp'])) {
            return false;
        }
        
        // Verificar se não expirou
        if (time() - $token_data['timestamp'] > RECOVERY_TOKEN_LIFETIME) {
            return false;
        }
        
        return $token_data;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Conta tentativas de recuperação por IP
 * 
 * @param string $ip Endereço IP
 * @return int Número de tentativas
 */
function count_recovery_attempts_by_ip($ip) {
    $log_file = SECURITY_LOG_DIR . '/recovery_attempts_' . date('Y-m-d') . '.log';
    
    if (!file_exists($log_file)) {
        return 0;
    }
    
    $count = 0;
    $cutoff_time = time() - RECOVERY_COOLDOWN;
    
    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data && $data['ip'] === $ip) {
            $attempt_time = strtotime($data['timestamp']);
            if ($attempt_time > $cutoff_time) {
                $count++;
            }
        }
    }
    
    return $count;
}

/**
 * Conta tentativas de recuperação por email
 * 
 * @param string $email Email
 * @return int Número de tentativas
 */
function count_recovery_attempts_by_email($email) {
    $log_file = SECURITY_LOG_DIR . '/recovery_attempts_' . date('Y-m-d') . '.log';
    
    if (!file_exists($log_file)) {
        return 0;
    }
    
    $count = 0;
    $cutoff_time = time() - RECOVERY_COOLDOWN;
    
    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data && $data['email'] === $email) {
            $attempt_time = strtotime($data['timestamp']);
            if ($attempt_time > $cutoff_time) {
                $count++;
            }
        }
    }
    
    return $count;
}

/**
 * Registra tentativa de recuperação
 * 
 * @param string $ip IP
 * @param string $email Email
 * @param bool $success Se foi bem-sucedida
 */
function log_recovery_attempt($ip, $email, $success = false) {
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $ip,
        'email' => $email,
        'success' => $success,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    if (!is_dir(SECURITY_LOG_DIR)) {
        mkdir(SECURITY_LOG_DIR, 0755, true);
    }
    
    $log_file = SECURITY_LOG_DIR . '/recovery_attempts_' . date('Y-m-d') . '.log';
    file_put_contents($log_file, json_encode($log_data) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

/**
 * Solicita recuperação de senha
 * 
 * @param string $email Email do usuário
 * @param object $db Conexão com banco
 * @return array Resultado da solicitação
 */
function request_password_recovery($email, $db) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Verificar rate limiting
    if (count_recovery_attempts_by_ip($ip) >= MAX_RECOVERY_ATTEMPTS_PER_IP) {
        log_recovery_attempt($ip, $email, false);
        return [
            'success' => false,
            'error' => 'IP_RATE_LIMITED',
            'message' => 'Muitas tentativas de recuperação. Tente novamente em 5 minutos.'
        ];
    }
    
    if (count_recovery_attempts_by_email($email) >= MAX_RECOVERY_ATTEMPTS_PER_EMAIL) {
        log_recovery_attempt($ip, $email, false);
        return [
            'success' => false,
            'error' => 'EMAIL_RATE_LIMITED',
            'message' => 'Muitas tentativas para este email. Tente novamente em 5 minutos.'
        ];
    }
    
    // Buscar usuário por email
    $stmt = odbc_prepare($db, "SELECT id, name, login, email FROM Users WHERE email = ? AND state = 0");
    $result = odbc_execute($stmt, [$email]);
    $user = odbc_fetch_array($stmt);
    
    if (!$user) {
        // Mesmo para email não encontrado, registrar tentativa
        log_recovery_attempt($ip, $email, false);
        // Não revelar se email existe ou não
        return [
            'success' => true,
            'message' => 'Se o email estiver cadastrado, você receberá instruções para redefinir sua senha.'
        ];
    }
    
    // Gerar token de recuperação
    $recovery_token = generate_recovery_token($email, $user['id']);
    
    // Criar URL de recuperação
    $recovery_url = "https://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . urlencode($recovery_token);
    
    // Preparar email (implementar conforme sistema de email existente)
    $email_subject = "SIGA - Recuperação de Senha";
    $email_body = "
    Olá {$user['name']},
    
    Você solicitou a recuperação de sua senha no sistema SIGA.
    
    Para redefinir sua senha, clique no link abaixo:
    $recovery_url
    
    Este link expira em 15 minutos.
    
    Se você não solicitou esta recuperação, ignore este email.
    
    Atenciosamente,
    Equipe SIGA
    ";
    
    // Enviar email (usar sistema existente do SIGA)
    $email_sent = send_recovery_email($user['email'], $email_subject, $email_body);
    
    if ($email_sent) {
        log_recovery_attempt($ip, $email, true);
        return [
            'success' => true,
            'message' => 'Instruções de recuperação enviadas para seu email.'
        ];
    } else {
        log_recovery_attempt($ip, $email, false);
        return [
            'success' => false,
            'error' => 'EMAIL_SEND_FAILED',
            'message' => 'Erro ao enviar email. Tente novamente mais tarde.'
        ];
    }
}

/**
 * Redefine senha usando token
 * 
 * @param string $token Token de recuperação
 * @param string $new_password Nova senha
 * @param object $db Conexão com banco
 * @return array Resultado da redefinição
 */
function reset_password_with_token($token, $new_password, $db) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Validar token
    $token_data = validate_recovery_token($token);
    if (!$token_data) {
        return [
            'success' => false,
            'error' => 'INVALID_TOKEN',
            'message' => 'Token inválido ou expirado.'
        ];
    }
    
    // Verificar se usuário ainda existe e está ativo
    $stmt = odbc_prepare($db, "SELECT id, email, login FROM Users WHERE id = ? AND email = ? AND state = 0");
    $result = odbc_execute($stmt, [$token_data['user_id'], $token_data['email']]);
    $user = odbc_fetch_array($stmt);
    
    if (!$user) {
        return [
            'success' => false,
            'error' => 'USER_NOT_FOUND',
            'message' => 'Usuário não encontrado.'
        ];
    }
    
    // Alterar senha usando sistema híbrido
    $success = hybrid_change_password($new_password, $user['id'], $db);
    
    if ($success) {
        // Log da recuperação bem-sucedida
        $log_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => 'PASSWORD_RECOVERY_SUCCESS',
            'user_id' => $user['id'],
            'email' => $user['email'],
            'ip' => $ip
        ];
        
        $recovery_log = SECURITY_LOG_DIR . '/password_recoveries_' . date('Y-m-d') . '.log';
        file_put_contents($recovery_log, json_encode($log_data) . PHP_EOL, FILE_APPEND | LOCK_EX);
        
        return [
            'success' => true,
            'message' => 'Senha redefinida com sucesso.'
        ];
    } else {
        return [
            'success' => false,
            'error' => 'UPDATE_FAILED',
            'message' => 'Erro ao redefinir senha. Tente novamente.'
        ];
    }
}

/**
 * Envia email de recuperação (integrar com sistema existente)
 * 
 * @param string $to Email destinatário
 * @param string $subject Assunto
 * @param string $body Corpo do email
 * @return bool True se enviado com sucesso
 */
function send_recovery_email($to, $subject, $body) {
    // Implementar usando o sistema de email existente do SIGA
    // Por exemplo, usando a classe MailSend.php que já existe
    
    try {
        // Validar email
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Headers básicos
        $headers = [
            'From: noreply@coface.com.br',
            'Reply-To: noreply@coface.com.br',
            'Content-Type: text/plain; charset=UTF-8',
            'X-Mailer: SIGA Security System'
        ];
        
        // Enviar email
        return mail($to, $subject, $body, implode("\r\n", $headers));
        
    } catch (Exception $e) {
        error_log("Erro ao enviar email de recuperação: " . $e->getMessage());
        return false;
    }
}

/**
 * Limpa tokens expirados (para manutenção)
 */
function cleanup_expired_recovery_tokens() {
    // Esta função pode ser chamada por um cron job
    $log_files = glob(SECURITY_LOG_DIR . '/recovery_attempts_*.log');
    
    foreach ($log_files as $log_file) {
        $file_date = basename($log_file, '.log');
        $file_date = str_replace('recovery_attempts_', '', $file_date);
        
        // Remover logs mais antigos que 7 dias
        if (strtotime($file_date) < strtotime('-7 days')) {
            unlink($log_file);
        }
    }
}

?>