<?php
  $forward = "error";
  $query  = "SELECT rtrim(password)
             FROM Users 
             where id = $userID";

  $cur = odbc_exec($db, $query);

  if (odbc_fetch_row($cur)) {
    $pass = odbc_result($cur,1);
    $pwd = crypt($field->getField("senha"), SALT);

    if ($pass != $pwd) {
      $msg = "A senha atual est� errada!";
    } else if($p1 != $p2) {
      $msg = "1� Senha n�o � igual � 2�Senha";
    } else {
      $pwd = crypt($p1, SALT);
      $query = "UPDATE Users SET password = '$pwd' WHERE id = $userID";
      $r = odbc_exec($db,$query);
      if ($r) {
        $forward="success";
        $msg = "Senha alterada com sucesso";
      } else {
        $msg = "Problemas na atualiza��o da base";
      } 
    }
  } else {
    $msg = "Usu�rio inexistente";
  } 
?>
