<?php
/**
 * SIGA Security Alerts System
 * 
 * Sistema Inteligente de Alertas de Segurança
 * Notificações contextuais, escalation automático, throttling
 * 
 * @version 1.0
 * @author Claude Code - Security Mission
 */

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/security_functions.php';
require_once __DIR__ . '/InputValidationFramework.php';
require_once __DIR__ . '/src/role/MailSend.php';

/**
 * Classe principal do Sistema de Alertas de Segurança
 */
class SecurityAlertSystem {
    
    private $config = [
        'email_alerts' => true,
        'sms_alerts' => false, // Implementar futuramente
        'slack_alerts' => false, // Implementar futuramente
        'alert_throttling' => true,
        'escalation_enabled' => true,
        'max_alerts_per_hour' => 10,
        'critical_max_per_hour' => 3
    ];
    
    private $alert_levels = [
        'low' => 1,
        'medium' => 2, 
        'high' => 3,
        'critical' => 4
    ];
    
    private $admin_emails = [
        'admin@coface.com.br',
        'security@coface.com.br',
        'ti@coface.com.br'
    ];
    
    private $alert_templates = [];
    private $throttle_cache = [];
    
    public function __construct() {
        $this->loadAlertTemplates();
        $this->loadThrottleCache();
    }
    
    /**
     * Enviar alerta de segurança
     */
    public function sendSecurityAlert($type, $severity, $data, $ip = null) {
        if (!$this->shouldSendAlert($type, $severity)) {
            return false;
        }
        
        $alert = $this->buildAlert($type, $severity, $data, $ip);
        
        // Registrar tentativa de alerta
        $this->logAlertAttempt($alert);
        
        $sent = false;
        
        // Enviar email
        if ($this->config['email_alerts']) {
            $sent = $this->sendEmailAlert($alert) || $sent;
        }
        
        // Enviar SMS (se configurado)
        if ($this->config['sms_alerts'] && $severity === 'critical') {
            $sent = $this->sendSMSAlert($alert) || $sent;
        }
        
        // Enviar para Slack (se configurado)
        if ($this->config['slack_alerts']) {
            $sent = $this->sendSlackAlert($alert) || $sent;
        }
        
        // Atualizar throttle cache
        if ($sent) {
            $this->updateThrottleCache($type, $severity);
        }
        
        return $sent;
    }
    
    /**
     * Verificar se deve enviar alerta (throttling)
     */
    private function shouldSendAlert($type, $severity) {
        if (!$this->config['alert_throttling']) {
            return true;
        }
        
        $current_hour = date('Y-m-d-H');
        $cache_key = $type . '_' . $severity . '_' . $current_hour;
        
        // Verificar limites específicos por severidade
        if ($severity === 'critical') {
            $limit = $this->config['critical_max_per_hour'];
        } else {
            $limit = $this->config['max_alerts_per_hour'];
        }
        
        $current_count = $this->throttle_cache[$cache_key] ?? 0;
        
        // Sempre permitir o primeiro alerta crítico
        if ($severity === 'critical' && $current_count === 0) {
            return true;
        }
        
        return $current_count < $limit;
    }
    
    /**
     * Construir objeto de alerta
     */
    private function buildAlert($type, $severity, $data, $ip) {
        $template = $this->alert_templates[$type] ?? $this->alert_templates['default'];
        
        return [
            'id' => uniqid('alert_'),
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'severity' => $severity,
            'level' => $this->alert_levels[$severity],
            'ip' => $ip ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'data' => $data,
            'template' => $template,
            'server' => $_SERVER['HTTP_HOST'] ?? 'unknown',
            'session_id' => session_id(),
            'user_id' => $_SESSION['userID'] ?? 'anonymous'
        ];
    }
    
    /**
     * Enviar alerta por email
     */
    private function sendEmailAlert($alert) {
        try {
            $subject = $this->buildEmailSubject($alert);
            $body = $this->buildEmailBody($alert);
            
            // Determinar destinatários baseado na severidade
            $recipients = $this->getEmailRecipients($alert['severity']);
            
            foreach ($recipients as $email) {
                $mail_sent = $this->sendEmail($email, $subject, $body);
                if (!$mail_sent) {
                    error_log("Falha ao enviar alerta de segurança para: $email");
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Erro no sistema de alertas por email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Construir assunto do email
     */
    private function buildEmailSubject($alert) {
        $severity_prefix = [
            'low' => '[INFO]',
            'medium' => '[AVISO]', 
            'high' => '[ALERTA]',
            'critical' => '[CRÍTICO]'
        ];
        
        $prefix = $severity_prefix[$alert['severity']] ?? '[ALERTA]';
        
        return sprintf(
            '%s SIGA Security - %s detectado em %s',
            $prefix,
            $alert['template']['name'],
            $alert['server']
        );
    }
    
    /**
     * Construir corpo do email
     */
    private function buildEmailBody($alert) {
        $template = $alert['template'];
        
        $body = "ALERTA DE SEGURANÇA SIGA\n";
        $body .= "========================\n\n";
        
        $body .= "DETALHES DO INCIDENTE:\n";
        $body .= "Tipo: " . $template['name'] . "\n";
        $body .= "Severidade: " . strtoupper($alert['severity']) . "\n";
        $body .= "Timestamp: " . $alert['timestamp'] . "\n";
        $body .= "Servidor: " . $alert['server'] . "\n";
        $body .= "URL: " . $alert['url'] . "\n";
        $body .= "IP de Origem: " . $alert['ip'] . "\n";
        $body .= "User Agent: " . substr($alert['user_agent'], 0, 100) . "\n";
        $body .= "Usuário: " . ($alert['user_id'] !== 'anonymous' ? $alert['user_id'] : 'Não autenticado') . "\n\n";
        
        $body .= "DESCRIÇÃO:\n";
        $body .= $template['description'] . "\n\n";
        
        $body .= "DADOS DO INCIDENTE:\n";
        if (is_array($alert['data'])) {
            foreach ($alert['data'] as $key => $value) {
                $body .= ucfirst($key) . ": " . substr($value, 0, 200) . "\n";
            }
        } else {
            $body .= substr($alert['data'], 0, 500) . "\n";
        }
        
        $body .= "\nAÇÕES RECOMENDADAS:\n";
        $body .= $template['recommended_actions'] . "\n\n";
        
        if ($alert['severity'] === 'critical' || $alert['severity'] === 'high') {
            $body .= "⚠️  AÇÃO IMEDIATA NECESSÁRIA ⚠️\n";
            $body .= "Este incidente requer atenção imediata da equipe de segurança.\n\n";
        }
        
        $body .= "LINKS ÚTEIS:\n";
        $body .= "Centro de Comando: http://" . $alert['server'] . "/security_monitoring.php\n";
        $body .= "Análise Forense: http://" . $alert['server'] . "/security_forensics.php\n";
        $body .= "Logs de Segurança: http://" . $alert['server'] . "/security_dashboard.php\n\n";
        
        $body .= "---\n";
        $body .= "Sistema de Alertas SIGA - Gerado automaticamente\n";
        $body .= "ID do Alerta: " . $alert['id'] . "\n";
        
        return $body;
    }
    
    /**
     * Obter destinatários de email baseado na severidade
     */
    private function getEmailRecipients($severity) {
        switch ($severity) {
            case 'critical':
                return $this->admin_emails; // Todos os administradores
            case 'high':
                return array_slice($this->admin_emails, 0, 2); // Principais administradores
            case 'medium':
                return array_slice($this->admin_emails, 0, 1); // Administrador principal
            case 'low':
            default:
                return []; // Não enviar email para alertas baixos
        }
    }
    
    /**
     * Enviar email usando sistema existente
     */
    private function sendEmail($to, $subject, $body) {
        try {
            // Usar classe MailSend existente do SIGA
            $mail = new MailSend();
            
            $mail_data = [
                'to' => $to,
                'subject' => $subject,
                'body' => $body,
                'from' => 'security@siga.coface.com.br',
                'from_name' => 'SIGA Security System'
            ];
            
            return $mail->sendSecurityAlert($mail_data);
        } catch (Exception $e) {
            error_log("Erro ao enviar email de segurança: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Enviar alerta por SMS (implementação futura)
     */
    private function sendSMSAlert($alert) {
        // TODO: Implementar integração com provedor de SMS
        return false;
    }
    
    /**
     * Enviar alerta para Slack (implementação futura)
     */
    private function sendSlackAlert($alert) {
        // TODO: Implementar integração com Slack
        return false;
    }
    
    /**
     * Carregar templates de alerta
     */
    private function loadAlertTemplates() {
        $this->alert_templates = [
            'SQL_INJECTION' => [
                'name' => 'Tentativa de SQL Injection',
                'description' => 'Foi detectada uma tentativa de SQL Injection no sistema. O ataque foi bloqueado automaticamente.',
                'recommended_actions' => '1. Verificar logs detalhados\n2. Analisar padrão de ataque\n3. Considerar bloqueio de IP\n4. Revisar validações de entrada'
            ],
            'XSS_ATTEMPT' => [
                'name' => 'Tentativa de Cross-Site Scripting (XSS)',
                'description' => 'Foi detectada uma tentativa de XSS no sistema. Scripts maliciosos foram neutralizados.',
                'recommended_actions' => '1. Verificar dados de entrada\n2. Revisar sanitização de output\n3. Analisar contexto do ataque\n4. Verificar se há outros ataques do mesmo IP'
            ],
            'CSRF_VIOLATION' => [
                'name' => 'Violação de Proteção CSRF',
                'description' => 'Foi detectada uma tentativa de ataque CSRF. A requisição foi bloqueada por falta de token válido.',
                'recommended_actions' => '1. Verificar origem da requisição\n2. Analisar logs de sessão\n3. Verificar se é ataque coordenado\n4. Orientar usuários sobre segurança'
            ],
            'BRUTE_FORCE' => [
                'name' => 'Tentativa de Força Bruta',
                'description' => 'Foi detectada uma tentativa de ataque de força bruta contra o sistema de login.',
                'recommended_actions' => '1. Bloquear IP temporariamente\n2. Analisar padrão de tentativas\n3. Verificar se credenciais foram comprometidas\n4. Considerar bloqueio permanente'
            ],
            'FILE_UPLOAD_BLOCKED' => [
                'name' => 'Upload de Arquivo Malicioso Bloqueado',
                'description' => 'Foi bloqueado o upload de um arquivo potencialmente malicioso.',
                'recommended_actions' => '1. Analisar tipo de arquivo\n2. Verificar origem do upload\n3. Revisar políticas de upload\n4. Verificar outros uploads do mesmo usuário/IP'
            ],
            'VALIDATION_FAILURE' => [
                'name' => 'Falha na Validação de Entrada',
                'description' => 'Dados de entrada falharam na validação de segurança.',
                'recommended_actions' => '1. Analisar dados rejeitados\n2. Verificar se é tentativa de bypass\n3. Revisar regras de validação\n4. Monitorar comportamento do usuário'
            ],
            'SUSPICIOUS_ACTIVITY' => [
                'name' => 'Atividade Suspeita Detectada',
                'description' => 'Foi detectada atividade suspeita que pode indicar tentativa de comprometimento.',
                'recommended_actions' => '1. Investigar atividade detalhadamente\n2. Verificar logs de acesso\n3. Analisar comportamento do usuário\n4. Considerar suspensão preventiva'
            ],
            'SYSTEM_COMPROMISE' => [
                'name' => 'Possível Comprometimento do Sistema',
                'description' => 'Indicadores sugerem possível comprometimento do sistema. Ação imediata necessária.',
                'recommended_actions' => '1. ISOLAR SISTEMA IMEDIATAMENTE\n2. Preservar evidências\n3. Iniciar procedimento de resposta a incidentes\n4. Contatar equipe de segurança especializada'
            ],
            'default' => [
                'name' => 'Incidente de Segurança',
                'description' => 'Foi detectado um incidente de segurança que requer atenção.',
                'recommended_actions' => '1. Analisar detalhes do incidente\n2. Verificar logs relacionados\n3. Tomar ações corretivas apropriadas\n4. Monitorar desdobramentos'
            ]
        ];
    }
    
    /**
     * Carregar cache de throttling
     */
    private function loadThrottleCache() {
        $cache_file = __DIR__ . '/logs/alert_throttle.json';
        if (file_exists($cache_file)) {
            $cache_data = json_decode(file_get_contents($cache_file), true);
            $this->throttle_cache = $cache_data ?: [];
        }
        
        // Limpar entradas antigas (mais de 2 horas)
        $current_time = time();
        foreach ($this->throttle_cache as $key => $count) {
            $key_parts = explode('_', $key);
            if (count($key_parts) >= 4) {
                $key_time = strtotime(end($key_parts));
                if ($current_time - $key_time > 7200) { // 2 horas
                    unset($this->throttle_cache[$key]);
                }
            }
        }
    }
    
    /**
     * Atualizar cache de throttling
     */
    private function updateThrottleCache($type, $severity) {
        $current_hour = date('Y-m-d-H');
        $cache_key = $type . '_' . $severity . '_' . $current_hour;
        
        $this->throttle_cache[$cache_key] = ($this->throttle_cache[$cache_key] ?? 0) + 1;
        
        // Salvar cache
        $cache_file = __DIR__ . '/logs/alert_throttle.json';
        file_put_contents($cache_file, json_encode($this->throttle_cache), LOCK_EX);
    }
    
    /**
     * Registrar tentativa de alerta
     */
    private function logAlertAttempt($alert) {
        $log_entry = [
            'alert_id' => $alert['id'],
            'timestamp' => $alert['timestamp'],
            'type' => $alert['type'],
            'severity' => $alert['severity'],
            'ip' => $alert['ip'],
            'user_id' => $alert['user_id'],
            'sent' => true
        ];
        
        $log_dir = __DIR__ . '/logs/alerts';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_file = $log_dir . '/alerts_' . date('Y-m-d') . '.log';
        file_put_contents($log_file, json_encode($log_entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Obter estatísticas de alertas
     */
    public function getAlertStats($days = 7) {
        $stats = [
            'total_alerts' => 0,
            'by_severity' => ['low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0],
            'by_type' => [],
            'by_day' => []
        ];
        
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $stats['by_day'][$date] = 0;
            
            $log_file = __DIR__ . "/logs/alerts/alerts_{$date}.log";
            if (file_exists($log_file)) {
                $lines = file($log_file, FILE_IGNORE_NEW_LINES);
                foreach ($lines as $line) {
                    $data = json_decode($line, true);
                    if ($data) {
                        $stats['total_alerts']++;
                        $stats['by_day'][$date]++;
                        $stats['by_severity'][$data['severity']]++;
                        
                        if (!isset($stats['by_type'][$data['type']])) {
                            $stats['by_type'][$data['type']] = 0;
                        }
                        $stats['by_type'][$data['type']]++;
                    }
                }
            }
        }
        
        return $stats;
    }
    
    /**
     * Testar sistema de alertas
     */
    public function testAlertSystem() {
        $test_results = [];
        
        // Testar alerta de baixa severidade
        $test_results['low'] = $this->sendSecurityAlert(
            'VALIDATION_FAILURE',
            'low',
            ['test' => 'Teste de alerta de baixa severidade'],
            '127.0.0.1'
        );
        
        // Testar alerta de média severidade
        $test_results['medium'] = $this->sendSecurityAlert(
            'XSS_ATTEMPT',
            'medium',
            ['test' => 'Teste de alerta de média severidade'],
            '127.0.0.1'
        );
        
        // Testar alerta de alta severidade
        $test_results['high'] = $this->sendSecurityAlert(
            'SQL_INJECTION',
            'high',
            ['test' => 'Teste de alerta de alta severidade'],
            '127.0.0.1'
        );
        
        // Testar alerta crítico
        $test_results['critical'] = $this->sendSecurityAlert(
            'SYSTEM_COMPROMISE',
            'critical',
            ['test' => 'Teste de alerta crítico'],
            '127.0.0.1'
        );
        
        return $test_results;
    }
}

/**
 * Função global para envio de alertas
 */
function sendSecurityAlert($type, $severity, $data, $ip = null) {
    static $alert_system = null;
    
    if ($alert_system === null) {
        $alert_system = new SecurityAlertSystem();
    }
    
    return $alert_system->sendSecurityAlert($type, $severity, $data, $ip);
}

/**
 * Integração com sistema de logging existente
 */
function enhanceSecurityLogging($type, $data, $ip = null) {
    // Determinar severidade baseada no tipo
    $severity_map = [
        'SQL_INJECTION' => 'critical',
        'XSS_ATTEMPT' => 'high',
        'CSRF' => 'high',
        'BRUTE_FORCE' => 'medium',
        'FILE_UPLOAD_BLOCKED' => 'medium',
        'VALIDATION_FAILED' => 'low',
        'SUSPICIOUS_ACTIVITY' => 'high',
        'SYSTEM_COMPROMISE' => 'critical'
    ];
    
    $severity = $severity_map[$type] ?? 'medium';
    
    // Enviar alerta se severidade for média ou maior
    if (in_array($severity, ['medium', 'high', 'critical'])) {
        sendSecurityAlert($type, $severity, $data, $ip);
    }
    
    // Continuar com logging normal
    if (function_exists('log_security_incident')) {
        log_security_incident($type, $data, $ip);
    }
}

// Processar requisições AJAX para interface de alertas
if (isset($_POST['action']) && validate_user_session() && $_SESSION['pefil'] === 'F') {
    $alert_system = new SecurityAlertSystem();
    
    switch ($_POST['action']) {
        case 'get_stats':
            header('Content-Type: application/json');
            echo json_encode($alert_system->getAlertStats());
            exit;
            
        case 'test_alerts':
            header('Content-Type: application/json');
            echo json_encode($alert_system->testAlertSystem());
            exit;
            
        default:
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Ação não reconhecida']);
            exit;
    }
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGA - Sistema de Alertas de Segurança</title>
    
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/geral.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="Scripts/jquery.js"></script>
    
    <style>
        body {
            background: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        
        .alerts-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .alerts-header {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .alerts-title {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 10px;
        }
        
        .alerts-subtitle {
            color: #666;
            font-size: 1.1em;
        }
        
        .config-panel {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .panel-title {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        
        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .config-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        
        .config-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .config-item:last-child {
            border-bottom: none;
        }
        
        .config-label {
            font-weight: bold;
            color: #333;
        }
        
        .config-value {
            color: #666;
        }
        
        .status-enabled {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-disabled {
            color: #dc3545;
            font-weight: bold;
        }
        
        .stats-panel {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #007bff;
        }
        
        .stat-card.critical {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        
        .stat-card.high {
            border-left-color: #fd7e14;
            background: #fff8f0;
        }
        
        .stat-card.medium {
            border-left-color: #ffc107;
            background: #fffbf0;
        }
        
        .stat-card.low {
            border-left-color: #28a745;
            background: #f0fff4;
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-number.critical { color: #dc3545; }
        .stat-number.high { color: #fd7e14; }
        .stat-number.medium { color: #ffc107; }
        .stat-number.low { color: #28a745; }
        .stat-number.info { color: #007bff; }
        
        .stat-label {
            color: #666;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .chart-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .chart-title {
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        
        .test-panel {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .test-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .test-button {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .test-button.low {
            background: #28a745;
            color: white;
        }
        
        .test-button.medium {
            background: #ffc107;
            color: #333;
        }
        
        .test-button.high {
            background: #fd7e14;
            color: white;
        }
        
        .test-button.critical {
            background: #dc3545;
            color: white;
        }
        
        .test-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .test-results {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>

<body>
    <?php if (!validate_user_session() || $_SESSION['pefil'] !== 'F'): ?>
        <div class="error-message">
            <h2>Acesso Negado</h2>
            <p>Apenas administradores podem acessar o Sistema de Alertas de Segurança.</p>
        </div>
    <?php else: ?>
    
    <div class="alerts-container">
        <div class="alerts-header">
            <h1 class="alerts-title">Sistema de Alertas de Segurança</h1>
            <p class="alerts-subtitle">Configuração e monitoramento de alertas inteligentes</p>
        </div>
        
        <!-- Configurações Atuais -->
        <div class="config-panel">
            <h2 class="panel-title">Configurações do Sistema</h2>
            <div class="config-grid">
                <div class="config-section">
                    <h3>Canais de Notificação</h3>
                    <div class="config-item">
                        <span class="config-label">Alertas por Email:</span>
                        <span class="config-value status-enabled">ATIVO</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Alertas por SMS:</span>
                        <span class="config-value status-disabled">INATIVO</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Alertas Slack:</span>
                        <span class="config-value status-disabled">INATIVO</span>
                    </div>
                </div>
                
                <div class="config-section">
                    <h3>Controle de Throttling</h3>
                    <div class="config-item">
                        <span class="config-label">Throttling Ativo:</span>
                        <span class="config-value status-enabled">ATIVO</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Max. Alertas/Hora:</span>
                        <span class="config-value">10</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Max. Críticos/Hora:</span>
                        <span class="config-value">3</span>
                    </div>
                </div>
                
                <div class="config-section">
                    <h3>Escalation</h3>
                    <div class="config-item">
                        <span class="config-label">Escalation Automático:</span>
                        <span class="config-value status-enabled">ATIVO</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Destinatários Críticos:</span>
                        <span class="config-value"><?php echo count((new SecurityAlertSystem())->admin_emails ?? []); ?> emails</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estatísticas de Alertas -->
        <div class="stats-panel">
            <h2 class="panel-title">Estatísticas de Alertas (Últimos 7 dias)</h2>
            <div class="stats-grid" id="alert-stats">
                <div class="loading">Carregando estatísticas...</div>
            </div>
            
            <div class="chart-container">
                <div class="chart-title">Distribuição de Alertas por Tipo</div>
                <div id="type-chart">Carregando gráfico...</div>
            </div>
        </div>
        
        <!-- Painel de Testes -->
        <div class="test-panel">
            <h2 class="panel-title">Teste do Sistema de Alertas</h2>
            <p>Use os botões abaixo para testar o funcionamento dos alertas em diferentes níveis de severidade:</p>
            
            <div class="test-buttons">
                <button class="test-button low" onclick="testAlert('low')">Teste Baixa Severidade</button>
                <button class="test-button medium" onclick="testAlert('medium')">Teste Média Severidade</button>
                <button class="test-button high" onclick="testAlert('high')">Teste Alta Severidade</button>
                <button class="test-button critical" onclick="testAlert('critical')">Teste Severidade Crítica</button>
            </div>
            
            <div id="test-results" class="test-results" style="display: none;">
                <h4>Resultados dos Testes:</h4>
                <div id="test-output"></div>
            </div>
        </div>
    </div>
    
    <script>
        // Carregar estatísticas ao carregar a página
        $(document).ready(function() {
            loadAlertStats();
        });
        
        // Carregar estatísticas de alertas
        function loadAlertStats() {
            $.post('', {action: 'get_stats'}, function(data) {
                if (data && !data.error) {
                    displayStats(data);
                } else {
                    $('#alert-stats').html('<div class="error-message">Erro ao carregar estatísticas</div>');
                }
            }, 'json').fail(function() {
                $('#alert-stats').html('<div class="error-message">Erro de comunicação com o servidor</div>');
            });
        }
        
        // Exibir estatísticas
        function displayStats(stats) {
            var html = '';
            
            // Card de total de alertas
            html += '<div class="stat-card info">';
            html += '<div class="stat-number info">' + stats.total_alerts + '</div>';
            html += '<div class="stat-label">Total de Alertas</div>';
            html += '</div>';
            
            // Cards por severidade
            var severities = ['critical', 'high', 'medium', 'low'];
            var labels = {'critical': 'Críticos', 'high': 'Altos', 'medium': 'Médios', 'low': 'Baixos'};
            
            severities.forEach(function(severity) {
                html += '<div class="stat-card ' + severity + '">';
                html += '<div class="stat-number ' + severity + '">' + (stats.by_severity[severity] || 0) + '</div>';
                html += '<div class="stat-label">Alertas ' + labels[severity] + '</div>';
                html += '</div>';
            });
            
            $('#alert-stats').html(html);
            
            // Atualizar gráfico de tipos
            displayTypeChart(stats.by_type);
        }
        
        // Exibir gráfico de tipos
        function displayTypeChart(typeData) {
            var html = '<div style="max-height: 200px; overflow-y: auto;">';
            
            if (Object.keys(typeData).length === 0) {
                html += '<p style="text-align: center; color: #999;">Nenhum alerta registrado</p>';
            } else {
                for (var type in typeData) {
                    var count = typeData[type];
                    var percentage = Math.round((count / Object.values(typeData).reduce((a, b) => a + b, 0)) * 100);
                    
                    html += '<div style="margin: 10px 0; padding: 8px; background: white; border-radius: 4px;">';
                    html += '<div style="display: flex; justify-content: space-between; align-items: center;">';
                    html += '<span><strong>' + type.replace(/_/g, ' ') + '</strong></span>';
                    html += '<span>' + count + ' (' + percentage + '%)</span>';
                    html += '</div>';
                    html += '<div style="width: 100%; height: 6px; background: #eee; border-radius: 3px; margin-top: 5px;">';
                    html += '<div style="width: ' + percentage + '%; height: 100%; background: #007bff; border-radius: 3px;"></div>';
                    html += '</div>';
                    html += '</div>';
                }
            }
            
            html += '</div>';
            $('#type-chart').html(html);
        }
        
        // Testar alertas
        function testAlert(severity) {
            $('#test-results').show();
            $('#test-output').html('<div class="loading">Executando teste de alerta ' + severity + '...</div>');
            
            $.post('', {action: 'test_alerts'}, function(data) {
                if (data && !data.error) {
                    displayTestResults(data);
                } else {
                    $('#test-output').html('<div class="error-message">Erro ao executar testes</div>');
                }
            }, 'json').fail(function() {
                $('#test-output').html('<div class="error-message">Erro de comunicação com o servidor</div>');
            });
        }
        
        // Exibir resultados dos testes
        function displayTestResults(results) {
            var html = '';
            
            for (var level in results) {
                var success = results[level];
                var statusClass = success ? 'success-message' : 'error-message';
                var statusText = success ? 'SUCESSO' : 'FALHOU';
                var icon = success ? '✓' : '✗';
                
                html += '<div class="' + statusClass + '">';
                html += '<strong>' + icon + ' Teste ' + level.toUpperCase() + ':</strong> ' + statusText;
                html += '</div>';
            }
            
            $('#test-output').html(html);
            
            // Recarregar estatísticas após os testes
            setTimeout(loadAlertStats, 2000);
        }
        
        // Auto-refresh das estatísticas a cada 5 minutos
        setInterval(loadAlertStats, 300000);
    </script>
    
    <?php endif; ?>
</body>
</html>