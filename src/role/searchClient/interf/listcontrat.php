<?php
/*
Criado por Tiago V N - (Elumini) 17/10/2005
*/
$nome = isset($_POST['nome']) ? $_POST['nome'] : '';
$stinforme = isset($_POST['stinforme']) ? $_POST['stinforme'] : '';
$envia = isset($_POST['envia']) ? $_POST['envia'] : '';

include_once("../../../navegacao.php");
?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->

<form id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post">
    <div class="conteudopagina">
        <li class="campo2colunas">
            <label>Nome</label>
            <input name="nome" type="text" value="<?php echo htmlspecialchars($nome); ?>">
        </li>
        <li class="campo2colunas">
            <label>Status</label>
            <select name="stinforme">
                <option value="0"></option>
                <option value="1" <?php if ($stinforme == "1") echo "selected"; ?>>Novo</option>
                <option value="2" <?php if ($stinforme == "2") echo "selected"; ?>>Preenchido</option>
                <option value="3" <?php if ($stinforme == "3") echo "selected"; ?>>Validado</option>
                <option value="4" <?php if ($stinforme == "4") echo "selected"; ?>>Analisado</option>
                <option value="5" <?php if ($stinforme == "5") echo "selected"; ?>>Tarifado</option>
                <option value="6" <?php if ($stinforme == "6") echo "selected"; ?>>Proposta</option>
                <option value="7" <?php if ($stinforme == "7") echo "selected"; ?>>Confirmado</option>
                <option value="8" <?php if ($stinforme == "8") echo "selected"; ?>>Alterado</option>
                <option value="9" <?php if ($stinforme == "9") echo "selected"; ?>>Cancelado</option>
            </select>
            <input type="hidden" name="comm" value="ListContrat">
        </li>
        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
            <button name="envia" value="1" type="submit" class="botaoagp">OK</button>
        </li>
    </div>
</form>

<div class="conteudopagina">
    <table summary="Submitted table designs" id="example">
        <thead>
            <tr>
                <th>Nome Empresa</th>
                <th>N&ordm; de CI</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($envia == "1") {
                $query = "SELECT id, name, contrat, state FROM Inform WHERE state NOT IN (10, 11)";
                $params = [];

                if (!empty($nome)) {
                    $query .= " AND name LIKE ?";
                    $params[] = strtoupper(trim($nome)) . '%';
                }

                if (!empty($stinforme) && $stinforme != "0") {
                    $query .= " AND state = ?";
                    $params[] = $stinforme;
                }

                $stmt = odbc_prepare($db, $query);
                $result = odbc_execute($stmt, $params);

                while ($result && odbc_fetch_row($stmt)) {
                    $statusMapping = [
                        1 => "Novo",
                        2 => "Preenchido",
                        3 => "Validado",
                        4 => "Analisado",
                        5 => "Tarifado",
                        6 => "Proposta",
                        7 => "Confirmado",
                        8 => "Alterado",
                        9 => "Cancelado",
                    ];

                    $status = $statusMapping[odbc_result($stmt, "state")] ?? "Desconhecido";

                    echo "<tr>";
                    echo "<td><a href='ListClient.php?comm=ViewContrat&idclient=" . odbc_result($stmt, "id") . "'>" .
                        htmlspecialchars(ucfirst(strtolower(trim(odbc_result($stmt, "name"))))) . "</a></td>";
                    echo "<td>" . htmlspecialchars(odbc_result($stmt, "contrat")) . "</td>";
                    echo "<td>" . htmlspecialchars($status) . "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
    <div class="divisoria01"></div>
</div>
