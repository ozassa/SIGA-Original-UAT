<?php 

	require_once("../rolePrefix.php");
	require_once("../../pdfConf.php");

	include_once("../../../gerar_pdf/MPDF45/mpdf.php");
	require_once("../../dbOpen.php"); 

	$sql = "EXEC SPR_BB_Consulta_Liquidacao_Faturamento '200', NULL, NULL, '".$Id_Liquidacao."'";
	$rsSql = odbc_exec($db, $sql);

	$dados = array();
	while(odbc_fetch_row($rsSql)) {

		$Nome_Segurado = odbc_result($rsSql, "Nome_Segurado");
		$n_Apolice = odbc_result($rsSql, "n_Apolice");
		$d_Inicio_Vigencia = odbc_result($rsSql, "d_Inicio_Vigencia") ? ymd2dmy(odbc_result($rsSql, "d_Inicio_Vigencia")) : '';
		$d_Fim_Vigencia = odbc_result($rsSql, "d_Fim_Vigencia") ? ymd2dmy(odbc_result($rsSql, "d_Fim_Vigencia")) : '';
		$d_Liquidacao = odbc_result($rsSql, "d_Liquidacao") ? ymd2dmy(odbc_result($rsSql, "d_Liquidacao")) : '';
		$Nome_Usuario = odbc_result($rsSql, "Nome_Usuario");
		$Nome_Moeda = odbc_result($rsSql, "Nome_Moeda");
		$Sigla_Moeda = odbc_result($rsSql, "Sigla_Moeda");
		$c_SUSEP = odbc_result($rsSql, "c_SUSEP");
		$n_CRS = odbc_result($rsSql, "n_CRS");
		$Nome_Comprador = odbc_result($rsSql, "Nome_Comprador");
		$Nome_Pais = odbc_result($rsSql, "Nome_Pais");
		$d_Embarque = odbc_result($rsSql, "d_Embarque") ? ymd2dmy(odbc_result($rsSql, "d_Embarque")) : '';
		$Num_Fatura = odbc_result($rsSql, "Num_Fatura");
		$d_Vencimento = odbc_result($rsSql, "d_Vencimento") ? ymd2dmy(odbc_result($rsSql, "d_Vencimento")) : '';
		$v_Embarque = number_format(odbc_result($rsSql, "v_Embarque"), 2, ",", ".");
		$v_PROEX = number_format(odbc_result($rsSql, "v_PROEX"), 2, ",", ".");
		$v_ACE = number_format(odbc_result($rsSql, "v_ACE"), 2, ",", ".");
		$d_Pagamento = odbc_result($rsSql, "d_Pagamento") ? ymd2dmy(odbc_result($rsSql, "d_Pagamento")) : '';
		$n_Operacao = odbc_result($rsSql, "n_Operacao");
		$v_Pago = number_format(odbc_result($rsSql, "v_Pago"), 2, ",", ".");
		$v_Saldo = number_format(odbc_result($rsSql, "v_Saldo"), 2, ",", ".");

		$dados[] = array(
			"Nome_Segurado" 			=> $Nome_Segurado,
			"n_Apolice" 					=> $n_Apolice,
			"d_Inicio_Vigencia" 	=> $d_Inicio_Vigencia,
			"d_Fim_Vigencia" 			=> $d_Fim_Vigencia,
			"d_Liquidacao" 				=> $d_Liquidacao,
			"Nome_Usuario" 				=> $Nome_Usuario,
			"Nome_Moeda" 					=> $Nome_Moeda,
			"Sigla_Moeda" 				=> $Sigla_Moeda,
			"c_SUSEP" 						=> $c_SUSEP,
			"n_CRS" 							=> $n_CRS,
			"Nome_Comprador" 			=> $Nome_Comprador,
			"Nome_Pais" 					=> $Nome_Pais,
			"d_Embarque" 					=> $d_Embarque,
			"Num_Fatura" 					=> $Num_Fatura,
			"d_Vencimento" 				=> $d_Vencimento,
			"v_Embarque" 					=> $v_Embarque,
			"v_PROEX" 						=> $v_PROEX,
			"v_ACE" 							=> $v_ACE,
			"d_Pagamento" 				=> $d_Pagamento,
			"n_Operacao"					=> $n_Operacao,
			"v_Pago" 							=> $v_Pago,
			"v_Saldo" 						=> $v_Saldo
		);
	}

	$opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
        'format' => 'A4-L',
        'margin_left' => 20,
        'margin_right' => 15,
        'margin_top' => 40,
        'margin_bottom' => 25,
        'margin_header' => 10,
        'margin_footer' => 10
        ];
    
    $mpdf=new  \Mpdf\Mpdf($opt);

	$html = ob_get_clean();
	// $mpdf->useOnlyCoreFonts = true;    // false is default
	//$mpdf->SetProtection(array('print'));
	$mpdf->SetTitle("Ficha de Aprocacao");
	$mpdf->SetAuthor('Coface do Brasil');
	$mpdf->SetWatermarkText(""); // fundo marca dágua
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.1;
	$mpdf->SetDisplayMode('fullpage');
	
	// Endereço do logotipo
	$logo  = '../../images/logo.jpg';	 

  // Início do arquivo montando primeiro o CSS	   
	$html = '<html>
						<head>
							<style>
								body {font-family: Arial, Helvetica, sans-serif; font-size: 12pt;}
								p {margin: 0pt;}
								table.bordasimples {border-collapse: collapse;}
								table.bordasimples tr td {border:2px solid #000000;}
								ol {counter-reset: item; font-weight:bold;}
								li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 12pt; text-align:justify;}
								li:before {content: counters(item, "."); counter-increment: item;}
								ul {list-style-type: none; font-weight:normal;} 
								ul li {padding: 3px 0px;color: #000000;text-align:justify;}
								#redondo {padding:60px; border:3px #000000 solid; border-radius:15px; -moz-border-radius:15px; -webkit-border-radius:15px;} 
								div.rounded {border:1mm solid #000000; background-color: #FFFFFF; border-radius: 3mm / 3mm; background-clip: border-box; padding: 1em;}
								#cobtexto {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
								#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
								#disclame {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
							</style>
						</head>
						<body>

							<!--mpdf
							<htmlpageheader name="myheader">
								<table width="100%">
								  <tr>
								    <td colspan = "4"  style="text-align:right;">
										  <img src="'.$logo.'" width="230" height="75"/>
								    </td>						
								  </tr>	
								  <tr>
								    <td colspan = "4" style="text-align: center;">
										<span style="font-weight: bold; font-size: 14pt;">Conclusão de Liquidação de Declaração de Volume de Exportações</span>
								    </td>
								  </tr>
								</table>
							</htmlpageheader>
				
							<htmlpagefooter name="myfooter">
								<table width="100%" border="0">
								 	<tr>
									 	<td width="24%" style="text-align:center; font-size: 9pt;">Proc. SUSEP n.º: '.$c_SUSEP.'</td>
									 	<td width="52%" style="text-align:center; font-size: 8pt;">
										 	Página {PAGENO} de {nb}
										</td>
										<td width="24%" style="text-align:right; font-size: 9pt;">Versão 05/2015</td>
									</tr>
								</table>									
							</htmlpagefooter>
				
							<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
							<sethtmlpagefooter name="myfooter" value="on" />
							mpdf-->
				
							<table width="100%" style="border:solid 0px #fff;" cellspacing="0" cellpadding="3">
							  <tr>
									<td width="100%" colspan="3" style="text-align:left;"><b>Segurado: </b>'.$Nome_Segurado.'</td>
							  </tr>
							  <tr>
									<td width="40%" colspan="1" style="text-align:left;"><b>Apólice: </b>'.$n_Apolice.'</td>
									<td width="30%" colspan="1" style="text-align:left;"><b>Vigência: </b>'.$d_Inicio_Vigencia.' à '.$d_Fim_Vigencia.'</td>
									<td width="30%" colspan="1" style="text-align:left;"><b>Moeda: </b>'.$Nome_Moeda.'</td>
							  </tr>
							  <tr>
									<td width="40%" colspan="1" style="text-align:left;"><b>Data de Conclusão: </b>'.$d_Liquidacao.'</td>
									<td width="60%" colspan="2" style="text-align:left;"><b>Responsável: </b>'.$Nome_Usuario.'</td>
							  </tr>
							</table>
							
							<br>
							<br>';

							for ($i=0; $i < count($dados); $i++) { 
								$html .='
								<table width="100%" style="border:solid 1px #000;" cellspacing="0" cellpadding="3">
									<tr>
								    <th style="border:solid 1px #000;" rowspan="2">Nº SBCE</th>
								    <th style="border:solid 1px #000;" rowspan="2">Comprador</th>
								    <th style="border:solid 1px #000;" rowspan="2">País</th>
								    <th style="border:solid 1px #000;" rowspan="2">Data Embarque</th>
								    <th style="border:solid 1px #000;" rowspan="2">Nº da Fatura</th>
								    <th style="border:solid 1px #000;" rowspan="2">Data Vencimento</th>
								    <th style="border:solid 1px #000;" rowspan="2">Valor Embarcado</th>
								    <th style="border:solid 1px #000;" rowspan="1">Valor Financiado</th>
								  </tr>
								  <tr>
										<th style="border:solid 1px #000;">PROEX</th>
								  </tr>
								  <tr>
								    <td style="border:solid 1px #000;" rowspan="4">'.$dados[$i]['n_CRS'].'</td>
								    <td style="border:solid 1px #000;" rowspan="4">'.$dados[$i]['Nome_Comprador'].'</td>
								    <td style="border:solid 1px #000;" rowspan="4">'.$dados[$i]['Nome_Pais'].'</td>
								    <td style="border:solid 1px #000;">'.$dados[$i]['d_Embarque'].'</td>
								    <td style="border:solid 1px #000;">'.$dados[$i]['Num_Fatura'].'</td>
								    <td style="border:solid 1px #000;">'.$dados[$i]['d_Vencimento'].'</td>
								    <td style="border:solid 1px #000;">'.$dados[$i]['Sigla_Moeda'].' '.$dados[$i]['v_Embarque'].'</td>
								    <td style="border:solid 1px #000;">'.$dados[$i]['Sigla_Moeda'].' '.$dados[$i]['v_PROEX'].'</td>
								  </tr>
								  <tr>
								    <th style="border:solid 1px #000;">Data Pagamento</th>
								    <th style="border:solid 1px #000;">Num. Operação</th>
								    <th style="border:solid 1px #000;">Valor Pago</th>
								    <th style="border:solid 1px #000;">Saldo</th>
								    <th style="border:solid 1px #000;">ACE</th>
								  </tr>
								  <tr>
								    <td style="border:solid 1px #000;">'.$dados[$i]['d_Pagamento'].'</td>
								    <td style="border:solid 1px #000;">'.$dados[$i]['n_Operacao'].'</td>
								    <td style="border:solid 1px #000;">'.$dados[$i]['Sigla_Moeda'].' '.$dados[$i]['v_Pago'].'</td>
								    <td style="border:solid 1px #000;">'.$dados[$i]['Sigla_Moeda'].' '.$dados[$i]['v_Saldo'].'</td>
								    <td style="border:solid 1px #000;">'.$dados[$i]['Sigla_Moeda'].' '.$dados[$i]['v_ACE'].'</td>
								  </tr>
								</table>

								<div style="clear=both">&nbsp;</div>';
							}
							$html .= '
							<div id="cobtexto">
								Declaramos que as informações constantes neste documento são completas e verdadeiras e assumimos, sob as penas contratuais e legais, a responsabilidade por sua exatidão.
							</div>
						</body>
					</html>';

	$html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
	$mpdf->allow_charset_conversion=true;
	$mpdf->charset_in='UTF-8';
	$mpdf->WriteHTML($html);

	$mpdf->Output($pdfDir.$key.'LiquidacaoFat.pdf', 'F');
?>