<?php  


    $Name = $field -> getField (Name);
	$tel = $field -> getField (tel);
	$fax = $field -> getField (fax);
	$email = $field -> getField (email);
	$cargo = $field -> getField (cargo);
	$emailCredit = $field -> getField (emailCredit);

	$idContact = $field -> getField (idContact);

	if ($idContact) {
    $query = "UPDATE Contact 
              SET name = ?, 
                  tel = ?, 
                  fax = ?, 
                  email = ?, 
                  title = ?, 
                  notificationForChangeCredit = ?, 
                  i_Tipo_Contato = ? 
              WHERE id = ?";
    $stmt = odbc_prepare($db, $query);
    odbc_execute($stmt, [$Name, $tel, $fax, $email, $cargo, $emailCredit, $_POST['tipo_contato'], $idContact]);
} else {
    $query = "UPDATE Inform 
              SET contact = ?, 
                  telContact = ?, 
                  faxContact = ?, 
                  emailContact = ?, 
                  ocupationContact = ? 
              WHERE id = ?";
    $stmt = odbc_prepare($db, $query);
    odbc_execute($stmt, [$Name, $tel, $fax, $email, $cargo, $idInform]);
}
$contact = $stmt;
odbc_free_result($stmt);

	
	if ($contact)
	  $msg1 = "Sucesso na atualizaחדo do banco de contatos.";
?>