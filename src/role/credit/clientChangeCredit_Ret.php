<?php  

$idInform = $_REQUEST["idInform"];
$idNotification = $_REQUEST["idNotification"];

if (!empty($idNotification) && !empty($idInform)) {

    $query = "UPDATE NotificationR
              SET state = ?, i_Usuario = ?, d_Encerramento = GETDATE()
              WHERE idInform = ? AND id = ?";

    $stmt = odbc_prepare($db, $query);

    if ($stmt) {
        $params = ['2', $_SESSION["userID"], $idInform, $idNotification];
        odbc_execute($stmt, $params);
    }

    odbc_free_result($stmt); // Libera a conexão
}

?>
