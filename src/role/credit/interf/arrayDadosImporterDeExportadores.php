<?php 
//esta parte abaixo serve para poder trazer todos os dados necessários dos importadores pertencentes a um determinado exportador
foreach($arrTodosImportadores as $key=>$valor)
	{	
		//cria os arrays abaixo com os dados do exportador
		$statusLocal     = explode(",",$valor['STATE']);
		$informLocal     = explode(",",$valor['INFORMS']);
		$fimApoliceLocal = explode(",",$valor['FIMDATEAPOLICE']);
		
		//print 'oi';
		//imprime na tela para sabernos os valores, nesta área serve apenas para isto
		/* echo $valor['EXPORTADOR']."<br>";
		echo $valor['TAXASAM']."<br>";
		echo $valor['INFORMS']."<br>";
		echo $valor['STATE']."<br>"; */
		
		//verifica o total de status dentro da string para realizar outras verificações, e para isto cria mais 2 arrays abaixo
		$arrInform = explode(",",$valor['INFORMS']);
		$arrStatus = explode(",",$valor['STATE']);
		$qtdStatus = count($arrStatus);
		
		//cria variaveis para armazenas os informes e status separadamente para depois ser usado em querys futuras
		$status1   = null;
		$status2   = null;
		$unStatus  = null;
		$umInform1 = null;
		$umInform2 = null;

      

		//verifica se tem 2 status, caso tenha ,armazena  em duas variaveis estes valors para serem usados posteriormente
		if($qtdStatus >= 2)
		{
			$status1   = $arrStatus[0];
			$status2   = $arrStatus[1];
			$umInform1 = $arrInform[0];
			$umInform2 = $arrInform[1];
		}else	{//caso contrario armazena o unico valor no resultado
			$unStatus  = $valor['STATE'];
			$umInform1 = $valor['INFORMS'];         
		}
     

		/* 
		ESTA AREA ABAIXO IRÁ PEGAR TODOS OS IMPORTADORES DE CADA EXPORTADOR E IRÁ GUGARDAR O SEUS DADOS 
		PARA SER USADO DEPOIS PARA CALCULAR AS ANALISES E MONITORAMENTO
		 */
		if($status1==10 && $status2==11)
		{
			
			$consUmImpor="select imp.name,imp.state,imp.stateDate,imp.credit,imp.creditTemp,c.name,imp.id,imp.limCredit,imp.dt_cob_analise,inf.startValidity,inf.i_Seg,inf.contrat,inf.nProp,imp.c_Coface_Imp 
					from Inform as inf join Importer as imp on inf.id = imp.idInform join Country as c on imp.idCountry = c.id 
					where inf.id =$umInform1 and inf.state = $status1 and (imp.credit <> 0 or imp.creditTemp<>0)
					and (imp.state=6 or (imp.state=7 and (imp.dt_cob_analise is null)))
					
					UNION
					
					select imp.name,imp.state,imp.stateDate,imp.credit,imp.creditTemp,c.name,imp.id,imp.limCredit,imp.dt_cob_analise,inf.startValidity,inf.i_Seg,inf.contrat,inf.nProp,imp.c_Coface_Imp 
					from Inform as inf join Importer as imp on inf.id = imp.idInform 
					join Country as c on imp.idCountry = c.id 
					where inf.id =$umInform1 and inf.state = $status1 
					and (imp.state=6 and imp.credit = 0 and imp.creditTemp=0 and imp.dt_cob_analise is null and imp.stateDate>'$dtFimUltimoTrimestre')
					order by imp.name";								
			$exec1 = odbc_exec($db, $consUmImpor);
			
			//este while vai percorrer os importadores e depois vai popular o array de importadores
			while(odbc_fetch_row ($exec1))
			{
				//armazena nas variaveis os respectivos campos do formulário
				$umImpName       = odbc_result ($exec1,1);
				$umImpState      = odbc_result ($exec1,2);
				$umImpStateDate  = odbc_result ($exec1,3);
				$umImpCredit     = odbc_result ($exec1,4);
				$umImpCreditTemp = odbc_result ($exec1,5);
				$umImpCobAna     = odbc_result ($exec1,9);
				$umImpId         = odbc_result ($exec1,7);
				$limCredit       = odbc_result ($exec1,8);
				
				$startValidity   = odbc_result ($exec1,10);
				$i_Seg           = odbc_result ($exec1,11);
				$contrat         = odbc_result ($exec1,12);
				$nProp           = odbc_result ($exec1,13);
				$c_Coface_Imp    = odbc_result ($exec1,14);
				
				//verifica os importadores do status vigente que é 10 e depois verifica se os importadores do vigente estão no status encerrado e atualiza  dados
				$consUmImporEnce="select imp.name,imp.state,imp.stateDate,imp.credit,imp.creditTemp,c.name,imp.id,imp.limCredit,imp.dt_cob_analise,imp.c_Coface_Imp 
					from Inform as inf join Importer as imp on inf.id = imp.idInform join Country as c on imp.idCountry = c.id 
					where inf.id =$umInform2 and inf.state = $status2 and imp.name like '$umImpName%'";								
				$exec3 = odbc_exec($db, $consUmImporEnce);
				
					$umImpNameEnc       = odbc_result ($exec3,1);
					$umImpStateDateEnc  = odbc_result ($exec3,3);
					$umImpCreditEnc     = odbc_result ($exec3,4);
					$c_Coface_Imp2    	= odbc_result ($exec3,9);
					
				$inativaEcobraAnalise = explode(";",inativaComCobAnalise($db,$umImpId));	
				/*  if($umImpNameEnc == $umImpName)
				{ */
				 if(($umImpNameEnc == $umImpName) || ($c_Coface_Imp == $c_Coface_Imp2))
				 {
					
					if(($umImpState==7 && $inativaEcobraAnalise[0]==1) || $umImpStateDate>$dtFimUltimoTrimestre)
					{
						array_push($valor['IMPORTADORES'],$umImpName.";".$umImpState.";".$umImpStateDate.";".$umImpCredit.";".$umImpCreditTemp.";".$umImpStateDateEnc.";".$umImpCreditEnc.";ImpAnt;".$umImpCobAna.";".$umImpId.";".$inativaEcobraAnalise[1].";".$startValidity.";".$i_Seg.";".$contrat.";".$nProp.";".$limCredit);				
					}
					else
					{
						array_push($valor['IMPORTADORES'],$umImpName.";".$umImpState.";".$umImpStateDate.";".$umImpCredit.";".$umImpCreditTemp.";".$umImpStateDateEnc.";".$umImpCreditEnc.";ImpAnt;".$umImpCobAna.";".$umImpId.";".$inativaEcobraAnalise[1].";".$startValidity.";".$i_Seg.";".$contrat.";".$nProp.";".$limCredit);
					}
					
				}else
				{
					if(($umImpState==7 && $inativaEcobraAnalise[0]==1) || $umImpStateDate>$dtFimUltimoTrimestre)
					{
						array_push($valor['IMPORTADORES'],$umImpName.";".$umImpState.";".$umImpStateDate.";".$umImpCredit.";".$umImpCreditTemp.";".$umImpCobAna.";".$umImpId.";".$inativaEcobraAnalise[1].";".$startValidity.";".$i_Seg.";".$contrat.";".$nProp.";".$limCredit);
					}
					else
					{
						array_push($valor['IMPORTADORES'],$umImpName.";".$umImpState.";".$umImpStateDate.";".$umImpCredit.";".$umImpCreditTemp.";".$umImpCobAna.";".$umImpId.";".$inativaEcobraAnalise[1].";".$startValidity.";".$i_Seg.";".$contrat.";".$nProp.";".$limCredit);
					}
				} 				
			}//FIM while
			
			//imprimindo o array de importadores de um determinado exportador
/* 			foreach($valor['IMPORTADORES'] as $valorImp)
			 {
				echo $valorImp."<br>";
			 }  */  		
			
		}
		else if($status1==10 && $status2=="")
		{
				
			$consUmImpor10="select imp.name,imp.state,imp.stateDate,imp.credit,imp.creditTemp,c.name,imp.id,imp.limCredit,imp.dt_cob_analise,inf.startValidity,inf.i_Seg,inf.contrat,inf.nProp 
					from Inform as inf join Importer as imp on inf.id = imp.idInform join Country as c on imp.idCountry = c.id 
					where inf.id =$umInform1 and inf.state = $status1 and (imp.credit <> 0 or imp.creditTemp<>0)
					and (imp.state=6 or (imp.state=7 and (imp.dt_cob_analise is null)))
					
					union
					
					select imp.name,imp.state,imp.stateDate,imp.credit,imp.creditTemp,c.name,imp.id,imp.limCredit,imp.dt_cob_analise,inf.startValidity,inf.i_Seg,inf.contrat,inf.nProp 
					from Inform as inf join Importer as imp on inf.id = imp.idInform 
					join Country as c on imp.idCountry = c.id 
					where inf.id =$umInform1 and inf.state = $status1 
					and (imp.state=6 and imp.credit = 0 and imp.creditTemp=0 and imp.dt_cob_analise is null and imp.stateDate>'$dtFimUltimoTrimestre')
					order by imp.name";							
			$exec10 = odbc_exec($db, $consUmImpor10);
			//echo $consUmImpor10;
			//este while vai percorrer os importadores e depois vai popular o array de importadores
			while(odbc_fetch_row ($exec10))
			{
				$umImpName       = odbc_result ($exec10,1);
				$umImpState      = odbc_result ($exec10,2);
				$umImpStateDate  = odbc_result ($exec10,3);
				$umImpCredit     = odbc_result ($exec10,4);
				$umImpCreditTemp = odbc_result ($exec10,5);
				$umImpCobAna     = odbc_result ($exec10,9);
				$umImpId         = odbc_result ($exec10,7);
				$limCredit       = odbc_result ($exec10,8);
				
				$startValidity   = odbc_result ($exec10,10);
				$i_Seg           = odbc_result ($exec10,11);
				$contrat         = odbc_result ($exec10,12);
				$nProp           = odbc_result ($exec10,13);
				
				$inativaEcobraAnalise = explode(";",inativaComCobAnalise($db,$umImpId));	
				if(($umImpState==7 && $inativaEcobraAnalise[0]==1) || $umImpStateDate>$dtFimUltimoTrimestre)
				{
					array_push($valor['IMPORTADORES'],$umImpName.";".$umImpState.";".$umImpStateDate.";".$umImpCredit.";".$umImpCreditTemp.";".$umImpCobAna.";".$umImpId.";".$inativaEcobraAnalise[1].";".$startValidity.";".$i_Seg.";".$contrat.";".$nProp.";".$limCredit);
				}
				else
				{
					array_push($valor['IMPORTADORES'],$umImpName.";".$umImpState.";".$umImpStateDate.";".$umImpCredit.";".$umImpCreditTemp.";".$umImpCobAna.";".$umImpId.";".$inativaEcobraAnalise[1].";".$startValidity.";".$i_Seg.";".$contrat.";".$nProp.";".$limCredit);
				}
			}//FIM while
			

			 
		}
		else
		{

			$consUmImpor="select imp.name,imp.state,imp.stateDate,imp.credit,imp.creditTemp,c.name,imp.id,imp.limCredit,imp.dt_cob_analise,inf.startValidity,inf.i_Seg,inf.contrat,inf.nProp 
					from Inform as inf join Importer as imp on inf.id = imp.idInform join Country as c on imp.idCountry = c.id 
					where inf.id =$umInform1 and inf.state = ".($unStatus?$unStatus:10)." and (imp.credit <> 0 or imp.creditTemp<>0)
					and (imp.state=6 or (imp.state=7 and (imp.dt_cob_analise is null)))
					
					union
					
					select imp.name,imp.state,imp.stateDate,imp.credit,imp.creditTemp,c.name,imp.id,imp.limCredit,imp.dt_cob_analise,inf.startValidity,inf.i_Seg,inf.contrat,inf.nProp 
					from Inform as inf join Importer as imp on inf.id = imp.idInform 
					join Country as c on imp.idCountry = c.id 
					where inf.id =$umInform1 and inf.state = ".($unStatus?$unStatus:10)." and (imp.state=6 and imp.credit = 0 and
               imp.creditTemp=0 and imp.dt_cob_analise is null and imp.stateDate>'$dtFimUltimoTrimestre')
					order by imp.name";								
			$exec1 = odbc_exec($db, $consUmImpor);
			
		   //este while vai percorrer os importadores e depois vai popular o array de importadores
			while(odbc_fetch_row ($exec1))
			{
				$umImpName       = odbc_result ($exec1,1);
				$umImpState      = odbc_result ($exec1,2);
				$umImpStateDate  = odbc_result ($exec1,3);
				$umImpCredit     = odbc_result ($exec1,4);
				$umImpCreditTemp = odbc_result ($exec1,5);
				$umImpCobAna     = odbc_result ($exec1,9);
				$umImpId         = odbc_result ($exec1,7);
				$limCredit       = odbc_result ($exec1,8);
				
				$startValidity   = odbc_result ($exec1,10);
				$i_Seg           = odbc_result ($exec1,11);
				$contrat         = odbc_result ($exec1,12);
				$nProp           = odbc_result ($exec1,13);
				
				$inativaEcobraAnalise = explode(";",inativaComCobAnalise($db,$umImpId));				
				if(($umImpState==7 && $inativaEcobraAnalise[0]==1) || $umImpStateDate>$dtFimUltimoTrimestre)
				{	
					array_push($valor['IMPORTADORES'],$umImpName.";".$umImpState.";".$umImpStateDate.";".$umImpCredit.";".$umImpCreditTemp.";".$umImpCobAna.";".$umImpId.";".$inativaEcobraAnalise[1].";".$startValidity.";".$i_Seg.";".$contrat.";".$nProp.";".$limCredit);
				}
				else
				{
					array_push($valor['IMPORTADORES'],$umImpName.";".$umImpState.";".$umImpStateDate.";".$umImpCredit.";".$umImpCreditTemp.";".$umImpCobAna.";".$umImpId.";".$inativaEcobraAnalise[1].";".$startValidity.";".$i_Seg.";".$contrat.";".$nProp.";".$limCredit);
				}
			}//FIM while
			
			//imprimindo o array de importadores de um determinado exportador
/* 			foreach($valor['IMPORTADORES'] as $valorImp)
			 {
				echo $valorImp."<br>";
			 }   */ 	
		}
		
		//echo "<hr>";
		
	array_push($arrImporterDados,$valor);	
	}//FIM foreach
	
	
?>