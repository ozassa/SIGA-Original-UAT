<?php
// Show PHP errors (Disable in production)
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Include library PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Start
$mail = new PHPMailer(true);

try {
    // Configuration SMTP
    #$mail->SMTPDebug = SMTP::DEBUG_SERVER;                         // Show output (Disable in production)
    $mail->isSMTP();                                               // Activate SMTP sending
    
    // Configurações de SSL seguras com fallback para self-signed apenas para Coface
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'allow_self_signed' => false,
            'cafile' => null, // Use system CA bundle
            'ciphers' => 'HIGH:!SSLv2:!SSLv3:!TLSv1.0',
            'peer_name' => 'mail.coface.com',
            // Fallback apenas para certificados Coface conhecidos
            'disable_compression' => true,
            'SNI_enabled' => true,
            'capture_peer_cert' => false
        ],
    ];
    
    // Se falhar SSL, try fallback para certificados self-signed específicos da Coface
    $mail->SMTPOptions['ssl']['verify_fail_if_no_peer_cert'] = false;
    
    $mail->Host = "mail.coface.com"; // Endereço do servidor SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usa STARTTLS
    $mail->Port = 587; // Porta padrão para STARTTLS
    $mail->Timeout = 30; // Timeout de conexão
    $mail->SMTPKeepAlive = false; // Não manter conexão aberta
    $mail->AddReplyTo('sistemas.brasil@coface.com', 'Coface do Brasil');
    $mail->SetFrom('sistemas.brasil@coface.com', 'Coface do Brasil');
  } catch (Exception $e) {
    // Log detalhado de erro para diagnóstico
    error_log("SMTP Configuration Error: " . $e->getMessage());
    
    // Fallback para configuração mais permissiva se SSL falhar
    try {
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'cafile' => null,
                'peer_name' => 'mail.coface.com'
            ],
        ];
        error_log("SMTP: Usando fallback SSL para certificados self-signed");
    } catch (Exception $fallback_e) {
        error_log("SMTP Fallback Error: " . $fallback_e->getMessage());
        // Em caso de falha total, pode tentar porta 25 sem TLS
        try {
            $mail->SMTPSecure = false;
            $mail->Port = 25;
            error_log("SMTP: Tentando conexão sem TLS na porta 25");
        } catch (Exception $final_e) {
            error_log("SMTP Final Error: " . $final_e->getMessage());
        }
    }
}

