
<script type="text/javascript" charset="">
  
    function nParc (form) {
    
<?php if ($vigencia != "2") {
      ?>
      if (form.numParc[0].checked) {
        numParc=1;
      } else if (form.numParc[1].checked) {
        numParc=2;
      } else if (form.numParc[2].checked) {
        numParc=4;
      } else if (form.numParc[3].checked) {
        numParc=7;
      } else if (form.numParc[4].checked) {
        numParc=10;
      }
      return numParc;
      <?php  }else if ($vigencia == "2"){
      ?>
      if (form.numParc[0].checked) {
        numParc=1;
      } else if (form.numParc[1].checked) {
        numParc=4;
      } else if (form.numParc[2].checked) {
        numParc=7;
      } else if (form.numParc[3].checked) {
        numParc=8;
      }
        return numParc;
      <?php  }
      ?>
    }
    
    
	function validadoFormCorretor(){		      
		  if(document.getElementById('Corretor1').value == "" ){
		     document.getElementById('Corretor1').focus();
			 verErro('O Corretor deve ser selecionado.');
			 return false;
			 
		  }else if(document.getElementById('i_Grupo').value == "" ){
		     document.getElementById('i_Grupo').focus();
			 verErro('O grupo do corretor deve ser selecionado.');
			 return false;
		  }else if(document.getElementById('Comissao').value == "0,00" ){
		     document.getElementById('Comissao').focus();
			 verErro('Um percentual de comiss&atilde;o deve ser informado.');
			 return false;
		  }else if(document.getElementById('Participacao').value == "0,00" ){
		     document.getElementById('Participacao').focus();
			 verErro('Um percentual de participa&ccedil;&atilde; deve ser informado.');
			 return false;
		  }else{
			 return true;  
		  }
	}
	 
	 
	 
	 
	  
    function consist (form) {
		
		
      msg = "";
      nP = nParc (form);
      calc(form);
	  
      if (nP > 1 &&  numVal(form.prDisplay.value) / nP < 1000) {
           msg += "Valor da parcela abaixo de <?php   echo $extMoeda;?> 1.000,00";
      }
    
    
      if (document.all.comm.value=='viewProp'){
    
      }else{
            if (document.all.nas.value == '0,00') {
                 verErro('Informe um valor para Notificação de Ameaça de Sinistro.');
                 return(false);
            }
      }
    
    
      if(document.getElementById('origemNegocio').value == ""){
         verErro('Atenção: Deve ser informada a origem do negócio!');
         document.getElementById('origemNegocio').focus();
         return false;
      }
    
      /*
      
      var ver = parseInt(document.getElementById('totalElement_corr').value);
      var total = 0;
      
      var comissao = 0;
      
      for(x=1; x<=ver;x++){
          if(document.getElementById('Corretor'+x).value != ""){                  
              comissao = parseFloat(document.getElementById('Comissao'+x).value.replace(",","."));  
              if (document.getElementById('Comissao'+x).value == ""  || comissao == 0){
                 verErro('Atenção: Deve ser informado o valor da comissão!');
                 document.getElementById('Comissao'+x).focus();
                 return false;
              }
              
              if (document.getElementById('Participacao'+x).value == ""  || comissao == 0){
                 verErro('Atenção: Deve ser informado o valor da Participação!');
                 document.getElementById('Participacao'+x).focus();
                 return false;
              }
          }
      }
      
      if(document.getElementById('Corretor1').value == ""){
              verErro('Atenção: Deve ser informado pelo menos um corretor!');
              document.getElementById('Corretor1').focus();
              return false;
      }
      var sim = 0;
      var zz = 0;
      
      
        for(zz=1; zz <= ver; zz++){
          //verErro(document.getElementById('CorretorPrincipal'+zz).value);
          
          if (document.getElementById('CorretorPrincipal'+zz).value == 1){
              sim = 1;
              //verErro('oi');
          }
          
        }
        
        if(document.getElementById('temCorretorPrincipal') == ''){	       
               verErro('Atenção: Deve ser informado pelo menos um corretor como Principal!');
               document.getElementById('Corretor1').focus();
               return false;
        }
          
    
      
      
      for(i=1; i<=ver;i++){
         total +=  parseFloat(document.getElementById('Participacao'+i).value.replace(",",".")); 
         //verErro(total);
      }
     
      if (total < 100 || total > 100){
          verErro('A soma da participação não poder ser menor ou maior que 100% !');
          document.getElementById('Participacao'+(i - 1)).focus();
          return false;
      }
      
      */
	 
	 
	  if(document.getElementById('i_Produto').value == "1"){ // Validação se for  produto interno
      
         
      if(document.getElementById('PrazoMaxEmiNota').value == "" || document.getElementById('PrazoMaxEmiNota').value == "0" ){
        
           /*verErro('Atenção: Deve ser informado o prazo máximo para emissão da nota fiscal!');
           document.getElementById('PrazoMaxEmiNota').focus();
           return false;*/
           document.getElementById('PrazoMaxEmiNota').value = 0;
      }

      if(document.getElementById('prazo_nao_intencao_renov').value == "0" || document.getElementById('prazo_nao_intencao_renov').value == "" ){
        
           verErro('Atenção: Deve ser informado o prazo para aviso da não intenção de renovação!');
           document.getElementById('prazo_nao_intencao_renov').focus();
           return false;
      }
		  
		  if(document.getElementById('b802ex').checked && document.getElementById('TemEmpComprador').value == "0"){			      
				  
				   verErro('Atenção: Deve ser informado pelo menos um Comprador !');
				   //document.getElementById('PrazoMaxEmiNota').focus();
				   return false;
		  }
		  
		  if(document.getElementById('b904check').checked && document.getElementById('b904').value == "0"){			      
				  
				   verErro('Atenção: Deve ser informado um valor para a franquia de maiores compradores !');
				   document.getElementById('b904').focus();
				   return false;
		  }
		  
		  if(document.getElementById('b2604').checked == true){
			  
			       if(document.getElementById('b2604NivelMax').value == "0,00" || document.getElementById('b2604NivelMax').value == ""){					  
					   verErro('Atenção: Dever ser informado o valor do Nível máximo de crédito !');
					   document.getElementById('b2604NivelMax').focus();
					   return false;
				   }
				   
				   if(document.getElementById('b2604Perc').value == "0,00" || document.getElementById('b2604Perc').value == ""){					  
					   verErro('Atenção: Dever ser informado o valor da percentagem segurada!');
					   document.getElementById('b2604Perc').focus();
					   return false;
				   }
		  }
		  
		  if(document.getElementById('b2801').checked){
			  
			       if(document.getElementById('b2801NivelMax').value == "0,00" || document.getElementById('b2801NivelMax').value == ""){					  
					   verErro('Atenção: Deve ser informado o nível máximo de crédito referente à cláusula 1  !');
					   document.getElementById('b2801NivelMax').focus();
					   return false;
				   }
				   
				   if(document.getElementById('b2801Perc').value == "0,00" || document.getElementById('b2801Perc').value == ""){					  
					   verErro('Atenção: Dever ser informada a percentagem segurada referente à cláusula 1.6 !');
					   document.getElementById('b2801Perc').focus();
					   return false;
				   }
		  }
		  
		  if(document.getElementById('d201check').checked){
			  
			       if(document.getElementById('d201').value == "0,00" || document.getElementById('d201').value == ""){					  
					   verErro('Atenção: Deve ser informado o valor da franquia!');
					   document.getElementById('d201').focus();
					   return false;
				   }
				   
		
		  } 
		  
		  if(document.getElementById('f401check').checked){
			  
			       if(document.getElementById('f401NivelSinistralidade').value == "0,00" || document.getElementById('f401NivelSinistralidade').value == ""){					  
					   verErro('Atenção: Deve ser informado o bônus por nível de sinistralidade!');
					   document.getElementById('f401NivelSinistralidade').focus();
					   return false;
				   }
				   
				   if(document.getElementById('f401PercPremio').value == "0,00" || document.getElementById('f401PercPremio').value == ""){					  
					   verErro('Atenção: Deve ser informada percentagem de prêmio!');
					   document.getElementById('f401PercPremio').focus();
					   return false;
				   }
				   
		
		  } 
		  
	  }
	 
	 
	 
	 
	   
	  if(document.getElementById('temCorretorPrincipal').value == "0" ){
		  
               verErro('Atenção: Deve ser informado pelo menos um corretor como Principal!');
               document.getElementById('Corretor1').focus();
               return false;
      }
	  
	  var total =  parseFloat(document.getElementById('TotalParticipacao').value.replace(",",".")); 
	  
	  if (total < 100 || total > 100){
          verErro('A soma da participação não poder ser menor ou maior que 100% !');
          document.getElementById('Corretor1').focus();
          return false;
      }
	  
	  if(document.getElementById('i_Sub_Produto').value == ""){
		 verErro('Selecione o Sub Produto.');
         document.getElementById('i_Sub_Produto').focus();
         return(false);   
	  }
	  
      // tipo banco
      if (document.getElementById('tipobanco').value == "") {
         verErro('Selecione o Banco.');
         document.getElementById('tipobanco').focus();
         return(false);
      }
    
	  if (document.getElementById('i_Gerente').value == "") {
         verErro('Selecione o Gerente Comercial.');
         document.getElementById('i_Gerente').focus();
         return(false);
      }
	  
	  if (document.getElementById('i_Contrato_Resseguro').value == "") {
         verErro('Selecione o Contrato Resseguro.');
         document.getElementById('i_Contrato_Resseguro').focus();
         return(false);
      }
	  
	  if (document.getElementById('p_Taxa_Desagio').value == "0,00" || document.getElementById('p_Taxa_Desagio').value == "" ) {
         verErro('Informe a taxa de Des&aacute;gio.');
         document.getElementById('p_Taxa_Desagio').focus();
         return(false);
      }
    
    
      if(document.all.Ad.checked == true){
          var ad_sinistr = parseFloat(document.all.ad_sinistr.value.replace(",","."));
          var ad_premio = parseFloat(document.all.ad_premio.value.replace(",","."));
          
          
         if (! ad_sinistr > 0){
            verErro("Você deve informar valor no campo Adequacão de sinistralidade.");
            return(false);
         }else if (! ad_premio > 0){
            verErro("Você deve informar valor no campo Adequacão de prêmio.");
            return(false);
         }
    
      }
    
      if(document.all.Adfranq.checked == true){
         var franq = parseFloat(document.all.franquia_anual.value);
         if (! franq > 0){
            verErro("Você deve informar o valor da franquia.");
            return(false);
         }
      }
    
      if(document.all.condesp_ck.checked == true){
         if(document.all.condicoes_especiais.value == "" ){
            verErro("Você deve preencher o campo condicões complementares.");
            return(false);
          }
    
    
      }
	  /*
      if(document.all.condesp_ck_col.checked == true){
         if(document.all.rz_col1.value == "" ){
            verErro("Você deve preencher o campo Razão Social da empresa coligada.");
            return(false);
         }
          
      }
	  */
    
      if(document.getElementById('periodMaxCred').value == ""){
         verErro('Atenção: Deve ser informada a duração neste campo.');
         document.getElementById('periodMaxCred').focus();
         return(false); 
      }
      if (document.all.opt.checked == true) {
         msgb = "";
         if (document.all.per.value == "" || document.all.per.value == "0") {
            verErro('Campo percentual esta vazio ou zerado.');
            return(false);
         }else{
            msgb = "\n e " + document.all.per.value + "% Bônus por Ausência de sinistros\n" +
            "vinculado a renovação da apólice por mais um período de seguro";
         }
    
      }else{
           msgb = "";
      }
    
      
      // d602
      if (document.getElementById('d602').checked == true) {
        if(document.getElementById('nivel_d602').value == "" || document.getElementById('nivel_d602').value == "0") {
            verErro('Campo de Nível de Pequenos Sinistros está vazio ou zerado.');
            return(false);
        }else{
    //        msgp += "\n Nível de Pequenos Sinistros : US$ " + document.getElementById('nivel_d602').value;
        }
      }
    
      // d701
      if (document.getElementById('d701').checked == true) {
        if(document.getElementById('p_cobertura_d701').value == "" || document.getElementById('p_cobertura_d701').value == "0") {
            verErro('Campo de % de Cobertura está vazio ou zerado.');
            return(false);
        }else{
            //msgp += "\n % de Cobertura : " + document.getElementById("p_cobertura_d701").value + "%";
        }
      }
    
      
    
      if (document.all.partic.checked == true) {
         msgp = "";
         msgp = "\n e Participação nos lucros com ";
         if (document.all.per1.value == "" ||
                    document.all.per1.value == "0" ) {
            verErro('Campo de percentagem de dedução esta vazio ou zerado.');
            return(false);
         }else{
            msgp += "\n Percentagem de Dedução : " + document.all.per1.value + "%";
         }
    
    
         if (document.all.per2.value == "" || document.all.per2.value == "0") {
            verErro('Campo de Participação nos lucros esta vazio ou zerado.');
            return(false);
         }else {
            msgp += "\n e Participação nos Lucros : " + document.all.per2.value + "%";
         }
    
         if (!document.all.plucro[0].checked && !document.all.plucro[1].checked && !document.all.plucro[2].checked ){
             verErro('Selecione um tipo de Participação nos Lucros.');
             return(false);
         }
      }else{
         msgp = "";
      }
    
    
    /*
     if (form.txAnalize.value == '0,00'){
       verErro("Informe um valor para a Taxa de Análise");
       form.txAnalize.focus();
       return (false);
     }
    */
    
	  
	   
      if (msg != "") {
        verErro("Favor corrigir o número de parcelas:\n"+ msg);
      } else {
        if (form.tipoapolice.value==1 && form.jurosmora.value==1){
              if (confirm ("Confirma o valor de Análise = R$ " + form.txAnalize.value+ "\n" +
                         "e Monitoramento = R$ " + form.txMonitor.value + msgb + msgp + "?")) {
                   HabilitaCamposBB();
                   form.submit();
                }
        }else{
            if (confirm ("Confirma o valor de Análise = R$ " + form.txAnalize.value+ "\n" +
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
    // Verificação da existência de valores percentuais de taxa Rise;
    TaxaRise = numVal(document.getElementById("txRise").value);
    TxRisedaBase = parseFloat("<?php   echo odbc_result($cur, 7);?>");
      if (TaxaRise > 0){
          novataxa = (TaxaRise/100);
      }else{
          novataxa = TxRisedaBase;
      }
    
      if (document.getElementById('tipobanco').value == '0'){
         //verErro('Taxa e Prêmio mínimos serão aumentados em 10%');
         novoPrMin = "<?php  echo $premioMinimo * ($field->getDBField("warantyInterest", 22) == 1 ? 1.04 : 1);?>" * (1+ novataxa) * (1.10);
    //     novoTxPrMin = "<?php echo (number_format((100 * $taxaMinima),3,'','') * ($field->getDBField("warantyInterest", 22) == 1 ? 1.04 : 1));?>" * (1+ novataxa) * (1.10);
         novoTxPrMin = "<?php   echo (number_format((100 * $taxaMinima),3,'',''));?>" * (1+ novataxa) * (1.10);
         novoPrMin = novoPrMin.toFixed(0);
         novoTxPrMin = novoTxPrMin.toFixed(3);
    
         valor.form.txDisplay.value = novoTxPrMin;
         valor.form.prDisplay.value = float2moeda(novoPrMin);
         document.getElementById('novoTxPrMin').value = novoTxPrMin;
         document.getElementById('novoPrMin').value = float2moeda(novoPrMin);
    
         document.getElementById('p_cobertura1').style.display='';
         document.getElementById('p_cobertura2').style.display='';
         document.getElementById('p_cobertura_d701').style.display='';
         document.getElementById('limite_d701').style.display='';
      }else{
         novoPrMin = "<?php   echo $premioMinimo * ($field->getDBField("warantyInterest", 22) == 1 ? 1.04 : 1);?>" * (1+ novataxa);
    //     novoTxPrMin = "<?php   echo (number_format((100 * $taxaMinima),3,'','') * ($field->getDBField("warantyInterest", 22) == 1 ? 1.04 : 1));?>" * (1+ novataxa);
         novoTxPrMin = "<?php   echo (number_format((100 * $taxaMinima),3,'',''));?>" * (1+ novataxa);
         novoPrMin = novoPrMin.toFixed(0);
         novoTxPrMin = novoTxPrMin.toFixed(3);
         valor.form.txDisplay.value = novoTxPrMin;
         valor.form.prDisplay.value = float2moeda(novoPrMin);
         document.getElementById('novoTxPrMin').value = novoTxPrMin;
         document.getElementById('novoPrMin').value = novoPrMin;
         document.getElementById('p_cobertura1').style.display='none';
         document.getElementById('p_cobertura2').style.display='none';
         document.getElementById('p_cobertura_d701').style.display='none';
         document.getElementById('limite_d701').style.display='none';
      }
      
      document.getElementById('novoPrMin').value = parseFloat(novoPrMin);
      document.getElementById('novoTxPrMin').value = novoTxPrMin;
      
    }
    
  function exibir_tira_taxa(valor){
		var valorTotal = valor.form.prDisplay.value.replace(".","").replace(",",".");
		var novoPrMin = 0;
		var novoTxPrMin = 0;
    
      if(valor.checked==false) {
        <?php  if(odbc_result($cur, 35) == '0') { ?>
            <?php  if($ic_gravou_mod!="1") { ?>
                if(document.getElementById('tipobanco').value=="0") { 
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
                novoPrMin = "<?php   echo odbc_result($cur, 5);?>";
                novoTxPrMin = "<?php   echo (100 * odbc_result($cur, 6) * (1 + odbc_result($cur, 7)));?>";
                valor.form.txDisplay.value = novoTxPrMin;
                valor.form.prDisplay.value = float2moeda(parseFloat(valorTotal));
            <?php  } ?>
        <?php  } else { ?>
            novoPrMin = "<?php   echo odbc_result($cur, 5);?>";
            novoTxPrMin = "<?php   echo (100 * odbc_result($cur, 6) * (1 + odbc_result($cur, 7)));?>";
            valor.form.prDisplay.value = float2moeda(novoPrMin);
            valor.form.txDisplay.value = novoTxPrMin;
        <?php  } ?>
    
        document.getElementById('novoTxPrMin').value = novoTxPrMin;
        document.getElementById('novoPrMin').value = novoPrMin;
    
      } else {
    
        <?php  if(odbc_result($cur, 35) == '0') { ?>
            novoPrMin = "<?php   echo odbc_result($cur, 5) * ($field->getDBField("warantyInterest", 22) == 1 ? 1.04 : 1);?>";
            novoTxPrMin = "<?php   echo (100 * odbc_result($cur, 6) * (1 + odbc_result($cur, 7)));?>";
            valor.form.prDisplay.value = float2moeda(novoPrMin);
            valor.form.txDisplay.value = novoTxPrMin;
        <?php  } else { ?>
            novoPrMin = "<?php   echo odbc_result($cur, 35);?>";
            novoTxPrMin = "<?php   echo (100 * odbc_result($cur, 36) * (1 + odbc_result($cur, 7)));?>";
            valor.form.prDisplay.value = float2moeda(novoPrMin);
            valor.form.txDisplay.value = novoTxPrMin;
        <?php  } ?>
        document.getElementById('novoTxPrMin').value = novoTxPrMin;
        document.getElementById('novoPrMin').value = novoPrMin;
        
      }
      novoPrMin = novoPrMin.toFixed(0);
      document.getElementById('novoPrMin').value = parseFloat(novoPrMin);
      document.getElementById('novoTxPrMin').value = novoTxPrMin;
    
    }
    



</script>