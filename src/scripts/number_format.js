// JavaScript Document

function number_format_js(number, decimals, dec_point, thousands_sep) {
    
    var n = number, prec = decimals; 
    var toFixedFix = function (n,prec) {
        var k = Math.pow(10,prec);        
		return (Math.round(n*k)/k).toString();
    };
 
    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);    
	
	var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
    var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
 
    var s = (prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;
    var abs = toFixedFix(Math.abs(n), prec);
    var _, i;
 
    if (abs >= 1000) {
        _ = abs.split(/\D/);        
		i = _[0].length % 3 || 3;
 
        _[0] = s.slice(0,i + (n < 0)) + _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
        s = _.join(dec);    } else {
        s = s.replace('.', dec);
    }
 
    var decPos = s.indexOf(dec);    
	if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) {
        s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
    }
    else if (prec >= 1 && decPos === -1) {
        s += dec+new Array(prec).join(0)+'0';    
	}
    return s;
}

function replaceAll(string, token, newtoken) {
    while (string.indexOf(token) != -1) {
        string = string.replace(token, newtoken);
    }

    return string;
}

function retorna_dinheiro_us(vlr){
    vlr = vlr+"";
    var val_sem_ponto = replaceAll(vlr, ".", "");
    var novo_val = replaceAll(val_sem_ponto, ",", ".");

    return novo_val;
}