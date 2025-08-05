<?php 
	require_once ("../rolePrefix.php");


	$qry = "SELECT a.id,a.name,c.login 
						FROM Role a
							INNER JOIN UserRole b ON b.idRole = a.id
							INNER JOIN Users c ON c.id = b.idUser
						WHERE c.id = '".$_SESSION['userID']."' AND c.perfil = '".$_SESSION['pefil']."'
						ORDER BY a.name, c.login";

	$cur=odbc_exec($db,$qry);
  $x = 0;
  while (odbc_fetch_row($cur)) {	 
		$x = $x + 1;
		$name  = odbc_result ($cur, 'name');
		$id    = odbc_result ($cur, 'id');
		$role[$name] = $id.'<br>';
  }

	if (!$role["policy"]) {
		$content = ("naoAutorizado.php");
		require_once("../../../home.php");
		$bloqueia = "sim";
		exit(1);
	} else {
		
	}

	$bloqueia = isset($bloqueia) ? $bloqueia : false;

 	if ($bloqueia == "" ){
		$tipoclient = $field->getField ("tipoclient");
		$idclient = $field->getField ("idclient");

		if ($comm == "listlogin") {
			$tipoclient = $field->getField ("tipoclient");
			$idclient = $field->getField ("idclient");

			$title = "Relaчуo Login e Senha";
			$content = "../searchClient/viewbc.php";
		} elseif ($comm == "alteraLogin") {
			$tipoclient  = $tipoclient;
			$idclient    = $idclient;

			require_once('alteraLogin.php');

			$title = "Relaчуo Senha Bancos/Clientes";
			$content = "../searchClient/listbc.php";
		} else {
			$title = "Relaчуo Senha Bancos/Clientes";	 
			$content = "../searchClient/listbc.php";
		}
	}

	require_once("../../../home.php");
?>