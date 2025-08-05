<?php
/**
 * SIGA Security Configuration Monitor
 * 
 * Sistema Centralizado de Configurações de Segurança
 * Gerenciamento, monitoramento e auditoria de configurações
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
    exit('Acesso negado. Apenas administradores podem acessar as Configurações de Segurança.');
}

/**
 * Classe principal do Sistema de Configurações de Segurança
 */
class SecurityConfigurationManager {
    
    private $config_file = 'config/security_config.json';
    private $config_backup_dir = 'config/backups';
    private $config_history_file = 'logs/config_changes.log';
    
    private $default_config = [
        // Configurações de Autenticação
        'authentication' => [
            'session_timeout' => 1800, // 30 minutos
            'session_regenerate_interval' => 300, // 5 minutos
            'max_login_attempts' => 5,
            'lockout_duration' => 900, // 15 minutos
            'password_min_length' => 8,
            'password_require_complex' => true,
            'two_factor_enabled' => false,
            'remember_me_enabled' => true,
            'remember_me_duration' => 2592000 // 30 dias
        ],
        
        // Configurações de Monitoramento
        'monitoring' => [
            'real_time_alerts' => true,
            'log_retention_days' => 90,
            'auto_cleanup_enabled' => true,
            'performance_monitoring' => true,
            'threat_intelligence' => true,
            'geolocation_tracking' => true,
            'user_behavior_analysis' => true,
            'anomaly_detection' => true
        ],
        
        // Configurações de Alertas
        'alerts' => [
            'email_enabled' => true,
            'sms_enabled' => false,
            'slack_enabled' => false,
            'webhook_enabled' => false,
            'throttling_enabled' => true,
            'max_alerts_per_hour' => 10,
            'critical_max_per_hour' => 3,
            'escalation_enabled' => true,
            'admin_emails' => ['admin@coface.com.br'],
            'notification_levels' => ['critical', 'high', 'medium']
        ],
        
        // Configurações de Resposta Automática
        'auto_response' => [
            'ip_blocking_enabled' => true,
            'user_lockout_enabled' => true,
            'session_quarantine_enabled' => true,
            'rate_limiting_enabled' => true,
            'token_rotation_enabled' => true,
            'backup_on_incident' => false,
            'ip_block_threshold' => 10,
            'ip_block_duration' => 3600,
            'user_lockout_threshold' => 5,
            'user_lockout_duration' => 1800,
            'base_rate_limit' => 60,
            'threat_rate_multiplier' => 0.5
        ],
        
        // Configurações de API
        'api' => [
            'enabled' => true,
            'rate_limit_per_minute' => 100,
            'require_api_key' => true,
            'ip_whitelist_enabled' => true,
            'cors_enabled' => true,
            'webhook_timeout' => 10,
            'api_version' => 'v1',
            'documentation_enabled' => true
        ],
        
        // Configurações de Compliance
        'compliance' => [
            'lgpd_monitoring' => true,
            'pci_dss_monitoring' => true,
            'iso_27001_monitoring' => true,
            'nist_monitoring' => true,
            'audit_trail_enabled' => true,
            'data_retention_policy' => 2555, // 7 anos em dias
            'anonymization_enabled' => false,
            'consent_tracking' => false,
            'privacy_by_design' => true
        ],
        
        // Configurações de Backup
        'backup' => [
            'auto_backup_enabled' => true,
            'backup_frequency' => 'daily',
            'backup_retention_days' => 30,
            'backup_encryption' => true,
            'backup_compression' => true,
            'backup_location' => 'local',
            'remote_backup_enabled' => false,
            'emergency_backup_threshold' => 3
        ],
        
        // Configurações de Logging
        'logging' => [
            'detailed_logging' => true,
            'log_level' => 'info', // debug, info, warning, error, critical
            'log_format' => 'json',
            'log_rotation_enabled' => true,
            'max_log_size_mb' => 100,
            'compress_old_logs' => true,
            'syslog_enabled' => false,
            'remote_logging' => false,
            'log_sanitization' => true
        ],
        
        // Configurações de Performance
        'performance' => [
            'cache_enabled' => true,
            'cache_duration' => 300, // 5 minutos
            'query_optimization' => true,
            'lazy_loading' => true,
            'compression_enabled' => true,
            'cdn_enabled' => false,
            'resource_monitoring' => true,
            'auto_scaling' => false
        ]
    ];
    
    private $current_config = [];
    private $config_schema = [];
    
    public function __construct() {
        $this->initializeDirectories();
        $this->loadConfiguration();
        $this->initializeConfigSchema();
    }
    
    /**
     * Inicializar diretórios necessários
     */
    private function initializeDirectories() {
        $dirs = [
            dirname(__FILE__) . '/config',
            dirname(__FILE__) . '/' . $this->config_backup_dir,
            dirname(__FILE__) . '/logs'
        ];
        
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    /**
     * Carregar configuração atual
     */
    private function loadConfiguration() {
        $config_path = __DIR__ . '/' . $this->config_file;
        
        if (file_exists($config_path)) {
            $stored_config = json_decode(file_get_contents($config_path), true);
            if ($stored_config) {
                $this->current_config = array_merge($this->default_config, $stored_config);
            } else {
                $this->current_config = $this->default_config;
                $this->saveConfiguration();
            }
        } else {
            $this->current_config = $this->default_config;
            $this->saveConfiguration();
        }
    }
    
    /**
     * Salvar configuração
     */
    private function saveConfiguration() {
        $config_path = __DIR__ . '/' . $this->config_file;
        
        // Backup da configuração atual
        if (file_exists($config_path)) {
            $backup_name = 'security_config_' . date('Y-m-d_H-i-s') . '.json';
            $backup_path = __DIR__ . '/' . $this->config_backup_dir . '/' . $backup_name;
            copy($config_path, $backup_path);
        }
        
        // Salvar nova configuração
        $success = file_put_contents($config_path, json_encode($this->current_config, JSON_PRETTY_PRINT), LOCK_EX);
        
        if ($success) {
            $this->logConfigurationChange('CONFIG_SAVED', 'Configuration saved successfully');
        } else {
            throw new Exception('Failed to save configuration');
        }
        
        return $success;
    }
    
    /**
     * Obter configuração completa
     */
    public function getConfiguration($section = null) {
        if ($section) {
            return $this->current_config[$section] ?? [];
        }
        
        return $this->current_config;
    }
    
    /**
     * Atualizar configuração
     */
    public function updateConfiguration($section, $key, $value) {
        if (!isset($this->current_config[$section])) {
            throw new Exception("Configuration section '$section' does not exist");
        }
        
        if (!array_key_exists($key, $this->current_config[$section])) {
            throw new Exception("Configuration key '$key' does not exist in section '$section'");
        }
        
        // Validar valor
        if (!$this->validateConfigValue($section, $key, $value)) {
            throw new Exception("Invalid value for configuration key '$section.$key'");
        }
        
        $old_value = $this->current_config[$section][$key];
        $this->current_config[$section][$key] = $value;
        
        $this->saveConfiguration();
        
        $this->logConfigurationChange('CONFIG_UPDATED', 
            "Updated $section.$key from '$old_value' to '$value'", 
            [
                'section' => $section,
                'key' => $key,
                'old_value' => $old_value,
                'new_value' => $value
            ]
        );
        
        return true;
    }
    
    /**
     * Validar valor de configuração
     */
    private function validateConfigValue($section, $key, $value) {
        // Validações específicas baseadas no tipo e contexto
        switch ($section) {
            case 'authentication':
                return $this->validateAuthenticationConfig($key, $value);
            case 'monitoring':
                return $this->validateMonitoringConfig($key, $value);
            case 'alerts':
                return $this->validateAlertsConfig($key, $value);
            case 'auto_response':
                return $this->validateAutoResponseConfig($key, $value);
            case 'api':
                return $this->validateAPIConfig($key, $value);
            default:
                return $this->validateGenericConfig($key, $value);
        }
    }
    
    /**
     * Validações específicas por seção
     */
    private function validateAuthenticationConfig($key, $value) {
        switch ($key) {
            case 'session_timeout':
            case 'session_regenerate_interval':
            case 'lockout_duration':
                return is_int($value) && $value > 0 && $value <= 86400; // Max 24h
                
            case 'max_login_attempts':
                return is_int($value) && $value >= 1 && $value <= 20;
                
            case 'password_min_length':
                return is_int($value) && $value >= 6 && $value <= 50;
                
            case 'password_require_complex':
            case 'two_factor_enabled':
            case 'remember_me_enabled':
                return is_bool($value);
                
            case 'remember_me_duration':
                return is_int($value) && $value > 0 && $value <= 31536000; // Max 1 ano
                
            default:
                return true;
        }
    }
    
    private function validateMonitoringConfig($key, $value) {
        switch ($key) {
            case 'log_retention_days':
                return is_int($value) && $value >= 1 && $value <= 3650; // Max 10 anos
                
            case 'real_time_alerts':
            case 'auto_cleanup_enabled':
            case 'performance_monitoring':
            case 'threat_intelligence':
            case 'geolocation_tracking':
            case 'user_behavior_analysis':
            case 'anomaly_detection':
                return is_bool($value);
                
            default:
                return true;
        }
    }
    
    private function validateAlertsConfig($key, $value) {
        switch ($key) {
            case 'max_alerts_per_hour':
            case 'critical_max_per_hour':
                return is_int($value) && $value >= 1 && $value <= 1000;
                
            case 'email_enabled':
            case 'sms_enabled':
            case 'slack_enabled':
            case 'webhook_enabled':
            case 'throttling_enabled':
            case 'escalation_enabled':
                return is_bool($value);
                
            case 'admin_emails':
                return is_array($value) && !empty($value);
                
            case 'notification_levels':
                $valid_levels = ['low', 'medium', 'high', 'critical'];
                return is_array($value) && !array_diff($value, $valid_levels);
                
            default:
                return true;
        }
    }
    
    private function validateAutoResponseConfig($key, $value) {
        switch ($key) {
            case 'ip_block_threshold':
            case 'user_lockout_threshold':
                return is_int($value) && $value >= 1 && $value <= 100;
                
            case 'ip_block_duration':
            case 'user_lockout_duration':
                return is_int($value) && $value > 0 && $value <= 86400;
                
            case 'base_rate_limit':
                return is_int($value) && $value >= 1 && $value <= 10000;
                
            case 'threat_rate_multiplier':
                return is_float($value) && $value > 0 && $value <= 1;
                
            case 'ip_blocking_enabled':
            case 'user_lockout_enabled':
            case 'session_quarantine_enabled':
            case 'rate_limiting_enabled':
            case 'token_rotation_enabled':
            case 'backup_on_incident':
                return is_bool($value);
                
            default:
                return true;
        }
    }
    
    private function validateAPIConfig($key, $value) {
        switch ($key) {
            case 'rate_limit_per_minute':
                return is_int($value) && $value >= 1 && $value <= 10000;
                
            case 'webhook_timeout':
                return is_int($value) && $value >= 1 && $value <= 60;
                
            case 'enabled':
            case 'require_api_key':
            case 'ip_whitelist_enabled':
            case 'cors_enabled':
            case 'documentation_enabled':
                return is_bool($value);
                
            case 'api_version':
                return is_string($value) && preg_match('/^v\d+$/', $value);
                
            default:
                return true;
        }
    }
    
    private function validateGenericConfig($key, $value) {
        // Validação genérica para outros tipos
        return true;
    }
    
    /**
     * Restaurar backup de configuração
     */
    public function restoreBackup($backup_filename) {
        $backup_path = __DIR__ . '/' . $this->config_backup_dir . '/' . $backup_filename;
        
        if (!file_exists($backup_path)) {
            throw new Exception("Backup file not found: $backup_filename");
        }
        
        $backup_config = json_decode(file_get_contents($backup_path), true);
        if (!$backup_config) {
            throw new Exception("Invalid backup file: $backup_filename");
        }
        
        // Validar configuração do backup
        if (!$this->validateConfiguration($backup_config)) {
            throw new Exception("Backup configuration is invalid");
        }
        
        $this->current_config = $backup_config;
        $this->saveConfiguration();
        
        $this->logConfigurationChange('CONFIG_RESTORED', 
            "Configuration restored from backup: $backup_filename"
        );
        
        return true;
    }
    
    /**
     * Validar configuração completa
     */
    private function validateConfiguration($config) {
        // Verificar se todas as seções obrigatórias existem
        $required_sections = array_keys($this->default_config);
        
        foreach ($required_sections as $section) {
            if (!isset($config[$section])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Obter histórico de mudanças
     */
    public function getConfigurationHistory($limit = 50) {
        $history_path = __DIR__ . '/' . $this->config_history_file;
        
        if (!file_exists($history_path)) {
            return [];
        }
        
        $lines = file($history_path, FILE_IGNORE_NEW_LINES);
        $history = [];
        
        foreach (array_slice($lines, -$limit) as $line) {
            $entry = json_decode($line, true);
            if ($entry) {
                $history[] = $entry;
            }
        }
        
        return array_reverse($history);
    }
    
    /**
     * Obter lista de backups
     */
    public function getBackupList() {
        $backup_dir = __DIR__ . '/' . $this->config_backup_dir;
        $backups = [];
        
        if (is_dir($backup_dir)) {
            $files = scandir($backup_dir);
            foreach ($files as $file) {
                if (preg_match('/^security_config_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.json$/', $file, $matches)) {
                    $backups[] = [
                        'filename' => $file,
                        'timestamp' => $matches[1],
                        'size' => filesize($backup_dir . '/' . $file),
                        'created' => filemtime($backup_dir . '/' . $file)
                    ];
                }
            }
        }
        
        // Ordenar por data de criação (mais recente primeiro)
        usort($backups, function($a, $b) {
            return $b['created'] - $a['created'];
        });
        
        return $backups;
    }
    
    /**
     * Exportar configuração
     */
    public function exportConfiguration($format = 'json') {
        switch ($format) {
            case 'json':
                return json_encode($this->current_config, JSON_PRETTY_PRINT);
                
            case 'yaml':
                // Em produção, usar biblioteca YAML
                return $this->arrayToYaml($this->current_config);
                
            case 'php':
                return "<?php\nreturn " . var_export($this->current_config, true) . ";\n";
                
            default:
                throw new Exception("Unsupported export format: $format");
        }
    }
    
    /**
     * Importar configuração
     */
    public function importConfiguration($data, $format = 'json') {
        switch ($format) {
            case 'json':
                $config = json_decode($data, true);
                break;
                
            case 'yaml':
                // Em produção, usar biblioteca YAML
                throw new Exception("YAML import not implemented");
                
            case 'php':
                $config = eval('return ' . $data . ';');
                break;
                
            default:
                throw new Exception("Unsupported import format: $format");
        }
        
        if (!$config || !$this->validateConfiguration($config)) {
            throw new Exception("Invalid configuration data");
        }
        
        $this->current_config = $config;
        $this->saveConfiguration();
        
        $this->logConfigurationChange('CONFIG_IMPORTED', 
            "Configuration imported from $format format"
        );
        
        return true;
    }
    
    /**
     * Monitoramento de configuração
     */
    public function getConfigurationStatus() {
        return [
            'config_file_exists' => file_exists(__DIR__ . '/' . $this->config_file),
            'config_file_writable' => is_writable(__DIR__ . '/' . $this->config_file),
            'backup_dir_exists' => is_dir(__DIR__ . '/' . $this->config_backup_dir),
            'backup_dir_writable' => is_writable(__DIR__ . '/' . $this->config_backup_dir),
            'total_backups' => count($this->getBackupList()),
            'last_modified' => filemtime(__DIR__ . '/' . $this->config_file),
            'config_size' => filesize(__DIR__ . '/' . $this->config_file),
            'validation_status' => $this->validateConfiguration($this->current_config) ? 'valid' : 'invalid'
        ];
    }
    
    /**
     * Reset para configurações padrão
     */
    public function resetToDefaults() {
        $this->current_config = $this->default_config;
        $this->saveConfiguration();
        
        $this->logConfigurationChange('CONFIG_RESET', 
            "Configuration reset to default values"
        );
        
        return true;
    }
    
    /**
     * Métodos auxiliares
     */
    private function logConfigurationChange($action, $description, $details = []) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action,
            'description' => $description,
            'user_id' => $_SESSION['userID'] ?? 'system',
            'user_name' => $_SESSION['nameUser'] ?? 'System',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'details' => $details
        ];
        
        $history_path = __DIR__ . '/' . $this->config_history_file;
        file_put_contents($history_path, json_encode($log_entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    private function arrayToYaml($array, $indent = 0) {
        $yaml = '';
        $spaces = str_repeat('  ', $indent);
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $yaml .= $spaces . $key . ":\n";
                $yaml .= $this->arrayToYaml($value, $indent + 1);
            } else {
                $yaml .= $spaces . $key . ': ' . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
            }
        }
        
        return $yaml;
    }
    
    private function initializeConfigSchema() {
        $this->config_schema = [
            'authentication' => [
                'title' => 'Configurações de Autenticação',
                'description' => 'Configurações relacionadas ao sistema de login e segurança de sessão'
            ],
            'monitoring' => [
                'title' => 'Configurações de Monitoramento',
                'description' => 'Configurações do sistema de monitoramento e análise de segurança'
            ],
            'alerts' => [
                'title' => 'Configurações de Alertas',
                'description' => 'Configurações do sistema de notificações e alertas'
            ],
            'auto_response' => [
                'title' => 'Resposta Automática',
                'description' => 'Configurações do sistema de resposta automática a ameaças'
            ],
            'api' => [
                'title' => 'Configurações da API',
                'description' => 'Configurações da API REST para integrações externas'
            ],
            'compliance' => [
                'title' => 'Configurações de Compliance',
                'description' => 'Configurações relacionadas a conformidade e regulamentações'
            ],
            'backup' => [
                'title' => 'Configurações de Backup',
                'description' => 'Configurações do sistema de backup e recuperação'
            ],
            'logging' => [
                'title' => 'Configurações de Log',
                'description' => 'Configurações do sistema de logging e auditoria'
            ],
            'performance' => [
                'title' => 'Configurações de Performance',
                'description' => 'Configurações relacionadas ao desempenho do sistema'
            ]
        ];
    }
    
    public function getConfigSchema() {
        return $this->config_schema;
    }
}

// Instanciar gerenciador de configuração
$config_manager = new SecurityConfigurationManager();

// Processar requisições AJAX
if (isset($_POST['action']) && validate_user_session() && $_SESSION['pefil'] === 'F') {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'get_config':
                $section = $_POST['section'] ?? null;
                echo json_encode(['success' => true, 'data' => $config_manager->getConfiguration($section)]);
                break;
                
            case 'update_config':
                $section = InputValidator::validate($_POST['section'] ?? '', ['type' => 'string', 'required' => true]);
                $key = InputValidator::validate($_POST['key'] ?? '', ['type' => 'string', 'required' => true]);
                $value = $_POST['value'] ?? null;
                
                // Converter string boolean para boolean real
                if ($value === 'true') $value = true;
                elseif ($value === 'false') $value = false;
                elseif (is_numeric($value)) $value = is_float($value) ? (float)$value : (int)$value;
                
                $config_manager->updateConfiguration($section, $key, $value);
                echo json_encode(['success' => true, 'message' => 'Configuration updated successfully']);
                break;
                
            case 'get_history':
                $limit = InputValidator::validate($_POST['limit'] ?? 50, ['type' => 'int']);
                echo json_encode(['success' => true, 'data' => $config_manager->getConfigurationHistory($limit)]);
                break;
                
            case 'get_backups':
                echo json_encode(['success' => true, 'data' => $config_manager->getBackupList()]);
                break;
                
            case 'restore_backup':
                $filename = InputValidator::validate($_POST['filename'] ?? '', ['type' => 'string', 'required' => true]);
                $config_manager->restoreBackup($filename);
                echo json_encode(['success' => true, 'message' => 'Configuration restored successfully']);
                break;
                
            case 'export_config':
                $format = InputValidator::validate($_POST['format'] ?? 'json', ['type' => 'string', 'whitelist' => ['json', 'yaml', 'php']]);
                $data = $config_manager->exportConfiguration($format);
                echo json_encode(['success' => true, 'data' => $data, 'format' => $format]);
                break;
                
            case 'get_status':
                echo json_encode(['success' => true, 'data' => $config_manager->getConfigurationStatus()]);
                break;
                
            case 'reset_defaults':
                $config_manager->resetToDefaults();
                echo json_encode(['success' => true, 'message' => 'Configuration reset to defaults']);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Unknown action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
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
    <title>SIGA - Configurações de Segurança</title>
    
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/geral.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="Scripts/jquery.js"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .config-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .config-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .config-title {
            font-size: 2.8em;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 300;
        }
        
        .config-subtitle {
            color: #7f8c8d;
            font-size: 1.2em;
            font-weight: 300;
        }
        
        .config-nav {
            display: flex;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .nav-item {
            flex: 1;
            padding: 15px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border-right: 1px solid #ecf0f1;
        }
        
        .nav-item:last-child {
            border-right: none;
        }
        
        .nav-item:hover {
            background: #f8f9fa;
        }
        
        .nav-item.active {
            background: #3498db;
            color: white;
        }
        
        .nav-icon {
            font-size: 1.5em;
            margin-bottom: 5px;
            display: block;
        }
        
        .nav-label {
            font-size: 0.9em;
            font-weight: 600;
        }
        
        .config-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .content-panel {
            display: none;
            padding: 30px;
        }
        
        .content-panel.active {
            display: block;
        }
        
        .panel-title {
            font-size: 1.8em;
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        
        .config-sections {
            display: grid;
            gap: 25px;
        }
        
        .config-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            border-left: 5px solid #3498db;
        }
        
        .section-title {
            font-size: 1.3em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section-icon {
            margin-right: 10px;
            color: #3498db;
        }
        
        .section-description {
            color: #7f8c8d;
            margin-bottom: 20px;
            font-size: 0.95em;
        }
        
        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .config-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .config-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: block;
        }
        
        .config-input {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }
        
        .config-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .config-toggle {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .config-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: #3498db;
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        .save-button {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 15px;
            transition: all 0.3s ease;
        }
        
        .save-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .history-table th,
        .history-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .history-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .history-table tr:hover {
            background: #f8f9fa;
        }
        
        .backup-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 10px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .backup-info {
            flex: 1;
        }
        
        .backup-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .backup-details {
            font-size: 0.9em;
            color: #7f8c8d;
        }
        
        .backup-actions {
            display: flex;
            gap: 10px;
        }
        
        .action-button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }
        
        .restore-button {
            background: #f39c12;
            color: white;
        }
        
        .restore-button:hover {
            background: #e67e22;
        }
        
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .status-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .status-value {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .status-value.ok { color: #27ae60; }
        .status-value.warning { color: #f39c12; }
        .status-value.error { color: #e74c3c; }
        
        .status-label {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        
        .export-buttons {
            display: flex;
            gap: 15px;
            margin: 20px 0;
        }
        
        .export-button {
            padding: 10px 20px;
            border: 2px solid #3498db;
            background: transparent;
            color: #3498db;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .export-button:hover {
            background: #3498db;
            color: white;
        }
        
        .loading {
            text-align: center;
            padding: 50px;
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
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #27ae60;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #e74c3c;
        }
    </style>
</head>

<body>
    <?php if (!validate_user_session() || $_SESSION['pefil'] !== 'F'): ?>
        <div style="text-align: center; padding: 50px; color: white;">
            <h2>Acesso Negado</h2>
            <p>Apenas administradores podem acessar as Configurações de Segurança.</p>
        </div>
    <?php else: ?>
    
    <div class="config-container">
        <div class="config-header">
            <h1 class="config-title">Configurações de Segurança</h1>
            <p class="config-subtitle">Gerenciamento centralizado das configurações do sistema de segurança</p>
        </div>
        
        <!-- Navegação -->
        <div class="config-nav">
            <div class="nav-item active" onclick="switchPanel('configuration')">
                <span class="nav-icon">⚙️</span>
                <span class="nav-label">Configurações</span>
            </div>
            <div class="nav-item" onclick="switchPanel('history')">
                <span class="nav-icon">📋</span>
                <span class="nav-label">Histórico</span>
            </div>
            <div class="nav-item" onclick="switchPanel('backups')">
                <span class="nav-icon">💾</span>
                <span class="nav-label">Backups</span>
            </div>
            <div class="nav-item" onclick="switchPanel('status')">
                <span class="nav-icon">📊</span>
                <span class="nav-label">Status</span>
            </div>
            <div class="nav-item" onclick="switchPanel('export')">
                <span class="nav-icon">📤</span>
                <span class="nav-label">Exportar</span>
            </div>
        </div>
        
        <!-- Conteúdo -->
        <div class="config-content">
            <!-- Painel de Configurações -->
            <div id="configuration-panel" class="content-panel active">
                <div class="panel-title">Configurações do Sistema</div>
                <div id="config-sections" class="config-sections">
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        Carregando configurações...
                    </div>
                </div>
            </div>
            
            <!-- Painel de Histórico -->
            <div id="history-panel" class="content-panel">
                <div class="panel-title">Histórico de Alterações</div>
                <div id="history-content">
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        Carregando histórico...
                    </div>
                </div>
            </div>
            
            <!-- Painel de Backups -->
            <div id="backups-panel" class="content-panel">
                <div class="panel-title">Gerenciamento de Backups</div>
                <div id="backups-content">
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        Carregando backups...
                    </div>
                </div>
            </div>
            
            <!-- Painel de Status -->
            <div id="status-panel" class="content-panel">
                <div class="panel-title">Status do Sistema</div>
                <div id="status-content">
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        Carregando status...
                    </div>
                </div>
            </div>
            
            <!-- Painel de Exportação -->
            <div id="export-panel" class="content-panel">
                <div class="panel-title">Exportar Configurações</div>
                <p>Exporte as configurações em diferentes formatos para backup ou migração.</p>
                
                <div class="export-buttons">
                    <button class="export-button" onclick="exportConfig('json')">📄 Exportar JSON</button>
                    <button class="export-button" onclick="exportConfig('yaml')">📝 Exportar YAML</button>
                    <button class="export-button" onclick="exportConfig('php')">🐘 Exportar PHP</button>
                </div>
                
                <div style="margin-top: 30px;">
                    <button class="action-button restore-button" onclick="resetToDefaults()" style="background: #e74c3c;">
                        🔄 Restaurar Configurações Padrão
                    </button>
                </div>
                
                <div id="export-result" style="margin-top: 20px;"></div>
            </div>
        </div>
    </div>
    
    <script>
        // Carregar dados iniciais
        $(document).ready(function() {
            loadConfigurations();
        });
        
        // Alternar painéis
        function switchPanel(panel) {
            $('.nav-item').removeClass('active');
            $('.content-panel').removeClass('active');
            
            $('[onclick="switchPanel(\'' + panel + '\')"]').addClass('active');
            $('#' + panel + '-panel').addClass('active');
            
            // Carregar dados específicos do painel
            switch (panel) {
                case 'configuration':
                    loadConfigurations();
                    break;
                case 'history':
                    loadHistory();
                    break;
                case 'backups':
                    loadBackups();
                    break;
                case 'status':
                    loadStatus();
                    break;
            }
        }
        
        // Carregar configurações
        function loadConfigurations() {
            $('#config-sections').html('<div class="loading"><div class="loading-spinner"></div>Carregando configurações...</div>');
            
            $.post('', {action: 'get_config'}, function(data) {
                if (data.success) {
                    displayConfigurations(data.data);
                } else {
                    $('#config-sections').html('<div class="error-message">Erro ao carregar configurações: ' + data.error + '</div>');
                }
            }, 'json');
        }
        
        // Exibir configurações
        function displayConfigurations(config) {
            var html = '';
            
            var sections = {
                'authentication': {title: 'Autenticação', icon: '🔐'},
                'monitoring': {title: 'Monitoramento', icon: '👀'},
                'alerts': {title: 'Alertas', icon: '🚨'},
                'auto_response': {title: 'Resposta Automática', icon: '🛡️'},
                'api': {title: 'API', icon: '🔌'},
                'compliance': {title: 'Compliance', icon: '✅'},
                'backup': {title: 'Backup', icon: '💾'},
                'logging': {title: 'Logging', icon: '📝'},
                'performance': {title: 'Performance', icon: '⚡'}
            };
            
            for (var sectionKey in sections) {
                if (config[sectionKey]) {
                    html += '<div class="config-section">';
                    html += '<div class="section-title">';
                    html += '<span class="section-icon">' + sections[sectionKey].icon + '</span>';
                    html += sections[sectionKey].title;
                    html += '</div>';
                    
                    html += '<div class="config-grid">';
                    
                    for (var key in config[sectionKey]) {
                        var value = config[sectionKey][key];
                        
                        html += '<div class="config-item">';
                        html += '<label class="config-label">' + formatLabel(key) + '</label>';
                        
                        if (typeof value === 'boolean') {
                            html += '<label class="config-toggle">';
                            html += '<input type="checkbox" ' + (value ? 'checked' : '') + ' onchange="updateConfig(\'' + sectionKey + '\', \'' + key + '\', this.checked)">';
                            html += '<span class="toggle-slider"></span>';
                            html += '</label>';
                        } else if (Array.isArray(value)) {
                            html += '<input type="text" class="config-input" value="' + value.join(', ') + '" onchange="updateConfigArray(\'' + sectionKey + '\', \'' + key + '\', this.value)">';
                        } else {
                            html += '<input type="' + (typeof value === 'number' ? 'number' : 'text') + '" class="config-input" value="' + value + '" onchange="updateConfig(\'' + sectionKey + '\', \'' + key + '\', this.value)">';
                        }
                        
                        html += '</div>';
                    }
                    
                    html += '</div>';
                    html += '</div>';
                }
            }
            
            $('#config-sections').html(html);
        }
        
        // Atualizar configuração
        function updateConfig(section, key, value) {
            // Converter tipos se necessário
            if (typeof value === 'string' && !isNaN(value) && value !== '') {
                value = value.includes('.') ? parseFloat(value) : parseInt(value);
            }
            
            $.post('', {
                action: 'update_config',
                section: section,
                key: key,
                value: value
            }, function(data) {
                if (data.success) {
                    showMessage('Configuração atualizada com sucesso!', 'success');
                } else {
                    showMessage('Erro ao atualizar configuração: ' + data.error, 'error');
                    loadConfigurations(); // Recarregar para reverter mudança
                }
            }, 'json');
        }
        
        // Atualizar configuração de array
        function updateConfigArray(section, key, value) {
            var arrayValue = value.split(',').map(function(item) {
                return item.trim();
            }).filter(function(item) {
                return item !== '';
            });
            
            updateConfig(section, key, arrayValue);
        }
        
        // Carregar histórico
        function loadHistory() {
            $('#history-content').html('<div class="loading"><div class="loading-spinner"></div>Carregando histórico...</div>');
            
            $.post('', {action: 'get_history', limit: 50}, function(data) {
                if (data.success) {
                    displayHistory(data.data);
                } else {
                    $('#history-content').html('<div class="error-message">Erro ao carregar histórico: ' + data.error + '</div>');
                }
            }, 'json');
        }
        
        // Exibir histórico
        function displayHistory(history) {
            if (history.length === 0) {
                $('#history-content').html('<p>Nenhuma alteração registrada.</p>');
                return;
            }
            
            var html = '<table class="history-table">';
            html += '<tr><th>Data/Hora</th><th>Ação</th><th>Usuário</th><th>Descrição</th></tr>';
            
            history.forEach(function(entry) {
                html += '<tr>';
                html += '<td>' + formatDateTime(entry.timestamp) + '</td>';
                html += '<td>' + entry.action + '</td>';
                html += '<td>' + entry.user_name + '</td>';
                html += '<td>' + entry.description + '</td>';
                html += '</tr>';
            });
            
            html += '</table>';
            
            $('#history-content').html(html);
        }
        
        // Carregar backups
        function loadBackups() {
            $('#backups-content').html('<div class="loading"><div class="loading-spinner"></div>Carregando backups...</div>');
            
            $.post('', {action: 'get_backups'}, function(data) {
                if (data.success) {
                    displayBackups(data.data);
                } else {
                    $('#backups-content').html('<div class="error-message">Erro ao carregar backups: ' + data.error + '</div>');
                }
            }, 'json');
        }
        
        // Exibir backups
        function displayBackups(backups) {
            if (backups.length === 0) {
                $('#backups-content').html('<p>Nenhum backup encontrado.</p>');
                return;
            }
            
            var html = '';
            
            backups.forEach(function(backup) {
                html += '<div class="backup-item">';
                html += '<div class="backup-info">';
                html += '<div class="backup-name">' + backup.filename + '</div>';
                html += '<div class="backup-details">';
                html += 'Criado: ' + formatDateTime(backup.timestamp.replace(/_/g, ' ').replace(/-/g, '/')) + ' | ';
                html += 'Tamanho: ' + formatFileSize(backup.size);
                html += '</div>';
                html += '</div>';
                html += '<div class="backup-actions">';
                html += '<button class="action-button restore-button" onclick="restoreBackup(\'' + backup.filename + '\')">Restaurar</button>';
                html += '</div>';
                html += '</div>';
            });
            
            $('#backups-content').html(html);
        }
        
        // Restaurar backup
        function restoreBackup(filename) {
            if (!confirm('Tem certeza que deseja restaurar este backup? As configurações atuais serão perdidas.')) {
                return;
            }
            
            $.post('', {
                action: 'restore_backup',
                filename: filename
            }, function(data) {
                if (data.success) {
                    showMessage('Backup restaurado com sucesso!', 'success');
                    loadConfigurations();
                } else {
                    showMessage('Erro ao restaurar backup: ' + data.error, 'error');
                }
            }, 'json');
        }
        
        // Carregar status
        function loadStatus() {
            $('#status-content').html('<div class="loading"><div class="loading-spinner"></div>Carregando status...</div>');
            
            $.post('', {action: 'get_status'}, function(data) {
                if (data.success) {
                    displayStatus(data.data);
                } else {
                    $('#status-content').html('<div class="error-message">Erro ao carregar status: ' + data.error + '</div>');
                }
            }, 'json');
        }
        
        // Exibir status
        function displayStatus(status) {
            var html = '<div class="status-grid">';
            
            var statusItems = [
                {key: 'config_file_exists', label: 'Arquivo de Config', value: status.config_file_exists},
                {key: 'config_file_writable', label: 'Config Gravável', value: status.config_file_writable},
                {key: 'backup_dir_exists', label: 'Diretório Backup', value: status.backup_dir_exists},
                {key: 'total_backups', label: 'Total de Backups', value: status.total_backups},
                {key: 'config_size', label: 'Tamanho Config', value: formatFileSize(status.config_size)},
                {key: 'validation_status', label: 'Status Validação', value: status.validation_status}
            ];
            
            statusItems.forEach(function(item) {
                html += '<div class="status-card">';
                
                if (typeof item.value === 'boolean') {
                    html += '<div class="status-value ' + (item.value ? 'ok' : 'error') + '">';
                    html += item.value ? '✓' : '✗';
                } else if (typeof item.value === 'number') {
                    html += '<div class="status-value ok">' + item.value;
                } else {
                    var statusClass = item.value === 'valid' ? 'ok' : (item.value === 'invalid' ? 'error' : 'warning');
                    html += '<div class="status-value ' + statusClass + '">' + item.value;
                }
                
                html += '</div>';
                html += '<div class="status-label">' + item.label + '</div>';
                html += '</div>';
            });
            
            html += '</div>';
            
            // Adicionar informações adicionais
            html += '<div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">';
            html += '<h4>Informações Adicionais</h4>';
            html += '<p><strong>Última Modificação:</strong> ' + formatDateTime(new Date(status.last_modified * 1000)) + '</p>';
            html += '</div>';
            
            $('#status-content').html(html);
        }
        
        // Exportar configuração
        function exportConfig(format) {
            $.post('', {
                action: 'export_config',
                format: format
            }, function(data) {
                if (data.success) {
                    var blob = new Blob([data.data], {type: 'text/plain'});
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'siga_security_config.' + format;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                    
                    $('#export-result').html('<div class="success-message">Configuração exportada com sucesso!</div>');
                } else {
                    $('#export-result').html('<div class="error-message">Erro ao exportar: ' + data.error + '</div>');
                }
            }, 'json');
        }
        
        // Resetar para padrões
        function resetToDefaults() {
            if (!confirm('Tem certeza que deseja restaurar todas as configurações para os valores padrão? Esta ação não pode ser desfeita.')) {
                return;
            }
            
            $.post('', {action: 'reset_defaults'}, function(data) {
                if (data.success) {
                    showMessage('Configurações restauradas para padrão!', 'success');
                    loadConfigurations();
                } else {
                    showMessage('Erro ao restaurar configurações: ' + data.error, 'error');
                }
            }, 'json');
        }
        
        // Funções auxiliares
        function formatLabel(key) {
            return key.replace(/_/g, ' ').replace(/\b\w/g, function(l) {
                return l.toUpperCase();
            });
        }
        
        function formatDateTime(dateStr) {
            var date = new Date(dateStr);
            return date.toLocaleString('pt-BR');
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        function showMessage(message, type) {
            var messageClass = type === 'success' ? 'success-message' : 'error-message';
            var messageHtml = '<div class="' + messageClass + '">' + message + '</div>';
            
            // Adicionar mensagem temporária
            $('body').append('<div id="temp-message" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">' + messageHtml + '</div>');
            
            setTimeout(function() {
                $('#temp-message').fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    </script>
    
    <?php endif; ?>
</body>
</html>