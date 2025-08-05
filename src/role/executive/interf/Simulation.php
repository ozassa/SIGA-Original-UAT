<?php  require_once("../tarif/Simul.php");
  
  //Alterador por Tiago V N - Elumini - 05/06/2006
  $query = "select * from Inform where id = $idInform";
  $cur = odbc_exec($db, $query);
  $nMoeda = odbc_result($cur, "currency");

  if ($nMoeda == "2") {
     $extMoeda = "US$";
  }else{
     $extMoeda = "€";
  }

?>
<div class="conteudopagina">
<h3>Sr.Cliente, Informamos:</h3>
    <ul>
        <li>Taxa de Pr&ecirc;mio - Indicativa : <?php   echo number_format ($T5 * 100, 3, ",", "");?>%</u>
        <li>Pr&ecirc;mio M&iacute;nimo - Indicativo  : <?php   echo $extMoeda?> <?php   echo number_format ($PM, 2, ",", ".");?></u>
    </ul>
<div class="divisoria01"></div>
<p>Vale ressaltar que o referido valor &eacute; apenas uma mera proje&ccedil;&atilde;o "indicativa" do custo do pr&ecirc;mio do seguro, j&aacute; que &eacute; imprescind&iacute;vel uma an&aacute;lise  posterior e criteriosa dos importadores, para que na sequ&ecirc;ncia &agrave; sua concord&acirc;ncia, seja poss&iacute;vel formular-se uma proposta. Assim sendo e t&atilde;o logo a an&aacute;lise seja finalizada, estaremos remetendo-lhe uma cota&ccedil;&atilde;o firme do Seguro de Cr&eacute;dito &agrave; Exporta&ccedil;&atilde;o, para sua aprecia&ccedil;&atilde;o final e aguardaremos o seu "de acordo" na expectativa de que possamos encaminhar-lhe os documentos originais para assinatura e formaliza&ccedil;&atilde;o da contrata&ccedil;&atilde;o em quest&atilde;o.

Desde j&aacute; nos colocamos &agrave; sua disposi&ccedil;&atilde;o para quaisquer esclarecimentos que se fizerem necess&aacute;rios.
</p>

<form action="<?php   echo $root;?>role/executive/Executive.php" method="post" name="">
<input type="hidden" name="idInform" value="<?php   echo $field->getField("idInform");?>">
<input type="hidden" name="idNotification" value="<?php   echo $field->getField("idNotification");?>">
<input type="hidden" name="comm" value="view">
    <div class="barrabotoes">
        <button name="ok" type="submit" class="botaoagm">OK</button>
    </div>
</form>
</div>