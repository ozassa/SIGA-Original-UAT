<?php include_once("../../../navegacao.php");?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">
  <form id="Form2" name="Form2" action="<?php echo $host;?>src/role/log/Log.php" method="post">
    <input type="hidden" name="buscar" value="buscar">

    <li class="campo2colunas">
      <label>Processo:</label>
      <select name="processoID" id="processoID">
        <option value="">Selecione</option>
        <?php 
          for ($i=0; $i < count($dados_pro); $i++) { 
            if ($processoID == $dados_pro[$i]['Id_Processo']) {
              $select = "selected";
            } else {
              $select = "";
            }
          ?>
          <option value="<?php echo $dados_pro[$i]['Id_Processo']; ?>" <?php echo $select; ?>><?php echo $dados_pro[$i]['Processo']; ?></option>
        <?php } ?>       
      </select>
    </li>

    <li class="campo2colunas">
      <label>Usu&aacute;rio:</label>
      <select name="usuarioID" id="usuarioID">
        <option value="">Selecione</option>
        <?php 
          for ($i=0; $i < count($dados_user); $i++) { 
            if ($usuarioID == $dados_user[$i]['id_Usuario']) {
              $select = "selected";
            } else {
              $select = "";
            }
          ?>
          <option value="<?php echo $dados_user[$i]['id_Usuario']; ?>" <?php echo $select; ?>><?php echo $dados_user[$i]['Usuario']; ?></option>
        <?php } ?>       
      </select>
    </li>
    
    <li class="barrabotoes" style="list-style:none;*margin-left:-15px;*margin-top:20px;">
      <button type="submit" class="botaoagm">Filtrar</button>
    </li>
  </form>
  
  <table summary="" id="example" class="no-sort">
      <div id="title-table">
        <h2>Rela&ccedil;&atilde;o de Trilha de Auditoria</h2>
      </div>
    <thead>
      <tr>
        <th>ID</th>
        <th>Processo</th>
        <th>Usu&aacute;rio</th>
        <th>Data</th>
      </tr>
    </thead>  

    <?php if(empty($dados)){ ?>
      <tbody><tr><td valign="top" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
    <?php } else { ?>
      <?php for ($a=0; $a < count($dados); $a++) { ?>
        <tr>
          <td><?php echo $dados[$a]['i_Log']; ?></td>
          <td><?php echo $dados[$a]['Tipo_Processo']; ?></td>
          <td><?php echo $dados[$a]['Usuario']; ?></td>
          <td><?php echo $dados[$a]['d_Log']; ?></td>
        </tr>
      <?php } ?>
    <?php } ?>    
  </table>

  <div class="divisoria01"></div>
      
</div>