<script>
	$(document).ready(function(){
		$(".js-check_parametro").on("click", function(){
			if ($(this).is(":checked")) {
				$(this).parent().parent().find(".Num_Parametro").val("1");
			} else {
				$(this).parent().parent().find(".Num_Parametro").val("0");
			}
		});
	});
</script>

<br clear="all">

<li class="barrabotoes" style="list-style:none;*margin-left:-15px;" id="js-title">   
	<label><h2>Par&acirc;metros da Certifica&ccedil;&atilde;o Digital</h2></label>
</li>

<?php 
	$sql = "SELECT Inf.id, P.i_Parametro, P.Parametro, ISNULL(IP.n_Parametro, ISNULL(PE.n_Parametro, ISNULL(P.n_Parametro, 0))) AS Num_Parametro
						FROM Inform Inf
							INNER JOIN Parametro_Empresa PE ON PE.i_Empresa = Inf.i_Empresa AND PE.i_Parametro Between 10000 AND 11000	-- Parâmetros relativos à Cessão de Direito
							INNER JOIN Parametro P ON P.i_Parametro = PE.i_Parametro 
							LEFT JOIN Inform_Parametro IP ON IP.i_Inform = Inf.id AND IP.i_Parametro = PE.i_Parametro
						WHERE Inf.id = ".$idInform;
	$rsParam = odbc_exec ($db, $sql); 
?>

<div style="clear:left">
	<table width="100%">
	  <thead>
		  <tr>
		  	<th scope="col">Par&acirc;metro</th>
		   	<th scope="col">Sele&ccedil;&atilde;o</th>
	  	</tr>
	  </thead>
	  <tbody>  
	  	<?php 
	  		while (odbc_fetch_row($rsParam)){
	  			$i_Parametro = odbc_result($rsParam, 'i_Parametro');
					$Parametro = odbc_result($rsParam, 'Parametro');
					$Num_Parametro = odbc_result($rsParam, 'Num_Parametro');
					if ($Num_Parametro == "1") {
					 	$val = "1";
					 	$checked = "checked";
					} else {
					 	$val = "0";
					 	$checked = "";
					} ?>
				  <tr>
				  	<input type="hidden" name="i_Parametro[]" class="i_Parametro" value="<?php echo $i_Parametro; ?>">
				  	<input type="hidden" name="Num_Parametro[]" class="Num_Parametro" value="<?php echo $val; ?>">
					  <td><?php echo $Parametro; ?></td>
						<td><input type="checkbox" class="js-check_parametro" <?php echo $checked; ?>></td>
					</tr>
			<?php } ?>
		</tbody>
	</table>  
</div>