<?php
session_start();
$log_query = "";
$totalcheck = $_REQUEST['totalcheck'];
if (!$idBuyer) {
    $idBuyer = $_REQUEST['idBuyer'];
}
$userID = $_SESSION['userID'];
if (!$idNotification) {
    $idNotification = $_REQUEST['idNotification'];
}
$z = 1;
for ($n = 0; $n < $totalcheck; $n++) {
    $idBuyer = $_REQUEST["check" . $z];
    $c = odbc_prepare($db, "SELECT inf.state, inf.idAnt, inf.name FROM Inform inf JOIN Importer imp ON (imp.idInform = inf.id) WHERE imp.id=?");
    odbc_execute($c, array($idBuyer));
    if (odbc_fetch_row($c)) {
        $state = odbc_result($c, 1);
        $idAnt = odbc_result($c, 2);
        $nameinf = odbc_result($c, 3);
    }
    odbc_free_result($c);

    $cur = odbc_prepare($db, "SELECT idTwin FROM Importer WHERE id=?");
    odbc_execute($cur, array($idBuyer));
    if (odbc_fetch_row($cur)) {
        $idOther = odbc_result($cur, 1);
    }
    odbc_free_result($cur);

    if (!$idOther) {
        $y = odbc_prepare($db, "SELECT id FROM Importer WHERE idTwin=?");
        odbc_execute($y, array($idBuyer));
        $idOther = odbc_result($y, 1);
        odbc_free_result($y);
    }

    if ($state >= 1 && $state <= 8) {
        $x = odbc_prepare($db, "SELECT * FROM ChangeCredit WHERE idImporter=? AND credit>0 AND state=6 ORDER BY id DESC");
        odbc_execute($x, array($idBuyer));
        if (odbc_fetch_row($x)) {
            odbc_free_result($x);
            $a = odbc_prepare($db, "UPDATE Importer SET state=7 WHERE id=?");
            odbc_execute($a, array($idBuyer));
            odbc_free_result($a);
            $log_query .= "UPDATE Importer SET state=7 WHERE id=$idBuyer";
            $query1 = "INSERT INTO ChangeCredit (state, userIdChangeCredit, idImporter, creditSolic, analysis, monitor, credit) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $r = odbc_prepare($db, $query1);
            odbc_execute($r, array(7, $userID, $idBuyer, 0, 1, 0, 0));
            odbc_free_result($r);
            $log_query .= $query1;
        } else {
            odbc_free_result($x);
            $b = odbc_prepare($db, "UPDATE Importer SET state=8 WHERE id=?");
            odbc_execute($b, array($idBuyer));
            odbc_free_result($b);
            $log_query .= "UPDATE Importer SET state=8 WHERE id=$idBuyer";
            if ($comm == 'rejeitar') {
                $x = odbc_prepare($db, "SELECT creditSolic FROM ChangeCredit WHERE idImporter=? AND creditSolic IS NOT NULL ORDER BY id DESC");
                odbc_execute($x, array($idBuyer));
                $creditSolic = odbc_result($x, 1);
                odbc_free_result($x);
            } else {
                $creditSolic = 0;
            }
            $query1 = "INSERT INTO ChangeCredit (state, userIdChangeCredit, idImporter, creditSolic, analysis, monitor) VALUES (?, ?, ?, ?, ?, ?)";
            $r = odbc_prepare($db, $query1);
            odbc_execute($r, array(8, $userID, $idBuyer, $creditSolic, 1, 1));
            odbc_free_result($r);
            $log_query .= $query1;
        }
    } else if ($state >= 9 && $state <= 11) {
        $query = "UPDATE Importer SET state=8, c_Coface_Imp=0 WHERE id=?";
        $c = odbc_prepare($db, $query);
        odbc_execute($c, array($idBuyer));
        odbc_free_result($c);
        $log_query .= $query;
        $query1 = "INSERT INTO ChangeCredit (state, userIdChangeCredit, idImporter, creditSolic, analysis, monitor) VALUES (?, ?, ?, ?, ?, ?)";
        $r = odbc_prepare($db, $query1);
        odbc_execute($r, array(8, $userID, $idBuyer, 0, 1, 1));
        odbc_free_result($r);
        $log_query .= $query1;
        $d = odbc_prepare($db, "SELECT MAX(id) FROM ChangeCredit WHERE idImporter=?");
        odbc_execute($d, array($idBuyer));
        if (odbc_fetch_row($d)) {
            $id_cc = odbc_result($d, 1);
        }
        odbc_free_result($d);
        $up = odbc_prepare($db, "UPDATE ChangeCredit SET monitor=0, analysis=0 WHERE id=?");
        if (!odbc_execute($up, array($id_cc))) {
            $msg = "Erro ao inativar importador";
        } else {
            $log_query .= "update ChangeCredit set monitor=0, analysis=0 where id=$id_cc";
        }
        odbc_free_result($up);
    }

    $cot = odbc_prepare($db, "SELECT emailContact, name, state, warantyInterest FROM Inform WHERE id=?");
    odbc_execute($cot, array($idInform));
    $emailContact = trim(odbc_result($cot, 1));
    $stateExp = odbc_result($cot, 3);
    $ApoliceBB = odbc_result($cot, 4);
    $exportador = odbc_result($cot, 2);
    odbc_free_result($cot);

    if (!$emailContact) {
        $emailContact = "siex@cofacedobrasil.com";
    }

    $ex = odbc_prepare($db, "SELECT name FROM Importer WHERE id=?");
    odbc_execute($ex, array($idBuyer));
    $name = odbc_result($ex, 1);
    odbc_free_result($ex);

    $ex2 = odbc_prepare($db, "SELECT i.name, c.name FROM Importer i JOIN Country c ON c.id=i.idCountry WHERE i.id=?");
    odbc_execute($ex2, array($idBuyer));
    $imp_country = odbc_result($ex2, 2);
    odbc_free_result($ex2);

    require_once("../MailSend.php");
    if ($stateExp == 10 && $ApoliceBB == 1) {
        if ($state >= 1 && $state <= 8) {
            $x = odbc_prepare($db, "SELECT u.email FROM Users u JOIN Inform i ON u.id=i.idUser WHERE i.id=?");
            odbc_execute($x, array($idInform));
            if (odbc_fetch_row($x)) {
                $email = trim(odbc_result($x, 1));
                $to = $email;
                $msgmail = "<font class=texto><br>Prezado Executivo,<br><br><br> A solicitação abaixo não pôde ser processada, favor contatar o Departamento de Crédito para maiores esclarecimentos.<br><br><ul><li>Importador: $name / $imp_country </li></ul> <br> Atenciosamente, <br><br><br> Departamento de Crédito </font>";
            }
            odbc_free_result($x);
        } else {
            $msgmail = "<font class=texto><br>Prezado Segurado,<br><br><br> A solicitação abaixo não pôde ser processada, favor contatar o Departamento de Crédito para maiores esclarecimentos.<br><br><ul><li>Comprador: $name / $imp_country </li></ul> <br> Atenciosamente, <br><br><br> Departamento de Crédito </font>";
            $not = odbc_prepare($db, "SELECT email FROM Contact WHERE idInform=? AND notificationForChangeCredit=1");
            odbc_execute($not, array($idInform));
            $to = trim($emailContact);
            while (odbc_fetch_row($not)) {
                $email = trim(odbc_result($not, 1));
                $mail->AddAddress($email);
            }
            odbc_free_result($not);
        }
        $mail->From = "siex@cofacedobrasil.com";
        $mail->FromName = "Credito";
        $mail->AddAddress('siex@cofacedobrasil.com');
        $mail->AddAddress($to);
        if ($emailContact) {
            $mail->AddAddress($emailContact);
        }
        $mail->IsHTML(true);
        $mail->Subject = trim($exportador);
        $mail->Body = $msgmail;
        if ($mail->Host != '') {
            $enviado = $mail->Send();
        }
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();
        if ($enviado) {
            $msg = "E-mail enviado com sucesso";
        } else {
            $msg = "Problemas no envio do e-mail" . $mail->ErrorInfo;
        }
    }

    $state = 1;
    $hold = " AND hold=0";
    $union = "";
    if ($flag_renovacao) {
        $union = " UNION SELECT Importer.id, Importer.name, Country.name, Country.code, Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, Importer.state FROM Importer, Country WHERE Importer.idInform=? AND Importer.idCountry=Country.id AND Importer.state=6 AND Importer.creditAut=1";
    }
    $cquery = "SELECT Importer.id, Importer.name, Country.name, Country.code, Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId FROM Importer, Country WHERE Importer.idInform=? AND Importer.idCountry=Country.id AND (Importer.state='0' OR Importer.state=?) $hold AND (Importer.creditAut IS NULL OR Importer.creditAut=0) ORDER BY Importer.id";
    $c = odbc_prepare($db, $cquery);
    odbc_execute($c, array($idInform, $state));
    if (!odbc_fetch_row($c)) {
        $vazio1 = 1;
    }
    odbc_free_result($c);

    if ($idAnt > 0) {
        if ($flag_renovacao) {
            $c2 = odbc_prepare($db, "SELECT Importer.id, Importer.name, Country.name, Country.code, Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, Importer.state FROM Importer, Country WHERE Importer.idInform=? AND Importer.idCountry=Country.id AND (Importer.state='0' OR Importer.state=?) $hold AND Importer.creditAut=1 $union ORDER BY Importer.id");
            odbc_execute($c2, array($idInform, $state, $idInform));
        } else {
            $c2 = odbc_prepare($db, "SELECT Importer.id, Importer.name, Country.name, Country.code, Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, Importer.state FROM Importer, Country WHERE Importer.idInform=? AND Importer.idCountry=Country.id AND (Importer.state='0' OR Importer.state=?) $hold AND Importer.creditAut=1 ORDER BY Importer.id");
            odbc_execute($c2, array($idInform, $state));
        }
        if (!odbc_fetch_row($c2)) {
            $vazio2 = 1;
        }
        odbc_free_result($c2);
        if ($vazio1 && $vazio2) {
            $vazio = 1;
        } else {
            $vazio = 0;
        }
    } else {
        $vazio = $vazio1;
    }

    if ($vazio) {
        $notif->doneRole($idNotification, $db);
    }

    $sql = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) VALUES (?, ?, ?, ?, ?)";
    $insLog = odbc_prepare($db, $sql);
    odbc_execute($insLog, array('12', $userID, $idInform, date("Y") . "-" . date("m") . "-" . date("d"), date("H") . ":" . date("i") . ":" . date("s")));
    odbc_free_result($insLog);

    $sql_id = "SELECT @@IDENTITY AS id_Log";
    $cur = odbc_prepare($db, $sql_id);
    odbc_execute($cur, array());
    $logId = odbc_result($cur, 1);
    odbc_free_result($cur);

    if ($logId) {
        $sql = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) VALUES (?, ?, ?, ?)";
        $rs = odbc_prepare($db, $sql);
        odbc_execute($rs, array($logId, 'Importador', $name, 'Alteração'));
        odbc_free_result($rs);
        $sql_id_detalhes = "SELECT @@IDENTITY AS id_detalhes";
        $cur = odbc_prepare($db, $sql_id_detalhes);
        odbc_execute($cur, array());
        $id_detalhes = odbc_result($cur, 1);
        odbc_free_result($cur);
        if ($id_detalhes) {
            $sqllogq = "INSERT INTO Log_Detalhes_Query (id_detalhes, query) VALUES (?, ?)";
            $rsq = odbc_prepare($db, $sqllogq);
            odbc_execute($rsq, array($id_detalhes, str_replace("'", "", $log_query)));
            odbc_free_result($rsq);
        }
    } else {
        $msg = "Erro no incluir do Log";
    }

    $z++;
}
?>
