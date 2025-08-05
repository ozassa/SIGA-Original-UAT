<?php

$sql = "SELECT i_Modulo, Cod_Modulo, Grupo_Modulo, Titulo_Modulo, ISNULL(Texto_Modulo, '') Texto_Modulo, Ordem_Modulo, s_Modulo
				FROM Modulo
				ORDER BY Ordem_Modulo";

$cur = odbc_exec($db, $sql);

$dados_sel = array();

while(odbc_fetch_row($cur)) {
	$id_mod = odbc_result($cur, "i_Modulo");
	$cod_mod = odbc_result($cur, "Cod_Modulo");
	$grupo_mod = odbc_result($cur, "Grupo_Modulo");
	$titulo_mod = odbc_result($cur, "Titulo_Modulo");
	$txt_mod = odbc_result($cur, "Texto_Modulo");
	$ordem_mod = odbc_result($cur, "Ordem_Modulo");
	$sit_mod = odbc_result($cur, "s_Modulo");

	$dados_sel[] = array(
		"id_mod" => $id_mod,
		"cod_mod" => $cod_mod,
		"grupo_mod" => $grupo_mod,
		"titulo_mod" => $titulo_mod,
		"txt_mod" => $txt_mod,
		"ordem_mod" => $ordem_mod
	);
}

if(isset($_POST["id_modulo"])){
	$sqlInto = "INSERT INTO Modulo (
														Cod_Modulo, 
														Grupo_Modulo, 
														Titulo_Modulo, 
														Texto_Modulo, 
														s_Modulo, 
														Ordem_Modulo)
											VALUES (
														".$_POST['codigoModulo'].", 
														".$_POST['grupoModulo'].", 
														".$_POST['tituloModulo'].", 
														".$_POST['descricaoModulo'].", 
														".$_POST['situacaoModulo'].", 
														".$_POST['ordemModulo'].")";

	$sqlUp = "UPDATE Modulo SET
							Cod_Modulo = ".$_POST['codigoModulo'].",
							Grupo_Modulo = ".$_POST['grupoModulo'].",
							Titulo_Modulo = ".$_POST['tituloModulo'].",
							Texto_Modulo = ".$_POST['descricaoModulo'].",
							s_Modulo = ".$_POST['situacaoModulo'].",
							Ordem_Modulo = ".$_POST['ordemModulo']."
						WHERE	i_Modulo = #IDModulo";
}

$title = "Ordenar de M&oacute;dulos";
$content = "../module/interf/ViewOrderModule.php";