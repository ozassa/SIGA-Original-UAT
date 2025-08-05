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
      $msg = "A senha atual está errada!";
    } else if($p1 != $p2) {
      $msg = "1ª Senha não é igual à 2ªSenha";
    } else {
      $pwd = crypt($p1, SALT);
      $query = "UPDATE Users SET password = '$pwd' WHERE id = $userID";
      $r = odbc_exec($db,$query);
      if ($r) {
        $forward="success";
        $msg = "Senha alterada com sucesso";
      } else {
        $msg = "Problemas na atualização da base";
      } 
    }
  } else {
    $msg = "Usuário inexistente";
  } 
?>
