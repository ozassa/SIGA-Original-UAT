<?php //Alterado HiCom mes 04

$cur = odbc_exec($db, "select i_Seg, startValidity, endValidity, name, idAnt, prodUnit, tipoDve from Inform where id=$idInform");
if(odbc_fetch_row($cur)){
  $idSeg = odbc_result($cur, 1);
  $start = ymd2dmy(odbc_result($cur, 2));
  $end   = ymd2dmy(odbc_result($cur, 3));
  $name  = odbc_result($cur, 4);
  $idAnt = odbc_result($cur, 5);
  $prod  = odbc_result($cur, 6);
  $tipoDve = odbc_result($cur, "tipoDve");
  
  if(! $idSeg){
    $idSeg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
  }
  $cc = odbc_exec($dbSisSeg, "select n_Apolice from Apolice where i_Seg=$idSeg order by n_Apolice desc");
  odbc_fetch_row($cc);
  $apolice = sprintf("062%06d", odbc_result($cc, 1)). ($prod != 62 ? "/$prod" : '');
}

$x = odbc_exec($db, "select num, inicio, periodo,LiberaAtraso,LiberaVencida from DVE where id=$idDVE");
$num = odbc_result($x, 1);
$inicio = ymd2dmy(odbc_result($x, 2));
$periodo = odbc_result($x, 3);
$LiberaAtraso = odbc_result($x, 4);
$LiberaVencida = odbc_result($x, 5);

//$fim = getEndDate($inicio, $periodo);
    if ($tipoDve == 1) { //Tipo DVE Trimestral
        $fim = getEndDate($inicio, 3);
    }else{ // Tipo DVE Mensal
        $fim = getEndDate($inicio, 1);
    }

$x = odbc_exec($db, "select sum(totalEmbarcado), sum(proex), sum(ace) from DVEDetails where idDVE=$idDVE and state=1 and modalidade=$modalidade");

//HICOM
//$totalEmbarcado = ereg_replace('[.,]0*', '', odbc_result($x, 1));
//$totalProex = ereg_replace('[.,]0*', '', odbc_result($x, 2));
//$totalAce = ereg_replace('[.,]0*', '', odbc_result($x, 3));



$totalEmbarcado = round(odbc_result($x, 1),2);
$totalProex = round(odbc_result($x, 2),2);
$totalAce = round(odbc_result($x, 3),2);

//$totalEmbarcado = 0;
//$totalProex = 0;
//$totalAce = 0;


//echo "--------------------------------" . $totalEmbarcado;
//die();


$x = odbc_exec($db, "select count(*) from DVEDetails where idDVE=$idDVE and modalidade=$modalidade and state=1");
$num_registros = odbc_result($x, 1);
if(! $registro){
  $registro = $num_registros + 1;
}

$details[0] = 'dummy';
$x = odbc_exec($db, "select id from DVEDetails where idDVE=$idDVE and modalidade=$modalidade and state=1 order by id");
while(odbc_fetch_row($x)){
  $details[] = odbc_result($x, 1);
}

if($registro <= $num_registros){
  $show = 1;
  $x = odbc_exec($db,
		 "select idImporter, idCountry, embDate, vencDate, fatura, totalEmbarcado, proex, ace, id, DataCadastro
                  from DVEDetails where id=$details[$registro]");
  $idBuyer = odbc_result($x, 1);
  $idCountry = odbc_result($x, 2);
  $dataEmb = ymd2dmy(odbc_result($x, 3));
  list($dataEmbDia, $dataEmbMes, $dataEmbAno) = explode('/', $dataEmb);
  $dataVenc = ymd2dmy(odbc_result($x, 4));
  list($dataVencDia, $dataVencMes, $dataVencAno) = explode('/', $dataVenc);
  $fatura = odbc_result($x, 5);
  $valorEmb = round( "0" . odbc_result($x, 6),2);
  $proex = round( "0" . odbc_result($x, 7),2);
  $ace = round( "0" . odbc_result($x, 8),2);
  //$DataCadastro = odbc_result($x, 10);
  
  //$valorEmb = 0;
  //$proex = 0;
  //$ace = 0;  
  
  
  $idDetail = odbc_result($x, 9);
  
  
}

if($no_values){
  $dataEmbDia = $dataEmbAno = $dataEmbMes = $dataVencDia = $dataVencMes = $dataVencAno = $fatura = $valorEmb = $proex = $ace = '';
}
?>
