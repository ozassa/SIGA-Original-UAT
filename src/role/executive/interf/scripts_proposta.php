<script>
	 function ContaCaracteres(field,MaxLength,id){
					 
				 var total = 0;
				 var str = document.getElementById(field).value;
				
				 Qtdecart = document.getElementById(field).value.length;
				 //alert('oi');
				 if (MaxLength !=0) {
					 if (Qtdecart > MaxLength)  {
						 //verErro('Este campo suporta apenas 1000 caracteres.');
						 str = str.substring(0, MaxLength);
						 document.getElementById(field).value = str;
					 }
				 }
				 
				 total = MaxLength - document.getElementById(field).value.length;
				 if(total <= MaxLength && total >=0){
					 $('#cont' + id).val(total);
				 }
				 document.getElementById(field).value.substring(0, MaxLength);		 
			 }
			 
			 
			 function limitarCaracteresInfo(field, MaxLength,id){
				 //alert(id);				
				 var str = document.getElementById(field).value;
				 str = str.substring(0, MaxLength);
				 document.getElementById(field).value = str;
				 
				 total = MaxLength - document.getElementById(field).value.length;
				 
				 if(total <= MaxLength && total >=0){
					 $('#cont' + id).val() = total;
				 }
				 
				 //$('#cont' + id).val() =  document.getElementById(field).val().length;
			 }
		
			 
 </script>
 
<script type="text/javascript" charset="">

   
	function converterValorFlutuante(vlr){
		var valor = 0;
		if(vlr != ''){
			valor = vlr.replace('.','');
			valor = valor.replace(',','.');
			
		}
		return valor;
	}

  
	function nParc (form) {
	
<?php if ($vigencia != "2") {
	  ?>
	  if (form.numParc[0].is(':checked')) {
		numParc=1;
	  } else
	  if (form.numParc[1].is(':checked')) {
		numParc=2;
	  } else if (form.numParc[2].is(':checked')) {
		numParc=4;
	  } else if (form.numParc[3].is(':checked')) {
		numParc=7;
	  } else if (form.numParc[4].is(':checked')) {
		numParc=10;
	  }
	  return numParc;
	  <?php  }else if ($vigencia == "2"){
	  ?>
	  if (form.numParc[0].is(':checked')) {
		numParc=1;
	  } else if (form.numParc[1].is(':checked')) {
		numParc=4;
	  } else if (form.numParc[2].is(':checked')) {
		numParc=7;
	  } else if (form.numParc[3].is(':checked')) {
		numParc=8;
	  }
		return numParc;
	  <?php  }
	  ?>
	  
	}
	
	
	function validadoFormCorretor(){		      
		  if($('#Corretor1').val() == "" ){
			 $('#Corretor1').focus();
			 verErro('O Corretor deve ser selecionado.');
			 return false;
			 
		  }else if($('#i_Grupo').val() == "" ){
			 $('#i_Grupo').focus();
			 verErro('O grupo do corretor deve ser selecionado.');
			 return false;
		  }else if($('#Comissao').val() == "0,00" ){
			 $('#Comissao').focus();
			 verErro('Um percentual de comiss&atilde;o deve ser informado.');
			 return false;
		  }else if($('#Participacao').val() == "0,00" ){
			 $('#Participacao').focus();
			 verErro('Um percentual de participa&ccedil;&atilde; deve ser informado.');
			 return false;
		  }else{
			 return true;  
		  }
	}
	 
	 
	function adicionaValorPerc1(valor){
		 $('#per1').val() = valor;
	}
	
	function adicionaValorPerc2(valor){
		$('#per2').val() = valor;
		
	}
	 
	 
	  
	function consist (form) {
		var certo  = 0;
	
	
	
	
	
	  msg = "";
	  nP  = $("#Num_Parcelas").val();
	  //calc(form);
	  if (nP > 1 &&  numVal(form.prDisplay.value) / nP < 1000) {
		   msg += "Valor da parcela abaixo de <?php   echo $extMoeda;?> 1.000,00";
	  }      
	  
	  //ALTEREI DOCUMENT.ALL.COMM.VALUE POIS APRESENTA ERRO NO FIREFOX - RODOLFO TELES 06/07/2012
	  if ($('#comm').val()=='viewProp'){
		
	  }else{
			if (document.all.nas.value == '0,00') {
				 verErro('Informe um valor para Notifica��o de Amea�a de Sinistro.');
				 return(false);
			}
	  }
	
	  if($('#origemNegocio').val() == ""){
		 verErro('Aten��o: Deve ser informada a origem do neg�cio!');
		 $('#origemNegocio').focus();
		 return false;
	  }

	  if($("#limPagIndeniz").val() == "0" || $("#limPagIndeniz").val() == ""){
      var lmi_fator = false;
    } else {
      var lmi_fator = true;
    }
    if($("#ValorLMI").val() == "0,00" || $("#ValorLMI").val() == ""){
      var lmi_valor = false;
    } else {
      var lmi_valor = true;
    }
		if(!lmi_fator && !lmi_valor){
      verErro("Informe o LMI.");
      return false;
    }

    if(lmi_fator && lmi_valor){
      verErro("Informe somente um LMI.");
      return false;
    }
	
	  
	  
	  if($('#Forma_Cobranca').val() == ""){
		  verErro('Aten��o: Deve ser informada a Regra de Cobran�a!');
		  $('#Forma_Cobranca').focus();
		  return false;
	  }else if($('#Forma_Cobranca').val() != "" && parseInt($('#Forma_Cobranca').val()) == 4){
	   
		   if($('#Numero_Parcelas').val() == "" || parseInt($('#Numero_Parcelas').val()) == 0){
			  verErro('Aten��o: Deve ser informada quantidade de parcelas da An�lise e Monitoramento!');
			  $('#Numero_Parcelas').focus();
			  return false;
		   }
		   
		   if($('#Tipo_Vencimento').val() == ""){
			  verErro('Aten��o: Deve ser informado o tipo de Vencimento!');
			  $('#Tipo_Vencimento').focus();
			  return false;
		   }
		   
			if($('#Primeiro_Vencimento').val().length < 10){
			  verErro('Aten��o: Deve ser informada a Data Primeiro Vencimento no formato DD/MM/AAAA');
			  $('#Primeiro_Vencimento').focus();
			  return false;
		   }
		   
			
	  }
	  
	  if($("#idModuloB4.04").is(":checked")){
		  
		 if($("#GerenteCredito").val() == ""){
			 verErro('Aten��o: � necess�rio informar o Gerente de Cr�dito.');
			 return false;
		 }
		 
		 if(!parseFloat(converterValorFlutuante($("#b404NivelMax").val())) >0){
			 verErro('Aten��o: � necess�rio informar o valor do n�vel m�ximo de cr�dito.');
			 return false;
		 
		 }if(!parseFloat(converterValorFlutuante($("#b404Perc").val())) >0){
			 verErro('Aten��o: � necess�rio informar a percentagem segurada.');
			 return false;
		 }
		 
		  
	  }
	 
	  var premio  = 0;
	 // alert($('#prDisplay').val());
	  premio  = $('#prDisplay').val().replace(".","");
	  premio  = premio.replace(".","");
	  premio  = premio.replace(".","");
	  premio  = premio.replace(",",".");
	  premio  = parseFloat(premio);
	  
	 
	  //if(parseFloat($('#TotalVigPremio').val()) > premio || parseFloat($('#TotalVigPremio').val()) < premio){
		//   alert(parseFloat($('#TotalVigPremio').val()));
		//   alert(premio);
		//   verErro("Aten��o: A Totaliza��o dos Valores de Vig�ncia deve ser igual ao Premio.");
		   //$('#texto_risco_politico').focus();
		//   return false;  
	  //}
	  
	  
	  if($('#i_Produto').val() == "1"){ // Valida��o se for  produto interno
		  
			   
		  if($('#PrazoMaxEmiNota').val() == "" || $('#PrazoMaxEmiNota').val() == "0" ){
			  
				   /*verErro('Aten��o: Deve ser informado o prazo m�ximo para emiss�o da nota fiscal!');
				   $('#PrazoMaxEmiNota').focus();
				   return false;*/
				   $('#PrazoMaxEmiNota').val(0);
		  }

		  if ($('#Renovacao_Tacita').val() != 0) {
			  if($('#prazo_nao_intencao_renov').val() == "0" || $('#prazo_nao_intencao_renov').val() == "" ){
	        
	           verErro('Aten��o: Deve ser informado o prazo para aviso da n�o inten��o de renova��o!');
	           $('#prazo_nao_intencao_renov').focus();
	           return false;
	      }
		  }

		  if($('#idModuloB8.02').is(':checked') && $('#TemEmpComprador').val() == "0"){			      
				  
				   verErro('Aten��o: Deve ser informado pelo menos um Comprador !');
				   //$('#PrazoMaxEmiNota').focus();
				   return false;
		  }
		  
		 
		  if($('#idModuloB26.04').is(":checked")){
			  
				   if($('#b2604NivelMax').val() == "0,00" || $('#b2604NivelMax').val() == ""){					  
					   verErro('Aten��o: Dever ser informado o valor do N�vel m�ximo de cr�dito !');
					   $('#b2604NivelMax').focus();
					   return false;
				   }
				   
				   if($('#b2604Perc').val() == "0,00" || $('#b2604Perc').val() == ""){					  
					   verErro('Aten��o: Dever ser informado o valor da percentagem segurada!');
					   $('#b2604Perc').focus();
					   return false;
				   }
		  }
		  
		  if($('#idModuloB28.01').is(":checked")){
			  
				   if($('#b2801NivelMax').val() == "0,00" || $('#b2801NivelMax').val() == ""){					  
					   verErro('Aten��o: Deve ser informado o n�vel m�ximo de cr�dito referente � cl�usula 1  !');
					   $('#b2801NivelMax').focus();
					   return false;
				   }
				   
				   if($('#b2801Perc').val() == "0,00" || $('#b2801Perc').val() == ""){					  
					   verErro('Aten��o: Dever ser informada a percentagem segurada referente � cl�usula 1.6 !');
					   $('#b2801Perc').focus();
					   return false;
				   }
		  }
		  
		  if($('#idModuloD2.01').is(":checked")){
			  
				   if($('#d201').val() == "0,00" || $('#d201').val() == ""){					  
					   verErro('Aten��o: Deve ser informado o valor da franquia!');
					   $('#d201').focus();
					   return false;
				   }
				   
		
		  } 
		  
		  if($('idModuloF4.01').is(":checked")){
			  
				   if($('#f401NivelSinistralidade').val() == "0,00" || $('#f401NivelSinistralidade').val() == ""){					  
					   verErro('Aten��o: Deve ser informado o b�nus por n�vel de sinistralidade!');
					   $('#f401NivelSinistralidade').focus();
					   return false;
				   }
				   
				   if($('#f401PercPremio').val() == "0,00" || $('#f401PercPremio').val() == ""){					  
					   verErro('Aten��o: Deve ser informada percentagem de pr�mio!');
					   $('#f401PercPremio').focus();
					   return false;
				   }
				   
		
		  } 
		  
	  }
	 
		  
	  if($('#i_Sub_Produto').val() == ""){
		  verErro("Selecione o Sub-Produto para prosseguir com o preenchimento deste formul�rio.");
		  $("#i_Sub_Produto").focus();
		  return false;
	  }
	  
	   
		   
	  if($('#temCorretorPrincipal').val() == "0" ){
		  
			   verErro('Aten��o: Deve ser informado pelo menos um corretor como Principal!');
			   $('#Corretor1').focus();
			   return false;
	  }

	  if ($.trim($("#id_sel_classe_cnae").val()) == ""){
		  $('html, body').animate({
			  scrollTop: $("#abre_modal_cnae").offset().top-300
		  }, 300);
		  verErro("Por favor, selecione a classe do CNAE");
		  return (false);
		}

		if($("#Num_Parcelas").length > 0){
			if ($.trim($("#Num_Parcelas").val()) == ""){
			  $('html, body').animate({
			      scrollTop: $("#Num_Parcelas").offset().top-300
			  }, 300);
			  verErro("Por favor, preencha o n�mero de parcelas.");
			  return (false);
			}
		}
	  
	  var total =  parseFloat($('#TotalParticipacao').val().replace(",",".")); 
	  
	  if (total < 100 || total > 100){
		  //alert(total);
		  verErro('A soma da participa��o n�o poder ser menor ou maior que 100% !');
		  $('#Corretor1').focus();
		  return false;
	  }
	  
	  
	  
	  // tipo banco
	  if ($('#tipobanco').val() == "") {
		 verErro('Selecione o Banco.');
		 $('#tipobanco').focus();
		 return(false);
	  }
	
	  if ($('#i_Gerente').val() == "") {
		 verErro('Selecione o Gerente Comercial.');
		 $('#i_Gerente').focus();
		 return(false);
	  }
	  if ($('#i_GerenteR').val() == "") {
		 verErro('Selecione o Gerente de Relacionamento.');
		 $('#i_GerenteR').focus();
		 return(false);
	  }
	  if ($('#prazoEntDvn').val() == "") {
		 verErro('Preencha Prazo de entrega de DVN.');
		 $('#prazoEntDvn').focus();
		 return(false);
	  }
	  /*
	  if ($('#i_Contrato_Resseguro').val() == "") {
		 verErro('Selecione o Contrato Resseguro.');
		 $('#i_Contrato_Resseguro').focus();
		 return(false);
	  }
	  */
	  if ($('#p_Taxa_Desagio').val() == "0,00" || $('#p_Taxa_Desagio').val() == "" ) {
		 verErro('Informe a taxa de Des&aacute;gio.');
		 $('#p_Taxa_Desagio').focus();
		 return(false);
	  }
	  
	  if($('#Ad').is(':checked') == true){
		  
		  var ad_sinistr = parseFloat($('#ad_sinistr').val().replace(",","."));
		  var ad_premio = parseFloat($('#ad_premio').val().replace(",","."));
		  

		 if (! ad_sinistr > 0){
			verErro("Voc� deve informar valor no campo Adequac�o de sinistralidade.");
			return(false);
		 }else if (! ad_premio > 0){
			verErro("Voc� deve informar valor no campo Adequac�o de pr�mio.");
			return(false);
		 }
	
	  }
	
	  if($('#idModuloD4.01').is(":checked")){
		 var franq = parseFloat(document.all.franquia_anual.value);
		 if (! franq > 0){
			verErro("Voc� deve informar o valor da franquia anual Global.");
			return(false);
		 }
	  }
	
	  if($('#condesp_ck').is(':checked') == true){
		 if(document.all.condicoes_especiais.value.length == 0){
			verErro("Voc� deve preencher o campo condic�es complementares.");
			return(false);
		  }
	
	
	  }
	  /*
	  if(document.all.condesp_ck_col.is(':checked') == true){
		 if(document.all.rz_col1.value == "" ){
			verErro("Voc� deve preencher o campo Raz�o Social da empresa coligada.");
			return(false);
		 }
		  
	  }
	  */
	
	  if($('#periodMaxCred').val() == ""){
		 verErro('Aten��o: Deve ser informada a dura��o neste campo.');
		 $('#periodMaxCred').focus();
		 return(false); 
	  }
	  /*
	  if (document.all.opt.is(':checked') == true) {
		 msgb = "";
		 if (document.all.per.value == "" || document.all.per.value == "0") {
			verErro('Campo percentual esta vazio ou zerado.');
			return(false);
		 }else{
			msgb = "\n e " + document.all.per.value + "% B�nus por Aus�ncia de sinistros\n" +
			"vinculado a renova��o da ap�lice por mais um per�odo de seguro";
		 }
	
	  }else{
		   msgb = "";
	  }
	*/
	  

	  // d602
	  if ($('#idModuloD6.02').is(":checked")) {
		if($('#nivel_d602').val() == "" || $('#nivel_d602').val() == "0") {
			verErro('Campo de N�vel de Pequenos Sinistros est� vazio ou zerado.');
			return(false);
		}else{
	//        msgp += "\n N�vel de Pequenos Sinistros : US$ " + $('#nivel_d602').val();
		}
	  }
	
	  // d701
	  if($('#i_Produto').val() != 1){
		  if ($('idModuloD7.01').is(":checked")) {
			if($('#p_cobertura_d701').val() == "" || $('#p_cobertura_d701').val() == "0") {
				verErro('Campo de % de Cobertura est� vazio ou zerado.');
				return(false);
			}else{
				//msgp += "\n % de Cobertura : " + document.getElementById("p_cobertura_d701").val() + "%";
			}
		  }
	  }
	  msgb = '';
	  msgp = '';
	  /*
	  if (document.all.partic.is(':checked') == true) {
		 msgp = "";
		 msgp = "\n e Participa��o nos lucros com ";
		 if (document.all.per1.value == "" ||
					document.all.per1.value == "0" ) {
			verErro('Campo de percentagem de dedu��o esta vazio ou zerado.');
			return(false);
		 }else{
			msgp += "\n Percentagem de Dedu��o : " + document.all.per1.value + "%";
		 }
	
	
		 if (document.all.per2.value == "" || document.all.per2.value == "0") {
			verErro('Campo de Participa��o nos lucros esta vazio ou zerado.');
			return(false);
		 }else {
			msgp += "\n e Participa��o nos Lucros : " + document.all.per2.value + "%";
		 }
	
		 if (!document.all.plucro[0].is(':checked') && !document.all.plucro[1].is(':checked') && !document.all.plucro[2].is(':checked') ){
			 verErro('Selecione um tipo de Participa��o nos Lucros.');
			 return(false);
		 }
	  }else{
		 msgp = "";
	  }
	
	  */
	
	/*
	 if (form.txAnalize.value == '0,00'){
	   verErro("Informe um valor para a Taxa de An�lise");
	   form.txAnalize.focus();
	   return (false);
	 }
	*/
	  if($('#Renovacao_Tacita').val() == ''){
		   verErro("Aten��o: � necess�rio o preenchimento do campo Renova��o T�cita.");
		   $('#Renovacao_Tacita').focus();
		   return false;  
		  
	  }
	  
	  if($('#rico_pol_ck').is(':checked') && $('#texto_risco_politico').val() == ""){
		   verErro("Aten��o: � necess�rio o preenchimento do campo Risco Pol�tico.");
		   $('#texto_risco_politico').focus();
		   return false;  
	  } 

	  //TOTAL PARCELAS
		if($('#parcelas_campos').parent().find(".valParcInt").length){
			var total = 0;
			$('#parcelas_campos').parent().find(".valParcInt").each(function(){
				var valor = retorna_dinheiro_us($(this).val());
				if(valor == ''){
					var valor = 0;
				}

				total = ((total) + parseFloat(valor));

			});
			
			var totalProp =	retorna_dinheiro_us(form.prDisplay.value);
			if(Math.round(total) != Math.round(totalProp)){
				verErro("Aten��o: � necess�rio que o Valor das Parcelas seja igual ao Valor Total.");
		   		return false;  
			}
			
			var ini_vigencia = $("#js-ini_vigencia").val().split("/");
			ini_vigencia = new Date(ini_vigencia[2], ini_vigencia[1] - 1, ini_vigencia[0]);

    	var fim_vigencia = $("#js-fim_vigencia").val().split("/");
			fim_vigencia = new Date(fim_vigencia[2], fim_vigencia[1] - 1, fim_vigencia[0]);

    	var parc_ap = [];
    	var a = 0;

	    $(".vencParcInt").each(function(){
	    	var num_parc = $(this).parent().parent().find(".numParcInt").val();

	    	var venc_parc = $(this).val().split("/");
	    	venc_parc = new Date(venc_parc[2], venc_parc[1] - 1, venc_parc[0]);
    		
	    	if (venc_parc < ini_vigencia || venc_parc > fim_vigencia) {
	    		parc_ap[a] = num_parc;
	    		a++;
	    	}
	    })

	    var num_parc_ap = parc_ap.length;

	    if (num_parc_ap) {
	    	var parc_join = parc_ap.join(', ');

	    	if (num_parc_ap > 1) {
	   			verErro("A data de vencimento das parcelas "+parc_join+" deve estar dentro da vig�ncia da ap�lice.");
	   		} else{
	   			verErro("A data de vencimento da parcela "+parc_join+" deve estar dentro da vig�ncia da ap�lice.");
	   		}
	    	return false;
	    } 
		}

	   
	  if (msg != "") {
		verErro("Favor corrigir o n�mero de parcelas:\n"+ msg);
		return false;
		
	  } else {
		
		if (form.tipoapolice.value==1 && form.jurosmora.value==1){
			  if (confirm ("Confirma o valor de An�lise = R$ " + form.txAnalize.value+ "\n" +
						 "e Monitoramento = R$ " + form.txMonitor.value + msgb + msgp + "?")) {
				   HabilitaCamposBB();
				   form.submit();
				 
			  }
		}else{
			if (confirm ("Confirma o valor de An�lise = R$ " + form.txAnalize.value+ "\n" +
						 "e Monitoramento = R$ " + form.txMonitor.value + msgb + msgp + "?")) {
			   HabilitaCamposBB();
			   form.submit();
			  
			}
		}
	
	  }
	  
	 
	  
	 
	  
	  
	  
  }
	
 
	
 function exibir_p_cobertura(valor){
	var valorTotal = valor.form.prDisplay.value.replace(".","").replace(",",".");
	var novoPrMin = 0;
	var novoTxPrMin = 0;
	// Verifica��o da exist�ncia de valores percentuais de taxa Rise;
	TaxaRise = numVal($("#txRise").val());
	TxRisedaBase = parseFloat("<?php echo $txRise; ?>");
	  if (TaxaRise > 0){
		  novataxa = (TaxaRise/100);
	  }else{
		  novataxa = TxRisedaBase;
	  }
	
	  if ($('#tipobanco').val() == '0'){
		 //verErro('Taxa e Pr�mio m�nimos ser�o aumentados em 10%');
		 novoPrMin = "<?php  echo $premioMinimo * ($jurosmora == 1 ? 1.04 : 1);?>" * (1+ novataxa) * (1.10);
	//     novoTxPrMin = "<?php echo (number_format((100 * $taxaMinima),3,'','') * ($jurosmora == 1 ? 1.04 : 1));?>" * (1+ novataxa) * (1.10);
		 novoTxPrMin = "<?php   echo (number_format((100 * $taxaMinima),3,'',''));?>" * (1+ novataxa) * (1.10);
		 novoPrMin = novoPrMin.toFixed(0);

		 novoTxPrMin = novoTxPrMin.toFixed(3);
	
		 valor.form.txDisplay.value = novoTxPrMin;
		 valor.form.prDisplay.value = float2moeda(novoPrMin);
		 $('#novoTxPrMin').val() = novoTxPrMin;
		 $('#novoPrMin').val() = float2moeda(novoPrMin);
	
		 $('#p_cobertura1').style.display='';
		 $('#p_cobertura2').style.display='';
		 $('#p_cobertura_d701').style.display='';
		 $('#limite_d701').style.display='';
	  }else{
		 novoPrMin = "<?php   echo $premioMinimo * ($jurosmora == 1 ? 1.04 : 1);?>" * (1+ novataxa);
	//     novoTxPrMin = "<?php   echo (number_format((100 * $taxaMinima),3,'','') * ($jurosmora == 1 ? 1.04 : 1));?>" * (1+ novataxa);
		 novoTxPrMin = "<?php   echo (number_format((100 * $taxaMinima),3,'',''));?>" * (1+ novataxa);
		 novoPrMin = novoPrMin.toFixed(0);
		 novoTxPrMin = novoTxPrMin.toFixed(3);
		 valor.form.txDisplay.value = novoTxPrMin;
		 valor.form.prDisplay.value = float2moeda(novoPrMin);
		 $('#novoTxPrMin').val() = novoTxPrMin;
		 $('#novoPrMin').val() = novoPrMin;
		 $('#p_cobertura1').style.display='none';
		 $('#p_cobertura2').style.display='none';
		 $('#p_cobertura_d701').style.display='none';
		 $('#limite_d701').style.display='none';
	  }
	  
	  $('#novoPrMin').val() = parseFloat(novoPrMin);
	  $('#novoTxPrMin').val() = novoTxPrMin;
	  
	}
	
  function checaNumero(obj,valor){
	  var x = obj.value;
	  if (isNaN(x)){ 
		  verErro('Neste campo voc� deve digitar somente n�meros');
		  return false;
	  }
	  
  }

	
  function exibir_tira_taxa(valor){
		var valorTotal = valor.form.prDisplay.value.replace(".","").replace(",",".");
		var novoPrMin = 0;
		var novoTxPrMin = 0;
	
	  if(valor.is(':checked')==false) {
		<?php  if($prAux == '0') { ?>
			<?php  if($ic_gravou_mod!="1") { ?>
				if($('#tipobanco').val()=="0") { 
					novoPrMin = parseFloat(valorTotal) + parseFloat(valorTotal * 0.10) ;
					novoTxPrMin = parseFloat(valor.form.txDisplay.value) + parseFloat(valor.form.txDisplay.value * 0.10);
					valor.form.txDisplay.value = novoTxPrMin;
					valor.form.prDisplay.value = float2moeda(parseFloat(valorTotal) + parseFloat(valorTotal * 0.10));
				} else {
					novoPrMin = parseFloat(valorTotal);
					novoTxPrMin = parseFloat(valor.form.txDisplay.value);
					valor.form.txDisplay.value = novoTxPrMin;
					valor.form.prDisplay.value = float2moeda(parseFloat(valorTotal));
				}
			<?php  } else { ?>
				novoPrMin = "<?php echo $premioMinimo ;?>";
				novoTxPrMin = "<?php   echo (100 * $taxaMinima * (1 + $txRise));?>";
				valor.form.txDisplay.value = novoTxPrMin;
				valor.form.prDisplay.value = float2moeda(parseFloat(valorTotal));
			<?php  } ?>
		<?php  } else { ?>
			novoPrMin = "<?php   echo $premioMinimo;?>";
			novoTxPrMin = "<?php   echo (100 * $taxaMinima * (1 + $txRise));?>";
			valor.form.prDisplay.value = float2moeda(novoPrMin);
			valor.form.txDisplay.value = novoTxPrMin;
		<?php  } ?>
	
		$('#novoTxPrMin').val() = novoTxPrMin;
		$('#novoPrMin').val() = novoPrMin;
	
	  } else {
	
		<?php  if($prAux == '0') { ?>
			novoPrMin = "<?php   echo $premioMinimo * ($jurosmora == 1 ? 1.04 : 1);?>";
			novoTxPrMin = "<?php   echo (100 * $taxaMinima * (1 + $txRise));?>";
			valor.form.prDisplay.value = float2moeda(novoPrMin);
			valor.form.txDisplay.value = novoTxPrMin;
		<?php  } else { ?>
			novoPrMin = "<?php   echo $prAux;?>";
			novoTxPrMin = "<?php   echo (100 * $txAux * (1 + $txRise));?>";
			valor.form.prDisplay.value = float2moeda(novoPrMin);
			valor.form.txDisplay.value = novoTxPrMin;
		<?php  } ?>
		$('#novoTxPrMin').val() = novoTxPrMin;
		$('#novoPrMin').val() = novoPrMin;
		
	  }
	  novoPrMin = novoPrMin.toFixed(0);
	  $('#novoPrMin').val() = parseFloat(novoPrMin);
	  $('#novoTxPrMin').val() = novoTxPrMin;
	
	}

	function numberToReal(numero) {
	    var numero = numero.split('.');
	    numero[0] = numero[0].split(/(?=(?:...)*$)/).join('.');
	    return numero.join(',')+',00';
	}
	
  	function calc (form) {
		
		
	  // Alterado: 06/05/2009 -
	  // Interaktiv - Elias Vaz - Motivo: quando alterar o tipo banco para BB acrescentar 0,10% Lit�gio no valor do pr�mio.
	  //nP = nParc (form);
	  nP = $("#Num_Parcelas").val();
	 
	  var Litigio = 0;
	 
		 
		  if ($('#tipobanco').val() == '0')
			 Litigio = 1.10;
		  else{
			 Litigio = 1;
		  }
		  
		  
		  premio = <?php echo  $premioMinimo * ($jurosmora == 1 ? 1.04 : 1) ?> * (1 + (numVal(form.txRise.value)/100))* Litigio;
		  premio = premio.toFixed(0);
		  
		  form.prDisplay.value = parseFloat(premio);
		  $('#novo_valor').text(numberToReal(premio));
		  checkDecimals (form.prDisplay, dot2comma(form.prDisplay.value));
		  //form.txDisplay.value = (<?php   echo ($taxaMinima * 100) * ($jurosmora == 1 ? 1.04 : 1);?>  * (1 + (numVal(form.txRise.value)/100)) * Litigio).toFixed(3);
		  form.txDisplay.value = (<?php   echo ($taxaMinima * 100);?>  * (1 + (numVal(form.txRise.value)/100)) * Litigio).toFixed(3);
		  
		  $('#novo_valor').innerText = form.prDisplay.value;
		  $('#taxa_novo_valor').innerText = form.txDisplay.value.replace('.',',');
		  
   }

   	
	function enviar(f) {
		
		ok = true;
	
		wtxRise = '<?php   echo  number_format(100 * odbc_result($cur, 7), 2, ',', '.');?>';
		if (wtxRise != f.txRise.value){
			ok = false;
		}
		 
		
		wnumParc = '<?php   echo  (($Num_Parcelas ? $Num_Parcelas : $numParc));?>';
		/*
		if (wnumParc != nParc(f)){
			ok = false;
		}
		*/
		
	
		wlimPagIndeniz = '<?php   echo  $limPagIndeniz ? $limPagIndeniz : '30';?>';
		if (wlimPagIndeniz != f.limPagIndeniz.value){
			ok = false;
		}
	
		wtxAnalize = '<?php   echo  number_format(odbc_result($cur, 'txAnalize'), 2, ',', '.');?>';
		if (wtxAnalize != f.txAnalize.value){
			ok = false;
		}
	
		wtxMonitor = '<?php   echo  number_format(odbc_result($cur,'txMonitor'), 2, ',', '.');?>';
		<!--number_format($field->getDBField('txMonitor', 14), 2, ',', '.')-->
		if (wtxMonitor != f.txMonitor.value){
			ok = false;
		}
	
		wpercCoverage = '<?php   echo  number_format($cobertura ? $cobertura : 85, 0, '', '');?>';
		if (wpercCoverage != f.percCoverage.value){
			ok = false;
		}
	
		wvalidCot = '<?php   echo  $field->getDBField('validCot', 13);?>';
		if (parseInt(f.validCot.value) > 90){
			ok = false;
		}
	
		/*    if (wvalidCot != f.validCot.value){
			ok = false;
		} */
	
		wmModulos = '<?php   echo  $field->getDBField('mModulos', 27);?>';
		if ($("#idModuloF9.02").length > 0){
			if ($("#idModuloF9.02").is(':checked')){
			  var mo = '1';
			}else {
			  if (wmModulos == '') {
				 var mo = wmModulos;
			  }else{
				 var mo = '0';
			  }
			}
		}

		if (wmModulos != mo){
		  ok = false;
		}
		
		if ($("#idModuloF9.02").length > 0){
			if ($("#idModuloF9.02").is(':checked')){
					wperBonus = '<?php   echo  number_format($field->getDBField('perBonus', 29), 2, ',', '.');?>';
					if (wperBonus != f.per.value){
						ok = false;
					}
			}
		}
	
		
		if (parseFloat(f.per1.value) > 0){
				wperPart1 = '<?php   echo  number_format($field->getDBField('perPart0', 30), 2, ',', '.');?>';
				
				
				if (wperPart1 != f.per1.value){
					
					ok = false;
				}
	
				wperPart2 = '<?php   echo  number_format($field->getDBField('perPart1', 31), 2, ',', '.');?>';
			   
				
				if (wperPart2 != f.per2.value){
					ok = false;
				}
	
				wpLucro = '<?php   echo  $field->getDBField('pLucro', 32);?>';
				/*
				if (f.idModuloRadio[0].is(':checked')) {
					var pl = 'F13';
				}else if (f.idModuloRadio[1].is(':checked')){
					var pl = 'F14';
				}else if (f.idModuloRadio[2].is(':checked')){
					var pl = 'F15';
				}
			   
				if (wpLucro != pl ){
					ok = false;
				}
				*/
		}
	
		wnas = '<?php   echo number_format(odbc_result($cur,'nas'), 2, ',', '.');?>';
		//verErro'<?php   echo number_format(odbc_result($cur,'nas'), 2, ',', '.');?>');
		if (wnas == ''){
				var na = '0.00';
		}else{
			var na = wnas;
		}
	
		/*
		if (na != f.nas.value){
			ok = false;
		}else{
			ok = true;	
		}
		*/
		
		ok = true;
		
		if (!ok) {
			verErro('Dados da oferta foram alterados. Somente s�o permitidas altera��es na data de vencimento e unidade de produ��o.');
		   
		} else {
						
			f.comm.value='viewProp';

			consist(f);
			
			//return true;
			
		}
		
		
	}


</script>   