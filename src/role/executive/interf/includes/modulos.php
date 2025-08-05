<!-- <script type="text/javascript" src="tiny_mce/tiny_mce.js"></script> -->
<!-- <script src="scripts_js/editor_html/wysihtml.min.js"></script> -->
<script src="<?php echo $root?>../Scripts/tinymce/tiny_mce/tiny_mce.js"></script>
<script>
	$(document).ready(function(){
		// var editor = new wysihtml5.Editor("descr_derrogacao2", {
	 //    toolbar:      "toolbar",
	 //    stylesheets:  "scripts_js/editor_html/css/stylesheet.css",
	 //    parserRules:  wysihtml5ParserRules
	 //  });
		var source_f401   = $("#entry-template-f401").html();
	  var template_f401 = Handlebars.compile(source_f401);

		var source_f5202   = $("#entry-template-f5202").html();
	  var template_f5202 = Handlebars.compile(source_f5202);

	  $("#gerar_f401").on("click", function(){  

	    var tipo = 'insert';
	    var id_inform = <?php echo $idInform; ?>;
	    var nivel_sin = $("#tb_f401_js").parent().find("#f401NivelSinistralidade").val();
	    var perc_premio = $("#tb_f401_js").parent().find("#f401PercPremio").val();
	    var html_h = '';

			$.ajax({
			  type: "POST",
			  url: '<?php echo $url_mod_f401; ?>',
			  data: {tipo: tipo, id_inform: id_inform, nivel_sin: nivel_sin, perc_premio: perc_premio},
			  success: function(data) {
	      	var context = {id_modulo_valor: data.IdModuloValor, Nivel_Sinistralidade: number_format_js(data.Nivel_Sinistralidade,2,',','.'), Percentagem_Premio: number_format_js(data.Percentagem_Premio,2,',','.')};
	      	html_h += template_f401(context);

	      	$("#f401_campos").append(html_h);
			  }
			})

	  })

	  $("#gerar_f5202").on("click", function(){  

	    var tipo = 'insert';
	    var id_inform = <?php echo $idInform; ?>;
	    var adeq_sin = $("#tb_f5202_js").parent().find("#f5202_adeq_sinist").val();
	    var adeq_premio = $("#tb_f5202_js").parent().find("#f5202_adeq_premio").val();
	    var html_h = '';

	  	if($("#f5202_campos").find("tr").length == 0){
	  		//alert(adeq_sin+' - '+adeq_premio);
	  		$("#ad_sinistr_hidden").val(adeq_sin);
	  		$("#ad_premio_hidden").val(adeq_premio);
	  	}

			$.ajax({
			  type: "POST",
			  url: '<?php echo $url_mod_f5202; ?>',
			  data: {tipo: tipo, id_inform: id_inform, adeq_sin: adeq_sin, adeq_premio: adeq_premio},
			  success: function(data) {
	      	var context = {id_modulo_valor: data.IdModuloValor, Adeq_Sinistralidade: number_format_js(data.Adeq_Sinistralidade,2,',','.'), Adeq_Premio: number_format_js(data.Adeq_Premio,2,',','.')};
	      	html_h += template_f5202(context);

	      	$("#f5202_campos").append(html_h);
			  }
			})

	  })

	  $("body").on("click", ".remove_f401", function(){

	    var tipo = 'delete';
	    var id_modulo_valor = $(this).parent().parent().find("#id_modulo_valor").val();

			$.ajax({
			  type: "POST",
			  url: '<?php echo $url_mod_f401; ?>',
			  data: {tipo: tipo, id_modulo_valor: id_modulo_valor}												  
			})

			$(this).parent().parent().remove();
	  })

	  $("body").on("click", ".remove_f5202", function(){

	    var tipo = 'delete';
	    var id_modulo_valor = $(this).parent().parent().find("#id_modulo_valor").val();

	  	if($("#f5202_campos").find("tr").length == 0){
	  		$("#ad_sinistr_hidden").val("0");
	  		$("#ad_premio_hidden").val("0");
	  	}

			$.ajax({
			  type: "POST",
			  url: '<?php echo $url_mod_f5202; ?>',
			  data: {tipo: tipo, id_modulo_valor: id_modulo_valor}												  
			})

			$(this).parent().parent().remove();
	  })

	  /*if($('.idModuloF52').is(':checked')){
			var html_adeq = $("#container_adeq_prem").clone().html();
			$("#container_adeq_prem_mod").html(html_adeq);
			$('#container_adeq_prem').html('');
	  } 

	  $('.idModuloF52').click(function() {	  	
	  	if($(this).is(':checked')){
  			var html_adeq = $("#container_adeq_prem").clone().html();
  			$("#container_adeq_prem_mod").html(html_adeq);
  			$('#container_adeq_prem').html('');
	  	} else{
  			var html_adeq_mod = $("#container_adeq_prem_mod").clone().html();
  			$("#container_adeq_prem").html(html_adeq_mod);
  			$('#container_adeq_prem_mod').html('');
	  	}
	  })*/


	})
</script>

<script id="entry-template-f401" type="text/x-handlebars-template">

  <tr class="f401_int" id="f401_int">
		<input type="hidden" id="id_modulo_valor" value="{{id_modulo_valor}}">														    
    <td>{{Nivel_Sinistralidade}}</td>
    <td>{{Percentagem_Premio}}</td> 
		<td>
			<a href="#" class="remove_f401"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a>
		</td>
  </tr>

</script>

<script id="entry-template-f5202" type="text/x-handlebars-template">

  <tr class="f5202_int" id="f5202_int">
		<input type="hidden" id="id_modulo_valor" value="{{id_modulo_valor}}">														    
    <td>{{Adeq_Sinistralidade}}</td>
    <td>{{Adeq_Premio}}</td> 
		<td>
			<a href="#" class="remove_f5202"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a>
		</td>
  </tr>

</script>

<?php 
	if ($adeaquacao != ""){
		$ativaAdeaquacao = "block";
		$chekAdequacao = 1;
	}else{
		$ativaAdeaquacao = "none";
		$chekAdequacao = 0;
	}
?>
														

<li class="barrabotoes campo3colunas" style="width:100%;">
	<label><h2>Par&acirc;metros de M&oacute;dulos Especiais</h2></label>
</li>
<div style="clear:both">&nbsp;</div>

<?php if(!$i_Sub_Produto){  ?>
<div style="clear:both">&nbsp;</div>
	<label style="color:#F00">Aten&ccedil;&atilde;o! Para prosseguir com o preenchimento de todos os m&oacute;dulos, voc&ecirc; deve selecionar o sub-produto.</label>
<div style="clear:both">&nbsp;</div>
<?php } ?>
<!-- Inicio Módulo dinâmico  -->
<?php
		
	// Condições que especificam a exibição do módulos
	// Verifica se o acesso tem permissão para visualizar  em envOfferBonus e Policcy.
	if ($role["envOfferBonus"]  || $role["policy"] ){  ?>  
	   <div class="divisoria01">&nbsp;</div>  
	  
	   <div id="exibir_modulos">
			<?php
				$qry_i_Sub_Produto = ($i_Sub_Produto ? 'IsNull(Inf.i_Sub_Produto, '.$i_Sub_Produto.')' : 'Inf.i_Sub_Produto');
			  //odbc_autocommit($db, true);
			  $qry = "Select 
						Inf.id,
						Inf.n_Apolice,
						Inf.name,
						M.i_Modulo as mod_id,
						M.Cod_Modulo,
						M.Grupo_Modulo,
						M.Titulo_Modulo,
						IM.Desc_Modulo,
						Case IsNull(IM.i_Modulo, 0)
							When 0 Then 0
							Else 1
						End AS ModuloSelecionado
					From 
						Inform Inf
					Inner Join Sub_Produto_Modulo SPM On
						SPM.i_Sub_Produto = ".$qry_i_Sub_Produto."
						
					Inner Join Modulo M On
						M.i_Modulo = SPM.i_Modulo
					Left Join Inform_Modulo IM On
						IM.idInform = Inf.id
						And IM.i_Modulo = SPM.i_Modulo
					Where
						Inf.id = $idInform
						And (M.s_Modulo = 0 Or IM.i_Modulo Is Not Null)
					Order By
						M.Ordem_Modulo";
							
				//$qry = "exec SPR_COF_Modulos ".$idInform. ", ".($i_Sub_Produto ? $i_Sub_Produto : 0);	
				//echo $qry;
			  $cur = odbc_exec($db,$qry);	?>
			
		  <!-- <script type="text/javascript" src="<?php echo $host;?>Scripts/tinymce/tiny_mce/tiny_mce.js"></script>-->
				
			<script type="text/javascript">

				if( typeof(tinyMCE) !== "undefined" ){
					 tinyMCE.init({

						// General options

						selector : "textarea.derrog",

						theme : "advanced",

						plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",

				

						// Theme options

						//theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",

										

						theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|formatselect,fontselect,fontsizeselect,undo,redo,|,preview,|,forecolor,backcolor",

						//theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
						theme_advanced_buttons2 : "",

						theme_advanced_buttons3 : "",

						theme_advanced_buttons4 : "",

						theme_advanced_toolbar_location : "top",

						theme_advanced_toolbar_align : "left",

						theme_advanced_statusbar_location : "bottom",

						theme_advanced_resizing : true,

				

						// Example word content CSS (should be your site CSS) this one removes paragraph margins

						content_css : "<?php echo $host;?>Scripts/tinymce/css/word.css",



						// Drop lists for link/image/media/template dialogs

						template_external_list_url : "<?php echo $host;?>Scripts/tinymce/lists/template_list.js",

						external_link_list_url : "<?php echo $host;?>Scripts/tinymce/lists/link_list.js",

						external_image_list_url : "<?php echo $host;?>Scripts/tinymce/lists/image_list.js",

						media_external_list_url : "<?php echo $host;?>Scripts/tinymce/lists/media_list.js",

				

						// Replace values for the template plugin

						template_replace_values : {

							username : "Some User",

							staffid : "991234"

						}

					});
				}

			</script>
			
	   	<script>
			  function exibir_b404(valor){	
				  
				  if(valor.checked == true) {
					  document.getElementById('exibir_b404').style.display = 'block';
				  }else {
					  document.getElementById('exibir_b404').style.display = 'none';
				  }
			
			  }
	   	</script>

		   
		   
				<div id="options">
					<a href="javascript:expandir_todos()" class="linktexto">Expandir todos</a> | 
					<a href="javascript:recolher_todos()" class="linktexto">Recolher todos</a> 
				</div>
				
				 <input type="hidden" name="per1" id="per1" value="<?php echo $perPart0;?>">
				 <input type="hidden" name="per2" id="per2" value="<?php  echo $perPart1;?>">
				
				<ul class="acc" id="acc">
				<?php
			   $nx = '';	   
			   $x =0;
			   $f = 0;
			   $vx = 0;
			   $j  = 0;
				while (odbc_fetch_row($cur)) {
					
					$i_Modulo            = odbc_result($cur,'mod_id');
					$Grupo_Modulo        = odbc_result($cur,'Grupo_Modulo'); 
					$Titulo_Modulo       = odbc_result($cur,'Titulo_Modulo');
					$Cod_Modulo          = odbc_result($cur,'Cod_Modulo');
					// $Desc_Modulo         = odbc_result($cur,'Desc_Modulo');
					$Desc_Modulo = ""; // MUDAR QUANDO FOR PRA PRODUCAO
					$ModuloSelecionado   = odbc_result($cur,'ModuloSelecionado');
						
									
					if($Grupo_Modulo != $nx){
						$nx = $Grupo_Modulo; 
						   
						if ($x >0){ 
							echo '</div>';                    
							echo '</li>';
						}	?>
							<li >
								 <h3 onClick="indiceAtiva(<?php echo $vx;?>);"><?php echo $Grupo_Modulo;?></h3>
								 <div class="acc-section" id="indice<?php echo $vx;?>" style="display:none;">
								  
						<?php $vx++;
						 } ?>  
									 
									  
										   
									 <div class="acc-content">
													
										 <div class="formopcaonew">
										 
											 <?php  
											 
																							   
													if($Cod_Modulo == 'B4.04'){ 
														 
											  ?>
														<input type="hidden" name="b404" id="b404"  value="1"> 
														<input type="checkbox" name="idModulo[]" id="idModuloB4.04"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_b404(this); verDerrogacao(this,<?php echo $j;?>);">
											<?php
													 }else if($Cod_Modulo == 'B8.02'){  
														$x = 0;
														$ter = 0;
														  
														  $query = "SELECT count(id) as id FROM ParModEsp_Maiores_Compradores WHERE idInform = ".$idInform;
														  $curj = odbc_exec ($db, $query);   
																 
														  if(odbc_result($curj,'id') > 0){
															   $b802ex = " checked ";
															   $b802   = 1;
															   $temComprador = odbc_result($curj,'id');
														  }else{
															  $b802ex = " ";
															  $temComprador = 0;
														  }
											 ?>												 
														<input type="checkbox" name="idModulo[]" id="idModuloB8.02"  <?php echo (($ModuloSelecionado > 0 || $temComprador >0) ? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_Cobertura(this); verDerrogacao(this,<?php echo $j;?>);">
														<input type="hidden" name="b802" id="b802"  value="1">
														
														
											 <?php  }else if($Cod_Modulo == 'B9.04'){   									               
											 ?>
														<input type="hidden" name="b904" id="b904"  value="1"> 
														<input type="checkbox" name="idModulo[]" id="idModuloB9.04"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_b904(this); verDerrogacao(this,<?php echo $j;?>);">
											 
											 <?php  }else if($Cod_Modulo == 'B12.02'){   
														 $query = "SELECT count(idInform) as id FROM ParModEsp_Empresa WHERE idInform = ".$idInform;
														 $curxy = odbc_exec($db, $query); 
														   if(odbc_result($curxy,'id') > 0){
															   $ModB1202 = 1;
														   }else{
															   $ModB1202 = 0;
														   }
											 ?>
														<input type="hidden" name="b1202" id="b1202" value="1">
														
														<input type="checkbox" name="idModulo[]" id="idModuloB12.02"  <?php echo ($ModB1202 > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_dados(this); verDerrogacao(this,<?php echo $j;?>);"> 
														
											 <?php  }else if($Cod_Modulo == 'B26.04'){ ?>
														<input type="checkbox" name="idModulo[]" id="idModuloB26.04"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_b2604(this); verDerrogacao(this,<?php echo $j;?>);">
											 <?php  }else if($Cod_Modulo == 'B28.01'){  ?>
														 <input type="checkbox" name="idModulo[]" id="idModuloB28.01"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_b2801(this); verDerrogacao(this,<?php echo $j;?>);">
											 <?php  }else if($Cod_Modulo == 'D2.01'){ ?>
														 <input type="hidden" name="d201" id="d201"   value="1"> 
														 <input type="checkbox" name="idModulo[]" id="idModuloD2.01"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_d201(this); verDerrogacao(this,<?php echo $j;?>);">
											
											<?php   }else if($Cod_Modulo == 'D4.01'){  ?>                                            
<!--                                                           <li  class="campo3colunas">
																<div class="formopcao">
																			<input type="checkbox" name="Adfranq" id="Adfranq" value="<?php  echo $chekfranq;?>" <?php echo $franquia;?> onClick="javascript: visualizafranq();" >
																	</div>
														
																	<div class="formdescricao"><span>Franquia Anual Global</span></div>
																</li>-->
														 <input type="hidden" name="Adfranq" id="Adfranq"   value="1"> 
														 <input type="checkbox" name="idModulo[]" id="idModuloD4.01"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="visualizafranq(); verDerrogacao(this,<?php echo $j;?>); ">

															   
											 <?php  }else if($Cod_Modulo == 'D6.02'){   ?> 
														<input type="hidden" name="d602" id="d602"  value="1">  
														<input type="checkbox" name="idModulo[]" id="idModuloD6.02"  <?php echo (($ModuloSelecionado > 0 || $nivel_d602 > 0) ? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_nivel(this); verDerrogacao(this,<?php echo $j;?>);">
											 <?php  }else if($Cod_Modulo == 'D7.01'){    ?> 
														<div  id="litDiv" >
															<input type="hidden" name="d701" id="d701" value="1">
															<input type="checkbox" name="idModulo[]" id="idModuloD7.01"  <?php echo (($ModuloSelecionado > 0 || ($p_cobertura_d701 && $limite_d701 && $tp_banco == 0)) ? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="desmarca_bb(this); exibirCobertura(this); verDerrogacao(this,<?php echo $j;?>);">
														</div>  
														<input type="hidden" id="limite_d701_old" value="<?php echo $limite_d701; ?>">
														<input type="hidden" id="p_cobertura_d701_old" value="<?php echo $p_cobertura_d701; ?>">                                                                                                                
														<!-- WILSON -->
													  
														<script>
														function desmarca_bb( campo ){
															if ( campo ) {
																if ( campo.checked == false ) {
																	document.getElementById('limite_d701_old').value = document.getElementById('limite_d701').value;
																	document.getElementById('p_cobertura_d701_old').value = document.getElementById('p_cobertura_d701').value;																																
	
																	document.getElementById('d701').value = 0;
																	document.getElementById('limite_d701').value = 0;
																	document.getElementById('p_cobertura_d701').value = 0;																
																} else {
																	document.getElementById('d701').value = 1;
																	document.getElementById('limite_d701').value = document.getElementById('limite_d701_old').value;
																	document.getElementById('p_cobertura_d701').value = 70;
																}
																
															} else {
																document.getElementById('limite_d701_old').value = document.getElementById('limite_d701').value;
																document.getElementById('p_cobertura_d701_old').value = document.getElementById('p_cobertura_d701').value;																																

																document.getElementById('idModuloD7.01').checked = false;
																document.getElementById('d701').value = 0;
																document.getElementById('limite_d701').value = 0;
																document.getElementById('p_cobertura_d701').value = 0;																	
															}
															  
														}
														</script>														  
											 <?php     
													}else if($Cod_Modulo == 'F4.01'){  ?>
														<input type="checkbox" name="idModulo[]" id="idModuloF4.01"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_f401(this); verDerrogacao(this,<?php echo $j;?>);">
														
											 <?php  }else if($Cod_Modulo == 'F9.02'){   ?> 
														<input type="checkbox" name="idModulo[]" id="idModuloF9.02"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_bonus(this); verDerrogacao(this,<?php echo $j;?>);" >                                          
											 <?php  }else if($Cod_Modulo == 'F52.02'){   ?> 
														<input type="checkbox" name="idModulo[]" id="idModuloF52.02" class="idModuloF52"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_F5202(this); verDerrogacao(this,<?php echo $j;?>);" >                                          
											 <?php  }else if($Cod_Modulo == 'F13.02' || $Cod_Modulo == 'F14.02' || $Cod_Modulo == 'F15.02'){   ?>  
														<!--<input type="radio" name="idModuloRadio" id="idModuloRadio<?php echo $x;?>"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_part(this);  exibir_opcpercentagem('idModuloRadio<?php echo $x;?>', <?php echo $ter;?>);   verDerrogacaoEspecial(this,<?php echo $j;?>,'<?php echo $Cod_Modulo;?>');">-->
														<input type="checkbox" name="idModuloRadio" class="js_unique_mod" id="idModuloRadio<?php echo $x;?>"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="exibir_part(this);  exibir_opcpercentagem('idModuloRadio<?php echo $x;?>', <?php echo $ter;?>);   verDerrogacaoEspecial(this,<?php echo $j;?>,'<?php echo $Cod_Modulo;?>');">
											 <?php      $marcar = $ModuloSelecionado;
														
											 
											 
													}else{ ?>
														<input type="checkbox" name="idModulo[]" id="idModulo<?php echo  $x;?>"  <?php echo ($ModuloSelecionado > 0? 'checked' : ''); ?> value="<?php echo $i_Modulo;?>" onClick="verDerrogacao(this,<?php echo $j;?>);">
											<?php   } ?>
											
											
											
											<?php if($Cod_Modulo == 'A8.01') { ?> 
													   <input type="hidden" name="a801" id="a801"   value="1">
											<?php }else if($Cod_Modulo == 'A5.02') { ?> 
														<input type="hidden" name="a502" id="a502"   value="1">
											<?php }else if($Cod_Modulo == 'B6.03') { ?> 
														<input type="hidden" name="b603" id="b603"   value="1">                
											<?php }else if($Cod_Modulo == 'B15.04') { ?> 
														<input type="hidden" name="b1504" id="b1504"  value="1">
											<?php }else if($Cod_Modulo == 'C1.02') { ?> 
														<input type="hidden" name="c102" id="c102"   value="1"> 
											<?php }else if($Cod_Modulo == 'D1.01') { ?> 
														<input type="hidden" name="d101" id="d101"   value="1">
											<?php }else if($Cod_Modulo == 'E1.01') { ?> 
														<input type="hidden" name="e101" id="e101"  value="1">
											<?php }else if($Cod_Modulo == 'F3.05') { ?> 
														<input type="hidden" name="f305" id="f305"   value="1">
											<?php }else if($Cod_Modulo == 'F33.01') { ?> 
														<input type="hidden" name="f3301" id="f3301"   value="1"> 
											<?php } ?>       

											
										 </div>
										 
										 
										<?php if($Cod_Modulo == 'D7.01'){ ?>
													 <div id="ativLitigio">
														 <div class= "formdescricao">
															 <span><?php echo '<strong>'.$Cod_Modulo .'</strong>  ' .$Titulo_Modulo;?></span>
														 </div> 
													 </div> 
										 <?php }else {  ?>
													 <div class= "formdescricao">
															 <span><?php echo '<strong>'.$Cod_Modulo .'</strong>  ' .$Titulo_Modulo;?></span>
													 </div> 
										  <?php } ?>   
										 <div style="clear-both">&nbsp;</div>
										 <?php if($Cod_Modulo == 'F13.02' || $Cod_Modulo == 'F14.02' || $Cod_Modulo == 'F15.02'){   ?> 
													 <div id="opcpercentagem<?php echo $ter;?>" class="js_opcpercentagem" style="display:<?php echo ($marcar ? 'block' : 'none'); ?>">
															<table style="border:0px;">
																 <tr>
																	 <td>
																		  <li class="campo3colunas">     
																			  <label>Percentagem de Dedu&ccedil;&atilde;o %</label>
																				 <input type="text" name="per1<?php echo $ter;?>" id="per1<?php echo $ter;?>" style="text-align:right;" value="<?php echo ($marcar ? $perPart0 : '0,00'); ?>" onBlur="tiraponto('per1<?php echo $ter?>'); adicionaValorPerc1(this.value);">
																		  </li>
																	  </td>
																 </tr>
																 <tr>     
																	  <td>
																		 <li  class="campo3colunas">   
																				 <label>Participa&ccedil;&atilde;o nos Lucros %</label>
																				 <input type="text" name="per2<?php echo $ter;?>" id="per2<?php echo $ter;?>" style="text-align:right;" value="<?php  echo ($marcar ? $perPart1 : '0,00'); ?>" onBlur="tiraponto('per2<?php echo $ter?>'); adicionaValorPerc2(this.value);">
																		 </li>
																	 </td>
																 </tr>
															  </table>
														</div>  
										   <?php 
												 $ter ++;
												} ?>
										 
										   
										  <?php
												  if($Cod_Modulo == 'B4.04'){   ?>
													   <div id="exibir_b404" style="display:<?php if ($ModuloSelecionado) echo 'block'; else echo 'none';?>">
																<table style="border:0px;">
																 
																 <tr><td>
																 <li class="campo3colunas">      
																		<label>Gerente de Cr&eacute;dito:</label>
																		<input type="text"  id="GerenteCredito" name="GerenteCredito"  value="<?php   echo $GerenteCredito; ?>" onBlur="" maxlength="50">
																 </li>
																
																 </td>
																 <td>&nbsp;
																 
																 </td>
																 
																 </tr>
																 
																 <tr><td>
																 <li class="campo3colunas">      
																  <label>O n&iacute;vel m&aacute;ximo de cr&eacute;dito referente &agrave; cl&aacute;usula 1 deste m&oacute;dulo &eacute; de <?php echo $extMoeda; ?>:</label>
																		<input type="text"  id="b404NivelMax" name="b404NivelMax"  value="<?php   echo $b404NivelMax; ?>" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																	   
																 </li>
																 </td><td>            
																  <li class="campo3colunas">      
																	   <label>A percentagem segurada referente &agrave; cl&aacute;usula 1.6 deste m&oacute;dulo &eacute; de: % </label>
																		<input type="text"  id="b404Perc" name="b404Perc"  value="<?php   echo $b404Perc; ?>" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																	   
																 </li>
																 </td></tr>
																 </table>
														 </div> 
												  <?php	  
													  
												  }
												  else if($Cod_Modulo == 'B8.02'){
															 
														 // Exibe as Empresas dos maiores compradores
														  $x = 0;
														  /*
														  $query = "SELECT count(id) as id FROM ParModEsp_Maiores_Compradores WHERE idInform = ".$idInform;
														  $curj = odbc_exec ($db, $query);   
																 
														  if(odbc_result($curj,'id') > 0){
															   $b802ex = " checked ";
															   $b802   = 1;
															   $temComprador = odbc_result($curj,'id');
														  }else{
															  $b802ex = " ";
															  $temComprador = 0;
														  }
														  */
													 ?>
							 
													 
															
													  <!--  INÍCIO DE EXTENSÃO DE EMPRESA  -->
													  
													 <div id="exibir_Cobertura" style="display:<?php if ($b802 == 1) echo 'block'; else echo 'none';?>">
													 
													 <div style="clear:both">&nbsp;</div> 
													
													
													  <?php
														   
														   $qry = "SELECT * FROM ParModEsp_Maiores_Compradores WHERE idInform = ".$idInform." ORDER BY Nome ";
														  
														   $curt = odbc_exec($db, $qry); 
														   $linha = odbc_num_rows($curt);
														   
													   
														 ?>
													
															<table width="100%" style="border:0px;"><tr><td>
															 <input type="hidden" id="b802" name="b802" value=" <?php  echo $b802; ?>">
															 <input type="hidden" id="TemEmpComprador" name="TemEmpComprador" value=" <?php  echo $b802; ?>">
															 <li class="campo3colunas">       
																	<label>Raz&atilde;o Social</label>
																	<input type="text" name="razaoComprador" id="razaoComprador" value=""/>
																	<input type="hidden" name="cobEmpresaID" id="cobEmpresaID" value="">
															 </li>
															 </td><td>
															  <li class="campo3colunas">       
																	<label>CNPJ</label>
																	<input type="text" name="cnpjComprador" id="cnpjComprador" maxlength="18" value="" onKeyUp="MascarCnpj(this,event)"/>
															 </li></td></tr>                         
															 </table>                        
															 <div class="barrabotoes">
																<button class="botaoagg" type="button"  onClick="javascript: loadHTMLIE('<?php echo $root.'role/executive/interf/Adiciona_Maiores_Compradores.php'; ?>','Retorno5','POST','','<?php echo $idInform;?>',document.getElementById('Operacao').value,'CobComprador');">Adicionar Cobertura</button>
															 </div>
															 
															 <div id="Retorno5">
															   <table summary="Submitted table designs">
																   <thead>
																		   <th scope="col">Raz&atilde;o Social</th>
																		   <th scope="col">CNPJ</th>
																		   <th scope="col" colspan="2">Op&ccedil;&otilde;es</th>
																		   
																  </thead>
																  <tbody> 																  
																	<?php  
																		while (odbc_fetch_row($curt)) {
																			 
																			$razaoComprador    = odbc_result ($curt, 'Nome');
																			$cnpjComprador     = odbc_result ($curt, 'CNPJ');
																			$idComprador       = odbc_result ($curt, 'id');	?>
																		  <tr>
																			  <td><?php echo ($razaoComprador);?></td>
																			  <td><?php echo $cnpjComprador; ?></td>
																			  <td><a href="#" onClick="edita_formCobertura('<?php echo $razaoComprador; ?>','<?php echo $cnpjComprador;?>','<?php echo $idComprador;?>');return false;"><img src="<?php echo $root;?>images/icone_editar.png" alt=""  title="Editar Registro" width="24" height="24" class="iconetabela" /></a></td>
																			  <td><a href="#" onClick="javascript: loadHTMLIE('<?php echo $root.'role/executive/interf/Adiciona_Maiores_Compradores.php'; ?>','Retorno5','POST','<?php echo  $idComprador;?>','<?php echo $idInform;?>','Remover','CobComprador');return false;"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a></td>
																			</tr>
																	  <?php  } ?>  															 
																  </tbody>  
																</table>
															 </div>
													 </div>
												  <?php
												  } else if($Cod_Modulo == 'B9.04'){  ?>
														<div id="exibir_b904" style="display:<?php if ($Cod_Modulo && $b904 > 0) echo 'block'; else echo 'none';?>">
															 
															<table width="100%" style="border:0px;"><tr><td>
															 <li class="campo3colunas">
																 <label>A franquia de maiores compradores mencionada no item 1.1 deste m&oacute;dulo &eacute; de: </label>                    
																  <input type="text" id="b904Valor" name="b904Valor"    value="<?php  echo ($b904 ? $b904 : 0);?>" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																				 
															 </li> 
															 </td></tr>
															 </table>
														</div>  
													  
												  <?php
												  } else if($Cod_Modulo == 'B12.02'){  ?><!--  INÍCIO DE EXTENSÃO DE EMPRESA  -->
														
														 <div id="exibirEmpresa" style="display:<?php if ($ModB1202 == 1) echo 'block'; else echo 'none';?>">
														 
															 <table width="100%" border="0">
																<tr>
																  <td>                                                        
																	 <li class="campo3colunas" style="width:280px;">       
																			<label>Raz&atilde;o Social</label>
																			<input type="text" name="RazaoSocialEmpresa" id="RazaoSocialEmpresa" value=""/>
																	 </li>
																	 
																	  <li class="campo3colunas" style="width:280px;">       
																			<label>CNPJ</label>
																			<input type="text" name="CNPJEmpresa" id="CNPJEmpresa" value="" onKeyUp="MascarCnpj(this,event)" maxlength="18"/>
																	  </li>
																	 
																	  <li class="campo3colunas" style="width:280px;">       
																			<label>Inscri&ccedil;&atilde;o Estadual</label>
																			<input type="text" name="InscricaoEstadualEmpresa" id="InscricaoEstadualEmpresa" value=""/>
																	 </li>
																	
																	  <li class="campo3colunas" style="width:280px;">       
																			<label>Endere&ccedil;o</label>
																			<input type="text" name="EnderecoEmpresa" id="EnderecoEmpresa" value=""/>
																	 </li>
																	  
																	  <li class="campo3colunas" style="width:280px;">       
																			<label>N&uacute;mero</label>
																			<input type="text" name="NumeroEmpresa" id="NumeroEmpresa" value=""/>
																	  </li>
																	  
																	  <li class="campo3colunas" style="width:280px;">       
																			<label>CEP</label>
																			<input type="text" name="CEPEmpresa" id="CEPEmpresa" value="" onKeyUp="return MascaraCEP(this);" maxlength="9"/>
																	 </li>
																	  <li class="campo3colunas" style="width:280px;">       
																			<label>Cidade</label>
																			<input type="text" name="CidadeEmpresa" id="CidadeEmpresa" value=""/>
																	 </li>
																	 <li class="campo3colunas" style="width:280px;">       
																		 <label>UF</label>
																		 <?php
																			 $qryUF = "select * from UF order by name";
																			 $curUF = odbc_exec ($db, $qryUF);
																		 ?>
																			<select name="UFEmpresa" id="UFEmpresa"/>
																			<option value="">Selecione...</option>
																		 <?php   while (odbc_fetch_row($curUF)) {   ?>
																					 <option value="<?php echo odbc_result ($curUF, 'uf');?>"><?php echo (odbc_result ($curUF, 'name'));?></option>
																		 <?php   }   ?>  
																		 </select>
																			
																	 </li>
																	 <li class="campo3colunas" style="width:280px;">
																		  <label>&nbsp;</label>
																		  <input type="hidden" name="empresaID" id="empresaID" value="">
																	 </li>
																	 </td>
																	 </tr>
																 </table>
																 
																 <div class="barrabotoes">
																	<button class="botaoagg" type="button"  onClick="javascript: loadHTMLIE('<?php echo $root.'role/executive/interf/Adiciona_Empresa.php'; ?>','Retorno1','GET','','<?php echo $idInform;?>',document.getElementById('Operacao').value,'Empresa');">Adicionar Empresa</button>
																 </div>
																 
																 <div id="Retorno1">
																   <table summary="Submitted table designs">
																	   <thead>
																			   <th scope="col">Raz&atilde;o Social</th>
																			   <th scope="col">CNPJ</th>
																			   <th scope="col">Inscr. Estadual</th>
																			   <th scope="col">Endere&ccedil;o</th>
																			   <th scope="col">N&deg;</th>
																			   <th scope="col">CEP</th>
																			   <th scope="col">Cidade</th>
																			   <th scope="col">UF</th>
																			   <th scope="col" colspan="2">&nbsp;</th>
																			   
																	  </thead>
																	  <tbody>
																	  <?php 																
																
																		$query = "SELECT * FROM ParModEsp_Empresa WHERE idInform = ".$idInform." ORDER BY no_razao_social ";
																		$curxz = odbc_exec ($db, $query);  
																	   
																	  while (odbc_fetch_row($curxz)) {
																				$no_razao_social       = odbc_result ($curxz, 'no_razao_social');
																				$nu_cnpj               = odbc_result ($curxz, 'nu_cnpj');
																				$nu_inscricao_estadual = odbc_result ($curxz, 'nu_inscricao_estadual');
																				$ds_endereco           = odbc_result ($curxz, 'ds_endereco');
																				$num_endereco          = odbc_result ($curxz, 'nu_endereco');
																				$ds_complemento        = odbc_result ($curxz, 'ds_complemento');
																				$nu_cep                = odbc_result ($curxz, 'nu_cep');
																				$no_cidade             = odbc_result ($curxz, 'no_cidade');
																				$no_estado             = odbc_result ($curxz, 'no_estado'); 
																				$idEmpresa             = odbc_result ($curxz, 'id'); ?>
																		 <tr>
																			 <td><?php echo ($no_razao_social);?></td>
																			 <td><?php echo $nu_cnpj;?></td>
																			 <td><?php echo $nu_inscricao_estadual;?></td>
																			 <td><?php echo ($ds_endereco);?></td>
																			 <td><?php echo $num_endereco;?></td>
																			 <td><?php echo $nu_cep;?></td>
																			 <td><?php echo ($no_cidade);?></td>
																			 <td><?php echo $no_estado;?></td>
																			 <td><a href="#" onClick="edita_formEmpresa('<?php echo $idEmpresa;?>','<?php echo $no_razao_social; ?>','<?php echo $nu_cnpj;?>','<?php echo $nu_inscricao_estadual; ?>','<?php echo $ds_endereco; ?>','<?php echo $num_endereco; ?>','<?php echo $nu_cep; ?>','<?php echo $no_cidade; ?>','<?php echo $no_estado;?>');return false;"><img src="<?php echo $root;?>images/icone_editar.png" alt=""  title="Editar Registro" width="24" height="24" class="iconetabela" /></a></td>
																			 <td><a href="#" onClick="javascript: loadHTML('<?php echo $root.'role/executive/interf/Adiciona_Empresa.php'; ?>','Retorno1','GET','<?php echo  $idEmpresa;?>','<?php echo $idInform;?>','Remover','Empresa');return false;"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a></td>
												
																		</tr>
																		<?php }  ?>
																	  </tbody>  
																	</table>
																 </div>
														 </div>
														 <!--  FIM DE EXTENSÃO DE EMPRESA  -->
													 
										   <?php    }elseif ($Cod_Modulo == 'B26.04'){  ?> 
														 <div id="exibir_b2604" style="display:<?php if ($ModuloSelecionado) echo 'block'; else echo 'none';?>">
															 <table style="border:0px;">
															 <tr><td>
															 <li class="campo3colunas">      
															  <label>O n&iacute;vel m&aacute;ximo de cr&eacute;dito referente &agrave; cl&aacute;usula 1 deste m&oacute;dulo &eacute; de <?php echo $extMoeda; ?>:</label>
																	<input type="text"  id="b2604NivelMax" name="b2604NivelMax"  value="<?php   echo $b2604NivelMax; ?>" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																   
															 </li>
															 </td><td>
															 <li class="campo3colunas">      
																   <label>A percentagem segurada referente &agrave; cl&aacute;usula 1.2 deste m&oacute;dulo &eacute; de: % </label>
																	<input type="text"  id="b2604Perc" name="b2604Perc"  value="<?php   echo $b2604Perc; ?>" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																   
															 </li> 
															 </td></tr>
															 </table>
															 
														 </div>
										   <?php    }else if ($Cod_Modulo == 'B28.01'){  ?>
														  <div id="exibir_b2801" style="display:<?php if ($Cod_Modulo && ($b2801NivelMax > 0 and $b2801Perc > 0)) echo 'block'; else echo 'none';?>">
																<table style="border:0px;">
																 <tr><td>
																 <li class="campo3colunas">      
																  <label>O n&iacute;vel m&aacute;ximo de cr&eacute;dito referente &agrave; cl&aacute;usula 1 deste m&oacute;dulo &eacute; de:</label>
																		<input type="text"  id="b2801NivelMax" name="b2801NivelMax"  value="<?php   echo $b2801NivelMax; ?>" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																	   
																 </li>
																 </td><td>            
																  <li class="campo3colunas">      
																	   <label>A percentagem segurada referente &agrave; cl&aacute;usula 1.6 deste m&oacute;dulo &eacute; de: % </label>
																		<input type="text"  id="b2801Perc" name="b2801Perc"  value="<?php   echo $b2801Perc; ?>" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																	   
																 </li>
																 </td></tr>
																 </table>
														  </div>
										   
										  <?php     }else if($Cod_Modulo == 'D2.01'){ ?>
													   <div id="exibir_d201" style="display:<?php if ($ModuloSelecionado) echo 'block'; else echo 'none';?>">               
															 <table style="border:0px;">
																 <tr><td>
																	 <li class="campo3colunas">      
																		   <label>A Franquia &eacute; de <?php echo $extMoeda; ?>: </label>
																			<input type="text"  id="d201" name="d201"  value="<?php   echo $d201; ?>" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																		   
																	 </li>
																 </td></tr>
															 </table> 
														 </div>
													
										  <?php     }else if ($Cod_Modulo == 'D4.01'){ 
											   ?>                                          
															<div id="franquia_an" style="display:<?php   echo ($ModuloSelecionado > 0 ? 'block' : 'none');?>;list-style:none;">
																  <div style="clear:both">&nbsp;</div>
																	<table style="border:0px;">
																		<tr>
																			<td><li  class="campo2colunas">          
																						<label>Valor da franquia <?php echo $extMoeda; ?>:</label>
																						<input type="text" name="franquia_anual" id="franquia_anual" value="<?php   echo $franquia_anual;?>"  style=" width:270px; text-align: right"  onBlur="checkDecimals(this, this.value);"/>
																				</li>
																			 </td>
																		</tr>
																	 </table>   
														   </div>           
										  <?php     }else if($Cod_Modulo == 'D6.02'){   ?>
														  <div id="nivelPequSinistro" style="display:<?php if ($ModuloSelecionado || $nivel_d602 > 0) echo 'block'; else echo 'none';?>"> 
															   <div style="clear:both">&nbsp;</div>
															   <table style="border:0px;">
																	<tr><td><li  class="campo3colunas"> 
																	<label>N&iacute;vel de Pequenos Sinistros <?php echo $extMoeda;?>:</label>
																	<input type="text" name="nivel_d602" id="nivel_d602" style="text-align:right;" value="<?php echo $nivel_d602;?>" onBlur="checkDecimals(this, this.value);">
																	</li> 
																	</td></tr>
															  </table>
														  </div> 
											
											 <?php  }else if($Cod_Modulo == 'D7.01'){   ?> 
														   <div style="clear:both" >&nbsp;</div>
															   <div id="verLitigio" style="display:<?php if ($tp_banco == "0") echo 'block'; else echo 'none';?>">
																	<div id="percentualCobertura" style="display:<?php if ($ModuloSelecionado || ($p_cobertura_d701 && $limite_d701)) echo 'block'; else echo 'none';?>">  
																		  <table style="border:0px;">
																		  <tr><td>
																			 <li  class="campo3colunas">     
																				   <label>% de Cobertura:&nbsp;</label>
																				   <input type="text" name="p_cobertura_d701" id="p_cobertura_d701"  style="text-align: right;" value="<?php   echo $p_cobertura_d701?>" <?php  if( ($userID!=4425) && ($userID!=1953) && ($userID!=456) ) { echo " readonly "; }?> >
																			 </li>                                                       
																		   </td></tr>                                                         
																		  <tr><td>
																		  <li id="clear" class="campo2colunas" style="width:970px; height:135px;"> 
																			  <label>Limite de Pagamento de Lit&iacute;gio:</label>
																			  <textarea  name="limite_d701" id="limite_d701" cols="50" rows="5"><?php   echo ($limite_d701);?></textarea>
																		  </li>
																		  
																		  </td></tr>
																		  </table>
																   </div>
															 </div>
															
											 <?php }else if ($Cod_Modulo == 'F4.01'){ ?>

														  <div id="exibir_f401" style="display:<?php if ($Cod_Modulo && ($f401NivelSinistralidade > 0 || $f401PercPremio > 0 )) echo 'block'; else echo 'none';?>"> 
															  <table style="border:0px;" id="tb_f401_js">
															    <tr>
															    	<td>
																		 	<li class="campo3colunas">      
																		    <label>O n&iacute;vel de sinistralidade &eacute; de: %</label>
																				<input type="text"  id="f401NivelSinistralidade" name="f401NivelSinistralidade"  value="<?php   echo $f401NivelSinistralidade; ?>" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																			</li>
																		  <li class="campo3colunas">      
																			  <label>A percentagem de pr&ecirc;mio &eacute;: % </label>
																				<input type="text"  id="f401PercPremio" name="f401PercPremio"  value="<?php   echo $f401PercPremio; ?>" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																			</li>
																			<li class="campo3colunas">
																				<button class="botaoagm" type="button" id="gerar_f401">INCLUIR</button>
																			</li>
															   		</td>
															   	</tr>
															  </table>

																<?php
																	$sql = "SELECT i_Modulo_Valor As IdModuloValor, v_1 As Nivel_Sinistralidade, v_2 As Percentagem_Premio
																					FROM Inform_Modulo_Valores IMV
																					WHERE	IMV.i_Inform = ".$idInform." AND IMV.i_Modulo = 24 
																					ORDER BY v_1";
																	$resultF401 = odbc_exec($db,$sql); 
																	$numResultF401 = odbc_num_rows($resultF401); ?>

																	<br>
																	<table class="tb_parcelas_js">
																		<thead>
																		    <tr>
																						<th>O n&iacute;vel de sinistralidade &eacute; de: %</th>
																		        <th>A percentagem de pr&ecirc;mio &eacute;: %</th>
																		        <th>Op&ccedil;&otilde;es</th>
																		    </tr>
																		</thead>        
																		<tbody id="f401_campos">
																			<?php	
 																				$a = 0;
 																				while(odbc_fetch_row($resultF401)){ ?>
																		    <tr class="f401_int" id="f401_int">
																		    		<input type="hidden" id="id_modulo_valor" value="<?php echo odbc_result($resultF401, 'IdModuloValor'); ?>">
																		      	<td><?php echo number_format(odbc_result($resultF401, 'Nivel_Sinistralidade'),2,',','.'); ?></td>
	                                        	<td><?php echo number_format(odbc_result($resultF401, 'Percentagem_Premio'),2,',','.'); ?></td> 
  																					<td>
  																						<a href="#" class="remove_f401"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a>
  																					</td>

																		    </tr>
																		  <?php 
																		  	$a++;
																		  }?>
																		</tbody>
																	</table>
																	<br>

														  </div>    
											 
											 <?php  }else if($Cod_Modulo == 'F9.02'){   ?> 
														  <div id="exibirBonus_campo" style="display:<?php if ($Cod_Modulo && $perBonus > 0) echo 'block'; else echo 'none';?>">  
															   <div style="clear:both">&nbsp;</div>
															   <table style="border:0px;">
															   <tr><td>
																   <li  class="campo3colunas">   
																		  <label>Percentual %</label>
																			  <input type="text" name="per" id="per" value="<?php   echo $perBonus; ?>" onBlur="tiraponto('per');" style="text-align:right">
																		 </span>
																   </li>
															   </tr></td>
															   </table>
														   </div> 
											 
											 <?php  } 

											 if($Cod_Modulo == 'F52.02'){ ?> 
													  <div id="exibirF5202_campo" style="display:<?php if ($Cod_Modulo && $ModuloSelecionado > 0) echo 'block'; else echo 'none';?>">  
														   <!-- <div style="clear:both">&nbsp;</div>
														   <table style="border:0px;">
														   <tr><td>
															   <li  class="campo3colunas">   
																	  <label>Percentual %</label>
																		  <input type="text" name="per" id="per" value="<?php   echo $perBonus; ?>" onBlur="tiraponto('per');" style="text-align:right">
																	 </span>
															   </li>
														   </tr></td>

														   </table>-->


																<table style="border:0px;" id="tb_f5202_js">
															    <tr>
															    	<td>
																		 	<li class="campo3colunas">      
																		    <label>Adequação de Sinistralidade: %</label>
																				<input type="text" id="f5202_adeq_sinist" name="f5202_adeq_sinist" value="" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																			</li>
																		  <li class="campo3colunas">      
																			  <label>Adequação do Prêmio: % </label>
																				<input type="text" id="f5202_adeq_premio" name="f5202_adeq_premio" value="" onBlur="checkDecimals(this, this.value);" style="text-align: right;">
																			</li>
																			<li class="campo3colunas">
																				<button class="botaoagm" type="button" id="gerar_f5202">INCLUIR</button>
																			</li>
															   		</td>
															   	</tr>
															  </table>

																<?php
																	$sql = "SELECT i_Modulo_Valor As IdModuloValor, v_1 As Adeq_Sinistralidade, v_2 As Adeq_Premio
																					FROM Inform_Modulo_Valores IMV
																					WHERE	IMV.i_Inform = ".$idInform." AND IMV.i_Modulo = 34 
																					ORDER BY v_1";
																	$resultF5202 = odbc_exec($db,$sql); 
																	$numResultF5202 = odbc_num_rows($resultF5202); ?>

																	<br>
																	<table class="tb_parcelas_js">
																		<thead>
																		    <tr>
																						<th>Adequação de Sinistralidade: %</th>
																		        <th>Adequação do Prêmio: %</th>
																		        <th>Op&ccedil;&otilde;es</th>
																		    </tr>
																		</thead>        
																		<tbody id="f5202_campos">
																			<?php	
 																				$a = 0;
 																				while(odbc_fetch_row($resultF5202)){ ?>
																		    <tr class="f5202_int" id="f5202_int">
																		    		<input type="hidden" id="id_modulo_valor" value="<?php echo odbc_result($resultF5202, 'IdModuloValor'); ?>">
																		      	<td><?php echo number_format(odbc_result($resultF5202, 'Adeq_Sinistralidade'),2,',','.'); ?></td>
	                                        	<td><?php echo number_format(odbc_result($resultF5202, 'Adeq_Premio'),2,',','.'); ?></td> 
  																					<td>
  																						<a href="#" class="remove_f5202"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a>
  																					</td>

																		    </tr>
																		  <?php 
																		  	$a++;
																		  }?>
																		</tbody>
																	</table>


														  <br>	
															<table style="border:0px;">													   
															
																<div id="container_adeq_prem_mod">
																	<div id="adeq_prem_mod">
																	
																	</div>
																</div>

															</table>
			

														</div>									 
											 <?php  } 
																									   
																						
											 if($Cod_Modulo == 'D7.01'){ ?>
												   <div id="DerrLitigio">								
														  <div id="verDerrogacao<?php echo $j;?>" style="display:<?php if ($ModuloSelecionado) echo 'block'; else echo 'none';?>;">
															 <div class="formopcaonew">
																  <input type="checkbox" name="derroga[]" id="derroga<?php echo $j;?>"  <?php if (str_replace('<br />','',$Desc_Modulo)) echo 'checked';?> value="<?php echo $i_Modulo;?>" onClick="exibir_derrogacoes(this,<?php echo $j;?>);">
															 </div>
															 <div class= "formdescricao">
																	 <span><strong>Derroga&ccedil;&otilde;es</strong></span>
															 </div> 
															   
															 <div id="mostraderrogacao<?php echo $j;?>" style="display:<?php if (str_replace('<br />','',$Desc_Modulo)) echo 'block'; else echo 'none';?>;">
																   <div style="clear:both">&nbsp;</div> 
																   <textarea name="descr_derrogacao<?php echo $i_Modulo;?>" id="descr_derrogacao<?php echo $j;?>" rows="7" cols="80" class="fontdest" style="width:915px;" onKeyPress="javascript: ContaCaracteres('descr_derrogacao<?php echo $j;?>',3880,<?php echo $j;?>);" onBlur="javascript: limitarCaracteresInfo('descr_derrogacao<?php echo $j;?>',3880,<?php echo $j;?>);"><?php echo (str_replace('<br />','',$Desc_Modulo));?></textarea>
																   <?php $tocart =  strlen(str_replace('<br />','',$Desc_Modulo)); ?>
																   <div class="formdescricao"><span>Caracteres Restantes:</span><input type="text" name="cont<?php echo $j;?>" id="cont<?php echo $j;?>" value="<?php echo (3880 - $tocart);?>" style="border: none; width:30px;position:relative;*top:15px;" readonly/></span></div>
															 </div>
														 </div>
												  </div>
											 <?php }else{ ?>
														<div id="verDerrogacao<?php echo $j;?>" style="display:<?php if ($ModuloSelecionado) echo 'block'; else echo 'none';?>;">
														 <div class="formopcaonew">
															  <input type="checkbox" name="derroga[]" id="derroga<?php echo $j;?>"  <?php if (str_replace('<br />','',$Desc_Modulo)) echo 'checked';?> value="<?php echo $i_Modulo;?>" onClick="exibir_derrogacoes(this,<?php echo $j;?>);">
														 </div>
														 <div class= "formdescricao">
																 <span><strong>Derroga&ccedil;&otilde;es</strong></span>
														 </div> 
														   
														 <div id="mostraderrogacao<?php echo $j;?>" style="display:<?php if (str_replace('<br />','',$Desc_Modulo)) echo 'block'; else echo 'none';?>;">
															   <div style="clear:both">&nbsp;</div> 
															   <textarea name="descr_derrogacao<?php echo $i_Modulo;?>" id="descr_derrogacao<?php echo $j;?>" rows="7" cols="80" class="fontdest derrog" style="width:915px;" onKeyPress="javascript: ContaCaracteres('descr_derrogacao<?php echo $j;?>',3880,<?php echo $j;?>);" onBlur="javascript: limitarCaracteresInfo('descr_derrogacao<?php echo $j;?>',3880,<?php echo $j;?>);"><?php echo (str_replace('<br />','',$Desc_Modulo));?></textarea> 
																   <?php $tocart =  strlen(str_replace('<br />','',$Desc_Modulo)); ?>
																   <div class="formdescricao"><span>Caracteres Restantes:</span><input type="text" name="cont<?php echo $j;?>" id="cont<?php echo $j;?>" value="<?php echo (3880 - $tocart);?>" style="border: none; width:30px;position:relative;*top:15px;" readonly/></span></div>
														 </div>
													   </div>
											 <?php } ?> 
							   
								  </div>  
								 
				<?php
					 $j++;
					 $x++;
				}
				
				if ($x >1){ 
					 
					  echo '</div>';                    
					  echo '</li>';
				} ?>
					 
			</ul>
			 
		</div>
			
		<input type="hidden" name="totModulos" id="totModulos" value="<?php echo $vx?>">	 

	 <?php  /* Habilita os checkbox após ter recebido o valor da consulta*/
	  } ?>

	  	<!-- FIM DAS CONDIÇOES PARA APARECER DADOS NO FORMULARIO  -->


   <!-- Fim Módulos -->  