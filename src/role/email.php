<?php
/*
if (mail($field->getField("login"),
	"Sua Senha SBCE",
	"Prezado cliente, \n\n Esta é a sua senha de acesso : ".odbc_result($cur, 1). "\n\n")){
	$msg = "E-mail enviado com sucesso";
}else{
	$msg = "Problemas no envio do e-mail";
	$forward = "error";
}
//    mail ("eduardo@tendencies.com.br", "Sua Senha SBCE", "Prezado cliente,  esta é a sua senha de acesso : 123");
} else {
    $forward = "error";
     //  $msg = "Este login não consta em nossos cadastros";
    $msg = "Este e-mail não consta em nossos cadastros";
}
*/

$headers = '';
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From: atendimento@sbce.com.br\r\n";
        
//smoreira@braspack.com.br
//	mail("tiagovillanova@yahoo.com.br", "teste", "Teste de E-mail, por favor responder este e-mail para confirmar o recebimento. Muito Obrigado", $headers);
        
?>