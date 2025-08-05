<?php  
require_once("../rolePrefix.php");

/*
  Motor de busca    
*/


// Prepara a query com um parâmetro placeholder
$query  = "SELECT DISTINCT Inform.name FROM Inform WHERE Inform.id = ?";

// Cria o statement
$stmt = odbc_prepare($db, $query);

// Define o valor do parâmetro de forma segura
$params = [$idInform];

// Executa o statement com os parâmetros
$result = odbc_execute($stmt, $params);

// Verifica se há resultados e captura o valor
if (odbc_fetch_row($stmt)) {
    $name = odbc_result($stmt, 1);
    $title = $name ?: 'Sem Nome';
} else {
    $title = 'Sem Nome';
}


?>
