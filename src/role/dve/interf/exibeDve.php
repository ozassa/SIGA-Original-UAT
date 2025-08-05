<?php
$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : '';
$idDVE = isset($_REQUEST['idDVE']) ? $_REQUEST['idDVE'] : '';
$STATUS = isset($_REQUEST['STATUS']) ? $_REQUEST['STATUS'] : '';
$STATUSAPOLICE = isset($_REQUEST['STATUSAPOLICE']) ? $_REQUEST['STATUSAPOLICE'] : '';
$nome = isset($_REQUEST['nome']) ? $_REQUEST['nome'] : '';
$Fat = isset($_REQUEST['Fat']) ? $_REQUEST['Fat'] : '';

$total2 = isset($_REQUEST['total2']) ? $_REQUEST['total2'] : '';
$comm = isset($_REQUEST['comm']) ? $_REQUEST['comm'] : '';
$executa = isset($_REQUEST['executa']) ? $_REQUEST['executa'] : '';

$nomeSegurado = isset($_REQUEST['nomeSegurado']) ? $_REQUEST['nomeSegurado'] : '';
$ApoliceR = isset($_REQUEST['Apolice']) ? $_REQUEST['Apolice'] : '';
$Dpp = isset($_REQUEST['Dpp']) ? $_REQUEST['Dpp'] : '';
$MesAno = isset($_REQUEST['MesAno']) ? $_REQUEST['MesAno'] : '';
$stateDVER = isset($_REQUEST['stateDVE']) ? $_REQUEST['stateDVE'] : '';

?>

<link rel="stylesheet" type="text/css" media="all"
    href="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>src/scripts/calendario/calendar-win2k-cold-1.css"
    title="win2k-cold-1" />
<script type="text/javascript"
    src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>src/scripts/calendario/calendar.js"></script>
<script type="text/javascript"
    src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>src/scripts/calendario/lang/calendar-en.js"></script>
<script type="text/javascript"
    src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>src/scripts/calendario/calendar-setup.js"></script>


<SCRIPT language="javascript">

    function checaTotalEmbarcado(obj, valor) {
        checkDecimals(obj, valor);
    }

    function mascara(o, f) {
        v_obj = o
        v_fun = f
        setTimeout("execmascara()", 1)
    }

    function execmascara() {
        v_obj.value = v_fun(v_obj.value)
    }

    function data(v) {
        v = v.replace(/\D/g, "")
        v = v.replace(/(\d{2})(\d)/, "$1/$2")
        v = v.replace(/(\d{2})(\d)/, "$1/$2")
        return v
    }

    function ShowObj(id, obj) {
        var i = 0;
        for (i == 0; i < document.getElementsByName(obj).length; i++) {
            document.getElementsByName(obj)[i].style.display = "none";
        }

        if (document.getElementsByName(obj)[id].style.display == "table") {
            document.getElementsByName(obj)[id].style.display = "none";
        }
        else {
            document.getElementsByName(obj)[id].style.display = "table";
        }
    }

    function voltar_old() {
        document.Form2.action = "../dve/dve.php?comm=exibeDve&idInform=<?php echo urlencode($idInform); ?>&idDVE=<?php echo urlencode($idDVE); ?>&STATUS=<?php echo urlencode($STATUS); ?>&STATUSAPOLICE=<?php echo urlencode($STATUSAPOLICE); ?>&nome=<?php echo urlencode($nome); ?>";
        document.Form2.submit();
    }

    function voltar() {
        document.Form2.action = "../dve/Dve.php?comm=consultadve" +
            "&nome=<?php echo urlencode($nome); ?>" +
            "&Fat=<?php echo urlencode($Fat); ?>" +
            "&STATUSAPOLICE=<?php echo urlencode($STATUSAPOLICE); ?>" +
            "&STATUS=<?php echo urlencode($STATUS); ?>" +
            "&DETALHE=1" +
            "&EXECUTAR=1";
        document.Form2.submit();
    }

    function voltar1() {
        history.back();
    }

    function proc(opc) {
        document.Form2.action = 'dve.php?comm=exibeDveDet&idInform=<?php echo urlencode($idInform); ?>&idDVE=<?php echo urlencode($idDVE); ?>&novo=1&modalidade=' + encodeURIComponent(opc);
        document.Form2.submit();
    }

</SCRIPT>

<script type="text/javascript"
    src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>src/scripts/javafunc.js"></script>


<?php

require_once("funcsDve.php");

function Convert_Data_Geral_new($data)
{
    if (strstr($data, "/")) {//verifica se tem a barra /
        $d = explode("/", $data);//tira a barra
        $invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mês etc...
        return $invert_data;
    } elseif (strstr($data, "-")) {
        $d = explode("-", $data);
        $invert_data = "$d[2]/$d[1]/$d[0]";
        return $invert_data;
    }
}

if ($executa == 3) {
    $msg = "";

    $nomeSegurado = $_POST['nomeSegurado'];
    $ApoliceR = $_POST['Apolice'];
    $Dpp = $_POST['Dpp'];
    $MesAno = $_POST['MesAno'];
    $stateDVER = $_POST['stateDVER'];
    $fim = $_POST['fim'];

    $total2 = str_replace(".", "", $_POST["total2"]);
    $total2 = str_replace(",", ".", $total2);

    $valor_Periodo = str_replace(".", "", $_POST["valor_Periodo"]);
    $valor_Periodo = str_replace(",", ".", $valor_Periodo);

    $data = 'null';
    if (trim($_POST["data_Declaracao"]) != '') {
        $dt = explode('/', $_POST["data_Declaracao"]);
        $data = $dt[2] . '-' . $dt[1] . '-' . $dt[0];
    }

    //é permitido um delay de 15 dias
    $dia_limite = date('Y-m-d', strtotime($fim . ' + 15 days'));

    $sql_state = "";
    if ($data <= $dia_limite) {
        $sql_state = "";

        $sql_v = "SELECT 1 AS is_valid FROM syscolumns WHERE id = object_id(?) AND name = ?";
        $cur_v = odbc_prepare($db, $sql_v);
        odbc_execute($cur_v, ['DVE', 'd_Cadastro']);


        $is_valid = odbc_result($cur_v, "is_valid");

        odbc_free_result($cur_v);

        if ($is_valid) {
            $data = $data == 'null' ? null : $data;
            $sql = "UPDATE DVE 
                    SET total2 = ?, 
                        Valor_Periodo = ?, 
                        Data_Declaracao = ?, 
                        d_Cadastro = ?, 
                        state = 2 
                    WHERE id = ?";
            $cur = odbc_prepare($db, $sql);
            odbc_execute($cur, [$total2, $valor_Periodo, $data, date('Y-m-d H:i:s'), $idDVE]);
        } else {
            $data = $data == 'null' ? null : $data;
            $sql = "UPDATE DVE 
                    SET total2 = ?, 
                        Valor_Periodo = ?, 
                        Data_Declaracao = ?, 
                        state = 2 
                    WHERE id = ?";
            $cur = odbc_prepare($db, $sql);
            odbc_execute($cur, [$total2, $valor_Periodo, $data, $idDVE]);
        }


        if ($cur) {
            $msg = "Total Valor Embarcado alterado com sucesso.";
        }


    } else {

        $sql_state = "";

        $sql_v = "SELECT 1 AS is_valid FROM syscolumns WHERE id = object_id(?) AND name = ?";
        $cur_v = odbc_prepare($db, $sql_v);
        odbc_execute($cur_v, ['DVE', 'd_Cadastro']);


        $is_valid = odbc_result($cur_v, "is_valid");

        odbc_free_result($cur_v);

        if ($is_valid) {
            $data = $data == 'null' ? null : $data;
            $sql = "UPDATE DVE 
                    SET total2 = ?, 
                        Valor_Periodo = ?, 
                        Data_Declaracao = ?, 
                        d_Cadastro = ?
                    WHERE id = ?";
            $cur = odbc_prepare($db, $sql);
            odbc_execute($cur, [$total2, $valor_Periodo, $data, date('Y-m-d H:i:s'), $idDVE]);
        } else {
            $data = $data == 'null' ? null : $data;
            $sql = "UPDATE DVE 
                    SET total2 = ?, 
                        Valor_Periodo = ?, 
                        Data_Declaracao = ? 
                    WHERE id = ?";
            $cur = odbc_prepare($db, $sql);
            odbc_execute($cur, [$total2, $valor_Periodo, $data, $idDVE]);
        }


        if ($cur) {
            $msg = "Total Valor Embarcado alterado com sucesso.";
        }

    }

}

$sql = "SELECT i.name, i.i_Seg, i.nProp, i.startValidity, i.endValidity, 
               isnull(i.statePa, 1) AS statePa, d.num, d.inicio, d.state AS stateDVE, 
               d.total2, i.currency, d.Valor_Periodo, d.Data_Declaracao, 
               isNull(DateAdd(D, -1, DF.inicio), i.endValidity) AS FimPeriodo, 
               i.n_Apolice
        FROM Inform i
        INNER JOIN DVE d ON d.idInform = i.id
        LEFT JOIN DVE DF ON DF.idInform = i.id AND DF.num = d.num + 1
        WHERE i.id = ? AND d.id = ?";

$cur = odbc_prepare($db, $sql);
odbc_execute($cur, [$idInform, $idDVE]);



$name = odbc_result($cur, "name");
$startValidity = odbc_result($cur, "startValidity");
$endValidity = odbc_result($cur, "endValidity");
$i_Seg = odbc_result($cur, "i_Seg");
$nProp = odbc_result($cur, "nProp");
$num = odbc_result($cur, "num");
$inicio = odbc_result($cur, "inicio");
$fim = odbc_result($cur, "fimPeriodo");
$stateDVE = odbc_result($cur, "stateDVE");
$total2 = odbc_result($cur, "total2");
$statePa = odbc_result($cur, "statePa");
$moeda = odbc_result($cur, "currency");
$valor_Periodo = odbc_result($cur, "Valor_Periodo");
$dateDE = explode('-', substr(odbc_result($cur, "Data_Declaracao") ?? '', 0, 10));
$dateDEN = odbc_result($cur, "Data_Declaracao");
$n_Apolice = odbc_result($cur, "n_Apolice");
//$Data_Declaracao  =  odbc_result($cur, "Data_Declaracao");

odbc_free_result($cur);

if ($moeda == "2") {
    $ext = "US$";
} else if ($moeda == "6") {
    $ext = "€";
}

$apolice = numApolice($idInform, $db, $dbSisSeg);

if (!$apolice) {
    $apolice = $n_Apolice;
}

?>

<?php require_once("../../../navegacao.php"); ?>

<div class="conteudopagina">
    <ul>
        <li class="campo3colunas">
            <label>Cliente</label>
            <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
        </li>
        <li class="campo3colunas">
            <label>Vigência</label>
            <?php echo htmlspecialchars(date("d/m/Y", strtotime($startValidity)), ENT_QUOTES, 'UTF-8') . " a " . htmlspecialchars(date("d/m/Y", strtotime($endValidity)), ENT_QUOTES, 'UTF-8'); ?>
        </li>
        <li class="campo3colunas">
            <label>Apólice</label>
            <?php echo htmlspecialchars($apolice, ENT_QUOTES, 'UTF-8'); ?>
        </li>
        <br clear="all" />
        <li class="campo3colunas">
            <label>DVE</label><?php echo htmlspecialchars($num, ENT_QUOTES, 'UTF-8'); ?>ª
        </li>
        <li class="campo3colunas">
            <label>Per&iacute;odo</label>
            <?php echo htmlspecialchars(date("d/m/Y", strtotime($inicio)), ENT_QUOTES, 'UTF-8') . " até " . htmlspecialchars(date("d/m/Y", strtotime($fim)), ENT_QUOTES, 'UTF-8'); ?>
        </li>
    </ul>


    <div style="clear:both">&nbsp;</div>

    <FORM name="Form1" action="../dve/dve.php" method="post" style="min-height:auto!important">
        <ul class="clear">
            <li class="campo2colunas" style="width:100%">
                <label>Total das vendas com pagamento antecipado e/ou carta de crédito confirmada
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="text" name="total2" onBlur=""
                        value="<?php echo number_format($total2 ?? 0, 2, ',', '.'); ?>" size="20"
                        style="text-align:right; width:200px;">
                </label>
            </li>
        </ul>

        <br clear="all" />

        <ul>
            <li class="campo3colunas"><label>Valor total embarcado</label>
                <input type="text" name="valor_Periodo" onBlur=""
                    value="<?php echo number_format($valor_Periodo ?? 0, 2, ',', '.'); ?>" size="20"
                    style="text-align:right">
            </li>

            <li class="campo3colunas">
                <label>Data recebimento</label>
                <input type="hidden" name="fim" value="<?php echo htmlspecialchars($fim, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="text" name="data_Declaracao" id="data_Declaracao"
                    value="<?php echo $dateDEN ? htmlspecialchars(date("d/m/Y", strtotime($dateDEN)), ENT_QUOTES, 'UTF-8') : ''; ?>"
                    size="20" maxlength="10" onKeyUp="mascara(this,data);" style="width:240px;">
                <img src="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>images/icone_calendario.png"
                    name="imgdata_Declaracao" id="imgdata_Declaracao" alt="" class="imagemcampo" />

                <script type="text/javascript">
                    Calendar.setup({
                        inputField: "data_Declaracao",      // id of the input field
                        ifFormat: "dd/mm/y",          // format of the input field
                        button: "imgdata_Declaracao",   // trigger for the calendar (button ID)
                        align: "Tl",               // alignment (defaults to "Bl")
                        singleClick: true
                    });
                </script>
            </li>


            <input type="hidden" name="comm" value="exibeDve">
            <input type="hidden" name="idInform"
                value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="idDVE" value="<?php echo htmlspecialchars($idDVE, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="STATUS" value="<?php echo htmlspecialchars($STATUS, ENT_QUOTES, 'UTF-8'); ?>">

            <input type="hidden" name="nomeSegurado"
                value="<?php echo htmlspecialchars($nomeSegurado, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="Apolice" value="<?php echo htmlspecialchars($ApoliceR, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="Dpp" value="<?php echo htmlspecialchars($Dpp, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="MesAno" value="<?php echo htmlspecialchars($MesAno, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="stateDVER"
                value="<?php echo htmlspecialchars($stateDVER, ENT_QUOTES, 'UTF-8'); ?>">

            <input type="hidden" name="executa" value="3">

            <input type="hidden" name="STATUSAPOLICE"
                value="<?php echo htmlspecialchars($STATUSAPOLICE, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="nome" value="<?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>">

            <?php
            if ($statePa == 1 || $statePa == 2) { ?>
                <li class="campo3colunas">
                    <label>&nbsp;</label>
                    <button type="button" class="botaoagm" onclick="document.Form1.submit();">Alterar</button>
                </li>
                <?php
            } ?>
        </ul>
    </FORM>

    <?php if ($msg) { ?>
        <label style="color:#F00"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></label>
    <?php } ?>

    <ul class="clear">
        <li class="campo3colunas">
            <label>Modalidade</label>
            <SELECT onChange="javascript:ShowObj(selectedIndex,'tab');">
                <option SELECTED>Cobrança à vista ou à prazo até 180 dias</option>
                <option>Via Coligada</option>
            </SELECT>
        </li>
    </ul>

    <div style="clear:both">&nbsp;</div>

    <FORM name="Form2" action="../dve/dve.php" method="post" style="width:930px;">

        <div id="tab" style="display:table; width:930px;">
            <TABLE name="tab" style="display:table; width:930px;" classe="tabela01">
                <thead>
                    <tr>
                        <Th>País</Th>
                        <Th>Importador</Th>
                        <Th align="center">Embarque</Th>
                        <Th align="center">Vencimento</Th>
                        <Th align="center">
                            Embarcado<BR>(<?php echo htmlspecialchars($ext ?? 'R$', ENT_QUOTES, 'UTF-8'); ?>)</Th>
                    </TR>
                </thead>
                <?php

                $sql = "SELECT c.name pais, im.name importador, dt.embDate, dt.vencDate, dt.totalEmbarcado, dt.proex, dt.ace, dt.id idDetail, dt.fatura 
                                FROM DVEDetails dt, Country c, Importer im 
                                WHERE dt.idImporter = im.id ";

                if ($Fat != "") {
                    $sql .= " AND dt.fatura = '" . $Fat . "'";
                }

                $sql .= " AND dt.idCountry = c.id AND dt.idDVE = ? AND modalidade = 1 AND dt.state <> 3";
                $cur = odbc_prepare($db, $sql);
                odbc_execute($cur, [$idDVE]);


                $i = 0;
                $tot = 0;
                ?>
                <tbody>
                    <?php
                    while (odbc_fetch_row($cur)) {

                        $pais = odbc_result($cur, "pais");
                        $importador = odbc_result($cur, "importador");
                        $embDate = odbc_result($cur, "embDate");
                        $vencDate = odbc_result($cur, "vencDate");
                        $totalEmbarcado = odbc_result($cur, "totalEmbarcado");
                        $proex = odbc_result($cur, "proex");
                        $ace = odbc_result($cur, "ace");
                        $idDetail = odbc_result($cur, "idDetail");
                        $Fatura = odbc_result($cur, "fatura");

                        $tot = $tot + $totalEmbarcado;

                        $i++; ?>
                        <tr <?php echo $i % 2 ? "" : ' class="odd"'; ?>>
                            <td><?php echo htmlspecialchars($pais, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a
                                    href="../dve/dve.php?comm=exibeDveDet&idInform=<?php echo urlencode($idInform); ?>&idDVE=<?php echo urlencode($idDVE); ?>&idDetail=<?php echo urlencode($idDetail); ?>&modalidade=1&STATUS=<?php echo urlencode($STATUS); ?>&STATUSAPOLICE=<?php echo urlencode($STATUSAPOLICE); ?>&nome=<?php echo urlencode($nome); ?>">
                                    <?php echo htmlspecialchars($importador, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </td>
                            <td align="center">
                                <?php echo htmlspecialchars(date("d/m/Y", strtotime($embDate)), ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td align="center">
                                &nbsp;<?php echo htmlspecialchars(date("d/m/Y", strtotime($vencDate)), ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td align="right">
                                &nbsp;<?php echo htmlspecialchars(number_format($totalEmbarcado, 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                        </tr>

                        <?php
                    }

                    odbc_free_result($cur);

                    if ($i == 0) { ?>
                        <tr>
                            <td colspan="5" align="center">Nenhum embarque encontrado</td>
                        </tr>
                        <?php
                    } ?>
                </tbody>
                <tfoot>
                    <TR>
                        <Th></Th>
                        <Th></Th>
                        <Th align="center"></Th>
                        <Th align="right">Total:</Th>
                        <Th align="right">&nbsp;<?php echo number_format($tot, 2, ',', '.'); ?></Th>
                    </TR>
                </tfoot>
            </TABLE>

            <br clear="all" />

            <?php if ($statePa == 1 || $statePa == 2) { ?>
                <button type="button" class="botaoagm" onClick="javascript:proc(1);">Novo</button>&nbsp;&nbsp;
            <?php } ?>

            <button type="button" class="botaoagm"
                onClick="window.location = '../dve/Dve.php?comm=consultadve&EXECUTAR=1&nomeSegurado=<?php echo urlencode($nomeSegurado); ?>&Apolice=<?php echo urlencode($ApoliceR); ?>&Dpp=<?php echo urlencode($Dpp); ?>&MesAno=<?php echo urlencode($MesAno); ?>&stateDVE=<?php echo urlencode($stateDVER); ?>';">
                Voltar
            </button>

        </div>

        <div id="tab2" style="display:none; width:930px;">
            <TABLE name="tab" style="display:none; width:930px;" classe="tabela01">
                <thead>
                    <TR>
                        <Th>País</Th>
                        <Th>Importador</Th>
                        <Th align="center">Embarque</Th>
                        <Th align="center">Vencimento</Th>
                        <Th align="center">Embarcado<BR>(US$)</Th>
                    </TR>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT c.name AS pais, im.name AS importador, dt.embDate, dt.vencDate, dt.totalEmbarcado, 
                    dt.proex, dt.ace, dt.id AS idDetail
                        FROM DVEDetails dt
                        INNER JOIN Country c ON dt.idCountry = c.id
                        INNER JOIN Importer im ON dt.idImporter = im.id
                        WHERE dt.idDVE = ? AND modalidade = 2 AND dt.state = 1";

                    $cur = odbc_prepare($db, $sql);
                    odbc_execute($cur, [$idDVE]);


                    $i = 0;
                    $tot = 0;

                    while (odbc_fetch_row($cur)) {

                        $pais = odbc_result($cur, "pais");
                        $importador = odbc_result($cur, "importador");
                        $embDate = odbc_result($cur, "embDate");
                        $vencDate = odbc_result($cur, "vencDate");
                        $totalEmbarcado = odbc_result($cur, "totalEmbarcado");
                        $proex = odbc_result($cur, "proex");
                        $ace = odbc_result($cur, "ace");
                        $idDetail = odbc_result($cur, "idDetail");

                        odbc_free_result($cur);

                        $tot = $tot + $totalEmbarcado;

                        $i++;
                        ?>
                        <TR <?php echo $i % 2 ? "" : ' class="odd"'; ?>>
                            <TD><?php echo htmlspecialchars($pais, ENT_QUOTES, 'UTF-8'); ?></TD>
                            <TD>
                                <a
                                    href="../dve/dve.php?comm=exibeDveDet&idInform=<?php echo urlencode($idInform); ?>&idDVE=<?php echo urlencode($idDVE); ?>&idDetail=<?php echo urlencode($idDetail); ?>&modalidade=2&STATUS=<?php echo urlencode($STATUS); ?>">
                                    <?php echo htmlspecialchars($importador, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </TD>
                            <TD align="center">
                                <?php echo htmlspecialchars(date("d/m/Y", strtotime($embDate)), ENT_QUOTES, 'UTF-8'); ?>
                            </TD>
                            <TD align="center">
                                &nbsp;<?php echo htmlspecialchars(date("d/m/Y", strtotime($vencDate)), ENT_QUOTES, 'UTF-8'); ?>
                            </TD>
                            <TD align="right">
                                &nbsp;<?php echo htmlspecialchars(number_format($totalEmbarcado, 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>
                            </TD>
                        </TR>

                        <?php
                    }

                    if ($i == 0) { ?>
                        <TR>
                            <TD colspan="5" align="center">Nenhum embarque encontrado</TD>
                        </TR>
                    <?php } ?>
                </tbody>

                <tfoot>
                    <TR>
                        <Th></Th>
                        <Th></Th>
                        <Th align="center"></Th>
                        <Th align="right">Total:</Th>
                        <Th align="right">&nbsp;<?php echo number_format($tot, 2, ',', '.'); ?></Th>
                    </TR>
                </tfoot>
            </TABLE>

            <br clear="all" />
            <button type="button" class="botaoagm" onClick="javascript:proc(2);">Novo</button>&nbsp;&nbsp;
            <button type="button" class="botaoagm"
                onClick="window.location = '../dve/Dve.php?comm=consultadve';">Voltar</button>

        </div>
    </FORM>

    <div style="clear:both">&nbsp;</div>

</div>