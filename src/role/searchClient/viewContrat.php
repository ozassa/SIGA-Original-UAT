<?php
//criado por Wagner 1/9/2008
$log_query = "";

$idclient = $_GET['idclient'];

if ($_POST['alterar']) {
    $ci = $_POST['ci'];
    $idInform = $_POST['idInform'];

    if (preg_match("/^([0-9]{0,255})$/", $ci)) {
        $usql = "UPDATE Inform SET contrat = ? WHERE id = ?";
        $stmtUpdate = odbc_prepare($db, $usql);
        if (odbc_execute($stmtUpdate, [$ci, $idInform])) {
            // Guarda na query do log
            $log_query .= "UPDATE Inform SET contrat = $ci WHERE id = $idInform";

            $sql = "SELECT id, name, contrat FROM Inform WHERE id = ?";
            $stmtSelect = odbc_prepare($db, $sql);
            odbc_execute($stmtSelect, [$idInform]);

            $idinform = odbc_result($stmtSelect, 1);
            $nome = odbc_result($stmtSelect, 2);
            $numeroci = odbc_result($stmtSelect, 3);
            odbc_free_result($stmtSelect);
            $msg = "Alteração realizada com sucesso !!!";

            $valor = empty($numeroci) ? "Limpa contrat" : $numeroci;

            $sqlLog = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) 
                       VALUES ('15', ?, ?, ?, ?)";
            $stmtLog = odbc_prepare($db, $sqlLog);
            odbc_execute($stmtLog, [
                $userID,
                $idInform,
                date("Y-m-d"),
                date("H:i:s")
            ]);

            $sqlId = "SELECT @@IDENTITY AS id_Log";
            $stmtIdentity = odbc_exec($db, $sqlId);
            $cur = odbc_result($stmtIdentity, 1);

            $sqlDetails = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) 
                           VALUES (?, 'Nº Contrat', ?, 'Alteração')";
            $stmtDetails = odbc_prepare($db, $sqlDetails);
            odbc_execute($stmtDetails, [$cur, $valor]);

            $sqlIdDetails = "SELECT @@IDENTITY AS id_detalhes";
            $stmtIdentityDetails = odbc_exec($db, $sqlIdDetails);
            $curDetails = odbc_result($stmtIdentityDetails, 1);

            $sqlDetailsQuery = "INSERT INTO Log_Detalhes_Query (id_detalhes, query) 
                                VALUES (?, ?)";
            $stmtDetailsQuery = odbc_prepare($db, $sqlDetailsQuery);
            odbc_execute($stmtDetailsQuery, [$curDetails, str_replace("'", "", $log_query)]);
        } else {
            $msg = "Erro em alterar contrat";
        }
    } else {
        $sql = "SELECT id, name, contrat FROM Inform WHERE id = ?";
        $stmtSelect = odbc_prepare($db, $sql);
        odbc_execute($stmtSelect, [$idInform]);

        $idinform = odbc_result($stmtSelect, 1);
        $nome = odbc_result($stmtSelect, 2);
        $numeroci = $ci;
        odbc_free_result($stmtSelect);

        $msg = "Nº do contrat está incorreto.";
    }

} else {
    $sql = "SELECT id, name, contrat FROM Inform WHERE id = ?";
    $stmtSelect = odbc_prepare($db, $sql);
    odbc_execute($stmtSelect, [$idclient]);

    $idinform = odbc_result($stmtSelect, 1);
    $nome = odbc_result($stmtSelect, 2);
    $numeroci = odbc_result($stmtSelect, 3);
    odbc_free_result($stmtSelect);
}

?>

<form id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post">
    <div class="conteudopagina">
        <li class="campo2colunas">
            <label>Nome</label>
            <?php echo $nome; ?>
        </li>
        <li class="campo2colunas">
            <label>N&ordm; CI/Contrat</label>
            <input type="text" name="ci" value="<?php echo $numeroci; ?>">
        </li>
        <p><?php echo $msg; ?></p>
        <div class="barrabotoes">
            <button name="voltar" class="botaovgm"
                onClick="this.form.comm.value='ListContrat';this.form.submit()">Voltar</button>
            <button type="submit" class="botaoagm" />Alterar</button>
            <input type="hidden" name="alterar" id="alterar" value="1" />
            <input type="hidden" value="ViewContrat" name="comm">
            <input type="hidden" value="<?php echo $idinform; ?>" name="idInform">
        </div>
    </div>
</form>