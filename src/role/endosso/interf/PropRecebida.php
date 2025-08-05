<a name=endosso></a>
<p> Agora você pode imprimir o endosso, a carta de encaminhamento e as parcelas:

<ul>
<li><a href="<?php echo $root;?>role/endosso/EndNatOper.php?idInform=<?php echo $idInform;?>&idEndosso=<?php echo $idEndosso;?>" target=_blank>Endosso</a>
<li><a href="<?php echo $root;?>role/endosso/carta.php?idEndosso=<?php echo $idEndosso;?>" target=_blank>Carta de encaminhamento</a>
<!-- falta fazer a parte das parcelas -->

<?php if($idPremio){ ?>
<li><a href="<?php echo $root;?>role/endosso/EndPremMin.php?idInform=<?php echo $idInform;?>&idEndosso=<?php echo $idEndosso;?>" target=_blank>Prêmio Mínimo</a>
<?php } ?>

</ul>


<FORM action="<?php echo $root;?>role/endosso/Endosso.php" method="post">
<input type=hidden name="comm">
<input type=hidden name="idInform" value="<?php echo $idInform;?>">

<p align="center">
 <input class="servicos" type=button value="Voltar" onClick="this.form.comm.value='view';this.form.submit()">
</p>

</form>