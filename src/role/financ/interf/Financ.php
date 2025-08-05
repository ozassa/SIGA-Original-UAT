<script language="JavaScript" src="<?php echo $root;?>scripts/calendario.js"></script>

<script Language="JavaScript">
<!--

function checa_formulario(conf_finc){
	if (conf_finc.date.value == ""){
		verErro("Por Favor, Preencha a Data de Pagamento");
		conf_finc.date.focus();

		return (false);
	}

	return (true);
}
//-->

</script>

<?php require_once("../../../navegacao.php");?>
<?php   
    
if ($role["financ"]){   ?>
       	<!-- CONTEÚDO PÁGINA - INÍCIO -->
       	<div class="conteudopagina">
       	<form action="../financ/Financ.php" name="form" id="form" method="post"> <!--onsubmit="return checa_formulario(this)"-->
       	<input type="hidden" name="comm" value="done">
       	<input type="hidden" name="mot" value="ok">
       	<input type="hidden" name="idNotification" value="<?php   echo $idNotification; ?>">
       	<table summary="Submitted table designs" id="example">
       	<caption>Pr&ecirc;mio (cobran&ccedil;a antecipada)</caption>
       	<thead>
       		<tr>
               		<th scope="col">Nome do Cliente</th>
               		<th scope="col">N&uacute;mero da Proposta</th>
               		<th scope="col">Data da Emiss&atilde;o</th>
               		<th scope="col">Valor da 1&ordf; Parcela</th>
               		<th scope="col">Data Pgto (dd/mm/aaaa)</th>
               		<th scope="col">&nbsp;</th>
       		</tr>
       	</thead>

       	<tbody>	

    	<?php   
	  	//####### ini ####### adicionado por eliel vieira - elumini - 12/05/2008
      		//$cur esta em ../view.php
      		//menu link prop. emitidas
      		//echo $sql_view."<br>";




// FALAR COM O TURRATO - INTERAKTIV
	// $sql_view = "
	// 	SELECT id, name, contrat, dateEmission, (prMin*(1+txRise)/numParc) as parcela, currency, pgOk, dateFinanc, Configuracao.i_Destino as Destino
	// 	FROM 
	// 		Inform Inf 
	// 	Inner Join Produto Produto On
	// 		Produto.i_Produto = Inf.i_Produto
	// 	Inner Join Produto_Configuracao Configuracao On
	// 		Configuracao.i_Produto = Produto.i_Produto
	// 	where 
	// 		(state = 6 or state = 7 or state = 10) 
	// 		AND pgOk = 0 
	// 		AND codProd > 0 
	// 	ORDER BY 
	// 		dateEmission";



	$sql_view = "
		SELECT id, name, contrat, dateEmission, (prMin*(1+txRise)/numParc) as parcela, currency, pgOk, dateFinanc
		FROM 
			Inform Inf 
		Inner Join Produto Produto On
			Produto.i_Produto = Inf.i_Produto
		where 
			(state = 6 or state = 7 or state = 10) 
			AND pgOk = 0 
			AND codProd > 0 
		ORDER BY 
			dateEmission";

    	$cur=odbc_exec($db, $sql_view);

    	$i = 0;

    	while (odbc_fetch_row($cur)) {  
      	$i ++;
      	$idInform = odbc_result($cur, 1);
      	$moeda = odbc_result($cur, 'currency');
        
				if ($moeda == 1){
					$ext = "R$";	
				}else if ($moeda == "2") {
        		$ext = "US$";
      	}else if ($moeda == "6") {
        		$ext = "&euro;";
      	}
		
				?>
		        	    
					<tr>

						<td>
							<?php  echo (odbc_result($cur, 2)); ?>
							<input type="hidden" name="idInform[]" value="<?php  echo odbc_result($cur, 'id'); ?>">  
						</td>

						<td>
							<?php   echo odbc_result($cur, 3); ?>
						</td>

						<td>
							<?php   
								$data = odbc_result($cur, 4);
								$data = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4);
								echo $data;
							?>
						</td>
				
			      <td>
							<?php  echo ($ext ." ". number_format(primeira_parcela($idInform), 2, ",", ".")); ?>
						</td>

				    <td>
							<?php if(odbc_result($cur, 'pgOk')  == 1){ 
								echo odbc_result($cur, 'dateFinanc');
							}else{ ?>
								<input name="date_<?php echo odbc_result($cur, 'id');?>" value="" type="text" class="semformatacao" onBlur="validate(this,this.value);">
						</td>
						<?php } ?>  

				    <td>
							<button name="moti" type="submit" class="botaoapm" onclick="javascript: form.submit();">OK</button>
						</td>

	        </tr>
		             
    		<?php 
	}

	if ($i == 0) {
		?>
		<tr>
			<td colspan="6">Nenhuma Proposta Encontrada</td>
		</tr>
	<?php   }
    	?>
    
    	<!-- ******************************** lista de propostas de juros de mora ***************************** -->
    	</tbody>
    	</table>
    	</form>
    	</div>
    
    	<div class="conteudopagina">
       	<table summary="Submitted table designs">
       	<caption>Juros de Mora</caption>
       	<thead>
       	<tr>
		<th scope="col">Nome do Cliente</th>
		<th scope="col">N&uacute;mero da Proposta</th>
                <th scope="col">Vencimento</th>
		<th scope="col">Valor da Parcela</th>
		<th scope="col">Data Pgto (dd/mm/aaaa)</th>
		<th scope="col">&nbsp;</th>
	</tr>
	</thead>

	<tbody>	
	<?php   $i = 0;
    
	$sql = "SELECT   i_Parcela, i_Seg, n_Prop, d_Venc, v_Parcela
               	FROM     Parcela
               	WHERE    t_parcela = 1 and opc_Mora = 1 and s_Parcela = 0";

	$cur=odbc_exec($dbSisSeg,$sql);
    
	while (odbc_fetch_row($cur)) {
		$i_Seg = odbc_result($cur, "i_Seg");
		$nProp = odbc_result($cur, "n_Prop");
		$d_Venc = odbc_result($cur, "d_Venc");
		$v_Parcela = odbc_result($cur, "v_Parcela");
    
		$sql = "SELECT   id, name, contrat, currency
			FROM     Inform
			WHERE    i_Seg = $i_Seg and nProp = $nProp ";

		$cur2=odbc_exec($db,$sql);
   
		$idInform = odbc_result($cur2, "id");
		$name = odbc_result($cur2, "name");
        	$contrat = odbc_result($cur2, "contrat");
        	$moeda = odbc_result($cur2, "currency");

		if ($moeda == "1") {
           		$ext = "R$";
        	}else  if ($moeda == "2") {
           		$ext = "US$";
        	}else if ($moeda == "6") {
           		$ext = "€";
        	}
    
       		$i ++;

		?>
		<tr>

		<form action="<?php echo $root;?>role/financ/Financ.php"  method="post" name="juros">
		<input type="hidden" name="usuario" value="<?php   echo $userID;?>">
		<input type="hidden" name="idInform"value="<?php   echo $idInform;?>">
		<input type="hidden" name="name" value="<?php   echo $name;?>">
		<input type="hidden" name="comm" value="confirmaSolicit">
		<td scope="row" id="r21"><?php  echo ($name); ?></td>
		<td scope="row" id="r21"><?php   echo $contrat;?></td>
		<td><?php   echo(substr($d_Venc, 8, 2). "/". substr($d_Venc, 5, 2). "/". substr($d_Venc, 0, 4));?></td>
		<td><?php   echo number_format($v_Parcela, 2, ",", "."); ?></td>
		<td><input class="semformatacao" type="text" name="date" size="12" onBlur="validate(this,this.value);"></td>
		<td><button name="moti" type="submit" class="botaoapm" onclick="javascript: this.form.submit();">OK</button></td>
		</form>
		</tr>
	<?php   }
      				
	if ($i == 0) {
		?>
		<tr>
			<td colspan=6>Nenhuma Proposta Encontrada</td>
		</tr>
	<?php   }
	?>
	</tbody>
	</table>
	</div>
	<?php
	
}else{  ?>
	<div class="conteudopagina">
	<table summary="Submitted table designs">
	<thead>
	<tr>
		<th scope="col">Nome do Cliente</th>
		<th scope="col">N&uacute;mero da Proposta</th>
                <th scope="col">Data da Emissão</th>
                <th scope="col">Valor da 1&ordf; Parcela</th>
	</tr>
	</thead>
	<tbody>	
	<?php   $i = 0;

	while (odbc_fetch_row($cur)) {
		$i ++;
		?>
            	<tr>
			<td scope="row" id="r21"><?php  echo (odbc_result($cur, 2)); ?></td>
                    	<td scope="row" id="r21"><?php   echo odbc_result($cur, 3);?></td>
                    	<td><?php  
                        $data = odbc_result($cur, 4);
                        $data = substr($data, 8, 2). "/". substr($data, 5, 2). "/". substr($data, 0, 4);
                        echo $data;
			?></td>
                    	<td><?php   echo ($ext . " " . number_format(primeira_parcela(odbc_result($cur, 1)), 2, ",", ".")); ?></td>
               	</tr>
              
	<?php   }

	if ($i == 0) {
		?>
		<tr>
			<td colspan="4">Nenhuma Proposta Encontrada</td>
		</tr>
	<?php   }
	?>
    	</tbody>
    	</table>
    	</div>
    	<div class="conteudopagina">
        <table summary="Submitted table designs">
        <caption>Juros de Mora</caption>
        <thead>
	<tr>
		<th scope="col">Nome do Cliente</th>
		<th scope="col">N&uacute;mero da Proposta</th>
		<th scope="col">Vencimento</th>
		<th scope="col">Valor da Parcela</th>
	</tr>
	</thead>
	<tbody>	
	<?php   $i = 0;
    
	$sql = "SELECT   i_Parcela, i_Seg, n_Prop, d_Venc, v_Parcela
		FROM     Parcela
                WHERE    t_parcela = 1 and opc_Mora = 1 and s_Parcela = 0 ";

	$cur=odbc_exec($dbSisSeg,$sql);
    
	while (odbc_fetch_row($cur)) {
		$i_Seg = odbc_result($cur, "i_Seg");
        	$nProp = odbc_result($cur, "n_Prop");
        	$d_Venc = odbc_result($cur, "d_Venc");
        	$v_Parcela = odbc_result($cur, "v_Parcela");
    
		$sql = "  SELECT   name, contrat
			FROM     Inform
			WHERE    i_Seg = $i_Seg and nProp = $nProp ";

        	$cur2=odbc_exec($db,$sql);

        	$name = odbc_result($cur2, "name");
        	$contrat = odbc_result($cur2, "contrat");
    
        	$i ++;
    		?>
      		<tr>
            		<td scope="row" id="r21"><?php  echo ($name);?></td>
            		<td scope="row" id="r21"><?php   echo $contrat;?></td>
            		<td><?php echo(substr($d_Venc, 8, 2). "/". substr($d_Venc, 5, 2). "/". substr($d_Venc, 0, 4));?></td>
            		<td><?php echo number_format($v_Parcela, 2, ",", ".");?></td>
      		</tr>
	<?php   }

	if ($i == 0) {
      		?>
      		<tr>
        		<td colspan=6>Nenhuma Proposta Encontrada</td>
      		</tr>
    	<?php   }
    	?>
    	</tbody>
    	</table>
    	</div>
    
<?php   } ?>

<?php   function primeira_parcela($idInform){
	global $db, $dbSisSeg;

	$sql_ver_parc = "select i_Seg, idAnt, contrat, nProp from Inform where id=$idInform";

      	$x = odbc_exec($db, $sql_ver_parc);

      	$iseg = odbc_result($x, 1);
      	$idAnt = odbc_result($x, 2);
      	$ci = odbc_result($x, 3);
      	$nProp = odbc_result($x, 4);

      	if(! $iseg){
        	$iseg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
      	}

      	if(! $nProp){
        	$sql_proposta = "select n_Prop from Proposta where i_Seg=$iseg order by n_Prop desc";
    
        	$x = odbc_exec($dbSisSeg,$sql_proposta);

        	if(odbc_fetch_row($x)){
          		$nProp = odbc_result($x, 1);
        	}
	}

      	$sql_parc = "select v_Parcela from Parcela where c_Coface=$ci and i_Seg=$iseg and n_Prop=$nProp and n_Seq_Parcela=1 and s_Parcela in (1,2)";

      	$x = odbc_exec($dbSisSeg, $sql_parc);

      	return odbc_result($x, 1);
}

?>