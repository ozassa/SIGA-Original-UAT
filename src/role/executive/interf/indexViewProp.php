<?php  // Alterado Hicom (Gustavo) - 03/01/05 - exibir proposta e fatura com texto "Sem valor" para o executivo

// frame para conferir os pdf's
?>

<frameset border=no rows="85%,15%">

<frame src="<?php echo $root;?>download/<?php  echo $key. $file;?>SemValor.pdf" name="main">
<frame src="buttons.php?idInform=<?php   echo $idInform;?>&idNotification=<?php   echo $idNotification;?>&file=<?php   echo $file;?>&key=<?php   echo $key;?>" name="secundario">
</frameset>
<noframes></noframes>

<?php  

exit;
?>
