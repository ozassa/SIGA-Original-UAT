<?php  

	require_once("clientData.php");

	$hc_acc = 0;

	if ($inform_state == 10 ){
	  	$titulo = 'Ficha de Aprovação de Limite de Crédito';
	} else {
	  	$titulo = 'Ficha Indicativa de Limite de Crédito';
	}

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
	$mpdf->SetTitle("Ficha de Aprocacao");
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
		<table style="width: 100%;">
			<tr>
			<td colspan="1" style="width: 10%;">
				&nbsp;
			</td>
			<td colspan="1" style="width: 60%;">
				<div style="text-align: center;">
					<span style="font-weight: bold; font-size: 18pt;">'.$titulo.'</span>
				</div>
			</td>
			<td colspan="1" style="width: 10%;">
				&nbsp;
			</td>
			<td colspan = "1" style="width: 20%; text-align:right;">
				<div style="float:right;">
					<img src="'.$logo.'" style="width: 80; height: 40px;" />
				</div>
			</td>
			</tr>
		</table>
	</htmlpageheader>
					
	<htmlpagefooter name="myfooter">
		<table style="width: 100%; border: 0; ">
			<tr>
				<td style="width: 22%;">
					&nbsp;
				</td>
				<td style="width: 56%; text-align:center; font-size: 8pt;">
					Página {PAGENO} de {nb}
				</td>
				<td style="width: 22%;">
					&nbsp;
				</td>
			</tr>
		</table>
	</htmlpagefooter>

	<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
	<sethtmlpagefooter name="myfooter" value="on" />

	mpdf-->
		
	<table style="width: 100%; font-size: 16pt;">
		<tr>	
			<td style="width: 17%; font-size: 16pt;"><b>Segurado:</b></td>	
			<td style="width: 83%; font-size: 16pt;">'.$nameCl.'</td>
		</tr>
		<tr>
			<td style="width: 17%; font-size: 16pt;"><b>DPP:</b></td>	
			<td style="width: 33%; font-size: 16pt;">'.$contrat.'</td>
			<td style="width: 17%; font-size: 16pt;"><b>Data Geração:</b></td>	
			<td style="width: 33%; font-size: 16pt;">'.date('d/m/Y').'</td>
		</tr>
		<tr>
			<td style="width: 17%; font-size: 16pt;"><b>Prazo Máximo:</b></td>	
			<td style="width: 33%; font-size: 16pt;">'.$PeriodoMaxCred.' dias</td>
			<td style="width: 17%; font-size: 16pt;"><b>Valores em:</b></td>	
			<td style="width: 33%; font-size: 16pt;">'.$ext.' Mil</td>
		</tr>
	</table>

	<br>

	<!--
	<div class="rounded">
		teste de borda redonda
	</div>
	-->

	<table style="width: 100%; font-size: 16pt;" class="bordasimples">
		<tr>
			<td style="width: 5%; text-align:center;"><b>Nº</b></td>
			<td style="width: 6%; text-align:center;"><b>CRS</b></td>
			<td style="width: 24%; text-align:left;"><b>Comprador</b></td>
			<td style="width: 10%; text-align:center;"><b>Valido a partir de</b></td>
			<td style="width: 15%; text-align:left;"><b>País</b></td>
			<td style="width: 8%; text-align:center;"><b>Crédito Solicitado</b></td>
			<td style="width: 8%; text-align:center;"><b>Crédito Concedido</b></td>
			<td style="width: 8%; text-align:center;"><b>Crédito Temporário</b></td>
			<td style="width: 8%; text-align:center;"><b>Validade</b></td>
			<td style="width: 8%; text-align:center;"><b>Código de aprovação</b></td>
		</tr> ';
					   
		/*while(odbc_fetch_row($cur)){
			$hc_acc		  +=1;
			$importerName     = odbc_result($cur, 2);
			$importerCoface   = odbc_result($cur, 3);
			$cred_solicitado  = odbc_result($cur, 4) / 1000;
			$importerCountry  = odbc_result($cur, 5);
			$cred_concedido   = odbc_result($cur, 6) / 1000;
			$validade         = odbc_result($cur, 7);
			$countryCode      = odbc_result($cur, 11);
			$hc_imp           = odbc_result($cur, 8);
			$idAprov	  = odbc_result($cur, "idAprov");
								  
			$strSQL = "SELECT * FROM tb_Aprovacao WHERE id = '$idAprov'";	  
			$cur1 = odbc_exec($db, "$strSQL");

			$codAprov 	= odbc_result($cur1, "codigo");
			$validade	= substr($validade, 8, 2). "/". substr($validade, 5, 2). "/". substr($validade, 2, 2);

			if ($validade == "//"){
				$validade = "";
			}
							
			$creditTemp     = odbc_result($cur, 9) / 1000;
			$limTemp        = odbc_result($cur, 10);
			$creditTemp     = number_format($creditTemp, 0, "", "");
			$today 		= date("Y", time())."-".date("m", time())."-".date("d", time());
			$tempValid = false;

			if ($limTemp >= $today){
				$tempValid 	= true;
				$limTemp        = substr($limTemp, 8, 2). "/". substr($limTemp, 5, 2). "/". substr($limTemp, 2, 2);
			}
									 
			$html .= '
				<tr>
					<td style="font-size: 12pt;" style="text-align:center;">'.$hc_acc.'</td>
					<td style="font-size: 12pt;" style="text-align:center;">'. $countryCode . substr($importerCoface, -6).'</td>
					<td style="font-size: 12pt;" style="text-align:left;">'.$importerName.'</td>
					<td style="font-size: 12pt;" style="text-align:center;">'.$validade.'</td>
					<td style="font-size: 12pt;" style="text-align:left;">'.$importerCountry.'</td>
					<td style="font-size: 12pt;" style="text-align:right;">'.$cred_solicitado.'</td>
					<td style="font-size: 12pt;" style="text-align:right;">'.$cred_concedido.'</td>
					<td style="font-size: 12pt;" style="text-align:right;">'.$creditTemp.'</td>
					<td style="font-size: 12pt;" style="text-align:center;">'.$limTemp.'</td>
					<td style="font-size: 12pt;" style="text-align:center;">'.$codAprov.'</td>
				</tr>';
		}
					   
		// Obtem os dados de observação
		$hc_sql = " SELECT comment, hide from ImpComment where idImporter = $hc_imp and hide = 0";
		$comment = odbc_exec($db,$hc_sql);
		$hc_comp = "";

		while(odbc_fetch_row($comment)){
			$hc_comment = odbc_result($comment, 1);

			if ($hc_comment != "" ){
				$hc_acc = $hc_acc + 1;
				$hc_comp = $hc_comp . " (" . $hc_acc . ")";
			}
		}*/
							  					  
		$html .= '
			</table>
			</body>
                    	</html>';
  
	$html = utf8_encode($html);
	$mpdf->allow_charset_conversion=true;
	$mpdf->charset_in='UTF-8';
	$mpdf->WriteHTML($html);

	$mpdf->Output($pdfDir.$key.$arq_name, \Mpdf\Output\Destination::FILE);
?>