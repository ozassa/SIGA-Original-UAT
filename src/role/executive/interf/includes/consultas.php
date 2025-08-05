<?php

$cur=odbc_exec($db,"SELECT i_Produto, i_Empresa FROM Inform WHERE id = $idInform");  
$i_Produto   = odbc_result($cur,'i_Produto');
$i_Empresa   = odbc_result($cur,'i_Empresa');


// Interaktiv
// fazendo trazer só as seções do CNAE
$sql  = "select * from CNAE where t_CNAE = 1";
$cur2 = odbc_exec($db,$sql);



$lista_secoes_cnae = array();
while ($dados = odbc_fetch_row($cur2)){ 
	$lista_secoes_cnae[]  = array("id" => odbc_result($cur2,'i_CNAE'), "nome" => odbc_result($cur2,'Descricao')." - ".odbc_result($cur2,'Cod_CNAE'));
}


$cur3=odbc_exec($db, "SELECT nu_banco FROM ParModEsp WHERE idInform = $idInform");

 
if (odbc_fetch_row($cur3)) {
	$tp_banco = odbc_result($cur3, 'nu_banco');
}


$qry = "SELECT a801, a502, b603, b1504, b1202, c102, d101, d602, d701, e101, f305, f3301, nivel_d602, p_cobertura_d701, 
	 limite_d701, ic_nao_aplica_taxa,adequacao_sinistralidade,adequacao_premio,franquia_anual,
	 SUBSTRING(condicoes_especiais, 0, 4000) AS cond_esp_part1, SUBSTRING(condicoes_especiais, 4000, 4000) AS cond_esp_part2, SUBSTRING(condicoes_especiais, 8000, 2000) AS cond_esp_part3, SUBSTRING(condicoes_especiais, 10000, 2000) AS cond_esp_part4,
	 CONVERT(varchar(1000),condicoes_especiais) AS condicoes_especiais, PrazoMaxEmiNota, b904, b2604Perc, b2604NivelMax, 
	 b2801NivelMax, b2801Perc, d201, f401NivelSinistralidade,f401PercPremio,b802, b404NivelMax,b404Perc,GerenteCredito,Texto_Risco_Politico		 
	 FROM ModuloOferta WHERE idInform = $idInform";

$cur_cnae =odbc_exec($db, "SELECT c.Descricao as desc_cnae, c.Cod_CNAE as cod_cnae, c.i_CNAE FROM Inform i LEFT JOIN CNAE c on i.i_CNAE = c.i_CNAE  WHERE id = $idInform");
$desc_cnae = odbc_result($cur_cnae, 'desc_cnae')." - ".odbc_result($cur_cnae, 'cod_cnae');
$i_CNAE = odbc_result($cur_cnae, 'i_CNAE');



 
//print $qry;
$cur4=odbc_exec($db,$qry);

// $valido = odbc_fetch_row($cur4);
$valido = true;

		if(odbc_result($cur4, 'a801')=="1") $a801 = " checked ";
		if(odbc_result($cur4, 'a502')=="1") $a502 = " checked ";

		//if(odbc_result($cur4, 'b603')=="1") $b603 = " checked ";
		// Alterado po Elias Vaz 30/04/2009 : Motivo - Não exibir a caixa  conforme solicitação: "Veja documento"
	   

		if(odbc_result($cur4, 'b1504')== "1") $b1504 = " checked ";
		if(odbc_result($cur4, 'b1202')== "1") $b1202 = " checked ";
		if(odbc_result($cur4, 'c102')== "1") $c102 = " checked ";
		if(odbc_result($cur4, 'd101')== "1") $d101 = " checked ";
		if(odbc_result($cur4, 'd602')== "1") $d602 = " checked ";
		if(odbc_result($cur4, 'd701')== "1") $d701 = " checked ";
		if(odbc_result($cur4, 'e101')== "1") $e101 = " checked ";
		if(odbc_result($cur4, 'f305')== "1") $f305 = " checked ";
		if(odbc_result($cur4, 'f3301')== "1") $f3301 = " checked ";

		
		$GerenteCredito  = odbc_result($cur4, 'GerenteCredito');
		$RiscoPolitico   = odbc_result($cur4,'Texto_Risco_Politico');
		//Valores para Produto Interno
		//if($i_Produto == 1){
		if(odbc_result($cur4, 'b603')== "1"){
			 $b603 = " checked ";		     
		}
	
		$PrazoMaxEmiNota  = odbc_result($cur4, 'PrazoMaxEmiNota');	
		$b802             = odbc_result($cur4, 'b802');	
		
		if($b802 >0){
			
			$b802ex  = " checked ";
		} 
		
		if(odbc_result($cur4, 'b904') >0){
			$b904             = number_format(odbc_result($cur4, 'b904'),2,',','.');
		}else{
		   $b904 = 0;
		}
		
		if ($b904  > 0){
			$b904check        = " checked ";
		}
			
		$b2604Perc               = number_format(odbc_result($cur4, 'b2604Perc'),2,',','.');
		$b2604NivelMax           = number_format(odbc_result($cur4, 'b2604NivelMax'),2,',','.');
		
		if($b2604Perc > 0 && $b2604NivelMax >0){
			$b2604  =  " checked ";
		}
		
		$b2801NivelMax           = number_format(odbc_result($cur4, 'b2801NivelMax'),2,',','.');
		$b2801Perc               = number_format(odbc_result($cur4, 'b2801Perc'),2,',','.');
		
		if($b2801NivelMax > 0 && $b2801Perc >0){
			$b2801  = " checked ";
		}
		
		$d201        = number_format(odbc_result($cur4, 'd201'),2,',','.');
		
		if($d201 > 0){			
			  $d201check  =  " checked ";
		}
		
		$b404NivelMax   = number_format(odbc_result($cur4, 'b404NivelMax'),2,',','.');
				$b404Perc       = number_format(odbc_result($cur4, 'b404Perc'),2,',','.');
		
		$f401NivelSinistralidade = number_format(odbc_result($cur4, 'f401NivelSinistralidade'),2,',','.');
		$f401PercPremio          = number_format(odbc_result($cur4, 'f401PercPremio'),2,',','.');
		
		if($f401NivelSinistralidade >0 && $f401PercPremio >0){
			 $f401check  =  " checked ";
		}


		
	//}else{
		/*
		$b603                    = 0;
		$b802                    = 0;
		$PrazoMaxEmiNota         = 0;
		$b904                    = 0;
		$b2604Perc               = 0;
		$b2604NivelMax           = 0;
		$b2801NivelMax           = 0;
		$b2801Perc               = 0;
		$d201                    = 0;
		$f401NivelSinistralidade = 0;
		$f401PercPremio          = 0;
		*/
	//}
	
	if(odbc_result($cur4, 'adequacao_sinistralidade')> 0 || odbc_result($cur4, 'adequacao_premio')> 0){
		$adeaquacao = " checked ";
		$ad_sinistr = number_format(odbc_result($cur4, 'adequacao_sinistralidade'),2,',','.');
		$ad_premio =  number_format(odbc_result($cur4, 'adequacao_premio'),2,',','.');
	}else{
		$adeaquacao = "";
		$ad_sinistr = 0;
		$ad_premio = 0;
	}

	if(odbc_result($cur4, 'franquia_anual')> 0){
		$franquia = " checked ";
		$franquia_anual = number_format(odbc_result($cur4, 'franquia_anual'),2,',','.');
	}else{
		$franquia = "";
		$franquia_anual = 0;
	}

	if ($franquia != ""){
		$ativaFrank = "block";
		$chekfranq = 1;
	}else{
		$ativaFrank = "none";
		$chekfranq = 0;
	}

	if(odbc_result($cur4, 'condicoes_especiais')!= "" ){
		$CondEsp = " checked ";
		// $condicoes_especiais = odbc_result($cur4,'condicoes_especiais');
 		$condicoes_especiais = odbc_result($cur4, 'cond_esp_part1').odbc_result($cur4, 'cond_esp_part2').odbc_result($cur4, 'cond_esp_part3').odbc_result($cur4, 'cond_esp_part4');
	}else{
		$CondEsp = "";
		$condicoes_especiais = "";
	}

	$ic_nao_aplica_taxa = odbc_result($cur4, 'ic_nao_aplica_taxa');
	$nivel_d602         = number_format(odbc_result($cur4, 'nivel_d602'),2,',','.');
	$p_cobertura_d701   = odbc_result($cur4, 'p_cobertura_d701');
	$limite_d701        = odbc_result($cur4, 'limite_d701');


  if($p_cobertura_d701=="") 
	   $p_cobertura_d701 = "70";
 

  $qry = "SELECT  Inf.id, Inf.respName, Inf.ocupation, Inf.bornDate, Inf.prMin, Inf.txMin, Inf.txRise, Inf.idRegion, Inf.numParc,
		 Inf.periodMaxCred, Inf.currency, Inf.limPagIndeniz, Inf.validCot, Inf.txAnalize, Inf.currencyAnalize,
		 Inf.txMonitor, Inf.currencyMonitor, Inf.prodUnit, Inf.percCoverage, Inf.sentOffer, Inf.tarifDate, 
		 Inf.warantyInterest, Inf.idUser, Inf.emailContact,
		 Inf.pvigencia, Inf.txMonitor, Inf.mModulos, Inf.tipoDve, Inf.perBonus, Inf.perPart0, Inf.perPart1, 
		 Inf.pLucro, Inf.nas, Inf.Ga, IsNull(Inf.prAux,'0') as prAux, 
		 Inf.txAux, Inf.ic_gravou_mod,Inf.idConsultor,Inf.p_Comissao,Inf.mPart,Inf.origemNegocio, Inf.Litigio,Inf.prMTotal,
		 Inf.txMTotal,Inf.dataOferta,Inf.dataPreench, Inf.i_Gerente, Inf.i_Gerente_Relacionamento, Inf.i_Contrato_Resseguro, Inf.p_Taxa_Desagio,Inf.i_Produto,
		 Inf.i_Sub_Produto, Inf.Periodo_Vigencia,Inf.Num_Parcelas,Inf.t_Vencimento, Inf.DataPrimeiraParcela, Inf.v_LMI, Inf.Renovacao_Tacita, 			 
		 Inf.Prazo_Entrega_DVN, Inf.Prazo_Nao_Intencao_Renov,
		 Iam.Forma_Cobranca_Analise As Forma_Cobranca,
		 IsNull(Iam.Taxa_Analise, IsNull(Inf.txAnalize, 0)) As Taxa_Analise,
		 IsNull(Iam.Taxa_Monitoramento, IsNull(Inf.txMonitor, 0)) as Taxa_Monitoramento,
		 IsNull(Iam.Num_Parcelas, 0) As Numero_Parcelas,
		 IsNull(Iam.t_Vencimento, 0) As Tipo_Vencimento,
		 Iam.d_Primeiro_Vencimento As Primeiro_Vencimento,
		 IsNull(Iam.Cobra_Apenas_Comprador_Novo, IsNull(Inf.CobraCompradorNovo, 0)) As Cobra_Apenas_Comprador_Novo,
		 Inf.NPC,
		 Inf.contrat
		 FROM Inform Inf
		 left join Inform_Analise_Monitoramento Iam on Iam.i_Inform =  Inf.id
		 
		 WHERE Inf.id = $idInform";

  $cur=odbc_exec($db,$qry);
  //print $qry; 






  if (odbc_fetch_row($cur)) {

	  
	 
	$date = odbc_result($cur,4);
	$limPagIndeniz = odbc_result($cur, 'limPagIndeniz');
	$ValorLMI = odbc_result($cur, 'v_LMI');
	$cobertura = odbc_result($cur, 'percCoverage');
	$npc = odbc_result($cur, 'NPC');
	$DPP = odbc_result($cur, 'contrat');
	$tarifDate = odbc_result($cur,21);
	$tarifDate = substr($tarifDate,8,2)."/".substr($tarifDate,5,2)."/".substr($tarifDate,0,4);
	$field->setDB ($cur);

	$t_Vencimento   = odbc_result($cur,'t_Vencimento'); // Tipo de vencimento = Mensal,Bimestral, Trimestral, Semestral
	
	
	// Trecho análise e monitoramento
	$Forma_Cobranca                   = odbc_result($cur,'Forma_Cobranca');
	$Numero_Parcelas                  = odbc_result($cur,'Numero_Parcelas');
	$Tipo_Vencimento                  = odbc_result($cur,'Tipo_Vencimento');
	$Primeiro_Vencimento              = odbc_result($cur,'Primeiro_Vencimento');
	$Cobra_Apenas_Comprador_Novo      = odbc_result($cur,'Cobra_Apenas_Comprador_Novo'); 
	
	
	
	
	
	$Num_Parcelas   = odbc_result($cur,'Num_Parcelas');

	$numParc   = odbc_result($cur,'numParc');
	
	$DataPrimeiraParcela   = Convert_Data_Geral(substr(odbc_result($cur,'DataPrimeiraParcela'),0,10));
	//print $DataPrimeiraParcela;
	
	if($numParc > 0 && (!$Num_Parcelas)){
			$Num_Parcelas   = $numParc;
	}else{
		$Num_Parcelas   = odbc_result($cur,'Num_Parcelas');
	}
	
	if((!$t_Vencimento)  && $numParc == 4){  //Trimestral
		$t_Vencimento   = 3;
		}else if((!$t_Vencimento) && $numParc == 7){ // Mensal
				$t_Vencimento   = 1;
		}else if((!$t_Vencimento) && $numParc == 8){ // Trimestral
				$t_Vencimento   = 3;
	}else{	
				$t_Vencimento   = odbc_result($cur,'t_Vencimento'); // Tipo de vencimento = Mensal,Bimestral, Trimestral, Semestral
	}
	
	//echo "Número de parcelas [".$numParc."]";
	//echo "<br>".($field->getDBField ("warantyInterest",22) == 1 ? 1.04 : 1)."<br>";
	$idUser = odbc_result($cur,"idUser");
	$respName = odbc_result($cur,"respName");
	$ocupation = odbc_result($cur,"ocupation");
	$txAnalize = odbc_result($cur,"txAnalize");
	$txMonitor = odbc_result($cur,"txMonitor");
	$num_parc = odbc_result($cur,"Numero_Parcelas");
	$prAux = odbc_result($cur,"prAux");
	$txAux = odbc_result($cur,"txAux");
	$emailContact = odbc_result($cur,"emailContact");
	$vigencia    = odbc_result($cur, "pvigencia");
	$dataOferta  = odbc_result($cur, "dataOferta");
	$dataPreench = odbc_result($cur, "dataPreench");
	$mBonus      = odbc_result($cur, "mModulos");
	$ic_gravou_mod = odbc_result($cur, "ic_gravou_mod");
	$mPart       = odbc_result($cur, "mPart");
	$tipoDve     = odbc_result($cur, "tipoDve");
	$perBonus    = number_format(odbc_result($cur, "perBonus"),2,",",".");
	$perPart0    = number_format(odbc_result($cur, "perPart0"),2,",",".");
	$perPart1    = number_format(odbc_result($cur, "perPart1"),2,",",".");
	$pLucro      = odbc_result($cur, "pLucro");
	$nas         = odbc_result($cur, "nas");
	$ga          = odbc_result($cur, "Ga");
	$moeda       = odbc_result($cur, "currency");
	$jurosmora	 = odbc_result($cur, "warantyInterest");
	$idConsultor = odbc_result($cur, "idConsultor");
	$p_Comissao    = number_format(odbc_result($cur, "p_Comissao"),2,',','.');
	$origemNegocio = odbc_result($cur, "origemNegocio");
	$litigio            =   odbc_result($cur, "Litigio");
	$sentOffer          =  odbc_result($cur, "sentOffer");
	$Periodo_Vigencia   =  odbc_result($cur, "Periodo_Vigencia");
	$Renovacao_Tacita   = odbc_result($cur,'Renovacao_Tacita');

	$premioMinimo = (odbc_result($cur, "prMin"));
	$taxaMinima = (odbc_result($cur, "txMin"));
	$prMTotal = (odbc_result($cur, "prMTotal"));
	$txMTotal = (odbc_result($cur, "txMTotal"));
	$txRise = (odbc_result($cur, "txRise"));
	$periodMaxCred = (odbc_result($cur, "periodMaxCred"));
	$prodUnit = $field->getDBField("prodUnit", 18);
	$validCotD = $field->getDBField("validCot", 13);
	$data_entrega_dvn = odbc_result($cur,'Prazo_Entrega_DVN');
	$data_nao_intencao_renov = odbc_result($cur,'Prazo_Nao_Intencao_Renov');
	$primeiro_venc = odbc_result($cur,'Primeiro_Vencimento');
	$moeda_pag = $field->getDBField('currency', 11);
	$currencyAnalize = $field->getDBField('currencyAnalize', 15);
	$currencyMonitor = $field->getDBField('currencyMonitor', 17);

	$i_Gerente              	= odbc_result($cur, "i_Gerente");
	$i_Gerente_Relacionamento = odbc_result($cur, "i_Gerente_Relacionamento");
  $i_Contrato_Resseguro   = odbc_result($cur, "i_Contrato_Resseguro");
  $p_Taxa_Desagio         = odbc_result($cur, "p_Taxa_Desagio");
	$i_Produto              = odbc_result($cur, "i_Produto");
	$i_Sub_Produto          = odbc_result($cur,'i_Sub_Produto');

   if ($nas == "" || $nas == "0.00"){
	  if ($moeda == "2") {
		 $nas = "1000";
	  }elseif ($moeda == "6"){
		 $nas = "1000";
	  }elseif ($moeda == "0"){
		 $nas = "1000";
	  }
	}

	
	
	if ($mBonus == "1") {
	   $nenhum = "checked";
	   $bonus  = "checked";
	}else{
	   $nenhum = "checked";
	   $bonus  = "";
	}


	if ($mPart  == "1"){
	   $nenhum = "checked";
	   $part   = "checked";
	}
	else{
	   $nenhum = "checked";
	   $part   = "";
	}


	if ($vigencia == "") {
	   $pvigencia = "12 Meses";
	}else if ( $vigencia == "1" ) {
	   $pvigencia = "12 Meses";
	}else{
	   $pvigencia = "24 Meses";
	}
	
	if($Periodo_Vigencia){
		$pvigencia = $Periodo_Vigencia. " Meses"; 	
	}

	if($moeda == '1'){
	   $tmoeda = "Real";
	   $extMoeda = "R\$";
	   $ext = "Real";
	}else if ($moeda == "2") {
	   $tmoeda = "Dólar";
	   $extMoeda = "US\$";
	   $ext = "Dolar Norte Americano";
	}else if($moeda == "6"){
	   $tmoeda = "Euro";
	   $extMoeda = "&euro;";
	   $ext = "Euro";
	}else if ($moeda == "0") {
	   $tmoeda = "Dólar";
	   $extMoeda = "US\$";
	   $ext = "Dolar Norte Americano";
	}

	//print '?'.$limite_d701;
	if($limite_d701 == "" || $limite_d701 == 0 || rtrim($limite_d701) == "310000000") {			
		$ValorExtenso =  $numberExtensive->extensive('100000',$moeda);
		$limite_d701 = "Esse módulo cobre até 3 casos de litígio no valor máximo de ".$extMoeda." 100.000,00 (".$ValorExtenso.") cada.";
	}
}