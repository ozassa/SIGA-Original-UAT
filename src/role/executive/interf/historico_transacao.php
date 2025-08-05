<?php
include_once('../../../navegacao.php');

?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">
    <?php
    $junta = '';
    $compl = '';
    $params = [];
    $sql = "
        SELECT hi.id, hi.idInform, inf.name AS Fantasia, 
        hi.DataReg, hi.NomeTabela, hi.NomeCampo, hi.ValorAnterior, hi.ValorAtual,
        CASE WHEN hi.Acao = 1 THEN
            'Alteração'
        ELSE
            'Inclusão'
        END AS Acao, hi.userID, b.name AS Usuario, b.login AS Login 
        FROM historico_inform hi 
        INNER JOIN Users b ON b.id = hi.userID
        INNER JOIN Inform inf ON inf.id = hi.idInform
    ";

    if (isset($_POST['NomeFantasia'])) {
        $compl .= $junta . "LOWER(inf.name) LIKE ?";
        $params[] = '%' . strtolower($_POST['NomeFantasia']) . '%';
        $junta = ' AND ';
    }
    if (isset($_POST['NomeUsuario'])) {
        $compl .= $junta . "LOWER(b.name) LIKE ?";
        $params[] = '%' . strtolower($_POST['NomeUsuario']) . '%';
        $junta = ' AND ';
    }
    if (isset($_POST['idInform'])) {
        $compl .= $junta . "hi.idInform = ?";
        $params[] = $_POST['idInform'];
        $junta = ' AND ';
    }

    if ($compl != '') {
        $sql .= ' WHERE ' . $compl;
    }
    $sql .= " ORDER BY hi.idInform, DataReg DESC";

    $stmt = odbc_prepare($db, $sql);
    $ree = odbc_execute($stmt, $params);

    ?>
    <form action="Executive.php?comm=historico_transacao" name="buscaavancada" method="post">
        <input type="hidden" name="operacao" value="1" />
        <ul>
            <li class="campo3colunas">
                <label>Informe N&ordm;</label>
                <input type="text" name="idInform" id="idInform" value="" />
            </li>

            <li class="campo3colunas">
                <label>Nome do Segurado</label>
                <input type="text" name="NomeFantasia" id="NomeFantasia" value="" />
            </li>

            <li class="campo3colunas">
                <label>Nome do Usu&aacute;rio</label>
                <input type="text" name="NomeUsuario" id="NomeUsuario" value="" />
            </li>
        </ul>
        <br clear="all" />
        <button type="button" class="botaoagm" onclick="document.buscaavancada.submit();">Buscar</button>
    </form>
    <table id="example" width="100%">
        <thead>
            <tr>
                <th width="5%">Informe</th>
                <th width="10%">Tabela</th>
                <th width="15%">Nome do Campo</th>
                <th width="25%">Dado Anterior</th>
                <th width="25%">Dado Atual</th>
                <th width="10%">Data Log</th>
                <th width="10%">Usu&aacute;rio</th>
            </tr>
        </thead>
        <?php $op = isset($_REQUEST['operacao']) ? $_REQUEST['operacao'] : 0; ?>
        <?php if ($op == 1) { ?>
            <tbody>
                <?php while (odbc_fetch_row($stmt)) { ?>
                    <tr>
                        <td><?php echo odbc_result($stmt, 'idInform'); ?></td>
                        <td><?php echo odbc_result($stmt, 'NomeTabela'); ?></td>
                        <td><?php echo odbc_result($stmt, 'NomeCampo'); ?></td>
                        <td><?php echo odbc_result($stmt, 'ValorAnterior'); ?></td>
                        <td><?php echo odbc_result($stmt, 'ValorAtual'); ?></td>
                        <td><?php echo Convert_Data_Geral(substr(odbc_result($stmt, 'DataReg'), 0, 10)) . ' ' . substr(odbc_result($stmt, 'DataReg'), 11, 8); ?></td>
                        <td><?php echo odbc_result($stmt, 'Usuario'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        <?php } ?>
        <tfoot>
            <th colspan="7"></th>
        </tfoot>
    </table>
</div>
