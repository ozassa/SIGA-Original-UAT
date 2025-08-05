<?php


          //Atualiza a tabela users
          $pwd = crypt($senha, SALT);
					
          $q  = "UPDATE Users set password = '$pwd' WHERE id = '".$userID."' " ;
          $cur = odbc_exec($db, $q);
          
          if($cur)
          {
?>
          <script language="Javascript">
          verErro("Senha alterada com sucesso!")
          </script>

<?php
          }

?>