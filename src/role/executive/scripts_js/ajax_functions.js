// JavaScript Document

var request = false;
var dest;

   //var browserName =navigator.appVersion;

	//    verErro(browserName);

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

	// Função para validar CNPJ.
	function validarCNPJ(cnpj) {
	 
	    cnpj = cnpj.replace(/[^\d]+/g,'');
	 
	    if(cnpj == '') return false;
	     
	    if (cnpj.length != 14)
	        return false;
	 
	    // Elimina CNPJs invalidos conhecidos
	    if (cnpj == "00000000000000" || 
	        cnpj == "11111111111111" || 
	        cnpj == "22222222222222" || 
	        cnpj == "33333333333333" || 
	        cnpj == "44444444444444" || 
	        cnpj == "55555555555555" || 
	        cnpj == "66666666666666" || 
	        cnpj == "77777777777777" || 
	        cnpj == "88888888888888" || 
	        cnpj == "99999999999999")
	        return false;
	         
	    // Valida DVs
	    tamanho = cnpj.length - 2
	    numeros = cnpj.substring(0,tamanho);
	    digitos = cnpj.substring(tamanho);
	    soma = 0;
	    pos = tamanho - 7;
	    for (i = tamanho; i >= 1; i--) {
	      soma += numeros.charAt(tamanho - i) * pos--;
	      if (pos < 2)
	            pos = 9;
	    }
	    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
	    if (resultado != digitos.charAt(0))
	        return false;
	         
	    tamanho = tamanho + 1;
	    numeros = cnpj.substring(0,tamanho);
	    soma = 0;
	    pos = tamanho - 7;
	    for (i = tamanho; i >= 1; i--) {
	      soma += numeros.charAt(tamanho - i) * pos--;
	      if (pos < 2)
	            pos = 9;
	    }
	    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
	    if (resultado != digitos.charAt(1))
	          return false;
	           
	    return true;
	    
	}
 
    // Função para retirar os espaços em branco do início e do fim da string.
    function Trim(strTexto) {
      // Substitúi os espaços vazios no inicio e no fim da string por vazio.
      return strTexto.replace(/^\s+|\s+$/g, '');
    }

    // Função para validação de CEP.
    function IsCEP(strCEP) {
      // Caso o CEP não esteja nesse formato ele é inválido!
      var objER = /^[0-9]{5}-[0-9]{3}$/;

      strCEP = Trim(strCEP);
      if(strCEP.length > 0) {
        if(objER.test(strCEP))
          return true;
        else
          return false;
      } else
        return false;
    }


	function loadHTMLIE(URL, destination,GetORPost,idConsultor,id,operacao,Tabela){
		if(Tabela == "CobComprador"){
	   	if(idConsultor != ""){
	   		empresaID = idConsultor;
	   	}else{
	   		empresaID = document.getElementById('cobEmpresaID').value;
	   	}

	   	if (validarCNPJ(document.getElementById('cnpjComprador').value) && document.getElementById('cnpjComprador').value) {
		   	var dados = {
		   		idInform        :  id,
			   	Operacao        :  operacao,
			   	cobEmpresaID    :  empresaID,
			   	Nome          	:	 document.getElementById('razaoComprador').value,
			   	CNPJ          	:	 document.getElementById('cnpjComprador').value,
			   	sessao          :  GetORPost
		   	};
		  } else{
		  	verErro('CNPJ inv&aacute;lido.');
			 	return false;
		  }
	   	
	  } else if(Tabela == "Empresa"){
		   	if(idConsultor != ""){
		   		empresaID = idConsultor;
		   	}else{
		   		empresaID = document.getElementById('empresaID').value;
		   	}

		   	if (validarCNPJ(document.getElementById('CNPJEmpresa').value) && document.getElementById('CNPJEmpresa').value) {
		   		if (IsCEP(document.getElementById('CEPEmpresa').value) && document.getElementById('CEPEmpresa').value){
				   	var dados = {
			   			idInform        	: id,
					   	Operacao        	: operacao,
					   	empresaID					: empresaID,
					   	RazaoSocial				: document.getElementById('RazaoSocialEmpresa').value,
					   	InscricaoEstadual	: document.getElementById('InscricaoEstadualEmpresa').value,
					   	CNPJ							: document.getElementById('CNPJEmpresa').value,
					   	EnderecoEmpresa		: document.getElementById('EnderecoEmpresa').value,
					   	NumeroEmpresa			: document.getElementById('NumeroEmpresa').value,
					   	CEP								: document.getElementById('CEPEmpresa').value,
					   	Cidade						: document.getElementById('CidadeEmpresa').value,
					   	UF								: document.getElementById('UFEmpresa').value,
					   	sessao 						: GetORPost
				   	};
				  } else{
				  	verErro('CEP inv&aacute;lido.');
					 	return false;
				  }
			  } else{
			  	verErro('CNPJ inv&aacute;lido.');
				 	return false;
			  }

		} else if(Tabela == "EmpresaColigada"){
		   	if(idConsultor != ""){
		   		empresaID = idConsultor;
		   	}else{
		   		empresaID = document.getElementById('empresaColID').value;
		   	}
		   	

		   	var dados = {
	   			idInform        	: id,
			   	Operacao        	: operacao,
			   	empresaColID			: empresaID,
			   	RazaoSocialCol		: document.getElementById('RazaoSocialCol').value,
			   	ZipCodeCol				: document.getElementById('ZipCodeCol').value,
			   	TaxIdCol					: document.getElementById('TaxIdCol').value,
			   	EnderecoCol				: document.getElementById('EnderecoCol').value,
			   	PaisCol						: document.getElementById('PaisCol').value,
			   	sessao 						: GetORPost
		   	};

		}

	   $.ajax({
			  type: "POST",
			  url: URL,
			  data: dados,
			  success: function(data) {
	      	$("#"+destination).html(data);
			  }
			})
	}
	

	
	function loadHTML(URL, destination,GetORPost,idConsultor,id,operacao,Tabela){
		dest = destination;
		var str = "";
		var chk = 0;
		URL = antiCacheRand(URL);
		contentDiv = document.getElementById(dest);
		var inform = id;	

		document.getElementById('Operacao').value = operacao;


		   //document.getElementById('empresaID').value;
		   if(document.getElementById('CorretorPrincipal').checked == true){
		   	chk = 1;  
		   }else{
		   	chk = 0;
		   }

		   if(operacao == "" || operacao == "Inserir"){
		   	operacao = "Inserir";
		   }else if(operacao != 'Remover'){
		   	operacao = "Alterar";
		   }


		   if(Tabela == "Corretor"){
		   	if(idConsultor == ""){
		   		idConsultor = document.getElementById('Corretor1').value;
		   	}




		   	str = str+
		   	'&idInform='+inform+
		   	'&Operacao='+operacao+
		   	'&Corretor1='+idConsultor+
		   	'&i_Grupo='+document.getElementById('i_Grupo').value+ 
		   	'&Comissao='+document.getElementById('Comissao').value+
		   	'&Participacao='+document.getElementById('Participacao').value+
		   	'&CorretorPrincipal='+chk+'&sessao='+GetORPost;


		   }else if(Tabela == "Empresa"){
		   	if(idConsultor != ""){
		   		empresaID = idConsultor;
		   	}else{
		   		empresaID = document.getElementById('empresaID').value;
		   	}

		   	str = str+
		   	'&idInform='+inform+
		   	'&Operacao='+operacao+
		   	'&empresaID='+empresaID+
		   	'&RazaoSocial='+document.getElementById('RazaoSocialEmpresa').value+
		   	'&InscricaoEstadual='+document.getElementById('InscricaoEstadualEmpresa').value+
		   	'&CNPJ='+document.getElementById('CNPJEmpresa').value+
		   	'&EnderecoEmpresa='+document.getElementById('EnderecoEmpresa').value+
		   	'&NumeroEmpresa='+document.getElementById('NumeroEmpresa').value+
		   	'&CEP='+document.getElementById('CEPEmpresa').value+
		   	'&Cidade='+document.getElementById('CidadeEmpresa').value+
		   	'&UF='+document.getElementById('UFEmpresa').value+'&sessao='+GetORPost;

		   }else if(Tabela == "EmpresaColigada"){
		   	if(idConsultor != ""){
		   		empresaID = idConsultor;
		   	}else{
		   		empresaID = document.getElementById('empresaColID').value;
		   	}
		   	str = str+
		   	'&idInform='+inform+
		   	'&Operacao='+operacao+  
		   	'&empresaColID='+empresaID+
		   	'&RazaoSocialCol='+document.getElementById('RazaoSocialCol').value+
		   	'&EnderecoCol='+document.getElementById('EnderecoCol').value+
		   	'&PaisCol='+document.getElementById('PaisCol').value+
		   	'&ZipCodeCol='+document.getElementById('ZipCodeCol').value+
		   	'&TaxIdCol='+document.getElementById('TaxIdCol').value+'&sessao='+GetORPost;

		   }else if(Tabela == "CobComprador"){
		   	if(idConsultor != ""){
		   		empresaID = idConsultor;
		   	}else{
		   		empresaID = document.getElementById('cobEmpresaID').value;
		   	}
		   	str = str+
		   	'&idInform='+inform+
		   	'&Operacao='+operacao+  
		   	'&cobEmpresaID='+empresaID+
		   	'&Nome='+document.getElementById('razaoComprador').value+
		   	'&CNPJ='+document.getElementById('cnpjComprador').value+
		   	'&sessao='+GetORPost;



		   }



		   if (GetORPost == 'GET'){
		   	request.open("GET", URL+str, true);

		   	request.onreadystatechange = function(){

		   		if (request.readyState == 4 && request.status == 200) {
		   			if(request.responseXML){
					//processXML(request.responseXML);	
					//verErro(request.responseText);
					contentDiv.innerHTML = request.responseText;
					document.getElementById('Operacao').value             = 'Inserir';
					document.getElementById('i_Grupo').value              = '';
					document.getElementById('Corretor1').value            = '';
					document.getElementById('Comissao').value             = '0,00';
					document.getElementById('Participacao').value         = '0,00';
					document.f.CorretorPrincipal[1].checked               = true; 
					document.f.CorretorPrincipal[1].value                 = 0;
					
					 //Limpar campos Empresa;

					 document.getElementById('empresaID').value                = '';
					 document.getElementById('RazaoSocialEmpresa').value       = '';
					 document.getElementById('InscricaoEstadualEmpresa').value = '';
					 document.getElementById('CNPJEmpresa').value              = '';
					 document.getElementById('EnderecoEmpresa').value          = '';
					 document.getElementById('NumeroEmpresa').value            = '';
					 document.getElementById('CEPEmpresa').value               = '';
					 document.getElementById('CidadeEmpresa').value            = '';
					 document.getElementById('UFEmpresa').value                = '';

					 //Limpar campos Coligadas;
					 document.getElementById('empresaColID').value     = '';
					 document.getElementById('RazaoSocialCol').value   = '';
					 document.getElementById('EnderecoCol').value      = '';
					 document.getElementById('PaisCol').value          = '';
					 document.getElementById('ZipCodeCol').value       = '';
					 document.getElementById('TaxIdCol').value         = '';
					 
					 
					 //limpar campos Empresa compradores
					 
					 document.getElementById('cobEmpresaID').value    ='';
					 document.getElementById('razaoComprador').value  ='';
					 document.getElementById('cnpjComprador').value    ='';
					 
					 //location.reload(); 
					}else{
						contentDiv.innerHTML = request.responseText;
						document.getElementById('Operacao').value             = 'Inserir';
						document.getElementById('i_Grupo').value              = '';
						document.getElementById('Corretor1').value            = '';
						document.getElementById('Comissao').value             = '0,00';
						document.getElementById('Participacao').value         = '0,00';
						document.f.CorretorPrincipal[1].checked               = true; 
						document.f.CorretorPrincipal[1].value                 = 0;

					 //Limpar campos Empresa;

					 document.getElementById('empresaID').value                = '';
					 document.getElementById('RazaoSocialEmpresa').value       = '';
					 document.getElementById('InscricaoEstadualEmpresa').value = '';
					 document.getElementById('CNPJEmpresa').value              = '';
					 document.getElementById('EnderecoEmpresa').value          = '';
					 document.getElementById('NumeroEmpresa').value            = '';
					 document.getElementById('CEPEmpresa').value               = '';
					 document.getElementById('CidadeEmpresa').value            = '';
					 document.getElementById('UFEmpresa').value                = '';

					 //Limpar campos Coligadas;
					 document.getElementById('empresaColID').value     = '';
					 document.getElementById('RazaoSocialCol').value   = '';
					 document.getElementById('EnderecoCol').value      = '';
					 document.getElementById('PaisCol').value          = '';
					 document.getElementById('ZipCodeCol').value       = '';
					 document.getElementById('TaxIdCol').value         = '';
					 
					 
					 //limpar campos Empresa compradores
					 
					 document.getElementById('cobEmpresaID').value    ='';
					 document.getElementById('razaoComprador').value  ='';
					 document.getElementById('cnpjComprador').value    ='';
					}
				}else{
				  //contentDiv.innerHTML = "Error: Status "+request.status;
				}
			}
			request.send(null);
		}else{		    				

			request.open('POST', URL, true);

			request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=iso-8859-1");
				//request.setRequestHeader("Content-Type", "text/html; charset=iso-8859-1");
				/*request.setRequestHeader("Content-length", "parameters-length");
				request.setRequestHeader("Connection", "close");*/
				request.send(str); 				
				
				request.onreadystatechange = function(){


					if (request.readyState == 4 && request.status == 200) {
						if(request.responseXML){

							processXML(request.responseXML);
						}else{

							contentDiv.innerHTML = request.responseText;
						}
					}else{
					 // alert('Error'+request.status);
				      //contentDiv.innerHTML = "Error: Status "+request.status;
				    }
				  }




				  document.getElementById('Operacao').value = 'Inserir';
				  document.getElementById('i_Grupo').value = '';
				  document.getElementById('Corretor1').value = '';
				  document.getElementById('Comissao').value = '0,00';
				  document.getElementById('Participacao').value = '0,00';
				  document.f.CorretorPrincipal[1].checked = true; 
				  document.f.CorretorPrincipal[1].value = 0;

					 //Limpar campos Empresa;

					 document.getElementById('empresaID').value = '';
					 document.getElementById('RazaoSocialEmpresa').value = '';
					 document.getElementById('InscricaoEstadualEmpresa').value = '';
					 document.getElementById('CNPJEmpresa').value = '';
					 document.getElementById('EnderecoEmpresa').value = '';
					 document.getElementById('NumeroEmpresa').value = '';
					 document.getElementById('CEPEmpresa').value = '';
					 document.getElementById('CidadeEmpresa').value = '';
					 document.getElementById('UFEmpresa').value = '';

					 //Limpar campos Coligadas;
					 document.getElementById('empresaColID').value = '';
					 document.getElementById('RazaoSocialCol').value = '';
					 document.getElementById('EnderecoCol').value = '';
					 document.getElementById('PaisCol').value = '';
					 document.getElementById('ZipCodeCol').value = '';
					 document.getElementById('TaxIdCol').value = '';
					 
					 
					 //limpar campos Empresa compradores
					 
					 document.getElementById('cobEmpresaID').value     ='';
					 document.getElementById('razaoComprador').value   ='';
					 document.getElementById('cnpjComprador').value    ='';


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


        function edita_Form(str1,str2,str3,str4,str5){

        	document.getElementById('Operacao').value = 'Alterar';		
        	document.getElementById('Corretor1').value = str1;
        	document.getElementById('Comissao').value = str2;
        	document.getElementById('Participacao').value = str3;
        	document.getElementById('i_Grupo').value = str5;
        	if(str4 == 1){
        		document.f.CorretorPrincipal[0].checked = true; 
        		document.f.CorretorPrincipal[0].value = 1;
        	}else{
        		document.f.CorretorPrincipal[1].checked = true; 
        		document.f.CorretorPrincipal[1].value = 0;
        	}
        	document.getElementById('Corretor1').focus();

        }

        function edita_formEmpresa(str1,str2,str3,str4,str5,str6,str7,str8,str9){

        	document.getElementById('Operacao').value = 'Alterar';		
        	document.getElementById('empresaID').value = str1;
        	document.getElementById('RazaoSocialEmpresa').value = str2;
        	document.getElementById('InscricaoEstadualEmpresa').value = str3;
        	document.getElementById('CNPJEmpresa').value = str4;
        	document.getElementById('EnderecoEmpresa').value = str5;
        	document.getElementById('NumeroEmpresa').value = str6;
        	document.getElementById('CEPEmpresa').value = str7;
        	document.getElementById('CidadeEmpresa').value = str8;
        	document.getElementById('UFEmpresa').value = str9;
        	document.getElementById('RazaoSocialEmpresa').focus();
        }

        function edita_formColigada(str1,str2,str3,str4,str5,str6,str7){

        	document.getElementById('Operacao').value = 'Alterar';		
        	document.getElementById('empresaColID').value = str1;
        	document.getElementById('RazaoSocialCol').value = str2;
        	document.getElementById('EnderecoCol').value = str3;
        	document.getElementById('PaisCol').value = str4;
        	document.getElementById('ZipCodeCol').value = str5;
        	document.getElementById('TaxIdCol').value = str6;
        	document.getElementById('RazaoSocialCol').focus();

        }

        function edita_formCobertura(str1,str2,str3){


        	document.getElementById('razaoComprador').value   = str1;
        	document.getElementById('cnpjComprador').value    = str2;
        	document.getElementById('cobEmpresaID').value     = str3;
        	document.getElementById('Operacao').value         = 'Alterar';	
        	document.getElementById('razaoComprador').focus();



        }




