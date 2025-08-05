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

<?php include_once("../../../navegacao.php");?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<form id="Form2" name="Form2" action="<?php echo $host;?>src/role/module/ModuleSystem.php?comm=cad_mod" method="post">
  <input type="hidden" name="id_mod" value="<?php echo $id_mod; ?>" >
  <div class="conteudopagina">

    <ul class="aba_inter">
      <li class="active">
        <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php">Cadastro de m&oacute;dulos</a>
      </li>
      <li>
        <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php?comm=ordem">Ordena&ccedil;&atilde;o</a>
      </li>
    </ul>

    <br style="clear:both">
    <br style="clear:both">
    <hr style="height: 20px;border: none;border-top: 1px solid #ddd;margin-top: -9px;">

    <li class="campo2colunas">
      <label>C&oacute;digo</label>
      <input name="codigoModulo" id="codigoModulo" value="<?php echo $cod_mod; ?>" type="text"  />
    </li>

    <li class="campo2colunas">
      <label>Grupo</label>
      <input name="grupoModulo" id="grupoModulo" type="text" value="<?php echo $grupo_mod; ?>" />
    </li>

    <li id="clear" class="campo2colunas">
      <label>T&iacute;tulo</label>
      <input name="tituloModulo" id="tituloModulo" type="text" value="<?php echo $titulo_mod; ?>" />
    </li>

    <li class="campo2colunas">
      <label>Ordem de apresenta&ccedil;&atilde;o</label>
      <input name="ordemModulo" id="ordemModulo" type="hidden"  value="<?php echo $ordem_mod ?>" />
      <?php echo $ordem_mod ?>&ordm;
    </li>

    <li class="campo2colunas">
      <label>Situa&ccedil;&atilde;o</label>
      <select name="situacaoModulo"  id="situacaoModulo">
        <option value=''>Selecione</option>
        <option value='0' <?php echo $sit_mod == 0 ? "selected='selected'" : ""; ?>>Ativo</option>
        <option value='1' <?php echo $sit_mod == 1 ? "selected='selected'" : ""; ?>>Inativo</option>
      </select>
    </li>

    <br style="clear:both">
    <li class="campo2colunas" style="margin-bottom: 170px;">
      <label>Descri&ccedil;&atilde;o</label>

      <textarea class="textarea" name="desc_mod" id="desc_mod" placeholder="Enter text ..." style="width: 810px; height: 200px"> <?php echo $txt_mod; ?> </textarea>
    </li>

    <br style="clear:both">
    <br style="clear:both">
    <br style="clear:both">


    <div class="barrabotoes">
      <button class="botaovgm" type="button">Voltar</button>
      <input type="submit" value="Alterar" class="botaoagm">
    </div>

  </div>

</form>
