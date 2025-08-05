<?php
  require_once("../../entity/user/User.php");

  if ($comm == "functionaryLogin" ) {
     $per = 'F';
  }

  //$u = new User($_POST["login"],$_POST["password"], $per ,$db);
  $u = new User($field->getField("login"),$field->getField("password"), $per ,$db);
  $user = $u->getUserView();
  //print '?'.$user;
  
  $_SESSION["user"] = $user;
  
  
  $_SESSION['Login'] = $field->getField("login");
  $_SESSION['pw']    = $field->getField("password");
    
  if ($user == NULL)
    $forward = "error";
		
?>
