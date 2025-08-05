<?php 

    include_once("policyData.php");

    $parc = $_GET['parc'];

	$id_empresa = $i_Empresa;
	
	If($i_Empresa == 2){
		$id_empresa = 5;
	}
	  
      	$sqlp = "SELECT Count(p.i_Inform) as num_parcelas FROM Parcela p WHERE p.i_Inform = ".$idInform."";
      	$res  = odbc_exec($db,$sqlp);
	$num_parcelas = odbc_result($res, "num_parcelas");

	for($parcela = $parc; $parcela <= $num_parcelas; $parcela++){
    		$verif = $num_parcelas - $parcela;

	    	If ($verif == 0 && $VlrUltparc > 0 ){
        		$valPar = $VlrUltparc;
        		$parc =  $VlrUltparc;
	    	}

		// 2020/10/02 - AIP: Inclui o número da fatura (IM_Parcela.i_Parcela) na query
		$sqlp = "SELECT p.*, x.i_Parcela as idP 
				from [OIM].OIM.dbo.IM_Emissao e 
				inner join [OIM].OIM.dbo.IM_Parcela x on x.i_Emissao = e.i_Emissao 
							and x.t_Parcela = 100 and x.d_Cancelamento is null 
				left  join SBCE.dbo.Inform i on e.n_Empresa = case when i.i_Empresa = 1 then 1 else 5 end 
							and cast(e.n_Apolice as int) = i.n_Apolice 
				left  join SBCE.dbo.Parcela p on i.id = p.i_Inform and x.n_Parcela = p.n_Parcela 
					and cast(e.n_Endosso as int) = p.n_Endosso 
				Where p.i_Inform = ".$idInform."
				And p.t_Parcela = 100
				And p.n_Parcela = ".$parcela."
				And p.n_Endosso = 0 
				Order By p.n_Endosso DESC, p.n_Parcela DESC";

        $parcela = (int)$parcela;
$idInform = (int)$idInform;

		$sqlp = "SELECT TOP 1 z.* 
			FROM (SELECT TOP $parcela p.*, x.i_Parcela as idP 
				from [OIM].OIM.dbo.IM_Emissao e 
				inner join [OIM].OIM.dbo.IM_Parcela x on x.i_Emissao = e.i_Emissao 
							and x.t_Parcela = 100 and x.d_Cancelamento is null 
				left  join SBCE.dbo.Inform i on e.n_Empresa = case when i.i_Empresa = 1 then 1 else 5 end 
							and cast(e.n_Apolice as int) = i.n_Apolice 
				left  join SBCE.dbo.Parcela p on i.id = p.i_Inform and x.n_Parcela = p.n_Parcela 
							and cast(e.n_Endosso as int) = p.n_Endosso 
				Where p.i_Inform = $idInform
				And p.t_Parcela = 100
				Order By p.n_Endosso, p.n_Parcela) z
			Order By z.n_Endosso DESC, z.n_Parcela DESC";


		$res = odbc_exec($db, $sqlp);

		$d_venc = odbc_result($res, "d_Vencimento");
		$valPar = odbc_result($res, "v_Parcela");
		$idP    = odbc_result($res, "idP");

		$hc_ano = substr($d_venc,0,4);
	  	$hc_mes = substr($d_venc,5,2);
		$hc_dia = substr($d_venc,8,2);

	  	//$five = mkdate($hc_ano,$hc_mes + $periodo * ($parcela - 1),$hc_dia);
		$five = mkdate($hc_ano,$hc_mes,$hc_dia);
	  	$five = substr($five,8,2)."/".substr($five,5,2)."/".substr($five,0,4);

		$opt = ['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
		        'format' => 'A4',
		        'mode' => 'UTF-8'
       		];

		$mpdf=new  \Mpdf\Mpdf($opt);
		$mpdf->SetTitle("Fatura");
		$mpdf->SetAuthor($nomeEmp);
		$mpdf->SetWatermarkText("");
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
							body {
								font-family: Arial,Helvetica,sans-serif;
								font-size: 12pt;
							}
							p {
								margin: 0pt;
							}
							ol {
								counter-reset: item; 
								font-weight:bold; 
							}
							li {
								display: block; 
								font-family: Arial,Helvetica,sans-serif; 
								font-size: 12pt; 
								text-align:justify;
							}
							li:before {
								content: counters(item, "."); 
								counter-increment: item;
							}
							ul {
								list-style-type: none; 
								font-weight:normal;
							}
							ul li {
								padding: 3px 0px;
								color: #000000;
								text-align:justify;
							}			
							#cobtexto {
								font-family: Arial,Helvetica,sans-serif; 
								font-size:12pt; 
								text-align:justify;
							}
							#sublinhado {
								font-family: Arial,Helvetica,sans-serif; 
								font-size:12pt; 
								text-align:justify; 
								font-weight:bold; 
								text-decoration:underline;
							}
							#disclame  {
								font-family: Arial,Helvetica,sans-serif; 
								font-size:8pt; 
								text-align:right;
							}
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
							<table style="width: 100%; border: 0; ">
								<tr>
									<td colspan="1" style="width: 30%;">&nbsp;</td>					
									<td colspan="1" style="text-align:center; font-weight: bold; font-size: 14pt; width: 40%; ">FATURA '.$idP.'/'.$ano.'</td>
									<td colspan="1" style="width: 30%;">&nbsp;</td>
								</tr>
								<tr>
								   <td colspan="3" style="width: 100%;">&nbsp;</td>						  
								</tr>
							</table>
							
							<div class="rounded">
									  <table style="width: 100%; border: 0; ">
										<tr>
										   <td colspan="1" style="width: 33%;"><b>Apólice n°: </b>'.$apoNum.'</td>
										   <td colspan="1" style="width: 33%;"><b>Endosso n°:</b> 0 </td>
										   <td colspan="1" style="width: 34%;"><b>Proposta n°:</b> '.$contract.'</td>
										</tr>
										<tr>
											<td colspan="3" style="width: 100%;"><b>Vigência da apólice:</b> '.$today.'</td>
										</tr>
										<tr>
											<td colspan="3" style="width: 100%;"><b>Nome segurado/sacado:</b> '.$name.'</td>
										</tr>
										<tr>
											<td colspan="3" style="width: 100%;"><b>Endereço:</b> '.$end.'</td>
										</tr>
										<tr>
											<td colspan="1" style="width: 50%;"><b>CEP: </b>'.$cep.'</td>
											<td colspan="2" style="width: 50%;"><b>CNPJ:</b> '. arruma_cnpj($cnpj).'</td>
										</tr>
									  </table>	
							</div>
							<br>
							<div class="rounded">
								<table style="width: 100%; border: 0; ">
									<tr>
									   <td colspan="3" style="width: 100%;"><b>Descrição:</b></td>
									</tr>					
									<tr>
									   <td colspan="3" style="width: 100%;">Valor referente a parcela '.$parcela.'/'.$num_parcelas.' da apólice supracitada.</td>
									</tr>
								</table>
							</div>
							<br>
							<div class="rounded">
									<table style="width: 100%; border: 0; ">
										<tr>

											<td colspan="3" style="text-align:center;"><b>VALORES EM '. utf8_decode($DescMoeda).'</b></td>

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

										  $ibancode = "";
								   										  
										  if ($moeda == "1") {
											$Simbolo_moeda = 'R$';
											 $prPrint = 'R$ '. number_format($pr, 2, ',', '.');
											 $valParc = 'R$ '. number_format($valPar, 2, ',', '.');
											 $agencia = "1778-7";
											 $conta   = "5053-9";
											 
										  }else if ($moeda == "2") {
										  	$Simbolo_moeda = 'USD';
											 $prPrint = 'USD '. number_format($pr, 2, ',', '.');
											 $valParc = 'USD '. number_format($valPar, 2, ',', '.');
											 $agencia = "1778-7";
											 $conta   = "002532";
											
										  }else if ($moeda == "6") {
										  	$Simbolo_moeda = 'EUR';
											 $prPrint = 'EUR '. number_format($pr, 2, ',', '.');
											 $valParc = 'EUR '. number_format($valPar, 2, ',', '.');
											 $agencia = "1778-7";
											 $conta   = "5053-9";
											 $ibancode = '<br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IBAN: LU410330233308690475<br>';
											 
										  }

										$html .= '<tr>
											<td colspan="3" style="width: 100%;"><b>Valor da Parcela: </b>'.$valParc.' ('.utf8_decode($valorParcExt).')</td>
										</tr>
										
										<tr>
											<td colspan="3" style="width: 100%;"><b>Vencimento da parcela:</b> '.$five.'</td>
										</tr>
										
										<tr>
											<td colspan="3" style="width: 100%;"><b>Prêmio Mínimo:</b> '.$prPrint.'</td>
										</tr>
									</table>
					        </div>
							<br>
							<div class="rounded">
								<table style="width: 100%; border: 0; ">
									<tr>
										<td colspan="3" style="text-align:center; width: 100%;"><b>INSTRUÇÕES</b></td>
									</tr>
									<tr>
										<td colspan="3" style="font-size:12pt; text-align:justify; width: 100%;">Creditar conta abaixo conforme Resolução n° 003525 BACEN de 20 de dezembro de 2007 e instruções a seguir:<br><br>
										Todas as despesas bancárias, no Brasil e no Exterior, incorrerão <b>por conta do Segurado</b>.<br>
										Ordens formatadas no sistema SWIFT:<br><br>
										
										<b>CAMPO 56</b> - BANCO CORRESPONDENTE:&nbsp;&nbsp;<b>THE BANK OF NEW YORK MELLON</b><br>
										SWIFT (BIC CODE):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>IRVTUS3NXXX</b><br><br>
										
										<b>CAMPO 57</b> - CONTA CORRENTE Nº:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>8901482129</b><br>
										BANCO BENEFICIÁRIO:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>BANCO SANTANDER (BRASIL) S.A &ndash;</b><br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>LUXEMBOURG BRANCH</b><br>
										SWIFT (BIC CODE):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>BSCHLULLMCY</b><br><br>

										<b>CAMPO 59</b> &ndash; NOME DO BENEFICIÁRIO:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>/33308690475</b><br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>COFACE DO BRASIL</b><br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>SEGUROS DE CRÉDITO S/A</b><br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PC JOÃO DURAN ALONSO, 34<br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;10º ANDAR SÃO PAULO &ndash; SP &ndash; BRAZIL<br>'.$ibancode.'

										<br>
										Após vencimento cobrar multa de ' . $Simbolo_moeda. ' ' . $ValorMulta . '<br>

										</td>
									</tr>
								  </table>
							 </div>
								<br>
							 
										
						   <table style="width: 100%; border: 0; ">
							  <tr>
								<td style="width: 25%;">&nbsp;</td>
								
							  </tr>
							   <tr>
								  <td colspan="1" style="width: 35%;">&nbsp;</td>
							   </tr>
						   </table>
						  
						   
							
							  
			</body>
			</html>';

			//<td colspan="1" style="text-align: right; width: 65%; ">'.$cp.'</td>


			//<td style="text-align:right; width: 75%;">
			//						<div style="text-align:right;" id="disclame">*A Seguradora Brasileira de Crédito à Exportação S.A 
			//					 	foi incorporada pela Coface do Brasil Seguros de Crédito S/A conforme Portaria SUSEP n° 7.640 
			//						de 15/06/2020, que está, desta forma, autorizada a comercializar o produto 
			//					  	SUSEP no. '.$c_SUSEP.'</div>
			//				  </td>	

	   	$html =mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
	   	$mpdf->allow_charset_conversion = true;
	   	$mpdf->charset_in = 'UTF-8';
	   	$mpdf->WriteHTML($html);

	    //$mpdf->Output($pdfDir.$key.str_replace($parc_num.".pdf", $parcela.".pdf", $arq_name), \Mpdf\Output\Destination::FILE);
		$mpdf->Output();
		$j++;
	}

?>