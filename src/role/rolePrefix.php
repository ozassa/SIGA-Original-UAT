<?php
if(!isset($_SESSION)){ 
  session_start(); 
} 
//Consultado HiCom mes 04

//error_reporting(E_ALL);
    //ini_set('display_errors', 1);



if (!isset($ROLE_PREFIX)) {
  $ROLE_PREFIX = true;

  $ds = DIRECTORY_SEPARATOR;

  require_once(__DIR__.$ds."..".$ds."entity".$ds."user".$ds."UserView.php");
  require_once(__DIR__.$ds."..".$ds."entity".$ds."notification".$ds."Notification.php");
  require_once(__DIR__.$ds."..".$ds."dbOpen.php");
  require_once(__DIR__.$ds."..".$ds."util".$ds."FieldUtils.php");
  require_once(__DIR__.$ds."..".$ds."util".$ds."NumberUtils.php");
  require_once(__DIR__.$ds."..".$ds."postie.php");
  require_once(__DIR__.$ds."..".$ds."security_log.php");
 
  // trazer o menu normal   
  //Fim
    
  //echo  $role['executive']; 
 
  // esta ação de pegar tudo como array
  $field = new FieldUtils ($_POST, $_GET, 0); 
   
  if(isset($_GET["idInform"])){
    $idInform   = $_GET["idInform"];
    $comm       = isset($_GET["comm"]) ? $_GET["comm"] : '';
  } else if (isset($_POST["idInform"])){
    $idInform   = $_POST["idInform"];
    $comm       = isset($_POST["comm"]) ? $_POST["comm"] : false;
  } else {
    $idInform   = 0;
  }

  
    //$userID  = $_SESSION["userID"];
  $user    = isset($_SESSION["userID"]) ? $_SESSION["userID"] : 0;

    #Pega variaveis vinda do formulário via POST
 
  /*
    function AntiInjection($param){
          $param = strip_tags($param); //  retirar as tags html
          $param = mysql_escape_string($param); //Retirar todas tags referentes do mysql ex: select, insert, update drop etc...
          return $param;
    }  

    foreach($_POST as $campo => $vlr){
          $campo = AntiInjection($vlr); 
    }
   
    foreach($_GET as $campo1 => $vlr){
        $campo1 = AntiInjection($vlr);   
    }

    print 'post ?'.$campo.'<br>';
  
    print 'get? '.$campo1;
  */
  
    $notif = new Notification ();
    $numberExtensive = new NumberUtils ();
    $title = "Título";
    $comm = $field->getField("comm");
    $forward = "success";
    //$root = "/siex/src/";
    //$root = "/coface/projetos/siex/web/src/";
    $msg = "";
    $postie = "../../postie.pl";
}

// verifica se a sessao expirou
/*
if($comm != "clientLogin" &&
    $comm != "login" &&
    $comm != "functionaryLogin" &&
    $comm != "createLog" &&
    $comm != "recoverClient" &&
    $comm != "setCreateLog" &&
    $comm != "setRecoverPassword" &&
    $comm != 'func' && $comm != 'client' &&
    $comm != 'database' && $comm != 'exit' &&
    $comm != 'gerapdf'){

    
    if (! isset($user)){
      print $comm;  
          if($comm != 'exitclient'){
              //echo "verErro('Sua sessao expirou');";
              echo "<script language=javascript>"."top.location = 'http://www.coface.com.br'".'</script>'; 
    }
    }

}
*/

/*if(! function_exists('esta_vigente')){
  function esta_vigente($idInform){
    global $db;
    $x = odbc_exec($db, "select idAnt, state from Inform where id=$idInform");
    $idAnt = odbc_result($x, 1);
    $state = odbc_result($x, 2);

    if($idAnt){
            $x = odbc_exec($db, "select state from Inform where id=$idAnt");
            $stateAnt = odbc_result($x, 1);

            if($stateAnt == 10 && $state != 9 && $state != 10 && $state != 11){
        return 1;
            }

            return 0;
        }

    if($state == 10){
            return 1;
        }

        return 0;
    }
} */

if (!function_exists('esta_vigente')) {
    function esta_vigente($idInform) {
        global $db;

        // Verifica se o ID é numérico para maior segurança
        if (!is_numeric($idInform)) {
            return 0;
        }

        // Prepara a primeira consulta
        $stmt = odbc_prepare($db, "SELECT idAnt, state FROM Inform WHERE id = ?");
        odbc_execute($stmt, [$idInform]);
        $result = odbc_fetch_array($stmt);

        if (!$result) {
            return 0;
        }

        $idAnt = $result['idAnt'];
        $state = $result['state'];

        if ($idAnt) {
            // Prepara a segunda consulta
            $stmtAnt = odbc_prepare($db, "SELECT state FROM Inform WHERE id = ?");
            odbc_execute($stmtAnt, [$idAnt]);
            $resultAnt = odbc_fetch_array($stmtAnt);

            if (!$resultAnt) {
                return 0;
            }

            $stateAnt = $resultAnt['state'];

            if ($stateAnt == 10 && $state != 9 && $state != 10 && $state != 11) {
                return 1;
            }

            return 0;
        }

        if ($state == 10) {
            return 1;
        }

        return 0;
    }
}


/*if(! function_exists('esta_em_renovacao')){
  function esta_em_renovacao($idInform){
        // saber se está em renovaçao: 
        // status diferente de 9, 10 e 11
        // possuir informe anterior com status 10
        global $db;
        $x = odbc_exec($db, "select state from Inform where id=$idInform");
        $state = odbc_result($x, 1);
        $x = odbc_exec($db, "select state from Inform where idAnt=$idInform");

        if(odbc_fetch_row($x)){
            $stateRenov = odbc_result($x, 1);

            if($state == 10 && $stateRenov != 9 && $stateRenov != 10 && $stateRenov != 11){
            return 1;
            }
        }

        return 0;
    }
}*/

if (!function_exists('esta_em_renovacao')) {
    function esta_em_renovacao($idInform) {
        // Verifica se está em renovação
        // Status diferente de 9, 10 e 11
        // Possuir informe anterior com status 10
        global $db;

        // Verifica se o ID é numérico para maior segurança
        if (!is_numeric($idInform)) {
            return 0;
        }

        // Prepara a consulta para obter o estado atual
        $stmt = odbc_prepare($db, "SELECT state FROM Inform WHERE id = ?");
        odbc_execute($stmt, [$idInform]);
        $result = odbc_fetch_array($stmt);

        if (!$result) {
            return 0;
        }

        $state = $result['state'];

        // Prepara a consulta para obter o estado do informe anterior
        $stmtAnt = odbc_prepare($db, "SELECT state FROM Inform WHERE idAnt = ?");
        odbc_execute($stmtAnt, [$idInform]);
        $resultAnt = odbc_fetch_array($stmtAnt);

        if ($resultAnt) {
            $stateRenov = $resultAnt['state'];

            if ($state == 10 && $stateRenov != 9 && $stateRenov != 10 && $stateRenov != 11) {
                return 1;
            }
        }

        return 0;
    }
}

/*
if(! function_exists('HC_renovando')){
    function HC_renovando($idInform){
        global $db;
    $HC_ok = 0;
        $x = odbc_exec($db, "select state, idAnt from Inform where id=$idInform");
        $state = odbc_result($x, 1);
    $HC_idAnt = odbc_result($x, 2);
    //echo $idInform;
    //die();
  
    if ($HC_idAnt){ 
            $x = odbc_exec($db, "select state from Inform where id=$HC_idAnt");

            if(odbc_fetch_row($x)){
              $stateRenov = odbc_result($x, 1);
    
              if(($stateRenov == 10 || $stateRenov == 9  ) && $state != 9 && $state != 10 && $state != 11){
                // return 1;
            $HC_ok = 1;
              }else{
            $HC_ok = 0; 
        }
            }else{
              $HC_ok = 0;
        }   
         
    }

    return $HC_ok;
    }  
}*/

if (!function_exists('HC_renovando')) {
    function HC_renovando($idInform) {
        global $db;
        $HC_ok = 0;

        // Verifica se o ID é numérico para evitar entradas maliciosas
        if (!is_numeric($idInform)) {
            return 0;
        }

        // Prepara a consulta para obter o estado e o ID anterior
        $stmt = odbc_prepare($db, "SELECT state, idAnt FROM Inform WHERE id = ?");
        odbc_execute($stmt, [$idInform]);
        $result = odbc_fetch_array($stmt);

        if (!$result) {
            return 0;
        }

        $state = $result['state'];
        $HC_idAnt = $result['idAnt'];

        if ($HC_idAnt) {
            // Prepara a consulta para obter o estado do informe anterior
            $stmtAnt = odbc_prepare($db, "SELECT state FROM Inform WHERE id = ?");
            odbc_execute($stmtAnt, [$HC_idAnt]);
            $resultAnt = odbc_fetch_array($stmtAnt);

            if ($resultAnt) {
                $stateRenov = $resultAnt['state'];

                if (($stateRenov == 10 || $stateRenov == 9) && $state != 9 && $state != 10 && $state != 11) {
                    $HC_ok = 1;
                } else {
                    $HC_ok = 0;
                }
            } else {
                $HC_ok = 0;
            }
        }

        return $HC_ok;
    }
}


  function check_menu($cfg, $role){
    $cont = 0;

    foreach ($cfg as $value) {
      if(array_key_exists($value, $role)){
        $cont = $cont + 1;
      }
    }

    return $cont > 0;
  }

  function is_valid_email($email){ 
      return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
  }

?>