<?php

/*$idInform = $_REQUEST["idInform"];

$sqry = "SELECT DISTINCT 
	Importer.name, 
	Right('000000' + Rtrim(Importer.c_Coface_Imp), 6), 
	Country.code, 
	Importer.stateDate, 
	Country.name, 
	Importer.id, 
	Importer.idAprov, 
	Importer.Easy_Number,
	CC.id AS IDCredit,
	CC.credit As Credit,
	CC.stateDate As StateDate,
	Case ISNull(CC.creditSolic, 0) When 0 Then Importer.limCredit Else CC.creditSolic End As CreditSolic, 
	CC.creditTemp As CreditTemp, 
	CC.limTemp As LimTemp,
	CC.Motivo_Decisao_1, 
	CC.Motivo_Decisao_2, 
	CC.Motivo_Decisao_3
FROM Importer 
Inner Join Country On
	Country.id = Importer.idCountry
Left Join ChangeCredit CC On
	CC.id = (Select Top 1 CCC.id From ChangeCredit CCC Where CCC.idImporter = Importer.id Order By CCC.stateDate Desc)
WHERE 
	Importer.idInform = $idInform
	AND (Importer.state In (3, 4) Or (Importer.state In (2)))
Order by Importer.name";
	
$Importers = odbc_exec ($db,$sqry);*/


//print $sqry;
?>