<?php 

    include_once("policyData.php");

    $opt = ['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
	        'format' => 'A4',
	        'margin_left' => 15,
	        'margin_right' => 15,
	        'margin_top' => 25,
	        'margin_bottom' => 25,
	        'margin_header' => 10,
	        'margin_footer' => 10
        	];

    $mpdf=new  \Mpdf\Mpdf($opt);
	$mpdf->SetTitle("Fatura");
	$mpdf->SetAuthor($nomeEmp);
	$mpdf->SetWatermarkText("");
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.1;
	$mpdf->SetDisplayMode('fullpage');
	
	if(!$n_Anterior){
		$n_Anterior = 'NOVA';
	}
	
	//Valor IOF
	//$v_IOF = $pr * ($v_IOF/100);
	
	// Endereço do logotipo
  	$logo  = $root .'images/logo_coface_co.jpg';
	$assinatura1 = $root .'images/Assinatura_Cleber.jpg';
	$assinatura2 = $root .'images/Assinatura_Rose.jpg';
		
  	// Início do arquivo montando primeiro o CSS
   
  $html = '
  		<html>
				<head>
					<style>
						body {font-family: Arial, Helvetica, sans-serif !important; font-size: 9pt !important; color: #002060 !important;}
						p {margin: 0pt !important;}
						ol {counter-reset: item !important; font-weight:bold !important;}
						li {display: block !important; font-family: Arial, Helvetica, sans-serif !important; font-size: 9pt !important; text-align:justify !important;}
						li:before {content: counters(item, ".") !important; counter-increment: item !important;}
						ul {list-style-type: none !important; font-weight:normal !important;} 
						ul li {padding: 3px 0px !important;color: #000000 !important;text-align:justify !important;} 
						.rounded {border:0.5mm solid #000000 !important; background-color: #FFFFFF !important; border-radius: 2mm / 2mm !important; background-clip: border-box !important; padding: inherit !important;}
						.rounded_parc {border:0.5mm solid #000000 !important; background-color: #FFFFFF !important; border-radius: 2mm / 2mm !important; background-clip: border-box !important; padding: inherit !important;}
						#cobtexto {font-family: Arial, Helvetica, sans-serif !important; font-size:9pt !important; text-align:justify !important;}
						#sublinhado {font-family: Arial, Helvetica, sans-serif !important; font-size:9pt !important; text-align:justify !important; font-weight:bold !important; text-decoration:underline !important;}
						#disclame {font-family: Arial, Helvetica, sans-serif !important; font-size:8pt !important; text-align:right !important;}
						#img1{width: 660px !important; height: 65px !important;}								
					</style>
				</head>

				<body>	

				<htmlpageheader name="myheader">
                  <div style="text-align: left;">
                      <img src="'.$logo.'" width="86" height="43"/>
                  </div> 
                </htmlpageheader>

                 <sethtmlpageheader name="myheader" value="on" show-this-page="1" />

					<div class="rounded">
					  <table style="width:100%; border: 0;" >
							<tr>
							  <td colspan="1" style="width:80%">Companhia</td>
							  <td colspan="1" style="width:20%">CNPJ</td>
							</tr>
							<tr>
								<td colspan="1" style="width:80%"><b>'.$dados['Nome'].'</b></td>
								<td colspan="1" style="width:20%"><b>'. formata_string('CNPJ', $dados['CNPJ']). '</b></td>
							</tr>
						</table>	
					</div>
					
					<br>
					<div class="rounded">
					  <table style="width:100%; border: 0;" >
							<tr>								   
							  <td colspan="1" style="width:22%">Sucursal emissora</td>
							  <td colspan="1" style="width:22%">Apólice anterior nº</td>
							  <td colspan="1" style="width:22%">Proposta nº</td>
							  <td colspan="1" style="width:22%">Apólice nº</td>
							  <td colspan="1" style="width:12%">Endosso nº</td>
							</tr>
							<tr>								   
							  <td colspan="1" style="width:22%"><b>'.$dados['Cidade'].'</b></td>
							  <td colspan="1" style="width:22%"><b>'.$n_Anterior.'</b></td>
							  <td colspan="1" style="width:22%"><b>'.$contract.'</b></td>
							  <td colspan="1" style="width:22%"><b>'.$apoNum.'</b></td>
							  <td colspan="1" style="width:12%"><b>000</b></td>
							</tr>
							<tr>
							  <td colspan="2" style="width:50%">Grupo SUSEP</td>
							  <td colspan="1" style="width:25%">Ramo SUSEP</td>
							  <td colspan="1" style="width:25%">Subproduto</td>
							</tr>
							<tr>
								<td colspan="2" style="width:50%"><b>'.$Grupo_Susep.'</b></td>
								<td colspan="1" style="width:25%"><b>'.$Ramo_Susep.'</b></td>
								<td colspan="1" style="width:25%"><b>Risco comercial</b></td>
							</tr>
							<tr>
							  <td colspan="2" style="width:50%">Produto</td>
							  <td colspan="1" style="width:25%">Código SUSEP</td>';
							  
							  if($i_Produto == 1) {
							  	$html .='<td colspan="1" style="width:25%"></td>';
							  } else {
							  	$html .='<td colspan="1" style="width:25%">Produto</td>';
							  }
							
							$html .='</tr>
							<tr>';
							if($i_Produto == 1) {
								$html .= '<td colspan="2" style="width:50%"><b>'.$dados['CodSusep'].'</b></td>
								<td colspan="1" style="width:25%"><b>'.$dados['n_SUSEP'].'</b></td>';
							}else{
								$html .= '<td colspan="2" style="width:50%"><b>'.$dados['CodSusep'].'</b></td>
								<td colspan="1" style="width:25%"><b>'.$dados['n_SUSEP'].'</b></td>';
								#$html .= '<td colspan="2" style="width:50%"><b>'.$dados['Produto'].'</b></td>
								#<td colspan="1" style="width:25%"><b>'.$dados['n_SUSEP'].'</b></td>
								#<td colspan="1" style="width:25%"><b>'.$dados['CodSusep'].'</b></td>';
							}
							  #<td colspan="2" style="width:50%"><b>'.$dados['Produto'].'</b></td>
							  #<td colspan="1" style="width:25%"><b>'.$dados['n_SUSEP'].'</b></td>
							  #<td colspan="1" style="width:25%"><b>'.$dados['CodSusep'].'</b></td>
							$html .= '</tr>								
						</table>	
	 			  </div>
						
					<table style="width:100%; border: 0;" >	
						<tr>
							<td colspan="3" style="text-align:justify; width: 100%;">
								A companhia acima mencionada, daqui em diante designada "Seguradora", baseando-se nas informações constantes da proposta supra 
								caracterizada que lhe foi apresentada por:
							</td>
	          </tr>
					</table>
					
					<div class="rounded">
						<table style="width:100%; border: 0;" >
						  <tr>
								<td colspan="1" style="width:80%">Razão Social</td>
								<td colspan="2" style="width:20%">CNPJ</td>
							</tr>								 
							<tr>
							  <td colspan="1" style="width:80%"><b>'.$name.'</b></td>
							  <td colspan="2" style="width:20%"><b>'.$cnpj.'</b></td>
							</tr>
							<tr>
							  <td colspan="3" style="width:100%">Endereço</td>
							</tr>								 
							<tr>
							  <td colspan="3" style="width:100%"><b>'.$endcompleto.'</b></td>								   
							</tr>
							<tr>
							  <td colspan="1" style="width:80%">Cidade</td>
							  <td colspan="1" style="width:6%">UF</td>
							  <td colspan="1" style="width:14%">CEP</td>
							</tr>								 
							<tr>
							  <td colspan="1" style="width:80%"><b>'.$city.'</b></td>
							  <td colspan="1" style="width:6%"><b>'.$uf.'</b></td>
							  <td colspan="1" style="width:14%"><b>'.$cep.'</b></td>
							</tr>
						</table>
					</div>

					<br/>					
					<div class="rounded" style="">
					  <table style="width:100%; border: 0;" >
							<tr>';
						    	
						    	If ($codigoSusep == 0){
						    		$html .='<td colspan="3" style="text-align:left; width: 70%;"></td>
						    		<td colspan="1" style="text-align:left; width: 30%;">CNPJ</td>';
						    	} else{
							 		$html .='<td colspan="3" style="text-align:left; width: 70%;">Nome do Corretor</td>
							 		<td colspan="1" style="text-align:left; width: 30%;">Cód SUSEP do Corretor</td>';
							 	}
							 	
							$html .='</tr>
							
							<tr>
						    	<td colspan="3" style="text-align:left; width: 70%;"><b>'.substr($Corretor,0,60).'</b></td>';
						    
						    	If ($codigoSusep == 0){
						    		$html .='<td colspan="1" style="text-align:left; width: 30%;"><b>'.formata_string('CNPJ', $CNPJ_CORRETOR).'</b></td>';
						    	} Else {
						    		$html .='<td colspan="1" style="text-align:left; width: 30%;"><b>'.$codigoSusep.'</b></td>';
						    	}
						    
							$html .='</tr>
				    </table>
			   	</div>

					<br>
					<div class="rounded">
						<table style="width:100%; border: 0;" >
							<tr>';

							if($i_Produto == 1) {
							  	$html .='<td colspan="3" style="text-align:justify;">Emite-se esta Apólice de Seguro de Crédito interno, conforme a Resolução 407/2021 que classifica o Seguro de Crédito Interno como seguro de danos para cobertura de grandes riscos.<br>
A presente Apólice é composta pelo presente documento, Especificação da Apólice, pelas Condições Especiais (Opções) contratadas, pelas Condições Gerais e pelo Questionário apresentado pelo SEGURADO referente à cobertura oferecida por esta Apólice.<br>
As coberturas e respectivos limites máximos de indenização contratados para esta apólice encontram-se descritos nas condições em anexo.
	              </td>';
							  } else {
							  	$html .='<td colspan="3" style="text-align:justify;">Emite-se esta Apólice de Seguro de Crédito à Exportação, conforme a Resolução 407/2021 que classifica o Seguro de Crédito à Exportação como seguro de danos para cobertura de grandes riscos. <br>
A presente Apólice é composta pelo presente documento, Especificação da Apólice, pelas Condições Especiais (Opções) contratadas, pelas Condições Gerais e pelo Questionário apresentado pelo SEGURADO referente à cobertura oferecida por esta Apólice.<br>
As coberturas e respectivos limites máximos de indenização contratados para esta apólice encontram-se descritos nas condições em anexo.
</td>';
							  }
								
							$html .='</tr>
									
						</table>
					</div>

					<br>				
					<div class="rounded_parc">
					  <div style="height:365px;">
							<table style="border: 0; width: 100%;">
								<tr>
									<td colspan="2" style="text-align:right; width: 50%;">Este contrato vigorará a partir das 0h do dia</td>
									<td colspan="2" style="text-align:left; width: 50%;"><b>'.$startValidity.'.</b></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align:right; width: 50%;">E terminará às 24h do dia</td>
									<td colspan="2" style="text-align:left; width: 50%;"><b>'.$endValidity.'.</b></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left; width: 100%;">Prêmio do Seguro</td>
								</tr>';
										
								$valor_iof = $pr + $v_IOF;
								
								if ($valor_iof > 0) {
									$valor_iof_ext = strtolower($numberExtensive->extensive(number_format($valor_iof, 2, '.', ''), $fMoeda));
								} else {
									$valor_iof_ext = "";
								}

								$html .='
								<tr>
									<td colspan="4" style="text-align:left; width: 100%;"><b>'.$ext.' '.number_format($valor_iof, 2, ',', '.').' ('.$valor_iof_ext.')</b></td>
								</tr>
								
								<tr>
									<td colspan="1" tyle="text-align:left; width: 25%;">Líquido</td>
									<td colspan="2" tyle="text-align:left; width: 50%;">IOF</td>
									<td colspan="1" tyle="text-align:left; width: 25%;">Total</td>
								</tr>
								<tr>
									<td colspan="1" style="text-align:left; width: 25%;"><b>'.$ext.' '.number_format($pr, 2, ',', '.').'</b></td>
									<td colspan="2" style="text-align:left; width: 50%;"><b>'.$ext.' '.number_format($v_IOF, 2, ',', '.').'</b></td>
									<td colspan="1" style="text-align:left; width: 25%;"><b>'.$ext.' '.number_format($valor_iof, 2, ',', '.').'</b></td>
								</tr>
								<tr>
									<td colspan="4" style="width: 100%;">&nbsp;</td>									
								</tr>
								<tr>
									<td colspan="4" style="text-align:center; width: 100%;">Vencimento das Parcelas</td>									
								</tr>
								<tr>
									<td colspan="1" style="text-align:center; width: 25%;">Data vencimento</td>
									<td colspan="1" style="text-align:center; width: 25%;">Valor '.$ext.'</td>
									<td colspan="1" style="text-align:center; width: 25%;">Data vencimento</td>
									<td colspan="1" style="text-align:center; width: 25%;">Valor '.$ext.'</td>
								</tr>';

								if($numResultParc == 0){
									$qdteParc = $num_parcelas;
									$limite = 8;						
									
									$resto  = $limite - $num_parcelas;								
									$x = 0;
									$j=1;
									$mais = 0;
									$resto  = 1;						
								

									for($i=0;$i<$num_parcelas;$i++){ 

										if($i > 0){
										  $hc_ano = substr($d_venc,0,4);
											$hc_mes = substr($d_venc,5,2);
											$hc_dia = substr($d_venc,8,2);
											$five = mkdate($hc_ano,$hc_mes + $periodo * ($i),$hc_dia);
											$dt = substr($five,8,2)."/".substr($five,5,2)."/".substr($five,0,4);										
										} else{

											$dt = $primeirovencimento;	
										}
										
		                $limite = ($qdteParc > 1 ? ($qdteParc - $i):($limite - 1));
										$resto  = $num_parcelas - $j;
										
										//$parc_IOF = ($v_IOF / $qdteParc);
									  If($x == 0){
											$x = 1;
										  $html .='<tr>
																<td colspan="1" style="text-align:center; width: 25%;"><b>'.$dt.'</b></td>
																<td colspan="1" style="text-align:center; width: 25%;"><b>'.$ext.' '. number_format(($parc + $parc_IOF), 2, ',', '.').'</b></td>';
										}else{
											$x = 0;	
										  $html .='	<td colspan="1" style="text-align:center; width: 25%;"><b>'.$dt.'</b></td>
																<td colspan="1" style="text-align:center; width: 25%;"><b>'.$ext.' '. number_format(($parc + $parc_IOF), 2, ',', '.').'</b></td>
									         		</tr>';
										}

										$mais += $t_Vencimento;
										$j++;
									}
								} else {
									$num_parcelas = $numResultParc;
									$qdteParc = $num_parcelas;
									$limite = 8;
									
									$resto  = $limite - $num_parcelas;								
									$x = 0;
									$j=1;
									$mais = 0;
									$resto  = 1;						
								

									for($i=0;$i<$num_parcelas;$i++){ 

										$dt = $dadosParc[$i]['vencimento'];
										$parc = $dadosParc[$i]['valPar'];
										//$parc_IOF = ($v_IOF / $qdteParc);
										$parc_IOF = $dadosParc[$i]['valIOF'];
										
		                $limite = ($qdteParc > 1 ? ($qdteParc - $i):($limite - 1));
										$resto  = $num_parcelas - $j;
										
									  if($x == 0){
											$x = 1;
										  $html .='<tr>
																<td colspan="1" style="text-align:center; width: 25%"><b>'.$dt.'</b></td>
																<td colspan="1" style="text-align:center; width: 25%"><b>'.$ext.' '. number_format(($parc + $parc_IOF), 2, ',', '.').'</b></td>';
										}else{
											$x = 0;	
										  $html .='	<td colspan="1" style="text-align:center; width: 25%"><b>'.$dt.'</b></td>
																<td colspan="1" style="text-align:center; width: 25%"><b>'.$ext.' '. number_format(($parc + $parc_IOF), 2, ',', '.').'</b></td>
									         		</tr>';
										}

										$mais += $t_Vencimento;
										$j++;
									}								
								}
								
								// Esta condição acrescenta a parcela Par caso sejam Ex: 3 parcelas é complementado com um espaço
								
								if($x == 1){									
									$limite = $limite -1;								
									$hc_ano = substr($d_venc,0,4);
									$hc_mes = substr($d_venc,5,2);
									$hc_dia = substr($d_venc,8,2);
									$five = mkdate($hc_ano,$hc_mes + $periodo * ($i),$hc_dia);
									$dt = substr($five,8,2)."/".substr($five,5,2)."/".substr($five,0,4);
										
									$x = 0;
									
									if($VlrUltparc > 0 && $resto > 0){	
									   $html .='<td colspan="1" style="text-align:left; width: 25%"><b>'.$dt.'</b></td>
									        <td colspan="1" style="text-align:left; width: 25%"><b>'.$ext.' '. number_format($VlrUltparc, 2, ',', '.') .'</b></td>
								        </tr>';
									}else if($resto == 0){
									       $html .='<td colspan="1" style="text-align:left; width: 25%">&nbsp;</td>
									           <td colspan="1" style="text-align:left; width: 25%">&nbsp;</td>
								           </tr>';
									
									}else{
										$html .='<td colspan="1" style="text-align:left; width: 25%">&nbsp;</td>
									      <td colspan="1" style="text-align:left; width: 25%">&nbsp;</td>
								        </tr>';
									}

								}
								
								$i=0;
								$x=0;								
								
								// Completmento do espaço caso as parcelas sejam menores que 8
								
								for($i = 0; $i < $resto; $i++){
									if($limite == 1){
										$html .='<td colspan="1" style="text-align:left; width: 25%">&nbsp;</td>
												<td colspan="1" style="text-align:left; width: 25%">&nbsp;</td>
										</tr>';
									}else{
										if($x == 0){
											$x = 1;
											$html .='
											 <tr>
												<td colspan="1" style="text-align:left; width: 25%">&nbsp;</td>
												<td colspan="1" style="text-align:left; width: 25%">&nbsp;</td>';
										}else{
											$x = 0;	
											$html .='<td colspan="1" style="text-align:left; width: 25%">&nbsp;</td>
												<td colspan="1" style="text-align:left; width: 25%">&nbsp;</td>
											 </tr>';
										}
									}
									
								}							
								
								
								$html .='		
							</table>
						</div>
						<div style="padding-left:5px;">
							O prêmio será ajustado no final de vigência desta apólice, conforme estabelecido nas condições em anexo.
						</div>
						<br>
			    </div>';
						
					// Interaktiv 03/06/2015
					$sqlInf = "Select TOP 1 Inf.products, Inf.v_LMI, IsNull(PV.v_Premio, Inf.prMTotal) As Premio, Inf.limPagIndeniz, Inf.nas, Inf.Periodo_Vigencia 
							From Inform Inf 
							Left Join Periodo_Vigencia PV On PV.i_Inform = Inf.id
							Where Inf.id = ".$idInform;

					$rsSqlInf =  odbc_exec($db, $sqlInf);

					$products = odbc_result($rsSqlInf, 'products');
					$v_LMI = odbc_result($rsSqlInf, 'v_LMI');
					$prMTotal = odbc_result($rsSqlInf, 'Premio');
					$limPagIndeniz = odbc_result($rsSqlInf, 'limPagIndeniz');
					$nas = odbc_result($rsSqlInf, 'nas');
					$Periodo_Vigencia  = odbc_result($rsSqlInf, 'Periodo_Vigencia');
					
					if ($v_LMI != 0) {
						$valor_indenizacao = $v_LMI;
						$valor_indenizacao_ext = $ext.' '.number_format($valor_indenizacao,2,',','.')." (".strtolower($numberExtensive->extensive(number_format($valor_indenizacao,2,'.',''), $fMoeda)).")";
					} else {
						$valor_indenizacao = $prMTotal * $limPagIndeniz;
						$valor_indenizacao_ext = $ext.' '.number_format($valor_indenizacao,2,',','.')." (".strtolower($numberExtensive->extensive(number_format($valor_indenizacao,2,'.',''), $fMoeda)).")";						
					}

					if ($valor_indenizacao == 0) {
						$valor_indenizacao_ext = '';
					}

					$sqlMod = "SELECT a.i_Modulo, b.Cod_Modulo, b.Titulo_Modulo
							FROM Inform_Modulo a 
							INNER JOIN Modulo b ON a.i_Modulo = b.i_Modulo
							WHERE a.idInform = ".$idInform." 
							And a.i_Modulo Not In (61, 62, 63, 64, 65, 66, 67, 68, 69)
							ORDER BY b.Ordem_Modulo";
					$rsSqlMod =  odbc_exec($db, $sqlMod);

					$html .= '
					<br/>					
			    <div class="rounded" style="">
				    <table style="width:100%; border: 0;" >
						<tr>
							<td colspan="4" style="text-align:left; width: 100%">
								<b>Bem Segurado:</b> '.$products.'
							</td>
						</tr>
						<tr>
							<td colspan="4" style="text-align:left; width: 100%">
								<b>Opções Coberturas Adicionais Contratadas:</b>
							</td>
						</tr>';

							$count_mod = 0;

							while(odbc_fetch_row($rsSqlMod)){ 
								$cod_modulo = odbc_result($rsSqlMod, 'Cod_Modulo');
								$titulo_modulo = odbc_result($rsSqlMod, 'Titulo_Modulo');

								#Adicionado pra verificar se o código começa com C_, se sim, pula
								if (strpos($cod_modulo, 'C_') === 0) {
							        continue; // Pula para a próxima iteração do loop
							    }

								$html .= '<tr>
										<td colspan="2" style="text-align:left; width: 30%">'.$cod_modulo.'</td>
										<td colspan="2" style="text-align:left; width: 70%">'.$titulo_modulo.'</td>
										</tr>';
								$count_mod++;
							}

							$html .= '
						  
				    </table>
			   	</div>

				  <br/>					
			    <div class="rounded" style="">
				    <table style="width:100%; border: 0;" >
				    <tr>
<td colspan="4" style="text-align:left; width: 100%">
									Este seguro está sujeito à participação do Seguro e as seguintes <i>franquias</i> previstas nas Condições Especiais
								</td>
				    </tr>
						  <tr> 
								<td colspan="4" style="text-align:left; width: 100%">
									<b>Franquias:</b>
								</td>
						  </tr>';
						  

							$sqlFranq = "SELECT Inf.nas, MO.franquia_anual, MO.d201, MO.nivel_d602, MO.b904, Inf.percCoverage 
									FROM Inform Inf Inner Join ModuloOferta MO On MO.idInform = Inf.id
									WHERE Inf.id = ".$idInform;

							$rsSqlFranq =  odbc_exec($db, $sqlFranq);

							$nas = odbc_result($rsSqlFranq, 'nas');
							$franquia_anual = odbc_result($rsSqlFranq, 'franquia_anual');
							$d201 = odbc_result($rsSqlFranq, 'd201');
							$nivel_d602 = odbc_result($rsSqlFranq, 'nivel_d602');
							$b904 = odbc_result($rsSqlFranq, 'b904');
							$percCoverage = odbc_result($rsSqlFranq, 'percCoverage');
							
							If ($nas > 0) {
								#$extnas = $ext.' '.number_format($nas, 2, ',','.')." (".strtolower($numberExtensive->extensive(number_format($nas,2, '.',''), $fMoeda)).').';
								$extnas = $ext . ' ' . number_format($nas, 2, ',', '.') . '';

							} Else {
								$extnas = 0;
							}
							
					  		If ($franquia_anual > 0) {
					  			$valor_franquia_anual = $ext.' '.number_format($franquia_anual,2,',','.')." (".strtolower($numberExtensive->extensive(number_format($franquia_anual,2,'.',''), $fMoeda)).").";
						  	} else {
						  		$valor_franquia_anual = 0;
						  	}

							if ($d201 > 0) {
					  			$valor_d201 = $ext.' '.number_format($d201,2,',','.')." (".strtolower($numberExtensive->extensive(number_format($d201,2,'.',''), $fMoeda)).").";
							} else {
								$valor_d201 = 0;
							}

							if ($nivel_d602 > 0) {
					  			$valor_d602 = $ext.' '.number_format($nivel_d602,2,',','.')." (".strtolower($numberExtensive->extensive(number_format($nivel_d602,2,'.',''), $fMoeda)).").";
							} else {
								$valor_d602 = 0;
							}

							if ($b904 > 0) {
					  			$valor_b904 = $ext.' '.number_format($b904,2,',','.')." (".strtolower($numberExtensive->extensive(number_format($b904,2,'.',''), $fMoeda)).").";
							} else {
								$valor_b904 = 0;
							}

							$html .= '<tr><td colspan="4" style="text-align:left; width: 100%">Percentual Segurável &ndash; ' .$percCoverage. '%</td>
						  </tr>';

						  	// Módulo D1.01
						  	If ($nas > 0) {
							  	$html .= '
							  	<tr>
									<td colspan="4" style="text-align:left; width: 100%">Franquia Simples &ndash; Notificação de Sinistro: '.$extnas.'</td>
								</tr>';
							}
						  
						  	// Módulo D4.01
						  	If ($franquia_anual > 0) {
							  	$html .= '
							  	<tr>
									<td colspan="4" style="text-align:left; width: 100%">Franquia Anual Global: '.$valor_franquia_anual.'</td>
								</tr>';
						  	}
						  
						  	// Módulo D2.01
						  	If ($d201 > 0) {
							  	$html .= '
							  	<tr>
									<td colspan="4" style="text-align:left; width: 100%">Franquia Dedutível Individual: '.$valor_d201.'</td>
								</tr>';
							}
						  
						  	// Módulo D6.02
						  	If ($nivel_d602 > 0) {
							  	$html .= '
							  	<tr>
									<td colspan="4" style="text-align:left; width: 100%">Pequenos Sinistros: '.$valor_d602.'</td>
								</tr>';
								}
						  
						  	// Módulo B9.04
						  	If ($b904 > 0) {
							  	$html .= '
							  	<tr>
									<td colspan="4" style="text-align:left; width: 100%">Maiores Compradores: '.$valor_b904.'</td>
								</tr>';
							}

							// Caso não exista nenhuma franquia
							if($nas == 0 && $franquia_anual == 0 && $d201 == 0 && $nivel_d602 == 0 && $b904 == 0) {
								$html .= '
							  	<tr>
									<td colspan="4" style="text-align:left; width: 100%">Não aplicável.</td>
								</tr>';
							}

				    $html .= '
				    </table>
			   	</div>

				  <br/>					
			    <div class="rounded" style="">
				    <table style="width:100%; border: 0;" >
						  <tr>
								<td colspan="4" style="text-align:left; width: 100%">
									<b>Limite Máximo de Indenização:</b> '.$valor_indenizacao_ext.' relativo ao período de '.$Periodo_Vigencia.' meses de vigência da Apólice.
								</td>
						  </tr>
				    </table>
			   	</div>';
			   	
					if ($count_mod > 15) {
						$html .= '<pagebreak />';
					} else {
						$html .= '<br/>';						
					}
					
					$html .= '
			    <div class="rounded" style="">
				    <table style="width:100%; border: 0;" >
						  <tr>
								<td colspan="4" style="text-align:justify; width: 100%;">
									"SUSEP &ndash; Superintendência de Seguros Privados &ndash; Autarquia Federal responsável pela fiscalização, normatização e controle 
									dos mercados de seguro, previdência complementar aberta, capitalização, resseguro e corretagem de seguros."
								</td>
						  </tr>
				    </table>
			   	</div>
						
					<br/>					
				  <div class="rounded" style="">
				    <table style="width:100%; border: 0;" >
						  <tr>
								<td colspan="4" style="text-align:left; width: 100%">
										Para validade do presente contrato, a "Seguradora" representada por seus procuradores, assina esta apólice no local e data abaixo.
								</td>
						  </tr>
						  <tr>
								<td colspan="4" style="text-align:left; width: 100%">São Paulo, <b>'.$datahoje.'.</b></td>
						  </tr>
						  <tr>
								<td colspan="4" style="text-align:left; width: 100%">Pela seguradora</td>
						  </tr>
						  <tr>
								<td colspan="4" style="width:100%">&nbsp;</td>
						  </tr>
						  <tr>
						  	<td>&nbsp;</td>
						  	<td>&nbsp;</td>
								<td style="text-align:center;"><img src="'.$assinatura1.'" style="width: 140px; height: 80px;"></td>
								<td style="text-align:center;"><img src="'.$assinatura2.'" style="width: 140px; height: 80px;"></td>
						  </tr>
						  <tr>
							 	<td>&nbsp;</td>
							 	<td>&nbsp;</td>
								<td style="text-align:center; width: 50%;"><b>Cleber Santos</b></td>
								<td style="text-align:center; width: 50%;"><b>Rose Cordeiro</b></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							 	<td>&nbsp;</td>
								<td style="text-align:center; width: 50%;">Superintendente Técnico</td>
								<td style="text-align:center; width: 50%;">Diretora Técnica</td>
							</tr>							 
					  </table>
				  </div>';
					  
				  if ($i_Produto != 1) {
				  	$html .= '<br/>';
					} else{
						$html .= '<br/><br/><br/>';
					}

				  $html .= '
			    <table style="width:100%; border: 0;" >
					  <tr>
							<td colspan="4" style="text-align:right; width: 100%;">
								Ouvidoria Coface Seguros de Crédito S/A: 0800 591 4787
							</td>
						</tr>
						<tr>
							<td colspan="4" style="text-align:right; width: 100%;">
								Central de Atendimento para deficiente auditivo: 0800 580 2755
							</td>
					  </tr>
						<tr>
							<td colspan="4" style="text-align:right; width: 100%;">
								Central de Atendimento ao Segurado: 0800 580 3241
							</td>
					  </tr>
						<tr>
							<td colspan="4" style="text-align:right; width: 100%;">
								http://www.susep.gov.br/menu/servicos-ao-cidadao/sistema-de-consulta-publica-de-produtos
							</td>
					  </tr>
						<tr>
							<td colspan="4" style="text-align:right; width: 100%;">
								Atendimento SUSEP: 0800-0218484
							</td>
					  </tr>
			    </table>';

				 // if ($i_Produto != 1) {
				//	  $html .=  '	
			//		  <br/>
			//			<br/>
			//	    <table style="width:100%; border: 0;" >
			//			  <tr>
			//					<td colspan="4" style="text-align:right; width: 100%;">
			//						*A Seguradora Brasileira de Crédito à Exportação S.A 
			//					 	foi incorporada pela Coface do Brasil Seguros de Crédito S/A conforme Portaria SUSEP n° 7.640 
			//						de 15/06/2020, que está, desta forma, autorizada a comercializar o produto 
			//					  	SUSEP no. '.$c_SUSEP.'
			//					</td>
			//				</tr>
			//	    </table>';
			//	  }
				   
					$html .=  '
				</body>
    	</html>'; 

    	//error_log($html);

   //	$html = utf8_encode($html);
   $html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
   	$mpdf->allow_charset_conversion = true;
   	$mpdf->charset_in = 'UTF-8';
   	$mpdf->WriteHTML($html);

   	header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="documento.pdf"');

$mpdf->Output();
?>