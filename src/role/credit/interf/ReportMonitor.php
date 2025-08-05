<?php  // monta a tela com a lista de exportadores
//Alterado HiCom mes 04

function mkdate($a, $m, $d)
{
  return date("Y-m-d", mktime(0, 0, 0, $m, $d, $a));
}

$anoCorrente = date('Y');
$mesCorrente = date('m');

if (($_REQUEST['ano'] == $anoCorrente || $_REQUEST['mes'] == $mesCorrente) && ($_REQUEST['ano'] != "" && $_REQUEST['mes'] != "") && $_REQUEST['ret2'] == 'retornar' || ($_REQUEST['ano'] >= 2008 && $_REQUEST['mes'] >= 12)) {
  echo "<script>location.href='" . htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . "role/credit/Credit.php?comm=fechamentoMensal&ano=" . urlencode($_REQUEST['ano']) . "&mes=" . urlencode($_REQUEST['mes']) . "'</script>";
}


$mesAtu = date('m');
if (!$mes)
  $mes = $mesAtu;
$m = ($mes - 3) % 12;
$m = $m + 1;

$anoAtu = date('Y');
if (!$ano)
  $ano = $anoAtu;
$a = $ano;

// comentario pra Andrea entender:
// verifica se ja teve fechamento do mes
// Acesso à nova estrutura de análise e monitoramento
$cur = odbc_exec($db, "SELECT id FROM resFatAnaliseMonitor WHERE mes=$mes AND ano=$ano");
$generated = odbc_fetch_row($cur);



$dInicio = mkdate($a, $m, 1);
$dFim = mkdate($a, $m + 1, 0);

$last = mkdate($ano, $mes + 1, 1);

$meses = "(startValidity >= '$dInicio' AND startValidity <= '$dFim')";
for ($i = 1; $i < 4; $i++) {
  $m = ($m + 2) % 12;
  $m++;

  $a = $m > $mes ? $ano - 1 : $ano;

  $dInicio = mkdate($a, $m, 1);
  $dFim = mkdate($a, $m + 1, 0);

  $meses .= "\n  OR (startValidity >= '$dInicio' AND startValidity <= '$dFim')";
}

$query = "";
if ($generated) { //|| ($mes != $mesAtu)) {
  // se fechou o mes
  // Acesso à nova estrutura de análise e monitoramento
  $query = "
  SELECT inf.name, startValidity, sum(ir.txAnalise) AS analysis, er.txAnalise AS txAnalize,
         sum (ir.txMonitor) AS monitor, er.txMonitor, inf.id AS idInform
  FROM resFatAnaliseMonitor er
    JOIN resFatAnaliseMonitorImport ir ON IdResFat = er.id
    JOIN Inform inf ON er.idInform = inf.id
  WHERE er.mes = $mes AND er.ano = $ano
  GROUP BY inf.name, startValidity, er.txAnalise, er.txMonitor, inf.id
  HAVING (sum(ir.txAnalise) <> 0 ) OR (sum (ir.txMonitor) <> 0)
  ORDER BY inf.name
  ";
} else { // nao fechou o mes
/*
  Alteração 04/10/2007 - realizado por Tiago V N e Fabio Lucena
  Na consulta do fechamento de análise e monitoramento
*/

  /*
      $query = "
      SELECT inf.name, startValidity, sum (analysis) AS analysis, txAnalize,
           sum (monitor) AS monitor, txMonitor, inf.id AS idInform
      FROM Inform inf
        JOIN Importer imp ON imp.idInform = inf.id
        JOIN ChangeCredit cc ON cc.idImporter = imp.id
      WHERE (inf.state = 10 OR inf.state = 11) AND (inf.txAnalize <> 0 OR
       inf.txMonitor <> 0) AND
       ($meses) AND
        cc.id in (
        SELECT MAX (id)
        FROM ChangeCredit
        WHERE (stateDate <= '$last')
            AND (state = 2 OR state = 4 OR state = 5 OR state = 6 OR state = 7)
        GROUP BY idImporter
        ) AND (imp.c_Coface_Imp IS NOT NULL) AND (RTRIM(LTRIM(imp.c_Coface_Imp)) 
                <> '0') AND (RTRIM(LTRIM(imp.c_Coface_Imp)) <> '')
      GROUP BY inf.name, startValidity, txAnalize, txMonitor, inf.id
      HAVING (SUM (analysis) <> 0) OR (SUM (monitor) <> 0)
      ORDER BY inf.name
      ";*/


  /*
      28/11/2007 - Tiago V N
      O id = 4819 referente a o segurado Porto Trading que tem o enceramento da
      apolice antes do periodo. O sistema não consegue considerar o encerramento
      e por isso cobrar analise e monitoramento.
      */

  //query anterior, que é igual a que esta online
  /*$query = "  SELECT name, startValidity, sum(analysis) as analysis, txAnalize,
         sum (monitor) AS monitor, txMonitor, idInform FROM
         (SELECT cc.state as st, datediff(d, cc.stateDate,'2007-11-30') as dt_dif,
          inf.name, startValidity, sum (analysis) AS analysis, txAnalize,
         sum (monitor) AS monitor, txMonitor, inf.id AS idInform
      FROM Inform inf
      JOIN Importer imp ON imp.idInform = inf.id
      JOIN ChangeCredit cc ON cc.idImporter = imp.id
      WHERE (inf.state = 10 OR inf.state = 11 ) AND inf.id <> 4819 AND (inf.txAnalize <> 0 OR
       inf.txMonitor <> 0) AND
       ($meses) AND
      cc.id in (
        SELECT MAX (id)
        FROM ChangeCredit
        WHERE (stateDate <= '$last' )
          AND (state = 2 OR state = 4 OR state = 5 OR state = 6 OR state = 7)
        GROUP BY idImporter
      ) AND (imp.c_Coface_Imp IS NOT NULL) AND (RTRIM(LTRIM(imp.c_Coface_Imp))
                <> '0') AND (RTRIM(LTRIM(imp.c_Coface_Imp)) <> '') AND imp.state <> 8
      GROUP BY cc.state, cc.stateDate, inf.name, startValidity, txAnalize, txMonitor, inf.id
      HAVING (SUM (analysis) <> 0) OR (SUM (monitor) <> 0) ) as t
      WHERE (st = 2 OR st = 4 OR st = 5 OR st = 6 OR (st = 7 AND dt_dif < 90) )
      GROUP BY name, startValidity, txAnalize, txMonitor, idInform
      ORDER BY name";*/

  //ESTA QUERY TEM UMA ALTERAÇÃO E FOI ADICIONADA POR WAGNER
  $query = "  SELECT name, startValidity, sum(analysis) as analysis, txAnalize,
         sum (monitor) AS monitor, txMonitor, idInform 
     FROM(SELECT cc.state as st, datediff(d, cc.stateDate,'2007-11-30') as dt_dif,
          inf.name, startValidity, sum (analysis) AS analysis, txAnalize,
         sum (monitor) AS monitor, txMonitor, inf.id AS idInform
        FROM Inform inf
        JOIN Importer imp ON imp.idInform = inf.id
        JOIN ChangeCredit cc ON cc.idImporter = imp.id
        WHERE (inf.state = 10 OR inf.state = 11 ) 
        AND inf.id <> 4819 
        AND (inf.txAnalize <> 0 OR inf.txMonitor <> 0) 
        AND ($meses) 
        AND cc.id in (SELECT MAX(id) FROM ChangeCredit WHERE (stateDate <= '$last') AND state=6 AND credit<>0 GROUP BY idImporter) 
        AND (imp.c_Coface_Imp IS NOT NULL) 
        AND (RTRIM(LTRIM(imp.c_Coface_Imp)) <> '0') 
        AND (RTRIM(LTRIM(imp.c_Coface_Imp)) <> '') 
        AND imp.state not in(7,8,9)
        AND imp.id NOT IN(SELECT imp.id 
                  from Inform as a join Inform as b on a.id = b.idAnt 
                  join Importer as imp on a.id = imp.idInform
                  join ChangeCredit as cc on imp.id = cc.idImporter
                  where cc.credit = 0
                  and a.notificaRenova = 'S'
                  and DATEDIFF(m,a.endValidity,b.startValidity) <= 3 group by imp.id) 
        GROUP BY cc.state, cc.stateDate, inf.name, startValidity, txAnalize, txMonitor, inf.id
        HAVING (SUM (analysis) <> 0) OR (SUM (monitor) <> 0) ) as t
        GROUP BY name, startValidity, txAnalize, txMonitor, idInform
        ORDER BY name";

}

//echo "<pre>$query</pre>";
//break;
$cur = odbc_exec($db, $query);


?>
<form action="<?php echo $root; ?>role/credit/Credit.php">
  <input name=comm value="resMonitor" type=hidden>
  <input name="ret2" value="<?php echo htmlspecialchars($_REQUEST['ret'], ENT_QUOTES, 'UTF-8'); ?>" type="hidden">
  <table align=center border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr class="bgCinza">
      <th class="textoBold">Mês</th>
      <td>
        <select name=mes class="caixa">
          <?php for ($i = 1; $i <= 12; $i++) {
            ?>
            <option<?php echo $i == $mes ? ' SELECTED' : ''; ?>><?php echo $i; ?></option>
            <?php }
          ?>
        </select>
      </td>
      <th class="textoBold">Ano</th>
      <td>
        <select name=ano class="caixa">
          <option<?php echo $ano == $anoAtu ? ' SELECTED' : ''; ?>><?php echo $anoAtu; ?></option>
            <option<?php echo $ano == ($anoAtu - 1) ? ' SELECTED' : ''; ?>><?php echo $anoAtu - 1; ?></option>
        </select>
      </td>
    </tr>
    <tr class="bgCinza">
      <th colspan="4" class="bgCinza"><input type=submit value="Consultar" class="servicos"></th>
    </tr>
  </table>
</form>

<table width=100% cellspacing=0 cellpadding=2 border="0" align="center">
  <tr class="bgAzul">
    <th align=left class="bgAzul">Segurado</th>
    <!--<th>Proposta</th>-->
    <th class="bgAzul">Qtde. Análises</th>
    <th class="bgAzul">Análises</th>
    <th class="bgAzul">Qtde. Monitor.</th>
    <th class="bgAzul">Monitoramento</th>
    <th>Total</th>
  </tr>
  <?php $count = 0;
  $nAnT = 0;
  $nAmT = 0;
  $analiseT = 0;
  $monitorT = 0;
  $totalT = 0;
  $analise = 0;

  while (odbc_fetch_row($cur)) {
    $data = odbc_result($cur, 'startValidity');
    $data = substr($data, 8, 2) . "/" . substr($data, 5, 2) . "/" . substr($data, 0, 4);
    $nome = trim(odbc_result($cur, 'name'));
    $nAn = odbc_result($cur, 'analysis');
    $nAnT += $nAn;
    $nAm = odbc_result($cur, 'monitor');
    $nAmT += $nAm;
    $txA = odbc_result($cur, 'txAnalize');
    $txM = odbc_result($cur, 'txMonitor');
    $analise = number_format($nAn * $txA, 2, ",", ".");
    $analiseT += $nAn * $txA;
    $monitor = number_format($nAm * $txM / 4, 2, ",", ".");
    $monitorT += $nAm * $txM / 4;
    $total = number_format($nAn * $txA + $nAm * $txM / 4, 2, ",", ".");
    $totalT += $nAn * $txA + $nAm * $txM / 4;
    $idInform = odbc_result($cur, 'idInform');

    ?>
    <tr <?php echo ($count % 2) ? "" : " bgcolor=#e9e9e9"; ?>>
      <td class="texto">
        <a
          href="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/credit/Credit.php?comm=reportImporter&anoMes=<?php echo urlencode($ano . '-' . $mes); ?>&idInform=<?php echo urlencode($idInform); ?>&origem=1&ret=<?php echo urlencode($_REQUEST['ret']); ?>">
          <?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>
        </a>
      </td>

      <!--<td>&nbsp;</td>-->
      <td class="texto" align=center><?php echo $nAn; ?></td>
      <td class="texto" align=center><?php echo $analise; ?></td>
      <td class="texto" align=center><?php echo $nAm; ?></td>
      <td class="texto" align=center><?php echo $monitor; ?></td>
      <td class="texto" align=center><?php echo $total; ?></td>
    </tr>
    <?php $count++;
  }
  if ($count == 0) {
    ?>
    <tr>
      <th colspan=6 align=center class="textoBold" bgcolor="#a4a4a4">Nenhuma Cobrança</th>
    </tr>
  <?php } else {
    ?>
    <tr bgcolor=#cccccc>
      <th class="textoBold">Totais</th>
      <!--<td>&nbsp;</td>-->
      <td class="textoBold" align=center><?php echo $nAnT; ?></td>
      <td class="textoBold" align=center><?php echo number_format($analiseT, 2, ',', '.'); ?></td>
      <td class="textoBold" align=center><?php echo $nAmT; ?></td>
      <td class="textoBold" align=center><?php echo number_format($monitorT, 2, ',', '.'); ?></td>
      <td class="textoBold" align=center><?php echo number_format($totalT, 2, ',', '.'); ?></td>
    </tr>
  <?php }
  ?>
</table>
<?php $day = date('d');
if (
  !$generated && (($mes == $mesAtu && $ano == $anoAtu && $day >= 25)
    || ($mes < $mesAtu && $ano == $anoAtu) || ($ano < $anoAtu))
) {
  ?>
  <form action="<?php echo $root; ?>role/credit/Credit.php" onSubmit="return confirm('Confirmar cobrança?')">
    <input type=hidden name=comm value="setCobr">
    <input type=hidden name=mes value="<?php echo $mes; ?>">
    <input type=hidden name=ano value="<?php echo $ano; ?>">
    <div align=center>
      <p>
        <input type=submit value="Enviar Cobrança" class="servicos">
      </p>
    </div>
  </form>
  <p>
  <?php }
?>