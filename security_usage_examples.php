<?php
/**
 * EXEMPLOS DE USO - Sistema de Segurança SIGA
 * 
 * Este arquivo demonstra como usar as funções de segurança implementadas
 * para proteger o sistema contra XSS e CSRF.
 */

// Incluir as funções de segurança
require_once("security_functions.php");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Exemplos de Uso - Segurança SIGA</title>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Guia de Uso - Sistema de Segurança SIGA</h1>
    
    <h2>1. Proteção CSRF em Formulários</h2>
    <p>Para adicionar proteção CSRF a qualquer formulário, inclua o token:</p>
    
    <form method="post" action="exemplo.php">
        <input type="text" name="nome" placeholder="Nome">
        <input type="email" name="email" placeholder="Email">
        
        <!-- TOKEN CSRF - SEMPRE INCLUIR EM FORMULÁRIOS -->
        <?php echo csrf_token_field(); ?>
        
        <button type="submit">Enviar</button>
    </form>
    
    <h2>2. Sanitização de Output (Proteção XSS)</h2>
    
    <?php
    // Exemplo de dados que poderiam vir do banco ou usuário
    $nome_usuario = "<script>alert('XSS')</script>João Silva";
    $url_perfil = "javascript:alert('XSS')";
    $bio_usuario = "<img src=x onerror=alert('XSS')>Desenvolvedor";
    ?>
    
    <h3>❌ ERRADO - Vulnerável a XSS:</h3>
    <code>
        &lt;p&gt;Nome: <?php echo htmlspecialchars($nome_usuario); ?>&lt;/p&gt;<br>
        &lt;a href="<?php echo htmlspecialchars($url_perfil); ?>"&gt;Perfil&lt;/a&gt;<br>
        &lt;p&gt;Bio: <?php echo htmlspecialchars($bio_usuario); ?>&lt;/p&gt;
    </code>
    
    <h3>✅ CORRETO - Protegido contra XSS:</h3>
    <p>Nome: <?php echo safe_output($nome_usuario); ?></p>
    <a href="<?php echo safe_output($url_perfil, 'url'); ?>">Perfil</a>
    <p>Bio: <?php echo safe_output($bio_usuario); ?></p>
    
    <h2>3. Validação Segura de Entrada</h2>
    
    <?php
    // Exemplos de validação segura de dados de entrada
    $_REQUEST['idade'] = "25'; DROP TABLE users; --";
    $_REQUEST['email'] = "<script>alert('XSS')</script>user@test.com";
    $_REQUEST['nome'] = str_repeat('A', 1000); // String muito longa
    
    $idade = safe_request('idade', 'int', 0);
    $email = safe_request('email', 'email', '');
    $nome = safe_request('nome', 'string', '', 100);
    ?>
    
    <h3>Dados Sanitizados:</h3>
    <ul>
        <li>Idade: <?php echo safe_output($idade); ?> (<?php echo gettype($idade); ?>)</li>
        <li>Email: <?php echo safe_output($email); ?> (válido: <?php echo $email ? 'Sim' : 'Não'; ?>)</li>
        <li>Nome: <?php echo safe_output($nome); ?> (comprimento: <?php echo strlen($nome); ?>)</li>
    </ul>
    
    <h2>4. Contextos de Sanitização</h2>
    
    <?php $dado_perigoso = "<script>alert('XSS')</script>"; ?>
    
    <table border="1">
        <tr>
            <th>Contexto</th>
            <th>Função</th>
            <th>Resultado</th>
        </tr>
        <tr>
            <td>HTML</td>
            <td>safe_output($dado, 'html')</td>
            <td><?php echo safe_output($dado_perigoso, 'html'); ?></td>
        </tr>
        <tr>
            <td>Atributo HTML</td>
            <td>safe_output($dado, 'attr')</td>
            <td title="<?php echo safe_output($dado_perigoso, 'attr'); ?>">Hover para ver</td>
        </tr>
        <tr>
            <td>JavaScript</td>
            <td>safe_output($dado, 'js')</td>
            <td><?php echo safe_output($dado_perigoso, 'js'); ?></td>
        </tr>
        <tr>
            <td>URL</td>
            <td>safe_output($dado, 'url')</td>
            <td><?php echo safe_output($dado_perigoso, 'url'); ?></td>
        </tr>
    </table>
    
    <h2>5. Validação CSRF no Backend</h2>
    
    <pre><code>
// No início de arquivos que processam formulários POST:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        // Token inválido - redirecionar com erro
        header("Location: formulario.php?erro=csrf");
        exit();
    }
}

// Ou usar o middleware automático:
csrf_middleware(); // Redireciona automaticamente se token inválido
    </code></pre>
    
    <h2>6. Exemplo de Página Segura Completa</h2>
    
    <pre><code>
&lt;?php
// 1. Incluir funções de segurança
require_once("security_functions.php");

// 2. Verificar CSRF se for POST
csrf_middleware();

// 3. Validar entrada de forma segura
$nome = safe_request('nome', 'string', '', 100);
$email = safe_request('email', 'email', '');
$idade = safe_request('idade', 'int', 0);

// 4. Processar dados...
if ($nome && $email && $idade) {
    // Salvar no banco usando prepared statements
    // ...
}
?&gt;

&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;Página Segura&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Bem-vindo, &lt;?php echo safe_output($nome); ?&gt;!&lt;/h1&gt;
    
    &lt;form method="post"&gt;
        &lt;input type="text" name="nome" value="&lt;?php echo safe_output($nome, 'attr'); ?&gt;" required&gt;
        &lt;input type="email" name="email" value="&lt;?php echo safe_output($email, 'attr'); ?&gt;" required&gt;
        &lt;input type="number" name="idade" value="&lt;?php echo safe_output($idade); ?&gt;" required&gt;
        
        &lt;!-- Token CSRF --&gt;
        &lt;?php echo csrf_token_field(); ?&gt;
        
        &lt;button type="submit"&gt;Salvar&lt;/button&gt;
    &lt;/form&gt;
&lt;/body&gt;
&lt;/html&gt;
    </code></pre>
    
    <h2>7. Headers de Segurança</h2>
    <p>Os seguintes headers de segurança são automaticamente definidos:</p>
    <ul>
        <li><strong>X-Frame-Options:</strong> SAMEORIGIN (proteção contra clickjacking)</li>
        <li><strong>X-XSS-Protection:</strong> 1; mode=block (proteção XSS do browser)</li>
        <li><strong>X-Content-Type-Options:</strong> nosniff (evita MIME sniffing)</li>
        <li><strong>Referrer-Policy:</strong> strict-origin-when-cross-origin</li>
        <li><strong>Content-Security-Policy:</strong> Política básica de segurança de conteúdo</li>
    </ul>
    
    <h2>8. Logs de Segurança</h2>
    <p>Tentativas de ataques são automaticamente registradas em:</p>
    <code>/logs/security/security_YYYY-MM-DD.log</code>
    
    <h2>⚠️ IMPORTANTE</h2>
    <div style="background: #ffebee; padding: 15px; border-left: 5px solid #f44336;">
        <p><strong>SEMPRE:</strong></p>
        <ul>
            <li>Use <code>safe_output()</code> para qualquer dado que será exibido</li>
            <li>Inclua <code>csrf_token_field()</code> em todos os formulários</li>
            <li>Use <code>safe_request()</code> para validar entrada de usuário</li>
            <li>Use prepared statements para consultas SQL</li>
            <li>Teste regularmente com ferramentas de segurança</li>
        </ul>
    </div>
    
    <footer style="margin-top: 50px; padding: 20px; background: #f5f5f5; text-align: center;">
        <p>Sistema de Segurança SIGA - Proteção contra XSS e CSRF implementada com sucesso!</p>
        <p><em>Funcionalidades mantidas, frontend inalterado, sistema protegido.</em></p>
    </footer>
</body>
</html>