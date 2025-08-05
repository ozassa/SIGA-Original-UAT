//-- FORMATAÇÃO DE VALORES COM CASAS DECIMAIS --

function Arredondamento(valor, numdec)
{
   var mascara = "999.999.999.999";
   var valmasc = "";
   var I       = 1;

   if (valor == "")
   {  valor = "0";
   }
   
   valor = parseFloat(ReplStr(valor, ",", "."));
   valor = ReplStr("" + valor, ".", ",");
	
   if (InStr(valor, ",") < 0)
   {  valor = valor + ","
      I = 1;
   }   
   else
   {  valor = valor.substring(0, (InStr(valor, ",") + numdec + 1));
      I = (valor.length - InStr(valor, ","));
   }

   for (n = I; n <= numdec; n++)
   {  valor = valor + "0"; 
   }

   valor = ReplStr(valor, ",", "");
   
   if (numdec > 0)
   {  mascara = mascara + ","; 
      
      for (n = 1; n <= numdec; n++)
      {  mascara = mascara + "9"; 
	  }
   }
   
   valmasc = FormatMasc(valor, mascara, "S");	  
   
   return valmasc;
}
	
//-- CRÍTICA DE DATAS --

function CritData(argvalue)
{
   var wdia;
   var wmes;
   var wano;
   var rret;
   var rstr;

   rret = true;

   if (argvalue.length == 10)
   {
      for (n = 0; n < argvalue.length; n++)
      {
         if ((argvalue.substring(n, n+1) < "0" || argvalue.substring(n, n+1) > "9") && (argvalue.substring(n, n+1) != "/"))
         {
            rret = false;
         }
      }

      if (rret)
      {
         wdia = argvalue.substring(0, 2);
         wmes = argvalue.substring(3, 5);
         wano = argvalue.substring(6, 10);

         if (argvalue.substring(2, 3) != "/" || argvalue.substring(5, 6) != "/")
         {
            rret = false;
         }
         else
         {
            if (wmes < "01" || wmes > "12")
            {
               rret = false;
            }
            else
            {
               if ((wmes == "01") || (wmes == "03") || (wmes == "05") || (wmes == "07") || (wmes == "08") || (wmes == "10") || (wmes == "12"))
               {
                  if (wdia < "01" || wdia > "31")
                  {
                     rret = false;
                  }
               }
               else
               {
                  if ((wmes == "04") || (wmes == "06") || (wmes == "09") || (wmes == "11"))
                  {
                     if (wdia < "01" || wdia > "30")
                     {
                        rret = false;
                     }
                  }
                  else
                  {
                     if (wmes == "02")
                     {
                        if ((wano % 4) == 0)
                        {
                           if (wdia < "01" || wdia > "29")
                           {
                              rret = false;
                           }
                        }
                        else
                        {
                           if (wdia < "01" || wdia > "28")
                           {
                              rret = false;
                           }
                        }
                     }
                     else
                     {
                        rret = false;
                     }
                  }
               }
            }
         }
      }
   }
   else
   {
      rret = false;
   }

   return rret;
}

//-- CRÍTICA DE HORAS --

function CritHora(argvalue)
{
   var whora;
   var wmin;
   var rret;
   var rstr;

   rret = true;

   if (argvalue.length != 5)
   {  rret = false; 
   }

   if (argvalue.substring(2,3) != ":")
   {  rret = false; 
   }
	  
   if (rret)
   {  whora = argvalue.substring(0,2);
	  wmin  = argvalue.substring(3,5);
		 
  	 if (whora < "00" || whora > "23")
	 {  rret = false; 
	 }
	  
	 if (wmin < "00" || whora > "59")
	 {  rret = false; 
	 }
   }

   return rret;
}

//-- CONVERTE DATA PARA STRING PARA COMPARAÇÃO --

function datastr(wdata)
{
   var retdata;

   retdata = "";
   retdata = wdata.substring(6,10) + wdata.substring(3,5) + wdata.substring(0,2);

   return retdata;
}

//-- CRITICA CAMPO OBRIGATÓRIO --
 
function CritTexto (pergunta)
{
   var wret;
   wret = true;
 
   if ((pergunta.value.length == 0) || (Trim(pergunta.value.substring(0,1)) == ""))
   {  wret = false;
   }

   return !wret;
}

//-- POSICIONA CURSOR EM UM OBJETO --
 
function Posiciona(campo) 
{
   campo.focus();
}

//-- PULO AUTOMÁTICO DO CURSOR PARA O PRÓXIMO OBJETO --

function Move(TheObj, Tam, TheProx)  
{
   var keycode = 0;

   if (TheObj.value.length == Tam)
   {  TheProx.focus();  
   }
   else
   {  if (window.event) 
      {  keycode = window.event.keyCode;
	  
	     if (keycode == 13)
	     {  TheProx.focus();
         }
      }
   }
}

//-- PULA O CURSOR PARA UM OBJETO --

function Pula(TheObj)  
{ 
   TheObj.focus();  
} 

//-- FORMATA UM NÚMERO COM ZEROS À ESQUERDA --

function FormatStr(argvalue, mask)  
{   
   var n;
   var strret = "";
   var nrep = 0;

   if ((argvalue.length==0)||(argvalue.substring(0,1)==" "))
   {  strret = argvalue;
   }
   else
   {  nrep   = mask.length - argvalue.length;
      strret = mask.substring(0, nrep) + argvalue;
   }
   
   return strret;
}   
 
//-- CRÍTICA DE NÚMEROS

function CritNum(argvalue)
{
  var rret;
  var temp;

  rret = true;
  
  for (n = 0; n < argvalue.length; n++)
      {
         temp = argvalue.substring(n, n+1);
		 if ((temp < "0" || temp > "9" ) & temp != "," )
         {
            rret = false;
         }
      }

  return !rret;
} 

//-- CRÍTICA DE CPF

function ValidaCPF(Vl_CgcCpf)
{
   var wret;
   var VA_CgcCpf;           
   var VA_Digito;           
   var Numero1;       
   var Numero2;       
   var Numero3;       
   var Numero4;       
   var Numero5;       
   var Numero6;       
   var Numero7;       
   var Numero8;       
   var Numero9;       
   var Numero10;       
   var Numero11;       
   var VA_Resto;            
   var VA_Resultado;        
   var VA_SomaDigito10;     
   var VA_resto1;           
   var divisao;
   var multiplic;
	
   wret = true;
   
   Vl_CgcCpf = ReplStr(Vl_CgcCpf, ".", "");
   Vl_CgcCpf = ReplStr(Vl_CgcCpf, "-", "");
   Vl_CgcCpf = ReplStr(Vl_CgcCpf, "/", "");
	
   VA_CgcCpf = Vl_CgcCpf;
   VA_Digito = VA_CgcCpf.substring(9, 11);
	
   Numero1  = parseInt( VA_CgcCpf.substring( 0, 1) );
   Numero2  = parseInt( VA_CgcCpf.substring( 1, 2));
   Numero3  = parseInt( VA_CgcCpf.substring( 2, 3));
   Numero4  = parseInt( VA_CgcCpf.substring( 3, 4));
   Numero5  = parseInt( VA_CgcCpf.substring( 4, 5));
   Numero6  = parseInt( VA_CgcCpf.substring( 5, 6));
   Numero7  = parseInt( VA_CgcCpf.substring( 6, 7));
   Numero8  = parseInt( VA_CgcCpf.substring( 7, 8));
   Numero9  = parseInt( VA_CgcCpf.substring( 8, 9));
   Numero10 = parseInt( VA_CgcCpf.substring( 9, 10));
   Numero11 = parseInt( VA_CgcCpf.substring( 10, 11));
    
   VA_Resultado = (Numero1*10)+(Numero2*9)+(Numero3*8)+(Numero4*7)+(Numero5*6)+(Numero6*5)+(Numero7*4)+(Numero8*3)+(Numero9*2);
	
   divisao = parseInt(VA_Resultado/11);
	
   multiplic = divisao * 11;

   VA_Resto = VA_Resultado - multiplic;
    
   if (VA_Resto < 2) 
   {  VA_resto1 = 0;
   }   
   else
   {  VA_resto1 = 11 - VA_Resto;
   }   
            
   if (VA_resto1 != Numero10) 
   {  wret = false;
   }
	 
   VA_Resultado=(Numero1*11)+(Numero2*10)+(Numero3*9)+(Numero4*8)+(Numero5*7)+(Numero6*6)+(Numero7*5)+(Numero8*4)+(Numero9*3)+(VA_resto1*2);
     
   divisao = parseInt(VA_Resultado/11);
	 
   multiplic = divisao * 11;
	  
   VA_Resto = VA_Resultado - multiplic;
    
   if (VA_Resto < 2) 
   {  VA_resto1 = 0;
   }		
   else
   {  VA_resto1 = 11 - VA_Resto;
   }
        
   if (VA_resto1 != Numero11)
   {  wret = false;
   } 

   return wret;
}

//-- CRÍTICA DE CGC

function ValidaCGC(Vl_CgcCpf)
{
   var wret = true;
   var k;
   var vind;
   var vaux;
   var var1;
   var vnum;
   var vdig;
   var n;
   var vloop;

   Vl_CgcCpf = ReplStr(Vl_CgcCpf, ".", "");
   Vl_CgcCpf = ReplStr(Vl_CgcCpf, "-", "");
   Vl_CgcCpf = ReplStr(Vl_CgcCpf, "/", "");
   
   vind  = 11;
   
   if (Vl_CgcCpf.length != 14)
   {  wret = false;
   }
   else
   {  for (k = 1; k < 3; k++)
      {  vaux = 0;
	     var1 = 2;
		 vnum = 0;
		 vdig = "";
         vloop = vind;
		 
		 for (vind = vloop; vind >= 0; vind--)
         {  vaux = parseInt(Vl_CgcCpf.substring(vind, vind+1));
		 
		    vnum = vnum + (vaux * var1);
			var1 = var1 + 1;
			
			if (var1 > 9)
			{  var1 = 2;
			}
		 }	 

		 vnum = vnum * 10;
		 var1 = vnum - (parseInt(vnum / 11) * 11);
		 
		 if (var1 > 9)
	 	 {  var1 = 0;
		 }

		 vdig = var1 + "";
		 vdig = vdig.substring(0,1);

		 if (vdig != Vl_CgcCpf.substring(k+11,k+12))
		 {  wret = false;
		 }
		 
		 vind = k + 11;
      }
   }  

   return wret;
}

//-- EQUIVALENTE AO REPLACE DO ASP 

function ReplStr(argvalue, strant, strnew)
{
   var straux = "";

   for (n = 0; n < argvalue.length; n++)
   {  if (argvalue.substring(n, n+1) == strant)
      {  straux = straux + strnew;
      }
	  else
	  {  straux = straux + argvalue.substring(n, n+1);
	  }
   }
 
   return straux;
}

//-- MÄSCARA PARA CPF E CGC

function MascIdent(ident,acao,tipo)
{
   // Ident é o campo a ser formatado
   // Acao pode ser : C(om) formatação ou  S(em) formataçao
   // Tipo pode ser : PFI ou PJU
 
   var retvalor;
   var temp;
 
   retvalor = ident;
 
   if (ident.length > 0)
   {  // transforma variável para caracter
      temp = "" + ident;

      if (tipo == "PFI") 
      {  if ((acao == "C") && (ident.length == 11))
         {  retvalor = temp.substring(0,3) + "." + temp.substring(3,6) +  "." + temp.substring(6,9) + "-" + temp.substring(9,11);
		 }
         else
	     {  if ((acao == "S") && (ident.length == 14))
            {  retvalor = temp.substring(0,3) + temp.substring(4,7) + temp.substring(8,11) + temp.substring(12,14);
		    }
		 }
      }
	  else
	  {  if (tipo == "PJU")
	     {  if ((acao == "C") && (ident.length == 14))
            {  retvalor = temp.substring(0,2) + "." + temp.substring(2,5) +  "." + temp.substring(5,8) + "/" + temp.substring(8,12) + "-" + temp.substring(12,14);
		    }
            else
            {  if ((acao == "S") && (ident.length == 18))
               {  retvalor = temp.substring(0,2) + temp.substring(3,6) + temp.substring(7,10) + temp.substring(11,15) + temp.substring(16,18);
		       }
		    }
		 }
	  }
   }
   	
   return retvalor;
}

//-- (DES)FORMATA STRING DE ACORDO COM A MÁSCARA

function FormatMasc(strin, mascara, formatar)
{
   var strout = "";
   var indin = strin.length - 1;

   if (formatar == "N")
   {  strout = ReplStr(strin, "-", "");
      strout = ReplStr(strout, ".", "");
      strout = ReplStr(strout, "/", "");
   }
   else
   {  if ((mascara == "") || (strin == ""))
      {  strout = strin;
      }
      else
      {  for (n = mascara.length - 1; n >= 0; n--)
         {  if ((mascara.substring(n, n+1) == ".") || (mascara.substring(n, n+1) == "-") || (mascara.substring(n, n+1) == "/") || (mascara.substring(n, n+1) == ","))
            {  strout = mascara.substring(n, n+1) + strout;
            }
	        else
	        {  strout = strin.substring(indin, indin+1) + strout;
			   indin = indin - 1;
			   
			   if (indin < 0)
			   {  n = -1;
			   } 
	        }
	     } 	 
		 
		 //if (strout.length < mascara.length)
		 //{  strout = mascara.substring(0, mascara.length - strout.length) + strout;
		 //}
      }
   }	  
 
   return strout;
}

//-- VERIFICA QUAL A TECLA PRESSIONADA

function ConfKey () 
{
   var keycode = 0;
   
   if (window.event) 
   {  keycode = window.event.keyCode;
   }

   return keycode;
}

//-- (DES)FORMATA CONTAS (CONTÁBIL e TRANSAÇÃO) DE ACORDO COM A MÁSCARA 

function FormatConta(strin, mascara, formatar)
{
   var strout = "";
   var indin = strin.length;
   var tam = 0;
   
   if (formatar == "N")
   {  strout = ReplStr(strin, "-", "");
      strout = ReplStr(strout, ".", "");
      strout = ReplStr(strout, "/", "");
   }
   else
   {  if ((mascara == "") || (strin == ""))
      {  strout = strin;
      }
      else
      {  for (n = 0; n <=mascara.length; n++)
	     {  if ((mascara.substring(n, n+1) == ".") || (mascara.substring(n, n+1) == "-") || (mascara.substring(n, n+1) == "/"))
            {  strout = strout + mascara.substring(n, n+1);
            }
	        else
	        {  strout = strout + strin.substring(tam,tam + 1);
			   tam  = tam + 1;
			   
			   if (tam == indin)
			   {  n = mascara.length + 1;
			   } 
	        }
	     } 	 
      }
   }	  
 
   return strout;
}

//-- VALIDA A TECLA PRESSIONADA DE ACORDO COM O TIPO DO CAMPO

function ValKey (restricao, argvalue) 
{
   var keycode = 0;

   // "C"  -> permite somente letras
   // "N"  -> permite somente números 
   // "D"  -> permite números com casa decimal
   // "DN" -> permite números (positivos e/ou negativos) com casa decimal 
   // "DT" -> permite números e barra para campo data
   // "X"  -> permite qualquer caractere menos plica e acento
   // "NL" -> impede que qualquer coisa seja digitada

   if (window.event) 
   {  keycode = window.event.keyCode;  
   
	  if ((restricao == "X") && (keycode == 39 || keycode == 180 || keycode == 38 || keycode == 34))
      {  window.event.keyCode = 0;
	  }
	  else 
      {  if ((keycode > 47) && (keycode < 58))   
	     {  if (restricao == "C")
	        {  window.event.keyCode = 0;
  		    }
	     }
	     else
	     {  if ((restricao == "DT") && (keycode != 47))
	        {  window.event.keyCode = 0;
		    }
		    else
		    {  if ((restricao == "N"))
	           {  window.event.keyCode = 0;
  		       }
		       else
		       {  if ((restricao == "D") && (keycode != 44))
  	              {  window.event.keyCode = 0;
		          } 
		          else
		          {  if ((restricao == "DN") && (keycode != 44) && (keycode != 45))
  	                 {  window.event.keyCode = 0;
		             }
		          }
			
			      if (keycode == 44)
			      {  if (InStr(argvalue, ",") >= 0 && restricao != "X")
  	                 {  window.event.keyCode = 0;
		             }
			      }  
			      else
			      {  if (keycode == 45 && restricao == "DN")
			         {  if (InStr(argvalue, "-") >= 0)
  	                    {  window.event.keyCode = 0;
		                } 
			         }  
			      }   
			   }   
			}   
		 }
		 
	     //--- TECLA PLIC E ACENTO SEM VOGAL ---
   
		 if ((window.event.keyCode == 39) || (window.event.keyCode == 180) || (keycode == 34)) 
	     {  window.event.keyCode = 0;
	     }
	  }	 
   }
}

//-- COLOCA AS BARRAS DE UM CAMPO DATA AUTOMATICAMENTE

function PoeBarra(argvalue)
{
   var I         = 0;
   var numbarras = 0;
   var keycode   = 0;

   if (argvalue.size >= 10)
   {  qtbarras = 2;
   }
   else
   {  qtbarras = 1;
   }
   
   keycode = window.event.keyCode; 
   
   if (keycode != 8 && keycode != 46)
   {  I = InStr(argvalue.value, "/");
   
      if (I >= 0)
      {  numbarras = numbarras + 1;
      }
   
      I = InStr(argvalue.value.substring(I + 1, 10), "/");
   
      if (I >= 0)
      {  numbarras = numbarras + 1;
      }
	  
	  if (numbarras < qtbarras)
      {  if ((argvalue.value.length == 2 || argvalue.value.length == 5) && (numbarras < 2))
         {  argvalue.value = argvalue.value + "/";
	     }
	  }	 
   }	  
}

function PoePontos(argvalue)
{
   var I         = 0;
   var numpontos = 0;
   var keycode   = 0;

   keycode = window.event.keyCode; 
   
   if (keycode != 8 && keycode != 46)
   {  I = InStr(argvalue.value, ":");
   
      if (I >= 0)
      {  numpontos = numpontos + 1;
      }
   
      I = InStr(argvalue.value.substring(I + 1, 5), ":");
   
      if (I >= 0)
      {  numpontos = numpontos + 1;
      }
      
      if ((argvalue.value.length == 2) && (numpontos < 1))
      {  argvalue.value = argvalue.value + ":";
	  }
   }	  
}

//-- RETORNA O CASE DA TECLA PRESSIONADA (MAIÚSCULA OU MINÚSCULA)

function TrataCase(flcase) 
{
   if ((window.event.keyCode >= 65 && window.event.keyCode <= 90) || (window.event.keyCode >= 192 && window.event.keyCode <= 220))
   {  if (flcase == "L")
      {  window.event.keyCode = window.event.keyCode + 32;
	  }
   }
   else
   {  if ((window.event.keyCode >= 97 && window.event.keyCode <= 122) || (window.event.keyCode >= 224 && window.event.keyCode <= 252))
      {  if (flcase == "U")
         {  window.event.keyCode = window.event.keyCode - 32;
	     } 
      }
   }	  
}

// -- Procura um caracter dentro de uma string, devolvendo a primeira posicao encontrada ou ,
// -- zero, se não achou

function InStr(cadeia,busca)
{
   var posicao = -1;
   var n;
   var tamanho;

   tamanho = cadeia.length ;

   for (n=0; n < tamanho; n++)
   {  if (cadeia.substring(n, busca.length + n) == busca)
      {  posicao = n;
         n = tamanho + 1
	  }
   }
  
   return posicao;
}

function CritMail(cadeia)
{
   var wok = true;
   var pos = InStr(cadeia, "@");
   
   if (pos <= 0)
   {  wok = false;
   }
   else
   {  if ((pos + 1) == cadeia.length)
      {  wok = false;
      }
   }
   
   return wok;	  
}

function ReplPlic(cadeia)
{  
   var strret = "";
   
   strret = ReplStr(cadeia, "'", "");
   strret = ReplStr(strret, "´", "");

   return strret;
}

// -- Limpa espaços à esquerda e à direita

function Trim(expressao) 
{  
   var Valor=""  
   var Aceitar_Espaco=false 
   var Contador=0           
   var Contador2=0          
   var Tem_Letras=false     
    
   for(Contador=0; Contador<expressao.length; Contador++) 
   {  if(expressao.charAt(Contador)==" ") 
      {  if (Aceitar_Espaco) 
         {  // Verificando se existe mais algum caracter direferente de espaco até o final 
            // da string 
          
            for(Contador2=Contador+1; Contador2<expressao.length; Contador2++) 
            {  if(expressao.charAt(Contador2)!=" ") 
               {  Tem_Letras=true 
                  Contador2=expressao.length 
               } 
            } 
          
            if (Tem_Letras) 
            {  Valor=Valor + expressao.charAt(Contador) 
               Tem_Letras=false 
            } 
         } 
      } 
      else 
      {  Valor=Valor + expressao.charAt(Contador) 
         Aceitar_Espaco=true 
      } 
   } 

   return(Valor) 
}

//-->

