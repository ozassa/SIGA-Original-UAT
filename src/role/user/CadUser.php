<?php

// Obtenha os parтmetros de entrada de forma segura
$id_Banco = isset($_REQUEST['id_Banco']) ? $_REQUEST['id_Banco'] : "";
$NomeUsuario = isset($_REQUEST['NomeUsuario']) ? $_REQUEST['NomeUsuario'] : "";
$LoginUsuario = isset($_REQUEST['LoginUsuario']) ? $_REQUEST['LoginUsuario'] : "";
$SenhaUsuario = isset($_REQUEST['SenhaUsuario']) ? crypt($_REQUEST['SenhaUsuario'], SALT) : "";
$EmailUsuario = isset($_REQUEST['EmailUsuario']) ? $_REQUEST['EmailUsuario'] : "";
$CPFUsuario = isset($_REQUEST['CPFUsuario']) ? str_replace(array(".", "-"), "", $_REQUEST['CPFUsuario']) : "";
$PerfilUsuario = isset($_REQUEST['PerfilUsuario']) ? $_REQUEST['PerfilUsuario'] : "";
$regiao = isset($_REQUEST['regiao']) ? $_REQUEST['regiao'] : false;
$cookie = session_id() . time();

if ($comm == "new") {
    // Inserчуo de um novo usuсrio
    $sql = "INSERT INTO Users (
                cookie, 
                name, 
                login, 
                password, 
                email, 
                state, 
                perfil, 
                alterSenha, 
                CPF, 
                i_Perfil,
                tentativaSenha
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = odbc_prepare($db, $sql);
    odbc_execute($stmt, [$cookie, $NomeUsuario, $LoginUsuario, $SenhaUsuario, $EmailUsuario, 0, 'B', date('Y-m-d'), $CPFUsuario, $PerfilUsuario, 0]);

    // Obter o њltimo ID inserido
    $sql_id = "SELECT @@IDENTITY AS id";
    $stmt_id = odbc_exec($db, $sql_id);
    $id_User = odbc_result($stmt_id, "id");

    // Inserir na tabela UserRole
    $sql = "INSERT INTO UserRole (idUser, idRole) VALUES (?, ?)";
    $stmt = odbc_prepare($db, $sql);
    odbc_execute($stmt, [$id_User, 19]);

    // Inserir na tabela UsersBanco
    $sqlU = "INSERT INTO UsersBanco (idUser, idBanco, t_Usuario) VALUES (?, ?, ?)";
    $stmtU = odbc_prepare($db, $sqlU);
    odbc_execute($stmtU, [$id_User, $id_Banco, 2]);
} else {
    $id_User = $_REQUEST['id_User'];

    // Atualizar os dados do usuсrio
    $sql = "UPDATE Users SET 
                name = ?, 
                login = ?, 
                email = ?, 
                CPF = ?, 
                i_Perfil = ?
            WHERE id = ?";
    $stmt = odbc_prepare($db, $sql);
    odbc_execute($stmt, [$NomeUsuario, $LoginUsuario, $EmailUsuario, $CPFUsuario, $PerfilUsuario, $id_User]);
}

if ($regiao) {
    // Remover regiѕes associadas ao usuсrio
    $sql = "DELETE FROM UsersNurim WHERE idUser = ?";
    $stmt = odbc_prepare($db, $sql);
    odbc_execute($stmt, [$id_User]);

    // Inserir as regiѕes selecionadas
    $sql = "INSERT INTO UsersNurim (idUser, idNurim) VALUES (?, ?)";
    $stmt = odbc_prepare($db, $sql);
    foreach ($regiao as $region) {
        odbc_execute($stmt, [$id_User, $region]);
    }
}

$location = "Location: " . $hostImagem . "/src/role/user/User.php";
header($location);
die;

