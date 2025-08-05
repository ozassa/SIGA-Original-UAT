<?php // Criada HICOM - 19/10/2004 (Gustavo)

function truncate($valor, $casas)
{
    return (floor($valor * (pow(10, $casas)))) / (pow(10, $casas));
}

function numApolice($idInform, $db, $dbSisSeg)
{
    // Obtem dados do inform
    $sqlInform = "SELECT id, contrat, i_Seg, nProp, name, cnpj, startValidity, endValidity, emailContact, contact, email 
                  FROM Inform 
                  WHERE id = ?";
    $stmtInform = odbc_prepare($db, $sqlInform);
    odbc_execute($stmtInform, [$idInform]);

    if (!odbc_fetch_row($stmtInform)) {
        return 0; // Retorna 0 se não encontrar dados
    }

    $hc_infName       = trim(odbc_result($stmtInform, "name"));
    $hc_startValidity = ymd2dmy(trim(odbc_result($stmtInform, "startValidity") ?? ''));
    $hc_endValidity   = ymd2dmy(trim(odbc_result($stmtInform, "endValidity") ?? ''));
    $hc_i_Seg         = trim(odbc_result($stmtInform, "i_Seg") ?? '');
    $hc_n_Prop        = trim(odbc_result($stmtInform, "nProp") ?? '');
    $hc_c_Coface      = trim(odbc_result($stmtInform, "contrat") ?? '');
    
    $achou = false;
    
    if ($dbSisSeg) {
        $achou = true;

        $conditions = [];
        $params = [];
        
        if ($hc_c_Coface) {
            $conditions[] = "c_Coface = ?";
            $params[] = $hc_c_Coface;
        } else {
            $conditions[] = "c_Coface IS NULL";
        }

        if ($hc_i_Seg) {
            $conditions[] = "i_Seg = ?";
            $params[] = $hc_i_Seg;
        } else {
            $conditions[] = "i_Seg IS NULL";
        }

        if ($hc_n_Prop) {
            $conditions[] = "n_Prop = ?";
            $params[] = $hc_n_Prop;
        } else {
            $conditions[] = "n_Prop IS NULL";
        }

        $loc_sql = "SELECT n_Apolice 
                    FROM Base_Calculo 
                    WHERE " . implode(" AND ", $conditions) . " 
                          AND t_Apolice = 0 
                    ORDER BY i_BC DESC";
        
        $stmtSisSeg = odbc_prepare($dbSisSeg, $loc_sql);
        odbc_execute($stmtSisSeg, $params);

        if (!odbc_fetch_row($stmtSisSeg)) {
            $loc_sql = "SELECT n_Apolice 
                        FROM Base_Calculo 
                        WHERE " . implode(" AND ", $conditions) . " 
                        ORDER BY i_BC DESC";
            
            $stmtSisSeg = odbc_prepare($dbSisSeg, $loc_sql);
            odbc_execute($stmtSisSeg, $params);

            if (!odbc_fetch_row($stmtSisSeg)) {
                unset($conditions[array_search("n_Prop = ?", $conditions)]);
                $loc_sql = "SELECT n_Apolice 
                            FROM Base_Calculo 
                            WHERE " . implode(" AND ", $conditions) . " 
                            ORDER BY i_BC DESC";
                
                $stmtSisSeg = odbc_prepare($dbSisSeg, $loc_sql);
                odbc_execute($stmtSisSeg, array_slice($params, 0, -1));

                if (odbc_fetch_row($stmtSisSeg)) {
                    $achou = true;
                }
            } else {
                $achou = true;
            }
        } else {
            $achou = true;
        }
    }
    
    if ($achou) {
        $hc_n_Apolice = trim(odbc_result($stmtSisSeg, "n_Apolice"));
    } else {
        $hc_n_Apolice = 0;
    }
    
    return $hc_n_Apolice;
}

?>
