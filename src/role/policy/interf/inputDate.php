<?php 
	if(isset($idInform) && $idInform != null){
		$r=odbc_exec($db, "SELECT dateAceit FROM Inform WHERE id = $idInform");
		$dateAceit = odbc_result($r, 1);
	}
?>

<script language="JavaScript" src="<?php echo $root;?>scripts/calendario.js"></script>
<script Language="JavaScript">
<!--
function checa_formulario(conf_date){
	if (conf_date.date.value == ""){
		verErro("Por Favor, Preencha a Data de Aceitação");
		conf_date.date.focus();

		return (false);
	}

	return (true);
}
//-->

</script>

<?php require_once("../../../navegacao.php");?>
	<!-- CONTEÚDO PÁGINA - INÍCIO -->
	<div class="conteudopagina">
	<table summary="Submitted table designs">

	<?php	   
		if($msgData){ ?>
			<caption><?php echo $msgData;?></caption>
		<?php } ?>

		<thead>
			<tr>
				<th scope="col">Segurado</th>
				<th scope="col">DPP</th>
				<th scope="col">Moeda</th>
				<th scope="col">Data de aceitação</th>
				<th scope="col">Opções</th>
			</tr>
		</thead>
	<?php 
       $sql = "SELECT inf.id, inf.name, Moeda.Sigla, inf.contrat, inf.dateAceit FROM Inform as inf
       		Inner Join Moeda Moeda On Moeda.i_Moeda = inf.currency
       		WHERE (inf.state = 6 or inf.state = 7) AND inf.mailOk = 1";     
		$cur=odbc_exec($db, $sql);
		
		//print $sql;
            
	?>
            
	<tbody>
	<?php 
		$i = 0;
		

		while (odbc_fetch_row($cur)) {
			$i ++;	
			$data = odbc_result($cur,5);
			?>
            		<form action="../policy/Policy.php" name="frm<?php echo $i;?>" method="post">
                	<input type="hidden" name="comm" value="gravaData">
                	<input type="hidden" name="idInform" value="<?php echo odbc_result($cur,1); ?>">
                	<input type="hidden" name="nomeEmp" value="<?php echo odbc_result($cur,2); ?>">
            
                	<tr >
                    		<td><?php echo (odbc_result($cur,2));?></td>
                    		<td><?php echo (odbc_result($cur,4));?></td>
                    		<td><?php echo (odbc_result($cur,3));?></td>
                    		<td><?php echo substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4); ;?></td>
                    		<td><button class="botaoagg" type="button"  onClick="javascript:frm<?php echo $i;?>.submit();">Emitir Ap&oacute;lice</button></td>
                	</tr>
            
            		</form>				     
		<?php }

		if ($i == 0) {
			?>
			<tr>
				<td align="center" colspan=2>Nenhuma Proposta Encontrada</td>
			</tr>
		<?php }?>
	</tbody>
	</table>
	</div>