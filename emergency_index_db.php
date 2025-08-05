<?php
/**
 * Emergency Index for Database Connection Failures
 * 
 * This page is displayed when the database connection fails
 * to provide a user-friendly error message and prevent system exposure.
 */

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'http://' . $_SERVER['HTTP_HOST'] . '/siga/';
$error_type = isset($_GET['erro']) ? $_GET['erro'] : 'unknown';

// Log the database error for system administrators
error_log("CRITICAL: Database connection failure - Error type: $error_type - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SIGA - Sistema Temporariamente Indisponível - COFACE</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/geral.css" rel="stylesheet" type="text/css" />
    <style>
        .error-container {
            background-color: #fff;
            border: 2px solid #d32f2f;
            border-radius: 8px;
            padding: 30px;
            margin: 50px auto;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .error-icon {
            color: #d32f2f;
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .error-title {
            color: #d32f2f;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .error-message {
            color: #666;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        
        .retry-button {
            background-color: #1976d2;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .retry-button:hover {
            background-color: #1565c0;
        }
        
        .support-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>

<body class="fundoimagem">
    <div id="entradageral">
        <div class="error-container">
            <div class="error-icon">⚠</div>
            
            <div class="error-title">Sistema Temporariamente Indisponível</div>
            
            <div class="error-message">
                <?php if ($error_type == 'db'): ?>
                    O sistema está temporariamente indisponível devido a problemas de conectividade com o banco de dados.
                <?php elseif ($error_type == 'db_query'): ?>
                    Ocorreu um erro ao processar sua solicitação. O problema foi reportado à equipe técnica.
                <?php elseif ($error_type == 'auth_system'): ?>
                    O sistema de autenticação está temporariamente indisponível. Tente novamente em alguns minutos.
                <?php else: ?>
                    O sistema está passando por manutenção. Tente acessar novamente em alguns minutos.
                <?php endif; ?>
            </div>
            
            <a href="index.php" class="retry-button">Tentar Novamente</a>
            
            <div class="support-info">
                <strong>Código do Erro:</strong> <?php echo htmlspecialchars($error_type, ENT_QUOTES, 'UTF-8'); ?><br>
                <strong>Horário:</strong> <?php echo date('d/m/Y H:i:s'); ?><br><br>
                Se o problema persistir, entre em contato com o suporte técnico.
            </div>
        </div>
    </div>
</body>
</html>