// JavaScript Document


 // JavaScript Document
    var xmlhttp = false;
	//Verifica se está usando internet explorer
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
		}
	}
	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
		xmlhttp = new XMLHttpRequest();
	}	

///////////////////////////////////////////////////////////////////////////////////////
// 1º COMBO
///////////////////////////////////////////////////////////////////////////////////////
var val_campo_pais;


function MascaraCEP(campocep){

    var cep = campocep.value;
	if (cep.length == 5){
		  cep = cep + '-';
		  $('#CEPEmpresa').value = cep;
		  return true;
	
	}

}


function MascarCnpj(campo, teclapres){
	var tecla = teclapres.keyCode;
	var vr = new String(campo.value);
	vr = vr.replace(".", "");
	vr = vr.replace("/", "");
	vr = vr.replace("-", "");
	tam = vr.length + 1;
	if (tecla != 14)
	{
		if (tam == 3)
			campo.value = vr.substr(0, 2) + '.';
		if (tam == 6)
			campo.value = vr.substr(0, 2) + '.' + vr.substr(2, 5) + '.';
		if (tam == 10)
			campo.value = vr.substr(0, 2) + '.' + vr.substr(2, 3) + '.' + vr.substr(6, 3) + '/';
		if (tam == 15)
			campo.value = vr.substr(0, 2) + '.' + vr.substr(2, 3) + '.' + vr.substr(6, 3) + '/' + vr.substr(9, 4) + '-' + vr.substr(13, 2);
	}
}





 function list_dados(valor,campo)
 {
	val_campo_pais = campo;
    xmlhttp.open("GET", "paises.php?pais_col=" + valor, true);
	xmlhttp.onreadystatechange = handleHttpResponse;
	xmlhttp.send(null);
 }

 function handleHttpResponse(){
	var campo = 'pais_col'+val_campo_pais;
	campo_select = document.getElementById(campo);
        
	if (xmlhttp.readyState == 4)
	{
		campo_select.options.length = 0;
		results = xmlhttp.responseText.split(",");

		for( i = 0; i < results.length; i++ )
		{
			string = results[i].split( "|" );
			campo_select.options[i] = new Option( string[1], string[0] );
			 
		}
 	}
}

///////////////////////////////////////////////////////////////////////////////////////
// FIM - 1º COMBO
///////////////////////////////////////////////////////////////////////////////////////

  function exibir_modulo(subproduto,id,url){
     	 xmlhttp.open("GET", "interf/Exibir_modulos.php?i_Sub_Produto="+subproduto+"&idInform="+id, true);
		 xmlhttp.onreadystatechange = handleHttpResponse_modulo;
		 xmlhttp.send(null);
					
  }

    
	function handleHttpResponse_modulo(){
	   var contentDiv;
	    contentDiv = document.getElementById("exibir_modulos");
        
		if (xmlhttp.readyState == 4){
			//alert(xmlhttp.responseText);
			//verErro("Sub-Produto selecionado!");
			//contentDiv.innerHTML = xmlhttp.responseText;
			location.reload(true);

			
		}else{
			//verErro("Por favor selecione um item do Sub-Produto");
			contentDiv.innerHTML = xmlhttp.responseText;
		}
	}

	
	


    function exibir_nenhum(){
    
      if (document.all.opt.checked == true) {
       document.all.per1.value = '';
       document.all.per2.value = '';
       document.all.plucro[0].checked = false;
       document.all.plucro[1].checked = false;
       document.all.plucro[2].checked = false;
       document.all.partic.value = '0';
       p0.style.display = 'none';
       p3.style.display = 'none'
       document.all.per.value = '';
       document.all.opt.value = '0';
    
      }
    
    }
    
    function ValMaxCredit(form){
        if (form.periodMaxCred.value){
           Valor = form.periodMaxCred.value;
               if (Valor > 360){
                  form.periodMaxCred.focus();
                  verErro('A duração não pode ser superior a 360 dias.');
                  return false;
               }
        }
        else{
            form.periodMaxCred.focus();
            verErro('Atenção: Deve ser informada a duração neste campo.');
            return false;
        }
    }
    
	
	
	
	
	function exibir_bonus(valor){
         if($('#idModuloF9.02').checked == true) {
			  $('#exibirBonus_campo').show();
			
		 } else {
			  $('#exibirBonus_campo').hide();
		 }
      
       
    
  }	
	
	function exibir_F5202(valor){
         if($('#idModuloF52.02').checked == true) {
			  $('#exibirF5202_campo').show();
			
		 } else {
			  $('#exibirF5202_campo').hide();
		 }
      
       
    
  }
	
	
	function exibir_opcpercentagem(valor,v){
		if($(".js_unique_mod:checked").length > 1){
			alert('Favor selecionar apenas um módulo de Participação nos Lucros');
			$(".js_unique_mod").removeAttr("checked");
			$(".js_opcpercentagem").hide();
			$('#opcpercentagem'+v).show();
			document.getElementById(valor).checked = true;
		} else {
			if($("#"+valor+":checked").length == 1){
				$('#opcpercentagem'+v).show();
			} else {
				$('#opcpercentagem'+v).hide();
			}
		}
	}
	
	
	
    
    function exibir_part(valor){
       
	     if($('#idModuloF9.02').checked == true) {
			 // $('#exibirParticipacao').show();
			
		 } else {
			 // $('#exibirParticipacao').style.display = 'none';
		 }
    }
    
    function tipoApl(){
		/*
       if (document.all.tipoapolice.value == '0') { <!--Apólice Antiga-->
          $('#a801').disabled=true;
          $('#a502').disabled=true;
          //$('#b603').disabled=true;
          $('#b1504').disabled=true;
          $('#b1202').disabled=true;
          $('#c102').disabled=true;
          $('#d101').disabled=true;
          $('#d602').disabled=true;
          $('#d701').disabled=true;
          $('#e101').disabled=true;
          $('#f305').disabled=true;
          $('#f3301').disabled=true;
    
          $('#aplicaTaxa').style.display='';
       }else{
          $('#a801').disabled=false;
          $('#a502').disabled=false;
          //$('#b603').disabled=false;
          $('#b1504').disabled=false;
          $('#b1202').disabled=false;
          $('#c102').disabled=false;
          $('#d101').disabled=false;
          $('#d602').disabled=false;
          $('#d701').disabled=false;
          $('#e101').disabled=false;
          $('#f305').disabled=false;
          $('#f3301').disabled=false;
    
          $('#aplicaTaxa').style.display='';
       }
	   */
    }
    
    function HabilitaCamposBB(){
		/*
      $('#a801').disabled=false;
      $('#a502').disabled=false;
      //$('#b603').disabled=false;
      $('#b1504').disabled=false;
      $('#b1202').disabled=false;
      $('#c102').disabled=false;
      $('#d101').disabled=false;
      $('#d602').disabled=false;
      $('#d701').disabled=false;
      $('#e101').disabled=false;
      $('#f305').disabled=false;
      $('#f3301').disabled=false;
	  
	  */
    }
    
    function tipoBanco(xtipo){
	
       if ($('#tipobanco').value == '0') { // BB
	        if($('#i_Produto').value != 1){
				if(!$('#verLitigio').is(":visible")){
					$('#verLitigio').show();
					$('#litDiv').show();
					$('#ativLitigio').show();
					$('#DerrLitigio').show();
				}else{ 
				   if($('#verLitigio').is(":visible")){
					   $('#verLitigio').hide();
				   }
				}
			}
       }else{
		   
		    if($('#verLitigio').is(":visible")){
				$('#verLitigio').hide();
				$('#litDiv').hide();
				$('#ativLitigio').hide();
				$('#DerrLitigio').hide();
			}
		   
	   }
    
    }
    

    function exibirCobertura(valor){
		 if(valor.checked==true) {
			  $('#percentualCobertura').show();
			
		 } else {
			  $('#percentualCobertura').hide();
		 }
		
	}

    function exibir_nivel(valor){
     
		  if(valor.checked==true) {
			  $('#nivelPequSinistro').show();
			
		  } else {
			  $('#nivelPequSinistro').hide();
		  }
   }
    
    function exibir_dados(valor){
      if(valor.checked == true) {
          $('#exibirEmpresa').show();
      }else {
          $('#exibirEmpresa').hide();
      }
     
    }
    
	 function exibir_Cobertura(valor){
		  if(valor.checked == true) {
			  $('#exibir_Cobertura').show();
		  }else {
			  $('#exibir_Cobertura').hide();
		  }
     
    }
    
	
	function exibir_b904(valor){
		  if(valor.checked == true) {
			  $('#exibir_b904').show();
		  }else {
			  $('#exibir_b904').hide();
		  }

	}
	
	
	
	function exibir_derrogacoes(obj,valor){
		  if(obj.checked == true) {
			  $('#mostraderrogacao'+valor).show();
		  }else {
			  $('#mostraderrogacao'+valor).hide();
		  }

	}
	
	function verDerrogacao(obj, num){
	      if(obj.checked == true) {
			  $('#verDerrogacao'+num).show();
		  }else {
			  $('#verDerrogacao'+num).hide();
		  }
	
	}
	
	function verDerrogacaoEspecial(obj, num, modulo){
		if(modulo == 'F13.02'){
	      if(obj.checked == true) {
			  $('#verDerrogacao'+num).show();
			  $('#verDerrogacao'+ (num +1)).hide();
			  $('#verDerrogacao'+ (num +2)).hide();
		  }else { 
			  $('#verDerrogacao'+num).hide();
			  $('#verDerrogacao'+ (num +1)).hide();
			  $('#verDerrogacao'+ (num +2)).hide();
		  }
		}else if(modulo == 'F14.02'){
			if(obj.checked == true) {
			  $('#verDerrogacao'+num).show();
			  $('#verDerrogacao'+ (num -1)).hide();
			  $('#verDerrogacao'+ (num +1)).hide();
		    }else { 
			  $('#verDerrogacao'+num).hide();
			  $('#verDerrogacao'+ (num -1)).hide();
			  $('#verDerrogacao'+ (num +1)).hide();
		   }
		}else if(modulo == 'F15.02'){
			if(obj.checked == true) {
			  $('#verDerrogacao'+num).show();
			  $('#verDerrogacao'+ (num -1)).hide();
			  $('#verDerrogacao'+ (num -2)).hide();
		    }else { 
			  $('#verDerrogacao'+num).hide();
			  $('#verDerrogacao'+ (num -1)).hide();
			  $('#verDerrogacao'+ (num -2)).hide();
		   }
		}
	
	}
	
	function exibir_b2604(valor){
		  if(valor.checked == true) {
			  $('#exibir_b2604').show();
		  }else {
			  $('#exibir_b2604').hide();
		  }

	}
	
	function exibir_b2801(valor){
		  if(valor.checked == true) {
			  $('#exibir_b2801').show();
		  }else {
			  $('#exibir_b2801').hide();
		  }

	}
	function exibir_d201(valor){
		  if(valor.checked == true) {
			  $('#exibir_d201').show();
		  }else {
			  $('#exibir_d201').hide();
		  }

	}
	
	function exibir_f401(valor){
		  if(valor.checked == true) {
			  $('#exibir_f401').show();
		  }else {
			  $('#exibir_f401').hide();
		  }

	}
	
	
    function validaCorrPrincipal(vlr){
        var cont = parseInt($('#totalElement_corr').value);
        
        for (j=1;j<=cont;j++){
            // verErro('? '+ j);
             $('#CorretorPrincipal'+j).checked = false;
             $('#CorretorPrincipal'+j).value = 0;
             
             
        }
        
        $('#CorretorPrincipal'+vlr).checked = true;
        $('#CorretorPrincipal'+vlr).value = 1;
    }
    
    function limpaCamp(iCont) {
      $('#rz'+iCont).value='';
      $('#cnpj'+iCont).value='';
      $('#insce'+iCont).value='';

      $('#endereco'+iCont).value='';
      $('#num_endereco'+iCont).value='';
      $('#complemento'+iCont).value='';
      $('#cep'+iCont).value='';
      $('#cidade'+iCont).value='';
      $('#estado'+iCont).value='';
      
    }
    
    function limpaCamp_col(iCont) {
      $('#rz_col'+iCont).value='';
      $('#endereco_col'+iCont).value='';
      $('#pais_col'+iCont).value='';
      $('#zipcode_col'+iCont).value='';
      $('#taxID_col'+iCont).value='';
     
    
    }
    
    function limpaCamp_corr(iCont) {
      $('#Corretor'+iCont).value='';
      $('#Comissao'+iCont).value='';
      $('#Participacao'+iCont).value='';
    }
    
    function numeros(){
       tecla = event.keyCode;
          if ((tecla >= 48 && tecla <= 57) ||(tecla == 44 || tecla ==46)) {
             return true;
          } else {
              verErro('Este campo só aceita números.');
              return false;
          }
    }
    
    function tiraponto(campo)
    {
        var str = document.getElementById(campo).value;
        document.getElementById(campo).value =  str.replace('.' , ',');
    }
    
    function visualiza(str){
       var i = str;
   
	   
       if (document.getElementById("Ad").checked == true){
          document.getElementById("Ad").value = 1;
       }else{
          document.getElementById("Ad").value = 0;
       }
	   
    }
    
	function viewAdequacao(valor){
		 if(valor.checked == true) {
            $('#viewAdequacao_campo').show();
        }else {
            $('#viewAdequacao_campo').hide();
        }
	    	
	}
	
	
    function visualizafranq(){
       
       if (document.getElementById("idModuloD4.01").checked){
          document.getElementById("franquia_an").show();
          document.getElementById("Adfranq").value = 1;
       }else{
          document.getElementById("Adfranq").value = 0;
          document.getElementById("franquia_an").style.display = 'none';
       }
	  
    
    }
    
    
    function visualizacondesp(valor){
		
		   if (document.getElementById("condesp_ck").checked){
			  document.getElementById("condicoes_esp").show();
			  document.getElementById("condesp_ck").value = 1;
		   }else{
			  document.getElementById("condesp_ck").value = 0;
			  document.getElementById("condicoes_esp").style.display = 'none';
		   }
		
    
    }
    
    function visualizacondesp_col(valor){
			
		   if (document.getElementById("condesp_ck_col").checked){
			  document.getElementById("condicoes_esp_col").show();
			  document.getElementById("condesp_ck_col").value = 1;
		   }else{
			  document.getElementById("condesp_ck_col").value = 0;
			  document.getElementById("condicoes_esp_col").style.display = 'none';
		   }
		
    
    }
    
    
   function limitarvalor(vlr){
        
       if(vlr.value){
		  var num = vlr.value.replace(",",".");
		  var  valor = parseFloat(num);
          if (valor > 100){
              verErro("Por favor insira valor até 100 por cento.")
			  vlr.value = '0,00';
              vlr.focus();
          }
		}
         
     }


    function limitarvalorCOM(vlr){        
         if(document.getElementById(vlr).value){
			 var num = document.getElementById(vlr).value.replace(",",".");
			 var  valor = parseFloat(num);
			  if (valor > 15 || valor < 0){
				  verErro("Por favor insira valor até 15 por cento.")
				  document.getElementById(vlr).value = '0,00';
				  document.getElementById(vlr).focus();
			  }
		 }
         
     }	 


   function float2moeda(num) {
    
       x = 0;
    
       if(num<0) {
          num = Math.abs(num);
          x = 1;
       }   if(isNaN(num)) num = "0";
          cents = Math.floor((num*100+0.5)%100);
    
       num = Math.floor((num*100+0.5)/100).toString();
    
       if(cents < 10) cents = "0" + cents;
          for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
             num = num.substring(0,num.length-(4*i+3))+'.'
                   +num.substring(num.length-(4*i+3));   ret = num + ',' + cents;   if (x == 1) ret = ' - ' + ret;return ret;
    
    }