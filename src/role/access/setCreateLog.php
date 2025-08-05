<?php
// Alterado Hicom (Gustavo) - 19/01/05 - login não precisa ser um e-mail
/*
    Alterado - Elumini(Tiago V N) - 17/08/2005
    Validar o campo email
    validar o campo password sendo ele diferente de vazio,menor que 3 e
    maior que oito.
    Validar o campo password não deixando ser diferente da password de
    confirmação.
*/

    if (empty($login) ) {
       $msg = "E-Mail não pode ser vazio.";
       $forward="error";
    }

    //Alterado por Michel Saddock  29/09/2006
    //else if (!eregi ("^[0-9a-z.-/\-\_]{0,255}\@([a-z0-9\-\_]+\.){1,15}([a-z]{2,3})(\.[a-b]{2})?$", $login)) {
    else if (!eregi ("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", $login)) {
       $msg = "E-Mail Incorreto.";
       $forward="error";
    }else if ( empty($password1) ) {
       $msg = "Senha não pode ser vazio.";
       $forward="error";
    }
    else if ( strlen($password1) < '3'  Or strlen($password1) > '8'){
       $msg = "Senha não pode ser Menor que 3 ou Maior que 8.";
       $forward="error";
    }else if ( $password1 == $login ) {
       $msg = "Senha não pode ser igual a login.";
       $forward="error";
    }else if ( $password2 <> $password1 ) {
       $msg = "Senha está direfente de Senha de confirmação.";
       $forward="error";
    }else{
    /*
      Alerado por Tiago V N - 23/08/2005 - ( Elumini ) - Cadastro State e perfil
   */
    $cur = odbc_exec(
      $db,
      "SELECT id FROM Users WHERE login='".$field->getField("login")."' And perfil = 'C'"
    );
    if (odbc_fetch_row($cur)) {
      $msg = "Este login já consta em nossa base";
      $forward="error";
    } else {
      odbc_autocommit ($db, FALSE);

      $key = session_id().time();
      $pwd = crypt($field->getField("password1"), SALT);

      odbc_exec($db,"INSERT INTO Users (cookie, name, login, password, state, perfil) VALUES ('$key','','".$field->getField("login")."','".$pwd."','0', 'C')"
      );

      $cur = odbc_exec(
        $db,
        "SELECT id FROM Users WHERE cookie = '$key'"
      );

      odbc_fetch_row($cur);

      $idUser = odbc_result($cur,1);

      // associa o papel cliente a este usuário

      $r = odbc_do(
        $db,
        "INSERT INTO UserRole (idUser, idRole) VALUES (".$idUser.",1)"
      );

      if ($r != FALSE)
        odbc_commit($db);
      else
        odbc_rollback($db);
      
      odbc_autocommit ($db, TRUE);

      $congratulation = true;

      if ( $comm == "setCreateLog" ) {
         $per = 'C';
      }

      // efetua o login do usuário
      require_once("../../entity/user/User.php");

      $pwd = crypt($field->getField("password1"), SALT);

      $u = new User($field->getField("login"),$pwd, $per, $db);
      $user = $u->getUserView();
      $_SESSION["user"] = $user;

      if ($user == NULL || !$role["client"]) {
	     $msg = "Não consegui efetuar o login do usuário";
         $forward = "error";
      }
    }
 }
?>
