<?php 

 include_once("../../../config.php");

 include_once("../consultaCoface.php");

    $sqlquery  = "Select E.*, IsNull(SP.c_SUSEP, E.c_Susep) as CodSusep, P.Nome as Produto  From Inform Inf
				Inner Join Produto P On
				  	P.i_Produto = Inf.i_Produto
				Inner Join Empresa_Produto EP On
				  	EP.i_Produto = P.i_Produto
				Inner Join Empresa E On
				  	E.i_Empresa = EP.i_Empresa
				Left Join Sub_Produto SP On
					SP.i_Produto = Inf.i_Produto
					And SP.i_Sub_Produto = Inf.i_Sub_Produto
			Where
				Inf.id = ".$idInform;
   
   $res = odbc_exec($db,$sqlquery);
   $dados = odbc_fetch_array($res);
   
   $retorno_rodape =  $dados['Endereco'].' - '.
			    $dados['Complemento'].' - '.
			    'CEP '.formata_string('CEP',$dados['CEP']).' - '.
			    $dados['Cidade'].', '.
			    $dados['Estado']. ' '.
			    'Tel.: '.$dados['Telefone'].'  '.
			    'Fax: ' . $dados['Fax'].'  '.
			    'Home Page: '. $dados['HomePage'];
				
  $disclame_retorno = $dados['Nome'].' CNPJ: '.formata_string('CNPJ', $dados['CNPJ']).', SUSEP no.: '. $dados['CodSusep'];
	

if(! file_exists($prefix. "CondJuros.pdf")){
  //$h = new Java ('java.util.HashMap');
 
    if($Num_Parcelas){
		if ($numParc == 1){
			$txtParcs .= "a vista.";
	    }else{
		    $txtParcs .= "em ".$Num_Parcelas." prestações ".$t_Vencimento.".";
	    }
	}else{
 
		 if ($pvigencia!=2) {
			  if ($numParc == 1){
				$txtParcs .= "a vista.";
			  }else if ($numParc == 2){
				$txtParcs .= "em duas prestações: 1 e mais 1 em noventa dias.";
			  }else if ($numParc == 4){
				$txtParcs .= "em quatro prestações: 1 e mais 3 trimestrais.";
			//  }else if ($numParc == 6){
			//    $txtParcs .= "em seis prestações: 1 e mais 5 bimestrais.";
			  }else if ($numParc == 7){
				$txtParcs .= "em seis prestações: 1 e mais 6 mensais.";
			  }else if ($numParc == 10){
				$txtParcs .= "em dez prestações: 1 e mais 9 mensais.";
			  }
		  
		 }else if($pvigencia==2){
			  if ($numParc == 1){
				$txtParcs .= "a vista.";
			  }else if ($numParc == 4){
				$txtParcs .= "em quatro prestações: 1 e mais 3 trimestrais.";
			  }else if ($numParc == 7){
				$txtParcs .= "em sete prestações: 1 e mais 6 mensais.";
			  }else if ($numParc == 8){
				$txtParcs .= "em oite prestações: 1 e mais 7 trimestrais.";
			  }
		 
		 }
	}
 $cquery = "select  a.idInform, a.nu_banco from ParModEsp a inner join Inform b on (a.idInform = b.id) where a.idInform =".$idInform;
 $rsbb = odbc_exec($db,$cquery);

 // tenta achar o usuário responsável
 $banco = odbc_result($rsbb,'nu_banco');
 
  /*
  $h->put('key', $prefix. "CondJuros.pdf");
  $h->put('dir', $pdfDir. "");
  $h->put('numero', $apoNum. "");
  $h->put('segurado', $name. "");
  $h->put('endereco', $address. "");
  $h->put('cep', $cep. "");
  $h->put('cidade_estado', "$city - $uf");
  $h->put('cnpj', $cnpj. "");
  $h->put('inscricao_estadual', $ie. "");
  $h->put('data_inicio', $startValidity. "");
  $h->put('data_fim', $endValidity. "");
  $h->put('banco',$banco."");

  //Alterado por Tiago V N - Elumini - 13/02/2006
  $h->put('susep', $susep. "");
  $h->put('cp', $cp. "");
  
  
  $prop = new Java ('CondJuros', $h);
  if($prop == null){
    die("<h1>condjuros null</h1>");
  }else{
    $prop->generate();
  }
  */
  
  
  
    // Começa aqui o pdf


   include_once("../../../config.php");

  
   // Emitir pdf pelo PHP diretamente
  
   include_once("../../../gerar_pdf/MPDF45/mpdf.php");
   

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
	$mpdf=new mPDF('win-1252','A4','','');
    $html = ob_get_clean();
    $mpdf->useOnlyCoreFonts = true;    // false is default
	//$mpdf->SetProtection(array('print'));
	$mpdf->SetTitle("Proposta");
	$mpdf->SetAuthor($nomeEmp);
	$mpdf->SetWatermarkText(""); // fundo marca dágua
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.1;
	$mpdf->SetDisplayMode('fullpage');
	
	
	
	// Endereço do logotipo
    $logo       = '../../images/logo_pdf.jpg';
	$assinatura  = $root .'images/Assinatura_Marcele.JPG';
    
	
    $datahoje =  dataconvert();


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
					<div align="center" style="font-size: 12pt;">
						<b>CONDIÇÕES ESPECIAIS DA APÓLICE Nº '.$apoNum.'</b>
					</div>
					<div align="center" style="font-size: 12pt;">
						<b>COBERTURA ACESSÓRIA DE JUROS OPERACIONAIS E MORATÓRIOS</b>
					</div>
					<br>
					<br>
					<table width="100%" border="0" style="font-size: 12pt;">
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
	               
		           $html .= '<b>CLÁUSULA 1ª - OBJETO -</b>
				             <br> 
							 <br> 
				            <div id="cobtexto">
								As disposições destas <b>CONDIÇÕES ESPECIAIS</b> serão aplicáveis a todas as operações de
								exportação efetuadas pelo <b>SEGURADO</b>, no âmbito de cobertura da <b>APÓLICE</b>, e
								prevalecerão sobre o que estiver disposto nas <b>CONDIÇÕES GERAIS e PARTICULARES</b>, em
								relação aos direitos e obrigações aqui tratados.
					        </div>
					         <br> 
							<b>CLÁUSULA 2ª - COBERTURA DE JUROS OPERACIONAIS E DE JUROS DE MORA</b>
							<br> 
							<br> 
							  <div id="cobtexto">
								As quantias referentes aos juros operacionais e aos juros de mora, calculados entre a data
								de vencimento do <b>CRÉDITO</b>, inicial ou devidamente prorrogada, e a data do efetivo
								pagamento da <b>INDENIZAÇÃO</b> estarão cobertas pela <b>APÓLICE</b> e serão computadas no
								cálculo da <b>CONTA DE PERDAS</b>, de acordo com os seguintes limites e condições:
															
								<br><br>
								2.1. Os valores referentes aos juros operacionais e aos juros de mora deverão ser
								faturados pelo <b>SEGURADO</b>, de acordo com os termos do contrato comercial de exportação
								ou com as condições gerais de venda adotadas, respeitada a legislação local;
								<br><br>
								2.2. O período de cálculo dos juros operacionais e de mora não deverá ultrapassar 6
								(seis) meses;
								<br><br>
								2.3. A taxa de juros a ser utilizada, para apuração do valor a ser indenizado, será a taxa
								contratual acrescida de 1% (um por cento) ao ano , a título de juros de mora, cujo total não
								deverá exceder a taxa LIBOR acrescida de 4% (quatro por cento) ao ano;
								<br><br>
								2.4. Em caso de litígio entre o <b>SEGURADO</b> e o <b>IMPORTADOR</b>, prevalecerão as 
								condições em relação aos valores de principal ou juros (operacionais e/ou de mora) 
								estabelecidas no módulo D07.01 <b>LITÍGIO</b>. Para valores que ultrapassarem as condições do 
								referido módulo, o pagamento da <b>INDENIZAÇÃO</b> será suspenso até que ocorra a solução do 
								mencionado <b>LITÍGIO</b>, nos termos previstos nas <b>CONDIÇÕES GERAIS</b> da apólice;

							</div>
							 <br> 
							<b>CLÁUSULA 3ª - PRÊMIO ADICIONAL -</b>
							 <br> <br> 
							<div id="cobtexto">
								3.1. A cobertura proporcionada por meio destas CONDIÇÕES ESPECIAIS, ensejará o
								pagamento de <b>PRÊMIO</b> adicional.
								<br> <br> 
								3.2. O <b>PRÊMIO</b> adicional corresponde a 4% (quatro por cento) do <b>PRÊMIO</b> da apólice,
								sendo faturado ao <b>SEGURADO</b> conjuntamente com o <b>PRÊMIO MÍNIMO</b> e com a parcela de
								ajuste de <b>PRÊMIO</b>, nos termos estabelecidos nas CONDIÇÕES PARTICULARES.
							</div>
							 <br> 
							<b>CLÁUSULA 4ª - VIGÊNCIA -</b>
							 <br> <br> 
							<div id="cobtexto">
								As disposições constantes destas <b>CONDIÇÕES ESPECIAIS</b> aplicam-se às operações de
								exportação efetuadas durante o período compreendido entre '.$startValidity.' e '.$endValidity.',
								salvo em relação àquelas cuja data de vencimento do <b>CRÉDITO</b> seja anterior à data de
								emissão do presente documento.
							</div>
							
							<br>
							<br>
							<br>
							<br>
							São Paulo, '. $datahoje. '


							<br>
							<br>
							<br>
							<br>
							
							<div align="center">
							     <img src="'.$assinatura.'" width="140px;" height="80px;">						
							</div>
							<div align="center">Marcele Lemos</div>
							<div align="center">Presidente</div>
															
					<br>
					<br>
					<br>
					<br>										
					
					<table width="100%" border="0">
						  <tr>
							 <td width="30%">&nbsp;</td>
							 <td align="right" width="70%">
							  <div align="right" id="disclame">'.$disclame_retorno.'</div>
						  </td>	
						  </tr>
			        </table>	
					
					</body>
					</html>';
				  
				  
				  
				   $html = utf8_encode($html);
				   $mpdf->allow_charset_conversion=true;
				   $mpdf->charset_in='UTF-8';
				   $mpdf->WriteHTML($html);
				   
				   //$mpdf->Output();
				  
				   $mpdf->Output($pdfDir.$key.'CondJuros.pdf',F); 
				   
				   //echo $html;
				   
				   $url_pdf = $host.'src/download/'.$key.'CondJuros.pdf';
				  
				 ?>
					
				 <?php 

  
            //exit();
  
}

    

?>


