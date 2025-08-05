function valida_cadastro() {

     var Form, s;
     Form = document.cadastro;

     s = limpa_string(Form.cnpj.value);
     // checa se é cpf
     if (s.length == 11) {
        if (valida_CPF(Form.cnpj.value) == false ) {
           Form.cnpj.select();
           Form.cnpj.focus();
           verErro("O CNPJ não é válido !");
           return false;
        }
     }
     // checa se é cgc
     else if (s.length == 14) {
        if (valida_CGC(Form.cnpj.value) == false ) {
           verErro("O CNPJ não é válido !");
           Form.cnpj.select();
           Form.cnpj.focus();
           return false;
        }
     } else {
        verErro("O CNPJ não é válido !");
        Form.cnpj.select();
	Form.cnpj.focus();
        return false;
     }

     return true;
}


function limpa_string(S){
// Deixa so' os digitos no numero
var Digitos = "0123456789";
var temp = "";
var digito = "";
    for (var i=0; i<S.length; i++){
      digito = S.charAt(i);
      if (Digitos.indexOf(digito)>=0){temp=temp+digito}
    }
    return temp
}
function valida_CPF(s)
{
        var i;
        s = limpa_string(s);
        var c = s.substr(0,9);
        var dv = s.substr(9,2);
        var d1 = 0;
        for (i = 0; i < 9; i++)
        {
                d1 += c.charAt(i)*(10-i);
        }
        if (d1 == 0) return false;
        d1 = 11 - (d1 % 11);
        if (d1 > 9) d1 = 0;
        if (dv.charAt(0) != d1)
        {
                return false;
        }

        d1 *= 2;
        for (i = 0; i < 9; i++)
        {
                d1 += c.charAt(i)*(11-i);
        }
        d1 = 11 - (d1 % 11);
        if (d1 > 9) d1 = 0;
        if (dv.charAt(1) != d1)
        {
                return false;
        }
        return true;
}

function valida_CGC(s)
{
        var i;
        s = limpa_string(s);
        var c = s.substr(0,12);
        var dv = s.substr(12,2);
        var d1 = 0;
        for (i = 0; i < 12; i++)
        {
                d1 += c.charAt(11-i)*(2+(i % 8));
        }
        if (d1 == 0) return false;
        d1 = 11 - (d1 % 11);
        if (d1 > 9) d1 = 0;
        if (dv.charAt(0) != d1)
        {
                return false;
        }

        d1 *= 2;
        for (i = 0; i < 12; i++)
        {
                d1 += c.charAt(11-i)*(2+((i+1) % 8));
        }
        d1 = 11 - (d1 % 11);
        if (d1 > 9) d1 = 0;
        if (dv.charAt(1) != d1)
        {
                return false;
        }
        return true;
}

function Ini(){
// colocando o foco no campo CNPJ
document.cadastro.cnpj.focus();
}
