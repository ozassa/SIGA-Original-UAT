<?php include_once("../../../navegacao.php"); ?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">
    <FORM id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/ListClient.php?comm=ViewExecRegion"
        method="post">
        <li class="campo2colunas">
            <label>Nome</label>
            <input name="nome" type="text">
        </li>

        <li class="campo2colunas">
            <label>Status</label>
            <select name="region">
                <option value=""></option>
                <?php
                $reg = "Select id, description from Region order by id asc";
                $xcur = odbc_exec($db, $reg);
                while (odbc_fetch_row($xcur)) {
                    echo "<option value=" . odbc_result($xcur, 'id') . ">" . (odbc_result($xcur, 'description')) . "</option>";
                }
                ?>
            </select>
        </li>
        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
            <button name="envia" type="submit" class="botaoagp">OK</button>
        </li>

    </form>
    <table summary="Submitted table designs" id="example">
        <thead>
            <tr>
                <th width="200" class="bgAzul">Executivo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($envia == 1) {

                $region = $_POST['region'];
                $nome = $_POST['nome'];

                $sql = "SELECT DISTINCT us.id, us.name " .
                    "FROM UserRole ur " .
                    "JOIN Users us ON ur.idUser = us.id " .
                    "LEFT JOIN UserRegion usr ON us.id = usr.idUser " .
                    "WHERE ur.idRole = 2 " .
                    "AND us.perfil <> 'C' " .
                    "AND ISNULL(us.state, 0) <> '1' ";

                // Adiciona condição baseada na política
                if (!$role["policy"]) {
                    $excludedIds = ['1', '3144', '3211', '3210', '1375', '3255', '3215'];
                    $sql .= " AND us.id NOT IN (" . implode(',', array_map(fn($id) => "'$id'", $excludedIds)) . ")";
                }

                // Adiciona filtro por região se necessário
                if ($region) {
                    $sql .= " AND usr.idRegion = ?";
                }

                // Adiciona filtro por nome se necessário
                if ($nome) {
                    $sql .= " AND UPPER(us.name) LIKE ?";
                }

                // Ordena por nome
                $sql .= " ORDER BY us.name ASC";

                // Prepara e executa a consulta de forma segura
                $cur = odbc_prepare($db, $sql);
                $params = [];

                // Adiciona os parâmetros na ordem correta
                if ($region) {
                    $params[] = $region;
                }
                if ($nome) {
                    $params[] = '%' . strtoupper($nome) . '%';
                }

                if (!odbc_execute($cur, $params)) {
                    //die("Erro ao executar a consulta: " . odbc_errormsg($db));
                }

                // Processa os resultados
                $c = "0";
                while (odbc_fetch_row($cur)) {


                    if ($c == "0") {
                        $color = "#FFFFFF";
                        $c = "1";
                    } else {
                        $color = "#e9e9e9";
                        $c = "0";
                    }
                    echo "<tr bgcolor=$color>";
                    echo "<td width=\"200\" class=\"texto\"><A href=ListClient.php?comm=ExecRegion&idExecutivo=" . odbc_result($cur, 1) . ">" .
                        (ucfirst(strtolower(trim(odbc_result($cur, 2))))) . "</a></td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
    <div class="divisoria01"></div>
</div>