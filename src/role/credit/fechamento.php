<?php  // alterado Hicom (Gustavo) - 04/01/05 - campo limCredit não existia na primeira query 
// e dava erro no arquivo ReportImporter.php

// verifica se ja teve fechamento do mes
// Acesso à nova estrutura de análise e monitoramento
$cur = odbc_exec($db, "SELECT id FROM resFatAnaliseMonitor WHERE mes=$mes AND ano=$ano");
$generated = odbc_fetch_row($cur);

$date = date("Y-m-d", mktime(0, 0, 0, $mes + 1, 1, $ano));

if($generated){ // se ja fechou o mes
  // Acesso à nova estrutura de análise e monitoramento
  $query = 
    "
SELECT imp.name AS impName, c.name AS countryName, ir.creditoSolicitado AS creditSolic, cc.creditTemp, ir.creditoConcedido AS credit, cc.limTemp,
       er.txAnalise AS txAnalyse, er.txMonitor AS txMonitor, ir.txMonitor AS monitor, ir.txAnalise AS analyse, imp.c_Coface_Imp, cc.creditDate AS data_estudo, 
       0 as limCredit, cc.state, cc.stateDate, DATEDIFF(d, cc.stateDate, getDate()) as qtdstateDate,
     imp.state as Impstate
FROM resFatAnaliseMonitor er
  JOIN resFatAnaliseMonitorImport ir ON ir.IdResFat = er.id
  JOIN Importer imp ON ir.idImporter = imp.id
  JOIN Country  c ON imp.idCountry = c.id
  JOIN ChangeCredit cc ON cc.idImporter = imp.id
WHERE er.idInform = $idInform AND (ir.txMonitor <> 0 OR ir.txAnalise <> 0) AND er.mes = $mes AND er.ano = $ano
  AND cc.id IN (
    SELECT MAX (id) FROM ChangeCredit WHERE creditDate < '$date' GROUP BY idImporter
  )
ORDER BY imp.name
";


//echo "<pre>$query</pre>";

}else{ // nao fechou o mes
  $query = 
     "
SELECT imp.name AS impName, c.name AS countryName,  CASE imp.limCredit WHEN NULL THEN 0 ELSE imp.limCredit END AS limCredit , cc.creditTemp, cc.credit, cc.limTemp,
       inf.txAnalize AS txAnalyse, inf.txMonitor, cc.monitor, cc.analysis AS analyse, rtrim(ltrim(imp.c_Coface_Imp)) as c_Coface_Imp , cc.creditDate AS data_estudo,
     imp.id as idbuyer, cc.state, cc.stateDate, DATEDIFF(d, cc.stateDate, '2007-08-31') as qtdstateDate,
     imp.state as Impstate
FROM Inform inf
  JOIN Importer imp ON imp.idInform = inf.id
  JOIN Country c ON imp.idCountry = c.id
  JOIN ChangeCredit cc ON cc.idImporter = imp.id
WHERE inf.id = $idInform AND (cc.monitor <> 0 OR cc.analysis <> 0)
  AND cc.id IN (
    SELECT MAX (id)
    FROM ChangeCredit
    WHERE (stateDate <= '$date')
      AND (state = 2 OR state = 4 OR state = 5 OR state = 6 OR state = 7)
    GROUP BY idImporter
  )
    AND (imp.c_Coface_Imp IS NOT NULL) AND (RTRIM(LTRIM(imp.c_Coface_Imp)) 
                      <> '0') AND (RTRIM(LTRIM(imp.c_Coface_Imp)) <> '')
ORDER BY imp.name 
";
//echo "<pre>$query</pre>";
//die();
}
// echo "<pre>$query</pre>";

?>
