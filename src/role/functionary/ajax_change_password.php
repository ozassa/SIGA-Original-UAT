<?php

require_once("../rolePrefix.php");

header('Content-type: application/json');

$qry = "SELECT a.name 
        FROM Role a 
        INNER JOIN UserRole b ON b.idRole = a.id 
        INNER JOIN Users c ON c.id = b.idUser
        WHERE c.id = ?";
$stmt = odbc_prepare($db, $qry);
odbc_execute($stmt, [$user]);

$role = [];

while (odbc_fetch_row($stmt)) {
    $name = odbc_result($stmt, 'name');
    $role[$name] = true;
}

if (!check_menu(['generalManager'], $role) && !check_menu(['clientAdmin'], $role)) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso não autorizado.']);
    exit;
}

if (!isset($_POST['userID'], $_POST['password']) || !is_numeric($_POST['userID'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}

$usuarioID = (int)$_POST['userID'];
$password = trim($_POST['password']);

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$sqlUp = "UPDATE Users SET password = ?, alterSenha = ? WHERE id = ?";
$rsSqlUp = odbc_prepare($db, $sqlUp);
$result = odbc_execute($rsSqlUp, [$hashedPassword, date('Y-m-d'), $usuarioID]);

echo json_encode($result);
?>
