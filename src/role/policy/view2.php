<?php $diaT = date ('d');
$mesT = date ('m');
$dMoeda = date ("Y-m-d", mktime (0,0,0, $mesT, $diaT - 1, date("Y"))). ' 00:00:00.000'; //formata data de ontem

// pega a data num formato decente
function data_string($d){
  $meses = array("janeiro", "fevereiro", "março", "abril", "maio", "junho",
		 "julho", "agosto", "setembro", "outubro", "novembro", "dezembro");
  //$dias_da_semana = array("Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado");
  list($dia, $mes, $ano) = split ('/', $d);
  $mes = $meses[$mes - 1];
  //$dia_da_semana = $dias_da_semana[$dia_da_semana];
  return "$dia de $mes de $ano";
}

function converte($valor, $conversao){
  return $valor * $conversao;
}

function getStartDate($d, $n, $c = 0){
  if(preg_match("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $d, $v)){
    return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
  }else if(preg_match("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})", $d, $v)){
    return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
  }
}

//-----------------------------------------------------------------
// converte uma data de '31/12/2002' para '2002-12-31 00:00:00.000'
function dmy2ymd($d){
  if(preg_match("([0-9]{2})/([0-9]{2})/([0-9]{4})", $d, $v)){
    return "$v[3]-$v[2]-$v[1] 00:00:00.000";
  }
  return "";
}

// devolve a data correspondente ao dia 15 de 3 meses após a data fornecida
function conserta($d){
  if(preg_match("([0-9]{4})-([0-9]{2})", $d, $v)){
    $ano = $v[1];
    if($v[2] >= 10){
      $ano++;
    }
    $mes = (($v[2] + 3) % 13) + ((int) ($v[2] / 10));
    return "$ano-$mes-15 00:00:00.000";
  }
  return '';
}

// verifica as renovacoes continuas
function verifica_continua($db, $idInform){
  $x = odbc_exec($db, "select idAnt, startValidity from Inform where id=$idInform");
  if(odbc_fetch_row($x)){
    $idAnt = odbc_result($x, 1);
    $start = odbc_result($x, 2);
    $x = odbc_exec($db, "select id, idCountry, c_Coface_Imp from Importer where idInform=$idAnt");
    while(odbc_fetch_row($x)){
      $idImporterAnt = odbc_result($x, 1);
      $idCountry = odbc_result($x, 2);
      $ciCoface = odbc_result($x, 3);
      $y = odbc_fetch_row($db, "select id from Importer where idInform=$idInform and c_Coface_Imp=$ciCoface and idCountry=$idCountry and id <> $idImporterAnt");
      if(odbc_fetch_row($y)){
	$idImporter = odbc_result($y, 1);
	$y = odbc_exec($db, "update Importer set validityDate='$start' where id=$idImporter");
      }
    }
  }
}

// converte a data de yyyy-mm-dd para dd/mm/yyyy
if(! function_exists('ymd2dmy')){
  function ymd2dmy($d){
    if(preg_match("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}

$ano = date("y", time());

//$cur = odbc_exec($db,
//		 "SELECT notification, cookie FROM NotificationR WHERE id=$idNotification");
//if (!odbc_fetch_row($cur)){
//  $title = "Notificação inválida";
//}else{
//  $title = odbc_result($cur, 1);
//  $key = odbc_result($cur, 2);
//}

$key = time(). session_id();

require("../../pdfConf.php");

$prefix = $pdfDir. $key;
$num_parcelas = ($Num_Parcelas ? $Num_Parcelas : $numParc);

if(! file_exists($prefix. 'Apolice.pdf')){
  odbc_exec($db, "update Inform set startValidity=dateAceit where id=". $field->getField("idInform"));
  verifica_continua($db, $idInform);
  $query =
    "SELECT startValidity, endValidity, i_Seg, policyKey, Inform.idAnt, Inform.percCoverage, Inform.dateEmission FROM Volume JOIN ".
    "Inform ON (idInform = Inform.id) WHERE idInform = ". $field->getField("idInform");
  $cur = odbc_exec($db, $query);
  $startValidity = "";
  $endValidity = "";
  $vencFirst = "";

  if(odbc_fetch_row($cur)) {
    $idAnt = odbc_result($cur, 5);
    $cobertura = number_format(odbc_result($cur, 6), 0, '', '');
    $inicio_vigencia = $data = odbc_result($cur, "startValidity");
    if($data){
      $d = substr($data, 8, 2);
      $m = substr($data, 5, 2);
      $a = substr($data, 0, 4);
      $startValidity = "$d/$m/$a";
      $dataEmissao = odbc_result($cur, 7);
    }

/*     $data = odbc_result($cur, "endValidity"); */
/*     if ($data){ */
/*       $endValidity = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4); */
/*     } */
    $data = date("Y-m-d", mktime(0, 0, 0, $m, $d - 1, $a + 1));
    odbc_exec($db, "update Inform set endValidity='$data 00:00:00.000' where id=". $field->getField("idInform"));
    $endValidity = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4);

    $idSeg = odbc_result ($cur, "i_Seg");
    if($idSeg == 0){
      $cc = odbc_exec($db, "select i_Seg from Inform where id=(select idAnt from Inform where id=$idInform)");
      $idSeg = odbc_result($cc, 1);
    }
    $policyKey = odbc_result($cur, "policyKey");

    // Países
    $cur = odbc_exec ($db,
		      " SELECT Distinct Country.name, Country.score".
		      " FROM Inform inf INNER JOIN".
		      " VolumeSeg v ON inf.id=v.idInform INNER JOIN".
/* 		    " Importer ON inf.id = Importer.idInform INNER JOIN". */
		      " Country ON v.idCountry = Country.id".
		      " WHERE inf.id = $idInform AND score < 8");
    $countries = "";
    $first = true;
    while (odbc_fetch_row ($cur)) {
      $countries .= ($first ? "" : ", ").odbc_result($cur, 1);
      if ($first) $first = false;
    }
    $countries .= ".";

    $cur = odbc_exec(
      $db,
      "SELECT Distinct u.login, inf.respName, inf.idRegion, inf.name, inf.txMin, inf.prMin,
            inf.cnpj, inf.ie, inf.address, inf.tel, inf.fax, inf.email, inf.ocupationContact,
            inf.city, reg.name, Sector.description, inf.warantyInterest,
            inf.periodMaxCred, Country.name, inf.cep, inf.contrat, inf.contact,
            inf.txRise, inf.i_Seg, inf.txAnalize, inf.txMonitor, inf.limPagIndeniz,
            inf.prodUnit
       FROM Users u INNER JOIN
	  Insured i ON u.id = i.idResp INNER JOIN
          Inform inf ON inf.idInsured = i.id INNER JOIN
          Region reg ON inf.idRegion = reg.id INNER JOIN
          Sector ON inf.idSector = Sector.id LEFT JOIN
          Importer ON inf.id = Importer.idInform LEFT JOIN
          Country ON Importer.idCountry = Country.id
       WHERE inf.id = $idInform");

    // tenta achar o usuário responsável
    if (odbc_fetch_row($cur)) {
      $login    = odbc_result($cur, 1);
      $respName = odbc_result($cur, 2);
      $idRegion = odbc_result($cur, 3);
      $name     = odbc_result($cur, 4);
      $txMin    = odbc_result($cur, 5);
      $prMin    = odbc_result($cur, 6);
      $cnpj     = odbc_result($cur, 7);
      $cnpj =
	substr($cnpj, 0, 2). ".".
	substr($cnpj, 2, 3). ".".
	substr($cnpj, 5, 3). "/".
	substr($cnpj, 8, 4). "-".
	substr($cnpj, 12);
      $ie       = odbc_result($cur, 8);
      $address  = odbc_result($cur, 9);
      $tel      = odbc_result($cur, 10);
      $fax      = odbc_result($cur, 11);
      $email    = odbc_result($cur, 12);
      $oContact = odbc_result($cur, 13);
      $city     = odbc_result($cur, 14);
      $uf       = substr(odbc_result($cur, 15), 0, 2);
      $descrip  = odbc_result($cur, 16);
      $interest = odbc_result($cur, 17);
      $period   = odbc_result($cur, 18);
      //$country  = odbc_result($cur, 19);
      $cep      = odbc_result($cur, 20);
      $sub = substr($cep, 0, 5);
      if(! preg_match("\.", $sub)){
	$sub = substr($sub, 0, 2). '.'. substr($sub, 2, 3);
      }else{
	$inc = 1;
	$sub = substr($cep, 0, 6);
      }
      if(! preg_match("-", $cep)){
	$cep = "$sub-". substr($cep, 5);
      }else{
	$cep = "$sub-". substr($cep, 6 + $inc);
      }

      // encontrar o número de propostas
      $contract = odbc_result($cur, 21) . "/$nProp";
      $contato  = odbc_result($cur, 22);
      $txRise   = odbc_result($cur, 23);
      $extAnalise = $numberExtensive->extensive(number_format(odbc_result($cur, "txAnalize"), 2, ".", ""), 1);
      $taxa_analise = number_format(odbc_result($cur, "txAnalize"), 2, ',', '.');
      $extMonit = $numberExtensive->extensive(number_format (odbc_result($cur, "txMonitor"), 2, ".", ""), 1);
      $taxa_monit = number_format(odbc_result($cur, "txMonitor"), 2, ',', '.');
      $limite = odbc_result($cur, "limPagIndeniz");

      $iSeg = odbc_result($cur, 'i_Seg');
      if($iSeg == 0){
	$cc = odbc_exec($db, "select i_Seg from Inform where id=(select idAnt from Inform where id=$idInform)");
	$iSeg = odbc_result($cc, 1);
      }
      $prodUnit = odbc_result($cur, 'prodUnit');
      $tx = $txMin * (1 + $txRise) * 100;
      $pr = $prMin * ($interest == 1 ? 1.04 : 1) * (1 + $txRise);

      // verifica se a apolice já existe
      /* $r = odbc_exec($dbSisSeg, "select * from Base_Calculo where c_Coface=$c_coface and n_Prop=$nProp"); */
      /* if(odbc_fetch_row($r)){ */
      /*   $ja_foi = 1; */
      /* }else{ */
      /*   $ja_foi = 0; */
      /* } */

      if($policyKey == ""){
	$ja_foi = 0;
      }else{
	$ja_foi = 1;
      }

      /*************************************************************************************/
      // gera a apolice (se ainda nao foi gerada)
      if(! $ja_foi){
	$start_aux = dmy2ymd($startValidity);
	// verifica se existe valor de compra
	$r = odbc_exec($dbSisSeg, "select v_Compra from Valor_Moeda where d_Cotacao='$dMoeda'");
	if(! odbc_fetch_row($r)){
	  $MoedaVal = substr($dMoeda, 8, 2). "/". substr($dMoeda, 5, 2). "/". substr($dMoeda, 0, 4);
	  $msg = "Não existe valor de compra para o dia $MoedaVal";
	  // echo "dMoeda $dMoeda";
	  // echo "<br>MoedaVal $MoedaVal";
	  $return_flag = 1;
	  return;
	}else{
	  $v_compra = odbc_result($r, 1);
	}

	odbc_autocommit($dbSisSeg, false);
	$ta_errado = false;

	// atualiza dados da proposta
	$end_aux = dmy2ymd($endValidity);
	$query =
	  "update Proposta set d_Aceitacao='$start_aux', d_Inicio='$start_aux', ".
	  "d_Fim='$end_aux', s_Proposta=11 where c_Coface=$c_coface and n_Prop=$nProp";
	$r = odbc_exec($dbSisSeg, $query);
	if(! $r){
	  echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
	  $ta_errado = true;
	}

	// atualiza dados da parcela
	$query =
	  "select i_Parcela, d_Venc from Parcela where i_Parcela=".
	  "(select max(i_Parcela) from Parcela where ".
	  "c_Coface=$c_coface and n_Prop=$nProp and n_Seq_Parcela=1)";

	$r = odbc_exec($dbSisSeg,$query);
	if(! $r){
	  echo odbc_errormsg($dbSisSeg);
	}
	if(odbc_fetch_row($r)){
	  $id_primeira_parcela = odbc_result($r, 1);
	  $d_venc = odbc_result($r, 2);
	}
	$query = "update Parcela set t_parcela=2 where i_Parcela=$id_primeira_parcela";
	$r = odbc_exec($dbSisSeg, $query);
	if(! $r){
	  echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
	  $ta_errado = true;
	}

	// insere nova entrada na Base de Calculo
	$query =
	  "insert into Base_Calculo (c_Coface, i_Seg, n_Prop, Tx_Moeda, d_Ini_Vig, d_Fim_Vig,".
	  "v_Premio, d_Aceitacao, n_Filial, d_Emissao, n_Sucursal, n_Moeda, n_Ramo, t_Apolice,".
	  "t_Endosso, d_Doc, n_User, s_Doc, n_Mod, p_Cobertura, d_Situacao) values (".
	  "$c_coface,".     // c_Coface
	  "$idSeg,".        // i_Seg
	  "$nProp,".        // n_Prop
	  "$v_compra,".     // taxa da moeda (Tabela Valor_Moeda.v_Compra) do dia da Aceitação(Tabela Proposta)
	  "'$start_aux',".  // início da vigência
	  "'$end_aux',".    // fim da vigência
	  "$pr,".           // valor do prêmio
	  "'$start_aux',".  // Pegar na tabela Proposta
	  "$prodUnit,".     // numero da filial
	  "getdate(),".     // Data de emissão da Apólice
	  // Dados Fixos
	  "62, 2, 49, 0, 0, getdate(), 47, 0, 0, 85, getdate())";
	$r = odbc_exec($dbSisSeg, $query);
	if(! $r){
	  echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
	  $ta_errado = true;
	}
	if($r){ // pega o id da Base de Calculo (vai ser muito usado depois)
	  $r = odbc_exec($dbSisSeg, "select max(i_BC) from Base_Calculo where c_Coface=$c_coface");
	  if(! $r){
	    echo odbc_errormsg($dbSisSeg);
	  }
	  if(odbc_fetch_row($r)){
	    $idBC = odbc_result($r, 1);
	  }
	}

	// pega o maior id de apolice até entao e incrementa
	$rr = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice");
	if(! $rr){
	  echo odbc_errormsg($dbSisSeg);
	}
	if(odbc_fetch_row($rr)){
	  $max = odbc_result($rr, 1);
	  $max++;
	}
	// finalmente gera a apolice...
	$query =
	  "insert into Apolice (n_Sucursal, n_Ramo, i_Seg, n_Apolice) ".
	  "values (62, 49, $iSeg, $max)"; // 62 ao inves de $prodUnit
	$r = odbc_exec($dbSisSeg, $query);
	if(! $r) {
	  echo "nao consegui gerar apolice $max: $query<br>". odbc_errormsg();
	  $ta_errado = true;
	}

	if($idAnt){ // se for renovacao
	  if(! odbc_exec($dbSisSeg, "update Base_Calculo set Renova_n_Apolice=$max, t_Apolice=1 where i_BC=$idBC")){
	    echo "Nao foi possivel atualizar Base_Calculo para apolice renovada";
	    $ta_errado = true;
	  }
	  $query =
	    "update Proposta set Renova = 1, n_Apolice_Renova=$max".
	    " where c_Coface=$c_coface and n_Prop=$nProp";
	  $r = odbc_exec($dbSisSeg, $query);
	  if(! $r){
	    echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
	    $ta_errado = true;
	  }
	}

	// insere dados do endosso
	$query =
	  "insert into Endosso (i_BC, n_Apolice, ".
	  "n_Sucursal, n_Ramo, n_Endosso, d_Endosso, n_User) ".
	  "values (".
	  "$idBC,".    // i_BC
	  "$max,".     // número da Apólice
	  // Valores Fixos
	  "62, 49, 0, getdate(), 47)";
	$r = odbc_exec($dbSisSeg, $query);
	if(! $r){
	  echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
	  $ta_errado = true;
	}

	if($ta_errado){
	  odbc_rollback($dbSisSeg);
	}else{
	  odbc_commit($dbSisSeg);
	}
	odbc_autocommit($dbSisSeg, true);
      }
    } // fim da geracao da apolice
    /*************************************************************************************/

    /*     if(!odbc_exec($dbSisSeg, */
    /* 		  "UPDATE Proposta SET s_Proposta=11 WHERE i_Seg=$iSeg AND n_Prop IN ". */
    /* 		  "(SELECT MAX(n_Prop) FROM Proposta WHERE i_Seg=$iSeg)")){ */
    /*       die("nao consegui atualizar proposta"); */
    /*     } */

    $rr = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$iSeg");
    if(odbc_fetch_row($rr)){
      $apoNum = odbc_result($rr, 1);
    }
    $apoNum = sprintf("062%06d", $apoNum);
    if($prodUnit != 62){
      $apoNum .= "/$prodUnit";
    }

    $end = "$address - $city - $uf";
    $prMin = number_format($prMin, 2, ',', '.');
    //$pr = number_format($pr, 2, ',', '.');
    $tx = number_format($tx, 3, '.', '');
    $txMin = number_format($txMin, 3, '.', '');
    if ($startValidity == ""){
      $today = "";
    }else{
      $today = "$startValidity à $endValidity";
    }

    $data = data_string($startValidity);
    if (!$t_Venc){
		switch($num_parcelas){ // periodo em meses
		case 2:
		  $periodo = 3;
		  break;
		case 4:
		  $periodo = 3;
		  break;
		case 6:
		  $periodo = 2;
		  break;
		case 7:
		case 10:
		  $periodo = 1;
		  break;
		}
	}else{
	   	 $periodo = $t_Venc;
	}
  }

  $valPar = number_format($pr / $num_parcelas, 0, "", "");
  $parc = $valPar;
  $pr = $num_parcelas * $valPar;
  $valExt = $numberExtensive->extensive(number_format($pr, 2, ".", ""), 2);

  // gera os pdfs
  for($parcela = 2; $parcela <= $num_parcelas; $parcela++){
    require("segParc.php");
    require("parcela.php");
  }

//$total = $num_parcelas * $parcExt;
//$valor_primeira_parcela = $parcExt;
  $total = $num_parcelas * $valPar;
  $valor_primeira_parcela = $valPar;

// if($total != $valor_total && $num_parcelas > 1){
//   $dif = $total - $valor_total;
//   $valor_primeira_parcela = ($parcExt + $dif);
// }else{
//   $valor_primeira_parcela = $parcExt;
// }

// Encontrar o vencimento da primeira parcela
  if(! $vencFirst){
    $cur = odbc_exec($dbSisSeg,
		     "SELECT d_Venc FROM Parcela WHERE i_Seg=$idSeg and s_Parcela=1 and ".
		     "c_Coface=$c_coface and n_Prop=$nProp ORDER BY d_Venc");
    if(odbc_fetch_row($cur)){
      $data = odbc_result($cur, "d_Venc");
      if($data){
	$vencFirst = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4);
      }
    }
  }

  require("apolice.php");
  require("apolice_real.php");

  if(! $ja_foi){
    odbc_autocommit($dbSisSeg, false);
    $ta_errado = false;
    // atualiza dados da parcela e da Base de Calculo
    $query =
      "update Parcela set n_Apolice=$max, i_BC=$idBC, n_Endosso=0 ".
      "where i_Seg=$idSeg and n_Prop=$nProp";
    $r = odbc_exec($dbSisSeg, $query);
    if(! $r){
      echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
      $ta_errado = true;
    }

    $query =
      "update Base_Calculo set n_Apolice=$max, s_Doc=1, n_Endosso=0 ".
      "where i_Seg=$idSeg and n_Prop=$nProp";
    $r = odbc_exec($dbSisSeg, $query);
    if(! $r){
      echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
      $ta_errado = true;
    }

    $d_venc = conserta($d_venc);

    // inclui Prêmio de Resseguro na tabela PagRec
    $query =
      "insert into PagRec (i_Seg, c_Coface, n_Prop, i_BC, i_Parcela, n_Apolice, d_Vencimento, v_Documento,".
      "c_Seg, n_Seq_Parcela, n_Ramo, n_Sucursal, n_Endosso, n_Moeda, t_Doc, s_Pagamento, d_Situacao, d_Sistema)".
      "values (".
      "$idSeg,".                // i_Seg
      "$c_coface,".             // código da Coface
      "$nProp,".                // n_Prop
      "$idBC,".                 // pegar i_BC na tabela Base_Calculo
      "$id_primeira_parcela,".  // pegar o i_Parcela da 1a parcela, que já deve estar com s_Parcela = 2(paga)
      "$max,".                  // número da apólice
      "'$d_venc',".             // dia=15, mes=3 meses na frente no mês de pagamento
      (-$parc * 0.8). ",".      // 80% do valor da 1a parcela já paga
      // Dados Fixos
      "1, 1, 49, 62, 0, 2, 1001, 1, getdate(), getdate())";
    $r = odbc_exec($dbSisSeg, $query);
    if(! $r){
      echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
      $ta_errado = true;
    }

    // inclui Comissão de Resseguro na tabela PagRec
    $query =
      "insert into PagRec (i_Seg, c_Coface, n_Prop, i_BC, i_Parcela, n_Apolice, d_Vencimento, v_Documento,".
      "c_Seg, n_Seq_Parcela, n_Ramo, n_Sucursal, n_Endosso, n_Moeda, t_Doc, s_Pagamento, d_Situacao, d_Sistema)".
      "values (".
      "$idSeg,".                // i_Seg
      "$c_coface,".             // código da Coface
      "$nProp,".                // n_Prop
      "$idBC,".                 // pegar i_BC na tabela Base_Calculo
      "$id_primeira_parcela,".  // pegar o i_Parcela da 1a parcela, que já deve estar com s_Parcela = 2(paga)
      "$max,".                  // número da apólice
      "'$d_venc',".             // dia=15, mes=3 meses na frente no mês de pagamento
      ($parc * 0.24). ",".      // 24% do valor da 1a parcela já paga
      // Dados Fixos
      "1, 1, 49, 62, 0, 2, 12, 1, getdate(), getdate())";
    $r = odbc_exec($dbSisSeg, $query);
    if(! $r){
      echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
      $ta_errado = true;
    }

    // inclui dados em Resseguro
    $query =
      "insert into Resseguro (i_BC, c_Seg, p_Seguradora, p_Resseguro, p_Com_Resseguro, v_Max_Retencao)".
      "values (".
      "$idBC,". // i_BC
		// valores fixos
      "1, 100, 80, 30, 80000)";
    $r = odbc_exec($dbSisSeg, $query);
    if(! $r){
      echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
      $ta_errado = true;
    }

    // inclui dados em Parcela_Resseguro
    $x = odbc_exec($dbSisSeg, "select * from Parcela_Resseguro where i_Parcela=$id_primeira_parcela");
    if(! odbc_fetch_row($x)){
      $query =
 	"insert into Parcela_Resseguro (i_Parcela, v_Resseguro, v_Com_Resseguro, d_Venc, s_PR,".
	"c_Seg, d_Situacao) values ( ".
	"$id_primeira_parcela,".   // i_Parcela
	($parc * 0.8). ",".        // 80% do valor da parcela
	($parc * 0.24). ",".       // 24% do valor da parcela
	"'$d_venc',".              // dia=15, mes=3 meses na frente no mês de pagamento
	"1,".                      // Colocar 1 para a 1a parcela e 0 para as outras
	// valores fixos
	"1, getdate())";
      $r = odbc_exec($dbSisSeg, $query);
      if(! $r){
	echo "erro: $query<br>". odbc_errormsg($dbSisSeg);
	$ta_errado = true;
      }
    }
    if($ta_errado){
      odbc_rollback($dbSisSeg);
    }else{
      odbc_commit($dbSisSeg);
    }
    odbc_autocommit($dbSisSeg, true);
  }
  odbc_exec($db, "update Inform set policyKey='$key' where id=". $field->getField("idInform"));

  require("condpart.php");
  if($interest){
    require("condjuros.php");
  }
  require("carta.php");
  require("carta_credito.php");
}
?>
