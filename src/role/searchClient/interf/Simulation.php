
<?php 
  require_once("../tarif/Simul.php");

  //Alterado por Tiago V N - Elumini - 10/04/2006
  $cur=odbc_exec(
    $db,
    "SELECT * FROM Inform WHERE id = ".$field->getField("idInform")
  );

  if (odbc_result($cur, 'currency') == 2) {
     $ext = "US$";
  }else if (odbc_result($cur, 'currency') == 6) {
     $ext = "€";
  }
  
?>
<?php require_once("../../../navegacao.php");?>
<div class="conteudopagina">
<p>Sr.Cliente, Informamos:</p>

<ul>
	<li><label>Taxa de Pr&ecirc;mio - Indicativa : <?php echo number_format ($T5 * 100, 3, ",", ""); ?>%</label></li>
	<li><label>Pr&ecirc;mio M&iacute;nimo  - Indicativo  : <?php echo $ext?> <?php echo number_format ($PM, 2, ",", "."); ?></label></li>
</ul>
<p>Vale ressaltar que o referido valor &eacute; apenas uma mera proje&ccedil;&atilde;o "indicativa" do custo do pr&ecirc;mio do seguro, j&aacute; que &eacute; imprescind&iacute;vel uma an&aacute;lise posterior e criteriosa dos importadores, para que na sequ&ecirc;ncia &agrave; sua concord&acirc;ncia, seja poss&iacute;vel formular-se uma proposta.
Assim sendo e t&atilde;o logo a an&aacute;lise seja finalizada, estaremos remetendo-lhe uma cota&ccedil;&atilde;o firme do Seguro de Cr&eacute;dito &agrave; Exporta&ccedil;&atilde;o, para sua aprecia&ccedil;&atilde;o final e aguardaremos 
o seu "de acordo" na expectativa de que possamos encaminhar-lhe os documentos originais para assinatura e formaliza&ccedil;&atilde;o da contrata&ccedil;&atilde;o em quest&atilde;o.
Desde j&aacute; nos colocamos &agrave; sua disposi&ccedil;&atilde;o para quaisquer esclarecimentos que se fizerem necess&aacute;rios.
</p>
<form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post" name="">
<input type="hidden" name="idInform" value="<?php echo $field->getField("idInform"); ?>">
<input type="hidden" name="comm" value="open">
<div class="barrabotoes">
    <button type="submit" name="ok_bt" class="botaoagm">OK</button>
</div>
</form>
</div>