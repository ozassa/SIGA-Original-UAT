<?php 

session_start();

include_once("../../../navegacao.php");


 function formataValorSql($formataValorSql){
	if ($formataValorSql == "") {
		$formataValorSql = '0.00';
	} else {
		$formataValorSql = str_replace('.','',$formataValorSql);
		$formataValorSql = str_replace(',','.',$formataValorSql);
	}
	return $formataValorSql;
 }
	
 function addDia($data, $qtd){	
	 $data_br = $data;
     list($dia, $mes, $ano) = explode('/', $data_br);
     $time = mktime(0, 0, 0, $mes, $dia + $qtd, $ano);
     return strftime('%Y-%m-%d', $time);
 }
?>
<div class="conteudopagina">
 
<?php
 
 
     $sql = "SELECT
            Inf.contrat AS DPP,
            Inf.name AS Segurado,
            Inf.n_Apolice AS NumApolice,
            Endosso.n_Endosso AS NumEndosso,
            TipoEndosso.Descricao_Item AS TipoEndosso,
            Endosso.d_Emissao AS DataEmissao,
            SituacaoEndosso.Descricao_Item AS SituacaoEndosso,
            Endosso.n_Parcelas AS NumParcelas,
            Endosso.v_Premio AS ValorEndosso,
            Endosso.d_Vigencia_Inicial AS InicioVigencia,
            Endosso.d_Vigencia_Final AS FimVigencia,
            Endosso.Descricao AS Descricao,
            Solicitante.name AS Solicitante
        FROM
            Inform Inf
        INNER JOIN Inform_Endosso Endosso ON
            Endosso.i_Inform = Inf.id
        INNER JOIN Campo_Item AS TipoEndosso ON
            TipoEndosso.i_Campo = 400
            AND TipoEndosso.i_Item = Endosso.t_Endosso
        INNER JOIN Campo_Item AS SituacaoEndosso ON
            SituacaoEndosso.i_Campo = 410
            AND SituacaoEndosso.i_Item = Endosso.s_Endosso
        INNER JOIN Users Solicitante ON
            Solicitante.id = Endosso.i_Solicitante
        WHERE
            Inf.id = ?
            AND Endosso.n_Endosso = ?";

$stmt = odbc_prepare($db, $sql);
$idInform = $_REQUEST['idInform'];
$NumEndosso = $_REQUEST['NumEndosso'];

odbc_execute($stmt, [$idInform, $NumEndosso]);
$res = $stmt;
odbc_free_result($stmt);		
		
	
/*	•	DPP – Campo DPP;
•	Nome do segurado – Campo Segurado;
•	Apólice – Campo NumApolice;
•	Endosso – Campo NumEndosso;
•	Tipo – Campo TipoEndosso;
•	Data de emissão – Campo DataEmissao;
•	Situação – Campo SituacaoEndosso;
•	Número de parcelas – Campo NumParcelas;
•	Valor – Campo ValorEndosso;
•	Início de vigência – Campo InicioVigencia;
•	Fim de vigência – Campo FimVigencia;
•	Descrição – Campo Descricao;
•	Solicitante – Campo Solicitante;*/


 ?>
 <ul>
     <li class="campo3colunas">
		<label>DPP</label>
		<?php echo odbc_result($res,'DPP');?>
	 </li>
		<li class="campo3colunas"><label>Nome do segurado</label><?php echo odbc_result($res,'Segurado');?></li>
		<li class="campo3colunas"><label>Ap&oacute;lice</label><?php echo odbc_result($res,'NumApolice');?></li>
		<li class="campo3colunas"><label>Endosso</label><?php echo odbc_result($res,'NumEndosso');?></li>
		<li class="campo3colunas"><label>Tipo</label><?php echo odbc_result($res,'TipoEndosso');?></li>
		<li class="campo3colunas"><label>Data de emiss&atilde;o</label><?php echo Convert_Data_Geral(substr(odbc_result($res,'DataEmissao'),0,10));?></li>
		<li class="campo3colunas"><label>Situa&ccedil;&atilde;o</label><?php echo odbc_result($res,'SituacaoEndosso');?></li>
		<li class="campo3colunas"><label>N&uacute;mero de parcelas</label><?php echo odbc_result($res,'NumParcelas');?></li>
		<li class="campo3colunas"><label>Valor</label><?php echo number_format(odbc_result($res,'ValorEndosso'),2,',','.');?></li>
		<li class="campo3colunas"><label>In&iacute;cio de vig&ecirc;ncia</label><?php echo Convert_Data_Geral(substr(odbc_result($res,'InicioVigencia'),0,10));?></li>
		<li class="campo3colunas"><label>Fim de vig&ecirc;ncia</label><?php echo Convert_Data_Geral(substr(odbc_result($res,'FimVigencia'),0,10));?></li>
		<li class="campo3colunas"><label>Descri&ccedil;&atilde;o</label><?php echo odbc_result($res,'Descricao');?></li>
		<li class="campo3colunas"><label>Solicitante</label><?php echo odbc_result($res,'Solicitante');?></li>
 </ul>
  <div class="barrabotoes"></div>
  <br clear="all"> 
   
 <?php
      $sql = "SELECT 
            Parcela.n_Parcela AS NumParcela,
            Parcela.d_Vencimento AS DataVencimento,
            Parcela.v_Parcela AS ValorParcela,
            SituacaoParcela.Descricao_Item AS SituacaoParcela
        FROM 
            Parcela Parcela
        LEFT JOIN Campo_Item AS SituacaoParcela ON
            SituacaoParcela.i_Campo = 310
            AND SituacaoParcela.i_Item = Parcela.s_Parcela
        WHERE
            Parcela.i_Inform = ?
            AND Parcela.n_Endosso = ?
        ORDER BY
            Parcela.n_Parcela";

$stmt = odbc_prepare($db, $sql);
$idInform = $_REQUEST['idInform'];
$NumEndosso = $_REQUEST['NumEndosso'];

odbc_execute($stmt, [$idInform, $NumEndosso]);
$res = $stmt;
odbc_free_result($stmt);
	  $num = odbc_num_rows($res);
	  
	  if($num > 0){ ?>
  		    <h3>Lista de Parcelas</h3>
			<table summary="Submitted table designs" id="example">
			  <thead>
				  <tr>
					<th width="15%" style="text-align:center">N&uacute;mero Parcela</th>
					<th width="35%" style="text-align:center">Vencimento</th>
					<th width="20%" style="text-align:center">Valor Parcela</th>
					<th width="20%" style="text-align:center">Situa&ccedil;&atilde;o</th>
					</tr>
			  </thead>
			  
			  <tbody>
			  <?php
				 $i = 0;
				  while(odbc_fetch_row($res)){ ?>
				  <tr>
					<td style="text-align:center"><?php echo odbc_result($res,'NumParcela');?></td>
					<td style="text-align:center"><?php echo Convert_Data_Geral(substr(odbc_result($res,'DataVencimento'),0,10));?></td>
					<td style="text-align:center"><?php echo number_format(odbc_result($res,'ValorParcela'),2,',','.');?></td>
					<td style="text-align:center"><?php echo odbc_result($res,'SituacaoParcela');?></td>
					
				  </tr>
		   <?php    $total  += odbc_result($res,'ValorParcela');
					$i++;
				  } ?>		   
			  </tbody>
			  
			  <tfoot>
				 <tr>
				    <th colspan="2">Total das Parcelas:</th>
					<th colspan="1" style="text-align:center"><?php echo number_format($total,2,',','.');?></th>
					<th colspan="1"></th>
				 </tr>
			  </tfoot>
		  </table>
       <?php } ?>
   </form>
	 <div class="barrabotoes">
      <button name="voltar" type="button" onClick="window.location = 'ListClient.php?comm=cadastro_endosso&idInform=<?php echo urlencode($_REQUEST['idInform']); ?>';" class="botaovgm">Voltar</button>

	  
	</div>
</div>


