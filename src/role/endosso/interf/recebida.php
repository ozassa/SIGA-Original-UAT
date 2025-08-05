<a name=endosso></a>
<p>Agora você pode imprimir o endosso e a carta de encaminhamento:

<ul>
<li><a href="<?php echo $root;?>role/endosso/EndDadosCadast.php?idInform=<?php echo $idInform;?>&idEndosso=<?php echo $idEndosso;?>" target=_blank>Endosso</a>
<li><a href="<?php echo $root;?>role/endosso/cartaEncaminhamento.php?idInform=<?php echo $idInform;?>&idEndosso=<?php echo $idEndosso;?>" target=_blank>Carta de encaminhamento</a>
</ul>


<FORM action="<?php echo $root;?>role/endosso/Endosso.php" method="post">
<input type=hidden name="comm">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">

<p align="center">
 <input class="servicos" type=button value="Voltar" onClick="this.form.comm.value='view';this.form.submit()">
</p>

</form>