<?php
header("Content-Type: text/html; charset=ISO-8859-1");
require_once("../../../dbOpen.php");

$Operacao = $_REQUEST['Operacao'];
$idEmpresa = $_REQUEST['empresaColID'];
$idInform = $_REQUEST['idInform'];
$RazaoSocial = $_REQUEST['RazaoSocialCol'];
$Endereco = $_REQUEST['EnderecoCol'];
$Pais = $_REQUEST['PaisCol'];
$ZipCodeCol = $_REQUEST['ZipCodeCol'];
$TaxIdCol = $_REQUEST['TaxIdCol'];
$root = '../../';
$sessao = $_REQUEST['sessao'];

if ($Operacao == 'Inserir') {
    $queryInsert = "INSERT INTO ParModEsp_Coligada (idInform, razaoSocial, endereco, pais, zipCode, taxID)
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = odbc_prepare($db, $queryInsert);
    $resp = odbc_execute($stmtInsert, [$idInform, $RazaoSocial, $Endereco, $Pais, $ZipCodeCol, $TaxIdCol]);

    $msg = $resp ? 'Gravado com Sucesso' : 'Erro na gravação';
} elseif ($Operacao == 'Alterar') {
    $queryUpdate = "UPDATE ParModEsp_Coligada
                    SET idInform = ?, razaoSocial = ?, endereco = ?, pais = ?, zipCode = ?, taxID = ?
                    WHERE id = ? AND idInform = ?";
    $stmtUpdate = odbc_prepare($db, $queryUpdate);
    $resp = odbc_execute($stmtUpdate, [$idInform, $RazaoSocial, $Endereco, $Pais, $ZipCodeCol, $TaxIdCol, $idEmpresa, $idInform]);

    $msg = $resp ? 'Alterado com Sucesso' : 'Erro na alteração';
} elseif ($Operacao == 'Remover') {
    $queryDelete = "DELETE FROM ParModEsp_Coligada WHERE id = ? AND idInform = ?";
    $stmtDelete = odbc_prepare($db, $queryDelete);
    $resp = odbc_execute($stmtDelete, [$idEmpresa, $idInform]);

    $msg = $resp ? 'Removido com Sucesso' : 'Erro na Remoção';
}

$querySelect = "SELECT a.id, a.idInform, a.razaoSocial, a.endereco, a.pais, b.name, 
                       a.zipCode, a.taxID
                FROM ParModEsp_Coligada a
                INNER JOIN Country b ON a.pais = b.id
                WHERE a.idInform = ?
                ORDER BY a.razaoSocial";
$stmtSelect = odbc_prepare($db, $querySelect);
odbc_execute($stmtSelect, [$idInform]);
?>

<label>
    <font style="color:#F00"><?php echo $msg; ?></font>
</label>

<table summary="Submitted table designs">
    <thead>
        <th scope="col">Raz&atilde;o Social</th>
        <th scope="col">Endere&ccedil;o Completo</th>
        <th scope="col">Pa&iacute;s</th>
        <th scope="col">ZIP Code</th>
        <th scope="col">Tax ID</th>
        <th scope="col" colspan="2">&nbsp;</th>
    </thead>
    <tbody>
        <?php
        while (odbc_fetch_row($stmtSelect)) {
            $razao_social_col = odbc_result($stmtSelect, 'razaoSocial');
            $idInform_Col = odbc_result($stmtSelect, 'idInform');
            $endereco_col = odbc_result($stmtSelect, 'endereco');
            $pais_col = odbc_result($stmtSelect, 'pais');
            $zipcode_col = odbc_result($stmtSelect, 'zipCode');
            $taxID_col = odbc_result($stmtSelect, 'taxID');
            $nomePaisCol = odbc_result($stmtSelect, 'name');
            $idEmpresa = odbc_result($stmtSelect, 'id');
        ?>
        <tr>
            <td><?php echo $razao_social_col; ?></td>
            <td><?php echo $endereco_col; ?></td>
            <td><?php echo $nomePaisCol; ?></td>
            <td><?php echo $zipcode_col; ?></td>
            <td><?php echo $taxID_col; ?></td>
            <td>
    <a href="#" onClick="edita_formColigada(
        '<?php echo htmlspecialchars($idEmpresa, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($razao_social_col, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($endereco_col, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($pais_col, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($zipcode_col, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($taxID_col, ENT_QUOTES, 'UTF-8'); ?>'
    ); return false;">
        <img src="../../images/icone_editar.png" alt="" title="Editar Registro" width="24" height="24" class="iconetabela" />
    </a>
</td>
<td>
    <a href="#" onClick="javascript: loadHTMLIE(
        '<?php echo htmlspecialchars($root . 'role/executive/interf/Adiciona_Coligadas.php', ENT_QUOTES, 'UTF-8'); ?>',
        'Retorno2',
        '<?php echo htmlspecialchars($sessao, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($idEmpresa, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>',
        'Remover',
        'EmpresaColigada'
    ); return false;">
        <img src="../../images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" />
    </a>
</td>

        </tr>
        <?php } ?>
    </tbody>
</table>
