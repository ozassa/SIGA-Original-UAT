<?php
/*
Motor de busca
*/

session_start();

$query = "INSERT INTO Contact (idInform, name, tel, fax, email, title, notificationForChangeCredit, userChangeContact, i_Tipo_Contato) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$params = [
    $_POST['idInform'],
    $_POST['Name'],
    $_POST['tel'],
    $_POST['fax'],
    $_POST['email'],
    $_POST['cargo'],
    $_POST['emailCredit'],
    $_SESSION['userID'],
    $_POST['tipo_contato']
];

$contact = odbc_prepare($db, $query);
$result = odbc_execute($contact, $params);

if ($result) {
    $msg = "Sucesso na inclusão do Contato.";
} else {
    $msg = "Erro ao incluir o contato.";
}

echo $msg;
?>
