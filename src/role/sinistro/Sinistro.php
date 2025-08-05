<?php 

session_start();
$userID = $_SESSION['userID'];  
//extract($_REQUEST);


require_once("../rolePrefix.php");

if($comm == 'view'){
  $title = 'Sinistro';
 // echo "Aqui";
  $content = "../sinistro/interf/viewAviso.php";
 // echo "Depois";
 // require_once ("../../../../site/func/nomenu.php");
  require_once("../../../home.php");
}else if($comm == 'voltar'){
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
  require_once("../../../home.php");
}else if($comm == 'criarSinistro'){
  require_once('criarSinistro.php');
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
  require_once("../../../home.php");
}else if($comm == 'avisoSinistro'){
  require_once('avisoSinistro.php');
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
  require_once("../../../home.php");
}else if($comm == 'cancelado'){
  require_once('cancelado.php');
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
  require_once("../../../home.php");
}else if($comm == 'atualizaDados'){
  require_once('atualizaDados.php');
  $title = 'Sinistro';
  $content = "../sinistro/interf/viewAviso.php";
  require_once("../../../home.php");
  //  require_once ("../../../../site/informe/index.php");
}else if($comm == 'AltFatura'){
  require_once('altFatura.php');
  $title = 'Sinistro';
  $content = "../sinistro/interf/viewAviso.php";
  require_once("../../../home.php");
  //  require_once ("../../../../site/informe/index.php");
}else if($comm == 'naoAceito'){
  require_once('naoAceito.php');
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
  require_once("../../../home.php");
//}else if($comm == 'RemFatura'){
//  require_once('remFatura.php');
//  $title = 'Aviso de Sinistro';
//  $content = "../client/interf/geraravisosinistro.php";
//    if($user->hasRole('client')){
//      return ("../../../../site/informe/index.php");
//    }else{
//      return ("../../../../site/func/index.php");
//    }
}else if($comm == 'indenizar'){
  require_once('indenizar.php');
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
  require_once("../../../home.php");
}else if($comm == 'recuperacao'){
  $title = 'Cadastro de Recuperações';
  $content = "../sinistro/interf/recuperacao.php";
  require_once("../../../home.php");
}else if($comm == 'voltarFunc'){
  $title = '';
  $content = "../searchClient/interf/InformRes.php";
  require_once ("../searchClient/interf/nameClient.php");
  require_once("../../../home.php");
}else if($comm == 'voltarCliente'){
  $title = '';
  $content = "../client/interf/ViewClient.php";
  require_once("../../../home.php");
}else if($comm == 'consultaSinistro'){
  $title = 'Consultar Sinistro';
  $content = "../sinistro/interf/consultaSinistro.php";
  require_once("../../../home.php");
}else if($comm == 'detalhesSinistro'){
  $title = 'Consultar Sinistro';
  $content = "../sinistro/interf/detalhesSinistro.php";  
  if ($role["client"]){
     require_once("../../../home.php");
  }else{
      require_once("../../../home.php");
  }
}else if($comm == 'histSinistro'){
  $title = 'Consultar Sinistro';
  $content = "../sinistro/interf/histSinistro.php";
  require_once("../../../home.php");
}else if($comm == 'obs'){
  require_once('incluirObs.php');
  $title = 'Consultar Sinistro';
  $content = "../sinistro/interf/histSinistro.php";
  require_once("../../../home.php");
}else if($comm == 'consultaSinistroFunc'){
  $title = 'Consultar Sinistro';
  $content = "../sinistro/interf/consultaSinistro.php";
  require_once("../../../home.php");
}else if($comm == 'pagInd'){
  require_once('calculaInd.php');
  $title = 'Pagamento de Indenização';
  $content = "../sinistro/interf/indenizacao.php";
  require_once("../../../home.php");
}else if($comm == 'recuperacaoSQL'){
  require_once('recuperacaoSQL.php');
  $title = 'Notificações';
  $content = "../sinistro/interf/recuperacao.php";
  //$content = "../notification/interf/ViewBox.php";
  require_once("../../../home.php");
}else if($comm == 'suspenso'){
  require_once('sinistroSuspenso.php');
  $title = 'Notificações';
  $content = "../notification/interf/ViewBox.php";
  require_once("../../../home.php");
}else if ($comm == "inserirFatura") {
  require_once ("../client/geraravisosinistroSQL.php");
  $title = 'Sinistro';
  $content = "../sinistro/interf/viewAviso.php";
  require_once("../../../home.php");
  //  require_once ("../../../../site/informe/index.php");
}else if ($comm == "valoresCobertos") {
  require_once ("valoresCobertos.php");
  $title = 'Sinistro';
  $content = "../sinistro/interf/viewAviso.php";
  require_once("../../../home.php");
  //  require_once ("../../../../site/informe/index.php");
}
?>
