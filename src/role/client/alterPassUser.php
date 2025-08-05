<?php  
  session_start();
  $userID = $_SESSION['userID'];  
 // extract($_REQUEST);

  require_once("../rolePrefix.php");

  if(!$comm){
    $comm = $_REQUEST["comm"];	
  }

  if ($comm == "interaktiv") {
    $sql = "SELECT id, password, senhaTmp FROM Users";
    $rsSql = odbc_exec ($db, $sql);

    while (odbc_fetch_row($rsSql)){
      $id = odbc_result($rsSql, 'id');
      $password = odbc_result($rsSql, 'password');
      $pwd = crypt($password, SALT);

      $sqlUp = "UPDATE Users SET senhaTmp = '".$pwd."' WHERE id = ".$id;
      $rsSqlUp = odbc_exec ($db, $sqlUp);
    }    
  }
?>

