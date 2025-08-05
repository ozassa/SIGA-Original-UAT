<?php
if(!isset($_SESSION)){
	session_set_cookie_params([
    'secure' => true,
    'httponly' => true
]);
session_start();
}



	$userID  = $_SESSION['userID']; 
	// Alterado Hicom (Gustavo) - 19/01/05 - possibilidade do usuário alterar o login separado da senha
	$forward = "error";
	$original = odbc_result(odbc_exec($db, "select login from Users where id=$userID"), 1);
	$senha_original = odbc_result(odbc_exec($db, "select password from Users where id=$userID"), 1);

	$opc     =  $_POST['opc'];  
	$login   =  $_POST['login'];

	if($opc == 'alteraSenhaLogin'){
		$password      =  $_POST['senha']; 
	} else {
		$p1      =  $_POST['p1']; 
		$p2      =  $_POST['p1'];
	}


	

		if ($opc == "login") {
			/*
			  Alterado por Tiago V N - ( Elumini ) 24/08/05 - Checagem de e-mail.
			*/
			if ( empty($login) ) {
				 $msg = "O campo E-mail não pode ser vazio.";
				 $forward = "error";
			} else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/", $login)) {
				 $msg = "E-mail incorreto.";
				 $forward = "error";
			}else{
			  $x = odbc_exec($db, "select * from Users where login = '$login' and login <> '$original'");
			  if(odbc_fetch_row($x)){
			  $msg = "O login '$login' já existe";
			}else{
			  $query = "SELECT rtrim(password)
						FROM Users 
						WHERE id=$userID";
			  $cur = odbc_exec($db, $query);
			
			  if (odbc_fetch_row($cur)) {
				$s = odbc_result($cur,1);
				$pwd = crypt($field->getField("senha"), SALT);
				if ($s != $pwd) {
				  $msg = "A senha atual está errada!";
				} else {
				  $query = "UPDATE Users
							SET login = '$login'
							WHERE id = $userID";
				  $r = odbc_exec($db,$query);
				  if ($r) {
				$forward="success";
				$msg = "Login alterado com sucesso";
				  } else {
				$msg = "Problemas na atualização da base";
				  } 
				}
			  }else{
				$msg = "Usuário inexistente";
			  }
			}
		 }//Fecha if de verificacao de e-mail
		}

		elseif ($opc == 'alteraSenhaLogin'){

		} else
		   {
			//echo $p1;
			//echo "<br>Orig: ".$senha_original ;
			//break;
			if ( empty($p1) )
			{
			   $msg = "A Senha nova não pode ser vazia !!!";
			   $forward="error";
			}
			else if ( $p1 == $senha_original ){
			   $msg = "Sua nova senha não pode ser iqual a antiga!";
			   $forward="error";
			}
			else if ( strlen($p1) < '8'){
			   $msg = "A Senha nova não pode ser Menor que 8 caracteres";
			   $forward="error";
			}
			else if ( strlen($p1) > '15'){
			   $msg = "Sua nova senha não pode ser Maior que 15  caracteres";
			   $forward="error";
			}
			//Adicionado por Michel Saddock 09/10/2006
			else if (!preg_match("/[a-zA-Z0-9]/" , $p1))
			{
			  $msg = "A senha deve conter somente letras e números!";
			  $forward="error";
			}
			else if (!preg_match("/[a-zA-Z]/" , $p1))
			{
			  $msg = "A senha deve conter obrigatoriamente letras e números!";
			  $forward="error";
			}
			else if (!preg_match("/[0-9]/" , $p1))
			{
			  $msg = "A senha deve conter obrigatoriamente letras e números!";
			  $forward="error";
			}
			//Fim Adicionado por Michel Saddock 09/10/2006
			else if ( $p2 <> $p1 )
			{
			   $msg = "Senha nova está direfente da senha de confirmação";
			   $forward="error";
			}
			else{
			
		  $query = "SELECT rtrim(password)
					FROM Users 
					WHERE id=$userID";
		  $cur = odbc_exec($db, $query);
		
		  if (odbc_fetch_row($cur))
		  {
			$s = odbc_result($cur,1);
			$pwd = crypt($field->getField("senha"), SALT);

			if ($s != $pwd)
			{
			  $msg = "A senha atual está errada!";
			}
			else if($p1 != $p2)
			{
			  $msg = "1ª Senha não é igual à 2ª Senha";
			}
			else
			{
			   //var soma recebe data atual + 6 meses
			   $soma = somadatax(date('d-m-Y'), 180);
				 $pwd = crypt($p1, SALT);
				 
			   $query = "UPDATE Users SET password = '$pwd', alterSenha = '$soma' WHERE id = $userID";
			   $r = odbc_exec($db,$query);
		
			  if ($r)
			  {
				$forward="success";
				$msg = "Senha alterada com sucesso";
			  }
			  else
			  {
				$msg = "Problemas na atualização da base";
			  } 
		   }
		  }
		  else
		  {
			$msg = "Usuário inexistente";
		  }
		 }
		}

  

 function somadatax($data,$nDias){

   if(!isset($nDias)){
	   $nDias = 1;
   }
   
   $aVet = explode("-",$data);
   return date("Y-m-d",mktime(0,0,0,$aVet[1],$aVet[0]+$nDias,$aVet[2]));
 }

?>
