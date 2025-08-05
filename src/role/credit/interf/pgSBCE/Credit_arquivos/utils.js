function notnull(fieldName, fieldValue){
if (fieldValue == ""){
  verErro("Preenchimento Obrigatório")
  fieldName.focus() }
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

function checkDecimalsMil(fieldName, fieldValue) {

  if (isNaN(fieldValue) && fieldValue != '') {
    verErro("Este não é um número válido.");
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
