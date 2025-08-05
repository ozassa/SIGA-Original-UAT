<?php /*
//####### ini ####### adicionado por eliel vieira - elumini - em 02/04/2008
// referente a demanda 1374 - SAD
*/

//define a classe para abertura do banco de dados
require_once("../../dbOpen.php");

//define a classe de roles
require_once("../RolePrefix.php");

//verifica procedimento
if ($acao_unb=="atualizar") {

  //verifica se existe habilitacao para bloqueio dos campos
  $sql_unb = "
              update Inform set unblock_dve = $unblock_dve
               where id = $idInform
             ";

  $rs_unb = odbc_exec($db, $sql_unb);
  if (!($rs_unb)) {
    echo "Ocorreu um erro ao atualizar dados. Contate seu administrador.<br>";
  }

}

//####### end ####### adicionado por eliel vieira - elumini - em 02/04/2008

?>