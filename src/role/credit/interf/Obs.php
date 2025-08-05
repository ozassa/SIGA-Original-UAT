<?php include_once('../../../navegacao.php'); ?>
<div class="conteudopagina">
	<form action="<?php echo  $root;?>role/credit/Credit.php" method="post" name="for5" style="width:950px"> 
        <input type="hidden" name="comm" value="obs">
        <input type="hidden" name="insert" value="1">
        <input type="hidden" name="idBuyer" value="<?php echo  $field->getField("idBuyer");?>">
        <input type="hidden" name="idInform" value="<?php echo  $field->getField("idInform");?>">
        <input type="hidden" name="idNotification" value="<?php echo  $field->getField("idNotification");?>">
       	<input type="hidden" name="origem" value="<?php echo  $field->getField("origem");?>">
      	<p align="center" class="textoBold">Acrescentar observação para importador "<?php echo  $importer;?>"</p>
		<br />
        <textarea name="observacao" rows="12" cols="80" ></textarea>
        <p align="center">
        	<button type="button" class="botaovgm" onClick="this.form.comm.value='showBuyers';this.form.submit()">Voltar</button>
        	<button type="button" class="botaoagm" onclick="document.for5.submit()">Inserir</button>
        </p>
	</form>
	<script language="javascript">
       function esconde (indice, obj, id){
         if (document.getElementById('ver'+indice).checked ) {
             val=1;
         } else {
             val=0;
         }
         document.form3.val.value = val;
         document.form3.idComment.value=id;
         document.form3.submit();
       }
    </script>
	<form action="<?php echo  $root;?>role/credit/Credit.php" style="width:950px" name="form3" method="post">
        <input type="hidden" name="comm" value="obs">
        <input type="hidden" name="idComment" id="idComment">
        <input type="hidden" name="val" id="val" value="">
        <input type="hidden" name="idBuyer" id="idBuyer"  value="<?php echo  $field->getField("idBuyer");?>">
        <input type="hidden" name="idInform" id="idInform" value="<?php echo  $field->getField("idInform");?>">
        <input type="hidden" name="idNotification" id="idNotification" value="<?php echo  $field->getField("idNotification");?>">
        <input type="hidden" name="origem" id="origem" value="<?php echo  $field->getField("origem");?>">
        <table width="100%" cellspacing=0 cellpadding=2 border=0 align="center">
            <tr class="bgAzul">
                <th width=100%>Observações já feitas</th>
                <th>Ocultar</th>
            </tr>
    <?php  $i = 0;
            while(odbc_fetch_row($obs_res)){
              $i++;
              $obs_id = odbc_result($obs_res, 'id');
              $comment = odbc_result($obs_res, 'comment');
              $val     = odbc_result($obs_res, 'hide');
                if($val != 1){
			 ?>
			   <tr <?php echo($i % 2 != 0 ? 'class="odd"' : "");?> >
                  <td colspan="1" title="<?php echo $comment;?>">
                   <?php echo ($full ? $comment : substr($comment, 0, 20). '...'); ?>
                  </td>
                  <td align="center" colspan="1">
                       <input name="ver<?php echo $i;?>"  id="ver<?php echo $i;?>" onClick="esconde(<?php echo $i;?>, this, <?php echo $obs_id;?>);" type="checkbox" <?php echo($val > 0 ? ' checked' : '');?>>
                  </td>
                </tr>
                
    <?php      }
	         
			}
    
            if($i == 0){  ?>
                <tr style="bgcolor:#a4a4a4">
                	<td colspan="2" align="center">Não há nenhuma observação para este importador</td>
                </tr>
   <?php    }
    
    ?>
    
        </table>
    </form>
    
    <br clear="all" />
    <label>Para ver o texto completo da observação movimente o mouse sobre o texto.</label>
<!--    <a href="<?php echo  $root;?>role/credit/Credit.php?comm=obs&idBuyer=<?php echo  $field->getField("idBuyer");?>&idNotification=<?php echo  $field->getField("idNotification") ?>&idInform=<?php echo  $field->getField("idInform");?>&origem=<?php echo  $field->getField("idBuyer");?>&full=<?php echo  (1 - $full);?>">
    <?php  	if($full){
               echo "Resumir texto das observações";
            }else{
               echo "Ver texto completo das observações";
            }
    ?>
    </a>
--></div>
<div style="clear:both">&nbsp;</div>