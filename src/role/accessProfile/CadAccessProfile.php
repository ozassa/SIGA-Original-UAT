<?php

$Descricao_Perfil = $_REQUEST['Descricao_Perfil'] ?? "";
$Situacao_Perfil = $_REQUEST['Situacao_Perfil'] ?? "";
$funcoes = $_REQUEST['funcoes'] ?? false;
$cookie = session_id() . time();

if ($comm == "new") {
    $sql = "INSERT INTO Perfil (Descricao, t_Perfil, s_Perfil) VALUES (?, 3, ?)";
    $stmt = odbc_prepare($db, $sql);
    odbc_execute($stmt, [$Descricao_Perfil, $Situacao_Perfil]);

    $sql_id = "SELECT @@IDENTITY AS 'id'"; // Pega o último ID da tabela
    $stmt_id = odbc_exec($db, $sql_id);
    $i_Perfil = odbc_result($stmt_id, 1); // Executa o select
} else {
    $i_Perfil = $_REQUEST['i_Perfil'];

    $sql = "UPDATE Perfil SET Descricao = ?, s_Perfil = ? WHERE i_Perfil = ?";
    $stmt = odbc_prepare($db, $sql);
    odbc_execute($stmt, [$Descricao_Perfil, $Situacao_Perfil, $i_Perfil]);
}

$sql = "DELETE FROM Perfil_Tela WHERE i_Perfil = ?";
$stmt = odbc_prepare($db, $sql);
odbc_execute($stmt, [$i_Perfil]);

if ($funcoes) {
    $sql = "INSERT INTO Perfil_Tela (i_Perfil, i_Tela, Leitura, Escrita, Exclusao) VALUES (?, ?, 1, 0, 0)";
    $stmt = odbc_prepare($db, $sql);
    foreach ($funcoes as $funcao) {
        odbc_execute($stmt, [$i_Perfil, $funcao]);
    }
}

$location = "Location: " . $host . "src/role/accessProfile/AccessProfile.php";
header($location);

?>
