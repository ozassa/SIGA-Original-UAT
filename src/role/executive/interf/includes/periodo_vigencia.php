 <script>
 
	function edita_Form_Vig(vlr1,vlr2,vlr3,vlr4){
		
		document.getElementById('n_Preriodo').value = vlr1;
		document.getElementById('d_Vigencia_Inicial').value = vlr2;
		document.getElementById('d_Vigencia_Final').value = vlr3;
		document.getElementById('v_Premio').value = vlr4;			
		
	}
	
	function enviaPeriodo_v(str, id){
		var url = "<?php echo $root.'role/executive/interf/';?>Adiciona_Vigencia.php?operacao=" + str +
				 "&idInform="+ id + 
				 '&n_Preriodo='+ document.getElementById('n_Preriodo').value +
				 '&d_Vigencia_Inicial='+ document.getElementById('d_Vigencia_Inicial').value +
				 '&d_Vigencia_Final='+ document.getElementById('d_Vigencia_Final').value +
				 '&v_Premio='+ document.getElementById('v_Premio').value;

		document.getElementById('n_Preriodo').value = "";
		document.getElementById('d_Vigencia_Inicial').value = "";
		document.getElementById('d_Vigencia_Final').value = "";
		document.getElementById('v_Premio').value = "";
			 
		$.post(url, function (data) {							 
			if(data){	
				//alert(data);			
				document.getElementById('RetornoVig').innerHTML = data;
			}
		});
	}
	
	function enviaPeriodo_v2(str,campo,id){
		$.post("<?php echo $root.'role/executive/interf/';?>Adiciona_Vigencia.php?operacao=" + str +
				 "&idInform="+ id + 
				 '&n_Preriodo='+ campo +
				 '&d_Vigencia_Inicial='+ document.getElementById('d_Vigencia_Inicial').value +
				 '&d_Vigencia_Final='+ document.getElementById('d_Vigencia_Final').value +
				 '&v_Premio='+ document.getElementById('v_Premio').value, function (data) {
					 
			if(data){	
				//alert(data);			
				document.getElementById('RetornoVig').innerHTML = data;
			}
		});
	}
	
	function validaPeriodoVig(dataini, datafim){
		
		if(dataini != '' && datafim != ''){
			
			dia = dataini.substring(0,2);
			mes = dataini.substring(5,3);
			ano = dataini.substring(10,6);	
			//alert(mes);
			dataini = mes + '/' + dia + '/' +ano;
			
			dia = datafim.substring(0,2);
			mes = datafim.substring(5,3);
			ano = datafim.substring(10,6);
						
			datafim = mes + '/' + dia + '/' +ano;
			
			
			Data1 = Date.parse(dataini);
			Data2 = Date.parse(datafim);
			
			//alert(Data1 + ' ' + Data2);
			if(Data2 < Data1) {
				verErro('O período final não pode ser inferior ao período inicial.');	
				document.getElementById('d_Vigencia_Final').value = '';
				return 0;
			} else {
				return 1;
			}
		}else{
		   return 0;	
		}
		
			
	}
	
	function validadoVig(){
			
		if(! validaPeriodoVig(document.getElementById('d_Vigencia_Inicial').value, document.getElementById('d_Vigencia_Final').value)){
			verErro("Atenção! Verifique os períodos de vigência, a data final não pode ser inferior a data inicial.");
			return false;
		}
		
		if(document.getElementById('d_Vigencia_Inicial').value.length == 10 && document.getElementById('d_Vigencia_Final').value.length == 10 &&  document.getElementById('v_Premio').value != ''){
			return true;
		}else{
			verErro("Atenção! Informe corretamente os dados de Vigência.");
			return false;
		}
	}
 
 </script>
 
 
 

 
 
 
 <!-- Adicionar os períodos de vigência-->
<li class="barrabotoes" style="list-style:none;*margin-left:-15px;">  
   <label><h2>Cadastro de Períodos de Vigência</h2> </label>
</li>

<li class="campo3colunas" style="width:240px;">  
   <label>Início DD/MM/AAAA</label>
   <input type="text"  name="d_Vigencia_Inicial" id="d_Vigencia_Inicial" value="" onKeyUp="mascara(this,data);" maxlength="10" style="width:190px;">
	<img src="<?php echo $host; ?>images/icone_calendario.png" name="imgDtVigInicio" id="imgDtVigInicio" alt="" class="imagemcampo" />
	<script type="text/javascript">
		Calendar.setup({
			inputField     :    "d_Vigencia_Inicial",     	// id of the input field
			ifFormat       :    "dd/mm/y",      	// format of the input field
			button         :    "imgDtVigInicio",  	// trigger for the calendar (button ID)
			align          :    "Tl",           	// alignment (defaults to "Bl")
			singleClick    :    true
		});
	</script>
   
</li>
<li class="campo3colunas" style="width:240px;">  
   <label>Fim  DD/MM/AAAA</label>
   <input type="text" name="d_Vigencia_Final" id="d_Vigencia_Final" value="" onKeyUp="mascara(this,data);" maxlength="10" style="width:190px;" onChange="validaPeriodoVig(document.getElementById('d_Vigencia_Inicial').value,this.value);" onBlur="validaPeriodoVig(document.getElementById('d_Vigencia_Inicial').value,this.value);">
	<img src="<?php echo $host; ?>images/icone_calendario.png" name="imgDtVigFim" id="imgDtVigFim" alt="" class="imagemcampo" />
	<script type="text/javascript">
		Calendar.setup({
			inputField     :    "d_Vigencia_Final",   // id of the input field
			ifFormat       :    "dd/mm/y",      			// format of the input field
			button         :    "imgDtVigFim",  			// trigger for the calendar (button ID)
			align          :    "Tl",           			// alignment (defaults to "Bl")
			singleClick    :    true
		});
	</script>
   
</li>

<li class="campo3colunas" style="width:240px;">  
   <label>Valor Prêmio <?php echo $extMoeda; ?></label>
   <input type="text" name="v_Premio" id="v_Premio" value="<?php echo isset($v_Premio) && $v_Premio != '0,00' ? number_format($v_Premio,2,',','.') : '0,00'; ?>" style="text-align:right" onBlur="checkDecimals(this, this.value);" onKeypress="return numeros();">
</li>
<li class="campo3colunas" style="width:100%; text-align:left"> 
 <label>&nbsp; </label>
 <input type="hidden" name="n_Preriodo" id="n_Preriodo" value="">		 
 <button class="botaoagm" type="button"  onClick="javascript: if(validadoVig()){ enviaPeriodo_v('Adicionar',<?php echo $idInform;?>);}" >Adicionar</button>             
</li>

<div id="RetornoVig" style="clear:left">
	 <table width="100%">
	 
	   <thead>
			   <th scope="col">N&ordm; Seq</th>
			   <th scope="col">In&iacute;cio Vig&ecirc;ncia</th>
			   <th scope="col">Fim Vig&ecirc;ncia</th>
			   <th scope="col">Valor Pr&ecirc;mio <?php echo $extMoeda; ?></th>
			   <th scope="col" colspan="2">Op&ccedil;&otilde;es</th>
			   
	  </thead>
	   <tbody> <?php 
		   $sql1 = " select * from Periodo_Vigencia where i_Inform =".$idInform;
		   $resp1 = odbc_exec($db,$sql1);
		$totalVigPremio = 0;   
		
		while (odbc_fetch_row($resp1)){
			
			$n_Preriodo          = odbc_result($resp1, 'n_Preriodo');
			$d_Vigencia_Inicial  = Convert_Data_Geral(substr(odbc_result($resp1, 'd_Vigencia_Inicial'),0,10));
			$d_Vigencia_Final    = Convert_Data_Geral(substr(odbc_result($resp1, 'd_Vigencia_Final'),0,10));
			$v_Premio     = (odbc_result($resp1, 'v_Premio'));
						   
		   ?> 
				 <tr id="lastRow_corr">
					 <td><?php echo $n_Preriodo;?></td>
					 <td><?php echo $d_Vigencia_Inicial;?></td>
					 <td><?php echo $d_Vigencia_Final;?></td>
					 <td style="text-align: right;"><?php echo ($v_Premio ? number_format($v_Premio,1,',','.') :'0,00');?></td>
					 <td><a href="#" onClick="edita_Form_Vig('<?php echo $n_Preriodo;?>','<?php echo $d_Vigencia_Inicial;?>','<?php echo $d_Vigencia_Final;?>','<?php echo number_format($v_Premio,2,',','.');?>');return false;"><img src="<?php echo $root;?>images/icone_editar.png" title="Editar Registro" width="24" height="24" class="iconetabela" /></a></td>
					 <td><a href="#" onClick="javascript: enviaPeriodo_v2('Remover','<?php echo $n_Preriodo;?>',<?php echo $idInform;?>);return false;"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a></td>
				</tr>
				
	  <?php  
			$totalVigPremio += $v_Premio;
		 } ?><tr>
				  <td colspan="3" style="text-align:right"><strong>Totalização Prêmio:</strong></td>
				  <td colspan="1" style="text-align:right"><strong><?php echo number_format( $totalVigPremio,2,',','.'); ?></strong></td>
				  <td colspan="2">&nbsp;</td>
			 </tr>
			   
		</tbody>
	   </table>
   <input type="hidden" name="TotalVigPremio" id="TotalVigPremio" value="<?php echo $totalVigPremio;?>">
</div>






<?php //Interaktiv ?>


<style>
.ui-autocomplete {
	width: 50px;
  max-height: 100px;
  overflow-y: auto;
  /* prevent horizontal scrollbar */
  overflow-x: hidden;
}
/* IE 6 doesn't support max-height
 * we use height instead, but this forces the menu to always be this tall
 */
* html .ui-autocomplete {
  height: 100px;
}
</style>

<link href="<?php echo $root; ?>scripts/jquery_ui/jquery_ui.min.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="<?php echo $root; ?>scripts/jquery_ui/jquery_ui.js"></script>


<script>
function bloqueia_tr_um(){
  $("#list_secoes").attr("disabled", true);
  $("#list_divisoes").attr("disabled", true);
  $("#list_grupos").attr("disabled", true);
  $("#list_classes").attr("disabled", true);

  $("#txt_busca_cnae").removeAttr("disabled");
  $(".tr_um").find("td").css("background-color", "#f5f5f5");
  $(".tr_dois").find("td").css("background-color", "#fff");
}
function bloqueia_tr_dois(){
  $("#list_secoes").removeAttr("disabled");
  $("#list_divisoes").removeAttr("disabled");
  $("#list_grupos").removeAttr("disabled");
  $("#list_classes").removeAttr("disabled");

  $("#txt_busca_cnae").attr("disabled", true);
  $(".tr_um").find("td").css("background-color", "#fff");
  $(".tr_dois").find("td").css("background-color", "#f5f5f5");
}
$(document).ready(function(){
  $("input[name='opt_cnae']").on("change", function(){
	var id = $(this).val();
	
	if(id == "1"){
	  bloqueia_tr_dois();
	} else {
	  bloqueia_tr_um();
	}
  })

  $(".bg-black").on("click", function(){
	$(".modal-ext").hide();
	$(".fundocor").css("overflow", "show");
  })

  $( "#txt_busca_cnae" ).autocomplete({
		source: '<?php echo $url_ac; ?>',
		minLength: 2,
		select: function( event, ui ) {
		  console.log(ui.item);
		  $("#id_class_ac").val(ui.item.id);
		}
  });

  $("#list_secoes").on("change", function(){
	var id = $(this).val();
	$.ajax({
	  type: "POST",
	  url: '<?php echo $url; ?>',
	  data: { tipo: 2, valor: id },
	  success: function(data) {
		var html = '<option>Selecione</option>';
		$.each(data, function(index, value){
		  html += '<option value="'+value.id+'">'+value.titulo+'</option>';
		})
		$("#list_divisoes").html(html);
		if(data.length == 1){
		  $("#list_divisoes").trigger("change");
		}
	  }
	})
  })

  $("#list_divisoes").on("change", function(){
	var id = $(this).val();
	$.ajax({
	  type: "POST",
	  url: '<?php echo $url; ?>',
	  data: { tipo: 3, valor: id },
	  success: function(data) {
		var html = '<option>Selecione</option>';
		$.each(data, function(index, value){
		  html += '<option value="'+value.id+'">'+value.titulo+'</option>';
		})
		$("#list_grupos").html(html);
		if(data.length == 1){
		  $("#list_grupos").trigger("change");
		}

	  }
	})
  })

  $("#list_grupos").on("change", function(){
	var id = $(this).val();
	$.ajax({
	  type: "POST",
	  url: '<?php echo $url; ?>',
	  data: { tipo: 4, valor: id },
	  success: function(data) {
		var html = '<option>Selecione</option>';
		$.each(data, function(index, value){
		  html += '<option value="'+value.id+'">'+value.titulo+'</option>';
		})
		$("#list_classes").html(html);
		if(data.length == 1){
		  $("#list_classes").trigger("change");
		}
	  }
	})
  })

  $("#seleciona_cnae").on("click", function(){
	var tipo = $("input[name='opt_cnae']:checked").val();
	
	if(tipo == 1){
	  var classe_id = $("#list_classes").val();
	  var classe_tit = $("#list_classes option:selected").text();

	  $("#nome_classe_cnae").html(classe_tit);
	  $("#id_sel_classe_cnae").val(classe_id);
	} else {
	  $("#nome_classe_cnae").html($("#txt_busca_cnae").val());
	  $("#id_sel_classe_cnae").val($("#id_class_ac").val());

	}

	$(".modal-ext").hide();
	$(".fundocor").css("overflow", "show");

  })

  $("#abre_modal_cnae").on("click", function(){
		$(".modal-ext").show();
		$(".fundocor").css("overflow", "hidden");
  })

  $("#Forma_Cobranca").on("change", function(){
  	var regraCobrancaID = $(this).val();

    if(regraCobrancaID != ''){
      $(".valDescCob-js").show();
    } else{
      $(".valDescCob-js").hide();
    }

		$.ajax({
		  type: "POST",
		  url: '<?php echo $url_cob; ?>',
		  data: { regraCobrancaID: regraCobrancaID },
		  success: function(data) {
		  	if(data.msg != false){
					$('.descReg').val(data.msg);
				} else{
					$('.descReg').val('');
				}

		  }
		})

  });	

})
</script>

<div class="divisoria01"></div>
<div style="clear:both">&nbsp;</div>