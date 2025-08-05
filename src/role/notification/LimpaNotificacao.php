<?php /*
  Oculta a notificação com mais de 181 dias.
  Criado por Tiago V N - Elumini - 01/08/2006
*/

require_once("../../dbOpen.php");


if (empty($idNotification)){
   echo "Variavel Principal esta vazio !!!!";

}else{
$sql = "UPDATE NotificationR set state = '2', i_Usuario = ".$_SESSION["userID"].", d_Encerramento = GETDATE() where id='$idNotification'";
$x = odbc_exec($db, $sql);
if (!$x) {
   echo "Erro em ocultar notificação!";
}else{
   echo "Notificação oculta com sucesso !!!!";
}

}
?>

