<?php
// CERTIFICAÇÃO DIGITAL
if (isset($_REQUEST["i_Parametro"])) {

    // Exclusão de todos os parâmetros relativos à Cessão de Direito do informe
    $sqlDelParam = "DELETE FROM Inform_Parametro WHERE i_Parametro BETWEEN 10000 AND 11000 AND i_Inform = ?";
    $stmtDelParam = odbc_prepare($db, $sqlDelParam);
    odbc_execute($stmtDelParam, [$idInform]);

    $i_Parametro = $_REQUEST["i_Parametro"];
    $Num_Parametro = $_REQUEST["Num_Parametro"];
    $qtde_param = count($i_Parametro);

    for ($i = 0; $i < $qtde_param; $i++) {
        $sqlIntoParam = "INSERT INTO Inform_Parametro (
                            i_Inform, 
                            i_Parametro, 
                            n_Parametro
                        ) VALUES (
                            ?, 
                            ?, 
                            ?
                        )";
        $stmtIntoParam = odbc_prepare($db, $sqlIntoParam);
        odbc_execute($stmtIntoParam, [
            $idInform,
            $i_Parametro[$i],
            $Num_Parametro[$i]
        ]);
    }
}
?>
