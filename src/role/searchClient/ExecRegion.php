<?php

//criado por Wagner 1/9/2008

$log_query = "";

//extract($_POST);
//extract($_GET);



if ($altera == "1") {

	$ncheck = count($check);
	$nrole = count($role);
	$arrLogDet = array();

	// Query parametrizada
	$sql = "DELETE FROM UserRegion WHERE idUser = ?";

	// Prepara a query
	$stmt = odbc_prepare($db, $sql);

	// Parâmetros a serem vinculados na query
	$params = array($idexecutivo);

	// Executa a query com os parâmetros
	if (odbc_execute($stmt, $params)) {
		// Sucesso na execução
	}

	/* foreach ($_POST['check'] as $value) {

		$sql = "Insert into UserRegion (idUser, idRegion) Values " .
			"('$idexecutivo', '$value')";
		if (odbc_exec($db, $sql)) {
			$log_query .= $sql;
		} else {
			$msg = "Erro em incluir region do executivo.";
		}


		$sql = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('14'," .
			"'$userID','$idexecutivo','" . date("Y") . "-" . date("m") . "-" . date("d") .
			"','" . date("H") . ":" . date("i") . ":" . date("s") . "')";
		if ($log_query) {
			$result = odbc_exec($db, $sql);
			if ($result) {
				$sql = "SELECT @@IDENTITY as id_Log";
				$cur = odbc_result(odbc_exec($db, $sql), 1);

				$narr = count($arrLogDet);
				for ($i = 0; $i <= $narr - 1; $i++) {
					$sql = " Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) " .
						"values ('$cur', 'Região', '" . $arrLogDet[$i] . "', 'Exlusão');";

					$rs = odbc_exec($db, $sql);

					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
					//CRIADO POR WAGNER
					// ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

					if ($rs) {
						$sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
						$cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
						$sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) " .
							"values ('$cur', '" . str_replace("'", "", $log_query) . "')";

						//echo $sql;
						odbc_exec($db, $sql);
					}//fim if	
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

				}
			} else {
			}
		}

	} */

	foreach ($_POST['check'] as $value) {
		// Prepara a query parametrizada para inserir na tabela UserRegion
		$sql = "INSERT INTO UserRegion (idUser, idRegion) VALUES (?, ?)";
		$stmt = odbc_prepare($db, $sql);
		if (odbc_execute($stmt, [$idexecutivo, $value])) {
			$log_query .= "INSERT INTO UserRegion (idUser, idRegion) VALUES ('$idexecutivo', '$value');";
		} else {
			$msg = "Erro ao incluir região do executivo.";
		}
	
		// Prepara a query parametrizada para inserir na tabela Log
		$sql = "INSERT INTO Log (tipoLog, id_User, Inform, data, hora) 
				VALUES ('14', ?, ?, ?, ?)";
		$stmt = odbc_prepare($db, $sql);
		$date = date("Y-m-d");
		$time = date("H:i:s");
		if (odbc_execute($stmt, [$userID, $idexecutivo, $date, $time])) {
			$sql = "SELECT @@IDENTITY as id_Log";
			$result = odbc_exec($db, $sql);
			$cur = odbc_result($result, 1);
	
			$narr = count($arrLogDet);
			for ($i = 0; $i < $narr; $i++) {
				// Prepara a query parametrizada para inserir em Log_Detalhes
				$sql = "INSERT INTO Log_Detalhes (id_Log, campo, valor, alteracao) 
						VALUES (?, ?, ?, ?)";
				$stmt = odbc_prepare($db, $sql);
				if (odbc_execute($stmt, [$cur, 'Região', $arrLogDet[$i], 'Exclusão'])) {
					// Obtem o id_detalhes
					$sql_id_detalhes = "SELECT @@IDENTITY AS id_detalhes";
					$result = odbc_exec($db, $sql_id_detalhes);
					$cur_detalhes = odbc_result($result, 1);
	
					// Prepara a query parametrizada para inserir em Log_Detalhes_Query
					$sql = "INSERT INTO Log_Detalhes_Query (id_detalhes, query) 
							VALUES (?, ?)";
					$stmt = odbc_prepare($db, $sql);
					$safe_log_query = str_replace("'", "", $log_query); // Evita problemas na query
					odbc_execute($stmt, [$cur_detalhes, $safe_log_query]);
				}
			}
		}
	}
	

	/*
		  if ($nrole >= $ncheck) { //Exluir a Region
				 foreach ($role as $key => $value) {
					
					  if (in_array($value, $check)){
						  
					  }else{
							  $sql = "Select description from Region where id='$value'";
							  
							  $cur = odbc_exec($db, $sql);
							  odbc_fetch_row($cur);
							  $name = odbc_result($cur, 1);
							  array_push($arrLogDet, $name);
							  $sql = "Delete from UserRegion where idUser='$idexecutivo' And idRegion='$value'";
							  //print $sql;	   
							  if (odbc_exec($db, $sql) ) {
							  
								  $log_query .=$sql;
							  
							  }else{
								   $msg = "Erro em excluir region do executivo.";
							  }
					  }
				 }//foreach
				 
				 
						  $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('14'," .
								  "'$userID','$idexecutivo','".date("Y")."-".date("m")."-".date("d").
								  "','".date("H").":".date("i").":".date("s")."')";
						  $result =  odbc_exec($db, $sql);
									   if ($result) {
										  $sql = "SELECT @@IDENTITY as id_Log";
										  $cur = odbc_result(odbc_exec($db, $sql), 1);

										   $narr = count($arrLogDet);
										   for ($i=0; $i<=$narr-1; $i++){
										   $sql = " Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
												   "values ('$cur', 'Região', '".$arrLogDet[$i]."', 'Exlusão');";

										   $rs = odbc_exec($db, $sql);
													   
											 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
											 //CRIADO POR WAGNER
											 // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
											 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
											 
											 if ($rs) {
												$sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
												$cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
												$sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
													"values ('$cur', '".str_replace("'","",$log_query)."')";
													
													//echo $sql;
													odbc_exec($db, $sql);
											 }//fim if	
											 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 
										   }
									   }else{
									   }


		 }else{

			 foreach ($check as $key => $value) {//Inseir Region
				 
				   if (in_array($value, $role)){
				   }else{
						  $sql = "Select description from Region where id='$value'";
						  $cur = odbc_exec($db, $sql);
						  odbc_fetch_row($cur);
						  $name = odbc_result($cur, 1);
						  array_push($arrLogDet, $name);
						  
						  $sql = "Insert into UserRegion (idUser, idRegion) Values ".
								 "('$idexecutivo', '$value')";
						  if (odbc_exec($db, $sql) ) {
						  
							  $log_query .=$sql;
						  
						  }else{
							   $msg = "Erro em incluir region do executivo.";
						  }
				   }

			 }

						   $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('14'," .
								   "'$userID','$idexecutivo','".date("Y")."-".date("m")."-".date("d").
								   "','".date("H").":".date("i").":".date("s")."')";

									   $result =  odbc_exec($db, $sql);
									   if ($result) {
										  $sql_id = "SELECT @@IDENTITY as id_Log";
										  $cur = odbc_result(odbc_exec($db, $sql_id), 1);

										   $narr = count($arrLogDet);
										   for ($i=0; $i<=$narr-1; $i++){
										   $sql = " Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
												   "values ('$cur', 'Região', '".$arrLogDet[$i]."', 'Inclusão');";
											 $rs = odbc_exec($db, $sql);
														 
												 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
												 //CRIADO POR WAGNER
												 // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
												 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
												 
												 if ($rs) {
													$sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
													$cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
													$sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
														"values ('$cur', '".str_replace("'","",$log_query)."')";
														
														//echo $sql;
														odbc_exec($db, $sql);
												 }//fim if	
												 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
											 
										   }
										   
										   
									   }
										
		 }//if maior
		 
		 */

}//if alterar


// Consulta 1: Busca o nome do usuário
$usql = "SELECT name FROM Users WHERE id = ?";
$stmt1 = odbc_prepare($db, $usql);
$params1 = array($idexecutivo);

if (odbc_execute($stmt1, $params1)) {
	if (odbc_fetch_row($stmt1)) {
		$name = odbc_result($stmt1, "name");
	}
}

// Consulta 2: Busca os IDs da Região
$sql = "SELECT r.id 
        FROM UserRole ur
        JOIN Users us ON ur.idUser = us.id
        JOIN UserRegion usr ON us.id = usr.idUser
        JOIN Region r ON r.id = usr.idRegion
        WHERE ur.idRole = 2 AND us.id = ?";
$stmt2 = odbc_prepare($db, $sql);
$params2 = array($idexecutivo);

$arrRole = array();
if (odbc_execute($stmt2, $params2)) {
	while (odbc_fetch_row($stmt2)) {
		array_push($arrRole, odbc_result($stmt2, "id"));
	}
}

?>
<script language="javascript">
	function selecionaTodos(form) {
		for (var i = 0; i < form.elements.length; i++) {
			if (form.elements[i].type == 'checkbox') {
				form.elements[i].checked = form.todos.checked;
			}
		}
	}
</script>
<?php include_once("../../../navegacao.php"); ?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->

<div class="conteudopagina">
	<li class="campo2colunas">
		<label>Executivo</label>
		
	</li>
</div>

<form id="Form2" name="Form2" action="<?php echo $root; ?>role/searchClient/ListClient.php?comm=ExecRegion"
	method="post">

	<div class="conteudopagina">
		<table summary="Submitted table designs">
			<thead>
				<tr>
					<th colspan="6">Regi&otilde;es</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" name="todos" onclick="selecionaTodos(Form2)"></td>
					<td colspan="5">Marcar Todos</td>
				</tr>
				<?php
				$tabela = "";
				$x = "0";
				$reg = "Select * from Region order by description asc";
				$rcur = odbc_exec($db, $reg);
				while (odbc_fetch_row($rcur)) {
					$x++;
					$arr = array_search(odbc_result($rcur, 'id'), $arrRole);
					if ($arrRole[$arr] == odbc_result($rcur, 'id')) {
						echo "<input type=\"hidden\" name=\"role[]\" value=" . $arrRole[$arr] . ">";
						$checked = "checked";
					} else {
						$checked = "";
					}

					if ($x == "1") {
						$tabela .= "<tr>";
						$tabela .= "<td><input type=\"checkbox\" name=\"check[]\" value=" . odbc_result($rcur, 'id') . " " . $checked . "></td>";
						$tabela .= "<td>" . (odbc_result($rcur, 'description')) . "</td>";
					} else if ($x <= "2") {
						$tabela .= "<td><input type=\"checkbox\" name=\"check[]\" value=" . odbc_result($rcur, 'id') . " " . $checked . "></td>";
						$tabela .= "<td>" . (odbc_result($rcur, 'description')) . "</td>";
					} else {
						$tabela .= "<td><input type=\"checkbox\" name=\"check[]\" value=" . odbc_result($rcur, 'id') . " " . $checked . "></td>";
						$tabela .= "<td>" . (odbc_result($rcur, 'description')) . "</td>";
						$x = "0";
						$tabela .= "</tr>";
					}
				}

				$idexecutivo = isset($idexecutivo) ? (int)$idexecutivo : 0;

echo $tabela;
echo "<input type=\"hidden\" name=\"idExecutivo\" value=\"" . htmlspecialchars($idexecutivo, ENT_QUOTES, 'UTF-8') . "\">";
echo "<input type=\"hidden\" name=\"comm\">";
echo "<input type=\"hidden\" name=\"altera\" value=\"1\">";
				?>
			</tbody>
		</table>
		<div class="barrabotoes">
			<button type="button" name="voltar" class="botaovgm"
				onclick="this.form.comm.value='voltar'; document.Form2.submit();">Voltar</button>
			<button type="button" name="alterar" class="botaoagm" onclick="document.Form2.submit();">Alterar</button>
		</div>
	</div>
</form>