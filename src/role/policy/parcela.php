<?php 
	include_once("../../../config.php"); 
  	include_once("../consultaCoface.php");

	function arruma_cnpj($c){
    	If(strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)){
      		return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
    	}
    	
    	return $c;
  	}
  
	//if(! file_exists($prefix. "Parcela". $parcela.".pdf")){
	
  	include_once("../../../gerar_pdf/MPDF45/mpdf.php");
	
	$sqlEmp  = "SELECT Nome, CNPJ, Endereco, Complemento, CEP, Cidade, Estado, Cod_Area,	Telefone,	Bairro, Fax, HomePage
							FROM Empresa 
							WHERE i_Empresa = 1";  
   
 	$resEmp = odbc_exec($db,$sqlEmp);
 	$dadosEmp = odbc_fetch_array($resEmp);
	
	$compEmp = $dadosEmp['Complemento'] ? ' - '.$dadosEmp['Complemento'] : '';
	$cepEmp = $dadosEmp['CEP'] ? ' - Cep: '.$dadosEmp['CEP'] : '';
	$cidEmp = $dadosEmp['Cidade'] ? ' - '.$dadosEmp['Cidade'] : '';
	$estEmp = $dadosEmp['Estado'] ? ', '.$dadosEmp['Estado'] : '';
	$telEmp = $dadosEmp['Telefone'] ? ' Tel.: '.$dadosEmp['Telefone'] : '';
	$faxEmp = $dadosEmp['Fax'] ? ' Fax: '.$dadosEmp['Fax'] : '';

	$enderecoEmp = $dadosEmp['Endereco'].$compEmp.$cepEmp.$cidEmp.$estEmp.$telEmp.$faxEmp;
	$siteEmp = $dadosEmp['HomePage'];
	$nomeEmp = $dadosEmp['Nome'];
	
  	//INTERAKTIV 23/06/2014
  	//era gerada a partir da 2ª parcela.
  	//$parc = 2;
  	$parc = 1;

	$id_empresa = $i_Empresa;
	
	If($i_Empresa == 2){
		$id_empresa = 5;
	}
	  
	$sqi = odbc_exec ($dbOIM, "Select i_Parcela From IM_Emissao E
		Inner Join IM_Parcela P On
			P.i_Emissao = E.i_Emissao
			And P.t_Parcela = 100
		Where
			E.n_Apolice = ".$NumeroApoliceOIM."
			And E.n_Endosso = 0
			And E.n_Empresa = ".$id_empresa."
		Order By
			P.n_Parcela");
	 
		while(odbc_fetch_row($sqi)){
			$idP[]  = odbc_result($sqi, 'i_Parcela');
	  	}

	  	$j= 0;
	  
	  	For($parcela = $parc; $parcela <= $num_parcelas; $parcela++){
	    	$verif = $num_parcelas - $parcela;

	    	If ($verif == 0 && $VlrUltparc > 0 ){
	        	$valPar = $VlrUltparc;
	        	$parc =  $VlrUltparc;
	    	}
					 
		  	$hc_ano = substr($d_venc,0,4);
		  	$hc_mes = substr($d_venc,5,2);
		  	$hc_dia = substr($d_venc,8,2);
		  	$five = mkdate($hc_ano,$hc_mes + $periodo * ($parcela - 1),$hc_dia);
		  	$five = substr($five,8,2)."/".substr($five,5,2)."/".substr($five,0,4);
		  
			$mpdf = new mPDF('win-1252','A4','',''); 
			$html = ob_get_clean();
			$mpdf->useOnlyCoreFonts = true;    // false is default
			//$mpdf->SetProtection(array('print')); // proteção de arquivo
			$mpdf->SetTitle("Fatura");
			$mpdf->SetAuthor($nomeEmp);
			$mpdf->SetWatermarkText(""); // fundo marca dágua
			$mpdf->showWatermarkText = true;
			$mpdf->watermark_font = 'DejaVuSansCondensed';
			$mpdf->watermarkTextAlpha = 0.1;
			$mpdf->SetDisplayMode('fullpage');
			
			// Endereço do logotipo
			$logo  		= '../../images/logo_pdf.jpg';
		 
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
						#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:8pt; text-align:right;}
						
						div.rounded {
							border:0.5mm solid #000000; 
							background-color: #FFFFFF;
							border-radius: 2mm / 2mm;
							background-clip: border-box;
							padding: inherit;
						 }
						 
						 #img1{
							width: 660px;
							height: 65px;
							/* correcao cfc */
						 }
						
						</style>
						
						</head>
						<body>
						';
				 
			  $html .= ' 	
							<htmlpageheader name="myheader">
								<div style="width:100%">
									<div id="img1"></div>
								 </div>	
							</htmlpageheader>
							
							<htmlpagefooter name="myfooter">
												
							</htmlpagefooter>
							
							<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
							<sethtmlpagefooter name="myfooter" value="on" />
							<br>
							<br>
							<table width="100%" border="0" >
								<tr>
									<td colspan="1" width="30%">&nbsp;</td>					
									<td colspan="1" width="40%" style="text-align:center; font-weight: bold; font-size: 14pt;">FATURA '.$idP[$j].'/'.$ano.'</td>
									<td colspan="1" width="30%">&nbsp;</td>
								</tr>
								<tr>
								   <td colspan="3" width="100%">&nbsp;</td>						  
								</tr>
							</table>
							
							<div class="rounded">
									  <table width="100%" border="0" >
										<tr>
										   <td colspan="1" width="33%"><b>Apólice n°: </b>'.$apoNum.'</td>
										   <td colspan="1" width="33%"><b>Endosso n°:</b> 0 </td>
										   <td colspan="1" width="34%"><b>Proposta n°:</b> '.$contract.'</td>
										</tr>
										<tr>
											<td colspan="3" width="100%"><b>Vigência da apólice:</b> '.utf8_decode($today).'</td>
										</tr>
										<tr>
											<td colspan="3" width="100%"><b>Nome segurado/sacado:</b> '.$name.'</td>
										</tr>
										<tr>
											<td colspan="3" width="100%"><b>Endereço:</b> '.$end.'</td>
										</tr>
										<tr>
											<td colspan="1" width="50%"><b>CEP: </b>'.$cep.'</td>
											<td colspan="2" width="50%"><b>CNPJ:</b> '. arruma_cnpj($cnpj).'</td>
										</tr>
									  </table>	
							</div>
							<br>
							<div class="rounded">
								<table width="100%" border="0" >
									<tr>
									   <td colspan="3" width="100%"><b>Descrição:</b></td>
									</tr>					
									<tr>
									   <td colspan="3" width="100%">Valor referente a parcela '.$parcela.'/'.$num_parcelas.' da apólice supracitada.</td>
									</tr>
								</table>
							</div>
							<br>
							<div class="rounded">
									<table width="100%" border="0" >
										<tr>
											<td colspan="1">&nbsp;</td>
											<td colspan="1" style="text-align:center;"><b>VALORES EM '.utf8_decode($extMoeda).' '.utf8_decode($DescMoeda).'</b></td>
											<td colspan="1">&nbsp;</td>
										</tr>';
										
										  //$valorParcExt = $numberExtensive->extensive(number_format($valPar, 2, '.', ''), $fMoeda);
										  $valorParcExt = utf8_encode($numberExtensive->extensive($valPar, $fMoeda));
										  
										  $ValorMulta = $valPar * $p_Multa / 100;
										  $ValorMulta = number_format($ValorMulta,2,',','.');
										  $ValorMulta = str_replace('.','',$ValorMulta);
										  
										  
										  $ValorJuros = $valPar * $p_Juros_Dia / 100;
										  $ValorJuros = number_format($ValorJuros,2,',','.');
										  $ValorJuros = str_replace('.','',$ValorJuros);
										  

										  $valPar  = number_format($valPar,2,',','.');
										  $valPar  = str_replace('.','',$valPar);
										  $valPar  = str_replace(',','.',$valPar);
								   										  
										  if ($currency == "1") {
											$Simbolo_moeda = 'R$';
											 $prPrint = 'R$ '. number_format($pr, 2, ',', '.');
											 $valParc  = 'R$ '. number_format($valPar, 2, ',', '.');
											 $agencia = "1778-7";
											 $conta   = "5053-9";
											 
										  }else if ($currency == "2") {
										  	$Simbolo_moeda = 'US$';
											 $prPrint = 'US$ '. number_format($pr, 2, ',', '.');
											 $valParc  = 'US$ '. number_format($valPar, 2, ',', '.');
											 $agencia = "1778-7";
											 $conta   = "002532";
											
										  }else if ($currency == "6") {
										  	$Simbolo_moeda = '€';
											 $prPrint = '€ '. number_format($pr, 2, ',', '.');
											 $valParc  = '€ '. number_format($valPar, 2, ',', '.');
											 $agencia = "1778-7";
											 $conta   = "5053-9";
											 
										  }
										
										$html .= '<tr>
											<td colspan="3" width="100%"><b>Valor da Parcela: </b>'.$valParc.' ('.utf8_decode($valorParcExt).')</td>
										</tr>
										
										<tr>
											<td colspan="3" width="100%"><b>Vencimento da parcela:</b> '.$five.'</td>
										</tr>
										
										<tr>
											<td colspan="3" width="100%"><b>Prêmio Mínimo:</b> '.$prPrint.'</td>
										</tr>
									</table>
					        </div>
							<br>
							<div class="rounded">
								<table width="100%" border="0" >
									<tr>
										<td colspan="3" width="100%" style="text-align:center;"><b>INSTRUÇÕES</b></td>
									</tr>
									<tr>
										<td colspan="3" width="100%" style="font-size:12pt; text-align:justify;">Creditar conta abaixo conforme resolução n.° 002532 BACEN de 14 de agosto de 1998 e instruções a seguir:
										Todas as despesas bancárias, no Brasil e no Exterior, incorrerão por conta do segurado.
										Ordens formatadas no sistema SWIFT, MT 103.<br>
										Preenchimento do campo 59: '.$nomeEmpSBCE.'<br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										C/C.: '.($currency == "2" ? "05.012-1": "5053-9").' Agência 1778-7<br>
										Preenchimento do Campo 71a: OUR (despesas bancárias por conta do segurado)<br>
										Banco do Brasil S/A<br>
										Agência Internacional Rio<br>
										Swift BRASBRRJRJO<br><br>
										
										Após vencto cobrar multa de ' . $Simbolo_moeda. ' ' . $ValorMulta . '<br>

										</td>
									</tr>
								  </table>
							 </div>
								<br>
							 <div class="rounded" style="padding: 2mm; width:700px;">
									<div align="center">
										 <strong>PARA USO DO BANCO CONTRATANTE</strong>
									</div>
									<br>
									   
									   <div class="rounded" style="height:100px; width:157px; float:left; text-align:center; font-size: 10pt;">							
										<b>Banco Contratante </b>
									   </div>
									   <div style="height:100px; width:6px;float:left;">&nbsp; </div>
									   
									   <div class="rounded" style="height:100px; width:157px;float:left; text-align:center;font-size: 10pt;">
										  <b>N.° Contrato de Câmbio</b>
									   </div>	
									   
									   <div style="height:100px; width:6px;float:left;">&nbsp; </div>
									   
									   <div class="rounded" style="height:100px; width:155px;float:left; text-align:center;font-size: 10pt;">
										  <b>Data</b>
									   </div>
									   
									   <div style="height:100px; width:6px;float:left;">&nbsp;</div>
										
									   <div class="rounded" style="height:100px; width:157px;float:left; text-align:center;font-size: 10pt;">
										   <b>Assinatura</b>
									   </div>			   
							   </div>
										
						   <br><br>
						   <table width="100%" border="0">
							  <tr>
								 <td width="25%">&nbsp;</td>
								 <td align="right" width="75%">
								  <div align="right" id="disclame">Seguro garantido pela '.$nomeEmpSBCE.' (uma empresa '.$nomeEmp.')
								  CNPJ: 02.166.824/0001-61, SUSEP no. '.$c_SUSEP.'</div>
							  </td>	
							  </tr>
							   <tr>
								  <td colspan="1"width="35%">&nbsp;</td>
								  <td colspan="1"width="65%" style="text-align:right">'.$cp.'</td>
							   </tr>
						   </table>
						  
						   
							
							  
			</body>
			</html>';
		  
		  
		  
		  $html = utf8_encode($html);
		  $mpdf->allow_charset_conversion=true;
		  $mpdf->charset_in='UTF-8';
		  $mpdf->WriteHTML($html);
		   
		  //$mpdf->Output();
		  
		  //echo $html;
		   
		  $mpdf->Output($pdfDir.$key."Parcela". $parcela.'.pdf',F); 
		   
		  $url_pdf = $host."src/download/".$key."Parcela".$parcela.".pdf";

			$j++;
	}

	?>

