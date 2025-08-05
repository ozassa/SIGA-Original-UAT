<?php

/**
 * Arquivo seguro para upload de certificados
 * Implementa validação rigorosa contra RCE
 */

// Configurações de segurança
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_DIR', 'C:\\xampp2\\htdocs\\coface-siga\\sign_cert\\files\\certs\\');
define('LOG_FILE', 'upload_security.log');

// Whitelist rigorosa de extensões permitidas
$allowed_extensions = array('pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'p7s', 'p7m', 'cer', 'crt', 'pem');

// Magic numbers para validação de tipo de arquivo
$magic_numbers = array(
    'pdf' => array('25504446'),
    'jpg' => array('FFD8FF'),
    'jpeg' => array('FFD8FF'),
    'png' => array('89504E47'),
    'doc' => array('D0CF11E0'),
    'docx' => array('504B0304'),
    'p7s' => array('308'), // PKCS#7
    'p7m' => array('308'), // PKCS#7
    'cer' => array('308'), // Certificate
    'crt' => array('308'), // Certificate
    'pem' => array('2D2D2D2D2D424547494E') // -----BEGIN
);

/**
 * Função para log de segurança
 */
function security_log($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $log_entry = "[{$timestamp}] [{$level}] IP: {$ip} - {$message}\n";
    file_put_contents(LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Valida a extensão do arquivo
 */
function validate_extension($filename, $allowed_extensions) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, $allowed_extensions);
}

/**
 * Valida magic number do arquivo
 */
function validate_magic_number($file_path, $extension, $magic_numbers) {
    if (!isset($magic_numbers[$extension])) {
        return false;
    }
    
    $handle = fopen($file_path, 'rb');
    if (!$handle) {
        return false;
    }
    
    $bytes = fread($handle, 20); // Lê os primeiros 20 bytes
    fclose($handle);
    
    $hex = strtoupper(bin2hex($bytes));
    
    foreach ($magic_numbers[$extension] as $magic) {
        if (strpos($hex, strtoupper($magic)) === 0) {
            return true;
        }
    }
    
    return false;
}

/**
 * Sanitiza nome do arquivo
 */
function sanitize_filename($filename) {
    // Remove caracteres perigosos
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    // Limita tamanho do nome
    $filename = substr($filename, 0, 100);
    return $filename;
}

/**
 * Gera nome randômico mantendo extensão
 */
function generate_random_filename($original_filename) {
    $extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    $random_name = bin2hex(random_bytes(16));
    return $random_name . '.' . $extension;
}

// Validação inicial
if (!isset($_FILES['bin']) || $_FILES['bin']['error'] !== UPLOAD_ERR_OK) {
    security_log('Upload falhou - erro no $_FILES', 'ERROR');
    http_response_code(400);
    die('Erro no upload do arquivo');
}

$file_info = $_FILES['bin'];
$original_filename = $file_info['name'];
$temp_file = $file_info['tmp_name'];
$file_size = $file_info['size'];

// Log da tentativa de upload
security_log('Tentativa de upload: ' . $original_filename . ' (' . $file_size . ' bytes)');

// Validação de tamanho
if ($file_size > MAX_FILE_SIZE) {
    security_log('Upload rejeitado - arquivo muito grande: ' . $file_size . ' bytes', 'SECURITY');
    http_response_code(413);
    die('Arquivo muito grande. Máximo permitido: ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB');
}

if ($file_size <= 0) {
    security_log('Upload rejeitado - arquivo vazio', 'SECURITY');
    http_response_code(400);
    die('Arquivo vazio não é permitido');
}

// Validação de extensão
if (!validate_extension($original_filename, $allowed_extensions)) {
    security_log('Upload rejeitado - extensão não permitida: ' . $original_filename, 'SECURITY');
    http_response_code(415);
    die('Tipo de arquivo não permitido. Extensões permitidas: ' . implode(', ', $allowed_extensions));
}

$extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

// Validação de magic number
if (!validate_magic_number($temp_file, $extension, $magic_numbers)) {
    security_log('Upload rejeitado - magic number inválido: ' . $original_filename, 'SECURITY');
    http_response_code(415);
    die('Arquivo corrompido ou tipo inválido');
}

// Gera nome seguro para o arquivo
$safe_filename = generate_random_filename($original_filename);
$destination = UPLOAD_DIR . $safe_filename;

// Verifica se diretório de destino existe e é gravável
if (!is_dir(UPLOAD_DIR)) {
    if (!mkdir(UPLOAD_DIR, 0755, true)) {
        security_log('Erro ao criar diretório de upload', 'ERROR');
        http_response_code(500);
        die('Erro interno do servidor');
    }
}

if (!is_writable(UPLOAD_DIR)) {
    security_log('Diretório de upload não é gravável', 'ERROR');
    http_response_code(500);
    die('Erro interno do servidor');
}

// Move arquivo com validação adicional
if (move_uploaded_file($temp_file, $destination)) {
    // Validação pós-upload - verifica novamente o magic number
    if (!validate_magic_number($destination, $extension, $magic_numbers)) {
        unlink($destination); // Remove arquivo inválido
        security_log('Upload rejeitado pós-movimento - magic number inválido', 'SECURITY');
        http_response_code(415);
        die('Arquivo corrompido ou tipo inválido');
    }
    
    // Define permissões restritivas
    chmod($destination, 0644);
    
    security_log('Upload bem-sucedido: ' . $original_filename . ' -> ' . $safe_filename, 'SUCCESS');
    
    // Salva mapeamento do arquivo original para o nome seguro (se necessário)
    $mapping_file = UPLOAD_DIR . 'file_mapping.txt';
    $mapping_entry = date('Y-m-d H:i:s') . ',' . $original_filename . ',' . $safe_filename . "\n";
    file_put_contents($mapping_file, $mapping_entry, FILE_APPEND | LOCK_EX);
    
    echo json_encode(array(
        'status' => 'success',
        'message' => 'Upload realizado com sucesso',
        'filename' => $safe_filename
    ));
} else {
    security_log('Falha ao mover arquivo: ' . $original_filename, 'ERROR');
    http_response_code(500);
    die('Erro ao salvar arquivo');
}
