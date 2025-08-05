// JavaScript Document


	function confirma(f, id, remove){
	  
	  if(remove){
		if(confirm('Remover este importador da lista?')){
		  f.idDetail.value = id;
		  f.submit();
		}
	  }else{
		f.comm.value = 'include';
		f.idDetail.value = id;
		f.action += '#importers';
		f.submit();
	  }
	  
	}



//Função aparecer mensagem
function enviaMsg(msg){
	
	var content3 = "<span>"+msg+"</span>";
	T$('validar').onclick = TINY.box.show(content3,0,0,0,0,3);
	
}
//Função validar formulario de login e senha index.php

function validaFrm(){
	//alert(document.getElementById("password").value);
	if(document.getElementById("login").value == "" || document.getElementById("password").value == ""){
		enviaMsg('Usu&aacute;rio ou Senha inv&aacute;lidos!');
		return false;
	}else{
		return true;
	}
	
}

function validate(campo,valor){
	var date=valor;
	var ardt=new Array;
	var ExpReg=new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
	ardt=date.split("/");
	erro=false;
	
	if(campo.value != ''){
		if (date.search(ExpReg)==-1){
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
	}
	
	if (erro) {
		verErro("\"" + valor + "\" n&atilde;o &eacute; uma data v&aacute;lida!!!");
		campo.value = '';
		campo.focus();
		campo.value = "";
		return false;
	}
	
}
