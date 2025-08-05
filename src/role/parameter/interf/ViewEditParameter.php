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
<!-- CONTEÚDO PÁGINA -->
<form id="Form2" name="Form2" action="<?php echo $host;?>src/role/parameter/ParameterSystem.php?comm=cad_par" method="post">
  <input type="hidden" id="empresaID" name="empresaID" value="<?php echo $empresaID; ?>">
  <input type="hidden" id="id_par" name="id_par" value="<?php echo $parametroID; ?>">
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

    <li class="campo2colunas">
      <label>Par&acirc;metro</label>
      <?php echo $Parametro; ?>
    </li>

    <li class="campo2colunas">
      <label>Numero</label>
      <input name="numeroParametro" id="numeroParametro" type="text" value="<?php echo $Numero; ?>" />
    </li>

    <li class="campo2colunas">
      <label>Valor R$:</label>
      <input name="valorParametro" id="valorParametro" type="text" style="text-align: right;" onBlur="checkDecimals(this, this.value)" value="<?php echo number_format($Valor, 2, ",", "."); ?>" />
    </li>

    <li class="campo2colunas">
      <label>Data</label>
      <input name="dataParametro" id="dataParametro" type="text" onKeyUp="mascara(this,data);" maxlength="10" style="width:245px;" value="<?php echo $Data ? date('d/m/Y', strtotime($Data)) : ""; ?>" />
      <img src="<?php echo $host; ?>images/icone_calendario.png" name="imgDataParametro" id="imgDataParametro" alt="" class="imagemcampo" />
      <script type="text/javascript">
        Calendar.setup({
          inputField     :    "dataParametro",      // id of the input field
          ifFormat       :    "dd/mm/y",        // format of the input field
          button         :    "imgDataParametro",   // trigger for the calendar (button ID)
          align          :    "Tl",             // alignment (defaults to "Bl")
          singleClick    :    true
        });
      </script>
    </li>

    <br style="clear:both">
    <li class="campo2colunas" style="margin-bottom: 170px;">
      <label>Texto</label>
      <textarea class="textarea" name="textoParametro" id="textoParametro" maxlength="200" placeholder="Enter text ..." style="width: 915px; height: 150px;"><?php echo $Texto; ?></textarea>
    </li>

    <?php $emp = $empresaID == 1 ? "coface" : "sbce"; ?>

    <div class="barrabotoes">
      <button class="botaovgm" type="button" onclick="location.href='<?php echo $host."src/role/parameter/ParameterSystem.php?comm=".$emp; ?>'">Voltar</button>
      <button class="botaoagg" type="button" onClick="this.form.submit();">Salvar</button>
    </div>

  </div>

</form>