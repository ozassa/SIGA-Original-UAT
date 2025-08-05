<?php 
	
    include_once("policyData.php");

    $opt = ['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
	        'format' => 'A4',
	        'margin_left' => 15,
	        'margin_right' => 15,
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

	// Endereço do logotipo
    $logo  = $root .'images/logo.jpg';
	$assinatura1 = $root .'images/Assinatura_Everton.jpg';
	$assinatura2 = $root .'images/Assinatura_Rose.jpg';
	
    $ext = 'R$';

   	// Início do arquivo montando primeiro o CSS   
        $html = '<html>
				<head>
				<style>
				body {font-family: Arial, Helvetica, sans-serif;
					font-size: 9pt;
				}
				p {    margin: 0pt;
				}
					
				
				ol {counter-reset: item; font-weight:bold; }
                li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 9pt; text-align:justify}
                li:before {content: counters(item, "."); counter-increment: item; }
				
				ul			{list-style-type: none; font-weight:normal } 
				ul li		{padding: 3px 0px;color: #000000;text-align:justify} 

                
				
				#cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;}
				#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify; font-weight:bold; text-decoration:underline;}
				#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:8pt; text-align:right;}
				
				div.rounded {
				    border:0.5mm solid #000000; 
				    background-color: #FFFFFF;
				    border-radius: 2mm / 2mm;
				    background-clip: border-box;
				    padding: inherit;
				 }
				
				</style>
				
				</head>
				<body>
				<htmlpageheader name="myheader">';
		 
      $html .= ' <!--mpdf
					<htmlpageheader name="myheader">
					      
							
							
					</htmlpageheader>
					
					<htmlpagefooter name="myfooter">
							
							
					</htmlpagefooter>
					
					<sethtmlpageheader name="myheader" value="off" show-this-page="1" />
					    
					
					<sethtmlpagefooter name="myfooter" value="off" />
					
					
					mpdf-->
					<div class="rounded">
						      <table style="border: 0; width: 100%;" >
								<tr>
								   <td colspan="1" style="width: 80%;">Companhia</td>
								   <td colspan="1" style="width: 20%;">CNPJ</td>
								   
								</tr>
								<tr>
									<td colspan="1" style="width: 80%;"><b>'.$nomeEmpSBCE.'</b></td>
									<td colspan="1" style="width: 20%;"><b>'.formata_string('CNPJ', $dados['CNPJ']).'</b></td>
								</tr>
								
							  </table>	
							 
					</div>
					<br>
					<div class="rounded">
						      <table style="border: 0; width: 100%;" >
								<tr>								   
								   <td colspan="1" style="width: 25%;">Sucursal emissora</td>
								   <td colspan="1" style="width: 25%;">Apólice anterior</td>
								   <td colspan="1" style="width: 25%;">Proposta nº</td>
								   <td colspan="1" style="width: 25%;">Apólice nº</td>
								</tr>
								<tr>								   
								   <td colspan="1" style="width: 25%;"><b>Rio de Janeiro</b></td>
								   <td colspan="1" style="width: 25%;"><b>'.$n_Anterior.'</b></td>
								   <td colspan="1" style="width: 25%;"><b>'.$contract.'</b></td>
								   <td colspan="1" style="width: 25%;"><b>'.$apoNum.'</b></td>
								</tr>';
								
								/*<tr>								   
								   <td colspan="2" style="width: 50%;">Grupo SUSEP</td>
								   <td colspan="1" style="width: 25%;">Ramo SUSEP</td>
								   <td colspan="1" style="width: 25%;">Subproduto</td>
								</tr>
								<tr>								   
								   <td colspan="2" style="width: 50%;"><b>'.$Grupo_Susep.'</b></td>
								   <td colspan="1" style="width: 25%;"><b>'.$Ramo_Susep.'</b></td>
								   <td colspan="1" style="width: 25%;"><b>Risco comercial</b></td>
								</tr>
								  
								<tr>								   
								   <td colspan="2" style="width: 50%;">Produto</td>
								   <td colspan="1" style="width: 25%;">Número de custódia</td>
								   <td colspan="1" style="width: 25%;">Processo SUSEP nº</td>
								</tr>
								<tr>								   
								   <td colspan="2" style="width: 50%;"><b>'.$dados['Produto'].'</b></td>
								   <td colspan="1" style="width: 25%;"><b>&nbsp;</b></td>
								   <td colspan="1" style="width: 25%;"><b>'.$dados['CodSusep'].'</b></td>
								</tr>*/
								
								$html .='<tr>								   
								   <td colspan="2" style="width: 50%;">Grupo SUSEP</td>
								   <td colspan="1" style="width: 25%;">Ramo SUSEP</td>
								   <td colspan="1" style="width: 25%;">Subproduto</td>
								</tr>
								<tr>								   
								   <td colspan="2" style="width: 50%;"><b>07 - Financeiro</b></td>
								   <td colspan="1" style="width: 25%;"><b>049 - Crédito à exportação</b></td>
								   <td colspan="1" style="width: 25%;"><b>Risco comercial</b></td>
								</tr>
								  
								<tr>								   
								   <td colspan="2" style="width: 50%;">Produto</td>
								   <td colspan="1" style="width: 25%;">Número de custódia</td>
								   <td colspan="1" style="width: 25%;">Processo SUSEP nº</td>
								</tr>
								<tr>								   
								   <td colspan="2" style="width: 50%;"><b>Seguro de Crédito Externo</b></td>
								   <td colspan="1" style="width: 25%;"><b>&nbsp;</b></td>
								   <td colspan="1" style="width: 25%;"><b>'.$c_SUSEP.'</b></td>
								</tr>
								
							  </table>	
							 
					</div>
					
					<table style="border: 0; width: 100%;" >
						<tr>
						   <td colspan="3" style="width: 100%;">A companhia acima mencionada, daqui em diante designada "Seguradora", baseando-se nas informações constantes da proposta supra caracterizada que lhe foi apresentada por:</td>
                        </tr>
												
					</table>
						
					<div class="rounded">
							<table style="border: 0; width: 100%;" >
							    <tr>
								   <td colspan="1" style="width: 80%;">Razão Social</td>
								   <td colspan="2" style="width: 20%;">CNPJ</td>
								</tr>								 
								<tr>
								   <td colspan="1" style="width: 80%;"><b>'.$name.'</b></td>
								   <td colspan="2" style="width: 20%;"><b>'.$cnpj.'</b></td>
								</tr>
								<tr>
								   <td colspan="3" style="width: 100%;">Endereço</td>
							    </tr>								 
								<tr>
								   <td colspan="3" style="width: 100%;"><b>'.$endcompleto.'</b></td>								   
								</tr>
								
								<tr>
								   <td colspan="1" style="width: 80%;">Cidade</td>
								   <td colspan="1" style="width: 6%;">UF</td>
								   <td colspan="1" style="width: 14%;">CEP</td>
								</tr>								 
								<tr>
								   <td colspan="1" style="width: 80%;"><b>'.$city.'</b></td>
								   <td colspan="1" style="width: 6%;"><b>'.$uf.'</b></td>
								   <td colspan="1" style="width: 14%;"><b>'.$cep.'</b></td>
								</tr>
								  
							</table>
					</div>
					<br>
					<div class="rounded">
							<table style="border: 0; width: 100%;" >
								<tr>
									<td colspan="3" style="text-align:justify;">Emite esta apólice de seguro de crédito à exportação, que é composta pelo presente documento, pelas condições
										Particulares, pelos módulos relacionados nas condições particulares, pelas condições gerais e pelo questionário
										apresentado pelo SEGURADO referente à cobertura oferecida por esta Apólice.
										As coberturas e respectivos limites máximos de indenização contratados para esta apólice encontram-se
										descritos nas condições em anexo.
                                    </td>
									
								</tr>
								
							</table>
				    </div>
					<br>
					
					<div class="rounded">
							<table style="border: 0; width: 100%;" >
								<tr>
									<td colspan="1" style="text-align:left; width: 5%; ">&nbsp;</td>
									<td colspan="2" style="text-align:left; width: 80%; ">Este contrato vigorará a partir das 0hs. do dia <b>'.$startValidity.'.</b></td>
									<td colspan="1" style="width: 15%;">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="1" style="text-align:left; width: 20%; ">&nbsp;</td>
									<td colspan="2" style="text-align:left; width: 65%; ">E terminará às 24hs. do dia <b>'.$endValidity.'.</b></td>
								    <td colspan="1" style="width: 15%;">&nbsp;</td>
								</tr>
								
								<tr>
									<td colspan="4" style="width: 100%; text-align:left;">Prêmio do seguro</td>
									
								</tr>';
								
								$sqlz = "Select prMTotal, currency, Num_Parcelas from Inform where id = '".$idInform."'";
								$curz = odbc_exec($db, $sqlz);
								if (odbc_fetch_row ($curz)) {
									$currency     =  odbc_result($curz, 'currency');
									$valorExtenso =  $numberExtensive->extensive(number_format(converte(odbc_result($curz, "prMTotal"),$v_compra),2,'.',''), 1);
								}
								
							$html .= '
								<tr>
									<td colspan="4" style="width: 100%; text-align:left;"><b>'.$ext.' '.number_format(converte($pr, $v_compra), 2, ',', '.').' ('. $valorExtenso .')</b></td>
								</tr>
								<tr>
									<td colspan="1" style="width: 25%; text-align:left;">Líquido</td>
									<td colspan="1" style="width: 25%; text-align:left;">IOF</td>
									<td colspan="2" style="width: 50%; text-align:left;">Total</td>
								</tr>
								<tr>
									<td colspan="1" style="width: 25%; text-align:left;"><b>'.$ext.' '.number_format(converte($pr, $v_compra), 2, ',', '.').'</b></td>
									<td colspan="1" style="width: 25%; text-align:left;"><b>0,00</b></td>
									<td colspan="2" style="width: 50%; text-align:left;"><b>'.$ext.' '.number_format(converte($pr, $v_compra), 2, ',', '.').'</b></td>
								</tr>
								<tr>
									<td colspan="4" style="width: 100%" >&nbsp;</td>									
								</tr>
								<tr>
									<td colspan="4" style="width: 100%; text-align:center;">Vencimento das parcelas</td>									
								</tr>
								<tr>
									<td colspan="1" style="width: 25%; text-align:left;">Data vencimento</td>
									<td colspan="1" style="width: 25%; text-align:left;">Valor</td>
									<td colspan="1" style="width: 25%; text-align:left;">Data vencimento</td>
									<td colspan="1" style="width: 25%; text-align:left;">Valor</td>
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
									  if($x == 0){
											$x = 1;
										  $html .='<tr>
																<td colspan="1" style="width: 25%; text-align:left;"><b>'.$dt.'</b></td>
																<td colspan="1" style="width: 25%; text-align:left;"><b>'.$ext.' '. number_format(converte($parc, $v_compra), 2, ',', '.').'</b></td>';
										}else{
											$x = 0;	
										  $html .='	<td colspan="1" style="width: 25%; text-align:left;"><b>'.$dt.'</b></td>
																<td colspan="1" style="width: 25%; text-align:left;"><b>'.$ext.' '. number_format(converte($parc, $v_compra), 2, ',', '.').'</b></td>
									         		</tr>';
										}

										$mais += $t_Vencimento;
										$j++;
									}
								} else{
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
										
	                  $limite = ($qdteParc > 1 ? ($qdteParc - $i):($limite - 1));
										$resto  = $num_parcelas - $j;
									  if($x == 0){
											$x = 1;
										  $html .='<tr>
																<td colspan="1" style="width: 25%; text-align:left;"><b>'.$dt.'</b></td>
																<td colspan="1" style="width: 25%; text-align:left;"><b>'.$ext.' '. number_format(converte($parc, $v_compra), 2, ',', '.').'</b></td>';
										}else{
											$x = 0;	
										  $html .='	<td colspan="1" style="width: 25%; text-align:left;"><b>'.$dt.'</b></td>
																<td colspan="1" style="width: 25%; text-align:left;"><b>'.$ext.' '. number_format(converte($parc, $v_compra), 2, ',', '.').'</b></td>
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
									   $html .='<td colspan="1" style="width: 25%; text-align:left;"><b>'.$dt.'</b></td>
									        <td colspan="1" style="width: 25%; text-align:left;"><b>'.$ext.' '.number_format(converte($VlrUltparc, $v_compra), 2, ',', '.').'</b></td>
								        </tr>';
									}else if($resto == 0){
									       $html .='<td colspan="1" style="width: 25%; text-align:left;">&nbsp;</td>
									           <td colspan="1" style="width: 25%; text-align:left;">&nbsp;</td>
								           </tr>';
									
									}else{
										$html .='<td colspan="1" style="width: 25%; text-align:left;">&nbsp;</td>
									      <td colspan="1" style="width: 25%; text-align:left;">&nbsp;</td>
								        </tr>';
									}

								}
								
								
								$i=0;
								$x=0;
								
								// Completmento do espaço caso as parcelas sejam menores que 8
								for($i = 0; $i < $resto; $i++){
									if($limite == 1){
										$html .='<td colspan="1" style="width: 25%; text-align:left;">&nbsp;</td>
												<td colspan="1" style="width: 25%; text-align:left;">&nbsp;</td>
										</tr>';
									}else{
										if($x == 0){
											$x = 1;
											$html .='
											 <tr>
												<td colspan="1" style="width: 25%; text-align:left;">&nbsp;</td>
												<td colspan="1" style="width: 25%; text-align:left;">&nbsp;</td>';
										}else{
											$x = 0;	
											$html .='<td colspan="1" style="width: 25%; text-align:left;">&nbsp;</td>
												<td colspan="1" style="width: 25%; text-align:left;">&nbsp;</td>
											 </tr>';
										}
									}
									
								}
								
								
								
								$html .='<tr>
									<td colspan="4" style="width: 100%" >&nbsp;</td>									
								</tr>
								<tr>
									<td colspan="4" style="width: 100%; text-align:left;">
										 O prêmio será ajustado no final de vigência desta apólice, conforme estabelecido nas condições em anexo.
									</td>
								</tr>
								
								<tr>
								     <td colspan="3" style="width: 70%; text-align:left;">Nome da corretora</td>
									 <td colspan="1" style="width: 30%; text-align:left;">Cód SUSEP da corretora</td>
								</tr>
								<tr>
								    <td colspan="3" style="width: 70%; text-align:left;">
                                        '.substr($Corretor,0,68).'
								    </td>
							        <td colspan="1" style="width: 30%; text-align:left;">'.$codigoSusep.'</td>
								</tr>
								
							</table>
				    </div>
					
					<br/>
					
					<div class="rounded" style="">
					       <table style="border: 0; width: 100%;" >
							  <tr>
								<td colspan="4" style="width: 100%; text-align:left;">
								   Para validade do presente contrato, a seguradora representada por seus procuradores, assina no local e data abaixo.
								</td>
							  </tr>
							  <tr>
								<td colspan="4" style="width: 100%; text-align:left;">São Paulo, 30/11/2013</td>
								
							  </tr>
							  <tr>
								<td colspan="4" style="width: 100%; text-align:left;">Pela seguradora</td>
							  </tr>
							  <tr>
								<td colspan="4" style="width: 100%;">&nbsp;</td>
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
								<td style="width: 50%; text-align:center;"><b>Everton Fauth</b></td>
								<td style="width: 50%; text-align:center;"><b>Rose Cordeiro</b></td>
							  </tr>
							  <tr>
							  	<td>&nbsp;</td>
							  	<td>&nbsp;</td>
								<td style="width: 50%; text-align:center;">Diretor Comercial</td>
								<td style="width: 50%; text-align:center;">Diretora Técnica</td>
							  </tr>
							 
					        </table>
				   </div>';
		$html .= '<div style="clear:both">&nbsp;</div>
		               <div style="width:450px; float: right;" id="disclame">'.$disclame_retorno. '</div>
				  
				   
					
					  
	</body>
    </html>';

   	$html = utf8_encode($html);
   	$mpdf->allow_charset_conversion = true;
   	$mpdf->charset_in = 'UTF-8';
   	$mpdf->WriteHTML($html);

   	$mpdf->Output($pdfDir.$key.$arq_name, \Mpdf\Output\Destination::FILE);
?>