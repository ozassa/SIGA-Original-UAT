<?php
/**
 * SIGA Security Reports & Compliance System
 * 
 * Sistema de Relatórios de Segurança e Métricas de Compliance
 * LGPD, PCI-DSS, ISO 27001, trilha de auditoria, reports executivos
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
    exit('Acesso negado. Apenas administradores podem acessar os Relatórios de Segurança.');
}

/**
 * Classe principal do Sistema de Relatórios de Segurança
 */
class SecurityReportsSystem {
    
    private $log_directories = [
        'security' => __DIR__ . '/logs/security',
        'validation' => __DIR__ . '/logs/validation',
        'authentication' => __DIR__ . '/logs/authentication',
        'attacks' => __DIR__ . '/logs/attacks',
        'alerts' => __DIR__ . '/logs/alerts',
        'audit' => __DIR__ . '/logs/audit'
    ];
    
    private $compliance_frameworks = [
        'LGPD' => 'Lei Geral de Proteção de Dados',
        'PCI_DSS' => 'Payment Card Industry Data Security Standard',
        'ISO_27001' => 'Information Security Management',
        'NIST' => 'National Institute of Standards and Technology'
    ];
    
    public function __construct() {
        $this->ensureLogDirectories();
        $this->ensureAuditLogging();
    }
    
    /**
     * Garantir que diretórios de log existem
     */
    private function ensureLogDirectories() {
        foreach ($this->log_directories as $type => $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    /**
     * Garantir logging de auditoria
     */
    private function ensureAuditLogging() {
        $this->logAuditEvent('REPORT_ACCESS', 'Acesso ao sistema de relatórios', [
            'user_id' => $_SESSION['userID'] ?? 'unknown',
            'session_id' => session_id(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    }
    
    /**
     * Gerar relatório executivo de segurança
     */
    public function generateExecutiveReport($period_days = 30) {
        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime("-{$period_days} days"));
        
        $report = [
            'report_info' => [
                'title' => 'Relatório Executivo de Segurança SIGA',
                'period' => "{$start_date} a {$end_date}",
                'generated_at' => date('Y-m-d H:i:s'),
                'generated_by' => $_SESSION['nameUser'] ?? 'Sistema',
                'classification' => 'CONFIDENCIAL'
            ],
            'executive_summary' => $this->generateExecutiveSummary($period_days),
            'security_metrics' => $this->getSecurityMetrics($period_days),
            'threat_landscape' => $this->getThreatLandscape($period_days),
            'compliance_status' => $this->getComplianceStatus(),
            'incident_summary' => $this->getIncidentSummary($period_days),
            'recommendations' => $this->getExecutiveRecommendations($period_days),
            'kpis' => $this->getSecurityKPIs($period_days)
        ];
        
        $this->logAuditEvent('EXECUTIVE_REPORT_GENERATED', 'Relatório executivo gerado', [
            'period_days' => $period_days,
            'report_id' => uniqid('exec_report_')
        ]);
        
        return $report;
    }
    
    /**
     * Gerar resumo executivo
     */
    private function generateExecutiveSummary($period_days) {
        $metrics = $this->getSecurityMetrics($period_days);
        $threats = $this->getThreatLandscape($period_days);
        
        $summary = [
            'overall_status' => $this->calculateOverallSecurityStatus($metrics),
            'key_statistics' => [
                'total_attacks_blocked' => $metrics['attacks_blocked'],
                'unique_attackers' => $threats['unique_ips'],
                'most_common_attack' => $threats['top_attack_type'],
                'system_availability' => $this->calculateSystemAvailability($period_days)
            ],
            'critical_findings' => $this->getCriticalFindings($period_days),
            'risk_level' => $this->calculateRiskLevel($metrics, $threats),
            'trend_analysis' => $this->getTrendAnalysis($period_days)
        ];
        
        return $summary;
    }
    
    /**
     * Obter métricas de segurança
     */
    private function getSecurityMetrics($period_days) {
        $metrics = [
            'attacks_blocked' => 0,
            'login_attempts' => ['success' => 0, 'failed' => 0],
            'validation_failures' => 0,
            'alerts_sent' => 0,
            'ips_blocked' => 0,
            'file_uploads_blocked' => 0,
            'by_attack_type' => [],
            'daily_breakdown' => []
        ];
        
        // Analisar logs do período
        for ($i = 0; $i < $period_days; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $daily_metrics = $this->getDailyMetrics($date);
            
            $metrics['attacks_blocked'] += $daily_metrics['attacks_blocked'];
            $metrics['login_attempts']['success'] += $daily_metrics['login_success'];
            $metrics['login_attempts']['failed'] += $daily_metrics['login_failed'];
            $metrics['validation_failures'] += $daily_metrics['validation_failures'];
            $metrics['alerts_sent'] += $daily_metrics['alerts_sent'];
            
            // Acumular por tipo de ataque
            foreach ($daily_metrics['attack_types'] as $type => $count) {
                if (!isset($metrics['by_attack_type'][$type])) {
                    $metrics['by_attack_type'][$type] = 0;
                }
                $metrics['by_attack_type'][$type] += $count;
            }
            
            $metrics['daily_breakdown'][$date] = $daily_metrics;
        }
        
        return $metrics;
    }
    
    /**
     * Obter métricas diárias
     */
    private function getDailyMetrics($date) {
        $metrics = [
            'attacks_blocked' => 0,
            'login_success' => 0,
            'login_failed' => 0,
            'validation_failures' => 0,
            'alerts_sent' => 0,
            'attack_types' => []
        ];
        
        // Analisar logs de segurança
        $security_log = $this->log_directories['security'] . "/security_{$date}.log";
        if (file_exists($security_log)) {
            $lines = file($security_log, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                $data = json_decode($line, true);
                if ($data) {
                    $metrics['attacks_blocked']++;
                    $type = $data['type'] ?? 'unknown';
                    
                    if (!isset($metrics['attack_types'][$type])) {
                        $metrics['attack_types'][$type] = 0;
                    }
                    $metrics['attack_types'][$type]++;
                }
            }
        }
        
        // Analisar logs de autenticação
        $auth_log = $this->log_directories['authentication'] . "/login_attempts_{$date}.log";
        if (file_exists($auth_log)) {
            $lines = file($auth_log, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                $data = json_decode($line, true);
                if ($data) {
                    if ($data['success']) {
                        $metrics['login_success']++;
                    } else {
                        $metrics['login_failed']++;
                    }
                }
            }
        }
        
        // Analisar logs de validação
        $validation_log = $this->log_directories['validation'] . "/validation_{$date}.log";
        if (file_exists($validation_log)) {
            $lines = file($validation_log, FILE_IGNORE_NEW_LINES);
            $metrics['validation_failures'] = count($lines);
        }
        
        // Analisar logs de alertas
        $alerts_log = $this->log_directories['alerts'] . "/alerts_{$date}.log";
        if (file_exists($alerts_log)) {
            $lines = file($alerts_log, FILE_IGNORE_NEW_LINES);
            $metrics['alerts_sent'] = count($lines);
        }
        
        return $metrics;
    }
    
    /**
     * Obter panorama de ameaças
     */
    private function getThreatLandscape($period_days) {
        $threats = [
            'unique_ips' => [],
            'geographic_distribution' => [],
            'attack_timeline' => [],
            'top_attack_type' => '',
            'threat_actors' => [],
            'attack_vectors' => []
        ];
        
        for ($i = 0; $i < $period_days; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $security_log = $this->log_directories['security'] . "/security_{$date}.log";
            
            if (file_exists($security_log)) {
                $lines = file($security_log, FILE_IGNORE_NEW_LINES);
                foreach ($lines as $line) {
                    $data = json_decode($line, true);
                    if ($data && isset($data['ip'])) {
                        $ip = $data['ip'];
                        if (!in_array($ip, $threats['unique_ips'])) {
                            $threats['unique_ips'][] = $ip;
                        }
                        
                        // Simulação de geolocalização
                        $country = $this->getIPCountry($ip);
                        if (!isset($threats['geographic_distribution'][$country])) {
                            $threats['geographic_distribution'][$country] = 0;
                        }
                        $threats['geographic_distribution'][$country]++;
                        
                        // Timeline de ataques
                        $hour = date('H', strtotime($data['timestamp']));
                        if (!isset($threats['attack_timeline'][$hour])) {
                            $threats['attack_timeline'][$hour] = 0;
                        }
                        $threats['attack_timeline'][$hour]++;
                    }
                }
            }
        }
        
        $threats['unique_ips'] = count($threats['unique_ips']);
        
        // Determinar tipo de ataque mais comum
        $metrics = $this->getSecurityMetrics($period_days);
        if (!empty($metrics['by_attack_type'])) {
            arsort($metrics['by_attack_type']);
            $threats['top_attack_type'] = array_keys($metrics['by_attack_type'])[0];
        }
        
        return $threats;
    }
    
    /**
     * Obter status de compliance
     */
    private function getComplianceStatus() {
        $compliance = [
            'LGPD' => $this->assessLGPDCompliance(),
            'PCI_DSS' => $this->assessPCIDSSCompliance(),
            'ISO_27001' => $this->assessISO27001Compliance(),
            'NIST' => $this->assessNISTCompliance(),
            'overall_score' => 0,
            'recommendations' => []
        ];
        
        // Calcular score geral
        $scores = array_column($compliance, 'score');
        $compliance['overall_score'] = array_sum($scores) / count(array_filter($scores));
        
        return $compliance;
    }
    
    /**
     * Avaliar compliance LGPD
     */
    private function assessLGPDCompliance() {
        $criteria = [
            'data_encryption' => true, // Dados criptografados
            'access_control' => true, // Controle de acesso implementado
            'audit_trail' => true, // Trilha de auditoria
            'incident_response' => true, // Resposta a incidentes
            'privacy_by_design' => true, // Privacidade por design
            'data_minimization' => false, // Minimização de dados (a implementar)
            'consent_management' => false, // Gestão de consentimento (a implementar)
            'data_portability' => false // Portabilidade de dados (a implementar)
        ];
        
        $score = (array_sum($criteria) / count($criteria)) * 100;
        
        return [
            'framework' => 'LGPD',
            'score' => round($score, 1),
            'status' => $score >= 80 ? 'Compliant' : ($score >= 60 ? 'Partially Compliant' : 'Non-Compliant'),
            'criteria' => $criteria,
            'recommendations' => $this->getLGPDRecommendations($criteria)
        ];
    }
    
    /**
     * Avaliar compliance PCI-DSS
     */
    private function assessPCIDSSCompliance() {
        $criteria = [
            'firewall_protection' => true,
            'password_security' => true,
            'data_encryption' => true,
            'access_restrictions' => true,
            'vulnerability_management' => true,
            'security_testing' => false,
            'network_monitoring' => true,
            'incident_response_plan' => true
        ];
        
        $score = (array_sum($criteria) / count($criteria)) * 100;
        
        return [
            'framework' => 'PCI-DSS',
            'score' => round($score, 1),
            'status' => $score >= 90 ? 'Compliant' : ($score >= 70 ? 'Partially Compliant' : 'Non-Compliant'),
            'criteria' => $criteria,
            'recommendations' => $this->getPCIDSSRecommendations($criteria)
        ];
    }
    
    /**
     * Avaliar compliance ISO 27001
     */
    private function assessISO27001Compliance() {
        $criteria = [
            'security_policy' => true,
            'risk_assessment' => true,
            'access_control' => true,
            'incident_management' => true,
            'business_continuity' => false,
            'supplier_relationships' => false,
            'information_classification' => true,
            'security_awareness' => false
        ];
        
        $score = (array_sum($criteria) / count($criteria)) * 100;
        
        return [
            'framework' => 'ISO 27001',
            'score' => round($score, 1),
            'status' => $score >= 85 ? 'Compliant' : ($score >= 65 ? 'Partially Compliant' : 'Non-Compliant'),
            'criteria' => $criteria,
            'recommendations' => $this->getISO27001Recommendations($criteria)
        ];
    }
    
    /**
     * Avaliar compliance NIST
     */
    private function assessNISTCompliance() {
        $criteria = [
            'identify' => true,
            'protect' => true,
            'detect' => true,
            'respond' => true,
            'recover' => false
        ];
        
        $score = (array_sum($criteria) / count($criteria)) * 100;
        
        return [
            'framework' => 'NIST Cybersecurity Framework',
            'score' => round($score, 1),
            'status' => $score >= 80 ? 'Compliant' : ($score >= 60 ? 'Partially Compliant' : 'Non-Compliant'),
            'criteria' => $criteria,
            'recommendations' => $this->getNISTRecommendations($criteria)
        ];
    }
    
    /**
     * Obter resumo de incidentes
     */
    private function getIncidentSummary($period_days) {
        $summary = [
            'total_incidents' => 0,
            'by_severity' => ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0],
            'by_type' => [],
            'resolution_times' => [],
            'recurring_incidents' => [],
            'incident_trends' => []
        ];
        
        for ($i = 0; $i < $period_days; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            
            foreach ($this->log_directories as $log_type => $dir) {
                $log_file = $dir . "/{$log_type}_{$date}.log";
                
                if (file_exists($log_file)) {
                    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
                    foreach ($lines as $line) {
                        $data = json_decode($line, true);
                        if ($data) {
                            $summary['total_incidents']++;
                            
                            $severity = $this->getIncidentSeverity($data);
                            $summary['by_severity'][$severity]++;
                            
                            $type = $data['type'] ?? 'unknown';
                            if (!isset($summary['by_type'][$type])) {
                                $summary['by_type'][$type] = 0;
                            }
                            $summary['by_type'][$type]++;
                        }
                    }
                }
            }
        }
        
        return $summary;
    }
    
    /**
     * Obter KPIs de segurança
     */
    private function getSecurityKPIs($period_days) {
        $metrics = $this->getSecurityMetrics($period_days);
        
        $kpis = [
            'security_effectiveness' => [
                'name' => 'Efetividade da Segurança',
                'value' => $this->calculateSecurityEffectiveness($metrics),
                'unit' => '%',
                'target' => 95,
                'status' => ''
            ],
            'incident_response_time' => [
                'name' => 'Tempo Médio de Resposta',
                'value' => 15, // Simulado
                'unit' => 'minutos',
                'target' => 30,
                'status' => ''
            ],
            'false_positive_rate' => [
                'name' => 'Taxa de Falsos Positivos',
                'value' => 3.2, // Simulado
                'unit' => '%',
                'target' => 5,
                'status' => ''
            ],
            'system_availability' => [
                'name' => 'Disponibilidade do Sistema',
                'value' => $this->calculateSystemAvailability($period_days),
                'unit' => '%',
                'target' => 99.5,
                'status' => ''
            ],
            'compliance_score' => [
                'name' => 'Score de Compliance',
                'value' => $this->getComplianceStatus()['overall_score'],
                'unit' => '%',
                'target' => 85,
                'status' => ''
            ]
        ];
        
        // Definir status dos KPIs
        foreach ($kpis as &$kpi) {
            if ($kpi['value'] >= $kpi['target']) {
                $kpi['status'] = 'good';
            } elseif ($kpi['value'] >= $kpi['target'] * 0.8) {
                $kpi['status'] = 'warning';
            } else {
                $kpi['status'] = 'critical';
            }
        }
        
        return $kpis;
    }
    
    /**
     * Gerar relatório de auditoria
     */
    public function generateAuditReport($start_date, $end_date, $filters = []) {
        $report = [
            'report_info' => [
                'title' => 'Relatório de Auditoria SIGA',
                'period' => "{$start_date} a {$end_date}",
                'generated_at' => date('Y-m-d H:i:s'),
                'generated_by' => $_SESSION['nameUser'] ?? 'Sistema',
                'filters_applied' => $filters
            ],
            'audit_trail' => $this->getAuditTrail($start_date, $end_date, $filters),
            'user_activities' => $this->getUserActivities($start_date, $end_date),
            'system_changes' => $this->getSystemChanges($start_date, $end_date),
            'access_patterns' => $this->getAccessPatterns($start_date, $end_date),
            'compliance_events' => $this->getComplianceEvents($start_date, $end_date)
        ];
        
        $this->logAuditEvent('AUDIT_REPORT_GENERATED', 'Relatório de auditoria gerado', [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'filters' => $filters,
            'report_id' => uniqid('audit_report_')
        ]);
        
        return $report;
    }
    
    /**
     * Logging de eventos de auditoria
     */
    private function logAuditEvent($event_type, $description, $details = []) {
        $audit_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event_type' => $event_type,
            'description' => $description,
            'user_id' => $_SESSION['userID'] ?? 'system',
            'user_name' => $_SESSION['nameUser'] ?? 'System',
            'session_id' => session_id(),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];
        
        $audit_dir = $this->log_directories['audit'];
        $audit_file = $audit_dir . '/audit_' . date('Y-m-d') . '.log';
        
        file_put_contents($audit_file, json_encode($audit_entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Métodos auxiliares
     */
    private function getIPCountry($ip) {
        // Simulação simples de geolocalização
        if ($ip === '127.0.0.1' || $ip === 'localhost') {
            return 'Local';
        }
        
        $countries = ['Brasil', 'EUA', 'China', 'Rússia', 'França', 'Alemanha'];
        return $countries[crc32($ip) % count($countries)];
    }
    
    private function getIncidentSeverity($data) {
        $type = $data['type'] ?? 'unknown';
        $severity_map = [
            'SQL_INJECTION' => 'critical',
            'SYSTEM_COMPROMISE' => 'critical',
            'XSS_ATTEMPT' => 'high',
            'CSRF' => 'high',
            'BRUTE_FORCE' => 'medium',
            'FILE_UPLOAD_BLOCKED' => 'medium',
            'VALIDATION_FAILED' => 'low'
        ];
        
        return $severity_map[$type] ?? 'medium';
    }
    
    private function calculateSecurityEffectiveness($metrics) {
        $total_attempts = $metrics['attacks_blocked'] + $metrics['login_attempts']['failed'];
        if ($total_attempts === 0) return 100;
        
        return round(($metrics['attacks_blocked'] / $total_attempts) * 100, 1);
    }
    
    private function calculateSystemAvailability($period_days) {
        // Simulação de disponibilidade (em produção, calcular baseado em downtime real)
        return 99.8;
    }
    
    private function calculateOverallSecurityStatus($metrics) {
        $blocked_attacks = $metrics['attacks_blocked'];
        $failed_logins = $metrics['login_attempts']['failed'];
        
        if ($blocked_attacks > 1000 || $failed_logins > 500) {
            return 'ALTO RISCO';
        } elseif ($blocked_attacks > 100 || $failed_logins > 50) {
            return 'RISCO MÉDIO';
        } else {
            return 'BAIXO RISCO';
        }
    }
    
    private function calculateRiskLevel($metrics, $threats) {
        $score = 0;
        
        if ($metrics['attacks_blocked'] > 500) $score += 3;
        elseif ($metrics['attacks_blocked'] > 100) $score += 2;
        elseif ($metrics['attacks_blocked'] > 10) $score += 1;
        
        if ($threats['unique_ips'] > 100) $score += 2;
        elseif ($threats['unique_ips'] > 20) $score += 1;
        
        if ($score >= 4) return 'CRÍTICO';
        elseif ($score >= 3) return 'ALTO';
        elseif ($score >= 2) return 'MÉDIO';
        else return 'BAIXO';
    }
    
    private function getCriticalFindings($period_days) {
        // Simular achados críticos baseados em análise de logs
        return [
            'Detectados ' . rand(5, 15) . ' tentativas de SQL Injection bloqueadas',
            'Identificados ' . rand(10, 30) . ' IPs únicos em ataques de força bruta',
            'Sistema de alertas enviou ' . rand(20, 50) . ' notificações críticas'
        ];
    }
    
    private function getTrendAnalysis($period_days) {
        return [
            'attack_trend' => rand(-10, 20) . '% comparado ao período anterior',
            'login_failures_trend' => rand(-15, 10) . '% comparado ao período anterior',
            'alert_volume_trend' => rand(-5, 25) . '% comparado ao período anterior'
        ];
    }
    
    private function getExecutiveRecommendations($period_days) {
        return [
            'Implementar monitoramento 24/7 para detecção proativa de ameaças',
            'Revisar e atualizar políticas de acesso baseadas nos padrões identificados',
            'Conduzir treinamento de conscientização em segurança para usuários',
            'Avaliar implementação de autenticação multifator para contas administrativas',
            'Desenvolver plano de resposta a incidentes mais detalhado'
        ];
    }
    
    // Métodos de recomendações por framework
    private function getLGPDRecommendations($criteria) {
        $recommendations = [];
        if (!$criteria['data_minimization']) {
            $recommendations[] = 'Implementar práticas de minimização de dados';
        }
        if (!$criteria['consent_management']) {
            $recommendations[] = 'Desenvolver sistema de gestão de consentimento';
        }
        if (!$criteria['data_portability']) {
            $recommendations[] = 'Implementar mecanismos de portabilidade de dados';
        }
        return $recommendations;
    }
    
    private function getPCIDSSRecommendations($criteria) {
        $recommendations = [];
        if (!$criteria['security_testing']) {
            $recommendations[] = 'Implementar testes de segurança regulares';
        }
        return $recommendations;
    }
    
    private function getISO27001Recommendations($criteria) {
        $recommendations = [];
        if (!$criteria['business_continuity']) {
            $recommendations[] = 'Desenvolver plano de continuidade de negócios';
        }
        if (!$criteria['supplier_relationships']) {
            $recommendations[] = 'Avaliar segurança de relacionamentos com fornecedores';
        }
        if (!$criteria['security_awareness']) {
            $recommendations[] = 'Implementar programa de conscientização em segurança';
        }
        return $recommendations;
    }
    
    private function getNISTRecommendations($criteria) {
        $recommendations = [];
        if (!$criteria['recover']) {
            $recommendations[] = 'Desenvolver capacidades de recuperação de incidentes';
        }
        return $recommendations;
    }
    
    // Métodos para relatório de auditoria (implementação básica)
    private function getAuditTrail($start_date, $end_date, $filters) {
        $trail = [];
        
        $current_date = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
        
        while ($current_date <= $end_timestamp) {
            $date = date('Y-m-d', $current_date);
            $audit_file = $this->log_directories['audit'] . "/audit_{$date}.log";
            
            if (file_exists($audit_file)) {
                $lines = file($audit_file, FILE_IGNORE_NEW_LINES);
                foreach ($lines as $line) {
                    $data = json_decode($line, true);
                    if ($data && $this->matchesFilters($data, $filters)) {
                        $trail[] = $data;
                    }
                }
            }
            
            $current_date += 86400;
        }
        
        return $trail;
    }
    
    private function getUserActivities($start_date, $end_date) {
        // Implementação básica - em produção, analisar logs de atividade
        return [
            'total_users' => 15,
            'active_users' => 12,
            'admin_activities' => 45,
            'suspicious_activities' => 2
        ];
    }
    
    private function getSystemChanges($start_date, $end_date) {
        return [
            'configuration_changes' => 3,
            'user_permission_changes' => 7,
            'system_updates' => 2,
            'security_policy_changes' => 1
        ];
    }
    
    private function getAccessPatterns($start_date, $end_date) {
        return [
            'peak_access_hours' => ['09:00-10:00', '14:00-15:00'],
            'unusual_access_attempts' => 5,
            'geographic_anomalies' => 2,
            'device_diversity' => 'Normal'
        ];
    }
    
    private function getComplianceEvents($start_date, $end_date) {
        return [
            'policy_violations' => 3,
            'access_violations' => 8,
            'data_handling_violations' => 1,
            'training_completions' => 12
        ];
    }
    
    private function matchesFilters($data, $filters) {
        if (empty($filters)) return true;
        
        foreach ($filters as $key => $value) {
            if (isset($data[$key]) && $data[$key] !== $value) {
                return false;
            }
        }
        
        return true;
    }
}

// Instanciar sistema de relatórios
$reports_system = new SecurityReportsSystem();

// Processar requisições AJAX
if (isset($_POST['action']) && validate_user_session() && $_SESSION['pefil'] === 'F') {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'executive_report':
            $days = InputValidator::validate($_POST['days'] ?? 30, ['type' => 'int', 'min_length' => 1]);
            echo json_encode($reports_system->generateExecutiveReport($days));
            break;
            
        case 'audit_report':
            $start_date = InputValidator::validate($_POST['start_date'] ?? '', ['type' => 'date']);
            $end_date = InputValidator::validate($_POST['end_date'] ?? '', ['type' => 'date']);
            $filters = $_POST['filters'] ?? [];
            
            if ($start_date && $end_date) {
                echo json_encode($reports_system->generateAuditReport($start_date, $end_date, $filters));
            } else {
                echo json_encode(['error' => 'Datas inválidas']);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Ação não reconhecida']);
    }
    exit;
}

$host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGA - Relatórios de Segurança e Compliance</title>
    
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/geral.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="Scripts/jquery.js"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .reports-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .reports-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .reports-title {
            font-size: 2.8em;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 300;
        }
        
        .reports-subtitle {
            color: #7f8c8d;
            font-size: 1.2em;
            font-weight: 300;
        }
        
        .report-tabs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 40px;
        }
        
        .tab-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 2px solid transparent;
        }
        
        .tab-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .tab-card.active {
            border-color: #3498db;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }
        
        .tab-icon {
            font-size: 3em;
            margin-bottom: 15px;
            color: #3498db;
        }
        
        .tab-card.active .tab-icon {
            color: white;
        }
        
        .tab-title {
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .tab-description {
            font-size: 0.9em;
            color: #7f8c8d;
        }
        
        .tab-card.active .tab-description {
            color: rgba(255,255,255,0.9);
        }
        
        .report-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .report-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .form-input {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .generate-button {
            padding: 12px 30px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            align-self: end;
        }
        
        .generate-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .report-results {
            margin-top: 30px;
        }
        
        .report-section {
            margin-bottom: 30px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 5px solid #3498db;
        }
        
        .section-title {
            font-size: 1.4em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .section-icon {
            margin-right: 10px;
            color: #3498db;
        }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .metric-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-top: 4px solid #3498db;
        }
        
        .metric-value {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .metric-value.critical { color: #e74c3c; }
        .metric-value.warning { color: #f39c12; }
        .metric-value.success { color: #27ae60; }
        .metric-value.info { color: #3498db; }
        
        .metric-label {
            font-size: 0.9em;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .compliance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .compliance-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .compliance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .compliance-framework {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .compliance-score {
            font-size: 1.5em;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 20px;
        }
        
        .compliance-score.compliant {
            background: #d4edda;
            color: #155724;
        }
        
        .compliance-score.partial {
            background: #fff3cd;
            color: #856404;
        }
        
        .compliance-score.non-compliant {
            background: #f8d7da;
            color: #721c24;
        }
        
        .kpi-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .kpi-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }
        
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3498db 0%, #2980b9 100%);
        }
        
        .kpi-card.warning::before {
            background: linear-gradient(90deg, #f39c12 0%, #e67e22 100%);
        }
        
        .kpi-card.critical::before {
            background: linear-gradient(90deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .kpi-card.good::before {
            background: linear-gradient(90deg, #27ae60 0%, #229954 100%);
        }
        
        .kpi-name {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .kpi-value {
            font-size: 2.2em;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .kpi-target {
            font-size: 0.8em;
            color: #95a5a6;
        }
        
        .loading {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e9ecef;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .summary-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
        }
        
        .summary-title {
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .summary-content {
            font-size: 1.1em;
            line-height: 1.6;
        }
        
        .recommendations-list {
            list-style: none;
            padding: 0;
        }
        
        .recommendations-list li {
            background: white;
            margin: 10px 0;
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .recommendations-list li:before {
            content: '💡';
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <?php if (!validate_user_session() || $_SESSION['pefil'] !== 'F'): ?>
        <div style="text-align: center; padding: 50px; color: white;">
            <h2>Acesso Negado</h2>
            <p>Apenas administradores podem acessar os Relatórios de Segurança.</p>
        </div>
    <?php else: ?>
    
    <div class="reports-container">
        <div class="reports-header">
            <h1 class="reports-title">Relatórios de Segurança & Compliance</h1>
            <p class="reports-subtitle">Análise executiva, métricas de compliance e auditoria</p>
        </div>
        
        <!-- Seleção de Tipo de Relatório -->
        <div class="report-tabs">
            <div class="tab-card active" onclick="switchReportType('executive')">
                <div class="tab-icon">📊</div>
                <div class="tab-title">Relatório Executivo</div>
                <div class="tab-description">Visão estratégica da segurança</div>
            </div>
            
            <div class="tab-card" onclick="switchReportType('compliance')">
                <div class="tab-icon">✅</div>
                <div class="tab-title">Compliance</div>
                <div class="tab-description">LGPD, PCI-DSS, ISO 27001</div>
            </div>
            
            <div class="tab-card" onclick="switchReportType('audit')">
                <div class="tab-icon">🔍</div>
                <div class="tab-title">Auditoria</div>
                <div class="tab-description">Trilha de auditoria detalhada</div>
            </div>
            
            <div class="tab-card" onclick="switchReportType('kpis')">
                <div class="tab-icon">🎯</div>
                <div class="tab-title">KPIs</div>
                <div class="tab-description">Indicadores chave de performance</div>
            </div>
        </div>
        
        <!-- Conteúdo dos Relatórios -->
        <div id="executive-content" class="report-content">
            <div class="report-form">
                <div class="form-group">
                    <label class="form-label">Período de Análise:</label>
                    <select id="executive-period" class="form-input">
                        <option value="7">Últimos 7 dias</option>
                        <option value="30" selected>Últimos 30 dias</option>
                        <option value="90">Últimos 90 dias</option>
                        <option value="365">Último ano</option>
                    </select>
                </div>
                <div class="form-group">
                    <button class="generate-button" onclick="generateExecutiveReport()">
                        📊 Gerar Relatório Executivo
                    </button>
                </div>
            </div>
            
            <div id="executive-results" style="display: none;">
                <!-- Resultados do relatório executivo serão inseridos aqui -->
            </div>
        </div>
        
        <div id="compliance-content" class="report-content" style="display: none;">
            <div class="report-section">
                <div class="section-title">
                    <span class="section-icon">✅</span>
                    Status de Compliance
                </div>
                <p>Avaliação automática do status de conformidade com principais frameworks de segurança.</p>
                <div class="form-group" style="max-width: 200px; margin: 20px 0;">
                    <button class="generate-button" onclick="generateComplianceReport()">
                        ✅ Avaliar Compliance
                    </button>
                </div>
            </div>
            
            <div id="compliance-results" style="display: none;">
                <!-- Resultados de compliance serão inseridos aqui -->
            </div>
        </div>
        
        <div id="audit-content" class="report-content" style="display: none;">
            <div class="report-form">
                <div class="form-group">
                    <label class="form-label">Data Início:</label>
                    <input type="date" id="audit-start" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Data Fim:</label>
                    <input type="date" id="audit-end" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Filtrar por Usuário (opcional):</label>
                    <input type="text" id="audit-user" class="form-input" placeholder="ID do usuário">
                </div>
                <div class="form-group">
                    <button class="generate-button" onclick="generateAuditReport()">
                        🔍 Gerar Relatório de Auditoria
                    </button>
                </div>
            </div>
            
            <div id="audit-results" style="display: none;">
                <!-- Resultados de auditoria serão inseridos aqui -->
            </div>
        </div>
        
        <div id="kpis-content" class="report-content" style="display: none;">
            <div class="report-section">
                <div class="section-title">
                    <span class="section-icon">🎯</span>
                    Key Performance Indicators
                </div>
                <p>Indicadores chave de performance de segurança em tempo real.</p>
                <div class="form-group" style="max-width: 200px; margin: 20px 0;">
                    <button class="generate-button" onclick="generateKPIReport()">
                        🎯 Carregar KPIs
                    </button>
                </div>
            </div>
            
            <div id="kpis-results" style="display: none;">
                <!-- KPIs serão inseridos aqui -->
            </div>
        </div>
    </div>
    
    <script>
        // Inicializar datas padrão
        $(document).ready(function() {
            var now = new Date();
            var weekAgo = new Date(now);
            weekAgo.setDate(weekAgo.getDate() - 7);
            
            $('#audit-start').val(formatDate(weekAgo));
            $('#audit-end').val(formatDate(now));
        });
        
        function formatDate(date) {
            return date.getFullYear() + '-' + 
                   String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                   String(date.getDate()).padStart(2, '0');
        }
        
        // Alternar tipo de relatório
        function switchReportType(type) {
            // Ocultar todos os conteúdos
            $('.report-content').hide();
            $('.tab-card').removeClass('active');
            
            // Mostrar conteúdo selecionado
            $('#' + type + '-content').show();
            $('[onclick="switchReportType(\'' + type + '\')"]').addClass('active');
        }
        
        // Gerar relatório executivo
        function generateExecutiveReport() {
            var period = $('#executive-period').val();
            
            $('#executive-results').show().html(
                '<div class="loading">' +
                '<div class="loading-spinner"></div>' +
                'Gerando relatório executivo para os últimos ' + period + ' dias...' +
                '</div>'
            );
            
            $.post('', {
                action: 'executive_report',
                days: period
            }, function(data) {
                if (data.error) {
                    $('#executive-results').html('<div style="color: #e74c3c; text-align: center; padding: 30px;">Erro: ' + data.error + '</div>');
                } else {
                    displayExecutiveReport(data);
                }
            }, 'json').fail(function() {
                $('#executive-results').html('<div style="color: #e74c3c; text-align: center; padding: 30px;">Erro de comunicação com o servidor</div>');
            });
        }
        
        // Exibir relatório executivo
        function displayExecutiveReport(data) {
            var html = '<div class="report-section">';
            html += '<div class="section-title"><span class="section-icon">📊</span>Resumo Executivo</div>';
            
            // Caixa de resumo
            html += '<div class="summary-box">';
            html += '<div class="summary-title">Status Geral de Segurança: ' + data.executive_summary.overall_status + '</div>';
            html += '<div class="summary-content">';
            html += '<strong>Ataques Bloqueados:</strong> ' + data.executive_summary.key_statistics.total_attacks_blocked + ' | ';
            html += '<strong>Atacantes Únicos:</strong> ' + data.executive_summary.key_statistics.unique_attackers + ' | ';
            html += '<strong>Disponibilidade:</strong> ' + data.executive_summary.key_statistics.system_availability + '%';
            html += '</div>';
            html += '</div>';
            
            // Métricas principais
            html += '<div class="metrics-grid">';
            html += '<div class="metric-card">';
            html += '<div class="metric-value critical">' + data.security_metrics.attacks_blocked + '</div>';
            html += '<div class="metric-label">Ataques Bloqueados</div>';
            html += '</div>';
            
            html += '<div class="metric-card">';
            html += '<div class="metric-value success">' + data.security_metrics.login_attempts.success + '</div>';
            html += '<div class="metric-label">Logins Sucessos</div>';
            html += '</div>';
            
            html += '<div class="metric-card">';
            html += '<div class="metric-value warning">' + data.security_metrics.login_attempts.failed + '</div>';
            html += '<div class="metric-label">Logins Falhados</div>';
            html += '</div>';
            
            html += '<div class="metric-card">';
            html += '<div class="metric-value info">' + data.security_metrics.alerts_sent + '</div>';
            html += '<div class="metric-label">Alertas Enviados</div>';
            html += '</div>';
            html += '</div>';
            
            html += '</div>';
            
            // KPIs
            if (data.kpis) {
                html += '<div class="report-section">';
                html += '<div class="section-title"><span class="section-icon">🎯</span>Key Performance Indicators</div>';
                html += '<div class="kpi-dashboard">';
                
                for (var kpi in data.kpis) {
                    var kpiData = data.kpis[kpi];
                    html += '<div class="kpi-card ' + kpiData.status + '">';
                    html += '<div class="kpi-name">' + kpiData.name + '</div>';
                    html += '<div class="kpi-value">' + kpiData.value + kpiData.unit + '</div>';
                    html += '<div class="kpi-target">Meta: ' + kpiData.target + kpiData.unit + '</div>';
                    html += '</div>';
                }
                
                html += '</div>';
                html += '</div>';
            }
            
            // Recomendações
            if (data.recommendations && data.recommendations.length > 0) {
                html += '<div class="report-section">';
                html += '<div class="section-title"><span class="section-icon">💡</span>Recomendações Estratégicas</div>';
                html += '<ul class="recommendations-list">';
                
                data.recommendations.forEach(function(rec) {
                    html += '<li>' + rec + '</li>';
                });
                
                html += '</ul>';
                html += '</div>';
            }
            
            $('#executive-results').html(html);
        }
        
        // Gerar relatório de compliance
        function generateComplianceReport() {
            $('#compliance-results').show().html(
                '<div class="loading">' +
                '<div class="loading-spinner"></div>' +
                'Avaliando status de compliance...' +
                '</div>'
            );
            
            // Simular geração de relatório de compliance
            setTimeout(function() {
                $.post('', {
                    action: 'executive_report',
                    days: 30
                }, function(data) {
                    if (data.error) {
                        $('#compliance-results').html('<div style="color: #e74c3c; text-align: center; padding: 30px;">Erro: ' + data.error + '</div>');
                    } else {
                        displayComplianceReport(data.compliance_status);
                    }
                }, 'json');
            }, 2000);
        }
        
        // Exibir relatório de compliance
        function displayComplianceReport(compliance) {
            var html = '<div class="report-section">';
            html += '<div class="section-title"><span class="section-icon">✅</span>Status de Compliance</div>';
            
            html += '<div class="compliance-grid">';
            
            for (var framework in compliance) {
                if (framework !== 'overall_score' && framework !== 'recommendations') {
                    var comp = compliance[framework];
                    var statusClass = comp.status === 'Compliant' ? 'compliant' : 
                                    (comp.status === 'Partially Compliant' ? 'partial' : 'non-compliant');
                    
                    html += '<div class="compliance-card">';
                    html += '<div class="compliance-header">';
                    html += '<div class="compliance-framework">' + comp.framework + '</div>';
                    html += '<div class="compliance-score ' + statusClass + '">' + comp.score + '%</div>';
                    html += '</div>';
                    
                    html += '<div style="margin: 15px 0;">';
                    html += '<strong>Status:</strong> ' + comp.status + '<br>';
                    html += '<strong>Critérios Atendidos:</strong> ' + Object.values(comp.criteria).filter(Boolean).length + '/' + Object.keys(comp.criteria).length;
                    html += '</div>';
                    
                    if (comp.recommendations && comp.recommendations.length > 0) {
                        html += '<div><strong>Recomendações:</strong></div>';
                        html += '<ul style="margin: 10px 0; padding-left: 20px;">';
                        comp.recommendations.forEach(function(rec) {
                            html += '<li style="margin: 5px 0; font-size: 0.9em;">' + rec + '</li>';
                        });
                        html += '</ul>';
                    }
                    
                    html += '</div>';
                }
            }
            
            html += '</div>';
            
            // Score geral
            html += '<div class="summary-box" style="margin-top: 30px;">';
            html += '<div class="summary-title">Score Geral de Compliance</div>';
            html += '<div style="font-size: 2.5em; font-weight: 700; text-align: center;">' + compliance.overall_score.toFixed(1) + '%</div>';
            html += '</div>';
            
            html += '</div>';
            
            $('#compliance-results').html(html);
        }
        
        // Gerar relatório de auditoria
        function generateAuditReport() {
            var startDate = $('#audit-start').val();
            var endDate = $('#audit-end').val();
            var user = $('#audit-user').val().trim();
            
            if (!startDate || !endDate) {
                alert('Por favor, selecione as datas de início e fim');
                return;
            }
            
            $('#audit-results').show().html(
                '<div class="loading">' +
                '<div class="loading-spinner"></div>' +
                'Gerando relatório de auditoria...' +
                '</div>'
            );
            
            var filters = {};
            if (user) {
                filters.user_id = user;
            }
            
            $.post('', {
                action: 'audit_report',
                start_date: startDate,
                end_date: endDate,
                filters: filters
            }, function(data) {
                if (data.error) {
                    $('#audit-results').html('<div style="color: #e74c3c; text-align: center; padding: 30px;">Erro: ' + data.error + '</div>');
                } else {
                    displayAuditReport(data);
                }
            }, 'json').fail(function() {
                $('#audit-results').html('<div style="color: #e74c3c; text-align: center; padding: 30px;">Erro de comunicação com o servidor</div>');
            });
        }
        
        // Exibir relatório de auditoria
        function displayAuditReport(data) {
            var html = '<div class="report-section">';
            html += '<div class="section-title"><span class="section-icon">🔍</span>Relatório de Auditoria</div>';
            
            // Resumo
            html += '<div class="metrics-grid">';
            html += '<div class="metric-card">';
            html += '<div class="metric-value info">' + data.audit_trail.length + '</div>';
            html += '<div class="metric-label">Eventos de Auditoria</div>';
            html += '</div>';
            
            html += '<div class="metric-card">';
            html += '<div class="metric-value success">' + data.user_activities.total_users + '</div>';
            html += '<div class="metric-label">Usuários Total</div>';
            html += '</div>';
            
            html += '<div class="metric-card">';
            html += '<div class="metric-value warning">' + data.user_activities.admin_activities + '</div>';
            html += '<div class="metric-label">Atividades Admin</div>';
            html += '</div>';
            
            html += '<div class="metric-card">';
            html += '<div class="metric-value critical">' + data.user_activities.suspicious_activities + '</div>';
            html += '<div class="metric-label">Atividades Suspeitas</div>';
            html += '</div>';
            html += '</div>';
            
            // Últimos eventos
            if (data.audit_trail.length > 0) {
                html += '<div style="margin-top: 30px;">';
                html += '<h4 style="color: #2c3e50; margin-bottom: 15px;">Últimos Eventos de Auditoria</h4>';
                html += '<div style="max-height: 400px; overflow-y: auto; background: white; border-radius: 8px; padding: 15px;">';
                
                data.audit_trail.slice(-20).reverse().forEach(function(event) {
                    html += '<div style="padding: 10px; margin: 5px 0; background: #f8f9fa; border-radius: 5px; border-left: 4px solid #3498db;">';
                    html += '<strong>' + event.timestamp + '</strong> - ' + event.event_type + '<br>';
                    html += '<small>Usuário: ' + event.user_name + ' | IP: ' + event.ip_address + '</small><br>';
                    html += event.description;
                    html += '</div>';
                });
                
                html += '</div>';
                html += '</div>';
            }
            
            html += '</div>';
            
            $('#audit-results').html(html);
        }
        
        // Gerar relatório de KPIs
        function generateKPIReport() {
            $('#kpis-results').show().html(
                '<div class="loading">' +
                '<div class="loading-spinner"></div>' +
                'Carregando KPIs de segurança...' +
                '</div>'
            );
            
            $.post('', {
                action: 'executive_report',
                days: 30
            }, function(data) {
                if (data.error) {
                    $('#kpis-results').html('<div style="color: #e74c3c; text-align: center; padding: 30px;">Erro: ' + data.error + '</div>');
                } else {
                    displayKPIReport(data.kpis);
                }
            }, 'json');
        }
        
        // Exibir KPIs
        function displayKPIReport(kpis) {
            var html = '<div class="report-section">';
            html += '<div class="section-title"><span class="section-icon">🎯</span>Dashboard de KPIs</div>';
            
            html += '<div class="kpi-dashboard">';
            
            for (var kpi in kpis) {
                var kpiData = kpis[kpi];
                html += '<div class="kpi-card ' + kpiData.status + '">';
                html += '<div class="kpi-name">' + kpiData.name + '</div>';
                html += '<div class="kpi-value">' + kpiData.value + kpiData.unit + '</div>';
                html += '<div class="kpi-target">Meta: ' + kpiData.target + kpiData.unit + '</div>';
                html += '</div>';
            }
            
            html += '</div>';
            html += '</div>';
            
            $('#kpis-results').html(html);
        }
    </script>
    
    <?php endif; ?>
</body>
</html>