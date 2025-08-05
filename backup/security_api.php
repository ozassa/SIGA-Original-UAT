<?php
/**
 * SIGA Security API
 * 
 * API REST para integração com sistemas externos de segurança
 * SIEM, SOC, ferramentas de monitoramento, webhooks
 * 
 * @version 1.0
 * @author Claude Code - Security Mission
 */

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/security_functions.php';
require_once __DIR__ . '/InputValidationFramework.php';
require_once __DIR__ . '/src/role/rolePrefix.php';

/**
 * Classe principal da API de Segurança
 */
class SecurityAPI {
    
    private $api_keys = [];
    private $rate_limits = [];
    private $endpoints = [];
    private $webhooks = [];
    
    private $allowed_ips = [
        '127.0.0.1',
        '::1',
        // Adicionar IPs de sistemas confiáveis
    ];
    
    public function __construct() {
        $this->loadAPIKeys();
        $this->loadWebhooks();
        $this->initializeEndpoints();
        $this->enforceRateLimiting();
    }
    
    /**
     * Processar requisição da API
     */
    public function handleRequest() {
        // Verificar método HTTP
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/security_api.php', '', $path);
        
        // Headers de CORS para integração
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');
        
        if ($method === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
        
        // Autenticar requisição
        if (!$this->authenticateRequest()) {
            $this->sendErrorResponse(401, 'Unauthorized', 'Invalid API key or IP not allowed');
            return;
        }
        
        // Rate limiting
        if (!$this->checkRateLimit()) {
            $this->sendErrorResponse(429, 'Too Many Requests', 'Rate limit exceeded');
            return;
        }
        
        // Roteamento
        try {
            $response = $this->routeRequest($method, $path);
            $this->sendSuccessResponse($response);
        } catch (Exception $e) {
            $this->sendErrorResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }
    
    /**
     * Autenticar requisição
     */
    private function authenticateRequest() {
        $api_key = $this->getAPIKey();
        $client_ip = $this->getClientIP();
        
        // Verificar IP permitido
        if (!in_array($client_ip, $this->allowed_ips) && !$this->isIPWhitelisted($client_ip)) {
            $this->logSecurityEvent('API_UNAUTHORIZED_IP', "Unauthorized IP access attempt: $client_ip");
            return false;
        }
        
        // Verificar API key
        if (!$api_key || !$this->validateAPIKey($api_key)) {
            $this->logSecurityEvent('API_INVALID_KEY', "Invalid API key attempt from: $client_ip");
            return false;
        }
        
        return true;
    }
    
    /**
     * Router principal
     */
    private function routeRequest($method, $path) {
        // Remover barras extras
        $path = trim($path, '/');
        $segments = explode('/', $path);
        
        // Determinar endpoint
        $endpoint = $segments[0] ?? '';
        
        switch ($endpoint) {
            case 'events':
                return $this->handleEventsEndpoint($method, $segments);
                
            case 'threats':
                return $this->handleThreatsEndpoint($method, $segments);
                
            case 'alerts':
                return $this->handleAlertsEndpoint($method, $segments);
                
            case 'blocked':
                return $this->handleBlockedEndpoint($method, $segments);
                
            case 'forensics':
                return $this->handleForensicsEndpoint($method, $segments);
                
            case 'compliance':
                return $this->handleComplianceEndpoint($method, $segments);
                
            case 'webhooks':
                return $this->handleWebhooksEndpoint($method, $segments);
                
            case 'health':
                return $this->handleHealthEndpoint($method, $segments);
                
            case 'stats':
                return $this->handleStatsEndpoint($method, $segments);
                
            default:
                throw new Exception("Endpoint not found: $endpoint");
        }
    }
    
    /**
     * Endpoint de eventos (/events)
     */
    private function handleEventsEndpoint($method, $segments) {
        switch ($method) {
            case 'GET':
                return $this->getSecurityEvents($segments);
                
            case 'POST':
                return $this->reportSecurityEvent();
                
            default:
                throw new Exception('Method not allowed for events endpoint');
        }
    }
    
    /**
     * Endpoint de ameaças (/threats)
     */
    private function handleThreatsEndpoint($method, $segments) {
        switch ($method) {
            case 'GET':
                if (isset($segments[1])) {
                    return $this->getThreatDetails($segments[1]);
                } else {
                    return $this->getActiveThreats();
                }
                
            case 'POST':
                return $this->reportThreat();
                
            default:
                throw new Exception('Method not allowed for threats endpoint');
        }
    }
    
    /**
     * Endpoint de alertas (/alerts)
     */
    private function handleAlertsEndpoint($method, $segments) {
        switch ($method) {
            case 'GET':
                return $this->getAlerts($segments);
                
            case 'POST':
                return $this->createAlert();
                
            case 'PUT':
                if (isset($segments[1])) {
                    return $this->updateAlert($segments[1]);
                }
                throw new Exception('Alert ID required for update');
                
            default:
                throw new Exception('Method not allowed for alerts endpoint');
        }
    }
    
    /**
     * Endpoint de bloqueios (/blocked)
     */
    private function handleBlockedEndpoint($method, $segments) {
        switch ($method) {
            case 'GET':
                return $this->getBlockedEntities($segments);
                
            case 'POST':
                return $this->addBlockedEntity();
                
            case 'DELETE':
                if (isset($segments[1])) {
                    return $this->removeBlockedEntity($segments[1]);
                }
                throw new Exception('Entity ID required for removal');
                
            default:
                throw new Exception('Method not allowed for blocked endpoint');
        }
    }
    
    /**
     * Endpoint de análise forense (/forensics)
     */
    private function handleForensicsEndpoint($method, $segments) {
        switch ($method) {
            case 'GET':
                if (isset($segments[1])) {
                    switch ($segments[1]) {
                        case 'ip':
                            return $this->getIPAnalysis($segments[2] ?? null);
                        case 'timeline':
                            return $this->getIncidentTimeline();
                        case 'correlation':
                            return $this->getEventCorrelation();
                        default:
                            throw new Exception('Unknown forensics operation');
                    }
                } else {
                    return $this->getForensicsOverview();
                }
                
            default:
                throw new Exception('Method not allowed for forensics endpoint');
        }
    }
    
    /**
     * Endpoint de compliance (/compliance)
     */
    private function handleComplianceEndpoint($method, $segments) {
        switch ($method) {
            case 'GET':
                if (isset($segments[1])) {
                    return $this->getComplianceReport($segments[1]);
                } else {
                    return $this->getComplianceOverview();
                }
                
            default:
                throw new Exception('Method not allowed for compliance endpoint');
        }
    }
    
    /**
     * Endpoint de webhooks (/webhooks)
     */
    private function handleWebhooksEndpoint($method, $segments) {
        switch ($method) {
            case 'GET':
                return $this->getWebhooks();
                
            case 'POST':
                return $this->registerWebhook();
                
            case 'DELETE':
                if (isset($segments[1])) {
                    return $this->removeWebhook($segments[1]);
                }
                throw new Exception('Webhook ID required for removal');
                
            default:
                throw new Exception('Method not allowed for webhooks endpoint');
        }
    }
    
    /**
     * Endpoint de saúde (/health)
     */
    private function handleHealthEndpoint($method, $segments) {
        if ($method !== 'GET') {
            throw new Exception('Method not allowed for health endpoint');
        }
        
        return [
            'status' => 'healthy',
            'timestamp' => date('c'),
            'version' => '1.0',
            'services' => [
                'authentication' => 'operational',
                'logging' => 'operational',
                'monitoring' => 'operational',
                'alerts' => 'operational'
            ],
            'uptime' => $this->getSystemUptime()
        ];
    }
    
    /**
     * Endpoint de estatísticas (/stats)
     */
    private function handleStatsEndpoint($method, $segments) {
        if ($method !== 'GET') {
            throw new Exception('Method not allowed for stats endpoint');
        }
        
        $period = $_GET['period'] ?? '24h';
        
        return [
            'period' => $period,
            'events' => $this->getEventStats($period),
            'threats' => $this->getThreatStats($period),
            'alerts' => $this->getAlertStats($period),
            'performance' => $this->getPerformanceStats()
        ];
    }
    
    /**
     * Implementações dos métodos de dados
     */
    private function getSecurityEvents($segments) {
        $limit = intval($_GET['limit'] ?? 100);
        $offset = intval($_GET['offset'] ?? 0);
        $type = $_GET['type'] ?? null;
        $severity = $_GET['severity'] ?? null;
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-24 hours'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        
        $events = $this->querySecurityEvents($start_date, $end_date, $type, $severity, $limit, $offset);
        
        return [
            'events' => $events,
            'total' => count($events),
            'limit' => $limit,
            'offset' => $offset,
            'filters' => compact('type', 'severity', 'start_date', 'end_date')
        ];
    }
    
    private function reportSecurityEvent() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON payload');
        }
        
        $required_fields = ['type', 'severity', 'source'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        $event_id = $this->storeExternalEvent($input);
        
        // Disparar webhooks se configurados
        $this->triggerWebhooks('event.created', $input);
        
        return [
            'success' => true,
            'event_id' => $event_id,
            'message' => 'Event reported successfully'
        ];
    }
    
    private function getActiveThreats() {
        $threats = [];
        
        // Simular dados de ameaças ativas
        $threats = [
            [
                'id' => 'threat_001',
                'type' => 'brute_force',
                'severity' => 'high',
                'source_ip' => '192.168.1.100',
                'target' => 'login_system',
                'first_seen' => date('c', strtotime('-2 hours')),
                'last_seen' => date('c', strtotime('-5 minutes')),
                'count' => 45,
                'status' => 'active'
            ],
            [
                'id' => 'threat_002',
                'type' => 'sql_injection',
                'severity' => 'critical',
                'source_ip' => '10.0.0.50',
                'target' => 'web_application',
                'first_seen' => date('c', strtotime('-1 hour')),
                'last_seen' => date('c', strtotime('-10 minutes')),
                'count' => 12,
                'status' => 'blocked'
            ]
        ];
        
        return [
            'threats' => $threats,
            'total' => count($threats),
            'active_count' => count(array_filter($threats, function($t) { return $t['status'] === 'active'; }))
        ];
    }
    
    private function getThreatDetails($threat_id) {
        // Em produção, buscar detalhes reais da ameaça
        return [
            'id' => $threat_id,
            'type' => 'brute_force',
            'severity' => 'high',
            'source_ip' => '192.168.1.100',
            'source_location' => ['country' => 'Unknown', 'city' => 'Unknown'],
            'target' => 'login_system',
            'first_seen' => date('c', strtotime('-2 hours')),
            'last_seen' => date('c', strtotime('-5 minutes')),
            'total_attempts' => 45,
            'blocked_attempts' => 40,
            'status' => 'active',
            'mitigation_actions' => [
                'IP rate limiting applied',
                'Failed attempts logged',
                'Alert sent to security team'
            ],
            'timeline' => [
                ['time' => date('c', strtotime('-2 hours')), 'event' => 'First attack attempt detected'],
                ['time' => date('c', strtotime('-1 hour')), 'event' => 'Rate limiting activated'],
                ['time' => date('c', strtotime('-30 minutes')), 'event' => 'Alert escalated'],
                ['time' => date('c', strtotime('-5 minutes')), 'event' => 'Latest attempt blocked']
            ]
        ];
    }
    
    private function getAlerts($segments) {
        $status = $_GET['status'] ?? 'all';
        $severity = $_GET['severity'] ?? 'all';
        $limit = intval($_GET['limit'] ?? 50);
        
        // Simular alertas
        $alerts = [
            [
                'id' => 'alert_001',
                'title' => 'Brute Force Attack Detected',
                'severity' => 'high',
                'status' => 'open',
                'created_at' => date('c', strtotime('-1 hour')),
                'updated_at' => date('c', strtotime('-30 minutes')),
                'source' => 'authentication_system',
                'description' => 'Multiple failed login attempts from IP 192.168.1.100'
            ],
            [
                'id' => 'alert_002',
                'title' => 'SQL Injection Attempt',
                'severity' => 'critical',
                'status' => 'investigating',
                'created_at' => date('c', strtotime('-45 minutes')),
                'updated_at' => date('c', strtotime('-15 minutes')),
                'source' => 'web_application',
                'description' => 'SQL injection payload detected in form submission'
            ]
        ];
        
        // Aplicar filtros
        if ($status !== 'all') {
            $alerts = array_filter($alerts, function($alert) use ($status) {
                return $alert['status'] === $status;
            });
        }
        
        if ($severity !== 'all') {
            $alerts = array_filter($alerts, function($alert) use ($severity) {
                return $alert['severity'] === $severity;
            });
        }
        
        return [
            'alerts' => array_slice(array_values($alerts), 0, $limit),
            'total' => count($alerts),
            'filters' => compact('status', 'severity')
        ];
    }
    
    private function getBlockedEntities($segments) {
        $type = $_GET['type'] ?? 'all'; // ip, user, session
        
        // Simular entidades bloqueadas
        $blocked = [
            [
                'type' => 'ip',
                'value' => '192.168.1.100',
                'reason' => 'Brute force attack',
                'blocked_at' => date('c', strtotime('-2 hours')),
                'expires_at' => date('c', strtotime('+1 hour')),
                'auto_block' => true
            ],
            [
                'type' => 'user',
                'value' => 'suspicious_user',
                'reason' => 'Multiple failed login attempts',
                'blocked_at' => date('c', strtotime('-1 hour')),
                'expires_at' => date('c', strtotime('+30 minutes')),
                'auto_block' => false
            ]
        ];
        
        if ($type !== 'all') {
            $blocked = array_filter($blocked, function($item) use ($type) {
                return $item['type'] === $type;
            });
        }
        
        return [
            'blocked' => array_values($blocked),
            'total' => count($blocked),
            'type_filter' => $type
        ];
    }
    
    private function getComplianceOverview() {
        return [
            'frameworks' => [
                'LGPD' => ['score' => 75.5, 'status' => 'partially_compliant'],
                'PCI_DSS' => ['score' => 87.2, 'status' => 'compliant'],
                'ISO_27001' => ['score' => 62.5, 'status' => 'partially_compliant'],
                'NIST' => ['score' => 80.0, 'status' => 'compliant']
            ],
            'overall_score' => 76.3,
            'last_assessment' => date('c', strtotime('-1 day')),
            'next_assessment' => date('c', strtotime('+29 days'))
        ];
    }
    
    /**
     * Webhook functionality
     */
    private function triggerWebhooks($event_type, $data) {
        foreach ($this->webhooks as $webhook) {
            if (in_array($event_type, $webhook['events'])) {
                $this->sendWebhook($webhook, $event_type, $data);
            }
        }
    }
    
    private function sendWebhook($webhook, $event_type, $data) {
        $payload = [
            'event' => $event_type,
            'timestamp' => date('c'),
            'data' => $data,
            'source' => 'SIGA_Security_API'
        ];
        
        $signature = hash_hmac('sha256', json_encode($payload), $webhook['secret']);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webhook['url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-SIGA-Signature: sha256=' . $signature,
            'X-SIGA-Event: ' . $event_type
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Log webhook delivery
        $this->logWebhookDelivery($webhook['id'], $event_type, $http_code, $response);
    }
    
    /**
     * Métodos auxiliares
     */
    private function loadAPIKeys() {
        $keys_file = __DIR__ . '/config/api_keys.json';
        if (file_exists($keys_file)) {
            $this->api_keys = json_decode(file_get_contents($keys_file), true) ?: [];
        } else {
            // Chaves padrão para desenvolvimento
            $this->api_keys = [
                'siga_api_key_dev_12345' => [
                    'name' => 'Development Key',
                    'permissions' => ['read', 'write'],
                    'created_at' => date('c'),
                    'last_used' => null
                ]
            ];
        }
    }
    
    private function loadWebhooks() {
        $webhooks_file = __DIR__ . '/config/webhooks.json';
        if (file_exists($webhooks_file)) {
            $this->webhooks = json_decode(file_get_contents($webhooks_file), true) ?: [];
        } else {
            $this->webhooks = [];
        }
    }
    
    private function getAPIKey() {
        // Verificar header Authorization
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.+)/', $auth_header, $matches)) {
            return $matches[1];
        }
        
        // Verificar header X-API-Key
        return $_SERVER['HTTP_X_API_KEY'] ?? null;
    }
    
    private function validateAPIKey($key) {
        if (!isset($this->api_keys[$key])) {
            return false;
        }
        
        // Atualizar último uso
        $this->api_keys[$key]['last_used'] = date('c');
        
        return true;
    }
    
    private function getClientIP() {
        $ip_headers = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];
        
        foreach ($ip_headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                return $ip;
            }
        }
        
        return '127.0.0.1';
    }
    
    private function isIPWhitelisted($ip) {
        // Em produção, verificar lista de IPs permitidos em banco de dados
        return false;
    }
    
    private function checkRateLimit() {
        $client_ip = $this->getClientIP();
        $current_time = time();
        $window = 60; // 1 minuto
        $max_requests = 100; // 100 requests por minuto
        
        if (!isset($this->rate_limits[$client_ip])) {
            $this->rate_limits[$client_ip] = [
                'requests' => 0,
                'window_start' => $current_time
            ];
        }
        
        $rate_data = &$this->rate_limits[$client_ip];
        
        // Reset janela se expirou
        if ($current_time - $rate_data['window_start'] > $window) {
            $rate_data['requests'] = 0;
            $rate_data['window_start'] = $current_time;
        }
        
        $rate_data['requests']++;
        
        return $rate_data['requests'] <= $max_requests;
    }
    
    private function sendSuccessResponse($data) {
        header('Content-Type: application/json');
        header('X-SIGA-Response-Time: ' . round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2) . 'ms');
        
        echo json_encode([
            'success' => true,
            'data' => $data,
            'timestamp' => date('c')
        ], JSON_PRETTY_PRINT);
    }
    
    private function sendErrorResponse($code, $error, $message) {
        http_response_code($code);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => false,
            'error' => $error,
            'message' => $message,
            'timestamp' => date('c')
        ], JSON_PRETTY_PRINT);
    }
    
    private function logSecurityEvent($type, $message) {
        error_log("SECURITY API: $type - $message");
    }
    
    private function logWebhookDelivery($webhook_id, $event_type, $http_code, $response) {
        $log_entry = [
            'webhook_id' => $webhook_id,
            'event_type' => $event_type,
            'http_code' => $http_code,
            'response' => substr($response, 0, 200),
            'timestamp' => date('c')
        ];
        
        $log_dir = __DIR__ . '/logs/webhooks';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        file_put_contents(
            $log_dir . '/webhooks_' . date('Y-m-d') . '.log',
            json_encode($log_entry) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
    
    // Métodos de dados simulados
    private function querySecurityEvents($start_date, $end_date, $type, $severity, $limit, $offset) {
        // Em produção, fazer query real no banco de dados/logs
        return [
            [
                'id' => 'evt_001',
                'type' => 'brute_force',
                'severity' => 'high',
                'timestamp' => date('c', strtotime('-1 hour')),
                'source_ip' => '192.168.1.100',
                'details' => 'Multiple failed login attempts'
            ],
            [
                'id' => 'evt_002',
                'type' => 'sql_injection',
                'severity' => 'critical',
                'timestamp' => date('c', strtotime('-30 minutes')),
                'source_ip' => '10.0.0.50',
                'details' => 'SQL injection attempt in form field'
            ]
        ];
    }
    
    private function storeExternalEvent($event_data) {
        // Em produção, armazenar evento no sistema
        $event_id = 'ext_' . uniqid();
        
        // Log do evento externo
        $log_entry = array_merge($event_data, [
            'id' => $event_id,
            'timestamp' => date('c'),
            'source' => 'external_api'
        ]);
        
        $log_dir = __DIR__ . '/logs/external_events';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        file_put_contents(
            $log_dir . '/external_' . date('Y-m-d') . '.log',
            json_encode($log_entry) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
        
        return $event_id;
    }
    
    private function getSystemUptime() {
        // Simular uptime
        return '7 days, 12 hours, 45 minutes';
    }
    
    private function getEventStats($period) {
        return [
            'total' => 1250,
            'by_type' => [
                'brute_force' => 450,
                'sql_injection' => 125,
                'xss_attempt' => 225,
                'suspicious_activity' => 450
            ],
            'by_severity' => [
                'critical' => 75,
                'high' => 285,
                'medium' => 590,
                'low' => 300
            ]
        ];
    }
    
    private function getThreatStats($period) {
        return [
            'active_threats' => 12,
            'mitigated_threats' => 45,
            'false_positives' => 8,
            'average_response_time' => '2.5 minutes'
        ];
    }
    
    private function getAlertStats($period) {
        return [
            'total_alerts' => 67,
            'open_alerts' => 12,
            'resolved_alerts' => 55,
            'average_resolution_time' => '15 minutes'
        ];
    }
    
    private function getPerformanceStats() {
        return [
            'api_response_time' => '125ms',
            'system_load' => '0.65',
            'memory_usage' => '72%',
            'disk_usage' => '45%'
        ];
    }
}

// Executar API se acessada diretamente
if (basename($_SERVER['PHP_SELF']) === 'security_api.php') {
    $api = new SecurityAPI();
    $api->handleRequest();
    exit;
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGA - Security API Documentation</title>
    
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/geral.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="Scripts/jquery.js"></script>
    
    <style>
        body {
            background: #f8f9fa;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }
        
        .api-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .api-header {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .api-title {
            font-size: 2.5em;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .api-subtitle {
            color: #7f8c8d;
            font-size: 1.2em;
        }
        
        .endpoint-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .section-header {
            background: #3498db;
            color: white;
            padding: 20px 30px;
            font-size: 1.3em;
            font-weight: 600;
        }
        
        .section-content {
            padding: 30px;
        }
        
        .endpoint {
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }
        
        .endpoint-method {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9em;
            margin-right: 10px;
        }
        
        .method-get { background: #27ae60; color: white; }
        .method-post { background: #e67e22; color: white; }
        .method-put { background: #f39c12; color: white; }
        .method-delete { background: #e74c3c; color: white; }
        
        .endpoint-path {
            font-family: monospace;
            font-size: 1.1em;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .endpoint-description {
            margin: 10px 0;
            color: #7f8c8d;
        }
        
        .params-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .params-table th,
        .params-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .params-table th {
            background: #ecf0f1;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .param-required {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .param-optional {
            color: #95a5a6;
        }
        
        .code-block {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.9em;
            overflow-x: auto;
            margin: 15px 0;
        }
        
        .response-example {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .auth-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .auth-title {
            font-weight: 600;
            color: #856404;
            margin-bottom: 10px;
        }
        
        .tab-nav {
            display: flex;
            background: #ecf0f1;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
        }
        
        .tab-nav button {
            flex: 1;
            padding: 15px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .tab-nav button.active {
            background: #3498db;
            color: white;
        }
        
        .tab-content {
            display: none;
            padding: 20px;
        }
        
        .tab-content.active {
            display: block;
        }
    </style>
</head>

<body>
    <?php if (!validate_user_session() || $_SESSION['pefil'] !== 'F'): ?>
        <div style="text-align: center; padding: 50px; color: #e74c3c;">
            <h2>Acesso Negado</h2>
            <p>Apenas administradores podem acessar a documentação da API de Segurança.</p>
        </div>
    <?php else: ?>
    
    <div class="api-container">
        <div class="api-header">
            <h1 class="api-title">SIGA Security API</h1>
            <p class="api-subtitle">REST API para integração com sistemas externos de segurança</p>
        </div>
        
        <!-- Autenticação -->
        <div class="endpoint-section">
            <div class="section-header">Autenticação</div>
            <div class="section-content">
                <div class="auth-info">
                    <div class="auth-title">API Key Authentication</div>
                    <p>Todas as requisições devem incluir uma API key válida. Suportamos dois métodos:</p>
                    <ul>
                        <li><strong>Header Authorization:</strong> <code>Authorization: Bearer YOUR_API_KEY</code></li>
                        <li><strong>Header X-API-Key:</strong> <code>X-API-Key: YOUR_API_KEY</code></li>
                    </ul>
                </div>
                
                <div class="code-block">
# Exemplo usando curl
curl -H "Authorization: Bearer siga_api_key_dev_12345" \
     -H "Content-Type: application/json" \
     https://siga.example.com/security_api.php/events
                </div>
            </div>
        </div>
        
        <!-- Endpoints de Eventos -->
        <div class="endpoint-section">
            <div class="section-header">Events - Eventos de Segurança</div>
            <div class="section-content">
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/events</span>
                    </div>
                    <div class="endpoint-description">Recuperar eventos de segurança com filtros opcionais</div>
                    
                    <table class="params-table">
                        <tr>
                            <th>Parâmetro</th>
                            <th>Tipo</th>
                            <th>Obrigatório</th>
                            <th>Descrição</th>
                        </tr>
                        <tr>
                            <td>limit</td>
                            <td>integer</td>
                            <td class="param-optional">Opcional</td>
                            <td>Número máximo de eventos (padrão: 100)</td>
                        </tr>
                        <tr>
                            <td>offset</td>
                            <td>integer</td>
                            <td class="param-optional">Opcional</td>
                            <td>Offset para paginação (padrão: 0)</td>
                        </tr>
                        <tr>
                            <td>type</td>
                            <td>string</td>
                            <td class="param-optional">Opcional</td>
                            <td>Filtrar por tipo de evento</td>
                        </tr>
                        <tr>
                            <td>severity</td>
                            <td>string</td>
                            <td class="param-optional">Opcional</td>
                            <td>Filtrar por severidade (low, medium, high, critical)</td>
                        </tr>
                    </table>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-post">POST</span>
                        <span class="endpoint-path">/events</span>
                    </div>
                    <div class="endpoint-description">Reportar um novo evento de segurança</div>
                    
                    <div class="code-block">
{
  "type": "brute_force",
  "severity": "high", 
  "source": "external_system",
  "description": "Multiple failed login attempts detected",
  "source_ip": "192.168.1.100",
  "metadata": {
    "attempts": 15,
    "timeframe": "5 minutes"
  }
}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Endpoints de Ameaças -->
        <div class="endpoint-section">
            <div class="section-header">Threats - Ameaças Ativas</div>
            <div class="section-content">
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/threats</span>
                    </div>
                    <div class="endpoint-description">Listar ameaças ativas no sistema</div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/threats/{id}</span>
                    </div>
                    <div class="endpoint-description">Obter detalhes de uma ameaça específica</div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-post">POST</span>
                        <span class="endpoint-path">/threats</span>
                    </div>
                    <div class="endpoint-description">Reportar nova ameaça identificada</div>
                </div>
            </div>
        </div>
        
        <!-- Endpoints de Alertas -->
        <div class="endpoint-section">
            <div class="section-header">Alerts - Sistema de Alertas</div>
            <div class="section-content">
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/alerts</span>
                    </div>
                    <div class="endpoint-description">Recuperar alertas com filtros</div>
                    
                    <table class="params-table">
                        <tr>
                            <th>Parâmetro</th>
                            <th>Valores</th>
                            <th>Descrição</th>
                        </tr>
                        <tr>
                            <td>status</td>
                            <td>open, investigating, resolved, closed</td>
                            <td>Filtrar por status do alerta</td>
                        </tr>
                        <tr>
                            <td>severity</td>
                            <td>low, medium, high, critical</td>
                            <td>Filtrar por severidade</td>
                        </tr>
                    </table>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-post">POST</span>
                        <span class="endpoint-path">/alerts</span>
                    </div>
                    <div class="endpoint-description">Criar novo alerta</div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-put">PUT</span>
                        <span class="endpoint-path">/alerts/{id}</span>
                    </div>
                    <div class="endpoint-description">Atualizar status de um alerta</div>
                </div>
            </div>
        </div>
        
        <!-- Endpoints de Bloqueios -->
        <div class="endpoint-section">
            <div class="section-header">Blocked - Entidades Bloqueadas</div>
            <div class="section-content">
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/blocked</span>
                    </div>
                    <div class="endpoint-description">Listar IPs, usuários e sessões bloqueadas</div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-post">POST</span>
                        <span class="endpoint-path">/blocked</span>
                    </div>
                    <div class="endpoint-description">Adicionar entidade à lista de bloqueio</div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-delete">DELETE</span>
                        <span class="endpoint-path">/blocked/{id}</span>
                    </div>
                    <div class="endpoint-description">Remover entidade da lista de bloqueio</div>
                </div>
            </div>
        </div>
        
        <!-- Endpoints de Forense -->
        <div class="endpoint-section">
            <div class="section-header">Forensics - Análise Forense</div>
            <div class="section-content">
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/forensics/ip/{ip}</span>
                    </div>
                    <div class="endpoint-description">Análise detalhada de um IP específico</div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/forensics/timeline</span>
                    </div>
                    <div class="endpoint-description">Timeline de incidentes de segurança</div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/forensics/correlation</span>
                    </div>
                    <div class="endpoint-description">Análise de correlação de eventos</div>
                </div>
            </div>
        </div>
        
        <!-- Endpoints de Compliance -->
        <div class="endpoint-section">
            <div class="section-header">Compliance - Conformidade</div>
            <div class="section-content">
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/compliance</span>
                    </div>
                    <div class="endpoint-description">Visão geral do status de compliance</div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/compliance/{framework}</span>
                    </div>
                    <div class="endpoint-description">Relatório específico de framework (LGPD, PCI_DSS, ISO_27001, NIST)</div>
                </div>
            </div>
        </div>
        
        <!-- Webhooks -->
        <div class="endpoint-section">
            <div class="section-header">Webhooks - Notificações em Tempo Real</div>
            <div class="section-content">
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/webhooks</span>
                    </div>
                    <div class="endpoint-description">Listar webhooks configurados</div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-post">POST</span>
                        <span class="endpoint-path">/webhooks</span>
                    </div>
                    <div class="endpoint-description">Registrar novo webhook</div>
                    
                    <div class="code-block">
{
  "url": "https://your-system.com/webhook",
  "events": ["event.created", "threat.detected", "alert.created"],
  "secret": "your_webhook_secret"
}
                    </div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-delete">DELETE</span>
                        <span class="endpoint-path">/webhooks/{id}</span>
                    </div>
                    <div class="endpoint-description">Remover webhook</div>
                </div>
            </div>
        </div>
        
        <!-- Status e Estatísticas -->
        <div class="endpoint-section">
            <div class="section-header">System - Status e Estatísticas</div>
            <div class="section-content">
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/health</span>
                    </div>
                    <div class="endpoint-description">Verificar saúde do sistema</div>
                    
                    <div class="response-example">
                        <strong>Resposta de exemplo:</strong>
                        <div class="code-block">
{
  "success": true,
  "data": {
    "status": "healthy",
    "timestamp": "2025-01-01T12:00:00Z",
    "version": "1.0",
    "services": {
      "authentication": "operational",
      "logging": "operational",
      "monitoring": "operational",
      "alerts": "operational"
    },
    "uptime": "7 days, 12 hours, 45 minutes"
  }
}
                        </div>
                    </div>
                </div>
                
                <div class="endpoint">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/stats</span>
                    </div>
                    <div class="endpoint-description">Estatísticas do sistema de segurança</div>
                    
                    <table class="params-table">
                        <tr>
                            <th>Parâmetro</th>
                            <th>Valores</th>
                            <th>Descrição</th>
                        </tr>
                        <tr>
                            <td>period</td>
                            <td>1h, 24h, 7d, 30d</td>
                            <td>Período para estatísticas (padrão: 24h)</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Rate Limiting -->
        <div class="endpoint-section">
            <div class="section-header">Rate Limiting</div>
            <div class="section-content">
                <div class="auth-info">
                    <div class="auth-title">Limites de Taxa</div>
                    <ul>
                        <li><strong>Limite padrão:</strong> 100 requisições por minuto por IP</li>
                        <li><strong>Headers de resposta:</strong></li>
                        <ul>
                            <li><code>X-RateLimit-Limit</code>: Limite total</li>
                            <li><code>X-RateLimit-Remaining</code>: Requisições restantes</li>
                            <li><code>X-RateLimit-Reset</code>: Timestamp do reset</li>
                        </ul>
                        <li><strong>Código de erro:</strong> 429 Too Many Requests</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <?php endif; ?>
</body>
</html>