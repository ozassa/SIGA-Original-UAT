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
		  document.getElementById('CEPEmpresa').value = cep;
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
               if (Valor > 180){
                  form.periodMaxCred.focus();
                  verErro('A duração não pode ser superior a 180 dias.');
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
         if(document.getElementById('opt').checked == true) {
			  document.getElementById('exibirBonus_campo').style.display = 'block';
			
		 } else {
			  document.getElementById('exibirBonus_campo').style.display = 'none';
		 }
      
       
    
    }
	
	
	
	
    
    function exibir_part(valor){
       
	     if(document.getElementById('partic').checked == true) {
			  document.getElementById('exibirParticipacao').style.display = 'block';
			
		 } else {
			  document.getElementById('exibirParticipacao').style.display = 'none';
		 }
    }
    
    function tipoApl(){
		/*
       if (document.all.tipoapolice.value == '0') { <!--Apólice Antiga-->
          document.getElementById('a801').disabled=true;
          document.getElementById('a502').disabled=true;
          //document.getElementById('b603').disabled=true;
          document.getElementById('b1504').disabled=true;
          document.getElementById('b1202').disabled=true;
          document.getElementById('c102').disabled=true;
          document.getElementById('d101').disabled=true;
          document.getElementById('d602').disabled=true;
          document.getElementById('d701').disabled=true;
          document.getElementById('e101').disabled=true;
          document.getElementById('f305').disabled=true;
          document.getElementById('f3301').disabled=true;
    
          document.getElementById('aplicaTaxa').style.display='';
       }else{
          document.getElementById('a801').disabled=false;
          document.getElementById('a502').disabled=false;
          //document.getElementById('b603').disabled=false;
          document.getElementById('b1504').disabled=false;
          document.getElementById('b1202').disabled=false;
          document.getElementById('c102').disabled=false;
          document.getElementById('d101').disabled=false;
          document.getElementById('d602').disabled=false;
          document.getElementById('d701').disabled=false;
          document.getElementById('e101').disabled=false;
          document.getElementById('f305').disabled=false;
          document.getElementById('f3301').disabled=false;
    
          document.getElementById('aplicaTaxa').style.display='';
       }
	   */
    }
    
    function HabilitaCamposBB(){
		/*
      document.getElementById('a801').disabled=false;
      document.getElementById('a502').disabled=false;
      //document.getElementById('b603').disabled=false;
      document.getElementById('b1504').disabled=false;
      document.getElementById('b1202').disabled=false;
      document.getElementById('c102').disabled=false;
      document.getElementById('d101').disabled=false;
      document.getElementById('d602').disabled=false;
      document.getElementById('d701').disabled=false;
      document.getElementById('e101').disabled=false;
      document.getElementById('f305').disabled=false;
      document.getElementById('f3301').disabled=false;
	  
	  */
    }
    
    function tipoBanco(xtipo){

       if (document.getElementById('tipobanco').value == '0') { // BB
	        if(document.getElementById('verLitigio').style.display = 'none')
                document.getElementById('verLitigio').style.display = 'block';
			
       }else{ 
          if(document.getElementById('verLitigio').style.display = 'block')
				document.getElementById('verLitigio').style.display = 'none';
		  
       }
    
    }
    

    function exibirCobertura(valor){
		 if(valor.checked==true) {
			  document.getElementById('percentualCobertura').style.display = 'block';
			
		 } else {
			  document.getElementById('percentualCobertura').style.display = 'none';
		 }
		
	}

    function exibir_nivel(valor){
     
		  if(valor.checked==true) {
			  document.getElementById('nivelPequSinistro').style.display = 'block';
			
		  } else {
			  document.getElementById('nivelPequSinistro').style.display = 'none';
		  }
   }
    
    function exibir_dados(valor){
      if(valor.checked == true) {
          document.getElementById('exibirEmpresa').style.display = 'block';
      }else {
          document.getElementById('exibirEmpresa').style.display='none';
      }
     
    }
    
	 function exibir_Cobertura(valor){
		  if(valor.checked == true) {
			  document.getElementById('exibir_Cobertura').style.display = 'block';
		  }else {
			  document.getElementById('exibir_Cobertura').style.display='none';
		  }
     
    }
    
	
	function exibir_b904(valor){
		  if(valor.checked == true) {
			  document.getElementById('exibir_b904').style.display = 'block';
		  }else {
			  document.getElementById('exibir_b904').style.display='none';
		  }

	}
	
	function exibir_b2604(valor){
		  if(valor.checked == true) {
			  document.getElementById('exibir_b2604').style.display = 'block';
		  }else {
			  document.getElementById('exibir_b2604').style.display='none';
		  }

	}
	
	function exibir_b2801(valor){
		  if(valor.checked == true) {
			  document.getElementById('exibir_b2801').style.display = 'block';
		  }else {
			  document.getElementById('exibir_b2801').style.display='none';
		  }

	}
	function exibir_d201(valor){
		  if(valor.checked == true) {
			  document.getElementById('exibir_d201').style.display = 'block';
		  }else {
			  document.getElementById('exibir_d201').style.display='none';
		  }

	}
	
	function exibir_f401(valor){
		  if(valor.checked == true) {
			  document.getElementById('exibir_f401').style.display = 'block';
		  }else {
			  document.getElementById('exibir_f401').style.display='none';
		  }

	}
	
	
    function validaCorrPrincipal(vlr){
        var cont = parseInt(document.getElementById('totalElement_corr').value);
        
        for (j=1;j<=cont;j++){
            // verErro('? '+ j);
             document.getElementById('CorretorPrincipal'+j).checked = false;
             document.getElementById('CorretorPrincipal'+j).value = 0;
             
             
        }
        
        document.getElementById('CorretorPrincipal'+vlr).checked = true;
        document.getElementById('CorretorPrincipal'+vlr).value = 1;
    }
    
    function limpaCamp(iCont) {
      document.getElementById('rz'+iCont).value='';
      document.getElementById('cnpj'+iCont).value='';
      document.getElementById('insce'+iCont).value='';

      document.getElementById('endereco'+iCont).value='';
      document.getElementById('num_endereco'+iCont).value='';
      document.getElementById('complemento'+iCont).value='';
      document.getElementById('cep'+iCont).value='';
      document.getElementById('cidade'+iCont).value='';
      document.getElementById('estado'+iCont).value='';
      
    }
    
    function limpaCamp_col(iCont) {
      document.getElementById('rz_col'+iCont).value='';
      document.getElementById('endereco_col'+iCont).value='';
      document.getElementById('pais_col'+iCont).value='';
      document.getElementById('zipcode_col'+iCont).value='';
      document.getElementById('taxID_col'+iCont).value='';
     
    
    }
    
    function limpaCamp_corr(iCont) {
      document.getElementById('Corretor'+iCont).value='';
      document.getElementById('Comissao'+iCont).value='';
      document.getElementById('Participacao'+iCont).value='';
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
            document.getElementById('viewAdequacao_campo').style.display  = 'block';
        }else {
            document.getElementById('viewAdequacao_campo').style.display  = 'none';
        }
	    	
	}
	
	
    function visualizafranq(){
       
       if (document.getElementById("Adfranq").checked){
          document.getElementById("franquia_an").style.display = 'block';
          document.getElementById("Adfranq").value = 1;
       }else{
          document.getElementById("Adfranq").value = 0;
          document.getElementById("franquia_an").style.display = 'none';
       }
	  
    
    }
    
    
    function visualizacondesp(valor){
		
		   if (document.getElementById("condesp_ck").checked){
			  document.getElementById("condicoes_esp").style.display = 'block';
			  document.getElementById("condesp_ck").value = 1;
		   }else{
			  document.getElementById("condesp_ck").value = 0;
			  document.getElementById("condicoes_esp").style.display = 'none';
		   }
		
    
    }
    
    function visualizacondesp_col(valor){
			
		   if (document.getElementById("condesp_ck_col").checked){
			  document.getElementById("condicoes_esp_col").style.display = 'block';
			  document.getElementById("condesp_ck_col").value = 1;
		   }else{
			  document.getElementById("condesp_ck_col").value = 0;
			  document.getElementById("condicoes_esp_col").style.display = 'none';
		   }
		
    
    }
     function ContaCaracteres(field,MaxLength) {
         obj = field.value;
         if (MaxLength !=0) {
             if (obj.length > MaxLength)  {
                 verErro('Este campo suporta apenas 1000 caracteres.');
                 document.all.condicoes_especiais.value.substring(0, MaxLength);
             }
         }
         document.all.cont.value = obj.length;
     }
    
     function limitarvalor(vlr){
        
    
         var  valor = parseFloat(vlr.value);
          if (valor > 100){
              verErro("Por favor insira valor até 100 por cento.")
              vlr.focus();
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