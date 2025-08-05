<?php session_start(); ?>

<html>
  <head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
  </head>
  <body style="background-color:#00518a;">
    <div style="left: 50%; position: absolute; background-image: url(../../../images/mensagem_versao_siga/siga_5.png); background-repeat: no-repeat; top: 45%; margin-left: -300px; margin-top: -275px; height: 685px; width: 600px;"></div>
    <div style="position: absolute; left: 43%; top: 38%; margin-left: -110px; margin-top: -40px; font-family: arial; color: #FFF; text-align: center; width: 480px;">
    	<?php echo isset($_SESSION['Mensagem_Atualizacao']) ? utf8_encode($_SESSION['Mensagem_Atualizacao']) : ''; ?>
    </div>
  </body>
</html>