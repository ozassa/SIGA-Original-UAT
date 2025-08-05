<?php include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">
  <form  name="frm_profile" id="frm_profile" action="<?php echo $root;?>role/accessProfile/AccessProfile.php" method="post">   
    <input type="hidden" name="comm" id="comm" value=""> 
  </form>

  <table>
    <thead>
      <tr>
        <th>Perfil</th>
        <th>Situa&ccedil;&atilde;o</th>
        <th colspan="2">Op&ccedil;&otilde;es</th>
      </tr>
    </thead>
    <?php if(empty($dados)){ ?>
      <tbody><tr><td valign="top" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
    <?php } else { ?>
      <?php for ($a=0; $a < count($dados); $a++) { ?>
        <tr>
          <td><?php echo $dados[$a]['Descricao']; ?></td>
          <td><?php echo $dados[$a]['Situacao']; ?></td>
          <td>
            <input type="hidden" value="<?php echo $dados[$a]['i_Perfil']; ?>" class="js_linha_id">
            <a href="<?php echo $host;?>src/role/accessProfile/AccessProfile.php?comm=editAccessProfile&i_Perfil=<?php echo $dados[$a]['i_Perfil']; ?>" class="btn">Editar</a>
          </td>       
        </tr>
      <?php } ?>
    <?php } ?>    
  </table>

  <div class="barrabotoes">
    <button class="botaoagg" type="button" onClick="document.frm_profile.comm.value = 'addAccessProfile'; document.frm_profile.submit();">Novo Perfil</button>
  </div>

</div>