<?php //Alterado HiCom mes 04

$cur = odbc_exec($db, "select i_Seg, startValidity, endValidity, name, idAnt, prodUnit , tipoDve, n_Apolice from Inform where id=$idInform");

if(odbc_fetch_row($cur)){
    $idSeg = odbc_result($cur, 1);
    $start = ymd2dmy(odbc_result($cur, 2));
    $end   = ymd2dmy(odbc_result($cur, 3));
    $name  = odbc_result($cur, 4);
    $namecl  = odbc_result($cur, 4);
    $idAnt = odbc_result($cur, 5);
    $prod  = odbc_result($cur, 6);
    $tipoDve = odbc_result($cur, "tipoDve");
    $apolice = odbc_result($cur, "n_Apolice");
    
    $apolice = sprintf("062%06d", $apolice). ($prod != 62 ? "/$prod" : '');

    /*if(! $idSeg){
      $idSeg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
    }
    
    $cc = odbc_exec($dbSisSeg, "select n_Apolice from Apolice where i_Seg=$idSeg order by n_Apolice desc");
    odbc_fetch_row($cc);
    $apolice = sprintf("062%06d", odbc_result($cc, 1)). ($prod != 62 ? "/$prod" : '');*/
}

$x = odbc_exec($db, "select num, inicio, periodo, total2 from DVE where id=$idDVE");

$num = odbc_result($x, 1);
$inicio = ymd2dmy(odbc_result($x, 2));
$periodo = odbc_result($x, 3);
$total2 = round("0" . odbc_result($x, 4),2);

if ($tipoDve == 3) { //Tipo DVE Anual
  $fim = getEndDate($inicio, 12);
}else if ($tipoDve == 2) { //Tipo DVE Trimestral
  $fim = getEndDate($inicio, 3);
}else{ // Tipo DVE Mensal
  $fim = getEndDate($inicio, 1);
}

?>