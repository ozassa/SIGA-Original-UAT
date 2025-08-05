<?php  /*
//####### ini ####### modificada por eliel vieira - elumini - 25/01/2008
//
// pagina de visualizacao do relatorio de apolice no formato HTML ou XLS,
// definido pelo parametro de autorizacao de abertura da mesma passada pela
// pagina de redirecionamento, e com a definicao de exibicao em html ou excel.
// Abertura default em HTML. Para abri-la em XLS, basta clicar no icone
// de referencia do EXCEL.
//
*/


//define a variavel de verificacao autorizada pela origem,
//para abertura do relatorio
$autorizacao = $_REQUEST['params'];

//define a visualizacao no formato XLS
$excel = $_REQUEST['excel'];



if ($excel=="yes") {
  //define o tipo do documento
  header('Content-type: application/msexcel');
  header('Content-Disposition: attachment; filename="view_rel_apolice.xls"');
}


//define a classe para abertura do banco de dados
require_once("../../dbOpen.php");

//define os valores dos campos para filtro da pesquisa
$edt_name           = $_REQUEST['edt_name'];
$edt_startValidity = $_REQUEST['edt_startValidity'];
$edt_endValidity   = $_REQUEST['edt_endValidity'];
$edt_prMin         = $_REQUEST['edt_prMin'];
$edt_proex         = $_REQUEST['edt_proex'];
$edt_caex          = $_REQUEST['edt_caex'];
$edt_tx_premio     = $_REQUEST['edt_tx_premio'];
$edt_aprovacao     = $_REQUEST['edt_aprovacao'];
$edt_uf            = $_REQUEST['edt_uf'];


//verifica autorizacao para abertura
if ($autorizacao=="autorized") {


if ($excel!="yes") {

  //define o estilo
  require_once("../../../../site/includes/sbce.css");

  echo '

  <HTML>
  <HEAD>
  <TITLE>Relatório de Apólice</TITLE>

  <style type="text/css">

    .titulo {
      font-family: "verdana";
      font-weight: bold;
      font-size : 11px;
    }
    .subTitulo {
      font-family: "verdana";
      font-weight: bold;
      font-size : 12px;
    }

    /*** Impressão de amostra ***/

    /* Padrão para a tela */
    #print-head,
    #print-foot {
      display: none;
    }

    /* Somente impressão */
    @media print {

      h1 {
        page-break-before: always;
        padding-top: 2em;
        font-size: 1px;
      }

      h1:first-child {
        page-break-before: avoid;
        counter-reset: page;
        font-size: 1px;
      }

      #print-head {
        display: block;
        position: fixed;
        top: 0pt;
        left:0pt;
        right: 0pt;
        font-size: 200%;
        text-align: center;
      }

      #print-foot {
        display: block;
        position: fixed;
        bottom: 0pt;
        right: 0pt;
        font-size: 200%;
      }

      #print-foot:after {
        content: counter(page);
        counter-increment: page;
      }

    }

    /* Fim de somente impressão */


  </style>

  <script language="javascript">

  function imprimir() {
    frm_view_rel_apolice.icones.style.display = \'none\';
    window.print();
    frm_view_rel_apolice.icones.style.display = \'block\';
  }

  function gerar_excel() {
    frm_view_rel_apolice.submit(true);
  }

  </script>

  </HEAD>
  <BODY>

  ';
} //fim if ($excel!="yes") {



//prepara as definicoes e formatacoes para pesquisa
$sql_filtro = "";

if ($edt_name != "") {
  $sql_filtro .= " and upper(i.name) like upper('%$edt_name%') ";
}

//tratamento para data
$cmpl_dt  = "";
if (($edt_startValidity != "") & ($edt_endValidity != "")) {

  //formata a data para pesquisa
  list($dia, $mes, $ano) = explode("[/-]", $edt_startValidity);
  $edt_nv_dt_i = "$ano/$mes/$dia";

  $cmpl_dt = "Período entre ".$edt_startValidity." e ";



  list($dia, $mes, $ano) = explode("[/-]", $edt_endValidity);
  $edt_nv_dt_e = "$ano$mes$dia";

  $sql_filtro .= " and i.startValidity  BETWEEN '$edt_nv_dt_i' and '$edt_nv_dt_e' ";

  /*
  $sql_filtro .= " and convert(datetime, i.startValidity, 103) >= '$edt_nv_dt_i 00:00:00.000' ";
  $sql_filtro .= " and (
                        substring(convert(char(10), i.endValidity, 103),7,4)+
                        substring(convert(char(10), i.endValidity, 103),4,2)+
                        substring(convert(char(10), i.endValidity, 103),1,2)
                       ) <= '$edt_nv_dt_e' ";
  */

  $cmpl_dt = $cmpl_dt.$edt_endValidity;


} else {


 if ($edt_startValidity != "") {

    list($dia, $mes, $ano) = explode("[/-]", $edt_startValidity);
    $edt_nv_dt_i = "$ano/$mes/$dia";

    $sql_filtro .= " and i.startValidity >= '$edt_nv_dt_i' ";

    $cmpl_dt = "Período a partir de ".$edt_startValidity;



 } elseif ($edt_endValidity != "") {

    list($dia, $mes, $ano) = explode("[/-]", $edt_endValidity);
    $edt_nv_dt_e = "$ano/$mes/$dia";

    $sql_filtro .= " and i.endValidity <= '$edt_nv_dt_e' ";
//	$sql_filtro .= " and (getdate()+84 between i.startValidity and i.endValidity ) ";
    $cmpl_dt = "Período até ".$edt_endValidity;

 }

}



if ($edt_prMin != "") {

  /*
  //filtro por edit - funcionando

  //retira os pontos (.)
  $edt_prMin = str_replace(".", "", $edt_prMin);
  //retira a virgula e os centavos
  $edt_prMin = number_format($edt_prMin,0,',','');

  $sql_filtro .= " and (";
  $sql_filtro .= " case when i.codProd = 1 then ";
  $sql_filtro .= "   convert(int,(i.prMTotal)) ";
  $sql_filtro .= " else ";
  $sql_filtro .= "   case when i.warantyInterest = 1 then ";
  $sql_filtro .= "     convert(varchar(15),(i.prMin)*(1+i.txRise)*(i.warantyInterest * 1),0) ";
  $sql_filtro .= "   else ";
  $sql_filtro .= "     convert(varchar(15),(i.prMin)*(1+i.txRise)*(i.warantyInterest * 1.04),0) ";
  $sql_filtro .= "   end  ";
  $sql_filtro .= " end ";
  $sql_filtro .= " ) = convert(int,'$edt_prMin') ";

  */
  /*
  $sql_add1  = " and (";
  $sql_add1 .= " case when i.codProd = 1 then ";
  $sql_add1 .= "   convert(int,(i.prMTotal)) ";
  $sql_add1 .= " else ";
  $sql_add1 .= "   case when i.warantyInterest = 1 then ";
  $sql_add1 .= "     convert(varchar(15),(i.prMin)*(1+i.txRise)*(i.warantyInterest * 1),0) ";
  $sql_add1 .= "   else ";
  $sql_add1 .= "     convert(varchar(15),(i.prMin)*(1+i.txRise)*(i.warantyInterest * 1.04),0) ";
  $sql_add1 .= "   end  ";
  $sql_add1 .= " end ";
  $sql_add1 .= " ) ";
  */

  $sql_add1  = " and (";
  $sql_add1 .= "
 case when i.warantyInterest = 1 then
    (convert(float(20),i.prMin,0)*(1+convert(float(20),i.txRise,0)))+(convert(float(20),i.prMin,0)*(1+convert(float(20),i.txRise,0))*(i.warantyInterest * 0.04))
  else
    (i.prMTotal)
  end
  ";
  $sql_add1 .= " ) ";


  //recebe variavel e verifica condicoes de pesquisa
  if ($edt_prMin == 1) {
    $sql_add2 = " <= convert(int,'10000') ";
    $sql_add3 = "";
    $sql_add4 = "";
  } elseif ($edt_prMin == 2) {
    $sql_add2 = " >= convert(int,'10000') ";
    $sql_add3 = " <= convert(int,'20000') ";
    $sql_add4 = $sql_add1;
  } elseif ($edt_prMin == 3) {
    $sql_add2 = " >= convert(int,'20000') ";
    $sql_add3 = " <= convert(int,'30000') ";
    $sql_add4 = $sql_add1;
  } elseif ($edt_prMin == 4) {
    $sql_add2 = " >= convert(int,'30000') ";
    $sql_add3 = " <= convert(int,'50000') ";
    $sql_add4 = $sql_add1;
  } elseif ($edt_prMin == 5) {
    $sql_add2 = " >= convert(int,'50000') ";
    $sql_add3 = " <= convert(int,'100000') ";
    $sql_add4 = $sql_add1;
  } elseif ($edt_prMin == 6) {
    $sql_add2 = " > convert(int,'100000') ";
    $sql_add3 = "";
    $sql_add4 = "";
  }

  //monta sql para filtro
  $sql_filtro .= " $sql_add1 $sql_add2 $sql_add4 $sql_add3 ";


}

if ($edt_proex != "") {

  //recebe variavel e verifica condicoes de pesquisa
  if ($edt_proex == 1) {
    //$sql_filtro .= " and i.proex is not null and i.proex <> 0.0 ";
    $sql_filtro .= " and i.warantyInterest = 1   ";
  } elseif ($edt_proex == 2) {
    //$sql_filtro .= " and i.proex is null ";
    $sql_filtro .= " and i.warantyInterest <> 1  ";
  }

}


if ($edt_caex != "") {
  //$sql_filtro .= " and i. = '$edt_caex' ";
}

if ($edt_tx_premio != "") {

  /*
  //filtro por edit - funcionando
  //$sql_filtro .= " and isnull(convert(varchar(10),((i.txMin * (1+i.txRise)) * 100),0),0) = '$edt_tx_premio' ";
  */

  $sql_add1 = " and isnull(convert(varchar(10),((i.txMin * (1+i.txRise)) * 100),0),0)  ";

  //recebe variavel e verifica condicoes de pesquisa
  if ($edt_tx_premio == 1) {
    $sql_add2 = " <= '0.50000' ";
    $sql_add3 = "";
    $sql_add4 = "";
  } elseif ($edt_tx_premio == 2) {
    $sql_add2 = " >= '0.50000' ";
    $sql_add3 = " <= '1.00000' ";
    $sql_add4 = $sql_add1;
  } elseif ($edt_tx_premio == 3) {
    $sql_add2 = " >= '1.00000' ";
    $sql_add3 = " <= '1.50000' ";
    $sql_add4 = $sql_add1;
  } elseif ($edt_tx_premio == 4) {
    $sql_add2 = " >= '1.50000' ";
    $sql_add3 = "";
    $sql_add4 = "";
  }

  //monta sql para filtro
  $sql_filtro .= " $sql_add1 $sql_add2 $sql_add4 $sql_add3 ";

}

if ($edt_aprovacao != "") {
  //
}


if ($edt_uf != "") {
  //$sql_filtro .= " and upper(i.uf) like upper('%$edt_uf%') ";
  //$sql_filtro .= " and upper(i.chargeUf) like upper('%$edt_uf%') ";
  $sql_filtro .= " and
            (
            case when i.sameAddress = 1 then
              i.uf
            else
              i.chargeUf
            end
            ) like upper('%$edt_uf%')
          ";
}





/*
//montagem do sql
$sql1 = "

     SELECT
           i.id
 	   FROM (
            SELECT idImporter, credit, creditDate, creditTemp, limTemp
              FROM ChangeCredit ch
             WHERE ch.id IN
             (
               SELECT max (id)
                 FROM ChangeCredit
                GROUP BY idImporter
             )
            ) ch
            RIGHT JOIN Importer imp ON (ch.idImporter = imp.id)
                  JOIN Inform   i   ON (imp.idInform  = i.id)
 	    where (imp.state = 6 OR ((imp.state = 2) AND NOT ch.credit IS NULL))
 	      and i.state in (10)
              $sql_filtro
        group by
                i.id
        order by i.id

          ";

$cur = odbc_exec($db,$sql1);

//echo $sql1."<br>";


while (odbc_fetch_row($cur)) {

  $idInform  = odbc_result($cur, 'id');
  //$sql_perc = " SELECT inf.contrat, imp.name, imp.c_Coface_Imp, imp.limCredit, c.name, ch.credit, imp.validityDate AS creditDate, imp.id, ch.creditTemp, ch.limTemp, c.code, imp.address, imp.idAprov FROM ( SELECT idImporter, credit, creditDate, creditTemp, limTemp FROM ChangeCredit ch WHERE ch.id IN ( SELECT max (id) FROM ChangeCredit GROUP BY idImporter ) ) ch RIGHT JOIN Importer imp ON (ch.idImporter = imp.id) JOIN Inform inf ON (imp.idInform = inf.id) JOIN Country c ON (imp.idCountry = c.id) WHERE inf.id = $idInform AND (imp.state = 6 OR ((imp.state = 2 OR imp.state = 4) AND NOT ch.credit IS NULL)) ORDER BY c.name, imp.name ";
  //$sql_perc = " SELECT sum(imp.limCredit), sum(ch.credit), sum(ch.creditTemp) FROM ( SELECT idImporter, credit, creditDate, creditTemp, limTemp FROM ChangeCredit ch WHERE ch.id IN ( SELECT max (id) FROM ChangeCredit GROUP BY idImporter ) ) ch RIGHT JOIN Importer imp ON (ch.idImporter = imp.id) JOIN Inform inf ON (imp.idInform = inf.id) JOIN Country c ON (imp.idCountry = c.id) WHERE inf.id = $idInform AND (imp.state = 6 OR ((imp.state = 2 OR imp.state = 4) AND NOT ch.credit IS NULL)) ";
  $sql_perc = " SELECT imp.limCredit, ch.credit, ch.creditTemp FROM ( SELECT idImporter, credit, creditDate, creditTemp, limTemp FROM ChangeCredit ch WHERE ch.id IN ( SELECT max (id) FROM ChangeCredit GROUP BY idImporter ) ) ch RIGHT JOIN Importer imp ON (ch.idImporter = imp.id) JOIN Inform inf ON (imp.idInform = inf.id) JOIN Country c ON (imp.idCountry = c.id) WHERE inf.id = $idInform AND (imp.state = 6 OR ((imp.state = 2 OR imp.state = 4) AND NOT ch.credit IS NULL)) ";
  $rs_perc = odbc_exec($db,$sql_perc);

  if (
      (trim($idInform)=="4747")
   or (trim($idInform)=="4694")
   ) {
    echo "<table>";
    echo "<tr><td>Credito solicitado</td><td>Credito concedido</td><td>Credito temporario</td><tr>";
  }

  while (odbc_fetch_row($rs_perc)) {

//Credito solicitado
$importerCredit      = odbc_result($rs_perc, 1);

//Credito concedido
$changeCredit        = odbc_result($rs_perc, 2);

//Credito temporario
$creditTemp          = odbc_result($rs_perc, 3);

//Credito solicitado
$importerCredit  = number_format($importerCredit/1000, 0, ",", ".");

//Credito concedido
$changeCredit    = number_format($changeCredit/1000, 0, ",", ".");

//Credito temporario
$creditTemp      = number_format($creditTemp/1000, 0, ",", ".");

//Credito solicitado
$importerCredit = str_replace(".", "", $importerCredit);
$importerCreditTotal += $importerCredit;

//Credito concedido
$changeCredit = str_replace(".", "", $changeCredit);
$changeCreditTotal += $changeCredit;

//Credito temporario
$creditTemp = str_replace(".", "", $creditTemp);
$creditTemptotal+=$creditTemp;

if (
      (trim($idInform)=="4747")
   or (trim($idInform)=="4694")
   ) {

echo "<tr><td>$importerCredit</td><td>$changeCredit</td><td>$creditTemp</td><tr>";

}


  }


if (
      (trim($idInform)=="4747")
   or (trim($idInform)=="4694")
   ) {
  echo "</table><br>";
}

//percentual de aprovacao
$resultado = (($changeCreditTotal + $creditTemptotal) / $importerCreditTotal)*100;

//arredondamento
$resultado = round($resultado, 2);

//cria vetor
$vet_perc[$idInform] = $resultado;

} //fim while
echo "fim<br>";
*/




//montagem do sql
$sqlFiltro = ""; // Defina os filtros de forma segura antes de montar a query

// Prepare a query utilizando parâmetros
$strSQL = "
    SELECT
        i.name,
        i.proex, i.prMin, i.txRise,
        i.prMTotal,
        i.codProd, i.warantyInterest,
        i.numParc, i.txMin,
        ISNULL(CONVERT(VARCHAR(10), ((i.txMin * (1 + i.txRise)) * 1) * (100), 0), 0) AS tx_premio,
        i.id,
        CASE 
            WHEN i.sameAddress = 1 THEN i.uf
            ELSE i.chargeUf
        END AS uf,
        i.idRegion,
        CONVERT(CHAR(10), i.startValidity, 103) AS dt_ini,
        CONVERT(CHAR(10), i.endValidity, 103) AS dt_end,
        i.currency,
        CASE 
            WHEN i.warantyInterest = 1 THEN
                (CONVERT(FLOAT(20), i.prMin, 0) * (1 + CONVERT(FLOAT(20), i.txRise, 0))) + 
                (CONVERT(FLOAT(20), i.prMin, 0) * (1 + CONVERT(FLOAT(20), i.txRise, 0)) * (i.warantyInterest * 0.04))
            ELSE
                i.prMTotal
        END AS premio_total,
        (
            (
                (
                    CASE 
                        WHEN SUM(ch.credit) IS NULL THEN 0
                        ELSE (SUM(ch.credit) / 1000)
                    END
                    +
                    CASE 
                        WHEN SUM(ch.creditTemp) IS NULL THEN 0
                        ELSE (SUM(ch.creditTemp) / 1000)
                    END
                )
                /
                CASE 
                    WHEN SUM(imp.limCredit) IS NULL THEN 0
                    ELSE (SUM(imp.limCredit) / 1000)
                END
            ) * 100
        ) AS perc_aprov,
        CASE 
            WHEN i.warantyInterest = 1 THEN 'Sim'
            ELSE 'Não'
        END AS warantyInterest_proex,
        SUM(imp.limCredit) AS ch1,
        SUM(ch.creditTemp) AS ch2,
        SUM(ch.credit) AS ch3,
        i.id, i.idConsultor, c.razao, c.contato
    FROM (
        SELECT idImporter, credit, creditDate, creditTemp, limTemp
        FROM ChangeCredit ch
        WHERE ch.id IN (
            SELECT MAX(id)
            FROM ChangeCredit
            GROUP BY idImporter
        )
    ) ch
    RIGHT JOIN Importer imp ON (ch.idImporter = imp.id)
    JOIN Inform i ON (imp.idInform = i.id)
    LEFT JOIN consultor c ON c.idconsultor = i.idConsultor
    WHERE 
        (imp.state = 6 OR (imp.state IN (2, 4) AND NOT ch.credit IS NULL))
        AND i.state IN (10)
        $sqlFiltro
    GROUP BY
        i.name, i.startValidity, i.endValidity,
        i.proex, i.prMin, i.txRise,
        i.prMTotal, i.codProd, i.warantyInterest,
        i.numParc, i.txMin, i.id, i.uf, i.chargeUf,
        i.currency, i.sameAddress, i.idRegion, i.idConsultor,
        c.razao, c.contato
    ORDER BY i.name;
";

$strTabela = "";

// Utilize prepared statements para executar a query
$stmt = odbc_prepare($db, $strSQL);
odbc_execute($stmt); // Passe os valores de $sqlFiltro como parâmetros, caso necessário

$rs = $stmt; // O resultado é mantido como $rs

odbc_free_result($stmt);
//variavel de definicao da cor da linha
$jj   = 0;
$jj_c = 0;
$jjj  = 0;

//quantidade de linhas para quebra de pagina
$ln_quebrar = 23;

while (odbc_fetch_row($rs)) {

  //o mesmo procedimento contido no arquivo informres.php
  //em informacoes gerais do cliente
  //$premio_min = odbc_result($rs, 'prMin') * (1 + odbc_result($rs, 'txRise'));
  //$add_juros  = $premio_min * (odbc_result($rs, 'warantyInterest') ? 0.04 : 0);
  //if (odbc_result($rs, 'warantyInterest')) {
  //  $premio_min_total = number_format(($premio_min + $add_juros),2,',','.');
  //} else {
  //  $premio_min_total = number_format(odbc_result($rs, 'prMTotal'),2,',','.');
  //}
  //$campo_8  = $premio_min_total;


  //atribui valores aos campos
  $campo_1  = ucwords(strtolower(odbc_result($rs, 'name')));
  $campo_2  = odbc_result($rs, 'dt_ini') . "&nbsp;a&nbsp;" . odbc_result($rs, 'dt_end');


  //$campo_3  = odbc_result($rs, 'uf');
  //$campo_3  = odbc_result($rs, 'chargeUf');
  //$campo_3  = odbc_result($rs, 'uf');

  //alterado em 13/03/2008 solicitado pela
  //marcele, dizendo ser este campo q devera
  //aparecer no relatorio
  $sel = odbc_result($rs, 'idRegion');
  // Preparar a query com um parâmetro
$sql_uf = "SELECT id, description, name FROM Region WHERE id = ?";

// Preparar a instrução ODBC
$stmt = odbc_prepare($db, $sql_uf);

// Executar a instrução passando o valor de $sel como parâmetro
odbc_execute($stmt, [$sel]);

$rs_uf = $stmt; // O resultado é mantido como $rs_uf

// Processar os resultados
while (odbc_fetch_row($rs_uf)) {
    $campo_3 = odbc_result($rs_uf, 'name');
}




  //$campo_4  = odbc_result($rs, 'proex');
  $campo_4  = odbc_result($rs, 'warantyInterest_proex');
  $campo_5  = "";
  //$campo_6  = number_format(odbc_result($rs, 'tx_premio'), 5, ',', '.');
  //$campo_6  = odbc_result($rs, 'tx_premio')."%";
  $campo_6  = number_format(odbc_result($rs, 'tx_premio'), 3, ',', '.')."%";

  //$campo_7  = number_format(odbc_result($rs, 'perc_aprov'),2,',','.')."%";
  $campo_7  = round(odbc_result($rs, 'perc_aprov'), 2);
  $campo_7  = number_format($campo_7,2,',','.')."%";



/*
  $idInform  = odbc_result($rs, 'id');

  $sql_perc = " SELECT inf.contrat, imp.name, imp.c_Coface_Imp, imp.limCredit, c.name, ch.credit, imp.validityDate AS creditDate, imp.id, ch.creditTemp, ch.limTemp, c.code, imp.address, imp.idAprov FROM ( SELECT idImporter, credit, creditDate, creditTemp, limTemp FROM ChangeCredit ch WHERE ch.id IN ( SELECT max (id) FROM ChangeCredit GROUP BY idImporter ) ) ch RIGHT JOIN Importer imp ON (ch.idImporter = imp.id) JOIN Inform inf ON (imp.idInform = inf.id) JOIN Country c ON (imp.idCountry = c.id) WHERE inf.id = $idInform AND (imp.state = 6 OR ((imp.state = 2 OR imp.state = 4) AND NOT ch.credit IS NULL)) ORDER BY c.name, imp.name ";
  $rs_perc = odbc_exec($db,$sql_perc);
  while (odbc_fetch_row($rs_perc)) {

//Credito solicitado
$importerCredit      = odbc_result(rs_perc, 4);
//$importerCredit      = odbc_result(rs_perc, 'ch1');

//Credito concedido
$changeCredit        = odbc_result(rs_perc, 6);
//$changeCredit        = odbc_result(rs_perc, 'ch2');

//Credito temporario
$creditTemp          = odbc_result(rs_perc, 9);
//$creditTemp          = odbc_result(rs_perc, 'ch3');

//Credito solicitado
$importerCredit  = number_format($importerCredit/1000, 0, ",", ".");

//Credito concedido
$changeCredit    = number_format($changeCredit/1000, 0, ",", ".");

//Credito temporario
$creditTemp      = number_format($creditTemp/1000, 0, ",", ".");

//Credito solicitado
$importerCredit = str_replace(".", "", $importerCredit);
$importerCreditTotal += $importerCredit;

//Credito concedido
$changeCredit = str_replace(".", "", $changeCredit);
$changeCreditTotal += $changeCredit;

//Credito temporario
$creditTemp = str_replace(".", "", $creditTemp);
$creditTemptotal+=$creditTemp;

  }

//percentual de aprovacao
$resultado = ( ($changeCreditTotal + $creditTemptotal) / $importerCreditTotal)*100;
$resultado = round($resultado, 2);



if (
   //   (trim($campo_1)=="Acesita Sa")
      (trim($campo_1)=="Cia Industrial Cataguases")
   //or (trim($campo_1)=="Sudambeef Ind Com Imp Exp Ltda")
   //or (trim($campo_1)=="Cristalia Produtos Quimicos Farmaceuticos Ltda")
   or (trim($campo_1)=="Curtume Cruzeiro Ltda")
   //or (trim($campo_1)=="Fosbrasil Sa")
   //or (trim($campo_1)=="Novelis Do Brasil Ltda")
   //or (trim($campo_1)=="Sanremo S.a")
   //or (trim($campo_1)=="Sudambeef Ind Com Imp Exp Ltda")
   ) {


  echo "########################<br>";
  echo $campo_1." -> ".odbc_result($rs, 'id')."<br>";
  echo odbc_result($rs, 'ch1')."<br>";
  echo odbc_result($rs, 'ch2')."<br>";
  echo odbc_result($rs, 'ch3')."<br>";

  echo (odbc_result($rs, 'ch1')/1000)."<br>";
  echo (odbc_result($rs, 'ch2')/1000)."<br>";
  echo (odbc_result($rs, 'ch3')/1000)."<br>";

  //$aa = (odbc_result($rs, 'ch1')/1000);
  //$bb = (odbc_result($rs, 'ch2')/1000);
  //$cc = (odbc_result($rs, 'ch3')/1000);
  $aa = (odbc_result($rs, 'ch1'));
  $bb = (odbc_result($rs, 'ch2'));
  $cc = (odbc_result($rs, 'ch3'));
  settype($aa,integer);
  settype($bb,integer);
  settype($cc,integer);
  $aa = floor($aa);
  $bb = floor($bb);
  $cc = floor($cc);
  $resultado = ( ($bb + $cc) / $aa ) * 100;
  $resultado = round($resultado,3);
  $campo_7  = number_format($resultado,2,',','.');
  echo "$aa - $bb - $cc<br>";
  echo "$resultado<br>";
  echo "$campo_7<br>";

}
*/

  //percentual de aprovacao
  $aa = (odbc_result($rs, 'ch1')/1000);
  $bb = (odbc_result($rs, 'ch2')/1000);
  $cc = (odbc_result($rs, 'ch3')/1000);
  settype($aa,integer);
  settype($bb,integer);
  settype($cc,integer);
  $aa = floor($aa);
  $bb = floor($bb);
  $cc = floor($cc);
  $resultado = ( ($bb + $cc) / $aa ) * 100;
  $resultado = round($resultado,3);
  $campo_7  = number_format($resultado,2,',','.');




  /*
  //premio minimo / premio minimo total
  $campo_8  = number_format(odbc_result($rs, 'premio_total'),2,',','.');

  if (odbc_result($rs, 'warantyInterest') == 1) {
    $campo_8  = (odbc_result($rs, 'prMin')*(1+odbc_result($rs, 'txRise')))+(odbc_result($rs, 'prMin')*(1+odbc_result($rs, 'txRise'))*(odbc_result($rs, 'warantyInterest') * 0.04));
  } else {
    $campo_8  = odbc_result($rs, 'prMTotal');
  }
  $campo_8  = number_format($campo_8,2,',','.');
  */


  //premio minimo / premio minimo total
  $premio_min = odbc_result($rs, 'prMin');
  $taxa_min   = odbc_result($rs, 'txMin');
  $taxa_rise  = odbc_result($rs, 'txRise');
  $waranty    = odbc_result($rs, 'warantyInterest');
  $premio_min = $premio_min * (1 + $taxa_rise);
  $adicional_juros = $premio_min * ($waranty ? 0.04 : 0);

  if ($waranty) {
    $campo_8 = $premio_min  + $adicional_juros;
  } else {
    $campo_8 = odbc_result($rs, 'prMTotal');
  }
  $campo_8 = number_format($campo_8,2,',','.');




  $campo_9  = odbc_result($rs, 'currency');


  //formatacao do tipo da moeda
  if ($campo_9 == 2) {
    $campo_9  = "US$";
  } elseif ($campo_9 == 6) {
    $campo_9  = "€";
  } else {
    $campo_9  = "";
  }


  //calculo do caex
  $display_caex = floor(odbc_result($rs, 'prMin') * (odbc_result($rs, 'warantyInterest') == 1 ? 1.04 : 1) * (1 + (odbc_result($rs, 'txRise'))/100) / odbc_result($rs, 'numParc')) * odbc_result($rs, 'numParc');
  $tx = odbc_result($rs, 'txMin') * (1 + odbc_result($rs, 'txRise') / 100) * 100;
  $tx = number_format ($tx, 3, '.','');
  $caex  = 0;
  $total = $display_caex / ($tx/100);
  $caex  = number_format ($total, 2, ',','.');
  $campo_5  = $caex;

  /*
  $prDisplay     = floor(odbc_result($cur, 'prMin') * (odbc_result($cur, 'warantyInterest') == 1 ? 1.04 : 1) * (1 + ($txRise)/100) / $numParc) * $numParc;
  $tx = $txMin * (1 + $txRise / 100) * 100;
  $tx = number_format ($tx, 3, '.','');
  $caex = 0;
  $total = $prDisplay / ($tx/100);
  $caex = number_format ($total, 2, ',','.');
  $campo_5  = $caex;
  */


  //tratamento especial do filtro para aprovacao e caex
  $ver_linha = "block";
  if ($edt_aprovacao != "") {

    $cmp = $campo_7;
    settype($cmp,float);

    if ($edt_aprovacao == "abaixo") {
      if ($cmp<70) {
        $ver_linha = "block";
      } else {
        $ver_linha = "none";
      }
    } else {
      if ($cmp>=70) {
        $ver_linha = "block";
      } else {
        $ver_linha = "none";
      }
    }

  }
  if ($edt_caex != "") {

    //retira os pontos (.)
    $cmp = str_replace(".", "", $campo_5);
    //retira a virgula e os centavos
    $cmp = number_format($cmp,0,',','');

    if ($edt_caex == 1) {
      if ($cmp<=1000000) {
        $ver_linha = "block";
      } else {
        $ver_linha = "none";
      }
    } elseif ($edt_caex == 2) {
      if (($cmp>=1000000)&&($cmp<=5000000)) {
        $ver_linha = "block";
      } else {
        $ver_linha = "none";
      }
    } elseif ($edt_caex == 3) {
      if (($cmp>=5000000)&&($cmp<=10000000)) {
        $ver_linha = "block";
      } else {
        $ver_linha = "none";
      }
    } elseif ($edt_caex == 4) {
      if (($cmp>=10000000)&&($cmp<=15000000)) {
        $ver_linha = "block";
      } else {
        $ver_linha = "none";
      }
    } elseif ($edt_caex == 5) {
      if (($cmp>=15000000)) {
        $ver_linha = "block";
      } else {
        $ver_linha = "none";
      }
    }

  } //fim if ($edt_caex != "") {


  $campo_10  = "";
  $campo_10  = trim(odbc_result($rs, 'idConsultor'));
  $campo_10a = odbc_result($rs, 'razao');
  $campo_10b = odbc_result($rs, 'contato');
  if (($campo_10!="")&($campo_10!="0")) {
    $campo_10 = "(<font color=red title='$campo_10a - ($campo_10b)'>*</font>)";
  }

  if ($jjj==$ln_quebrar) {
    $strTabela .= "</table><h1>&nbsp;</h1><table>";
    $jjj = 0;
  }

  $jjj++;


  $str_cabecalho = '
    <tr bgcolor="#aaccff">
       <td align="center" class="subTitulo" width="23%">Nome</td>
       <td align="center" class="subTitulo" width="25%">Data de Vigência</td>
       <td align="center" class="subTitulo" width="8%">Moeda</td>
       <td align="center" class="subTitulo" width="12%">Prêmio Mínimo Total</td>
       <td align="center" class="subTitulo" width="8%">Cobertura PROEX</td>
       <td align="center" class="subTitulo" width="15%">Faturamento Segurável</td>
       <td align="center" class="subTitulo" width="9%">Taxa de Prêmio (%)</td>
       <td align="center" class="subTitulo" width="8%">Percentual de Aprovação</td>
       <td align="center" class="subTitulo" width="8%">Estado</td>
    </tr>
  ';

  //cabecalho
  if (($jj==0)||($jj==$jj_c)) {
    $strTabela .= "$str_cabecalho";
    $jj_c = $jj+$ln_quebrar;
  }

  //altera a cor da linha
  $cor_bg = "";
  if ( $jj % 2 == 0) {
    $cor_bg = "background-color:#EEE9E9;";
  }
  $jj++;


  //montar linhas da tabela em html
  $strTabela .= "<tr style='font-weight:normal;display:$ver_linha;$cor_bg'>";
  $strTabela .= " <td align=\"left\"  style='font-weight:normal;'>".$campo_1.$campo_10."</td>";
  $strTabela .= " <td align=\"center\"style='font-weight:normal;'>".$campo_2."</td>";
  $strTabela .= " <td align=\"center\"style='font-weight:normal;'>".$campo_9."</td>";
  $strTabela .= " <td align=\"right\" style='font-weight:normal;'>".$campo_8."</td>";
  $strTabela .= " <td align=\"center\"style='font-weight:normal;'>".$campo_4."</td>";
  $strTabela .= " <td align=\"right\" style='font-weight:normal;'>".$campo_5."</td>";
  $strTabela .= " <td align=\"right\" style='font-weight:normal;'>".$campo_6."</td>";
  $strTabela .= " <td align=\"right\" style='font-weight:normal;'>".$campo_7."</td>";
  $strTabela .= " <td align=\"center\"style='font-weight:normal;'>".$campo_3."</td>";
  $strTabela .= "</tr>";


} //fim while



//define a quantidade de colunas na tabela
$col = 8;

echo '
  <form name="frm_view_rel_apolice" action="view_rel_apolice.php" method="post" target="_self">
  <input type="hidden" name="params" value="autorized">
  <input type="hidden" name="excel" value="yes">

  <input type="hidden" name="edt_name" value="' . htmlspecialchars($edt_name, ENT_QUOTES, 'UTF-8') . '">
  <input type="hidden" name="edt_startValidity" value="' . htmlspecialchars($edt_startValidity, ENT_QUOTES, 'UTF-8') . '">
  <input type="hidden" name="edt_endValidity" value="' . htmlspecialchars($edt_endValidity, ENT_QUOTES, 'UTF-8') . '">
  <input type="hidden" name="edt_prMin" value="' . htmlspecialchars($edt_prMin, ENT_QUOTES, 'UTF-8') . '">
  <input type="hidden" name="edt_proex" value="' . htmlspecialchars($edt_proex, ENT_QUOTES, 'UTF-8') . '">
  <input type="hidden" name="edt_caex" value="' . htmlspecialchars($edt_caex, ENT_QUOTES, 'UTF-8') . '">
  <input type="hidden" name="edt_tx_premio" value="' . htmlspecialchars($edt_tx_premio, ENT_QUOTES, 'UTF-8') . '">
  <input type="hidden" name="edt_aprovacao" value="' . htmlspecialchars($edt_aprovacao, ENT_QUOTES, 'UTF-8') . '">
  <input type="hidden" name="edt_uf" value="' . htmlspecialchars($edt_uf, ENT_QUOTES, 'UTF-8') . '">

  <table align="center" width="100%" border="0" cellspacing="1" cellpadding="1">
    <tr>
       <td colspan="' . ($col - 0) . '" align="left">
       <center><h4>Relatório de Apólice</h4></center>
       <center><h6>' . htmlspecialchars($cmpl_dt, ENT_QUOTES, 'UTF-8') . '</h6></center>
       </td>
    </tr>
    ' . $strTabela . '
    <tr>
       <td colspan="' . $col . '" align="center">&nbsp;</td>
    </tr>
    <tr>
       <td colspan="' . $col . '" align="left">(<font color=red>*</font>) - Possui corretagem</td>
    </tr>
    <tr>
       <td colspan="' . $col . '" align="center">&nbsp;</td>
    </tr>
    <tr id="icones">
       <td colspan="' . $col . '" align="left">
';




if ($excel!="yes") {
  echo '
          <a href="#" onclick="imprimir()" title="Imprimir"><img src="img/btn_printer.jpg" width="40" height="40" border="0"></a>
          <a href="#" onclick="gerar_excel()" title="Exportar para Excel"><img src="img/icone_xls.gif" width="32" height="32" border="0"></a>
       ';
}


echo '
       </td>
    </tr>

  </table>
';


if ($excel!="yes") {
  echo '</form></BODY></HTML>';
}


} else {
  echo "Página não autorizada para abertura.";
}

/*
//####### end ####### modificada por eliel vieira - elumini - 28/01/2008
*/


?>
