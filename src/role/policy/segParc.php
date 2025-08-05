<?php 
session_start();
// alterado Hicom (Gustavo) - 07/01/05 - data das demais faturas sempre no mesmo dia do mês
   
$ok = false;
$cur = odbc_exec ($db,
		  "SELECT contrat, i_Seg, currency, prodUnit, products, frameOper, ".
		  "frameInadim, frameCom, prMin, txMin, txRise, txAnalize, txMonitor, ".
		  "percCoverage, limPagIndeniz, polRisk, numParc, warantyInterest, Ga,Num_Parcelas,t_Vencimento ".
		  "FROM Inform WHERE id=$idInform");
  

if (odbc_fetch_row($cur)) {
   $valor_total = odbc_result($cur, 9) * (odbc_result ($cur, 18) == 1 ? 1.04 : 1) * (1 + odbc_result($cur, 11));
   
   odbc_autocommit($dbSisSeg,false);
   
   
   $currency = odbc_result($cur, 3);
   //Alterado por Tiago V N - Elumini - 07/04/2006
      if ($currency == "1") {
         $extMoeda = "R$";
      }else if ($currency == "2") {
         $extMoeda = "US$";
      }else if ($currency == "6"){
         $extMoeda = "€";
      }
   $Num_Parcelas = (odbc_result($cur, 'Num_Parcelas') ? odbc_result($cur, 'Num_Parcelas')  : odbc_result($cur, 17));
   
   $parc = (odbc_result($cur, 9) * (odbc_result ($cur, 18) == 1 ? 1.04 : 1) * (1 + odbc_result($cur, 11))) / $Num_Parcelas;
   
   $parc = $parcExt = number_format ($parc, 0, '', '');

   //Alterado por Tiago V N - Elumini - 13/02/2006
   $ga   = odbc_result($cur, 19);
  
      if (($ga=="0") || ($ga=="")){
         $susep = "15.414005212/2005-89";
         $cp    = "CP/RC/06-01";
      }else{
         $susep = "15.414004768/2004-08";
         $cp    = "CP/GA/07-01";
      }
  
   // alterado Hicom
   
   
   
   $hc_ano = substr($d_venc,0,4);
   $hc_mes = substr($d_venc,5,2);
   $hc_dia = substr($d_venc,8,2);
   $hc_venc = mkdate($hc_ano,$hc_mes + $periodo * ($parcela - 1),$hc_dia);
   // fim
  
  //die($hc_venc .' <br>'.  odbc_result($cur, 2) );
   $r = 1;
       
   $x = 2; 
	  // lançar a quantidade de parcelas no sisseg
	//acertando para o valor ficar extato
	$valPar  = number_format($valPar,2,',','.');
	$valPar  = str_replace('.','',$valPar);
	$valPar  = str_replace(',','.',$valPar);
										  
	
	
	  if(!$ja_foi){
		 $ja_foi = 0;
	  }
	 	
	   $ja_foi = 0;
	   
	  odbc_autocommit($dbSisSeg,true);
	  
	  
	   
	   $rrs = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice");
	   if(odbc_fetch_row($rrs)){
			$max = odbc_result($rrs, 1);
			
	   }
	    
		$qrys = " SELECT contrat FROM Inform WHERE id = ".$idInform."";
        $curs = odbc_exec ($db,$qrys);

        if (odbc_fetch_row($cur)) {
	 	    $c_coface       = odbc_result($cur, 1);
	    }
	  
	   $rss = odbc_exec($dbSisSeg, "select max(i_BC) from Base_Calculo where c_Coface=$c_coface");
	   if(odbc_fetch_row($rss)){
			$idBC = odbc_result($rss, 1);
	   }
	   
	  for($x = 2; $x <= $Num_Parcelas; $x++){
		    $parcela = $x;
		  
		    $hc_ano = substr($d_venc,0,4);
		    $hc_mes = substr($d_venc,5,2);
		    $hc_dia = substr($d_venc,8,2);
		    $hc_venc = mkdate($hc_ano,$hc_mes + $periodo * ($parcela - 1),$hc_dia);
										
		   if($ja_foi == 0){
			   $query = "insert into Parcela (c_Coface, i_Seg, n_Prop, v_Parcela, d_Venc, n_Seq_Parcela, d_Parcela, v_Extenso, n_Ramo, n_Sucursal, t_parcela, n_Moeda, s_Parcela, v_IOF) values (".
			   			 odbc_result($cur, 1). ",".odbc_result($cur, 2). ",". $nProp. ",". $valPar.",'". $hc_venc."',". $parcela.",'". date('Y-m-d')."','". $numberExtensive->extensive($valPar, $fMoeda). "',
						 49, 62, 2, '".$moeda."', 1, 0)";
			   
			   //print ($query.'<br>');
			   $r = odbc_exec($dbSisSeg, $query);
			   
			   //Moeda --
		   }else {
			   $r = 1;
		   }

		   if ($r) {
			  $cur2 = odbc_exec ($dbSisSeg, "SELECT max(i_Parcela) FROM Parcela WHERE i_Seg=$idSeg");
			 
			 
			  $idParcela = 0;
					if (odbc_fetch_row ($cur2)){
					    $idParcela = odbc_result($cur2, 1);
					}
					//Moeda --
					if($ja_foi == 0){
					   $query =
						   "INSERT into PagRec (i_Seg, i_Parcela, n_Seq_Parcela, n_Sucursal,".
						   "n_Ramo, c_Coface, n_Prop, d_Vencimento, v_Documento, n_Moeda, t_Doc, s_Pagamento,".
						   "d_Situacao, d_Sistema, i_BC, n_Apolice, n_Endosso, v_IOF) VALUES (".
						   odbc_result($cur, 2). ",".                              // -- i_Seg
						   "$idParcela,".                                          // -- Código da Parcela
						   "$parcela,".                                            // -- Número de seqüência da parcela
						   "62, 49,".                                              // -- Sucursal e Ramo
						   odbc_result($cur, 1). ",".                              // -- c_Coface
						   "$nProp,".                                              // -- n_Prop
						   //	"getdate() + ". ($periodo * ($parcela - 1) * 30). ",".  // -- Data de vencimento *
						   "'$hc_venc',".  // d_Venc
						   "$valPar,".                                               // -- Valor da Parcela
						   "'$moeda', 2, 1,".                                             // -- Moeda, Tipo de Documento (PRPT - Proposta)   e Situação do Pagamento (A Receber)
						   "getdate(),".                                           // -- Data em a situação foi alterada
						   "getdate(),".                                           // -- Data em que a parcela foi cadastrada
						   "$idBC,".                                               // -- id da Base de Calculo
						   "$max,".                                                // -- numero da apolice
						   "0,
						    0)";                                                // -- endosso e v_IOF
						   $r = odbc_exec ($dbSisSeg, $query);
					   //echo "<br>pagrec:  $query<br><br>";
		
					   if($r){
						  $query =
							  "insert into Parcela_Resseguro (i_Parcela, v_Resseguro, v_Com_Resseguro, s_PR, c_Seg, d_Situacao) 
							  values (". "$idParcela,".         // i_Parcela
									  ($valPar * ($PerRess/100)). ",".    // 80% do valor da parcela
									  ($valPar * ($PerComRess/100)). ",".   // 24% do valor da parcela
							 			 "0,".                  // Colocar 1 para a 1a parcela e 0 para as outras
									   ($c_Seguro ? $c_Seguro : ($_SESSION['c_Seguro'] ? $_SESSION['c_Seguro'] : 1)).", 
									   getdate())";
							
							  $r = odbc_exec($dbSisSeg, $query);
						     
						       //echo "<br>parcela resseguro:  $query<br><br>";
					   }else
						   $erroq .= "Erro ".($errorx +=1)." Problemas na inserção na tabela Parcela_Resseguro (SisSeg)<br><br>".$query."<br><br>";
					}else{
					    $r = 1;
					}
					
					if ($r) {
					   odbc_commit($dbSisSeg);
					   $ok = true;
					} else{
					   $erroq .= "Erro ".($errorx +=1)." Problemas na inserção na tabela PagRec na base do SisSeg<br><br>".$query."<br><br>";
					}
			   //die($idParcela);		
		   } else{
               $erroq .= "Erro ".($errorx +=1)." Problemas na inserção da parcela na base do SisSeg<br><br>". $query ."<br><br>";
	       }  
		   
		   
	  }
	 
	  $_SESSION['c_Seguro'] = ''; 
	 // die('?'. $ok.'?'.$ja_foi);
	  
	  
	  
}

if (!$ok){
   odbc_rollback ($dbSisSeg);
}

odbc_autocommit($dbSisSeg,true);




?>
