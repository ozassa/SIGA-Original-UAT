
UTF8 = {
    encode: function(s){
        for(var c, i = -1, l = (s = s.split("")).length, o = String.fromCharCode; ++i < l;
            s[i] = (c = s[i].charCodeAt(0)) >= 127 ? o(0xc0 | (c >>> 6)) + o(0x80 | (c & 0x3f)) : s[i]
        );
        return s.join("");
    },
    decode: function(s){
        for(var a, b, i = -1, l = (s = s.split("")).length, o = String.fromCharCode, c = "charCodeAt"; ++i < l;
            ((a = s[i][c](0)) & 0x80) &&
            (s[i] = (a & 0xfc) == 0xc0 && ((b = s[i + 1][c](0)) & 0xc0) == 0x80 ?
            o(((a & 0x03) << 6) + (b & 0x3f)) : o(128), s[++i] = "")
        );
        return s.join("");
    }
};


 var request = false;
 var dest;
 
 
//Verifica se está usando internet explorer
try {
	request = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
	try {
		request = new ActiveXObject("Microsoft.XMLHTTP");
	} catch (E) {
		request = false;
	}
}
if (!request && typeof XMLHttpRequest != 'undefined') {
	request = new XMLHttpRequest();
}	

//************Função para acessar as páginas e envio de parâmentro POST ou GET
function loadHTML(URL, destination,GetORPost,str,stritem){
    dest = destination;
  URL = antiCacheRand(URL);
  contentDiv = document.getElementById(dest);
        /*
        str = str+'&buscar=1&Exportador='+document.getElementById('Exportador').value+
		         '&Importador='+document.getElementById('Importador').value+'&Fatura='+
				 document.getElementById('Fatura').value+'&DATAINI='+
				 document.getElementById('DATAINI').value+'&DATAFIM='+
				 document.getElementById('DATAFIM').value;
		
		*/
		//verErro(document.getElementById('DataNPC'+stritem).value);
		
		str = str+'&DataLiq='+document.getElementById('DataLiq'+stritem).value+		
		          '&DataProrrogacao='+document.getElementById('DataProrrogacao'+stritem).value+
				  '&DataNPC='+document.getElementById('DataNPC'+stritem).value+
				  '&stritem='+stritem+
				  '&validaDataNPC='+document.getElementById('ValidaDataNPC'+stritem).value+					  
				  '&t_Financiamento='+document.getElementById('t_Financiamento'+stritem).value+
                  '&n_Operacao='+document.getElementById('n_Operacao'+stritem).value+
                  '&v_Financiamento='+document.getElementById('v_Financiamento'+stritem).value+
                    '&v_Pago='+document.getElementById('v_Pago'+stritem).value+
				  '&v_SaldoFinanc='+document.getElementById('SaldoFinanc'+stritem).value;
				
				  
	  //verErro(str);			  
				 
  		if (GetORPost == 'GET'){
			
		    request.open("GET", URL+str, true);
			
			request.onreadystatechange = function(){
              if (request.readyState == 4 && request.status == 200) {
			     if(request.responseXML){
				    //processXML(request.responseXML);	
					verErro(request.responseText);
					//contentDiv.innerHTML = request.responseText;	  
				 }else{
					contentDiv.innerHTML = request.responseText;
					verErro(request.responseText);
				 }
			  }else{
			     //contentDiv.innerHTML = "Error: Status "+request.status;
			  }
			}
			request.send(null);
		}else{		    				
			 
			request.open('POST', URL, true);
			request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			request.setRequestHeader("Content-length", "parameters-length");
			request.setRequestHeader("Connection", "close");
			request.send(str); 				
			
			request.onreadystatechange = function(){
              if (request.readyState == 4 && request.status == 200) {
			     if(request.responseXML){
				    processXML(request.responseXML);
				 }else{
					 contentDiv.innerHTML = request.responseText;
					 
				 }
			  }else{
			      //contentDiv.innerHTML = "Error: Status "+request.status;
			  }
			}
				
		}
			
}
  	

//Função para correção de chache da verificação
function antiCacheRand(aurl){
      var dt = new Date();
      if(aurl.indexOf("?")>=0){// já tem parametros
          return aurl + "&" + encodeURI(Math.random() + "_" + dt.getTime());
      }else{ return aurl + "?" + encodeURI(Math.random() + "_" + dt.getTime());}
  }

  //******************************************************************

function getformvalues(fobj,valfunc){
    var str = "";
    for(var i=0; i<fobj.elements.length; i++){
         str +=fobj.elements[i].name +"="+escape(fobj.elements[i].value)+"&";  
     }return str; 
  }

function SubmitForm(theform,serverPage,objID,getOrPost){
	
   var file= serverPage;
   var str = getformvalues(theform,objID);
   var valor = "";       
       obj = document.getElementById(objID);	
	 
	 loadHTML(serverPage,obj,getOrPost,str);
           
 }
 
  function processXML(obj){
	
    var dataArray = obj.getElementsByTagName("busca");
      if (dataArray.length >0){  
	    
          // percorre o arquivo XML paara extrair os dados
        for (var i = 0; i<dataArray.length; i++){
            var item = dataArray[i];
	        var nome = item.getElementsByTagName("nome")[0].firstChild.nodeValue;
	       
        }
               
        if (nome != '')
            verErro(nome);   
         
                          
      }
            
}

  //////////////////////////////////////////////////////////////////////////////////////
// 1º COMBO INICIO
///////////////////////////////////////////////////////////////////////////////////////
function BuscaApolice(vlr, cod_banco){
{  
    //verErro(vlr.length);
	request.open("GET", "interf/control_busca_apolice.php?idInsured=" + vlr +"&codBanco=" + cod_banco, true);
	request.onreadystatechange = handleHttpResponse;  
	request.send(null);		
}

function handleHttpResponse()
{  
	if (document.forms[0].n_Apolice) {
		campo_select = document.forms[0].n_Apolice;
	} else {
		campo_select = document.forms[0].idInform;		
	}
	if (request.readyState == 4) 
	{    
		campo_select.options.length = 0;    
		results = request.responseText.split(",");    
		
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
   
 }


function validaForm(){
	var campoCliente = document.getElementById('idInsured').value;
	var campoApolice = document.getElementById('n_Apolice').value;
	var campoPerfil = document.getElementById('perfil').value;

  if(campoCliente.length == 0){
		verErro('Você deve selecionar o cliente.');
		return false;
	} else if(campoApolice == 0 && campoPerfil == 'B'){
		verErro('Você deve selecionar o número da apólice.');
		return false;
	} else {
		return true;
	}
}

function formatar(src, mask){
  var i = src.value.length;
  var saida = mask.substring(0,1);
  var texto = mask.substring(i)
	if (texto.substring(0,1) != saida)
	  {
		src.value += texto.substring(0,1);
	  }
}


function validaDat(campo,valor) {
	var date=valor;
	var ardt=new Array;
	var ExpReg=new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
	ardt=date.split("/");
	erro=false;
	if (valor.length > 0){
		if ( date.search(ExpReg)==-1){
			erro = true;
			}
		else if (((ardt[1]==4)||(ardt[1]==6)||(ardt[1]==9)||(ardt[1]==11))&&(ardt[0]>30))
			erro = true;
		else if ( ardt[1]==2) {
			if ((ardt[0]>28)&&((ardt[2]%4)!=0))
				erro = true;
			if ((ardt[0]>29)&&((ardt[2]%4)==0))
				erro = true;
		}
		if (erro) {
			verErro("\"" + valor + "\" não é uma data válida!!!");
			campo.focus();
			campo.value = "";
			return false;
		}
	}
	return true;
}

function checarDatas(campo,data1,data2,str){   
   //var NomeForm = document.Formulario;   
  
   var data_1 = data1;   
   var data_2 = data2; 
      
   if (data1.length > 0){
	   var Compara01 = parseInt(data_1.split("/")[2].toString() + data_1.split("/")[1].toString() + data_1.split("/")[0].toString());   
	   var Compara02 = parseInt(data_2.split("/")[2].toString() + data_2.split("/")[1].toString() + data_2.split("/")[0].toString());   
	   
		if (Compara01 <= Compara02) {   
			verErro('Atenção: Esta data deve ser maior que a '+str); 
			campo.focus();
			campo.value = "";
		}
				
   }
    return false;   
}  

function checarDatas2(campo,data1,data2,str,dado){   
   //var NomeForm = document.Formulario;   
   
   var data_1 = data1;   
   var data_2 = data2; 
     
   if (str == 'validar180'){
	   //data_2 = somadiasvalidos(data_2, dado)
	   //data_2 = somadias(data_2, dado);
	   str = ' data de embarque mais '+ dado + ' dias.';
	   
   }
   
   
   
   if (data_1.length > 0){
	   var Compara01 = parseInt(data_1.split("/")[2].toString() + data_1.split("/")[1].toString() + data_1.split("/")[0].toString());   
	   var Compara02 = parseInt(data_2.split("/")[2].toString() + data_2.split("/")[1].toString() + data_2.split("/")[0].toString());   
	   
	   //verErro(Compara01+' - '+ Compara02+'??'+data_1+' '+data2+' '+str+' '+dado);
		if (Compara01 > Compara02) {   
				verErro('Atenção: Esta data não pode ser maior que a '+str); 
				campo.focus();
			    campo.value = "";
	    }
			
   }
   
    return false;   
}   


function validaDataNPC(valor,strnum){
	 
	 if (document.getElementById('DataNPC'+strnum).value){
		  document.getElementById('validaDataNPC'+strnum).value = 1;
	 }else{
		  document.getElementById('validaDataNPC'+strnum).value = 0;
	 }
	
}


function numdias(mes,ano) {
    if((mes<8 && mes%2==1) || (mes>7 && mes%2==0)) 
        return 31;  
    if(mes!=2)
       return 30; 
    if(ano%4==0) 
       return 29;    
    return 28;
}

function verificardias(data,dias){
	data=data.split('/'); 
    dia=parseInt(data[0]);
	mes=parseInt(data[1]);
    ano=parseInt(data[2]);  
	
	valor = ano +mes+dia;
	valor = (valor + dias);
	return valor;
}

function somadias(data, dias) {
    data=data.split('/'); 
    diafuturo=parseInt(data[0])+dias;
    mes=parseInt(data[1]);    

    ano=parseInt(data[2]);
      while(diafuturo>numdias(mes,ano)) {   
         diafuturo-=numdias(mes,ano);
         mes++;     

            if(mes>12) {  
               mes=1;
               ano++;   
            }
       }
	
	if (diafuturo <=9)
	   diafuturo = '0'+diafuturo;
	if (mes <=9) 
	   mes = '0'+mes;
	   
    return diafuturo+"/"+mes+"/"+ano;
}

function exportarExcel(){
    document.getElementById("Exportar").value = 1;
    //consultarDVE.submit();
}

function ChecarBox(campo,campo1){	
	if(document.getElementById(campo).checked){
	     document.getElementById(campo1).value = 1; 	
	}else{
	     document.getElementById(campo1).value = 0; 
	}	
}

function checkDecimals(fieldName, fieldValue) {

  if (fieldValue == "") {
    verErro("Preenchimento obrigatório.");
    fieldName.value='0,00';
    fieldName.focus();
  } else {
    err = false;
    dec = ",";
    mil = ".";
    v = "";
    c = "";
    len = fieldValue.length;
    for (i = 0; i < len; i++) {
      c = fieldValue.substring (i, i+1);
      if (c == dec) { break; }
      if (c != mil) {
        if (isNaN(c)) {
          err = true;
          verErro("Este não é um número válido.");
          fieldName.value='0,00';
          fieldName.focus();
          break;
        } else {
          v += c;
        }
      }
    }
    if (!err) {
      if (i == len) {
        v += "00";
      } else {
        if (c == dec) i++;
        if (i == len) {
          v += "00";
        } else {
          c = fieldValue.substring (i, i+1);
          if (isNaN(c)) {
            verErro("Este não é um número válido.");
            fieldName.value='0,00';
            fieldName.focus();
            err = true;
          } else {
            v += c;
          }
        }
        i++;
        if (!err && i == len) {
          v += "0";
        } else {
          c = fieldValue.substring (i, i+1);
          if (isNaN(c)) {
            verErro("Este não é um número válido.");
            fieldName.value='0,00';
            fieldName.focus();
            err = true;
          } else {
            v += c;
          }
        }
      }
      fieldValue = "," + v.substring (v.length - 2, v.length);
      v = v.substring (0, v.length - 2);
      while (v.length > 0) {
        t = v.substring (v.length >= 3 ? v.length - 3 : 0, v.length);
        v = v.substring (0, v.length >= 3 ? v.length - 3 : 0);
        fieldValue = (v.length > 0 ? "." : "") + t + fieldValue;
      }
      fieldName.value = fieldValue;
    }
  }
}


	function toFloat(str){
  		if($.trim(str) === ''){
  			return 0;
  		}
  		str = str.replace(/\./g,'');
  		str = str.replace(/\,/g,'.');
  		return parseFloat(str);
  	}

  	function validarValorPago(el){
  		var valor_pago = toFloat(el.val());
  		var valor_original = toFloat(el.parent().find('.v_Original').val());

  		if(valor_pago > valor_original + 10){
  			console.log(valor_pago);
  			console.log(valor_original);
  			verErro('Valor pago não pode ser maior que o valor do embarque');
  			el.val(0)
  		}
	}

	function validaVencimentoProrrogado(el){
		var dt = el.val();
		dt = dt.substr(6,4)+'-'+dt.substr(3,2)+'-'+dt.substr(0,2);
		var newDt = new Date(dt);
		var dtVenc = new Date(el.parent().find('.dtVenc').val().substr(0,10));

		if(newDt <= dtVenc){
			verErro('Data Vencimento Prorrogada deve ser maior que a data de vencimento original');
  			el.val('')
		}
	}

	jQuery(document).ready(function($) {
		$('body').on('blur', '.v_Pago', function(event) {
			checkDecimals(this, this.value);
			validarValorPago($(this));
		});

		$('body').on('blur', '.DataProrrogacao', function(event) {
			validaVencimentoProrrogado($(this));
		});
	});