

<?php  // Alterado Hicom (Gustavo) - 03/01/05 - enviar variável semValor para o pdf da proposta.

//print 'oi';

if($_SESSION['Configurar']){
	include_once("../../../config.php");
}else{
	$_SESSION['Configurar'] = '';
}

if(! function_exists('arruma_cnpj')){
  function arruma_cnpj($c){
    if(strlen($c) == 14 && preg_match("@([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})@", $c, $v)){
      return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
    }
    return $c;
  }
}

$cur = odbc_exec($db, "Select currency, Ga from Inform where id = $idInform");
$moeda = odbc_result($cur, "currency");
$ga = odbc_result($cur, "Ga");

//Alterado por Tiago V N - Elumini - 10/02/2006
if (($ga == "0") || ($ga =="")) { //Apolice Antiga
   $susep = "15.414005218/2005-89";
   $cp    = "CP/RC/05-01";
}else{       //Apolice GA
   $susep = "15.414004768/2004-08";
   $cp    = "CP/GA/05-01";
}


  
   

  $valida = "";
  $cur = odbc_exec($db, "Select currency from Inform where id = $idInform");
  $moeda = odbc_result($cur, "currency");

  if ($moeda == "2") {
     $conta = "5012-1";
     $agencia = "1778-7";
      $extMoeda = "USD ";
      $ext      = "DÓLARES NORTE-AMERICANOS";
      $valida = "sim";
  }else{
    $agencia = "1778-7";
    $conta = "5053-9";
    $extMoeda = "€ ";
    $ext      = "EUROS";
    $valida = "";
  }


// Emitir pdf pelo PHP 5 diretamente

 $sqlquery  = "Select E.* From Inform Inf
				Inner Join Produto P On
				  P.i_Produto = Inf.i_Produto
				Inner Join Empresa_Produto EP On
				  EP.i_Produto = P.i_Produto
				Inner Join Empresa E On
				  E.i_Empresa = EP.i_Empresa
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
				
	$disclame_retorno = $dados['Nome'].' CNPJ: '.formata_string('CNPJ', $dados['CNPJ']).', SUSEP no.: '. $dados['c_Susep'];


include_once("../../../gerar_pdf/MPDF45/mpdf.php");
    

    $opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
        'format' => 'A4'
        ];
    
    $mpdf=new  \Mpdf\Mpdf($opt);

    $html = ob_get_clean();
    // $mpdf->useOnlyCoreFonts = true;    // false is default
	//$mpdf->SetProtection(array('print'));
	$mpdf->SetTitle("Fatura");
	$mpdf->SetAuthor($dados['Nome']);
	$mpdf->SetWatermarkText(""); // fundo marca dágua
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.1;
	$mpdf->SetDisplayMode('fullpage');
	
	
	
	// Endereço do logotipo
    $logo  = '../../logo_pdf.jpg';
 

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
				#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:8pt; text-align:right;}
				
				div.rounded {
							border:0.5mm solid #000000; 
							background-color: #FFFFFF;
							border-radius: 2mm / 2mm;
							background-clip: border-box;
							padding: inherit;
				}
				
				#img1{
					width: 660px;
					height: 65px;
					background:url('.$logo.') no-repeat;

				}
				
				</style>
				
				</head>
				<body>
				<htmlpageheader name="myheader">';

				$fatNum = isset($fatNum) ? $fatNum : '';
				$apoNum = isset($apoNum) ? $apoNum : '';
		 
      $html .= ' 
					<htmlpageheader name="myheader">
							<div style="width:100%">
								<div id="img1"></div>
							</div>
					</htmlpageheader>
					
					<htmlpagefooter name="myfooter">
										
					</htmlpagefooter>
					
					<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
					<sethtmlpagefooter name="myfooter" value="on" />
					
					<table width="100%" border="0" >
						<tr>
						    <td colspan="1" width="30%">&nbsp;</td>					
							<td colspan="1" width="40%" style="text-align:center; font-weight: bold; font-size: 14pt;">FATURA '.$fatNum.'</td>
						    <td colspan="1" width="30%">&nbsp;</td>
						</tr>
						<tr>
						   <td colspan="3" width="100%">&nbsp;</td>						  
					    </tr>
					</table>
					
					<div class="rounded">    
						      <table width="100%" border="0" >
								<tr>
								   <td colspan="1" width="33%"><b>Apólice n°: </b>'.$apoNum.'</td>
								   <td colspan="1" width="33%"><b>Endosso n°:</b> 0 </td>
								   <td colspan="1" width="34%"><b>Proposta n°:</b> '.$contract.'</td>
								</tr>
								<tr>
									<td colspan="3" width="100%"><b>Vigência da apólice:</b> '.$today.'</td>
								</tr>
								<tr>
									<td colspan="3" width="100%"><b>Nome segurado/sacado:</b> '.$name.'</td>
								</tr>
								<tr>
									<td colspan="3" width="100%"><b>Endereço:</b> '.$address.'</td>
								</tr>
								<tr>
									<td colspan="1" width="50%"><b>CEP: </b>'.$cep.'</td>
									<td colspan="2" width="50%"><b>CNPJ:</b> '. arruma_cnpj($cnpj).'</td>
								</tr>
							  </table>	
							 
					</div>
					<br>
					
					<div class="rounded">
						<table width="100%" border="0" >
							<tr>
							   <td colspan="3" width="100%"><b>Descrição:</b></td>
							</tr>					
							<tr>
							   <td colspan="3" width="100%">Valor referente a parcela 1/'.$numPre.' da apólice supracitada.</td>
							</tr>
						</table>
					</div>
					<br>
					<div class="rounded">
							<table width="100%" border="0" >
								<tr>
									<td colspan="1">&nbsp;</td>
									<td colspan="1" style="text-align:center;"><b>VALORES EM '.$extMoeda.''. $ext.'</b></td>
									<td colspan="1">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3" width="100%"><b>Valor da Parcela: </b>'. str_replace('USD','US$',$valPar). ' ('.$valParExt.')</td>
								</tr>
								<tr>
									<td colspan="3" width="100%"><b>Vencimento da parcela:</b> '.$five.'</td>
								</tr>
								<tr>
									<td colspan="3" width="100%"><b>Prêmio Mínimo:</b> '. str_replace('USD','US$',$prPrint) .'</td>
								</tr>
							</table>
					</div>
					<br>
					<div class="rounded">
							<table width="100%" border="0" >
								<tr>
									<td colspan="3" width="100%" style="text-align:center;"><b>INSTRUÇÕES</b></td>
								</tr>
								<tr>
									<td colspan="3" width="100%" style="font-size:12pt; text-align:justify;">Creditar conta abaixo conforme resolução n.° 002532 BACEN de 14 de agosto de 1998 e instruções a seguir:
									Todas as despesas bancárias, no Brasil e no Exterior, incorrerão por conta do segurado.
									Ordens formatadas no sistema SWIFT, MT 103.<br>
									Preenchimento do campo 59: '. strtoupper($dados['Nome']).'<br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									C/C.: '.($valida ? "05.012-1": "5053-9").' Agência 1778-7<br>
									Preenchimento do Campo 71a: OUR (despesas bancárias por conta do segurado)<br>
									Banco do Brasil S/A<br>
									Agência Internacional Rio<br>
									Swift BRASBRRJRJO
									</td>
								</tr>
							</table>
					</div>
					<br>
					<div class="rounded" style="padding: 2mm; width:700px;">
						<div align="center">
						     <strong>PARA USO DO BANCO CONTRATANTE</strong>
						</div>
						<br>
						   
						   <div class="rounded" style="height:100px; width:157px; float:left; text-align:center; font-size: 10pt;">							
							<b>Banco Contratante </b>
						   </div>
						   <div style="height:100px; width:6px;float:left;">&nbsp; </div>
						   
						   <div class="rounded" style="height:100px; width:157px;float:left; text-align:center;font-size: 10pt;">
						      <b>N.° Contrato de Câmbio</b>
						   </div>	
						   
						   <div style="height:100px; width:6px;float:left;">&nbsp; </div>
						   
						   <div class="rounded" style="height:100px; width:155px;float:left; text-align:center;font-size: 10pt;">
						      <b>Data</b>
						   </div>
						   
						   <div style="height:100px; width:6px;float:left;">&nbsp;</div>
						   	
						   <div class="rounded" style="height:100px; width:157px;float:left; text-align:center;font-size: 10pt;">
						       <b>Assinatura</b>
						   </div>			   
				   </div>
		           <br><br>
		           <table width="100%" border="0">
					   <tr>
						  <td colspan="1"width="35%">&nbsp;</td>
						  <td colspan="1"width="65%" style="text-align:right">'.$cp.'</td>
					   </tr>
				   </table>
				  
				   
					
					  
	</body>
    </html>';
  
  
  
   $html = utf8_encode($html);
   $mpdf->allow_charset_conversion=true;
   $mpdf->charset_in='UTF-8';
   $mpdf->WriteHTML($html);
   
   //$mpdf->Output();
   //$mpdf->Output($key.$file.'.pdf','D');
   
   $mpdf->Output($pdfDir.$key.'parcela.pdf','F');    
   $url_pdfparc = $host.'src/download/'.$key.'parcela.pdf';
  
 

   //exit();
   
   

 
?>




