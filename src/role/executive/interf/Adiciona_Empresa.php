<?php
header("Content-Type: text/html; charset=ISO-8859-1");

require_once("../../../dbOpen.php");

$Operacao = $_REQUEST['Operacao'];
$idEmpresa = $_REQUEST['empresaID'];
$idInform = $_REQUEST['idInform'];
$RazaoSocial = $_REQUEST['RazaoSocial'];
$CNPJ = $_REQUEST['CNPJ'];
$InscricaoEstadual = $_REQUEST['InscricaoEstadual'];
$Endereco = $_REQUEST['EnderecoEmpresa'];
$ds_complemento = 0;
$Numero = $_REQUEST['NumeroEmpresa'];
$CEP = $_REQUEST['CEP'];
$Cidade = $_REQUEST['Cidade'];
$UF = $_REQUEST['UF'];
$root = '../../';
$sessao = $_REQUEST['sessao'];

if ($Operacao == 'Inserir') {
    $qry = "INSERT INTO ParModEsp_Empresa 
            (idInform, no_razao_social, nu_cnpj, nu_inscricao_estadual, ds_endereco, ds_complemento, nu_endereco, nu_cep, no_cidade, no_estado, dt_registro) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = odbc_prepare($db, $qry);
    $params = [$idInform, $RazaoSocial, $CNPJ, $InscricaoEstadual, $Endereco, $ds_complemento, $Numero, $CEP, $Cidade, $UF, date('Y-m-d')];
    $resp = odbc_execute($stmt, $params);

    $msg = $resp ? 'Gravado com Sucesso' : 'Erro na gravação';
} elseif ($Operacao == 'Alterar') {
    $qry = "UPDATE ParModEsp_Empresa SET
            idInform = ?, no_razao_social = ?, nu_cnpj = ?, nu_inscricao_estadual = ?, ds_endereco = ?, nu_endereco = ?, ds_complemento = ?, nu_cep = ?, no_cidade = ?, no_estado = ? 
            WHERE id = ? AND idInform = ?";
    $stmt = odbc_prepare($db, $qry);
    $params = [$idInform, $RazaoSocial, $CNPJ, $InscricaoEstadual, $Endereco, $Numero, $ds_complemento, $CEP, $Cidade, $UF, $idEmpresa, $idInform];
    $resp = odbc_execute($stmt, $params);

    $msg = $resp ? 'Alterado com Sucesso' : 'Erro na alteração';
} elseif ($Operacao == 'Remover') {
    $qry = "DELETE FROM ParModEsp_Empresa WHERE id = ? AND idInform = ?";
    $stmt = odbc_prepare($db, $qry);
    $params = [$idEmpresa, $idInform];
    $resp = odbc_execute($stmt, $params);

    $msg = $resp ? 'Removido com Sucesso' : 'Erro na Remoção';
}

?>
<label>
    <font style="color:#F00"><?php echo $msg; ?></font>
</label>
<table summary="Submitted table designs">
    <thead>
        <th scope="col">Raz&atilde;o Social</th>
        <th scope="col">CNPJ</th>
        <th scope="col">Inscr. Estadual</th>
        <th scope="col">Endere&ccedil;o</th>
        <th scope="col">N&deg;</th>
        <th scope="col">CEP</th>
        <th scope="col">Cidade</th>
        <th scope="col">UF</th>
        <th scope="col" colspan="2">&nbsp;</th>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = ? ORDER BY no_razao_social";
        $stmt = odbc_prepare($db, $query);
        odbc_execute($stmt, [$idInform]);

        while (odbc_fetch_row($stmt)) {
            $no_razao_social = odbc_result($stmt, 'no_razao_social');
            $nu_cnpj = odbc_result($stmt, 'nu_cnpj');
            $nu_inscricao_estadual = odbc_result($stmt, 'nu_inscricao_estadual');
            $ds_endereco = odbc_result($stmt, 'ds_endereco');
            $num_endereco = odbc_result($stmt, 'nu_endereco');
            $ds_complemento = odbc_result($stmt, 'ds_complemento');
            $nu_cep = odbc_result($stmt, 'nu_cep');
            $no_cidade = odbc_result($stmt, 'no_cidade');
            $no_estado = odbc_result($stmt, 'no_estado');
            $idEmpresa = odbc_result($stmt, 'id');
        ?>
        <tr>
            <td><?php echo ($no_razao_social); ?></td>
            <td><?php echo $nu_cnpj; ?></td>
            <td><?php echo $nu_inscricao_estadual; ?></td>
            <td><?php echo ($ds_endereco); ?></td>
            <td><?php echo $num_endereco; ?></td>
            <td><?php echo $nu_cep; ?></td>
            <td><?php echo ($no_cidade); ?></td>
            <td><?php echo $no_estado; ?></td>
            <td>
    <a href="#" onClick="edita_formEmpresa(
        '<?php echo htmlspecialchars($idEmpresa, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($no_razao_social, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($nu_cnpj, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($nu_inscricao_estadual, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($ds_endereco, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($num_endereco, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($nu_cep, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($no_cidade, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($no_estado, ENT_QUOTES, 'UTF-8'); ?>'
    ); return false;">
        <img src="../../images/icone_editar.png" alt="" title="Editar Registro" width="24" height="24" class="iconetabela" />
    </a>
</td>
<td>
    <a href="#" onClick="javascript: loadHTML(
        '<?php echo htmlspecialchars($root . 'role/executive/interf/Adiciona_Empresa.php', ENT_QUOTES, 'UTF-8'); ?>',
        'Retorno1',
        '<?php echo htmlspecialchars($sessao, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($idEmpresa, ENT_QUOTES, 'UTF-8'); ?>',
        '<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>',
        'Remover',
        'Empresa'
    ); return false;">
        <img src="../../images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" />
    </a>
</td>

        </tr>
        <?php } ?>
    </tbody>
</table>
