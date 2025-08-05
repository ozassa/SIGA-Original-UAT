<?php 
	
    include_once("policyData.php");

    $opt = ['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
	        'format' => 'A4',
	        'margin_left' => 20,
	        'margin_right' => 15
        	];

    $mpdf=new  \Mpdf\Mpdf($opt);
	$mpdf->SetTitle("Carta");
	$mpdf->SetAuthor($nomeEmp);
	$mpdf->SetWatermarkText("");
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.1;
	$mpdf->SetDisplayMode('fullpage');
	
	// Endere�o do logotipo
//    	$logo  = '../../logo_pdf.jpg';
  	$logo  = $root .'images/logo_coface_co.jpg';
    	$assinatura  = $root .'images/Assinatura_Rose.jpg';
 
 	// In�cio do arquivo montando primeiro o CSS
   
        $html = '<html>
			<head>
				<style>
					body {font-family: Arial, Helvetica, sans-serif;
						font-size: 11pt; ; color: #002060 !important;
					}
					p {    margin: 0pt;
					}
					
					ol {counter-reset: item; font-weight:bold; }
                li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align:justify}
                li:before {content: counters(item, "."); counter-increment: item; }
				
				ul			{list-style-type: none; font-weight:normal } 
				ul li		{padding: 3px 0px;color: #000000;text-align:justify} 

                
				
				#cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;}
				#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify; font-weight:bold; text-decoration:underline;}
				#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
				
				#img1{
					width: 660px;
					height: 65px;
					background:url('.$logo.') no-repeat;

				}
				
				div.redondo {
						padding:20px; 
						border:2px #000000 solid;
						border-radius:15px;
						-moz-border-radius:15px; 
						-webkit-border-radius:15px;
						width:100%;
						}  
				
				</style>
				
				</head>
				<body>
				';
		 
      $html .= ' 
					<htmlpageheader name="myheader">
     						 <div style="text-align: left;">
    							<img src="'.$logo.'" style="width: 86px; height: 46px;" />
							</div>
					</htmlpageheader>
					
					
					<htmlpagefooter name="myfooter">
							<table style="width:100%; border: 0;" >
							 <tr>
								 <td style="width: 22%;">&nbsp;</td>
								 <td style="width: 56%; text-align:center; font-size: 7pt;">									
										  '.$retornoRodape.'									 
								</td>
								<td style="width: 22%;">&nbsp;</td>
							</tr>
				     	</table>		
					</htmlpagefooter>
					
					
					
					<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
					<sethtmlpagefooter name="myfooter" value="on" />
					
					
					<br>
					<div>
					   S�o Paulo, '.$datahoje.'
					</div>
					<br>
					�
					<br>
					'.strtoupper(trim($name)).'<br><br>
					
					Prezado Segurado, <br><br>';
					
					//$idant = 0;
					
					If ($interest == 1){
						$Texto_Juros_Mora = '<br>&bull;	Condi��es especiais da ap�lice - Cobertura acess�ria de juros operacionais e morat�rios';
					}else{
						$Texto_Juros_Mora = '';
					}

					// 2018/09/12 - AIP: Identificar os informes de Tradeliner para layout das cartas em PDF (carta.php / carta_Dom.php)
					If ($tpProd == "RTL"){
						$Texto_Documentos = '
								&bull;	Certificado de seguros<br>
								&bull;	Especifica��o da ap�lice<br>
								&bull;	Condi��es gerais<br>
								&bull;	Condi��es Especiais - Op��es<br>
								&bull;	Fatura de pagamento<br>
								&bull;	Contrato de presta��o de servi�os assinado pelas partes<br>
								&bull;	Contrato de Cobran�a assinado pelas partes';
					}else{
						$Texto_Documentos = '
								&bull;	Certificado de seguros<br>
								&bull;	Condi��es particulares<br>
								&bull;	Condi��es gerais<br>
								&bull;	M�dulos<br>
								&bull;	Fatura de pagamento<br>
								&bull;	Contrato de presta��o de servi�os assinado pelas partes';
					}
					
					if($idant == 1){
					
						$html .= '<div id="cobtexto">
							� com grande satisfa��o que a Coface lhe d� as boas-vindas. Temos certeza que esta parceria trar� resultados 
							ainda maiores aos neg�cios de sua empresa. Parab�ns por esta importante escolha!<br><br>
							
							Sua decis�o em se tornar um cliente da Coface representa muito mais que uma simples ap�lice de seguros. 
							Ela representa a seguran�a de realizar suas transa��es comerciais de cr�dito de forma tranq�ila e a 
							comodidade de focar cada vez mais em seus neg�cios, sem comprometer o relacionamento com seus clientes.<br><br>
							
							Sendo um cliente da Coface, subsidi�ria da Compagnie Fran�aise d&rsquo;Assurance pour le Commerce Ext�rieur, 
							com sede na Fran�a, voc� passa a usufruir da cobertura de uma empresa s�lida com mais de 75 anos de experi�ncia 
							e 100 mil clientes em 200 pa�ses.<br><br>
							
							Voc� est�, agora, recebendo o seu Kit de boas-vindas Coface, que cont�m:<br><br>
							
							<div style="margin-left:35px;">
								'.$Texto_Documentos.'
								'.$Texto_Juros_Mora.'
							</div>
							<br>
							
							Leia atentamente todas as informa��es contidas em seu kit e em caso de d�vidas, entre em contato com o seu Corretor ou com o
							seu Gerente de Relacionamento, que estar� � sua inteira disposi��o.<br><br>
							
							Nossa miss�o � garantir que voc� continue realizando suas transa��es comerciais sem se preocupar 
							com imprevistos e nem burocracias que possam tirar o foco de seus neg�cios.<br><br> 
							
							A Coface agradece a sua confian�a e reafirma o compromisso de continuar a oferecer as 
							melhores solu��es em servi�os de gerenciamento de cr�dito do mercado.
						</div>';
					}else{
						$html .= '<div id="cobtexto">
							A Coface tem a satisfa��o de ter a sua empresa mais uma vez como nossa segurada.<br><br>
							
							Com a renova��o da sua ap�lice de seguro de cr�dito, voc� est�, agora, recebendo os seguintes documentos:<br><br>
							
							<div style="margin-left:35px;">
								'.$Texto_Documentos.'
								'.$Texto_Juros_Mora.'
							</div>
							<br>
							
							Leia atentamente todas as informa��es contidas em seu kit e em caso de d�vidas, entre em contato com o 
							seu Corretor ou com o seu Gerente de Relacionamento, que estar� � sua inteira disposi��o.<br><br>
				
							Reiteramos o nosso compromisso de garantir que sua empresa continue realizando suas transa��es comerciais, 
							sem se preocupar com imprevistos e nem burocracias que possam tirar o foco de seus neg�cios.<br><br>
				
							Agradecemos mais uma vez pela confian�a depositada em nossa seguradora e, continue contando conosco para a 
							prote��o dos seus neg�cios de cr�dito.
					    </div>';
					}
					
					
					$html .= '<br>		
					
						   <table style="width:100%; border: 0;" >
							  <tr>
								 <td style="width: 25%;">&nbsp;</td>
								 <td style="width: 75%; text-align:right;">
								 
							  </td>	
							  </tr>
							 
						   </table>
						   
						   
					
					Atenciosamente,
					
					
					
					<br><br>
					<div style="text-align:left;">
						<img src="'.$assinatura .'" style="width: 140px; height: 80px;">						
					</div>
							
							<div style="text-align:left;">Rose Cordeiro</div>
							<div style="text-align:left;">Diretora T�cnica</div>	

							<table style="width:100%; border: 0; font-size: 9px;" >
					  <tr>
							<td colspan="4" style="text-align:right; width: 100%;">
								Ouvidoria Coface Seguros de Cr�dito S/A: 0800 591 4787
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
			    </table>				  
	</body>
    </html>';

   	//$html = utf8_encode($html);
   	$html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
   	//$mpdf->setAutoLoadImages(false);

   	$mpdf->allow_charset_conversion = true;
   	$mpdf->charset_in = 'UTF-8';
   	$mpdf->WriteHTML($html);

   	//$mpdf->Output($pdfDir.$key.$arq_name, \Mpdf\Output\Destination::FILE);
   	$mpdf->Output();

?>