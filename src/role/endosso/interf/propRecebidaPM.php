<a name=endosso></a>
<p> Agora você pode imprimir o endosso, a carta de encaminhamento e as parcelas:

<ul>
<?php if($mudou_natureza){ ?>
<li><a href="<?php echo $root;?>role/endosso/EndNatOper.php?idInform=<?php echo $idInform;?>&idEndosso=<?php echo $idEndosso;?>" target=_blank>Endosso de Natureza da Operação</a>
<?php }
  if($mudou_premio){ ?>
<li><a href="<?php echo $root;?>role/endosso/EndPremMin.php?idInform=<?php echo $idInform;?>&idEndosso=<?php echo $idEndosso;?>" target=_blank>Endosso de Prêmio Mínimo</a>
<?php } ?>
<li><a href="<?php echo $root;?>role/endosso/cartaEncaminhamento.php?idInform=<?php echo $idInform;?>&idEndosso=<?php echo $idEndosso;?>" target=_blank>Carta de encaminhamento</a>

<li>Parcelas geradas:
 <ul>
<?php if(count($parcelas) == 0){
  echo "<li>Nenhuma parcela foi gerada.";
}else{
  $i = 1;
  foreach($parcelas as $p){
    echo " <li><a href=\"$root".
      "role/endosso/parcela.php?idInform=$idInform&idParcela=$p[i_Parcela]&dataVenc=".
      urlencode("$p[d_Venc]"). "&parcela=$i&endosso=$idEndosso&num_parcelas=".
      count($parcelas). "&parc=$p[v_Parcela]" target=_blank>$i".
      "ª Parcela</a>\n";
    $i++;
  }
}
?>
 </ul>
</ul>

<FORM action="<?php echo $root;?>role/endosso/Endosso.php" method="post">
<input type=hidden name="comm">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">

<p align="center">
 <input class="servicos" type=button value="OK" onClick="this.form.comm.value='view';this.form.submit()">
</p>

</form>

<?php if($msg){
   echo "<p align=center><font color=#ff0000>$msg</font>";
 }
?>
