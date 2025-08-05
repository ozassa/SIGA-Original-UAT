<?php 
	////////////////////////////////////////////////////////////////////////////
	if(! function_exists('ymd2dmy')){
	  // converte a data de yyyy-mm-dd para dd/mm/yyyy
	  function ymd2dmy($d){
	    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d ?? '', $v)){
	      return "$v[3]/$v[2]/$v[1]";
	    }
	  }
	}

	/////////////////////////////////////////////////////////////////////////////
	if(! function_exists('get_de_st_inform')){
	  // Retorna o status do Inform
	  function get_de_st_inform($status)
	  {
	     if($status == 1)
		 {
		    return "Novo";
			
		 }elseif ($status == 2)
		 {
		    return "Preenchido";  
			
		 }elseif ($status == 3)
		 {
		    return "Validado";  
			
		 }elseif ($status == 4)
		 {
		    return "Analisado";
			  
		 }elseif ($status == 5)
		 {
		    return "Tarifado";
			  
		 }elseif ($status == 6)
		 {
		    return "Proposta";
			  
		 }elseif ($status == 7)
		 {
		    return "Confirmado";
			  
		 }elseif ($status == 8)
		 {
		    return "Alterado";  
			
		 }elseif ($status == 9)
		 {
		    return "Cancelado";  
			
		 }elseif ($status == 10)
		 {
		    return "Apólice";  
			
		 }elseif ($status == 11)
		 {
		    return "Encerrado";  
		 }else
		 {
		    return "Indefinido ($status)";  
		 }
	  }
	}

	////////////////////////////////////////////////////////
	
	$query = "SELECT i.id, i.name, i.state, i.startValidity
		          FROM Inform i
		            JOIN Insured ins ON (ins.id = i.idInsured)
		         	WHERE ins.idResp = $userID and i.name is not null and i.id <> $idInform 
				 			ORDER BY i.id";
	$cur = odbc_exec($db, $query);
	$acc = 1;
		
	while(odbc_fetch_row($cur)){
		if ($acc == 1) {  ?>
			<table width="100%" >
			  <caption>Seus informes</caption>
			    <thead>                       
			      <tr>
						  <th width="25%" align="left">Inicio&nbsp;de&nbsp;Vig&ecirc;ncia</th>
						  <th width="20%" align="left">Status</th>
						  <th width="60%" align="left">Segurado</th>
					  </tr>
				  </thead> 
          <tbody> 
          <?php 
    }
		
		$acc = $acc + 1;
		?>			 
		<tr>
			<td align="left"><a href="<?php echo $root; ?>role/inform/Inform.php?comm=open&idInform=<?php echo odbc_result($cur, 1); ?>"><?php echo ymd2dmy(odbc_result($cur, 4)); ?></a><br></td>
			<td align="left"><a href="<?php echo $root; ?>role/inform/Inform.php?comm=open&idInform=<?php echo odbc_result($cur, 1); ?>"><?php echo get_de_st_inform(odbc_result($cur, 3)); ?></a><br></td>	 
			<td align="left"><a href="<?php echo $root; ?>role/inform/Inform.php?comm=open&idInform=<?php echo odbc_result($cur, 1); ?>"><?php echo (odbc_result($cur, 2)); ?></a><br></td>
		</tr>			 
  	<?php 
  }
	 
	if ($acc != 1) {
	  echo "</tbody></table>";
	}
		
	if (!isset($j) && $_SESSION['pefil'] == 'CO') { ?>
	  <div class="barrabotoes">
			<a href="../access/Access.php?comm=openConsultor"><button class="botaoagm" type="button" onClick="">Voltar</button></a>
	  </div>				 
		<?php		 
	}
?>