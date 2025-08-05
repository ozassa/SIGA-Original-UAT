<?php 

    include_once("policyData.php");

	$numParc  = ($Num_Parcelas ? $Num_Parcelas : $numParc);

	if ($pvigencia != 2) {
		if ($numParc == 1){
			$txtParcs = "à vista.";
		}else if ($numParc == 2){
			$txtParcs = "em duas prestações: 1 e mais 1 em noventa dias.";
		}else if ($numParc == 4){
			$txtParcs = "em quatro prestações: 1 e mais 3 trimestrais.";
		}else if ($numParc == 7){
			$txtParcs = "em sete prestações: 1 e mais 6 mensais.";
		}else if ($numParc == 10){
			$txtParcs = "em dez prestações: 1 e mais 9 mensais.";
		}

	}else if ($pvigencia == 2) {
		if ($numParc == 1){
			$txtParcs = "à vista.";
		}else if ($numParc == 4){
			$txtParcs = "em quatro prestações: 1 e mais 3 trimestrais.";
		}else if ($numParc == 7){
			$txtParcs = "em sete prestações: 1 e mais 6 mensais.";
		}else if ($numParc == 8){
			$txtParcs = "em oito prestações: 1 e mais 7 trimestrais.";
		}

	}	  
		 
	if ($mBonus == "1") {
		$bonus = "<b>F9.02 - BÔNUS POR AUSÊNCIA DE SINISTROS<b>\n" .
					 "                     A percentagem de bônus referente ao item 2 deste módulo é ".round($perBonus)."%\n"; //10%(dez por cento).
	}else if ($mBonus == "0") {
	    $bonus = "";
	}
		   
	if ($mPart == "1") {
		if ($pLucro == "F13") {
			$part  = "F13.02 - Ao término de cada Período de Seguro.\n";
		}else if ($pLucro == "F14") {
			$part  = "F14.02 - Caso a presente apólice se mantenha vigente durante 2 (dois) Períodos de Seguro.\n";
		}else if ($pLucro == "F15") {
			$part  = "F15.02 - Caso a presente apólice se mantenha vigente durante 3 (três) Períodos de Seguro.\n";
		}

		$part .= "Percentagem de Dedução: ".round($perPart0)."% (".$extpart0.")\n".
		"Participação nos Lucros: ".round($perPart1)."% (".$extpart1.")\n";
	}
		
	if ($tipodve == "1") {
		$tipo = "mensal";
	}else if ($tipodve == "2") {
		$tipo = "trimestral";
	}else if ($tipodve == "3") {
		$tipo = "anual";
	}
			 
	if($Num_Parcelas){
		if ($numParc == 1){
			$txtParcs = "à vista";
			$periodo = "à vista";
			$periodo1 = "à vista";
		}else{
			$txtParcs = "em ".$Num_Parcelas." prestações ".$t_Vencimento.".";
			$periodo = $t_Vencimento;
			$periodo1 = $t_Vencimento;
		}
	}else{ 
		  
		if ($pvigencia != 2) {

			if ($numParc == 1) {
				$txtParcs = "à vista";
				$periodo = "à vista";
				$periodo1 = "à vista";
			}else if ($numParc == 2){
				$txtParcs = "em duas prestações: 1 e mais 1 em noventa dias";
				$periodo = "trimestral";
				$periodo1 = "trimestrais";
			}else if ($numParc == 4){
				$txtParcs = "em 4 parcela(s) iguais e trimestral(is)";
				$periodo = "trimestral";
				$periodo1 = "trimestrais";
			}else if ($numParc == 7){
				$txtParcs = "em 7 parcela(s) iguais e mensal(is)";
				$periodo = "mensal";
				$periodo1 = "mensais";
			}else if ($numParc == 10){
				$txtParcs = "em 10 parcela(s) iguais e mensal(is)";
				$periodo = "mensal";
				$periodo1 = "mensais";
			}
		}else if ($pvigencia == 2){
			if ($numParc == 1){
				$txtParcs = "à vista";
				$periodo = "à vista";
				$periodo1 = "à vista";
			}else if ($numParc == 4){
				$txtParcs = "em 4 parcela(s) iguais e trimestral(is)";
				$periodo = "trimestral";
				$periodo1 = "trimestrais";
			}else if ($numParc == 7){
				$txtParcs = "em 7 parcela(s) iguais e mensal(is)";
				$periodo = "mensal";
				$periodo1 = "mensais";
			}else if ($numParc == 8){
				$txtParcs = "em 8 parcela(s) iguais e trimestral(is)";
				$periodo = "trimestral";
				$periodo1 = "trimestrais";
			}
		}
	}
		  
	$bonus = "";
		
	if ($mBonus == "1") {
		$bonus = "A percentagem de bônus referente ao item 2 deste módulo é ".round($perBonus)."%\n"; //10%(dez por cento).				
		//$bonus = "A percentagem referente ao item 2 deste módulo e de ". $perBonus."% ".$numberExtensive->porcentagem($perBonus)."\n\n"; //10%(dez por cento).
				
	}

	if ($mPart == "1") {
		 if ($pLucro == "F13") {
			 $partic  = "<b>F13.02    PARTICIPAÇÃO NOS LUCROS</b>";
		 }else if ($pLucro == "F14") {
			 $partic  = "<b>F14.02    PARTICIPAÇÃO NOS LUCROS</b>";
		 }else if ($pLucro == "F15") {
			 $partic  = "<b>F15.02    PARTICIPAÇÃO NOS LUCROS</b>";
		 }
			  $extpart0   = $numberExtensive->porcentagem($perPart0);
			 $extpart1   = $numberExtensive->porcentagem($perPart1);
			 $part .= "Percentagem de dedução: ".round($perPart0)."% (".$extpart0.")\n".
					  "Participação nos lucros: ".round($perPart1)."% (".$extpart1.")\n";
		$valbo = "1";
	}	
	
	if ($tipodve == "1") {
	   	$tipo = "mensal";
	}else if ($tipodve == "2") {
	   $tipo = "trimestral";
	}else if ($tipodve == "3") {
	   $tipo = "anual";
	}
	
	$a502titulo = "";
	$a502 = "";
	$b1202X = "";
	$b1202 = "";	   
	
	$periodoMaxCredito = odbc_result(odbc_exec($db, "select periodMaxCred from Inform where id=$idInform"), 1);

	$jurosMora = odbc_result(odbc_exec($db, "select warantyInterest from Inform where id=$idInform"), 1);
	
	$x = odbc_exec($db, "select a502, b1202, d602, d701, nivel_d602, p_cobertura_d701, limite_d701,a801, 
					 b603, b1504, b1202, c102, d101,e101, f305, f3301,adequacao_sinistralidade,
					 adequacao_premio,franquia_anual, CONVERT(varchar(1000),condicoes_especiais) AS condicoes_especiais,
					 PrazoMaxEmiNota, b904, b2604Perc, b2604NivelMax, 
					 b2801NivelMax, b2801Perc, d201, f401NivelSinistralidade,f401PercPremio,b802, b404NivelMax,b404Perc,GerenteCredito
					 
					 from ModuloOferta where idInform=$idInform");
	// Definiçao dos modulos
	$mod_a801 = odbc_result($x,8);
	$mod_a502 = odbc_result($x,1);
	$mod_b603 = odbc_result($x,9);
	$mod_b1504 = odbc_result($x,10);
	$mod_b1202 = odbc_result($x,11);
	$mod_c102 = odbc_result($x,12);
	$mod_d101 = odbc_result($x,13);
	$mod_d602 = odbc_result($x,3);
	$mod_d701 = odbc_result($x,4);
	$mod_e101 = odbc_result($x,14);
	$mod_f305 = odbc_result($x,15);
	$mod_f3301 = odbc_result($x,16);
	$GerenteNome     = odbc_result($x,'GerenteCredito');

	$PrazoMaxEmiNota           = odbc_result($x,'PrazoMaxEmiNota');
	$mod_b904                  = number_format(odbc_result($x,'b904'),2,',','.'); 
	$mod_b904Ext               = $numberExtensive->extensive(number_format(odbc_result($x,'b904'),2,'.',''),$fMoeda);
	$mod_b2604                 = round(odbc_result($x,'b2604Perc'));
	$b2604NivelMax             = number_format(odbc_result($x,'b2604NivelMax'),2,',','.');
	$b2604NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($x,'b2604NivelMax'),2,'.',''),$fMoeda);

	$b2801NivelMax             = number_format(odbc_result($x,'b2801NivelMax'),2,',','.');
	$b2801NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($x,'b2801NivelMax'),2,'.',''),$fMoeda);
	$b2801Perc                 = round(odbc_result($x,'b2801Perc'));

	$b404NivelMax              = number_format(odbc_result($x,'b404NivelMax'),2,',','.');
	$b404NivelMaxExt           = $numberExtensive->extensive(number_format(odbc_result($x,'b404NivelMax'),2,'.',''),$fMoeda);
	$mod_b404                  = round(odbc_result($x,'b404Perc'));

	$d201                      = number_format(odbc_result($x,'d201'),2,',','.');
	$valorExtD201             = $numberExtensive->extensive(number_format(odbc_result($x,'d201'),2,'.',''),$fMoeda);
	$f401NivelSinistralidade   = round(odbc_result($x,'f401NivelSinistralidade'));
	$f401PercPremio            = round(odbc_result($x,'f401PercPremio'));
	$mod_b802                  = number_format(odbc_result($x,'b802'),2,',','.');


	$ad_sinistr = odbc_result($x,'adequacao_sinistralidade');
	$ad_premio = odbc_result($x,'adequacao_premio');
		  
	if ($ad_sinistr > 0 || $ad_premio > 0){
		$ad_sinistr = number_format($ad_sinistr,2,',','.');
		$ad_premio = number_format($ad_premio,2,',','.');
		$exibe_ad = 1;

	}else {
		$ad_sinistr = 0;
		$ad_premio = 0;
		$exibe_ad = 0;

	}
	
	$franquia_anual = odbc_result($x,'franquia_anual');
	
	if ($franquia_anual > 0){
		$franquia_anual = number_format(odbc_result($x,'franquia_anual'),2,',','.'). " (".strtolower($numberExtensive->extensive(number_format(odbc_result($x,'franquia_anual'),2,'.',''),$fMoeda)).").";
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
	
	  if(odbc_result($x, 1) == "1") {
		$a502titulo = "<b>A5.02    COBERTURA DE RISCO DE PRODUÇÃO</b> ";
		$a502 = $a502."i) A cobertura de risco de produção se aplica para todos os importadores indicados na ficha de aprovação de limites de crédito.";
	  }
	  if(odbc_result($x, 2) == "1") {
		  $b1202X = "<b>B12.02</b>    <b>EXTENSÃO DO CONTRATO A UMA OU MAIS EMPRESAS</b>   ";
		 $b1202 = $b1202."A cobertura é estendida aos contratos de vendas celebrados pelas seguintes empresas: \n\n";
		  $query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = $idInform ORDER BY no_razao_social ";
		  $cur = odbc_exec ($db, $query);
		  $i = 0;
		 while (odbc_fetch_row($cur))
		  {
			$no_razao_social       = odbc_result ($cur, 'no_razao_social');
			$nu_cnpj               = odbc_result ($cur, 'nu_cnpj');
			$nu_inscricao_estadual = odbc_result ($cur, 'nu_inscricao_estadual');
			$ds_endereco           = odbc_result ($cur, 'ds_endereco');
			$nu_cep                = odbc_result ($cur, 'nu_cep');
			$no_cidade             = odbc_result ($cur, 'no_cidade');
			$no_estado             = odbc_result ($cur, 'no_estado');
	//		$b1202 = $b1202."Razão Social: ".$no_razao_social." - CNPJ: ".$nu_cnpj." - Inscrição Estadual: ".$nu_inscricao_estadual." - Endereço: ".$ds_endereco."' - CEP: ".$nu_cep." - Cidade: ".$no_cidade." - Estado: ".$no_estado." \n";
			$i ++;
				if ($no_razao_social){
					if ($i > 1){
						  $b1202 .= "\n";
					   }
					//$modulo_final .= ''.$no_razao_social." - Endereço: ".$ds_endereco.", ".$nu_endereco."  ".$ds_complemento."\nCidade: ".$no_cidade."  Estado: ".$no_estado."  CEP: ".$nu_cep."   CNPJ: ".$nu_cnpj."  IE: ".$nu_inscricao_estadual." \n";
					$b1202 .= 'Razão Social:'.$no_razao_social."\n";
					$b1202 .= 'Endereço:'.$ds_endereco.", ".$nu_endereco." ".$ds_complemento."\nCidade: ".$no_cidade."  Estado: ".$no_estado."  CEP: ".$nu_cep."\nCNPJ: ".$nu_cnpj."\n";
					   
				}
	
	
		  }
		  //$b1202 = $b1202."\n";
	  }

   	//Verifica o período total de vigência da apólice
	$query = "select MIN(d_Vigencia_Inicial)as Ini, MAX(d_Vigencia_Final) as Fim from Periodo_Vigencia where i_Inform =".$idInform. "";

	$cur = odbc_exec ($db, $query);

	$VigIni = Convert_Data_Geral(substr(odbc_result($cur,'Ini'),0,10));
	$VigFim = Convert_Data_Geral(substr(odbc_result($cur,'Fim'),0,10));	
	
	//Condição Especial de Cobertura de Coligadas	
	$rsquery = odbc_exec($db, "select a.idInform,a.razaoSocial,a.endereco,a.zipCode, a.pais,b.name, a.taxID
							   from ParModEsp_Coligada a inner join Country b on b.id = a.pais
							   where a.idInform = $idInform ORDER BY a.razaoSocial ");
	
	while (odbc_fetch_row($rsquery)){
	   $condespcol   .= "\n";

	   $condespcol   .= " <b>".  odbc_result ($rsquery, 'razaoSocial')."</b>\n";
	   $condespcol   .= " Endereço: ".odbc_result ($rsquery, 'endereco')."\n";
	   $condespcol   .= " Pais: ". odbc_result ($rsquery, 'name')."\n Zip Code: ".odbc_result ($rsquery, 'zipCode') ."\n";
	   $condespcol   .= " Tax ID: ". odbc_result ($rsquery, 'taxID')." \n";
	}
	
	if(odbc_result($x, 3) == "1") {
		//$d602  = "\n\n|D6.02 PEQUENOS SINISTROS| \n";
		$nivel_d602 = odbc_result($x, 5);
		$nivel = strtolower($numberExtensive->extensive(number_format($nivel_d602,2, '.',''),$fMoeda));
		$d602 = $d602."O nível de pequenos sinistros é de ".$ext.number_format($nivel_d602,2,',','.')." (".$nivel.")";
	}

	if(odbc_result($x, 4) == "1") {
		if($d602=="") {
			$d701  = "\n\n|D7.01 LITÍGIO|";
		} else {
			$d701  = "\n\n|D7.01 LITÍGIO|";
		}
		$p_cobertura_d701  = odbc_result($x, 6);
		$limite_d701 = odbc_result($x, 7);

		$d701 = "O percentual de cobertura é de: ".round($p_cobertura_d701)."% <br>".$limite_d701."";
	}
							
	$sqll  = "select limPagIndeniz, v_LMI, Prazo_Nao_Intencao_Renov from Inform where id = ".$idInform. "";
	$rsxl = odbc_exec($db, $sqll);

	$limiteInden = odbc_result($rsxl, "limPagIndeniz");
	$ValorLMI = odbc_result($rsxl, 'v_LMI');
	$Prazo_Nao_Intencao_Renov = odbc_result($rsxl, 'Prazo_Nao_Intencao_Renov');

    $opt = ['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
	        'format' => 'A4',
	        'margin_left' => 20,
	        'margin_right' => 15,
	        'margin_top' => 48,
	        'margin_bottom' => 25,
	        'margin_header' => 10,
	        'margin_footer' => 10
        	];

    $mpdf=new  \Mpdf\Mpdf($opt);
	$mpdf->SetTitle("Proposta");
	$mpdf->SetAuthor($nomeEmp);
	$mpdf->SetWatermarkText("");
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.1;
	$mpdf->SetDisplayMode('fullpage');
	
	    // Endereço do logotipo
   		$logo  = $root .'images/logo.jpg';
 

        // Início do arquivo montando primeiro o CSS
   
        $html = '<html>
				<head>
				<style>
				body {font-family: Arial, Helvetica, sans-serif;
					font-size: 12pt;
				}
				p {    margin: 0pt;
				}
					
				
				ol {counter-reset: item; font-weight:bold; }
                li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 12pt; text-align:justify}
                li:before {content: counters(item, "."); counter-increment: item; }
				
				ul			{list-style-type: none; font-weight:normal } 
				ul li		{padding: 3px 0px;color: #000000;text-align:justify} 

                
				
				#cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
				#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
				#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
				
				</style>
				
				</head>
				<body>
				';
				
				
				
		 
      $html .= ' 
      		<htmlpageheader name="myheader2">
					</htmlpageheader>

					<htmlpageheader name="myheader">
						<div height="50">
						</div><br>
						<div style="text-align: left;">
						    <span style="font-weight: bold; font-size: 9pt;">CONDIÇÕES PARTICULARES - SEGURO DE CRÉDITO INTERNO</span>
						 </div>	
						 <div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>
						 <div style="text-align: left;"><span style="font-style:italic; font-size: 8pt;">'. $dados['SubProduto'] .'</span></div>
					</htmlpageheader>
						
					<htmlpagefooter name="myfooter">
						<table width="100%" border="0">
							<tr>
								<td width="22%">&nbsp;</td>
								<td width="56%" style="text-align:center; font-size: 8pt;">
										Página {PAGENO} de {nb}
										<br><br>

										'.$enderecoEmp.'<br>
										'.$siteEmp.'

								</td>
	
								<td width="22%">&nbsp;</td>
							</tr>
						</table>
							
					</htmlpagefooter>
					<sethtmlpageheader name="myheader2" value="on" show-this-page="1" />
					<sethtmlpageheader name="myheader" value="on" show-this-page="0" />
					<sethtmlpagefooter name="myfooter" value="on" />

					
  					<div style="text-align: left;">
						  <span style="font-weight: bold; font-size: 10pt;">CONDIÇÕES PARTICULARES - SEGURO DE CRÉDITO INTERNO</span>
	    			</div>	
						<div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; ">
							<div style="text-align: left;"><span style="font-style:italic; font-size: 8pt;">'. $dados['SubProduto'] .'</span></div>
							<div style="text-align: left;">
								<span style="font-style:italic; font-size: 7pt;">RISCO COMERCIAL</span>
							<div>
							<div style="text-align: right;">
								<span style="font-style:italic; font-size: 7pt;">APÓLICE NÚMERO : <strong>'.$apoNum.'</strong></span>
							<div>
							<div style="text-align: right;">
								<span style="font-style:italic; font-size: 8pt;">VIGÊNCIA: '.$VigIni.' - '.$VigFim.'</span>
							<div>
						</div>

						<br>
						<div style="text-align: left;">
							<span style="font-weight: bold; font-size: 12pt;"><u>DADOS DO SEGURADO:</u></span><br>
						</div>
						<table width="100%" border="0" style="font-size: 12pt;">			
						<tr>
						   <td width="25%">Razão Social: </td><td width="75%"><span style="font-weight: bold; font-size: 12pt; text-align:justify;">'.strtoupper(trim($name)).'</span></td>
						</tr>
						<tr>
						  <td width="25%">Endereço: </td><td width="75%" style="text-align:justify">'.$endcompleto.'</td>
						</tr> 
						<tr>
						  <td width="25%">&nbsp;</td><td width="75%" style="text-align:justify">'.$city .' - '. $uf.'</td>
						</tr>
						<tr>  
						  <td width="25%">&nbsp;</td><td width="75%" style="text-align:justify">CEP '.$cep.'</td>
						</tr>
						<tr>
						  <td width="25%">CNPJ: </td><td width="75%" style="text-align:justify">'. formata_string('CNPJ',$cnpj).'</td>			
						</tr>
						<tr>
						  <td width="25%">Corretor nomeado: </td><td width="75%" style="text-align:justify">'.($MultiCorretor != ''? $MultiCorretor: $corretor).'</td>			
						</tr>
						</table> 
						
						<br>
						<div style="border-top: 1px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>
						<br>
						
						
						 <div id="sublinhado">COBERTURA:</div>					
						 
						 <table width="100%" border="0" style="font-size: 12pt;">
					    <tr>
					       <td colspan="1" width="8%" style="font-weight:bold">1.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">ABRANGÊNCIA DESTE CONTRATO – PERCENTAGEM SEGURADA</td>			
					    </tr>
						<tr>
					       <td colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
						   <td colspan="2" width="92%" style="font-weight:bold"> - CUSTO DA COBERTURA</td>			
					    </tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="1" width="8%" style="font-weight:bold">1.1</td>
							   <td  colspan="1" width="84%" style="font-weight:bold">NATUREZA DAS VENDAS SEGURADAS</td>			
							</tr>
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
							   <td colspan="2" width="92%" style="text-align:justify">'.$DescrNatureza.'</td>			
							</tr>

							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>		

							<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="1" width="8%" style="font-weight:bold">1.2</td>
						   <td  colspan="1" width="84%" style="font-weight:bold">PRÊMIO MÍNIMO</td>			
					    </tr>
						<tr>
					       <td colspan="1" width="8%">&nbsp;</td>
						   <td colspan="2" width="92%" style="text-align:justify">'.$ext . number_format($pr, 2, ',', '.'). ' ('.strtolower($valExt).') por período de seguro (IOF de 7,38% não incluso).</td>					
					    </tr>			

							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>			

						<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="1" width="8%" style="font-weight:bold">1.3</td>
						   <td  colspan="1" width="84%" style="font-weight:bold">PERCENTAGEM SEGURADA</td>			
					    </tr>
						<tr>
					       <td colspan="1" width="8%">&nbsp;</td>
						   <td colspan="2" width="92%" style="text-align:justify">'.round($cobertura).'% (ICMS, IPI, ISS e demais tributos incluídos no valor total da nota fiscal de venda ou serviço prestado).</td>			
					    </tr>

							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>		
						
						<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="1" width="8%" style="font-weight:bold">1.4</td>
						   <td  colspan="1" width="84%" style="font-weight:bold">TAXA DE PRÊMIO</td>			
					    </tr>
						<tr>
					       <td colspan="1" width="8%">&nbsp;</td>
						   <td colspan="2" width="92%" style="text-align:justify">'.number_format($tx,4,",",".").'%  aplicável ao faturamento (ICMS, IPI, ISS e demais tributos incluídos no valor total da nota fiscal de venda ou serviço prestado).</td>			
					    </tr>

						<tr>
						 <td colspan="3">&nbsp;</td>
						</tr>

							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">2.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">LIMITE MÁXIMO DE INDENIZAÇÃO</td>			
							</tr>';

					    if ($limiteInden != 0) {
					    	$html .= '
						    <tr>
						      <td  colspan="1" width="8%">&nbsp;</td>
							   	<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'.$limiteInden .' vezes o prêmio pago por período de seguro.</div></td>
						    </tr>';
					    }

					    if ($ValorLMI != 0) {
					    	$ValorLMIExt = $numberExtensive->extensive(number_format($ValorLMI,0,'.',''),$currency);
					    	$html .= '
						    <tr>
						      <td  colspan="1" width="8%">&nbsp;</td>
							   	<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'.$mo.' '. number_format($ValorLMI, 2, ",", ".").' ('.$ValorLMIExt.').</div></td>
						    </tr>';
					    }
					    	
					    $html .= '
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
						
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">3.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">PRAZO MÁXIMO DE CRÉDITO</td>			
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%" style="text-align:justify">'.$periodoMaxCredito.' dias contados a partir da data da emissão da fatura da mercadoria vendida ou serviço prestado.</td>
										
							</tr>

							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>									
							
							<tr>
					       <td colspan="1" width="8%" style="font-weight:bold">4.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">PRAZO MÁXIMO PARA EMISSÃO DA NOTA FISCAL</td>			
					    </tr>
						<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="2" width="92%" style="text-align:justify">
						             Não é aplicável para as vendas de mercadorias.<br>';
						             

							if($PrazoMaxEmiNota > 0){
								$html .= 'Prestação de serviços: '.$PrazoMaxEmiNota.' dias, contado a partir da data da prestação do serviço para qual o pagamento seja devido. (somente quando tem serviços contratados)';
							}					             
						        
						   $html .= '</td>
						   			
					    </tr>	
				        
						<tr>
						 <td colspan="3">&nbsp;</td>
						</tr>
						<tr>
					       <td colspan="1" width="8%" style="font-weight:bold">5.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">PRAZO PARA NOTIFICAR A AMEAÇA DE SINISTRO</td>			
					    </tr>
						<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="2" width="92%" style="text-align:justify">'. ($periodoMaxCredito + 30).' dias contados a partir da data da emissão da fatura da mercadoria vendida ou serviço prestado. <br>
                              No caso de prorrogação da data de vencimento, dentro das condições constantes no item 2.2.3 da cláusula 2 das CONDIÇÕES GERAIS, o prazo é de 30 dias contados do novo vencimento.
                           </td>
					    </tr>
						
						    <tr>
								<td colspan="3">&nbsp;</td>
							  </tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">6.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">MOEDA DA APÓLICE</td>			
							</tr>						
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%" style="text-align:justify"><div id="cobtexto">A moeda da apólice é o '. $extmoeda.'</div></td>
										
							</tr>
							
							
						';
							$countNumber = 7; 
							
						
							$html .= '<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">'.($countNumber++).'.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">VIGÊNCIA DA APÓLICE</td>			
							</tr>';
			   
							  // verificações 
					          	// INTERAKTIV 18/06/2014
								$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
								$rres = odbc_exec($db, $sqlvig);
								$num  = 1;
								$indx = 0;
								while(odbc_fetch_row($rres)){
									$indx++;
								}

								if($num){
									$ssqx = "select MIN(d_Vigencia_Inicial)as Ini, MAX(d_Vigencia_Final) as Fim from Periodo_Vigencia where i_Inform =".$idInform. "";
									$rresx = odbc_exec($db, $ssqx);
									$html .= '<tr>
												<td colspan="1" width="8%">&nbsp;</td>
												<td colspan="2" width="92%" style="text-align:justify;">
												<div id="cobtexto">
												A apólice entra em vigor no dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Ini'),0,10)).' e terá validade até o dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Fim'),0,10)).', cujo período equivale ao período de seguro.
												<br><br></div>';
									
									//$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).')<br></div>';

									$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
									$rres = odbc_exec($db, $sqlvig);
									$indx1 = 0;
									
									if ($indx > 1) {										
										while(odbc_fetch_row($rres)){
											if ($indx1 == 0) {
												$html .= '<div id="cobtexto">Este período de seguro é dividido em '.trim(strtolower(valor_extenso($indx))).' períodos distintos de vigência compreendidos entre:<br></div>';
											}

											$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
											$html .= '<div id="cobtexto">Período '.odbc_result($rres,'n_Preriodo').' – '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'<br></div>';
											$indx1++;
										}
										
										if ($indx1 > 0) {
											$html .= '<br>';
										}
									}

									if($renovacao_Tacica == 1){ // campo no inform
								    	$html .= '
											<div id="cobtexto">
											A apólice poderá ser renovada automaticamente, por igual período de seguro, caso não seja comunicada a intenção de não renovação por qualquer das partes, devendo tal comunicação de não renovação ser feita por escrito pelo SEGURADO ou pela SEGURADORA com antecedência de '.$Prazo_Nao_Intencao_Renov.' dias do termo final do período de seguro total. </div>
											</td>			
											</tr>
										';
									}else{
										$html .= '	 
											<div id="cobtexto"><br>
											Revoga-se o item 12.2 das Condições Gerais da apólice o qual passará a vigorar com a seguinte redação:</div>
											<div id="cobtexto"><br>
											"A apólice tem duração definida nas CONDIÇÕES PARTICULARES e não pode ser renovada tacitamente."</div>
											</td>			
									   		</tr>
								     	';
									
									}
								}else{				   
							   
									$html .= '<tr>
								       	<td  colspan="1" width="8%">&nbsp;</td>
										<td  colspan="2" width="92%" style="text-align:justify;">
										<div id="cobtexto">A apólice entra em vigor no dia '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)).' e terá validade até o dia '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)).', cujo período equivalente ao período de seguro.<br></div>';
								
										if($renovacao_Tacica ==1){ // campo no inform
										    $html .= '
												<div id="cobtexto">
												A apólice poderá ser renovada automaticamente, por igual período de seguro, caso não seja comunicada a intenção de não renovação por qualquer das partes, devendo tal comunicação de não renovação ser feita por escrito pelo SEGURADO ou pela SEGURADORA com antecedência de '.$Prazo_Nao_Intencao_Renov.' dias do termo final do primeiro período de seguro. </div>
												</td>			
												</tr>
										';
										}else{
											 $html .= '	 
												<div id="cobtexto"><br>
												Revoga-se o item 12.2 das Condições Gerais da apólice o qual passará a vigorar com a seguinte redação:</div>
												<div id="cobtexto"><br>
												"A apólice tem duração definida nas CONDIÇÕES PARTICULARES e não pode ser renovada tacitamente."</div>
												</td>			
											   	</tr>
										     ';
											
										}
								
								}			
							
							$html .= '							
							<tr>
							
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">'.($countNumber++).'.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">MÓDULOS</td>			
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%">Os seguintes módulos fazem parte integrante deste contrato:</td>						   			
							</tr>
							</table>';
												
							
			       $html .= '<table width="100%" border="0" style="font-size: 12pt;"> ';
				   
			       odbc_close($db);
						//Novo formato de módulos
						    						//Início da exibição dos módulos
						
						$qryM = "select a.i_Modulo, b.Cod_Modulo,b.Grupo_Modulo,b.Titulo_Modulo,
								cast(a.Desc_Modulo as nvarchar(3900)) as Desc_Modulo
								from Inform_Modulo a inner join Modulo b on a.i_Modulo = b.i_Modulo 
								where a.idInform = ". $idInform. " order by b.Ordem_Modulo";	
								
						$mod = odbc_exec($db,$qryM);
						
						$GrupoModulo = "";
						
						while(odbc_fetch_row($mod)){ 
						
						           $descricao_Modulo = odbc_result($mod,'Desc_Modulo');
								   
						       if(odbc_result($mod,'Grupo_Modulo') != $GrupoModulo){
									   $GrupoModulo  = odbc_result($mod,'Grupo_Modulo');
									   $html .= '<tr>
												   <td colspan="4">&nbsp;</td>
											   </tr>
											   <tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="3" width="92%" style="font-weight:bold; text-align:justify;"><div id="sublinhado">'.odbc_result($mod,'Grupo_Modulo').'</div></td>
											   </tr>';
								   }
								   
								    $Titulo = strlen(odbc_result($mod,'Titulo_Modulo'));
						            $html .= '<tr>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="1" width="8%" style="font-weight:bold; text-align:justify;">'.odbc_result($mod,'Cod_Modulo').'</td>
										   <td  colspan="1" width="76%" style="font-weight:bold; text-align:justify;">'.odbc_result($mod,'Titulo_Modulo').'</td>						   			
							                </tr>';
											/*
											if($Titulo > 47){
											   '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td colspan="1" width="8%">&nbsp;</td>
												   <td colspan="1" width="76%" style="font-weight:bold">'.substr(odbc_result($mod,'Titulo_Modulo'),47,$Titulo).'</td>
												</tr>';
											}
											*/
											
								
											   
									if(odbc_result($mod,'Cod_Modulo') == "B4.04"){
										 $html .= '									  									  
										  <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">
											   - O valor de limite de crédito máximo referente da cláusula 1 deste módulo é de R$ '.$b404NivelMax.' ('.$b404NivelMaxExt.').<br>
											   - A percentagem segurada para estes compradores é de '.round($mod_b404).'% (ICMS, IPI e ISS incluídos).</td>
										  </tr>
										  <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">
										        O nome do Gerente de crédito é <strong>'.$GerenteNome.'.</strong><br>
                                                O procedimento de gerenciamento de crédito está anexado ao presente contrato.
										  </tr> ';
										
									}else if (odbc_result($mod,'Cod_Modulo') == "B8.02"){
										
									    										 
										 $query = "SELECT * FROM ParModEsp_Maiores_Compradores WHERE idInform = '".$idInform."' ORDER BY Nome ";
                                         $cury = odbc_exec ($db, $query);
                                    
									 
												if ($cury){ 
												   $html .= '								   
														  <tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%">											   
														   <table>';
														   
															while (odbc_fetch_row($cury)){				
																$NomeE                = odbc_result ($cury, 'Nome');
																$CNPJE                = odbc_result ($cury, 'CNPJ');
																$i ++;
																$emp .= '<tr>
																           <td colspan="2"><b>Nome Empresa: </b>'.$NomeE.'</td>
																		 </tr>
																		 <tr>
																		   <td colspan="2"><b>CNPJ: </b>'.$CNPJE.'</td>																		
																		</tr>';
																
															}
															
															  $html .= $emp.'</table>
														     </td>
													      </tr>';
												 }
										
									
									}else if(odbc_result($mod,'Cod_Modulo') == "B9.04"){
										 $html .= ' <tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%" style="text-align:justify;">A franquia de maiores compradores mencionada no item 1.1 deste módulo é de R$ '.$mod_b904.' ('.$mod_b904Ext.').</td>
												  </tr>';
										
									}else if(odbc_result($mod,'Cod_Modulo') == "B12.02"){
									     $query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = '".$idInform."' ORDER BY no_razao_social ";
                                              $cury = odbc_exec ($db, $query);
										  if ($cury){ 
										      $html .= '								   
												  <tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%">											   
												   <table>';
												   
													while (odbc_fetch_row($cury)){				
														$no_razao_social       = odbc_result ($cury, 'no_razao_social');
														$nu_cnpj               = odbc_result ($cury, 'nu_cnpj');
														$nu_inscricao_estadual = odbc_result ($cury, 'nu_inscricao_estadual');
														$ds_endereco           = odbc_result ($cury, 'ds_endereco');
														$nu_endereco           = odbc_result ($cury, 'nu_endereco');
														$ds_complemento        = odbc_result ($cury, 'ds_complemento');
														$nu_cep                = odbc_result ($cury, 'nu_cep');
														$no_cidade             = odbc_result ($cury, 'no_cidade');
														$no_estado             = odbc_result ($cury, 'no_estado');
														$i ++;
														$empre .= '<tr><td colspan="2"><b>'.$no_razao_social.'</b></td></tr>
																   <tr><td><b>CNPJ   : </b>'.$nu_cnpj.'</td><td><b>IE:</b> '.$nu_inscricao_estadual.'</td></tr>
																   <tr><td colspan="2"><b>Endereço: </b>'.$ds_endereco.', '.$nu_endereco. ($ds_complemento != "" ? " - ".$ds_complemento : "").'</td></tr>
																   <tr><td colspan="1"><b>Cep: </b>'.$nu_cep.'</td><td></b>Cidade: </b>'.$no_cidade.' - <b>UF: </b>'.$no_estado.'</b></td></tr>';
														
											        }
													
													  $html .= $empre.'</table>
												   </td>
											   </tr>';	
										
										  }
									
									
									}else if (odbc_result($mod,'Cod_Modulo') == "B26.04"){								       
							            $html .= '									  									  
										  <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">- O nível máximo de crédito referente à cláusula 1 deste módulo é de R$ '.$b2604NivelMax.' ('.$b2604NivelMaxExt.').
											   <br>
											   <br>
											   - A percentagem segurada referente à cláusula 1.2 deste módulo é de '.round($mod_b2604).'%.</td>
										 </tr> ';
							
						      } else if (odbc_result($mod,'Cod_Modulo') == "B28.01"){
						    
							             $html .= '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%" style="text-align:justify;">
													 O nível máximo de crédito referente à cláusula 1 deste módulo é de  R$ '.$b2801NivelMax.' ('.$b2801NivelMaxExt.').<br/>
													As empresas de informações comerciais referentes à cláusula 1.4 deste módulo são SERASA e SCI EQUIFAX.<br/>
													A percentagem segurada referente à cláusula 1.6 deste módulo é de '.round($b2801Perc).'% (ICMS, IPI e ISS incluídos).<br/><br/>
											
													Inclui-se na Cláusula 1-ÁREA DE LIMITE DE CRÉDITO DISCRICIONÁRIO, deste mesmo Módulo o item 1.7, com a seguinte redação:<br></br/> 
													
													1.7 – O SEGURADO não poderá conceder crédito  a comprador que, anteriormente ao faturamento da mercadoria ou prestação de serviço, tenha sido objeto de recusa total, redução ou cancelamento de Limite de Crédito por parte da SEGURADORA, na vigência de qualquer apólice emitida pela SEGURADORA a favor do SEGURADO.”   
	
												   </td>
											 </tr>';
												
									}else if(odbc_result($mod,'Cod_Modulo') == "D1.01"){
										    
											 $html .= ' <tr>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="2" width="84%" style="text-align:justify;">O limite mínimo para notificação de ameaça de sinistro é de '.$extnas.'</td>
											           </tr>';
									
									    
									
									}else if(odbc_result($mod,'Cod_Modulo') == "D2.01"){	
										$html .= '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%" style="text-align:justify;">A franquia anual global é de '.$ext.' '. $d201.' ('.$valorExtD201.')<br></td>
										 </tr>';
									
									}else if(odbc_result($mod,'Cod_Modulo') == "D4.01"){
										 $html .= '<tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">A franquia anual global é de '.$ext.' '. $franquia_anual.'<br></td>
									      </tr>';
										
									}else if(odbc_result($mod,'Cod_Modulo') == "D6.02"){
										 $html .= '<tr>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="2" width="84%" style="text-align:justify;">'.$d602.'</td>
										    </tr>';
											
									}else if(odbc_result($mod,'Cod_Modulo') == "D7.01"){
									    $html .= '<tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">'.$d701.'</td>
									     </tr>';	
										 
									}else if (odbc_result($mod,'Cod_Modulo') == "F3.05"){
									        if ($numParc == 1){
												 $html .= ' <tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%" style="text-align:justify;">
															  <div id="cobtexto">O período de declaração é '.$periodo.'<br>
																	<div id="cobtexto">A forma de declaração é volume total de negócios aberto por número 
																	de fatura comercial, importador e valor.<br></div>
																	<div id="cobtexto">O prêmio mínimo será pago em 01 parcela à vista.</div></td>
																
														   </tr>';
															   
												
											  }else{
												 $html .= '<tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%" style="text-align:justify;">
														   <div id="cobtexto">O período de declaração é '.$periodo.'.<br>
																A forma de declaração é em arquivo Excel contendo o volume total de negócios aberto por comprador.<br/> 
																O prêmio mínimo será pago em '.$numParc.' parcelas iguais e '.$periodo1.'</div>
														   </td>
														   </tr>';
								
											  }
									
									}else if (odbc_result($mod,'Cod_Modulo') == "F4.01"){
											$sql_f401 = "Select
																			IsNull(IMVI.v_1, 0) As Perc_Sinistralidade_Inicial,
																			IMV.v_1 As Perc_Sinistralidade_Final,
																			IMV.v_2 As Perc_Adequacao
																		From
																			Inform_Modulo_Valores IMV
																		Left Join Inform_Modulo_Valores IMVI On
																			IMVI.i_Modulo_Valor = (Select Top 1 IMVV.i_Modulo_Valor From Inform_Modulo_Valores IMVV
																				Where IMVV.i_Inform = IMV.i_Inform And IMVV.i_Modulo = IMV.i_Modulo And IMVV.v_1 < IMV.v_1
																				Order By IMVV.v_1 Desc)
																		Where
																			IMV.i_Inform = ".$idInform."
																			And IMV.i_Modulo = 24	-- Módulo F4.01
																		Order By
																			IMV.v_1
																		";
											$ex_sql_f401 = odbc_exec($db, $sql_f401);

											$count_f401 = 0;
											$txt_sinist_f401 = array();
											$txt_percent_f401 = array();
											while (odbc_fetch_row($ex_sql_f401)){
												$sinist_inicial_f401 = odbc_result($ex_sql_f401, 'Perc_Sinistralidade_Inicial');
												$sinist_final_f401 = odbc_result($ex_sql_f401, 'Perc_Sinistralidade_Final');
												$perc_adequacao_f401 = odbc_result($ex_sql_f401, 'Perc_Adequacao');

												if ($sinist_inicial_f401 != 0 && $sinist_final_f401 != 0) {
													$txt_sinist_f401[] = 'Se nível de sinistralidade for maior ou igual a '.round($sinist_inicial_f401).'% e menor que '.round($sinist_final_f401).'%';
													$txt_percent_f401[] = 'A percentagem de prêmio é '.round($perc_adequacao_f401).'%.';
												} elseif($sinist_final_f401 != 0){
													$txt_sinist_f401[] = 'Se nível de sinistralidade for menor que '.round($sinist_final_f401).'%';
													$txt_percent_f401[] = 'A percentagem de prêmio é '.round($perc_adequacao_f401).'%.';
												}

												$count_f401++;
											}

											for ($i=0;$i<$count_f401;$i++) { 
											  $html .= '<tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">'.$txt_sinist_f401[$i].'<br/></div>
																								<div id="cobtexto">'.$txt_percent_f401[$i].'</div></td>
																 </tr>';
												if($count_f401 != ($i+1)){
													$html .= '<br>';
												}
											}
											
									}else if(odbc_result($mod,'Cod_Modulo') == "F9.02"){
									      $html .= '<tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">'.$bonus.'</td>
											 </tr>';	
									}else if (odbc_result($mod,'Cod_Modulo') == "F13.02"){
									    $html .= '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%" style="text-align:justify;">A percentagem referente ao item a deste módulo é de '.round($perPart0).'%
														   <BR>A percentagem referente ao item b deste módulo é de '.round($perPart1).'%</td>
												 </tr> '; 
												
									}else if (odbc_result($mod,'Cod_Modulo') == "F33.01"){
										  $html .= '
													<tr>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="2" width="84%" style="text-align:justify;">A tarifa de análise cadastral é de '.$ext.' '. $taxa_analise.' ('.strtolower($extAnalise).')<br>
															   A tarifa de monitoramento cadastral é de '.$ext.' '. $taxa_monit.' ('.strtolower($extMonit).')
														</td>
													</tr>';
												  
									}else if (odbc_result($mod,'Cod_Modulo') == "F37.02"){						  
							  
										  $html .= '										 
											 <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">A forma de notificação é volume total de negócios aberto por nota fiscal.<br/>O período de declaração é mensal.</td>
											 </tr>';
									
									}else if (odbc_result($mod,'Cod_Modulo') == "F52.02"){
										$sql_f5202 = "Select
																			IMV.v_1 As Perc_Sinistralidade_Inicial,
																			IsNull(IMVF.v_1, 0) As Perc_Sinistralidade_Final,
																			IMV.v_2 As Perc_Adequacao
																		From
																			Inform_Modulo_Valores IMV
																		Left Join Inform_Modulo_Valores IMVF On
																			IMVF.i_Modulo_Valor = (Select Top 1 IMVV.i_Modulo_Valor From Inform_Modulo_Valores IMVV
																				Where IMVV.i_Inform = IMV.i_Inform And IMVV.i_Modulo = IMV.i_Modulo And IMVV.v_1 > IMV.v_1
																				Order By IMVV.v_1 Asc)
																		Where
																			IMV.i_Inform = ".$idInform."
																			And IMV.i_Modulo = 34	-- Módulo F52.02
																		Order By
																			IMV.v_1 
																		";
										$ex_sql_f5202 = odbc_exec($db, $sql_f5202);
										
										while (odbc_fetch_row($ex_sql_f5202)){
											$sinist_inicial_f5202 = odbc_result($ex_sql_f5202, 'Perc_Sinistralidade_Inicial');
											$sinist_final_f5202 = odbc_result($ex_sql_f5202, 'Perc_Sinistralidade_Final');
											$perc_adequacao_f5202 = odbc_result($ex_sql_f5202, 'Perc_Adequacao');
											$txt_sinist_f5202 = '';
											$txt_percent_f5202 = '';

											if ($sinist_inicial_f5202 != 0 && $sinist_final_f5202 != 0) {
												$txt_sinist_f5202 = 'Se o percentual de sinistralidade for maior que '.round($sinist_inicial_f5202).'% e menor que '.round($sinist_final_f5202).'%';
												$txt_percent_f5202 = 'Taxa de adequação de prêmio '.round($perc_adequacao_f5202).'%';
											} elseif($sinist_inicial_f5202 != 0){
												$txt_sinist_f5202 = 'Se o percentual de sinistralidade for maior ou igual que '.round($sinist_inicial_f5202).'%';
												$txt_percent_f5202 = 'Taxa de adequação de prêmio de '.round($perc_adequacao_f5202).'%';
											}

										  $html .= '<tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">'.$txt_sinist_f5202.'<br/></div>
																								<div id="cobtexto">'.$txt_percent_f5202.'</div></td>
																 </tr><br>';
										}

										$html .= '<tr>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">
															   	Fica estabelecido que o cálculo da adequação de prêmio nos termos acima mencionados será realizado e cobrado a cada 12 meses de  vigência da Apólice.
															   </td>
															 </tr>';		
									}
									
									
									
									
									if (odbc_result($mod,'Desc_Modulo') != ''){
										$html .= '
										     <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify">
												'. nl2br($descricao_Modulo).'
												</td>
											</tr>';
										
									}
									$html .= '<tr>
										        <td colspan="4">&nbsp;</td>
									         </tr>';
						
						} // Fim do módulos
				   
				   
			      
				   
				  // $html .= '</table>
				  
						//   <br>
						//   <table width="100%" border="0" style="font-size: 12pt;">
						// 	  <tr>
						// 		 <td  colspan="1" width="8%" style="font-weight:bold">'.($countNumber++).'.</td>
						// 		 <td  colspan="1" width="92%" style="font-weight:bold">OBRIGAÇÃO DE NOTIFICAÇÃO DE ATRASOS DE PAGAMENTO<td>
						// 	  </tr>';
						   
				  // $html .= '
						//   <tr>
						// 	  <td  colspan="2" width="100%" style="text-align:justify; font-size: 12pt;">
						// 		  Sem prejuízo das demais obrigações do contrato de seguro de crédito interno, o SEGURADO compromete-se a notificar a SEGURADORA de quaisquer dívidas vencidas e não pagas há mais de 60 dias 
						// 		  da data original de vencimento. Esta obrigação não se aplica se, para aquela dívida, uma <b>notificação</b> de <b>ameaça de sinistro</b> já tiver sido feita ou 
						// 		  para dívidas que já reúnam condições de uma <b>notificação</b> de <b>ameaça de sinistro</b> de acordo com  os termos do contrato de seguro.
						// 		  Caso o SEGURADO deixe de cumprir com esta obrigação, aplicar-se-á a cláusula 9.4 das CONDIÇÕES GERAIS tanto para as dívidas objeto de <b>notificação</b> de atraso de pagamento como para as dívidas correspondentes 
						// 		  a <b>entregas</b> ou prestações de serviço realizados após a devida data de obrigação de <b>notificação</b> de atraso de pagamento.
								 
						// 	  </td>
						//    </tr>';
						  $html .= '</table>';

			      	/*if($exibe_ad == 1){
			      		$ad_sinistr = (int)$ad_sinistr;
								$ad_premio = (int)$ad_premio;
			          	$html .= '
				    					      <table width="100%" border="0" style="font-size: 12pt;">
															<tr>
																<td colspan="4">&nbsp;</td>
															</tr>

															<tr>
																<td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
																<td  colspan="3" width="92%" style="font-weight:bold">ADEQUAÇÃO DE PRÊMIO</td>
															</tr>';
														            
												          	$html .= '
															<tr><td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
																<td  colspan="3" width="92%" style="text-align:justify;">
																	Caso o valor das indenizações pagas durante o período de seguro superar a percentagem de sinistralidade de '.$ad_sinistr.'% do prêmio emitido 
																	correspondente ao mesmo período de seguro, um prêmio complementar será faturado.<br><br>		          
												          Este prêmio complementar será calculado retroativamente ao início da apólice, aplicando o percentual de '.$ad_premio.'% ao prêmio emitido durante o 
												          período de seguro (excluído IOF - 7,38%).<br><br>
												          O faturamento e cobrança deste prêmio complementar não impedirão uma eventual revisão da taxa para período de seguro seguinte.<br>
																</td>
															</tr>';
															$countNumber++;
			      	}*/
						  $html .= '</table>';
								
					
					
	         
			 
						$html .= '</body>
						</html>';	

   	$html = utf8_encode($html);
   	$mpdf->allow_charset_conversion = true;
   	$mpdf->charset_in = 'UTF-8';
   	$mpdf->WriteHTML($html);

   	$mpdf->Output($pdfDir.$key.$arq_name, \Mpdf\Output\Destination::FILE);
?>