<?php
	
	
	$idInform = isset($idInform) ? $idInform : false;
	$id_Parametro = isset($id_Parametro) ? $id_Parametro : false;

  // Verifica se existe a obrigatoriedade do uso do certificado digital
  $sqlCert = "SELECT IsNull(IP.n_Parametro, PE.n_Parametro) AS Certificado_Obrigatorio 
                FROM Inform Inf 
                  LEFT JOIN Inform_Parametro IP ON IP.i_Inform = Inf.id 
                  RIGHT JOIN Parametro_Empresa PE ON PE.i_Parametro = IsNull(IP.i_Parametro, '".$id_Parametro."') AND PE.i_Empresa = Inf.i_Empresa
                WHERE Inf.id = '".$idInform."' AND PE.i_Parametro = '".$id_Parametro."'";

  $rsSqlCert = odbc_exec($db, $sqlCert);

  $Certificado_Obrigatorio = odbc_result($rsSqlCert, "Certificado_Obrigatorio");

  $perm_cert = true;

  /*
	$perm_cert = false;
  if ($idInform && $id_Parametro) {
	  if ($Certificado_Obrigatorio != 1) {
	    //if (isset($_SERVER['SSL_CLIENT_VERIFY'])) {
	    	//if ($_SERVER['SSL_CLIENT_VERIFY'] == 'SUCCESS') {
		      $perm_cert = true;
	    	//}
	    //}
	  } //else {
	    //$perm_cert = true;
	  //}
	}
  */

?>