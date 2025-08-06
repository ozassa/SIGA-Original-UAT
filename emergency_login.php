<?php
/**
 * PÁGINA DE LOGIN DE EMERGÊNCIA
 * Para contornar erro 500 no Access.php
 */

// Inicializar sessão
session_start();

// Mostrar informação sobre o problema
?>
<!DOCTYPE html>
<html>
<head>
    <title>SIGA - Login de Emergência</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f0f0f0; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        .error { background: #ffebee; border: 1px solid #f44336; padding: 15px; border-radius: 4px; color: #c62828; margin-bottom: 20px; }
        .info { background: #e3f2fd; border: 1px solid #2196f3; padding: 15px; border-radius: 4px; color: #1565c0; margin-bottom: 20px; }
        .solution { background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px; }
        h1 { color: #1976d2; text-align: center; }
        h2 { color: #424242; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .diagnostic { margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #1976d2; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #1565c0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚨 SIGA - Sistema de Emergência</h1>
        
        <div class="error">
            <h2>❌ Problema Identificado: Erro 500 no Access.php</h2>
            <p><strong>Causa:</strong> O sistema está tentando usar funções ODBC mas a extensão PHP ODBC não está instalada ou carregada no servidor.</p>
        </div>

        <div class="info">
            <h2>🔍 Diagnóstico Automático</h2>
            <div class="diagnostic">
                <p><strong>Extensão ODBC:</strong> 
                <?php if (extension_loaded('odbc')): ?>
                    <span style="color: green;">✅ CARREGADA</span>
                <?php else: ?>
                    <span style="color: red;">❌ NÃO CARREGADA - ESTE É O PROBLEMA!</span>
                <?php endif; ?>
                </p>

                <p><strong>Versão PHP:</strong> <?php echo phpversion(); ?></p>
                
                <p><strong>Servidor Web:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Não identificado'; ?></p>
                
                <p><strong>Extensões Carregadas:</strong></p>
                <div style="max-height: 100px; overflow-y: auto; background: #f9f9f9; padding: 10px; margin: 10px 0;">
                    <?php
                    $extensions = get_loaded_extensions();
                    sort($extensions);
                    echo implode(', ', $extensions);
                    ?>
                </div>
            </div>
        </div>

        <div class="solution">
            <h2>💡 Soluções para o Administrador</h2>
            
            <h3>Para Ubuntu/Debian:</h3>
            <code>sudo apt-get update && sudo apt-get install php-odbc</code><br>
            <code>sudo systemctl restart apache2</code> (ou nginx)
            
            <h3>Para CentOS/RHEL:</h3>
            <code>sudo yum install php-odbc</code><br>
            <code>sudo systemctl restart httpd</code>
            
            <h3>Para Windows/XAMPP:</h3>
            <p>1. Abrir <code>php.ini</code></p>
            <p>2. Descomentar: <code>extension=odbc</code></p>
            <p>3. Reiniciar Apache</p>
            
            <h3>Verificação:</h3>
            <p>Após instalar, recarregue esta página. Se ODBC aparecer como "CARREGADA", o sistema funcionará normalmente.</p>
        </div>

        <div class="diagnostic">
            <h2>🛠️ Ferramentas de Diagnóstico</h2>
            <p>Para mais detalhes técnicos:</p>
            <a href="test_access_direct.php" class="btn">🔍 Teste Completo</a>
            <a href="access_simple_test.php" class="btn">📋 Teste Simplificado</a>
            <?php if (extension_loaded('odbc')): ?>
                <a href="index.php" class="btn">🏠 Tentar Login Normal</a>
            <?php endif; ?>
        </div>

        <div class="info">
            <h2>📞 Para Suporte Técnico</h2>
            <p><strong>Erro:</strong> 500 Internal Server Error no Access.php</p>
            <p><strong>Causa:</strong> Extensão PHP ODBC não instalada</p>
            <p><strong>Status ODBC:</strong> <?php echo extension_loaded('odbc') ? 'Carregada' : 'NÃO CARREGADA'; ?></p>
            <p><strong>Versão PHP:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Data/Hora:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>
</body>
</html>