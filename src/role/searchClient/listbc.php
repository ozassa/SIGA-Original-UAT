<?php 
	/*
	Criado por Tiago V N - (Elumini)
	Criacao da Interface Listar Senha Banco/Cliente
	*/
	$tipo = "0";

	require_once ("../../dbOpen.php");

	include_once('../../../navegacao.php');

	$envia  = isset($_REQUEST['envia']) ? $_REQUEST['envia'] : '';
	$tipoc  = isset($_REQUEST['tipoclient']) ? $_REQUEST['tipoclient'] : '';
?>

<div class="conteudopagina">
	<script type="text/javascript">
    
		function checkcbo(){
			if (document.Form2.tipoclient.value == "0") {
				verErro("Por Favor Escolha um tipo de Cliente/Banco !!!");
				return false;
			} else {
				return true;	
			}
		}
        
  </script>

	<?php echo '<label style="color:#F00">'.htmlspecialchars($msg, ENT_QUOTES, 'UTF-8').'</label>'; ?>

	<form id="Form2" name="Form2" action="<?php echo htmlspecialchars($root, ENT_QUOTES, 'UTF-8'); ?>role/searchClient/searchSenha.php" method="post">
    <input type="hidden" name="envia" value="OK">

    <li class="campo3colunas">
     	<label>Tipo:</label>
      <select name="tipoclient">
        <option value="0" <?php echo ($tipoc == "0" ? 'selected' : ''); ?>>Selecione...</option>
        <option value="1" <?php echo ($tipoc == "1" ? 'selected' : ''); ?>>Banco</option>
        <option value="2" <?php echo ($tipoc == "2" ? 'selected' : ''); ?>>Clientes Propesctivos</option>
        <option value="3" <?php echo ($tipoc == "3" ? 'selected' : ''); ?>>Clientes Vigentes</option>
        <option value="5" <?php echo ($tipoc == "5" ? 'selected' : ''); ?>>Enc/Canc</option>
      </select>
    </li>

    <div class="barrabotoes">   
      <button class="botaoagm" type="button" onclick="javascript: if(checkcbo()) this.form.submit()">OK</button>
    </div>   
  </form>

  <div style="clear:both">&nbsp;</div>
   
 	<table class="tabela01" id="example">
    <thead>
      <tr>
        <th>Nome Empresa</th>
      </tr>
    </thead>
    <tbody>
	  <?php 
	    if($envia){       
				//Banco lista login e password
				if ($tipoc == "1") {				   
					$sql = "SELECT u.id, u.name, u.login, u.password 
					 					FROM Nurim n 
					 						JOIN UsersNurim un ON n.id = un.idNurim 
					 						JOIN Users u ON u.id = un.idUser 
					 					WHERE u.id <> 1461 
										UNION 
										SELECT u.id, u.name, u.login, u.password 
											FROM Users u
											WHERE u.id = 1461  
					 				UNION 
					 				SELECT u.id, b.name, u.login, u.password 
					 					FROM Banco b 
					 						JOIN Users u ON b.idUser = u.id 
					 						WHERE b.id <> 1 AND rtrim(b.name) <> ''
					   				ORDER BY u.name";				
				  $cur = odbc_exec($db, $sql);
					
					$c = "0";
					if ($cur) {
					  while (odbc_fetch_row($cur)) {
							if ($c == "0") {
							  $color = "odd";
							  $c = "1";
							} else {
							  $color = "";
							  $c = "0";
							}

							echo '<tr class="'.$color.'">';
							echo "<td><a href=searchSenha.php?comm=listlogin&tipoclient=".htmlspecialchars($tipoc, ENT_QUOTES, 'UTF-8')."&idclient=".urlencode(odbc_result($cur, 1)).">".htmlspecialchars(ucfirst(strtolower(trim(odbc_result($cur, 2)))))."</a></td>";
							echo "</tr>";
					  }
				  } else {
					  echo '<tr class="'.$color.'">';
					  echo "<TD>Nenhum Banco Encontrado</TD>";
					  echo "</tr>";
				  }
				}
			
				if ($tipoc == "2") { //Propectivos				
					$sql = "SELECT i.id, i.name, u.login, u.password 
										FROM Insured s, Users u, Inform i 
										WHERE i.idInsured = s.id AND u.id = s.idResp AND i.state IN(1,2,3,4,5,6,7) AND (i.name IS NOT NULL AND rtrim(i.name) <> '') 
										ORDER BY i.name";
					$cur = odbc_exec($db, $sql);

					$c = "0";
					if ($cur) {
						while (odbc_fetch_row($cur)) {
							if ($c == "0") {
								$color = "odd";
								$c = "1";
							} else {
								$color = "";
								$c = "0";
							}

							echo '<tr class="'.$color.'">';
							echo "<td><a href=searchSenha.php?comm=listlogin&tipoclient=".htmlspecialchars($tipoc, ENT_QUOTES, 'UTF-8')."&idclient=" .urlencode(odbc_result($cur, 1)).">" .  htmlspecialchars(ucfirst(strtolower(trim(odbc_result($cur, 2)))))."</a></td>";
							echo "</tr>";
						}
					} else {
						echo '<tr class="'.$color.'">';
						echo "<td>Nenhum Cliente Encontrado</td>";
						echo "</tr>";
					}
				}
			
				if ($tipoc == "3") { // Vigentes			
					$sql = "SELECT i.id, i.name, us.name, u.login, u.password, i.codProd, i.startValidity 
										FROM Inform i 
											JOIN Insured s ON i.idInsured = s.id 
											JOIN Users u ON s.idResp = u.id 
											JOIN Users us ON us.id = i.idUser 
										WHERE i.state IN(10) 
										ORDER BY i.name";
					$cur = odbc_exec($db, $sql);

					$c = "0";
					if ($cur) {						
						while (odbc_fetch_row($cur)) {
							if ($c == "0") {
								$color = "odd";
								$c = "1";
							} else {
								$color = "";
								$c = "0";
							}

							echo '<tr class="'.$color.'">';
							echo "<td><a href=searchSenha.php?comm=listlogin&tipoclient=".htmlspecialchars($tipoc, ENT_QUOTES, 'UTF-8')."&idclient=".urlencode(odbc_result($cur, 1)).">".htmlspecialchars(ucfirst(strtolower(odbc_result($cur, 2)))) ."</a></td>";
							echo "</tr>";
						}
					} else {
						echo '<tr class="'.$color.'">';
						echo "<td>Nenhum Funcionário Encontrado</td>";
						echo "</tr>";
					}
			  }
			
			} // If do Envia
			
			if ($tipoc == "5") { // Encerrado
				$sql = "SELECT i.id, i.name, u.login, u.password FROM ".
				"Insured s, Users u, Inform i WHERE i.idInsured=s.id AND ".
				"u.id=s.idResp AND i.state in (9, 11) AND ".
				"(rtrim(i.name) <> '' AND i.name IS NOT NULL) ORDER BY i.name";
				$cur = odbc_exec($db, $sql);

				$c = "0";
				if ($cur) {					
					while (odbc_fetch_row($cur)) {
						if ($c == "0") {
							$color = "odd";
							$c = "1";
						} else {
							$color = "";
							$c = "0";
						}

						echo '<tr class="'.$color.'">';
						echo "<td><a href=searchSenha.php?comm=listlogin&tipoclient=".htmlspecialchars($tipoc, ENT_QUOTES, UTF-8)."&idclient=".urlencode(odbc_result($cur, 1)).">".htmlspecialchars(ucfirst(strtolower(odbc_result($cur, 2)))) ."</a></td>";
						echo "</tr>";
					}
				} else {
					echo '<tr class="'.$color.'">';
					echo "<td>Nenhum Funcionário Encontrado</td>";
					echo "</tr>";
				}
			}
			   
   
			odbc_close($db); //Fecha conexao com Banco
	    ?>
    </tbody>
  </table>

  <div style="clear:both">&nbsp;</div>
</div>
