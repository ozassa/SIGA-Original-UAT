<?php

	require_once('../../../dbOpen.php');  

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

	require_once("../../../dbOpen.php");
  require_once("../../../pdfConf.php");
  include_once("../../../../gerar_pdf/MPDF45/mpdf.php");
  
   
   function verificaMes($mes){    
	if ($mes == "01"){
	    $ret  = "Janeiro";
    }else if ($mes == "02"){
	    $ret  = "Fevereiro";
	}else if ($mes == "03"){
	    $ret  = "Março";
	}else if ($mes == "04"){
	    $ret  = "Abril";
	}else if ($mes == "05"){
	    $ret  = "Maio";
	}else if($mes == "06"){
	    $ret  = "Junho";
	}else if ($mes == "07"){
	    $ret  = "Julho";
	}else if ($mes == "08"){
	    $ret  = "Agosto";
	}else if ($mes == "09"){
	    $ret  = "Setembro";	
	}else if ($mes == "10"){
	    $ret  = "Outubro";
	}else if ($mes == "11"){
	    $ret  = "Novembro";
	}else if ($mes == "12"){
	    $ret  = "Dezembro";								
    }
    return $ret;
   
   }
   
    if($_REQUEST['mes'] != ''){
	   $mes = $_REQUEST['mes'];
	}else{
	   $mes = date("n");
	}
	
	if($_REQUEST['ano']){
		$ano = $_REQUEST['ano'];
	}else{		
	  $ano = date("Y");
	}
	
	
	 if($_REQUEST['idInform'])
		 $idInform  = $_REQUEST['idInform'];
		 
			 
	$sql = "SELECT name, contrat, txAnalize, txMonitor, n_Apolice As Apolice, CONVERT(CHAR, startValidity, 103) AS Ini_Vigencia, CONVERT(CHAR, endValidity, 103) AS Fim_Vigencia FROM Inform WHERE id = ?";
	$cur = odbc_prepare($db, $sql);
	odbc_execute($cur, [$idInform]);

	$name = "";
	if (odbc_fetch_row($cur)) {
	    $name = odbc_result($cur, 1);
	    $contrat = odbc_result($cur, 2);
	    $txtAnalize = odbc_result($cur, 3);
	    $txtMonitor = odbc_result($cur, 4);
	    $Num_Apolice = odbc_result($cur, "Apolice");
	    $Ini_Vigencia = odbc_result($cur, "Ini_Vigencia");
	    $Fim_Vigencia = odbc_result($cur, "Fim_Vigencia");
	}

	
	
	if($mes <12 &&  $ano <= 2008){
	
		require('../fechamento.php');
	
		$cur = odbc_exec($db, $query);
	
		//$list = new Java("java.util.ArrayList");
		if(! $list){
		   // die("Erro em list<br>\n");
		}
        
		$count = 1;

		$opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
				'format' => 'A4-L',
				'margin_left' => 20,
				'margin_right' => 15,
				'margin_top' => 48,
				'margin_bottom' => 25,
				'margin_header' => 10,
				'margin_footer' => 10
				];
		
		$mpdf=new  \Mpdf\Mpdf($opt);

		

		$html = ob_get_clean();
		//$mpdf->useOnlyCoreFonts = true;    // false is default
		//$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("Demonstrativo");
		$mpdf->SetAuthor($nomeEmp);
		$mpdf->SetWatermarkText(""); // fundo marca dágua
		$mpdf->showWatermarkText = true;
		$mpdf->watermark_font = 'DejaVuSansCondensed';
		$mpdf->watermarkTextAlpha = 0.1;
		$mpdf->SetDisplayMode('fullpage');
		
		
		
		// Endereço do logotipo
		 $logo  = $root .'../../../images/logo.jpg';
	 
	
	   // Início do arquivo montando primeiro o CSS
	   
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
						<td colspan = "4" style="text-align: left;">							 
							<span style="font-weight: bold; font-size: 22pt;">Demonstrativo do Faturamento de Análise e Monitoramento por Segurado</span>							 
						</td>
					  </tr>
					  
					</table>
			
					</htmlpageheader>
					
					<htmlpagefooter name="myfooter">
						 <table width="100%" border="0">
							 <tr>
								 <td width="22%">&nbsp;</td>
								 <td width="56%" style="text-align:center; font-size: 8pt;">									
										  
										  Página {PAGENO} de {nb}
									 
								</td>
								<td width="22%">&nbsp;</td>
							</tr>
						</table>
						
					</htmlpagefooter>
					
					<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
					<sethtmlpagefooter name="myfooter" value="on" />
					mpdf--> ';
		
		$html .= '<div align="left" style="font-size: 12pt;"><b>Cobrança do mês de '. verificaMes($mes) .' de '. $ano .'</b></div>
					<br>
					<br>
					<table width="100%">
						<tr>
						 <td><strong>Segurado: </strong>'.$name.'</td>
						 <td style="text-align:right;"><strong>Proposta: </strong>'. $contrat.'/'.sprintf("%1d", substr($ano, 2, 2)).'</td>
						</tr>
					</table>
					<br>
					<table width="100%" style="border:solid 1px #000;" cellspacing="0" cellpadding="3">					
						  <tr>
							 <td width="45%" style="border:solid 1px #000; text-align:center; font-size:22px;"><strong>Comprador</strong></td>
							 <td width="45%" style="border:solid 1px #000; text-align:center; font-size:22px;"><strong>País</strong></td>
							 <td width="30%" style="border:solid 1px #000; text-align:center; font-size:22px;"><strong>CI Comprador</strong></td>
							 <td width="20%" style="border:solid 1px #000; text-align:center; font-size:22px;"><strong>Análise</strong></td>	
							 <td width="20%" style="border:solid 1px #000; text-align:center; font-size:22px;"><strong>Monit.</strong></td>
							 <td width="20%" style="border:solid 1px #000; text-align:center; font-size:22px;"><strong>Total</strong></td>
							 <td width="20%" style="border:solid 1px #000; text-align:center; font-size:22px;"><strong>Motivo</strong></td>
						  </tr>';
		
							while (odbc_fetch_row($cur)) {
							  $impName     = odbc_result($cur, 'impName');
							  $countryName = odbc_result($cur, 'countryName');
							  $creditSolic = odbc_result($cur, 'creditSolic') / 1000;
							  $credit      = odbc_result($cur, 'credit') / 1000;
							  $data_estudo = odbc_result($cur, 'data_estudo');
							  $creditTemp  = odbc_result($cur, 'creditTemp') / 1000;
							  $limTemp     = odbc_result($cur, 'limTemp');
							  $limTempBr   = odbc_result($cur, 'limTempBr');
							  $ci_coface   = odbc_result($cur, 'c_Coface_Imp');
						
						
							  $data_estudo = substr($data_estudo, 8, 2). "/". substr($data_estudo, 5, 2). "/". substr($data_estudo, 2, 2);
							  if ($data_estudo == "//"){
								$data_estudo = "";
							  }
						
							  //$limTemp = substr($limTemp, 8, 2). "/". substr($limTemp, 5, 2). "/". substr($limTemp, 2, 2);
							  //$limTemp = substr($limTemp, 1, 2). "/". substr($limTemp, 6, 2). "/". substr($limTemp, 9, 2);
							  if ($limTemp == "//"){
								 $limTemp = "";
							  }
						
							  $monitor = 0;
							  $mot = '';
							  if(odbc_result($cur, 'monitor') > 0){
								$monitor = odbc_result($cur, 'txMonitor') / 4;
								$totM += $monitor;
								$mot = 'Monitoramento';
							  }
						
							  $analyse = 0;
							  if(odbc_result($cur, 'analyse') > 0){
								$analyse = odbc_result($cur, 'txAnalyse');
								$totA += $analyse;
								$mot .= ($mot != '' ? ' e ' : '' ). 'Análise';
							  }
						
							// if($limTemp != '' && $creditTemp > 0){
						
							   if($limTempBr > $date && $creditTemp > 0){
							   // echo "<pre>Data = $date</pre>";
						
								$credit += $creditTemp;
							  }
						
							  //$list_aux = new Java('java.util.ArrayList');
							  if(! $list_aux){
								 //die("Erro na list_aux<br>\n");
							  }
								
							   $html .= '<tr>	
										   <td style="border:solid 1px #000; font-size:22px;">'. $count.' '. $impName .'</td>
										   <td style="border:solid 1px #000; font-size:22px;">'. $countryName.'</td>
										   <td style="border:solid 1px #000; font-size:22px;">'. sprintf("%06d", $ci_coface) .'</td>	
										   <td style="border:solid 1px #000; font-size:22px; text-align:center">'. number_format($analyse, 2, ',', '.') .'</td>
										   <td style="border:solid 1px #000; font-size:22px; text-align:center">'. number_format($monitor, 2, ',', '.') .'</td>
										   <td style="border:solid 1px #000; font-size:22px; text-align:center">'. number_format($monitor + $analyse, 2, ',', '.') .'</td>
										   <td style="border:solid 1px #000; font-size:22px; text-align:center">'. $mot.'</td>
									   </tr>'; 
						  
							  
							  /*
							  $list_aux->add("$count $impName");
							  $list_aux->add($countryName);
							  $list_aux->add(sprintf("%06d", $ci_coface));
							  $list_aux->add(number_format($creditSolic, 0, ',', '.'));
							  $list_aux->add(number_format($credit, 0, ',', '.'));
							  $list_aux->add($data_estudo);
							  $list_aux->add(number_format($analyse, 2, ',', '.'));
							  $list_aux->add(number_format($monitor, 2, ',', '.'));
							  $list_aux->add(number_format($monitor + $analyse, 2, ',', '.'));
							  $list_aux->add($mot);
						
							  $list->add($list_aux);
							  */
							  $count++;
							} // while
	
						
							/*
							$h = new Java("java.util.HashMap");
						   
							$h->put('key', $pdfDir. $key. "demonstrativo.pdf");
							$h->put('erros', $pdfDir. $key. "erros.txt");
							$h->put('segurado', $name);
							$h->put('proposta', "$contrat/". sprintf("%1d", substr($ano, 2, 2)));
							$h->put('mes', $mes);
							$h->put('total_analise', number_format($totA, 2, ',', '.'));
							$h->put('total_monit', number_format($totM, 2, ',', '.'));
							$h->put('total', number_format($totA + $totM, 2, ',', '.'));
							$h->put('list', $list);
							$h->put('dir', $pdfDir);
							*/
							
							 $html .= '
							  	 <tr>	
						    		   <td  colspan="7">&nbsp;</td>
								</tr>
							   <tr>	
						    		   <td  style="border:solid 1px #000; font-size:22px;" colspan="3"><strong>Total por segurado:</strong></td>
									   <td style="border:solid 1px #000; font-size:22px; text-align:center">'.  number_format($totA, 2, ',', '.') .'</td>
									   <td style="border:solid 1px #000; font-size:22px; text-align:center">'. number_format($totM, 2, ',', '.') .'</td>
									   <td  style="border:solid 1px #000; font-size:22px; text-align:center">'. number_format($totA + $totM, 2, ',', '.').'</td>
									   <td  style="border:solid 1px #000; font-size:22px;" colspan="1">&nbsp;</td>
						      </tr>'; 
				   
							
								 $html .= '
								</table>
									</body>
								</html>';
										
						
							   $html = utf8_encode($html);
							   $mpdf->allow_charset_conversion=true;
							   $mpdf->charset_in='UTF-8';
							   $mpdf->WriteHTML($html);
							   
							  					  
							   //$mpdf->Output($pdfDir.$key.'demonstrativo.pdf',F); 
							   
							   $mpdf->Output();
							   //echo $html;
							   //$url_pdf = $host.'src/download/'.$key.'demonstrativo.pdf';
							   
											   
							
							
							
							//$pdf = new Java("Demonstrativo", $h);
							
							$loc = '/siex/src/download/'.$key.'demonstrativo.pdf';
							  
							// if(! $pdf){
							//	 echo "Erro em criar objeto Demonstrativo<br>\n";
							//  }else{
		
		
	}else{
	
		$rsquery = "SELECT imp.idImporter, imp.importador, imp.creditoSolicitado, imp.creditoConcedido, imp.txAnalise, imp.txMonitor, imp.total, imp.motivo, c.name, c.code, CAST(imp.codPais AS varchar) + imp.codBuyer AS CRS
            FROM resFatAnaliseMonitorImport AS imp
            LEFT JOIN Importer AS imp2 ON imp.idImporter = imp2.id
            LEFT JOIN Country AS c ON c.code = imp.codPais
            WHERE imp.idInform = ? AND imp.ano = ? AND imp.mes = ?
            ORDER BY imp.importador";

			$cur = odbc_prepare($db, $rsquery);
			odbc_execute($cur, [$idInform, $ano, $mes]);

	
	   
	
		$count = 1;
		$opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
				'format' => 'A4-L',
				'margin_left' => 20,
				'margin_right' => 15,
				'margin_top' => 48,
				'margin_bottom' => 25,
				'margin_header' => 10,
				'margin_footer' => 10
				];
		
		$mpdf=new  \Mpdf\Mpdf($opt);
		$html = ob_get_clean();
		// $mpdf->useOnlyCoreFonts = true;    // false is default
		//$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("Demonstrativo");
		$mpdf->SetAuthor($nomeEmp);
		$mpdf->SetWatermarkText(""); // fundo marca dágua
		$mpdf->showWatermarkText = true;
		$mpdf->watermark_font = 'DejaVuSansCondensed';
		$mpdf->watermarkTextAlpha = 0.1;
		$mpdf->SetDisplayMode('fullpage');
		
		
		
		// Endereço do logotipo
		 $logo  = '../../../../images/logo.jpg';
	 
	
	   // Início do arquivo montando primeiro o CSS
	   
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
							<td colspan = "2" style="text-align: left;">							 
								<span style="font-weight: bold; font-size: 18pt;">Demonstrativo do Faturamento de Análise e Monitoramento por Segurado</span>							 
							</td>
							
							<td colspan = "2"  style="text-align: right;">							 
							  	<img src="'.$logo.'" widht ="180" height="90"/>
							</td>
					  	</tr>
					</table>
			
					</htmlpageheader>
					
					<htmlpagefooter name="myfooter">
						 <table width="100%" border="0">
							 <tr>
								 <td width="22%">&nbsp;</td>
								 <td width="56%" style="text-align:center; font-size: 8pt;">									
										  
										  Página {PAGENO} de {nb}
									 
								</td>
								<td width="22%">&nbsp;</td>
							</tr>
						</table>
						
					</htmlpagefooter>
					
					<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
					<sethtmlpagefooter name="myfooter" value="on" />
					mpdf--> ';
		
		$html .= '
					
					<table width="100%">
						<tr>
							<td style="text-align: left; font-size: 11pt;">
								<b>Cobrança do mês de '. verificaMes($mes) .' de '. $ano .' </b>
							</tr>
						</tr>
						
						<tr>
						 	<td style="text-align: left; font-size: 11pt;">
						 		<strong>Segurado: </strong><b>'.$name.'</b>
						 	</td>
						 	
						 	<td style="text-align:right; font-size: 11pt;">
						 		<strong>Proposta: </strong><b>'. $contrat.'/'.sprintf("%1d", substr($ano, 2, 2)).'</b>
						 	</td>
						</tr>
						
						<tr>
							<td style="text-align: left; font-size: 11pt;">
						 		<strong>Vigência: </strong><b>'.$Ini_Vigencia.' até '. $Fim_Vigencia.'</b>
						 	</td>
						 	
						 	<td style="text-align:right; font-size: 11pt;">
						 		<strong>Apólice: </strong><b>'. $Num_Apolice .'</b>
						 	</td>
						
						</tr>
						
					</table>
					
					<br>
					
					<table width="100%" style="border:solid 1px #000;" cellspacing="0" cellpadding="3">
						<tr>
							<td width="40%" style="border:solid 1px #000; text-align:Left; font-size:14px;"><strong>Comprador</strong></td>
							<td width="20%" style="border:solid 1px #000; text-align:Left; font-size:14px;"><strong>País</strong></td>
							<td width="10%" style="border:solid 1px #000; text-align:center; font-size:14px;"><strong>CI Comprador</strong></td>
							<td width="10%" style="border:solid 1px #000; text-align:right; font-size:14px;"><strong>Análise</strong></td>	
							<td width="10%" style="border:solid 1px #000; text-align:right; font-size:14px;"><strong>Monit.</strong></td>
							<td width="10%" style="border:solid 1px #000; text-align:right; font-size:14px;"><strong>Total</strong></td>
						 </tr>';

						$totalCobrado = 0; 
						$totA = 0;
						$totM = 0;
		
						  
						While (odbc_fetch_row($cur)) {
							$importador = odbc_result($cur,2);
							$creditSolic = odbc_result($cur,3);
							$credit = odbc_result($cur,4);
							$analyse = odbc_result($cur,5);
							$monitor = odbc_result($cur,6);
							$total = odbc_result($cur,7);
							$mot = odbc_result($cur,8);
							$countryName = odbc_result($cur,9);
							$codPais   = odbc_result($cur, "code");
							$ci_coface   = odbc_result($cur, "CRS");
								
							$totalCobrado +=$total; 
							$totA +=$analyse;
							$totM +=$monitor;
							$totAn = $monitor + $analyse;

							$html .= '
								<tr>	
									<td style="border:solid 1px #000; font-size:14px; text-align:left">'. $importador .'</td>
									<td style="border:solid 1px #000; font-size:14px; text-align:left">'. $countryName.'</td>
									<td style="border:solid 1px #000; font-size:14px; text-align:center">'. $ci_coface .'</td>	
									<td style="border:solid 1px #000; font-size:14px; text-align:right">'. number_format($analyse, 2, ',', '.') .'</td>
									<td style="border:solid 1px #000; font-size:14px; text-align:right">'. number_format($monitor, 2, ',', '.') .'</td>
									<td style="border:solid 1px #000; font-size:14px; text-align:right">'. number_format($totAn, 2, ',', '.') .'</td>
								</tr>'; 

							/*
								 $list_aux->add("$count $impName");
								 $list_aux->add($countryName);
							
								 $list_aux->add(sprintf("%06d", $ci_coface));
								 $list_aux->add(number_format($creditSolic, 0, ',', '.'));
								 $list_aux->add(number_format($credit, 0, ',', '.'));
								 $list_aux->add($data_estudo);
								 $list_aux->add(number_format($analyse, 2, ',', '.'));
								 $list_aux->add(number_format($monitor, 2, ',', '.'));
								 $list_aux->add(number_format($total, 2, ',', '.'));
								 $list_aux->add(str_replace("<br>"," e ",$mot));
								 $list->add($list_aux);
							*/
								 
							$count++;
						} // While
						
						/*
							$h = new Java("java.util.HashMap");
							$h->put('key', $pdfDir. $key. "demonstrativo.pdf");
							$h->put('erros', $pdfDir. $key. "erros.txt");
							$h->put('segurado', $name);
							$h->put('proposta', "$contrat/". sprintf("%1d", substr($ano, 2, 2)));
							$h->put('mes', $mes);
							$h->put('total_analise', number_format($totA, 2, ',', '.'));
							$h->put('total_monit', number_format($totM, 2, ',', '.'));
							$h->put('total', number_format($totA + $totM, 2, ',', '.'));
							$h->put('list', $list);
							$h->put('dir', $pdfDir);
						*/
						
						$html .= '
										 <tr>	
											   <td  colspan="7">&nbsp;</td>
										</tr>
									   <tr>	
											   <td  style="border:solid 1px #000; font-size:14px;" colspan="3"><strong>Total por segurado:</strong></td>
											   <td style="border:solid 1px #000; font-size:14px; text-align:center">'.  number_format($totA, 2, ',', '.') .'</td>
											   <td style="border:solid 1px #000; font-size:14px; text-align:center">'. number_format($totM, 2, ',', '.') .'</td>
											   <td  style="border:solid 1px #000; font-size:14px; text-align:center">'. number_format($totA + $totM, 2, ',', '.').'</td>
									  </tr>';  
				   
		
						 $html .= '
											</table>
									</body>
								</html>';
										
						
							   $html = utf8_encode($html);
							   $mpdf->allow_charset_conversion=true;
							   $mpdf->charset_in='UTF-8';
							   $mpdf->WriteHTML($html);
							   $mpdf->Output();
							  
							   //$mpdf->Output($pdfDir.$key.'demonstrativo.pdf',F); 
							   
							  // $mpdf->Output();
							   //echo $html;
							   //$url_pdf = $host.'src/download/'.$key.'demonstrativo.pdf';
							   
		
		
		
	}

	?>

