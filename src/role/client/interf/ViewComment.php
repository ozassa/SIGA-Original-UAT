<?php require_once("../../../navegacao.php");?>
<?php
	$x = odbc_exec($db,"select u.name, i.texto, i.date
		from InformComment i join Users u on i.idUser=u.id
		where i.id=$idComment");
		
	$autor = odbc_result($x, 1);
	$texto = odbc_result($x, 2);
	$data = ymd2dmy(odbc_result($x, 3));

	$x = odbc_exec($db, "select name from Inform where id=$idInform");
	$name = odbc_result($x, 1);
?>

<div class="conteudopagina">
	<li class="campo2colunas">
		<label>Cliente</label>
    	<?php  echo ($name);?>
	</li>

	<li class="campo2colunas">
		<label>Coment&aacute;rio enviado</label>
    	<?php  echo $data;?> por <?php  echo ($autor);?>
	</li>
</div>

<div class="conteudopagina">
	<p><?php  echo nl2br($texto);?></p>

	<form action="<?php  echo $root;?>role/client/Client.php" method=post>
		<input type="hidden" name="comm" value="comments">
		<input type="hidden" name="idInform" value="<?php  echo $idInform;?>">
		<div class="barrabotoes">
    		<button class="botaoagm" type="submit">Voltar</button>
		</div>
	</form>
</div>