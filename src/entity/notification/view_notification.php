<?php

//####### ini ####### adicionado por eliel vieira - elumini - 13/03/2008
/*
// procedimento para update no campo idInform da tabela notificationr
// com base no &idInform=??? capturado no campo link
*/
//####### end ####### adicionado por eliel vieira - elumini - 13/03/2008

//limita o tempo para execucao do script
set_time_limit (900);


//define a visualizacao no formato XLS
$excel = "";
$excel = $_REQUEST['excel'];


if ($excel=="yes") {
  //define o tipo do documento
  header('Content-type: application/msexcel');
  header('Content-Disposition: attachment; filename="notificacoes.xls"');
}


//define a classe para abertura do banco de dados
require_once("../../dbOpen.php");


//montagem do sql
$sql = "
        SELECT
               id
             , notification
             , link
 	      FROM NotificationR
 	     where idInform is null
 	     order by notification
       ";

$rs = odbc_exec($db,$sql);

//echo "<table border=1>";
//echo "<tr><td>id</td><td>notification</td><td>link</td><td>idInform</td></tr>";

echo "Processo iniciado. Aguarde...";

while (odbc_fetch_row($rs)) {

  $id           = odbc_result($rs, 'id');
  $notification = odbc_result($rs, 'notification');
  $link         = odbc_result($rs, 'link');

  $idInform = "";
  $id_i1 = explode('&idInform=',$link);
  $id_i2 = explode('&',$id_i1[1]);
  $idInform = $id_i2[0];

  //echo "<tr><td>$id</td><td>$notification</td><td>$link</td><td>$idInform</td></tr>";


if (($idInform!="")&($id!="")) {
  //montagem do sql
  $sql_up = "update NotificationR set idInform = $idInform where id = $id and idInform is null ";
  //execucao do sql
  $rs_up = odbc_exec($db,$sql_up);
  //echo $sql_up."<br>";
}

} //fim while

//echo "</table>";

echo "Processo finalizado!!!";


?>
