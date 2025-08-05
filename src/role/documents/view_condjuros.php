<?php 

    include_once("policyData.php");

    $txtParcs = '';

    if($Num_Parcelas){
		if ($numParc == 1){
			$txtParcs .= "a vista.";
	    }else{
		    $txtParcs .= "em ".$Num_Parcelas." presta��es ".$t_Vencimento.".";
	    }
	}else{
 
		 if ($pvigencia!=2) {
			  if ($numParc == 1){
				$txtParcs .= "a vista.";
			  }else if ($numParc == 2){
				$txtParcs .= "em duas presta��es: 1 e mais 1 em noventa dias.";
			  }else if ($numParc == 4){
				$txtParcs .= "em quatro presta��es: 1 e mais 3 trimestrais.";
			//  }else if ($numParc == 6){
			//    $txtParcs .= "em seis presta��es: 1 e mais 5 bimestrais.";
			  }else if ($numParc == 7){
				$txtParcs .= "em seis presta��es: 1 e mais 6 mensais.";
			  }else if ($numParc == 10){
				$txtParcs .= "em dez presta��es: 1 e mais 9 mensais.";
			  }
		  
		 }else if($pvigencia==2){
			  if ($numParc == 1){
				$txtParcs .= "a vista.";
			  }else if ($numParc == 4){
				$txtParcs .= "em quatro presta��es: 1 e mais 3 trimestrais.";
			  }else if ($numParc == 7){
				$txtParcs .= "em sete presta��es: 1 e mais 6 mensais.";
			  }else if ($numParc == 8){
				$txtParcs .= "em oite presta��es: 1 e mais 7 trimestrais.";
			  }
		 
		 }
	}

    $opt = ['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
	        'format' => 'A4'
        	];

    $mpdf=new  \Mpdf\Mpdf($opt);
	$mpdf->SetTitle("CondJuros");
	$mpdf->SetAuthor($nomeEmp);
	$mpdf->SetWatermarkText("");
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.1;
	$mpdf->SetDisplayMode('fullpage');
	
	// Endere�o do logotipo
    $logo       = '../../images/logo_pdf.jpg';
	$assinatura  = $root .'images/Assinatura_Rigobello.jpg';
	
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
				
				ul			{list-style-type: none; font-weight:normal } 
				ul li		{padding: 3px 0px;color: #000000;text-align:justify} 

                
				
				#cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
				#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
				#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:8pt; text-align:right; width:480px;}
				
				#img1{
					width: 660px;
					height: 65px;
					/* correcao cfc */

				}
				
				</style>
				
				</head>
				<body>   
				
					<htmlpageheader name="myheader">
							<div style="width:100%">
								<div id="img1"></div>
							</div>
					</htmlpageheader>
				';
				
				
				
		 
      $html .= ' 				
					<br>
					<br>
					<div style="text-align:center; font-size: 12pt; text-align:center;">
						<b>CONDI��ES ESPECIAIS DA AP�LICE N� '.$apoNum.'</b>
					</div>
					<div style="text-align:center; font-size: 12pt; text-align:center;">
						<b>COBERTURA ACESS�RIA DE JUROS OPERACIONAIS E MORAT�RIOS</b>
					</div>
					<br>
					<br>
					<table style="font-size: 12pt; width: 100%; border: 0;">
					   <tr>
						   <td>
						      <b>SEGURADORA: '.$nomeEmpSBCE.'</b>
						   </td>
						</tr>
						<tr>
						   <td>'.
						      $dados['Endereco'].' - '. $dados['Complemento'].'
						   </td>
						</tr>
						<tr>
						   <td>'.
						      $dados['Cidade'].' - '.$dados['Estado'].'
						   </td>
						</tr>
						<tr>
						   <td>
						      CNPJ: '.formata_string('CNPJ', $dados['CNPJ']).'
						   </td>
						</tr>
						
					</table>
					
					<br>
					
					<div style="font-size: 12pt;"> <b>SEGURADO: '.strtoupper(trim($name)).'</b></div>					
					<div style="font-size: 12pt;">'.$address.'</div>
					<div style="font-size: 12pt;">'.$city .' - '. $uf.'</div>
					<div style="font-size: 12pt;">CEP: '.$cep.'</div>
					<div style="font-size: 12pt;">CNPJ: '.arruma_cnpj($cnpj).'</div>
					<br>
					';
	               
		           $html .= '<b>CL�USULA 1� - OBJETO -</b>
				             <br> 
							 <br> 
				            <div id="cobtexto">
								As disposi��es destas <b>CONDI��ES ESPECIAIS</b> ser�o aplic�veis a todas as opera��es de
								exporta��o efetuadas pelo <b>SEGURADO</b>, no �mbito de cobertura da <b>AP�LICE</b>, e
								prevalecer�o sobre o que estiver disposto nas <b>CONDI��ES GERAIS e PARTICULARES</b>, em
								rela��o aos direitos e obriga��es aqui tratados.
					        </div>
					         <br> 
							<b>CL�USULA 2� - COBERTURA DE JUROS OPERACIONAIS E DE JUROS DE MORA</b>
							<br> 
							<br> 
							  <div id="cobtexto">
								As quantias referentes aos juros operacionais e aos juros de mora, calculados entre a data
								de vencimento do <b>CR�DITO</b>, inicial ou devidamente prorrogada, e a data do efetivo
								pagamento da <b>INDENIZA��O</b> estar�o cobertas pela <b>AP�LICE</b> e ser�o computadas no
								c�lculo da <b>CONTA DE PERDAS</b>, de acordo com os seguintes limites e condi��es:
															
								<br><br>
								2.1. Os valores referentes aos juros operacionais e aos juros de mora dever�o ser
								faturados pelo <b>SEGURADO</b>, de acordo com os termos do contrato comercial de exporta��o
								ou com as condi��es gerais de venda adotadas, respeitada a legisla��o local;
								<br><br>
								2.2. O per�odo de c�lculo dos juros operacionais e de mora n�o dever� ultrapassar 6
								(seis) meses;
								<br><br>
								2.3. A taxa de juros a ser utilizada, para apura��o do valor a ser indenizado, ser� a taxa
								contratual acrescida de 1% (um por cento) ao ano , a t�tulo de juros de mora, cujo total n�o
								dever� exceder a taxa LIBOR acrescida de 4% (quatro por cento) ao ano;
								<br><br>
								2.4. Em caso de lit�gio entre o <b>SEGURADO</b> e o <b>IMPORTADOR</b>, prevalecer�o as 
								condi��es em rela��o aos valores de principal ou juros (operacionais e/ou de mora) 
								estabelecidas no m�dulo D07.01 <b>LIT�GIO</b>. Para valores que ultrapassarem as condi��es do 
								referido m�dulo, o pagamento da <b>INDENIZA��O</b> ser� suspenso at� que ocorra a solu��o do 
								mencionado <b>LIT�GIO</b>, nos termos previstos nas <b>CONDI��ES GERAIS</b> da ap�lice;

							</div>
							 <br> 
							<b>CL�USULA 3� - PR�MIO ADICIONAL -</b>
							 <br> <br> 
							<div id="cobtexto">
								3.1. A cobertura proporcionada por meio destas CONDI��ES ESPECIAIS, ensejar� o
								pagamento de <b>PR�MIO</b> adicional.
								<br> <br> 
								3.2. O <b>PR�MIO</b> adicional corresponde a 4% (quatro por cento) do <b>PR�MIO</b> da ap�lice,
								sendo faturado ao <b>SEGURADO</b> conjuntamente com o <b>PR�MIO M�NIMO</b> e com a parcela de
								ajuste de <b>PR�MIO</b>, nos termos estabelecidos nas CONDI��ES PARTICULARES.
							</div>
							 <br> 
							<b>CL�USULA 4� - VIG�NCIA -</b>
							 <br> <br> 
							<div id="cobtexto">
								As disposi��es constantes destas <b>CONDI��ES ESPECIAIS</b> aplicam-se �s opera��es de
								exporta��o efetuadas durante o per�odo compreendido entre '.$startValidity.' e '.$endValidity.',
								salvo em rela��o �quelas cuja data de vencimento do <b>CR�DITO</b> seja anterior � data de
								emiss�o do presente documento.
							</div>
							
							<br>
							<br>
							<br>
							<br>
							S�o Paulo, '. $datahoje. '


							<br>
							<br>
							<br>
							<br>
							
							<div style="text-align:center;">
							     <img src="'.$assinatura.'" style="width: 140px; height: 80px;">						
							</div>
							<div style="text-align:center;">Jo�o Rigobello</div>
							<div style="text-align:center;">Diretor Comercial</div>
															
					<br>
					<br>
					<br>
					<br>										
					
					<table style="width:100%; border: 0;" >
						  <tr>
							 <td style="width: 30%; ">&nbsp;</td>
							 <td style="text-align:right; width: 70%; ">
							  <div style="text-align:right;" id="disclame">'.$disclame_retorno.'</div>
						  </td>	
						  </tr>
			        </table>	
					
					</body>
					</html>';

   	$html = utf8_encode($html);
   	$mpdf->allow_charset_conversion = true;
   	$mpdf->charset_in = 'UTF-8';
   	$mpdf->WriteHTML($html);

   	$mpdf->Output($pdfDir.$key.$arq_name, \Mpdf\Output\Destination::FILE);
?>