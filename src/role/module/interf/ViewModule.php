<script>
  $(document).ready(function(){
    $(".js-btn_exclui").on("click", function(event){
      var msg = $("#js_msg_exc").val();
      var r = confirm(msg);
      if (r == true) {
        var id = $(this).parent().find(".js_linha_id").val();
        window.location = "<?php echo $host; ?>/src/role/module/ModuleSystem.php?comm=exc_mod&id_mod="+id;
      }

      return false;
      event.preventDefault();
    })
  })
</script>

<!-- Exibir Calendario -->
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $host;?>src/css/bootstrap-wysihtml5.css" title="win2k-cold-1" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $host;?>src/scripts/calendario/calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="<?php echo $host;?>src/scripts/calendario/calendar.js"></script>

<!-- language for the calendar -->
<script type="text/javascript" src="<?php echo $host;?>src/scripts/calendario/lang/calendar-en.js"></script>

<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="<?php echo $host;?>src/scripts/calendario/calendar-setup.js"></script>

<script type="text/javascript" src="<?php echo $host;?>src/scripts/wysihtml5.js"></script>
<script type="text/javascript" src="<?php echo $host;?>src/scripts/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $host;?>src/scripts/bootstrap-wysihtml5.js"></script>

<script>
  $(document).ready(function(){
    $('#desc_mod').wysihtml5({
      "font-styles": false, //Font styling, e.g. h1, h2, etc. Default true
      "emphasis": true, //Italics, bold, etc. Default true
      "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
      "html": false, //Button which allows you to edit the generated HTML. Default false
      "link": false, //Button to insert a link. Default true
      "image": false, //Button to insert an image. Default true,
      "color": false //Button to change color of font  
    });
  })
</script>

<input type="hidden" id="js_msg_exc" value="Deseja mesmo excluir este m&oacute;dulo?">

<?php include_once("../../../navegacao.php");?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<form id="Form2" name="Form2" action="<?php echo $host;?>src/role/module/ModuleSystem.php?comm=cad_mod" method="post">
  <input type="hidden" name="id_prox" value="<?php echo $id_prox; ?>">
  <div class="conteudopagina">

    <ul class="aba_inter">
      <li class="active">
        <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php">Cadastro de m&oacute;dulos</a>
      </li>
      <li>
        <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php?comm=ordem">Ordena&ccedil;&atilde;o</a>
      </li>

      <li>
        <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php?comm=relacionamento">Relacionamento com Subprodutos</a>
      </li>

      
    </ul>

    <br style="clear:both">
    <br style="clear:both">
    <hr style="height: 20px;border: none;border-top: 1px solid #ddd;margin-top: -9px;">

    <li class="campo2colunas">
      <label>C&oacute;digo</label>
      <input name="codigoModulo" id="codigoModulo" type="text" />
    </li>

    <li class="campo2colunas">
      <label>Grupo</label>
      <input name="grupoModulo" id="grupoModulo" type="text" />
    </li>

    <li id="clear" class="campo2colunas">
      <label>T&iacute;tulo</label>
      <input name="tituloModulo" id="tituloModulo" type="text" />
    </li>

    <li class="campo2colunas">
      <label>Ordem de apresenta&ccedil;&atilde;o</label>
      <input name="ordemModulo" id="ordemModulo" type="hidden" value="<?php echo $qtde ?>" />
      <?php echo $qtde ?>&ordm;
    </li>

    <li class="campo2colunas">
      <label>Situa&ccedil;&atilde;o</label>
      <select name="situacaoModulo" id="situacaoModulo">
        <option value='' selected>Selecione</option>
        <option value='0' selected>Ativo</option>
        <option value='1' selected>Inativo</option>
      </select>
    </li>

    <br style="clear:both">
    <li class="campo2colunas" style="margin-bottom: 170px;">
      <label>Descri&ccedil;&atilde;o</label>
      <textarea class="textarea" name="desc_mod" id="desc_mod" placeholder="Enter text ..." style="width: 810px; height: 200px"></textarea>
    </li>

    <br style="clear:both">
    <br style="clear:both">
    <br style="clear:both">


    <div class="barrabotoes">
      <button class="botaovgm" type="button">Voltar</button>
      <input type="submit" value="Inserir" class="botaoagm">
    </div>

  </div>

</form>



<div class="conteudopagina">
  <h3>M&oacute;dulos cadastrados</h3>
  <table summary="" id="">
    <thead>
      <tr>
        <th>C&oacute;digo</th>
        <th>Grupo</th>
        <th>T&iacute;tulo</th>
        <th>Descri&ccedil;&atilde;o</th>
        <th>Ordem de apresenta&ccedil;&atilde;o</th>
        <th>Situa&ccedil;&atilde;o</th>
        <th colspan="2">Op&ccedil;&atilde;o</th>
      </tr>
    </thead>  

    <?php if(empty($dados_sel)){ ?>
      <tbody><tr><td valign="top" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
    <?php } else { ?>
      <?php for ($i=0; $i < count($dados_sel); $i++) { ?>
        <tr>
          <td><?php echo $dados_sel[$i]["cod_mod"]; ?></td>
          <td><?php echo $dados_sel[$i]["grupo_mod"]; ?></td>
          <td><?php echo $dados_sel[$i]["titulo_mod"]; ?></td>
          <td><?php echo $dados_sel[$i]["txt_mod"]; ?></td>
          <td><?php echo $dados_sel[$i]["ordem_mod"]; ?></td>
          <td><?php echo $lista_situacao[$dados_sel[$i]["sit_mod"]]; ?></td>
          <td style="width: 14%;">
            <input type="hidden" value="<?php echo $dados_sel[$i]["id_mod"]; ?>" class="js_linha_id">
            <a href="<?php echo $host;?>src/role/module/ModuleSystem.php?comm=mod_edit&id_mod=<?php echo $dados_sel[$i]["id_mod"]; ?>" class="btn">Editar</a>
            <a href="#" class="btn btn-danger js-btn_exclui">Excluir</a>
          </td>
        </tr>
      <?php } ?>
    <?php } ?>    
  </table>

  <div class="divisoria01"></div>
      
</div>
