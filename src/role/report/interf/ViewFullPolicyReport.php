<?php include_once("../../../navegacao.php");?>

<script>
  function mascara(o,f){
    v_obj = o;
    v_fun = f;
    setTimeout("execmascara()", 1);
  }
  
  function execmascara(){
    v_obj.value = v_fun(v_obj.value);
  }
  
  function data(v){
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d{2})(\d)/, "$1/$2");
    v = v.replace(/(\d{2})(\d)/, "$1/$2");
    
    return v
  }
</script>

<style>  
  thead th {
    font-size: 10px;
  }
</style>

<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $host?>src/scripts/calendario/calendar-win2k-cold-1.css" title="win2k-cold-1" />
<script type="text/javascript" src="<?php echo $host?>src/scripts/calendario/calendar.js"></script>
<script type="text/javascript" src="<?php echo $host?>src/scripts/calendario/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?php echo $host?>src/scripts/calendario/calendar-setup.js"></script>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">
  <?php if (!isset($_REQUEST['buscar'])) { ?>
    
    <form id="Form2" name="Form2" action="<?php echo $host;?>src/role/report/Report.php?comm=fullPolicyReportDetail" method="post">
      <input type="hidden" name="buscar" value="buscar">

      <li class="campo3colunas">
        <label>Data de In&iacute;cio de Vig&ecirc;ncia:</label>
        <input type="text"  name="d_Inicio_Vigencia" id="d_Inicio_Vigencia" value="" onKeyUp="mascara(this,data);" maxlength="10" style="width:190px;">
        <img src="<?php echo $host; ?>images/icone_calendario.png" name="imgDtVigIni" id="imgDtVigIni" alt="" class="imagemcampo" />
        <script type="text/javascript">
          Calendar.setup({
            inputField     :    "d_Inicio_Vigencia",   // id of the input field
            ifFormat       :    "dd/mm/y",            // format of the input field
            button         :    "imgDtVigIni",        // trigger for the calendar (button ID)
            align          :    "Tl",                 // alignment (defaults to "Bl")
            singleClick    :    true
          });
        </script>
      </li>

      <li class="campo3colunas">
        <label>Data de Fim de Vig&ecirc;ncia:</label>
        <input type="text"  name="d_Fim_Vigencia" id="d_Fim_Vigencia" value="" onKeyUp="mascara(this, data);" maxlength="10" style="width:190px;">
        <img src="<?php echo $host; ?>images/icone_calendario.png" name="imgDtVigFim" id="imgDtVigFim" alt="" class="imagemcampo" />
        <script type="text/javascript">
          Calendar.setup({
            inputField     :    "d_Fim_Vigencia",   // id of the input field
            ifFormat       :    "dd/mm/y",            // format of the input field
            button         :    "imgDtVigFim",        // trigger for the calendar (button ID)
            align          :    "Tl",                 // alignment (defaults to "Bl")
            singleClick    :    true
          });
        </script>
      </li>
      
      <li class="barrabotoes" style="list-style:none;*margin-left:-15px;*margin-top:20px;">
        <button class="botaovgm" type="button" onClick="window.location = '<?php echo $host;?>src/role/report/Report.php?comm=index';">Voltar</button>
        <button type="submit" class="botaoagm">Filtrar</button>
      </li>
    </form>

  <?php } else { ?>
    <table summary="" id="" style="font-size: 9px;">
      <thead>
        <tr>
          <th>Regi&atilde;o</th>
          <th>Ag&ecirc;ncia</th>
          <th>N&ordm; da Ap&oacute;lice</th>
          <th>Segurado</th>
          <th>Ini. Vig&ecirc;ncia</th>
          <th>Fim. Vig&ecirc;ncia</th>
          <th>Valor Total de Pr&ecirc;mio Emitido</th>
          <th>Valor Total de Pr&ecirc;mio Pago</th>
          <th>Valor Total dos Sinistros Pagos</th>
          <th>Valor Total de Sinistro em Reserva</th>
          <th>Valor do Limite M&aacute;ximo de Indeniza&ccedil;&atilde;o da Ap&oacute;lice</th>
          <th>Saldo Dispon&iacute;vel do Limite M&aacute;ximo de Indeniza&ccedil;&atilde;o da Ap&oacute;lice</th>
        <tr>
      </thead>

      <?php if(empty($dados)){ ?>
        <tbody><tr><td valign="top" colspan="20" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
      <?php } else { ?>
        <?php for ($a=0; $a < count($dados); $a++) { ?>
          </tr>
            <td><?php echo $dados[$a]['Nome_Regiao']; ?></td>
            <td><?php echo $dados[$a]['Nome_Agencia']; ?></td>
            <td><?php echo $dados[$a]['n_Apolice']; ?></td>
            <td><?php echo $dados[$a]['Segurado']; ?></td>
            <td><?php echo $dados[$a]['d_Inicio_Vigencia']; ?></td>
            <td><?php echo $dados[$a]['d_Fim_Vigencia']; ?></td>
            <td><?php echo $dados[$a]['Sigla_Moeda']; ?> <?php echo $dados[$a]['v_Premio_Emitido']; ?></td>
            <td><?php echo $dados[$a]['Sigla_Moeda']; ?> <?php echo $dados[$a]['v_Premio_Pago']; ?></td>
            <td><?php echo $dados[$a]['Sigla_Moeda']; ?> <?php echo $dados[$a]['v_Sinistro_Pago']; ?></td>
            <td><?php echo $dados[$a]['Sigla_Moeda']; ?> <?php echo $dados[$a]['v_Sinistro_Pendente']; ?></td>
            <td><?php echo $dados[$a]['Sigla_Moeda']; ?> <?php echo $dados[$a]['v_LMI']; ?></td>
            <td><?php echo $dados[$a]['Sigla_Moeda']; ?> <?php echo $dados[$a]['v_LMI_Disponivel']; ?></td>          
          </tr>
        <?php } ?>
      <?php } ?> 
    </table>
    <div class="barrabotoes">
      <button class="botaovgm" type="button" onClick="window.location = '<?php echo $host;?>src/role/report/Report.php?comm=fullPolicyReport';">Voltar</button>
      <button type="button" class="botaoagg" onClick="window.open('<?php echo $root; ?>role/report/interf/PolicyReportExcel.php?d_Inicio_Vigencia=<?php echo str_replace("'", "", $SPR_d_Inicio_Vigencia) ; ?>&d_Fim_Vigencia=<?php echo str_replace("'", "", $SPR_d_Fim_Vigencia); ?>', '_blank')">Exportar para Excel</button>
    </div>  

  <?php } ?>
</div>