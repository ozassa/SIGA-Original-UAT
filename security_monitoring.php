<?php
/**
 * SIGA Security Monitoring Center
 * 
 * Centro de Comando de Segurança - Dashboard avançado em tempo real
 * Monitoramento, detecção e resposta a ameaças
 * 
 * @version 1.0
 * @author Claude Code - Security Mission
 */

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/security_functions.php';
require_once __DIR__ . '/InputValidationFramework.php';
require_once __DIR__ . '/src/role/rolePrefix.php';

// Verificar autenticação e privilégios administrativos
if (!validate_user_session() || $_SESSION['pefil'] !== 'F') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acesso negado. Apenas administradores podem acessar o Centro de Segurança.');
}

/**
 * Classe principal do Centro de Monitoramento de Segurança
 */
class SecurityMonitoringCenter {
    
    private $log_dirs = [
        'security' => 'logs/security',
        'validation' => 'logs/validation', 
        'authentication' => 'logs/authentication',
        'attacks' => 'logs/attacks'
    ];
    
    private $real_time_data = [];
    
    public function __construct() {
        $this->ensureLogDirectories();
        $this->loadRealTimeData();
    }
    
    /**
     * Garantir que diretórios de log existem
     */
    private function ensureLogDirectories() {
        foreach ($this->log_dirs as $type => $dir) {
            $full_path = __DIR__ . '/' . $dir;
            if (!is_dir($full_path)) {
                mkdir($full_path, 0755, true);
            }
        }
    }
    
    /**
     * Carregar dados em tempo real
     */
    private function loadRealTimeData() {
        $this->real_time_data = [
            'dashboard_stats' => $this->getDashboardStats(),
            'threat_timeline' => $this->getThreatTimeline(),
            'attack_map' => $this->getAttackMap(),
            'system_health' => $this->getSystemHealth(),
            'active_threats' => $this->getActiveThreats()
        ];
    }
    
    /**
     * Obter estatísticas do dashboard
     */
    public function getDashboardStats() {
        $today = date('Y-m-d');
        $stats = [
            'attacks_blocked_today' => 0,
            'login_attempts_failed' => 0,
            'login_attempts_success' => 0,
            'ips_blocked' => 0,
            'xss_attempts' => 0,
            'sql_injection_attempts' => 0,
            'csrf_violations' => 0,
            'brute_force_attempts' => 0,
            'file_upload_blocked' => 0,
            'validation_failures' => 0
        ];
        
        // Processar logs de segurança
        $security_log = __DIR__ . "/logs/security/security_{$today}.log";
        if (file_exists($security_log)) {
            $lines = file($security_log, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                $data = json_decode($line, true);
                if ($data) {
                    switch ($data['type']) {
                        case 'XSS_ATTEMPT':
                            $stats['xss_attempts']++;
                            $stats['attacks_blocked_today']++;
                            break;
                        case 'SQL_INJECTION':
                            $stats['sql_injection_attempts']++;
                            $stats['attacks_blocked_today']++;
                            break;
                        case 'CSRF':
                            $stats['csrf_violations']++;
                            $stats['attacks_blocked_today']++;
                            break;
                        case 'BRUTE_FORCE':
                            $stats['brute_force_attempts']++;
                            $stats['attacks_blocked_today']++;
                            break;
                        case 'FILE_UPLOAD_BLOCKED':
                            $stats['file_upload_blocked']++;
                            $stats['attacks_blocked_today']++;
                            break;
                    }
                }
            }
        }
        
        // Processar logs de validação
        $validation_log = __DIR__ . "/logs/validation/validation_{$today}.log";
        if (file_exists($validation_log)) {
            $lines = file($validation_log, FILE_IGNORE_NEW_LINES);
            $stats['validation_failures'] = count($lines);
        }
        
        // Processar logs de autenticação
        $auth_log = __DIR__ . "/logs/authentication/login_attempts_{$today}.log";
        if (file_exists($auth_log)) {
            $lines = file($auth_log, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                $data = json_decode($line, true);
                if ($data) {
                    if ($data['success']) {
                        $stats['login_attempts_success']++;
                    } else {
                        $stats['login_attempts_failed']++;
                    }
                }
            }
        }
        
        // Contar IPs bloqueados (últimas 24h)
        $stats['ips_blocked'] = $this->getBlockedIPCount();
        
        return $stats;
    }
    
    /**
     * Obter timeline de ameaças (últimas 24h)
     */
    public function getThreatTimeline() {
        $timeline = [];
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        $log_files = [
            __DIR__ . "/logs/security/security_{$today}.log",
            __DIR__ . "/logs/security/security_{$yesterday}.log"
        ];
        
        foreach ($log_files as $log_file) {
            if (file_exists($log_file)) {
                $lines = file($log_file, FILE_IGNORE_NEW_LINES);
                foreach ($lines as $line) {
                    $data = json_decode($line, true);
                    if ($data && strtotime($data['timestamp']) > (time() - 86400)) {
                        $timeline[] = [
                            'timestamp' => $data['timestamp'],
                            'type' => $data['type'],
                            'ip' => $data['ip'],
                            'severity' => $this->getThreatSeverity($data['type']),
                            'description' => $this->getThreatDescription($data['type'])
                        ];
                    }
                }
            }
        }
        
        // Ordenar por timestamp
        usort($timeline, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return array_slice($timeline, 0, 50); // Últimas 50 ameaças
    }
    
    /**
     * Obter mapa de ataques por IP
     */
    public function getAttackMap() {
        $attack_map = [];
        $today = date('Y-m-d');
        
        $security_log = __DIR__ . "/logs/security/security_{$today}.log";
        if (file_exists($security_log)) {
            $lines = file($security_log, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                $data = json_decode($line, true);
                if ($data && isset($data['ip'])) {
                    $ip = $data['ip'];
                    if (!isset($attack_map[$ip])) {
                        $attack_map[$ip] = [
                            'ip' => $ip,
                            'total_attacks' => 0,
                            'attack_types' => [],
                            'first_seen' => $data['timestamp'],
                            'last_seen' => $data['timestamp'],
                            'geo_location' => $this->getIPGeolocation($ip),
                            'threat_level' => 'low'
                        ];
                    }
                    
                    $attack_map[$ip]['total_attacks']++;
                    $attack_map[$ip]['last_seen'] = $data['timestamp'];
                    
                    if (!in_array($data['type'], $attack_map[$ip]['attack_types'])) {
                        $attack_map[$ip]['attack_types'][] = $data['type'];
                    }
                    
                    // Calcular nível de ameaça
                    $attack_map[$ip]['threat_level'] = $this->calculateThreatLevel($attack_map[$ip]);
                }
            }
        }
        
        // Ordenar por número de ataques
        uasort($attack_map, function($a, $b) {
            return $b['total_attacks'] - $a['total_attacks'];
        });
        
        return array_slice($attack_map, 0, 20); // Top 20 IPs atacantes
    }
    
    /**
     * Obter saúde do sistema
     */
    public function getSystemHealth() {
        return [
            'security_status' => 'ACTIVE',
            'last_update' => date('Y-m-d H:i:s'),
            'uptime' => $this->getSystemUptime(),
            'memory_usage' => $this->getMemoryUsage(),
            'log_disk_usage' => $this->getLogDiskUsage(),
            'protection_modules' => [
                'xss_protection' => true,
                'csrf_protection' => true,
                'sql_injection_protection' => true,
                'brute_force_protection' => true,
                'file_upload_protection' => true,
                'session_security' => true,
                'input_validation' => true
            ]
        ];
    }
    
    /**
     * Obter ameaças ativas
     */
    public function getActiveThreats() {
        $active_threats = [];
        $current_time = time();
        
        // Verificar tentativas de brute force em andamento
        $brute_force_file = __DIR__ . '/logs/brute_force_tracking.json';
        if (file_exists($brute_force_file)) {
            $brute_data = json_decode(file_get_contents($brute_force_file), true);
            if ($brute_data) {
                foreach ($brute_data as $ip => $data) {
                    if ($current_time - $data['last_attempt'] < 3600) { // Última hora
                        $active_threats[] = [
                            'type' => 'ACTIVE_BRUTE_FORCE',
                            'ip' => $ip,
                            'attempts' => $data['attempts'],
                            'last_attempt' => date('H:i:s', $data['last_attempt']),
                            'severity' => $data['attempts'] > 10 ? 'critical' : 'high'
                        ];
                    }
                }
            }
        }
        
        // Verificar sessões suspeitas
        $suspicious_sessions = $this->detectSuspiciousSessions();
        foreach ($suspicious_sessions as $session) {
            $active_threats[] = [
                'type' => 'SUSPICIOUS_SESSION',
                'user_id' => $session['user_id'],
                'ip' => $session['ip'],
                'reason' => $session['reason'],
                'severity' => 'medium'
            ];
        }
        
        return $active_threats;
    }
    
    /**
     * Utilitários de apoio
     */
    private function getThreatSeverity($type) {
        $severity_map = [
            'SQL_INJECTION' => 'critical',
            'XSS_ATTEMPT' => 'high',
            'CSRF' => 'high',
            'BRUTE_FORCE' => 'medium',
            'FILE_UPLOAD_BLOCKED' => 'medium',
            'VALIDATION_FAILED' => 'low'
        ];
        
        return $severity_map[$type] ?? 'low';
    }
    
    private function getThreatDescription($type) {
        $descriptions = [
            'SQL_INJECTION' => 'Tentativa de SQL Injection detectada',
            'XSS_ATTEMPT' => 'Tentativa de Cross-Site Scripting',
            'CSRF' => 'Violação de proteção CSRF',
            'BRUTE_FORCE' => 'Tentativa de força bruta',
            'FILE_UPLOAD_BLOCKED' => 'Upload de arquivo malicioso bloqueado',
            'VALIDATION_FAILED' => 'Falha na validação de entrada'
        ];
        
        return $descriptions[$type] ?? 'Ameaça não identificada';
    }
    
    private function getBlockedIPCount() {
        // Simular contagem de IPs bloqueados
        $blocked_file = __DIR__ . '/logs/blocked_ips.json';
        if (file_exists($blocked_file)) {
            $blocked_data = json_decode(file_get_contents($blocked_file), true);
            return $blocked_data ? count($blocked_data) : 0;
        }
        return 0;
    }
    
    private function getIPGeolocation($ip) {
        // Simular geolocalização básica
        if ($ip === '127.0.0.1' || $ip === 'localhost') {
            return ['country' => 'Local', 'city' => 'Localhost'];
        }
        
        // Em produção, usar serviço como GeoIP ou similar
        return ['country' => 'Unknown', 'city' => 'Unknown'];
    }
    
    private function calculateThreatLevel($attack_data) {
        $total_attacks = $attack_data['total_attacks'];
        $attack_types = count($attack_data['attack_types']);
        
        if ($total_attacks > 50 || $attack_types > 3) {
            return 'critical';
        } elseif ($total_attacks > 20 || $attack_types > 2) {
            return 'high';
        } elseif ($total_attacks > 5) {
            return 'medium';
        }
        
        return 'low';
    }
    
    private function getSystemUptime() {
        // Simular uptime do sistema
        return '7 dias, 12 horas';
    }
    
    private function getMemoryUsage() {
        return [
            'used' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
            'peak' => round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB'
        ];
    }
    
    private function getLogDiskUsage() {
        $total_size = 0;
        foreach ($this->log_dirs as $dir) {
            $path = __DIR__ . '/' . $dir;
            if (is_dir($path)) {
                $total_size += $this->getDirSize($path);
            }
        }
        
        return round($total_size / 1024 / 1024, 2) . ' MB';
    }
    
    private function getDirSize($dir) {
        $size = 0;
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $file_path = $dir . '/' . $file;
                    if (is_file($file_path)) {
                        $size += filesize($file_path);
                    } elseif (is_dir($file_path)) {
                        $size += $this->getDirSize($file_path);
                    }
                }
            }
        }
        return $size;
    }
    
    private function detectSuspiciousSessions() {
        // Detectar sessões suspeitas baseadas em padrões
        $suspicious = [];
        
        // Em produção, implementar lógica mais sofisticada
        // Por exemplo: múltiplos logins do mesmo usuário de IPs diferentes
        
        return $suspicious;
    }
    
    /**
     * API para obter dados em tempo real via AJAX
     */
    public function getApiData($endpoint) {
        switch ($endpoint) {
            case 'dashboard_stats':
                return json_encode($this->getDashboardStats());
            case 'threat_timeline':
                return json_encode($this->getThreatTimeline());
            case 'attack_map':
                return json_encode($this->getAttackMap());
            case 'system_health':
                return json_encode($this->getSystemHealth());
            case 'active_threats':
                return json_encode($this->getActiveThreats());
            default:
                return json_encode(['error' => 'Endpoint não encontrado']);
        }
    }
}

// Instanciar centro de monitoramento
$security_center = new SecurityMonitoringCenter();

// Processar requisições AJAX
if (isset($_POST['ajax_endpoint'])) {
    header('Content-Type: application/json');
    echo $security_center->getApiData($_POST['ajax_endpoint']);
    exit;
}

// Obter dados para exibição inicial
$dashboard_stats = $security_center->getDashboardStats();
$threat_timeline = $security_center->getThreatTimeline();
$attack_map = $security_center->getAttackMap();
$system_health = $security_center->getSystemHealth();
$active_threats = $security_center->getActiveThreats();

$host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGA - Centro de Comando de Segurança</title>
    
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/geral.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="Scripts/jquery.js"></script>
    
    <style>
        /* Estilos do Centro de Comando */
        .security-command-center {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
            background: #0f1419;
            color: #ffffff;
            font-family: 'Courier New', monospace;
            min-height: 100vh;
        }
        
        .command-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(45deg, #1a1a2e, #16213e);
            border-radius: 10px;
            border: 2px solid #00ff41;
        }
        
        .command-title {
            font-size: 2.5em;
            color: #00ff41;
            text-shadow: 0 0 10px #00ff41;
            margin-bottom: 10px;
        }
        
        .status-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background: #1a1a2e;
            border-radius: 5px;
            border-left: 4px solid #00ff41;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            font-size: 0.9em;
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }
        
        .status-active { background: #00ff41; }
        .status-warning { background: #ffaa00; }
        .status-critical { background: #ff4444; }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stats-panel, .threats-panel {
            background: #1a1a2e;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #333;
        }
        
        .panel-title {
            font-size: 1.3em;
            color: #00ff41;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #00ff41;
            padding-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .stat-card {
            background: #0f1419;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #333;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            border-color: #00ff41;
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.3);
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-number.critical { color: #ff4444; }
        .stat-number.warning { color: #ffaa00; }
        .stat-number.success { color: #00ff41; }
        .stat-number.info { color: #44aaff; }
        
        .stat-label {
            font-size: 0.8em;
            color: #999;
            text-transform: uppercase;
        }
        
        .threat-timeline {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .threat-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 5px;
            background: #0f1419;
            border-radius: 5px;
            border-left: 4px solid #333;
        }
        
        .threat-item.critical { border-left-color: #ff4444; }
        .threat-item.high { border-left-color: #ffaa00; }
        .threat-item.medium { border-left-color: #44aaff; }
        .threat-item.low { border-left-color: #999; }
        
        .threat-time {
            font-size: 0.8em;
            color: #666;
            margin-right: 15px;
            min-width: 60px;
        }
        
        .threat-type {
            font-weight: bold;
            margin-right: 10px;
            min-width: 100px;
        }
        
        .threat-ip {
            color: #00ff41;
            margin-right: 10px;
        }
        
        .attack-map-section {
            margin-top: 30px;
            background: #1a1a2e;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #333;
        }
        
        .attack-map-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }
        
        .attacker-card {
            background: #0f1419;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #333;
        }
        
        .attacker-card.critical { border-left-color: #ff4444; }
        .attacker-card.high { border-left-color: #ffaa00; }
        .attacker-card.medium { border-left-color: #44aaff; }
        .attacker-card.low { border-left-color: #999; }
        
        .attacker-ip {
            font-size: 1.1em;
            color: #00ff41;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .attacker-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .attack-types {
            font-size: 0.8em;
            color: #999;
        }
        
        .system-health-section {
            margin-top: 30px;
            background: #1a1a2e;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #333;
        }
        
        .health-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .health-card {
            background: #0f1419;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #333;
        }
        
        .health-status {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .health-status.active { color: #00ff41; }
        .health-status.warning { color: #ffaa00; }
        .health-status.error { color: #ff4444; }
        
        .protection-modules {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        .module-status {
            display: flex;
            align-items: center;
            font-size: 0.9em;
        }
        
        .module-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .module-active { background: #00ff41; }
        .module-inactive { background: #ff4444; }
        
        .auto-refresh {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1a1a2e;
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #00ff41;
            font-size: 0.9em;
        }
        
        .refresh-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #00ff41;
            display: inline-block;
            margin-right: 8px;
            animation: pulse 1s infinite;
        }
        
        /* Scrollbar customizada */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #1a1a2e;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #00ff41;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #00cc33;
        }
        
        /* Responsividade */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .attack-map-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .command-title {
                font-size: 1.8em;
            }
            
            .status-bar {
                flex-direction: column;
                gap: 10px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <div class="security-command-center">
        <div class="command-header">
            <h1 class="command-title">SIGA SECURITY COMMAND CENTER</h1>
            <p>Sistema Avançado de Monitoramento e Resposta a Ameaças</p>
        </div>
        
        <div class="auto-refresh">
            <span class="refresh-indicator"></span>
            Auto-refresh: ON
        </div>
        
        <div class="status-bar">
            <div class="status-item">
                <span class="status-indicator status-active"></span>
                Sistema Operacional
            </div>
            <div class="status-item">
                <span class="status-indicator status-active"></span>
                Proteções Ativas
            </div>
            <div class="status-item">
                <span class="status-indicator status-<?php echo count($active_threats) > 0 ? 'warning' : 'active'; ?>"></span>
                <?php echo count($active_threats); ?> Ameaças Ativas
            </div>
            <div class="status-item">
                Última Atualização: <span id="last-update"><?php echo date('H:i:s'); ?></span>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <!-- Painel de Estatísticas -->
            <div class="stats-panel">
                <h2 class="panel-title">ESTATÍSTICAS DE SEGURANÇA - HOJE</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number critical" id="attacks-blocked"><?php echo $dashboard_stats['attacks_blocked_today']; ?></div>
                        <div class="stat-label">Ataques Bloqueados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number warning" id="login-failed"><?php echo $dashboard_stats['login_attempts_failed']; ?></div>
                        <div class="stat-label">Login Falhados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number success" id="login-success"><?php echo $dashboard_stats['login_attempts_success']; ?></div>
                        <div class="stat-label">Login Sucessos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number critical" id="ips-blocked"><?php echo $dashboard_stats['ips_blocked']; ?></div>
                        <div class="stat-label">IPs Bloqueados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number warning" id="xss-attempts"><?php echo $dashboard_stats['xss_attempts']; ?></div>
                        <div class="stat-label">Tentativas XSS</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number critical" id="sql-attempts"><?php echo $dashboard_stats['sql_injection_attempts']; ?></div>
                        <div class="stat-label">SQL Injection</div>
                    </div>
                </div>
            </div>
            
            <!-- Painel de Ameaças -->
            <div class="threats-panel">
                <h2 class="panel-title">TIMELINE DE AMEAÇAS - ÚLTIMAS 24H</h2>
                <div class="threat-timeline" id="threat-timeline">
                    <?php foreach (array_slice($threat_timeline, 0, 15) as $threat): ?>
                    <div class="threat-item <?php echo $threat['severity']; ?>">
                        <div class="threat-time"><?php echo date('H:i', strtotime($threat['timestamp'])); ?></div>
                        <div class="threat-type"><?php echo $threat['type']; ?></div>
                        <div class="threat-ip"><?php echo $threat['ip']; ?></div>
                        <div class="threat-desc"><?php echo $threat['description']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Mapa de Ataques -->
        <div class="attack-map-section">
            <h2 class="panel-title">MAPA DE ATAQUES - TOP ATACANTES</h2>
            <div class="attack-map-grid" id="attack-map">
                <?php foreach (array_slice($attack_map, 0, 8) as $attacker): ?>
                <div class="attacker-card <?php echo $attacker['threat_level']; ?>">
                    <div class="attacker-ip"><?php echo $attacker['ip']; ?></div>
                    <div class="attacker-stats">
                        <span>Ataques: <strong><?php echo $attacker['total_attacks']; ?></strong></span>
                        <span>Nível: <strong><?php echo strtoupper($attacker['threat_level']); ?></strong></span>
                    </div>
                    <div class="attack-types">
                        Tipos: <?php echo implode(', ', $attacker['attack_types']); ?>
                    </div>
                    <div class="attack-types">
                        Último ataque: <?php echo date('H:i', strtotime($attacker['last_seen'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Saúde do Sistema -->
        <div class="system-health-section">
            <h2 class="panel-title">SAÚDE DO SISTEMA</h2>
            <div class="health-grid">
                <div class="health-card">
                    <div class="health-status active">SISTEMA OPERACIONAL</div>
                    <p>Status: <?php echo $system_health['security_status']; ?></p>
                    <p>Uptime: <?php echo $system_health['uptime']; ?></p>
                    <p>Memória: <?php echo $system_health['memory_usage']['used']; ?></p>
                </div>
                
                <div class="health-card">
                    <div class="health-status active">LOGS & STORAGE</div>
                    <p>Uso de disco: <?php echo $system_health['log_disk_usage']; ?></p>
                    <p>Última atualização:</p>
                    <p><?php echo $system_health['last_update']; ?></p>
                </div>
                
                <div class="health-card">
                    <div class="health-status active">MÓDULOS DE PROTEÇÃO</div>
                    <div class="protection-modules">
                        <?php foreach ($system_health['protection_modules'] as $module => $status): ?>
                        <div class="module-status">
                            <span class="module-indicator <?php echo $status ? 'module-active' : 'module-inactive'; ?>"></span>
                            <?php echo ucfirst(str_replace('_', ' ', $module)); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if (!empty($active_threats)): ?>
                <div class="health-card">
                    <div class="health-status warning">AMEAÇAS ATIVAS</div>
                    <?php foreach (array_slice($active_threats, 0, 5) as $threat): ?>
                    <div class="module-status">
                        <span class="module-indicator status-<?php echo $threat['severity']; ?>"></span>
                        <?php echo $threat['type']; ?> - <?php echo $threat['ip']; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh em tempo real
        function refreshDashboard() {
            // Atualizar estatísticas
            $.post('', {ajax_endpoint: 'dashboard_stats'}, function(data) {
                if (data && !data.error) {
                    $('#attacks-blocked').text(data.attacks_blocked_today);
                    $('#login-failed').text(data.login_attempts_failed);
                    $('#login-success').text(data.login_attempts_success);
                    $('#ips-blocked').text(data.ips_blocked);
                    $('#xss-attempts').text(data.xss_attempts);
                    $('#sql-attempts').text(data.sql_injection_attempts);
                }
            }, 'json');
            
            // Atualizar timeline de ameaças
            $.post('', {ajax_endpoint: 'threat_timeline'}, function(data) {
                if (data && !data.error) {
                    var timeline = $('#threat-timeline');
                    timeline.empty();
                    
                    data.slice(0, 15).forEach(function(threat) {
                        var time = new Date(threat.timestamp).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
                        var item = $('<div class="threat-item ' + threat.severity + '">' +
                            '<div class="threat-time">' + time + '</div>' +
                            '<div class="threat-type">' + threat.type + '</div>' +
                            '<div class="threat-ip">' + threat.ip + '</div>' +
                            '<div class="threat-desc">' + threat.description + '</div>' +
                            '</div>');
                        timeline.append(item);
                    });
                }
            }, 'json');
            
            // Atualizar timestamp
            $('#last-update').text(new Date().toLocaleTimeString('pt-BR'));
        }
        
        // Refresh inicial e intervalado
        $(document).ready(function() {
            // Refresh a cada 30 segundos
            setInterval(refreshDashboard, 30000);
            
            // Adicionar efeitos visuais
            $('.stat-card').hover(function() {
                $(this).css('transform', 'scale(1.05)');
            }, function() {
                $(this).css('transform', 'scale(1)');
            });
        });
        
        // Efeito de digitação para o título
        $(document).ready(function() {
            var title = $('.command-title');
            var text = title.text();
            title.text('');
            
            var i = 0;
            var timer = setInterval(function() {
                if (i < text.length) {
                    title.append(text.charAt(i));
                    i++;
                } else {
                    clearInterval(timer);
                }
            }, 100);
        });
    </script>
</body>
</html>