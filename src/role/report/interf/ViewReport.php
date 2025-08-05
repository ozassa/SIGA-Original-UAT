<?php include_once("../../../navegacao.php");?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">
  
  <?php 
    if ($dados) { 
      for ($i=0; $i < count($dados); $i++) { 
        if ($dados[$i]['i_Processamento'] == 1) {
          $link = '../report/Report.php?comm=policyReport';
        } else if ($dados[$i]['i_Processamento'] == 2) {
          $link = '../report/Report.php?comm=fullPolicyReport';
        } else {
          $link = '#';
        } ?>
        <a href="<?php echo $link; ?>"><?php echo $dados[$i]['Descricao']; ?></a><br>
    <?php } ?>
  <?php } ?>

  <div class="divisoria01"></div>
      
</div>