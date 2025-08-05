<?php /*
$ano = isset($_GET['ano']) ? $_GET['ano'] : false;
if($ano){
  $prox = $ano + 1;
  $c = odbc_exec($db, "select distinct idUser, u.name from Inform join Users u on u.id=Inform.idUser where startValidity >= '$ano-01-01' and startValidity < '$prox-01-01' order by u.name");
  while(odbc_fetch_row($c)){
    $idUser = odbc_result($c, 1);
    $name = odbc_result($c, 2);

    // apolices novas
    $r = odbc_exec($db, "select count(id) from Inform where startValidity >= '$ano-01-01' and startValidity < '$prox-01-01' and idUser=$idUser and (idAnt = 0 or idAnt is null)");
    if(odbc_fetch_row($r)){
      $v[$name][0] = odbc_result($r, 1);
    }else{
      $v[$name][0] = 0;
    }

    // apolices renovadas
    $r = odbc_exec($db, "select count(id) from Inform where startValidity >= '$ano-01-01' and startValidity < '$prox-01-01' and idUser=$idUser and idAnt > 0");
    if(odbc_fetch_row($r)){
      $v[$name][1] = odbc_result($r, 1);
    }else{
      $v[$name][1] = 0;
    }
  }
} */

$ano = isset($_GET['ano']) ? $_GET['ano'] : false;

if ($ano) {
    $prox = $ano + 1;

    // Query para obter os usuários com validade no ano especificado
    $query = "SELECT DISTINCT idUser, u.name 
              FROM Inform 
              JOIN Users u ON u.id = Inform.idUser 
              WHERE startValidity >= ? AND startValidity < ? 
              ORDER BY u.name";
    
    $stmt = odbc_prepare($db, $query);
    odbc_execute($stmt, ["$ano-01-01", "$prox-01-01"]);

    while (odbc_fetch_row($stmt)) {
        $idUser = odbc_result($stmt, 1);
        $name = odbc_result($stmt, 2);

        // Inicializar os contadores
        $v[$name] = [0, 0];

        // Apólices novas
        $queryNew = "SELECT COUNT(id) 
                     FROM Inform 
                     WHERE startValidity >= ? 
                     AND startValidity < ? 
                     AND idUser = ? 
                     AND (idAnt = 0 OR idAnt IS NULL)";
        
        $stmtNew = odbc_prepare($db, $queryNew);
        odbc_execute($stmtNew, ["$ano-01-01", "$prox-01-01", $idUser]);

        if (odbc_fetch_row($stmtNew)) {
            $v[$name][0] = odbc_result($stmtNew, 1);
        }

        // Apólices renovadas
        $queryRenew = "SELECT COUNT(id) 
                       FROM Inform 
                       WHERE startValidity >= ? 
                       AND startValidity < ? 
                       AND idUser = ? 
                       AND idAnt > 0";
        
        $stmtRenew = odbc_prepare($db, $queryRenew);
        odbc_execute($stmtRenew, ["$ano-01-01", "$prox-01-01", $idUser]);

        if (odbc_fetch_row($stmtRenew)) {
            $v[$name][1] = odbc_result($stmtRenew, 1);
        }
    }
    
    // Liberar conexões para evitar problemas de ODBC ocupada
    odbc_free_result($stmt);
    odbc_free_result($stmtNew);
    odbc_free_result($stmtRenew);
}
?>


