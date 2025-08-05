<html>
<head>
<title>::Envio Autenticado::</title>
</head>

<?php
if(isset($_GET['status']) && $_GET['status'] == 'enviou') {
?>
<body>
   <DIV style="position:absolute; top:10px; left:200px;text-align:center;">
      <span style="font: bold 20px Arial; color: rgb(150,150,150);">Mensagem enviada com sucesso!</span><br/>
      <a href="index.php">Clique Aqui para Voltar!</a>
   </DIV>
</body>

<?php
}elseif(isset($_GET['status']) && $_GET['status'] == 'falhou'){
?>
<body>
   <DIV style="position:absolute; top:10px; left:200px;text-align:center;">
     <span style="font: bold 20px Arial; color: rgb(255,0,0);">Falha no envio!</span><br/>
     <a href="index.php">Clique Aqui para Voltar!</a>
   </DIV>
</body>
<?php
}
else{
?>
<body>
<center>
<form method="post" action="send.php">
   <h2>Envio de e-mail autenticado</h2>
   <span style="font: bold 12px Arial;">DESTINO: </span><input type="text" name="destino" size="45"></input></br>
   <input type="submit" value="Enviar Email AGORA!" />
</form>
</center>
</body>
<?php
}
?>
</html>
