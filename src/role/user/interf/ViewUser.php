<script>
  $(document).ready(function(){
    $(".js-btn_exclui").on("click", function(event){
      var msg = 'Deseja mesmo excluir este usu\u00e1rio?';
      var r = confirm(msg);
      if (r == true) {
        var id = $(this).parent().find(".js_linha_id").val();
        window.location = "<?php echo $host; ?>/src/role/user/User.php?comm=delUser&id_User="+id;
      }

      return false;
      event.preventDefault();
    })
  })
</script>

<?php include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">
  <form  name="frm_user" id="frm_user" action="<?php echo $root;?>role/user/User.php" method="post">   
    <input type="hidden" name="comm" id="comm" value=""> 
  </form>

  <table>
    <thead>
      <tr>
        <th>Nome Usu&aacute;rio</th>
        <th>Login</th>
        <th>Status</th>
        <th colspan="2">Op&ccedil;&otilde;es</th>
      </tr>
    </thead>
    <?php if(empty($dados)){ ?>
      <tbody><tr><td valign="top" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
    <?php } else { ?>
      <?php for ($a=0; $a < count($dados); $a++) { ?>
        <tr>
          <td><?php echo $dados[$a]['Name']; ?></td>
          <td><?php echo $dados[$a]['Login']; ?></td>
          <td><?php echo $dados[$a]['Situacao']; ?></td>
          <td>
            <input type="hidden" value="<?php echo $dados[$a]['id_User']; ?>" class="js_linha_id">
            <a href="<?php echo $host;?>src/role/user/User.php?comm=editUser&id_User=<?php echo $dados[$a]['id_User']; ?>" class="btn">Editar</a>
            <a href="#" class="btn btn-danger js-btn_exclui">Excluir</a>
          </td>       
        </tr>
      <?php } ?>
    <?php } ?>    
  </table>

  <div class="barrabotoes">
    <button class="botaoagg" type="button" onClick="document.frm_user.comm.value = 'addUser'; document.frm_user.submit();">Novo Usu&aacute;rio</button>
  </div>

</div>