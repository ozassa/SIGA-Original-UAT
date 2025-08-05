<?php

require_once("../../../dbOpen.php");

$Operacao = $_REQUEST['Operacao'];
$ID = $_REQUEST['cobEmpresaID'];
$idInform = $_REQUEST['idInform'];
$CNPJ = $_REQUEST['CNPJ'];
$Nome = $_REQUEST['Nome'];
$sessao = $_REQUEST['sessao'];
$root = '../../';

if ($Operacao == 'Inserir') {
    $qry = "INSERT INTO ParModEsp_Maiores_Compradores (idInform, Nome, CNPJ) VALUES (?, ?, ?)";
    $stmt = odbc_prepare($db, $qry);
    $params = [$idInform, $Nome, $CNPJ];
    $resp = odbc_execute($stmt, $params);

    $msg = $resp ? 'Gravado com Sucesso' : 'Erro na gravação';
} elseif ($Operacao == 'Alterar') {
    $qry = "UPDATE ParModEsp_Maiores_Compradores SET idInform = ?, Nome = ?, CNPJ = ? WHERE id = ? AND idInform = ?";
    $stmt = odbc_prepare($db, $qry);
    $params = [$idInform, $Nome, $CNPJ, $ID, $idInform];
    $resp = odbc_execute($stmt, $params);

    $msg = $resp ? 'Alterado com Sucesso' : 'Erro na alteração';
} elseif ($Operacao == 'Remover') {
    $qry = "DELETE FROM ParModEsp_Maiores_Compradores WHERE id = ? AND idInform = ?";
    $stmt = odbc_prepare($db, $qry);
    $params = [$ID, $idInform];
    $resp = odbc_execute($stmt, $params);

    $msg = $resp ? 'Removido com Sucesso' : 'Erro na Remoção';
}

$query = "SELECT * FROM ParModEsp_Maiores_Compradores WHERE idInform = ? ORDER BY Nome";
$stmt = odbc_prepare($db, $query);
odbc_execute($stmt, [$idInform]);
?>

<label>
    <font style="color:#F00"><?php echo $msg; ?></font>
</label>

<table summary="Submitted table designs">
    <thead>
        <th scope="col">Raz&atilde;o Social</th>
        <th scope="col">CNPJ</th>
        <th scope="col" colspan="2">&nbsp;</th>
    </thead>
    <tbody>
        <?php
        while ($row = odbc_fetch_array($stmt)) {
            $razaoComprador = $row['Nome'];
            $cnpjComprador = $row['CNPJ'];
            $idComprador = $row['id'];
        ?>
            <tr>
                <td><?php echo htmlspecialchars($razaoComprador); ?></td>
                <td><?php echo htmlspecialchars($cnpjComprador); ?></td>
                <td>
                    <a href="#" onClick="edita_formCobertura('<?php echo htmlspecialchars($razaoComprador); ?>','<?php echo htmlspecialchars($cnpjComprador); ?>','<?php echo $idComprador; ?>');return false;">
                        <img src="<?php echo $root; ?>images/icone_editar.png" alt="" title="Editar Registro" width="24" height="24" class="iconetabela" />
                    </a>
                </td>
                <td>
                    <a href="#" onClick="javascript: loadHTMLIE('<?php echo htmlspecialchars($root . 'role/executive/interf/Adiciona_Maiores_Compradores.php', ENT_QUOTES, 'UTF-8'); ?>','Retorno5','POST','<?php echo htmlspecialchars($idComprador, ENT_QUOTES, 'UTF-8'); ?>','<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>','Remover','CobComprador');return false;">
    <img src="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" />
</a>

                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
