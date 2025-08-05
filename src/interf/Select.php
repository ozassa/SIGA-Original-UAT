<?php  

//alterado hicom mes 04
$hc_desc_select = ""; 
$acao = isset($acao) ? $acao : '';
$name = isset($name) ? $name : '';
$disabled = isset($disabled) ? $disabled : '';
?>
<select name="<?php  echo $name;?>" <?php  echo $acao;?> <?php echo $disabled ?> >


<?php  $c = odbc_exec($db,$sql);
  
  $first = true;
  while (odbc_fetch_row($c)) {
    $atu = odbc_result($c,1);
    $selec = ($atu == $sel) ? 1 : 0;
	if ($atu == $sel)
	{
	   $hc_desc_select = odbc_result($c,2);
	}
	
?>
<?php

  if (!$disabled) {
	if ($first && $empty != '') {
	  $first = false;
?>
<option value="0"><?php  echo $empty;?></option>
<?php  }?>
<option value="<?php  echo $atu;?>"<?php  echo $selec ? " selected" : "";?>><?php echo odbc_result($c,2);?></option>
<?php  } else if ($selec) echo odbc_result($c,2);
  }
?>
<?php  if (!$disabled) { ?>
</select>
<?php  } ?>
