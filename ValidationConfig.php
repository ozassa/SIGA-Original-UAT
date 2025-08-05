<?php
/**
 * SIGA Validation Configuration
 * 
 * Configurações avançadas para o framework de validação de entrada.
 * Permite customização de regras por módulo, alertas e comportamentos.
 * 
 * @version 1.0
 * @author Claude Code - Security Hardening Mission
 */

class ValidationConfig {
    
    /**
     * Configurações globais do sistema de validação
     */
    public static function getGlobalConfig() {
        return [
            // Rate limiting
            'rate_limit' => [
                'enabled' => true,
                'max_attempts' => 20,
                'time_window' => 300, // 5 minutos
                'block_duration' => 900 // 15 minutos
            ],
            
            // Logging
            'logging' => [
                'enabled' => true,
                'log_directory' => dirname(__FILE__) . '/logs',
                'rotate_logs' => true,
                'max_log_size' => 10485760, // 10MB
                'retention_days' => 30
            ],
            
            // Alertas de segurança
            'security_alerts' => [
                'enabled' => true,
                'email_alerts' => true,
                'alert_recipients' => [
                    'security@coface.com.br',
                    'admin@coface.com.br'
                ],
                'webhook_url' => null,
                'critical_attack_threshold' => 5
            ],
            
            // Backup compatibility mode
            'backward_compatibility' => [
                'enabled' => true,
                'preserve_original_data' => true,
                'fallback_to_sanitized' => true,
                'log_compatibility_issues' => true
            ],
            
            // Performance
            'performance' => [
                'cache_validation_rules' => true,
                'cache_duration' => 3600, // 1 hora
                'max_input_size' => 1048576, // 1MB
                'max_array_depth' => 10
            ]
        ];
    }
    
    /**
     * Regras específicas por arquivo/script
     */
    public static function getFileSpecificRules() {
        return [
            // Módulo Credit
            'src/role/credit/Credit.php' => [
                'comm' => [
                    'type' => 'string',
                    'max_length' => 50,
                    'whitelist' => [
                        'notif', 'return', 'accept', 'reject', 'view', 'edit',
                        'done', 'list', 'search', 'import', 'export', 'show',
                        'add', 'remove', 'update', 'cancel', 'confirm'
                    ]
                ],
                'refusal' => [
                    'type' => 'int',
                    'min_value' => 0,
                    'max_value' => 1
                ],
                'finish' => [
                    'type' => 'boolean'
                ],
                'done' => [
                    'type' => 'boolean'
                ]
            ],
            
            'src/role/credit/creditAccord.php' => [
                'buyer_id' => [
                    'type' => 'int',
                    'required' => true,
                    'min_value' => 1
                ],
                'limit_value' => [
                    'type' => 'currency',
                    'max_length' => 15,
                    'required' => true
                ],
                'currency' => [
                    'type' => 'string',
                    'max_length' => 3,
                    'whitelist' => ['USD', 'EUR', 'BRL', 'GBP']
                ]
            ],
            
            // Módulo Client
            'src/role/client/Client.php' => [
                'comm' => [
                    'type' => 'string',
                    'max_length' => 50,
                    'whitelist' => [
                        'view', 'edit', 'update', 'list', 'search', 'register',
                        'password', 'profile', 'address', 'contact', 'document'
                    ]
                ],
                'idInform' => [
                    'type' => 'int',
                    'required' => true,
                    'min_value' => 1
                ],
                'origem' => [
                    'type' => 'string',
                    'max_length' => 100,
                    'sanitize' => true
                ]
            ],
            
            // Módulo DVE
            'src/role/dve/Dve.php' => [
                'comm' => [
                    'type' => 'string',
                    'max_length' => 50,
                    'whitelist' => [
                        'view', 'open', 'done', 'entregavenciada', 'entregavenciadaok',
                        'comite', 'include', 'exclude', 'modalidade', 'salvatotal',
                        'send', 'editImporter', 'consultadve', 'libera_dve'
                    ]
                ],
                'export_value' => [
                    'type' => 'currency',
                    'max_length' => 20,
                    'required' => true
                ],
                'dve_number' => [
                    'type' => 'alphanumeric',
                    'max_length' => 20
                ]
            ],
            
            // Módulo Executive
            'src/role/executive/Executive.php' => [
                'comm' => [
                    'type' => 'string',
                    'max_length' => 50,
                    'whitelist' => [
                        'view', 'edit', 'create', 'approve', 'reject',
                        'report', 'export', 'analysis', 'proposal'
                    ]
                ],
                'proposal_id' => [
                    'type' => 'int',
                    'required' => true
                ],
                'risk_rating' => [
                    'type' => 'string',
                    'max_length' => 2,
                    'whitelist' => ['A', 'B', 'C', 'D', 'E']
                ]
            ]
        ];
    }
    
    /**
     * Regras para campos comuns em todo o sistema
     */
    public static function getCommonFieldRules() {
        return [
            // IDs
            'id' => [
                'type' => 'int',
                'min_value' => 1,
                'required' => true
            ],
            'user_id' => [
                'type' => 'int',
                'min_value' => 1
            ],
            'client_id' => [
                'type' => 'int',
                'min_value' => 1
            ],
            
            // Dados pessoais
            'name' => [
                'type' => 'string',
                'max_length' => 255,
                'min_length' => 2,
                'sanitize' => true
            ],
            'email' => [
                'type' => 'email',
                'max_length' => 255
            ],
            'phone' => [
                'type' => 'string',
                'max_length' => 20,
                'pattern' => '/^[\d\s\(\)\-\+]+$/'
            ],
            
            // Documentos
            'cnpj' => [
                'type' => 'cnpj'
            ],
            'cpf' => [
                'type' => 'cpf'
            ],
            
            // Datas
            'date' => [
                'type' => 'date'
            ],
            'datetime' => [
                'type' => 'datetime'
            ],
            'start_date' => [
                'type' => 'date'
            ],
            'end_date' => [
                'type' => 'date'
            ],
            
            // Valores monetários
            'value' => [
                'type' => 'currency',
                'max_length' => 15
            ],
            'amount' => [
                'type' => 'currency',
                'max_length' => 15
            ],
            'price' => [
                'type' => 'currency',
                'max_length' => 15
            ],
            
            // Arquivos
            'filename' => [
                'type' => 'filename',
                'max_length' => 255
            ],
            'file_path' => [
                'type' => 'path',
                'max_length' => 500
            ],
            
            // Paginação
            'page' => [
                'type' => 'int',
                'min_value' => 1,
                'default' => 1
            ],
            'limit' => [
                'type' => 'int',
                'min_value' => 1,
                'max_value' => 1000,
                'default' => 50
            ],
            'offset' => [
                'type' => 'int',
                'min_value' => 0,
                'default' => 0
            ],
            
            // Ordenação
            'sort' => [
                'type' => 'string',
                'max_length' => 50,
                'whitelist' => ['asc', 'desc']
            ],
            'order_by' => [
                'type' => 'string',
                'max_length' => 50,
                'pattern' => '/^[a-z_]+$/'
            ]
        ];
    }
    
    /**
     * Padrões de ataques conhecidos para detecção
     */
    public static function getAttackPatterns() {
        return [
            'xss' => [
                '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
                '/javascript\s*:/i',
                '/on\w+\s*=/i',
                '/<iframe/i',
                '/<object/i',
                '/<embed/i',
                '/vbscript\s*:/i',
                '/expression\s*\(/i',
                '/<svg.*onload/i',
                '/<img.*onerror/i'
            ],
            
            'sql_injection' => [
                '/(\bunion\b.*\bselect\b)|(\bselect\b.*\bunion\b)/i',
                '/\b(select|insert|update|delete|drop|create|alter)\b.*\b(from|into|table|database)\b/i',
                '/(\bor\b|\band\b)\s+\d+\s*=\s*\d+/i',
                '/\'\s*(or|and)\s*\'/i',
                '/\b(waitfor|delay)\b/i',
                '/\b(benchmark|sleep)\s*\(/i',
                '/\bconcat\s*\(/i'
            ],
            
            'command_injection' => [
                '/[;&|`$(){}[\]]/i',
                '/\b(wget|curl|nc|netcat|php|python|perl|ruby|bash|sh|cmd|powershell)\b/i',
                '/\b(eval|exec|system|passthru|shell_exec)\s*\(/i',
                '/\$\{.*\}/i',
                '/\`.*\`/i'
            ],
            
            'path_traversal' => [
                '/\.\.[\/\\]/i',
                '/\/(etc|usr|var|tmp|home|root)\//i',
                '/\\\\(windows|system32|boot)/i',
                '/%2e%2e/i',
                '/\.{2,}/i'
            ],
            
            'file_inclusion' => [
                '/\b(include|require)(_once)?\s*\(/i',
                '/file_get_contents\s*\(/i',
                '/fopen\s*\(/i',
                '/readfile\s*\(/i',
                '/file\s*\(/i'
            ],
            
            'ldap_injection' => [
                '/\*\)|&\|/i',
                '/\(\|/i',
                '/\)\(/i'
            ],
            
            'xpath_injection' => [
                '/\bor\b.*\btext\(\)/i',
                '/\band\b.*\btext\(\)/i',
                '/\bstring-length\b/i'
            ]
        ];
    }
    
    /**
     * User agents suspeitos
     */
    public static function getSuspiciousUserAgents() {
        return [
            'sqlmap',
            'nikto',
            'nmap',
            'masscan',
            'burp',
            'owasp',
            'zap',
            'acunetix',
            'netsparker',
            'w3af',
            'skipfish',
            'havij',
            'pangolin',
            'libwww-perl',
            'python-requests',
            'curl',
            'wget'
        ];
    }
    
    /**
     * IPs whitelist (nunca bloquear)
     */
    public static function getWhitelistIPs() {
        return [
            '127.0.0.1',
            '::1',
            // Adicionar IPs da Coface aqui
            // '192.168.1.0/24',
            // '10.0.0.0/8'
        ];
    }
    
    /**
     * Configurações específicas de módulos
     */
    public static function getModuleConfig($module) {
        $configs = [
            'credit' => [
                'strict_validation' => true,
                'require_csrf' => true,
                'log_all_actions' => true,
                'max_file_upload' => 5242880, // 5MB
                'allowed_file_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx']
            ],
            
            'client' => [
                'strict_validation' => true,
                'require_csrf' => true,
                'log_sensitive_data' => false,
                'password_complexity' => true,
                'session_timeout' => 3600 // 1 hora
            ],
            
            'dve' => [
                'strict_validation' => true,
                'require_csrf' => true,
                'validate_export_data' => true,
                'currency_validation' => true,
                'max_export_value' => 100000000 // 100M
            ],
            
            'executive' => [
                'strict_validation' => true,
                'require_csrf' => true,
                'log_all_decisions' => true,
                'require_approval_chain' => true,
                'max_proposal_value' => 50000000 // 50M
            ]
        ];
        
        return isset($configs[$module]) ? $configs[$module] : [];
    }
    
    /**
     * Configurações de emergência (para desabilitar validação se necessário)
     */
    public static function getEmergencyConfig() {
        return [
            'emergency_bypass' => false, // NUNCA definir como true em produção
            'emergency_contact' => 'admin@coface.com.br',
            'emergency_log_file' => dirname(__FILE__) . '/logs/emergency.log'
        ];
    }
}