<?php include_once("../../../navegacao.php");?>

<script language="JavaScript" src="<?php echo $root; ?>scripts/handlebars.js"></script>

<?php
$ped = explode("/module/", $_SERVER["REQUEST_URI"]); 
$url_mods = $ped[0]."/module/AjaxModules.php";

?>

<script>
  $(document).ready(function(){
    carrega_mods();

    $("#sub_prods").on("change", function(){
      carrega_mods();
    })
  })

  function carrega_mods(){
    var source   = $("#entry-template").html();
    var template = Handlebars.compile(source);
    var id = $("#sub_prods").val();

    $.ajax({
      type: "POST",
      url: '<?php echo urlencode($url_mods); ?>',
      data: {id: id},
      success: function(data) {
        var html_h = '';
        $.each(data.mods, function(index, value){
          var context = {id_mod: value.id, cod_mod: value.cod, nome_mod: value.titulo, html_checked: value.html_checked};
          html_h += template(context);
        })

        $("#js_tbl_mods").find("tbody").html(html_h);
      }
    })
  }
</script>

<script id="entry-template" type="text/x-handlebars-template">

  <tr>
    <td><input type="checkbox" name="mods[]" value="{{id_mod}}" {{html_checked}}></td>
    <td>{{cod_mod}} - {{nome_mod}}</td>
  </tr>

</script>


<div class="conteudopagina">
  <ul class="aba_inter">
    <li>
      <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php">Cadastro de m&oacute;dulos</a>
    </li>
    <li>
      <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php?comm=ordem">Ordena&ccedil;&atilde;o</a>
    </li>
    <li  class="active">
      <a href="<?php echo $host; ?>src/role/module/ModuleSystem.php?comm=relacionamento">Relacionamento com Subprodutos</a>
    </li>
  </ul>
  <br style="clear:both">
  <br style="clear:both">
  <hr style="height: 20px;border: none;border-top: 1px solid #ddd;margin-top: -9px;">
  
  <?php $sel = isset($_GET["sub_prod"]) ? $_GET["sub_prod"] : 0; ?>

  <form action="<?php echo $host; ?>src/role/module/ModuleSystem.php?comm=insere_relac" method="post" style="width:100%">
    <select name="sub_prods" id="sub_prods">
      <option value="">Selecione</option>
      <?php for ($i=0; $i < count($dados_sel); $i++) { ?>
        <?php if($sel == $dados_sel[$i]["id_prod"]){ ?>
          <option value="<?php echo $dados_sel[$i]["id_prod"]; ?>" selected='selected'><?php echo $dados_sel[$i]["cod_prod"]; ?></option>
        <?php } else { ?>
          <option value="<?php echo $dados_sel[$i]["id_prod"]; ?>"><?php echo $dados_sel[$i]["cod_prod"]; ?></option>
        <?php } ?>
      <?php } ?>
    </select>

    <table id="js_tbl_mods">
      <thead>
        <th></th>
        <th>M&oacute;dulo</th>
      </thead>
      <tbody>
        <tr>
          <td colspan='3'>Escolha um Subproduto</td>
        </tr>
      </tbody>
    </table>
    <input type="submit" class="botaoagm" value="Salvar" >
  </form>
  <div class="divisoria01"></div>
      
</div>
