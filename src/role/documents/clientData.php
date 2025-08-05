<?php 
    
    include_once("../consultaCoface.php");
    include_once("../../../gerar_pdf/MPDF45/mpdf.php");

	$q = "SELECT contrat, name, state, currency, periodMaxCred, policyKey
			FROM Inform 
			WHERE Inform.id = ".$idInform;
	$c = odbc_exec($db,$q);
 
	if(odbc_fetch_row($c)) {
		$contrat 	= odbc_result($c, 1);
		$nameCl  	= odbc_result($c, 2);
		$inform_state 	= odbc_result($c, 3);
		$moeda        	= odbc_result($c, 4);
		$PeriodoMaxCred	= odbc_result($c, 5);
		$key	= odbc_result($c, 6);

		if ($moeda == "1"){
			$ext = "R$";
		}else if ($moeda == "2"){
			$ext = "US$";
		}else if ($moeda == "6") {
			$ext = "€";
		}  
	}

?>