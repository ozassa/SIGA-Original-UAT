<?php 


$ok = false;
$qry = " SELECT contrat, i_Seg, currency, prodUnit, products, frameOper, frameInadim,".
  "        frameCom, prMin, txMin, txRise, txAnalize, txMonitor, percCoverage, limPagIndeniz, polRisk,".
  "        numParc, idAnt,Num_Parcelas,t_Vencimento, nProp FROM Inform WHERE id = $idInform";
$cur = odbc_exec ($db,$qry);




  if (odbc_fetch_row($cur)) {
	  $idSeg = odbc_result($cur, 2);
	  $idAnt = odbc_result($cur, 'idAnt');
		  if(!$idSeg){
			$idSeg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id='".$idAnt."'"), 1);
		  }
	  $c_coface       = odbc_result($cur, 1);
	  $numParc        = odbc_result($cur, 17);
	  $Num_Parcelas	  = (odbc_result($cur, 'Num_Parcelas') ? odbc_result($cur, 'Num_Parcelas') : $numParc);
	  $t_Venc         = odbc_result($cur, 't_Vencimento');
	  $nProp 		  = odbc_result($cur, "nProp");
	  
		  if($t_Venc == 1){
			  $t_Vencimento  = 'mensais';
		  }else if ($t_Venc == 2){
			  $t_Vencimento  = 'bimestrais';
		  }else if ($t_Venc == 3){
			  $t_Vencimento  = 'trimestrais';
		  }else if ($t_Venc == 4){
			  $t_Vencimento  = 'semestrais';
		  }
	  //$propCur = odbc_exec ($dbSisSeg,"SELECT max (n_Prop) FROM Proposta WHERE i_Seg = '".$idSeg."'");
		
		 //  if (odbc_fetch_row($propCur)) {
			// $nProp = odbc_result($propCur, 1);
			// 	if (!$nProp){ 
			// 	   $nProp=0;
			// 	}
		 //  }     

  		$r = 1;
     
	  
		if ($r){
			$parc = (odbc_result($cur, 9) * (1 + odbc_result($cur, 11)) / $Num_Parcelas);
			$parcExt = number_format ($parc, 2, '.', '');
			$r = 1;
		 
				if ($r) {
				  $cur2 = odbc_exec ($dbSisSeg, "SELECT max(i_Parcela) FROM Parcela WHERE i_Seg = '".$idSeg."'");
				  $idParcela = 0;
					  if (odbc_fetch_row ($cur2)){ 
						  $idParcela = odbc_result($cur2, 1);
					  }
				  
				   $r = 1;
			
					  if ($r) {
						  //odbc_commit($dbSisSeg);
						  $ok = true;
					  }else{ 
						  $msg1 = "Problemas na inserção na tabela PagRec na base do SisSeg";
					  }
				 }else{ 
					 $msg1 = "Problemas na inserção da parcela na base do SisSeg";
				 }
		}else{ 
			   $msg1 = "Problemas na inserção da proposta na base do SisSeg";
		}
		
		// die($msg);
   }	   
//die();

?>
