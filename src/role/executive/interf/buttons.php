<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php

?>

<!--
 Alterado Hicom (Gustavo) - 03/01/05 - exibir proposta e parcela com o texto "Sem valor" para p o executivo
-->
<style type="text/css">
<!--
.servicos {  
	font-family: Verdana, Arial, Helvetica, sans-serif; 
	font-size: 10px; 
	font-weight: bold; 
	color: #000000; 
	background-color: #E1ECFF; 
}
-->
</style>

<script language="javascript"  type="text/javascript" src="<?php echo $host;?>Scripts/jquery.form.js"></script>

	<script>
		function editarProposta(){
			$(".js_prop_edit").toggle();
		}

		$(document).ready(function() {

			$("#modal_upload").on("click", function(){
				$(".modal-ext").show();
			});

			$("#close_modal").on("click", function(){
				$(".modal-ext").hide();
			});
				
			$("#form-upload").ajaxForm({
			  complete: function(xhr) {
			    alert($.trim(xhr.responseText));

			    $("#save_upload").html("Salvar");
			    $("#save_upload").prop("disabled", false);

			    $(".modal-ext").hide();
			  }
			});

			$("#save_upload").on("click", function() {
				$("#save_upload").html("Aguarde...");
				$("#save_upload").prop("disabled", true);

				$("#form-upload").submit();
			});

			$("#terlinha").on("click", function(e) {
				editarProposta();

				$("#textoCorpo_visualaid").removeClass('mceButton mceButtonEnabled mce_visualaid mceButtonActive');
				$("#textoCorpo_visualaid").addClass('mceButton mceButtonEnabled mce_visualaid');

				e.preventDefault();
			});

		});
	</script>

<script type="text/javascript">

function muda(url, mesmo, inf, not,str){
  args = "idInform=" + inf + "&idNotification=" + not + "&key=<?php   echo $key;?>&ids=<?php echo $userID;?>";
    
  if(str == "Enviar" && mesmo != 0){
    top.location = "../../role/executive/Executive.php?comm=sendProp&" + args;
    return;
  }
    
  url = url + "&" + args;
 
  
  if(mesmo == 1){
    top.location = url;
  }else{ // cancela
    top.location = url;
  }
}

</script>

  
<?php include_once('../../../navegacao.php'); 

		//INTERAKTIV 18/06/2014
    //$sql = "select  CAST(proposta AS TEXT) AS proposta from InformPdf where idInform = '". $idInform."'";
    //$re = odbc_exec($db,$sql); ?>

<script language="javascript"  type="text/javascript" src="<?php echo $host;?>Scripts/tinymce/tiny_mce/tiny_mce.js"></script>
<!-- <script language="javascript"  type="text/javascript" src="<?php echo $host;?>Scripts/tinymce/tiny_mce/basic_config.js"></script> -->
 <script language="javascript" type="text/javascript">
   tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
       // theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
       // theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        //theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
      //  theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
      
	    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect|cut,copy,anchor,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        //theme_advanced_buttons2 : "cut,copy,paste,pastetext|anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	    theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : "<?php echo $host;?>Scripts/tinymce/css/example.css",

        // Drop lists for link/image/media/template dialogs
       // template_external_list_url : "<?php echo $host;?>Scripts/tinymce/js/template_list.js",
      //  external_link_list_url : "<?php echo $host;?>Scripts/tinymce/js/link_list.js",
       // external_image_list_url : "<?php echo $host;?>Scripts/tinymce/js/image_list.js",
      //  media_external_list_url : "<?php echo $host;?>Scripts/tinymce/js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});
	
	
   </script>
   
<div class="conteudopagina">
    <ul> 
	   <li class="campo3colunas"> 
		  <label>Deseja Emitir este(s) documento(s)?</label>
	   </li> 
	   <div style="clear:both">&nbsp;</div>
	   <li class="campo3colunas"> 
		   <label><a href="<?php echo $url_pdfprop;?>" target="_blank">Proposta Original</a></label>
	   </li>
      <div style="clear:both">&nbsp;</div>
       <?php
	      $final = $url_pdfprop;
		  $final = substr($final,0,strlen($final)-4);
		  $finalx = explode("/",$final);
		  
	     		 
		  if(file_exists($pdfDir.$finalx[6].'_alterada.pdf')){  ?>        
               <li class="campo3colunas">
               
                   <label><a href="<?php echo $final.'_alterada'.'.pdf';?>" target="_blank">Proposta alterada</a></label>
               </li>
    <?php }  ?>
       
	  <div style="clear:both">&nbsp;</div>
		  <li class="campo3colunas"> 
			  <label><a href="#" id="terlinha">Editar Proposta Original</a></label>
		  </li>

	   <?php /*if ($i_Produto > 1){?>
				   <div style="clear:both">&nbsp;</div>
				   <li class="campo2colunas"> 
					   <label><a href="<?php echo $url_pdfparc;?>" target="_blank">Primeira Parcela</a></label>
				   </li> 
	   <?php }*/ ?>  
	   
	</ul>   
	<br clear="all" />
	<!-- </div>-->
<form action="<?php echo $root;?>role/executive/editar_proposta_pdf.php" name="form1" method="post" target="_blank" style="width:90%; height:auto !important;">
	<div id="prop_edit" class="js_prop_edit" style="display:none; width:100%; height:auto !important">  
	<?php //echo odbc_result($re,'proposta');
	   
	   
		//include_once($host."gerar_pdf/MPDF45/mpdf.php");
  	include_once("../../../gerar_pdf/MPDF45/mpdf.php");

  	$rp_root = realpath($root);

 		include_once($rp_root.DIRECTORY_SEPARATOR."role".DIRECTORY_SEPARATOR."consultaCoface.php");

		$sqlquery  = "Select E.*,P.Nome as Produto, SP.Descricao as SubProduto, SP.c_SUSEP,Inf.i_Gerente,Inf.i_Produto, Inf.contrat, Inf.nProp
			From Inform Inf
			Inner Join Produto P On
				P.i_Produto = Inf.i_Produto
			Inner Join Empresa_Produto EP On
				EP.i_Produto = P.i_Produto
			Inner Join Empresa E On
				E.i_Empresa = EP.i_Empresa
			Inner Join Sub_Produto SP On
				SP.i_Produto = Inf.i_Produto
				And SP.i_Sub_Produto = Inf.i_Sub_Produto
			Where
				Inf.id = ".$idInform;
	   
		$res = odbc_exec($db,$sqlquery);
		$dados = odbc_fetch_array($res);
	   
		$i_Gerente = $dados['i_Gerente'];
		$i_Produto = $dados['i_Produto'];
		//print $i_Produto;
		$retorno_rodape =  $dados['Endereco'].' - '.
			$dados['Complemento'].' - '.
			'CEP '.formata_string('CEP',$dados['CEP']).' - '.
			$dados['Cidade'].', '.
			$dados['Estado']. ' '.
			'Tel.: '.$dados['Telefone'].'  '.
			'Fax: ' . $dados['Fax'].'  '.
			'Home Page: '. $dados['HomePage'];
					
		$disclame_retorno = $dados['Nome'].' CNPJ: '.formata_string('CNPJ', $dados['CNPJ']).', SUSEP no.: '. $dados['c_SUSEP'];
		
		$sqlEmp  = "SELECT Nome, CNPJ,	Endereco,	Complemento, CEP, Cidade,	Estado,	Cod_Area,	Telefone,	Bairro, Fax, HomePage
								FROM Empresa 
								WHERE i_Empresa = 1";  
	   
	 	$resEmp = odbc_exec($db,$sqlEmp);
	 	$dadosEmp = odbc_fetch_array($resEmp);		
		
		$compEmp = $dadosEmp['Complemento'] ? ' - '.$dadosEmp['Complemento'] : '';
		$cepEmp = $dadosEmp['CEP'] ? ' - Cep: '.$dadosEmp['CEP'] : '';
		$cidEmp = $dadosEmp['Cidade'] ? $dadosEmp['Cidade'] : '';
		$estEmp = $dadosEmp['Estado'] ? ', '.$dadosEmp['Estado'] : '';
		$telEmp = $dadosEmp['Telefone'] ? ' Tel.: '.$dadosEmp['Telefone'] : '';
		$faxEmp = $dadosEmp['Fax'] ? ' Fax: '.$dadosEmp['Fax'] : '';

		$enderecoEmp = $dadosEmp['Endereco'].$compEmp.$cepEmp;
		$compEmp = $cidEmp.$estEmp.$telEmp.$faxEmp;

		$siteEmp = $dadosEmp['HomePage'];
		$nomeEmp = $dadosEmp['Nome'];
					
		$disclame_retorno = $dados['Nome'].' CNPJ: '.formata_string('CNPJ', $dados['CNPJ']).', SUSEP no.: '. $dados['c_SUSEP'];
													  
    $opt = ['mode' => 'win-1252','tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
	        'format' => 'A4',
	        'margin_left' => 20,
	        'margin_right' => 15,
	        'margin_top' => 50,
	        'margin_bottom' => 25,
	        'margin_header' => 10,
	        'margin_footer' => 10
	        ];
	    
	    $mpdf=new  \Mpdf\Mpdf($opt);

    $html = ob_get_clean();
    // $mpdf->useOnlyCoreFonts = true;    // false is default
    //$mpdf->SetProtection(array('print'));
    $mpdf->SetTitle("Proposta");
    $mpdf->SetAuthor($dados['Nome']);
    $mpdf->SetWatermarkText(""); // fundo marca dágua
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->SetDisplayMode('fullpage');
	
    // Endereço do logotipo
    $logo  = '../../images/logo.jpg';
    $logo_peq  	= '../../images/logo_peq.jpg';
		
		  $qry = "SELECT Distinct u.login, inf.respName , inf.idRegion, inf.name as NomeFantasia, inf.txMin,inf.prMTotal,
							 inf.cnpj, inf.ie, inf.address, inf.tel, inf.fax, inf.email, inf.ocupationContact,
							 inf.city, r.name AS uf, Sector.description, inf.warantyInterest,
							 inf.periodMaxCred, Country.name, inf.cep, inf.contrat, inf.prodUnit, inf.i_Seg,
							 inf.contact, inf.products, inf.addressNumber, inf.pvigencia, inf.currency,
							 inf.addressComp,inf.prMin,inf.prMTotal,txMTotal,limPagIndeniz,inf.perPart0, 
							 inf.perPart1, inf.v_LMI,Renovacao_Tacita,inf.nProp
						   FROM Users u INNER JOIN
					 Insured i ON u.id = i.idResp INNER JOIN
							 Inform inf ON inf.idInsured = i.id INNER JOIN
							 Sector ON inf.idSector = Sector.id LEFT JOIN
							 Importer ON inf.id = Importer.idInform LEFT JOIN
							 Country ON Importer.idCountry = Country.id JOIN
							 Region r ON inf.idRegion = r.id
						   WHERE inf.id = $idInform";
						   
		  $cur = odbc_exec ($db,$qry);
		  
		  
		 
		  
		  
		  // tenta achar o usuário responsável
		 if (odbc_fetch_row ($cur)) {
			
				$login    = odbc_result($cur, 1);
				$respName = odbc_result($cur, 2);
				$nomeFantasia =  odbc_result($cur, 'NomeFantasia');
					if(! $respName){
					  $respName = odbc_result($cur, 'contact');
					}
				$idRegion = odbc_result($cur, 3);
				$name     = odbc_result($cur, 'NomeFantasia');
				$txMin    = odbc_result($cur, 5);
				//$prMin    = odbc_result($cur, 6);
				$prMin    = odbc_result($cur, 'prMin');
				$cnpj     = odbc_result($cur, 7);
				$ie       = odbc_result($cur, 8);
				$address  = odbc_result($cur, 9);
				$addNumber= odbc_result($cur, 26);  //Adicionado por Andréa em 08/06/05
				$tel      = odbc_result($cur, 10);
				$fax      = odbc_result($cur, 11);
				$email    = odbc_result($cur, 12);
				$oContact = odbc_result($cur, 13);
				$city     = odbc_result($cur, 14);
				$uf       = substr(odbc_result($cur, 15), 0, 2);
				$descrip  = odbc_result($cur, 16);
				$interest = odbc_result($cur, 17);
				$period   = odbc_result($cur, 18);
				$cep      = odbc_result($cur, 20);
				$renovacao_Tacica  = odbc_result($curx,'Renovacao_Tacita');
				//Alterado por Tiago V N - Elumini - 24/09/2007
				$complemento = odbc_result($cur, "addressComp");
				$limPagIndeniz   = odbc_result($cur, 'limPagIndeniz');
				$ValorLMI   = odbc_result($cur, 'v_LMI');	
				$pvigencia= odbc_Result($cur, "pvigencia");
				//Alterador por Tiago V N -Elumini - 06/04/2006
				$moeda    = odbc_result($cur, "currency");
					if ($moeda == "1"){
					  $extMoeda = "R$ ";
					  $ext      = "REAIS";
					}else if ($moeda == "2"){
					  $extMoeda = "USD ";
					  $ext      = "DÓLARES NORTE-AMERICANOS";
					}else if ($moeda == "6") {
					  $extMoeda = "€ ";
					  $ext      = "EUROS";
					}elseif ($moeda == "0") {
					  $extMoeda = "USD ";
					  $ext      = "DÓLARES NORTE-AMERICANOS";
					}
			   
//				$sub = substr($cep, 0, 5);
//					if(! preg_match("\.", $sub)){
//					  $sub = substr($sub, 0, 2). '.'. substr($sub, 2, 3);
//					}else{
//					  $inc = 1;
//					  $sub = substr($cep, 0, 6);
//					}
//					if(! preg_match("-", $cep)){
//					  $cep = "$sub-". substr($cep, 5);
//					}else{
//					  $cep = "$sub-". substr($cep, 6 + $inc);
//					}
			
				//$prodUnit = odbc_result($cur, 'prodUnit');
				$iSeg = odbc_result($cur, 'i_Seg');
			
				// encontrar o número de propostas
				$contract = odbc_result($cur, 21).'/'.odbc_result($cur, 'nProp');
				$products = odbc_result($cur, 'products');
			
				//Endereço do proponente
			
				$endcompleto = "$address". ($addNumber ? ", ".$addNumber : "").($complemento ? " - ".$complemento : "" );
			
					if ( ! $addNumber) {     //alterei essa parte em 08/06/05(adicionei o numero do endereço
						$end = $address." - ".$city." - CEP: ".$cep." - ".$uf;
						$address = $address." - ".$city." - ".$uf;
					}else{
					   $end = $address.", ".$addNumber." - ".$city." - CEP: ".$cep." - ".$uf;
					   $address = $address.", ".$addNumber." - ".$city." - ".$uf;
					}
				$tx = $txMin * (1 + $field->getNumField("txRise") / 100) * 100;
			
			
				// 28 -12-2009  Elias Vaz - Interaktiv
				// Alteração realizada para simplificar a exibição da taxa mínima e prêmio mínimo
				
				
				
				$prMTotal = odbc_result($cur, 'prMTotal');
				$txMTotal = odbc_result($cur, 'txMTotal');
				$tx = $txMTotal;
				$pr = $prMTotal;
			
			
				$partDeducao  = number_format(odbc_result($cur, 'perPart0'),2,',','.').'%';
				$partLucro    = number_format(odbc_result($cur, 'perPart1'),2,',','.').'%';
				
				//$pr = $prMin * (1 + $field->getNumField("txRise") / 100);
				$pr = number_format($pr,2,".","");
				
				
				$tx = number_format ($tx, 4, '.','');
			
			   
				
				//  $xPr = number_format ((odbc_result($cur, 1) * (1 + odbc_result($cur, 3))), 2, '.', '');

				if((int)$tx != 0){
					$totasseg = $pr / ($tx/100);  //coloquei agora
				} else {
					$totasseg = $pr;
				}

				$totasseg = $extMoeda . number_format ($totasseg, 2, ',','.');
			
		  }
		
	?>		

		<?php
		//Proposta editavel

		if($i_Produto  == 2){  // produto externo
					 
					$html .= '<div id="sublinhado">DADOS DO PROPONENTE:</div><br>
			<table width="100%" border="0" style="font-size: 12pt;">					
				<tr>
					<td width="25%">Razão Social: </td><td width="75%"><span style="font-weight: bold; font-size: 12pt;"><div id="cobtexto">'.strtoupper(trim($name)).'</div></span></td>
				</tr>

				<tr>
					<td width="25%">Endereco: </td><td width="75%"><div id="cobtexto">'.$endcompleto.'</div></td>
				</tr> 

				<tr>
					<td width="25%">&nbsp;</td><td width="75%"><div id="cobtexto">'.$city .' - '. $uf.'</div></td>
				</tr>

				<tr>  
					<td width="25%">&nbsp;</td><td width="75%"><div id="cobtexto">CEP '.$cep.'</div></td>
				</tr>

				<tr>
					<td width="25%">CNPJ: </td><td width="75%"><div id="cobtexto">'.arruma_cnpj($cnpj).'</div></td>			
				</tr>

				<tr>
					<td width="25%">Corretor Nomeado: </td><td width="75%"><div id="cobtexto">'.($MultiCorretor != ''? $MultiCorretor: $corretor).'</div></td>			
				</tr>
			</table> 
					
			<br>
			<div style="border-top: 1px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>

			<br><div id="sublinhado">COBERTURA:</div>					

			<table width="100%" border="0" style="font-size: 12pt;">
				<tr>
					<td colspan="1" width="8%" style="font-weight:bold">1.</td>
					<td colspan="2" width="92%" style="font-weight:bold">ABRANGÊNCIA DO SEGURO:</td>			
				</tr>

				<tr>
					<td  colspan="1" width="8%">&nbsp;</td>
					<td  colspan="1" width="8%" style="font-weight:bold">1.1</td>
					<td  colspan="1" width="84%" style="font-weight:bold">Natureza das vendas seguradas:</td>			
				</tr>

				<tr>
					<td colspan="2" width="16%">&nbsp;</td>
					<td colspan="1" width="84%"><div id="cobtexto">'.$products.'</div></td>			
				</tr>
				
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>

				<tr>
					<td colspan="1" width="8%" style="font-weight:bold">2.</td>
					<td colspan="2" width="92%" style="font-weight:bold">RISCOS COBERTOS:</td>			
				</tr>

				<tr>
					<td  colspan="1" width="8%">&nbsp;</td>
					<td  colspan="1" width="8%" style="font-weight:bold">2.1</td>
					<td  colspan="1" width="84%" style="font-weight:bold">TIPO DE IMPORTADOR:</td>			
				</tr>

				<tr>
					<td colspan="2" width="16%">&nbsp;</td>
					<td colspan="1" width="84%">Privado</td>			
				</tr>

				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>

				<tr>
					<td  colspan="1" width="8%">&nbsp;</td>
					<td  colspan="1" width="8%" style="font-weight:bold">2.2</td>
					<td  colspan="1" width="84%" style="font-weight:bold">PAÍSES COBERTOS:</td>			
				</tr>

				<tr>
					<td colspan="2" width="16%">&nbsp;</td>
					<td colspan="1" width="84%">a) Risco comercial: todos, exceto Brasil.</td>												 
				</tr>';
			   
			   if($riscopolitico){				   	
					$html .=	'<tr>
							<td colspan="2" width="16%">&nbsp;</td>
							<td colspan="1" width="84%"><div id="cobtexto">b) Risco político: '. $riscopolitico . ' </div></td>
						</tr>';
			   }

			   $percCoverage = (int)$percCoverage;
				
			$html .=	'	

				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>

				<tr>
					<td  colspan="1" width="8%">&nbsp;</td>
					<td  colspan="1" width="8%" style="font-weight:bold">2.3</td>
					<td  colspan="1" width="84%" style="font-weight:bold">PERCENTAGEM DE COBERTURA:</td>			
				</tr>

				<tr>
					<td colspan="2" width="16%">&nbsp;</td>
					<td colspan="1" width="84%"><div id="cobtexto">'. round($percCoverage).'%</div></td>			
				</tr>

				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>

				<tr>
					<td  colspan="1" width="8%">&nbsp;</td>
					<td  colspan="1" width="8%" style="font-weight:bold">2.4</td>
					<td  colspan="1" width="84%" style="font-weight:bold">TAXA DE PRÊMIO:</td>			
				</tr>

				<tr>
					<td colspan="2" width="16%">&nbsp;</td>
					<td colspan="1" width="84%"><div id="cobtexto">'.number_format($tx,4,",",".").'%  aplicável ao volume de exportações</div></td>			
				</tr>

				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>

				<tr>
					<td  colspan="1" width="8%">&nbsp;</td>
					<td  colspan="1" width="8%" style="font-weight:bold">2.5</td>
					<td  colspan="1" width="84%" style="font-weight:bold">PRÊMIO MÍNIMO:</td>			
				</tr>

			    <tr>
			       <td colspan="2" width="16%">&nbsp;</td>
				   <td colspan="1" width="84%" style="text-align:justify;">';

					$sqlvig1 = "select count(*) as quantidade from Periodo_Vigencia where i_Inform = ".$idInform;
					$rres1 = odbc_exec($db, $sqlvig1);
					$qtdeVig  = odbc_result($rres1, "quantidade");

					
					$num  = $qtdeVig;
				
					if($num > 1){
						$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
						$rres = odbc_exec($db, $sqlvig);

						while(odbc_fetch_row($rres)){
							$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
							$html .= '<div id="cobtexto">'.$mo.' '. number_format(odbc_result($rres,'v_Premio'), 2, ",", ".").' ('.$valorext.') pelo período compreendido entre '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'.<br><br></div>';
						}
						$html .= 'Prêmio Mínimo Total por todo período de seguro:<br>';
						$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).').<br></div>';
					} else{
						$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).').<br></div>';
					}

				   $html .= '</td>			
			    </tr>';				
						      
				if($jurosMora){
					$html .= '
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>

						<tr>
							<td  colspan="1" width="8%">&nbsp;</td>
							<td  colspan="1" width="8%" style="font-weight:bold">2.6</td>
							<td  colspan="1" width="84%" style="font-weight:bold">CONDIÇÕES ESPECIAIS DE COBERTURA:</td>			
						</tr>

						<tr>
							<td colspan="2" width="16%">&nbsp;</td>
							<td colspan="1" width="84%" style="text-align:justify;"><div id="cobtexto">O SEGURADO contrata cobertura acessória de juros operacionais e moratórios, cujo adicional de prêmio é de 4% sobre o prêmio da apólice.</div></td>			
						</tr>';
				}
								 			 
				$html .= '
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<tr>
						<td colspan="1" width="8%" style="font-weight:bold">3.</td>
						<td colspan="2" width="92%" style="font-weight:bold">MOEDA DA APÓLICE</td>			
					</tr>						

					<tr>
					   	<td  colspan="1" width="8%">&nbsp;</td>
						<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">A moeda da apólice é o '. strtolower($ext) .' ('.$mo.')</div></td>   			
					</tr>
						
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
				
					<tr>
						<td colspan="1" width="8%" style="font-weight:bold">4.</td>
						<td colspan="2" width="92%" style="font-weight:bold">TAXA DE CÂMBIO</td>			
					</tr>
					<tr>
					   	<td  colspan="1" width="8%">&nbsp;</td>
						<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">A conversão de quaisquer valores será sempre efetuada mediante aplicação da taxa de câmbio divulgada pelo Banco Central do Brasil PTAX800.
							</div>
					 	</td>   			
					</tr>	
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					
					<tr>
					       	<td colspan="1" width="8%" style="font-weight:bold">5.</td>
						<td colspan="2" width="92%" style="font-weight:bold">LIMITE MÁXIMO DE INDENIZAÇÃO</td>			
					</tr>';

				if($limPagIndeniz){
					$html .= '
						<tr>
					       		<td  colspan="1" width="8%">&nbsp;</td>
							<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'.number_format($limPagIndeniz, 0, '', '.') .' vezes o prêmio pago por cada período de 12 meses de vigência da apólice.</div></td>
						</tr>';
				} else{
					$html .= '
						<tr>
					       		<td  colspan="1" width="8%">&nbsp;</td>
							<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">O Limite máximo para pagamento de indenizações por período de vigência da apólice é de '.$mo. ' ' .number_format($ValorLMI, 2, ',', '.') .' (' . strtolower($ExtValorLMI) .').</div></td>
						</tr>';
				}

				$html .= '
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<tr>
					       	<td colspan="1" width="8%" style="font-weight:bold">6.</td>
						<td colspan="2" width="92%" style="font-weight:bold">PRAZO MÁXIMO DE CRÉDITO</td>			
					</tr>

					<tr>
					       	<td  colspan="1" width="8%">&nbsp;</td>
						<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'.$periodoMaxCredito.' dias contados a partir da data do embarque internacional das mercadorias vendidas ou da data do faturamento dos serviços prestados.</div></td>
					</tr>

					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<tr>
					       	<td colspan="1" width="8%" style="font-weight:bold">7.</td>
						<td colspan="2" width="92%" style="font-weight:bold">PRAZO PARA NOTIFICAÇÃO DE AMEAÇA DE SINISTROS</td>			
					</tr>

					<tr>
					   	<td  colspan="1" width="8%">&nbsp;</td>
						<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'. ($periodoMaxCredito + 30).' dias contados a partir da data do embarque internacional das mercadorias vendidas ou da data do faturamento dos serviços prestados.</div></td>
					</tr>

					<tr>
					    <td  colspan="1" width="8%">&nbsp;</td>
						<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">No caso de prorrogação da data de vencimento, dentro das condições constantes no item 2.2.3 da cláusula 2 das condições gerais da apólice, o prazo é de 30 dias contados do novo vencimento.</div></td>
					</tr>

					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<tr>
					       	<td colspan="1" width="8%" style="font-weight:bold">8.</td>
						<td colspan="2" width="92%" style="font-weight:bold">VIGÊNCIA DA APÓLICE</td>			
					</tr>
                   ';
			   
				  // verificações 
		          	// INTERAKTIV 18/06/2014
					$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
					$rres = odbc_exec($db, $sqlvig);
					$num  = 1;
					$indx = 0;
					while(odbc_fetch_row($rres)){
						$indx++;
					}
				
					if($num){
						$ssqx = "select MIN(d_Vigencia_Inicial)as Ini, MAX(d_Vigencia_Final) as Fim from Periodo_Vigencia where i_Inform =".$idInform. "";
						$rresx = odbc_exec($db, $ssqx);
						$html .= '<tr>
									<td colspan="1" width="8%">&nbsp;</td>
									<td colspan="2" width="92%" style="text-align:justify;">
									<div id="cobtexto">
									A apólice entra em vigor no dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Ini'),0,10)).' e terá validade até o dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Fim'),0,10)).', cujo período equivale ao período de seguro.
									<br><br></div>';
						
						//$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).')<br></div>';

						
						$indx1 = 0;
						
						if ($indx > 1) {
							$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
							$rres = odbc_exec($db, $sqlvig);
							
							while(odbc_fetch_row($rres)){
								$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
								$html .= '<div id="cobtexto">Período '.odbc_result($rres,'n_Preriodo').' – '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'<br></div>';
								$indx1++;
							}
						}
					
						if($renovacao_Tacica == 1){ // campo no inform
					    	$html .= '
								<div id="cobtexto">
								A apólice poderá ser renovada automaticamente, por igual período de seguro, caso não seja comunicada a intenção de não renovação por qualquer das partes, devendo tal comunicação de não renovação ser feita por escrito pelo SEGURADO ou pela SEGURADORA com antecedência de '.$Prazo_Nao_Intencao_Renov.' dias do termo final do período de seguro total. </div>
								</td>			
								</tr>
							';
						}else{
							$html .= '	 
								<div id="cobtexto"><br>
								Revoga-se o item 12.2 das Condições Gerais da apólice o qual passará a vigorar com a seguinte redação:</div>
								<div id="cobtexto"><br>
								"A apólice tem duração definida nas CONDIÇÕES PARTICULARES e não pode ser renovada tacitamente."</div>
								</td>			
						   		</tr>
					     	';
						
						}
					}else{				   
				   
						$html .= '<tr>
					       	<td  colspan="1" width="8%">&nbsp;</td>
							<td  colspan="2" width="92%" style="text-align:justify;">
							<div id="cobtexto">A apólice entra em vigor no dia '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)).' e terá validade até o dia '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)).', cujo período equivalente ao período de seguro.<br></div>';
					
							if($renovacao_Tacica ==1){ // campo no inform
							    $html .= '
									<div id="cobtexto">
									A apólice poderá ser renovada automaticamente, por igual período de seguro, caso não seja comunicada a intenção de não renovação por qualquer das partes, devendo tal comunicação de não renovação ser feita por escrito pelo SEGURADO ou pela SEGURADORA com antecedência de '.$Prazo_Nao_Intencao_Renov.' dias do termo final do primeiro período de seguro. </div>
									</td>			
									</tr>
							';
							}else{
								 $html .= '	 
									<div id="cobtexto"><br>
									Revoga-se o item 12.2 das Condições Gerais da apólice o qual passará a vigorar com a seguinte redação:</div>
									<div id="cobtexto"><br>
									"A apólice tem duração definida nas CONDIÇÕES PARTICULARES e não pode ser renovada tacitamente."</div>
									</td>			
								   	</tr>
							     ';
								
							}
					
				}
					
					$html .= '<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<tr>
						<td colspan="1" width="8%" style="font-weight:bold">9.</td>
						<td colspan="2" width="92%" style="font-weight:bold">MÓDULOS</td>			
					</tr>

					<tr>
					       	<td  colspan="1" width="8%">&nbsp;</td>
						<td  colspan="2" width="92%">Os seguintes módulos fazem parte desta proposta:</td>						   			
					</tr> 
			</table>
						
			<table width="100%" border="0" style="font-size: 12pt;"> ';
				//Início da exibição dos módulos
						
				$qryM = "select a.i_Modulo, b.Cod_Modulo,b.Grupo_Modulo,b.Titulo_Modulo, 
					cast(a.Desc_Modulo as nvarchar(3900)) as Desc_Modulo
					from Inform_Modulo a inner join Modulo b on a.i_Modulo = b.i_Modulo 
					where a.idInform = ". $idInform. " order by b.Ordem_Modulo";	
								
				$mod = odbc_exec($db,$qryM);
						
				$GrupoModulo = "";
						
				while(odbc_fetch_row($mod)){ 
					$descricao_Modulo = odbc_result($mod,'Desc_Modulo');
					//print $descricao_Modulo;
							   
					if(odbc_result($mod,'Grupo_Modulo') != $GrupoModulo){
						$GrupoModulo  = odbc_result($mod,'Grupo_Modulo');

						$html .= '<tr>
								<td colspan="4">&nbsp;</td>
							</tr>

							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="3" width="92%" style="font-weight:bold"><div id="sublinhado">'.odbc_result($mod,'Grupo_Modulo').'</div></td>
							</tr>';
					}
								   
					$Titulo = strlen(odbc_result($mod,'Titulo_Modulo'));

					$html .= '<tr>
						<td  colspan="1" width="8%">&nbsp;</td>
						<td  colspan="1" width="8%">&nbsp;</td>
						<td  colspan="1" width="8%" style="font-weight:bold"><div id="cobtexto">'.odbc_result($mod,'Cod_Modulo').'</div></td>
						<td  colspan="1" width="76%" style="font-weight:bold"><div id="cobtexto">'.odbc_result($mod,'Titulo_Modulo').'</div></td>						   			
					</tr>';

					/*
					if($Titulo > 47){
						'<tr>
							<td  colspan="1" width="8%">&nbsp;</td>
							<td  colspan="1" width="8%">&nbsp;</td>
							<td colspan="1" width="8%">&nbsp;</td>
							<td colspan="1" width="76%" style="font-weight:bold">'.substr(odbc_result($mod,'Titulo_Modulo'),47,$Titulo).'</td>
						</tr>';
					}
					*/

					if(odbc_result($mod,'Cod_Modulo') == "B4.04"){
						$html .= '									  									  
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;">
								<div id="cobtexto">- O valor de limite de crédito máximo referente da cláusula 1 deste módulo é de '.$mo.' '.$b404NivelMax.' ('.$b404NivelMaxExt.').<br></div>
								<div id="cobtexto">- A percentagem segurada para estes compradores é de '.round($mod_b404).'% (ICMS, IPI e ISS incluídos).</div></td>
							</tr>

							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;">
								<div id="cobtexto">O nome do Gerente de crédito é <strong>'.$GerenteNome.'.</strong><br></div>
                                                		<div id="cobtexto">O procedimento de gerenciamento de crédito está anexado ao presente contrato.</div>
							</tr>

							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> ';					
					}else if(odbc_result($mod,'Cod_Modulo') == "B12.02"){
						$empre = "";
						
						$query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = '".$idInform."' ORDER BY no_razao_social ";
            $cury = odbc_exec ($db, $query);
										  
						if ($cury){ 
							$html .= '								   
								<tr>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="2" width="84%">											   
										<table width="100%" border="0" style="font-size: 12pt;"> ';	   
											while (odbc_fetch_row($cury)){				
												$no_razao_social       = odbc_result ($cury, 'no_razao_social');
												$nu_cnpj               = odbc_result ($cury, 'nu_cnpj');
												$nu_inscricao_estadual = odbc_result ($cury, 'nu_inscricao_estadual');
												$ds_endereco           = odbc_result ($cury, 'ds_endereco');
												$nu_endereco           = odbc_result ($cury, 'nu_endereco');
												$ds_complemento        = odbc_result ($cury, 'ds_complemento');
												$nu_cep                = odbc_result ($cury, 'nu_cep');
												$no_cidade             = odbc_result ($cury, 'no_cidade');
												$no_estado             = odbc_result ($cury, 'no_estado');
												$i ++;
												$empre .= '<tr><td colspan="2"><b>'.$no_razao_social.'</b></td></tr>
													<tr><td><b>CNPJ   : </b>'.$nu_cnpj.'</td><td><b>IE:</b> '.$nu_inscricao_estadual.'</td></tr>
													<tr><td colspan="2"><b>Endereço: </b>'.$ds_endereco.', '.$nu_endereco. ($ds_complemento != "" ? " - ".$ds_complemento : "").'</td></tr>
													<tr><td colspan="1"><b>Cep: </b>'.$nu_cep.'</td><td><b>Cidade: </b>'.$no_cidade.' - <b>UF: </b>'.$no_estado.'</b></td></tr>';
											}
													
											$html .= $empre.'</table>
									</td>
								</tr>';	
						}
					}else if (odbc_result($mod,'Cod_Modulo') == "B26.04"){	
						$mod_b2604 = (int)$mod_b2604;							       
						$html .= '									  									  
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;">
									<div id="cobtexto">- O nível máximo de crédito referente à cláusula 1 deste módulo é de '. $mo.' '.$b2604NivelMax.' ('.$b2604NivelMaxExt.').<br></div>
									
									<div id="cobtexto"><br>
									- A percentagem segurada referente à cláusula 1.2 deste módulo é de '.round($mod_b2604).'%.</div></td>
							</tr>
										  
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> ';
					}else if (odbc_result($mod,'Cod_Modulo') == "B28.01"){
						$html .= ' 
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;">
									<div id="cobtexto">O nível máximo de crédito referente à cláusula 1 deste módulo é de  '. $mo.' '.$b2801NivelMax.' ('.$b2801NivelMaxExt.').<br/></div>
									<div id="cobtexto">As empresas de informações comerciais referentes à cláusula 1.4 deste módulo são SERASA e BOA VISTA SERVIÇOS.<br/></div>
									<div id="cobtexto">A percentagem segurada referente à cláusula 1.6 deste módulo é de '.round($b2801Perc).'% (ICMS, IPI e ISS incluídos).<br/><br/></div>
																				
									<div id="cobtexto">Inclui-se na Cláusula 1-ÁREA DE LIMITE DE CRÉDITO DISCRICIONÁRIO, deste mesmo Módulo o item 1.7, com a seguinte redação:<br></br/> </div>
												
									<div id="cobtexto">1.7 – O SEGURADO não poderá conceder crédito  a comprador que, anteriormente ao faturamento da mercadoria ou prestação de serviço, tenha sido objeto de recusa total, redução ou cancelamento de Limite de Crédito por parte da SEGURADORA, na vigência de qualquer apólice emitida pela SEGURADORA a favor do SEGURADO.”</div>
								</td>
							</tr>

							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> ';
					}else if(odbc_result($mod,'Cod_Modulo') == "D1.01"){
						$val_extnas = str_replace(")"," ", $extnas);						
						$html .= ' 
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">O limite mínimo para <b>notificação</b> de <b> ameaça de sinistro é de </b>'.$val_extnas.').</div> </td>
							</tr>';
					}else if(odbc_result($mod,'Cod_Modulo') == "D2.01"){										  
						$html .= '<tr>
								   <td  colspan="1" width="8%">&nbsp;</td>
								   <td  colspan="1" width="8%">&nbsp;</td>
								   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">O valor da franquia é de '.$mo.' '. $d201 .' ('. $valorExtD201 .')<br></div></td>
								 </tr>';
					}else if(odbc_result($mod,'Cod_Modulo') == "D4.01"){
						$val_franquia_anual = str_replace(")."," ", $franquia_anual);
						 $html .= '<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A franquia anual global é de '.$mo.' '. $val_franquia_anual.'). <br></div></td>
						  </tr>';
							
					}else if(odbc_result($mod,'Cod_Modulo') == "D6.02"){
						$nivel = strtolower($numberExtensive->extensive(number_format($nivel_d602,2, '.',''),$fMoeda));
						$d602 = "O nível de pequenos sinistros é de: ".$mo." ".number_format($nivel_d602,2,',','.')." (".$nivel." ". strtolower($ext).")";
						$html .= '
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">'.$d602.'</div></td>
							</tr>';
					}else if(odbc_result($mod,'Cod_Modulo') == "D7.01"){
						$html .= '
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">'.$d701.'</div></td>
							</tr>';	
					}else if (odbc_result($mod,'Cod_Modulo') == "F3.05"){
						if ($numParc == 1){
							$html .= ' 
								<tr>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="2" width="84%" style="text-align:justify;">
									<div id="cobtexto">O período de declaração é '.$periodo.'<br></div>
										<div id="cobtexto">A forma de declaração é volume total de negócios aberto por número 
										de fatura comercial, importador e valor.<br></div>
										<div id="cobtexto">O prêmio mínimo será pago em 01 parcela à vista.</div></td>
								</tr>';
						}else{
							$html .= '
								<tr>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="1" width="8%">&nbsp;</td>
									<td  colspan="2" width="84%" style="text-align:justify;">
									<div id="cobtexto">O período de declaração é '.$periodo.'.<br></div>
										<div id="cobtexto">A forma de declaração é volume total de negócios aberto por número 
										de fatura comercial, importador e valor.<br></div>
										<div id="cobtexto">O prêmio mínimo será pago em '.$numParc.' parcelas iguais e '.$periodo1.'</div>
									</td>
								</tr>';
						}
					}else if(odbc_result($mod,'Cod_Modulo') == "F9.02"){
						$html .= '
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">'.$bonus.'</div></td>
							</tr>';	
					}else if(odbc_result($mod,'Cod_Modulo') == "F13.02"){
						$perPart1 = (int)$perPart1;
						$perPart0 = (int)$perPart0;
						
						$html .= ' 
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A percentagem referente ao item a deste módulo é de '. round($perPart0) .'% ('.$extpart0.').<br></div>
									<div id="cobtexto">A percentagem referente ao item b deste módulo é de '. round($perPart1) .'% ('.$extpart1.').</div></td>
							</tr>  
						';
					}else if(odbc_result($mod,'Cod_Modulo') == "F14.02"){
						$html .= ' 
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A percentagem referente ao item a deste módulo é de '. round($perPart0) .'% ('.$extpart0.').<br></div>
									<div id="cobtexto">A percentagem referente ao item b deste módulo é de '. round($perPart1) .'% ('.$extpart1.').</div></td>
							</tr>  
						';
					}else if(odbc_result($mod,'Cod_Modulo') == "F15.02"){
						$html .= ' 
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A percentagem referente ao item a deste módulo é de '. round($perPart0) .'% ('.$extpart0.').<br></div>
									<div id="cobtexto">A percentagem referente ao item b deste módulo é de '. round($perPart1) .'% ('.$extpart1.').</div></td>
							</tr>  
						';
					}else if (odbc_result($mod,'Cod_Modulo') == "F33.01"){
						$html .= '
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify;">';
													           
									if($taxa_analise > 0) 
										$html .= '<div id="cobtexto">A tarifa de análise cadastral é de R$ '. $taxa_analise.' ('.$extAnalise.')<br></div>';

									if($taxa_monitoramento > 0)
										$html .= '<div id="cobtexto">A tarifa de monitoramento cadastral é de R$ '. $taxa_monitoramento.' ('.$extMonit.')</div>';

								$html .= '</td>
							</tr>';
					}

					if ($descricao_Modulo != '' || odbc_result($mod,'Desc_Modulo') != ''){
						$html .= '
							<tr>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="1" width="8%">&nbsp;</td>
								<td  colspan="2" width="84%" style="text-align:justify">
								<div id="cobtexto">'. nl2br(odbc_result($mod,'Desc_Modulo')).'</div>
								</td>
							</tr>';
					}
									
					$html .= '<tr>
						<td colspan="4">&nbsp;</td>
					</tr>';
				}
			
    //die();	
    $countNumber = 10;

	$html .= '</table>
		<br>
		<table width="100%" border="0" style="font-size: 12pt;"> ';


        if($riscopolitico != ''){			
			$html .= '
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>

			<tr>
			    <td  colspan="1" width="8%" style="font-weight:bold">'.$countNumber.'.</td>
				<td  colspan="3" width="92%" style="font-weight:bold">RISCO POLÍTICO</td>
			</tr>';

           	$html .= '
											<tr>
											    <td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
													<td  colspan="3" width="92%" style="text-align:justify;"><div id="cobtexto">10.1 - Consideram-se riscos políticos cobertos pela apólice a ocorrência dos atos ou fatos  seguintes:</div></td>
											</tr>
											<tr>
													<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											    <td colspan="3" width="92%" style="text-align:justify;">
													<div id="cobtexto"><br>a-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inadimplência do importador empresa pública.</div>
													<div id="cobtexto"><br>b-&nbsp;&nbsp;&nbsp;&nbsp;Guerra, declarada ou não, no país do importador, com exceção de guerra, declarada ou não, entre dois ou mais dos seguintes países: França, República Popular da China, Rússia, Reino Unido e Estados Unidos da América.</div>
													<div id="cobtexto"><br>c-&nbsp;&nbsp;&nbsp;&nbsp;Moratória estabelecida em caráter geral no país do importador e mais genericamente qualquer decisão ou ato normativo decretado pelo Governo do país onde está domiciliado o importador proibindo ou exonerando este último do pagamento do débito com o Segurado.</div>
													<div id="cobtexto"><br>d-&nbsp;&nbsp;&nbsp;&nbsp;Promulgação de lei (ou de regulação com força de lei) no pais de domicílio do importador interditando importação de bens ou execução de serviços.</div>
													<div id="cobtexto"><br>e-&nbsp;&nbsp;&nbsp;&nbsp;Evento de não transferência de divisas decretado pelo país do importador que impeçam o repasse do valor depositado por este último em banco oficial dentro do seu país, tendo o importador efetuado todas as formalidades requeridas para a transferência.</div>
											    </td>
										    </tr>		
											<tr>
												<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
												<td colspan="3" width="92%" style="text-align:justify;">
													<div id="cobtexto">10.2 – A cobertura de Risco Político da apólice não abrange o(s) país(es) excluído(s) no item 2.2 (b)  deste instrumento.</div>
												</td>
											</tr>		';
	   
			$countNumber++;
	   }

       /*if ($exibe_franq == 1){
		  $html .= '
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>

			<tr>
				<td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
				<td  colspan="3" width="92%" style="font-weight:bold">FRANQUIA ANUAL GLOBAL</td>
			</tr>';

           	$html .= '
			<tr><td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
				<td  colspan="3" width="92%" style="text-align:justify;">
					A franquia anual global é de '.$mo.' '. $franquia_anual.'<br>
                         		O SEGURADO será responsável e manterá por sua conta uma franquia anual global.<br>
                         		Nesta medida, não serão pagas quaisquer indenizações enquanto o montante total 
                         		das indenizações devidas, resultantes de <b>notificações</b> pelo SEGURADO, 
                         		relacionadas com <b>créditos</b> correspondentes de um determinado período de 
                         		seguro, não exceder o montante correspondente à franquia anual global.
				</td>
			</tr>	';
			$countNumber++;
	    }*/

      	if($exibe_ad == 1){
          	$html .= '
												<tr>
													<td colspan="4">&nbsp;</td>
												</tr>

												<tr>
													<td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
													<td  colspan="3" width="92%" style="font-weight:bold">ADEQUAÇÃO DE PRÊMIO</td>
												</tr>';
											            
									          	$html .= '
												<tr><td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
													<td  colspan="3" width="92%" style="text-align:justify;">
													<div id="cobtexto">
														Caso o valor das indenizações pagas durante o período de seguro superar a 
									                     			percentagem de sinistralidade de '.round($ad_sinistr).'% do <b>prêmio</b> pago correspondente 
									                     			ao mesmo período de seguro, um <b>prêmio</b> complementar será faturado.<br></div>
									                     			<div id="cobtexto">Este <b>prêmio</b> complementar será calculado retroativamente ao início da 
									                     			apólice, aplicando uma taxa de prêmio de adequação de '.round($ad_premio).'% sobre 
									                     			a taxa de prêmio mencionada na proposta, multiplicada pelo faturamento 
									                     			segurável realizado durante todo o período de seguro.<br></div>
									                     			<div id="cobtexto">A aplicação da taxa 
									                    			retroativamente não impedirá uma eventual revisão da taxa para período de 
									                   			seguro seguinte.
									        </div>
													</td>
												</tr>';
												$countNumber++;
      	}


     	// Condição Especial de Cobertura de Coligadas 
     	if(($temempcol == 1) || ($condespcol != "")){
          	$html .= '
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>

			<tr>
				<td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
				<td  colspan="3" width="92%" style="font-weight:bold"><div id="cobtexto">EXTENSÃO DE COBERTURA PARA OPERAÇÕES REALIZADAS ATRAVÉS DE COLIGADAS NO EXTERIOR</div></td>
			</tr> ';

          	$html .= '
			<tr><td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
				<td  colspan="3" width="92%" style="text-align:justify;"><div id="cobtexto">'.$condespcol.'</div>
				</td>
			</tr>';
          $countNumber++;

     	}

      	// exbibe as condições especiais
      	if($exibe_cond == 1){
          	$html .= '
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>

			<tr>
				<td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
				<td  colspan="3" width="92%" style="font-weight:bold">CONDIÇÕES COMPLEMENTARES</td>
			</tr> ';
		           
          	$html .= '
			<tr><td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
				<td  colspan="3" width="92%" style="text-align:justify;">
					<div id="cobtexto">
						'.$condicoes_especiais.'
					</div>
				</td>
			</tr>';
			$countNumber++;
      	}
	  
      $html .= '</table>';

    $html.= '<!--mpdf '.$mpdf->AddPage().'<pagebreak /> mpdf-->';
	  
	  $html .= '<div id="cobtitulo"><h3>CONDIÇÕES PARA ACEITAÇÃO</h3></div>
                   <div id="cobtexto">Fazem parte desta proposta as CONDIÇÕES GERAIS e as CONDIÇÕES ESPECIAIS (módulos), devidamente aprovadas conforme processo 
                        SUSEP n° '. $dados['c_SUSEP'] .' e plenamente reconhecidas pelo SEGURADO, assim como o QUESTIONÁRIO apresentado pelo mesmo.<br><br></div>
                        <div id="cobtexto">Depois de devidamente protocolada a proposta assinada pelo SEGURADO, a '.$nomeEmpSBCE.' terá 
                        o prazo de 15 (quinze) dias para se manifestar a respeito da aceitação do seguro.  Caso não haja nenhuma manifestação, neste 
                        prazo, por parte da SEGURADORA, o seguro estará automaticamente aceito.  Em caso de não aceitação da proposta, a SEGURADORA enviará
                        uma notificação ao SEGURADO no prazo máximo de 15 (quinze) dias e devolverá quaisquer valores de prêmio eventualmente pagos.</div></li>
						
	     <br><br>';
     
      $html .= '<br>
						      <table width="100%" border="0" style="font-size: 12pt;">
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%"><div>________________________,______de_____________________de_________</div></td>
										</tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%"><div>______________________________________</div></td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%" style="font-weight:bold">'.$name.'</td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%"><div>Nome:</div></td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%"><div>RG:</div></td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%"><div>CPF:</div></td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%"><div>Cargo:</div></td>
										</tr>
									</table>
								<br>';

      $html .= '<table width="100%" border="0">
	              	<tr>
							    	<td width="35%">&nbsp;</td>
							    	<td align="right" width="65%">
	                  	<div align="right" id="disclame">'.$disclame_retorno.' </div>
	                  </td>
	                </tr>
			    			</table>';

      $html .= '
					</body>
    		</html>';  
		
		}else if($i_Produto == 1){


			$html = '					<div id="sublinhado">DADOS DO PROPONENTE:</div><br>
					<table width="100%" border="0" style="font-size: 12pt;">					
					<tr>
					   <td width="25%"><div id="cobtexto">Razão Social: </div></td><td width="75%"><span style="font-weight: bold; font-size: 12pt;"><div id="cobtexto">'.strtoupper(trim($name)).'</div></span></td>
					</tr>
					<tr>
					  <td width="25%"><div id="cobtexto">Endereço: </div></td><td width="75%"><div id="cobtexto">'.$endcompleto.'</div></td>
					</tr> 
					<tr>
					  <td width="25%">&nbsp;</td><td width="75%"><div id="cobtexto">'.$city .' - '. $uf.'</div></td>
					</tr>
					<tr>  
					  <td width="25%">&nbsp;</td><td width="75%"><div id="cobtexto">CEP '.$cep.'</div></td>
					</tr>
					<tr>
					  <td width="25%">CNPJ: </td><td width="75%"><div id="cobtexto">'.arruma_cnpj($cnpj).'</div></td>			
					</tr>
					<tr>
					  <td width="25%">Corretor nomeado: </td><td width="75%"><div id="cobtexto">'.($MultiCorretor != ''? $MultiCorretor: $corretor).'</div></td>			
					</tr>
	                </table> 
					
					<br>
					<div style="border-top: 1px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>
                    <br>
					
					
					 <div id="sublinhado">COBERTURA:</div>					
					 
					 <table width="100%" border="0" style="font-size: 12pt;">
					    <tr>
					       <td colspan="1" width="8%" style="font-weight:bold">1.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">ABRANGÊNCIA DESTE CONTRATO – PERCENTAGEM SEGURADA</td>			
					    </tr>
						<tr>
					       <td colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
						   <td colspan="2" width="92%" style="font-weight:bold"> - CUSTO DA COBERTURA</td>			
					    </tr>
						<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="1" width="8%" style="font-weight:bold">1.1</td>
						   <td  colspan="1" width="84%" style="font-weight:bold">NATUREZA DAS VENDAS SEGURADAS</td>			
					    </tr>

					    <tr>
					       <td colspan="1" width="8%">&nbsp;</td>
						   <td colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'.$products.'.</div></td>			
					    </tr>

					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>

					    <tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="1" width="8%" style="font-weight:bold">1.2</td>
						   <td  colspan="1" width="84%" style="font-weight:bold">PRÊMIO MÍNIMO</td>			
					    </tr>

					    <tr>
					       <td colspan="1" width="8%">&nbsp;</td>
						   <td colspan="2" width="92%" style="text-align:justify;">';

							$sqlvig1 = "select count(*) as quantidade from Periodo_Vigencia where i_Inform = ".$idInform;
							$rres1 = odbc_exec($db, $sqlvig1);
							$qtdeVig  = odbc_result($rres1, "quantidade");

							
							$num  = $qtdeVig;
						
							if($num > 1){
								$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
								$rres = odbc_exec($db, $sqlvig);

								while(odbc_fetch_row($rres)){
									$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
									$html .= '<div id="cobtexto">'.$mo.' '. number_format(odbc_result($rres,'v_Premio'), 2, ",", ".").' ('.$valorext.') pelo período compreendido entre '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .' - IOF de 7,38% não incluído.<br><br></div>';
								}
								$html .= 'Prêmio Mínimo Total por todo período de seguro:<br>';
								$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).') - IOF de 7,38% não incluído.<br></div>';
							} else{
								$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).') por período de seguro (IOF de 7,38% não incluso).<br></div>';
							}

						   $html .= '</td>			
					    </tr>

					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>						

					    <tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="1" width="8%" style="font-weight:bold">1.3</td>
						   <td  colspan="1" width="84%" style="font-weight:bold">PERCENTAGEM SEGURADA</td>			
					    </tr>

					    <tr>
					       <td colspan="1" width="8%">&nbsp;</td>
						   <td colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'.round($percCoverage).'% (ICMS, IPI, ISS e demais tributos incluídos no valor total da nota fiscal de venda ou serviço prestado).</div></td>			
					    </tr>

					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>
						
					    <tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="1" width="8%" style="font-weight:bold">1.4</td>
						   <td  colspan="1" width="84%" style="font-weight:bold">TAXA DE PRÊMIO</td>			
					    </tr>

					    <tr>
					       <td colspan="1" width="8%">&nbsp;</td>
						   <td colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'.number_format($tx,4,",",".").'%  aplicável ao faturamento (ICMS, IPI, ISS e demais tributos incluídos no valor total da nota fiscal de venda ou serviço prestado).</div></td>			
					    </tr>

					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>

					    <tr>
					       <td colspan="1" width="8%" style="font-weight:bold">2.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">LIMITE MÁXIMO DE INDENIZAÇÃO</td>			
					    </tr>';

					    if ($limPagIndeniz != 0) {
					    	$html .= '
						    <tr>
						      <td  colspan="1" width="8%">&nbsp;</td>
							   	<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'.$limPagIndeniz .' vezes o prêmio pago por período de seguro.</div></td>
						    </tr>';
					    }

					    if ($ValorLMI != 0) {
					    	$ValorLMIExt = $numberExtensive->extensive(number_format($ValorLMI,0,'.',''),$currency);
					    	$html .= '
						    <tr>
						      <td  colspan="1" width="8%">&nbsp;</td>
							   	<td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'.$mo.' '. number_format($ValorLMI, 2, ",", ".").' ('.$ValorLMIExt.').</div></td>
						    </tr>';
					    }
					    	
					    $html .= '
					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>

					    <tr>
					       <td colspan="1" width="8%" style="font-weight:bold">3.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">PRAZO MÁXIMO DE CRÉDITO</td>			
					    </tr>

					    <tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'.$periodoMaxCredito.' dias contados a partir da data da emissão da fatura da mercadoria vendida ou serviço prestado. </div></td>
					    </tr>

					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>

					    <tr>
					    <td colspan="1" width="8%" style="font-weight:bold">4.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">PRAZO MÁXIMO PARA EMISSÃO DA NOTA FISCAL</td>			
					    </tr>
						<tr>
					      <td  colspan="1" width="8%">&nbsp;</td>
						   	<td  colspan="2" width="92%" style="text-align:justify;">
						      <div id="cobtexto">Não é aplicável para as vendas de mercadorias.<br></div>';
						             

								if($PrazoMaxEmiNota > 0){
									$html .= '<div id="cobtexto">Prestação de serviços: '.$PrazoMaxEmiNota.' dias, contado a partir da data da prestação do serviço para qual o pagamento seja devido. (somente quando tem serviços contratados)</div>';
								}					             
						        
						   $html .= '</td>
						   			
					    </tr>	
					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>
						
					    <tr>
					       <td colspan="1" width="8%" style="font-weight:bold">5.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">PRAZO PARA DECLARAR A AMEAÇA DE SINISTRO</td>			
					    </tr>

					    <tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">'. ($periodoMaxCredito + 30).' dias contados a partir da data da emissão da fatura da mercadoria vendida ou serviço prestado.<br> </div>
							<div id="cobtexto">No caso de prorrogação da data de vencimento, dentro das condições constantes no item 2.2.3 da cláusula 2 das CONDIÇÕES GERAIS, o prazo é de 30 dias contados do novo vencimento.</div>
						   </td>
					    </tr>
						
					    <tr>
						 <td colspan="3">&nbsp;</td>
					    </tr>

					    <tr>
					       <td colspan="1" width="8%" style="font-weight:bold">6.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">MOEDA DA APÓLICE</td>			
					    </tr>						

					    <tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="2" width="92%" style="text-align:justify;"><div id="cobtexto">A moeda da apólice é o '. $extM.' ('.$mo.').</div></td>
						   			
					    </tr>';
						
					    $countNumber = 7; 
						
						/*if($VigIni){
							$html .= '<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
							
								<tr>
									<td colspan="1" width="8%" style="font-weight:bold">'.($countNumber++).'.</td>
									<td colspan="2" width="92%" style="font-weight:bold">VIGÊNCIA DA APÓLICE</td>			
								</tr>

								<tr>
							   		<td  colspan="1" width="8%">&nbsp;</td>
							   		<td  colspan="2" width="92%">A apólice entra em vigor no dia '.$VigIni.' e terá validade até o dia '.$VigFim.', cujo período equivale ao período de seguro.</td>
								</tr>';
						}*/
						
						if($VigIni){
							$html .= '<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
							
								<tr>
									<td colspan="1" width="8%" style="font-weight:bold">'.($countNumber++).'.</td>
									<td colspan="2" width="92%" style="font-weight:bold">VIGÊNCIA DA APÓLICE</td>			
								</tr>';

							  	// verificações 
					          	// INTERAKTIV 18/06/2014
								$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
								$rres = odbc_exec($db, $sqlvig);
								$num  = 1;
								$indx = 0;
								while(odbc_fetch_row($rres)){
									$indx++;
								}

								if($num){
									$ssqx = "select MIN(d_Vigencia_Inicial)as Ini, MAX(d_Vigencia_Final) as Fim from Periodo_Vigencia where i_Inform =".$idInform. "";
									$rresx = odbc_exec($db, $ssqx);
									$html .= '<tr>
												<td colspan="1" width="8%">&nbsp;</td>
												<td colspan="2" width="92%" style="text-align:justify;">
												<div id="cobtexto">
												A apólice entra em vigor no dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Ini'),0,10)).' e terá validade até o dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Fim'),0,10)).', cujo período equivale ao período de seguro.
												<br><br></div>';
									
									//$html .= '<div id="cobtexto">'.$mo.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($extPremioMinimo).')<br></div>';

									
									$indx1 = 0;
									
									if ($indx > 1) {
										$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
										$rres = odbc_exec($db, $sqlvig);
																				
										while(odbc_fetch_row($rres)){
											if ($indx1 == 0) {
												$html .= '<div id="cobtexto">Este período de seguro é dividido em '.trim(strtolower(valor_extenso($indx))).' períodos distintos de vigência compreendidos entre:<br></div>';
											}

											$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
											$html .= '<div id="cobtexto">Período '.odbc_result($rres,'n_Preriodo').' – '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'<br></div>';
											$indx1++;
										}
										
										if ($indx1 > 0) {
											$html .= '<br>';
										}
									}

									if($renovacao_Tacica == 1){ // campo no inform
								    	$html .= '	 
											<div id="cobtexto">
											A apólice poderá ser renovada automaticamente, por igual período de seguro, caso não seja comunicada a intenção de não renovação por qualquer das partes, devendo tal comunicação de não renovação ser feita por escrito pelo SEGURADO ou pela SEGURADORA com antecedência de '.$Prazo_Nao_Intencao_Renov.' dias do termo final do período de seguro total. </div>
											</td>			
											</tr>
										';
									}else{
										$html .= '	 
											<div id="cobtexto">
											Revoga-se o item 12.2 das Condições Gerais da apólice o qual passará a vigorar com a seguinte redação:</div>
											<div id="cobtexto">
											"A apólice tem duração definida nas CONDIÇÕES PARTICULARES e não pode ser renovada tacitamente."</div>
											</td>			
									   		</tr>
								     	';
									
									}
								}else{				   
							   
									$html .= '<tr>
								       	<td  colspan="1" width="8%">&nbsp;</td>
										<td  colspan="2" width="92%" style="text-align:justify;">
										<div id="cobtexto">
										A apólice entra em vigor no dia '.$VigIni.' e terá validade até o dia '.$VigFim.', cujo período equivale ao período de seguro.<br></div>';
								
										if($renovacao_Tacica ==1){ // campo no inform
										    $html .= '	 
												<div id="cobtexto">
												A apólice poderá ser renovada automaticamente, por igual período de seguro, caso não seja comunicada a intenção de não renovação por qualquer das partes, devendo tal comunicação de não renovação ser feita por escrito pelo SEGURADO ou pela SEGURADORA com antecedência de '.$Prazo_Nao_Intencao_Renov.' dias do termo final do período de seguro total. </div>
												</td>			
												</tr>
										';
										}else{
											 $html .= '	 
												<div id="cobtexto">
												Revoga-se o item 12.2 das Condições Gerais da apólice o qual passará a vigorar com a seguinte redação:</div>
												<div id="cobtexto">
												"A apólice tem duração definida nas CONDIÇÕES PARTICULARES e não pode ser renovada tacitamente."</div>
												</td>			
											   	</tr>
										     ';
											
										}
								
								}
						}
						
						$html .= '<tr>
								<td colspan="3">&nbsp;</td>
							</tr>

							<tr>
					       			<td colspan="1" width="8%" style="font-weight:bold">'.($countNumber++).'.</td>
						   		<td colspan="2" width="92%" style="font-weight:bold">MÓDULOS</td>			
					    		</tr>

							<tr>
					       			<td  colspan="1" width="8%">&nbsp;</td>
						   		<td  colspan="2" width="92%"><div id="cobtexto">Os seguintes módulos fazem parte integrante deste contrato:</div></td>						   			
					    		</tr>

							</table>
						
							<table width="100%" border="0" style="font-size: 12pt;"> ';
						
						
						//Novo formato de módulos
						    						//Início da exibição dos módulos
						
						$qryM = "select a.i_Modulo, b.Cod_Modulo,b.Grupo_Modulo,b.Titulo_Modulo,
								cast(a.Desc_Modulo as nvarchar(3900)) as Desc_Modulo
								from Inform_Modulo a inner join Modulo b on a.i_Modulo = b.i_Modulo 
								where a.idInform = ". $idInform. " order by b.Ordem_Modulo";	
								
						$mod = odbc_exec($db,$qryM);
						
						$GrupoModulo = "";
						
						while(odbc_fetch_row($mod)){ 
						
						           $descricao_Modulo = odbc_result($mod,'Desc_Modulo');
								   
						           if(odbc_result($mod,'Grupo_Modulo') != $GrupoModulo){
									   $GrupoModulo  = odbc_result($mod,'Grupo_Modulo');
									   $html .= '<tr>
												   <td colspan="4">&nbsp;</td>
											   </tr>
											   <tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="3" width="92%" style="font-weight:bold"><div id="sublinhado">'.odbc_result($mod,'Grupo_Modulo').'</div></td>
											   </tr>';
								   }
								   
								    $Titulo = strlen(odbc_result($mod,'Titulo_Modulo'));
						            $html .= '<tr>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="1" width="8%" style="font-weight:bold"><div id="cobtexto">'.odbc_result($mod,'Cod_Modulo').'</div></td>
										   <td  colspan="1" width="76%" style="font-weight:bold"><div id="cobtexto">'.odbc_result($mod,'Titulo_Modulo').'</div></td>						   			
							                </tr>';
											/*
											if($Titulo > 47){
											   '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td colspan="1" width="8%">&nbsp;</td>
												   <td colspan="1" width="76%" style="font-weight:bold">'.substr(odbc_result($mod,'Titulo_Modulo'),47,$Titulo).'</td>
												</tr>';
											}
											*/
										
								
											   
									if(odbc_result($mod,'Cod_Modulo') == "B4.04"){
										 $html .= '									  									  
										  <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">
											   <div id="cobtexto">
											   - O valor de limite de crédito máximo referente da cláusula 1 deste módulo é de R$ '.$b404NivelMax.' ('.$b404NivelMaxExt.').<br></div>
											   <div id="cobtexto">
											   - A percentagem segurada para estes compradores é de '.round($mod_b404).'% (ICMS, IPI e ISS incluídos).</div></td>
										  </tr>
										  <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">
											   <div id="cobtexto">
										        O nome do Gerente de crédito é <strong>'.$GerenteNome.'.</strong><br></div>
                                                <div id="cobtexto">O procedimento de gerenciamento de crédito está anexado ao presente contrato.</div>
										  </tr>
										  
										  <tr>
										    <td colspan="4">&nbsp;</td>
									      </tr> ';
										
									}else if (odbc_result($mod,'Cod_Modulo') == "B8.02"){
											$emp = "";
									    										 
										 $query = "SELECT * FROM ParModEsp_Maiores_Compradores WHERE idInform = '".$idInform."' ORDER BY Nome ";
                                         $cury = odbc_exec ($db, $query);
                                    
									 
												if ($cury){ 
												   $html .= '								   
														  <tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%">											   
														   <table>';
														   
															while (odbc_fetch_row($cury)){				
																$NomeE                = odbc_result ($cury, 'Nome');
																$CNPJE                = odbc_result ($cury, 'CNPJ');
																$i ++;
																$emp .= '<tr>
																           <td colspan="2"><b>Nome Empresa: </b>'.$NomeE.'</td>
																		 </tr>
																		 <tr>
																		   <td colspan="2"><b>CNPJ: </b>'.$CNPJE.'</td>																		
																		</tr>';
																
															}
															
															  $html .= $emp.'</table>
														     </td>
													      </tr>';
												 }
									  
									  
									  
								      $html .=' <tr>
										 <td colspan="4">&nbsp;</td>
									   </tr>';	
										
									
									}else if(odbc_result($mod,'Cod_Modulo') == "B9.04"){
										 $html .= '  <tr>
											     <td  colspan="1" width="8%">&nbsp;</td>
											     <td  colspan="1" width="8%">&nbsp;</td>
											      <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A franquia de maiores compradores mencionada no item 1.1 deste módulo é de R$ '.$mod_b904.' ('.$mod_b904Ext.').</div></td>
												  </tr>
												  <tr>
													 <td colspan="4">&nbsp;</td>
												   </tr>';
										
									}else if(odbc_result($mod,'Cod_Modulo') == "B12.02"){
											$empre = "";
											
									    $query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = '".$idInform."' ORDER BY no_razao_social ";
                                              $cury = odbc_exec ($db, $query);
										  if ($cury){ 
										      $html .= '								   
												  <tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%">											   
												   <table>';
												   
													while (odbc_fetch_row($cury)){				
														$no_razao_social       = odbc_result ($cury, 'no_razao_social');
														$nu_cnpj               = odbc_result ($cury, 'nu_cnpj');
														$nu_inscricao_estadual = odbc_result ($cury, 'nu_inscricao_estadual');
														$ds_endereco           = odbc_result ($cury, 'ds_endereco');
														$nu_endereco           = odbc_result ($cury, 'nu_endereco');
														$ds_complemento        = odbc_result ($cury, 'ds_complemento');
														$nu_cep                = odbc_result ($cury, 'nu_cep');
														$no_cidade             = odbc_result ($cury, 'no_cidade');
														$no_estado             = odbc_result ($cury, 'no_estado');
														$i ++;
														$empre .= '<tr><td colspan="2"><b>'.$no_razao_social.'</b></td></tr>
																   <tr><td><b>CNPJ   : </b>'.$nu_cnpj.'</td><td><b>IE:</b> '.$nu_inscricao_estadual.'</td></tr>
																   <tr><td colspan="2"><b>Endereço: </b>'.$ds_endereco.', '.$nu_endereco. ($ds_complemento != "" ? " - ".$ds_complemento : "").'</td></tr>
																   <tr><td colspan="1"><b>Cep: </b>'.$nu_cep.'</td><td><b>Cidade: </b>'.$no_cidade.' - <b>UF: </b>'.$no_estado.'</b></td></tr>';
														
											        }
													
													  $html .= $empre.'</table>
												   </td>
											   </tr>';	
										
										  }
									
									
									}else if (odbc_result($mod,'Cod_Modulo') == "B26.04"){								       
							            $html .= '									  									  
										  <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">
											   <div id="cobtexto">
											   - O nível máximo de crédito referente à cláusula 1 deste módulo é de R$ '.$b2604NivelMax.' ('.$b2604NivelMaxExt.').
											   <br></div>
											   <div id="cobtexto">
											   <br>
											   - A percentagem segurada referente à cláusula 1.2 deste módulo é de '.round($mod_b2604).'%.</div></td>
										  </tr>
										  
										  <tr>
										    <td colspan="4">&nbsp;</td>
									      </tr> ';
							
						            }else if (odbc_result($mod,'Cod_Modulo') == "B28.01"){
						    
							             $html .= ' <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;">
											   <div id="cobtexto">
											     O nível máximo de crédito referente à cláusula 1 deste módulo é de  R$ '.$b2801NivelMax.' ('.$b2801NivelMaxExt.').<br/></div>
											   <div id="cobtexto">
												As empresas de informações comerciais referentes à cláusula 1.4 deste módulo são SERASA e BOA VISTA SERVIÇOS.<br/></div>
												<div id="cobtexto">
												A percentagem segurada referente à cláusula 1.6 deste módulo é de '.round($b2801Perc).'% (ICMS, IPI e ISS incluídos).<br/><br/></div>
												<div id="cobtexto">
												Inclui-se na Cláusula 1-ÁREA DE LIMITE DE CRÉDITO DISCRICIONÁRIO, deste mesmo Módulo o item 1.7, com a seguinte redação:<br></br/> </div>
												<div id="cobtexto">
												1.7 – O SEGURADO não poderá conceder crédito  a comprador que, anteriormente ao faturamento da mercadoria ou prestação de serviço, tenha sido objeto de recusa total, redução ou cancelamento de Limite de Crédito por parte da SEGURADORA, na vigência de qualquer apólice emitida pela SEGURADORA a favor do SEGURADO.”   </div>

											   </td>
										 </tr>
										 <tr>
										 <td colspan="4">&nbsp;</td>
									     </tr> ';
												
									}else if(odbc_result($mod,'Cod_Modulo') == "D1.01"){
										    
											 $html .= ' <tr>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">O limite mínimo para notificação de ameaça de sinistro é de '.$extnas.'.</div></td>
											 </tr>';
									
									    
									
									}else if(odbc_result($mod,'Cod_Modulo') == "D2.01"){	
									  
										$html .= '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">O valor da franquia é de '.$mo.' '. $d201 .' ('. $valorExtD201 .').<br></div></td>
												 </tr>';
									
									}else if(odbc_result($mod,'Cod_Modulo') == "D4.01"){
										 $html .= '<tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A franquia anual global é de '.$mo.' '. $franquia_anual.'<br></div></td>
									      </tr>';
										
									}else if(odbc_result($mod,'Cod_Modulo') == "D6.02"){
										 $html .= '<tr>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">'.$d602.'</div></td>
										    </tr>';
											
									}else if(odbc_result($mod,'Cod_Modulo') == "D7.01"){
									    $html .= '<tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">'.$d701.'</div></td>
									     </tr>';	
										 
									}else if (odbc_result($mod,'Cod_Modulo') == "F3.05"){
									        if ($numParc == 1){
												 $html .= ' <tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%" style="text-align:justify;">
															  <div id="cobtexto">O período de declaração é '.$periodo.'.<br></div>
															  <div id="cobtexto">
																A forma de declaração é em arquivo Excel contendo o volume total de negócios aberto por comprador.<br/></div>
																<div id="cobtexto">
																O prêmio mínimo será pago em 01 parcela à vista.</div></td>
																
														   </tr>';
															   
												
											  }else{
												 $html .= '<tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%" style="text-align:justify;">
														   <div id="cobtexto">O período de declaração é '.$periodo.'.<br></div>
														   <div id="cobtexto">
																A forma de declaração é em arquivo Excel contendo o volume total de negócios aberto por comprador.<br/> </div>
																<div id="cobtexto">
																O prêmio mínimo será pago em '.$numParc.' parcelas iguais e '.$periodo1.'</div>
														   </td>
														   </tr>';
								
											  }
									
									}else if (odbc_result($mod,'Cod_Modulo') == "F4.01"){
											$sql_f401 = "Select
																			IsNull(IMVI.v_1, 0) As Perc_Sinistralidade_Inicial,
																			IMV.v_1 As Perc_Sinistralidade_Final,
																			IMV.v_2 As Perc_Adequacao
																		From
																			Inform_Modulo_Valores IMV
																		Left Join Inform_Modulo_Valores IMVI On
																			IMVI.i_Modulo_Valor = (Select Top 1 IMVV.i_Modulo_Valor From Inform_Modulo_Valores IMVV
																				Where IMVV.i_Inform = IMV.i_Inform And IMVV.i_Modulo = IMV.i_Modulo And IMVV.v_1 < IMV.v_1
																				Order By IMVV.v_1 Desc)
																		Where
																			IMV.i_Inform = ".$idInform."
																			And IMV.i_Modulo = 24	-- Módulo F4.01
																		Order By
																			IMV.v_1
																		";
											$ex_sql_f401 = odbc_exec($db, $sql_f401);

											$count_f401 = 0;
											$txt_sinist_f401 = array();
											$txt_percent_f401 = array();
											while (odbc_fetch_row($ex_sql_f401)){
												$sinist_inicial_f401 = odbc_result($ex_sql_f401, 'Perc_Sinistralidade_Inicial');
												$sinist_final_f401 = odbc_result($ex_sql_f401, 'Perc_Sinistralidade_Final');
												$perc_adequacao_f401 = odbc_result($ex_sql_f401, 'Perc_Adequacao');

												if ($sinist_inicial_f401 != 0 && $sinist_final_f401 != 0) {
													$txt_sinist_f401[] = 'Se nível de sinistralidade for maior ou igual a '.round($sinist_inicial_f401).'% e menor que '.round($sinist_final_f401).'%';
													$txt_percent_f401[] = 'A percentagem de prêmio é '.round($perc_adequacao_f401).'%.';
												} elseif($sinist_final_f401 != 0){
													$txt_sinist_f401[] = 'Se nível de sinistralidade for menor que '.round($sinist_final_f401).'%';
													$txt_percent_f401[] = 'A percentagem de prêmio é '.round($perc_adequacao_f401).'%.';
												}

												$count_f401++;
											}

											for ($i=0;$i<$count_f401;$i++) { 
											  $html .= '<tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">'.$txt_sinist_f401[$i].'<br/></div>
																								<div id="cobtexto">'.$txt_percent_f401[$i].'</div></td>
																 </tr>';
												if($count_f401 != ($i+1)){
													$html .= '<br>';
												}
											}

									}else if(odbc_result($mod,'Cod_Modulo') == "F9.02"){
									      $html .= '<tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">'.$bonus.'</div></td>
											 </tr>';	
									}else if (odbc_result($mod,'Cod_Modulo') == "F13.02"){
									    $html .= '<tr>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A percentagem referente ao item a deste módulo é de '.round($partDeducao).'%.<br></div>
															   <div id="cobtexto">A percentagem referente ao item b deste módulo é de '.round($partLucro).'%.</div></td>
															 </tr>  
												 ';

									}else if (odbc_result($mod,'Cod_Modulo') == "F14.02"){
									    $html .= '<tr>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A percentagem referente ao item a deste módulo é de '.round($partDeducao).'%.<br></div>
																	<div id="cobtexto">A percentagem referente ao item b deste módulo é de '.round($partLucro).'%.</div></td>
															 </tr>  
												 ';

									}else if (odbc_result($mod,'Cod_Modulo') == "F15.02"){
									    $html .= '<tr>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A percentagem referente ao item a deste módulo é de '.round($partDeducao).'%.</div>
																	  <div id="cobtexto">A percentagem referente ao item b deste módulo é de '.round($partLucro).'%.</div></td>
															 </tr>  
												 ';
												
									}else if (odbc_result($mod,'Cod_Modulo') == "F33.01"){
										  $html .= '
										    		 <tr>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A tarifa de análise cadastral é de '.$mo.' '. $taxa_analise.' ('.$extAnalise.')<br></div>
															  <div id="cobtexto">A tarifa de monitoramento cadastral é de '.$mo.' '. $taxa_monitoramento.' ('.$extMonit.')</div>
														</td>
													</tr>';
											  
									}else if (odbc_result($mod,'Cod_Modulo') == "F37.02"){						  
							  
										  $html .= '										 
											 <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">A forma de notificação é volume total de negócios aberto por nota fiscal.<br/>O período de declaração é mensal.</div></td>
											 </tr>';
									
									}else if (odbc_result($mod,'Cod_Modulo') == "F52.02"){
										$sql_f5202 = "Select
																			IMV.v_1 As Perc_Sinistralidade_Inicial,
																			IsNull(IMVF.v_1, 0) As Perc_Sinistralidade_Final,
																			IMV.v_2 As Perc_Adequacao
																		From
																			Inform_Modulo_Valores IMV
																		Left Join Inform_Modulo_Valores IMVF On
																			IMVF.i_Modulo_Valor = (Select Top 1 IMVV.i_Modulo_Valor From Inform_Modulo_Valores IMVV
																				Where IMVV.i_Inform = IMV.i_Inform And IMVV.i_Modulo = IMV.i_Modulo And IMVV.v_1 > IMV.v_1
																				Order By IMVV.v_1 Asc)
																		Where
																			IMV.i_Inform = ".$idInform."
																			And IMV.i_Modulo = 34	-- Módulo F52.02
																		Order By
																			IMV.v_1 
																		";
										$ex_sql_f5202 = odbc_exec($db, $sql_f5202);
										
										while (odbc_fetch_row($ex_sql_f5202)){
											$sinist_inicial_f5202 = odbc_result($ex_sql_f5202, 'Perc_Sinistralidade_Inicial');
											$sinist_final_f5202 = odbc_result($ex_sql_f5202, 'Perc_Sinistralidade_Final');
											$perc_adequacao_f5202 = odbc_result($ex_sql_f5202, 'Perc_Adequacao');
											$txt_sinist_f5202 = '';
											$txt_percent_f5202 = '';

											if ($sinist_inicial_f5202 != 0 && $sinist_final_f5202 != 0) {
												$txt_sinist_f5202 = 'Se o percentual de sinistralidade for maior que '.round($sinist_inicial_f5202).'% e menor que '.round($sinist_final_f5202).'%';
												$txt_percent_f5202 = 'Taxa de adequação de prêmio '.round($perc_adequacao_f5202).'%';
											} elseif($sinist_inicial_f5202 != 0){
												$txt_sinist_f5202 = 'Se o percentual de sinistralidade for maior ou igual que '.round($sinist_inicial_f5202).'%';
												$txt_percent_f5202 = 'Taxa de adequação de prêmio de '.round($perc_adequacao_f5202).'%';
											}

										  $html .= '<tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">'.$txt_sinist_f5202.'<br/></div>
																								<div id="cobtexto">'.$txt_percent_f5202.'</div></td>
																 </tr><br>';
										}

										$html .= '<tr>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="2" width="84%" style="text-align:justify;"><div id="cobtexto">
															   	Fica estabelecido que o cálculo da adequação de prêmio nos termos acima mencionados será realizado e cobrado a cada 12 meses de  vigência da Apólice.
															   </td>
															 </tr>';															 
									}
									
									
									
									
									if (odbc_result($mod,'Desc_Modulo') != ''){
										$html .= '
										     <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%" style="text-align:justify">
											   <div id="cobtexto">'. nl2br($descricao_Modulo).'</div>
											   </td>
											</tr>';
										
									}
									
									$html .= '<tr>
										        <td colspan="4">&nbsp;</td>
									         </tr>';
						
						} // Fim do módulos
						
						
       
    						
       
      // $html .= '</table>
	  
	     //      <br>
	     //      <table width="100%" border="0" style="font-size: 12pt;">
				  // <tr>
					 // <td  colspan="1" width="8%" style="font-weight:bold">'.($countNumber++).'.</td>
					 // <td  colspan="1" width="92%" style="font-weight:bold">OBRIGAÇÃO DE NOTIFICAÇÃO DE ATRASOS DE PAGAMENTO<td>
				  // </tr>';
               
     //  $html .= '
	    //       <tr>
				 //  <td  colspan="2" width="100%" style="text-align:justify; font-size: 12pt;">
					// Sem prejuízo das demais obrigação do contrato de seguro de crédito à exportação, o  SEGURADO compromete-se a notificar a SEGURADORA,
					// de quaisquer  dívidas vencidas  e não pagas há  mais de 60 dias da data original de vencimento. Esta obrigação não se  aplica se, para aquela
					// dívida, uma <b>notificação</b> de <b>ameaça de sinistro</b> já tiver sido feita ou para dívidas que já reúnam condições de uma <b>notificação</b> de <b>ameaça de
					// sinistro</b> de  acordo com os termos do contrato de seguro.<br><br>

					// O SEGURADO deverá enviar à SEGURADORA o total de <b>notificações</b> de atraso de que  trata esta cláusula , mensalmente, de forma unificada até
					// o 5º dia útil do mês subseqüente  ao da apuração.<br><br>
         
					// Caso o SEGURADO deixe de cumprir com esta obrigação, aplicar-se-á  a cláusula 9.4 das CONDIÇÕES GERAIS tanto para as dívidas objeto de
					// notificação de atraso de pagamento como para as dívidas correspondentes a entregas ou prestações de serviços realizados após a devida data
					// de obrigação de notificação de atraso de pagamento.
				 //  </td>
			  //  </tr>';
			  $html .= '</table>';

      	if($exibe_ad == 1){
      		$ad_sinistr = (int)$ad_sinistr;
					$ad_premio = (int)$ad_premio;
          	$html .= '
	    					      <table width="100%" border="0" style="font-size: 12pt;">
												<tr>
													<td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
													<td  colspan="3" width="92%" style="font-weight:bold">ADEQUAÇÃO DE PRÊMIO</td>
												</tr>';
											            
									          	$html .= '
												<tr><td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
													<td  colspan="3" width="92%" style="text-align:justify;">
														Caso o valor das indenizações pagas durante o período de seguro superar a percentagem de sinistralidade de '.round($ad_sinistr).'% do prêmio emitido 
														correspondente ao mesmo período de seguro, um prêmio complementar será faturado.<br><br>		          
									          Este prêmio complementar será calculado retroativamente ao início da apólice, aplicando o percentual de '.round($ad_premio).'% ao prêmio emitido durante o 
									          período de seguro (excluído IOF - 7,38%).<br><br>
									          O faturamento e cobrança deste prêmio complementar não impedirão uma eventual revisão da taxa para período de seguro seguinte.<br>
													</td>
												</tr>';
												$countNumber++;
      	}

      	// exbibe as condições especiais
      	if($exibe_cond == 1){
          	$html .= '
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>

			<tr>
				<td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
				<td  colspan="3" width="92%" style="font-weight:bold">CONDIÇÕES COMPLEMENTARES</td>
			</tr> ';
		           
          	$html .= '
			<tr><td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
				<td  colspan="3" width="92%" style="text-align:justify;">
					<div id="cobtexto">
						'.$condicoes_especiais.'
					</div>
				</td>
			</tr>';
			$countNumber++;
      	}
			  $html .= '</table>';

	  

      $html.= '<!--mpdf '.$mpdf->AddPage().'<pagebreak /> mpdf-->';
	  
	  /*$html.= '<div id="cobtitulo"><h3>CONDIÇÕES PARA ACEITAÇÃO</h3></div>
                   <div id="cobtexto">Fazem parte desta proposta as CONDIÇÕES GERAIS e as CONDIÇÕES ESPECIAIS (módulos), devidamente aprovadas conforme processo 
                        SUSEP n° '. $dados['c_SUSEP'] .' e plenamente reconhecidas pelo SEGURADO, assim como o QUESTIONÁRIO apresentado pelo mesmo.<br><br>
                        Depois de devidamente protocolada a proposta assinada pelo SEGURADO, a '.mb_strtoupper(utf8_encode($dados['Nome'])).' terá 
                        o prazo de 15 (quinze) dias para se manifestar a respeito da aceitação do seguro.  Caso não haja nenhuma manifestação, neste 
                        prazo, por parte da SEGURADORA, o seguro estará automaticamente aceito.  Em caso de não aceitação da proposta, a SEGURADORA enviará
                        uma notificação ao SEGURADO no prazo máximo de 15 (quinze) dias e devolverá quaisquer valores de prêmio eventualmente pagos.</div></li>
						
	     <br><br>';*/


			$html .= '<table width="100%" border="0" style="font-size: 12pt;">
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
											<td  colspan="3" width="92%" style="font-weight:bold">CONDIÇÕES PARA ACEITAÇÃO</td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%" style="text-align:justify;">
												<div id="cobtexto">Fazem parte desta proposta as CONDIÇÕES GERAIS (Versão Setembro/2013) e as CONDIÇÕES ESPECIAIS (módulos), devidamente aprovadas conforme processo SUSEP
												n° 15414.005252/2005-53 e plenamente reconhecidas pelo SEGURADO, assim como o QUESTIONÁRIO apresentado pelo mesmo.<br><br></div>		          
							          <div id="cobtexto">Depois de devidamente protocolada a proposta assinada pelo SEGURADO, a COFACE DO BRASIL SEGUROS DE CRÉDITO SA terá o prazo de 15 (quinze) dias para se manifestar
							          a respeito da aceitação do seguro. Caso não haja nenhuma manifestação, neste prazo, por parte da SEGURADORA, o seguro estará automaticamente aceito. Em caso de não
							          aceitação da proposta, a SEGURADORA enviará uma notificação ao SEGURADO no prazo máximo de 15 (quinze) dias.<br><br></div>
							          <div id="cobtexto">Ao assinar esta proposta o PROPONENTE declara que o corretor '.$corretor_nome.' é o corretor nomeado para representá-lo
							          em quaisquer questões referentes à Apólice de Seguro de Crédito Interno originada por esta proposta.<br></div>
											</td>
										</tr>
									</table>'; 
     
      $html.= '<br>
						      <table width="100%" border="0" style="font-size: 12pt;">
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%">________________________,______de_____________________de_________</td>
										</tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%">______________________________________</td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%" style="font-weight:bold">'.$name.'</td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%">Procurador ou pessoa autorizada</td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%">Nome:</td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%">RG:</td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%">CPF:</td>
										</tr>
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%">Cargo:</td>
										</tr>
									</table>
								<br>';   

      $html.= '
					</body>
    		</html>';   
		
		}


		?>




   
    <textarea cols="90" rows="20"  name="textoCorpo" id="textoCorpo" style="height:auto;">
	   
	  <?php
	 
		// Início do arquivo montando primeiro o CSS
		
		//print $i_Produto;
		 
		 

	  /* //echo '?'. mb_detect_encoding($html);	 
	   $html =  mb_convert_encoding($html,'ISO-8859-1','UTF-8');
	   
	   echo  str_replace('dlares','d&oacute;lares',str_replace('Dlares','D&oacute;lares',$html)); 
		   
	   echo corrigir_text($html);
	   
	   function corrigir_text($html){
		    $html  = str_replace('dlares','d&oacute;lares',str_replace('Dlares','D&oacute;lares',$html));
		    return $html; 
	   }*/

	   
	   echo $html; 
	   
	   ?>
	   
	   </textarea>
      <input type="hidden" name="file_alterado" id="file_alterado" value="<?php echo $pdfDir.$finalx[6].'_alterada';?>"/>
	   	<input type="hidden" name="idInform" id="idInform" value="<?php echo $idInform;?>" />
	   	<input type="hidden" name="nomePropOrig" id="nomePropOrig" value="<?php echo $url_pdfprop;?>" />
</form>
   
  
   <div style="clear:both"></div>
  
    <br clear="all" />
   <li class="campo3colunas"> 
       <button type="button" onclick="document.form1.submit();" class="botaoagg">Emitir C&oacute;pia da Proposta</button>
   </li>
   
 </div>

<?php

	$tipo_documento = "proposta";	
	include_once("../../../gerar_word/htmltodocx/word.php");

	
?>		

<ul>       
   <div style="clear:both"></div>
  
    <br clear="all" />
		  <li class="campo3colunas"> 
			  <label><a href="<?php echo $propWord; ?>" target="_blank">Proposta Word</a></label>
		</li>
	<br clear="all" />

   <div style="clear:both"></div>

    <br clear="all" />
		  <li class="campo3colunas"> 
			  <label><a id="modal_upload" href="#">Upload Proposta Original</a></label>
		</li>
	<br clear="all" />
</ul>
  
   <div style="clear:both">&nbsp;</div>
     <form name="f">   
        <li class="campo2colunas">
          <button class="botaoagg" type="button" name="cont" id="cont" onClick="muda('../../role/executive/Executive.php?comm=notif', 0, <?php   echo $idInform;?>, <?php   echo $idNotification;?>,'Cancelar')">Cancelar Proposta</button>
          <button class="botaoagg" type="button" name="cont" id="cont" onClick="muda('../../role/executive/Executive.php?comm=viewProp&file=Parc', 1, <?php   echo $idInform;?>, <?php   echo $idNotification;?>,'Enviar')">Enviar Proposta</button>
          
		</li>
     </form>
    <div style="clear:both">&nbsp;</div>
</div>

<!-- UPLOAD -->


	<!-- Modal -->
		<div class="modal-ext" style="display:none">
			<div class="bg-black"></div>

			<div class='modal-int'>
			  <h1>Upload Proposta Original</h1>
			  <div class="divisoriaamarelo"></div>

				<form id="form-upload" action="<?php echo $root;?>role/executive/upload_proposta.php" method="post" enctype="multipart/form-data">
					<input type="hidden" name="file_name" value="<?php echo $url_pdfprop;?>" />
				  <div style="clear:both">&nbsp;</div>
					  <li class="campo3colunas"> 
						  <label><input type="file" id="arquivo" name="arquivo" style="width:100%;" /></label>
					</li>

					<li class="barrabotoes" style="list-style:none;*margin-left:-15px;width:700px;">
				  	<button type="button" class="botaovgm" id="close_modal">Voltar</button>
				  	<button type="button" class="botaoagg" id="save_upload">Salvar</button>
					</li>
				</form>

			</div>
		</div>
	<!-- Fim modal -->
<!-- UPLOAD -->