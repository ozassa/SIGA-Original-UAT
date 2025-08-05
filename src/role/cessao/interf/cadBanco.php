<script>
<!--
function seleciona (obj) {
//  verErro(obj.selectedIndex);
  form = obj.form;
  form.submit();
}

function valida(){
  if($('#codigo').val() != '' && $('#name').val() != '' ){
    frm.comm.value='cadBancoSQL';
    return true;
  }
  
  alert('Preencha o código e o nome');
  return false;
}
<?php

if(isset($valida) && $valida != ''){ ?> 

alert("<?php echo utf8_decode($valida); ?>");

<?php } ?>


<?php 
if(isset($success) && $success){ ?> 

alert('Banco cadastrado com sucesso');

<?php } ?>
// -->
</script>

<?php 
  $tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : 0;
  $cond = $tipo != 0 ? ' and tipo = '.$tipo : '';
?>

  <?php require_once("../../../navegacao.php");?>
    <div class="conteudopagina">
  <!-- CONTEÚDO PÁGINA - INÍCIO -->
    
    <form name="frm" id="frm" action="<?php echo  $root;?>role/cessao/Cessao.php" method="post">
      <ul>


            <li class="campo2colunas"><label>C&oacute;digo do Banco</label>
             <input type="text" name="codigo" id="codigo" required="required">
            </li>
            <li class="campo2colunas"><label>Nome do Banco</label>
             <input type="text" name="name" id="name" required="required">
            </li>
            <li class="campo2colunas"><label>Tipo do Banco</label>
             <select name="tipo" onChange="seleciona(this)" required="required">
                <option value="1" <?php if($tipo==1){ echo 'selected'; }?> >Banco do Brasil</option>
                <option value="2" <?php if($tipo==2){ echo 'selected'; }?> >Parceiros</option>
             </select>
            </li> 
          <input type=hidden name="comm" value="cadBanco">
          <input type=hidden name="idInform" value="<?php echo  htmlspecialchars((int)$idInform, ENT_QUOTES, 'UTF-8');?>">
          <div class="barrabotoes">
            <button class="botaoagm" type="button"  onClick="javascript: if(valida()){frm.submit();}">Cadastrar</button>
          </div>
    
    </form>
   
          <table summary="Submitted table designs" id="example">
                <caption>Bancos Cadastrados</caption>
                <thead>
                  <tr>
                      <th scope="col">&nbsp;</th>
                      <th scope="col">C&oacute;digo</th>
                      <th scope="col">Banco</th>
                      <th scope="col">Tipo</th>
                  </tr>
                </thead>
                <tbody>
                <?php  

                  $query = "SELECT * FROM Banco WHERE codigo <> 0 ";
                    $cur = odbc_exec($db, $query);
                    $i = 0;
                    while (odbc_fetch_row($cur)) {
                      $i++;
                      $codigo = odbc_result($cur,2);    
                      $banco = odbc_result($cur, 3);
                      $type = odbc_result($cur, 5);
                      switch($type){
                        case 1: $type = "Banco do Brasil"; break;
                        case 2: $type = "Banco Parceiro"; break;
                        case 3: $type = "Outros Bancos"; break;
                      }
                ?>
                
                  <tr>
                    <td><?php echo (int) $i; ?></td>
                    <td><?php echo (int) $codigo; ?></td>
                    <td><?php echo (int) $banco; ?></td>
                    <td><?php echo (int) $type; ?></td>


                 </tr>
                <?php  } // while
                  if ($i == 0) {
                ?>
                  <tr>
                    <td colspan=4>Nenhum Banco Cadastrado</td>
                  </tr>
                <?php  }?>      
                </tbody>
           </table>
           <div class="divisoria01"></div>
    </div>