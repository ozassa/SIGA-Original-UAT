<?php  
die();
/*
Motor de Busca
*/

////////////////

	$qQ = "SELECT a.id, a.idImporter, a.idInform, a.name, a.tel, a.fax, a.title, 
               a.email, a.notificationForChangeCredit, a.i_Tipo_Contato, c.Descricao 
        FROM Contact a
        INNER JOIN Inform b ON b.id = a.idInform 
        LEFT JOIN Tipo_Contato c ON c.i_Tipo_Contato = a.i_Tipo_Contato 
        WHERE a.idInform = ?";

if (isset($_REQUEST['searchContact'])) {
    $searchContact = '%' . strtolower($_REQUEST['searchContact']) . '%';
    $qQ .= " AND LOWER(a.name) LIKE ?";
}

$stmt = odbc_prepare($db, $qQ);

if (isset($_REQUEST['searchContact'])) {
    odbc_execute($stmt, [$idInform, $searchContact]);
} else {
    odbc_execute($stmt, [$idInform]);
}
odbc_free_result($stmt);
$cC = $stmt;


   // print $qQ;
	
	if($cC) $msg = "OK";
	else   $msg = "ERRO";

///// Table Contact  //////////////

	///////////////
	
	
		// Query principal para buscar contatos
$query = "SELECT contact, ocupationContact, emailContact, telContact, faxContact, id
          FROM Inform
          WHERE id = ?";

$params = [$idInform];

// Verifica se há um filtro de busca e adiciona o parâmetro
if (!empty($_REQUEST['searchContact'])) {
    $query .= " AND LOWER(contact) LIKE ?";
    $params[] = '%' . strtolower($_REQUEST['searchContact']) . '%';
}

// Prepara e executa a query
$stmt = odbc_prepare($db, $query);
odbc_execute($stmt, $params);

$contact = $stmt;

// Query para buscar o nome da exposição
$q = "SELECT name FROM Inform WHERE id = ?";
$c_stmt = odbc_prepare($db, $q);
odbc_execute($c_stmt, [$idInform]);

if (odbc_fetch_row($c_stmt)) {
    $nameExpo = odbc_result($c_stmt, 1);
} else {
    $nameExpo = "Erro";
}


?>




