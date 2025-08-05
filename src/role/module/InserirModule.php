<?php

if (isset($_POST["id_mod"])) {
    $id_mod = $_POST["id_mod"];
    $codigoModulo = $_POST["codigoModulo"];
    $grupoModulo = $_POST["grupoModulo"];
    $tituloModulo = $_POST["tituloModulo"];
    $desc_mod = $_POST["desc_mod"];
    $situacaoModulo = $_POST["situacaoModulo"];
    $ordemModulo = $_POST["ordemModulo"];

    $sqlUp = "UPDATE Modulo SET
                Cod_Modulo = ?, 
                Grupo_Modulo = ?, 
                Titulo_Modulo = ?, 
                Texto_Modulo = ?, 
                s_Modulo = ?, 
                Ordem_Modulo = ? 
              WHERE i_Modulo = ?";

    $stmt = odbc_prepare($db, $sqlUp);
    odbc_execute($stmt, [$codigoModulo, $grupoModulo, $tituloModulo, $desc_mod, $situacaoModulo, $ordemModulo, $id_mod]);

    odbc_close($db);
} else {
    $codigoModulo = $_POST["codigoModulo"];
    $grupoModulo = $_POST["grupoModulo"];
    $tituloModulo = $_POST["tituloModulo"];
    $desc_mod = $_POST["desc_mod"];
    $situacaoModulo = $_POST["situacaoModulo"];
    $ordemModulo = $_POST["ordemModulo"];

    $sqlInto = "INSERT INTO Modulo (
                  Cod_Modulo, 
                  Grupo_Modulo, 
                  Titulo_Modulo, 
                  Texto_Modulo, 
                  s_Modulo, 
                  Ordem_Modulo
              ) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = odbc_prepare($db, $sqlInto);
    odbc_execute($stmt, [$codigoModulo, $grupoModulo, $tituloModulo, $desc_mod, $situacaoModulo, $ordemModulo]);

    odbc_close($db);
}

$location = "Location: " . $host . "src/role/module/ModuleSystem.php";
header($location);
