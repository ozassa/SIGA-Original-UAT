<?php
include_once('../../../navegacao.php');

?>
<div class="conteudopagina">
<?php
$id = $_REQUEST['id'];

$sql = "SELECT l.id_Log, lt.msgLog, l.data, l.hora 
        FROM Log l
        JOIN LogTipo lt ON l.tipoLog = lt.id_TipoLog 
        WHERE l.id_Log = ?";
$stmt = odbc_prepare($db, $sql);
odbc_execute($stmt, [$id]);

if (odbc_fetch_row($stmt)) {
    $idLog = odbc_result($stmt, 1);
    $msgLog = odbc_result($stmt, 2);
    $d = odbc_result($stmt, 3);
    $data = date("d/m/Y", strtotime($d));
    $hora = odbc_result($stmt, 4);
}

?>
<li class="campo2colunas">
   <label>Tipo do log:</label>
   <?php echo ($msgLog); ?>
</li>

<li class="campo3colunas">
   <label>Data / Hora:</label>
   <?php echo $data . " - " . $hora; ?>
</li>

<div style="clear:both">&nbsp;</div>   
<table summary="Submitted table designs" id="example">
    <thead>
        <tr>
            <th scope="col">CAMPO:</th>
            <th scope="col">VALOR:</th>
            <th scope="col">ALTERA&Ccedil;&Atilde;O:</th>
            <th scope="col">QUERY:</th>
        </tr>
    </thead>
    <tbody>     
<?php
$sqlDetalhes = "SELECT * FROM Log_Detalhes WHERE id_Log = ? ORDER BY campo";
$stmtDetalhes = odbc_prepare($db, $sqlDetalhes);
odbc_execute($stmtDetalhes, [$idLog]);

$d = 0;
while (odbc_fetch_row($stmtDetalhes)) {
    $Campo = odbc_result($stmtDetalhes, "campo");
    $Valor = odbc_result($stmtDetalhes, "valor");
    $Alter = odbc_result($stmtDetalhes, "alteracao");
    $id_detalhes = odbc_result($stmtDetalhes, "id_detalhes");

    $sqlQuery = "SELECT query FROM Log_Detalhes_Query WHERE id_detalhes = ?";
    $stmtQuery = odbc_prepare($db, $sqlQuery);
    odbc_execute($stmtQuery, [$id_detalhes]);

    $queryLog_detalhes_query = odbc_fetch_row($stmtQuery) ? odbc_result($stmtQuery, "query") : null;
?>
    <tr <?php echo $d % 2 ? "" : ' class="odd;"' ?>>
        <td height="20px" class="texto"><?php echo $Campo; ?></td>
        <td height="20px" class="texto"><?php echo $Valor; ?></td>
        <td height="20px" class="texto"><?php echo ($Alter); ?></td>
        <td height="20px" class="texto">
            <?php if ($queryLog_detalhes_query) { ?>
                <button class="botaoapm" type="button" name="query" onclick="verErro('<?php echo $queryLog_detalhes_query; ?>');">Veja a query</button>
            <?php } else {
                echo "Sem Query";
            } ?>
        </td>
    </tr>
<?php
    $d++;
}
?>
    </tbody>
</table>

<div class="barrabotoes">
    <button class="botaovgm" type="button" onClick="javascript: window.location = 'Access.php?comm=log';">Voltar</button>       
</div>

<div style="clear:both">&nbsp;</div>
</div>
<?php
odbc_free_result($stmt);
odbc_free_result($stmtDetalhes);
odbc_close($db);
?>
