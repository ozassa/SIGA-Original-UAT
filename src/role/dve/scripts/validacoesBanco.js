// JavaScript Document

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
	        str = str+'&EXECUTAR=1&Exportador='+document.getElementById('Exportador').value+
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
			'&validaDataNPC='+document.getElementById('validaDataNPC'+stritem).value+					  
			'&t_Financiamento='+document.getElementById('t_Financiamento'+stritem).value+
			'&n_Operacao='+document.getElementById('n_Operacao'+stritem).value+
			'&v_Financiamento='+document.getElementById('v_Financiamento'+stritem).value+
			'&v_Pago='+document.getElementById('v_Pago'+stritem).value+
			'&v_SaldoFinanc='+document.getElementById('SaldoFinanc'+stritem).value;
			
			
		 // verErro(str);			  
		 
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
	function BuscaApolice(vlr){
		{  
			request.open("GET", "<?php echo $root?>role/dve/interf/control_busca_apolice.php?Cliente=" + vlr, true);
			request.onreadystatechange = handleHttpResponse;  
			request.send(null);		
		}
		
		function handleHttpResponse()
		{  
			campo_select = document.forms[0].n_Apolice;  
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
	if(document.getElementById('Exportador').value == ''){
		if(document.getElementById('Importador').value == '' && document.getElementById('DATAINI').value == '' && document.getElementById('DATAFIM').value == '' && document.getElementById('Fatura').value == '' ){
			verErro('Para exibir todos os clientes você deve informar o período ou importador.');
			return false;
		}
		
	}else{
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


	function paginacao(valor){
		document.getElementById("Paginar").value = valor;
    //consultarDVE.submit();
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
  	

