<?php
  include_once ("enviamail/smtp.class.php");

  $host_send = "smtp.cofacedobrasil.com"; /*host do servidor SMTP */
  $mail_send = "siex@cofacedobrasil.com";//o endereço de e-mail deve ser válido.
  $senha_send = "coface1501";
  /* Configuração da classe.e smtp.class.php */
  $smtp = new Smtp($host_send, 587);
  //$smtp = new Smtp($host, 25);
  $smtp->user = $mail_send; /*usuario do servidor SMTP */
  $smtp->pass = $senha_send; /* senha do usuario do servidor SMTP*/
  $smtp->debug = true; /*ativa a autenticacao SMTP */

 
?>
