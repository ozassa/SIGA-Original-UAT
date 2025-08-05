<?php

   $is_https = "//";
   //$is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
   
   $req =   $_SERVER['REQUEST_URI'];
   if (strpos($req,'coface-siga') !== false) {
    $host_v =  $is_https.$_SERVER['HTTP_HOST'].'/coface-siga/'; // endere?os da localiza??o das p?ginas na Web
   } else {
    if (strpos($_SERVER['HTTP_HOST'],'siga.coface') !== false) {
      $host_v =  $is_https.$_SERVER['HTTP_HOST'].'/'; // endere?os da localiza??o das p?ginas na Web
    } else {
      $host_v =  $is_https.$_SERVER['HTTP_HOST'].'/siga/'; // endere?os da localiza??o das p?ginas na Web
    }
   }

	require_once ("src/dbOpen.php");

	$qry = "SELECT i_Versao, n_Versao, d_Inicio_Atualizacao, d_Fim_Atualizacao, Mensagem_Atualizacao, Descricao_Atualizacao
						FROM Versao_Sistema 
						WHERE d_Fim_Atualizacao IS NULL";
	$cur = odbc_exec($db,$qry);

	$Mensagem_Atualizacao = "";
	while (odbc_fetch_row($cur)) {
		$Mensagem_Atualizacao = odbc_result ($cur, 'Mensagem_Atualizacao');
	} 

	if ($Mensagem_Atualizacao) { 
		$_SESSION['Mensagem_Atualizacao'] = $Mensagem_Atualizacao; ?>
		<script>window.location = '<?php echo $host_v; ?>/src/role/versao_sistema/index.php';</script>
		<?php
	}
?>