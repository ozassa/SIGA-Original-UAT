<?php

$forward = "error";
$original = odbc_result(odbc_exec($db, "select login from Users where id=$userID"), 1);

$x = odbc_exec($db, "select * from Users where login = '$login' and login <> '$original'");
if(odbc_fetch_row($x)){
  $msg = "O login '$login' já existe";
}else{
  $query = "SELECT password
            FROM Users 
            WHERE id=$userID";
  $cur = odbc_exec($db, $query);

  if (odbc_fetch_row($cur)) {
    $s = odbc_result($cur,1);
    if ($s != $field->getField("senha")) {
      $msg = "A senha atual está errada!";
    } else if($p1 != $p2) {
      $msg = "1ª Senha não é igual à 2ª Senha";
    } else {
      $pwd = crypt($p1, SALT);
      
      $query = "UPDATE Users
                SET password = '$pwd', login = '$login'
                WHERE id = $userID";
      $r = odbc_exec($db,$query);
      if ($r) {
	$forward="success";
	$msg = "Senha e login alterados com sucesso";
      } else {
	$msg = "Problemas na atualização da base";
      } 
    }
  }else{
    $msg = "Usuário inexistente";
  }
}
?>
