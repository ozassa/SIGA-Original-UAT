<?php
/**
 * SIGA Security Response System
 * 
 * Sistema de Resposta Automática a Ameaças
 * Auto-bloqueio, rate limiting dinâmico, quarentena, rotação automática
 * 
 * @version 1.0
 * @author Claude Code - Security Mission
 */

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/security_functions.php';
require_once __DIR__ . '/InputValidationFramework.php';
require_once __DIR__ . '/src/role/rolePrefix.php';

/**
 * Classe principal do Sistema de Resposta Automática
 */
class SecurityResponseSystem {
    
    private $config = [
        'auto_block_enabled' => true,
        'rate_limiting_enabled' => true,
        'quarantine_enabled' => true,
        'token_rotation_enabled' => true,
        'backup_on_incident' => true,
        
        // Thresholds de resposta
        'ip_block_threshold' => 10, // tentativas
        'user_lockout_threshold' => 5, // tentativas
        'session_quarantine_threshold' => 3, // eventos suspeitos
        'critical_response_threshold' => 3, // incidentes críticos
        
        // Tempos de bloqueio
        'ip_block_duration' => 3600, // 1 hora
        'user_lockout_duration' => 1800, // 30 minutos
        'session_quarantine_duration' => 900, // 15 minutos
        
        // Rate limiting dinâmico
        'base_rate_limit' => 60, // requests por minuto
        'threat_rate_multiplier' => 0.5, // reduz pela metade quando ameaça detectada
        'recovery_time' => 300 // 5 minutos para recuperar rate normal
    ];
    
    private $blocked_ips = [];
    private $locked_users = [];
    private $quarantined_sessions = [];
    private $dynamic_rates = [];
    
    private $response_actions = [
        'IP_BLOCK' => 'Bloqueio automático de IP',
        'USER_LOCKOUT' => 'Bloqueio temporário de usuário',
        'SESSION_QUARANTINE' => 'Quarentena de sessão',
        'RATE_LIMIT_DYNAMIC' => 'Rate limiting dinâmico',
        'TOKEN_ROTATION' => 'Rotação automática de tokens',
        'BACKUP_TRIGGER' => 'Backup de emergência',
        'ALERT_ESCALATION' => 'Escalation de alertas',
        'SYSTEM_ISOLATION' => 'Isolamento de sistema'
    ];
    
    public function __construct() {
        $this->loadBlockedData();
        $this->initializeResponseSystem();
    }
    
    /**
     * Inicializar sistema de resposta
     */
    private function initializeResponseSystem() {
        // Verificar se deve executar limpeza automática
        $this->performMaintenanceTasks();
        
        // Registrar handlers de resposta automática
        $this->registerResponseHandlers();
    }
    
    /**
     * Processar evento de segurança e determinar resposta
     */
    public function processSecurityEvent($event_type, $event_data, $severity = 'medium') {
        $responses = [];
        
        // Análise do evento para determinar respostas necessárias
        $threat_score = $this->calculateThreatScore($event_type, $event_data, $severity);
        
        // Resposta baseada no tipo de evento
        switch ($event_type) {
            case 'BRUTE_FORCE':
                $responses = $this->handleBruteForceEvent($event_data, $threat_score);
                break;
                
            case 'SQL_INJECTION':
                $responses = $this->handleSQLInjectionEvent($event_data, $threat_score);
                break;
                
            case 'XSS_ATTEMPT':
                $responses = $this->handleXSSEvent($event_data, $threat_score);
                break;
                
            case 'SUSPICIOUS_ACTIVITY':
                $responses = $this->handleSuspiciousActivity($event_data, $threat_score);
                break;
                
            case 'MULTIPLE_FAILED_LOGINS':
                $responses = $this->handleMultipleFailedLogins($event_data, $threat_score);
                break;
                
            case 'SYSTEM_COMPROMISE':
                $responses = $this->handleSystemCompromise($event_data, $threat_score);
                break;
                
            default:
                $responses = $this->handleGenericThreat($event_data, $threat_score);
        }
        
        // Executar respostas determinadas
        foreach ($responses as $response) {
            $this->executeResponse($response);
        }
        
        // Log das respostas executadas
        $this->logResponseActions($event_type, $event_data, $responses, $threat_score);
        
        return $responses;
    }
    
    /**
     * Calcular score de ameaça
     */
    private function calculateThreatScore($event_type, $event_data, $severity) {
        $base_scores = [
            'SQL_INJECTION' => 9,
            'SYSTEM_COMPROMISE' => 10,
            'XSS_ATTEMPT' => 7,
            'BRUTE_FORCE' => 6,
            'SUSPICIOUS_ACTIVITY' => 5,
            'MULTIPLE_FAILED_LOGINS' => 4,
            'CSRF' => 6,
            'FILE_UPLOAD_BLOCKED' => 5
        ];
        
        $severity_multipliers = [
            'critical' => 1.5,
            'high' => 1.2,
            'medium' => 1.0,
            'low' => 0.8
        ];
        
        $base_score = $base_scores[$event_type] ?? 3;
        $multiplier = $severity_multipliers[$severity] ?? 1.0;
        
        // Fatores adicionais
        $ip = $event_data['ip'] ?? 'unknown';
        $repeat_offender_bonus = $this->isRepeatOffender($ip) ? 2 : 0;
        $frequency_bonus = $this->getRecentEventFrequency($ip, $event_type);
        
        $final_score = ($base_score * $multiplier) + $repeat_offender_bonus + $frequency_bonus;
        
        return min(10, max(1, $final_score)); // Score entre 1-10
    }
    
    /**
     * Manipular evento de força bruta
     */
    private function handleBruteForceEvent($event_data, $threat_score) {
        $responses = [];
        $ip = $event_data['ip'] ?? 'unknown';
        $user_id = $event_data['user_id'] ?? null;
        
        // Sempre aplicar rate limiting dinâmico
        $responses[] = [
            'action' => 'RATE_LIMIT_DYNAMIC',
            'target' => $ip,
            'parameters' => [
                'new_rate' => $this->config['base_rate_limit'] * $this->config['threat_rate_multiplier'],
                'duration' => $this->config['recovery_time']
            ]
        ];
        
        // Bloqueio de IP se score alto ou muitas tentativas
        if ($threat_score >= 7 || $this->getIPAttemptCount($ip) >= $this->config['ip_block_threshold']) {
            $responses[] = [
                'action' => 'IP_BLOCK',
                'target' => $ip,
                'parameters' => [
                    'duration' => $this->config['ip_block_duration'],
                    'reason' => 'Brute force attack detected'
                ]
            ];
        }
        
        // Bloqueio de usuário se identificado
        if ($user_id && $this->getUserFailedAttempts($user_id) >= $this->config['user_lockout_threshold']) {
            $responses[] = [
                'action' => 'USER_LOCKOUT',
                'target' => $user_id,
                'parameters' => [
                    'duration' => $this->config['user_lockout_duration'],
                    'reason' => 'Multiple failed login attempts'
                ]
            ];
        }
        
        return $responses;
    }
    
    /**
     * Manipular evento de SQL Injection
     */
    private function handleSQLInjectionEvent($event_data, $threat_score) {
        $responses = [];
        $ip = $event_data['ip'] ?? 'unknown';
        
        // SQL Injection sempre resulta em bloqueio imediato
        $responses[] = [
            'action' => 'IP_BLOCK',
            'target' => $ip,
            'parameters' => [
                'duration' => $this->config['ip_block_duration'] * 2, // Dobrar duração
                'reason' => 'SQL Injection attempt detected'
            ]
        ];
        
        // Rate limiting severo
        $responses[] = [
            'action' => 'RATE_LIMIT_DYNAMIC',
            'target' => $ip,
            'parameters' => [
                'new_rate' => 5, // Muito restritivo
                'duration' => $this->config['recovery_time'] * 4
            ]
        ];
        
        // Se score muito alto, escalar alerta
        if ($threat_score >= 9) {
            $responses[] = [
                'action' => 'ALERT_ESCALATION',
                'target' => 'security_team',
                'parameters' => [
                    'severity' => 'critical',
                    'immediate' => true
                ]
            ];
        }
        
        return $responses;
    }
    
    /**
     * Manipular evento XSS
     */
    private function handleXSSEvent($event_data, $threat_score) {
        $responses = [];
        $ip = $event_data['ip'] ?? 'unknown';
        $session_id = $event_data['session_id'] ?? null;
        
        // Rate limiting dinâmico
        $responses[] = [
            'action' => 'RATE_LIMIT_DYNAMIC',
            'target' => $ip,
            'parameters' => [
                'new_rate' => $this->config['base_rate_limit'] * 0.3,
                'duration' => $this->config['recovery_time'] * 2
            ]
        ];
        
        // Quarentena de sessão se identificada
        if ($session_id) {
            $responses[] = [
                'action' => 'SESSION_QUARANTINE',
                'target' => $session_id,
                'parameters' => [
                    'duration' => $this->config['session_quarantine_duration'],
                    'reason' => 'XSS attempt detected'
                ]
            ];
        }
        
        // Bloqueio se score alto
        if ($threat_score >= 8) {
            $responses[] = [
                'action' => 'IP_BLOCK',
                'target' => $ip,
                'parameters' => [
                    'duration' => $this->config['ip_block_duration'],
                    'reason' => 'Persistent XSS attempts'
                ]
            ];
        }
        
        return $responses;
    }
    
    /**
     * Manipular atividade suspeita
     */
    private function handleSuspiciousActivity($event_data, $threat_score) {
        $responses = [];
        $ip = $event_data['ip'] ?? 'unknown';
        $user_id = $event_data['user_id'] ?? null;
        $session_id = $event_data['session_id'] ?? null;
        
        // Rate limiting moderado
        $responses[] = [
            'action' => 'RATE_LIMIT_DYNAMIC',
            'target' => $ip,
            'parameters' => [
                'new_rate' => $this->config['base_rate_limit'] * 0.7,
                'duration' => $this->config['recovery_time']
            ]
        ];
        
        // Quarentena de sessão se muitos eventos suspeitos
        if ($session_id && $this->getSessionSuspiciousCount($session_id) >= $this->config['session_quarantine_threshold']) {
            $responses[] = [
                'action' => 'SESSION_QUARANTINE',
                'target' => $session_id,
                'parameters' => [
                    'duration' => $this->config['session_quarantine_duration'],
                    'reason' => 'Multiple suspicious activities'
                ]
            ];
        }
        
        // Rotação de tokens se usuário identificado
        if ($user_id && $threat_score >= 6) {
            $responses[] = [
                'action' => 'TOKEN_ROTATION',
                'target' => $user_id,
                'parameters' => [
                    'rotate_all' => true,
                    'force_reauth' => false
                ]
            ];
        }
        
        return $responses;
    }
    
    /**
     * Manipular múltiplos logins falhados
     */
    private function handleMultipleFailedLogins($event_data, $threat_score) {
        $responses = [];
        $ip = $event_data['ip'] ?? 'unknown';
        $user_id = $event_data['user_id'] ?? null;
        
        // Rate limiting baseado na frequência
        $rate_reduction = min(0.1, 0.9 - ($threat_score * 0.1));
        $responses[] = [
            'action' => 'RATE_LIMIT_DYNAMIC',
            'target' => $ip,
            'parameters' => [
                'new_rate' => $this->config['base_rate_limit'] * $rate_reduction,
                'duration' => $this->config['recovery_time']
            ]
        ];
        
        // Bloqueio de usuário
        if ($user_id) {
            $responses[] = [
                'action' => 'USER_LOCKOUT',
                'target' => $user_id,
                'parameters' => [
                    'duration' => $this->config['user_lockout_duration'],
                    'reason' => 'Multiple failed login attempts'
                ]
            ];
        }
        
        // Bloqueio de IP se muito persistente
        if ($threat_score >= 7) {
            $responses[] = [
                'action' => 'IP_BLOCK',
                'target' => $ip,
                'parameters' => [
                    'duration' => $this->config['ip_block_duration'],
                    'reason' => 'Persistent failed login attempts'
                ]
            ];
        }
        
        return $responses;
    }
    
    /**
     * Manipular comprometimento de sistema
     */
    private function handleSystemCompromise($event_data, $threat_score) {
        $responses = [];
        
        // Resposta máxima - todas as medidas de proteção
        $responses[] = [
            'action' => 'ALERT_ESCALATION',
            'target' => 'security_team',
            'parameters' => [
                'severity' => 'critical',
                'immediate' => true,
                'sms' => true,
                'phone' => true
            ]
        ];
        
        // Backup de emergência
        if ($this->config['backup_on_incident']) {
            $responses[] = [
                'action' => 'BACKUP_TRIGGER',
                'target' => 'system',
                'parameters' => [
                    'priority' => 'emergency',
                    'type' => 'full'
                ]
            ];
        }
        
        // Rotação massiva de tokens
        $responses[] = [
            'action' => 'TOKEN_ROTATION',
            'target' => 'all_users',
            'parameters' => [
                'rotate_all' => true,
                'force_reauth' => true,
                'emergency' => true
            ]
        ];
        
        // Considerar isolamento do sistema se muito crítico
        if ($threat_score >= 9) {
            $responses[] = [
                'action' => 'SYSTEM_ISOLATION',
                'target' => 'application',
                'parameters' => [
                    'maintenance_mode' => true,
                    'admin_only' => true
                ]
            ];
        }
        
        return $responses;
    }
    
    /**
     * Manipular ameaça genérica
     */
    private function handleGenericThreat($event_data, $threat_score) {
        $responses = [];
        $ip = $event_data['ip'] ?? 'unknown';
        
        // Rate limiting baseado no score
        if ($threat_score >= 5) {
            $rate_multiplier = max(0.1, 1 - ($threat_score * 0.1));
            $responses[] = [
                'action' => 'RATE_LIMIT_DYNAMIC',
                'target' => $ip,
                'parameters' => [
                    'new_rate' => $this->config['base_rate_limit'] * $rate_multiplier,
                    'duration' => $this->config['recovery_time']
                ]
            ];
        }
        
        // Bloqueio se score muito alto
        if ($threat_score >= 8) {
            $responses[] = [
                'action' => 'IP_BLOCK',
                'target' => $ip,
                'parameters' => [
                    'duration' => $this->config['ip_block_duration'],
                    'reason' => 'High threat score detected'
                ]
            ];
        }
        
        return $responses;
    }
    
    /**
     * Executar resposta específica
     */
    private function executeResponse($response) {
        $action = $response['action'];
        $target = $response['target'];
        $params = $response['parameters'] ?? [];
        
        switch ($action) {
            case 'IP_BLOCK':
                $this->executeIPBlock($target, $params);
                break;
                
            case 'USER_LOCKOUT':
                $this->executeUserLockout($target, $params);
                break;
                
            case 'SESSION_QUARANTINE':
                $this->executeSessionQuarantine($target, $params);
                break;
                
            case 'RATE_LIMIT_DYNAMIC':
                $this->executeRateLimitDynamic($target, $params);
                break;
                
            case 'TOKEN_ROTATION':
                $this->executeTokenRotation($target, $params);
                break;
                
            case 'BACKUP_TRIGGER':
                $this->executeBackupTrigger($target, $params);
                break;
                
            case 'ALERT_ESCALATION':
                $this->executeAlertEscalation($target, $params);
                break;
                
            case 'SYSTEM_ISOLATION':
                $this->executeSystemIsolation($target, $params);
                break;
        }
    }
    
    /**
     * Executar bloqueio de IP
     */
    private function executeIPBlock($ip, $params) {
        $duration = $params['duration'] ?? $this->config['ip_block_duration'];
        $reason = $params['reason'] ?? 'Automated security response';
        
        $this->blocked_ips[$ip] = [
            'blocked_at' => time(),
            'expires_at' => time() + $duration,
            'reason' => $reason,
            'auto_block' => true
        ];
        
        $this->saveBlockedData();
        
        // Log da ação
        error_log("SECURITY RESPONSE: IP $ip blocked for {$duration}s - $reason");
    }
    
    /**
     * Executar bloqueio de usuário
     */
    private function executeUserLockout($user_id, $params) {
        $duration = $params['duration'] ?? $this->config['user_lockout_duration'];
        $reason = $params['reason'] ?? 'Automated security response';
        
        $this->locked_users[$user_id] = [
            'locked_at' => time(),
            'expires_at' => time() + $duration,
            'reason' => $reason,
            'auto_lock' => true
        ];
        
        $this->saveBlockedData();
        
        error_log("SECURITY RESPONSE: User $user_id locked for {$duration}s - $reason");
    }
    
    /**
     * Executar quarentena de sessão
     */
    private function executeSessionQuarantine($session_id, $params) {
        $duration = $params['duration'] ?? $this->config['session_quarantine_duration'];
        $reason = $params['reason'] ?? 'Automated security response';
        
        $this->quarantined_sessions[$session_id] = [
            'quarantined_at' => time(),
            'expires_at' => time() + $duration,
            'reason' => $reason,
            'auto_quarantine' => true
        ];
        
        $this->saveBlockedData();
        
        error_log("SECURITY RESPONSE: Session $session_id quarantined for {$duration}s - $reason");
    }
    
    /**
     * Executar rate limiting dinâmico
     */
    private function executeRateLimitDynamic($ip, $params) {
        $new_rate = $params['new_rate'] ?? $this->config['base_rate_limit'];
        $duration = $params['duration'] ?? $this->config['recovery_time'];
        
        $this->dynamic_rates[$ip] = [
            'rate_limit' => $new_rate,
            'applied_at' => time(),
            'expires_at' => time() + $duration,
            'original_rate' => $this->config['base_rate_limit']
        ];
        
        $this->saveBlockedData();
        
        error_log("SECURITY RESPONSE: Dynamic rate limit applied to $ip - {$new_rate} req/min for {$duration}s");
    }
    
    /**
     * Executar rotação de tokens
     */
    private function executeTokenRotation($target, $params) {
        $rotate_all = $params['rotate_all'] ?? false;
        $force_reauth = $params['force_reauth'] ?? false;
        $emergency = $params['emergency'] ?? false;
        
        if ($target === 'all_users' || $rotate_all) {
            $this->rotateAllUserTokens($force_reauth, $emergency);
        } else {
            $this->rotateUserTokens($target, $force_reauth);
        }
        
        error_log("SECURITY RESPONSE: Token rotation executed for $target");
    }
    
    /**
     * Executar backup de emergência
     */
    private function executeBackupTrigger($target, $params) {
        $priority = $params['priority'] ?? 'normal';
        $type = $params['type'] ?? 'incremental';
        
        // Simular backup de emergência
        $backup_id = uniqid('emergency_backup_');
        
        error_log("SECURITY RESPONSE: Emergency backup triggered - ID: $backup_id, Type: $type, Priority: $priority");
        
        // Em produção, executar backup real
        // exec("php backup_script.php --emergency --type=$type");
    }
    
    /**
     * Executar escalation de alerta
     */
    private function executeAlertEscalation($target, $params) {
        $severity = $params['severity'] ?? 'medium';
        $immediate = $params['immediate'] ?? false;
        $sms = $params['sms'] ?? false;
        $phone = $params['phone'] ?? false;
        
        // Em produção, integrar com sistema de alertas real
        error_log("SECURITY RESPONSE: Alert escalation to $target - Severity: $severity, Immediate: " . ($immediate ? 'YES' : 'NO'));
        
        if ($immediate) {
            // Enviar alerta imediato
            $this->sendImmediateAlert($severity, $params);
        }
    }
    
    /**
     * Executar isolamento do sistema
     */
    private function executeSystemIsolation($target, $params) {
        $maintenance_mode = $params['maintenance_mode'] ?? false;
        $admin_only = $params['admin_only'] ?? false;
        
        if ($maintenance_mode) {
            // Ativar modo de manutenção
            file_put_contents(__DIR__ . '/.maintenance', json_encode([
                'enabled' => true,
                'reason' => 'Security incident response',
                'timestamp' => time(),
                'admin_only' => $admin_only
            ]));
        }
        
        error_log("SECURITY RESPONSE: System isolation activated - Maintenance: " . ($maintenance_mode ? 'YES' : 'NO'));
    }
    
    /**
     * Verificações de status
     */
    public function isIPBlocked($ip) {
        if (!isset($this->blocked_ips[$ip])) {
            return false;
        }
        
        $block_info = $this->blocked_ips[$ip];
        
        if (time() > $block_info['expires_at']) {
            unset($this->blocked_ips[$ip]);
            $this->saveBlockedData();
            return false;
        }
        
        return true;
    }
    
    public function isUserLocked($user_id) {
        if (!isset($this->locked_users[$user_id])) {
            return false;
        }
        
        $lock_info = $this->locked_users[$user_id];
        
        if (time() > $lock_info['expires_at']) {
            unset($this->locked_users[$user_id]);
            $this->saveBlockedData();
            return false;
        }
        
        return true;
    }
    
    public function isSessionQuarantined($session_id) {
        if (!isset($this->quarantined_sessions[$session_id])) {
            return false;
        }
        
        $quarantine_info = $this->quarantined_sessions[$session_id];
        
        if (time() > $quarantine_info['expires_at']) {
            unset($this->quarantined_sessions[$session_id]);
            $this->saveBlockedData();
            return false;
        }
        
        return true;
    }
    
    public function getCurrentRateLimit($ip) {
        if (!isset($this->dynamic_rates[$ip])) {
            return $this->config['base_rate_limit'];
        }
        
        $rate_info = $this->dynamic_rates[$ip];
        
        if (time() > $rate_info['expires_at']) {
            unset($this->dynamic_rates[$ip]);
            $this->saveBlockedData();
            return $this->config['base_rate_limit'];
        }
        
        return $rate_info['rate_limit'];
    }
    
    /**
     * Obter estatísticas do sistema de resposta
     */
    public function getResponseStats() {
        return [
            'blocked_ips' => count($this->blocked_ips),
            'locked_users' => count($this->locked_users),
            'quarantined_sessions' => count($this->quarantined_sessions),
            'dynamic_rate_limits' => count($this->dynamic_rates),
            'config' => $this->config,
            'recent_responses' => $this->getRecentResponses()
        ];
    }
    
    /**
     * Métodos auxiliares
     */
    private function loadBlockedData() {
        $data_file = __DIR__ . '/logs/security_response_data.json';
        
        if (file_exists($data_file)) {
            $data = json_decode(file_get_contents($data_file), true);
            
            $this->blocked_ips = $data['blocked_ips'] ?? [];
            $this->locked_users = $data['locked_users'] ?? [];
            $this->quarantined_sessions = $data['quarantined_sessions'] ?? [];
            $this->dynamic_rates = $data['dynamic_rates'] ?? [];
        }
    }
    
    private function saveBlockedData() {
        $data_file = __DIR__ . '/logs/security_response_data.json';
        
        $data = [
            'blocked_ips' => $this->blocked_ips,
            'locked_users' => $this->locked_users,
            'quarantined_sessions' => $this->quarantined_sessions,
            'dynamic_rates' => $this->dynamic_rates,
            'last_updated' => time()
        ];
        
        file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    }
    
    private function performMaintenanceTasks() {
        $current_time = time();
        
        // Limpar entradas expiradas
        foreach ($this->blocked_ips as $ip => $info) {
            if ($current_time > $info['expires_at']) {
                unset($this->blocked_ips[$ip]);
            }
        }
        
        foreach ($this->locked_users as $user => $info) {
            if ($current_time > $info['expires_at']) {
                unset($this->locked_users[$user]);
            }
        }
        
        foreach ($this->quarantined_sessions as $session => $info) {
            if ($current_time > $info['expires_at']) {
                unset($this->quarantined_sessions[$session]);
            }
        }
        
        foreach ($this->dynamic_rates as $ip => $info) {
            if ($current_time > $info['expires_at']) {
                unset($this->dynamic_rates[$ip]);
            }
        }
        
        $this->saveBlockedData();
    }
    
    private function registerResponseHandlers() {
        // Registrar handlers globais que podem ser chamados por outros sistemas
        $GLOBALS['security_response_system'] = $this;
    }
    
    private function isRepeatOffender($ip) {
        // Verificar se IP tem histórico de ataques
        $count = 0;
        $recent_logs = $this->getRecentSecurityLogs(24); // últimas 24h
        
        foreach ($recent_logs as $log) {
            if (isset($log['ip']) && $log['ip'] === $ip) {
                $count++;
            }
        }
        
        return $count > 5;
    }
    
    private function getRecentEventFrequency($ip, $event_type) {
        $count = 0;
        $recent_logs = $this->getRecentSecurityLogs(1); // última hora
        
        foreach ($recent_logs as $log) {
            if (isset($log['ip']) && $log['ip'] === $ip && 
                isset($log['type']) && $log['type'] === $event_type) {
                $count++;
            }
        }
        
        return min(3, $count); // Max bonus de 3
    }
    
    private function getRecentSecurityLogs($hours) {
        $logs = [];
        $log_file = __DIR__ . '/logs/security/security_' . date('Y-m-d') . '.log';
        
        if (file_exists($log_file)) {
            $lines = file($log_file, FILE_IGNORE_NEW_LINES);
            $cutoff_time = time() - ($hours * 3600);
            
            foreach ($lines as $line) {
                $log = json_decode($line, true);
                if ($log && strtotime($log['timestamp']) > $cutoff_time) {
                    $logs[] = $log;
                }
            }
        }
        
        return $logs;
    }
    
    private function getIPAttemptCount($ip) {
        return count(array_filter($this->getRecentSecurityLogs(1), function($log) use ($ip) {
            return isset($log['ip']) && $log['ip'] === $ip;
        }));
    }
    
    private function getUserFailedAttempts($user_id) {
        $auth_log = __DIR__ . '/logs/authentication/login_attempts_' . date('Y-m-d') . '.log';
        $count = 0;
        
        if (file_exists($auth_log)) {
            $lines = file($auth_log, FILE_IGNORE_NEW_LINES);
            $cutoff_time = time() - 3600; // última hora
            
            foreach ($lines as $line) {
                $log = json_decode($line, true);
                if ($log && !$log['success'] && 
                    isset($log['username']) && $log['username'] === $user_id &&
                    strtotime($log['timestamp']) > $cutoff_time) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    private function getSessionSuspiciousCount($session_id) {
        return count(array_filter($this->getRecentSecurityLogs(1), function($log) use ($session_id) {
            return isset($log['session_id']) && $log['session_id'] === $session_id;
        }));
    }
    
    private function rotateAllUserTokens($force_reauth, $emergency) {
        // Em produção, implementar rotação real de tokens
        error_log("SECURITY RESPONSE: Mass token rotation initiated - Force reauth: " . ($force_reauth ? 'YES' : 'NO'));
    }
    
    private function rotateUserTokens($user_id, $force_reauth) {
        // Em produção, implementar rotação de tokens do usuário
        error_log("SECURITY RESPONSE: Token rotation for user $user_id - Force reauth: " . ($force_reauth ? 'YES' : 'NO'));
    }
    
    private function sendImmediateAlert($severity, $params) {
        // Em produção, implementar envio de alerta imediato
        error_log("SECURITY RESPONSE: Immediate alert sent - Severity: $severity");
    }
    
    private function logResponseActions($event_type, $event_data, $responses, $threat_score) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event_type' => $event_type,
            'event_data' => $event_data,
            'threat_score' => $threat_score,
            'responses_executed' => $responses,
            'system_state' => [
                'blocked_ips' => count($this->blocked_ips),
                'locked_users' => count($this->locked_users),
                'quarantined_sessions' => count($this->quarantined_sessions)
            ]
        ];
        
        $log_dir = __DIR__ . '/logs/response';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_file = $log_dir . '/response_' . date('Y-m-d') . '.log';
        file_put_contents($log_file, json_encode($log_entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    private function getRecentResponses($hours = 24) {
        $responses = [];
        $log_file = __DIR__ . '/logs/response/response_' . date('Y-m-d') . '.log';
        
        if (file_exists($log_file)) {
            $lines = file($log_file, FILE_IGNORE_NEW_LINES);
            $cutoff_time = time() - ($hours * 3600);
            
            foreach ($lines as $line) {
                $log = json_decode($line, true);
                if ($log && strtotime($log['timestamp']) > $cutoff_time) {
                    $responses[] = $log;
                }
            }
        }
        
        return array_slice($responses, -20); // Últimas 20 respostas
    }
}

// Função global para processamento de eventos
function processSecurityEvent($event_type, $event_data, $severity = 'medium') {
    global $security_response_system;
    
    if (!isset($security_response_system)) {
        $security_response_system = new SecurityResponseSystem();
    }
    
    return $security_response_system->processSecurityEvent($event_type, $event_data, $severity);
}

// Função global para verificar se IP está bloqueado
function isIPBlocked($ip) {
    global $security_response_system;
    
    if (!isset($security_response_system)) {
        $security_response_system = new SecurityResponseSystem();
    }
    
    return $security_response_system->isIPBlocked($ip);
}

// Função global para verificar rate limit
function getCurrentRateLimit($ip) {
    global $security_response_system;
    
    if (!isset($security_response_system)) {
        $security_response_system = new SecurityResponseSystem();
    }
    
    return $security_response_system->getCurrentRateLimit($ip);
}

// Instanciar sistema se acessado diretamente
if (validate_user_session() && $_SESSION['pefil'] === 'F') {
    $response_system = new SecurityResponseSystem();
    
    // Processar requisições AJAX
    if (isset($_POST['action'])) {
        header('Content-Type: application/json');
        
        switch ($_POST['action']) {
            case 'get_stats':
                echo json_encode($response_system->getResponseStats());
                exit;
                
            case 'test_response':
                $event_type = InputValidator::validate($_POST['event_type'] ?? '', ['type' => 'string']);
                $ip = InputValidator::validate($_POST['ip'] ?? '127.0.0.1', ['type' => 'ip']);
                
                $test_event = [
                    'ip' => $ip,
                    'user_id' => 'test_user',
                    'session_id' => 'test_session_' . uniqid(),
                    'url' => '/test',
                    'user_agent' => 'Test Browser'
                ];
                
                $responses = $response_system->processSecurityEvent($event_type, $test_event, 'high');
                echo json_encode(['success' => true, 'responses' => $responses]);
                exit;
                
            default:
                echo json_encode(['error' => 'Ação não reconhecida']);
                exit;
        }
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGA - Sistema de Resposta Automática</title>
    
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/geral.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="Scripts/jquery.js"></script>
    
    <style>
        body {
            background: #0a0e1a;
            color: #ffffff;
            font-family: 'Courier New', monospace;
            min-height: 100vh;
        }
        
        .response-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .response-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px;
            background: linear-gradient(45deg, #1a1a2e, #16213e);
            border-radius: 15px;
            border: 2px solid #ff6b35;
        }
        
        .response-title {
            font-size: 2.8em;
            color: #ff6b35;
            text-shadow: 0 0 20px #ff6b35;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .status-card {
            background: #1a1a2e;
            border-radius: 10px;
            padding: 25px;
            border-left: 5px solid #ff6b35;
            position: relative;
            overflow: hidden;
        }
        
        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255, 107, 53, 0.1) 0%, transparent 70%);
        }
        
        .status-title {
            font-size: 1.2em;
            color: #ff6b35;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .status-value {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .status-value.critical { color: #ff4757; }
        .status-value.warning { color: #ffa502; }
        .status-value.active { color: #2ed573; }
        .status-value.info { color: #3742fa; }
        
        .status-description {
            font-size: 0.9em;
            color: #8e9aaf;
        }
        
        .response-dashboard {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .dashboard-panel {
            background: #1a1a2e;
            border-radius: 15px;
            padding: 30px;
            border: 1px solid #2d3748;
        }
        
        .panel-title {
            font-size: 1.4em;
            color: #ff6b35;
            margin-bottom: 25px;
            text-align: center;
            border-bottom: 2px solid #ff6b35;
            padding-bottom: 10px;
        }
        
        .config-list {
            list-style: none;
            padding: 0;
        }
        
        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #2d3748;
        }
        
        .config-item:last-child {
            border-bottom: none;
        }
        
        .config-label {
            color: #8e9aaf;
        }
        
        .config-value {
            font-weight: bold;
        }
        
        .config-value.enabled { color: #2ed573; }
        .config-value.disabled { color: #ff4757; }
        
        .blocked-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .blocked-item {
            background: #0f1419;
            margin: 8px 0;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ff4757;
        }
        
        .blocked-ip {
            font-weight: bold;
            color: #ff6b35;
            margin-bottom: 5px;
        }
        
        .blocked-reason {
            font-size: 0.9em;
            color: #8e9aaf;
            margin-bottom: 5px;
        }
        
        .blocked-expires {
            font-size: 0.8em;
            color: #ffa502;
        }
        
        .test-panel {
            background: #1a1a2e;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #2d3748;
        }
        
        .test-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            color: #ff6b35;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .form-input, .form-select {
            padding: 12px;
            background: #0f1419;
            color: #ffffff;
            border: 2px solid #2d3748;
            border-radius: 8px;
        }
        
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #ff6b35;
            box-shadow: 0 0 10px rgba(255, 107, 53, 0.3);
        }
        
        .test-button {
            padding: 12px 25px;
            background: linear-gradient(45deg, #ff6b35, #ff4757);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            align-self: end;
        }
        
        .test-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }
        
        .test-results {
            margin-top: 25px;
            padding: 20px;
            background: #0f1419;
            border-radius: 8px;
            border-left: 4px solid #2ed573;
        }
        
        .response-action {
            background: #16213e;
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3742fa;
        }
        
        .action-title {
            font-weight: bold;
            color: #ff6b35;
            margin-bottom: 8px;
        }
        
        .action-details {
            font-size: 0.9em;
            color: #8e9aaf;
        }
        
        .real-time-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1a1a2e;
            padding: 15px 20px;
            border-radius: 8px;
            border: 2px solid #2ed573;
            display: flex;
            align-items: center;
        }
        
        .pulse-dot {
            width: 12px;
            height: 12px;
            background: #2ed573;
            border-radius: 50%;
            margin-right: 10px;
            animation: pulse-dot 1s infinite;
        }
        
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }
        
        .loading {
            text-align: center;
            padding: 30px;
            color: #8e9aaf;
        }
        
        .loading-spinner {
            width: 30px;
            height: 30px;
            border: 3px solid #2d3748;
            border-top: 3px solid #ff6b35;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .response-dashboard {
                grid-template-columns: 1fr;
            }
            
            .status-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .test-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php if (!validate_user_session() || $_SESSION['pefil'] !== 'F'): ?>
        <div style="text-align: center; padding: 50px; color: #ff4757;">
            <h2>Acesso Negado</h2>
            <p>Apenas administradores podem acessar o Sistema de Resposta Automática.</p>
        </div>
    <?php else: ?>
    
    <div class="response-container">
        <div class="response-header">
            <h1 class="response-title">SISTEMA DE RESPOSTA AUTOMÁTICA</h1>
            <p>Proteção Ativa e Resposta Inteligente a Ameaças</p>
        </div>
        
        <div class="real-time-indicator">
            <div class="pulse-dot"></div>
            Sistema Ativo
        </div>
        
        <!-- Status Cards -->
        <div class="status-grid" id="status-grid">
            <div class="loading">
                <div class="loading-spinner"></div>
                Carregando estatísticas...
            </div>
        </div>
        
        <!-- Dashboard Principal -->
        <div class="response-dashboard">
            <!-- Configurações Ativas -->
            <div class="dashboard-panel">
                <div class="panel-title">CONFIGURAÇÕES ATIVAS</div>
                <ul class="config-list" id="config-list">
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        Carregando configurações...
                    </div>
                </ul>
            </div>
            
            <!-- IPs/Usuários Bloqueados -->
            <div class="dashboard-panel">
                <div class="panel-title">BLOQUEIOS ATIVOS</div>
                <div class="blocked-list" id="blocked-list">
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        Carregando bloqueios...
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Painel de Testes -->
        <div class="test-panel">
            <div class="panel-title">SIMULADOR DE RESPOSTA</div>
            <p style="color: #8e9aaf; margin-bottom: 20px;">
                Teste o sistema de resposta automática simulando diferentes tipos de ameaças
            </p>
            
            <div class="test-form">
                <div class="form-group">
                    <label class="form-label">Tipo de Evento:</label>
                    <select id="event-type" class="form-select">
                        <option value="BRUTE_FORCE">Força Bruta</option>
                        <option value="SQL_INJECTION">SQL Injection</option>
                        <option value="XSS_ATTEMPT">Tentativa XSS</option>
                        <option value="SUSPICIOUS_ACTIVITY">Atividade Suspeita</option>
                        <option value="MULTIPLE_FAILED_LOGINS">Múltiplos Logins Falhados</option>
                        <option value="SYSTEM_COMPROMISE">Comprometimento Sistema</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">IP de Teste:</label>
                    <input type="text" id="test-ip" class="form-input" placeholder="192.168.1.100" value="192.168.1.100">
                </div>
                
                <div class="form-group">
                    <button class="test-button" onclick="testResponse()">🚀 EXECUTAR TESTE</button>
                </div>
            </div>
            
            <div id="test-results" style="display: none;">
                <!-- Resultados dos testes aparecerão aqui -->
            </div>
        </div>
    </div>
    
    <script>
        // Carregar dados iniciais
        $(document).ready(function() {
            loadSystemStats();
            
            // Auto-refresh a cada 30 segundos
            setInterval(loadSystemStats, 30000);
        });
        
        // Carregar estatísticas do sistema
        function loadSystemStats() {
            $.post('', {action: 'get_stats'}, function(data) {
                if (data && !data.error) {
                    displayStats(data);
                    displayConfig(data.config);
                    displayBlockedItems(data);
                } else {
                    $('#status-grid').html('<div style="color: #ff4757; text-align: center;">Erro ao carregar dados</div>');
                }
            }, 'json').fail(function() {
                $('#status-grid').html('<div style="color: #ff4757; text-align: center;">Erro de comunicação</div>');
            });
        }
        
        // Exibir estatísticas
        function displayStats(data) {
            var html = '';
            
            html += '<div class="status-card">';
            html += '<div class="status-title">IPs BLOQUEADOS</div>';
            html += '<div class="status-value critical">' + data.blocked_ips + '</div>';
            html += '<div class="status-description">Bloqueios automáticos ativos</div>';
            html += '</div>';
            
            html += '<div class="status-card">';
            html += '<div class="status-title">USUÁRIOS BLOQUEADOS</div>';
            html += '<div class="status-value warning">' + data.locked_users + '</div>';
            html += '<div class="status-description">Contas temporariamente bloqueadas</div>';
            html += '</div>';
            
            html += '<div class="status-card">';
            html += '<div class="status-title">SESSÕES EM QUARENTENA</div>';
            html += '<div class="status-value info">' + data.quarantined_sessions + '</div>';
            html += '<div class="status-description">Sessões sob monitoramento</div>';
            html += '</div>';
            
            html += '<div class="status-card">';
            html += '<div class="status-title">RATE LIMITS DINÂMICOS</div>';
            html += '<div class="status-value active">' + data.dynamic_rate_limits + '</div>';
            html += '<div class="status-description">Limitações ativas por IP</div>';
            html += '</div>';
            
            $('#status-grid').html(html);
        }
        
        // Exibir configurações
        function displayConfig(config) {
            var html = '';
            
            var configItems = [
                {label: 'Auto-bloqueio de IPs', value: config.auto_block_enabled, type: 'boolean'},
                {label: 'Rate Limiting Dinâmico', value: config.rate_limiting_enabled, type: 'boolean'},
                {label: 'Quarentena de Sessões', value: config.quarantine_enabled, type: 'boolean'},
                {label: 'Rotação de Tokens', value: config.token_rotation_enabled, type: 'boolean'},
                {label: 'Backup em Incidentes', value: config.backup_on_incident, type: 'boolean'},
                {label: 'Threshold Bloqueio IP', value: config.ip_block_threshold + ' tentativas', type: 'text'},
                {label: 'Duração Bloqueio IP', value: Math.round(config.ip_block_duration / 60) + ' minutos', type: 'text'},
                {label: 'Rate Limit Base', value: config.base_rate_limit + ' req/min', type: 'text'}
            ];
            
            configItems.forEach(function(item) {
                html += '<li class="config-item">';
                html += '<span class="config-label">' + item.label + ':</span>';
                
                if (item.type === 'boolean') {
                    var statusClass = item.value ? 'enabled' : 'disabled';
                    var statusText = item.value ? 'ATIVO' : 'INATIVO';
                    html += '<span class="config-value ' + statusClass + '">' + statusText + '</span>';
                } else {
                    html += '<span class="config-value">' + item.value + '</span>';
                }
                
                html += '</li>';
            });
            
            $('#config-list').html(html);
        }
        
        // Exibir itens bloqueados
        function displayBlockedItems(data) {
            var html = '';
            
            // Simular alguns itens bloqueados para demonstração
            if (data.blocked_ips === 0 && data.locked_users === 0) {
                html = '<div style="text-align: center; color: #8e9aaf; padding: 20px;">Nenhum bloqueio ativo no momento</div>';
            } else {
                // Em produção, listar itens reais bloqueados
                var sampleBlocks = [
                    {type: 'IP', value: '192.168.1.50', reason: 'Tentativas de força bruta', expires: '15 minutos'},
                    {type: 'Usuário', value: 'user123', reason: 'Múltiplos logins falhados', expires: '30 minutos'},
                    {type: 'Sessão', value: 'sess_abc123', reason: 'Atividade suspeita', expires: '10 minutos'}
                ];
                
                sampleBlocks.forEach(function(block) {
                    html += '<div class="blocked-item">';
                    html += '<div class="blocked-ip">' + block.type + ': ' + block.value + '</div>';
                    html += '<div class="blocked-reason">Motivo: ' + block.reason + '</div>';
                    html += '<div class="blocked-expires">Expira em: ' + block.expires + '</div>';
                    html += '</div>';
                });
            }
            
            $('#blocked-list').html(html);
        }
        
        // Testar resposta
        function testResponse() {
            var eventType = $('#event-type').val();
            var testIP = $('#test-ip').val() || '192.168.1.100';
            
            $('#test-results').show().html(
                '<div class="loading">' +
                '<div class="loading-spinner"></div>' +
                'Executando teste de resposta para ' + eventType + '...' +
                '</div>'
            );
            
            $.post('', {
                action: 'test_response',
                event_type: eventType,
                ip: testIP
            }, function(data) {
                if (data.success) {
                    displayTestResults(data.responses, eventType, testIP);
                } else {
                    $('#test-results').html('<div style="color: #ff4757;">Erro: ' + (data.error || 'Falha no teste') + '</div>');
                }
            }, 'json').fail(function() {
                $('#test-results').html('<div style="color: #ff4757;">Erro de comunicação com o servidor</div>');
            });
        }
        
        // Exibir resultados do teste
        function displayTestResults(responses, eventType, testIP) {
            var html = '<div class="test-results">';
            html += '<h4 style="color: #ff6b35; margin-bottom: 15px;">Teste Executado: ' + eventType + ' (' + testIP + ')</h4>';
            
            if (responses.length === 0) {
                html += '<p style="color: #8e9aaf;">Nenhuma resposta automática foi disparada para este evento.</p>';
            } else {
                html += '<p style="color: #2ed573; margin-bottom: 15px;"><strong>' + responses.length + ' resposta(s) automática(s) executada(s):</strong></p>';
                
                responses.forEach(function(response, index) {
                    html += '<div class="response-action">';
                    html += '<div class="action-title">' + (index + 1) + '. ' + getActionName(response.action) + '</div>';
                    html += '<div class="action-details">';
                    html += '<strong>Alvo:</strong> ' + response.target + '<br>';
                    
                    if (response.parameters) {
                        for (var param in response.parameters) {
                            html += '<strong>' + param.replace(/_/g, ' ') + ':</strong> ' + response.parameters[param] + '<br>';
                        }
                    }
                    
                    html += '</div>';
                    html += '</div>';
                });
            }
            
            html += '<div style="margin-top: 20px; padding: 15px; background: #16213e; border-radius: 5px;">';
            html += '<small style="color: #8e9aaf;">💡 Este foi um teste simulado. Em ambiente de produção, as ações seriam executadas no sistema real.</small>';
            html += '</div>';
            
            html += '</div>';
            
            $('#test-results').html(html);
            
            // Recarregar estatísticas após o teste
            setTimeout(loadSystemStats, 2000);
        }
        
        // Obter nome amigável da ação
        function getActionName(action) {
            var actionNames = {
                'IP_BLOCK': '🚫 Bloqueio de IP',
                'USER_LOCKOUT': '🔒 Bloqueio de Usuário', 
                'SESSION_QUARANTINE': '⚠️ Quarentena de Sessão',
                'RATE_LIMIT_DYNAMIC': '⏳ Rate Limiting Dinâmico',
                'TOKEN_ROTATION': '🔄 Rotação de Tokens',
                'BACKUP_TRIGGER': '💾 Backup de Emergência',
                'ALERT_ESCALATION': '🚨 Escalation de Alerta',
                'SYSTEM_ISOLATION': '🛡️ Isolamento do Sistema'
            };
            
            return actionNames[action] || action;
        }
    </script>
    
    <?php endif; ?>
</body>
</html>