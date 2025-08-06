<?php
/**
 * DESCOBERTA DE SENHA PARA USUÁRIO EXECUTIVO
 * Para teste em desenvolvimento apenas
 */

echo "<h1>🔍 Descoberta de Senha - Usuário Executivo</h1>";

// Hash conhecido do dump SQL
$hash_executivo = '$2a$07$9j6459sdgsrt959345h49uDJud3GA3JzbFx0ILWS9TXYlQv3rUWqO';

echo "<p><strong>Hash alvo:</strong> <code>$hash_executivo</code></p>";
echo "<p><strong>Tipo:</strong> bcrypt, cost 7</p>";

// Lista extensa de senhas comuns para sistemas corporativos
$senhas_comuns = [
    // Básicas
    '123', '1234', '12345', '123456', '1234567', '12345678', '123456789',
    
    // Administrativas
    'admin', 'administrator', 'root', 'master', 'executivo', 'exec',
    
    // Relacionadas ao sistema
    'siga', 'coface', 'sistema', 'password', 'senha', 'pass',
    
    // Anos comuns
    '2020', '2021', '2022', '2023', '2024', '2025',
    
    // Datas
    '01012020', '01012021', '01012022', '01012023', '01012024',
    
    // Combinações
    'admin123', 'siga123', 'coface123', 'executivo123', 'master123',
    'admin2020', 'admin2021', 'admin2022', 'admin2023', 'admin2024',
    'siga2020', 'siga2021', 'siga2022', 'siga2023', 'siga2024',
    
    // Empresariais
    'Coface123', 'COFACE123', 'Siga123', 'SIGA123',
    'Coface2024', 'COFACE2024', 'Siga2024', 'SIGA2024',
    
    // Padrões corporativos
    'P@ssw0rd', 'P@ssword123', 'Admin123!', 'Master123!',
    
    // Senhas vazias e simples
    '', ' ', 'a', 'aa', 'aaa',
    
    // Outras possibilidades
    'test', 'teste', 'testing', 'demo', 'example',
    'user', 'guest', 'public', 'default'
];

echo "<h2>🔎 Testando " . count($senhas_comuns) . " senhas comuns...</h2>";

$encontrou = false;
$senha_correta = '';

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th style='width: 150px;'>Senha</th><th>Resultado</th></tr>";

foreach ($senhas_comuns as $senha) {
    echo "<tr>";
    echo "<td><code>" . ($senha === '' ? '(vazia)' : htmlspecialchars($senha)) . "</code></td>";
    
    if (password_verify($senha, $hash_executivo)) {
        echo "<td style='color: green; font-weight: bold;'>🎉 ENCONTRADA!</td>";
        $encontrou = true;
        $senha_correta = $senha;
        echo "</tr>";
        break;
    } else {
        echo "<td style='color: #ccc;'>❌</td>";
    }
    echo "</tr>";
    
    // Flush output para mostrar progresso
    if (ob_get_level()) ob_flush();
    flush();
}

echo "</table>";

if ($encontrou) {
    echo "<div style='background: #e8f5e8; border: 2px solid #4caf50; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
    echo "<h2 style='color: #2e7d32;'>🎉 SENHA DESCOBERTA!</h2>";
    echo "<p style='font-size: 18px;'><strong>Usuário:</strong> <code>executivo</code></p>";
    echo "<p style='font-size: 18px;'><strong>Senha:</strong> <code style='background: yellow; padding: 5px;'>" . 
         ($senha_correta === '' ? '(senha vazia)' : htmlspecialchars($senha_correta)) . "</code></p>";
    echo "<p>Agora você pode testar o login com estas credenciais!</p>";
    echo "</div>";
    
    echo "<h2>🧪 Teste Direto no Sistema</h2>";
    echo "<form method='post' action='src/role/access/Access.php' style='background: #f0f0f0; padding: 20px; border-radius: 8px;'>";
    echo "<p><strong>Teste automaticamente:</strong></p>";
    echo "<input type='hidden' name='login' value='executivo'>";
    echo "<input type='hidden' name='password' value='" . htmlspecialchars($senha_correta) . "'>";
    echo "<input type='hidden' name='validar' value='login'>";
    echo "<button type='submit' style='background: #4caf50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>🚀 Fazer Login com Senha Descoberta</button>";
    echo "</form>";
    
} else {
    echo "<div style='background: #ffebee; border: 2px solid #f44336; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
    echo "<h2 style='color: #c62828;'>❌ Senha não encontrada</h2>";
    echo "<p>A senha não está na lista de senhas comuns testadas.</p>";
    echo "<p><strong>Próximos passos:</strong></p>";
    echo "<ul>";
    echo "<li>Consultar o administrador do sistema sobre a senha</li>";
    echo "<li>Verificar documentação ou configuração do sistema</li>";
    echo "<li>Usar ferramentas de reset de senha se disponíveis</li>";
    echo "</ul>";
    echo "</div>";
}

// Informações adicionais sobre o hash
echo "<h2>ℹ️ Informações Técnicas</h2>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Propriedade</th><th>Valor</th></tr>";
echo "<tr><td>Algoritmo</td><td>bcrypt</td></tr>";
echo "<tr><td>Cost</td><td>7</td></tr>";
echo "<tr><td>Salt</td><td>9j6459sdgsrt959345h49u</td></tr>";
echo "<tr><td>Hash completo</td><td style='font-family: monospace; font-size: 11px;'>$hash_executivo</td></tr>";
echo "</table>";

echo "<p><em>Este teste é apenas para desenvolvimento. Em produção, use senhas fortes e únicas!</em></p>";

?>