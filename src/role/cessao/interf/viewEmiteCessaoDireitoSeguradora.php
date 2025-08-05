<?php
$idUser = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
$id_Cessao = isset($_REQUEST['id_Cessao']) ? $_REQUEST['id_Cessao'] : null;
$id_Parametro = '10040';

$sql = "EXEC SPR_BB_Consulta_Cessao_Direito ?, ?, ?, NULL";
$stmt = odbc_prepare($db, $sql);

// Proteção contra SQL Injection ao usar variáveis diretamente
$params = array('250', $idUser, $id_Cessao);
$rsSql = odbc_execute($stmt, $params);

$dados = array();
if ($rsSql) {
    while (odbc_fetch_row($stmt)) {
        $idInform = odbc_result($stmt, 'Id_Inform');
        $n_Apolice = odbc_result($stmt, "n_Apolice");
        $Segurado = odbc_result($stmt, "Segurado");
        $Banco = odbc_result($stmt, "Banco");
        $Agencia = odbc_result($stmt, "Agencia");
        $Cod_Cessao = odbc_result($stmt, "Cod_Cessao");
        $Data_Solic = Convert_Data_Geral(substr(odbc_result($stmt, "Data_Solic"), 0, 10));
        $d_Aceite_Banco = Convert_Data_Geral(substr(odbc_result($stmt, "d_Aceite_Banco"), 0, 10));
        $Cod_Comprador = odbc_result($stmt, "Cod_Comprador");
        $Comprador = odbc_result($stmt, "Comprador");
        $Pais = odbc_result($stmt, "Pais");

        $dados[] = array(
            "n_Apolice" => $n_Apolice,
            "Segurado" => $Segurado,
            "Banco" => $Banco,
            "Agencia" => $Agencia,
            "Cod_Cessao" => $Cod_Cessao,
            "Data_Solic" => $Data_Solic,
            "d_Aceite_Banco" => $d_Aceite_Banco,
            "Cod_Comprador" => $Cod_Comprador,
            "Comprador" => $Comprador,
            "Pais" => $Pais,
        );
    }
}

// Liberar a conexão ODBC para evitar problemas com conexões ocupadas
odbc_free_result($stmt);

require_once("../../../navegacao.php");
require_once("../consultaCertificacao.php");
?>

<div class="conteudopagina">
    <!-- Renderização do conteúdo -->
    <li class="campo3colunas" style="width: 220px;">
        <label>Ap&oacute;lice</label>
        <?php echo htmlspecialchars($n_Apolice); ?>
    </li>

    <li class="campo3colunas" style="width: 465px;">
        <label>Nome do Segurado</label>
        <?php echo htmlspecialchars($Segurado); ?>
    </li>

    <li class="campo3colunas" style="width: 220px;">
        <label>Banco</label>
        <?php echo htmlspecialchars($Banco); ?>
    </li>

    <li class="campo3colunas" style="width: 190px;">
        <label>Ag&ecirc;ncia</label>
        <?php echo htmlspecialchars($Agencia); ?>
    </li>

    <li class="campo3colunas" style="width: 150px;">
        <label>Cod. Cess&atilde;o</label>
        <?php echo htmlspecialchars($Cod_Cessao); ?>
    </li>

    <li class="campo3colunas" style="width: 150px;">
        <label>Data Solicita&ccedil;&atilde;o</label>
        <?php echo htmlspecialchars($Data_Solic); ?>
    </li>

    <li class="campo3colunas" style="width: 150px;">
        <label>Data Aceite Banco</label>
        <?php echo htmlspecialchars($d_Aceite_Banco); ?>
    </li>

    <br clear="all">

    <li class="barrabotoes" style="list-style:none;">
        <label>
            <h2>Compradores</h2>
        </label>

        <table summary="">
            <thead>
                <tr>
                    <th></th>
                    <th>Cod. Comprador</th>
                    <th>Nome Comprador</th>
                    <th>Pa&iacute;s</th>
                </tr>
            </thead>

            <?php if (empty($dados)) { ?>
                <tbody>
                    <tr>
                        <td valign="top" colspan="10" class="dataTables_empty">Nenhum dado retornado na tabela</td>
                    </tr>
                </tbody>
            <?php } else {
                foreach ($dados as $index => $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($index + 1); ?></td>
                        <td><?php echo htmlspecialchars($row['Cod_Comprador']); ?></td>
                        <td><?php echo htmlspecialchars($row['Comprador']); ?></td>
                        <td><?php echo htmlspecialchars($row['Pais']); ?></td>
                    </tr>
                <?php }
            } ?>
        </table>
    </li>

    <div class="barrabotoes">
        <button class="botaovgm" type="button" onClick="window.location = '<?php echo $host; ?>src/role/cessao/Cessao.php?comm=emiteCessaoDireitoSeguradora';">Voltar</button>
        <button class="botaovgm <?php echo $perm_cert ? 'js-recusar_cessao' : 'js-certificado'; ?>" id="recusar" type="button">Recusar</button>
        <button class="botaoagm <?php echo $perm_cert ? 'js-aceitar_cessao' : 'js-certificado2'; ?>" id="aceitar" type="button">Aceitar</button>
    </div>
</div>

<script>
    $(document).ready(function () {
        <?php if (isset($_GET['show_modal'])) { ?>
        <?php if ($_GET['show_modal'] == 'modal-recusar') { ?>
        $(".modal-recusar").show();
        <?php } else { ?>
        $(".modal-aceitar").show();
        <?php } ?>
        <?php } ?>

        $(".js-certificado").on("click", function () {
            $(".modal-certificado2").show();
        });

        $(".js-certificado2").on("click", function () {
            $(".modal-certificado").show();
        });

        $(".js-recusar_cessao").on("click", function () {
            $(".modal-recusar").show();
        });

        $(".js-aceitar_cessao").on("click", function () {
            $(".modal-aceitar").show();
        });

        $("#close_modal_certificado").on("click", function () {
            $(".modal-certificado").hide();
        });

        $("#close_modal_certificado2").on("click", function () {
            $(".modal-certificado2").hide();
        });

        $("#close_modal_recusar").on("click", function () {
            $(".modal-recusar").hide();
        });

        $("#close_modal_aceitar").on("click", function () {
            $(".modal-aceitar").hide();
        });
    });
</script>
