<?php 
// Criado por Fábio Campos (Analista - Elumini)
include_once('../../../navegacao.php');


$qry = "select a.id,a.name,c.login 
		from Role a
			inner join UserRole b on b.idRole = a.id
			inner join Users c on c.id = b.idUser
		where c.id = '".$_SESSION['userID']."' and c.perfil = '".$_SESSION['pefil']."'
		order by a.name,c.login";

 

 
 $cur=odbc_exec($db,$qry);
  while (odbc_fetch_row($cur)) {
	 
	 $x = $x + 1;
	 $name  = odbc_result ($cur, 'name');
	 $id    = odbc_result ($cur, 'id');
	 $role[$name] = $id.'<br>';
  }
  
  
  
if ((!$role["policy"]) && (!$role["relacaoCliExec"])) {
   require_once("naoAutorizado.php");
   $bloqueia = "sim";
} else {
}


?>
<div class="conteudopagina">

<?php
require_once("../dve/interf/funcsDve.php");

$acao = $field->getField ("relacao");


if ($acao == "Alterar") {

   $novoExecutivo = $field->getField ("executivoAtual");

   if ($novoExecutivo == "") {
      $erro = "Favor selecionar um executivo da listagem ao lado.";
   } else {
      $sql = "UPDATE Inform SET idUser='$novoExecutivo' WHERE id='$idInform'";
      $cur = odbc_exec($db, $sql);


      //Alterado por Tiago V N - Elumini - 05/10/2006
      $notif->doneRole($idNotification, $db);

      $sucesso = "ok";
   }

}
if ($bloqueia == "") {
    $sql = "SELECT name, idUser FROM Inform WHERE id='$idInform'";
    $cur = odbc_exec($db,$sql);

    $cliente     = odbc_result($cur, 1);
    $executivo   = odbc_result($cur, 2);

    $sql = "SELECT name FROM Users WHERE id='$executivo'";
    $cur = odbc_exec($db,$sql);

    $executivo   = odbc_result($cur, 1);
}

if ($bloqueia == "") {   ?>
      <form name="mudaRelacao" action="ListClient.php?comm=mudaRelacao&idInform=<?php echo $idInform?>" method="post">
    <?php
    if ($sucesso != "") {  ?>
          <label style="color:#F00">Executivo alterado com sucesso!</label>
   <?php }
   ?>
  <li class="campo2colunas"> 
     <label>Cliente</label>
         <?php echo $cliente;?>
  </li>
  
  <li class="campo2colunas">
     <label>Executivo</label>
     <select name="executivoAtual">
        <option value="">Selecione</option>
        
        <?php
          
          $sql = " SELECT ur.idUser, u.name, u.state FROM Users u
            INNER JOIN UserRole ur ON (u.id=ur.idUser AND ur.idRole = 2)
            WHERE u.state = 0 AND u.perfil = 'F' ";
            
                  $cur = odbc_exec($db,$sql);
                 while (odbc_fetch_row ($cur)) {
                    $idExecutivo = trim(odbc_result($cur, 1));
                    $nomeExecutivo = trim(odbc_result($cur, 2));
                    $stateExecutivo = trim(odbc_result($cur, 3));
        
                    if ($executivo == $nomeExecutivo) {
                       $select = "selected";
                    } else {
                       $select = "";
                    }
        
                    echo ("<option value=".$idExecutivo." ".$select." ".$stateExecutivo.">" .$nomeExecutivo ."</option>\n");
                 }
          ?>
        </select>
        
  </li>
<?php
      if ($erro != "") {  ?>
         <label style="color:#F00"><?php echo ($erro);?></label>
 <?php     }  ?>

       <input type="hidden" name="idNotification" value="<?php echo $idNotification;?>">
       <div class="barrabotoes">   
          <input type="hidden" name="relacao" id="relacao"  value="">       
          <button  type="button" class="botaoagm" onclick="javascript: document.getElementById('relacao').value = 'Alterar'; this.form.submit()">Alterar</button>
          <button  type="button" class="botaovgm" onclick="window.location = '<?php echo $root;?>role/searchClient/RelacaoClientExecutivo.php';">Voltar</button>
       </div>
      
    </form>

</form>
<?php
   }
?>
</div>
