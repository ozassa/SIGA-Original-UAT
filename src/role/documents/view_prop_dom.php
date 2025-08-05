<?php 

    include_once("propData.php");

    $opt = ['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
	        'format' => 'A4',
	        'margin_left' => 20,
	        'margin_right' => 15,
	        'margin_top' => 50,
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

    $opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
        'format' => 'A4',
        ];
	
    // Endere�o do logotipo
    $logo  = '../../images/logo.jpg';
    $logo_peq  	= '../../images/logo_peq.jpg';
 
    // In�cio do arquivo montando primeiro o CSS 
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
				
				ul		{list-style-type: none; font-weight:normal } 
				ul li		{padding: 3px 0px;color: #000000;text-align:justify}                
				
				#cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
				#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
				#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}

				#img1{
					width: 660px;
					height: 65px;
					

				}

			</style>		
		</head>

		<body>';
			 
					$html .= '
      				<htmlpageheader name="myheader2">
						<div style="text-align: center;">
								<img src="'.$logo.'" style="width: 230; height: 75;" />
						</div>
						<div style="text-align: left;">
								<span style="font-weight: bold; font-size: 10pt;">PROPOSTA DE SEGURO DE CR�DITO INTERNO</span>
						</div>	
						<div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>
						<div style="text-align: left;"><span style="font-style:italic; font-size: 8pt;">'. $dados['DescSubProduto'] .'</span></div>
						<div style="text-align: right;"><span style="font-style:italic; font-size: 8pt;">N�MERO DA PROPOSTA: </span><span style="font-weight:bold;font-size:8pt;">'. $dados['contrat'] .'/ '.$dados["nProp"].'</span></div>
						<div style="text-align: right;">
							<span style="font-style:italic; font-size: 8pt;">VIG�NCIA: '.$VigIni.' - '.$VigFim.'</span>
						<div>
					</htmlpageheader>

					<htmlpageheader name="myheader">
						<div style="text-align: center;">
								<img src="'.$logo_peq.'" style="width: 80; height: 40;" />
						</div><br>
	
						<div style="text-align: left;">
								<span style="font-weight: bold; font-size: 10pt;">PROPOSTA DE SEGURO DE CR�DITO INTERNO</span>
						</div>	
	
						<div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; ">
						<div style="text-align: left;"><span style="font-style:italic; font-size: 8pt;">'. $dados['DescSubProduto'] .'</span></div>
					</htmlpageheader>
						
					<htmlpagefooter name="myfooter">
						<table style="width:100%; border: 0;" >
							<tr>
								<td style="width: 40%; text-align:left; font-size: 8pt;">'.$nomeEmp.'</td>
								<td style="width: 10%; text-align:center; font-size: 8pt;">		
									P�gina {PAGENO} de {nb}								
								</td>
	
								<td style="width: 40%; text-align:right; font-size: 8pt;">Processo SUSEP n� 15414.005252/2005-53</td>
							</tr>
						</table>

						<div style="text-align:left; font-size: 8pt; position: relative;">
							&nbsp;'.$enderecoEmp.'<br>
							&nbsp;'.$compEmp.'
						</div>
						

					</htmlpagefooter>
					<sethtmlpageheader name="myheader2" value="on" show-this-page="1" />
					<sethtmlpageheader name="myheader" value="on" show-this-page="0" />
					<sethtmlpagefooter name="myfooter" value="on" />
									
									
					<div id="sublinhado">DADOS DO PROPONENTE:</div><br>
					<table style="font-size: 12pt; width: 100%; border: 0;">					
					<tr>
					   <td style="width: 25%;"><div id="cobtexto">Raz�o Social: </div></td><td style="width: 75%;"><span style="font-weight: bold; font-size: 12pt;"><div id="cobtexto">'.strtoupper(trim($name)).'</div></span></td>
					</tr>
					<tr>
					  <td style="width: 25%;"><div id="cobtexto">Endere�o: </div></td><td style="width: 75%;"><div id="cobtexto">'.$endcompleto.'</div></td>
					</tr> 
					<tr>
					  <td style="width: 25%;">&nbsp;</td><td style="width: 75%;"><div id="cobtexto">'.$city .' - '. $uf.'</div></td>
					</tr>
					<tr>  
					  <td style="width: 25%;">&nbsp;</td><td style="width: 75%;"><div id="cobtexto">CEP '.$cep.'</div></td>
					</tr>
					<tr>
					  <td style="width: 25%;">CNPJ: </td><td style="width: 75%;"><div id="cobtexto">'.arruma_cnpj($cnpj).'</div></td>			
					</tr>
					<tr>
					  <td style="width: 25%;">Corretor nomeado: </td><td style="width: 75%;"><div id="cobtexto">'.($MultiCorretor != ''? $MultiCorretor: $corretor).'</div></td>			
					</tr>
	                </table> 
					
					<br>
					<div style="border-top: 1px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>
                    <br>
					
					
					 <div id="sublinhado">COBERTURA:</div>					
					 
					 <table style="font-size: 12pt; width: 100%; border: 0;">
					    <tr>
					       <td colspan="1" style="font-weight: bold; width: 8%;">1.</td>
						   <td colspan="2" style="font-weight:bold; width: 92%;">ABRANG�NCIA DESTE CONTRATO - PERCENTAGEM SEGURADA</td>			
					    </tr>
						<tr>
					       <td colspan="1" style="font-weight: bold; width: 8%;">&nbsp;</td>
						   <td colspan="2" style="font-weight:bold; width: 92%;"> - CUSTO DA COBERTURA</td>			
					    </tr>
						<tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="1" style="font-weight: bold; width: 8%;">1.1</td>
						   <td colspan="1" style="font-weight: bold; width: 84%;">NATUREZA DAS VENDAS SEGURADAS</td>			
					    </tr>

					    <tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="2" style="text-align: justify; width: 92%;"><div id="cobtexto">'.$products.'.</div></td>			
					    </tr>

					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>

					    <tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="1" style="font-weight: bold; width: 8%;">1.2</td>
						   <td colspan="1" style="font-weight: bold; width: 84%;">PR�MIO M�NIMO</td>			
					    </tr>

					    <tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="2" style="text-align: justify; width: 92%;">';

							$sqlvig1 = "select count(*) as quantidade from Periodo_Vigencia where i_Inform = ".$idInform;
							$rres1 = odbc_exec($db, $sqlvig1);
							$qtdeVig  = odbc_result($rres1, "quantidade");

							
							$num  = $qtdeVig;
						
							if($num > 1){
								$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
								$rres = odbc_exec($db, $sqlvig);

								while(odbc_fetch_row($rres)){
									$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
									$html .= '<div id="cobtexto">'.$mo.' '. number_format(odbc_result($rres,'v_Premio'), 2, ",", ".").' ('.$valorext.') pelo per�odo compreendido entre '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .' - IOF de 7,38% n�o inclu�do.<br><br></div>';
								}
								$html .= 'Pr�mio M�nimo Total por todo per�odo de seguro:<br>';
								$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).') - IOF de 7,38% n�o inclu�do.<br></div>';
							} else{
								$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).') por per�odo de seguro (IOF de 7,38% n�o incluso).<br></div>';
							}

						   $html .= '</td>			
					    </tr>

					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>						

					    <tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="1" style="font-weight: bold; width: 8%;">1.3</td>
						   <td colspan="1" style="font-weight: bold; width: 84%;">PERCENTAGEM SEGURADA</td>			
					    </tr>

					    <tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="2" style="text-align: justify; width: 92%;"><div id="cobtexto">'.round($percCoverage).'% (ICMS, IPI, ISS e demais tributos inclu�dos no valor total da nota fiscal de venda ou servi�o prestado).</div></td>			
					    </tr>

					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>
						
					    <tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="1" style="font-weight: bold; width: 8%;">1.4</td>
						   <td colspan="1" style="font-weight: bold; width: 84%;">TAXA DE PR�MIO</td>			
					    </tr>

					    <tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="2" style="text-align: justify; width: 92%;"><div id="cobtexto">'.number_format($tx,4,",",".").'%  aplic�vel ao faturamento (ICMS, IPI, ISS e demais tributos inclu�dos no valor total da nota fiscal de venda ou servi�o prestado).</div></td>			
					    </tr>

					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>

					    <tr>
					       <td colspan="1" style="font-weight: bold; width: 8%;">2.</td>
						   <td colspan="2" style="font-weight:bold; width: 92%;">LIMITE M�XIMO DE INDENIZA��O</td>			
					    </tr>';

					    if ($limPagIndeniz != 0) {
					    	$html .= '
						    <tr>
						      <td colspan="1" style="width: 8%;">&nbsp;</td>
							   	<td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">'.$limPagIndeniz .' vezes o pr�mio pago por per�odo de seguro.</div></td>
						    </tr>';
					    }

					    if ($ValorLMI != 0) {
					    	$ValorLMIExt = $numberExtensive->extensive(number_format($ValorLMI,0,'.',''),$currency);
					    	$html .= '
						    <tr>
						      <td colspan="1" style="width: 8%;">&nbsp;</td>
							   	<td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">'.$mo.' '. number_format($ValorLMI, 2, ",", ".").' ('.$ValorLMIExt.').</div></td>
						    </tr>';
					    }
					    	
					    $html .= '
					   	<tr>
						 		<td colspan="3">&nbsp;</td>
					    </tr>

					    <tr>
					       <td colspan="1" style="font-weight: bold; width: 8%;">3.</td>
						   <td colspan="2" style="font-weight:bold; width: 92%;">PRAZO M�XIMO DE CR�DITO</td>			
					    </tr>

					    <tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">'.$periodoMaxCredito.' dias contados a partir da data da emiss�o da fatura da mercadoria vendida ou servi�o prestado. </div></td>
					    </tr>

					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>

					    <tr>
					    <td colspan="1" style="font-weight: bold; width: 8%;">4.</td>
						   <td colspan="2" style="font-weight:bold; width: 92%;">PRAZO M�XIMO PARA EMISS�O DA NOTA FISCAL</td>			
					    </tr>
						<tr>
					      <td colspan="1" style="width: 8%;">&nbsp;</td>
						   	<td colspan="2" style="text-align:justify; width: 92%;">
						      <div id="cobtexto">N�o � aplic�vel para as vendas de mercadorias.<br><br></div>';
						             

								if($PrazoMaxEmiNota > 0){
									$html .= '<div id="cobtexto">Presta��o de servi�os: '.$PrazoMaxEmiNota.' dias, contado a partir da data da presta��o do servi�o para qual o pagamento seja devido. (somente quando tem servi�os contratados)</div>';
								}					             
						        
						   $html .= '</td>
						   			
					    </tr>	
					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>
						
					    <tr>
					       <td colspan="1" style="font-weight: bold; width: 8%;">5.</td>
						   <td colspan="2" style="font-weight:bold; width: 92%;">PRAZO PARA DECLARAR A AMEA�A DE SINISTRO</td>			
					    </tr>

					    <tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">'. ($periodoMaxCredito + 30).' dias contados a partir da data da emiss�o da fatura da mercadoria vendida ou servi�o prestado.<br> </div>
							<div id="cobtexto">No caso de prorroga��o da data de vencimento, dentro das condi��es constantes no item 2.2.3 da cl�usula 2 das CONDI��ES GERAIS, o prazo � de 30 dias contados do novo vencimento.</div>
						   </td>
					    </tr>
						
					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>

					    <tr>
					       <td colspan="1" style="font-weight: bold; width: 8%;">6.</td>
						   <td colspan="2" style="font-weight:bold; width: 92%;">MOEDA DA AP�LICE</td>			
					    </tr>						

					    <tr>
					       <td colspan="1" style="width: 8%;">&nbsp;</td>
						   <td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">A moeda da ap�lice � o '. $extM.' ('.$mo.').</div></td>
						   			
					    </tr>';
						
					    $countNumber = 7; 
						
						/*if($VigIni){
							$html .= '<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
							
								<tr>
									<td colspan="1" style="font-weight: bold; width: 8%;">'.($countNumber++).'.</td>
									<td colspan="2" style="font-weight:bold; width: 92%;">VIG�NCIA DA AP�LICE</td>			
								</tr>

								<tr>
							   		<td colspan="1" style="width: 8%;">&nbsp;</td>
							   		<td colspan="2" style="width: 92%;">A ap�lice entra em vigor no dia '.$VigIni.' e ter� validade at� o dia '.$VigFim.', cujo per�odo equivale ao per�odo de seguro.</td>
								</tr>';
						}*/
						
						if($VigIni){
							$html .= '<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
							
								<tr>
									<td colspan="1" style="font-weight: bold; width: 8%;">'.($countNumber++).'.</td>
									<td colspan="2" style="font-weight:bold; width: 92%;">VIG�NCIA DA AP�LICE</td>			
								</tr>';

							  	// verifica��es 
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
												<td colspan="1" style="width: 8%;">&nbsp;</td>
												<td colspan="2" style="text-align: justify; width: 92%;">
												<div id="cobtexto">
												A ap�lice entra em vigor no dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Ini'),0,10)).' e ter� validade at� o dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Fim'),0,10)).', cujo per�odo equivale ao per�odo de seguro.
												<br><br></div>';
									
									//$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).')<br></div>';

									// $sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
									// $rres = odbc_exec($db, $sqlvig);
									$indx1 = 0;
									
									if ($indx1 > 1) {										
										while(odbc_fetch_row($rres)){
											if ($indx1 == 0) {
												$html .= '<div id="cobtexto">Este per�odo de seguro � dividido em '.trim(strtolower(valor_extenso($indx))).' per�odos distintos de vig�ncia compreendidos entre:<br></div>';
											}

											$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
											$html .= '<div id="cobtexto">Per�odo '.odbc_result($rres,'n_Preriodo').' - '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'<br></div>';
											$indx1++;
										}
										
										if ($indx1 > 0) {
											$html .= '<br>';
										}
									}

									if($renovacao_Tacica == 1){ // campo no inform
								    	$html .= '	 
											<div id="cobtexto">
											A ap�lice poder� ser renovada automaticamente, por igual per�odo de seguro, caso n�o seja comunicada a inten��o de n�o renova��o por qualquer das partes, devendo tal comunica��o de n�o renova��o ser feita por escrito pelo SEGURADO ou pela SEGURADORA com anteced�ncia de '.$Prazo_Nao_Intencao_Renov.' dias do termo final do per�odo de seguro total. </div>
											</td>			
											</tr>
										';
									}else{
										$html .= '	 
											<div id="cobtexto">
											Revoga-se o item 12.2 das Condi��es Gerais da ap�lice o qual passar� a vigorar com a seguinte reda��o:</div>
											<div id="cobtexto">
											"A ap�lice tem dura��o definida nas CONDI��ES PARTICULARES e n�o pode ser renovada tacitamente."</div>
											</td>			
									   		</tr>
								     	';
									
									}
								}else{				   
							   
									$html .= '<tr>
								       	<td colspan="1" style="width: 8%;">&nbsp;</td>
										<td colspan="2" style="text-align:justify; width: 92%;">
										<div id="cobtexto">
										A ap�lice entra em vigor no dia '.$VigIni.' e ter� validade at� o dia '.$VigFim.', cujo per�odo equivale ao per�odo de seguro.<br></div>';
								
										if($renovacao_Tacica ==1){ // campo no inform
										    $html .= '	 
												<div id="cobtexto">
												A ap�lice poder� ser renovada automaticamente, por igual per�odo de seguro, caso n�o seja comunicada a inten��o de n�o renova��o por qualquer das partes, devendo tal comunica��o de n�o renova��o ser feita por escrito pelo SEGURADO ou pela SEGURADORA com anteced�ncia de '.$Prazo_Nao_Intencao_Renov.' dias do termo final do per�odo de seguro total. </div>
												</td>			
												</tr>
										';
										}else{
											 $html .= '	 
												<div id="cobtexto">
												Revoga-se o item 12.2 das Condi��es Gerais da ap�lice o qual passar� a vigorar com a seguinte reda��o:</div>
												<div id="cobtexto">
												"A ap�lice tem dura��o definida nas CONDI��ES PARTICULARES e n�o pode ser renovada tacitamente."</div>
												</td>			
											   	</tr>
										     ';
											
										}
								
								}
						}
						
						$html .= '<tr>
								<td colspan="3">&nbsp;</td>
							</tr>

							<tr>
					       			<td colspan="1" style="font-weight: bold; width: 8%;">'.($countNumber++).'.</td>
						   		<td colspan="2" style="font-weight:bold; width: 92%;">M�DULOS</td>			
					    		</tr>

							<tr>
					       			<td colspan="1" style="width: 8%;">&nbsp;</td>
						   		<td colspan="2" style="width: 92%;"><div id="cobtexto">Os seguintes m�dulos fazem parte integrante deste contrato:</div></td>						   			
					    		</tr>

							</table>
						
							<table style="font-size: 12pt; width: 100%; border: 0;"> ';
						
						
						//Novo formato de m�dulos
						    						//In�cio da exibi��o dos m�dulos
						
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
												   <td colspan="1" style="width: 8%;">&nbsp;</td>
												   <td colspan="3" style="font-weight:bold; width: 92%;"><div id="sublinhado">'.odbc_result($mod,'Grupo_Modulo').'</div></td>
											   </tr>';
								   }
								   
								    $Titulo = strlen(odbc_result($mod,'Titulo_Modulo'));
						            $html .= '<tr>
										   <td colspan="1" style="width: 8%;">&nbsp;</td>
										   <td colspan="1" style="width: 8%;">&nbsp;</td>
										   <td colspan="1" style="font-weight: bold; width: 8%;"><div id="cobtexto">'.odbc_result($mod,'Cod_Modulo').'</div></td>
										   <td colspan="1" style="font-weight:bold; width: 76%;"><div id="cobtexto">'.odbc_result($mod,'Titulo_Modulo').'</div></td>						   			
							                </tr>';
											/*
											if($Titulo > 47){
											   '<tr>
												   <td colspan="1" style="width: 8%;">&nbsp;</td>
												   <td colspan="1" style="width: 8%;">&nbsp;</td>
												   <td colspan="1" style="width: 8%;">&nbsp;</td>
												   <td colspan="1" style="font-weight:bold; width: 76%;">'.substr(odbc_result($mod,'Titulo_Modulo'),47,$Titulo).'</td>
												</tr>';
											}
											*/
										
								
											   
									if(odbc_result($mod,'Cod_Modulo') == "B4.04"){
										 $html .= '									  									  
										  <tr>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="2" style="text-align: justify; width: 84%;">
											   <div id="cobtexto">
											   - O valor de limite de cr�dito m�ximo referente da cl�usula 1 deste m�dulo � de R$ '.$b404NivelMax.' ('.$b404NivelMaxExt.').<br></div>
											   <div id="cobtexto">
											   - A percentagem segurada para estes compradores � de '.round($mod_b404).'% (ICMS, IPI e ISS inclu�dos).</div></td>
										  </tr>
										  <tr>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="2" style="text-align: justify; width: 84%;">
											   <div id="cobtexto">
										        O nome do Gerente de cr�dito � <strong>'.$GerenteNome.'.</strong><br></div>
                                                <div id="cobtexto">O procedimento de gerenciamento de cr�dito est� anexado ao presente contrato.</div>
										  </tr>
										  
										  <tr>
										    <td colspan="4">&nbsp;</td>
									      </tr> ';
										
									}else if (odbc_result($mod,'Cod_Modulo') == "B8.02"){
											$emp = "";
									    										 
										 $query = "SELECT * FROM ParModEsp_Maiores_Compradores WHERE idInform = '".$idInform."' ORDER BY Nome ";
                                         $cury = odbc_exec ($db, $query);
                                    
									 
												if ($cury){ 
												   $html .= '								   
														  <tr>
														   <td colspan="1" style="width: 8%;">&nbsp;</td>
														   <td colspan="1" style="width: 8%;">&nbsp;</td>
														   <td colspan="2" style="width: 84%;">											   
														   <table>';
														    
														    $i = 0;
															
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
									  
									  
									  
								      $html .=' <tr>
										 <td colspan="4">&nbsp;</td>
									   </tr>';										
									
									}else if(odbc_result($mod,'Cod_Modulo') == "B9.04"){
										 $html .= '  <tr>
											     <td colspan="1" style="width: 8%;">&nbsp;</td>
											     <td colspan="1" style="width: 8%;">&nbsp;</td>
											      <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A franquia de maiores compradores mencionada no item 1.1 deste m�dulo � de R$ '.$mod_b904.' ('.$mod_b904Ext.').</div></td>
												  </tr>
												  <tr>
													 <td colspan="4">&nbsp;</td>
												   </tr>';
										
									}else if(odbc_result($mod,'Cod_Modulo') == "B12.02"){
											$empre = "";
											
									    $query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = '".$idInform."' ORDER BY no_razao_social ";
                                              $cury = odbc_exec ($db, $query);
										  if ($cury){ 
										      $html .= '								   
												  <tr>
												   <td colspan="1" style="width: 8%;">&nbsp;</td>
												   <td colspan="1" style="width: 8%;">&nbsp;</td>
												   <td colspan="2" style="width: 84%;">											   
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
																   <tr><td colspan="2"><b>Endere�o: </b>'.$ds_endereco.', '.$nu_endereco. ($ds_complemento != "" ? " - ".$ds_complemento : "").'</td></tr>
																   <tr><td colspan="1"><b>Cep: </b>'.$nu_cep.'</td><td><b>Cidade: </b>'.$no_cidade.' - <b>UF: </b>'.$no_estado.'</b></td></tr>';
														
											        }
													
													  $html .= $empre.'</table>
												   </td>
											   </tr>';	
										
										  }
									
									
									}else if (odbc_result($mod,'Cod_Modulo') == "B26.04"){								       
							            $html .= '									  									  
										  <tr>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="2" style="text-align: justify; width: 84%;">
											   <div id="cobtexto">
											   - O n�vel m�ximo de cr�dito referente � cl�usula 1 deste m�dulo � de R$ '.$b2604NivelMax.' ('.$b2604NivelMaxExt.').
											   <br></div>
											   <div id="cobtexto">
											   <br>
											   - A percentagem segurada referente � cl�usula 1.2 deste m�dulo � de '.round($mod_b2604).'%.</div></td>
										  </tr>
										  
										  <tr>
										    <td colspan="4">&nbsp;</td>
									      </tr> ';
							
						            }else if (odbc_result($mod,'Cod_Modulo') == "B28.01"){
						    
							             $html .= ' <tr>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="2" style="text-align: justify; width: 84%;">
											   <div id="cobtexto">
											     O n�vel m�ximo de cr�dito referente � cl�usula 1 deste m�dulo � de  R$ '.$b2801NivelMax.' ('.$b2801NivelMaxExt.').<br/></div>
											   <div id="cobtexto">
												As empresas de informa��es comerciais referentes � cl�usula 1.4 deste m�dulo s�o SERASA e BOA VISTA SERVI�OS.<br/></div>
												<div id="cobtexto">
												A percentagem segurada referente � cl�usula 1.6 deste m�dulo � de '.round($b2801Perc).'% (ICMS, IPI e ISS inclu�dos).<br/><br/></div>
												<div id="cobtexto">
												Inclui-se na Cl�usula 1-�REA DE LIMITE DE CR�DITO DISCRICION�RIO, deste mesmo M�dulo o item 1.7, com a seguinte reda��o:<br></br/> </div>
												<div id="cobtexto">
												1.7 - O SEGURADO n�o poder� conceder cr�dito  a comprador que, anteriormente ao faturamento da mercadoria ou presta��o de servi�o, tenha sido objeto de recusa total, redu��o ou cancelamento de Limite de Cr�dito por parte da SEGURADORA, na vig�ncia de qualquer ap�lice emitida pela SEGURADORA a favor do SEGURADO.�   </div>

											   </td>
										 </tr>
										 <tr>
										 <td colspan="4">&nbsp;</td>
									     </tr> ';
												
									}else if(odbc_result($mod,'Cod_Modulo') == "D1.01"){
										    
											 $html .= ' <tr>
													   <td colspan="1" style="width: 8%;">&nbsp;</td>
													   <td colspan="1" style="width: 8%;">&nbsp;</td>
													   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">O limite m�nimo para notifica��o de amea�a de sinistro � de '.$extnas.'.</div></td>
											 </tr>';
									
									}else if(odbc_result($mod,'Cod_Modulo') == "D2.01"){	
									  
										$html .= '<tr>
												   <td colspan="1" style="width: 8%;">&nbsp;</td>
												   <td colspan="1" style="width: 8%;">&nbsp;</td>
												   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">O valor da franquia � de '.$mo.' '. $d201 .' ('. $valorExtD201 .').<br></div></td>
												 </tr>';
									
									}else if(odbc_result($mod,'Cod_Modulo') == "D4.01"){
										 $html .= '<tr>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A franquia anual global � de '.$mo.' '. $franquia_anual.'<br></div></td>
									      </tr>';
										
									}else if(odbc_result($mod,'Cod_Modulo') == "D6.02"){
										 $html .= '<tr>
										   <td colspan="1" style="width: 8%;">&nbsp;</td>
										   <td colspan="1" style="width: 8%;">&nbsp;</td>
										   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">'.$d602.'</div></td>
										    </tr>';
											
									}else if(odbc_result($mod,'Cod_Modulo') == "D7.01"){
									    $html .= '<tr>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">'.$d701.'</div></td>
									     </tr>';	
										 
									}else if (odbc_result($mod,'Cod_Modulo') == "F3.05"){
									        if ($numParc == 1){
												 $html .= ' <tr>
														   <td colspan="1" style="width: 8%;">&nbsp;</td>
														   <td colspan="1" style="width: 8%;">&nbsp;</td>
														   <td colspan="2" style="text-align: justify; width: 84%;">
															  <div id="cobtexto">O per�odo de declara��o � '.$periodo.'.<br></div>
															  <div id="cobtexto">
																A forma de declara��o � em arquivo Excel contendo o volume total de neg�cios aberto por comprador.<br/></div>
																<div id="cobtexto">
																O pr�mio m�nimo ser� pago em 01 parcela � vista.</div></td>
																
														   </tr>';
															   
												
											  }else{
												 $html .= '<tr>
														   <td colspan="1" style="width: 8%;">&nbsp;</td>
														   <td colspan="1" style="width: 8%;">&nbsp;</td>
														   <td colspan="2" style="text-align: justify; width: 84%;">
														   <div id="cobtexto">O per�odo de declara��o � '.$periodo.'.<br></div>
														   <div id="cobtexto">
																A forma de declara��o � em arquivo Excel contendo o volume total de neg�cios aberto por comprador.<br/> </div>
																<div id="cobtexto">
																O pr�mio m�nimo ser� pago em '.$numParc.' parcelas iguais e '.$periodo1.'</div>
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
																			And IMV.i_Modulo = 24	-- M�dulo F4.01
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
													$txt_sinist_f401[] = 'Se n�vel de sinistralidade for maior ou igual a '.round($sinist_inicial_f401).'% e menor que '.round($sinist_final_f401).'%';
													$txt_percent_f401[] = 'A percentagem de pr�mio � '.round($perc_adequacao_f401).'%.';
												} elseif($sinist_final_f401 != 0){
													$txt_sinist_f401[] = 'Se n�vel de sinistralidade for menor que '.round($sinist_final_f401).'%';
													$txt_percent_f401[] = 'A percentagem de pr�mio � '.round($perc_adequacao_f401).'%.';
												}

												$count_f401++;
											}

											for ($i=0;$i<$count_f401;$i++) { 
											  $html .= '<tr>
																   <td colspan="1" style="width: 8%;">&nbsp;</td>
																   <td colspan="1" style="width: 8%;">&nbsp;</td>
																   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">'.$txt_sinist_f401[$i].'<br/></div>
																								<div id="cobtexto">'.$txt_percent_f401[$i].'</div></td>
																 </tr>';
												if($count_f401 != ($i+1)){
													$html .= '<br>';
												}
											}

									}else if(odbc_result($mod,'Cod_Modulo') == "F9.02"){
									      $html .= '<tr>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">'.$bonus.'</div></td>
											 </tr>';	
									}else if (odbc_result($mod,'Cod_Modulo') == "F13.02"){
									    $html .= '<tr>
															   <td colspan="1" style="width: 8%;">&nbsp;</td>
															   <td colspan="1" style="width: 8%;">&nbsp;</td>
															   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A percentagem referente ao item a deste m�dulo � de '.round($partDeducao).'%.<br></div>
															   <div id="cobtexto">A percentagem referente ao item b deste m�dulo � de '.round($partLucro).'%.</div></td>
															 </tr>  
												 ';

									}else if (odbc_result($mod,'Cod_Modulo') == "F14.02"){
									    $html .= '<tr>
															   <td colspan="1" style="width: 8%;">&nbsp;</td>
															   <td colspan="1" style="width: 8%;">&nbsp;</td>
															   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A percentagem referente ao item a deste m�dulo � de '.round($partDeducao).'%.<br></div>
																	<div id="cobtexto">A percentagem referente ao item b deste m�dulo � de '.round($partLucro).'%.</div></td>
															 </tr>  
												 ';

									}else if (odbc_result($mod,'Cod_Modulo') == "F15.02"){
									    $html .= '<tr>
															   <td colspan="1" style="width: 8%;">&nbsp;</td>
															   <td colspan="1" style="width: 8%;">&nbsp;</td>
															   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A percentagem referente ao item a deste m�dulo � de '.round($partDeducao).'%.</div>
																	  <div id="cobtexto">A percentagem referente ao item b deste m�dulo � de '.round($partLucro).'%.</div></td>
															 </tr>  
												 ';
												
									}else if (odbc_result($mod,'Cod_Modulo') == "F33.01"){
										  $html .= '
										    		 <tr>
													   <td colspan="1" style="width: 8%;">&nbsp;</td>
													   <td colspan="1" style="width: 8%;">&nbsp;</td>
													   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A tarifa de an�lise cadastral � de '.$mo.' '. $taxa_analise.' ('.$extAnalise.')<br></div>
															  <div id="cobtexto">A tarifa de monitoramento cadastral � de '.$mo.' '. $taxa_monitoramento.' ('.$extMonit.')</div>
														</td>
													</tr>';
											  
									}else if (odbc_result($mod,'Cod_Modulo') == "F37.02"){						  
							  
										  $html .= '										 
											 <tr>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A forma de notifica��o � volume total de neg�cios aberto por nota fiscal.<br/>O per�odo de declara��o � mensal.</div></td>
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
																			And IMV.i_Modulo = 34	-- M�dulo F52.02
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
												$txt_percent_f5202 = 'Taxa de adequa��o de pr�mio '.round($perc_adequacao_f5202).'%';
											} elseif($sinist_inicial_f5202 != 0){
												$txt_sinist_f5202 = 'Se o percentual de sinistralidade for maior ou igual que '.round($sinist_inicial_f5202).'%';
												$txt_percent_f5202 = 'Taxa de adequa��o de pr�mio de '.round($perc_adequacao_f5202).'%';
											}

										  $html .= '<tr>
																   <td colspan="1" style="width: 8%;">&nbsp;</td>
																   <td colspan="1" style="width: 8%;">&nbsp;</td>
																   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">'.$txt_sinist_f5202.'<br/></div>
																								<div id="cobtexto">'.$txt_percent_f5202.'</div></td>
																 </tr><br>';
										}

										$html .= '<tr>
															   <td colspan="1" style="width: 8%;">&nbsp;</td>
															   <td colspan="1" style="width: 8%;">&nbsp;</td>
															   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">
															   	Fica estabelecido que o c�lculo da adequa��o de pr�mio nos termos acima mencionados ser� realizado e cobrado a cada 12 meses de  vig�ncia da Ap�lice.
															   </td>
															 </tr>';															 
									}
									
									
									
									
									if (odbc_result($mod,'Desc_Modulo') != ''){
										$html .= '
										     <tr>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="1" style="width: 8%;">&nbsp;</td>
											   <td colspan="2" style="text-align: justify; width: 84%;">
											   <div id="cobtexto">'. nl2br($descricao_Modulo).'</div>
											   </td>
											</tr>';
										
									}
									
									$html .= '<tr>
										        <td colspan="4">&nbsp;</td>
									         </tr>';
						
						} // Fim do m�dulos
						
						
       
    						
       
      // $html .= '</table>
	  
	     //      <br>
	     //      <table style="font-size: 12pt; width: 100%; border: 0;">
				  // <tr>
					 // <td colspan="1" style="font-weight: bold; width: 8%;">'.($countNumber++).'.</td>
					 // <td  colspan="1" style="width: 92%; font-weight:bold">OBRIGA��O DE NOTIFICA��O DE ATRASOS DE PAGAMENTO<td>
				  // </tr>';
               
     //  $html .= '
	    //       <tr>
				 //  <td  colspan="2" style="width: 100%; text-align:justify; font-size: 12pt;">
					// Sem preju�zo das demais obriga��o do contrato de seguro de cr�dito � exporta��o, o  SEGURADO compromete-se a notificar a SEGURADORA,
					// de quaisquer  d�vidas vencidas  e n�o pagas h�  mais de 60 dias da data original de vencimento. Esta obriga��o n�o se  aplica se, para aquela
					// d�vida, uma <b>notifica��o</b> de <b>amea�a de sinistro</b> j� tiver sido feita ou para d�vidas que j� re�nam condi��es de uma <b>notifica��o</b> de <b>amea�a de
					// sinistro</b> de  acordo com os termos do contrato de seguro.<br><br>

					// O SEGURADO dever� enviar � SEGURADORA o total de <b>notifica��es</b> de atraso de que  trata esta cl�usula , mensalmente, de forma unificada at�
					// o 5� dia �til do m�s subseq�ente  ao da apura��o.<br><br>
         
					// Caso o SEGURADO deixe de cumprir com esta obriga��o, aplicar-se-�  a cl�usula 9.4 das CONDI��ES GERAIS tanto para as d�vidas objeto de
					// notifica��o de atraso de pagamento como para as d�vidas correspondentes a entregas ou presta��es de servi�os realizados ap�s a devida data
					// de obriga��o de notifica��o de atraso de pagamento.
				 //  </td>
			  //  </tr>';
			  $html .= '</table>';

      	/*if($exibe_ad == 1){
      		$ad_sinistr = (int)$ad_sinistr;
					$ad_premio = (int)$ad_premio;
          	$html .= '
	    					      <table style="font-size: 12pt; width: 100%; border: 0;">
												<tr>
													<td colspan="1" style="font-weight: bold; width: 8%;">'. $countNumber .'.</td>
													<td colspan="3" style="font-weight:bold; width: 92%;">ADEQUA��O DE PR�MIO</td>
												</tr>';
											            
									          	$html .= '
												<tr><td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
													<td colspan="3" style="text-align: justify; width: 92%;">
														Caso o valor das indeniza��es pagas durante o per�odo de seguro superar a percentagem de sinistralidade de '.round($ad_sinistr).'% do pr�mio emitido 
														correspondente ao mesmo per�odo de seguro, um pr�mio complementar ser� faturado.<br><br>		          
									          Este pr�mio complementar ser� calculado retroativamente ao in�cio da ap�lice, aplicando o percentual de '.round($ad_premio).'% ao pr�mio emitido durante o 
									          per�odo de seguro (exclu�do IOF - 7,38%).<br><br>
									          O faturamento e cobran�a deste pr�mio complementar n�o impedir�o uma eventual revis�o da taxa para per�odo de seguro seguinte.<br>
													</td>
												</tr>';
												$countNumber++;
      	}*/

      	// exbibe as condi��es especiais
      	if($exibe_cond == 1){
          	$html .= '<table style="font-size: 12pt; width: 100%; border: 0;">
											<tr>
												<td colspan="4">&nbsp;</td>
											</tr>

											<tr>
												<td colspan="1" style="font-weight: bold; width: 8%;">'. $countNumber .'.</td>
												<td colspan="3" style="font-weight:bold; width: 92%;">CONDI��ES COMPLEMENTARES</td>
											</tr> ';
										           
								          	$html .= '
											<tr><td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
												<td colspan="3" style="text-align: justify; width: 92%;">
													<div id="cobtexto">
														'.$condicoes_especiais.'
													</div>
												</td>
											</tr>';
			  $html .= '</table>';
				$countNumber++;
      	}

	  

      $html.= '<!--mpdf '.$mpdf->AddPage().'<pagebreak /> mpdf-->';
	  
	  /*$html.= '<div id="cobtitulo"><h3>CONDI��ES PARA ACEITA��O</h3></div>
                   <div id="cobtexto">Fazem parte desta proposta as CONDI��ES GERAIS e as CONDI��ES ESPECIAIS (m�dulos), devidamente aprovadas conforme processo 
                        SUSEP n� '. $dados['c_SUSEP'] .' e plenamente reconhecidas pelo SEGURADO, assim como o QUESTION�RIO apresentado pelo mesmo.<br><br>
                        Depois de devidamente protocolada a proposta assinada pelo SEGURADO, a '.mb_strtoupper(utf8_encode($dados['Nome'])).' ter� 
                        o prazo de 15 (quinze) dias para se manifestar a respeito da aceita��o do seguro.  Caso n�o haja nenhuma manifesta��o, neste 
                        prazo, por parte da SEGURADORA, o seguro estar� automaticamente aceito.  Em caso de n�o aceita��o da proposta, a SEGURADORA enviar�
                        uma notifica��o ao SEGURADO no prazo m�ximo de 15 (quinze) dias e devolver� quaisquer valores de pr�mio eventualmente pagos.</div></li>
						
	     <br><br>';*/


			$html .= '<table style="font-size: 12pt; width: 100%; border: 0;">
										<tr>
											<td colspan="1" style="font-weight: bold; width: 8%;">'. $countNumber .'.</td>
											<td colspan="3" style="font-weight:bold; width: 92%;">CONDI��ES PARA ACEITA��O</td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td colspan="3" style="text-align: justify; width: 92%;">
												<div id="cobtexto">Fazem parte desta proposta as CONDI��ES GERAIS (Vers�o Setembro/2013) e as CONDI��ES ESPECIAIS (m�dulos), devidamente aprovadas conforme processo SUSEP
												n� 15414.005252/2005-53 e plenamente reconhecidas pelo SEGURADO, assim como o QUESTION�RIO apresentado pelo mesmo.<br><br></div>		          
							          <div id="cobtexto">Depois de devidamente protocolada a proposta assinada pelo SEGURADO, a COFACE DO BRASIL SEGUROS DE CR�DITO SA ter� o prazo de 15 (quinze) dias para se manifestar
							          a respeito da aceita��o do seguro. Caso n�o haja nenhuma manifesta��o, neste prazo, por parte da SEGURADORA, o seguro estar� automaticamente aceito. Em caso de n�o
							          aceita��o da proposta, a SEGURADORA enviar� uma notifica��o ao SEGURADO no prazo m�ximo de 15 (quinze) dias.<br><br></div>
							          <div id="cobtexto">Ao assinar esta proposta o PROPONENTE declara que o corretor '.$corretor_nome.' � o corretor nomeado para represent�-lo
							          em quaisquer quest�es referentes � Ap�lice de Seguro de Cr�dito Interno originada por esta proposta.<br></div>
											</td>
										</tr>
									</table>'; 
     
      $html.= '<br>
						      <table style="font-size: 12pt; width: 100%; border: 0;">
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;">________________________,______de_____________________de_________</td>
										</tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;">______________________________________</td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td colspan="3" style="font-weight:bold; width: 92%;">'.$name.'</td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;">Procurador ou pessoa autorizada</td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;">Nome:</td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;">RG:</td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;">CPF:</td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;">Cargo:</td>
										</tr>
									</table>
								<br>';   

      $html.= '
					</body>
    		</html>';

   	$html = utf8_encode($html);
   	$mpdf->allow_charset_conversion = true;
   	$mpdf->charset_in = 'UTF-8';
   	$mpdf->WriteHTML($html);
	
   	$mpdf->Output($pdfDir.$key.$arq_name, \Mpdf\Output\Destination::FILE);
?>