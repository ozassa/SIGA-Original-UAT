<?php 
	include_once('../../../navegacao.php');

	$idNotification   = $_REQUEST["idNotification"];

    	$sql	= "SELECT name FROM Inform WHERE id='$idInform'";
    	$cur 	= odbc_exec($db, $sql);
    	$nome 	= odbc_result($cur, 1);
	
	$sqli 	= "SELECT * FROM Importer ip, Country c where ip.idInform = $idInform And 
			ip.idCountry=c.id order by ip.name";	  
	$cur 	= odbc_exec($db, $sqli);
?>

<form id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/searchApolice.php" method="post">
	<div class="conteudopagina">
		<ul>
			<li class="campo2colunas">
    			<label>Nome Segurado</label>
        		<?php  echo ($nome);?>
    		</li>
		</ul>
    	<div class="divisoria01"></div>
		<table id="example">
			<thead>
				<tr>
						<th>Comprador</th>
						<th>C&oacute;digo País</th>
						<th>CRS</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$tp ="0";
				while(odbc_fetch_row($cur)){
						if ($tp == "0") {
							$tp = "1";
						}else{
							$tp = "0";
						}
		
						echo "<tr>";
						echo "<td>".odbc_result($cur, "name")."</td>";
						echo "<td>".odbc_result($cur, "code")."</td>";
						echo "<td>".odbc_result($cur, "c_Coface_Imp")."</td>";
						echo "</tr>";
				}
				?>
			</tbody>
		</table>

		<div class="barrabotoes">
			<input type="hidden" name="comm" value="notif">
			<input type="hidden" name="idNotification">
			<input type="hidden" name="idInform">
			<button name="voltar" type="submit" class="botaovgm">Voltar</button>
			<button onClick="notific(Form2)" class="botaoagg">Ocultar Notifica&ccedil;&atilde;o</button>
		</div>
	</div>
</form>

<script language="javascript">
	function notific(form){
    document.all.comm.value = 'notig';
    document.all.idNotification.value = '<?php echo htmlspecialchars($idNotification, ENT_QUOTES, 'UTF-8'); ?>';
    document.all.idInform.value = '<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>';
    form.submit();
}

</script>