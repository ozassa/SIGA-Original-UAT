<?php

if ($_REQUEST['idBuy']) {
    $idBuyer = $_REQUEST['idBuy'];
} else {
    $idBuyer = $_REQUEST['idBuyer'];
}

$idInform = $_REQUEST['idInform'];
$action = $_REQUEST['action'];
$hc_acao = $_REQUEST['hc_acao'];
$novo_endereco = $_REQUEST['novo_endereco'];
$nova_cidade = $_REQUEST['nova_cidade'];
$novo_telefone = $_REQUEST['novo_telefone'];
$novo_cep = $_REQUEST['novo_cep'];

if ($hc_acao == "INC") {
    // Obter os dados do inform com proteção
    $hc_aux_query = "SELECT id, name FROM Inform WHERE id = ?";
    $hc_aux_stmt = odbc_prepare($db, $hc_aux_query);
    odbc_execute($hc_aux_stmt, [$idInform]);

    if (odbc_fetch_row($hc_aux_stmt)) {
        $hc_name = odbc_result($hc_aux_stmt, 2);
    } else {
        $hc_name = "ID: " . $idInform;
    }

    // Inserir endereço com proteção
    $insert_query = "INSERT INTO ImporterAddress (idImporter, address, city, tel, cep, pendente, idUser) VALUES (?, ?, ?, ?, ?, 'S', ?)";
    $insert_stmt = odbc_prepare($db, $insert_query);
    $r = odbc_execute($insert_stmt, [$idBuyer, $novo_endereco, $nova_cidade, $novo_telefone, $novo_cep, $userID]);

    if (!$r) {
        $msg = "Problemas ao inserir endereço";
    } else {
        $msg = "Endereço incluído com sucesso!";
        // Chamar função de notificação
        $hc_r = $notif->novoEndereco($userID, $hc_name, $idInform, $db, 12);
        if (!$hc_r) {
            $msg .= "<BR>A notificação NÃO foi encaminhada ao crédito!";
        } else {
            $msg .= "<BR>A notificação para aprovação já foi enviada para a área de crédito.";
        }
    }
}

// Consulta protegida para obter dados do importador
$qry = "SELECT b.name as Country, a.* FROM Importer a
        LEFT JOIN Country b ON b.id = a.idCountry WHERE a.id = ?";
$cur_stmt = odbc_prepare($db, $qry);
odbc_execute($cur_stmt, [$idBuyer]);

?>

<?php require_once("../../../navegacao.php"); ?>

<div class="conteudopagina">

<label><h2>Endere&ccedil;os</h2></label>

<table summary="Submitted table designs" class="tabela01">
    <thead>
       <th colspan="3" align="center" scope="col">Endere&ccedil;o Principal</th>
    </thead>
    <tr>
       <td colspan="1" width="200"><b>Raz&atilde;o Social:</b></td>
       <td colspan="2"><?php echo odbc_result($cur_stmt, 'name'); ?></td>
    </tr>
    <tr>
       <td colspan="1" width="200"><b>Endere&ccedil;o:</b></td>
       <td colspan="2"><?php echo odbc_result($cur_stmt, 'address'); ?></td>
    </tr>
    <tr>
       <td colspan="1" width="200"><b>Cidade:</b></td>
       <td colspan="2"><?php echo odbc_result($cur_stmt, 'city'); ?></td>
    </tr>
    <tr>
       <td colspan="1" width="200"><b>CEP:</b></td>
       <td colspan="2"><?php echo odbc_result($cur_stmt, 'cep'); ?></td>
    </tr>
    <tr>
        <td colspan="1" width="200"><b>Pa&iacute;s:</b></td>
        <td colspan="2"><?php echo odbc_result($cur_stmt, 'Country'); ?></td>
    </tr>
    <tr>
        <td colspan="1" width="200"><b>Telefone:</b></td>
        <td colspan="2"><?php echo odbc_result($cur_stmt, 'tel'); ?></td>
    </tr>
    <tr>
       <td colspan="1" width="200"><b>FAX:</b></td>
       <td colspan="2"><?php echo odbc_result($cur_stmt, 'fax'); ?></td>
    </tr>
</table>

<div style="clear:both">&nbsp;</div>
<?php 

$additional_query = "SELECT id, address, city, tel, cep, pendente FROM ImporterAddress WHERE idImporter = ? AND state = 1";
$additional_stmt = odbc_prepare($db, $additional_query);
odbc_execute($additional_stmt, [$idBuyer]);

$i = 0;
?>
<table summary="Submitted table designs" class="tabela01">
    <thead>
       <th colspan="3" align="center" scope="col">Endere&ccedil;os Adicionais</th>
    </thead>
<?php

while (odbc_fetch_row($additional_stmt)) {
    $i++;
    $cor = ($i % 2 == 0) ? 'class="odd"' : '';
    $aprovacao = odbc_result($additional_stmt, 6);

    if ($aprovacao == "S") { ?>
        <tr <?php echo $cor; ?>>
            <td colspan="2"><font color="red">Pendente de aprova&ccedil;&atilde;o</font></td>
        </tr>
<?php } ?>

        <tr <?php echo $cor; ?>>
            <td width="30%"><b>Endere&ccedil;o:</b></td><td class="texto" width="70%"><?php echo odbc_result($additional_stmt, 2); ?></td>
        </tr>
        <tr <?php echo $cor; ?>>
            <td width="30%"><b>Cidade:</b></td><td class="texto"><?php echo odbc_result($additional_stmt, 3); ?></td>
        </tr>
        <tr <?php echo $cor; ?>>
            <td width="30%"><b>CEP:</b></td><td class="texto"><?php echo odbc_result($additional_stmt, 5); ?></td>
        </tr>
        <tr <?php echo $cor; ?>>
            <td width="30%"><b>Telefone:</b></td><td class="texto"><?php echo odbc_result($additional_stmt, 4); ?></td>
        </tr>
<?php } ?>

</table>
<?php
if ($i == 0) {
    echo '<label><font style="color:red;">N&atilde;o h&aacute; endere&ccedil;os adicionais relacionados a este importador.</font></label>';
}
?>
