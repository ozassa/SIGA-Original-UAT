<?php 

function arruma($str){
  list($dia, $mes, $ano) = explode("/", $str);
  list($hora, $min, $seg) = explode(" ", date("H i s"));
  return "$ano-$mes-$dia $hora:$min:$seg";
}

odbc_autocommit($dbSisSeg, FALSE);
odbc_autocommit($db, FALSE);
$ok = true;

$dateA = arruma($date);

$q =
 "INSERT INTO Recuperacao (idSinistro, value, date, custoCoface) VALUES ($idSinistro, ".
 number_format($field->getNumField('valor'), 0, '', ''). ", '$dateA', $custo)";
$cur = odbc_exec($db, $q);
if(! $cur){
  $ok = false;
}

$qu = "INSERT INTO SinistroObs (idSinistro, name, date, obs) VALUES ($idSinistro, '$user->name', getdate(),  'Efetuada Recuperacao')";
$obs = odbc_exec($db, $qu);
if(! $obs){
  $ok = false;
}

$cur = odbc_exec($db, "SELECT sum(value) FROM Recuperacao WHERE idSinistro = $idSinistro");
$valueTotal = 0;
if(odbc_fetch_row($cur)) {
  $valueTotal = odbc_result($cur, 1);
}

if ($valueTotal < $total){
  $msg = "Valor total não recuperado<br>";
} else if ($valueTotal == $total){
  $q = "UPDATE Sinistro SET status = 7 WHERE idImporter = $idImporter";
  $cur = odbc_exec($db, $q);

  if (!$cur) {
    $msg = "Problemas na atualização da base";
    $ok = false;
  } else {
    $cur = odbc_exec($db, "SELECT Inform.name, Importer.name FROM Inform, Importer Where Inform.id = $idInform AND Importer.id = $idImporter");
    $name = odbc_result($cur, 1);
    $nameI = odbc_result($cur, 2);

    $r = $notif->doneRole($idNotification, $db);
    if ($r){
      $r = $notif->sinistroRecuperado($idNotification, $idImporter, $idInform, $user, $db, $name, $idSinistro, $nameI);
      if (!$r) {
	$msg = "problemas na criação da notificação";
	$ok = false;
      }
    } else {
      $msg = "problemas na criação da notificação";
      $ok = false;
    }
  }
}

$x = odbc_exec($db, "select status from Sinistro where id=$idSinistro");
$status = odbc_result($x, 1);

$cur = odbc_exec($db,
		 "SELECT Inform.i_Seg, Inform.idAnt
                  FROM Inform, Importer Where Inform.id=$idInform AND Importer.id=$idImporter");
$i_Seg = odbc_result($cur, 1);
$idAnt = odbc_result($cur, 2);
if($idAnt && ! $i_Seg){
  $i_Seg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
}
$x = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$i_Seg");
$n_Apolice = odbc_result($x, 1);
$x = odbc_exec($dbSisSeg, "select n_Sucursal, n_Ramo from Apolice where n_Apolice=$n_Apolice");
$n_Sucursal = odbc_result($x, 1);
$n_Ramo = odbc_result($x, 2);
$n_Imp = 0;
$ci_coface = odbc_result(odbc_exec($db, "select c_Coface_Imp from Importer where id=$idImporter"), 1);
if($ci_coface){
  $n_Imp = odbc_result(odbc_exec($dbSisSeg,
				 "select n_Imp from Importador where c_Coface_Imp='$ci_coface' and i_Seg=$i_Seg"),
		       1);
}

$x = odbc_exec($dbSisSeg,
	       "select i_Aviso from Aviso_Sinistro
                where n_Apolice=$n_Apolice and i_Seg=$i_Seg
                and n_Sucursal=$n_Sucursal and n_Ramo=$n_Ramo and n_Imp=$n_Imp");
$i_Aviso = odbc_result($x, 1);
$x = odbc_exec($dbSisSeg,
	       "select i_Parcela from Parcela
                where n_Apolice=$n_Apolice and i_Seg=$i_Seg
                and n_Sucursal=$n_Sucursal and n_Ramo=$n_Ramo
                order by d_Parcela");
$Numero_Fatura = odbc_result($x, 1);
if($status != 2){
  if($i_Aviso && $Numero_Fatura){
    require_once('recuperacaoSisSeg.php');
  }else{
    $ok = false;
    if(! $i_Aviso){
      $msg = 'Não foi encontrado Aviso de Sinistro';
    }else if(! $Numero_Fatura){
      $msg = 'Não foi encontrada Fatura';
    }
  }
}

if($ok){
  odbc_commit($dbSisSeg);
  odbc_commit($db);
}else{
  odbc_rollback($dbSisSeg);
  odbc_rollback($db);
  if(! $msg){
    $msg = 'Erro na recuperação';
  }
}

odbc_autocommit($dbSisSeg, TRUE);
odbc_autocommit($db, true);
?>
