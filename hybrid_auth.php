<?php
/**
 * Sistema Híbrido de Autenticação SIGA
 * 
 * Este sistema implementa autenticação híbrida mantendo compatibilidade
 * com senhas crypt() existentes enquanto migra para password_hash() seguro.
 * 
 * SEGURANÇA IMPLEMENTADA:
 * - Password hashing com BCRYPT (cost 12)
 * - Migração automática transparente
 * - Proteção contra brute force
 * - Rate limiting por IP e usuário
 * - Logging detalhado de tentativas
 */

// Configurações de segurança
define('BCRYPT_COST', 12);
define('MAX_LOGIN_ATTEMPTS_PER_IP', 5);
define('MAX_LOGIN_ATTEMPTS_PER_USER', 3);
define('LOCKOUT_TIME_IP', 300); // 5 minutos
define('LOCKOUT_TIME_USER', 900); // 15 minutos
define('SECURITY_LOG_DIR', __DIR__ . '/logs/security');

/**
 * Verifica se uma senha corresponde ao hash usando método híbrido
 * 
 * @param string $password Senha em texto plano
 * @param string $hash Hash armazenado
 * @param string $salt SALT legacy para crypt()
 * @return bool True se a senha corresponde
 */
function hybrid_password_verify($password, $hash, $salt = null) {
    // Primeiro tenta verificar com password_verify (novo sistema)
    if (password_verify($password, $hash)) {
        return true;
    }
    
    // Se falhar, tenta com crypt() (sistema legacy)
    if ($salt && crypt($password, $salt) === $hash) {
        return true;
    }
    
    return false;
}

/**
 * Cria hash seguro da senha usando BCRYPT
 * 
 * @param string $password Senha em texto plano
 * @return string Hash seguro
 */
function hybrid_password_hash($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
}

/**
 * Verifica se o hash precisa ser atualizado
 * 
 * @param string $hash Hash atual
 * @return bool True se precisa atualizar
 */
function hybrid_password_needs_rehash($hash) {
    // Se não começa com $2y$ (BCRYPT), precisa atualizar
    if (!str_starts_with($hash, '$2y$')) {
        return true;
    }
    
    return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
}

/**
 * Registra tentativa de login para controle de brute force
 * 
 * @param string $ip Endereço IP
 * @param string $username Nome de usuário
 * @param bool $success Se foi bem-sucedida
 */
function log_login_attempt($ip, $username, $success = false) {
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $ip,
        'username' => $username,
        'success' => $success,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    // Criar diretório se não existir
    if (!is_dir(SECURITY_LOG_DIR)) {
        mkdir(SECURITY_LOG_DIR, 0755, true);
    }
    
    $log_file = SECURITY_LOG_DIR . '/login_attempts_' . date('Y-m-d') . '.log';
    file_put_contents($log_file, json_encode($log_data) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

/**
 * Conta tentativas de login falhadas para um IP
 * 
 * @param string $ip Endereço IP
 * @param int $timeframe Período em segundos
 * @return int Número de tentativas
 */
function count_failed_attempts_by_ip($ip, $timeframe = LOCKOUT_TIME_IP) {
    $log_file = SECURITY_LOG_DIR . '/login_attempts_' . date('Y-m-d') . '.log';
    
    if (!file_exists($log_file)) {
        return 0;
    }
    
    $count = 0;
    $cutoff_time = time() - $timeframe;
    
    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data && $data['ip'] === $ip && !$data['success']) {
            $attempt_time = strtotime($data['timestamp']);
            if ($attempt_time > $cutoff_time) {
                $count++;
            }
        }
    }
    
    return $count;
}

/**
 * Conta tentativas de login falhadas para um usuário
 * 
 * @param string $username Nome de usuário
 * @param int $timeframe Período em segundos
 * @return int Número de tentativas
 */
function count_failed_attempts_by_user($username, $timeframe = LOCKOUT_TIME_USER) {
    $log_file = SECURITY_LOG_DIR . '/login_attempts_' . date('Y-m-d') . '.log';
    
    if (!file_exists($log_file)) {
        return 0;
    }
    
    $count = 0;
    $cutoff_time = time() - $timeframe;
    
    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data && $data['username'] === $username && !$data['success']) {
            $attempt_time = strtotime($data['timestamp']);
            if ($attempt_time > $cutoff_time) {
                $count++;
            }
        }
    }
    
    return $count;
}

/**
 * Verifica se IP está bloqueado por brute force
 * 
 * @param string $ip Endereço IP
 * @return bool True se está bloqueado
 */
function is_ip_blocked($ip) {
    return count_failed_attempts_by_ip($ip) >= MAX_LOGIN_ATTEMPTS_PER_IP;
}

/**
 * Verifica se usuário está bloqueado por brute force
 * 
 * @param string $username Nome de usuário
 * @return bool True se está bloqueado
 */
function is_user_blocked($username) {
    return count_failed_attempts_by_user($username) >= MAX_LOGIN_ATTEMPTS_PER_USER;
}

/**
 * Autentica usuário com sistema híbrido e proteção brute force
 * 
 * @param string $username Nome de usuário
 * @param string $password Senha
 * @param object $db Conexão com banco
 * @return array Resultado da autenticação
 */
function hybrid_authenticate($username, $password, $db) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Verificar proteção brute force
    if (is_ip_blocked($ip)) {
        log_login_attempt($ip, $username, false);
        return [
            'success' => false,
            'error' => 'IP_BLOCKED',
            'message' => 'Muitas tentativas de login. Tente novamente em ' . (LOCKOUT_TIME_IP / 60) . ' minutos.'
        ];
    }
    
    if (is_user_blocked($username)) {
        log_login_attempt($ip, $username, false);
        return [
            'success' => false,
            'error' => 'USER_BLOCKED',
            'message' => 'Usuário temporariamente bloqueado. Tente novamente em ' . (LOCKOUT_TIME_USER / 60) . ' minutos.'
        ];
    }
    
    // Buscar usuário no banco
    $stmt = odbc_prepare($db, "SELECT id, name, login, password, email, state, perfil, alterSenha FROM Users WHERE login = ? AND state = 0");
    $result = odbc_execute($stmt, [$username]);
    $user = odbc_fetch_array($stmt);
    
    if (!$user) {
        log_login_attempt($ip, $username, false);
        return [
            'success' => false,
            'error' => 'INVALID_CREDENTIALS',
            'message' => 'Usuário ou senha inválidos.'
        ];
    }
    
    // Verificar senha com sistema híbrido
    $password_valid = false;
    
    // Primeiro tenta novo sistema (password_hash)
    if (password_verify($password, $user['password'])) {
        $password_valid = true;
    } else {
        // Tenta sistema legacy (crypt com SALT)
        if (defined('SALT') && crypt($password, SALT) === $user['password']) {
            $password_valid = true;
            
            // Marcar para upgrade automático da senha
            $user['needs_password_upgrade'] = true;
        }
    }
    
    if (!$password_valid) {
        log_login_attempt($ip, $username, false);
        return [
            'success' => false,
            'error' => 'INVALID_CREDENTIALS',
            'message' => 'Usuário ou senha inválidos.'
        ];
    }
    
    // Login bem-sucedido
    log_login_attempt($ip, $username, true);
    
    // Fazer upgrade da senha se necessário
    if (isset($user['needs_password_upgrade']) && $user['needs_password_upgrade']) {
        $new_hash = hybrid_password_hash($password);
        $update_stmt = odbc_prepare($db, "UPDATE Users SET password = ? WHERE id = ?");
        odbc_execute($update_stmt, [$new_hash, $user['id']]);
        
        // Log do upgrade
        $log_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => 'PASSWORD_UPGRADE',
            'user_id' => $user['id'],
            'username' => $username,
            'ip' => $ip
        ];
        
        $upgrade_log = SECURITY_LOG_DIR . '/password_upgrades_' . date('Y-m-d') . '.log';
        file_put_contents($upgrade_log, json_encode($log_data) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    return [
        'success' => true,
        'user' => $user,
        'password_upgraded' => isset($user['needs_password_upgrade'])
    ];
}

/**
 * Função para alterar senha com hash seguro
 * 
 * @param string $new_password Nova senha
 * @param int $user_id ID do usuário
 * @param object $db Conexão com banco
 * @return bool True se alterada com sucesso
 */
function hybrid_change_password($new_password, $user_id, $db) {
    $new_hash = hybrid_password_hash($new_password);
    $data_expiracao = date("Y-m-d", time() + (45 * 24 * 3600)); // 45 dias
    
    $stmt = odbc_prepare($db, "UPDATE Users SET password = ?, alterSenha = ?, tentativaSenha = 0 WHERE id = ?");
    $result = odbc_execute($stmt, [$new_hash, $data_expiracao, $user_id]);
    
    if ($result) {
        // Log da alteração
        $log_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => 'PASSWORD_CHANGE',
            'user_id' => $user_id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        if (!is_dir(SECURITY_LOG_DIR)) {
            mkdir(SECURITY_LOG_DIR, 0755, true);
        }
        
        $change_log = SECURITY_LOG_DIR . '/password_changes_' . date('Y-m-d') . '.log';
        file_put_contents($change_log, json_encode($log_data) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    return $result;
}

/**
 * Gera estatísticas de segurança (para monitoramento)
 * 
 * @return array Estatísticas
 */
function get_security_stats() {
    $stats = [
        'blocked_ips' => 0,
        'failed_attempts_today' => 0,
        'successful_logins_today' => 0,
        'password_upgrades_today' => 0
    ];
    
    $log_file = SECURITY_LOG_DIR . '/login_attempts_' . date('Y-m-d') . '.log';
    
    if (file_exists($log_file)) {
        $lines = file($log_file, FILE_IGNORE_NEW_LINES);
        $blocked_ips = [];
        
        foreach ($lines as $line) {
            $data = json_decode($line, true);
            if ($data) {
                if ($data['success']) {
                    $stats['successful_logins_today']++;
                } else {
                    $stats['failed_attempts_today']++;
                    
                    // Contar IPs únicos bloqueados
                    if (count_failed_attempts_by_ip($data['ip']) >= MAX_LOGIN_ATTEMPTS_PER_IP) {
                        $blocked_ips[$data['ip']] = true;
                    }
                }
            }
        }
        
        $stats['blocked_ips'] = count($blocked_ips);
    }
    
    // Contar upgrades de senha
    $upgrade_log = SECURITY_LOG_DIR . '/password_upgrades_' . date('Y-m-d') . '.log';
    if (file_exists($upgrade_log)) {
        $stats['password_upgrades_today'] = count(file($upgrade_log, FILE_IGNORE_NEW_LINES));
    }
    
    return $stats;
}

?>