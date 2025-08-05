<?php  $query = "SELECT Importer.przPag, Importer.periodicity, Importer.name,
	    Importer.c_Coface_Imp, Importer.limCredit, Country.code,
	    ChangeCredit.credit, MAX(ChangeCredit.stateDate) as stateDate,
	    Country.name, Importer.id, ChangeCredit.state
	  FROM ChangeCredit, Importer, Country, Inform
	  WHERE ChangeCredit.idImporter = Importer.id AND
	    Importer.idCountry = Country.id AND
	    (ChangeCredit.state = 4 OR
	    ChangeCredit.state = 5 OR
	    ChangeCredit.state = 6 OR
	    ChangeCredit.state = 7) AND
            Importer.idInform = Inform.id AND
	    Importer.id = $idBuyer
	  GROUP BY Importer.przPag, Importer.periodicity, Importer.name,
	    Importer.c_Coface_Imp, Importer.limCredit, Country.code,
	    ChangeCredit.credit, Country.name, Importer.id, ChangeCredit.state
          ORDER BY stateDate desc";
$cur = odbc_exec($db,$query);

$q = "SELECT Inform.contrat, Inform.name, Inform.state
	FROM Inform, Importer
	WHERE Inform.id=Importer.idInform AND
	Importer.id=$idBuyer";
$curInform = odbc_exec($db,$q);
// if(odbc_fetch_row($curInform)){
//   $informContrat	= odbc_result($curInform, 1);
//   $informName	    	= odbc_result($curInform, 2);
//   $informState		= odbc_result($curInform, 3);
//   //$informSitCoface	= odbc_result($curInform, 4);
// }

$query = "SELECT monitor, analysis, credit FROM ChangeCredit WHERE id=
          (select max(id) from ChangeCredit where idImporter=$idBuyer)";
$cursor = odbc_exec($db, $query);

if(odbc_fetch_row($cursor)) {
  $sit_monitoramento = odbc_result($cursor, 1);
  $sit_analise = odbc_result($cursor, 2);
  $cred_ant = odbc_result($cursor, 3);
  if($cred_ant == 0 && $credit != 0)
    $decision_date = date("d/m/Y");
}

if ($sit_analise == 1 && $sit_monitoramento == 1) {
  $sitFat = "Faturar Análise e Monitoramento";
  $invoice = 4;
} else if ($sit_analise == 1) {
  $sitFat = "Faturar Análise";
  $invoice = 3;
} else if ($sit_monitoramento == 1){
  $sitFat = "Faturar Monitoramento";
  $invoice = 2;
} else {
  $sitFat = "Não faturar";
  $invoice = 1;
}

?>
