<?php  
//extract($_SESSION);

 include_once("../consultaCoface.php");

$msgMail =
"Caros Senhores,

Estamos enviando os links para a proposta e para a primeira parcela.

Pedimos que nos retornem devidamente assinada, via Correios ou Courier a
proposta em quest�o e que o pagamento da primeira parcela (ou parcela �nica)
de seu pr�mio seja efetuado em qualquer ag�ncia banc�ria de seu relacionamento
e que esteja autorizada a operar em c�mbio no Brasil.

Permanecemos � disposi��o no que for necess�rio

Fa�a o download do Acrobat Reader clicando no seguinte link:
http://www.adobe.com/products/acrobat/readstep.html

Fa�a o download da sua proposta clicando no seguinte link:
http://$SERVER_NAME".$root."download/$downProp

Fa�a o download da primeira parcela clicando no seguinte link:
http://$SERVER_NAME".$root."download/$downParc

Atenciosamente,

".$user->name."


Departamento de Opera��es de Curto Prazo
".$nomeEmpSBCE."
Homepage: ".$siteEmpSBCE."

Rio de Janeiro
Rua Senador Dantas, 74 - 16� andar
Centro - Rio de Janeiro - RJ - 20031.201
Telefone: (21) 2510.5000

S�o Paulo
P�a. Jo�o Duran Alonso, 34 - 12� andar
Brooklin Novo - SP - 04571-070
Tel.: (11) 5509 8181
Fax : (11) 5509 8182\n\n";

?>
