<?php
/*
 * Created on 15/05/2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
    require_once("../RolePrefix.php");
    require_once("../../pdfConf.php");
    include_once("../../../gerar_pdf/MPDF45/mpdf.php");
	require_once("../../dbOpen.php"); 
	

    function dataconvert($dt){
		// leitura das datas
		$dia     = date('d');
		$mes     = date('m');
		$ano     = date('Y');
		$semana  = date('w');
		
		
		// configura��o mes
		
		switch ($mes){
		
			case 1: $mes = "Janeiro"; break;
			case 2: $mes = "Fevereiro"; break;
			case 3: $mes = "Mar�o"; break;
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
	
	
		// configura��o semana
		
		switch ($semana) {
		
		case 0: $semana = "Domingo"; break;
		case 1: $semana = "Segunda Feira"; break;
		case 2: $semana = "Ter�a Feira"; break;
		case 3: $semana = "Quarta Feira"; break;
		case 4: $semana = "Quinta Feira"; break;
		case 5: $semana = "Sexta Feira"; break;
		case 6: $semana = "S�bado"; break;
		
		}
		
	  $data =  $dia.' de '. $mes. ' de '. $ano; 
	  return $data; 
    }


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

		$opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
        'format' => 'A4',
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 35,
        'margin_bottom' => 25,
        'margin_header' => 10,
        'margin_footer' => 10
        ];
    
    	$mpdf=new  \Mpdf\Mpdf($opt);

	   //$mpdf=new mPDF('win-1252','A4-L','','',20,15,48,25,10,10); 
		// $mpdf=new mPDF('win-1252','A4','','',10,10,35,25,10,10); 
		$html = ob_get_clean();
		// $mpdf->useOnlyCoreFonts = true;    // false is default
		//$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("Codigo de Aprocacao");
		$mpdf->SetAuthor($nomeEmp);
		$mpdf->SetWatermarkText(""); // fundo marca d�gua
		$mpdf->showWatermarkText = true;
		$mpdf->watermark_font = 'DejaVuSansCondensed';
		$mpdf->watermarkTextAlpha = 0.1;
		$mpdf->SetDisplayMode('fullpage');
		
		
		
		// Endere�o do logotipo
		$logo  = '../../images/logo.jpg';
	 
	
	   // In�cio do arquivo montando primeiro o CSS
	   
			$html = '<html>
					<head>
					<style>
					body {font-family: Arial, Helvetica, sans-serif;
						font-size: 12pt;
					}
					p {    margin: 0pt;
					}
					
					table.bordasimples {border-collapse: collapse;}
					table.bordasimples tr td {border:2px solid #000000;}
					
									
					ol {counter-reset: item; font-weight:bold; }
					li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 12pt; text-align:justify}
					li:before {content: counters(item, "."); counter-increment: item; }
					
					ul			{list-style-type: none; font-weight:normal } 
					ul li		{padding: 3px 0px;color: #000000;text-align:justify} 
	
					
					
					
					#redondo {padding:60px; border:3px #000000 solid; border-radius:15px; -moz-border-radius:15px; -webkit-border-radius:15px;} 
				   
					
					 div.rounded {
						border:1mm solid #000000; 
						background-color: #FFFFFF;
						border-radius: 3mm / 3mm;
						background-clip: border-box;
						padding: 1em;
					 }
					
					
					
					#cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
					#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
					#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
					
					</style>
					
					</head>
					<body>
					<htmlpageheader name="myheader">';
			 
		  $html .= ' <!--mpdf
						<htmlpageheader name="myheader">
						 <table width="100%">
						  <tr>
						    <td colspan = "4"  style="text-align:right;">							 
								  <img src="'.$logo.'" widht ="60" height="30"/>
							  
						    </td>						
						  </tr>	
						  <tr>
						    <td colspan = "4" style="text-align: center;">							 
								<span style="font-weight: bold; font-size: 14pt;">C�DIGO DE APROVA��O</span>							 
						    </td>
						  </tr>
						  
						</table>
				
						</htmlpageheader>
						
						<htmlpagefooter name="myfooter">
							 <table width="100%" border="0">
								 <tr>
									 <td width="22%">&nbsp;</td>
									 <td width="56%" style="text-align:center; font-size: 8pt;">									
											  
											  P�gina {PAGENO} de {nb}
										 
									</td>
									<td width="22%">&nbsp;</td>
								</tr>
							</table>
							
						</htmlpagefooter>
						
						<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
						<sethtmlpagefooter name="myfooter" value="on" />
						mpdf-->
						
						<div align="left" style="font-size: 11pt;">'.dataconvert(date('d/m/Y')).'</div>
						<br>
						
						<!--
						<div class="rounded">
							teste de borda redonda
						</div>
						-->
						
									
							
							<table width="100%" style="border:solid 1px #000;" cellspacing="0" cellpadding="3">					
							  <tr bgcolor="#CCCCCC">
							     <td width="5%" style="border:solid 1px #000;"><strong>N�</strong></td>
								 <td width="10%" style="border:solid 1px #000;"><strong>C�digo</strong></td>
								 <td width="85%" style="border:solid 1px #000;"><strong>Descri��o</strong></td>
									
							  </tr>';
							
							 $i = 1;
							 $cur = odbc_exec($db, "SELECT * FROM tb_Aprovacao ORDER BY codigo ASC");
							 
							 while (odbc_fetch_row($cur)) { 
								 $html .= '<tr>	
											   <td style="border:solid 1px #000; font-size:12px;">'.odbc_result($cur, 1).'</td>
											   <td style="border:solid 1px #000; font-size:12px;">'.odbc_result($cur, 2).'</td>	
											   <td style="border:solid 1px #000; font-size:12px;">'.odbc_result($cur, 3).'</td>
										   </tr>'; 
										
								$i++;	  
							 }	
						
			
						
						
						 $html .= '
						</table>
							</body>
						</html>';
	  
	  
	  
				   $html = utf8_encode($html);
				   $mpdf->allow_charset_conversion=true;
				   $mpdf->charset_in='UTF-8';
				   $mpdf->WriteHTML($html);
				   
				   //$mpdf->Output();
				   $pdfDir = '../../download/';
				  
				   $mpdf->Output($pdfDir.'codigoAprovacao.pdf','F'); 
				   
				   $mpdf->Output();
				   //echo $html;
				   // $url_pdf = $host.'src/download/'.$key.'codigoAprovacao.pdf';


  
?>
