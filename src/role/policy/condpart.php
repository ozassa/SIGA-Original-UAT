<?php 
//Alterado por Tiago V N - Elumini - 16/01/2006
//Alterado por Tiago V N - Elumini - 13/02/2006

function formata($numero){
	if(strpos($numero,'.')!=''){
		$var=explode('.',$numero);
		
		if(strlen($var[0])==4){
			$parte1=substr($var[0],0,1);
			$parte2=substr($var[0],1,3);
			
			if(strlen($var[1])<2){
				$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
			}else{
				$formatado=$parte1.'.'.$parte2.','.$var[1];
			}
		}elseif(strlen($var[0])==5){
			$parte1=substr($var[0],0,2);
			$parte2=substr($var[0],2,3);
		
			if(strlen($var[1])<2){
				$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
			}else{
				$formatado=$parte1.'.'.$parte2.','.$var[1];
			}
		}elseif(strlen($var[0])==6){
			$parte1=substr($var[0],0,3);
			$parte2=substr($var[0],3,3);
		
			if(strlen($var[1])<2){
				$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
			}else{
				$formatado=$parte1.'.'.$parte2.','.$var[1];
			}
		}elseif(strlen($var[0])==7){
			$parte1=substr($var[0],0,1);
			$parte2=substr($var[0],1,3);
			$parte3=substr($var[0],4,3);
		
			if(strlen($var[1])<2){
				$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
			}else{
				$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
			}
		}elseif(strlen($var[0])==8){
			$parte1=substr($var[0],0,2);
			$parte2=substr($var[0],2,3);
			$parte3=substr($var[0],5,3);
		
			if(strlen($var[1])<2){
				$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
			}else{
				$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
			}
		}elseif(strlen($var[0])==9){
			$parte1=substr($var[0],0,3);
			$parte2=substr($var[0],3,3);
			$parte3=substr($var[0],6,3);
			
			if(strlen($var[1])<2){
				$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
			}else{
				$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
			}
		}elseif(strlen($var[0])==10){
			$parte1=substr($var[0],0,1);
			$parte2=substr($var[0],1,3);
			$parte3=substr($var[0],4,3);
			$parte4=substr($var[0],7,3);
	
			if(strlen($var[1])<2){
				$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1].'0';
			}else{
				$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1];
			}
		}else{
			if(strlen($var[1])<2){
				$formatado=$var[0].','.$var[1].'0';
			}else{
				$formatado=$var[0].','.$var[1];	
			}
		}
	}else{
		$var=$numero;
		
		if(strlen($var)==4){
			$parte1=substr($var,0,1);
			$parte2=substr($var,1,3);
			$formatado=$parte1.'.'.$parte2.','.'00';
		}elseif(strlen($var)==5){
			$parte1=substr($var,0,2);
			$parte2=substr($var,2,3);
			$formatado=$parte1.'.'.$parte2.','.'00';
		}elseif(strlen($var)==6){
			$parte1=substr($var,0,3);
			$parte2=substr($var,3,3);
			$formatado=$parte1.'.'.$parte2.','.'00';
		}elseif(strlen($var)==7){
			$parte1=substr($var,0,1);
			$parte2=substr($var,1,3);
			$parte3=substr($var,4,3);
			$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
		}elseif(strlen($var)==8){
			$parte1=substr($var,0,2);
			$parte2=substr($var,2,3);
			$parte3=substr($var,5,3);
			$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
		}elseif(strlen($var)==9){
			$parte1=substr($var,0,3);
			$parte2=substr($var,3,3);
			$parte3=substr($var,6,3);
			$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
		}elseif(strlen($var)==10){
			$parte1=substr($var,0,1);
			$parte2=substr($var,1,3);
			$parte3=substr($var,4,3);
			$parte4=substr($var,7,3);
			$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.'00';	
		}else{
			$formatado=$var.','.'00';
		}
	}

	return $formatado;
}

// Começa aqui o pdf
include_once("../../../config.php");
// Emitir pdf pelo PHP diretamente
  
include_once("../../../gerar_pdf/MPDF45/mpdf.php");
   
if ($moeda == "2") {
	$ext = "US$ ";
	$DescMoeda = "DÓLARES NORTE-AMERICANOS";
	$extmoeda = "dólar norte-americano (US$)";
	$fMoeda = "2";
}else if ($moeda == "6") {
	$ext = "€ ";
	$DescMoeda = "EUROS";
	$extmoeda = "euro (€)";
	$fMoeda = "6";
}else if ($moeda == "1") {
	$ext = "R$ ";
	$DescMoeda = "REAIS";
	$extmoeda = "real (R$)";
	$fMoeda = "1";
}

$numParc  = ($Num_Parcelas ? $Num_Parcelas : $numParc); 
	 
if ($ga=="0" || $ga==""){
	if(! file_exists($prefix. "CondPart.pdf")){
		if($Num_Parcelas){
			if ($Num_Parcelas == 1){
				$txtParcs = "à vista.";
			}else{
				$txtParcs = "em ". $Num_Parcelas. " prestações ".$t_DescVencimento.".";
			}
		}else{	  
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
		  }
	  
	  
		  if ($mBonus == "1") {
				$bonus = "Percentual do bônus por ausência de sinistros: ". $perBonus."%"; //10%(dez por cento).
		  }else if ($mBonus == "0") {
				$bonus = "";
		  }
	
		  if ($mPart == "1") {
			 if ($pLucro == "F13") {
				 $part  = "F13.02 - Ao término de cada Período de Seguro.<br>";
			 }else if ($pLucro == "F14") {
				 $part  = "F14.02 - Caso a presente apólice se mantenha vigente durante 2 (dois) Períodos de Seguro.<br>";
			 }else if ($pLucro == "F15") {
				 $part  = "F15.02 - Caso a presente apólice se mantenha vigente durante 3 (três) Períodos de Seguro.<br>";
			 }
		   $part .= "Percentagem de Dedução: ".$perPart0."% (".$extpart0.")<br>".
					"Participação nos Lucros: ".$perPart1."% (".$extpart1.")<br>";
		  }
		
		  if ($tipodve == "1") {
		     $tipo = "mensal";
		  }else if ($tipodve == "2") {
		     $tipo = "trimestral";
		  }else if ($tipodve == "3") {
		    $tipo = "Anual";
		  }
		
		
		  $data = date("d")."/".date("m")."/".date("Y");
	
	   }
		
  }else{ //Apolice Ga	      
		  
		 if(! file_exists($prefix. "CondPart.pdf")){		  
			  if($Num_Parcelas){
				  if ($Num_Parcelas == 1){
					  $txtParcs = "à vista";
					  $periodo = "à vista";
					  $periodo1 = "à vista";
				  }else{
					  $txtParcs = "em ". $Num_Parcelas. " prestações ".$t_DescVencimento.".";
					  $periodo =  $t_DescVencimento;
					  $periodo1 = $t_DescVencimento;
				  }
			  }else{
				 if ($pvigencia != 2) {			 
					  if ($numParc == 1){
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
					$bonus = "A percentagem referente ao item 2 deste módulo e de ". $perBonus."%"; //10%(dez por cento).
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
					 $part .= "Percentagem de dedução: ".$perPart0."% (".$extpart0.")<br>".
							  "Participação nos lucros: ".$perPart1."% (".$extpart1.")<br>";
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
	
			  $data = date("d")."/".date("m")."/".date("Y");
			
			  $periodoMaxCredito = odbc_result(odbc_exec($db, "select periodMaxCred from Inform where id=$idInform"), 1);
			
			  $jurosMora = odbc_result(odbc_exec($db, "select warantyInterest from Inform where id=$idInform"), 1);
			
			  $x = odbc_exec($db, "select a502, b1202, d602, d701, nivel_d602, p_cobertura_d701, limite_d701,a801, 
									 b603, b1504, b1202, c102, d101,e101, f305, f3301,adequacao_sinistralidade,
									 adequacao_premio,franquia_anual, CONVERT(varchar(1000),condicoes_especiais) AS condicoes_especiais,
									 PrazoMaxEmiNota, b904, b2604Perc, b2604NivelMax, 
									 b2801NivelMax, b2801Perc, d201, f401NivelSinistralidade,f401PercPremio,b802, b404NivelMax,b404Perc,GerenteCredito,Texto_Risco_Politico
								   from ModuloOferta where idInform=$idInform");
			         
			
			  //$x = odbc_exec($db, "select a801, a502, b603, b1504, b1202, c102, d101, d602, d701, e101, f305, f3301, nivel_d602, p_cobertura_d701, limite_d701 from ModuloOferta where idInform=$idInform");
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
	
	          $mod_b2604                 = number_format(odbc_result($x,'b2604Perc'),2,',','.');
			  $b2604NivelMax             = number_format(odbc_result($x,'b2604NivelMax'),2,',','.');
			  $b2604NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($x,'b2604NivelMax'),2,'.',''),$fMoeda);
			  
			  $b2801NivelMax             = number_format(odbc_result($x,'b2801NivelMax'),2,',','.');
			  $b2801NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($x,'b2801NivelMax'),2,'.',''),$fMoeda);
			  $b2801Perc                 = number_format(odbc_result($x,'b2801Perc'),2,',','.');
			  $riscopolitico             = odbc_result($x,'Texto_Risco_Politico');
	
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
					 while (odbc_fetch_row($cur)) {
						$no_razao_social       = odbc_result ($cur, 'no_razao_social');
						$nu_cnpj               = odbc_result ($cur, 'nu_cnpj');
						$nu_inscricao_estadual = odbc_result ($cur, 'nu_inscricao_estadual');
						$ds_endereco           = odbc_result ($cur, 'ds_endereco');
						$nu_cep                = odbc_result ($cur, 'nu_cep');
						$no_cidade             = odbc_result ($cur, 'no_cidade');
						$no_estado             = odbc_result ($cur, 'no_estado');
				        //	$b1202 = $b1202."Razão Social: ".$no_razao_social." - CNPJ: ".$nu_cnpj." - Inscrição Estadual: ".$nu_inscricao_estadual." - Endereço: ".$ds_endereco."' - CEP: ".$nu_cep." - Cidade: ".$no_cidade." - Estado: ".$no_estado." \n";
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
	
	
				//Condição Especial de Cobertura de Coligadas
				
				$sql  = "select limPagIndeniz from Inform where id =  ".$idInform. "";
				$rsx = odbc_exec($db, $sql);
				$limiteInden       = odbc_result($rsx, "limPagIndeniz");
			
				$rsquery = odbc_exec($db, "select a.idInform,a.razaoSocial,a.endereco,a.zipCode, a.pais,b.name, a.taxID
										   from ParModEsp_Coligada a inner join Country b on b.id = a.pais
										   where a.idInform = $idInform ORDER BY a.razaoSocial ");
			
				 while (odbc_fetch_row($rsquery)){
					   $condespcol   .= "\n";
				
					   $condespcol   .= " <b>".  odbc_result ($rsquery, 'razaoSocial')."</b>\n";
					   $condespcol   .= " Endereço: ".odbc_result ($rsquery, 'endereco')."\n";
					   $condespcol   .= " Pais: ". odbc_result ($rsquery, 'name')."\n Zip Code: ".odbc_result ($rsquery, 'zipCode') ."\n";
					   $condespcol   .= " Tax ID: ". odbc_result ($rsquery, 'taxID')." \n";
					   /*
					   $condespcol   .= " |".  odbc_result ($rsquery, 'razaoSocial')."|\n";
					   $condespcol   .= " ".odbc_result ($rsquery, 'endereco').", ".odbc_result ($rsquery, 'numeroEndereco') ." - ".odbc_result ($rsquery, 'complementoEndereco')." \n";
					   $condespcol   .= " ". odbc_result ($rsquery, 'cidade')." -  ".odbc_result ($rsquery, 'estado') ."\n";
					   $condespcol   .= " Número do registro: ". odbc_result ($rsquery, 'numRegistro')." \n";
					   */
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
					//$d701 = $d701."\ni) Percentual de Cobertura: ".$p_cobertura_d701."% \nii) Limite de pagamento por Litígio: ".$limite_d701."\n";
					//$d701 = "O percentual de cobertura é de: ".$p_cobertura_d701."% \nO limite de pagamento por <b>litígio</b> é de: ".str_replace('E','e',$limite_d701)."";
					$d701 = "O percentual de cobertura é de: ".$p_cobertura_d701."% <br>".$limite_d701."";
			    }
	
                
				$b404NivelMax              = number_format(odbc_result($x,'b404NivelMax'),2,',','.');
				$b404NivelMaxExt           = $numberExtensive->extensive(number_format(odbc_result($x,'b404NivelMax'),2,'.',''),$fMoeda);
				$mod_b404                  = number_format(odbc_result($x,'b404Perc'),2,',','.');
		  
	  
							
		$sqll  = "select Prazo_Nao_Intencao_Renov from Inform where id = ".$idInform. "";
		$rsxl = odbc_exec($db, $sqll);
		$Prazo_Nao_Intencao_Renov = odbc_result($rsxl, 'Prazo_Nao_Intencao_Renov');

		$mpdf=new mPDF('win-1252','A4','','',20,15,48,25,10,10); 
		$html = ob_get_clean();
		$mpdf->useOnlyCoreFonts = true;    // false is default
		//$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("Proposta");
		$mpdf->SetAuthor("Coface Brasil SA.");
		$mpdf->SetWatermarkText(""); // fundo marca dágua
		$mpdf->showWatermarkText = true;
		$mpdf->watermark_font = 'DejaVuSansCondensed';
		$mpdf->watermarkTextAlpha = 0.1;
		$mpdf->SetDisplayMode('fullpage');
	
	    // Endereço do logotipo
        $logo  = '../../images/logo_pdf.jpg';
        $logo_peq  	= '../../images/logo_peq.jpg';

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
			   		 <htmlpageheader name="myheader">
						<div style="text-align: center;">
								<img src="'.$logo_peq.'" width="80" height="40"/>
						</div>
						<br>
					 	<div style="text-align: left;">
					    	<span style="font-weight: bold; font-size: 11pt;">CONDIÇÕES PARTICULARES - SEGURO DE CRÉDITO À EXPORTAÇÃO</span>
					  	</div>

					    <div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; ">
					 	<div style="text-align: right;"><span style="font-style:italic; font-size: 8pt;">APÓLICE Nº</span>
					 	<span style="font-weight: bold; font-size: 8pt;">'.$apoNum.'</span><div>
			
					</htmlpageheader>
					
					<htmlpagefooter name="myfooter">
						 <table width="100%" border="0">
							 <tr>
								 <td width="22%">&nbsp;</td>
								 <td width="56%" style="text-align:center; font-size: 8pt;">									
										  Página {PAGENO} de {nb}
									 
								</td>
								<td width="22%">&nbsp;</td>
							</tr>
				     	</table>
						
					</htmlpagefooter>
					
					
					
					<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
					<sethtmlpagefooter name="myfooter" value="on" />
					
					
					<span style="font-weight: bold; font-size: 12pt;float:left"><u>DADOS DO SEGURADO:</u></span><br>
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
						  <td width="25%">CNPJ: </td><td width="75%" style="text-align:justify">'.arruma_cnpj($cnpj).'</td>			
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
							   <td colspan="2" width="92%" style="font-weight:bold">ABRANGÊNCIA DO SEGURO:</td>			
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="1" width="8%" style="font-weight:bold">1.1</td>
							   <td  colspan="1" width="84%" style="font-weight:bold">Natureza das vendas seguradas:</td>			
							</tr>
							<tr>
							   <td colspan="2" width="16%">&nbsp;</td>
							   <td colspan="1" width="84%" style="text-align:justify">'.$DescrNatureza.'</td>			
							</tr>
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">2.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">RISCOS COBERTOS:</td>			
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="1" width="8%" style="font-weight:bold">2.1</td>
							   <td  colspan="1" width="84%" style="font-weight:bold">TIPO DE IMPORTADOR:</td>			
							</tr>
							<tr>
							   <td colspan="2" width="16%">&nbsp;</td>
							   <td colspan="1" width="84%">Privado</td>			
							</tr>
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="1" width="8%" style="font-weight:bold">2.2</td>
							   <td  colspan="1" width="84%" style="font-weight:bold">PAÍSES COBERTOS:</td>			
							</tr>
							<tr>
								<td colspan="2" width="16%">&nbsp;</td>
								<td colspan="1" width="84%">a) Risco comercial: todos, exceto Brasil.</td>												 
							</tr>';
							   
						   if($riscopolitico){				   	
								$html .=	'<tr>
										<td colspan="2" width="16%">&nbsp;</td>
										<td colspan="1" width="84%" style="text-align:justify">b) Risco político: '. $riscopolitico . ' </td>
									</tr>';
						   }
								
							$html .=	'	
							
							
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="1" width="8%" style="font-weight:bold">2.3</td>
							   <td  colspan="1" width="84%" style="font-weight:bold">PERCENTAGEM DE COBERTURA:</td>			
							</tr>
							<tr>
							   <td colspan="2" width="16%">&nbsp;</td>
							   <td colspan="1" width="84%" style="text-align:justify">'. round($cobertura).'%</td>			
							</tr>
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="1" width="8%" style="font-weight:bold">2.4</td>
							   <td  colspan="1" width="84%" style="font-weight:bold">TAXA DE PRÊMIO:</td>			
							</tr>
							<tr>
							   <td colspan="2" width="16%">&nbsp;</td>
							   <td colspan="1" width="84%" style="text-align:justify">'.number_format($tx,4,",",".").'%  aplicável ao volume de exportações</td>			
							</tr>
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="1" width="8%" style="font-weight:bold">2.5</td>
							   <td  colspan="1" width="84%" style="font-weight:bold">PRÊMIO MÍNIMO:</td>			
							</tr>';
							
							/*<tr>
							   <td colspan="2" width="16%">&nbsp;</td>
							   <td colspan="1" width="84%">'.$ext . number_format($pr, 2, ',', '.'). ' ('.strtolower($valExt).')</td>			
							</tr>'; */
							
							
							
							
							// verificações 
							$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
							$rres = odbc_exec($db, $sqlvig);
							$num  = odbc_num_rows($rres);
							
							if($num > 0){
								$total = 0;
								
								while(odbc_fetch_row($rres)){
									$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
										
									if($num == 1){
										$html .= '
											<tr>
												<td colspan="2" width="16%">&nbsp;</td>
												<td colspan="1" width="84%" style="text-align:justify">Prêmio Mínimo pelo período de seguro de '. Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) . ' ' . $ext. ' '. number_format(odbc_result($rres,'v_Premio'), 2, ",", "."). ' ('.strtolower($valorext) .').</td>			
											</tr>
										'; 
									}else{
										$html .= '
											<tr>
												<td colspan="2" width="16%">&nbsp;</td>
												<td colspan="1" width="84%" style="text-align:justify">'.$ext.' '. number_format(odbc_result($rres,'v_Premio'), 2, ",", "."). ' ('.strtolower($valorext) .'). Pelo período de 12 meses compreendido entre '. Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' e ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'</td>			
											</tr>
										'; 
									}
								     
									 $total +=	odbc_result($rres,'v_Premio');
									
								}
								
								if($num > 1){
									$totalext = $numberExtensive->extensive(number_format($total,0,'.',''),$currency);
									$html .= '
											<tr>
												<td colspan="2" width="16%">&nbsp;</td>
												<td colspan="1" width="84%" style="text-align:justify">Prêmio Mínimo Total por todo período de seguro:<br>'.$ext.' '. number_format($total, 2, ",", "."). ' ('.strtolower($totalext) .').</td>			
											</tr>
										'; 
								}
							}else{
							   $html .= '
								<tr>
									<td colspan="2" width="16%">&nbsp;</td>
									<td colspan="1" width="84%" style="text-align:justify">'.$ext.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($valExt).').</td>			
								</tr>
								'; 
							}
								  
						  if($jurosMora){
							 $html .= '
									<tr>
									 <td colspan="3">&nbsp;</td>
									</tr>
									<tr>
									   <td  colspan="1" width="8%">&nbsp;</td>
									   <td  colspan="1" width="8%" style="font-weight:bold">2.6</td>
									   <td  colspan="1" width="84%" style="font-weight:bold">CONDIÇÕES ESPECIAIS DE COBERTURA:</td>			
									</tr>
									<tr>
									   <td colspan="2" width="16%">&nbsp;</td>
									   <td colspan="1" width="84%" style="text-align:justify">O SEGURADO contrata cobertura acessória de juros operacionais e moratórios, cujo adicional de prêmio é de 4% sobre o prêmio da apólice.</td>			
									</tr>';
							 }
							 
									 
						$html .= '
							  <tr>
								<td colspan="3">&nbsp;</td>
							  </tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">3.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">MOEDA DA APÓLICE</td>			
							</tr>						
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%" style="text-align:justify"><div id="cobtexto">A moeda da apólice é o '. $extmoeda.'</div></td>
										
							</tr>
							
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">4.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">TAXA DE CÂMBIO</td>			
							</tr>						
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%" style="text-align:justify">A conversão de quaisquer valores será sempre efetuada mediante aplicação da  taxa  de  câmbio divulgada pelo Banco Central do Brasil PTAX800.</td>
										
							</tr>
							
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">5.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">LIMITE MÁXIMO DE INDENIZAÇÃO</td>			
							</tr>';
								/*5.1 - 30 vezes o prêmio pago por cada período de 12 meses de vigência da apólice.

								5.2 - O Limite Máximo de Indenização será calculado e aplicado anualmente
								com base no prêmio total pago no respectivo período 12 meses.
								
								5.3 – O valor do Limite Máximo de Indenização calculado no primeiro período
								de 12 meses de vigência da apólice não será transportado para o próximo período de vigência.*/



							
							if($limiteInden){
								$html .= '
									<tr>
										<td  colspan="1" width="8%">&nbsp;</td>
										<td  colspan="2" width="92%" style="text-align:justify">5.1 - '.$limiteInden  .' vezes o prêmio pago por cada período de 12 meses de vigência da apólice.</td>
									</tr>
									<tr>
										<td  colspan="1" width="8%">&nbsp;</td>
										<td  colspan="2" width="92%" style="text-align:justify">5.2 - O Limite Máximo de Indenização será calculado e aplicado anualmente com base no prêmio total pago no respectivo período 12 meses.</td>
									</tr>
									<tr>
										<td  colspan="1" width="8%">&nbsp;</td>
										<td  colspan="2" width="92%" style="text-align:justify">5.3 - O valor do Limite Máximo de Indenização calculado no primeiro período de 12 meses de vigência da apólice não será transportado para o próximo período de vigência.</td>
									</tr>';
							 } else{
								$html .= '
									<tr>
										<td  colspan="1" width="8%">&nbsp;</td>
										<td  colspan="2" width="92%" style="text-align:justify">O Limite máximo para pagamento de indenizações por período de vigência da apólice é de '.$ext. ' ' .number_format($ValorLMI, 2, ',', '.') .' (' . strtolower($ExtValorLMI).').</td>
									</tr>';
							 }
							
							
							/*<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%">'.$limiteInden  .' vezes o valor do prêmio por período de seguro.</td>
										
							</tr>*/
							
						$html .= '<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">6.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">PRAZO MÁXIMO DE CRÉDITO</td>			
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%" style="text-align:justify">'.$periodoMaxCredito.' dias contados a partir da data do embarque internacional das mercadorias vendidas ou da data do faturamento dos serviços prestados.</td>
										
							</tr>
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">7.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">PRAZO PARA DECLARAR A AMEAÇA DE SINISTRO </td>			
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%" style="text-align:justify">'. ($periodoMaxCredito + 30).' dias contados a partir da data do embarque internacional das mercadorias vendidas ou da data do faturamento dos serviços prestados.</td>
										
							</tr>
							<tr>
					      <td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="92%" style="text-align:justify">No caso de prorrogação da data de vencimento, dentro das condições constantes no item 2.2.3 da cláusula 2 das condições gerais da apólice, o prazo é de 30 dias contados do novo vencimento.</td>
							</tr>
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">8.</td>
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
										$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
										$html .= '<div id="cobtexto">Período '.odbc_result($rres,'n_Preriodo').' – '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'<br></div>';
										$indx1++;
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
							
							
							/*$html .= '	
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%">A apólice entra em vigor no dia '.$startValidity.' e terá validade até o dia '.$endValidity.', cujo período equivale ao período do seguro.</td>
										
							</tr>';*/
							
						$html .= '
							
							<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">9.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">MÓDULOS</td>			
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%">Os seguintes módulos fazem parte deste contrato:</td>						   			
							</tr>
							</table>';
							
			         $html .= '<table width="100%" border="0" style="font-size: 12pt;"> ';
			  
				   
				                 						
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
											   <td  colspan="2" width="84%" style="text-align:justify">
											   - O valor de limite de crédito máximo referente da cláusula 1 deste módulo é de '.$ext.'
											    '.$b404NivelMax.' ('.$b404NivelMaxExt.').<br>
											   - A percentagem segurada para estes compradores é de '.round($mod_b404).'% (ICMS, IPI e ISS incluídos).</td>
										  </tr>
										  <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify">
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
														   <td  colspan="2" width="84%" style="text-align:justify">A franquia de maiores compradores mencionada no item 1.1 deste módulo é de '.$ext.' '.$mod_b904.' ('.$mod_b904Ext.').</td>
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
											   <td  colspan="2" width="84%" style="text-align:justify">- O nível máximo de crédito referente à cláusula 1 deste módulo é de '.$ext.' '.$b2604NivelMax.' ('.$b2604NivelMaxExt.').
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
													   <td  colspan="2" width="84%" style="text-align:justify">O limite mínimo para notificação de ameaça de sinistro é de '.$extnas.'</td>
											           </tr>';
									
									    
									
									}else if(odbc_result($mod,'Cod_Modulo') == "D2.01"){	
										$html .= '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%" style="text-align:justify">A franquia anual global é de '.$ext.' '. $d201.' ('.$valorExtD201.')<br></td>
										 </tr>';
									
									}else if(odbc_result($mod,'Cod_Modulo') == "D4.01"){
										 $html .= '<tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify">A franquia anual global é de '.$ext.' '. $franquia_anual.'<br></td>
									      </tr>';
										
									}else if(odbc_result($mod,'Cod_Modulo') == "D6.02"){
										 $html .= '<tr>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="2" width="84%" style="text-align:justify">'.$d602.'</td>
										    </tr>';
											
									}else if(odbc_result($mod,'Cod_Modulo') == "D7.01"){
									    $html .= '<tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify">'.$d701.'</td>
									     </tr>';	
										 
									}else if (odbc_result($mod,'Cod_Modulo') == "F3.05"){
									        if ($numParc == 1){
												 $html .= ' <tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%" style="text-align:justify">
															  <div id="cobtexto">O período de declaração é '.$periodo.'<br>
																	<div id="cobtexto">A forma de declaração é volume total de negócios aberto por número 
																	de fatura comercial, importador e valor.<br></div>
																	<div id="cobtexto">O prêmio mínimo será pago em 01 parcela à vista.</div></td>
																
														   </tr>';
															   
												
											  }else{
												 $html .= '<tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%" style="text-align:justify">
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
											   <td  colspan="2" width="84%" style="text-align:justify">'.round($bonus).'</td>
											 </tr>';
											 	
									}else if (odbc_result($mod,'Cod_Modulo') == "F13.02" || odbc_result($mod,'Cod_Modulo') == "F14.02"){
									    $html .= '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%" style="text-align:justify">A percentagem referente ao item a deste módulo é de '.round($perPart0).'%
														   <BR>A percentagem referente ao item b deste módulo é de '.round($perPart1).'%</td>
												 </tr> '; 
												
									}else if (odbc_result($mod,'Cod_Modulo') == "F33.01"){
										
										If ($taxa_monit == 0){
											$Texto_F3301 = 'A tarifa de análise cadastral é de R$ '. $taxa_analise.' ('.strtolower($extAnalise).')';
										} Else{
											$Texto_F3301 = 'A tarifa de análise cadastral é de R$ '. $taxa_analise.' ('.strtolower($extAnalise).')<br>
												A tarifa de monitoramento cadastral é de R$ '. $taxa_monit.' ('.strtolower($extMonit).')';
										}
										
										  $html .= '
													<tr>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="2" width="84%" style="text-align:justify">'.$Texto_F3301.'
													   			
														</td>
													</tr>';
												  
									}else if (odbc_result($mod,'Cod_Modulo') == "F37.02"){						  
							  
										  $html .= '										 
											 <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify">A forma de notificação é volume total de negócios aberto por nota fiscal.<br/>O período de declaração é mensal.</td>
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
			  
			  
			              
	
			$countNumber = 10; 
	           
			  $html .= '</table>
				  <br>
				 <table width="100%" border="0" style="font-size: 12pt;">'; 
							 
							 
			  if($riscopolitico != ''){			
					$html .= '
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr>
		
					<tr>
						<td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
						<td  colspan="3" width="92%" style="font-weight:bold">RISCO POLÍTICO</td>
					</tr>';
		
					$html .= '
					<tr>
						<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
						<td  colspan="3" width="92%" style="text-align:justify;">
							10.1 - Consideram-se riscos políticos cobertos pela apólice a ocorrência dos atos ou fatos  seguintes:		
						</td>
					</tr>
					<tr>
						<td colspan="1" width="8%" style="text-align:justify;">&nbsp;</td>
						<td colspan="3" width="84%" style="text-align:justify;">
							<br>a-&nbsp;Inadimplência do importador empresa pública.					
							<br><br>b-Guerra, declarada ou não, no país do importador, com exceção de guerra, declarada ou não, entre dois ou mais dos seguintes países: França, República Popular da China, Rússia, Reino Unido e Estados Unidos da América.
							<br><br>c-Moratória estabelecida em caráter geral no país do importador e mais genericamente qualquer decisão ou ato normativo decretado pelo Governo do país onde está domiciliado o importador proibindo ou exonerando este último do pagamento do débito com o Segurado.
							<br><br>d-Promulgação de lei (ou de regulação com força de lei) no pais de domicílio do importador interditando importação de bens ou execução de serviços.
							<br><br>e-Evento de não transferência de divisas decretado pelo país do importador que impeçam o repasse do valor depositado por este último em banco oficial dentro do seu país, tendo o importador efetuado todas as formalidades requeridas para a transferência.
						</td>
					</tr>		
					<tr>
						<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
						<td colspan="3" width="92%" style="text-align:justify;">
							<br>10.2 - A cobertura de Risco Político da apólice não abrange o(s) país(es) excluído(s) no item 2.2 (b)  deste instrumento.
						</td>
					</tr>	';
			      $countNumber++;
		
			  }					 
			   
			/*  if ($exibe_franq == 1){
				   $html .= '						
							 <tr>
								 <td colspan="4">&nbsp;</td>
							  </tr>
							  <tr>
								 <td  colspan="1" width="8%" style="font-weight:bold">'.   $countNumber .'.</td>
								 <td  colspan="3" width="92%" style="font-weight:bold">FRANQUIA ANUAL GLOBAL</td>
							  </tr>
							 
							 <tr>
							  <td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
							  <td  colspan="3" width="92%" style="text-align:justify;">
							   A franquia anual global é de '.$ext.' '. $franquia_anual.'<br>
								 O SEGURADO será responsável e manterá por sua conta uma franquia anual global.<br>
								 Nesta medida, não serão pagas quaisquer indenizações enquanto o montante total 
								 das indenizações devidas, resultantes de <b>notificações</b> pelo SEGURADO, 
								 relacionadas com <b>créditos</b> correspondentes de um determinado período de 
								 seguro, não exceder o montante correspondente à franquia anual global.
								</td>
							 </tr>	';
				 $countNumber++;
		
			  }*/
	
			  if($exibe_ad == 1){
				  $html .= '<tr>
								<td colspan="4">&nbsp;</td>
							</tr>
							 <tr>
								 <td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber . '.</td>
								 <td  colspan="3" width="92%" style="font-weight:bold">ADEQUAÇÃO DE PRÊMIO</td>
							 </tr>
							 
							 <tr>
							  <td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
							  <td  colspan="3" width="92%" style="text-align:justify;">
							   Caso o valor das indenizações pagas durante o período de seguro superar a 
							 percentagem de sinistralidade de '.$ad_sinistr.'% do <b>prêmio</b> pago correspondente 
							 ao mesmo período de seguro, um <b>prêmio</b> complementar será faturado.<br>
							 Este <b>prêmio</b> complementar será calculado retroativamente ao início da 
							 apólice, aplicando uma taxa de prêmio de adequação de '.$ad_premio.'% sobre 
							 a taxa de prêmio mencionada na proposta, multiplicada pelo faturamento 
							 segurável realizado durante todo o período de seguro.<br>A aplicação da taxa 
							 retroativamente não impedirá uma eventual revisão da taxa para período de 
							 seguro seguinte.
							 </td>
							 </tr>';
				   $countNumber++;
		
			  }
		
	
			 // Condição Especial de Cobertura de Coligadas 
			 if($temempcol == 1){
				  $html .= '<tr>
									<td colspan="4">&nbsp;</td>
							</tr>
							<tr>
								 <td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
								 <td  colspan="3" width="92%" style="font-weight:bold">EXTENSÃO DE COBERTURA PARA OPERAÇÕES REALIZADAS ATRAVÉS<br>  DE COLIGADAS NO EXTERIOR</td>
							</tr> 
							<tr>
							   <td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
							   <td  colspan="3" width="92%" style="text-align:justify;">'.$condespcol.'
							</td>
							</tr>';
				  $countNumber++;
		
			  }// exbibe as condições especiais
			  
		  
			  if($exibe_cond == 1){
				  $html .= '<tr>
									<td colspan="4">&nbsp;</td>
							</tr>
							 <tr>
								 <td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
								 <td  colspan="3" width="92%" style="font-weight:bold">CONDIÇÕES COMPLEMENTARES</td>
							</tr> 
							<tr>
							   <td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
							   <td  colspan="3" width="92%" style="text-align:justify;">
							   '.$condicoes_especiais.'
							</td>
							</tr>';
			      $countNumber++;
			  }
			  
	
			  $html .= '</table>
						
						
						
				 
						</body>
						</html>';
					  
					  
					  
					   $html = utf8_encode($html);
					   $mpdf->allow_charset_conversion=true;
					   $mpdf->charset_in='UTF-8';
					   $mpdf->WriteHTML($html);
					   
					   //$mpdf->Output();
					  
					   $mpdf->Output($pdfDir.$key.'CondPart.pdf',F); 
					   
					   $url_pdf = $host.'src/download/'.$key.'CondPart.pdf';
				  
  
	 
	}

}
?>
