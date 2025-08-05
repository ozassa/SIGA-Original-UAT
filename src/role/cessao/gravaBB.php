<?php



$sub = isset($_REQUEST['sub']) ? $_REQUEST['sub'] : false;
$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
$agencia = isset($_REQUEST['agencia']) ? $_REQUEST['agencia'] : false;
$idAgencia = isset($_REQUEST['idAgencia']) ? $_REQUEST['idAgencia'] : false;
$bancoName = isset($_REQUEST['bancoName']) ? $_REQUEST['bancoName'] : false;
$idBanco = isset($_REQUEST['idBanco']) ? $_REQUEST['idBanco'] : false;
$tipoBanco = isset($_REQUEST['tipoBanco']) ? $_REQUEST['tipoBanco'] : false;
$codBanco = isset($_REQUEST['codBanc']) ? $_REQUEST['codBanc'] : false;
$idImporter = isset($_REQUEST['idImporter']) ? $_REQUEST['idImporter'] : false;
$contR = isset($_REQUEST['contR']) ? $_REQUEST['contR'] : false;
$idCDBB = isset($_REQUEST['idCDBB']) ? $_REQUEST['idCDBB'] : false;
$idCDParc = isset($_REQUEST['idCDParc']) ? $_REQUEST['idCDParc'] : false;
$idCDOB = isset($_REQUEST['idCDOB']) ? $_REQUEST['idCDOB'] : false;
$total = isset($_REQUEST['total']) ? $_REQUEST['total'] : false;
$totalR = isset($_REQUEST['totalR']) ? $_REQUEST['totalR'] : false;
$agencia = isset($_REQUEST['agencia']) ? $_REQUEST['agencia'] : false;
$agNome = isset($_REQUEST['agNome']) ? $_REQUEST['agNome'] : false;
$agEnd = isset($_REQUEST['agEnd']) ? $_REQUEST['agEnd'] : false;
$agCid = isset($_REQUEST['agCid']) ? $_REQUEST['agCid'] : false;
$idRegion = isset($_REQUEST['idRegion']) ? $_REQUEST['idRegion'] : false;
$agCNPJ = isset($_REQUEST['agCNPJ']) ? $_REQUEST['agCNPJ'] : false;
$agIE = isset($_REQUEST['agIE']) ? $_REQUEST['agIE'] : false;



if ($tipoBanco == 3) {
    if ($sub == 'Incluir') {
        $stmt = odbc_prepare($db, "SELECT idImporter FROM CDOBDetails WHERE idImporter = ? AND idCDOB = ?");
        odbc_execute($stmt, [$idImporter, $idCDOB]);

        // Verificar se há resultados armazenando em uma variável
        $existe = odbc_fetch_row($stmt);

        // Liberar o ODBC após a consulta
        odbc_free_result($stmt);

        if (!$existe) {
            $stmt = odbc_prepare($db, "INSERT INTO CDOBDetails (idImporter, idCDOB) VALUES (?, ?)");
            odbc_execute($stmt, [$idImporter, $idCDOB]);

            // Liberar após a execução do INSERT
            odbc_free_result($stmt);

            $content = "../cessao/interf/selImp.php";
        } else {
            echo '<script>verErro("Este Importador já faz parte da Cessão");</script>';
        }
    } else if ($sub == 'Excluir') {
        $stmt = odbc_prepare($db, "DELETE FROM CDOBDetails WHERE idImporter = ? AND idCDOB = ?");
        odbc_execute($stmt, [$idImporter, $idCDOB]);
        $content = "../cessao/interf/selImp.php";
    } else if ($sub == 'Remover Todos') {
        $stmt = odbc_prepare($db, "DELETE FROM CDOBDetails WHERE idCDOB = ?");
        odbc_execute($stmt, [$idCDOB]);
        $content = "../cessao/interf/selImp.php";
    } else if ($sub == 'Incluir Todos') {
        $query = "
            SELECT imp.id
            FROM Importer imp
            WHERE imp.idInform = ?
              AND imp.state NOT IN (1, 3, 7, 8, 9)
              AND imp.credit > 0
              AND imp.id NOT IN (
                SELECT cdd.idImporter
                FROM CDBBDetails cdd
                  JOIN CDBB cd ON (cdd.idCDBB = cd.id)
                WHERE cd.status <> 3
                UNION
                SELECT cdod.idImporter
                FROM CDOBDetails cdod
                  JOIN CDOB cdo ON (cdo.id = cdod.idCDOB)
                WHERE cdo.status <> 3
              )
            ORDER BY imp.name";

        $stmt = odbc_prepare($db, $query);
        odbc_execute($stmt, [$idInform]);

        while (odbc_fetch_row($stmt)) {
            $idImporter = odbc_result($stmt, 1);
            $insertStmt = odbc_prepare($db, "INSERT INTO CDOBDetails (idImporter, idCDOB) VALUES (?, ?)");
            odbc_execute($insertStmt, [$idImporter, $idCDOB]);
        }

        odbc_free_result($stmt);

        $content = "../cessao/interf/selImp.php";
    } else {
        $stmt = odbc_prepare($db, "SELECT * FROM CDOBDetails WHERE idCDOB = ?");
        odbc_execute($stmt, [$idCDOB]);

        if (odbc_fetch_row($stmt)) {
            $content = "../cessao/interf/imprBB.php";
        } else {
            echo '<script>verErro("Nenhum Importador foi Selecionado");</script>';
        }

        odbc_free_result($stmt);

    }

    odbc_free_result($stmt);
} else if ($tipoBanco == 1) {
    if ($sub == 'Incluir') {
        $stmt = odbc_prepare($db, "SELECT idImporter FROM CDBBDetails WHERE idImporter = ? AND idCDBB = ?");
        odbc_execute($stmt, [$idImporter, $idCDBB]);

        if (!odbc_fetch_row($stmt)) {
            $stmt = odbc_prepare($db, "INSERT INTO CDBBDetails (idImporter, idCDBB) VALUES (?, ?)");
            odbc_execute($stmt, [$idImporter, $idCDBB]);

            $content = "../cessao/interf/selImp.php";
        } else {
            echo '<script>verErro("Este Importador já faz parte da Cessão");</script>';
        }

        odbc_free_result($stmt);

    } else if ($sub == 'Excluir') {
        $stmt = odbc_prepare($db, "DELETE FROM CDBBDetails WHERE idImporter = ? AND idCDBB = ?");
        odbc_execute($stmt, [$idImporter, $idCDBB]);
        odbc_free_result($stmt);

        $content = "../cessao/interf/selImp.php";
    } else if ($sub == 'Remover Todos') {
        $stmt = odbc_prepare($db, "DELETE FROM CDBBDetails WHERE idCDBB = ?");
        odbc_execute($stmt, [$idCDBB]);
        odbc_free_result($stmt);

        $content = "../cessao/interf/selImp.php";
    } else if ($sub == 'Incluir Todos') {
        $query = "
            SELECT imp.id
            FROM Importer imp
            WHERE imp.idInform = ?
              AND imp.state NOT IN (1, 3, 7, 8, 9)
              AND imp.credit > 0
              AND imp.id NOT IN (
                SELECT cdd.idImporter
                FROM CDBBDetails cdd
                  JOIN CDBB cd ON (cdd.idCDBB = cd.id)
                WHERE cd.status <> 3
                UNION
                SELECT cdod.idImporter
                FROM CDOBDetails cdod
                  JOIN CDOB cdo ON (cdo.id = cdod.idCDOB)
                WHERE cdo.status <> 3
              )
            ORDER BY imp.name";

        $stmt = odbc_prepare($db, $query);
        odbc_execute($stmt, [$idInform]);

        // Armazenar resultados em um array
        $resultados = [];
        while (odbc_fetch_row($stmt)) {
            $resultados[] = odbc_result($stmt, 1);
        }

        // Liberar o ODBC
        odbc_free_result($stmt);

        // Processar os resultados armazenados
        foreach ($resultados as $idImporter) {
            $stmt = odbc_prepare($db, "INSERT INTO CDBBDetails (idImporter, idCDBB) VALUES (?, ?)");
            odbc_execute($stmt, [$idImporter, $idCDBB]);
            odbc_free_result($stmt); // Liberar após cada execução
        }


        $content = "../cessao/interf/selImp.php";
    } else {
        $stmt = odbc_prepare($db, "SELECT * FROM CDBBDetails WHERE idCDBB = ?");
        odbc_execute($stmt, [$idCDBB]);

        if (odbc_fetch_row($stmt)) {
            $content = "../cessao/interf/imprBB.php";
        } else {
            echo '<script>verErro("Nenhum Importador foi Selecionado");</script>';
        }

        odbc_free_result($stmt);

    }
} { // tipoBanco

    if ($sub == 'Incluir') {
        $stmt = odbc_prepare($db, "SELECT idImporter FROM CDParcDetails WHERE idImporter = ? AND idCDParc = ?");
        odbc_execute($stmt, [$idImporter, $idCDParc]);

        // Verificar se há resultados armazenando em uma variável
        $existe = odbc_fetch_row($stmt);

        // Liberar o ODBC após a consulta
        odbc_free_result($stmt);

        if (!$existe) {
            $stmt = odbc_prepare($db, "INSERT INTO CDParcDetails (idImporter, idCDParc) VALUES (?, ?)");
            odbc_execute($stmt, [$idImporter, $idCDParc]);

            // Liberar após a execução do INSERT
            odbc_free_result($stmt);

            $content = "../cessao/interf/selImp.php";
        } else {
            echo '<script>verErro("Este Importador já faz parte da Cessão");</script>';
        }
    } else if ($sub == 'Excluir') {
        $stmt = odbc_prepare($db, "DELETE FROM CDParcDetails WHERE idImporter = ? AND idCDParc = ?");
        odbc_execute($stmt, [$idImporter, $idCDParc]);
        $content = "../cessao/interf/selImp.php";
    } else if ($sub == 'Remover Todos') {
        $stmt = odbc_prepare($db, "DELETE FROM CDParcDetails WHERE idCDParc = ?");
        odbc_execute($stmt, [$idCDParc]);
        $content = "../cessao/interf/selImp.php";
    } else if ($sub == 'Incluir Todos') {
        $query = "
            SELECT imp.id
            FROM Importer imp
            WHERE imp.idInform = ?
              AND imp.state NOT IN (1, 3, 7, 8, 9)
              AND imp.credit > 0
              AND imp.id NOT IN (
                SELECT cdd.idImporter
                FROM CDBBDetails cdd
                  JOIN CDBB cd ON (cdd.idCDBB = cd.id)
                WHERE cd.status <> 3
                UNION
                SELECT cdod.idImporter
                FROM CDOBDetails cdod
                  JOIN CDOB cdo ON (cdo.id = cdod.idCDOB)
                WHERE cdo.status <> 3
                UNION
                SELECT cdpc.idImporter
                FROM CDParcDetails cdpc
                  JOIN CDParc cdp ON (cdp.id = cdpc.idCDParc)
                WHERE cdp.status <> 3
              )
            ORDER BY imp.name";

        $stmt = odbc_prepare($db, $query);
        odbc_execute($stmt, [$idInform]);

        while (odbc_fetch_row($stmt)) {
            $idImporter = odbc_result($stmt, 1);
            $insertStmt = odbc_prepare($db, "INSERT INTO CDParcDetails (idImporter, idCDParc) VALUES (?, ?)");
            odbc_execute($insertStmt, [$idImporter, $idCDParc]);
        }
        $content = "../cessao/interf/selImp.php";
    } else {
        $stmt = odbc_prepare($db, "SELECT * FROM CDParcDetails WHERE idCDParc = ?");
        odbc_execute($stmt, [$idCDParc]);

        if (odbc_fetch_row($stmt)) {
            $content = "../cessao/interf/imprBB.php";
        } else {
            echo '<script>verErro("Nenhum Importador foi Selecionado");</script>';
        }
    }

    odbc_free_result($stmt);

}





?>