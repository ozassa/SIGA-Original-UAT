<?php  
//$idInform  = $_REQUEST['idInform'];

//extract($_POST);
//extract($_GET);



if(!isset($alter)){
	$alter = isset($_REQUEST['alter']) ? $_REQUEST['alter'] : '';
}

$q = "select contrat, name, state, currency, periodMaxCred FROM Inform WHERE Inform.id=$idInform";
$c = odbc_exec($db,$q);
 
if(odbc_fetch_row($c)) {
	$contrat 	= odbc_result($c, 1);
	$nameCl  	= odbc_result($c, 2);
	$inform_state 	= odbc_result($c, 3);
	$moeda        	= odbc_result($c, 4);
	$PeriodoMaxCred	= odbc_result($c, 5);

	if ($moeda == "1"){
		$ext = "R$";
	}else if ($moeda == "2"){
		$ext = "US$";
	}else if ($moeda == "6") {
		$ext = "€";
	}  
}
 
 



?>