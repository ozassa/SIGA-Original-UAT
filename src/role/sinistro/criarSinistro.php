<?php $ok = TRUE;
odbc_autocommit($db, FALSE);
odbc_autocommit($dbSisSeg, FALSE);
$q = "UPDATE Sinistro SET status = 3 WHERE id = $idSinistro";
$cur = odbc_exec($db, $q);

if (!$cur) {
  $msg = "Problemas na atualização da base";
} else {
  $var = odbc_exec($db, "SELECT Inform.name, Inform.i_Seg, Inform.idAnt, Importer.name FROM Inform, Importer Where Inform.id = $idInform AND Importer.id = $idImporter");
  $name = odbc_result($var, 1);
  $i_Seg = odbc_result($var, 2);
  $idAnt = odbc_result($var, 3);
  $nameI = odbc_result($var, 4);
  if($idAnt && ! $i_Seg){
    $i_Seg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
  }
  $x = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$i_Seg");
  $n_Apolice = odbc_result($x, 1);

  $x = odbc_exec($dbSisSeg, "select n_Sucursal, n_Ramo from Apolice where n_Apolice=$n_Apolice");
  $n_Sucursal = odbc_result($x, 1);
  $n_Ramo = odbc_result($x, 2);

  $ci_coface = odbc_result(odbc_exec($db, "select c_Coface_Imp from Importer where id=$idImporter"), 1);
  if($ci_coface){
    $n_Imp = odbc_result(odbc_exec($dbSisSeg,
				   "select n_Imp from Importador where c_Coface_Imp='$ci_coface' and i_Seg=$i_Seg"),
			 1);
  }
  if(! $n_Imp){
    $n_Imp = 0;
  }

  $r = $notif->doneRole($idNotification, $db);
  if ($r){
    $r = $notif->sinistro($idNotification, $idImporter, $idInform, $idInform, $db, $name, $idSinistro, $nameI);
    if (!$r) {
      $msg = "problemas na criação da notificação";
      $ok = FALSE;
    }

    $q = "INSERT INTO SinistroObs (idSinistro, name, date, obs) VALUES ($idSinistro, '$user->name', getdate(),  'Sinistro Criado')";
    $obs = odbc_exec($db, $q);

// cria o aviso no sisseg
//     $mss = mssql_connect('sbcesun', 'sa', '');
//     if(! $mss){
//       $msg .= "Ops, falha ao conectar-se ao banco de dados<br>";
//       $ok = FALSE;
//     }
//     $d = mssql_select_db('SisSegTeste', $mss);
//     if(! $d){
//       $msg .= "Ops, falha ao selecionar ao banco de dados<br>";
//       $ok = FALSE;
//     }

    $i_Aviso = $n_Sinistro = 0;
    $n_User = 66;
    $v_Serv_Rec_COFACE = 0;
    $d_Aviso = date("Y-m-d"). ' 00:00:00.000';
    $d_Interv = $d_Aviso;
    $d_Prev_Pag = $d_Pag_COFACE = '';
    $s_Aviso = 2;
    $v_Credito = '';
    $t_Coface = 0;
    $Coment = '';
    $p_Exp_Recup = 0;

//     $sp = mssql_init('ssI_Aviso_Sinistro', $mss);
//     if(! $sp){
//       $msg .= "Ops, falha ao iniciar stored procedure ssI_Aviso_Sinistro<br>";
//       $ok = FALSE;
//     }
//     mssql_bind($sp, "@i_Aviso", &$i_Aviso, SQLINT2, TRUE, FALSE);
//     mssql_bind($sp, "@n_Sinistro", &$n_Sinistro, SQLINT2, TRUE, FALSE);
//     mssql_bind($sp, "@n_User", &$n_User, SQLINT2);
//     mssql_bind($sp, "@n_Sucursal", &$n_Sucursal, SQLINT2, FALSE, FALSE);
//     mssql_bind($sp, "@n_Ramo", &$n_Ramo, SQLINT2, FALSE, FALSE);
//     mssql_bind($sp, "@n_Apolice", &$n_Apolice, SQLINT2, FALSE, FALSE);
//     mssql_bind($sp, "@i_Seg", &$i_Seg, SQLINT2);
//     mssql_bind($sp, "@n_Imp", &$n_Imp, SQLINT2);
//     mssql_bind($sp, "@v_Sinistro", &$v_Sinistro, SQLFLT8, FALSE, FALSE);
//     mssql_bind($sp, "@v_Serv_Rec_COFACE", &$v_Serv_Rec_COFACE, SQLFLT8, FALSE, TRUE);
//     mssql_bind($sp, "@d_Aviso", &$d_Aviso, SQLVARCHAR, FALSE, FALSE, 8);
//     mssql_bind($sp, "@d_Interv", &$d_Interv, SQLVARCHAR, FALSE, TRUE, 8);
//     mssql_bind($sp, "@d_Prev_Pag", &$d_Prev_Pag, SQLVARCHAR, FALSE, TRUE, 8);
//     mssql_bind($sp, "@d_Pag_COFACE", &$d_Pag_COFACE, SQLVARCHAR, FALSE, TRUE, 8);
//     mssql_bind($sp, "@s_Aviso", &$s_Aviso, SQLINT2, FALSE, FALSE);
//     mssql_bind($sp, "@v_Credito", &$v_Credito, SQLFLT8, FALSE, TRUE);
//     mssql_bind($sp, "@t_Coface", &$t_Coface, SQLINT1);
//     mssql_bind($sp, "@Coment", &$Coment, SQLVARCHAR, FALSE, TRUE, 255);
//     mssql_bind($sp, "@p_Exp_Recup", &$p_Exp_Recup, SQLFLT8);
//     mssql_bind($sp, "RETVAL", &$ret, SQLINT2);
//     $r = mssql_execute($sp);
//     unset($sp);

    require_once("aviso.php");
    if($ret != 0){
      $msg .= "Erro na inclusão do Aviso de Sinistro<br>";
      $ok = FALSE;
    }else{
      // cria as perdas
      $x = odbc_exec($dbSisSeg,
		     "select max(i_Aviso) from Aviso_Sinistro
                      where n_Apolice=$n_Apolice and i_Seg=$i_Seg and n_Imp=$n_Imp");
      $i_Aviso = odbc_result($x, 1);

      $query = "SELECT * FROM SinistroDetails WHERE idSinistro = $idSinistro ORDER BY numFat";
      $cur = odbc_exec($db,$query);
      $valueTotal = 0;
      while (odbc_fetch_row($cur)) {
	$Numero_Fatura = strtoupper(odbc_result($cur, 4));
	$d_Embarque = odbc_result($cur, 5);
	$d_Vencimento = odbc_result($cur, 6);
	$v_Pago = odbc_result($cur, 7);
	$v_Fatura = odbc_result($cur, 8);
	$valueAbt = odbc_result($cur, 9);

	$v_Juros = $n_Seq = 0;
	$d_Fatura = $d_Prorrogacao = '';

	require_once("perda.php");
/* 	$sp = mssql_init('ssI_Perda', $mss); */
/* 	if(! $sp){ */
/* 	  $msg .= "Ops, falha ao iniciar stored procedure ssI_Perda<br>"; */
/* 	  $ok = FALSE; */
/* 	} */
/* 	mssql_bind($sp, "@i_Aviso", &$i_Aviso, SQLINT2); */
/* 	mssql_bind($sp, "@n_Seq", &$n_Seq, SQLINT1, TRUE); */
/* 	mssql_bind($sp, "@Numero_Fatura", &$numFat, SQLVARCHAR, FALSE, FALSE, 15); */
/* 	mssql_bind($sp, "@d_Fatura", &$dateFat, SQLVARCHAR, FALSE, TRUE, 8); */
/* 	mssql_bind($sp, "@d_Embarque", &$dateEmb, SQLVARCHAR, FALSE, FALSE, 8); */
/* 	mssql_bind($sp, "@d_Vencimento", &$dateVenc, SQLVARCHAR, FALSE, FALSE, 8); */
/* 	mssql_bind($sp, "@d_Prorrogacao", &$dateProrrog, SQLVARCHAR, FALSE, TRUE, 8); */
/* 	mssql_bind($sp, "@v_Fatura", &$valueFat, SQLFLT8); */
/* 	mssql_bind($sp, "@v_Juros", &$valueJuros, SQLFLT8, FALSE, TRUE); */
/* 	mssql_bind($sp, "@v_Pago", &$valuePag, SQLFLT8); */
/* 	mssql_bind($sp, "@v_Sinistro", &$v_Sinistro, SQLFLT8); */
/* 	mssql_bind($sp, "RETVAL", &$ret, SQLINT2); */
/* 	mssql_bind($sp, "@v_Perda", &$v_Perda, SQLFLT8, TRUE); */
/* 	$r = mssql_execute($sp); */
/* 	unset($sp); */
 	if($ret != 0){
 	  //$msg .= "Erro na execução do stored procedure<br>". mssql_get_last_message();
 	  $ok = FALSE;
 	  break;
 	}
      } // while
    }
  } else {
    $msg = "problemas na criação da notificação";
    $ok = FALSE;
  }
}

if($ok){
  odbc_commit($db);
  odbc_commit($dbSisSeg);
  $msg = 'Sinistro criado';
}else{
  $msg .= "<br>". odbc_errormsg();
  odbc_rollback($db);
  odbc_rollback($dbSisSeg);
}
odbc_autocommit($db, TRUE);
odbc_autocommit($dbSisSeg, TRUE);
?>
