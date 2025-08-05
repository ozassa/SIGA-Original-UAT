<?php /*
//####### ini ####### adicionado por eliel vieira - elumini - em 25/03/2008
// referente a demanda 1374 - SAD
//
// solicitacao: Caso o segurado esteja com DVE em atraso, não deverá ser permitido
// a ele, na tela, executar nenhuma atividade. Somente o envio de DVE.
//
// complemento da demanda:
// criar campo unblock_dve na tabela inform com valor default 0, para verificacao
// se deve (1) ou nao (0) ocultar campos para envio de dve em atraso
//
// arquivo utilizado por:
// ../client/interf/viewclient.php
// ../dve/includeimporter.php
//
*/

//verifica se existe habilitacao para bloqueio dos campos
$sql_unb = "
            select unblock_dve from Inform
             where id = $idInform
               and state in (10,11)
           ";

$rs_unb = odbc_exec($db, $sql_unb);
$unblock_dve = 1;
if (($rs_unb)) {
  $unblock_dve = odbc_result($rs_unb,1);
}

// modifica visualizacao da tela para dves em atraso
// desde que seja apolice e/ou que tenha sido emitidas as dves (no minimo 2)
// com o campos de exibicao definido com sim (1)
if ($unblock_dve==0) {


  //verifica os tipos de apolice para verificacao
  if (($statusCl == 10 || $statusCl == 11) && $codProd ) {


    $rs_validity   = odbc_exec($db, "SELECT startValidity FROM Inform WHERE id = $idInform");
    $startValidity = odbc_result($rs_validity, 'startValidity');
    $startValidity = substr($startValidity,0,10);

    $inicio = ymd2dmy2($startValidity);

    $num_dves = (odbc_result(odbc_exec($db, "select max(num) from DVE where idInform = $idInform"), 1));

    $id_atrz   = 0;
    $cont_atrz = 1;
    for($num = 1; $num <= $num_dves; $num++){

      $fim = getEndDate2($inicio, 1);

      $sql =  "SELECT state ".
              "  FROM DVE   ".
              " WHERE idInform = $idInform and num = $num";

      $cur=odbc_exec($db,$sql);

      $state = odbc_result($cur,"state");

      $vencimentoDve = mkdate (substr($fim, 6, 4), substr($fim, 3, 2), substr($fim, 0, 2) + 16);
      if($state == 1 && getTimeStamp2($vencimentoDve) < time()){
        //define mais de uma dve em atraso
        if ($cont_atrz>1) {
          $id_atrz = $num;
          break;
        }
        $cont_atrz++;
      }

      $inicio = getEndDate2($inicio, 1, 1);
      if(getTimeStamp2($inicio) > time()){
        break;
      }

    }

    /*
    //sql para selecionar dves em atraso
    $sql_atrz = " select a.i_Seg, a.nProp, a.id as idInform, a.name, b.num, b.id as idDVE, b.state, b.inicio, isnull(( select sum(dt.totalEmbarcado)
                    from DVEDetails dt
                   where dt.idDVE = b.id
                     and isnull(dt.state,1) = 1 ),0) totalEmbarcado,
                         isnull(( select sum(dt.totalEmbarcado)
                                    from DVEDetails dt, DVE d
                                   where dt.idDVE = d.id
                                     and d.idInform = a.id
                                     and d.num <= b.num
                                     and isnull(dt.state,1) = 1 ),0) totalEmbarcadoAte
                                    FROM Inform a
                                   INNER JOIN DVE b ON a.id = b.idInform
                                   WHERE (a.state = 10 or a.state = 11)
                                     and (a.endValidity > getdate() - 60)
                                     AND a.codProd = 1
                                     AND ( b.state = 1 )
                                     AND a.id = $idInform
                                   GROUP BY a.i_Seg, a.nProp, a.id , a.name, b.num, b.id, b.state, b.inicio
                                   ORDER BY a.name, b.num
                 ";
    $rs_atrz = odbc_exec($db, $sql_atrz);

    $id_atrz = 0;
    $cont_atrz = 1;
    if (($rs_atrz)) {
     while(odbc_fetch_row($rs_atrz)){
      //define id para mais de uma dve em atraso
      if ($cont_atrz>1) {
        $id_atrz =  trim(odbc_result($rs_atrz,1));
		break;
      }
      $cont_atrz++;
     }
	}
	*/

    //se foi encontradas mais de um (01) dve
    if (($id_atrz!=0)) {
      $dspl_pri = "none";
      $dspl_seg = "block";
      $dspl_ter = "none";
      $dspl_qua = "block";
	}

  }
}

//####### end ####### adicionado por eliel vieira - elumini - em 02/04/2008



  function getEndDate2($d, $n, $c = 0){

    global $idDVE, $db, $idInform;
    $rs = odbc_exec($db, "select startValidity, tipoDve from Inform where id=$idInform");

    $nv_data = substr(odbc_result($rs, 1),0,10);

    $tipodve = odbc_result($rs, 2);

    $num_dves = (odbc_result(odbc_exec($db, "select max(num) from DVE where idInform = $idInform"), 1));

    if ($idDVE) {
      $num = odbc_result(odbc_exec($db, "select num from DVE where id=$idDVE"), 1);
    }

    if($num != $num_dves) {
      if(preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $d, $v)) {
        if ($tipodve == 1) {
          // Dve do tipo Trimestral
          return date("d/m/Y", mktime(0, 0, 0, $v[2] + 3, $c, $v[3]));
        } else {
          // Dve do tipo Mensal
          return date("d/m/Y", mktime(0, 0, 0, $v[2] + 1, $c, $v[3]));
        }
      }
    } else {
      $end = odbc_result(odbc_exec($db, "select endValidity from Inform where id=$idInform"), 1);
      return ymd2dmy2($end);
    }
  }

  function getTimeStamp2($date){
    if(preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})/', $date, $res)){
      return mktime(0, 0, 0, $res[2], $res[3], $res[1]);
    }else if(preg_match('^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})', $date, $res)){
      return mktime(0, 0, 0, $res[2], $res[1], $res[3]);
    }
  }

  function ymd2dmy2($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
  }

?>