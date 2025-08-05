 function numeros(){
       tecla = event.keyCode;
          if ((tecla >= 48 && tecla <= 57) ||(tecla == 44 || tecla ==46)) {
             return true;
          } else {
              verErro('Este campo aceita somente valores num&eacute;ricos.');
              return false;
          }
    }


function MascaraDecimal(num){
	x = 0;
    //num  = moeda2float(num);
	if(num<0)
	{
	num = Math.abs(num);
	x = 1;
	}
	
	if(isNaN(num))
	num = "0";
	
	cents = Math.floor((num*100+0.5)%100);
	
	num = Math.floor((num*100+0.5)/100).toString();
	
	if(cents < 10) cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
	num = num.substring(0,num.length-(4*i+3))+'.'+num.substring(num.length-(4*i+3)); ret = num + ',' + cents;
	
	if (x == 1)
	ret = '-' + ret;
	
	return ret;


}

function moeda2float(moeda){
	if (moeda.lenght > 0){
		while (moeda.indexOf(".") > 0)
		moeda = moeda.replace(".","");
		
		moeda = moeda.replace(",",".");
		return parseFloat(moeda);
	}else{
	    return 0;	
	}
}


function notnull(fieldName, fieldValue){
	if (fieldValue == ""){
	  verErro("Preenchimento Obrigat&oacute;rio")
	  fieldName.focus()
	  
	}
}
function numVal (n) {
	  v = "";
	  len = n.length;
	  for (i = 0; i < len; i++) {
		c = n.substring (i, i+1);
		v = v + ((c == ",") ? "." : (isNaN(c) ? "" : c));
	  }
	  return v;
}

function dot2comma (n) {
  v = "";
  len = n.length;
  for (i = 0; i < len; i++) {
    c = n.substring (i, i+1);
    v = v + (c == "." ? "," : c);
  }
  return v;
}

function checkDecimals(fieldName, fieldValue) {
  
  if (fieldValue == "") {
    verErro("Preenchimento obrigat&oacute;rio.");
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
          verErro(unescape("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido."));
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
            verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
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
            verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
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

function formatDecimals(fieldName, fieldValue) {

  if (fieldValue == "") {
    fieldValue="0";
  }
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
        verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
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
          verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
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
          verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
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

function checkDecimalsMil(fieldName, fieldValue) {

  if (isNaN(fieldValue) && fieldValue != '') {
    verErro("Este n&atilde;o &eacute; um n&uacute;mero v&aacute;lido.");
    fieldName.value = '';
    fieldName.select();
    return;
  }
  if(fieldValue != ''){
    fieldName.value = Math.round(fieldValue - 0.5);
  }
}

function checkValue(field, value){
  checkDecimals(field, value);
  if(value > 100){
    verErro("O valor deste campo nao pode ser maior que 100");
    field.value='0,00';
    field.focus();
  }
}




