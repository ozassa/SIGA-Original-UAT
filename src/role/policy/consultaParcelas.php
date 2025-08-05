<?php 

  $sqlParc = "SELECT P.i_Parcela As IdParcela, P.n_Parcela As NumParcela, P.d_Vencimento As DataVencimento, P.v_Parcela As ValorParcela 
				FROM Parcela P 
				WHERE P.i_Inform = ".$idInform." AND P.t_Parcela = 100	AND P.n_Endosso = 0 
				ORDER BY P.n_Parcela";
	$resultParc = odbc_exec($db, $sqlParc);
	
	$dadosParc = array();
  while(odbc_fetch_row($resultParc)){
  	$dadosParc[] = array("idPar" => odbc_result($resultParc,'IdParcela'), "valPar" => odbc_result($resultParc,'ValorParcela'), "parcela" => odbc_result($resultParc,'NumParcela'), 
  										"vencimento" => date("d/m/Y", strtotime(odbc_result($resultParc, 'DataVencimento'))));
	}
	$numResultParc = count($dadosParc);

?>