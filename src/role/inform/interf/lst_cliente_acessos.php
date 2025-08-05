<?php
 
 if(isset($_REQUEST['idInform'])){
     $idInform = $_REQUEST['idInform'];
 }
 
include_once('../../../navegacao.php'); 

if ($field->getField("idNotification")){
	$_SESSION['idNotification'] = $field->getField("idNotification");
    $idNotification  = $field->getField("idNotification");
}else{
    $idNotification = isset($_SESSION['idNotification']) ? $_SESSION['idNotification'] : null;
}   
?>
<div class="conteudopagina">
 <?php
    
	
	  $query = "SELECT i.id, i.name, i.state, i.startValidity, i.i_Produto
              FROM Inform i
                 JOIN Insured ins ON
                 (ins.id = i.idInsured)
               WHERE ins.idResp = ".$userID."  
		      ORDER BY i.id ";

 
     $cury = odbc_exec($db, $query);
     $junta = '';
	
	$idIn = '';
	 while(odbc_fetch_row($cury)){  
	    $idIn  .= $junta. odbc_result($cury, 1);
		$junta = ',';
	 }
	
	
    $sql= "Select
	        U.id as UserID,
			Inf.name As Segurado,
			U.name As NomeUsuario,
			U.login As LoginUsuario,
			IsNull(U.email, U.login) as EmailUsuario,
			Case U.state
			When 0 Then 'Ativo'
			When 1 Then 'Inativo'
			End As SituacaoUsuario
			From
			Inform Inf
			Inner Join Inform_Usuarios InformUsuarios On
			InformUsuarios.idInform = Inf.id
			Inner Join Users U On
			U.id = InformUsuarios.idUser
			Where
			Inf.id in (".$idIn.") 
			group by
			 U.id,  Inf.name, U.name, U.login , IsNull(U.email, U.login), 
			Case U.state When 0 Then 'Ativo' When 1 Then 'Inativo' End 
			 Order By U.name";
  
       $cur2= odbc_exec($db,$sql); 
	   
	 //print  $sql;
        
  ?>
  <?php if(isset($msgi)){  ?>
			  <script>
                   verErro('<?php echo $msgi;?>');
              </script>  
  <?php     $msg = '';
        }  ?>              
  <form  name="cli_acesso" id="cli_acesso" action="<?php echo $root;?>role/inform/Inform.php" method="post">   
       <input type="hidden" name="comm" id="comm" value=""> 
       <input type="hidden" name="idInform" id="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="Notification" id="Notification" value="<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>">


       <table width="900">
           <thead>
              <tr>
                   <th width="300">
                      Nome Usu&aacute;rio
                   </th>
                   <th width="300">
                      login
                   </th>
                   <th width="100" style="text-align:center">
                      Status
                   </th>
                   <th width="200" colspan="2" style="text-align:center">
                      Op&ccedil;&otilde;es
                   </th>
              </tr>
           </thead>
          <tbody>
<?php    
          $trava = 0; 
		  $ud  = '';
          while(odbc_fetch_row($cur2)){  
		  
			  $sqly = "select  * from Insured where idResp = ".odbc_result($cur2,'UserID')."";
			  $qrr =  odbc_exec($db,$sqly);  
			  
			  //print $sqly;
			  if(odbc_result($qrr, 'idResp') == $userID){
				  //print 'oi';
				  $trava  = 1;
			  }else{
				  $trava = 0;  
			  }
			  
			    if(odbc_result($cur2,'UserID') != $ud){
				  $ud = odbc_result($cur2,'UserID');
			  ?>
				  <tr>
					   <td>
						  <?php echo odbc_result($cur2,'NomeUsuario');?>
					   </td>
					   <td>
						  <?php echo odbc_result($cur2,'LoginUsuario');?>
					   </td>
						<td style="text-align:center">
						  <?php echo odbc_result($cur2,'SituacaoUsuario'); ?>
					   </td>
					   <?php if($trava == 1){ ?>
								   <td style="text-align:center" colspan="2">Usu&aacute;rio principal</td>
					   <?php }else { ?>	
								   <td style="text-align:center">
									  <a href="<?php echo $root;?>role/inform/Inform.php?comm=frm_acesso_cliente&idUsuario=<?php echo odbc_result($cur2,'UserID');?>"><img src="<?php echo $root;?>images/icone_editar.png" alt=""  title="Editar Registro" width="24" height="24" class="iconetabela" /></a>
								   </td>
								   <td style="text-align:center">
									  <a href="#" onclick="vConfirm('Caro usu&aacute;rio, esta a&ccedil;&atilde;o ser&aacute; definitiva deseja continuar?','<?php $root;?>Inform.php?comm=edit_acesso_cliente&idUsuario=<?php echo odbc_result($cur2,'UserID');?>&operacao=3');"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" title="Remover Registro" width="24" height="24" class="iconetabela" /></a>
								   </td>
					   
					   <?php } ?>			   
				  
				  </tr>
		    <?php }  ?>
		  
<?php     }    ?>
          </tbody>
      </table>
   </form>
   
<div class="barrabotoes">
    <button class="botaoagg" type="button"  onClick="document.cli_acesso.comm.value = 'frm_acesso_cliente'; document.cli_acesso.submit();">Novo Usu&aacute;rio</button>
    <button class="botaovgm" type="button"  onClick="document.cli_acesso.comm.value = 'open'; document.cli_acesso.submit();">Voltar</button>
</div>


</div>