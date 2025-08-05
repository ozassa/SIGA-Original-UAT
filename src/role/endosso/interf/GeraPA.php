<div align=center>
<p>Parcela de Ajuste

<form action="<?php echo $root;?>role/endosso/Endosso.php" method=post>
<input type=hidden name=comm value="naogeradoPA">
<input type=submit class=sair value="Voltar">
</form>
</div>

<?php if($msg){
  echo "<p align=center><font color=#ff0000>$msg</font>";
}
?>
