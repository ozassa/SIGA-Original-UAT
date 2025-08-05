<?php //Criado Hicom 11/01/05 (Gustavo)
include_once('../../../navegacao.php');

$usu  = '';
$tipo = '';

if (isset($_POST['envio'])){
  $usu  = $_POST['usu'];
  $tipo = $_POST['tipo'];
}

?>
<div class="conteudopagina">

<FORM name="Form1" action="../access/Access.php?comm=log" method="post">
   <input type="hidden" name="envio" id="envio" value="1">
<li class="campo2colunas">
 <label>Usu&aacute;rio</label>
    <select name="usu" id="usu">
		<option value=""></option>
		<?php $sql = "select * from Users where perfil = 'F' Or perfil is null And state='0' order by name,login";
			$c = odbc_exec($db, $sql);
            while( odbc_fetch_row($c) ) {
			if ($usu == odbc_result($c, "id")){
                   $selected_u = "selected";
			}else{
                   $selected_u = "";
			}

			echo "<option value=".odbc_result($c, "id")." $selected_u >".(odbc_result($c, "name"))."</option>";
			}

		?>
	    </select>
</li>

<li class="campo2colunas"><label>Tipo Log</label>
     <select name="tipo" id="tipo">
		<option value=""></option>
		<?php $sql = "select * from LogTipo order by id_TipoLog";
			$c = odbc_exec($db, $sql);
            while( odbc_fetch_row($c) ) {
			if ($tipo == odbc_result($c, "id_TipoLog")){
                   $selected = "selected";
			}else{
                   $selected = "";
			}
			
			echo "<option value=".odbc_result($c, "id_TipoLog")." $selected >".(odbc_result($c, "msgLog"))."</option>";
			}
			
		?>
	    </select>
</li>        
 <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">
    <button class="botaoagm" type="button"  onClick="javascript: this.form.submit()">OK</button>       
 </li>
</FORM>


<table summary="Submitted table designs" id="example">
    <thead>
      <tr>
    	<th scope="col">A&ccedil;&atilde;o</th>
	    <th scope="col">Nome</th>
	    <th scope="col">Dia</th>
	    <th scope="col">Hora</th>
      </tr>
	</thead>
    <tbody>
<?php /*
  Alterado por Cristiano - ( Elumini )
  Alterador por Tiago V N - Elumini - 29/03/2006
*/
if (isset($_POST['envio'])){
	
	/*$sql = "SELECT l.id_Log, l.tipoLog, l.id_User, l.Inform, l.data, l.hora, lt.msgLog, u.name, i.name as nomeinf
            FROM Log l LEFT OUTER JOIN
                 Users u ON u.id = l.id_User LEFT OUTER JOIN
                 LogTipo lt ON lt.id_TipoLog = l.tipoLog LEFT OUTER JOIN
                 Inform i ON i.id = l.Inform ";

    if ($tipo!="") {
       $sql.= "where l.tipoLog='$tipo' ";
    }

    if ($usu!="" && $tipo!=""){
       $sql.= " And l.id_User='$usu' ";
    }elseif ($usu!=""){
       $sql.= "where l.id_User='$usu' ";
    }
    
    $sql.="order by l.data desc";
    
	//echo $sql;
		
	$cur=odbc_exec($db,$sql);
	$i = 0;
	while (odbc_fetch_row($cur)) {
			$i++;
			$cli = '';
	    $id = odbc_result($cur,"id_Log");*/



	// Query base
	$sql = "SELECT l.id_Log, l.tipoLog, l.id_User, l.Inform, l.data, l.hora, lt.msgLog, u.name, i.name as nomeinf
	        FROM Log l 
	        LEFT OUTER JOIN Users u ON u.id = l.id_User 
	        LEFT OUTER JOIN LogTipo lt ON lt.id_TipoLog = l.tipoLog 
	        LEFT OUTER JOIN Inform i ON i.id = l.Inform 
	        WHERE 1=1"; // '1=1' garante que os filtros podem ser adicionados dinamicamente

	// Array para armazenar parâmetros
	$params = [];

	if (!empty($tipo)) {
	    $sql .= " AND l.tipoLog = ?";
	    $params[] = $tipo;
	}

	if (!empty($usu)) {
	    $sql .= " AND l.id_User = ?";
	    $params[] = $usu;
	}

	$sql .= " ORDER BY l.data DESC";

	$cur = odbc_prepare($db, $sql);
	if (!$cur) {
	    //die("Erro ao preparar a query: " . odbc_errormsg($db));
	}

	
	if (!odbc_execute($cur, $params)) {
	    //die("Erro ao executar a query: " . odbc_errormsg($db));
	}

	$i = 0;
	while (odbc_fetch_array($cur)) {
	    $i++;
	    $id = odbc_result($cur,"id_Log");
	    $acao = (odbc_result($cur,"msgLog"));
	    $nome = (odbc_result($cur,"name"));
	    $inform = odbc_result($cur,"Inform");
	    $nomeInform = odbc_result($cur, "nomeinf");
	    $dia = odbc_result($cur,"data");
      $data = date("d/m/Y", strtotime($dia));
      $hora = odbc_result($cur,"hora");
      $TipoLog = odbc_result($cur,2);
        
?>
	<TR <?php echo $i % 2 ? "" : " bgcolor=#e9e9e9"; ?>>
    	<?php
		// Obtém dados necessários fora do HTML
		$nomeUsuario = null;
		if (in_array($TipoLog, ['1', '2', '3', '7', '8'])) {
		    // Prepara a query para buscar o nome do usuário
		    $stmt = odbc_prepare($db, "SELECT name FROM Users WHERE id = ?");
		    if ($stmt && odbc_execute($stmt, [$inform])) {
		        $row = odbc_fetch_array($stmt);
		        $nomeUsuario = $row['name'] ?? null;
		    }
		}

		// Monta o título com sxecegurança
		$title = $nomeUsuario ?: $nomeInform;

		// Monta o link com os parâmetros escapados para evitar XSS

		$cli = $cli ?? '';

		$link = "../access/access.php?comm=loadLog&id=" . htmlspecialchars($id) .
		        "&acao=" . htmlspecialchars($acao) .
		        "&cli=" . htmlspecialchars($cli) .
		        "&inform=" . htmlspecialchars($inform);

		?>
		<!-- Exibe o HTML de forma limpa -->
		<TD class="texto" height="20px">
		    <a href="<?php echo $link; ?>" title="<?php echo htmlspecialchars($title); ?>">
		        <?php if (!empty($inform)): ?>
		            <?php echo htmlspecialchars($acao) . "&nbsp;[ " . htmlspecialchars($inform) . "]"; ?>
		        <?php endif; ?>
		    </a>
		</TD>
    	<TD class="texto" height="20px"><?php echo $nome;?></TD>
    	<TD class="texto" height="20px"><?php echo $data;?></TD>
        <TD class="texto" height="20px"><?php echo $hora;?></TD>
    </TR>
<?php }

 	if ($i == 0) {
?>
	<TR bgcolor=#e9e9e9>
		<TD colspan=4 align=center class="textoBold">Nenhum registro encontrado</TD>
    </TR>
<?php }
}//Envio
?>

  </tbody>
  <tfoot>
        <TR>
            <TD colspan="4" >Logs</TD>
        </TR>
  </tfoot>
</TABLE>

<div style="clear:both">&nbsp;</div>

</div>
