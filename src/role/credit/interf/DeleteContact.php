<?php
if ($_REQUEST['local'] == "Contact") {
    // Preparação da query para evitar SQL Injection
    $query = "DELETE FROM Contact WHERE id = ?";
    
    // Preparação do statement
    $stmt = odbc_prepare($db, $query);
    
    // Bind do parâmetro
    $idContact = $_REQUEST['idContact'];
    $params = [$idContact];
    
    // Execução do statement
    $cur = odbc_execute($stmt, $params);
    
    if ($cur) {
        $mensagem = "Contato excluído com sucesso!";
    } else {
        $mensagem = "<font color='red'>Atenção: Erro na exclusão do contato</font>";
    }
    
    // Liberar a conexão ODBC
    odbc_free_result($stmt);
} else {
    $mensagem = "<font color='red'>Atenção: Contato principal do informe.<br>Não pode ser excluído.</font>";
}
?>
