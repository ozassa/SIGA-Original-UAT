<?php  //Alterado HiCom mes 04

$ok = false;
$cur = odbc_exec ($db,
	" SELECT contrat, i_Seg, currency, prodUnit, products, frameOper, frameInadim,".
  	"        frameCom, prMin, txMin, txRise, txAnalize, txMonitor, percCoverage, limPagIndeniz, polRisk,".
  	"        numParc, warantyInterest, prMTotal,txMTotal,Num_Parcelas,t_Vencimento".
  	" FROM Inform ".
  	" WHERE id = $idInform"
  	);

if (odbc_fetch_row($cur)) {
  	$idSeg 		= odbc_result($cur, 2);
  	$waranty 	= odbc_result($cur, 'warantyInterest');
  	$valPar  	= odbc_result($cur,'prMTotal') / odbc_result($cur,'Num_Parcelas');
  	$valPar  	= number_format($valPar,2,',','.');
  	$valPar  	= str_replace('.','',$valPar);
  	$valPar  	= str_replace(',','.',$valPar);
 
  	if(! $idSeg){
		$idSeg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=(select idAnt from Inform where id=$idInform)"), 1);
  	}
  	
  	$propCur = odbc_exec($db, "Select IsNull(Max(nProp), 0) AS n_Prop From Inform Inf Where contrat = (select contrat From Inform Where id = $idInform) And Inf.id < $idInform");

  	$nProp = 0;
  	
  	$d_Pagamento = '';
  	
  	if (odbc_fetch_row($propCur)) {
  		$nProp = odbc_result($propCur, 'n_Prop');
  	}

  	$nProp++;
  	$r = 1;
  	$rp = odbc_result($cur, 16);
  
  	if ($rp == '')
      		$rp = 0;

	  	$query =
			" INSERT INTO Proposta".
			"   (".
			" c_Coface,".
			" n_Prop,".
			" i_Seg,".
			" n_Moeda,".
			" n_Sucursal,".
			" n_Ramo,".
			" Nat_Oper,".
			" n_Prazo_Oper,".
			" n_Prazo_Inad,".
			" n_Prazo_Com,".
			" v_PrMin,".
			" t_Pr,".
			" v_Un_AnCad,".
			" v_Un_Monitor,".
			" p_Cobertura,".
			" n_Coef_VMI,".
			" x_Risco_Politico,".
			" d_Proposta,".
			" d_deAcordo,".
			" d_Aceitacao,".
			" d_Situacao,".
			" s_Proposta,".
			" n_User,".
			" d_Sistema,".
			" Renova,".
			" n_Apolice_Renova,".
			" n_Parcelas,".
			" n_Mod,".
			" v_Frac,".
			" n_Filial ".
			"   )".
			" VALUES".
			"   (".
			" ".odbc_result($cur, 1).",".                                // -- c_Coface
			"     ".$nProp.",".                                              // -- n_Prop
			"     ".$idSeg.",".                                              // -- i_Seg
			"     ".odbc_result($cur, 3).",".                                // -- n_Moeda
			"     62,".                                                      // -- n_Sucursal
			"     49,".                                                      // -- n_Ramo
			"     '".odbc_result($cur, 5)."',".                              // -- n_Nat_Oper
			"     ".odbc_result($cur, 6).",".                                // -- n_Prazo_Oper
			"     ".odbc_result($cur, 7).",".                                // -- n_Prazo_Inad
			"     ".odbc_result($cur, 8).",".                                // -- n_Prazo_Com
	   		// "     ".(odbc_result($cur, 9) * ($waranty ? 1.04 : 1) * (1 + odbc_result($cur, 11))).",".  // -- v_PrMin,
			"     ".(odbc_result($cur, 19)).",".                              // -- v_PrMin, igual ao campo prMTotal do inform
	  		 // "     ".(odbc_result($cur, 10) * 100 * (1 + odbc_result($cur, 11))).",". // -- t_Pr,
			"     ".(odbc_result($cur, 20)).",".                             // -- t_Pr, igual ao campo txMTotal do inform
			"     ".odbc_result($cur, 12).",".                               // -- v_Un_AnCad,
			"     ".odbc_result($cur, 13).",".                               // -- v_Un_Monitor,
			"     ".odbc_result($cur, 14).",".                               // -- p_Cobertura,
			//    "     ".odbc_result($cur, 15).",".                               // -- n_Coef_VMI,
			"     30,".                               // -- n_Coef_VMI,
			"     ".$rp.",".                                                 // -- x_Risco_Politico,
			"     getdate(),".                                               // -- d_Proposta (Data da proposta)
			"     getdate() + 1,".                  // -- @d_deAcordo
			"     getdate() + 1,".                  // -- @d_Aceitacao
			"     getdate(),".                                               // -- d_Situacao(Data da situação)
			"     8,".                                                       // -- s_Proposta (Situação 8 = 1a parcela gerada)
			"     66,".                                                      // -- n_User
			"     getdate(),".                                               // -- d_Sistema
			"     0,".                                                       // -- Renova
			"     '',".                                                      // -- n_Apolice_Renova
			"     ".odbc_result($cur, 'Num_Parcelas').",".                               // -- n_Parcelas(número de parcelas)
			"     0,".                                                       // -- n_Mod(Modalidade),
			"     0,".                                                       // -- v_Frac(Adic. Fracionamento)
			"     ".odbc_result($cur, 4).                                    // -- n_Filial(Número da Filial)
			"   )";
	  		//odbc_autocommit($dbSisSeg,false);
	  		//$r = odbc_exec ($dbSisSeg, $query);
	  		$sisSegQueries[] = $query; // guarda a query num vetor para executar depois
	 
 		if ($r) {
    			//$parc = (odbc_result($cur, 9) * ($waranty ? 1.04 : 1) * (1 + odbc_result($cur, 11))) / odbc_result($cur, 17);
    			$parc = $valPar;
	
			//die($parc);
    			$parcExt = $valPar;
    			$query =
      " INSERT INTO Parcela".
      "   ( ".
      "     d_Venc,".
      "     n_Sucursal,".
      "     n_Ramo,".
      "     c_Coface,".
      "     n_Prop,".
      "     i_Seg,".
      "     d_Parcela,".
      "     n_Seq_Parcela,".
      "     t_parcela,".
      "     v_Parcela,".
      "     n_Moeda,".
      "     s_Parcela,".
      "     v_Frac,".
      "     v_Extenso".
      "   )".
      " VALUES".
      "   (".
      "     '".$fiveX."',".                                        // -- Data de vencimento *
      "     62,".                                                    // -- Sucursal
      "     49,".                                                    // -- Ramo
      "     ".odbc_result($cur, 1).",".                              // -- c_Coface
      "     $nProp,".                                                // -- n_Prop
      "     ".$idSeg.",".                                            // -- i_Seg
      "     getdate(),".                                             // -- data de criação da parcela
      "     1,".                                                     // -- número de sequencia da parcela*
      "     1,".                                                     // -- t_Parcela - tipo de parcela *
      "     $parc,".                                                 // -- Valor da Parcela
      "     ".odbc_result($cur, 3).",".                              // -- Moeda,
      "     1,".                                                     // -- Situação da Parcela *
      "     0,".                                                     // -- Adicional de Fracionamento
      "     '".$numberExtensive->extensive($parcExt,$currency)."'".         // -- Valor por Extenso
      "   )";
    //$r = odbc_exec ($dbSisSeg, $query);
//    echo "<pre>$query</pre>";
    $sisSegQueries[] = $query; // guarda a query num vetor para executar depois
    if ($r) {

     	 $query =
		" INSERT into PagRec".
		"   (".
		"     i_Seg,".
		"     i_Parcela,".
		"     n_Seq_Parcela,".
		"     n_Sucursal,".
		"     n_Ramo,".
		"     c_Coface,".
		"     n_Prop,".
		"     d_Vencimento,".
		"     v_Documento,".
		"     n_Moeda,".
		"     t_Doc,".
		"     s_Pagamento,".
		"     d_Situacao,".
			"     v_Frac,".
		"     d_Sistema".
		"   )".
		" VALUES".
		"   (".
		"     ".$idSeg.",".                                          // -- i_Seg
		"     idParcelaSubs,".                                       // -- Código da Parcela (será substituido depois)
		"     1,".                                                   // -- Número de seqüência da parcela
		"     62,".                                                  // -- Sucursal
		"     49,".                                                  // -- Ramo
		"     ".odbc_result($cur, 1).",".                            // -- c_Coface
		"     $nProp,".                                              // -- n_Prop
	/*	"     getdate() + 15,".                                      // -- Data de vencimento */
		"     '$fiveX',".
		"     $parc,".                                               // -- Valor da Parcela
		"     ".odbc_result($cur, 3).",".                            // -- Moeda,
		"     1,".                                                   // -- Tipo de Documento (PRPT - Proposta)
		"     1,".                                                   // -- Situação do Pagamento - A Receber
		"     getdate(),".                                           // -- Data em a situação foi alterada
			"     0,".                                                   // -- Adicional de Fracionamento
		"     getdate()".                                            // -- Data em que a parcela foi cadastrada
		"   )";
      //$r = odbc_exec ($dbSisSeg, $query);
      $sisSegQueries[] = $query; // guarda a query num vetor para executar depois
      /* if ($r) { */
/*             odbc_commit($dbSisSeg); */
/* 	    $ok = true; */
/*           } else $msg = "problemas na inserção na tabela PagRec na base do SisSeg"; */
    } else { 
	   $msg = "problemas na inserção da parcela na base do SisSeg";
	}
	  //parcela resseguo
	  if($r){
		     
			   $query = "SELECT startValidity, endValidity FROM Volume JOIN Inform ON (idInform = Inform.id) WHERE idInform = ". $idInform;
			   $cury = odbc_exec($db, $query);
			   $ValidaData = odbc_result($cury, "startValidity"); // Valor será utilizado na consulta em Par_Resseguro adiante.
				
				 // $csql = "select * from Par_Resseguro where (".substr(str_replace('-','',$ValidaData),0,8)." between n_Per_Inicio and n_Per_Fim)";
				 // $retor = odbc_exec($dbSisSeg, $csql);
				 
				 $c_Seguro = 10;
				 $PerRess = 10;
				 $PerComRess = 10;
		  
		  $query =
			  "insert into Parcela_Resseguro (i_Parcela, v_Resseguro, v_Com_Resseguro, s_PR, c_Seg, d_Situacao)
			   values (idParcela,".         // i_Parcela
			   $parc * (($PerRess/100) ? ($PerRess/100) : 1) . ",".    // 80% do valor da parcela
			   $parc * (($PerComRess/100) ? ($PerComRess/100) : 1) . ",".   // 24% do valor da parcela
			   "1,".                  // Colocar 1 para a 1a parcela e 0 para as outras
			  ($c_Seguro ? $c_Seguro : 1).", getdate())";			  
		      $sisSegQueries[] = $query;
		 
	   }else{
		   $msg = "problemas na inserção na tabela Parcela_Resseguro (SisSeg)";
	   }
	
  } else $msg = "problemas na inserção da proposta na base do SisSeg";
  
}
//if (!$ok) odbc_rollback ($dbSisSeg);
//odbc_autocommit($dbSisSeg,true);
?>
