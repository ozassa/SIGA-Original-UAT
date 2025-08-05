<?php 
	
	require_once("getBrowser.php");

	$navegador = getBrowser();

	$t_Processo = '100';
	$i_Processo = isset($id_Parametro) ? $id_Parametro : false;
	$i_Usuario = isset($_SESSION['userID']) ? $_SESSION['userID'] : false;
	$d_Log = date('Y-m-d H:i:s');
	$t_Navegador = $navegador['name']." ".$navegador['version'];
	$SSL_CLIENT_M_SERIAL = isset($_SERVER['SSL_CLIENT_M_SERIAL']) ? $_SERVER['SSL_CLIENT_M_SERIAL'] : 'NULL';
	$SSL_CLIENT_V_START = isset($_SERVER['SSL_CLIENT_V_START']) ? gmdate('Y-m-d H:i:s', strtotime($_SERVER['SSL_CLIENT_V_START'])) : 'NULL';
	$SSL_CLIENT_V_END = isset($_SERVER['SSL_CLIENT_V_END']) ? gmdate('Y-m-d H:i:s', strtotime($_SERVER['SSL_CLIENT_V_END'])) : 'NULL';
	$SSL_CLIENT_S_DN = isset($_SERVER["SSL_CLIENT_S_DN"]) ? $_SERVER["SSL_CLIENT_S_DN"] : 'NULL';

	$sql = "EXEC SPR_BB_LOG_CERTIFICADO_DIGITAL '".$t_Processo."', '".$i_Processo."', '".$i_Usuario."', '".$d_Log."', '".$t_Navegador."', '".$SSL_CLIENT_M_SERIAL."', '".$SSL_CLIENT_V_START."', '".$SSL_CLIENT_V_END."', '".$SSL_CLIENT_S_DN."'";
	// $rsSql = odbc_exec($db, $sql);
	
?>