<?php
	include_once("../../../gerar_pdf/MPDF45/mpdf.php");

	include_once("../consultaCoface.php");
	
	function dataextenso1($data) {
        	$data = explode("-",$data);
        	$dia = $data[2];
        	$mes = $data[1];
        	$ano = $data[0];

        	switch ($mes){
        		case 1: $mes = "Janeiro"; break;
        		case 2: $mes = "Fevereiro"; break;
        		case 3: $mes = "Março"; break;
        		case 4: $mes = "Abril"; break;
        		case 5: $mes = "Maio"; break;
        		case 6: $mes = "Junho"; break;
        		case 7: $mes = "Julho"; break;
        		case 8: $mes = "Agosto"; break;
        		case 9: $mes = "Setembro"; break;
        		case 10: $mes = "Outubro"; break;
        		case 11: $mes = "Novembro"; break;
        		case 12: $mes = "Dezembro"; break;
        	}

        	$mes=strtolower($mes);

        	return  ("$dia de $mes de $ano");
    	}

	$datahoje  = dataextenso1(date('Y-m-d'));

		$sqlEmp  = "SELECT Nome, CNPJ,	Endereco,	Complemento, CEP, Cidade,	Estado,	Cod_Area,	Telefone,	Bairro, Fax, HomePage
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

    	//$mpdf=new mPDF('win-1252','A4','','',20,15,48,25,10,10); 
    	$mpdf=new mPDF('win-1252','A4','','',20,15); 
    	$html = ob_get_clean();
    	$mpdf->useOnlyCoreFonts = true;    // false is default
	//$mpdf->SetProtection(array('print'));
	$mpdf->SetTitle("Carta");
	$mpdf->SetAuthor($nomeEmp);
	$mpdf->SetWatermarkText(""); // fundo marca dágua
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.1;
	$mpdf->SetDisplayMode('fullpage');
	
	// Endereço do logotipo
    	$logo  = '../../logo_pdf.jpg';
    	$assinatura  = $root .'images/Assinatura_Marcele.JPG';
 
 	// Início do arquivo montando primeiro o CSS
   
        $html = '<html>
			<head>
				<style>
					body {font-family: Arial, Helvetica, sans-serif;
						font-size: 11pt;
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
							<div style="width:100%">
								<div id="img1"></div>
							</div>
					</htmlpageheader>
					
					
					<htmlpagefooter name="myfooter">
							<table width="100%" border="0">
							 <tr>
								 <td width="22%">&nbsp;</td>
								 <td width="56%" style="text-align:center; font-size: 8pt;">									
										  '.$retornoRodape.'									 
								</td>
								<td width="22%">&nbsp;</td>
							</tr>
				     	</table>		
					</htmlpagefooter>
					
					
					
					<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
					<sethtmlpagefooter name="myfooter" value="on" />
					
					
					<br>
					<div>
					   São Paulo, '.$datahoje.'
					</div>
					<br>
					À
					<br>
					'.strtoupper(trim($name)).'<br><br>
					
					Prezado Segurado, <br><br>';
					
					//$idant = 0;
					
					If ($interest == 1){
						$Texto_Juros_Mora = '<br>•	Condições especiais da apólice - Cobertura acessória de juros operacionais e moratórios';
					}else{
						$Texto_Juros_Mora = '';
					}

					// 2018/09/12 - AIP: Identificar os informes de Tradeliner para layout das cartas em PDF (carta.php / carta_Dom.php)
					If ($tpProd == "RTL"){
						$Texto_Documentos = '
								•	Certificado de seguros<br>
								•	Especificação da apólice<br>
								•	Condições gerais<br>
								•	Condições Especiais - Opções<br>
								•	Fatura de pagamento<br>
								•	Contrato de prestação de serviços assinado pelas partes<br>
								•	Contrato de Cobrança assinado pelas partes';
					}else{
						$Texto_Documentos = '
								•	Certificado de seguros<br>
								•	Condições particulares<br>
								•	Condições gerais<br>
								•	Módulos<br>
								•	Fatura de pagamento<br>
								•	Contrato de prestação de serviços assinado pelas partes';
					}
					
					if($idant == 1){
					
						$html .= '<div id="cobtexto">
							É com grande satisfação que a SBCE lhe dá as boas-vindas. Temos	certeza que esta parceria trará resultados 
							ainda maiores aos negócios de sua empresa. Parabéns	por esta importante escolha!<br><br>
							
							Sua decisão em se tornar um cliente da SBCE representa muito mais que uma simples apólice de seguros. 
							Ela representa a segurança de realizar suas transações comerciais de crédito de forma tranqüila e a 
							comodidade de focar cada vez mais em seus negócios, sem comprometer o relacionamento com seus clientes.<br><br>
							
							Sendo um cliente da SBCE, subsidiária da Compagnie Française d’Assurance pour le Commerce Extérieur, 
							com sede na França, você passa a usufruir da cobertura de uma empresa sólida com mais de 75 anos de experiência 
							e 100 mil clientes em 200 países.<br><br>
							
							Você está, agora, recebendo o seu Kit de boas-vindas SBCE, que contém:<br><br>
							
							<div style="margin-left:35px;">
								'.$Texto_Documentos.'
								'.$Texto_Juros_Mora.'
							</div>
							<br>
							
							Leia atentamente todas as informações contidas em seu kit e em caso de dúvidas, entre em contato com o seu Corretor ou com o
							seu Gerente de Relacionamento, que estará à sua inteira disposição.<br><br>
							
							Nossa missão é garantir que você continue realizando suas transações comerciais sem se preocupar 
							com imprevistos e nem burocracias que possam tirar o foco de seus negócios.<br><br> 
							
							A SBCE agradece a sua confiança e reafirma o compromisso de continuar a oferecer as 
							melhores soluções em serviços de gerenciamento de crédito do mercado.
						</div>';
					}else{
						$html .= '<div id="cobtexto">
							A SBCE tem a satisfação de ter a sua empresa mais uma vez como nossa segurada.<br><br>
							
							Com a renovação da sua apólice de seguro de crédito, você está, agora, recebendo os seguintes documentos:<br><br>
							
							<div style="margin-left:35px;">
								'.$Texto_Documentos.'
								'.$Texto_Juros_Mora.'
							</div>
							<br>
							
							Leia atentamente todas as informações contidas em seu kit e em caso de dúvidas, entre em contato com o 
							seu Corretor ou com o seu Gerente de Relacionamento, que estará à sua inteira disposição.<br><br>
				
							Reiteramos o nosso compromisso de garantir que sua empresa continue realizando suas transações comerciais, 
							sem se preocupar com imprevistos e nem burocracias que possam tirar o foco de seus negócios.<br><br>
				
							Agradecemos mais uma vez pela confiança depositada em nossa seguradora e, continue contando conosco para a 
							proteção dos seus negócios de crédito.
					    </div>';
					}
					
					
					$html .= '<br><br>			
					
						   <table width="100%" border="0">
							  <tr>
								 <td width="25%">&nbsp;</td>
								 <td align="right" width="75%">
								  <div align="right" id="disclame">'.$disclame_retorno.'</div>
							  </td>	
							  </tr>
							 
						   </table>
						   
						   <br><br>
					
					Atenciosamente,
					
					
					
					<br><br>
					<div align="left">
						<img src="'.$assinatura .'" width="140px;" height="80px;">						
					</div>
							
							<div align="left">Marcele Lemos</div>
							<div align="left">Presidente</div>					  
	</body>
    </html>';
  
  
  
   $html = utf8_encode($html);
   $mpdf->allow_charset_conversion=true;
   $mpdf->charset_in='UTF-8';
   $mpdf->WriteHTML($html);
   
   //$mpdf->Output();
  
   
   //echo utf8_decode($html);
   
   $mpdf->Output($pdfDir.$key."Carta.pdf",F); 
   
   $url_pdf = $host.'src/download/'.$key."Carta.pdf";
  
 ?>
    
 <?php 
 
 
 //   exit();
 


if(! file_exists($prefix. "Carta.pdf")){
  //Alterado Por Tiago V N(Elumini) - 01/09/2005
  $separa = explode(" ", $data);
  list( $ano, $mes, $dia ) = split ("[/-]", $separa[0]);
  $dataform = $dia . "/" . $mes . "/" . $ano;

  /*
  $h = new Java ('java.util.HashMap');
//  if ($h != null) echo "<pre>h OK!";
  $h->put('key', $prefix. "Carta.pdf");
  $h->put('empresa', trim($name). "");
  $h->put('endereco', $address. "");
  $h->put('cidade_estado', "$city - $uf");
  $h->put('cep', $cep. "");
  $h->put('diretor', $contato. "");
  $h->put('cargo', $oContact. "");
  $h->put('num_proposta', $contract. "");
  $h->put('data', $dataform. "");
  $h->put('num_prestacoes', $num_parcelas. "");
  $h->put('contato', trim($contato). "");
  $h->put('cargo', trim($oContact). "");
  $h->put('apolice', $apoNum. "");
  //$h->put('data', $data. "");
  $h->put('dir', $pdfDir);

  $h->put('idant', $idant."");
 

  //Alterado por Tiago V N - Elumini - 13/02/2006
  $h->put('susep', $susep. "");
  $h->put('cp', $cp. "");

  $prop = new Java ('Carta', $h);
  if($prop == null){
    die("<h1>carta null</h1>");
  }else{
    $prop->generate();
  }
  */
  
}


?>

