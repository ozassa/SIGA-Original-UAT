<?php

  $idDve = isset($_REQUEST['idDve']) ? $_REQUEST['idDve'] : false;
  $idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
  $idInsured = isset($_REQUEST['idInsured']) ? $_REQUEST['idInsured'] : false;
  $data = date("Y/m/d H:i:s");

  $key = session_id().time();
  $pdf_liquid = $key.'LiquidacaoFat.pdf';

  if ($idDve) {
    $sqlInto = "INSERT INTO Liquidacao_Faturamento (i_Inform, i_Usuario, d_Liquidacao, Nome_Documento) 
            VALUES (?, ?, getdate(), ?)";
$stmt = odbc_prepare($db, $sqlInto);
$params = [$idInform, $userID, $pdf_liquid];
odbc_execute($stmt, $params);


    $sql = "SELECT IDENT_CURRENT('Liquidacao_Faturamento') AS id";
    $rsSql = odbc_exec($db, $sql);

    $Id_Liquidacao = odbc_result($rsSql, 'id');

    foreach ($idDve as $id) {
     $sqlDet = "INSERT INTO Liquidacao_Faturamento_Detalhes (i_Liquidacao, i_DVE_Details) 
           VALUES (?, ?)";
      $stmt = odbc_prepare($db, $sqlDet);
      $params = [$Id_Liquidacao, $id];
      odbc_execute($stmt, $params);

    }
  }

  if ($idDve) {
    $msg = "Alteração realizada com sucesso.";
  } else {
    $msg = "Nenhum registro foi alterado.";
  }

  $msg = rawurlencode($msg);

  require_once('DVELiquidacaoPdf.php');

  //$location = "Location: ".$hostImagem."src/role/dve/Dve.php?comm=DVELiquidacao&msg=".$msg."&pdf=".$pdf_liquid."&idInform=".$idInform."&idInsured=".$idInsured;
  $location = "Location: ../dve/Dve.php?comm=DVELiquidacao&msg=".$msg."&pdf=".$pdf_liquid."&idInform=".$idInform."&idInsured=".$idInsured;
  header($location);die;