<?php
if ($_REQUEST['local'] == "Contact") {
    // Prepara��o da query para evitar SQL Injection
    $query = "DELETE FROM Contact WHERE id = ?";
    
    // Prepara��o do statement
    $stmt = odbc_prepare($db, $query);
    
    // Bind do par�metro
    $idContact = $_REQUEST['idContact'];
    $params = [$idContact];
    
    // Execu��o do statement
    $cur = odbc_execute($stmt, $params);
    
    if ($cur) {
        $mensagem = "Contato exclu�do com sucesso!";
    } else {
        $mensagem = "<font color='red'>Aten��o: Erro na exclus�o do contato</font>";
    }
    
    // Liberar a conex�o ODBC
    odbc_free_result($stmt);
} else {
    $mensagem = "<font color='red'>Aten��o: Contato principal do informe.<br>N�o pode ser exclu�do.</font>";
}
?>
