<?php 
    
    include_once("docFunctions.php");
    include_once("../consultaCoface.php");
    include_once("../../../gerar_pdf/MPDF45/mpdf.php");

	$idParcela = 0;
	  
  	// soma dos campos A,B,C
  	$cur = odbc_exec($db, "SELECT vol2 + vol3 + vol4, startValidity, endValidity 
  								FROM Volume 
  									JOIN Inform ON Volume.idInform = Inform.id 
  								WHERE Inform.id = ".$idInform
					);

  	$abc = 0;
  	$startValidity = "";
  	$endValidity = "";

	if (odbc_fetch_row($cur)) {
		$abc = odbc_result($cur, 1);
		$data = odbc_result($cur, "startValidity");

		if ($data){
			$startValidity =
				substr($data, 8, 2).
				"/".
				substr($data, 5, 2). 
				"/".
				substr($data, 0, 4);
		}
	
		$data = odbc_result($cur, "endValidity");

		if ($data){
			$endValidity =
				substr($data, 8, 2).
				"/".
				substr($data, 5, 2).
				"/".
				substr($data, 0, 4);
		}
	}
		
	// Países
	$cur = odbc_exec ($db, "SELECT Distinct Country.name, Country.score
								FROM Inform inf 
									INNER JOIN VolumeSeg v ON inf.id = v.idInform 
									INNER JOIN Country ON v.idCountry = Country.id
								WHERE inf.id = ".$idInform." AND score < 8");
	$country = "";
	$first = true;
	while (odbc_fetch_row ($cur)) {
		$country .= ($first ? "" : ", "). odbc_result($cur, 1);
		if ($first) { 
			$first = false;
		}
	}

	$country .= ".";

	$totasseg = 0;
	//pega o premio - codigo antigo
	$cur = odbc_exec($db, "SELECT prMin, txMin, txRise, i_Produto FROM Inform WHERE id = $idInform");
	
	if(odbc_fetch_row($cur)) {
		//calcula premio - antigo
		$xPr = number_format ((odbc_result($cur, 1) * (1 + odbc_result($cur, 3))), 2, '.', '');

		//calcula premio - novo
		$xTx = number_format ((odbc_result($cur, 2) * (1 + odbc_result($cur, 3))), 4, '.', '');

		$i_Produto  = odbc_result($cur, 4);
	}
	  
	$qry = "SELECT Distinct u.login, inf.respName, inf.idRegion, inf.name, inf.txMin,inf.prMTotal,
						 inf.cnpj, inf.ie, inf.address, inf.tel, inf.fax, inf.email, inf.ocupationContact,
						 inf.city, r.name AS uf, Sector.description, inf.warantyInterest,
						 inf.periodMaxCred, Country.name, inf.cep, inf.contrat, inf.prodUnit, inf.i_Seg,
						 inf.contact, inf.products, inf.addressNumber, inf.pvigencia, inf.currency,
						 inf.addressComp,inf.prMin,inf.prMTotal,txMTotal,limPagIndeniz,inf.perPart0, inf.perPart1, inf.v_LMI,
						 inf.nProp 
					   FROM Users u INNER JOIN
				 		 Insured i ON u.id = i.idResp INNER JOIN
						 Inform inf ON inf.idInsured = i.id INNER JOIN
						 Sector ON inf.idSector = Sector.id LEFT JOIN
						 Importer ON inf.id = Importer.idInform LEFT JOIN
						 Country ON Importer.idCountry = Country.id JOIN
						 Region r ON inf.idRegion = r.id
					   WHERE inf.id = $idInform";					   
	$cur = odbc_exec ($db,$qry);
	  
	// tenta achar o usuário responsável
	if (odbc_fetch_row ($cur)) {
		$login    = odbc_result($cur, 1);
		$respName = odbc_result($cur, 2);
		if(! $respName){
			$respName = odbc_result($cur, 'contact');
		}
		$idRegion 		= odbc_result($cur, 3);
		$name     		= odbc_result($cur, 4);
		$txMin    		= odbc_result($cur, 5);
		$prMin    		= odbc_result($cur, 'prMin');
		$cnpj     		= odbc_result($cur, 7);
		$ie       		= odbc_result($cur, 8);
		$address  		= odbc_result($cur, 9);
		$addNumber		= odbc_result($cur, 26);
		$tel      		= odbc_result($cur, 10);
		$fax      		= odbc_result($cur, 11);
		$email    		= odbc_result($cur, 12);
		$oContact 		= odbc_result($cur, 13);
		$city     		= odbc_result($cur, 14);
		$uf       		= substr(odbc_result($cur, 15), 0, 2);
		$descrip  		= odbc_result($cur, 16);
		$interest 		= odbc_result($cur, 17);
		$period   		= odbc_result($cur, 18);
		$cep      		= odbc_result($cur, 20);
		$complemento 	= odbc_result($cur, "addressComp");
		$limPagIndeniz  = odbc_result($cur, 'limPagIndeniz');
		$ValorLMI   	= odbc_result($cur, 'v_LMI');	
		$pvigencia 		= odbc_Result($cur, "pvigencia");
		$moeda    		= odbc_result($cur, "currency");
		$currency  		= $moeda;
		$nProp    		= odbc_result($cur, "nProp");

		$extMoeda = '';
		$ext = '';
		if ($moeda == "1"){
			$extMoeda = "R$ ";
			$ext      = "REAIS";
		}else if ($moeda == "2"){
			$extMoeda = "USD ";
			$ext      = "DÓLARES NORTE-AMERICANOS";
		}else if ($moeda == "6") {
			$extMoeda = "€ ";
			$ext      = "EUROS";
		}elseif ($moeda == "0") {
			$extMoeda = "USD ";
			$ext      = "DÓLARES NORTE-AMERICANOS";
		}
	   
		$sub = substr($cep, 0, 5);
		$inc = 0;

		if (! preg_match("/\./", $sub)) {
			$sub = substr($sub, 0, 2). '.'. substr($sub, 2, 3);
		} else {
			$inc = 1;
			$sub = substr($cep, 0, 6);
		}

		if (! preg_match("/-/", $cep)) {
			$cep = "$sub-". substr($cep, 5);
		} else {
			$cep = "$sub-". substr($cep, 6 + $inc);
		}

		$iSeg = odbc_result($cur, 'i_Seg');

		// encontrar o número de propostas
		$contract = odbc_result($cur, 21);
		$products = odbc_result($cur, 'products');

		//Endereço do proponente
		$endcompleto = "$address". ($addNumber ? ", ".$addNumber : "").($complemento ? " - ".$complemento : "" );

		if ( ! $addNumber) {     //alterei essa parte em 08/06/05(adicionei o numero do endereço
			$end = $address." - ".$city." - CEP: ".$cep." - ".$uf;
			$address = $address." - ".$city." - ".$uf;
		}else{
			$end = $address.", ".$addNumber." - ".$city." - CEP: ".$cep." - ".$uf;
			$address = $address.", ".$addNumber." - ".$city." - ".$uf;
		}
		$tx = $txMin * (1 + $field->getNumField("txRise") / 100) * 100;		
		
		$prMTotal = odbc_result($cur, 'prMTotal');
		$txMTotal = odbc_result($cur, 'txMTotal');

		$tx = $txMTotal;
		$pr = $prMTotal;

		$extPremioMinimo = $numberExtensive->extensive(number_format($pr,0,'.',''),$currency);

		$partDeducao  = number_format(odbc_result($cur, 'perPart0'),2,',','.').'%';
		$partLucro    = number_format(odbc_result($cur, 'perPart1'),2,',','.').'%';		
		$pr = number_format($pr,2,".","");
		$tx = number_format ($tx, 4, '.','');	   
		
		$totasseg = 0;

		if($tx != 0){
			$totasseg = $pr / ($tx/100);  //coloquei agora
		}

		$totasseg = $extMoeda . number_format ($totasseg, 2, ',','.');
	}
	
	$contract .="/$nProp";
	
	$txMonitor = $field->getNumField("txMonitor");
	$txAnalise = $field->getNumField("txAnalize");	
	$percCoverage = $field->getNumField("percCoverage");
	$numParc = $field->getNumField("Num_Parcelas");
	$t_Vencimento  = $field->getNumField("t_Vencimento");

	$idAnt = odbc_result(odbc_exec($db, "SELECT idAnt from Inform where id=$idInform"), 1);
	  
	$q_res = odbc_exec($db, "SELECT txRise, periodMaxCred, currency, limPagIndeniz, txAnalize, txMonitor, currencyAnalize,
									currencyMonitor, prodUnit, percCoverage, validCot,Num_Parcelas,t_Vencimento 
								FROM Inform 
								WHERE id = $idInform");

	$Num_Parcelas = (odbc_result($q_res, 'Num_Parcelas') ? odbc_result($q_res, 'Num_Parcelas') : odbc_result($q_res, 'numParc'));
	$t_Venc  =  odbc_result($q_res, 't_Vencimento');
 
	$valPar = number_format($pr / $Num_Parcelas, 2, ".", "");
	$parc = $valPar;   
	  
	if($t_Venc == 1){
		$t_Vencimento = 'mensais';
	}else if ($t_Venc == 2){
		$t_Vencimento = 'bimestrais';
	}else if ($t_Venc == 3){
		$t_Vencimento = 'trimestrais';
	}else if ($t_Venc == 4){
		$t_Vencimento = 'semestrais';
	}

	$valParExt = $valPar;
	$valParExt = $numberExtensive->extensive($valParExt, $currency);
  
	//Alterador por Tiago V N - Elumini - 06/04/2006
	if ($moeda == "1") {
		$valPar = "R$ " . number_format($valPar, 2, ",", ".");
	}else if ($moeda == "2") {
		$valPar = "USD " . number_format($valPar, 2, ",", ".");
	}else if ($moeda == "6") {
		$valPar = "€ " . number_format($valPar, 2, ",", ".");
	}else if ($moeda == "0") {
		$valPar = "USD " . number_format($valPar, 2, ",", ".");
	}

	$prExt = $pr;
	$prExt = number_format($prExt, 2, '.', '');  
  
	//alterador por Tiago V N - Elumini - 06/04/2006
	if ($moeda == "1") {
		$prPrint = "R$ " . number_format ($pr, 2,",",".");
	}else if ($moeda == "2") {
		$prPrint = "USD " . number_format ($pr, 2,",",".") ;
	}else if ($moeda == "6") {
		$prPrint = "€ " . number_format ($pr, 2,",",".");
	}else if ($moeda == "2") {
		$prPrint = "USD " . number_format ($pr, 2,",",".") ;
	}

	if ($startValidity == ""){
		$today = "";
	} else {
		$today = $startValidity." à ".$endValidity;
	}

	$sql = "SELECT pvigencia, Ga, Renovacao_Tacita from Inform where id='".$idInform."'";

	$cur = odbc_exec($db, $sql);

	while (odbc_fetch_row ($cur)) {
		$pvigencia 			= odbc_result($cur, 1);
		$ga        			= odbc_result($cur, 2);
		$renovacao_Tacica  	= odbc_result($cur,'Renovacao_Tacita');
	}
		
	$ano = date("y", time());

	$segundavia = 0;
	$semValor = 0; 

  	$sqlquery  = "SELECT E.*,
							P.Nome as Produto,
							SP.Descricao as SubProduto,
							SP.c_SUSEP,
							Inf.i_Gerente,
							Inf.contrat,
							Inf.nProp,
							Inf.Prazo_Nao_Intencao_Renov, 
							SP.Descricao as DescSubProduto, 
							P.n_SUSEP AS n_SUSEP 
					From Inform Inf
						Inner Join Produto P On P.i_Produto = Inf.i_Produto
						Inner Join Empresa_Produto EP On EP.i_Produto = P.i_Produto
						Inner Join Empresa E On E.i_Empresa = EP.i_Empresa
						Inner Join Sub_Produto SP On SP.i_Produto = Inf.i_Produto 
														And SP.i_Sub_Produto = Inf.i_Sub_Produto
					Where Inf.id = ".$idInform;
   	$res = odbc_exec($db,$sqlquery);
   	$dados = odbc_fetch_array($res);

   	$Prazo_Nao_Intencao_Renov = $dados['Prazo_Nao_Intencao_Renov'];   
   	$i_Gerente = $dados['i_Gerente'];
   
   	$retorno_rodape =  $dados['Endereco'].' - '.
						$dados['Complemento'].' - '.
						'CEP '.formata_string('CEP',$dados['CEP']).' - '.
						$dados['Cidade'].', '.
						$dados['Estado']. ' '.
						'Tel.: '.$dados['Telefone'].'  '.
						'Fax: ' . $dados['Fax'].'  '.
						'Home Page: '. $dados['HomePage'];
				
   	$disclame_retorno = $dados['Nome'].' CNPJ: '.formata_string('CNPJ', $dados['CNPJ']).', SUSEP no.: '. $dados['c_SUSEP'];

	$qry = "SELECT b.razao as Razao 
					from InformCorretor a
					inner join consultor b on a.idCorretor = b.idconsultor
					left join Grupo_Corretor c on c.i_Grupo = a.i_Grupo
					where a.idInform = '".$idInform."' and a.CorretorPrincipal = '1' and a.i_Grupo = '1'";
	$resp = odbc_exec($db,$qry);

	$corretor_nome = '';
	while (odbc_fetch_row($resp)){
		$corretor_nome = odbc_result($resp, 'Razao');
	}

	//Verifica o período total de vigência da apólice
	$query = "SELECT MIN(d_Vigencia_Inicial)as Ini, MAX(d_Vigencia_Final) as Fim 
				from Periodo_Vigencia 
				where i_Inform = ".$idInform."";
	$cur = odbc_exec ($db, $query);

	$VigIni = Convert_Data_Geral(substr(odbc_result($cur,'Ini'),0,10));
	$VigFim = Convert_Data_Geral(substr(odbc_result($cur,'Fim'),0,10));



	$rsquery = odbc_exec($db, "select a.idInform,a.razaoSocial,a.endereco,a.zipCode, a.pais,b.name, a.taxID
								from ParModEsp_Coligada a inner join Country b on b.id = a.pais
								where a.idInform = $idInform ORDER BY a.razaoSocial ");
	$condespcol = "";
	while (odbc_fetch_row($rsquery)){
		$temempcol	= 1;

		$condespcol   	.= "\n";
		$condespcol   	.= " |".  odbc_result ($rsquery, 'razaoSocial')."|\n";
		$condespcol   	.= " Endereço: ".odbc_result ($rsquery, 'endereco')."\n";
		$condespcol   	.= " Pais: ". odbc_result ($rsquery, 'name')."\n Zip Code: ".odbc_result ($rsquery, 'zipCode') ."\n";
		$condespcol   	.= " Tax ID: ". odbc_result ($rsquery, 'taxID')." \n";
	}

	$jurosMora = odbc_result(odbc_exec($db, "select warantyInterest from Inform where id=$idInform"), 1);

	$Litigio = odbc_result(odbc_exec($db, "select Litigio from Inform where id=$idInform"), 1);

	$inicio_vigencia = odbc_result(odbc_exec($db, "select startValidity from Inform where id=$idInform"), 1);

	$periodoMaxCredito = odbc_result(odbc_exec($db, "select periodMaxCred from Inform where id=$idInform"), 1);

	$Periodo_Vigencia   = odbc_result(odbc_exec($db, "select Periodo_Vigencia from Inform where id=$idInform"), 1);

	$msg1 = "
                                                     PROPOSTA DE SEGURO DE CRÉDITO À EXPORTAÇÃO";
	$msg2 = "Seguro novo";

    $csql = "SELECT  convert(char,startValidity,103) as startValidity,
         convert(char,endValidity,103) as endValidity FROM Volume JOIN
         Inform ON (idInform = Inform.id) WHERE  idInform =". $idInform;
    $cur = odbc_exec($db, $csql);

    $startValidity = trim(odbc_result($cur, "startValidity"));
    $endValidity   = trim(odbc_result($cur, "endValidity"));

	if($idAnt){
		$x = odbc_exec($db,
				 "select 	*
				  from 		Inform
				  where 	id=$idAnt
							and (state = 9 or state = 10 or state = 11)"); // vigente ou encerrado
	
		if(odbc_fetch_row($x)){ // renovacao	
			$iseg = odbc_result($x, "i_Seg");
			$n_Prop = odbc_result($x, "nProp");
			$prod = odbc_result($x, "prodUnit");
		}
	}

	$msg5 = "DADOS DO PROPONENTE:";

	$cons = "SELECT a.idCorretor,a.p_Comissao,a.p_Corretor,a.CorretorPrincipal,b.razao, b.c_SUSEP
				from InformCorretor a
					inner join consultor b on b.idconsultor = a.idCorretor
				where a.idInform = ".$idInform. " 
				order by a.CorretorPrincipal desc";
	$resultado = odbc_exec($db, $cons);

	$pularlinha = '';
	$MultiCorretor  = '';

	$linhas = odbc_num_rows($resultado);
	if ($linhas){  
		while (odbc_fetch_row($resultado)){
			if (odbc_result($resultado,'CorretorPrincipal') == 1){	
				$Corretor =  odbc_result($resultado,'razao');
				$codSusep = odbc_result($resultado,'c_SUSEP');
			}

			$MultiCorretor .= $pularlinha.odbc_result($resultado,'razao');
			$pularlinha = '<br>';
		} 
	}else{
		$Corretor = '';
		$codSusep = '';
	}

	$sql = "Select nas, tipoDve, currency from Inform where id = '".$idInform."'";
	$cur = odbc_exec($db, $sql);
	
	$nas =  odbc_result($cur, 1);
	$tipdve = odbc_result($cur, 2);

    if ($tipdve == "1") {
        $tipodve = "mês";
        $periodo = "mensal";
    }elseif($tipdve == "2") {
        $tipodve = "trimestre";
        $periodo = "trimestral";
	}elseif($tipdve == "3") {
        $tipodve = "ano";
        $periodo = "anual";
    }else{
        $tipodve = "mês";
        $periodo = "mensal";
    }

    $txExt = number_format($tx, 3, '.', '');

	$txMonExt =  number_format($txMonitor, 2, '.', '');
	$txMon = "R$ ".number_format($txMonitor,2,",",".");
	$txAn = "R$ ".number_format($txAnalise,2,",",".");

	$extAnalise = $numberExtensive->extensive($txAnalise, 1);   

	$qryx = "SELECT inf.txMin,inf.prMTotal,inf.prMin,txMTotal, inf.v_LMI,Renovacao_Tacita  FROM  Inform inf WHERE inf.id = $idInform";
	$curx = odbc_exec ($db,$qryx);

	$pr = odbc_result($curx,'prMTotal');
	$txMTotal = odbc_result($curx, 'txMTotal');
	$tx = $txMTotal;
	$ValorLMI = odbc_result($curx, 'v_LMI');
	$renovacao_Tacica  = odbc_result($curx,'Renovacao_Tacita');

	$extPremioMinimo = $numberExtensive->extensive(number_format($pr,0,'.',''),$currency);

	$ExtValorLMI = $numberExtensive->extensive(number_format($ValorLMI,0,'.',''),$currency);

	$taxa_analise = number_format($txAnalise,2,",",".");
	$extMonit = $numberExtensive->extensive($txMonitor, 1);
	$taxa_monit = number_format($txMonitor,2,",",".");
	$taxa_monitoramento = $taxa_monit;

	$txtParcs="";

	$sql = "Select mModulos, pLucro, perBonus, perPart0, perPart1, Ga, mPart from Inform where id = '".$idInform."'";
	$cur = odbc_exec($db, $sql);
	
	if (odbc_fetch_row ($cur)) {
		$mBonus    =  odbc_result($cur, 1);
		$mPart     =  odbc_result($cur, 7);
		$pLucro    =  odbc_result($cur, 2);
		$perBonus  =  odbc_result($cur, 3);
		$perPart0  =  odbc_result($cur, 4);
		$perPart1  =  odbc_result($cur, 5);
		$ga        = odbc_Result($cur, 6);
	}

    if($Periodo_Vigencia){
		$pvigencia = $Periodo_Vigencia;
	}else{
		$pvigencia = ($pvigencia == "" || $pvigencia == 1 ? 12: 24);		
	}
	
	if ($t_Vencimento || $Num_Parcelas){
		  if ($numParc == 1){
			  $txtParcs .= "à vista.";
			  $periodo1 = "à vista";
		  }else{
			  $txtParcs .= $Num_Parcelas." prestações iguais e ".$t_Vencimento;
			  //$periodo = "trimestral";
			  $periodo1 = $t_Vencimento;
		  }
	} else if ($pvigencia <=12) {
			$Periodo_Vigencia   = $pvigencia;
			if ($numParc == 1){
			  $txtParcs .= "à vista.";
			  //$periodo = "à vista";
			  $periodo1 = "à vista";
			}else if ($numParc == 2){
			  $txtParcs .= "duas prestações iguais: 1 e mais 1 em noventa dias.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}else if ($numParc == 4){
			  $txtParcs .= "quatro prestações iguais: 1 e mais 3 trimestrais.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}else if ($numParc == 7){
			  $txtParcs .= "sete prestações iguais mensais.";
			  //$periodo = "mensal";
			  $periodo1 = "mensais";
			}
		
	} elseif ($pvigencia > 12){	
			if ($numParc == 1){
			  $txtParcs .= "à vista.";
			  //$periodo = "à vista";
			  $periodo1 = "à vista";
			}else if ($numParc == 4){
			  $txtParcs .= "quatro prestações iguais: 1 e mais 3 trimestrais.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}else if ($numParc == 7){
			  $txtParcs .= "sete prestações iguais mensais.";
			  //$periodo = "mensal";
			  $periodo1 = "mensais";
			}else if ($numParc == 8){
			  $txtParcs .= "oito prestações iguais trimestrais.";
			  //$periodo = "trimestral";
			  $periodo1 = "trimestrais";
			}
	}

	$x = odbc_exec($db, "SELECT a801, a502, b603, b1504, b1202, c102, d101, d602, d701, e101, f305, f3301, nivel_d602,
								p_cobertura_d701, limite_d701,adequacao_sinistralidade,adequacao_premio,franquia_anual, 
								CONVERT(varchar(1000),condicoes_especiais) AS condicoes_especiais,
								PrazoMaxEmiNota, b904, b2604Perc, b2604NivelMax, 
								b2801NivelMax, b2801Perc, d201, f401NivelSinistralidade,f401PercPremio,b802,b404NivelMax,
								b404Perc,GerenteCredito,Texto_Risco_Politico
					   FROM ModuloOferta 
					   WHERE idInform = ".$idInform);

	$mod_a801 = odbc_result($x,1);
	$mod_a502 = odbc_result($x,2);
	$mod_b603 = odbc_result($x,3);
	$mod_b1504 = odbc_result($x,4);
	$mod_b1202 = odbc_result($x,5);
	$mod_c102 = odbc_result($x,6);
	$mod_d101 = odbc_result($x,7);
	$mod_d602 = odbc_result($x,8);
	$mod_d701 = odbc_result($x,9);
	$mod_e101 = odbc_result($x,10);
	$mod_f305 = odbc_result($x,11);
	$mod_f3301 = odbc_result($x,12);
	$GerenteNome    = odbc_result($x,'GerenteCredito');
	$ad_sinistr = odbc_result($x,'adequacao_sinistralidade');
	$ad_premio = odbc_result($x,'adequacao_premio');

	$PrazoMaxEmiNota           = odbc_result($x,'PrazoMaxEmiNota');
	$mod_b904                  = number_format(odbc_result($x,'b904'),2,',','.'); 
	$mod_b904Ext               = $numberExtensive->extensive(number_format(odbc_result($x,'b904'),2,'.',''),$currency);
	$mod_b2604                 = number_format(odbc_result($x,'b2604Perc'),2,',','.');
	$b2604NivelMax             = number_format(odbc_result($x,'b2604NivelMax'),2,',','.');
	$b2604NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($x,'b2604NivelMax'),2,'.',''),$currency);

	$b2801NivelMax             = number_format(odbc_result($x,'b2801NivelMax'),2,',','.');
	$b2801NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($x,'b2801NivelMax'),2,'.',''),$currency);
	$b2801Perc                 = number_format(odbc_result($x,'b2801Perc'),2,',','.');

	$b404NivelMax              = number_format(odbc_result($x,'b404NivelMax'),2,',','.');
	$b404NivelMaxExt           = $numberExtensive->extensive(number_format(odbc_result($x,'b404NivelMax'),2,'.',''),$currency);
	$mod_b404                  = odbc_result($x,'b404Perc');
	$riscopolitico             = odbc_result($x,'Texto_Risco_Politico');

	$d201                      = number_format(odbc_result($x,'d201'),2,',','.');
	$valorExtD201              = $numberExtensive->extensive(number_format(odbc_result($x,'d201'),2,'.',''),$currency);
	$f401NivelSinistralidade   = number_format(odbc_result($x,'f401NivelSinistralidade'),2,',','.');
	$f401PercPremio            = number_format(odbc_result($x,'f401PercPremio'),2,',','.');
	$mod_b802                  = number_format(odbc_result($x,'b802'),2,',','.');

	if ($currency == "1") {
		$extM = "Real";
		$mo = "R$";
		$sifra = "R$";
	}else if ($currency == "2") {
		$extM = "dólar norte-americano";
		$mo = "USD ";
		$sifra = "US$";
	}elseif ($currency == "6") {
		$extM = "Euro";
		$mo = "€ ";
		$sifra = "€";
	}elseif ($currency == "0") {
		$extMoeda = "dólar norte-americano";
		$mo = "US$ ";
		$sifra = "US$";
	}
	  
	if ($ad_sinistr > 0 || $ad_premio > 0){
	   $ad_sinistr = $ad_sinistr;
	   $ad_premio = $ad_premio;
	   $exibe_ad = 1;

	}else {
	   $ad_sinistr = 0;
	   $ad_premio = 0;
	   $exibe_ad = 0;
	}

	$franquia_anual = odbc_result($x,'franquia_anual');

	if ($franquia_anual > 0){
	  $franquia_anual = number_format(odbc_result($x,'franquia_anual'),2,',','.') ." (".strtolower($numberExtensive->extensive(number_format(odbc_result($x,'franquia_anual'),2,'.',''),$currency)).").";
	  $exibe_franq = 1;

	}else{
		$franquia_anual = 0;
		$exibe_franq = 0;
	}

	if (odbc_result($x,'condicoes_especiais') != "" ){
		$condicoes_especiais = odbc_result($x,'condicoes_especiais') ;
		$exibe_cond = 1;
	}else{
		$condicoes_especiais = "";
		$exibe_cond = 0;
	}

	if(odbc_result($x, 8) == "1") {
		$nivel_d602 = number_format(odbc_result($x, 13),2,'.','');
		$nivel = strtolower($numberExtensive->extensive(number_format($nivel_d602,2, '.',''),$currency));
		$d602 = "O nível de pequenos sinistros é de: ".$sifra." ".formata($nivel_d602)." (".$nivel.").";
	}

	if(odbc_result($x, 9) == "1") {
		if($d602=="") {
		   $d701  = "\n\n|D7.01 LITÍGIO|";
		} else {
		   $d701  = "\n\n|D7.01 LITÍGIO|";
		}

		$p_cobertura_d701  = odbc_result($x, 14);
		$limite_d701       = odbc_result($x, 15);

		$d701 = "O percentual de cobertura é de: ".$p_cobertura_d701."% <br>".$limite_d701."";
	}

	$bonus = "";
	// ALTERAÇÃO BÔNUS
	if ($mBonus == "1") {
		$perBonus = (int)$perBonus;
		$bonus = "A percentagem de bônus referente ao item 2 deste módulo é de ".round($perBonus)."%.\n\n"; //10%(dez por cento).
	}

	$part = "";
	if ($mPart == "1") {
		if ($pLucro == "F13") {
		   $partic  = "F13.02    PARTICIPAÇÃO NOS LUCROS";
		}else if ($pLucro == "F14") {
		   $partic  = "F14.02    PARTICIPAÇÃO NOS LUCROS";
		}else if ($pLucro == "F15") {
		   $partic  = "F15.02    PARTICIPAÇÃO NOS LUCROS";
		}

		$extpart0   = $numberExtensive->porcentagem(number_format($perPart0,2,'.',''));
		$extpart1   = $numberExtensive->porcentagem(number_format($perPart1,2,'.',''));
		$part .= "Percentagem de Dedução: ".$perPart0."% (".$extpart0.")\n".
		         "Participação nos Lucros: ".$perPart1."% (".$extpart1.")\n";
		$valbo ="1";
	}

	$extnas = $mo." ".number_format($nas, 2, ',','.')." (".strtolower($numberExtensive->extensive(number_format($nas,2, '.',''),$currency)).')';

	if (odbc_result($x,'condicoes_especiais') != "" ){
		$condicoes_especiais = odbc_result($x,'condicoes_especiais') ;
		$exibe_cond = 1;
	}else{
		$condicoes_especiais = "";
		$exibe_cond = 0;
	}

	$cons = " SELECT a.idCorretor,a.p_Comissao,a.p_Corretor,a.CorretorPrincipal,b.razao, b.c_SUSEP
				from InformCorretor a
				inner join consultor b on b.idconsultor = a.idCorretor
				where a.idInform = ".$idInform. " 
				order by a.CorretorPrincipal desc";
	$resultado = odbc_exec($db, $cons);

	$pularlinha = '';
	$MultiCorretor  = '';
			
	$linhas = odbc_num_rows($resultado);
	if ($linhas){
		while (odbc_fetch_row($resultado)){
			if (odbc_result($resultado,'CorretorPrincipal') == 1){	
				$Corretor =  odbc_result($resultado,'razao');
				$codSusep = odbc_result($resultado,'c_SUSEP');
			}

			$MultiCorretor .= $pularlinha.odbc_result($resultado,'razao');
			$pularlinha = '<br>';
		} 
	} else {
		$Corretor = '';
		$codSusep = '';
	}
?>