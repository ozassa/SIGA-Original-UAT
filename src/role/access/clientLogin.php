<?php
  require_once("../../entity/user/User.php");
  /*
    Alterado por Tiago V N - Elumini - 24/08/2005
  */

  if ($comm == "clientLogin" )
  {
      $per ='C';
  }

  if ($_POST['Cli'] == '1') // cliente acessando de fora
      $u = new User($_POST['LOGIN'],$_POST['SENHA'], $per ,$db);
  else
      $u = new User($field->getField("login"),$field->getField("password"), $per ,$db);
	   
  $user = $u->getUserView();
  $_SESSION["user"] = $user;
  
  //print '?'.$_POST['LOGIN'].'?'.$_POST['SENHA'].'?'.$field->getField("login");

  if ($user->per=="C")
  {
  
     if ($user == NULL || !$role["client"] && !$role["bancoBB"] && !$role["bancoOB"] && !$role["bancoParc"])
     {
            $forward = "error";
     }
     else if($role["bancoBB"] || $role["bancoOB"] || $role["bancoParc"])
     {
         $banco = 1;
     }
  }
  else if ($user->per=="CO")
  {     //Tiago V N - 06/09/2006
      $per = "CO";
  }
  else //Adicionado por Michel Saddock 10/10/2006
  {
   $forward = "error";
  }

?>
