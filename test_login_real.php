<?php
/**
 * TESTE DE LOGIN COM USUÁRIOS REAIS DO BANCO
 * Usando dados do dump SQL fornecido
 */

// Habilitar todos os erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🧪 TESTE DE LOGIN COM USUÁRIOS REAIS</h1>";
echo "<hr>";

// Simular dados do formulário
$_POST['login'] = 'executivo';
$_POST['password'] = 'teste'; // Vamos tentar descobrir a senha original
$_POST['validar'] = 'login';
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "<h2>1. Teste de Extensões</h2>";

echo "ODBC Extension: ";
if (extension_loaded('odbc')) {
    echo "<span style='color: green;'>✅ CARREGADA</span><br>";
} else {
    echo "<span style='color: red;'>❌ NÃO CARREGADA - ERRO PRINCIPAL!</span><br>";
    echo "<div style='background: #ffebee; padding: 15px; margin: 10px 0; border-radius: 4px; color: #c62828;'>";
    echo "<strong>SOLUÇÃO:</strong><br>";
    echo "<code>sudo apt-get install php-odbc && sudo systemctl restart apache2</code><br>";
    echo "ou para Windows: ativar extension=odbc no php.ini e reiniciar servidor";
    echo "</div>";
    
    // Parar aqui se ODBC não estiver disponível
    echo "<p>❌ Impossível continuar teste sem extensão ODBC.</p>";
    echo "<p>📋 <strong>DIAGNÓSTICO:</strong> Este é exatamente o problema que causa o erro 500!</p>";
    exit();
}

echo "<h2>2. Teste de Conexão de Banco</h2>";

try {
    echo "Incluindo dbOpen.php... ";
    require_once __DIR__ . '/src/dbOpen.php';
    echo "✅ OK<br>";
    
    echo "Verificando variável \$db... ";
    if (isset($db) && $db) {
        echo "✅ CONECTADO<br>";
        
        // Testar query simples
        echo "Testando query simples... ";
        $result = odbc_exec($db, "SELECT COUNT(*) as total FROM Users");
        if ($result) {
            $row = odbc_fetch_array($result);
            echo "✅ OK - Total usuários: " . $row['total'] . "<br>";
        } else {
            echo "❌ FALHOU na query<br>";
        }
        
    } else {
        echo "❌ SEM CONEXÃO<br>";
        throw new Exception("Database connection failed");
    }
    
} catch (Exception $e) {
    echo "<span style='color: red;'>❌ ERRO: " . $e->getMessage() . "</span><br>";
    echo "<p>📋 <strong>DIAGNÓSTICO:</strong> Problema de conexão com banco de dados!</p>";
    exit();
}

echo "<h2>3. Teste de Usuários do Banco</h2>";

// Dados reais do dump SQL
$usuarios_teste = [
    ['login' => 'executivo', 'perfil' => 'F', 'hash' => '$2a$07$9j6459sdgsrt959345h49uDJud3GA3JzbFx0ILWS9TXYlQv3rUWqO'],
    ['login' => 'mlegey', 'perfil' => 'F', 'hash' => '$2a$07$9j6459sdgsrt959345h49uv6BqDZ14runcAiec1YvCMH9EpFRGrbi']
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Login</th><th>Perfil</th><th>Hash no Banco</th><th>Estado</th></tr>";

foreach ($usuarios_teste as $user) {
    $login = $user['login'];
    $perfil = $user['perfil'];
    $hash_esperado = $user['hash'];
    
    echo "<tr>";
    echo "<td>$login</td>";
    echo "<td>$perfil</td>";
    echo "<td style='font-family: monospace; font-size: 10px;'>" . substr($hash_esperado, 0, 30) . "...</td>";
    
    // Testar se usuário existe no banco
    try {
        $query = "SELECT id, name, password FROM Users WHERE login = ? AND perfil = ?";
        $stmt = odbc_prepare($db, $query);
        
        if ($stmt && odbc_execute($stmt, [$login, $perfil])) {
            if (odbc_fetch_row($stmt)) {
                $id = odbc_result($stmt, 1);
                $name = odbc_result($stmt, 2);
                $password_hash = odbc_result($stmt, 3);
                
                echo "<td style='color: green;'>✅ ENCONTRADO<br>";
                echo "ID: $id<br>";
                echo "Nome: " . htmlspecialchars($name) . "<br>";
                echo "Hash: " . substr($password_hash, 0, 20) . "...</td>";
            } else {
                echo "<td style='color: red;'>❌ NÃO ENCONTRADO</td>";
            }
        } else {
            echo "<td style='color: red;'>❌ ERRO NA QUERY</td>";
        }
    } catch (Exception $e) {
        echo "<td style='color: red;'>❌ ERRO: " . $e->getMessage() . "</td>";
    }
    
    echo "</tr>";
}
echo "</table>";

echo "<h2>4. Teste de Autenticação com Classe User</h2>";

try {
    echo "Incluindo classe User... ";
    require_once __DIR__ . '/src/entity/user/User.php';
    echo "✅ OK<br>";
    
    // Testar com usuário executivo e algumas senhas comuns
    $senhas_teste = ['teste', '123456', 'admin', 'executivo', 'password', ''];
    
    echo "<h3>Testando usuário 'executivo' com senhas comuns:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Senha</th><th>Resultado</th></tr>";
    
    foreach ($senhas_teste as $senha) {
        echo "<tr>";
        echo "<td>" . ($senha === '' ? '(vazia)' : htmlspecialchars($senha)) . "</td>";
        
        try {
            $user = new User('executivo', $senha, 'F', $db);
            $userData = $user->getUserView();
            
            if ($userData && isset($userData['userID'])) {
                echo "<td style='color: green;'>✅ LOGIN SUCESSO!</td>";
                echo "</tr>";
                echo "</table>";
                
                echo "<div style='background: #e8f5e8; padding: 15px; margin: 10px 0; border-radius: 4px; color: #2e7d32;'>";
                echo "<h3>🎉 LOGIN BEM-SUCEDIDO!</h3>";
                echo "<p><strong>Usuário:</strong> executivo</p>";
                echo "<p><strong>Senha:</strong> " . ($senha === '' ? '(senha vazia)' : htmlspecialchars($senha)) . "</p>";
                echo "<p><strong>User ID:</strong> " . $userData['userID'] . "</p>";
                echo "<p><strong>Nome:</strong> " . htmlspecialchars($userData['nameUser']) . "</p>";
                echo "</div>";
                
                // Não precisamos testar mais senhas
                break;
                
            } else {
                echo "<td style='color: red;'>❌ Falhou</td>";
            }
        } catch (Exception $e) {
            echo "<td style='color: red;'>❌ ERRO: " . $e->getMessage() . "</td>";
        }
        
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<span style='color: red;'>❌ ERRO ao incluir User.php: " . $e->getMessage() . "</span><br>";
}

echo "<h2>5. Análise de Hash de Senha</h2>";

// Vamos analisar o hash para entender o padrão
echo "<p><strong>Hash exemplo:</strong> <code>\$2a\$07\$9j6459sdgsrt959345h49uDJud3GA3JzbFx0ILWS9TXYlQv3rUWqO</code></p>";
echo "<p>Este é um hash <strong>bcrypt</strong> com cost 07.</p>";
echo "<p>Para descobrir a senha original, seria necessário:</p>";
echo "<ul>";
echo "<li>Usar password_verify() com senha conhecida, ou</li>";
echo "<li>Fazer brute force (não recomendado), ou</li>";
echo "<li>Perguntar ao administrador do sistema a senha</li>";
echo "</ul>";

// Testar algumas senhas óbvias contra o hash
if (function_exists('password_verify')) {
    echo "<h3>Testando password_verify() contra hash:</h3>";
    $hash = '$2a$07$9j6459sdgsrt959345h49uDJud3GA3JzbFx0ILWS9TXYlQv3rUWqO';
    $senhas_obvias = ['123456', 'admin', 'executivo', 'teste', 'password', 'coface', 'siga'];
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Senha</th><th>Resultado</th></tr>";
    
    foreach ($senhas_obvias as $senha) {
        echo "<tr>";
        echo "<td>$senha</td>";
        if (password_verify($senha, $hash)) {
            echo "<td style='color: green;'>✅ MATCH! Esta é a senha!</td>";
        } else {
            echo "<td style='color: red;'>❌ Não confere</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<h2>📋 CONCLUSÃO DO TESTE</h2>";

if (!extension_loaded('odbc')) {
    echo "<p style='color: red; font-weight: bold;'>❌ PROBLEMA PRINCIPAL: Extensão ODBC não carregada</p>";
    echo "<p>Este é exatamente o motivo do erro 500. Instalar php-odbc resolve o problema.</p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ ODBC funcionando - erro 500 deve estar em outra parte</p>";
    echo "<p>Sistema está pronto para autenticação. Teste as credenciais corretas.</p>";
}

?>