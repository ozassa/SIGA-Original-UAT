<?php 

 session_start();
 $userID = $_SESSION['userID'];  
// extract($_REQUEST);
 


if(! function_exists('ymd2dmy')){
  // converte a data de yyyy-mm-dd para dd/mm/yyyy
  function ymd2dmy($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}

require_once("../rolePrefix.php");

if($comm == 'view'){
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
}else if ($comm == "dados") {
  // endosso de dados cadastrais
  require_once("dados.php");
  if($inativo){
    $msg = 'Endosso não está ativo';
    $title = 'Notificações';
    $content = "../notification/interf/ViewBox.php";
  }else{
    $title = 'Solicitação de Endosso';
    $content = "../endosso/interf/Dados.php";
  }
}else if($comm == 'cancelar'){
  // cancelar endosso
  $status = 3;
  require_once("changeStatus.php");
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
}else if($comm == 'recebida'){
  // proposta recebida
  $status = 2;
  require_once("changeStatus.php");
  $title = 'Proposta recebida';
  $content = "../endosso/interf/recebida.php";
}else if($comm == 'reemitir'){

}else if ($comm == "natureza") {
  // endosso de natureza da operacao
  require_once("natureza.php");
  if($inativo){
    $msg = 'Endosso não está ativo';
    $title = 'Notificações';
    $content = "../notification/interf/ViewBox.php";
  }else{
    $title = 'Solicitação de Endosso';
    $content = "../endosso/interf/Natureza.php";
  }
}else if($comm == 'natRecebida'){
  $status = 1;
  require_once("changeStatus.php");
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
}else if($comm == 'tarifacao'){
  $status = 4;
  require_once("changeStatus.php");
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
}else if($comm == 'prMin'){
  // endosso de premio minimo
  require_once("premio.php");
  $title = 'Endosso de Prêmio Mínimo';
  $content = '../endosso/interf/Premio.php';
}else if($comm == 'sendProp'){
  // enviar proposta
  require_once("sendProp.php");
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
}else if($comm == 'natOperPrMin'){
  require_once("natureza.php");
  $title = 'Endosso de Natureza de Operação';
  $content = '../endosso/interf/Natureza.php';
}else if($comm == 'naogeradoPA'){
  $title = 'Endosso de Parcela de Ajuste';
  $content = '../endosso/interf/naogeradoPA.php';
}else if($comm == 'propRecebida'){
  require_once("propRecebidaPM.php");
  $title = 'Proposta Recebida';
  $content = '../endosso/interf/PropRecebidaPM.php';
}else if($comm == 'propRecebidaPM'){
  require_once("propRecebidaPM.php");
  $title = 'Endosso de Prêmio Mínimo Emitido';
  $content = '../endosso/interf/propRecebidaPM.php';
}else if($comm == 'parcela'){
  // endosso de parcela de ajuste
  require_once('parcelaAjuste.php');
  $title = 'Endosso de Parcela de Ajuste';
  $content = '../endosso/interf/ParcelaAjuste.php';
}else if($comm == 'gerar'){
  require_once('gerar.php');
  $title = 'Gerar Endosso';
  $content = '../endosso/interf/Gerar.php';

// gera a parcela de ajuste
}else if($comm == 'geraPA'){
  require_once('geraPA.php');
  $title = 'Parcela de Ajuste';
  $content = '../endosso/interf/GeraPA.php';
}else if($comm == 'razao'){
  require_once('razao.php');
  $title = 'Endosso de Dados Cadastrais';
  $content = "../endosso/interf/Dados.php";
}else if($comm == 'killnotif'){
  $notif->doneUser($idNotification, $db);
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
}

require_once("../../../home.php");

?>
