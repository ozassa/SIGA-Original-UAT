<?php  //Alterado Hicom - 02/12/04 (Gustavo) - link p/ PA
//Alterado Hicom - 15/12/04 (Gustavo) - |link p/ Alteração de Opção de Juros de Mora
//Alterado Hicom - 17/01/05 (Gustavo) - link p/ alterar informações gerais

//####### ini ####### adicionado por eliel vieira - elumini - em 25/04/2008

  if(!isset($_SESSION)){
    session_set_cookie_params([
    'secure' => true,
    'httponly' => true
]);
session_start();
  }
  $userID = $_SESSION['userID'];  
  //extract($_REQUEST);
  //error_log("Conteúdo de client client:");
  //error_log(print_r($_REQUEST, true));

    $comm = isset($_REQUEST['comm']) ? trim($_REQUEST['comm']) : null;
    $comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');

    $idInform = isset($_REQUEST['idInform']) ? trim($_REQUEST['idInform']) : null;
    $idInform = htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8');
    $idInform = preg_replace('/\D/', '', $idInform);
    $idInform = (int)$idInform;

    $origem = isset($_REQUEST['origem']) ? trim($_REQUEST['origem']) : null;
   # $origem = htmlspecialchars($origem, ENT_QUOTES, 'UTF-8');
    $origem = htmlspecialchars(trim($_REQUEST['origem'] ?? ''), ENT_QUOTES);


  //error_log($comm);
  //error_log($idInform);
  //error_log($origem);


if(! function_exists('ymd2dmy')){
  // converte a data de yyyy-mm-dd para dd/mm/yyyy
  function ymd2dmy($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d ?? '', $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}

//die($comm);

if(! function_exists('getInterf')){
  function getInterf($user){
    $role['client'] = isset($role['client']) ? $role['client'] : false;
    if($role['client']){
      //return "../../../../site/informe/index.php";
    return("../../../home.php");
    }else{
      //return "../../../../site/func/index.php";
    return("../../../home.php");
    }
  }
}

if(! function_exists('getInterfB')){
  function getInterfB($user){
    if($role['client']){
      return("../../../home.php");
    }else{
      return("../../../home.php");
    }
  }
}

require_once("../rolePrefix.php");

if(!$comm){
    $comm = $_REQUEST["comm"];  
}


 /*if(count($_POST) > 2){
  // armazena dados do log
  $idImporter = isset($idImporter) ? $idImporter : 0;
  $tem = $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db,1,'','Inform','');  
  $tem1 = $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db,1,'','Importer',(isset($_REQUEST['idBuyer']) ? $_REQUEST['idBuyer'] : $idImporter)); 
 }*/




$opc = isset($opc) ? $opc : false;
if(!$opc){
  $_REQUEST['opc'] = isset($_REQUEST['opc']) ? $_REQUEST['opc'] : false;
  $opc = $_REQUEST['opc'];
}


$qry = "SELECT a.id, a.name, c.login 
        FROM Role a
        INNER JOIN UserRole b ON b.idRole = a.id
        INNER JOIN Users c ON c.id = b.idUser
        WHERE c.id = ? AND c.perfil = ?
        ORDER BY UPPER(a.name), c.login";

$stmt = odbc_prepare($db, $qry);

$params = [$_SESSION['userID'], $_SESSION['pefil']];
odbc_execute($stmt, $params);

$x = 0;
$role = [];
while (odbc_fetch_row($stmt)) {
    $x++;
    $name = odbc_result($stmt, 'name');
    $id = odbc_result($stmt, 'id');
    $role[$name] = $id . '<br>';
}

odbc_free_result($stmt);




if ($comm == "voltar") {
  $title = "Informações do Segurado";
  $content = "../client/interf/ViewClient.php";
  require_once(getInterf($user));


//monta a tela da mudança de credito
//chamada para ficha indicativa de limites de créditos
}else if ($comm == "ficha") { 
  $title = "Ficha Indicativa de Limites de Crédito";
  require_once("ClientAlterCredit.php");  
  $content = "../client/interf/clientAlterCredit.php";
  require_once("../../../home.php");
  
// gera o pdf de Ficha de Aprovacao de Limites de Credito
} else if ($comm == 'geraFicha'){
  $title = "Ficha gerada";
  require_once("ClientAlterCredit.php");
  $content = "../client/interf/geraFicha.php";
  
  //require ("../functionary/interf/Main.php");
  require_once("../../../home.php");

} else if ($comm == "alterCredit") {
  $alter = 1;
  $title = "Alteração de Limites de Crédito";
  require ("ClientAlterCredit.php");
  $content = "../client/interf/clientAlterCredit.php";
  require_once("../../../home.php");

} else if ($comm == "alterCreditImporter") {
  require ("AlterCreditImporter.php");
  $idInform = $field->getField("idInform");
  $comm == "open";

  require ("Client.php");

} else if ($comm == "alterLim") {  
  require_once ("AlterLim.php");  
  $idInform = $field->getField("idInform");  
  $comm = "alterImporter"; 
   
  $title = "Compradores";
  $action = 'exclude';
  $content = "../client/interf/alterImporter.php";  
  require_once("../../../home.php");
  

} else if ($comm == "alterDivulga") {

  require ("AlterDivulga.php");
  $idInform = $field->getField("idInform");
  $comm = "alterImporter";
  $action = "exclude";
  require ("Client.php");

// fim Hicom

} else if ($comm == "reportImporter") {
   //$comm == "reportImporter";
  
   require_once ("../credit/Credit.php");

}else if($comm == "addAddress"){ // adicionar endereço a um importador
  require ("addAddress.php");
  $title = "Atualizar ou Incluir Endereços";
  $content = "../client/interf/addAddress.php";
  require_once("../../../home.php");

}else if($comm == "alterarDados"){ // alteração de dados de um importador
  require ("addAddress.php");
  $title = "Alterar Dados do Comprador";
  $content = "interf/alterImporter.php";
  require_once("../../../home.php");

}else if($comm == "alterarDadosSQL"){ // alteração de dados de um importador Update
  require ("alterarDadosSQL.php");
  require ("interf/alterImporter.php");

//sinistro
} else if ($comm == "avisosinistro") {
  $title = 'Aviso de Sinistro';
  $content = "../client/interf/avisosinistro.php";
  require(getInterf($user));

}else if($comm == 'RemFatura'){
  require('../sinistro/remFatura.php');
  $title = 'Aviso de Sinistro';
    if($role['client']){
      $content = "../client/interf/geraravisosinistro.php";
    } else {
      $content = "../sinistro/interf/viewAviso.php";
    }
  require(getInterfB($user));

} else if ($comm == "geraravisosinistro") {
  require ("avisoSinistro.php");
  $title = 'Aviso de Sinistro';
  $content = "../client/interf/geraravisosinistro.php";
  require(getInterf($user));

} else if ($comm == "confirmaAvisosinistro") {
  $title = 'Aviso de Sinistro';
  $content = "../client/interf/confirmaAvisosinistro.php";
  require(getInterf($user));

} else if ($comm == "geraravisosinistroSQL") {
  require ("geraravisosinistroSQL.php");
  $title = 'Aviso de Sinistro';
  $content = "../client/interf/geraravisosinistro.php";
  require(getInterf($user));

} else if ($comm == "gerarNotf") {
  require ("gerarNotf.php");
  $title = 'Aviso de Sinistro';
  if($sol == "banco"){
   $content = "../cessao/interf/consultaImp.php";
  }else{
   $content = "../client/interf/avisosinistro.php";
  }
  require(getInterf($user));


//cessão de direito
} else if ($comm == "cessao") {
  $title = 'Cessão de Direito - Emissão de Cessão de Direito';
  $content = "../client/interf/cessao.php";
  require(getInterf($user));

//juros de mora
}else if($comm == 'jurosMora'){
  $title = 'Condição Especial Juros de Mora';
  $content = '../client/interf/jurosMora.php';
  require(getInterf($user));

}else if($comm == 'condEsp'){
  $title = 'Condição Especial Juros de Mora';
  $content = '../client/interf/condEsp.php';
  require(getInterf($user));

}else if($comm == 'envNotfJuros'){
  require ("dataVenc.php");
  require ("envNotfJuros.php");
  $title = 'Condição Especial Juros de Mora';
  $content = "../client/interf/ViewClient.php";
  require(getInterf($user));

//endosso
}else if($comm == 'ConsultaEndosso'){
  $title = 'Consulta de Endosso';
  $content = '../client/interf/consultaEndosso.php';
  require(getInterf($user));

} else if ($comm == "endosso") {
  $title = 'Endosso';
  $content = "../client/interf/endosso.php";
  
  require(getInterf($user));

} else if ($comm == "emitirDadosCadastrais") {
  require ("dadosCadastrais.php");
  if($volta){
    $title = 'Endosso de Dados Cadastrais';
    $content = "../client/interf/dadosCadastrais.php";
  }else if($solic == 2){ // se foi o cliente que solicitou o endosso
    $title = 'Endosso';
    $content = "../client/interf/endosso.php";
  }else{
    $title = 'Notificações';
    $content = "../notification/interf/ViewBox.php";
  }
  require(getInterf($user));

} else if ($comm == "solEndosso") {
  require ("/interf/solEndosso.php");

} else if ($comm == "back") {
  $title = 'Endosso';
  $content = "../client/interf/endosso.php";
  require(getInterf($user));

} else if ($comm == "alterNatureza") {
  require ("alterNatureza.php");
  if($solic == 2){ // se foi o cliente que solicitou o endosso
    $title = 'Endosso';
    $content = "../client/interf/endosso.php";
  }else{
    $title = 'Notificações';
    $content = "../notification/interf/ViewBox.php";
  }
  require(getInterf($user));

} else if ($comm == "newEndosso") {

  switch ($tipo){
  case 0: $title = 'Endosso'; $content = "../client/interf/endosso.php"; break;
  case 1: $title = 'Endosso de Dados Cadastrais'; $content = "../client/interf/dadosCadastrais.php"; break;
  case 2: $title = 'Endosso de Natureza da Operação'; $content = "../client/interf/naturezaOp.php"; break;
  //case 3: $content = ""; break;
  case 3: $title = 'Endosso de Prêmio Mínimo'; $content = "../executive/interf/endossoPM.php"; break;
  case 4: $title = 'Endosso de Prêmio Mínimo'; $content = "../executive/interf/endossoPM.php"; break;
  case 5: $title = 'Endosso de Vigência'; $content = "../executive/interf/endossoPM.php"; break;
  case 6: $title = 'Endosso de Vigência'; $content = "../executive/interf/endossoPM.php"; break;
  }
  require(getInterf($user));

//monta a tela de voltar
} else if ($comm == "back") {
  require("http://www.coface.com.br");

//monta a tela o sql que atualiza o saldo de importador
} else if ($comm == "open") {
   
  $idInform = $field->getField("idInform");
  require ("query.php");

  require ("verifyAnt.php");
  $title = 'Informações do Segurado';
  $content = "../client/interf/ViewClient.php";
  
  require_once("../../../home.php");

}
//Alterado por Michel Saddock 16/10/2006
else if ($comm == "changePassword")
{
  //echo $_GET['opc'];
  //break;
  
  if(!isset($_GET['opc']))
  {
    $opc = "senha";
  }
  
  if((check_menu(["client"], $role) || $_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') || $_SESSION['pefil'] != "CO")
  {
     $tipoCli = "comum";
  }
    //print '?'.$tipoCli;
   $title = "Alterar ".$opc;
   $content = "../client/interf/AlterLog.php";
   require_once("../../../home.php");

} else if ($comm == "alterImporter"){
  $alter = 1;
 // die($comm);
  
  require_once("ClientAlterCredit.php");
  
  //$action='exclude';
  
  $title = "Compradores";
  $content = "../client/interf/alterImporter.php";
 
  require_once("../../../home.php");

} else if ($comm == "insertImporter") { //botão incluir
  require ("insertImporter.php");
  
  //$comm = "alterImporter";  
  $comm = "open";  
  //$action = 'include';
  
  //die($comm);
  //require ("Client.php");Client.php?comm=alterImporter&idInform=6885&action=include
  //$content = "../Client.php?comm=alterImporter&idInform=$idInform&action=include";
  $content = "../client/interf/alterImporter.php";
  require_once("../../../home.php");
  
 
} else if ($comm == "remove") {
  require ("remove.php");
  //$comm = "alterImporter";
  $comm = "open";
  //require ("Client.php");
  $content = "../client/interf/alterImporter.php";
  require_once("../../../home.php");

} else if ($comm == "reply") {
  require ("checkReply.php"); //atualiza o banco
  if ($type == 3){
    require ("TextData.php");
    require ("interf/textData.php");
  } else {
    require ("TextCredit.php");
    require ("interf/textCredit.php");
  }
} else if($comm == "renovacao"){ // renovacao de apolice
   
  require("renovacao.php");
  $comm = 'open';
  $idInform = $newIdInform;
  require ("../searchClient/SearchClient.php");
  
  //require ("../inform/Inform.php");
 
}else if($comm == 'comments'){
  require_once('comments.php');
  $title = 'Comentários';
  $content = "../client/interf/Comments.php";
  require_once("../../../home.php");
}else if($comm == 'viewComment'){
  require('viewComment.php');
  $title = 'Comentários';
  $content = "../client/interf/ViewComment.php";
  require_once("../../../home.php");
}else if($comm == 'consultaImpCessao'){
  $title = 'Cessão de Direitos';
  $content = "../cessao/interf/consultaImp.php";
  require_once("../../../home.php");
//Alterado Hicom - (Gustavo)
}else if($comm == 'consultaPa'){
  $title = 'Parcela de Ajuste  - Demonstrativo';
  $content = "../client/interf/consultaPa.php";
  require_once("../../../home.php");
} else if ($comm == "changeWarantyInterest") {
  $title = 'Juros mora';  
  $content = "../client/interf/AlterWar.php";
  require_once("../../../home.php");
  
} else if ($comm == "changeGeneralInf") {
  $title = 'Informações Gerais - Alteração';
  $content = "../client/interf/changeGeneralInf.php";
  require_once("../../../home.php");
  
}else if($comm == "importpdf") {
  $content = "relimportador.php"; 
  require_once("../../../home.php");
  
}else if ($comm == "listpendcliente") {
  $title = "Compradores em Análise";
  $content = "../client/interf/ListPendCliente.php";
  require_once("../../../home.php");
}else if ($comm == "VoltarCliente") {
   $content = "../client/interf/ViewClient.php";
   require_once("../../../home.php");

}
  if(count($_POST) > 2){
   // adiciona dados do log após alterações no inform
   /*$notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 2, $tem,'Inform','');  
   $notif->historicolog($_REQUEST['idInform'], $_SESSION['userID'], $db, 2, $tem1,'Importer',(isset($_REQUEST['idBuyer']) ? $_REQUEST['idBuyer'] : $idImporter)); */
  }
// fim alterado Hicom
?>

