<?php

class User {
  var $user;

  function User ($u, $p, $per, $db)
  {
    $idUser;
    $nameUser;
    $emailUser;
    $role;

   
   
   // Query parametrizada para evitar SQL Injection
   $qry = "SELECT id, name FROM Users WHERE login = ? AND password = ? AND isnull(state,0) <> 1 AND perfil = ?";
   
   // Validação e sanitização de entrada
   if (empty($u) || empty($p) || empty($per)) {
     $this->user = NULL;
     return;
   }
   
   // Sanitizar inputs
   $u = trim($u);
   $p = trim($p);
   $per = trim($per);
   
   // Validar perfil permitido
   $perfilValidos = ['F', 'CO', 'C', 'E', 'A', 'D'];
   if (!in_array($per, $perfilValidos)) {
     $this->user = NULL;
     return;
   }
   
   $cur = odbc_prepare($db, $qry);
   if (!$cur) {
     error_log("Failed to prepare login query - User: $u, IP: " . $_SERVER['REMOTE_ADDR']);
     $this->user = NULL;
     return;
   }
   
   if (!odbc_execute($cur, [$u, $p, $per])) {
     // Log da tentativa de acesso inválida
     error_log("Tentativa de login falhada - User: $u, IP: " . $_SERVER['REMOTE_ADDR']);
     $this->user = NULL;
     return;
   }
   
   
   
   if (odbc_fetch_row($cur)) {
      $idUser = odbc_result($cur,1);
      $nameUser = odbc_result($cur,2);
	  
	   $_SESSION['userID'] = $idUser;
	  
      // Query parametrizada para buscar email
      $cSql = "SELECT inf.emailContact
                FROM Inform inf, Insured ins, Users u
                WHERE u.id = ins.idResp
                      AND ins.id = inf.idInsured
                      AND inf.state <> 9
                      AND u.id = ?
                ORDER BY inf.id DESC";
      $curEmail = odbc_prepare($db, $cSql);
      if ($curEmail) {
        if (odbc_execute($curEmail, [$idUser]) && odbc_fetch_row($curEmail)) {
          $emailUser = odbc_result($curEmail, 1);
        }
      }
      
      // carregar os papeis - query parametrizada
      $roleQuery = "SELECT name FROM UserRole AS ur JOIN Role AS r ON (ur.idRole = r.id) WHERE ur.idUser = ?";
      $curRole = odbc_prepare($db, $roleQuery);
      if ($curRole) {
        if (odbc_execute($curRole, [$idUser])) {
          while (odbc_fetch_row($curRole)) {
            $roleName = odbc_result($curRole, 1);
            $role[$roleName] = $roleName;
          }
        }
      }
      $this->user = new UserView($idUser, $nameUser, $role, $emailUser, $per);
      
     
    }
    elseif($per!="F")
    { //Tiago V N - 06/09/2006

      $per="CO";
      // Tentativa com perfil CO - query parametrizada
      $curCO = odbc_prepare($db, "SELECT id, login FROM Users WHERE login = ? AND password = ? AND isnull(state,0) <> 1 AND perfil = ?");
      if (!$curCO) {
        error_log("Failed to prepare CO query - User: $u, IP: " . $_SERVER['REMOTE_ADDR']);
        $this->user = NULL;
        return;
      }
      
      if (!odbc_execute($curCO, [$u, $p, $per])) {
        error_log("Erro na query CO - User: $u, IP: " . $_SERVER['REMOTE_ADDR']);
        $this->user = NULL;
        return;
      }
      if (odbc_fetch_row($curCO)) {
         $idUser = odbc_result($curCO, 1);
         $emailUser = odbc_result($curCO, 2);

            // carregar os papeis - query parametrizada
      $roleQueryCO = "SELECT name FROM UserRole AS ur JOIN Role AS r ON (ur.idRole = r.id) WHERE ur.idUser = ?";
      $curRoleCO = odbc_prepare($db, $roleQueryCO);
      if ($curRoleCO) {
        if (odbc_execute($curRoleCO, [$idUser])) {
          while (odbc_fetch_row($curRoleCO)) {
            $roleName = odbc_result($curRoleCO, 1);
            $role[$roleName] = $roleName;
          }
        }
      }

      $this->user = new UserView ($idUser, $emailUser, $role, $emailUser, $per);
      }
      else
      {
      $this->user = NULL;
      }
    }
  }

  function getUserView () {
    return $this->user;
  }

}
?>
