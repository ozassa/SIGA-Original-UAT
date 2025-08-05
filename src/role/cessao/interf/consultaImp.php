<?php

require_once "../../../navegacao.php";

require_once "../../dbOpen.php";
?>
<?php
// Turn off all error reporting
//error_reporting(0);
//ini_set('error_reporting', 0);
?>

<div class="conteudopagina">

    <?php
    $codCessao = explode("/", $_REQUEST["codCessao"]);
    $cessaoExport = $codCessao[0];

    $codigo = $cessaoExport;
    $tipoBanco = "";
    $bleanDVE = true;

    $sql = "SELECT * FROM Inform WHERE id = ?";
    $cur = odbc_prepare($db, $sql);
    odbc_execute($cur, [$idInform]);

    $moeda = odbc_result($cur, "currency");
    $iSubProduto = odbc_result($cur, "i_Sub_Produto");
    if ($moeda == "1") {
        $ext = "R$";
    } elseif ($moeda == "2") {
        $ext = "US$";
    } elseif ($moeda == "6") {
        $ext = "&euro;";
    }

    odbc_free_result($cur);

    $sqlCDBB = "SELECT CB.id AS id_Cessao FROM CDBB CB WHERE CB.idInform = ? AND CB.codigo = ?";
    $rsSqlCDBB = odbc_prepare($db, $sqlCDBB);
    odbc_execute($rsSqlCDBB, [$idInform, $_REQUEST["codigo"]]);

    $id_Cessao = odbc_result($rsSqlCDBB, "id_Cessao");
    odbc_free_result($rsSqlCDBB);
    $idCDOB = $id_Cessao;
    $idCDBB = $id_Cessao;
    $idCDParc = $id_Cessao;
    $idDVE = isset($_REQUEST["idDVE"]) ? $_REQUEST["idDVE"] : 0;
    $idInform = isset($_REQUEST["idInform"]) ? $_REQUEST["idInform"] : 0;
    if (!function_exists("getStrDate")) {
        function getStrDate($str)
        {
            $row = explode("-", $str);
            $ret = $row[2] . "/" . $row[1] . "/" . $row[0];
            if ($ret == "//") {
                return "";
            }
            return $ret;
        }
    }
    if (!function_exists("getTimeStamp")) {
        function getTimeStamp($date)
        {
            if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date, $res)) {
                return mktime(0, 0, 0, $res[2], $res[3], $res[1]);
            } elseif (
                preg_match("/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $date, $res)
            ) {
                return mktime(0, 0, 0, $res[2], $res[1], $res[3]);
            }
        }
    }

    if ($role["bancoBB"]) {
        $query = "
           SELECT imp.name, c.name as countryName, credit, credit, imp.id
           FROM Importer imp
             JOIN Country c ON (c.id = imp.idCountry)
             JOIN CDBBDetails cdd ON (cdd.idImporter = imp.id)
             JOIN CDBB cdbb ON (cdbb.id = cdd.idCDBB)
           WHERE cdbb.idInform = ? AND cdbb.status IN (2, 4) AND cdbb.codigo = ?
             AND imp.state NOT IN (7, 8, 9)
           ORDER BY imp.name
        ";
        $tipoBanco = 1;
    } elseif ($role["bancoParc"]) {
        $query = "
           SELECT imp.name, c.name as countryName, credit, credit, imp.id
           FROM Importer imp
             JOIN Country c ON (c.id = imp.idCountry)
             JOIN CDParcDetails cdd ON (cdd.idImporter = imp.id)
             JOIN CDParc cdp ON (cdp.id = cdd.idCDParc)
           WHERE cdp.idInform = ? AND cdp.status IN (2, 4) AND cdp.codigo = ?
             AND imp.state NOT IN (7, 8, 9)
           ORDER BY imp.name
        ";
        $tipoBanco = 2;
    } else {
        $query = "
           SELECT imp.name, c.name as countryName, credit, credit, imp.id
           FROM Importer imp
             JOIN Country c ON (c.id = imp.idCountry)
             JOIN CDOBDetails cdd ON (cdd.idImporter = imp.id)
             JOIN CDOB cdob ON (cdob.id = cdd.idCDOB)
           WHERE cdob.idInform = ? AND cdob.status IN (2, 4) AND cdob.codigo = ?
             AND imp.state NOT IN (7, 8, 9)
           ORDER BY imp.name
        ";
        $tipoBanco = 3;
    }

    // Prepara e executa a consulta com parâmetros seguros
    
    //echo "<pre>$query</pre>";
    ?>
    <li class="campo2colunas">
        <label>Nome do Segurado:</label>
        <?php echo htmlspecialchars($_REQUEST["NomeSegurado"], ENT_QUOTES, 'UTF-8'); ?>
    </li>

    <li class="campo2colunas">
        <label>C&oacute;digo
            Cess&atilde;o</label><?php echo htmlspecialchars($_REQUEST["codCessao"], ENT_QUOTES, 'UTF-8'); ?>

    </li>

    <br clear="all">

    <li class="barrabotoes" style="list-style:none;*margin-left:-15px;padding-top: 0px;"></li>

    <h2>Lista de Compradores</h2>

    <table summary="" id="example" class="no-sort">
        <thead>
            <tr>
                <th width="5%" align="center">&nbsp;</th>
                <th width="35%">Comprador</th>
                <th width="30%">País</th>
                <th width="20%" align="center">Crédito <br>Concedido (<?php echo $ext; ?> Mil)</th>
                <th width="20%" align="center">Crédito <br>Temporário (<?php echo $ext; ?> Mil)</th>
                <!--td width="10%" align="center">Aviso de Sinistro</td--> <!--comentei o link de aviso de sinistro-->
            </tr>
        </thead>
        <tbody>

            <?php
            $cur = odbc_prepare($db, $query);
            odbc_execute($cur, [$idInform, $codigo]);

            $i = 0;
            while (odbc_fetch_row($cur)) {

                $i++;
                $idImporter = odbc_result($cur, "id"); //incluido HiCom.... // busca dados de crédito
                $wsql = "select case when limTemp < getdate() then 0 else creditTemp end creditTemp, limTemp, credit, getdate() as hoje from ChangeCredit where id=(select max(id) from ChangeCredit where idImporter= $idImporter) "; //echo  $wsql;
                $y = odbc_exec($db, $wsql);
                $hc_creditTemp_imp = odbc_result($y, 1);
                $hc_limTemp_imp = odbc_result($y, 2);
                $hc_credit_imp = odbc_result($y, 3);
                if ($hc_limTemp_imp) {
                    if ($hc_creditTemp_imp > 0) {
                        $hc_creditTemp_imp =
                            number_format($hc_creditTemp_imp / 1000, 0, ",", ".") .
                            "<br>até:&nbsp;" .
                            getStrDate(substr($hc_limTemp_imp, 0, 10));
                    } else {
                        $hc_creditTemp_imp = "0";
                    }
                } else {
                    $hc_creditTemp_imp = number_format(0, 0, ",", ".");
                }
                $qu = "SELECT id FROM Sinistro WHERE idImporter = $idImporter and (status >= 2 and status <> 7 and status <> 6)";
                $sin = odbc_exec($db, $qu);
                ?>
                <tr <?php echo $i % 2 != 0 ? " bgcolor=\"#e9e9e9\"" : ""; ?>>
                    <td width="5%"><?php echo $i; ?></td>
                    <td><?php echo odbc_result($cur, 1); ?></td>
                    <td><?php echo odbc_result($cur, 2); ?></td>
                    <td align="center"><?php echo odbc_result($cur, 4) / 1000; ?></td>
                    <td align="center"><?php echo $hc_creditTemp_imp; ?></td>
                    <?php if (odbc_fetch_row($sin)) { ?>
                        <td align="center">-</td>
                    <?php } ?>
                </tr>
                <?php
            }
            odbc_free_result($cur);
            // while
            if ($i == 0) {
                $bleanDVE = false; ?>
                <TR>
                    <TD colspan=5>Nenhum Importador Cadastrado </TD>
                </TR>
                <?php
            }
            $total = $i;
            ?>
        </tbody>
    </TABLE>


    <?php
    $link =
        $root .
        "role/cessao/nadaconsta.php?idInform=$idInform&codigo=$cessaoExport&total=$total";
    $link_doc =
        $root . "role/cessao/Cessao.php?comm=consultaDoc&idInform=" . $idInform;
    $link_sit =
        $root .
        "role/cessao/Cessao.php?comm=consultaSituacaoFinanc&idInform=" .
        $idInform;
    $link_ac =
        $root .
        "role/cessao/Cessao.php?comm=consultaSinistro&idInform=" .
        $idInform;
    $link_ficha = $root . "role/client/Client.php?comm=ficha&idInform=" . $idInform;
    $id_Parametro = "10020";
    require_once "../consultaCertificacao.php";
    $sqlLink = "SELECT P.i_Parametro, ISNULL(PE.t_Parametro, P.t_Parametro) AS Endereco_Site 
            FROM Parametro P 
              INNER JOIN Parametro_Empresa PE ON PE.i_Parametro = P.i_Parametro 
            WHERE P.i_Parametro = ? -- Endereço do Cofanet para aviso de sinistro pelo Banco
                  AND PE.i_Empresa = ?";
    $rsSqlLink = odbc_prepare($db, $sqlLink);
    odbc_execute($rsSqlLink, [500, 1]);

    $Endereco_Site = odbc_result($rsSqlLink, "Endereco_Site");
    odbc_free_result($rsSqlLink);
    ?>

    <br>
    <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
        <button class="botaoagg" type="button"
            onclick="window.open('<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>')">Declaração de
            Regularidade</button>
        <button class="botaoagg <?php echo $perm_cert ? "imprime(this.form)" : "js-certificado"; ?>" <?php echo $perm_cert ? 'onClick="imprime(this.form)"' : ""; ?> type="button">Cl&aacute;usula
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Benefici&aacute;ria</button>
        <?php if ($_SESSION["pefil"] == "B") { ?>
            <button class="botaoagg" type="button"
                onclick="window.open('<?php echo htmlspecialchars($link_doc, ENT_QUOTES, 'UTF-8'); ?>')">Documentação da
                Apólice</button>
            <button class="botaoagg" type="button"
                onclick="window.open('<?php echo htmlspecialchars($link_sit, ENT_QUOTES, 'UTF-8'); ?>')">Detalhamento da
                Situação Financeira</button>
            <button class="botaoagg" type="button"
                onclick="window.open('<?php echo htmlspecialchars($Endereco_Site, ENT_QUOTES, 'UTF-8'); ?>')">Notificação de
                Ameaça de Sinistro</button>
            <button class="botaoagg" type="button"
                onclick="window.open('<?php echo htmlspecialchars($link_ac, ENT_QUOTES, 'UTF-8'); ?>')">Acompanhamento de
                Sinistros</button>
            <button class="botaoagg" type="button"
                onclick="window.open('<?php echo htmlspecialchars($link_ficha, ENT_QUOTES, 'UTF-8'); ?>')">Ficha de
                Aprovação de Limite de Crédito</button>
        <?php } ?>
    </li>

    <br>

    <?php
    function mkdate($a, $m, $d)
    {
        return date(
            "Y-m-d",
            mktime(
                0,

                0,
                0,
                $m,
                $d,
                $a
            )
        );
    }
    if (!function_exists("getEndDate")) {
        function getEndDate($d, $n, $db, $idDVE, $c = 0)
        {
            $stmtDVE = odbc_prepare($db, "SELECT num FROM DVE WHERE id = ?");
            odbc_execute($stmtDVE, [$idDVE]);
            $num = odbc_result($stmtDVE, 1);

            if ($num != 12) {
                if (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $d, $v)) {
                    return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
                } elseif (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})/", $d, $v)) {
                    return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
                }
            } else {
                $stmtInform = odbc_prepare($db, "SELECT endValidity FROM Inform WHERE id = ?");
                odbc_execute($stmtInform, [$GLOBALS['idInform']]); // $idInform foi tratado como global
                $end = odbc_result($stmtInform, 1);
                return dmy2ymd($end);
            }
        }
    }
    // converte a data de yyyy-mm-dd para dd/mm/yyyy
    if (!function_exists("ymd2dmy")) {
        function ymd2dmy($d)
        {
            if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)) {
                return "$v[3]/$v[2]/$v[1]";
            }
            return $d;
        }
    }
    if ($bleanDVE == true) { ?>
        <script language=javascript>
            function verifica(s) {
                var v = s.options[s.selectedIndex].value;
                if (v == 0) {
                    verErro("Selecione um per&iacute;odo de DVE");
                    return false;
                }
                return true;
            }

            function condEsp(myId) {
                if (confirm("Deseja Realmente Criar uma Condi&ccedil;&atilde;o Especial Juros de Mora?")) {
                    document.forms["juros"].idInform.value = myId;
                    document.forms["juros"].submit();
                }
            }
        </script>
        <!--<label>Declaração do Volume de Exportação</label>
             <form action="<?php
             //echo $root;
            ?>role/dve/Dve.php#tabela" onSubmit="return verifica(this.numDVE)" method="post">
                 <input type="hidden" name="comm" value="view">
                 <input type="hidden" name="idInform" value="<?php
                 //echo $idInform;
                ?>">
                 <input type="hidden" name="tipobanco" value="<?php
                 //echo $tipoBanco;
                ?>">
                 <input type="hidden" name="cessao" value="<?php
                 //echo $cessaoExport;
                ?>">
                 <input type="hidden" name="agencia" value="<?php
                 //echo $_REQUEST['agencia'];
                ?>">
                 <input type="hidden" name="client" value="1">
                 <input type="hidden" name="primeira_tela" value="1">
                 <input type="hidden" name="exibe" value="none">
                 <input type="hidden" name="dveInicio" id="dveInicio" value=""> -->
        <?php
        /*$query = "Select name, state, endValidity, startValidity, codProd, idAnt, tipoDve from Inform where id = $idInform";
                                                     $cur = odbc_exec($db, $query);
                                                     if(odbc_fetch_row($cur)){
                                                       $nameCl = odbc_result($cur, 1);
                                                       $statusCl = odbc_result($cur, 2);
                                                       $endValidity = odbc_result($cur, 3);
                                                       $startValidity = odbc_result($cur, 4);
                                                       $codProd = odbc_result($cur, 5);
                                                       $idAnt = odbc_result($cur, 6);
                                                       $tipoDve = odbc_result($cur, 7);
                                                     }
                                                   
                                                     
                                                       $inicio = ymd2dmy($startValidity);
                                                     
                                                       if(preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $inicio, $v)){
                                                         $dia_inicial = $v[1];
                                                       }
                                                     
                                                       //criado por wagner 9/9/2008
                                                       //$sqlAntigo = "select max(num) from DVE where idInform = $idInform";
                                                       
                                                       if($tipoBanco==1){//tipo banco do brasil
                                                         
                                                         $sql = "select MAX(dve.num)as numDVE,CONVERT(VARCHAR,dve.inicio,103) as inicio
                                                       from Inform as inf join DVE as dve on inf.id = dve.idInform 
                                                       join DVEDetails as dveD on dve.id = dveD.idDVE
                                                       join Importer as imp on dveD.idImporter = imp.id
                                                       where inf.id=$idInform and dve.state =2
                                                       and imp.id in(select cdbd.idImporter
                                                         from Inform as inf join CDBB as cdbb on inf.id = cdbb.idInform 
                                                         join CDBBDetails as cdbd on cdbb.id = cdbd.idCDBB
                                                         join Importer as imp on cdbd.idImporter = imp.id
                                                         join Agencia as a on cdbb.idAgencia = a.id
                                                         join Banco as b on a.idBanco = b.id
                                                       where inf.id=$idInform and cdbb.codigo = $cessaoExport
                                                         and b.tipo = $tipoBanco
                                                         and a.codigo = ".$_REQUEST['agencia']."
                                                         and cdbd.idImporter in(select imp.id
                                                         from Inform as inf join DVE as dve on inf.id = dve.idInform 
                                                         join DVEDetails as dveD on dve.id = dveD.idDVE
                                                         join Importer as imp on dveD.idImporter = imp.id
                                                         where inf.id=$idInform and dve.state = 2 group by imp.id))
                                                         group by dve.inicio";
                                                         
                                                       }else if($tipoBanco==2){//tipo banco parceiros
                                                             
                                                         $sql = "select MAX(dve.num)as numDVE,CONVERT(VARCHAR,dve.inicio,103) as inicio
                                                       from Inform as inf join DVE as dve on inf.id = dve.idInform 
                                                       join DVEDetails as dveD on dve.id = dveD.idDVE
                                                       join Importer as imp on dveD.idImporter = imp.id
                                                       where inf.id=$idInform and dve.state =2
                                                       and imp.id in(select cdbd.idImporter
                                                         from Inform as inf join CDParc as cdbb on inf.id = cdbb.idInform 
                                                         join CDParcDetails as cdbd on cdbb.id = cdbd.idCDParc
                                                         join Importer as imp on cdbd.idImporter = imp.id
                                                         join Agencia as a on cdbb.idAgencia = a.id
                                                         join Banco as b on a.idBanco = b.id
                                                       where inf.id=$idInform and cdbb.codigo = $cessaoExport
                                                         and b.tipo = $tipoBanco
                                                         and a.codigo = ".$_REQUEST['agencia']."
                                                         and cdbd.idImporter in(select imp.id
                                                         from Inform as inf join DVE as dve on inf.id = dve.idInform 
                                                         join DVEDetails as dveD on dve.id = dveD.idDVE
                                                         join Importer as imp on dveD.idImporter = imp.id
                                                         where inf.id=$idInform and dve.state = 2 group by imp.id))
                                                         group by dve.inicio"; 
                                                             
                                                       }else if($tipoBanco==3){//tipo banco outros
                                                         
                                                         $sql = "select MAX(dve.num)as numDVE,CONVERT(VARCHAR,dve.inicio,103) as inicio
                                                       from Inform as inf join DVE as dve on inf.id = dve.idInform 
                                                       join DVEDetails as dveD on dve.id = dveD.idDVE
                                                       join Importer as imp on dveD.idImporter = imp.id
                                                       where inf.id=$idInform and dve.state =2
                                                       and imp.id in(select cdbd.idImporter
                                                         from Inform as inf join CDOB as cdbb on inf.id = cdbb.idInform 
                                                         join CDOBDetails as cdbd on cdbb.id = cdbd.idCDOB
                                                         join Importer as imp on cdbd.idImporter = imp.id
                                                         join Agencia as a on cdbb.idAgencia = a.id
                                                         join Banco as b on a.idBanco = b.id
                                                       where inf.id=$idInform and cdbb.codigo = $cessaoExport
                                                         and b.tipo = $tipoBanco
                                                         and a.codigo = ".$_REQUEST['agencia']."
                                                         and cdbd.idImporter in(select imp.id
                                                         from Inform as inf join DVE as dve on inf.id = dve.idInform 
                                                         join DVEDetails as dveD on dve.id = dveD.idDVE
                                                         join Importer as imp on dveD.idImporter = imp.id
                                                         where inf.id=$idInform and dve.state = 2 group by imp.id))
                                                         group by dve.inicio"; 
                                                     
                                                     
                                                       }
                                                       $num_dves = odbc_exec($db, "$sql");
                                                       $num_dvesTot = odbc_num_rows($num_dves);//total de linhas*/
        ?>
        <script>
            function getOption() {

                var x = document.getElementById("mySelect")
                var valor = x.options[x.selectedIndex].text;
                document.getElementById('dveInicio').value = valor;
            }

        </script>


        <!--<li class="campo3colunas">
          <select name="numDVE" id="mySelect" onchange="getOption()" >
             <option value="0" selected>Selecione o per&iacute;odo</option>-->
        <?php
        //while(odbc_fetch_row($num_dves)){
        //$fim = getEndDate(odbc_result($num_dves, 2), 1, 0, $db, $idDVE);
        ?>
        <!-- <option value="<?php
        //echo odbc_result($num_dves, 1)
        ?>"><?php
        //echo odbc_result($num_dves, 2)." a ". $fim;
        ?> </option> -->
        <?php
        //}
        ?>
        <!--</select> 
        </li> 
        
        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
            <button class="botaoagm" type="button"  onClick="this.form.submit();">Abrir</button>
        </li>-->
    <?php }
    $sql_sum = "
    SELECT SUM(ROUND(dt.totalEmbarcado, 2)) AS totalEmbarcado
    FROM DVE d
    JOIN DVEDetails dt ON d.id = dt.idDVE
    JOIN Importer imp ON dt.idImporter = imp.id
    WHERE d.idInform = ?
      AND ISNULL(dt.state, 1) = 1
      AND d.state > 1
        ";
    $xx = odbc_prepare($db, $sql_sum);
    odbc_execute($xx, [$idInform]);

    $sum_dve_ok = odbc_result($xx, 1);
    $sum_dve_ok = number_format($sum_dve_ok, 2, ",", ".");
    //pesquisa o tipo da moeda
    
    odbc_free_result($xx);

    $sql_mod = "
        SELECT currency
        FROM Inform
        WHERE id = ?
        ";
    $xxx = odbc_prepare($db, $sql_mod);
    odbc_execute($xxx, [$idInform]);

    $currency = odbc_result($xxx, "currency");
    //define a moeda
    if ($currency == 1) {
        $moeda = "R$";
    } elseif ($currency == 2) {
        $moeda = "US$";
    } elseif ($currency == 6) {
        $moeda = "&euro;";
    }

    odbc_free_result($xxx);

    ?>

    </form>

    <?php
    if ($tipoBanco == 3) {
        $query = "
        SELECT inf.name, cdob.agencia, bc.id, cdob.status, cdob.codigo, cdob.dateClient 
        FROM Inform inf 
        JOIN CDOB cdob ON (cdob.idInform = inf.id) 
        JOIN Banco bc ON (cdob.idBanco = bc.id) 
        WHERE inf.id = ? AND cdob.id = ?
    ";
        $cur = odbc_prepare($db, $query);
        odbc_execute($cur, [$idInform, $idCDOB]);
        $nameInf = odbc_result($cur, 1);
        $agencia = odbc_result($cur, 2);
        $idBanco = odbc_result($cur, 3);
        $status = odbc_result($cur, 4);
        $hc_codigo = odbc_result($cur, 5);
        $dateEnv = odbc_result($cur, 6);
        list($ano, $mes, $dia) = explode("-", $dateEnv);
    } elseif ($tipoBanco == 1) {
        $query = "
        SELECT inf.name, cdbb.idAgencia, ag.name, cdbb.status, cdbb.codigo, cdbb.dateClient, ag.codigo 
        FROM Inform inf 
        JOIN CDBB cdbb ON (cdbb.idInform = inf.id) 
        JOIN Agencia ag ON (cdbb.idAgencia = ag.id) 
        WHERE inf.id = ? AND cdbb.id = ?
    ";
        $cur = odbc_prepare($db, $query);
        odbc_execute($cur, [$idInform, $idCDBB]);
        $nameInf = odbc_result($cur, 1);
        $agencia = odbc_result($cur, 3);
        $idAgencia = odbc_result($cur, "idAgencia");
        $numeroagencia = odbc_result($cur, 7);
        $status = odbc_result($cur, 4);
        $hc_codigo = odbc_result($cur, 5);
        $dateEnv = odbc_result($cur, 6);
        list($ano, $mes, $dia) = explode("-", $dateEnv);
    } else {
        $query = "
        SELECT inf.name, cdparc.idAgencia, ag.name, cdparc.status, bc.id, cdparc.codigo, cdparc.dateClient, ag.codigo 
        FROM Inform inf 
        JOIN CDParc cdparc ON (cdparc.idInform = inf.id) 
        JOIN Agencia ag ON (cdparc.idAgencia = ag.id) 
        JOIN Banco bc ON (cdparc.idBanco = bc.id) 
        WHERE inf.id = ? AND cdparc.id = ?
    ";
        $cur = odbc_prepare($db, $query);
        odbc_execute($cur, [$idInform, $idCDParc]);
        $nameInf = odbc_result($cur, 1);
        $agencia = odbc_result($cur, 3);
        $idAgencia = odbc_result($cur, "idAgencia");
        $numeroagencia = odbc_result($cur, 8);
        $status = odbc_result($cur, 4);
        $idBanco = odbc_result($cur, 5);
        $hc_codigo = odbc_result($cur, 6);
        $dateEnv = odbc_result($cur, 7);
        list($ano, $mes, $dia) = explode("-", $dateEnv);
    }
    ?>


    <form action="<?php echo $root; ?>role/cessao/Cessao.php" method="post">
        <input type="hidden" name="comm">
        <div class="barrabotoes">
            <button class="botaovgm" type="button"
                onClick="this.form.comm.value='consultaCessao';this.form.submit();window.history.back();">Voltar</button>
        </div>
    </form>

    <script language="javascript">
        function imprime(f) {

            <?php if (!in_array($iSubProduto, [11, 12, 13, 14, 15, 16])) { ?>

                <?php if ($iSubProduto >= 16 && $iSubProduto <= 21) { ?>

                    var str = '../cessao/cond_esp.php?consulta=1&idInform=<?php echo urlencode($idInform); ?>&agencia=<?php echo urlencode($agencia); ?>' +
                        '&idAgencia=<?php echo urlencode($idAgencia); ?>&idBanco=<?php echo urlencode($idBanco); ?>&tipoBanco=<?php echo urlencode($tipoBanco); ?>&idCDBB=<?php echo urlencode($idCDBB); ?>&idCDParc=<?php echo urlencode($idCDParc); ?>&idCDOB=<?php echo urlencode($idCDOB); ?>&total=<?php echo urlencode($i); ?>&totalR=' +
                        '&idImporterR1=<?php echo urlencode(""); ?>&comm=gerapdf';

                <?php } else { ?>

                    var str = '../cessao/cond_espnovo.php?consulta=1&idInform=<?php echo urlencode($idInform); ?>&agencia=<?php echo urlencode($agencia); ?>' +
                        '&idAgencia=<?php echo urlencode($idAgencia); ?>&idBanco=<?php echo urlencode($idBanco); ?>&tipoBanco=<?php echo urlencode($tipoBanco); ?>&idCDBB=<?php echo urlencode($idCDBB); ?>&idCDParc=<?php echo urlencode($idCDParc); ?>&idCDOB=<?php echo urlencode($idCDOB); ?>&total=<?php echo urlencode($i); ?>&totalR=' +
                        '&idImporterR1=<?php echo urlencode(""); ?>&comm=gerapdf';

                <?php } ?>

            <?php } else { ?>

                var str = '../cessao/cond_esp.php?consulta=1&idInform=<?php echo urlencode($idInform); ?>&agencia=<?php echo urlencode($agencia); ?>' +
                    '&idAgencia=<?php echo urlencode($idAgencia); ?>&idBanco=<?php echo urlencode($idBanco); ?>&tipoBanco=<?php echo urlencode($tipoBanco); ?>&idCDBB=<?php echo urlencode($idCDBB); ?>&idCDParc=<?php echo urlencode($idCDParc); ?>&idCDOB=<?php echo urlencode($idCDOB); ?>&total=<?php echo urlencode($i); ?>&totalR=' +
                    '&idImporterR1=<?php echo urlencode(""); ?>&comm=gerapdf';

            <?php } ?>
            w = window.open(str, 'pdf_windowoficial', 'toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1');
            w.moveTo(5, 5);
            w.resizeTo(screen.availWidth - 35, screen.availHeight - 35);
        }

    </script>



</div>



<script>
    $(document).ready(function () {
        $(".js-certificado").on("click", function () {
            $(".modal-certificado").show();
        });

        $("#close_modal_certificado").on("click", function () {
            $(".modal-certificado").hide();
        });
    });
</script>

<!-- Modal Certificado -->
<div class="modal-certificado" style="display:none">
    <div class="bg-black"></div>

    <div class='modal-int'>
        <h1>Aten&ccedil;&atilde;o</h1>
        <div class="divisoriaamarelo"></div>

        <li class="campo2colunas" style="width: 690px;">
            <label>&nbsp;</label>
            <p>Para emitir a Cess&atilde;o de Direito, &eacute; obrigat&oacute;rio a Certifica&ccedil;&atilde;o Digital.
            </p>
        </li>

        <li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
            <button type="button" class="botaovgm" id="close_modal_certificado">Fechar</button>
            <button type="button" class="botaoagg" onClick="window.location = '../../../auth_cert/index.php';">Usar
                certificado</button>
        </li>

    </div>
</div>
<!-- Fim modal -->