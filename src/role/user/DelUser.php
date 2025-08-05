<?php
if (!isset($_REQUEST["id_User"])) {
    $location = "Location: " . $host . "src/role/user/User.php";
    header($location);
    exit;
}

// Obtém o id_User de forma segura
$id_User = $_REQUEST["id_User"];

try {
    // Consulta preparada para evitar SQL Injection
    $sql = "UPDATE Users SET state = 1, d_Cancelamento = ? WHERE id = ?";
    $stmt = odbc_prepare($db, $sql);

    // Parâmetros seguros
    $currentDate = date('Y-m-d H:i:s');
    $params = [$currentDate, $id_User];

    if (!odbc_execute($stmt, $params)) {
        throw new Exception("Erro ao executar a consulta SQL.");
    }
} catch (Exception $e) {
    // Log de erro (opcional, pode ser melhorado para fins de auditoria)
    error_log($e->getMessage());
    exit("Erro interno. Tente novamente mais tarde.");
} finally {
    // Sempre feche a conexão com o banco
    odbc_close($db);
}

// Redireciona após a execução
$location = "Location: " . $host . "src/role/user/User.php";
header($location);
exit;
?>
