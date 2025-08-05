<?php  /*
  Envia e-mail notificando o segurado que o limite temporário
  do seu importador esta se encerrando.

  Criado por Tiago V N - Elumini - 25/01/2008

*/

require_once("../../dbOpen.php");
require_once("../../entity/notification/Notification.php");


if ( empty($idInform) ) {
   echo "Variavel Principal esta vazio !!!!";
}else{

if(! function_exists('ymd2dmy')){
  // converte a data de yyyy-mm-dd para dd/mm/yyyy
  function ymd2dmy($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}

$notif = new Notification ();

//envia email para o cliente e os contatos
$t = odbc_exec($db, "SELECT   id, name
                     FROM Inform
                     WHERE state = 11 and Ga = 1 and mModulos in (1, 2) and id = $idInform
                     order by name asc");

if (odbc_fetch_row($t)) {
   if (!$notif->notifica_bonus(odbc_result($t, "id"), odbc_result($t, "name"), $db)){
        echo "Erro no gerar a notificação.";
   }

}

}

?>
