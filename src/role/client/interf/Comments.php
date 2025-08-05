<?php require_once("../../../navegacao.php"); ?>

<div class="conteudopagina">
	<form action="<?php echo $root; ?>role/client/Client.php" method="post">
		<label>Incluir coment&aacute;rio</label>
		<input type="hidden" name="comm" value="comments">
		<input type="hidden" name="insert" value="1">
		<input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
		<div style="width:500px">
			<textarea name="comentario"></textarea>
		</div>

		<div class="barrabotoes">
			<button class="botaoagm" onClick="volta(this.form)">Voltar</button>
			<button type="submit" class="botaoagm">Incluir</button>
		</div>
	</form>
</div>

<div class="conteudopagina">
	<table id="example">
		<caption>Coment&aacute;rios deste cliente</caption>
		<thead>
			<tr>
				<th>Data</th>
				<th>Autor</th>
				<th>&nbsp;</th>
			</tr>
		</thead>

		<tbody>
			<?php
			//$query = "select i.id, u.name, i.texto, i.date from InformComment i join Users u on i.idUser=u.id where i.idInform=$idInform";
			
			//$q = "select idAnt from Inform where id = $idInform";
			//$x = odbc_exec($db, $q);
			
			//$q1 = "";
			
			//While (odbc_result($x, 1)) {
			//	$id1 = odbc_result($x, 1);  
			//	$q1 = $q1. " or i.idInform in ($id1)";
			//	$q = "select idAnt from Inform where id = $id1";
			//	$x = odbc_exec($db, $q);
			//}
			
			//$query = $query.$q1;
			
			if (!isset($idInform)) {
				$idInform = 0;
				if (isset($_REQUEST['idInform'])) {
					$idInform = $_REQUEST['idInform'];
				}
			}

			$idInform = intval($idInform); // Garante que $idInform seja um número inteiro para evitar injeção
			
			$query = "SELECT IC.id, U.name, IC.texto as txt, IC.date 
          FROM Inform Inf
          INNER JOIN Inform Inf2 ON Inf2.idInsured = Inf.idInsured 
          INNER JOIN InformComment IC ON IC.idInform = Inf2.id
          LEFT JOIN Users U ON U.id = IC.idUser
          WHERE Inf.id = ?
          ORDER BY IC.date";

			$x = odbc_prepare($db, $query);
			odbc_execute($x, [$idInform]);

			$i = 1;

			while (odbc_fetch_row($x)) {
				$idComment = odbc_result($x, 'id');
				$autor = odbc_result($x, 'name');
				// $texto = odbc_result($x, 'txt');
				// $texto = '';
				$data = ymd2dmy(odbc_result($x, 'date'));
				echo "<tr>";
				echo "<td>$data</td>";
				echo "<td>$autor</td>";
				echo "<td><a href=$root" .
					"role/client/Client.php?comm=viewComment&idInform=$idInform&idComment=$idComment>Exibir</a></td>";

				$i++;
			}

			odbc_free_result($x);
			?>
		</tbody>
	</table>
</div>

<script language=javascript>
	Function volta(f){
		f.action = '<?php echo $root; ?>role/searchClient/ListClient.php';
		f.comm.value = 'view';
		f.submit();
	}
</script>