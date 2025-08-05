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
	
	// Endere�o do logotipo
    //$logo  = $root .'images/logo.jpg';
	$logo  = $root .'images/logo_sbce.gif';
	
 

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
				Informamos que se encontra dispon�vel no site da '.$nomeEmpSBCE.'
				('.$siteEmpSBCE.') a Ficha de Aprova��o de Limites de Cr�dito referente � Ap�lice de Seguro de Cr�dito �
				Exporta��o n.� '.$apoNum.', que relaciona os limites de cr�dito solicitados pela '.strtoupper(trim($name)).' e os
				valores efetivamente aprovados por esta Seguradora.
				<br><br>
				Salientamos que os importadores elencados na Ficha de Aprova��o de Limites de Cr�dito permanecer�o sob
				constante monitoramento no decorrer da vig�ncia da Ap�lice, de modo que qualquer redu��o ou rescis�o do
				respectivo limite de cr�dito ser� informada a esta empresa atrav�s de correio eletr�nico.
				<br><br>
				Al�m disso quaisquer solicita��es de aumento dos limites de cr�dito vigentes, bem como de inclus�o ou exclus�o de
				importadores do �mbito de cobertura da Ap�lice dever�o ser efetuadas atrav�s de acesso ao site da Seguradora.
				<br><br>
				Enfatizamos que todas as altera��es nos limites de cr�dito vigentes (aumento, redu��o ou rescis�o), bem como a
				atribui��o de limite de cr�dito a importadores novos, somente produzir�o efeito ap�s informadas ao segurado e
				registradas na Ficha de Aprova��o de Limite de Cr�dito.
				<br><br>
				Permanecemos � disposi��o para o esclarecimento de eventuais d�vidas e para quaisquer informa��es adicionais
				que se fa�am necess�rias. <b>Portanto, a cobertura securit�ria proporcionada pela Ap�lice estar� limitada ao
				valor do limite de cr�dito atribu�do pela Seguradora para determinado importador, vigente na data do
				embarque das mercadorias.</b>
				
				<br><br>
				
				<div style="text-align:center;">
					Atenciosamente,
				</div>
				<br><br><br>
				<div style="text-align:center;">
				    Elisa Salom�o
				</div>				
				<div style="text-align:center;">
				    Departamento de Cr�dito
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