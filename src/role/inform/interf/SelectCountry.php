<?php $cur = odbc_exec($db, "Select id, name from Country order by id");
?>
	<select name=idCountry>
<?php while(odbc_fetch_row($cur)){
		$id 	= odbc_result($cur, 1);
		$name	= odbc_result($cur, 2);
?>
	<option <?php echo $id==$idCountry ? " selected  " : " "; ?> value=<?php echo $id; ?>><?php echo $name; ?></option>;
<?php }
?>
	</select>