<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php



 // error_reporting(E_ALL);
 // ini_set("display_errors", 1 );
  
// header("Content-Type: text/html; charset=ISO-8859-1");
   if(! function_exists('ymd2dmy')){
           // converte a data de yyyy-mm-dd para dd/mm/yyyy
		  function ymd2dmy($d){
			if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
			  return "$v[3]/$v[2]/$v[1]";
			}
			return $d;
		  }
    }

        include_once('../../../navegacao.php');
	//$key	
?>        
 <div class="conteudopagina">
     
 
	 <?php
	 
	   if ($erro != ""){  ?>
		   <li id="clear" class="campo2colunas" style="width:400px; height:auto;">
	           <label style="color:#C00"><?php echo $erro; ?></label>
           </li>    
		   <div style="clear:both">&nbsp;</div>
<?php  }


	 
	   $xq =  "select name, startValidity, i_Produto,segundaVia,policyKey from Inform where id=$idInform";
	   
        $x = odbc_exec($db,$xq);
		$name  = odbc_result($x, 1);
		$i_Produto = odbc_result($x, 3);
		$pKeyVia = odbc_result($x, 'segundaVia');
		
		$key = ($key ? $key : (odbc_result($x, 'policyKey') ? odbc_result($x, 'policyKey') : odbc_result($x, 'segundaVia')));
		
		//print ':??   ' .$xq;
		
		//13409072144be5nekgesr8ot4jutbt2rj0i5
		
	 ?>
     
    <li class="campo2colunas" style="height:45px; width:600px;">
	<label>Cliente:</label>
	     <div class="formdescricao"><span><?php echo (odbc_result($x, 1));?></span></div>
    </li>
    <div style="clear:both">&nbsp;</div>
    
	<script language="javascript" type="text/javascript" src="<?php echo $host;?>Scripts/tinymce/tiny_mce/tiny_mce.js"></script>
	<script language="javascript"  type="text/javascript" src="<?php echo $host;?>Scripts/tinymce/tiny_mce/basic_config.js"></script>
	
 <script language="javascript" type="text/javascript">
 
 function editarProposta(){
	if(document.getElementById('prop_edit').style.display == 'none'){
		document.getElementById('prop_edit').style.display = 'block';
		
	}else{
		document.getElementById('prop_edit').style.display = 'none';
	}
}

 
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
	<li class="campo2colunas"><label><h2>In&iacute;cio da Vig&ecirc;ncia:</h2></label>
       <div class="formdescricao"><span><?php echo ymd2dmy(odbc_result($x, 2))?></span></div>
    </li>
    
    <div style="clear:both; height:2px;">&nbsp;</div>
    
    <li class="campo2colunas" style="height:30px;">
       <div class="formdescricao"><span><a href="<?php echo $root.'download/'.$key;?>Apolice.pdf" target=_blank>Ap&oacute;lice</a></span></div>
    </li>
    <?php  if ($i_Produto != 1){ ?>
                <div style="clear:both; height:2px;">&nbsp;</div>
                <li class="campo2colunas" style="height:30px;">
                   <div class="formdescricao"><span><a href="<?php echo $root.'download/'.$key;?>ApoliceReal.pdf" target=_blank>Ap&oacute;lice em Reais</a></span></div>
                </li>
    <?php  } ?>
    <div style="clear:both; height:2px;">&nbsp;</div>
    <li class="campo2colunas" style="height:30px;">
       <div class="formdescricao"><span><a href="<?php echo $root.'download/'.$key;?>CondPart.pdf" target=_blank>Condi&ccedil;&otilde;es Particulares</a></span></div>
	   <div class="formdescricao"><span style="color:#F00"><a href="javascript:" onclick="editarProposta();">Editar Condi&ccedil;&otilde;es Particulares</a></span></div>
	</li>
	<br clear="all" />
	<?php

	
			
						$diaT = date ('d');
						$mesT = date ('m');
						$dMoeda = date ("Y-m-d", mktime (0,0,0, $mesT, $diaT - 8, date("Y"))). ' 00:00:00.000'; //formata data de ontem

						
						function data_stringx($d){
							$meses = array("janeiro", "fevereiro", "março", "abril", "maio", "junho",
								"julho", "agosto", "setembro", "outubro", "novembro", "dezembro");
							//$dias_da_semana = array("Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado");
							list($dia, $mes, $ano) = split ('/', $d);
							$mes = $meses[$mes - 1];
							//$dia_da_semana = $dias_da_semana[$dia_da_semana];
							return "$dia de $mes de $ano";
						}
					
						
						$ano = date("y", time());
						//$key = time(). session_id();

						//Alterado por Tiago V N - Elumini - 13/02/2006
						// 2018/09/14 - AIP: Inclusão Do Número SUSEP Do produto
						$csql = "SELECT top 1 u.login, inf.respName, inf.idRegion, inf.name, inf.txMin, inf.prMin,
								inf.cnpj, inf.ie, inf.address, inf.tel, inf.fax, inf.email, inf.ocupationContact,
								inf.city, reg.name, Sector.description, inf.warantyInterest,
								inf.periodMaxCred, Country.name, inf.cep, inf.contrat, inf.contact,
								inf.txRise, inf.i_Seg, inf.txAnalize, inf.txMonitor, inf.limPagIndeniz,
								inf.prodUnit, inf.prMTotal, inf.percCoverage, inf.mModulos, inf.perBonus,
								inf.perPart0, inf.perPart1, inf.pLucro, inf.nas, inf.tipoDve,
								inf.addressNumber, inf.Ga, inf.addressComp, inf.products,inf.mPart,inf.txMTotal,inf.prMTotal,
								inf.i_Ramo,inf.i_Empresa,inf.startValidity as DataInicio,inf.i_Produto,inf.startValidity, 
								inf.endValidity,inf.v_LMI, inf.Renovacao_Tacita, SP.c_SUSEP
								FROM Users u 
								INNER JOIN Insured i ON u.id = i.idResp 
								INNER JOIN Inform inf ON inf.idInsured = i.id 
								INNER JOIN Region reg ON inf.idRegion = reg.id 
								INNER JOIN Sector ON inf.idSector = Sector.id 
								INNER Join Sub_Produto SP ON SP.i_Produto = inf.i_Produto AND SP.i_Sub_Produto = inf.i_Sub_Produto 
								LEFT  JOIN Importer ON inf.id = Importer.idInform 
								LEFT  JOIN Country ON Importer.idCountry = Country.id
								WHERE inf.id = $idInform";
						$cur = odbc_exec($db,$csql);
				
						// tenta achar o usuário responsável
						if (odbc_fetch_row($cur)) {
							
							$c_SUSEP  = odbc_result($cur,'c_SUSEP');
							$login    = odbc_result($cur, 1);
							$respName = odbc_result($cur, 2);
							$idRegion = odbc_result($cur, 3);
							$name     = odbc_result($cur, 4);	  
							$i_Ramo       = odbc_result($cur, 'i_Ramo');
							$i_Empresa    = odbc_result($cur, 'i_Empresa');
							$DataInicio   =  odbc_result($cur, 'DataInicio');
							$startValidity = Convert_Data_Geral(substr(odbc_result($cur,'startValidity'),0,10));
							$endValidity  = Convert_Data_Geral(substr(odbc_result($cur,'endValidity'),0,10));	  
							$txMin    = odbc_result($cur, 5);
							$prMin    = odbc_result($cur, 6);
							$prMTotal = odbc_result($cur, 'prMTotal');
							$txMTotal = odbc_result($cur, 'txMTotal');
							$cnpj     = odbc_result($cur, 7);
							$ValorLMI = odbc_result($curx, 'v_LMI');
							$ExtValorLMI = $numberExtensive->extensive(number_format($ValorLMI,0,'.',''),$fMoeda);								
							$renovacao_Tacica  = odbc_result($curx,'Renovacao_Tacita');
					
							$cnpj =
								substr($cnpj, 0, 2). ".".
								substr($cnpj, 2, 3). ".".
								substr($cnpj, 5, 3). "/".
								substr($cnpj, 8, 4). "-".
								substr($cnpj, 12);
							$ie       = odbc_result($cur, 8);
							$address  = odbc_result($cur, 9);
							$tel      = odbc_result($cur, 10);
							$fax      = odbc_result($cur, 11);
							$email    = odbc_result($cur, 12);
							$oContact = odbc_result($cur, 13);
							$city     = odbc_result($cur, 14);
							$uf       = substr(odbc_result($cur, 15), 0, 2);
							$descrip  = odbc_result($cur, 16);
							$interest = odbc_result($cur, 17);
							$period   = odbc_result($cur, 18);
							$DescrNatureza = odbc_result($cur, 'products');
							$complemento = odbc_result($cur, "addressComp");
							//$country  = odbc_result($cur, 19);
							$cep      = odbc_result($cur, 20);
							$sub = substr($cep, 0, 5);
								if(! preg_match("\.", $sub)){
//									$sub = substr($sub, 0, 2). '.'. substr($sub, 2, 3);
//								}else{
//									$inc = 1;
//									$sub = substr($cep, 0, 6);
//								}
//								if(! preg_match("-", $cep)){
//									$cep = "$sub-". substr($cep, 5);
//								}else{
//									$cep = "$sub-". substr($cep, 6 + $inc);
//								}
		
							// encontrar o número de propostas
							$contract     = odbc_result($cur, 21) . "/$nProp";
							$contato      = odbc_result($cur, 22);
							$txRise       = odbc_result($cur, 23);
							$extAnalise   = $numberExtensive->extensive(number_format(odbc_result($cur, "txAnalize"), 2, ".", ""), 1);
							$taxa_analise = number_format(odbc_result($cur, "txAnalize"), 2, ',', '.');
							$extMonit     = $numberExtensive->extensive(number_format (odbc_result($cur, "txMonitor"), 2, ".", ""), 1);
							$taxa_monit   = number_format(odbc_result($cur, "txMonitor"), 2, ',', '.');
							$limite       = odbc_result($cur, "limPagIndeniz");
							//die('?'.$limite. '<br>'.$csql.'<br>?'.odbc_result($cur, "limPagIndeniz"));	  
							$cobertura    = odbc_result($cur, 30);
							$extcob       = $numberExtensive->porcentagem(odbc_result($cur, 30));
							$mBonus = odbc_result($cur, 31);
							$mPart  = odbc_result($cur, 'mPart');
							$perBonus = odbc_result($cur, 32);
							$perPart0 = odbc_result($cur, 33);
							$perPart1 = odbc_result($cur, 34);
							$pLucro = odbc_result($cur, 35);
		
								if (odbc_result($cur, 36)== "" || odbc_result($cur, 36)=="0.00"){
		
									if ($moeda == "2") {
											$nas = "500.00";
									}else if ($moeda == "6") {
											$nas = "400.00";
									}
								}else{
									$nas = odbc_result($cur, 36);
		
								}
				
									$extnas = strtolower($numberExtensive->extensive(number_format($nas,2, '.',''),$fMoeda));                 
									$tipodve    = odbc_result($cur, 37);
									$extpart0   = $numberExtensive->porcentagem($perPart0);
									$extpart1   = $numberExtensive->porcentagem($perPart1);
									$addressNumber = odbc_result($cur, 38);
									$ga            = odbc_result($cur, 39);
				
										if (($ga=="0") || ($ga=="")){
											$susep = "15.414005218/2005-89";
											$cp    = "CP/RC/06-01";
										}else{
											$susep = "15.414004768/2004-08";
											$cp    = "CP/GA/07-01";
										}
				
									$idSeg = odbc_result($cur, 'i_Seg');
									  
										if($idSeg == 0){
											$cc = odbc_exec($db, "select i_Seg from Inform where id=(select idAnt from Inform where id=$idInform)");
											$idSeg = odbc_result($cc, 1);
										}
				
									$prodUnit = odbc_result($cur, 'prodUnit');
									//Alterado por Elias Vaz Interaktiv - Dez/2009
									//$tx = $txMin * (1 + $txRise) * 100;
									$tx = $txMTotal;
				
									$IC_APLICA = odbc_exec($db, "select IC_APLICA from TB_TAXA_INFORME_BB where ID_INFORME=$idInform");
									$IC_APLICA_TAXA = odbc_result($IC_APLICA, 1);
				
										//# Taxa de prêmio + 10%
										if($IC_APLICA_TAXA=="1") {
											$tx = $tx;
										} else {
											//$tx = $tx+($tx*0.10);
											$tx = $tx;
										}
				
									$exttx = $numberExtensive->porcentagem($tx);
									//$pr = $prMin * ($interest == 1 ? 1.04 : 1) * (1 + $txRise);
									$pr =  number_format(odbc_result($cur, 'prMTotal'),0,".","");
				
									if($policyKey == ""){
											$ja_foi = 0;
									}else{
											$ja_foi = 1;
									}
				
								
								// Pegar o número da apólice uma vez gerada. 	
								if($i_Produto == 1){				 
									$apoNum = $NumeroApoliceOIM;
									// $apoNum = sprintf("062%06d", $apoNum);
										 
									if($prodUnit != 62){
										  $apoNum .= "/".$prodUnit;
									}
										 
								}else{
									$rr = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$idSeg");
						
									if(odbc_fetch_row($rr)){
										$apoNum = odbc_result($rr, 1);
									}
											
									$apoNum = sprintf("062%06d", $apoNum);
											
									if($prodUnit != 62){
										$apoNum .= "/$prodUnit";
									}
									
									
									
								}
								
								}
									
								//Seguir
						
										$end = "$address $addressNumber - $complemento - $city - $uf";
										$endcompleto = "$address $addressNumber - $complemento";
										$prMin = number_format($prMin, 2, ',', '.');
										//$pr = number_format($pr, 2, ',', '.');
										$tx = number_format($tx, 3, '.', '');
										$txMin = number_format($txMin, 3, '.', '');
						
										if ($startValidity == ""){
											$today = "";
										}else{
											$today = "$startValidity à $endValidity";
										}
						
										$data = data_stringx($startValidity);
						
									    if($t_Venc){
									       $periodo = $t_Venc;
								        }else{
											if ($pvigencia !="2"){
												switch($num_parcelas){ // periodo em meses
													case 2:
														  $periodo = 3;
														  break;
													case 4:
														  $periodo = 3;
														  break;
													case 6:
														  $periodo = 2;
														  break;
													case 7:
													case 10:
														  $periodo = 1;
													break;
												}
											}else{
												switch($num_parcelas){ // periodo em meses
													case 4:
														  //$periodo = 6;
														  $periodo = 3;
														  break;
													case 7:
														  $periodo = 1;
														  break;
													case 8:
														  $periodo = 3;
													break;
												}
											}
										}
							
						
							$valPar = $pr / $num_parcelas;
							$parc = $valPar;
							$AuxParc = $valPar;
							//$parc = number_format($prMTotal / $num_parcelas, 0 "", "");
						
							//$pr = $num_parcelas * $valPar;
							$valExt = $numberExtensive->extensive($pr, $fMoeda);
						  
							$valExtReal =  $numberExtensive->extensive($pr, 1);
						  
							//************************** calcular valor final da parcela
							$VerResto = ($valPar * $num_parcelas);
							$restoParc = $pr - $VerResto;
							$restoParc1 = $VerResto - $pr;
							//valor da última parcela
						
							if ($restoParc > 0){
									$VlrUltparc = ($restoParc +  $valPar);
							}else if ($restoParc < 0){
									$VlrUltparc = ($valPar - $restoParc1);
							}else{
									$VlrUltparc = $parc;
							}
				
				
				
				
				
				
				
				
			/************************************************************************************/	
				
				  
				
					if ($moeda == "2") {
						 $ext = "US$ ";
						 $DescMoeda = "DÓLARES NORTE-AMERICANOS";
						 $extmoeda = "dólar norte-americano (US$)";
						 $fMoeda = "2";
					}else if ($moeda == "6") {
						 $ext = "€ ";
						 $DescMoeda = "EUROS";
						 $extmoeda = "euro (€)";
						 $fMoeda = "6";
					}
				
					$numParc  = ($Num_Parcelas ? $Num_Parcelas : $numParc);

 
	  
			  if($Num_Parcelas){
				  if ($Num_Parcelas == 1){
					  $txtParcs = "à vista";
					  $periodo = "à vista";
					  $periodo1 = "à vista";
				  }else{
					  $txtParcs = "em ". $Num_Parcelas. " prestações ".$t_DescVencimento.".";
					  $periodo =  $t_DescVencimento;
					  $periodo1 = $t_DescVencimento;
				  }
			  }else{
				 if ($pvigencia != 2) {			 
					  if ($numParc == 1){
						$txtParcs = "à vista";
						$periodo = "à vista";
						$periodo1 = "à vista";
					  }else if ($numParc == 2){
						$txtParcs = "em duas prestações: 1 e mais 1 em noventa dias";
						$periodo = "trimestral";
						$periodo1 = "trimestrais";
					  }else if ($numParc == 4){
						$txtParcs = "em 4 parcela(s) iguais e trimestral(is)";
						$periodo = "trimestral";
						$periodo1 = "trimestrais";
					  }else if ($numParc == 7){
						$txtParcs = "em 7 parcela(s) iguais e mensal(is)";
						$periodo = "mensal";
						$periodo1 = "mensais";
					  }else if ($numParc == 10){
						$txtParcs = "em 10 parcela(s) iguais e mensal(is)";
						$periodo = "mensal";
						$periodo1 = "mensais";
					  }
				  
				  }else if ($pvigencia == 2){			 
					  if ($numParc == 1){
						$txtParcs = "à vista";
						$periodo = "à vista";
						$periodo1 = "à vista";
					  }else if ($numParc == 4){
						$txtParcs = "em 4 parcela(s) iguais e trimestral(is)";
						$periodo = "trimestral";
						$periodo1 = "trimestrais";
					  }else if ($numParc == 7){
						$txtParcs = "em 7 parcela(s) iguais e mensal(is)";
						$periodo = "mensal";
						$periodo1 = "mensais";
					  }else if ($numParc == 8){
						$txtParcs = "em 8 parcela(s) iguais e trimestral(is)";
						$periodo = "trimestral";
						$periodo1 = "trimestrais";
					  }
				
				  }
		     }
		 
		     $bonus = "";
		
			  if ($mBonus == "1") {
					$bonus = "A percentagem de bônus referente ao item 2 deste módulo é ".round($perBonus)."%"; //10%(dez por cento).
			  }
		  
			  if ($mPart == "1") {
				 if ($pLucro == "F13") {
					 $partic  = "<b>F13.02    PARTICIPAÇÃO NOS LUCROS</b>";
				 }else if ($pLucro == "F14") {
					 $partic  = "<b>F14.02    PARTICIPAÇÃO NOS LUCROS</b>";
				 }else if ($pLucro == "F15") {
					 $partic  = "<b>F15.02    PARTICIPAÇÃO NOS LUCROS</b>";
				 }
					  $extpart0   = $numberExtensive->porcentagem($perPart0);
					 $extpart1   = $numberExtensive->porcentagem($perPart1);
					 $part .= "Percentagem de dedução: ".$perPart0."% (".$extpart0.")<br>".
							  "Participação nos lucros: ".$perPart1."% (".$extpart1.")<br>";
					 $valbo = "1";
			  }
	
	
			  if ($tipodve == "1") {
			      $tipo = "mensal";
			  }else if ($tipodve == "2") {
			      $tipo = "trimestral";
				}else if ($tipodve == "3") {
			      $tipo = "anual";
			  }
	
			  $a502titulo = "";
			  $a502 = "";
			  $b1202X = "";
			  $b1202 = "";
	
			  $data = date("d")."/".date("m")."/".date("Y");
			
			  $periodoMaxCredito = odbc_result(odbc_exec($db, "select periodMaxCred from Inform where id=$idInform"), 1);
			
			  $jurosMora = odbc_result(odbc_exec($db, "select warantyInterest from Inform where id=$idInform"), 1);
			
			  $x = odbc_exec($db, "select a502, b1202, d602, d701, nivel_d602, p_cobertura_d701, limite_d701,a801, 
									 b603, b1504, b1202, c102, d101,e101, f305, f3301,adequacao_sinistralidade,
									 adequacao_premio,franquia_anual, CONVERT(varchar(1000),condicoes_especiais) AS condicoes_especiais,
									 PrazoMaxEmiNota, b904, b2604Perc, b2604NivelMax, 
									 b2801NivelMax, b2801Perc, d201, f401NivelSinistralidade,f401PercPremio,b802, b404NivelMax,b404Perc,GerenteCredito,Texto_Risco_Politico
								   from ModuloOferta where idInform=$idInform");
			         
			
			  //$x = odbc_exec($db, "select a801, a502, b603, b1504, b1202, c102, d101, d602, d701, e101, f305, f3301, nivel_d602, p_cobertura_d701, limite_d701 from ModuloOferta where idInform=$idInform");
				  // Definiçao dos modulos
			  $mod_a801 = odbc_result($x,8);
			  $mod_a502 = odbc_result($x,1);
			  $mod_b603 = odbc_result($x,9);
			  $mod_b1504 = odbc_result($x,10);
			  $mod_b1202 = odbc_result($x,11);
			  $mod_c102 = odbc_result($x,12);
			  $mod_d101 = odbc_result($x,13);
			  $mod_d602 = odbc_result($x,3);
			  $mod_d701 = odbc_result($x,4);
			  $mod_e101 = odbc_result($x,14);
			  $mod_f305 = odbc_result($x,15);
			  $mod_f3301 = odbc_result($x,16);	          
			  $GerenteNome     = odbc_result($x,'GerenteCredito');
			   
			  $ad_sinistr = odbc_result($x,'adequacao_sinistralidade');
			  $ad_premio = odbc_result($x,'adequacao_premio');
			   if ($ad_sinistr > 0 || $ad_premio > 0){
				   $ad_sinistr = number_format($ad_sinistr,2,',','.');
				   $ad_premio = number_format($ad_premio,2,',','.');
				   $exibe_ad = 1;
		
			   }else {
				   $ad_sinistr = 0;
				   $ad_premio = 0;
				   $exibe_ad = 0;
			   }
		
			  $franquia_anual = odbc_result($x,'franquia_anual');
	
	          $mod_b2604                 = number_format(odbc_result($x,'b2604Perc'),2,',','.');
			  $b2604NivelMax             = number_format(odbc_result($x,'b2604NivelMax'),2,',','.');
			  $b2604NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($x,'b2604NivelMax'),2,'.',''),$fMoeda);
			  
			  $b2801NivelMax             = number_format(odbc_result($x,'b2801NivelMax'),2,',','.');
			  $b2801NivelMaxExt          = $numberExtensive->extensive(number_format(odbc_result($x,'b2801NivelMax'),2,'.',''),$fMoeda);
			  $b2801Perc                 = number_format(odbc_result($x,'b2801Perc'),2,',','.');
			  $riscopolitico             = odbc_result($x,'Texto_Risco_Politico');
	
			  if ($franquia_anual > 0){
				  $franquia_anual = number_format(odbc_result($x,'franquia_anual'),2,',','.'). " (".strtolower($numberExtensive->extensive(number_format(odbc_result($x,'franquia_anual'),2,'.',''),$fMoeda)).").";
				  $exibe_franq = 1;
		
			  }else{
				  $franquia_anual = 0;
				  $exibe_franq = 0;
			  }
	
			  if (odbc_result($x,'condicoes_especiais') != "" ){
				 $condicoes_especiais = odbc_result($x,'condicoes_especiais') ;
				 $exibe_cond = 1;
			  }else{
				 $condicoes_especiais = "";
				 $exibe_cond = 0;
			  }
	
			  if(odbc_result($x, 1) == "1") {
				$a502titulo = "<b>A5.02    COBERTURA DE RISCO DE PRODUÇÃO</b> ";
				$a502 = $a502."i) A cobertura de risco de produção se aplica para todos os importadores indicados na ficha de aprovação de limites de crédito.";
			  }
			  
			  if(odbc_result($x, 2) == "1") {
				  $b1202X = "<b>B12.02</b>    <b>EXTENSÃO DO CONTRATO A UMA OU MAIS EMPRESAS</b>   ";
				  $b1202 = $b1202."A cobertura é estendida aos contratos de vendas celebrados pelas seguintes empresas: \n\n";
				  $query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = $idInform ORDER BY no_razao_social ";
				  $cur = odbc_exec ($db, $query);
				  $i = 0;
					 while (odbc_fetch_row($cur)) {
						$no_razao_social       = odbc_result ($cur, 'no_razao_social');
						$nu_cnpj               = odbc_result ($cur, 'nu_cnpj');
						$nu_inscricao_estadual = odbc_result ($cur, 'nu_inscricao_estadual');
						$ds_endereco           = odbc_result ($cur, 'ds_endereco');
						$nu_cep                = odbc_result ($cur, 'nu_cep');
						$no_cidade             = odbc_result ($cur, 'no_cidade');
						$no_estado             = odbc_result ($cur, 'no_estado');
				        //	$b1202 = $b1202."Razão Social: ".$no_razao_social." - CNPJ: ".$nu_cnpj." - Inscrição Estadual: ".$nu_inscricao_estadual." - Endereço: ".$ds_endereco."' - CEP: ".$nu_cep." - Cidade: ".$no_cidade." - Estado: ".$no_estado." \n";
						$i ++;
							if ($no_razao_social){
								if ($i > 1){
									  $b1202 .= "\n";
								   }
								//$modulo_final .= ''.$no_razao_social." - Endereço: ".$ds_endereco.", ".$nu_endereco."  ".$ds_complemento."\nCidade: ".$no_cidade."  Estado: ".$no_estado."  CEP: ".$nu_cep."   CNPJ: ".$nu_cnpj."  IE: ".$nu_inscricao_estadual." \n";
								$b1202 .= 'Razão Social:'.$no_razao_social."\n";
								$b1202 .= 'Endereço:'.$ds_endereco.", ".$nu_endereco." ".$ds_complemento."\nCidade: ".$no_cidade."  Estado: ".$no_estado."  CEP: ".$nu_cep."\nCNPJ: ".$nu_cnpj."\n";
								   
							}
				
				
					  }
				       //$b1202 = $b1202."\n";
			   }
	
	          
			  $cons = " select a.idCorretor,a.p_Comissao,a.p_Corretor,a.CorretorPrincipal,b.razao, b.c_SUSEP
					from InformCorretor a
					inner join consultor b on b.idconsultor = a.idCorretor
					where a.idInform = ".$idInform. " order by a.CorretorPrincipal desc";					
				$resultado = odbc_exec($db, $cons);
			
				$pularlinha = '';
				$MultiCorretor  = '';
							
				$linhas = odbc_num_rows($resultado);
			
					if ($linhas){   
					while (odbc_fetch_row($resultado)){
						if (odbc_result($resultado,'CorretorPrincipal') == 1){	
								$Corretor =  odbc_result($resultado,'razao');
								$codigoSusep = odbc_result($resultado,'c_SUSEP');
						}
			
						$MultiCorretor .=  $pularlinha.strtoupper(odbc_result($resultado,'razao'));
						$pularlinha = '<br>';
					} 
				}else{
						$Corretor = '';
						$codigoSusep = '';
				}  
	
			  
				//Condição Especial de Cobertura de Coligadas
				
				$sql  = "select limPagIndeniz from Inform where id =  ".$idInform. "";
				$rsx = odbc_exec($db, $sql);
				$limiteInden       = odbc_result($rsx, "limPagIndeniz");
			
				$rsquery = odbc_exec($db, "select a.idInform,a.razaoSocial,a.endereco,a.zipCode, a.pais,b.name, a.taxID
										   from ParModEsp_Coligada a inner join Country b on b.id = a.pais
										   where a.idInform = $idInform ORDER BY a.razaoSocial ");
			
				 while (odbc_fetch_row($rsquery)){
					   $condespcol   .= "\n";
				
					   $condespcol   .= " <b>".  odbc_result ($rsquery, 'razaoSocial')."</b>\n";
					   $condespcol   .= " Endereço: ".odbc_result ($rsquery, 'endereco')."\n";
					   $condespcol   .= " Pais: ". odbc_result ($rsquery, 'name')."\n Zip Code: ".odbc_result ($rsquery, 'zipCode') ."\n";
					   $condespcol   .= " Tax ID: ". odbc_result ($rsquery, 'taxID')." \n";
					   /*
					   $condespcol   .= " |".  odbc_result ($rsquery, 'razaoSocial')."|\n";
					   $condespcol   .= " ".odbc_result ($rsquery, 'endereco').", ".odbc_result ($rsquery, 'numeroEndereco') ." - ".odbc_result ($rsquery, 'complementoEndereco')." \n";
					   $condespcol   .= " ". odbc_result ($rsquery, 'cidade')." -  ".odbc_result ($rsquery, 'estado') ."\n";
					   $condespcol   .= " Número do registro: ". odbc_result ($rsquery, 'numRegistro')." \n";
					   */
				 }
	
				if(odbc_result($x, 3) == "1") {
					 //$d602  = "\n\n|D6.02 PEQUENOS SINISTROS| \n";
					$nivel_d602 = odbc_result($x, 5);
					$nivel = strtolower($numberExtensive->extensive(number_format($nivel_d602,2, '.',''),$fMoeda));
					$d602 = $d602."O nível de pequenos sinistros é de:".$ext.number_format($nivel_d602,2,',','.')." (".$nivel.")";
				}
			    if(odbc_result($x, 4) == "1") {
				    if($d602=="") {
					    $d701  = "\n\n|D7.01 LITÍGIO|";
					} else {
						$d701  = "\n\n|D7.01 LITÍGIO|";
					}
					$p_cobertura_d701  = odbc_result($x, 6);
					$limite_d701 = odbc_result($x, 7);
					//$d701 = $d701."\ni) Percentual de Cobertura: ".$p_cobertura_d701."% \nii) Limite de pagamento por Litígio: ".$limite_d701."\n";
					//$d701 = "O percentual de cobertura é de: ".$p_cobertura_d701."% \nO limite de pagamento por <b>litígio</b> é de: ".str_replace('E','e',$limite_d701)."";
					$d701 = "O percentual de cobertura é de: ".$p_cobertura_d701."% <br>".$limite_d701."";
			    }
	
                
				$b404NivelMax              = number_format(odbc_result($x,'b404NivelMax'),2,',','.');
				$b404NivelMaxExt           = $numberExtensive->extensive(number_format(odbc_result($x,'b404NivelMax'),2,'.',''),$fMoeda);
				$mod_b404                  = number_format(odbc_result($x,'b404Perc'),2,',','.');
					
   
					$sqlquery  = "Select E.*,P.Nome as Produto, SP.Descricao as SubProduto, SP.c_SUSEP,Inf.i_Gerente,Inf.i_Produto  
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
																  
					/*$mpdf=new mPDF('win-1252','A4','','',20,15,48,25,10,10); 
					$html_pdf = ob_get_clean();
					$mpdf->useOnlyCoreFonts = true;    // false is default
					//$mpdf->SetProtection(array('print'));
					$mpdf->SetTitle("Proposta");
					$mpdf->SetAuthor("Coface Brasil SA.");
					$mpdf->SetWatermarkText(""); // fundo marca dágua
					$mpdf->showWatermarkText = true;
					$mpdf->watermark_font = 'DejaVuSansCondensed';
					$mpdf->watermarkTextAlpha = 0.1;
					$mpdf->SetDisplayMode('fullpage');*/
				 
				 
				// Endereço do logotipo
				$logo  = '../../images/logo_pdf.jpg';
				$logo_mini  = '../../images/logo_mini.jpg';
				
	?>
    <div id="prop_edit" style="display:none">
   	   <form  action="<?php echo $root;?>role/policy/editar_condicoes_part.php" name="edit_cond" id="edit_cond" method="post" target="_blank">
	        <input type="hidden" name="idInform" id="idInform" value="<?php echo $idInform;?>" />
	        
	        <textarea cols="90" rows="20"  name="textoCorpo" id="textoCorpo" style="height:auto;">
			   <?php 

			   $logo  = '../../images/logo_pdf.jpg';
        $logo_peq  	= '../../images/logo_peq.jpg';
			   
			   
			   /*	include_once("../../gerar_pdf/MPDF45/mpdf.php");
   												  
				$mpdf=new mPDF('win-1252','A4','','',20,15,48,25,10,10); 
				$html = ob_get_clean();
				$mpdf->useOnlyCoreFonts = true;    // false is default
				//$mpdf->SetProtection(array('print'));
				$mpdf->SetTitle("Proposta");
				$mpdf->SetAuthor("Coface Brasil SA.");
				$mpdf->SetWatermarkText(""); // fundo marca dágua
				$mpdf->showWatermarkText = true;
				$mpdf->watermark_font = 'DejaVuSansCondensed';
				$mpdf->watermarkTextAlpha = 0.1;
				$mpdf->SetDisplayMode('fullpage');*/
				
			   if($i_Produto == 2){  
					
              			 $html_pdf = '<html>
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
												#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
												
												</style>
												
												</head>
												<body>
								
													 <htmlpageheader name="myheader">
														<div style="text-align: center;">
																<img src="'.$logo_peq.'" width="80" height="40"/>
														</div>
														<br>
													 	<div style="text-align: left;">
													    	<span style="font-weight: bold; font-size: 11pt;">CONDIÇÕES PARTICULARES - SEGURO DE CRÉDITO À EXPORTAÇÃO</span>
													  	</div>

													    <div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; ">
													 	<div style="text-align: right;"><span style="font-style:italic; font-size: 8pt;">APÓLICE Nº</span>
													 	<span style="font-weight: bold; font-size: 8pt;">'.$apoNum.'</span><div>
											
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
													<sethtmlpagefooter name="myfooter" value="on" />';
					
									
										$html = ' 
									    <span style="font-weight: bold; font-size: 12pt;"><u>DADOS DO SEGURADO:</u></span><br>
										<table width="100%" border="0" style="font-size: 12pt;">					
										<tr>
										   <td width="25%">Razão Social: </td><td width="75%"><span style="font-weight: bold; font-size: 12pt;">'.strtoupper(trim(utf8_encode($name))).'</span></td>
										</tr>
										<tr>
										  <td width="25%">Endereço: </td><td width="75%">'. utf8_encode($endcompleto).'</td>
										</tr> 
										<tr>
										  <td width="25%">&nbsp;</td><td width="75%">'.utf8_encode($city) .' - '. $uf.'</td>
										</tr>
										<tr>  
										  <td width="25%">&nbsp;</td><td width="75%">CEP '.$cep.'</td>
										</tr>
										<tr>
										  <td width="25%">CNPJ: </td><td width="75%">'. formata_string('CNPJ',$cnpj).'</td>			
										</tr>
										<tr>
										  <td width="25%">Corretor nomeado: </td><td width="75%">'. utf8_encode(($MultiCorretor != ''? $MultiCorretor: $corretor)).'</td>			
										</tr>
										</table> 
										
											<br>
											<div style="border-top: 1px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>
											<br>
											
											
											 <div id="sublinhado">COBERTURA:</div>					
											 
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
												   <td colspan="1" width="84%">'. utf8_encode($DescrNatureza).'</td>			
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
															<td colspan="1" width="84%">b) Risco político: '. $riscopolitico . ' </td>
														</tr>';
											   }
													
											$html .= '	
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
												   <td colspan="1" width="84%">'. $cobertura.'%</td>			
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
												   <td colspan="1" width="84%">'.number_format($tx,4,",",".").'%  aplicável ao volume de exportações</td>			
												</tr>
												<tr>
												 <td colspan="3">&nbsp;</td>
												</tr>
												<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%" style="font-weight:bold">2.5</td>
												   <td  colspan="1" width="84%" style="font-weight:bold">PRÊMIO MÍNIMO:</td>			
												</tr>';
												
												
										
												// verificações 
												$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
												$rres = odbc_exec($db, $sqlvig);
												$num  = odbc_num_rows($rres);
												
												if($num > 0){
													while(odbc_fetch_row($rres)){
														$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$currency);
															
														if($num == 1){
															$html .= '
																<tr>
																	<td colspan="2" width="16%">&nbsp;</td>
																	<td colspan="1" width="84%" style="text-align:justify">Prêmio Mínimo pelo período de seguro de '. Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) . ' ' . $ext. ' '. number_format(odbc_result($rres,'v_Premio'), 2, ",", "."). ' ('.strtolower($valExt) .').</td>			
																</tr>
															'; 
														}else{
															$html .= '
																<tr>
																	<td colspan="2" width="16%">&nbsp;</td>
																	<td colspan="1" width="84%" style="text-align:justify">'.$ext.' '. number_format(odbc_result($rres,'v_Premio'), 2, ",", "."). ' ('.strtolower($valExt) .'). Pelo período de 12 meses compreendido entre '. Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' e ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'</td>			
																</tr>
															'; 
														}
														
														$total +=	odbc_result($rres,'v_Premio');
													}
													
													if($num > 1){
														$totalext = $numberExtensive->extensive(number_format($total,0,'.',''),$currency);
														
														$html .= '
																<tr>
																	<td colspan="2" width="16%">&nbsp;</td>
																	<td colspan="1" width="84%"style="text-align:justify">Prêmio Mínimo Total por todo período de seguro:<br>'.$ext.' '. number_format($total, 2, ",", "."). ' ('.strtolower($totalext) .').</td>			
																</tr>
															'; 
													}
												}else{
												   $html .= '
													<tr>
														<td colspan="2" width="16%">&nbsp;</td>
														<td colspan="1" width="84%" style="text-align:justify">'.$ext.' '. number_format($pr, 2, ",", "."). ' ('.strtolower($valExt).').</td>			
													</tr>
													'; 
												}
					
												
												/*<tr>
												   <td colspan="2" width="16%">&nbsp;</td>
												   <td colspan="1" width="84%">'.$ext . number_format($pr, 2, ',', '.'). ' ('. utf8_encode(strtolower($valExt)).')</td>			
												</tr>'; */
													  
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
														   <td colspan="1" width="84%" style="text-align:justify">O SEGURADO contrata cobertura acessória de juros operacionais e moratórios, cujo adicional de prêmio é de 4% sobre o prêmio da apólice.</td>			
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
												   <td  colspan="2" width="92%"><div id="cobtexto">A moeda da apólice é o '. ($extmoeda).'</div></td>
															
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
												   <td  colspan="2" width="92%" style="text-align:justify">A conversão de quaisquer valores será sempre efetuada mediante aplicação da  taxa  de  câmbio divulgada pelo Banco Central do Brasil PTAX800.</td>
															
												</tr>
												
												<tr>
												 <td colspan="3">&nbsp;</td>
												</tr>
												
												<tr>
												   <td colspan="1" width="8%" style="font-weight:bold">5.</td>
												   <td colspan="2" width="92%" style="font-weight:bold">LIMITE MÁXIMO DE INDENIZAÇÃO</td>			
												</tr>';
												
												if($limiteInden){
													$html .= '
														<tr>
															<td  colspan="1" width="8%">&nbsp;</td>
															<td  colspan="2" width="92%" style="text-align:justify">5.1 - '.$limiteInden  .' vezes o prêmio pago por cada período de 12 meses de vigência da apólice.</td>
														</tr>
														<tr>
															<td  colspan="1" width="8%">&nbsp;</td>
															<td  colspan="2" width="92%" style="text-align:justify">5.2 - O Limite Máximo de Indenização será calculado e aplicado anualmente com base no prêmio total pago no respectivo período 12 meses.</td>
														</tr>
														<tr>
															<td  colspan="1" width="8%">&nbsp;</td>
															<td  colspan="2" width="92%" style="text-align:justify">5.3 - O valor do Limite Máximo de Indenização calculado no primeiro período de 12 meses de vigência da apólice não será transportado para o próximo período de vigência.</td>
														</tr>';
												 } else{
													$html .= '
														<tr>
															<td  colspan="1" width="8%">&nbsp;</td>
															<td  colspan="2" width="92%" style="text-align:justify">O Limite máximo para pagamento de indenizações por período de vigência da apólice é de '.$ext. ' ' .number_format($ValorLMI, 2, ',', '.') .' (' . strtolower($ExtValorLMI).').</td>
														</tr>';
												 }
												
/*												<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="92%">'.$limiteInden  .' vezes o valor do prêmio por período de seguro.</td>*/
															
											$html .= '</tr>
												<tr>
												 <td colspan="3">&nbsp;</td>
												</tr>
												<tr>
												   <td colspan="1" width="8%" style="font-weight:bold">6.</td>
												   <td colspan="2" width="92%" style="font-weight:bold">PRAZO MÁXIMO DE CRÉDITO</td>			
												</tr>
												<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="92%"  style="text-align:justify">'.$periodoMaxCredito.' dias contados a partir da data do embarque internacional das mercadorias vendidas ou da data do faturamento dos serviços prestados.</td>
															
												</tr>
												<tr>
												 <td colspan="3">&nbsp;</td>
												</tr>
												<tr>
												   <td colspan="1" width="8%" style="font-weight:bold">7.</td>
												   <td colspan="2" width="92%" style="font-weight:bold">PRAZO PARA DECLARAR A AMEAÇA DE SINISTRO</td>			
												</tr>
												<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="92%"  style="text-align:justify">'. ($periodoMaxCredito + 30).' dias contados a partir da data do embarque internacional das mercadorias vendidas ou da data do faturamento dos serviços prestados.</td>
															
												</tr>
												<tr>
														<td  colspan="1" width="8%">&nbsp;</td>
													<td  colspan="2" width="92%"  style="text-align:justify">No caso de prorrogação da data de vencimento, dentro das condições constantes no item 2.2.3 da cláusula 2 das condições gerais da apólice, o prazo é de 30 dias contados do novo vencimento.</td>
												</tr>
												<tr>
												 <td colspan="3">&nbsp;</td>
												</tr>
												<tr>
												   <td colspan="1" width="8%" style="font-weight:bold">8.</td>
												   <td colspan="2" width="92%" style="font-weight:bold">VIGÊNCIA DA APÓLICE</td>			
												</tr>';
												 // verificações 
												$sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
												$rres = odbc_exec($db, $sqlvig);
												$num  = odbc_num_rows($rres);
												
														if($num > 1){
															
															$ssqx = "select MIN(d_Vigencia_Inicial)as Ini, MAX(d_Vigencia_Final) as Fim from Periodo_Vigencia where i_Inform =".$idInform. "";
															$rresx = odbc_exec($db, $ssqx);
															$html .= '<tr>
																			<td colspan="1" width="8%">&nbsp;</td>
																			<td colspan="2" width="92%" style="text-align:justify">
																			A apólice entra em vigor no dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Ini'),0,10)).' e terá validade até o dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Fim'),0,10)).', cujo período equivalente ao período de seguro.
																			Este período de seguro é dividido em dois períodos distintos de vigência compreendidos entre:<br>';
																			
															while(odbc_fetch_row($rres)){
																$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$fMoeda);
																$html .= 'Período '.odbc_result($rres,'n_Preriodo').' – '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'<br>';
															}
															
															
															if($renovacao_Tacica == 1){ // cmapo no inform
																$html .= '	 
																	<br>
																	A apólice poderá ser renovada automaticamente, por igual período de seguro, caso não seja comunicada a intenção de não renovação por qualquer das partes, devendo tal comunicação de não renovação ser feita por escrito pelo SEGURADO ou pela SEGURADORA com antecedência de 60 dias do termo final do primeiro período de seguro. 
																	<br><br>
																	Revoga-se o item 12.2 das Condições Gerais da apólice o qual passará a vigorar com a seguinte redação:
																	</td>			
																</tr>
															';
															}else{
																 $html .= '	 
																	<br>
																		"A apólice tem duração definida nas CONDIÇÕES PARTICULARES e não pode ser renovada tacitamente."
																	 </td>			
																   </tr>
																 ';
																
															}
														}else{				   
														   
															$html .= '<tr>
																	<td  colspan="1" width="8%">&nbsp;</td>
																<td  colspan="2" width="92%" style="text-align:justify">'. ($Periodo_Vigencia ? $Periodo_Vigencia : ($pvigencia == 1? 12 : 24)) .' meses a partir da data de aceitação da presente PROPOSTA DE SEGURO.<br>';
															
															if($renovacao_Tacica ==1){ // campo no inform
																$html .= '	 
																	<br>
																	A apólice poderá ser renovada automaticamente, por igual período de seguro, caso não seja comunicada a intenção de não renovação por qualquer das partes, devendo tal comunicação de não renovação ser feita por escrito pelo SEGURADO ou pela SEGURADORA com antecedência de 60 dias do termo final do primeiro período de seguro. 
																	<br><br>
																	Revoga-se o item 12.2 das Condições Gerais da apólice o qual passará a vigorar com a seguinte redação:
																	</td>			
																</tr>
															';
															}else{
																 $html .= '	 
																	<br>
																		"A apólice tem duração definida nas CONDIÇÕES PARTICULARES e não pode ser renovada tacitamente."
																	 </td>			
																   </tr>
																 ';
																
															}
															
														}
												
												
												/*$html .= '	
												<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="92%">A apólice entra em vigor no dia '.$startValidity.' e terá validade até o dia '.$endValidity.', cujo período equivale ao período do seguro.</td>
															
												</tr>';*/
												
											$html .= '
												<tr>
												 <td colspan="3">&nbsp;</td>
												</tr>
												<tr>
												   <td colspan="1" width="8%" style="font-weight:bold">9.</td>
												   <td colspan="2" width="92%" style="font-weight:bold">MÓDULOS</td>			
												</tr>
												<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="92%">Os seguintes módulos fazem parte deste contrato:</td>						   			
												</tr>
												</table>';
												
										 $html .= '<table width="100%" border="0" style="font-size: 12pt;"> ';
								  
								  
												
								  
											$qryM = "select a.i_Modulo, b.Cod_Modulo,b.Grupo_Modulo,b.Titulo_Modulo,convert(varchar(1000),a.Desc_Modulo) as Desc_Modulo 
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
																	   <td  colspan="3" width="92%" style="font-weight:bold"><div id="sublinhado">'. odbc_result($mod,'Grupo_Modulo') .'</div></td>
																   </tr>';
													   }
													   
														$Titulo = strlen(odbc_result($mod,'Titulo_Modulo'));
														$html .= '<tr>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="1" width="8%">&nbsp;</td>
															   <td  colspan="1" width="8%" style="font-weight:bold">'. utf8_encode(odbc_result($mod,'Cod_Modulo')).'</td>
															   <td  colspan="1" width="76%" style="font-weight:bold">'.utf8_encode(odbc_result($mod,'Titulo_Modulo')).'</td>						   			
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
																   <td  colspan="2" width="84%" style="text-align:justify">
																   - O valor de limite de crédito máximo referente da cláusula 1 deste módulo é de '.$ext.' '.$b404NivelMax.' ('.utf8_encode($b404NivelMaxExt).').<br>
																   - A percentagem segurada para estes compradores é de '.$mod_b404.'% (ICMS, IPI e ISS incluídos).</td>
															  </tr>
															  <tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%" style="text-align:justify">
																	O nome do Gerente de crédito é <strong>'.utf8_encode($GerenteNome).'.</strong><br>
																	O procedimento de gerenciamento de crédito está anexado ao presente contrato.
															  </tr>
															  
															  <tr>
																<td colspan="4">&nbsp;</td>
															  </tr> ';
														
															
														}else if(odbc_result($mod,'Cod_Modulo') == "B12.02"){
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
																			$empre .= '<tr><td colspan="2"><b>'.utf8_encode($no_razao_social).'</b></td></tr>
																					   <tr><td><b>CNPJ   : </b>'.$nu_cnpj.'</td><td><b>IE:</b> '.$nu_inscricao_estadual.'</td></tr>
																					   <tr><td colspan="2"><b>Endereço: </b>'.utf8_encode($ds_endereco).', '.$nu_endereco. ($ds_complemento != "" ? " - ".utf8_encode($ds_complemento) : "").'</td></tr>
																					   <tr><td colspan="1"><b>Cep: </b>'.$nu_cep.'</td><td><b>Cidade: </b>'.utf8_encode($no_cidade).' - <b>UF: </b>'.$no_estado.'</b></td></tr>';
																			
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
																   <td  colspan="2" width="84%">- O nível máximo de crédito referente à cláusula 1 deste módulo é de '.$ext.' '.$b2604NivelMax.' ('.utf8_encode($b2604NivelMaxExt).').<br>
																   - A percentagem segurada referente à cláusula 1.2 deste módulo é de '.$mod_b2604.'% (ICMS, IPI e ISS incluídos).</td>
															 </tr>
															 <tr>
																<td colspan="4">&nbsp;</td>
															  </tr> ';
												
														}else if (odbc_result($mod,'Cod_Modulo') == "B28.01"){
												
															 $html .= '<tr>
																	   <td  colspan="1" width="8%">&nbsp;</td>
																	   <td  colspan="1" width="8%">&nbsp;</td>
																	   <td  colspan="2" width="84%">
																		 O nível máximo de crédito referente à cláusula 1 deste módulo é de  '.$ext.' '.$b2801NivelMax.' ('.utf8_encode($b2801NivelMaxExt).').<br/>
																		As empresas de informações comerciais referentes à cláusula 1.4 deste módulo são SERASA e SCI EQUIFAX.<br/>
																		A percentagem segurada referente à cláusula 1.6 deste módulo é de '.$b2801Perc.'% (ICMS, IPI e ISS incluídos).<br/><br/>
																		
																		Revoga-se parcialmente a redação da cláusula 1.4 do mesmo módulo, sendo a mesma substituída pelo texto abaixo:<br/><br/>
																		
																		"1.4. Na ocasião da data de faturamento da mercadoria ou prestação de serviços, o SEGURADO deverá dispor de informações provenientes de Empresas de Informações Comerciais aprovadas pela Seguradora, atualizadas há menos de 2 (dois) meses, que mostrem que o Comprador não apresenta existência de qualquer restrição ou apontamento."<br/><br/>
																		
																		Inclui-se na Cláusula 1-ÁREA DE LIMITE DE CRÉDITO DISCRICIONÁRIO, deste mesmo Módulo o item 1.7, com a seguinte redação:<br></br/> 
																		
																		1.7 – O SEGURADO não poderá conceder crédito  a comprador que, anteriormente ao faturamento da mercadoria ou prestação de serviço, tenha sido objeto de recusa total, redução ou cancelamento de Limite de Crédito por parte da SEGURADORA, na vigência de qualquer apólice emitida pela SEGURADORA a favor do SEGURADO."   
						
																	   </td>
																 </tr>
																 <tr>
																 <td colspan="4">&nbsp;</td>
																 </tr>';
																 
														}else if(odbc_result($mod,'Cod_Modulo') == "D1.01"){										    
																 $html .= '<tr>
																	   <td  colspan="1" width="8%">&nbsp;</td>
																	   <td  colspan="1" width="8%">&nbsp;</td>
																	   <td  colspan="2" width="84%">O limite mínimo para <b>notificação</b> de <b> ameaça de sinistro é de </b>'.$ext.' '.number_format($nas,2,',','.').' ('.utf8_encode($extnas).')</td>
																	   </tr>';
															
														}else if(odbc_result($mod,'Cod_Modulo') == "D6.02"){
															 $html .= '<tr>
																	   <td  colspan="1" width="8%">&nbsp;</td>
																	   <td  colspan="1" width="8%">&nbsp;</td>
																	   <td  colspan="2" width="84%">'.$d602.'</td>
																	 </tr>';
																
														}else if(odbc_result($mod,'Cod_Modulo') == "D7.01"){
															$html .= '<tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%">'. utf8_encode($d701).'</td>
															 </tr>';	
														}else if (odbc_result($mod,'Cod_Modulo') == "F3.05"){
																 if ($numParc == 1){
																	 $html .= ' <tr>
																			   <td  colspan="1" width="8%">&nbsp;</td>
																			   <td  colspan="1" width="8%">&nbsp;</td>
																			   <td  colspan="2" width="84%" style="text-align:justify">
																				  O período de declaração é '.$tipo. '<br>
																					A forma de declaração é volume total de negócios aberto por número 
																					de fatura comercial, importador e valor.<br>
																					O prêmio mínimo será pago em 01 parcela à vista.</td>
																					
																			   </tr>';
																				   
																	
																  }else{
																	 $html .= '<tr>
																			   <td  colspan="1" width="8%">&nbsp;</td>
																			   <td  colspan="1" width="8%">&nbsp;</td>
																			   <td  colspan="2" width="84%" style="text-align:justify">
																				  O período de declaração é '.$tipo.'.<br>
																					A forma de declaração é volume total de negócios aberto por número 
																					de fatura comercial, importador e valor.<br>
																					O prêmio mínimo será pago em '.$numParc.' parcelas iguais e '.$periodo1.'
																			   </td>
																			   </tr>';
													
																	
													
																  }
															
														}else if(odbc_result($mod,'Cod_Modulo') == "F9.02"){
															  $html .= '<tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%">'.$bonus.'</td>
																 </tr>';	
														}else if (odbc_result($mod,'Cod_Modulo') == "F33.01"){
															  $html .= '
																		<tr>
																		   <td  colspan="1" width="8%">&nbsp;</td>
																		   <td  colspan="1" width="8%">&nbsp;</td>
																		   <td  colspan="2" width="84%">';
																				   if( $taxa_analise > 0)
																					   $html .= 'A tarifa de análise cadastral é de '.$extAnalise.' '. $taxa_analise.' ('.strtolower(utf8_encode($extAnalise)).')<br>';
																				   if($taxa_monit > 0)
																					   $html .= 'A tarifa de monitoramento cadastral é de '.$extMonit.' '. $taxa_monit.' ('.strtolower(utf8_encode($extMonit)).')';
																		
																		$html .= '</td>
																		</tr>';
														}else if(odbc_result($mod,'Cod_Modulo') == "F13.02"){
															   $html .= '<tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%">A percentagem referente ao item a deste módulo é de '. number_format($perPart0,2,',','.') .'% ('.utf8_encode($extpart0).')<br>
																		   A percentagem referente ao item b deste módulo é de '. number_format($perPart1,2,',','.') .'% ('.utf8_encode($extpart1).')</td>
																 </tr>  ';
														}else if(odbc_result($mod,'Cod_Modulo') == "F14.02"){
															  $html .= '<tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%">A percentagem referente ao item a deste módulo é de '. number_format($perPart0,2,',','.') .'% ('.utf8_encode($extpart0).')<br>
																		   A percentagem referente ao item b deste módulo é de '. number_format($perPart1,2,',','.') .'% ('.utf8_encode($extpart1).')</td>
																 </tr>  ';
														}else if(odbc_result($mod,'Cod_Modulo') == "F15.02"){
															   $html .= '<tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%">A percentagem referente ao item a deste módulo é de '. number_format($perPart0,2,',','.') .'% ('.utf8_encode($extpart0).')<br>
																		   A percentagem referente ao item b deste módulo é de '. number_format($perPart1,2,',','.') .'% ('.utf8_encode($extpart1).')</td>
																 </tr>  ';
														}
														
														if (odbc_result($mod,'Desc_Modulo') != ''){
															$html .= '
																 <tr>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="1" width="8%">&nbsp;</td>
																   <td  colspan="2" width="84%">
																   <div id="cobtexto"><strong>Derroga&ccedil;&otilde;es:</strong>&nbsp;'.utf8_encode($descricao_Modulo).'</div>
																   </td>
																</tr>';
															
														}
														$html .= '<tr>
																	<td colspan="4">&nbsp;</td>
																 </tr>';
											
											  } /// Fim dos modulos
								  
								  
											
						
								$countNumber = 10; 
								   
								  $html .= '</table>
												  <br>
												 <table width="100%" border="0" style="font-size: 12pt;">'; 
												 
												 
												 
								   if($riscopolitico != ''){			
										$html .= '
										<tr>
											<td colspan="4">&nbsp;</td>
										</tr>
							
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
											<td  colspan="3" width="92%" style="font-weight:bold">RISCO POLÍTICO</td>
										</tr>';
							
										$html .= '
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td  colspan="3" width="92%" style="text-align:justify;">
												10.1 - Consideram-se riscos políticos cobertos pela apólice a ocorrência dos atos ou fatos  seguintes:		
											</td>
										</tr>
										<tr>
											<td colspan="1" width="8%" style="text-align:justify;">&nbsp;</td>
											<td colspan="3" width="84%" style="text-align:justify;">
												<br>a-&nbsp;Inadimplência do importador empresa pública.					
												<br><br>b-Guerra, declarada ou não, no país do importador, com exceção de guerra, declarada ou não, entre dois ou mais dos seguintes países: França, República Popular da China, Rússia, Reino Unido e Estados Unidos da América.
												<br><br>c-Moratória estabelecida em caráter geral no país do importador e mais genericamente qualquer decisão ou ato normativo decretado pelo Governo do país onde está domiciliado o importador proibindo ou exonerando este último do pagamento do débito com o Segurado.
												<br><br>d-Promulgação de lei (ou de regulação com força de lei) no pais de domicílio do importador interditando importação de bens ou execução de serviços.
												<br><br>e-Evento de não transferência de divisas decretado pelo país do importador que impeçam o repasse do valor depositado por este último em banco oficial dentro do seu país, tendo o importador efetuado todas as formalidades requeridas para a transferência.
											</td>
										</tr>		
										<tr>
											<td  colspan="1" width="8%" style="font-weight:bold">&nbsp;</td>
											<td colspan="3" width="92%" style="text-align:justify;">
												<br><br>10.2 - A cobertura de Risco Político da apólice não abrange o(s) país(es) excluído(s) no item 2.2 (b)  deste instrumento.
											</td>
										</tr>	';
								        $countNumber++;
							
								  }				 			 
								   
								  if ($exibe_franq == 1){
									   $html .= '						
												 <tr>
													 <td colspan="4">&nbsp;</td>
												  </tr>
												  <tr>
													 <td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
													 <td  colspan="3" width="92%" style="font-weight:bold">FRANQUIA ANUAL GLOBAL</td>
												  </tr>
												 
												 <tr>
												   <td colspan="1" width="8%" style="text-align:justify;">&nbsp;</td>
												   <td  colspan="3" width="92%" style="text-align:justify;">
												   A franquia anual global é de '.$ext.' '. $franquia_anual.'<br>
													 O SEGURADO será responsável e manterá por sua conta uma franquia anual global.<br>
													 Nesta medida, não serão pagas quaisquer indenizações enquanto o montante total 
													 das indenizações devidas, resultantes de <b>notificações</b> pelo SEGURADO, 
													 relacionadas com <b>créditos</b> correspondentes de um determinado período de 
													 seguro, não exceder o montante correspondente à franquia anual global.
													</td>
												 </tr>	';
									   $countNumber++;
							
								  }
						
								  if($exibe_ad == 1){
									  $html .= '<tr>
													<td colspan="4">&nbsp;</td>
												</tr>
												 <tr>
													 <td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
													 <td  colspan="3" width="92%" style="font-weight:bold">ADEQUAÇÃO DE PRÊMIO</td>
												 </tr>
												 
												 <tr>
												    <td colspan="1" width="8%" style="text-align:justify;">&nbsp;</td>
												   <td  colspan="3" width="92%" style="text-align:justify;">
												   Caso o valor das indenizações pagas durante o período de seguro superar a 
												 percentagem de sinistralidade de '.$ad_sinistr.'% do <b>prêmio</b> pago correspondente 
												 ao mesmo período de seguro, um <b>prêmio</b> complementar será faturado.<br>
												 Este <b>prêmio</b> complementar será calculado retroativamente ao início da 
												 apólice, aplicando uma taxa de prêmio de adequação de '.$ad_premio.'% sobre 
												 a taxa de prêmio mencionada na proposta, multiplicada pelo faturamento 
												 segurável realizado durante todo o período de seguro.<br>A aplicação da taxa 
												 retroativamente não impedirá uma eventual revisão da taxa para período de 
												 seguro seguinte.
												 </td>
												 </tr>';
									   $countNumber++;
							
								  }
							
						
								 // Condição Especial de Cobertura de Coligadas 
								 if($temempcol == 1){
									  $html .= '<tr>
														<td colspan="4">&nbsp;</td>
												</tr>
												<tr>
													 <td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
													 <td  colspan="3" width="92%" style="font-weight:bold">EXTENSÃO DE COBERTURA PARA OPERAÇÕES REALIZADAS ATRAVÉS<br>  DE COLIGADAS NO EXTERIOR</td>
												</tr> 
												<tr>
												   <td colspan="1" width="8%" style="text-align:justify;">&nbsp;</td>
												   <td  colspan="3" width="92%" style="text-align:justify;">
												   '.utf8_encode($condespcol).'
												</td>
												</tr>';
									
							          $countNumber++;
								  }// exbibe as condições especiais
								  
							  
								  if($exibe_cond == 1){
									  $html .= '<tr>
														<td colspan="4">&nbsp;</td>
												</tr>
												 <tr>
													 <td  colspan="1" width="8%" style="font-weight:bold">'. $countNumber .'.</td>
													 <td  colspan="3" width="92%" style="font-weight:bold">CONDIÇÕES COMPLEMENTARES</td>
												</tr> 
												<tr><td colspan="1" width="8%" style="text-align:justify;">&nbsp;</td>												   
												   <td  colspan="3" width="92%" style="text-align:justify;">
												   '.utf8_encode($condicoes_especiais).'
												</td>
												</tr>';
								        $countNumber++;
								  }
								  
						
								  $html .= '</table>
										
										
										
								 
										</body>
								</html>';
									  
									 
				  
			 }else if($i_Produto == 1){ // produto interno
			 
        $html_pdf = '<html>
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
				#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
				
				</style>
				
				</head>
				<body>
      		<htmlpageheader name="myheader2">
					</htmlpageheader>

					<htmlpageheader name="myheader">
						<div height="20">
						</div><br>
						<div style="text-align: left;">
						    <span style="font-weight: bold; font-size: 9pt;">Condições Particulares - Seguro de Crédito Interno</span>
						 </div>	
						 <div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; "></div>
						 <div style="text-align: left;"><span style="font-style:italic; font-size: 8pt;">'. $dados['SubProduto'] .'</span></div>
					</htmlpageheader>
						
					<htmlpagefooter name="myfooter">
						<table width="100%" border="0">
							<tr>
								<td width="22%">&nbsp;</td>
								<td width="56%" style="text-align:center; font-size: 8pt;">
										Página {PAGENO} de {nb}
										<br><br>

										'.$enderecoEmp.'<br>
										'.$siteEmp.'

								</td>
	
								<td width="22%">&nbsp;</td>
							</tr>
						</table>
							
					</htmlpagefooter>
					<sethtmlpageheader name="myheader2" value="on" show-this-page="1" />
					<sethtmlpageheader name="myheader" value="on" show-this-page="0" />
					<sethtmlpagefooter name="myfooter" value="on" />
  					<div style="text-align: left;">
						  <span style="font-weight: bold; font-size: 10pt;">Condições Particulares - Seguro de Crédito Interno</span>
	    			</div>	
						<div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; ">
							<div style="text-align: left;"><span style="font-style:italic; font-size: 8pt;">'. $dados['SubProduto'] .'</span></div>
							<div style="text-align: left;">
								<span style="font-style:italic; font-size: 7pt;">RISCO COMERCIAL</span>
							<div>
							<div style="text-align: right;">
								<span style="font-style:italic; font-size: 7pt;">APÓLICE NÚMERO : <strong>'.$apoNum.'</strong></span>
							<div>
							<div style="text-align: right;">
								<span style="font-style:italic; font-size: 8pt;">VIGÊNCIA: '.$VigIni.' - '.$VigFim.'</span>
							<div>
						</div>

						<br>';

					$html = '
						<div style="text-align: left;">
							<span style="font-weight: bold; font-size: 12pt;"><u>DADOS DO SEGURADO:</u></span><br>
						</div>
						<table width="100%" border="0" style="font-size: 12pt;">			
						<tr>
						   <td width="25%">Razão Social: </td><td width="75%"><span style="font-weight: bold; font-size: 12pt;">'.strtoupper(trim($name)).'</span></td>
						</tr>
						<tr>
						  <td width="25%">Endereço: </td><td width="75%">'.$endcompleto.'</td>
						</tr> 
						<tr>
						  <td width="25%">&nbsp;</td><td width="75%">'.$city .' - '. $uf.'</td>
						</tr>
						<tr>  
						  <td width="25%">&nbsp;</td><td width="75%">CEP '.$cep.'</td>
						</tr>
						<tr>
						  <td width="25%">CNPJ: </td><td width="75%">'. formata_string('CNPJ',$cnpj).'</td>			
						</tr>
						<tr>
						  <td width="25%">Corretor nomeado: </td><td width="75%">'.($MultiCorretor != ''? $MultiCorretor: $corretor).'</td>			
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
								<td  colspan="1" width="8%">&nbsp;</td>
							   <td colspan="2" width="92%">'.$descrip.'</td>			
							</tr>
							
							<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="1" width="8%" style="font-weight:bold">1.2</td>
						   <td  colspan="1" width="84%" style="font-weight:bold">PRÊMIO MÍNIMO</td>			
					    </tr>
						<tr>
					       <td colspan="1" width="8%">&nbsp;</td>
						   <td colspan="2" width="92%">'.$ext . number_format($pr, 2, ',', '.'). ' ('.strtolower($valExt).') por período de seguro (IOF de 7,38% não incluso).</td>					
					    </tr>						
						<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="1" width="8%" style="font-weight:bold">1.3</td>
						   <td  colspan="1" width="84%" style="font-weight:bold">PERCENTAGEM SEGURADA</td>			
					    </tr>
						<tr>
					       <td colspan="1" width="8%">&nbsp;</td>
						   <td colspan="2" width="92%">'.round($cobertura).'% (ICMS, IPI, ISS e demais tributos incluídos no valor total da nota fiscal de venda ou serviço prestado).</td>			
					    </tr>
						
						<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="1" width="8%" style="font-weight:bold">1.4</td>
						   <td  colspan="1" width="84%" style="font-weight:bold">TAXA DE PRÊMIO</td>			
					    </tr>
						<tr>
					       <td colspan="1" width="8%">&nbsp;</td>
						   <td colspan="2" width="92%">'.number_format($tx,4,",",".").'%  aplicável ao faturamento (ICMS, IPI, ISS e demais tributos incluídos no valor total da nota fiscal de venda ou serviço prestado).</td>			
					    </tr>
						<tr>
						 <td colspan="3">&nbsp;</td>
						</tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">2.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">LIMITE MÁXIMO DE INDENIZAÇÃO</td>			
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%">'.$limPagIndeniz .' vezes o prêmio pago por período de seguro.</td>
										
							</tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">3.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">PRAZO MÁXIMO DE CRÉDITO</td>			
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%">'.$periodoMaxCredito.' dias contados a partir da data da emissão da fatura da mercadoria vendida ou serviço prestado.</td>
										
							</tr>
							
							
							<tr>
					       <td colspan="1" width="8%" style="font-weight:bold">4.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">PRAZO MÁXIMO PARA EMISSÃO DA NOTA FISCAL</td>			
					    </tr>
						<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="2" width="92%">
						             Não é aplicável para as vendas de mercadorias<br>';
						             

							/*if($PrazoMaxEmiNota > 0){
								$html .= 'Prestação de serviços: '.$PrazoMaxEmiNota.' dias, contado a partir da data da prestação do serviço para qual o pagamento seja devido. (somente quando tem serviços contratados)';
							}					*/             
						        
						   $html .= '</td>
						   			
					    </tr>	
				        
						<tr>
						 <td colspan="3">&nbsp;</td>
						</tr>
						<tr>
					       <td colspan="1" width="8%" style="font-weight:bold">5.</td>
						   <td colspan="2" width="92%" style="font-weight:bold">PRAZO PARA NOTIFICAR A AMEAÇA DE SINISTRO</td>			
					    </tr>
						<tr>
					       <td  colspan="1" width="8%">&nbsp;</td>
						   <td  colspan="2" width="92%">'. ($periodoMaxCredito + 30).' dias contados a partir da data da emissão da fatura da mercadoria vendida ou serviço prestado. <br>
                              No caso de prorrogação da data de vencimento, dentro das condições constantes no item 2.2.3 da cláusula 2 das CONDIÇÕES GERAIS, o prazo é de 30 dias contados do novo vencimento.
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
							   <td  colspan="2" width="92%"><div id="cobtexto">A moeda da apólice é o '. $extmoeda.'</div></td>
										
							</tr>
							
							
						';
							$countNumber = 7; 
							
						
							$html .= '<tr>
							 <td colspan="3">&nbsp;</td>
							</tr>
							
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">'.($countNumber++).'.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">VIGÊNCIA DA APÓLICE</td>			
							</tr>';
							
							   
						   // verificações 
							   $sqlvig = "select * from Periodo_Vigencia where i_Inform = ".$idInform. " order by n_Preriodo";
							   $rres = odbc_exec($db, $sqlvig);
							   $num  = odbc_num_rows($rres);
							   
							   if($num > 1){
							   	
							   	$ssqx = "select MIN(d_Vigencia_Inicial)as Ini, MAX(d_Vigencia_Final) as Fim from Periodo_Vigencia where i_Inform =".$idInform. "";
							   	$rresx = odbc_exec($db, $ssqx);
							   	$html .= '<tr>
							   	<td colspan="1" width="8%">&nbsp;</td>
							   	<td colspan="2" width="92%" style="text-align:justify">
							   	A apólice entra em vigor no dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Ini'),0,10)).' e terá validade até o dia '.Convert_Data_Geral(substr(odbc_result($rresx,'Fim'),0,10)).', cujo período equivale ao período de seguro.
							   	Este período de seguro é dividido em dois períodos distintos de vigência compreendidos entre:<br>';
							   	
							   	/*while(odbc_fetch_row($rres)){
							   		$valorext = $numberExtensive->extensive(number_format(odbc_result($rres,'v_Premio'),0,'.',''),$fMoeda);
							   		$html .= 'Período '.odbc_result($rres,'n_Preriodo').' – '.Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Inicial'),0,10)) . ' a ' . Convert_Data_Geral(substr(odbc_result($rres,'d_Vigencia_Final'),0,10)) .'<br>';
							   	}*/
							   	
							   	
										if($renovacao_Tacica == 1){ // cmapo no inform
											$html .= '	 
											<br>
											A apólice poderá ser renovada automaticamente, por igual período de seguro, caso não seja comunicada a intenção de não renovação por qualquer das partes, devendo tal comunicação de não renovação ser feita por escrito pelo SEGURADO ou pela SEGURADORA com antecedência de 60 dias do termo final do período de seguro total. 
											<br><br>
											Revoga-se o item 12.2 das Condições Gerais da apólice o qual passará a vigorar com a seguinte redação:
											</td>			
											</tr>
											';
										}else{
											$html .= '	 
											<br>
											"A apólice tem duração definida nas CONDIÇÕES PARTICULARES e não pode ser renovada tacitamente."
											</td>			
											</tr>
											';
											
										}
									}else{				   
										
										$html .= '<tr>
										<td  colspan="1" width="8%">&nbsp;</td>
										<td  colspan="2" width="92%" style="text-align:justify">'. ($Periodo_Vigencia ? $Periodo_Vigencia : ($pvigencia == 1? 12 : 24)) .' meses a partir da data de aceitação da presente PROPOSTA DE SEGURO.<br>';
										
										if($renovacao_Tacica ==1){ // campo no inform
											$html .= '	 
											<br>
											A apólice poderá ser renovada automaticamente, por igual período de seguro, caso não seja comunicada a intenção de não renovação por qualquer das partes, devendo tal comunicação de não renovação ser feita por escrito pelo SEGURADO ou pela SEGURADORA com antecedência de 60 dias do termo final do período de seguro total. 
											<br><br>
											Revoga-se o item 12.2 das Condições Gerais da apólice o qual passará a vigorar com a seguinte redação:
											</td>			
											</tr>
											';
										}else{
											$html .= '	 
											<br>
											"A apólice tem duração definida nas CONDIÇÕES PARTICULARES e não pode ser renovada tacitamente."
											</td>			
											</tr>
											';
											
										}
										
									}
							
							$html .= '							
							<tr>
							
							 <td colspan="3">&nbsp;</td>
							</tr>
							<tr>
							   <td colspan="1" width="8%" style="font-weight:bold">'.($countNumber++).'.</td>
							   <td colspan="2" width="92%" style="font-weight:bold">MÓDULOS</td>			
							</tr>
							<tr>
							   <td  colspan="1" width="8%">&nbsp;</td>
							   <td  colspan="2" width="92%">Os seguintes módulos fazem parte integrante deste contrato:</td>						   			
							</tr>
							</table>';
												
							
			       $html .= '<table width="100%" border="0" style="font-size: 12pt;"> ';
				   
				                 						
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
										   <td  colspan="1" width="8%" style="font-weight:bold">'.odbc_result($mod,'Cod_Modulo').'</td>
										   <td  colspan="1" width="76%" style="font-weight:bold">'.odbc_result($mod,'Titulo_Modulo').'</td>						   			
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
											   <td  colspan="2" width="84%">
											   - O valor de limite de crédito máximo referente da cláusula 1 deste módulo é de R$ '.$b404NivelMax.' ('.$b404NivelMaxExt.').<br>
											   - A percentagem segurada para estes compradores é de '.$mod_b404.'% (ICMS, IPI e ISS incluídos).</td>
										  </tr>
										  <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%">
										        O nome do Gerente de crédito é <strong>'.$GerenteNome.'.</strong><br>
                                                O procedimento de gerenciamento de crédito está anexado ao presente contrato.
										  </tr>
										  
										  <tr>
										    <td colspan="4">&nbsp;</td>
									      </tr> ';
										
									}else if (odbc_result($mod,'Cod_Modulo') == "B8.02"){
										
									    										 
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
										 $html .= ' <tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%">A franquia de maiores compradores mencionada no item 1.1 deste módulo é de R$ '.$mod_b904.' ('.$mod_b904Ext.').</td>
												  </tr>
												  <tr>
													 <td colspan="4">&nbsp;</td>
												   </tr>';
										
									}else if(odbc_result($mod,'Cod_Modulo') == "B12.02"){
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
																   <tr><td colspan="1"><b>Cep: </b>'.$nu_cep.'</td><td></b>Cidade: </b>'.$no_cidade.' - <b>UF: </b>'.$no_estado.'</b></td></tr>';
														
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
											   <td  colspan="2" width="84%">- O nível máximo de crédito referente à cláusula 1 deste módulo é de R$ '.$b2604NivelMax.' ('.$b2604NivelMaxExt.').
											   <br>
											   <br>
											   - A percentagem segurada referente à cláusula 1.2 deste módulo é de '.$mod_b2604.'%.</td>
										 </tr>
										 <tr>
										    <td colspan="4">&nbsp;</td>
									      </tr> ';
							
						      } else if (odbc_result($mod,'Cod_Modulo') == "B28.01"){
						    
							             $html .= '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%">
													 O nível máximo de crédito referente à cláusula 1 deste módulo é de  R$ '.$b2801NivelMax.' ('.$b2801NivelMaxExt.').<br/>
													As empresas de informações comerciais referentes à cláusula 1.4 deste módulo são SERASA e SCI EQUIFAX.<br/>
													A percentagem segurada referente à cláusula 1.6 deste módulo é de '.$b2801Perc.'% (ICMS, IPI e ISS incluídos).<br/><br/>
													
													Revoga-se parcialmente a redação da cláusula 1.4 do mesmo módulo, sendo a mesma substituída pelo texto abaixo:<br/><br/>
													
													“1.4. Na ocasião da data de faturamento da mercadoria ou prestação de serviços, o SEGURADO deverá dispor de informações provenientes de Empresas de Informações Comerciais aprovadas pela Seguradora, atualizadas há menos de 2 (dois) meses, que mostrem que o Comprador não apresenta existência de qualquer restrição ou apontamento.”<br/><br/>
													
													Inclui-se na Cláusula 1-ÁREA DE LIMITE DE CRÉDITO DISCRICIONÁRIO, deste mesmo Módulo o item 1.7, com a seguinte redação:<br></br/> 
													
													1.7 – O SEGURADO não poderá conceder crédito  a comprador que, anteriormente ao faturamento da mercadoria ou prestação de serviço, tenha sido objeto de recusa total, redução ou cancelamento de Limite de Crédito por parte da SEGURADORA, na vigência de qualquer apólice emitida pela SEGURADORA a favor do SEGURADO.”   
	
												   </td>
											 </tr>
											 <tr>
											 <td colspan="4">&nbsp;</td>
											 </tr>';
												
									}else if(odbc_result($mod,'Cod_Modulo') == "D1.01"){
										    
											 $html .= ' <tr>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="2" width="84%">O limite mínimo para notificação de ameaça de sinistro é de '.$extnas.'</td>
											           </tr>';
									
									    
									
									}else if(odbc_result($mod,'Cod_Modulo') == "D2.01"){	
										$html .= '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%">A franquia anual global é de '.$ext.' '. $d201.' ('.$valorExtD201.')<br></td>
										 </tr>';
									
									}else if(odbc_result($mod,'Cod_Modulo') == "D4.01"){
										 $html .= '<tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%">A franquia anual global é de '.$ext.' '. $franquia_anual.'<br></td>
									      </tr>';
										
									}else if(odbc_result($mod,'Cod_Modulo') == "D6.02"){
										 $html .= '<tr>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="1" width="8%">&nbsp;</td>
										   <td  colspan="2" width="84%">'.$d602.'</td>
										    </tr>';
											
									}else if(odbc_result($mod,'Cod_Modulo') == "D7.01"){
									    $html .= '<tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%">'.$d701.'</td>
									     </tr>';	
										 
									}else if (odbc_result($mod,'Cod_Modulo') == "F3.05"){
									        if ($numParc == 1){
												 $html .= ' <tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%">
															  <div id="cobtexto">O período de declaração é '.$periodo.'<br>
																A forma de declaração é em arquivo Excel contendo o volume total de negócios aberto por comprador.<br/>
																O prêmio mínimo será pago em 01 parcela à vista.</div></td>
																
														   </tr>';
															   
												
											  }else{
												 $html .= '<tr>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="1" width="8%">&nbsp;</td>
														   <td  colspan="2" width="84%">
														   <div id="cobtexto">O período de declaração é '.$periodo.'.<br>
																A forma de declaração é em arquivo Excel contendo o volume total de negócios aberto por comprador.<br/> 
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
											   <td  colspan="2" width="84%">'.$bonus.'</td>
											 </tr>';	
									}else if (odbc_result($mod,'Cod_Modulo') == "F13.02"){
									    $html .= '<tr>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="1" width="8%">&nbsp;</td>
												   <td  colspan="2" width="84%">A percentagem referente ao item a deste módulo é de '.round($perPart0).'%
														   <BR>A percentagem referente ao item b deste módulo é de '.round($perPart1).'%</td>
												 </tr> '; 
												
									}else if (odbc_result($mod,'Cod_Modulo') == "F33.01"){
										  $html .= '
													<tr>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="1" width="8%">&nbsp;</td>
													   <td  colspan="2" width="84%">A tarifa de análise cadastral é de '.$ext.' '. $taxa_analise.' ('.strtolower($extAnalise).')<br>
															   A tarifa de monitoramento cadastral é de '.$ext.' '. $taxa_monit.' ('.strtolower($extMonit).')
														</td>
													</tr>';
												  
									}else if (odbc_result($mod,'Cod_Modulo') == "F37.02"){						  
							  
										  $html .= '										 
											 <tr>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="1" width="8%">&nbsp;</td>
											   <td  colspan="2" width="84%">A forma de notificação é volume total de negócios aberto por nota fiscal.<br/>O período de declaração é mensal.</td>
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
												'. nl2br($descricao_Modulo).'
												</td>
											</tr>';
										
									}
									$html .= '<tr>
										        <td colspan="4">&nbsp;</td>
									         </tr>';
						
						} // Fim do módulos
				   
				   
			      
				   
				  // $html .= '</table>
				  
						//   <br>
						//   <table width="100%" border="0" style="font-size: 12pt;">
						// 	  <tr>
						// 		 <td  colspan="1" width="8%" style="font-weight:bold">'.($countNumber++).'.</td>
						// 		 <td  colspan="1" width="92%" style="font-weight:bold">OBRIGAÇÃO DE NOTIFICAÇÃO DE ATRASOS DE PAGAMENTO<td>
						// 	  </tr>';
						   
				  // $html .= '
						//   <tr>
						// 	  <td  colspan="2" width="100%" style="text-align:justify; font-size: 12pt;">
						// 		  Sem prejuízo das demais obrigações do contrato de seguro de crédito interno, o SEGURADO compromete-se a notificar a SEGURADORA de quaisquer dívidas vencidas e não pagas há mais de 60 dias 
						// 		  da data original de vencimento. Esta obrigação não se aplica se, para aquela dívida, uma <b>notificação</b> de <b>ameaça de sinistro</b> já tiver sido feita ou 
						// 		  para dívidas que já reúnam condições de uma <b>notificação</b> de <b>ameaça de sinistro</b> de acordo com  os termos do contrato de seguro.
						// 		  Caso o SEGURADO deixe de cumprir com esta obrigação, aplicar-se-á a cláusula 9.4 das CONDIÇÕES GERAIS tanto para as dívidas objeto de <b>notificação</b> de atraso de pagamento como para as dívidas correspondentes 
						// 		  a <b>entregas</b> ou prestações de serviço realizados após a devida data de obrigação de <b>notificação</b> de atraso de pagamento.
								 
						// 	  </td>
						//    </tr>';
						  $html .= '</table>';

						  $exibe_ad = 1;
			      	if($exibe_ad == 1){
			      		$ad_sinistr = (int)$ad_sinistr;
								$ad_premio = (int)$ad_premio;
			          	$html .= '
				    					      <table width="100%" border="0" style="font-size: 12pt;">
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
																	Caso o valor das indenizações pagas durante o período de seguro superar a percentagem de sinistralidade de '.$ad_sinistr.'% do prêmio emitido 
																	correspondente ao mesmo período de seguro, um prêmio complementar será faturado.<br><br>		          
												          Este prêmio complementar será calculado retroativamente ao início da apólice, aplicando o percentual de '.$ad_premio.'% ao prêmio emitido durante o 
												          período de seguro (excluído IOF - 7,38%).<br><br>
												          O faturamento e cobrança deste prêmio complementar não impedirão uma eventual revisão da taxa para período de seguro seguinte.<br>
																</td>
															</tr>';
															$countNumber++;
			      	}
						  $html .= '</table>';
								
					
					
	         
			 
						$html .= '</body>
						</html>';	
					
				      
			   	}
			    //$html =  mb_detect_encoding($html_pdf, 'UTF-8, ISO-8859-1');
				
				$html_pdf = $html_pdf.$html;

			    echo mb_convert_encoding($html_pdf,'ISO-8859-1','UTF-8'); 
				//echo mb_convert_encoding($html_pdf,'HTML-ENTITIES','UTF-8');
			   
			   // echo $html_pdf;
				
			   
			      ?>
			   
			   
				    
			</textarea>
	  		
            <?php
	   
             $final = $pdfDir. $key.'CondPart_alterada';
		     		 
	         ?>
             <input type="hidden" name="file_alterado" id="file_alterado" value="<?php echo $final;?>"/>
              
       </form>
	   
	   <br clear="all" />
	   <li class="campo2colunas">
             <button class="botaoagg" type="button" name="emitir" onClick="document.edit_cond.submit();">Emitir Condi&ccedil;&otilde;es</button>
        </li> 
    </div>
	
    <div style="clear:both">&nbsp;</div>
    <?php
         require_once("../../pdfConf.php");
	
	
    $prefix = $pdfDir. $key;
      if(file_exists($prefix. 'CondJuros.pdf')){  ?>
          <li class="campo2colunas" style="height:30px;">
             <div class="formdescricao"><span><a class=textoBold href="<?php echo $root.'download/'.$key;?>CondJuros.pdf" target=_blank>Condi&ccedil;&otilde;es Especiais Juros de Mora</a></span></div>
          </li> 
          <div style="clear:both; height:2px;">&nbsp;</div>
<?php }  ?>
    
    <li class="campo2colunas" style="height:30px;">
         <div class="formdescricao"><span><a class=textoBold href="<?php echo $root.'download/'.$key;?>Carta.pdf" target=_blank>Carta</a></span></div>
    </li>
    <div style="clear:both; height:2px;">&nbsp;</div>
    <li class="campo2colunas" style="height:30px;">    
        <div class="formdescricao"><span><a class=textoBold href="<?php echo $root."role/client/interf/geraFicha.php?idInform=".$idInform."&key=".$key?>" target=_blank>Ficha de Aprova&ccedil;&atilde;o de Limite de Cr&eacute;dito</a></span></div>
    </li>
    <div style="clear:both; height:2px;">&nbsp;</div>
<?php  
	If ($i_Produto != 1){ 
		if($num_parcelas >= 1){
		   	$i = 1;  ?>
		  	
			For($i; $i <= $num_parcelas; $i++){  ?>
				<li class="campo2colunas" style="height:30px;">
					<div class="formdescricao"><span><a href="<?php echo $root.'download/'.$key.'Parcela'.$i.'.pdf';?>" target=_blank>Parcela <?php echo $i;?></a></span></div>
				</li>
				
				<div style="clear:both; height:2px;">&nbsp;</div>
			<?php	  }   ?>
		   
		<?php  }else{   ?>
			<li class="campo2colunas" >
				<label>N&atilde;o h&aacute; mais parcelas a pagar.</label>
			</li> 
			
			<div style="clear:both; height:2px;">&nbsp;</div>   
		<?php  }
	}
  ?>


<?php
	$tipo_documento = "";
	include_once("../../../gerar_word/htmltodocx/word.php");
?>		

   <div style="clear:both"></div>
  
    <br clear="all" />
		  <li class="campo2colunas"> 
			  <label><a href="<?php echo $propWord; ?>" target="_blank">Condi&ccedil;&otilde;es Particulares Word</a></label>
		</li>
	<br clear="all" />

    <li class="campo2colunas" style="height:30px;">
         <div class="formdescricao"><span><a id="modal_upload" href="#">Upload Condi&ccedil;&otilde;es Particulares Original</a></span></div>
    </li>
		<div style="clear:both; height:2px;">&nbsp;</div>


        <form action="../policy/Policy.php" method="post" name="form">
            <input type=hidden name="comm" value="done">
            <input type=hidden name="idInform" value="<?php echo $idInform; ?>">
            <input type=hidden name="idNotification" value="<?php echo $idNotification; ?>">
            <div style="clear:both">&nbsp;</div>
            <li class="campo2colunas" style="height:30px;">
              <label><h2>Ap&oacute;lice emitida</h2></label>
            </li>
            <!--
            <div style="clear:both">&nbsp;</div>
            <li class="campo2colunas"><label style="color:#F00"><?php //echo ($msg);?></label>
            </li>
            -->
            <div style="clear:both">&nbsp;</div>
            <li class="campo2colunas">
                 <button class="botaoagm" type="button" name="mot" onClick="form.submit();">Voltar</button>
            </li>
            <div style="clear:both">&nbsp;</div>
            <div style="clear:both">&nbsp;</div>
        </form>

 </div>

<!-- UPLOAD -->
	<script language="javascript"  type="text/javascript" src="<?php echo $host;?>Scripts/jquery.form.js"></script>

	<script>
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

		  $("#terlinha").click(function(){
	      $("#textoCorpo_visualaid").removeClass('mceButton mceButtonEnabled mce_visualaid mceButtonActive');
		   	$("#textoCorpo_visualaid").addClass('mceButton mceButtonEnabled mce_visualaid');
	    });

	  });
	</script>

	<!-- Modal -->
		<div class="modal-ext" style="display:none">
			<div class="bg-black"></div>

			<div class='modal-int'>
			  <h1>Upload Condições Particulares Original</h1>
			  <div class="divisoriaamarelo"></div>

				<form id="form-upload" action="<?php echo $root;?>role/executive/upload_proposta.php" method="post" enctype="multipart/form-data">
					<input type="hidden" name="file_name" value="<?php echo $root.'download/'.$key;?>CondPart.pdf" />
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