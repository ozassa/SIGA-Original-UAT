<?php
/**
 * Dashboard de Segurança SIGA
 * 
 * Página para monitoramento das implementações de segurança
 * Mostra estatísticas de autenticação, brute force, tentativas bloqueadas, etc.
 */

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/security_functions.php';
require_once __DIR__ . '/hybrid_auth.php';
require_once __DIR__ . '/secure_password_recovery.php';
require_once __DIR__ . '/src/role/rolePrefix.php';

// Verificar se usuário é administrador
if (!validate_user_session() || $_SESSION['pefil'] !== 'F') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acesso negado. Apenas administradores podem acessar esta página.');
}

// Obter estatísticas de segurança
$security_stats = get_security_stats();

$host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SIGA - Dashboard de Segurança</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/geral.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="Scripts/jquery.js"></script>
    <style>
        .security-dashboard {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
            text-align: center;
        }
        
        .stat-card.warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        
        .stat-card.danger {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        
        .stat-card.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stat-description {
            font-size: 0.8em;
            color: #888;
        }
        
        .security-features {
            margin: 30px 0;
        }
        
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .feature-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #28a745;
        }
        
        .feature-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .feature-description {
            font-size: 0.9em;
            color: #666;
        }
        
        .log-viewer {
            margin: 30px 0;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
        }
        
        .log-entry {
            font-family: monospace;
            font-size: 0.85em;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        .log-entry:last-child {
            border-bottom: none;
        }
        
        .timestamp {
            color: #666;
        }
        
        .success {
            color: #28a745;
        }
        
        .failed {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="security-dashboard">
        <h1>Dashboard de Segurança SIGA</h1>
        <p>Monitoramento das implementações de segurança e estatísticas de acesso</p>
        
        <div class="stats-grid">
            <div class="stat-card success">
                <div class="stat-number"><?php echo $security_stats['successful_logins_today']; ?></div>
                <div class="stat-label">Logins Bem-sucedidos</div>
                <div class="stat-description">Hoje</div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-number"><?php echo $security_stats['failed_attempts_today']; ?></div>
                <div class="stat-label">Tentativas Falhadas</div>
                <div class="stat-description">Hoje</div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-number"><?php echo $security_stats['blocked_ips']; ?></div>
                <div class="stat-label">IPs Bloqueados</div>
                <div class="stat-description">Atualmente</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $security_stats['password_upgrades_today']; ?></div>
                <div class="stat-label">Senhas Atualizadas</div>
                <div class="stat-description">Hoje (migração automática)</div>
            </div>
        </div>
        
        <div class="security-features">
            <h2>Recursos de Segurança Implementados</h2>
            
            <div class="feature-list">
                <div class="feature-card">
                    <div class="feature-title">Sistema Híbrido de Autenticação</div>
                    <div class="feature-description">
                        Migração automática e transparente de senhas crypt() para password_hash() BCRYPT com cost 12.
                        Senhas antigas continuam funcionando durante a transição.
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-title">Proteção Brute Force</div>
                    <div class="feature-description">
                        Rate limiting: máximo 5 tentativas por IP (5 min) e 3 por usuário (15 min).
                        Lockout automático com escalation temporal.
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-title">Segurança de Sessão</div>
                    <div class="feature-description">
                        Timeout de 30 minutos, regeneração de ID a cada 5 minutos,
                        detecção de hijacking por fingerprinting, cookies seguros.
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-title">Recuperação Segura de Senha</div>
                    <div class="feature-description">
                        Tokens time-based de 15 minutos, rate limiting, validação por email,
                        não revelar existência de usuários.
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-title">Proteção XSS e CSRF</div>
                    <div class="feature-description">
                        Sanitização contextual de saída, tokens CSRF únicos,
                        headers de segurança, validação de entrada.
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-title">Logging de Segurança</div>
                    <div class="feature-description">
                        Log detalhado de tentativas de login, ataques detectados,
                        mudanças de senha, tentativas de recuperação.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="log-viewer">
            <h3>Últimas Tentativas de Login (Hoje)</h3>
            <?php
            $log_file = SECURITY_LOG_DIR . '/login_attempts_' . date('Y-m-d') . '.log';
            if (file_exists($log_file)) {
                $lines = array_slice(file($log_file, FILE_IGNORE_NEW_LINES), -20); // Últimas 20 linhas
                foreach (array_reverse($lines) as $line) {
                    $data = json_decode($line, true);
                    if ($data) {
                        $class = $data['success'] ? 'success' : 'failed';
                        $status = $data['success'] ? 'SUCESSO' : 'FALHA';
                        echo "<div class='log-entry'>";
                        echo "<span class='timestamp'>{$data['timestamp']}</span> - ";
                        echo "<span class='{$class}'>{$status}</span> - ";
                        echo "IP: {$data['ip']} - ";
                        echo "Usuário: " . htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8');
                        echo "</div>";
                    }
                }
            } else {
                echo "<p>Nenhum log de hoje encontrado.</p>";
            }
            ?>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666;">
            <p>Sistema de Segurança SIGA - Implementado em <?php echo date('Y'); ?></p>
            <p>Todos os acessos e tentativas são logados para auditoria</p>
        </div>
    </div>
</body>
</html>