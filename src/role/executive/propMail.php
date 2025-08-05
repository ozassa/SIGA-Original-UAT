<?php  
//extract($_SESSION);

 include_once("../consultaCoface.php");

$msgMail =
"Caros Senhores,

Estamos enviando os links para a proposta e para a primeira parcela.

Pedimos que nos retornem devidamente assinada, via Correios ou Courier a
proposta em questão e que o pagamento da primeira parcela (ou parcela única)
de seu prêmio seja efetuado em qualquer agência bancária de seu relacionamento
e que esteja autorizada a operar em câmbio no Brasil.

Permanecemos à disposição no que for necessário

Faça o download do Acrobat Reader clicando no seguinte link:
http://www.adobe.com/products/acrobat/readstep.html

Faça o download da sua proposta clicando no seguinte link:
http://$SERVER_NAME".$root."download/$downProp

Faça o download da primeira parcela clicando no seguinte link:
http://$SERVER_NAME".$root."download/$downParc

Atenciosamente,

".$user->name."


Departamento de Operações de Curto Prazo
".$nomeEmpSBCE."
Homepage: ".$siteEmpSBCE."

Rio de Janeiro
Rua Senador Dantas, 74 - 16º andar
Centro - Rio de Janeiro - RJ - 20031.201
Telefone: (21) 2510.5000

São Paulo
Pça. João Duran Alonso, 34 - 12º andar
Brooklin Novo - SP - 04571-070
Tel.: (11) 5509 8181
Fax : (11) 5509 8182\n\n";

?>
