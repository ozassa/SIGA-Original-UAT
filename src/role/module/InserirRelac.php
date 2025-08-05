<?php

$id_mod = $_POST["sub_prods"];
$mods = $_POST["mods"];
$sub_prods = $_POST["sub_prods"];

// Prepara a consulta para deletar o registro
$sqlDel = "DELETE FROM Sub_Produto_Modulo WHERE i_Sub_Produto = ?";
$stmtDel = odbc_prepare($db, $sqlDel);
odbc_execute($stmtDel, [$id_mod]);

// Insere os novos registros de forma segura
for ($i = 0; $i < count($mods); $i++) {
    $sqlUp = "INSERT INTO Sub_Produto_Modulo (i_Modulo, i_Sub_Produto) VALUES (?, ?)";
    $stmtUp = odbc_prepare($db, $sqlUp);
    odbc_execute($stmtUp, [$mods[$i], $sub_prods]);
}

odbc_close($db);

$location = "Location: ".$host."src/role/module/ModuleSystem.php?comm=relacionamento&sub_prod=".$_POST["sub_prods"];
header($location);
