<?php

$sql = "SELECT 
					SP.i_Sub_Produto As i_Sub_Produto, 
					P.Nome + ' - ' + SP.Descricao As Descricao
				From
					Produto P 
				Inner Join Sub_Produto SP On
					SP.i_Produto = P.i_Produto
				Where 
					SP.Situacao = 0 
				Order By 
					SP.i_Produto,
					SP.i_Sub_Produto";

$cur = odbc_exec($db, $sql);

$dados_sel = array();

while(odbc_fetch_row($cur)) {
	$id_prod = odbc_result($cur, "i_Sub_Produto");
	$cod_prod = odbc_result($cur, "Descricao");

	$dados_sel[] = array(
		"id_prod" => $id_prod,
		"cod_prod" => $cod_prod,
	);
}

odbc_close($db);



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

$title = "Relacionamento com Subprodutos";
$content = "../module/interf/ViewRelacModule.php";