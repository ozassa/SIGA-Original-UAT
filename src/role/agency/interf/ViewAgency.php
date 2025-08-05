<?php include_once("../../../navegacao.php");?>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">
  <form id="Form2" name="Form2" action="<?php echo $host;?>src/role/agency/Agency.php" method="post">
    <input type="hidden" name="buscar" value="buscar">

    <li class="campo2colunas">
      <label>Regi&atilde;o:</label>
      <select name="regiaoID" id="regiaoID">
        <option value="">Selecione</option>
        <?php 
          for ($i=0; $i < count($dados_sel); $i++) { 
            if ($regiaoID == $dados_sel[$i]['idRegiao']) {
              $select = "selected";
            } else {
              $select = "";
            }
          ?>
          <option value="<?php echo $dados_sel[$i]['idRegiao']; ?>" <?php echo $select; ?>><?php echo $dados_sel[$i]['Regiao']; ?></option>
        <?php } ?>       
      </select>
    </li>
    
    <li class="barrabotoes" style="list-style:none;*margin-left:-15px;*margin-top:20px;">
      <button type="submit" class="botaoagm">Filtrar</button>
    </li>
  </form>

  <?php if(isset($_REQUEST['buscar'])){ ?>
    <div id="title-table">
      <h2>Lista de Ag&ecirc;ncias</h2>
    </div>

    <table summary="" id="example" class="no-sort">
      <thead>
        <tr>
          <th>Regi&atilde;o</th>
          <th>Cod. Ag&ecirc;ncia</th>
          <th>Ag&ecirc;ncia</th>
          <th>Endere&ccedil;o</th>
          <th>Cidade</th>
          <th>UF</th>
        </tr>
      </thead>  

      <?php if(empty($dados)){ ?>
        <tbody><tr><td valign="top" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
      <?php } else { ?>
        <?php for ($a=0; $a < count($dados); $a++) { ?>
          <tr>
            <td><?php echo $dados[$a]['Regiao']; ?></td>
            <td><?php echo $dados[$a]['Codigo']; ?></td>
            <td><?php echo $dados[$a]['Agencia']; ?></td>
            <td><?php echo $dados[$a]['Endereco']; ?></td>
            <td><?php echo $dados[$a]['Cidade']; ?></td>
            <td><?php echo $dados[$a]['UF']; ?></td>          
          </tr>
        <?php } ?>
      <?php } ?>    
    </table>

    <div class="divisoria01"></div>
  <?php } ?>
</div>