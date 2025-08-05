<?php 

$news = odbc_exec(
  $db,
    " UPDATE Noticias".
    "  SET titulo      = '".$titulo."',".  
    "      data        = '".$data."',".
    "      noticia     = '".$news."',".
    "      exibir      = ".$exibir.
    "  WHERE id =".$id
  );

  if ($news == FALSE) {
    $msg = "campos preenchidos incorretamente";
    $forward = "error";
  } 
  else {
    $msg = "campos preenchidos sucesso";
    $forward = "success";
  }



?>







