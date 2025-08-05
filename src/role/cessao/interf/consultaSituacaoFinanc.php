<?php

	$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : '';

	// Prepara a consulta para evitar SQL Injection
$qry = "SELECT name, i_Empresa, n_Apolice  
        FROM Inform 
        WHERE id = ?";
$stmt = odbc_prepare($db, $qry);

// Executa a consulta com o parâmetro
odbc_execute($stmt, [$idInform]);

// Recupera os resultados
$Nome_Segurado = odbc_result($stmt, "name");
$n_Empresa = odbc_result($stmt, "i_Empresa");
$n_Apolice = odbc_result($stmt, "n_Apolice");
 odbc_free_result($stmt);

// Prepara a chamada da stored procedure para evitar SQL Injection
$sql = "{CALL SPR_BB_Consulta_Financeira(?, ?, ?)}";
$rsSql = odbc_prepare($db, $sql);

// Executa a stored procedure com os parâmetros
odbc_execute($rsSql, [$n_Empresa, $n_Apolice, '100']);

// Mantém o resultado em $rsSql
	$dados = array();
	while(odbc_fetch_row($rsSql)) {
		$n_Endosso = odbc_result($rsSql, "n_Endosso");
		$Desc_Tipo = odbc_result($rsSql, "Desc_Tipo");
		$n_Parcela = odbc_result($rsSql, "n_Parcela");
		$d_Emissao = Convert_Data_Geral(substr(odbc_result($rsSql, "d_Emissao"), 0, 10));
		$Moeda = odbc_result($rsSql, "Moeda");
		$v_Parcela = number_format(odbc_result($rsSql, "v_Parcela"), 2, ",", ".");
		$d_Vencimento = Convert_Data_Geral(substr(odbc_result($rsSql, "d_Vencimento") ?? '', 0, 10));
		$d_Pagamento = Convert_Data_Geral(substr(odbc_result($rsSql, "d_Pagamento") ?? '', 0, 10));
		$d_Cancelamento = Convert_Data_Geral(substr(odbc_result($rsSql, "d_Cancelamento") ?? '', 0, 10));
		$Desc_Situacao = odbc_result($rsSql, "Desc_Situacao");

		$dados[] = array(
			"n_Endosso"				=> $n_Endosso,
			"Desc_Tipo"				=> $Desc_Tipo,
			"n_Parcela"				=> $n_Parcela,
			"d_Emissao"				=> $d_Emissao,
			"Moeda"						=> $Moeda,
			"v_Parcela"				=> $v_Parcela,
			"d_Vencimento"		=> $d_Vencimento, 
			"d_Pagamento"			=> $d_Pagamento,
			"d_Cancelamento"	=> $d_Cancelamento,
			"Desc_Situacao"		=> $Desc_Situacao
		);
	}

	odbc_free_result($rsSql);


	$sqlParc = "EXEC SPR_BB_Consulta_Financeira '".$n_Empresa."', '".$n_Apolice."', '200'";
	$rsSqlParc = odbc_exec($db, $sqlParc);

	$dados_parc = array();
	while(odbc_fetch_row($rsSqlParc)) {
		$n_Endosso = odbc_result($rsSqlParc, "n_Endosso");
		$Desc_Tipo = odbc_result($rsSqlParc, "Desc_Tipo");
		$n_Parcela = odbc_result($rsSqlParc, "n_Parcela");
		$d_Emissao = Convert_Data_Geral(substr(odbc_result($rsSqlParc, "d_Emissao") ?? '', 0, 10));
		$Moeda = odbc_result($rsSqlParc, "Moeda");
		$v_Parcela = number_format(odbc_result($rsSqlParc, "v_Parcela") ?? 0, 2, ",", ".");
		$d_Vencimento = Convert_Data_Geral(substr(odbc_result($rsSqlParc, "d_Vencimento") ?? '', 0, 10));
		$d_Pagamento = Convert_Data_Geral(substr(odbc_result($rsSqlParc, "d_Pagamento") ?? '', 0, 10));
		$d_Cancelamento = Convert_Data_Geral(substr(odbc_result($rsSqlParc, "d_Cancelamento") ?? '', 0, 10));
		$Desc_Situacao = odbc_result($rsSqlParc, "Desc_Situacao");

		$dados_parc[] = array(
			"n_Endosso"				=> $n_Endosso,
			"Desc_Tipo"				=> $Desc_Tipo,
			"n_Parcela"				=> $n_Parcela,
			"d_Emissao"				=> $d_Emissao,
			"Moeda"						=> $Moeda,
			"v_Parcela"				=> $v_Parcela,
			"d_Vencimento"		=> $d_Vencimento, 
			"d_Pagamento"			=> $d_Pagamento,
			"d_Cancelamento"	=> $d_Cancelamento,
			"Desc_Situacao"		=> $Desc_Situacao
		);
	}

	

	$sqlCab = "EXEC SPR_BB_Consulta_Financeira '".$n_Empresa."', '".$n_Apolice."', '10'";
	$rsSqlCab = odbc_exec($db, $sqlCab);

	while(odbc_fetch_row($rsSqlCab)) {
		$Nome_Segurado = odbc_result($rsSqlCab, "Nome_Segurado");
		$n_Apolice = odbc_result($rsSqlCab, "n_Apolice");
		$d_Emissao = Convert_Data_Geral(substr(odbc_result($rsSqlCab, "d_Emissao"), 0, 10));
		$d_Inicio_Vigencia = Convert_Data_Geral(substr(odbc_result($rsSqlCab, "d_Inicio_Vigencia"), 0, 10));
		$d_Fim_Vigencia = Convert_Data_Geral(substr(odbc_result($rsSqlCab, "d_Fim_Vigencia"), 0, 10));
		$d_Consulta = Convert_Data_Geral(substr(odbc_result($rsSqlCab, "d_Consulta"), 0, 10));
		$d_Atualizacao = odbc_result($rsSqlCab, "d_Atualizacao") ? date('d/m/Y H:i:s', strtotime(odbc_result($rsSqlCab, "d_Atualizacao"))) : '';
	}

	

  require_once("../../../navegacao.php"); 
?>

<style>
  .consulta_table {
    margin-bottom: 25px;
  }
</style>

<div class="conteudopagina">

	<li class="campo3colunas" style="width: 400px;"> 
		<label>Nome do Segurado</label>
		<?php echo $Nome_Segurado; ?>
	</li>

	<li class="campo3colunas" style="width: 150px;"> 
		<label>N&ordm; da Ap&oacute;lice</label>
		<?php echo $n_Apolice; ?>
	</li>

	<li class="campo3colunas" style="width: 150px;"> 
		<label>Data de Emiss&atilde;o</label>
		<?php echo $d_Emissao; ?>
	</li>
	
	<?php if(isset($d_Inicio_Vigencia)){ ?>
		<li class="campo3colunas" style="width: 150px;"> 
			<label>Vig&ecirc;ncia da Ap&oacute;lice</label>
			<?php echo $d_Inicio_Vigencia.' &agrave; '.$d_Fim_Vigencia; ?>
		</li>

		<br clear="all">

		<li class="campo2colunas" style="width: 738px;"></li>

		<li class="campo2colunas" style="width: 150px;"> 
			<label>Data Consulta</label>
			<?php echo $d_Consulta; ?>
		</li>
	<?php } ?>
	
  <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">  
    <label>
      <h2>Relat&oacute;rio de Parcelas de Pr&ecirc;mio</h2>
    </label>

		<div class="consulta_table">
		  <table summary="" id="">
		    <thead>
		      <tr>
		        <th>N&ordm; da Parcela</th>
		        <th>Situa&ccedil;&atilde;o da Parcela</th>
		        <th>Data de Emiss&atilde;o da Parcela</th>		        
		        <th>Valor da Parcela</th>
		        <th>Data de Vencimento</th>
		        <th>Data de Pagamento</th>
		        <th>Data de Cancelamento</th>
		      </tr>
		    </thead>  

		    <?php if(empty($dados)){ ?>
		      <tbody><tr><td valign="top" colspan="10" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
		    <?php 
		      } else { 
		        for ($a=0; $a < count($dados); $a++) { ?>
		          <tr>
		            <td><?php echo $dados[$a]['n_Parcela']; ?></td>
		            <td><?php echo $dados[$a]['Desc_Situacao']; ?></td>
		            <td><?php echo $dados[$a]['d_Emissao']; ?></td>
		            <td><?php echo $dados[$a]['Moeda'].' '.$dados[$a]['v_Parcela']; ?></td>
		            <td><?php echo $dados[$a]['d_Vencimento']; ?></td>
		            <td><?php echo $dados[$a]['d_Pagamento']; ?></td>
		            <td><?php echo $dados[$a]['d_Cancelamento']; ?></td>
		          </tr>
		      <?php } ?>
		    <?php } ?>    
		  </table>
		</div>

		<br clear="all">
		
	<!--<label>
      <h2>Relat&oacute;rio de Parcelas de An&aacute;lise e Monitoramento</h2>
    </label>
		<div class="consulta_table">
		  <table summary="" id="">
		    <thead>
		      <tr>
		        <th>N&ordm; da Parcela</th>
		        <th>Situa&ccedil;&atilde;o da Parcela</th>
		        <th>Data de Emiss&atilde;o da Parcela</th>		        
		        <th>Valor da Parcela</th>
		        <th>Data de Vencimento</th>
		        <th>Data de Pagamento</th>
		        <th>Data de Cancelamento</th>
		      </tr>
		    </thead>  

		    <?php if(empty($dados_parc)){ ?>
		      <tbody><tr><td valign="top" colspan="10" class="dataTables_empty">Nenhum dado retornado na tabela</td></tr></tbody>  
		    <?php 
		      } else { 
		        for ($a=0; $a < count($dados_parc); $a++) { ?>
		          <tr>
		            <td><?php echo $dados_parc[$a]['n_Parcela']; ?></td>
		            <td><?php echo $dados_parc[$a]['Desc_Situacao']; ?></td>
		            <td><?php echo $dados_parc[$a]['d_Emissao']; ?></td>
		            <td><?php echo $dados_parc[$a]['Moeda'].' '.$dados_parc[$a]['v_Parcela']; ?></td>
		            <td><?php echo $dados_parc[$a]['d_Vencimento']; ?></td>
		            <td><?php echo $dados_parc[$a]['d_Pagamento']; ?></td>
		            <td><?php echo $dados_parc[$a]['d_Cancelamento']; ?></td>
		          </tr>
		      <?php } ?>
		    <?php } ?>    
		  </table>
		</div> -->
	
  </li>
		   	
	<div style="clear:both">&nbsp;</div> 	

 	<div style="text-align: justify; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #FF0000;">
 		* As informa&ccedil;&otilde;es relativas &agrave; situa&ccedil;&atilde;o financeira desse segurado foram atualizadas em <?php echo $d_Atualizacao; ?>
 	</div>
	
  <div class="barrabotoes">
    <button class="botaovgm" type="button" onClick="window.history.back()">Voltar</button>
  </div>  
</div>