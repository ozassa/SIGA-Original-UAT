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

	// Endereço do logotipo
	$logo  = '../../images/logo.jpg';
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
								<span style="font-weight: bold; font-size: 10pt;">PROPOSTA DE SEGURO DE CRÉDITO À EXPORTAÇÃO</span>
						</div>	
						<div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>
						<div style="text-align: right;"><span style="font-style:italic; font-size: 8pt;">NÚMERO DA PROPOSTA: </span><span style="font-weight:bold;font-size:8pt;">'. $dados['contrat'] .'/ '.$dados["nProp"].'</span></div>
					</htmlpageheader>

					<htmlpageheader name="myheader">
						<div style="text-align: center ;">
								<img src="'.$logo_peq.'" style="width: 80; height: 40;" />
						</div><br>
	
						<div style="text-align: left;">
								<span style="font-weight: bold; font-size: 10pt;">PROPOSTA DE SEGURO DE CRÉDITO À EXPORTAÇÃO</span>
						</div>	
	
						<div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>
					</htmlpageheader>
						
					<htmlpagefooter name="myfooter">
						<table style="width:100%; border: 0;" >
							<tr>
								<td style="width: 22%;">&nbsp;</td>
								<td style="width: 56%; text-align:center; font-size: 8pt;">
										Página {PAGENO} de {nb}
										<br><br>

										'.$enderecoEmp.'<br>
										'.$siteEmp.'
										
								</td>
	
								<td style="width: 22%;">&nbsp;</td>
							</tr>
						</table>
							
					</htmlpagefooter>
					<sethtmlpageheader name="myheader2" value="on" show-this-page="1" />
					<sethtmlpageheader name="myheader" value="on" show-this-page="0" />
					<sethtmlpagefooter name="myfooter" value="on" />
					
					<div id="sublinhado">DADOS DO PROPONENTE:</div><br>
			<table style="font-size: 12pt; width: 100%; border: 0;">					
				<tr>
					<td style="width: 25%;">Razão Social: </td><td style="width: 75%;"><span style="font-weight: bold; font-size: 12pt;"><div id="cobtexto">'.strtoupper(trim($name)).'</div></span></td>
				</tr>

				<tr>
					<td style="width: 25%;">Endereco: </td><td style="width: 75%;"><div id="cobtexto">'.$endcompleto.'</div></td>
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
					<td style="width: 25%;">Corretor Nomeado: </td><td style="width: 75%;"><div id="cobtexto">'.($MultiCorretor != ''? $MultiCorretor: $corretor).'</div></td>			
				</tr>
			</table> 
					
			<br>
			<div style="border-top: 1px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>

			<br><div id="sublinhado">COBERTURA:</div>					

			<table style="font-size: 12pt; width: 100%; border: 0;">
				<tr>
					<td colspan="1" style="font-weight: bold; width: 8%;">1.</td>
					<td colspan="2" style="font-weight:bold; width: 92%;">ABRANGÊNCIA DO SEGURO:</td>			
				</tr>

				<tr>
					<td colspan="1" style="width: 8%;">&nbsp;</td>
					<td colspan="1" style="font-weight: bold; width: 8%;">1.1</td>
					<td colspan="1" style="font-weight: bold; width: 84%;">Natureza das vendas seguradas:</td>			
				</tr>

				<tr>
					<td colspan="2" style="width: 16%">&nbsp;</td>
					<td colspan="1" style="width: 84%;"><div id="cobtexto">'.$products.'</div></td>			
				</tr>
				
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>

				<tr>
					<td colspan="1" style="font-weight: bold; width: 8%;">2.</td>
					<td colspan="2" style="font-weight:bold; width: 92%;">RISCOS COBERTOS:</td>			
				</tr>

				<tr>
					<td colspan="1" style="width: 8%;">&nbsp;</td>
					<td colspan="1" style="font-weight: bold; width: 8%;">2.1</td>
					<td colspan="1" style="font-weight: bold; width: 84%;">TIPO DE IMPORTADOR:</td>			
				</tr>

				<tr>
					<td colspan="2" style="width: 16%">&nbsp;</td>
					<td colspan="1" style="width: 84%;">Privado</td>			
				</tr>

				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>

				<tr>
					<td colspan="1" style="width: 8%;">&nbsp;</td>
					<td colspan="1" style="font-weight: bold; width: 8%;">2.2</td>
					<td colspan="1" style="font-weight: bold; width: 84%;">PAÍSES COBERTOS:</td>			
				</tr>

				<tr>
					<td colspan="2" style="width: 16%">&nbsp;</td>
					<td colspan="1" style="width: 84%;">a) Risco comercial: todos, exceto Brasil.</td>												 
				</tr>';
			   
			   if($riscopolitico){				   	
					$html .=	'<tr>
							<td colspan="2" style="width: 16%">&nbsp;</td>
							<td colspan="1" style="width: 84%;"><div id="cobtexto">b) Risco político: '. $riscopolitico . ' </div></td>
						</tr>';
			   }

			   $percCoverage = (int)$percCoverage;
				
			$html .=	'	

				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>

				<tr>
					<td colspan="1" style="width: 8%;">&nbsp;</td>
					<td colspan="1" style="font-weight: bold; width: 8%;">2.3</td>
					<td colspan="1" style="font-weight: bold; width: 84%;">PERCENTAGEM DE COBERTURA:</td>			
				</tr>

				<tr>
					<td colspan="2" style="width: 16%">&nbsp;</td>
					<td colspan="1" style="width: 84%;"><div id="cobtexto">'. round($percCoverage).'%</div></td>			
				</tr>

				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>

				<tr>
					<td colspan="1" style="width: 8%;">&nbsp;</td>
					<td colspan="1" style="font-weight: bold; width: 8%;">2.4</td>
					<td colspan="1" style="font-weight: bold; width: 84%;">TAXA DE PRÊMIO:</td>			
				</tr>

				<tr>
					<td colspan="2" style="width: 16%">&nbsp;</td>
					<td colspan="1" style="width: 84%;"><div id="cobtexto">'.number_format($tx,4,",",".").'%  aplicável ao volume de exportações</div></td>			
				</tr>

				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>

				<tr>
					<td colspan="1" style="width: 8%;">&nbsp;</td>
					<td colspan="1" style="font-weight: bold; width: 8%;">2.5</td>
					<td colspan="1" style="font-weight: bold; width: 84%;">PRÊMIO MÍNIMO:</td>			
				</tr>

			    <tr>
			       <td colspan="2" style="width: 16%">&nbsp;</td>
				   <td colspan="1" style="text-align: justify; width: 84%;">';

					$sqlvig1 = "select count(*) as quantidade from Periodo_Vigencia where i_Inform = ".$idInform;
					$rres1 = odbc_exec($db, $sqlvig1);
					$qtdeVig  = odbc_result($rres1, "quantidade");

					
					$num  = $qtdeVig;
				
					if($num > 1){
						$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
						$rres = odbc_exec($db, $sqlvig);

						while(odbc_fetch_row($rres)){
							$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
							$html .= '<div id="cobtexto">'.$mo.' '. number_format(odbc_result($rres,'v_Premio'), 2, ",", ".").' ('.$valorext.') pelo período compreendido entre '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'.<br><br></div>';
						}
						$html .= 'Prêmio Mínimo Total por todo período de seguro:<br>';
						$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).').<br></div>';
					} else{
						$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).').<br></div>';
					}

				   $html .= '</td>			
			    </tr>';				
						      
				if($jurosMora){
					$html .= '
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>

						<tr>
							<td colspan="1" style="width: 8%;">&nbsp;</td>
							<td colspan="1" style="font-weight: bold; width: 8%;">2.6</td>
							<td colspan="1" style="font-weight: bold; width: 84%;">CONDIÇÕES ESPECIAIS DE COBERTURA:</td>			
						</tr>

						<tr>
							<td colspan="2" style="width: 16%">&nbsp;</td>
							<td colspan="1" style="text-align: justify; width: 84%;"><div id="cobtexto">O SEGURADO contrata cobertura acessória de juros operacionais e moratórios, cujo adicional de prêmio é de 4% sobre o prêmio da apólice.</div></td>			
						</tr>';
				}
								 			 
				$html .= '
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<tr>
						<td colspan="1" style="font-weight: bold; width: 8%;">3.</td>
						<td colspan="2" style="font-weight:bold; width: 92%;">MOEDA DA APÓLICE</td>			
					</tr>						

					<tr>
					   	<td colspan="1" style="width: 8%;">&nbsp;</td>
						<td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">A moeda da apólice é o '. strtolower($ext) .' ('.$mo.')</div></td>   			
					</tr>
						
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
				
					<tr>
						<td colspan="1" style="font-weight: bold; width: 8%;">4.</td>
						<td colspan="2" style="font-weight:bold; width: 92%;">TAXA DE CÂMBIO</td>			
					</tr>
					<tr>
					   	<td colspan="1" style="width: 8%;">&nbsp;</td>
						<td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">A conversão de quaisquer valores será sempre efetuada mediante aplicação da taxa de câmbio divulgada pelo Banco Central do Brasil PTAX800.
							</div>
					 	</td>   			
					</tr>	
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					
					<tr>
					       	<td colspan="1" style="font-weight: bold; width: 8%;">5.</td>
						<td colspan="2" style="font-weight:bold; width: 92%;">LIMITE MÁXIMO DE INDENIZAÇÃO</td>			
					</tr>';

				if($limPagIndeniz){
					$html .= '
						<tr>
					       		<td colspan="1" style="width: 8%;">&nbsp;</td>
							<td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">'.number_format($limPagIndeniz, 0, '', '.') .' vezes o prêmio pago por cada período de 12 meses de vigência da apólice.</div></td>
						</tr>';
				} else{
					$html .= '
						<tr>
					       		<td colspan="1" style="width: 8%;">&nbsp;</td>
							<td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">O Limite máximo para pagamento de indenizações por período de vigência da apólice é de '.$mo. ' ' .number_format($ValorLMI, 2, ',', '.') .' (' . strtolower($ExtValorLMI) .').</div></td>
						</tr>';
				}

				$html .= '
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<tr>
					       	<td colspan="1" style="font-weight: bold; width: 8%;">6.</td>
						<td colspan="2" style="font-weight:bold; width: 92%;">PRAZO MÁXIMO DE CRÉDITO</td>			
					</tr>

					<tr>
					       	<td colspan="1" style="width: 8%;">&nbsp;</td>
						<td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">'.$periodoMaxCredito.' dias contados a partir da data do embarque internacional das mercadorias vendidas ou da data do faturamento dos serviços prestados.</div></td>
					</tr>

					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<tr>
					       	<td colspan="1" style="font-weight: bold; width: 8%;">7.</td>
						<td colspan="2" style="font-weight:bold; width: 92%;">PRAZO PARA NOTIFICAÇÃO DE AMEAÇA DE SINISTROS</td>			
					</tr>

					<tr>
					   	<td colspan="1" style="width: 8%;">&nbsp;</td>
						<td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">'. ($periodoMaxCredito + 30).' dias contados a partir da data do embarque internacional das mercadorias vendidas ou da data do faturamento dos serviços prestados.</div></td>
					</tr>

					<tr>
					    <td colspan="1" style="width: 8%;">&nbsp;</td>
						<td colspan="2" style="text-align:justify; width: 92%;"><div id="cobtexto">No caso de prorrogação da data de vencimento, dentro das condições constantes no item 2.2.3 da cláusula 2 das condições gerais da apólice, o prazo é de 30 dias contados do novo vencimento.</div></td>
					</tr>

					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<tr>
					       	<td colspan="1" style="font-weight: bold; width: 8%;">8.</td>
						<td colspan="2" style="font-weight:bold; width: 92%;">VIGÊNCIA DA APÓLICE</td>			
					</tr>
                   ';
			   
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
									<td colspan="1" style="width: 8%;">&nbsp;</td>
									<td colspan="2" style="text-align: justify; width: 92%;">
									<div id="cobtexto">
									A apólice entra em vigor no dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Ini'),0,10)).' e terá validade até o dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Fim'),0,10)).', cujo período equivale ao período de seguro.
									<br><br></div>';
						
						//$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).')<br></div>';

						
						$indx1 = 0;
						
						if ($indx > 1) {
							$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
							$rres = odbc_exec($db, $sqlvig);

							while(odbc_fetch_row($rres)){
								$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
								$html .= '<div id="cobtexto">Período '.odbc_result($rres,'n_Preriodo').' &ndash; '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'<br></div>';
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
					       	<td colspan="1" style="width: 8%;">&nbsp;</td>
							<td colspan="2" style="text-align:justify; width: 92%;">
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
					
					$html .= '<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<tr>
						<td colspan="1" style="font-weight: bold; width: 8%;">9.</td>
						<td colspan="2" style="font-weight:bold; width: 92%;">MÓDULOS</td>			
					</tr>

					<tr>
					       	<td colspan="1" style="width: 8%;">&nbsp;</td>
						<td colspan="2" style="width: 92%;">Os seguintes módulos fazem parte desta proposta:</td>						   			
					</tr> 
			</table>
						
			<table style="font-size: 12pt; width: 100%; border: 0;"> ';
				//Início da exibição dos módulos
						
				$qryM = "select a.i_Modulo, b.Cod_Modulo,b.Grupo_Modulo,b.Titulo_Modulo, 
					cast(a.Desc_Modulo as nvarchar(3900)) as Desc_Modulo
					from Inform_Modulo a inner join Modulo b on a.i_Modulo = b.i_Modulo 
					where a.idInform = ". $idInform. " order by b.Ordem_Modulo";

				$mod = odbc_exec($db,$qryM);

				$GrupoModulo = "";
				while(odbc_fetch_row($mod)){ 
					$descricao_Modulo = odbc_result($mod,'Desc_Modulo');
					//print $descricao_Modulo;
							   
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

					if(odbc_result($mod,'Cod_Modulo') == "B4.04"){
						$html .= '									  									  
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;">
								<div id="cobtexto">- O valor de limite de crédito máximo referente da cláusula 1 deste módulo é de '.$mo.' '.$b404NivelMax.' ('.$b404NivelMaxExt.').<br></div>
								<div id="cobtexto">- A percentagem segurada para estes compradores é de '.round($mod_b404).'% (ICMS, IPI e ISS incluídos).</div></td>
							</tr>

							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;">
								<div id="cobtexto">O nome do Gerente de crédito é <strong>'.$GerenteNome.'.</strong><br></div>
                                                		<div id="cobtexto">O procedimento de gerenciamento de crédito está anexado ao presente contrato.</div>
							</tr>

							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> ';					
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
										<table style="font-size: 12pt; width: 100%; border: 0;"> ';
											$i = 0;											
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
													<tr><td colspan="1"><b>Cep: </b>'.$nu_cep.'</td><td><b>Cidade: </b>'.$no_cidade.' - <b>UF: </b>'.$no_estado.'</b></td></tr>';
											}
													
											$html .= $empre.'</table>
									</td>
								</tr>';	
						}
					}else if (odbc_result($mod,'Cod_Modulo') == "B26.04"){	
						$mod_b2604 = (int)$mod_b2604;							       
						$html .= '									  									  
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;">
									<div id="cobtexto">- O nível máximo de crédito referente à cláusula 1 deste módulo é de '. $mo.' '.$b2604NivelMax.' ('.$b2604NivelMaxExt.').<br></div>
									
									<div id="cobtexto"><br>
									- A percentagem segurada referente à cláusula 1.2 deste módulo é de '.round($mod_b2604).'%.</div></td>
							</tr>
										  
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> ';
					}else if (odbc_result($mod,'Cod_Modulo') == "B28.01"){
						$html .= ' 
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;">
									<div id="cobtexto">O nível máximo de crédito referente à cláusula 1 deste módulo é de  '. $mo.' '.$b2801NivelMax.' ('.$b2801NivelMaxExt.').<br/></div>
									<div id="cobtexto">As empresas de informações comerciais referentes à cláusula 1.4 deste módulo são SERASA e BOA VISTA SERVIÇOS.<br/></div>
									<div id="cobtexto">A percentagem segurada referente à cláusula 1.6 deste módulo é de '.round($b2801Perc).'% (ICMS, IPI e ISS incluídos).<br/><br/></div>
																				
									<div id="cobtexto">Inclui-se na Cláusula 1-ÁREA DE LIMITE DE CRÉDITO DISCRICIONÁRIO, deste mesmo Módulo o item 1.7, com a seguinte redação:<br></br/> </div>
												
									<div id="cobtexto">1.7 – O SEGURADO não poderá conceder crédito  a comprador que, anteriormente ao faturamento da mercadoria ou prestação de serviço, tenha sido objeto de recusa total, redução ou cancelamento de Limite de Crédito por parte da SEGURADORA, na vigência de qualquer apólice emitida pela SEGURADORA a favor do SEGURADO.”</div>
								</td>
							</tr>

							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> ';
					}else if(odbc_result($mod,'Cod_Modulo') == "D1.01"){
						$val_extnas = str_replace(")"," ", $extnas);						
						$html .= ' 
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">O limite mínimo para <b>notificação</b> de <b> ameaça de sinistro é de </b>'.$val_extnas.').</div> </td>
							</tr>';
					}else if(odbc_result($mod,'Cod_Modulo') == "D2.01"){										  
						$html .= '<tr>
								   <td colspan="1" style="width: 8%;">&nbsp;</td>
								   <td colspan="1" style="width: 8%;">&nbsp;</td>
								   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">O valor da franquia é de '.$mo.' '. $d201 .' ('. $valorExtD201 .')<br></div></td>
								 </tr>';
					}else if(odbc_result($mod,'Cod_Modulo') == "D4.01"){
						$val_franquia_anual = str_replace(")."," ", $franquia_anual);
						 $html .= '<tr>
							   <td colspan="1" style="width: 8%;">&nbsp;</td>
							   <td colspan="1" style="width: 8%;">&nbsp;</td>
							   <td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A franquia anual global é de '.$mo.' '. $val_franquia_anual.'). <br></div></td>
						  </tr>';
							
					}else if(odbc_result($mod,'Cod_Modulo') == "D6.02"){
						$nivel = strtolower($numberExtensive->extensive(number_format($nivel_d602,2, '.',''),$currency));
						$d602 = "O nível de pequenos sinistros é de: ".$mo." ".number_format($nivel_d602,2,',','.')." (".$nivel." ". strtolower($ext).")";
						$html .= '
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">'.$d602.'</div></td>
							</tr>';
					}else if(odbc_result($mod,'Cod_Modulo') == "D7.01"){
						$html .= '
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">'.$d701.'</div></td>
							</tr>';	
					}else if (odbc_result($mod,'Cod_Modulo') == "F3.05"){
						if ($numParc == 1){
							$html .= ' 
								<tr>
									<td colspan="1" style="width: 8%;">&nbsp;</td>
									<td colspan="1" style="width: 8%;">&nbsp;</td>
									<td colspan="2" style="text-align: justify; width: 84%;">
									<div id="cobtexto">O período de declaração é '.$periodo.'<br></div>
										<div id="cobtexto">A forma de declaração é volume total de negócios aberto por número 
										de fatura comercial, importador e valor.<br></div>
										<div id="cobtexto">O prêmio mínimo será pago em 01 parcela à vista.</div></td>
								</tr>';
						}else{
							$html .= '
								<tr>
									<td colspan="1" style="width: 8%;">&nbsp;</td>
									<td colspan="1" style="width: 8%;">&nbsp;</td>
									<td colspan="2" style="text-align: justify; width: 84%;">
									<div id="cobtexto">O período de declaração é '.$periodo.'.<br></div>
										<div id="cobtexto">A forma de declaração é volume total de negócios aberto por número 
										de fatura comercial, importador e valor.<br></div>
										<div id="cobtexto">O prêmio mínimo será pago em '.$numParc.' parcelas iguais e '.$periodo1.'</div>
									</td>
								</tr>';
						}
					}else if(odbc_result($mod,'Cod_Modulo') == "F9.02"){
						$html .= '
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">'.$bonus.'</div></td>
							</tr>';	
					}else if(odbc_result($mod,'Cod_Modulo') == "F13.02"){
						$perPart1 = (int)$perPart1;
						$perPart0 = (int)$perPart0;
						
						$html .= ' 
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A percentagem referente ao item a deste módulo é de '. round($perPart0) .'% ('.$extpart0.').<br></div>
									<div id="cobtexto">A percentagem referente ao item b deste módulo é de '. round($perPart1) .'% ('.$extpart1.').</div></td>
							</tr>  
						';
					}else if(odbc_result($mod,'Cod_Modulo') == "F14.02"){
						$html .= ' 
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A percentagem referente ao item a deste módulo é de '. round($perPart0) .'% ('.$extpart0.').<br></div>
									<div id="cobtexto">A percentagem referente ao item b deste módulo é de '. round($perPart1) .'% ('.$extpart1.').</div></td>
							</tr>  
						';
					}else if(odbc_result($mod,'Cod_Modulo') == "F15.02"){
						$html .= ' 
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;"><div id="cobtexto">A percentagem referente ao item a deste módulo é de '. round($perPart0) .'% ('.$extpart0.').<br></div>
									<div id="cobtexto">A percentagem referente ao item b deste módulo é de '. round($perPart1) .'% ('.$extpart1.').</div></td>
							</tr>  
						';
					}else if (odbc_result($mod,'Cod_Modulo') == "F33.01"){
						$html .= '
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;">';
													           
									if($taxa_analise > 0) 
										$html .= '<div id="cobtexto">A tarifa de análise cadastral é de R$ '. $taxa_analise.' ('.$extAnalise.')<br></div>';

									if($taxa_monitoramento > 0)
										$html .= '<div id="cobtexto">A tarifa de monitoramento cadastral é de R$ '. $taxa_monitoramento.' ('.$extMonit.')</div>';

								$html .= '</td>
							</tr>';
					}

					if ($descricao_Modulo != '' || odbc_result($mod,'Desc_Modulo') != ''){
						$html .= '
							<tr>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="1" style="width: 8%;">&nbsp;</td>
								<td colspan="2" style="text-align: justify; width: 84%;">
								<div id="cobtexto">'. nl2br(odbc_result($mod,'Desc_Modulo')).'</div>
								</td>
							</tr>';
					}
									
					$html .= '<tr>
						<td colspan="4">&nbsp;</td>
					</tr>';
				}
			
    //die();	
    $countNumber = 10;

	$html .= '</table>
		<br>
		<table style="font-size: 12pt; width: 100%; border: 0;"> ';


        if($riscopolitico != ''){			
			$html .= '
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>

			<tr>
			    <td colspan="1" style="font-weight: bold; width: 8%;">'.$countNumber.'.</td>
				<td colspan="3" style="font-weight:bold; width: 92%;">RISCO POLÍTICO</td>
			</tr>';

           	$html .= '
											<tr>
											    <td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
													<td colspan="3" style="text-align: justify; width: 92%;"><div id="cobtexto">10.1 - Consideram-se riscos políticos cobertos pela apólice a ocorrência dos atos ou fatos  seguintes:</div></td>
											</tr>
											<tr>
													<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											    <td colspan="3" style="text-align:justify; width: 92%;">
													<div id="cobtexto"><br>a-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inadimplência do importador empresa pública.</div>
													<div id="cobtexto"><br>b-&nbsp;&nbsp;&nbsp;&nbsp;Guerra, declarada ou não, no país do importador, com exceção de guerra, declarada ou não, entre dois ou mais dos seguintes países: França, República Popular da China, Rússia, Reino Unido e Estados Unidos da América.</div>
													<div id="cobtexto"><br>c-&nbsp;&nbsp;&nbsp;&nbsp;Moratória estabelecida em caráter geral no país do importador e mais genericamente qualquer decisão ou ato normativo decretado pelo Governo do país onde está domiciliado o importador proibindo ou exonerando este último do pagamento do débito com o Segurado.</div>
													<div id="cobtexto"><br>d-&nbsp;&nbsp;&nbsp;&nbsp;Promulgação de lei (ou de regulação com força de lei) no pais de domicílio do importador interditando importação de bens ou execução de serviços.</div>
													<div id="cobtexto"><br>e-&nbsp;&nbsp;&nbsp;&nbsp;Evento de não transferência de divisas decretado pelo país do importador que impeçam o repasse do valor depositado por este último em banco oficial dentro do seu país, tendo o importador efetuado todas as formalidades requeridas para a transferência.</div>
											    </td>
										    </tr>		
											<tr>
												<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
												<td colspan="3" style="text-align:justify; width: 92%;">
													<div id="cobtexto">10.2 – A cobertura de Risco Político da apólice não abrange o(s) país(es) excluído(s) no item 2.2 (b)  deste instrumento.</div>
												</td>
											</tr>		';
	   
			$countNumber++;
	   }

       /*if ($exibe_franq == 1){
		  $html .= '
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>

			<tr>
				<td colspan="1" style="font-weight: bold; width: 8%;">'. $countNumber .'.</td>
				<td colspan="3" style="font-weight:bold; width: 92%;">FRANQUIA ANUAL GLOBAL</td>
			</tr>';

           	$html .= '
			<tr><td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
				<td colspan="3" style="text-align: justify; width: 92%;">
					A franquia anual global é de '.$mo.' '. $franquia_anual.'<br>
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
          	$html .= '
												<tr>
													<td colspan="4">&nbsp;</td>
												</tr>

												<tr>
													<td colspan="1" style="font-weight: bold; width: 8%;">'. $countNumber .'.</td>
													<td colspan="3" style="font-weight:bold; width: 92%;">ADEQUAÇÃO DE PRÊMIO</td>
												</tr>';
											            
									          	$html .= '
												<tr><td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
													<td colspan="3" style="text-align: justify; width: 92%;">
													<div id="cobtexto">
														Caso o valor das indenizações pagas durante o período de seguro superar a 
									                     			percentagem de sinistralidade de '.round($ad_sinistr).'% do <b>prêmio</b> pago correspondente 
									                     			ao mesmo período de seguro, um <b>prêmio</b> complementar será faturado.<br></div>
									                     			<div id="cobtexto">Este <b>prêmio</b> complementar será calculado retroativamente ao início da 
									                     			apólice, aplicando uma taxa de prêmio de adequação de '.round($ad_premio).'% sobre 
									                     			a taxa de prêmio mencionada na proposta, multiplicada pelo faturamento 
									                     			segurável realizado durante todo o período de seguro.<br></div>
									                     			<div id="cobtexto">A aplicação da taxa 
									                    			retroativamente não impedirá uma eventual revisão da taxa para período de 
									                   			seguro seguinte.
									        </div>
													</td>
												</tr>';
												$countNumber++;
      	}

      	$temempcol = isset($temempcol) ? $temempcol : 0;
     	// Condição Especial de Cobertura de Coligadas 
     	if(($temempcol == 1) || ($condespcol != "")){
          	$html .= '
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>

			<tr>
				<td colspan="1" style="font-weight: bold; width: 8%;">'. $countNumber .'.</td>
				<td colspan="3" style="font-weight:bold; width: 92%;"><div id="cobtexto">EXTENSÃO DE COBERTURA PARA OPERAÇÕES REALIZADAS ATRAVÉS DE COLIGADAS NO EXTERIOR</div></td>
			</tr> ';

          	$html .= '
			<tr><td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
				<td colspan="3" style="text-align: justify; width: 92%;"><div id="cobtexto">'.$condespcol.'</div>
				</td>
			</tr>';
          $countNumber++;

     	}

      	// exbibe as condições especiais
      	if($exibe_cond == 1){
          	$html .= '
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>

			<tr>
				<td colspan="1" style="font-weight: bold; width: 8%;">'. $countNumber .'.</td>
				<td colspan="3" style="font-weight:bold; width: 92%;">CONDIÇÕES COMPLEMENTARES</td>
			</tr> ';
		           
          	$html .= '
			<tr><td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
				<td colspan="3" style="text-align: justify; width: 92%;">
					<div id="cobtexto">
						'.$condicoes_especiais.'
					</div>
				</td>
			</tr>';
			$countNumber++;
      	}
	  
	$mpdf->AddPage();

      $html .= '</table><pagebreak /> ';
	  
	  $html .= '<div id="cobtitulo"><h3>CONDIÇÕES PARA ACEITAÇÃO</h3></div>
                   <div id="cobtexto">Fazem parte desta proposta as CONDIÇÕES GERAIS e as CONDIÇÕES ESPECIAIS (módulos), devidamente aprovadas conforme processo 
                        SUSEP n° '. $dados['c_SUSEP'] .' e plenamente reconhecidas pelo SEGURADO, assim como o QUESTIONÁRIO apresentado pelo mesmo.<br><br></div>
                        <div id="cobtexto">Depois de devidamente protocolada a proposta assinada pelo SEGURADO, a '.$nomeEmpSBCE.' terá 
                        o prazo de 15 (quinze) dias para se manifestar a respeito da aceitação do seguro.  Caso não haja nenhuma manifestação, neste 
                        prazo, por parte da SEGURADORA, o seguro estará automaticamente aceito.  Em caso de não aceitação da proposta, a SEGURADORA enviará
                        uma notificação ao SEGURADO no prazo máximo de 15 (quinze) dias e devolverá quaisquer valores de prêmio eventualmente pagos.</div></li>
						
	     <br><br>';
     
      $html .= '<br>
						      <table style="font-size: 12pt; width: 100%; border: 0;">
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;"><div>________________________,______de_____________________de_________</div></td>
										</tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;"><div>______________________________________</div></td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td colspan="3" style="font-weight:bold; width: 92%;">'.$name.'</td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;"><div>Nome:</div></td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;"><div>RG:</div></td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;"><div>CPF:</div></td>
										</tr>
										<tr>
											<td  colspan="1" style="font-weight:bold; width: 8%;">&nbsp;</td>
											<td  colspan="3" style="width: 92%;"><div>Cargo:</div></td>
										</tr>
									</table>
								<br>';

      $html .= '<table style="width:100%; border: 0;" >
	              	<tr>
							    	<td style="width: 35%;">&nbsp;</td>
							    	<td style="text-align:right; width: 65%;">
	                  	<div style="text-align:right;" id="disclame">'.$disclame_retorno.' </div>
	                  </td>
	                </tr>
			    			</table>';

      $html .= '
					</body>
    		</html>';

   	$html = utf8_encode($html);
   	$mpdf->allow_charset_conversion = true;
   	$mpdf->charset_in = 'UTF-8';
   	$mpdf->WriteHTML($html);

   	$mpdf->Output($pdfDir.$key.$arq_name, \Mpdf\Output\Destination::FILE);
?>