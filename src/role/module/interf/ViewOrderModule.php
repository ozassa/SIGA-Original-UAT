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

<script type="text/javascript" src="<?php echo $host;?>src/scripts/jquery_ui/jquery_ui.js"></script>


<?php include_once("../../../navegacao.php");?>

<script>
  $(document).ready(function(){
    $( "#ls_ord" ).sortable({
      stop: function( event, ui ) {
        console.log(ui.item);
      }
    });
    $( "#ls_ord" ).disableSelection();
  })
</script>


<div class="conteudopagina">
  <ul class="aba_inter">
    <li>
      <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php">Cadastro de m&oacute;dulos</a>
    </li>
    <li  class="active">
      <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php?comm=ordem">Ordena&ccedil;&atilde;o</a>
    </li>
    <li>
      <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php?comm=relacionamento">Relacionamento com Subprodutos</a>
    </li>
  </ul>
  <br style="clear:both">
  <br style="clear:both">
  <hr style="height: 20px;border: none;border-top: 1px solid #ddd;margin-top: -9px;">


  <form action="<?php echo $host; ?>src/role/module/ModuleSystem.php?comm=cad_ordem" method="post">
    <ul class="ls_ord" id="ls_ord">
      <?php if(empty($dados_sel)){ ?>
        <tbody><tr><td valign="top" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
      <?php } else { ?>
        <?php for ($i=0; $i < count($dados_sel); $i++) { ?>
          <li><input type="hidden" name="ord_id[]" value="<?php echo $dados_sel[$i]["id_mod"]; ?>"><?php echo $dados_sel[$i]["cod_mod"]; ?>-<?php echo $dados_sel[$i]["titulo_mod"]; ?></li>
        <?php } ?>
      <?php } ?> 
    </ul>
    <input type="submit" class="botaoagm" value="Salvar" >
  </form>

  <div class="divisoria01"></div>
      
</div>
