<?php 

$query = "INSERT INTO Noticias (titulo, data, noticia, exibir) VALUES ('$titulo', '$data', '$news', $exibir)";
$news = odbc_exec($db,$query);

  if ($news == FALSE) {
    $msg = "campos preenchidos incorretamente";
    $forward = "error";
  } 
  else {
    $msg = "campos preenchidos sucesso";
    $forward = "success";
  }



?>







