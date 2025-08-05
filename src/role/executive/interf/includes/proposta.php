<script>
$(document).ready(function(){
	var source_parc   = $("#entry-template-parcelas").html();
  var template_parc = Handlebars.compile(source_parc);

  $("#gerar_parcelas").on("click", function(){  
    var idInform = <?php echo $idInform; ?>;
    var dtVenc = $("#vencFirst").val();
    var tipoVenc = $("#t_Vencimento").val();
    var qtde = retorna_dinheiro_us($("#Num_Parcelas").val());
    var envia = true;
    var msg_erro = "";

    if(qtde <= 0){
    	envia = false;
    	msg_erro += "<li>Quantidade deve ser maior ou igual a 1.</li>";
    }

    if(tipoVenc == ""){
    	envia = false;
    	msg_erro += "<li>Selecione o tipo de vencimento.</li>";
    }

    if($.trim(dtVenc) == ""){
    	envia = false;
    	msg_erro += "<li>Preencha o vencimento da 1a. parcela.</li>";
    }

    if(envia){
    	var valor = retorna_dinheiro_us($("#novo_valor").html());

	    var total = (valor/qtde);
	    total = total.toFixed(2);
	    var total_geral = 0;

			$.ajax({
			  type: "POST",
			  url: '<?php echo $url_dt_parc; ?>',
			  data: { dtVenc: dtVenc, numParc: qtde, tipoVenc: tipoVenc},
			  success: function(data) {
					$.template_parcelas(data, total, total_geral, qtde, valor);
			  }
			})

	    $(".tb_parcelas_js").show();
    } else {
    	verErro("Corrija os seguintes erros:<br><ul style='list-style: disc;margin-left: 18px;'>"+msg_erro+"</ul>")
    }

    // Busca período de vigência do inform
		$.ajax({
		  type: "POST",
		  url: '<?php echo $url_periodo_vigencia; ?>',
		  data: { idInform: idInform},
		  success: function(data) {
				$(".js-ini_vigencia").val(data['Ini_Vigencia']);
				$(".js-fim_vigencia").val(data['Fim_Vigencia']);
		  }
		})
  })

	jQuery.template_parcelas = function(data, total, total_geral, qtde, valor) {

    $(".parc_int").remove();

    var html_h = '';

    if(qtde > 0){
      for (var i = 0; i < qtde; i++) {

        if(i == (qtde - 1)){
        	total = valor - total_geral;
        } else{
        	total_geral = total_geral + parseFloat(total);
        }

        var context = {numeroParcInt: (i+1), total: number_format_js(total,2,',','.'), dataVencimento: data[i], i: i};
        html_h += template_parc(context);
      }

    } else{
      html_h = '<tr><td colspan="10">Nenhuma parcela gerada.</td></tr>';
    }

    $("#parcelas_campos").after(html_h);
		$.template_parcelas_calendar(qtde);
  }

	jQuery.template_parcelas_calendar = function(qtde) {

    if(qtde > 0){
      for (var i = 0; i < qtde; i++) {

        Calendar.setup({
					inputField     :    "vencParcInt"+i,  // id of the input field
					ifFormat       :    "dd/mm/y",      // format of the input field
					button         :    "imgDtVenc"+i,  	// trigger for the calendar (button ID)
					align          :    "Tl",           // alignment (defaults to "Bl")
					singleClick    :    true
				});

      }
    }

  }

})
</script>

<script id="entry-template-parcelas" type="text/x-handlebars-template">

<tr class="parc_int">
   <td><input type="hidden" name="numParcInt[]" id="numParcInt" class="numParcInt" value="{{numeroParcInt}}">{{numeroParcInt}}</td>
   <td>
			<input type="text" name="vencParcInt[]" class="vencParcInt" id="vencParcInt{{i}}" value="{{dataVencimento}}" onKeyUp="mascara(this,data);" maxlength="10" style="width:245px;">
			<img src="<?php echo $host; ?>images/icone_calendario.png" name="imgDtVenc" id="imgDtVenc{{i}}" alt="" class="imagemcampo" />

   </td>
   <td><input type="text" style="text-align:right;" name="valParcInt[]" id="valParcInt" class="valParcInt" value="{{total}}"></td> 
</tr>

</script>

<br clear="all">

<li class="barrabotoes" style="list-style:none;*margin-left:-15px;">   
	<label><h2>Par&acirc;metros da Proposta</h2></label>
</li>

 
<li class="campo3colunas" style="width:173px;">
  <label>Pr&ecirc;mio M&iacute;nimo <?php echo $extMoeda; ?>:</label>
  <input type="hidden" name="prMimLabel"  id="prMimLabel" style="text-align:right" readonly value=" <?php echo number_format($premioMinimo, 2, ",", ".");?>">
  <div class="formdescricao"><span> <?php echo number_format($premioMinimo, 2, ",", ".");?></span></div>
</li>
<li class="campo3colunas"  style="width:173px;">
<label>Taxa M&iacute;nima %:</label>
<?php  if($prAux == '0') {   ?>
		   <input type="hidden" name="prMimLabel"  readonly value="<?php echo round(100 * $taxaMinima, 3);?>" style="text-align:right">            
			<div class="formdescricao"><span> <?php echo number_format(round(100 * $taxaMinima, 3),3,',','.');?></span>
<?php  } else {   ?>
		   <input type="hidden" name="prMimLabel"  readonly value="<?php echo round(100 * $txAux,2);?>" style="text-align:right">
		   <div class="formdescricao"><span> <?php echo number_format(round(100 * $txAux,2),3,',','.');?></span></div>
<?php  } ?>
   
</li>
<li class="campo3colunas"  style="width:173px;">
	  <label>Aumentar Taxa %: </label>
	  <input onFocus="select()" name="txRise" id="txRise"  value="<?php   echo  number_format(100 * $txRise, 2, ',', '.');?>" onBlur="checkDecimals(this, this.value); calc(this.form)" style="text-align: right;">
</li>
<li class="campo3colunas"  style="width:173px;">
	  <label>Novo Valor <?php echo $extMoeda; ?>: </label>
	  <input type="hidden" name="prDisplay" id="prDisplay" onFocus="blur()" value="<?php echo number_format($prMTotal,2,',','.');?>" style="text-align: right;">
	  <div class="formdescricao"><span id="novo_valor"><?php echo number_format($prMTotal,2,',','.');?></span></div>
</li>
<li class="campo3colunas"  style="width:173px;">
	  <label>Valor total da Taxa %: </label>
	  <input type="hidden" name="txDisplay" onFocus="blur();" value="<?php echo number_format($txMTotal,3,",",".");?>" style="text-align: right;">
	   <div class="formdescricao">
	   <span id="taxa_novo_valor"><?php echo number_format($txMTotal,3,",",".");?></span></div>
</li>
<?php if( ($userID==4425) || ($userID==1953) || ($userID==456) ) { ?>
<li class="campo3colunas">
  <label>N&atilde;o aplicar 10%</label>                                   
	 <div class="formopcao">
		<input type="checkbox" name="xaplicaTaxa" id="xaplicaTaxa" value="1" onClick="exibir_p_cobertura(this)" <?php  if($ic_nao_aplica_taxa=="1") { echo " checked "; } ?> />
	 </div>
</li> 

<?php } ?> 

<div style="clear:both"> </div>




<div style="clear:both">&nbsp;</div>   
<input type="hidden" id="numParc" name="numParc" value="<?php echo $numParc;?>">
<!--
<input type="radio" onClick="calc(this.form)" name = "numParc" value="1" checked>
<li id="clear" class="campo2colunas" style="width:300em">
<div class="formopcao"> 
   <input type="radio" onClick="calc(this.form)" name = "numParc" value="1" checked>
</div><div class="formdescricao"><span>&Agrave; vista</span></div>
 
<?php  if ($vigencia !="2") {  ?>
	   <div class="formopcao">
			<input type="radio" onClick="calc(this.form)" name = "numParc" value="2"<?php   echo  $numParc == 2 ? " checked" : "";?>>
	   </div><div class="formdescricao"><span>02 Parcelas &agrave; vista/90 dias</span></div>
	   
<?php  }  ?>

<?php  if ($vigencia !="2") {   ?>
	<div class="formopcao">
		<input type="radio" onClick="calc(this.form)" name = "numParc" value="4"<?php   echo  $numParc == 4 ? " checked" : "";?>>
	</div><div class="formdescricao"><span>04 Parcelas &agrave; vista/90/180/270 dias</span></div>
<?php  }else{    ?>
		<div class="formopcao">
			<input type="radio" onClick="calc(this.form)" name = "numParc" value="4"<?php   echo  $numParc == 4 ? " checked" : "";?>>
		</div><div class="formdescricao"><span>04 Parcelas Trimestrais</span></div>
<?php  } ?>
 
<div class="formopcao">
   <input type="radio" onClick="calc(this.form)" name = "numParc" value="7"<?php   echo  $numParc == 7 ? " checked" : "";?>>
</div><div class="formdescricao"><span>07 Parcelas mensais</span></div>

<?php  if ($vigencia == "2"){   ?>
<div class="formopcao">
   <input type="radio" onClick="calc(this.form)" name = "numParc" value="8"<?php   echo  $numParc == 8 ? " checked" : "";?>>
</div><div class="formdescricao"><span>08 Parcelas Trimestrais</span></div>
  
<?php  } ?>
</li>
-->
<li class="campo3colunas">
 <label>N&uacute;mero de Parcelas</label>
 <input type="text" name="Num_Parcelas" id="Num_Parcelas" value="<?php echo $Num_Parcelas;?>" style="text-align:right;" onBlur="checaNumero(this,this.value);">
</li>
<li class="campo3colunas">
 <label>Tipo de Vencimento</label>
 <select name="t_Vencimento" id="t_Vencimento">
	<option value="">Selecione...</option>
	<option value="1" <?php if($t_Vencimento == 1) echo "selected";?>>Mensal</option>
	<option value="2" <?php if($t_Vencimento == 2) echo "selected";?>>Bimestral</option>
	<option value="3" <?php if($t_Vencimento == 3) echo "selected";?>>Trimestral</option>
	<option value="4" <?php if($t_Vencimento == 4) echo "selected";?>>Semestral</option>
 </select>
</li>

<li class="campo3colunas"> 
<label>Vencimento da 1&ordf; parcela:</label>
	<!--<select name="vencFirst" id="vencFirst">
			<?php 
			
			$dia = date('d');
			$mes = date('m');
			$ano = date('Y');
			for($j = 1; $j <= 28; $j++){
				if ($j >= $dia){
				$tmp = date('d/m/Y', mktime(0, 0, 0, $mes, $j, $ano));
				   echo "<option value=\"$tmp\"> $tmp\n";
				}
			 }
			for($j = 1; $j <= 28; $j++){
				if ($j < $dia){
				$tmp = date('d/m/Y', mktime(0, 0, 0, $mes + 1, $j, $ano));
				   echo "<option value=\"$tmp\"> $tmp\n";
				}
			 }
			
			?>
	 </select>
	 -->
	 <input type="text" name="vencFirst" id="vencFirst" value="<?php echo ($DataPrimeiraParcela ? $DataPrimeiraParcela : date('d/m/Y'));?>" onKeyUp="mascara(this,data);" maxlength="10" style="width:245px;">
	 <img src="<?php echo $host; ?>images/icone_calendario.png" name="imgDtinicio" id="imgDtinicio" alt="" class="imagemcampo" />
	<script type="text/javascript">
		Calendar.setup({
			inputField     :    "vencFirst",     	// id of the input field
			ifFormat       :    "dd/mm/y",      	// format of the input field
			button         :    "imgDtinicio",  	// trigger for the calendar (button ID)
			align          :    "Tl",           	// alignment (defaults to "Bl")
			singleClick    :    true
		});
	</script>

</li>



<button class="botaoagg" type="button" id="gerar_parcelas">GERAR PARCELAS</button>
	
	<input type="hidden" name="js-ini_vigencia" id="js-ini_vigencia" class="js-ini_vigencia">
	<input type="hidden" name="js-fim_vigencia" id="js-fim_vigencia" class="js-fim_vigencia">

<?php
	$sql = "SELECT P.i_Parcela As IdParcela, P.n_Parcela As NumParcela, P.d_Vencimento As DataVencimento, P.v_Parcela As ValorParcela 
					FROM Parcela P 
					WHERE P.i_Inform = ".$idInform." AND P.t_Parcela = 100	AND P.n_Endosso = 0 
					ORDER BY P.n_Parcela";
	$resultParc = odbc_exec($db,$sql); 
	$numResultParc = odbc_num_rows($resultParc); ?>
	
<br><br>
<table class="tb_parcelas_js"  id="dataTable">
	<thead>
	    <tr>
					<th style="width: 240px;">N&deg;</th>
	        <th style="width:310px;">Data Vencimento</th>
	        <th>Valor da Parcela <?php echo $extMoeda; ?></th>
	    </tr>
	</thead>        
	<tbody id="parcelas_campos">
	 	<?php	
	 		$a = 0;
	 		while(odbc_fetch_row($resultParc)){ ?>
	    <tr class="parc_int" id="parc_int">
	       <td><input type="hidden" name="numParcInt[]" id="numParcInt" class="numParcInt" value="<?php echo odbc_result($resultParc, 'NumParcela'); ?>"><?php echo odbc_result($resultParc, 'NumParcela'); ?></td>
	       <td>
		 			<input type="text" name="vencParcInt[]" class="vencParcInt" id="vencParcInt<?php echo $a; ?>" value="<?php echo date("d/m/Y", strtotime(odbc_result($resultParc, 'DataVencimento'))); ?>" onKeyUp="mascara(this,data);" maxlength="10" style="width:245px;">
		 			<img src="<?php echo $host; ?>images/icone_calendario.png" name="imgDtVenc" id="imgDtVenc<?php echo $a; ?>" alt="" class="imagemcampo" />
					<script type="text/javascript">
						Calendar.setup({
							inputField     :    "vencParcInt<?php echo $a; ?>", // id of the input field
							ifFormat       :    "dd/mm/y",      								// format of the input field
							button         :    "imgDtVenc<?php echo $a; ?>",  	// trigger for the calendar (button ID)
							align          :    "Tl",           								// alignment (defaults to "Bl")
							singleClick    :    true
						});
					</script>
	       </td>
	       <td><input type="text" style="text-align:right;" name="valParcInt[]" id="valParcInt" class="valParcInt" value="<?php echo number_format(odbc_result($resultParc, 'ValorParcela'),2,',','.'); ?>"></td> 
	    </tr>
		<?php 
			$a++;
		} ?>
	</tbody>
</table>
<br><br>



<li class="campo3colunas">
<label>Per&iacute;odo m&aacute;ximo de Cr&eacute;dito:</label>
 <input name="periodMaxCred" id="periodMaxCred" value="<?php echo ($periodMaxCred > 0 ? $periodMaxCred : 180);?>"  onBlur="ValMaxCredit(this.form);" style="text-align: right">
</li>


<!-- Alterado Hicom (Gustavo) -->
<?php  
if (false) {  // somente o executivo responsável pode escolher a data  ?>
   <li class="campo3colunas">
   <label>Dia das demais parcelas:</label>
	<select name="diaFaturas" id="diaFaturas">
		<OPTION value="1" selected>1</OPTION>
		<OPTION value="2">2</OPTION>
		<OPTION value="3">3</OPTION>
		<!--		... -->
  </select>
  </li>
<?php  }  ?>



<li class="campo3colunas" style="height:83px;*height:86px;">        
 <label>Fator LMI</label>
 <input type="text" name="limPagIndeniz" id="limPagIndeniz" style="text-align:right" value="<?php echo ($limPagIndeniz ? $limPagIndeniz : '0');?>">
</li>

<li class="campo3colunas" style="height:83px;*height:86px;">        
 <label>Valor LMI Flat <?php echo $extMoeda; ?></label>
 <input type="text" name="ValorLMI" id="ValorLMI" style="text-align:right" onBlur="checkDecimals(this, this.value)" value="<?php echo  number_format($ValorLMI, 2, ',', '.');?>">
</li>
<br clear="all">
<li class="campo3colunas">
<label>Cobertura &nbsp;%:</label>
<input  name="percCoverage" value="<?php echo number_format($cobertura ? $cobertura : 85, 0, '', '');?>"  size=4 style="text-align: right;">
</li>

<li class="campo3colunas">
<label>Taxa Des&aacute;gio&nbsp;%:</label>
<input  name="p_Taxa_Desagio" id="p_Taxa_Desagio" onBlur="checkDecimals(this, this.value)" value="<?php if(!$p_Taxa_Desagio) echo "0,00"; else echo number_format($p_Taxa_Desagio,2,',','.');?>"  style="text-align: right;">
</li>

<li class="campo3colunas">
 <label>Unidade de Produ&ccedil;&atilde;o:</label>
	<SELECT name="prodUnit">
		<option value="62"<?php   echo  $prodUnit == 62 ? " selected" : "";?>>Matriz-062</option>
		<option value="202"<?php   echo  $prodUnit == 202 ? " selected" : "";?>>Filial SP-202</option>
		<option value="204"<?php   echo  $prodUnit == 204 ? " selected" : "";?>>Filial RS-204</option>
	</SELECT>
</li>
<br clear="all" />
<li class="campo3colunas">
<label>Validade da Cota&ccedil;&atilde;o: Dias</label>
<input name="validCot" style="text-align: right;" value="<?php  echo $validCotD != "" ? $validCotD : "90"; ?>">
</li>
<li class="campo3colunas">
<label>Per&iacute;odo de declara&ccedil;&atilde;o de DVN:</label>
  <select name="tipoDve">
	  <?php

	  	$selmensal = '';
		$seltrimestral = '';
		$selAnual = '';

		if ($tipoDve == "1") {
		   $selmensal = "selected";
		}else if ($tipoDve == "2") {
		   $seltrimestral = "selected";
		}else  if ($tipoDve == "3"){
			$selAnual = "selected";
		}
	  ?>
	  <option value="1" <?php   echo $selmensal;?> >Mensal</option>
	  <option value="2" <?php   echo $seltrimestral;?> >Trimestral</option>
	  <option value="3" <?php   echo $selAnual;?> >Anual</option>
  </select>
</li>

<li class="campo3colunas">
<label>Prazo de entrega de DVN:</label>
	<!--<select name="vencFirst" id="vencFirst">
			<?php 
			
			$dia = date('d');
			$mes = date('m');
			$ano = date('Y');
			for($j = 1; $j <= 28; $j++){
				if ($j >= $dia){
				$tmp = date('d/m/Y', mktime(0, 0, 0, $mes, $j, $ano));
				   echo "<option value=\"$tmp\"> $tmp\n";
				}
			 }
			for($j = 1; $j <= 28; $j++){
				if ($j < $dia){
				$tmp = date('d/m/Y', mktime(0, 0, 0, $mes + 1, $j, $ano));
				   echo "<option value=\"$tmp\"> $tmp\n";
				}
			 }
			
			?>
	 </select>
	 -->
	 <input type="text" name="prazoEntDvn" id="prazoEntDvn" value="<?php echo $data_entrega_dvn;?>">

</li>
<br clear="all" />
<li class="campo3colunas">
 <label>Limite M&iacute;n. para Notif. de Amea&ccedil;a de Sinistro &nbsp;<?php   echo $extMoeda;?></label>
	<input name="nas" style="text-align: right;" onBlur="checkDecimals(this, this.value)" value="<?php   echo number_format($nas, 2, ',','.');?>">
</li>
<li class="campo3colunas" style="height:70px">
 <label>Tipo Ap&oacute;lice</label>
	<select name="tipoapolice" onFocus="tipoApl()" onChange="tipoApl()">
	  <?php
	  $ap = '';
	  $g = '';
	  
		if ($ga != ""){
			if ($ga == "0"){
				$ap = "Selected";
			}else{
				$g  = "Selected";
			}
		}else{
			$g  = "Selected";
		} 
		?>
	   <option value="1" <?php echo $g;?>>GA</option>
	   <option value="0" <?php echo $ap;?>>Apol. Antiga</option>
   </select>
</li>

<li class="campo3colunas">
<label>Banco</label>
 <select name="tipobanco" id="tipobanco" onChange="if ( this.value == 0 ) { desmarca_bb( ) } ; tipoBanco(0)">
		<?php

		$tp_banco_bb = '';
		$tp_banco_outros = '';
		$tp_banco = isset($tp_banco) ? $tp_banco : '';
		if ($tp_banco!=""){
			if ($tp_banco=="0"){
				$tp_banco_bb = "Selected";
			}else{
				$tp_banco_outros = "Selected";
			}
		}else{
			$tp_banco_outros = "";
		}
	
	   ?>
	   <option value="">Selecione</option>
	   <option value="0" <?php echo $tp_banco_bb; ?> >BB</option>
	   <option value="1" <?php echo $tp_banco_outros;?> >Outros</option>
   </select>
</li> 
<br clear="all" />
 <!-- 
 <li class="campo3colunas"> 
  <label>Contrato Resseguro</label>
  <?php 
	  $sql = " select 
					CR.i_Contrato_Resseguro,
					CR.Desc_Contrato
				From 
					Inform Inf 
				Inner Join Empresa_Produto EP On
					EP.i_Produto = Inf.i_Produto
				Inner Join Contrato_Resseguro CR On
					CR.i_Empresa = EP.i_Empresa 
				Where
					GetDate() Between CR.Inicio_Vigencia And CR.Fim_Vigencia
					And Inf.id = ".$idInform."
				ORder By
					Desc_Contrato
				 ";
	 $cur1 = odbc_exec($db,$sql);
	 
  ?>
  <select name="i_Contrato_Resseguro" id="i_Contrato_Resseguro" onChange="">
	   <option value="">Selecione...</option>
	   <?php while ($dados = odbc_fetch_row($cur1)){ 
				 if($i_Contrato_Resseguro == odbc_result($cur1,'i_Contrato_Resseguro'))
					 $selt = 'selected';
				 else
					$selt = ''; 
	   ?>
			  <option value="<?php echo odbc_result($cur1,'i_Contrato_Resseguro');?>"  <?php echo $selt;?>><?php echo (odbc_result($cur1,'Desc_Contrato'));?></option>
	   
	   <?php } ?> 
 </select>
</li> 
-->
 
<li class="campo3colunas">  
 <?php
	   
  $sql  = "select GC.i_Gerente, GC.Nome from 
			Gerente_Comercial GC  
			Where Situacao = 0 Or Exists 
		   (Select * From Inform Inf Where Inf.i_Gerente = GC.i_Gerente And Inf.id = 6065) Order By GC.Nome ";
		$cur2 = odbc_exec($db,$sql);
   
	$lista_gerentes = array();
	while ($dados = odbc_fetch_row($cur2)){ 
		$lista_gerentes[]  = array("id" => odbc_result($cur2,'i_Gerente'), "nome" => odbc_result($cur2,'Nome'));
	}
   
 ?>
 <label>Gerente Originador</label>
  <select name="i_Gerente" id="i_Gerente" onChange="">
			<option value="">Selecione...</option>

			<?php 
				for ($i=0; $i < count($lista_gerentes); $i++) { 
					if($i_Gerente == $lista_gerentes[$i]["id"]) {
					 $selt = 'selected';
					} else {
					  $selt = ''; 
					}
					echo '<option value="'.$lista_gerentes[$i]["id"].'"  '.$selt.'>'.$lista_gerentes[$i]["nome"].'</option>';
				} 
			?>
	</select>
</li> 

<li class="campo3colunas">  
 <?php
 ?>
 <label>Gerente de Relacionamento</label>
  <select name="i_GerenteR" id="i_GerenteR" onChange="">
		<option value="">Selecione...</option>

		<?php 
		for ($i=0; $i < count($lista_gerentes); $i++) { 
			if($i_Gerente_Relacionamento == $lista_gerentes[$i]["id"]) {
			 $selt = 'selected';
			} else {
			  $selt = ''; 
			}
			echo '<option value="'.$lista_gerentes[$i]["id"].'"  '.$selt.'>'.$lista_gerentes[$i]["nome"].'</option>';
		} 
		?>
	</select>
</li> 

<?php //if($i_Produto == 1){ ?>   
		   <li class="campo3colunas">        
			   <label>Prazo M&aacute;ximo para emiss&atilde;o da Nota Fiscal</label>
				 <input type="text" name="PrazoMaxEmiNota" id="PrazoMaxEmiNota" style="text-align:right" value="<?php echo number_format($PrazoMaxEmiNota,0,'','');?>">
		   </li>
<?php //} ?>  