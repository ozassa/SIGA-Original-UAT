<?php 
/*
*************************************************************************************************************************************
CRIADO POR WAGNER 08/10/2008
ESTA FUN��O CRIA 4 DATAS TRIMESTRAIS ANTERIORES, IGUAL AO CODIGO QUE � USADO NO FECHAMENTO
SENDO QUE NESTA FUN��O A IMPLEMENTA��O DO ALGORITMO FOI DE FORMA DIFERENTE AO CRIADO NO ARQUIVO DE ReportMonitor.php

PEGA O ANO ATUAL E O MES ATUAL,SOMA 1 AO MES ATUAL, ISTO SERVE PARA PODER GERAR O MES A SER COBRADO ,  COM BASE NA DATA 
DE INICIO, DO INFORM QUE DEVE ESTAR EM 1 DAS 4 DATAS, ESTES 4 TRIMESTRES GERADOS DINAMICAMENTE, TENTA PEGAR O INICIO 
DE VIGENCIA DOS INFORMS
*************************************************************************************************************************************
*/
//PEGA O ANO ATUAL E O MES ATUAL, SOMA 1 AO MES ATUAL, ESTA SOMA � PARA INFORMAR O MES DE COBRAN�A
function criaTrimesAnt($umAno,$umMesIni)
	{
		//CRIA DOIS ARRAYS DE MESES, O OBJETIVO � TERMOS UMA ESPECIE DE 2 DOIS ANOS COMPLETOS, PARA NAVEGARMOS PELOS 
		//MESES A SEREM COBRADOS
		$arrAnoAnt = array(0,1,2,3,4,5,6,7,8,9,10,11,12);
		$arrAnoDep = array(0,1,2,3,4,5,6,7,8,9,10,11,12);
		
		//1� COBRAN�A, PEGA-SE O MES ATUAL SOMA 1, ISTO NOS INDICA QUE O MES ONDE SER� COBRADO, � SEMPRE O MES SEGUINTE
		//EM RELA��O AO MES QUE ESTA SENDO GERADO A COBRAN�A
		$umMesIniPri = $arrAnoAnt[$umMesIni];//PEGA O TRIMESTRE INCIAL DO ANO PASSADO
		$umAnoRetPri = $umAno - 1;
		
		
		//2� COBRAN�A, APOS 3 MESES, OU SEJA APARTIR DO PRIMEIRO MES COME�A AS COBRAN�AS TIMESTRAIS
		if(($umMesIniPri + 3) > 12 )
		{
			$umMesIniSeg = $arrAnoDep[($umMesIniPri + 3)-12];
			$umAnoRetSeg = $umAno;
		}else
		{
			$umMesIniSeg = $arrAnoAnt[($umMesIniPri + 3)];
			$umAnoRetSeg = $umAno - 1;
		}
				
		
		//3� cobran�a,APOS 3 MESES, OU SEJA APARTIR DO PRIMEIRO MES COME�A AS COBRAN�AS TIMESTRAIS
		if(($umMesIniPri + 6) > 12 )
		{
			$umMesIniTer = $arrAnoDep[($umMesIniPri + 6)-12];
			$umAnoRetTer = $umAno;
		}else
		{
			$umMesIniTer = $arrAnoAnt[($umMesIniPri + 6)];
			$umAnoRetTer = $umAno - 1;
		}
		
		
		
		//4� cobran�a,APOS 3 MESES, OU SEJA APARTIR DO PRIMEIRO MES COME�A AS COBRAN�AS TIMESTRAIS
		if(($umMesIniPri + 9) > 12 )
		{
			$umMesIniQua = $arrAnoDep[($umMesIniPri + 9)-12];
			$umAnoRetQua = $umAno;
		}else
		{
			$umMesIniQua = $arrAnoAnt[($umMesIniPri + 9)];
			$umAnoRetQua = $umAno - 1;
		}
		
		
		//1� data		
		$dtRetIni1 = mkdate ($umAnoRetPri, $umMesIniPri , 1);//cria  a data inicial 
		$dtRetFim1 = mkdate ($umAnoRetPri, $umMesIniPri + 1, 0);//cria a data final 
		
		//2� data		
		$dtRetIni2 = mkdate ($umAnoRetSeg, $umMesIniSeg , 1);//cria  a data inicial 
		$dtRetFim2 = mkdate ($umAnoRetSeg, $umMesIniSeg + 1, 0);//cria a data final 
		
		//3� data		
		$dtRetIni3 = mkdate ($umAnoRetTer, $umMesIniTer , 1);//cria  a data inicial 
		$dtRetFim3 = mkdate ($umAnoRetTer, $umMesIniTer + 1, 0);//cria a data final 
		
		//3� data		
		$dtRetIni4 = mkdate ($umAnoRetQua, $umMesIniQua , 1);//cria  a data inicial 
		$dtRetFim4 = mkdate ($umAnoRetQua, $umMesIniQua + 1, 0);//cria a data final 
		
		$dates = "(startValidity >= '$dtRetIni1' AND startValidity <= '$dtRetFim1')
				  OR (startValidity >= '$dtRetIni2' AND startValidity <= '$dtRetFim2')
				  OR (startValidity >= '$dtRetIni3' AND startValidity <= '$dtRetFim3')
				  OR (startValidity >= '$dtRetIni4' AND startValidity <= '$dtRetFim4')";
		
		return $dates;
	}
?>