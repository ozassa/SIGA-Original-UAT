<?php
/**
 * SIGA Security Forensics System
 * 
 * Sistema de Análise Forense para Investigação de Incidentes de Segurança
 * Correlação de eventos, análise de padrões, geolocalização, timeline detalhada
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
    exit('Acesso negado. Apenas administradores podem acessar a Análise Forense.');
}

/**
 * Classe principal do Sistema de Análise Forense
 */
class SecurityForensicsSystem {
    
    private $log_directories = [
        'security' => __DIR__ . '/logs/security',
        'validation' => __DIR__ . '/logs/validation',
        'authentication' => __DIR__ . '/logs/authentication',
        'attacks' => __DIR__ . '/logs/attacks',
        'alerts' => __DIR__ . '/logs/alerts'
    ];
    
    private $geoip_cache = [];
    
    public function __construct() {
        $this->ensureLogDirectories();
        $this->loadGeoIPCache();
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
     * Análise detalhada por IP
     */
    public function analyzeIP($ip, $days = 30) {
        $analysis = [
            'ip' => $ip,
            'analysis_period' => $days . ' dias',
            'timeline' => [],
            'attack_summary' => [],
            'user_agents' => [],
            'urls_targeted' => [],
            'session_analysis' => [],
            'geolocation' => $this->getIPGeolocation($ip),
            'threat_assessment' => '',
            'recommendations' => []
        ];
        
        // Analisar logs de diferentes períodos
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $this->analyzeIPForDate($ip, $date, $analysis);
        }
        
        // Processar dados coletados
        $this->processIPAnalysis($analysis);
        
        return $analysis;
    }
    
    /**
     * Analisar IP para uma data específica
     */
    private function analyzeIPForDate($ip, $date, &$analysis) {
        foreach ($this->log_directories as $type => $dir) {
            $log_file = $dir . '/' . $type . '_' . $date . '.log';
            
            if (file_exists($log_file)) {
                $lines = file($log_file, FILE_IGNORE_NEW_LINES);
                
                foreach ($lines as $line) {
                    $data = json_decode($line, true);
                    
                    if ($data && isset($data['ip']) && $data['ip'] === $ip) {
                        // Adicionar à timeline
                        $analysis['timeline'][] = [
                            'timestamp' => $data['timestamp'],
                            'type' => $data['type'] ?? $type,
                            'data' => $data,
                            'log_source' => $type
                        ];
                        
                        // Coletar User Agents
                        if (isset($data['user_agent']) && !in_array($data['user_agent'], $analysis['user_agents'])) {
                            $analysis['user_agents'][] = $data['user_agent'];
                        }
                        
                        // Coletar URLs alvo
                        if (isset($data['url']) && !in_array($data['url'], $analysis['urls_targeted'])) {
                            $analysis['urls_targeted'][] = $data['url'];
                        }
                        
                        // Resumo de ataques
                        $attack_type = $data['type'] ?? 'unknown';
                        if (!isset($analysis['attack_summary'][$attack_type])) {
                            $analysis['attack_summary'][$attack_type] = 0;
                        }
                        $analysis['attack_summary'][$attack_type]++;
                    }
                }
            }
        }
    }
    
    /**
     * Processar análise do IP
     */
    private function processIPAnalysis(&$analysis) {
        // Ordenar timeline por timestamp
        usort($analysis['timeline'], function($a, $b) {
            return strtotime($a['timestamp']) - strtotime($b['timestamp']);
        });
        
        // Avaliar nível de ameaça
        $total_attacks = array_sum($analysis['attack_summary']);
        $attack_types = count($analysis['attack_summary']);
        $user_agents_count = count($analysis['user_agents']);
        
        if ($total_attacks > 100 || $attack_types > 5) {
            $analysis['threat_assessment'] = 'CRÍTICO';
        } elseif ($total_attacks > 50 || $attack_types > 3) {
            $analysis['threat_assessment'] = 'ALTO';
        } elseif ($total_attacks > 10 || $attack_types > 1) {
            $analysis['threat_assessment'] = 'MÉDIO';
        } else {
            $analysis['threat_assessment'] = 'BAIXO';
        }
        
        // Gerar recomendações
        $analysis['recommendations'] = $this->generateIPRecommendations($analysis);
    }
    
    /**
     * Análise de correlação de eventos
     */
    public function analyzeEventCorrelation($hours = 24) {
        $correlations = [
            'time_patterns' => [],
            'ip_clusters' => [],
            'attack_sequences' => [],
            'user_behavior_anomalies' => [],
            'geographic_patterns' => []
        ];
        
        $events = $this->collectEventsFromPeriod($hours);
        
        // Analisar padrões temporais
        $correlations['time_patterns'] = $this->analyzeTimePatterns($events);
        
        // Analisar clusters de IPs
        $correlations['ip_clusters'] = $this->analyzeIPClusters($events);
        
        // Analisar sequências de ataque
        $correlations['attack_sequences'] = $this->analyzeAttackSequences($events);
        
        // Analisar anomalias de comportamento
        $correlations['user_behavior_anomalies'] = $this->analyzeUserBehaviorAnomalies($events);
        
        // Analisar padrões geográficos
        $correlations['geographic_patterns'] = $this->analyzeGeographicPatterns($events);
        
        return $correlations;
    }
    
    /**
     * Coletar eventos de um período
     */
    private function collectEventsFromPeriod($hours) {
        $events = [];
        $end_time = time();
        $start_time = $end_time - ($hours * 3600);
        
        // Coletar de todos os logs
        for ($timestamp = $start_time; $timestamp <= $end_time; $timestamp += 86400) {
            $date = date('Y-m-d', $timestamp);
            
            foreach ($this->log_directories as $type => $dir) {
                $log_file = $dir . '/' . $type . '_' . $date . '.log';
                
                if (file_exists($log_file)) {
                    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
                    
                    foreach ($lines as $line) {
                        $data = json_decode($line, true);
                        
                        if ($data && isset($data['timestamp'])) {
                            $event_time = strtotime($data['timestamp']);
                            
                            if ($event_time >= $start_time && $event_time <= $end_time) {
                                $data['log_source'] = $type;
                                $events[] = $data;
                            }
                        }
                    }
                }
            }
        }
        
        // Ordenar por timestamp
        usort($events, function($a, $b) {
            return strtotime($a['timestamp']) - strtotime($b['timestamp']);
        });
        
        return $events;
    }
    
    /**
     * Analisar padrões temporais
     */
    private function analyzeTimePatterns($events) {
        $patterns = [
            'hourly_distribution' => array_fill(0, 24, 0),
            'daily_distribution' => [],
            'peak_hours' => [],
            'suspicious_patterns' => []
        ];
        
        foreach ($events as $event) {
            $hour = (int)date('H', strtotime($event['timestamp']));
            $day = date('Y-m-d', strtotime($event['timestamp']));
            
            $patterns['hourly_distribution'][$hour]++;
            
            if (!isset($patterns['daily_distribution'][$day])) {
                $patterns['daily_distribution'][$day] = 0;
            }
            $patterns['daily_distribution'][$day]++;
        }
        
        // Identificar horários de pico
        $max_hourly = max($patterns['hourly_distribution']);
        for ($i = 0; $i < 24; $i++) {
            if ($patterns['hourly_distribution'][$i] > $max_hourly * 0.8) {
                $patterns['peak_hours'][] = sprintf('%02d:00-%02d:59', $i, $i);
            }
        }
        
        // Detectar padrões suspeitos (atividade muito alta em horários incomuns)
        for ($i = 0; $i < 24; $i++) {
            if (($i >= 2 && $i <= 5) && $patterns['hourly_distribution'][$i] > $max_hourly * 0.3) {
                $patterns['suspicious_patterns'][] = [
                    'type' => 'unusual_night_activity',
                    'hour' => $i,
                    'count' => $patterns['hourly_distribution'][$i],
                    'description' => 'Atividade alta em horário noturno suspeito'
                ];
            }
        }
        
        return $patterns;
    }
    
    /**
     * Analisar clusters de IPs
     */
    private function analyzeIPClusters($events) {
        $ip_data = [];
        
        foreach ($events as $event) {
            $ip = $event['ip'] ?? 'unknown';
            
            if (!isset($ip_data[$ip])) {
                $ip_data[$ip] = [
                    'count' => 0,
                    'types' => [],
                    'first_seen' => $event['timestamp'],
                    'last_seen' => $event['timestamp'],
                    'user_agents' => []
                ];
            }
            
            $ip_data[$ip]['count']++;
            $ip_data[$ip]['last_seen'] = $event['timestamp'];
            
            $type = $event['type'] ?? 'unknown';
            if (!in_array($type, $ip_data[$ip]['types'])) {
                $ip_data[$ip]['types'][] = $type;
            }
            
            $ua = $event['user_agent'] ?? 'unknown';
            if (!in_array($ua, $ip_data[$ip]['user_agents'])) {
                $ip_data[$ip]['user_agents'][] = $ua;
            }
        }
        
        // Identificar clusters suspeitos
        $clusters = [];
        
        // Cluster 1: IPs com muitos tipos de ataque
        foreach ($ip_data as $ip => $data) {
            if (count($data['types']) > 3) {
                $clusters[] = [
                    'type' => 'multi_attack_ip',
                    'ip' => $ip,
                    'risk_level' => 'high',
                    'description' => 'IP com múltiplos tipos de ataque',
                    'details' => $data
                ];
            }
        }
        
        // Cluster 2: IPs com atividade muito alta
        $avg_activity = array_sum(array_column($ip_data, 'count')) / count($ip_data);
        foreach ($ip_data as $ip => $data) {
            if ($data['count'] > $avg_activity * 5) {
                $clusters[] = [
                    'type' => 'high_volume_ip',
                    'ip' => $ip,
                    'risk_level' => 'medium',
                    'description' => 'IP com volume de atividade muito alto',
                    'details' => $data
                ];
            }
        }
        
        return $clusters;
    }
    
    /**
     * Analisar sequências de ataque
     */
    private function analyzeAttackSequences($events) {
        $sequences = [];
        $current_sequence = [];
        $last_ip = null;
        $last_time = 0;
        
        foreach ($events as $event) {
            $ip = $event['ip'] ?? 'unknown';
            $time = strtotime($event['timestamp']);
            $type = $event['type'] ?? 'unknown';
            
            // Considerar sequência se mesmo IP em até 5 minutos
            if ($ip === $last_ip && ($time - $last_time) <= 300) {
                $current_sequence[] = [
                    'type' => $type,
                    'timestamp' => $event['timestamp'],
                    'data' => $event
                ];
            } else {
                // Finalizar sequência anterior se houver
                if (count($current_sequence) >= 3) {
                    $sequences[] = [
                        'ip' => $last_ip,
                        'sequence_length' => count($current_sequence),
                        'duration' => $last_time - strtotime($current_sequence[0]['timestamp']),
                        'events' => $current_sequence,
                        'risk_assessment' => $this->assessSequenceRisk($current_sequence)
                    ];
                }
                
                // Iniciar nova sequência
                $current_sequence = [[
                    'type' => $type,
                    'timestamp' => $event['timestamp'],
                    'data' => $event
                ]];
            }
            
            $last_ip = $ip;
            $last_time = $time;
        }
        
        // Processar última sequência
        if (count($current_sequence) >= 3) {
            $sequences[] = [
                'ip' => $last_ip,
                'sequence_length' => count($current_sequence),
                'duration' => $last_time - strtotime($current_sequence[0]['timestamp']),
                'events' => $current_sequence,
                'risk_assessment' => $this->assessSequenceRisk($current_sequence)
            ];
        }
        
        // Ordenar por risco
        usort($sequences, function($a, $b) {
            $risk_order = ['critical' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
            return ($risk_order[$b['risk_assessment']] ?? 0) - ($risk_order[$a['risk_assessment']] ?? 0);
        });
        
        return $sequences;
    }
    
    /**
     * Avaliar risco de sequência
     */
    private function assessSequenceRisk($sequence) {
        $length = count($sequence);
        $types = array_unique(array_column($sequence, 'type'));
        $type_count = count($types);
        
        // Verificar se há escalation de ataques
        $critical_types = ['SQL_INJECTION', 'SYSTEM_COMPROMISE', 'BRUTE_FORCE'];
        $has_critical = !empty(array_intersect($types, $critical_types));
        
        if ($has_critical && $type_count > 2 && $length > 10) {
            return 'critical';
        } elseif ($type_count > 3 || $length > 15) {
            return 'high';
        } elseif ($type_count > 1 || $length > 5) {
            return 'medium';
        } else {
            return 'low';
        }
    }
    
    /**
     * Analisar anomalias de comportamento de usuário
     */
    private function analyzeUserBehaviorAnomalies($events) {
        $user_behavior = [];
        $anomalies = [];
        
        // Coletar dados de comportamento por usuário
        foreach ($events as $event) {
            $user_id = $event['user_id'] ?? 'anonymous';
            $ip = $event['ip'] ?? 'unknown';
            
            if (!isset($user_behavior[$user_id])) {
                $user_behavior[$user_id] = [
                    'ips' => [],
                    'sessions' => [],
                    'activities' => [],
                    'first_seen' => $event['timestamp'],
                    'last_seen' => $event['timestamp']
                ];
            }
            
            if (!in_array($ip, $user_behavior[$user_id]['ips'])) {
                $user_behavior[$user_id]['ips'][] = $ip;
            }
            
            $user_behavior[$user_id]['activities'][] = $event;
            $user_behavior[$user_id]['last_seen'] = $event['timestamp'];
        }
        
        // Detectar anomalias
        foreach ($user_behavior as $user_id => $data) {
            // Múltiplos IPs para mesmo usuário
            if (count($data['ips']) > 3) {
                $anomalies[] = [
                    'type' => 'multiple_ips',
                    'user_id' => $user_id,
                    'severity' => 'medium',
                    'description' => 'Usuário acessando de múltiplos IPs',
                    'details' => [
                        'ip_count' => count($data['ips']),
                        'ips' => $data['ips']
                    ]
                ];
            }
            
            // Atividade suspeita (muitas ações em pouco tempo)
            $activity_count = count($data['activities']);
            $time_span = strtotime($data['last_seen']) - strtotime($data['first_seen']);
            
            if ($activity_count > 50 && $time_span < 3600) { // 50 ações em 1 hora
                $anomalies[] = [
                    'type' => 'high_activity_rate',
                    'user_id' => $user_id,
                    'severity' => 'high',
                    'description' => 'Taxa de atividade suspeita',
                    'details' => [
                        'activity_count' => $activity_count,
                        'time_span_minutes' => round($time_span / 60),
                        'rate_per_minute' => round($activity_count / ($time_span / 60))
                    ]
                ];
            }
        }
        
        return $anomalies;
    }
    
    /**
     * Analisar padrões geográficos
     */
    private function analyzeGeographicPatterns($events) {
        $geo_data = [];
        
        foreach ($events as $event) {
            $ip = $event['ip'] ?? 'unknown';
            $geo = $this->getIPGeolocation($ip);
            
            $country = $geo['country'] ?? 'Unknown';
            
            if (!isset($geo_data[$country])) {
                $geo_data[$country] = [
                    'count' => 0,
                    'ips' => [],
                    'attack_types' => []
                ];
            }
            
            $geo_data[$country]['count']++;
            
            if (!in_array($ip, $geo_data[$country]['ips'])) {
                $geo_data[$country]['ips'][] = $ip;
            }
            
            $type = $event['type'] ?? 'unknown';
            if (!in_array($type, $geo_data[$country]['attack_types'])) {
                $geo_data[$country]['attack_types'][] = $type;
            }
        }
        
        // Ordenar por volume de ataques
        arsort($geo_data);
        
        return array_slice($geo_data, 0, 10); // Top 10 países
    }
    
    /**
     * Gerar timeline detalhada de incidente
     */
    public function generateIncidentTimeline($start_time, $end_time, $filters = []) {
        $timeline = [];
        
        $start_timestamp = strtotime($start_time);
        $end_timestamp = strtotime($end_time);
        
        // Coletar eventos no período
        for ($timestamp = $start_timestamp; $timestamp <= $end_timestamp; $timestamp += 86400) {
            $date = date('Y-m-d', $timestamp);
            
            foreach ($this->log_directories as $type => $dir) {
                $log_file = $dir . '/' . $type . '_' . $date . '.log';
                
                if (file_exists($log_file)) {
                    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
                    
                    foreach ($lines as $line) {
                        $data = json_decode($line, true);
                        
                        if ($data && isset($data['timestamp'])) {
                            $event_time = strtotime($data['timestamp']);
                            
                            if ($event_time >= $start_timestamp && $event_time <= $end_timestamp) {
                                // Aplicar filtros
                                if ($this->eventMatchesFilters($data, $filters)) {
                                    $timeline[] = [
                                        'timestamp' => $data['timestamp'],
                                        'unix_timestamp' => $event_time,
                                        'type' => $data['type'] ?? $type,
                                        'severity' => $this->getEventSeverity($data),
                                        'source' => $type,
                                        'ip' => $data['ip'] ?? 'unknown',
                                        'user_id' => $data['user_id'] ?? 'anonymous',
                                        'description' => $this->getEventDescription($data),
                                        'data' => $data
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // Ordenar por timestamp
        usort($timeline, function($a, $b) {
            return $a['unix_timestamp'] - $b['unix_timestamp'];
        });
        
        return $timeline;
    }
    
    /**
     * Verificar se evento corresponde aos filtros
     */
    private function eventMatchesFilters($event, $filters) {
        if (empty($filters)) {
            return true;
        }
        
        foreach ($filters as $key => $value) {
            if (isset($event[$key]) && $event[$key] !== $value) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Obter severidade do evento
     */
    private function getEventSeverity($event) {
        $type = $event['type'] ?? 'unknown';
        
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
    
    /**
     * Obter descrição do evento
     */
    private function getEventDescription($event) {
        $type = $event['type'] ?? 'unknown';
        $ip = $event['ip'] ?? 'unknown';
        
        $descriptions = [
            'SQL_INJECTION' => "Tentativa de SQL Injection de $ip",
            'XSS_ATTEMPT' => "Tentativa de XSS de $ip",
            'CSRF' => "Violação CSRF de $ip",
            'BRUTE_FORCE' => "Ataque de força bruta de $ip",
            'FILE_UPLOAD_BLOCKED' => "Upload malicioso bloqueado de $ip",
            'VALIDATION_FAILED' => "Falha na validação de $ip"
        ];
        
        return $descriptions[$type] ?? "Evento de segurança de $ip";
    }
    
    /**
     * Geolocalização de IP (simulada)
     */
    private function getIPGeolocation($ip) {
        if (isset($this->geoip_cache[$ip])) {
            return $this->geoip_cache[$ip];
        }
        
        // Simular geolocalização
        if ($ip === '127.0.0.1' || $ip === 'localhost') {
            $geo = ['country' => 'Local', 'city' => 'Localhost', 'region' => 'Local'];
        } else {
            // Em produção, usar serviço real de geolocalização
            $countries = ['Brasil', 'EUA', 'China', 'Rússia', 'França', 'Alemanha', 'Reino Unido'];
            $geo = [
                'country' => $countries[array_rand($countries)],
                'city' => 'Unknown',
                'region' => 'Unknown'
            ];
        }
        
        $this->geoip_cache[$ip] = $geo;
        return $geo;
    }
    
    /**
     * Carregar cache de geolocalização
     */
    private function loadGeoIPCache() {
        $cache_file = __DIR__ . '/logs/geoip_cache.json';
        if (file_exists($cache_file)) {
            $this->geoip_cache = json_decode(file_get_contents($cache_file), true) ?: [];
        }
    }
    
    /**
     * Salvar cache de geolocalização
     */
    private function saveGeoIPCache() {
        $cache_file = __DIR__ . '/logs/geoip_cache.json';
        file_put_contents($cache_file, json_encode($this->geoip_cache), LOCK_EX);
    }
    
    /**
     * Gerar recomendações para IP
     */
    private function generateIPRecommendations($analysis) {
        $recommendations = [];
        
        $threat_level = $analysis['threat_assessment'];
        $total_attacks = array_sum($analysis['attack_summary']);
        
        if ($threat_level === 'CRÍTICO') {
            $recommendations[] = 'BLOQUEIO IMEDIATO: Este IP representa ameaça crítica';
            $recommendations[] = 'Escalar para equipe de resposta a incidentes';
            $recommendations[] = 'Preservar evidências para análise forense';
        } elseif ($threat_level === 'ALTO') {
            $recommendations[] = 'Considerar bloqueio temporário do IP';
            $recommendations[] = 'Monitoramento reforçado das atividades';
            $recommendations[] = 'Analisar padrões de ataque para detecção';
        } elseif ($threat_level === 'MÉDIO') {
            $recommendations[] = 'Implementar rate limiting mais restritivo';
            $recommendations[] = 'Monitorar comportamento futuro';
            $recommendations[] = 'Revisar logs de atividade regularmente';
        } else {
            $recommendations[] = 'Manter monitoramento padrão';
            $recommendations[] = 'Considerar apenas se houver escalation';
        }
        
        return $recommendations;
    }
    
    /**
     * Exportar dados para formato SIEM
     */
    public function exportToSIEM($format = 'json', $start_date = null, $end_date = null) {
        $start_date = $start_date ?: date('Y-m-d', strtotime('-7 days'));
        $end_date = $end_date ?: date('Y-m-d');
        
        $events = [];
        
        $current_date = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
        
        while ($current_date <= $end_timestamp) {
            $date = date('Y-m-d', $current_date);
            
            foreach ($this->log_directories as $type => $dir) {
                $log_file = $dir . '/' . $type . '_' . $date . '.log';
                
                if (file_exists($log_file)) {
                    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
                    
                    foreach ($lines as $line) {
                        $data = json_decode($line, true);
                        if ($data) {
                            $events[] = $this->formatForSIEM($data, $type);
                        }
                    }
                }
            }
            
            $current_date += 86400; // Próximo dia
        }
        
        switch ($format) {
            case 'csv':
                return $this->exportToCSV($events);
            case 'xml':
                return $this->exportToXML($events);
            case 'json':
            default:
                return json_encode($events, JSON_PRETTY_PRINT);
        }
    }
    
    /**
     * Formatar evento para SIEM
     */
    private function formatForSIEM($event, $source) {
        return [
            'timestamp' => $event['timestamp'],
            'source' => 'SIGA_' . strtoupper($source),
            'severity' => $this->getEventSeverity($event),
            'event_type' => $event['type'] ?? 'unknown',
            'source_ip' => $event['ip'] ?? 'unknown',
            'user_id' => $event['user_id'] ?? 'anonymous',
            'description' => $this->getEventDescription($event),
            'raw_data' => json_encode($event)
        ];
    }
    
    /**
     * Exportar para CSV
     */
    private function exportToCSV($events) {
        $csv = "timestamp,source,severity,event_type,source_ip,user_id,description\n";
        
        foreach ($events as $event) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $event['timestamp'],
                $event['source'],
                $event['severity'],
                $event['event_type'],
                $event['source_ip'],
                $event['user_id'],
                str_replace('"', '""', $event['description'])
            );
        }
        
        return $csv;
    }
    
    /**
     * Exportar para XML
     */
    private function exportToXML($events) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<security_events>' . "\n";
        
        foreach ($events as $event) {
            $xml .= '  <event>' . "\n";
            foreach ($event as $key => $value) {
                if ($key !== 'raw_data') {
                    $xml .= '    <' . $key . '>' . htmlspecialchars($value) . '</' . $key . '>' . "\n";
                }
            }
            $xml .= '  </event>' . "\n";
        }
        
        $xml .= '</security_events>' . "\n";
        
        return $xml;
    }
}

// Instanciar sistema forense
$forensics = new SecurityForensicsSystem();

// Processar requisições AJAX
if (isset($_POST['action']) && validate_user_session() && $_SESSION['pefil'] === 'F') {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'analyze_ip':
            $ip = InputValidator::validate($_POST['ip'] ?? '', ['type' => 'ip']);
            $days = InputValidator::validate($_POST['days'] ?? 30, ['type' => 'int', 'min_length' => 1]);
            
            if ($ip) {
                echo json_encode($forensics->analyzeIP($ip, $days));
            } else {
                echo json_encode(['error' => 'IP inválido']);
            }
            break;
            
        case 'correlation_analysis':
            $hours = InputValidator::validate($_POST['hours'] ?? 24, ['type' => 'int']);
            echo json_encode($forensics->analyzeEventCorrelation($hours));
            break;
            
        case 'incident_timeline':
            $start = InputValidator::validate($_POST['start_time'] ?? '', ['type' => 'datetime']);
            $end = InputValidator::validate($_POST['end_time'] ?? '', ['type' => 'datetime']);
            $filters = $_POST['filters'] ?? [];
            
            if ($start && $end) {
                echo json_encode($forensics->generateIncidentTimeline($start, $end, $filters));
            } else {
                echo json_encode(['error' => 'Datas inválidas']);
            }
            break;
            
        case 'export_siem':
            $format = InputValidator::validate($_POST['format'] ?? 'json', ['type' => 'string', 'whitelist' => ['json', 'csv', 'xml']]);
            $start_date = InputValidator::validate($_POST['start_date'] ?? '', ['type' => 'date']);
            $end_date = InputValidator::validate($_POST['end_date'] ?? '', ['type' => 'date']);
            
            $export_data = $forensics->exportToSIEM($format, $start_date, $end_date);
            
            // Definir headers apropriados
            if ($format === 'csv') {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="siga_security_export.csv"');
            } elseif ($format === 'xml') {
                header('Content-Type: text/xml');
                header('Content-Disposition: attachment; filename="siga_security_export.xml"');
            } else {
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="siga_security_export.json"');
            }
            
            echo $export_data;
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
    <title>SIGA - Análise Forense de Segurança</title>
    
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/geral.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="Scripts/jquery.js"></script>
    
    <style>
        body {
            background: #1a1a2e;
            color: #ffffff;
            font-family: 'Courier New', monospace;
        }
        
        .forensics-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .forensics-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 30px;
            background: linear-gradient(45deg, #1a1a2e, #16213e);
            border-radius: 10px;
            border: 2px solid #00ff41;
        }
        
        .forensics-title {
            font-size: 2.5em;
            color: #00ff41;
            text-shadow: 0 0 10px #00ff41;
            margin-bottom: 10px;
        }
        
        .analysis-tabs {
            display: flex;
            margin-bottom: 30px;
            background: #16213e;
            border-radius: 10px;
            padding: 5px;
        }
        
        .tab-button {
            flex: 1;
            padding: 15px;
            background: transparent;
            border: none;
            color: #ffffff;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .tab-button.active {
            background: #00ff41;
            color: #1a1a2e;
            font-weight: bold;
        }
        
        .tab-button:hover {
            background: rgba(0, 255, 65, 0.1);
        }
        
        .tab-content {
            display: none;
            background: #16213e;
            border-radius: 10px;
            padding: 25px;
            border: 1px solid #333;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .analysis-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #1a1a2e;
            border-radius: 8px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            color: #00ff41;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .form-input {
            padding: 10px;
            background: #0f1419;
            color: #ffffff;
            border: 1px solid #333;
            border-radius: 5px;
        }
        
        .form-input:focus {
            border-color: #00ff41;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 255, 65, 0.3);
        }
        
        .analyze-button {
            padding: 12px 25px;
            background: #00ff41;
            color: #1a1a2e;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .analyze-button:hover {
            background: #00cc33;
            transform: translateY(-2px);
        }
        
        .results-panel {
            background: #1a1a2e;
            border-radius: 10px;
            padding: 25px;
            margin-top: 20px;
            border: 1px solid #333;
        }
        
        .results-title {
            color: #00ff41;
            font-size: 1.3em;
            margin-bottom: 20px;
            border-bottom: 2px solid #00ff41;
            padding-bottom: 10px;
        }
        
        .ip-analysis-card {
            background: #0f1419;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #00ff41;
        }
        
        .threat-level {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .threat-level.CRÍTICO {
            background: #ff4444;
            color: white;
        }
        
        .threat-level.ALTO {
            background: #ffaa00;
            color: white;
        }
        
        .threat-level.MÉDIO {
            background: #44aaff;
            color: white;
        }
        
        .threat-level.BAIXO {
            background: #00ff41;
            color: #1a1a2e;
        }
        
        .attack-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        
        .attack-type-card {
            background: #16213e;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        
        .attack-count {
            font-size: 2em;
            color: #ff4444;
            font-weight: bold;
        }
        
        .timeline-container {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
        }
        
        .timeline-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 5px;
            background: #0f1419;
            border-radius: 5px;
            border-left: 4px solid #333;
        }
        
        .timeline-item.critical { border-left-color: #ff4444; }
        .timeline-item.high { border-left-color: #ffaa00; }
        .timeline-item.medium { border-left-color: #44aaff; }
        .timeline-item.low { border-left-color: #00ff41; }
        
        .timeline-time {
            color: #666;
            margin-right: 15px;
            min-width: 120px;
            font-size: 0.9em;
        }
        
        .timeline-type {
            color: #00ff41;
            margin-right: 15px;
            min-width: 120px;
            font-weight: bold;
        }
        
        .correlation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .correlation-card {
            background: #0f1419;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #00ff41;
        }
        
        .correlation-title {
            color: #00ff41;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .export-panel {
            background: #1a1a2e;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .export-buttons {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .export-button {
            padding: 10px 20px;
            background: #16213e;
            color: #ffffff;
            border: 1px solid #00ff41;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .export-button:hover {
            background: #00ff41;
            color: #1a1a2e;
        }
        
        .loading {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #333;
            border-top: 2px solid #00ff41;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <?php if (!validate_user_session() || $_SESSION['pefil'] !== 'F'): ?>
        <div style="text-align: center; padding: 50px; color: #ff4444;">
            <h2>Acesso Negado</h2>
            <p>Apenas administradores podem acessar a Análise Forense de Segurança.</p>
        </div>
    <?php else: ?>
    
    <div class="forensics-container">
        <div class="forensics-header">
            <h1 class="forensics-title">ANÁLISE FORENSE DE SEGURANÇA</h1>
            <p>Investigação avançada de incidentes e correlação de eventos</p>
        </div>
        
        <!-- Abas de análise -->
        <div class="analysis-tabs">
            <button class="tab-button active" onclick="switchTab('ip-analysis')">Análise por IP</button>
            <button class="tab-button" onclick="switchTab('correlation')">Correlação de Eventos</button>
            <button class="tab-button" onclick="switchTab('timeline')">Timeline de Incidentes</button>
            <button class="tab-button" onclick="switchTab('export')">Exportação SIEM</button>
        </div>
        
        <!-- Aba: Análise por IP -->
        <div id="ip-analysis" class="tab-content active">
            <div class="analysis-form">
                <div class="form-group">
                    <label class="form-label">Endereço IP:</label>
                    <input type="text" id="ip-input" class="form-input" placeholder="Ex: 192.168.1.1">
                </div>
                <div class="form-group">
                    <label class="form-label">Período (dias):</label>
                    <input type="number" id="ip-days" class="form-input" value="30" min="1" max="365">
                </div>
                <div class="form-group">
                    <button class="analyze-button" onclick="analyzeIP()">Analisar IP</button>
                </div>
            </div>
            
            <div id="ip-results" class="results-panel" style="display: none;">
                <!-- Resultados da análise de IP serão inseridos aqui -->
            </div>
        </div>
        
        <!-- Aba: Correlação de Eventos -->
        <div id="correlation" class="tab-content">
            <div class="analysis-form">
                <div class="form-group">
                    <label class="form-label">Período de análise (horas):</label>
                    <input type="number" id="correlation-hours" class="form-input" value="24" min="1" max="168">
                </div>
                <div class="form-group">
                    <button class="analyze-button" onclick="analyzeCorrelation()">Analisar Correlações</button>
                </div>
            </div>
            
            <div id="correlation-results" class="results-panel" style="display: none;">
                <!-- Resultados da correlação serão inseridos aqui -->
            </div>
        </div>
        
        <!-- Aba: Timeline de Incidentes -->
        <div id="timeline" class="tab-content">
            <div class="analysis-form">
                <div class="form-group">
                    <label class="form-label">Data/Hora Início:</label>
                    <input type="datetime-local" id="timeline-start" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Data/Hora Fim:</label>
                    <input type="datetime-local" id="timeline-end" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Filtrar por IP (opcional):</label>
                    <input type="text" id="timeline-ip" class="form-input" placeholder="Ex: 192.168.1.1">
                </div>
                <div class="form-group">
                    <button class="analyze-button" onclick="generateTimeline()">Gerar Timeline</button>
                </div>
            </div>
            
            <div id="timeline-results" class="results-panel" style="display: none;">
                <!-- Timeline será inserida aqui -->
            </div>
        </div>
        
        <!-- Aba: Exportação SIEM -->
        <div id="export" class="tab-content">
            <div class="analysis-form">
                <div class="form-group">
                    <label class="form-label">Data Início:</label>
                    <input type="date" id="export-start" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Data Fim:</label>
                    <input type="date" id="export-end" class="form-input">
                </div>
            </div>
            
            <div class="export-panel">
                <h3 style="color: #00ff41; margin-bottom: 15px;">Exportar Dados para SIEM</h3>
                <p>Exporte os dados de segurança em diferentes formatos para integração com sistemas SIEM externos.</p>
                
                <div class="export-buttons">
                    <button class="export-button" onclick="exportSIEM('json')">Exportar JSON</button>
                    <button class="export-button" onclick="exportSIEM('csv')">Exportar CSV</button>
                    <button class="export-button" onclick="exportSIEM('xml')">Exportar XML</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Inicializar datas padrão
        $(document).ready(function() {
            var now = new Date();
            var yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            
            // Timeline
            $('#timeline-start').val(formatDateTimeLocal(yesterday));
            $('#timeline-end').val(formatDateTimeLocal(now));
            
            // Export
            var weekAgo = new Date(now);
            weekAgo.setDate(weekAgo.getDate() - 7);
            $('#export-start').val(formatDate(weekAgo));
            $('#export-end').val(formatDate(now));
        });
        
        // Utilitários de data
        function formatDateTimeLocal(date) {
            return date.getFullYear() + '-' + 
                   String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                   String(date.getDate()).padStart(2, '0') + 'T' + 
                   String(date.getHours()).padStart(2, '0') + ':' + 
                   String(date.getMinutes()).padStart(2, '0');
        }
        
        function formatDate(date) {
            return date.getFullYear() + '-' + 
                   String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                   String(date.getDate()).padStart(2, '0');
        }
        
        // Alternar abas
        function switchTab(tabName) {
            $('.tab-content').removeClass('active');
            $('.tab-button').removeClass('active');
            
            $('#' + tabName).addClass('active');
            $('[onclick="switchTab(\'' + tabName + '\')"]').addClass('active');
        }
        
        // Analisar IP
        function analyzeIP() {
            var ip = $('#ip-input').val().trim();
            var days = $('#ip-days').val();
            
            if (!ip) {
                alert('Por favor, insira um endereço IP');
                return;
            }
            
            $('#ip-results').show().html('<div class="loading">Analisando IP ' + ip + '...</div>');
            
            $.post('', {
                action: 'analyze_ip',
                ip: ip,
                days: days
            }, function(data) {
                if (data.error) {
                    $('#ip-results').html('<div style="color: #ff4444;">Erro: ' + data.error + '</div>');
                } else {
                    displayIPAnalysis(data);
                }
            }, 'json').fail(function() {
                $('#ip-results').html('<div style="color: #ff4444;">Erro de comunicação com o servidor</div>');
            });
        }
        
        // Exibir análise de IP
        function displayIPAnalysis(data) {
            var html = '<div class="results-title">Análise Detalhada do IP: ' + data.ip + '</div>';
            
            html += '<div class="ip-analysis-card">';
            html += '<div class="threat-level ' + data.threat_assessment + '">' + data.threat_assessment + '</div>';
            
            html += '<h4 style="color: #00ff41; margin: 15px 0;">Resumo dos Ataques</h4>';
            html += '<div class="attack-summary">';
            
            for (var type in data.attack_summary) {
                html += '<div class="attack-type-card">';
                html += '<div class="attack-count">' + data.attack_summary[type] + '</div>';
                html += '<div>' + type.replace(/_/g, ' ') + '</div>';
                html += '</div>';
            }
            
            html += '</div>';
            
            // Geolocalização
            if (data.geolocation) {
                html += '<h4 style="color: #00ff41; margin: 15px 0;">Geolocalização</h4>';
                html += '<p>País: <strong>' + data.geolocation.country + '</strong></p>';
                html += '<p>Cidade: <strong>' + data.geolocation.city + '</strong></p>';
            }
            
            // User Agents
            if (data.user_agents.length > 0) {
                html += '<h4 style="color: #00ff41; margin: 15px 0;">User Agents (' + data.user_agents.length + ')</h4>';
                html += '<div style="max-height: 150px; overflow-y: auto; background: #0f1419; padding: 10px; border-radius: 5px;">';
                data.user_agents.forEach(function(ua) {
                    html += '<div style="margin: 5px 0; font-size: 0.9em;">' + ua.substring(0, 80) + '...</div>';
                });
                html += '</div>';
            }
            
            // Recomendações
            if (data.recommendations.length > 0) {
                html += '<h4 style="color: #00ff41; margin: 15px 0;">Recomendações</h4>';
                html += '<ul style="margin-left: 20px;">';
                data.recommendations.forEach(function(rec) {
                    html += '<li style="margin: 8px 0;">' + rec + '</li>';
                });
                html += '</ul>';
            }
            
            // Timeline
            if (data.timeline.length > 0) {
                html += '<h4 style="color: #00ff41; margin: 15px 0;">Timeline de Atividades (últimas 20)</h4>';
                html += '<div class="timeline-container">';
                
                data.timeline.slice(-20).reverse().forEach(function(event) {
                    var severity = getSeverityFromType(event.type);
                    html += '<div class="timeline-item ' + severity + '">';
                    html += '<div class="timeline-time">' + event.timestamp + '</div>';
                    html += '<div class="timeline-type">' + event.type + '</div>';
                    html += '<div>' + (event.data.url || 'N/A') + '</div>';
                    html += '</div>';
                });
                
                html += '</div>';
            }
            
            html += '</div>';
            
            $('#ip-results').html(html);
        }
        
        // Analisar correlação
        function analyzeCorrelation() {
            var hours = $('#correlation-hours').val();
            
            $('#correlation-results').show().html('<div class="loading">Analisando correlações dos últimos ' + hours + ' horas...</div>');
            
            $.post('', {
                action: 'correlation_analysis',
                hours: hours
            }, function(data) {
                if (data.error) {
                    $('#correlation-results').html('<div style="color: #ff4444;">Erro: ' + data.error + '</div>');
                } else {
                    displayCorrelationAnalysis(data);
                }
            }, 'json').fail(function() {
                $('#correlation-results').html('<div style="color: #ff4444;">Erro de comunicação com o servidor</div>');
            });
        }
        
        // Exibir análise de correlação
        function displayCorrelationAnalysis(data) {
            var html = '<div class="results-title">Análise de Correlação de Eventos</div>';
            
            html += '<div class="correlation-grid">';
            
            // Padrões temporais
            if (data.time_patterns) {
                html += '<div class="correlation-card">';
                html += '<div class="correlation-title">Padrões Temporais</div>';
                
                if (data.time_patterns.peak_hours.length > 0) {
                    html += '<p><strong>Horários de Pico:</strong> ' + data.time_patterns.peak_hours.join(', ') + '</p>';
                }
                
                if (data.time_patterns.suspicious_patterns.length > 0) {
                    html += '<p style="color: #ff4444;"><strong>Padrões Suspeitos:</strong></p>';
                    data.time_patterns.suspicious_patterns.forEach(function(pattern) {
                        html += '<div style="margin: 5px 0; padding: 8px; background: #1a1a2e; border-radius: 4px;">';
                        html += pattern.description + ' (Hora: ' + pattern.hour + ':00, Count: ' + pattern.count + ')';
                        html += '</div>';
                    });
                }
                
                html += '</div>';
            }
            
            // Clusters de IP
            if (data.ip_clusters && data.ip_clusters.length > 0) {
                html += '<div class="correlation-card">';
                html += '<div class="correlation-title">Clusters de IPs Suspeitos</div>';
                
                data.ip_clusters.forEach(function(cluster) {
                    var riskColor = cluster.risk_level === 'high' ? '#ff4444' : '#ffaa00';
                    html += '<div style="margin: 10px 0; padding: 10px; background: #1a1a2e; border-left: 4px solid ' + riskColor + '; border-radius: 4px;">';
                    html += '<strong>' + cluster.ip + '</strong> - ' + cluster.description;
                    html += '<br><small>Ataques: ' + cluster.details.count + ', Tipos: ' + cluster.details.types.join(', ') + '</small>';
                    html += '</div>';
                });
                
                html += '</div>';
            }
            
            // Sequências de ataque
            if (data.attack_sequences && data.attack_sequences.length > 0) {
                html += '<div class="correlation-card">';
                html += '<div class="correlation-title">Sequências de Ataque</div>';
                
                data.attack_sequences.slice(0, 5).forEach(function(sequence) {
                    var riskColor = {
                        'critical': '#ff4444',
                        'high': '#ffaa00', 
                        'medium': '#44aaff',
                        'low': '#00ff41'
                    }[sequence.risk_assessment] || '#666';
                    
                    html += '<div style="margin: 10px 0; padding: 10px; background: #1a1a2e; border-left: 4px solid ' + riskColor + '; border-radius: 4px;">';
                    html += '<strong>IP: ' + sequence.ip + '</strong>';
                    html += '<br>Eventos: ' + sequence.sequence_length + ', Duração: ' + Math.round(sequence.duration / 60) + ' min';
                    html += '<br>Risco: <span style="color: ' + riskColor + ';">' + sequence.risk_assessment.toUpperCase() + '</span>';
                    html += '</div>';
                });
                
                html += '</div>';
            }
            
            // Anomalias de comportamento
            if (data.user_behavior_anomalies && data.user_behavior_anomalies.length > 0) {
                html += '<div class="correlation-card">';
                html += '<div class="correlation-title">Anomalias de Comportamento</div>';
                
                data.user_behavior_anomalies.forEach(function(anomaly) {
                    var severityColor = {
                        'high': '#ff4444',
                        'medium': '#ffaa00',
                        'low': '#44aaff'
                    }[anomaly.severity] || '#666';
                    
                    html += '<div style="margin: 10px 0; padding: 10px; background: #1a1a2e; border-left: 4px solid ' + severityColor + '; border-radius: 4px;">';
                    html += '<strong>Usuário: ' + anomaly.user_id + '</strong>';
                    html += '<br>' + anomaly.description;
                    html += '<br><small>Severidade: <span style="color: ' + severityColor + ';">' + anomaly.severity.toUpperCase() + '</span></small>';
                    html += '</div>';
                });
                
                html += '</div>';
            }
            
            html += '</div>';
            
            $('#correlation-results').html(html);
        }
        
        // Gerar timeline
        function generateTimeline() {
            var start = $('#timeline-start').val();
            var end = $('#timeline-end').val();
            var ip = $('#timeline-ip').val().trim();
            
            if (!start || !end) {
                alert('Por favor, insira as datas de início e fim');
                return;
            }
            
            $('#timeline-results').show().html('<div class="loading">Gerando timeline de incidentes...</div>');
            
            var filters = {};
            if (ip) {
                filters.ip = ip;
            }
            
            $.post('', {
                action: 'incident_timeline',
                start_time: start,
                end_time: end,
                filters: filters
            }, function(data) {
                if (data.error) {
                    $('#timeline-results').html('<div style="color: #ff4444;">Erro: ' + data.error + '</div>');
                } else {
                    displayTimeline(data);
                }
            }, 'json').fail(function() {
                $('#timeline-results').html('<div style="color: #ff4444;">Erro de comunicação com o servidor</div>');
            });
        }
        
        // Exibir timeline
        function displayTimeline(data) {
            var html = '<div class="results-title">Timeline de Incidentes (' + data.length + ' eventos)</div>';
            
            if (data.length === 0) {
                html += '<p style="text-align: center; color: #666; padding: 50px;">Nenhum evento encontrado no período especificado.</p>';
            } else {
                html += '<div class="timeline-container">';
                
                data.forEach(function(event) {
                    html += '<div class="timeline-item ' + event.severity + '">';
                    html += '<div class="timeline-time">' + event.timestamp + '</div>';
                    html += '<div class="timeline-type">' + event.type + '</div>';
                    html += '<div style="flex: 1;">';
                    html += '<strong>IP:</strong> ' + event.ip + ' | ';
                    html += '<strong>Usuário:</strong> ' + event.user_id + '<br>';
                    html += event.description;
                    html += '</div>';
                    html += '</div>';
                });
                
                html += '</div>';
            }
            
            $('#timeline-results').html(html);
        }
        
        // Exportar para SIEM
        function exportSIEM(format) {
            var startDate = $('#export-start').val();
            var endDate = $('#export-end').val();
            
            if (!startDate || !endDate) {
                alert('Por favor, selecione as datas de início e fim');
                return;
            }
            
            // Criar formulário oculto para download
            var form = $('<form>', {
                'method': 'POST',
                'action': ''
            });
            
            form.append($('<input>', {'type': 'hidden', 'name': 'action', 'value': 'export_siem'}));
            form.append($('<input>', {'type': 'hidden', 'name': 'format', 'value': format}));
            form.append($('<input>', {'type': 'hidden', 'name': 'start_date', 'value': startDate}));
            form.append($('<input>', {'type': 'hidden', 'name': 'end_date', 'value': endDate}));
            
            $('body').append(form);
            form.submit();
            form.remove();
        }
        
        // Utilitário para obter severidade do tipo
        function getSeverityFromType(type) {
            var severityMap = {
                'SQL_INJECTION': 'critical',
                'SYSTEM_COMPROMISE': 'critical',
                'XSS_ATTEMPT': 'high',
                'CSRF': 'high',
                'BRUTE_FORCE': 'medium',
                'FILE_UPLOAD_BLOCKED': 'medium',
                'VALIDATION_FAILED': 'low'
            };
            
            return severityMap[type] || 'medium';
        }
    </script>
    
    <?php endif; ?>
</body>
</html>