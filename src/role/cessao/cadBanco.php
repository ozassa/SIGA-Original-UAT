<?php 

$valida = null;
$success = false;
$codigo = (isset($_POST['codigo']) && ctype_digit($_POST['codigo'])) ? (int)$_POST['codigo'] : null;

// Validando e evitando SQL Injection com prepared statements
$q = "SELECT count(id) FROM Banco WHERE codigo = ?";
$stmt = odbc_prepare($db, $q);
odbc_execute($stmt, array($codigo));
$cont = odbc_result($stmt, 1);

if ($cont > 0) {
    $valida = 'Código já utilizado para outro banco';
}

if ($valida === null) {
    $name = $_POST['name'];
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : false;
    $senha = isset($_POST['senha']) ? crypt($_POST['senha'], SALT) : false;
    $tipo = $_POST['tipo'];
    
    $nameBc = 'Banco Parceiro - '.$name;

    // Inserindo dados do usuário com prepared statements
    $q = "INSERT INTO Users (name, login, password) VALUES (?, ?, ?)";
    $stmt = odbc_prepare($db, $q);
    odbc_execute($stmt, array($nameBc, $usuario, $senha));

    // Obtendo o último ID inserido
    $q = "SELECT max(id) FROM Users";
    $stmt = odbc_prepare($db, $q);
    odbc_execute($stmt);
    $idUser = odbc_result($stmt, 1);

    // Inserindo o papel do usuário
    $q = "INSERT INTO UserRole (idUser, idRole) VALUES (?, 21)";
    $stmt = odbc_prepare($db, $q);
    odbc_execute($stmt, array($idUser));

    // Inserindo dados do banco
    $q = "INSERT INTO Banco (codigo, name, tipo, idUser) VALUES (?, ?, ?, ?)";
    $stmt = odbc_prepare($db, $q);
    odbc_execute($stmt, array($codigo, $name, $tipo, $idUser));

    $success = true;
}

?>
