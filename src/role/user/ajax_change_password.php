<?php 
    require_once ("../rolePrefix.php");

    header('Content-type: text/json');

    $userID = $_POST['userID'];
    $password = crypt($_POST['password'], SALT);

    $sqlUp = "UPDATE Users SET password = ?, alterSenha = ? WHERE id = ?";
    $params = [$password, date('Y-m-d'), $userID];
    $rsSqlUp = odbc_prepare($db, $sqlUp);

    if ($rsSqlUp === false) {
        echo json_encode(["success" => false, "error" => "Failed to prepare SQL statement."]);
        exit;
    }

    $result = odbc_execute($rsSqlUp, $params);

    if ($result === false) {
        echo json_encode(["success" => false, "error" => "Failed to execute SQL statement."]);
    } else {
        echo json_encode(["success" => true]);
    }
?>
