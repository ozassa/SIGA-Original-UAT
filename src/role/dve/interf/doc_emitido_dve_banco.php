<?php
///////////////////////////////////////////////////////////
// Inicio do programa
$ID_User = $userID;
$criterio = '';
$junta = '';
$idBanco = isset($_POST['UsuarioBanco']) ? $_POST['UsuarioBanco'] : null;
$NomeUser = isset($_POST['NomeUser']) ? $_POST['NomeUser'] : null;

$qry = "SELECT a.id, a.name FROM Banco a INNER JOIN DocEmitidoDVE b ON b.idBanco = a.id GROUP BY a.id, a.name";
$exec = odbc_exec($db, $qry);

$dadosBanco = array();
while (odbc_fetch_row($exec)) {
    $dadosBanco[] = [
        "id" => odbc_result($exec, "id"),
        "name" => odbc_result($exec, "name")
    ];
}

$qry = "SELECT NomeUser FROM DocEmitidoDVE GROUP BY NomeUser ORDER BY NomeUser";
$exec1 = odbc_exec($db, $qry);

$dadosBancoUs = array();
while (odbc_fetch_row($exec1)) {
    $dadosBancoUs[] = ["NomeUser" => odbc_result($exec1, "NomeUser")];
}

$cquery = "SELECT DocEmitidoDVE_ID, idUser, NomeUser, EmailUser, loginUser, 
             CONVERT(char, DataEmissaoDoc, 113) AS DataEmissaoDoc, urlDoc, idBanco
             FROM DocEmitidoDVE ";

$params = [];

if (isset($_POST['executar']) && $_POST['executar'] == 1) {
    if (!empty($_POST['UsuarioBanco'])) {
        $criterio .= $junta . " idBanco = ? ";
        $params[] = $_POST['UsuarioBanco'];
        $junta = " AND ";
    }
    if (!empty($_POST['NomeUser'])) {
        $criterio .= $junta . " NomeUser LIKE ? ";
        $params[] = '%' . $_POST['NomeUser'] . '%';
        $junta = " AND ";
    }
}

if ($criterio != "") {
    $cquery .= ' WHERE ' . $criterio;
}

$cquery .= " ORDER BY CONVERT(char, DataEmissaoDoc, 102) DESC, NomeUser";
$stmt = odbc_prepare($db, $cquery);
odbc_execute($stmt, $params);
?>

<?php require_once("../../../navegacao.php"); ?>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
<form name="consult" id="consult" action="<?php echo $root; ?>role/dve/Dve.php" method="post">
    <div class="conteudopagina">
        <li class="campo2colunas">
            <label>Banco</label>
            <select name="UsuarioBanco" id="UsuarioBanco">
                <option value="">Todos</option>
                <?php foreach ($dadosBanco as $banco) { ?>
                    <option value="<?php echo $banco["id"]; ?>" <?php echo ($banco["id"] == $idBanco ? 'selected' : ''); ?>><?php echo $banco["name"]; ?></option>
                <?php } ?>
            </select>
        </li>

        <li class="campo2colunas">
            <label>Usuário Banco</label>
            <select name="NomeUser" id="NomeUser">
                <option value="">Todos</option>
                <?php foreach ($dadosBancoUs as $usuario) { ?>
                    <option value="<?php echo $usuario["NomeUser"]; ?>" <?php echo ($usuario["NomeUser"] == $NomeUser ? 'selected' : ''); ?>><?php echo $usuario["NomeUser"]; ?></option>
                <?php } ?>
            </select>
        </li>

        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
            <button class="botaoagm" type="button" onClick="this.form.submit()">Pesquisar</button>
        </li>
    </div>
    <input type="hidden" name="comm" value="consultaDveEmitidaBanco">
    <input type="hidden" name="executar" value="1">
</form>

<div class="conteudopagina">
    <table summary="Submitted table designs" id="example">
        <thead>
            <tr>
                <th scope="col">Usuário do Banco</th>
                <th scope="col">Email</th>
                <th scope="col">Login</th>
                <th scope="col">Emissão</th>
                <th scope="col">Ver arquivo</th>
            </tr>
        </thead>
        <tbody>
            <?php while (odbc_fetch_row($stmt)) { ?>
                <tr>
                    <td><?php echo odbc_result($stmt, "NomeUser"); ?></td>
                    <td><?php echo odbc_result($stmt, "EmailUser"); ?></td>
                    <td><?php echo odbc_result($stmt, "LoginUser"); ?></td>
                    <td><?php echo ymd2dmy2(odbc_result($stmt, "DataEmissaoDoc")); ?></td>
                    <td>
                        <?php
                        $filename = odbc_result($stmt, "urlDoc");

                        if (file_exists($filename)) {
                            echo "<a href='" . odbc_result($stmt, 'urlDoc') . "'>Clique</a>";
                        } else {
                            echo '<a href="#" onclick="verErro(\'Arquivo n&atilde;o existe\');">Clique</a>';
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="divisoria01"></div>
</div>
<!-- CONTEÚDO PÁGINA - FIM -->
