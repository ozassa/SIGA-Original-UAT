<?php 

    include_once("policyData.php");

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
    //$logo  = $root .'images/logo.jpg';
	$logo  = $root .'images/logo_sbce.gif';
	
 

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
                li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; text-align:justify}
                li:before {content: counters(item, "."); counter-increment: item; }
				
				ul			{list-style-type: none; font-weight:normal } 
				ul li		{padding: 3px 0px;color: #000000;text-align:justify} 

                
				
				#cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:11pt; text-align:justify;}
				#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:11pt; text-align:justify; font-weight:bold; text-decoration:underline;}
				#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:8pt; text-align:right;}
				
				</style>
				
				</head>
				<body>
				<htmlpageheader name="myheader">';
		 
      $html .= ' <!--mpdf
					<htmlpageheader name="myheader">
						 <div style="text-align: right;">
							<img src="'.$logo.'" style="width: 80; height: 60px;" />
							<!-- <img src="'.$logo.'" style="width: 80; height: 40px;" /> -->
						 </div><br>
						 
						 <div style="width:370px; text-align:right; float:right;">
						   '.$nomeEmpSBCE.'
						   Rio de Janeiro, '.$datahoje.'
						 </div>
					 			
					</htmlpageheader>
					
					<htmlpagefooter name="myfooter">
						 <table style="width:100%; border: 0;" >
							 <tr>
								 <td style="width: 22%;">&nbsp;</td>
								 <td style="width: 56%; text-align:center; font-size: 8pt;">									
										  '.$retornoRodape.'
										  
									 
								</td>
								<td style="width: 22%;">&nbsp;</td>
							</tr>
				     	</table>
						
					</htmlpagefooter>
					
					<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
					<sethtmlpagefooter name="myfooter" value="on" />
					mpdf-->';
					
					
					
	  $html .= '  				  
				  '.strtoupper(trim($name)).'<br>
				  
				'.trim($contato).'<br>
				<div style="font-size:10pt;">'.trim($oContact).'</div>
				
				<br><br>
				Prezado Segurado,<br><br>
				Informamos que se encontra disponível no site da '.$nomeEmpSBCE.'
				('.$siteEmpSBCE.') a Ficha de Aprovação de Limites de Crédito referente à Apólice de Seguro de Crédito à
				Exportação n.º '.$apoNum.', que relaciona os limites de crédito solicitados pela '.strtoupper(trim($name)).' e os
				valores efetivamente aprovados por esta Seguradora.
				<br><br>
				Salientamos que os importadores elencados na Ficha de Aprovação de Limites de Crédito permanecerão sob
				constante monitoramento no decorrer da vigência da Apólice, de modo que qualquer redução ou rescisão do
				respectivo limite de crédito será informada a esta empresa através de correio eletrônico.
				<br><br>
				Além disso quaisquer solicitações de aumento dos limites de crédito vigentes, bem como de inclusão ou exclusão de
				importadores do âmbito de cobertura da Apólice deverão ser efetuadas através de acesso ao site da Seguradora.
				<br><br>
				Enfatizamos que todas as alterações nos limites de crédito vigentes (aumento, redução ou rescisão), bem como a
				atribuição de limite de crédito a importadores novos, somente produzirão efeito após informadas ao segurado e
				registradas na Ficha de Aprovação de Limite de Crédito.
				<br><br>
				Permanecemos à disposição para o esclarecimento de eventuais dúvidas e para quaisquer informações adicionais
				que se façam necessárias. <b>Portanto, a cobertura securitária proporcionada pela Apólice estará limitada ao
				valor do limite de crédito atribuído pela Seguradora para determinado importador, vigente na data do
				embarque das mercadorias.</b>
				
				<br><br>
				
				<div style="text-align:center;">
					Atenciosamente,
				</div>
				<br><br><br>
				<div style="text-align:center;">
				    Elisa Salomão
				</div>				
				<div style="text-align:center;">
				    Departamento de Crédito
				</div>';			
					


      $html .= '
     
    
	</body>
    </html>';

   	$html = utf8_encode($html);
   	$mpdf->allow_charset_conversion = true;
   	$mpdf->charset_in = 'UTF-8';
   	$mpdf->WriteHTML($html);

   	$mpdf->Output($pdfDir.$key.$arq_name, \Mpdf\Output\Destination::FILE);
?>