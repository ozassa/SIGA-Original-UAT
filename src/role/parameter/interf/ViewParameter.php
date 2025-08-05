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

<script language="JavaScript" src="<?php echo $root; ?>scripts/utils.js"></script>

<script>
  $(document).ready(function(){
    $('#textoParametro').wysihtml5({
      "font-styles": false, //Font styling, e.g. h1, h2, etc. Default true
      "emphasis": true, //Italics, bold, etc. Default true
      "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
      "html": false, //Button which allows you to edit the generated HTML. Default false
      "link": false, //Button to insert a link. Default true
      "image": false, //Button to insert an image. Default true,
      "color": false //Button to change color of font  
    });
  })

  function mascara(o,f){
    v_obj=o
    v_fun=f
    setTimeout("execmascara()",1)
  }
  
  function execmascara(){
    v_obj.value=v_fun(v_obj.value)
  }
  
  function data(v){
    v=v.replace(/\D/g,"")
    v=v.replace(/(\d{2})(\d)/,"$1/$2")
    v=v.replace(/(\d{2})(\d)/,"$1/$2")

    return v
  }
 </script>

<input type="hidden" id="js_msg_exc" value="Deseja mesmo excluir este m&oacute;dulo?">

<?php include_once("../../../navegacao.php");?>
<!-- CONTEÚDO PÁGINA -->
  
<input type="hidden" id="empresaID" name="empresaID" value="<?php echo $empresaID; ?>">
<div class="conteudopagina">    
  <ul class="aba_inter">
    <li <?php echo $empresaID == 1 ? 'class="active"': ''; ?>>
      <a href="<?php echo $host; ?>src/role/parameter/ParameterSystem.php?comm=coface">Coface</a>
    </li>
    <li <?php echo $empresaID == 2 ? 'class="active"': ''; ?>>
      <a href="<?php echo $host; ?>src/role/parameter/ParameterSystem.php?comm=sbce">SBCE</a>
    </li>    
  </ul>

  <br style="clear:both">
  <br style="clear:both">
  <hr style="height: 20px;border: none;border-top: 1px solid #ddd;margin-top: -9px;">
  
  <table summary="" id="">
    <thead>
      <tr>
        <th>Parametro</th>
        <th>Numero</th>
        <th>Valor</th>
        <th>Data</th>
        <th>Texto</th>
        <th colspan="2">Op&ccedil;&atilde;o</th>       
      </tr>
    </thead>  

    <?php if(empty($dados_sel)){ ?>
      <tbody><tr><td valign="top" colspan="7" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
    <?php } else { ?>
      <?php for ($i=0; $i < count($dados_sel); $i++) { ?>
        <tr>
          <td>
            <a href="<?php echo $host;?>src/role/parameter/ParameterSystem.php?comm=edit_par&id_par=<?php echo $dados_sel[$i]["i_Parametro"]; ?>&id_emp=<?php echo $empresaID; ?>"><?php echo $dados_sel[$i]["Parametro"]; ?></a>
          </td>
          <td><?php echo $dados_sel[$i]["Numero"]; ?></td>
          <td><?php echo number_format($dados_sel[$i]["Valor"], 2, ",", "."); ?></td>
          <td><?php echo $dados_sel[$i]["Data"] ? date('d/m/Y', strtotime($dados_sel[$i]["Data"])) : ""; ?></td>
          <td><?php echo $dados_sel[$i]["Texto"]; ?></td>
          <td>
            <a href="<?php echo $host;?>src/role/parameter/ParameterSystem.php?comm=edit_par&id_par=<?php echo $dados_sel[$i]["i_Parametro"]; ?>&id_emp=<?php echo $empresaID; ?>" class="btn">Editar</a>
          </td>
        </tr>
      <?php } ?>
    <?php } ?>    
  </table>

  <div class="divisoria01"></div>
      
</div>
