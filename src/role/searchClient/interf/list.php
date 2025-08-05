<tbody>
<?php 

if(! function_exists('ymd2dmy')){
	function ymd2dmy($d){
    		if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      			return "$v[3]/$v[2]/$v[1]";
    		}

    		return $d;
  	}
}

if(! function_exists('check_ant')){
  	function check_ant($idAnt){
    		global $db;
    		$x = odbc_exec($db, "select state from Inform where id=$idAnt");
    		$state = odbc_result($x, 1);

    		if($state == 10 || $state == 11){
      			return true;
    		}

    		return false;
  	}
}

if(! function_exists('regiadochefe')){
  	function regiadochefe($db, $user, $hc_idRegion){

		$wstr = " Select count(UN.idRegion) as qtd " .
	        	" from UserRole UR, UserRegion UN " .
			" where UR.idRole = 3 " .
			" and UR.idUser = UN.idUser " .
			" AND (UR.idUser IN (SELECT idUser FROM UserRegion WHERE idRegion = " . $hc_idRegion . ")) " .
			" AND UN.idRegion IN (SELECT idRegion FROM UserRegion WHERE idUser = " . $userID . " )" .
			" ";

     		$aux = odbc_exec($db, $wstr);

	 	$qtd = odbc_result($aux, 1);

	 	if ($qtd > 0){
	    		return true;
	 	}else{
	    		return false;
	 	}
  	}
}


if(! function_exists('regiaok')){
  	function regiaok($db, $user, $hc_idRegion){

  		if($hc_idRegion == ''){
  			return false;
  		}
		$wstr = " Select count(id) as qtd from UserRegion " .
	         	" where idUser = '" . trim("" . $user) ."'
			AND idRegion = " . $hc_idRegion . " " ;

     		$aux = odbc_exec($db, $wstr);
	 	$qtd = odbc_result($aux, 1);

	 	if ($qtd > 0){
	    		return true;
	 	}else{
	    		return false;
	 	}

  		if(trim($hc_idRegion) == '' || trim($user) == ''){
  			return false;
  		}
  		
		$wstr = " Select count(id) as qtd from UserRegion " .
	         	" where idUser = '" . trim("" . $user) ."'
			AND idRegion = " . $hc_idRegion . " " ;

     		$aux = odbc_exec($db, $wstr);
	 	$qtd = odbc_result($aux, 1);

	 	if ($qtd > 0){
	    		return true;
	 	}else{
	    		return false;
	 	}
  	}
}


$i = 0;
$numero="";

while(odbc_fetch_row($cur)){
	$inform     	= odbc_result($cur, 1); //chave de busca para linkar o informe
  	$clientR    	= (odbc_result($cur, 2));
  	$executiveR 	= (odbc_result($cur, 3)); //NO FUTURO OTIMIZAR: $executive e $executiveR
  	$stateR     	= (odbc_result($cur, 4));
  	$idAnt      	= odbc_result($cur, 6);
  	$start      	= ymd2dmy(odbc_result($cur, 7) ?? '');
  	$end        	= ymd2dmy(odbc_result($cur, 8) ?? '');
  	$hc_idUser   	= trim("" . odbc_result($cur, 9));
  	$hc_idRegion   	= trim("" . odbc_result($cur, 10));
    	$currency      	= odbc_result($cur, "currency");
     	$Ga      	= odbc_result($cur, "Ga");
     	$DPP      	= odbc_result($cur, "contrat");
     	$hc_visualiza 	= false;

     	if (!$role["executive"]) {
        	$hc_visualiza = true;
	}else{
     		if ($hc_idUser == trim("" . $userID)){
			$hc_visualiza = true;
		}else{
			if ($hc_idUser == "" && $role["executive"] && regiaok($db, $user, $hc_idRegion)){
				$hc_visualiza = true;
			}else{
			   	if ($role["regionalManager"] && regiaok($db, $user, $hc_idRegion) || $role["policy"]){
					$hc_visualiza = true;
			   	}else{
					if (regiadochefe($db, $user, $hc_idRegion)){
						$hc_visualiza = true;
					}
				}
		    	}
	     	}
      	}

	if ($hc_visualiza){
		$i++;
		 
		if(! trim($clientR))
			continue;
	
		if($i % 2 == 0){
			$ver = 'style="background-color:#FFF"';
		}else{
			$ver = ''; 
		}

		?>
		<tr <?php echo $ver;?>>
		<?php

		echo '<td class="td-no-sort" style="display: none;">'.$i.'</td>';
		
		if ($hc_visualiza){	
			if ($userID == $hc_idUser &&
			   	$role["executive"] ||
			   	$role["credit"] ||
			   	$role["creditManager"] ||
			   	$role["financ"] ||
			   	$role["regionalManager"] ||
			   	$role["tariffer"] ||
			   	$role["policy"] ||
			   	$role["sinistro"] ) {
	
				echo ("<td><a href=ListClient.php?comm=view&idInform=$inform>".$clientR."</a>".
				 	($stateR >= 9 && $stateR <= 11 && $start && $end ?
				 	" (vigência: $start a $end)" : "").
				 	($stateR < 9 && $idAnt && check_ant($idAnt) ? ' (renovação)' : '').
				 	"</td>");
		   	}else{
	
				echo ("<td >".$clientR."".
				 	($stateR >= 9 && $stateR <= 11 && $start && $end ?
				 	" (vigência: $start a $end)" : "").
				 	($stateR < 9 && $idAnt && check_ant($idAnt) ? ' (renovação)' : '').
				 	"</td>");
		   	}
		}else{
			echo ("<td >$clientR".
			 	($stateR >= 9 && $stateR <= 11 && $start && $end ?
			 	" (vigência: $start a $end)" : "").
			 	($stateR < 9 && $idAnt && check_ant($idAnt) ? ' (renovação)' : '').
			 	"</td>");
		}

		echo "<td align='center'>$DPP</td>";

		echo "<td align='center'>";
	
		if( (($Ga)=="") && ($stateR !=6 && $stateR !=10 && $stateR !=5) ){
			echo "--";
		}elseif( (($Ga)=="") && ($stateR ==6 || $stateR ==10 || $stateR ==5)){
			echo "RC";
		}elseif(($Ga)=="1"){
			echo "GA";
		}else{
			echo "RC";
		}

		echo "</td>";

         	if ($currency == "1") {
		    	$moeda = "R$";
		}if ($currency == "2") {
			$moeda = "US$";
		}elseif ($currency == "6") {
			$moeda = "&euro;";
		}elseif ($currency == "0") {
			$moeda = "&euro;";
		}
	
		echo "<td align='center'>$moeda</td>";
	
		echo ("<td >$executiveR&nbsp;</td>");
	
		switch ($stateR) {
			case 1:
				$stateR = "Novo";
			 	break;
			case 2:
			   	$stateR = "Preenchido";
			 	break;
			case 3:
				$stateR = "An. Crédito";
			 	break;
			case 4:
				$stateR = "Tarifação";
			 	break;
			case 5:
				$stateR = "Oferta";
			 	break;
			case 6:
				$stateR = "Proposta";
			 	break;
			case 7:
			   	$stateR = "1ª Parc. Pg";
			 	break;
			case 8:
				$stateR = "Alterado";
			 	break;
			case 9:
			   	$stateR = "Cancelado";
			 	break;
			case 10:
			   	$stateR = "Apólice";
			 	break;
			case 11:
			   	$stateR = "Encerrado";
			 	break;
		}

		echo "<td >".($stateR)."</td>";

		if ($stateR == "Cancelado") {
			$cur2 = odbc_exec($db, "select id from Inform where idAnt=$inform");

			if ( !odbc_fetch_row($cur2)) {
			  	$apolice = numApolice ($inform,$db,$dbSisSeg);

			  	if ($userID == $hc_idUser Or $role["policy"]) {
					?>
					<td  colspan="3" align="center">
                  			<button name="estudo" onClick="vai(1,<?php echo $inform;?>,'<?php echo $clientR;?>','<?php echo $executiveR;?>')" class="botaoapm">Novo Estudo</button>
					</td>
					<?php 
					?>
					</tr>
					<?php 
			  	} else {
					if ($userID == $hc_idUser &&
				   		$role["executive"] ||
				   		$role["credit"] ||
				   		$role["creditManager"] ||
				   		$role["financ"] ||
				   		$role["regionalManager"] ||
				   		$role["tariffer"] ||
				   		$role["policy"] ||
				   		$role["sinistro"] ) {
	
				   		if ($stateR == "Cancelado" && !$start && !$end){
					 		echo "<td  colspan=\"3\" align=\"center\">&nbsp;</td>";
				   		} else {
					 		echo "<td  colspan=\"3\" align=\"center\">&nbsp;</td>";
				   		}
					} else {
				   		echo "<td  colspan=\"3\" align=\"center\">&nbsp;</td>";
					}

					?>
					</tr>
					<?php 
				}
		   	} else
	
			 if ($userID == $hc_idUser &&
				  $role["executive"] ||
				  $role["credit"] ||
				  $role["creditManager"] ||
				  $role["financ"] ||
				  $role["regionalManager"] ||
				  $role["tariffer"] ||
				  $role["policy"] ||
				  $role["sinistro"] ) {
	
				if ($stateR == "Cancelado" && !$start && !$end) {
	
				  /*
				  //####### ini ####### adicionado por eliel vieira - elumini - 26/03/2008
				  // retirada do botao reativar informe - ref. demanda 1440
				  //
				  //
				  <td  colspan="3" align="center">
					<input type="button" onClick="vai(0,$inform,'$clientR','$executiveR')" value="Reativar Informe" class="sair">
				  </td>
				  */
	
				  echo "<td  colspan=\"3\" align=\"center\">&nbsp;</td>";
	
				} else {
	
				  echo "<td  colspan=\"3\" align=\"center\">&nbsp;</td>";
	
				}
	
				echo "</tr>";
	
			 }
	
		 } else {
	?>
			   <td  align="center">
	
			   <?php
			   
			    //print_r($user->roles);
	
			   if ($userID == $hc_idUser &&
				  $role["executive"] ||
				  $role["credit"] ||
				  $role["creditManager"] ||
				  $role["financ"] ||
				  $role["regionalManager"] ||
				  $role["tariffer"] ||
				  $role["policy"] ||
				  $role["sinistro"]
				  ) {
	
	              
					// $executive = array_search("executive", $userID);
	
					if (($stateR == "Novo") && (($role["executive"]) || ($role["executiveLow"]) || ($role["regionalManager"]) || ($role["policy"])))
					{
						echo "
						<button name=\"sair\" type=\"submit\" class=\"botaovgm\" onClick=\"cancela(0,".$inform.",'".$clientR.">','".$executiveR."')\">Cancelar Informe</button>
						";
					}
					else if (($stateR == "Preenchido") && (($role["executive"]) || ($role["executiveLow"]) || ($role["regionalManager"]) || ($role["policy"])))
					{
						echo "
						<button name=\"sair\" type=\"submit\" class=\"botaovgm\" onClick=\"cancela(0,".$inform.",'".$clientR.">','".$executiveR."')\">Cancelar Informe</button>
						";
					}
					else if (($stateR == "An. Crédito") && (($role["credit"]) || ($role["creditManager"]) || ($role["creditInform"]) || ($role["policy"])))
					{
						echo "
						<button name=\"sair\" type=\"submit\" class=\"botaovgm\" onClick=\"cancela(0,".$inform.",'".$clientR.">','".$executiveR."')\">Cancelar Informe</button>
						";
					}
	
					//Ativado por Michel Saddock 11/10/2006
					else if (($stateR == "Tarifação") && (($role["tariffer"]) || ($role["policy"])))
					{
						echo "
						<button name=\"sair\" type=\"submit\" class=\"botaovgm\" onClick=\"cancela(0,".$inform.",'".$clientR.">','".$executiveR."')\">Cancelar Informe</button>
						";
					}
					//Ativado por Michel Saddock 03/10/2006
					//else if ((($stateR == "Oferta") || ($stateR == "Proposta")) && (($role["regionalManager")) || ($role["policy"))))
					else if ((($stateR == "Oferta") ) && (($role["regionalManager"]) || ($role["policy"]||($role["executive"]))))
					{
						echo "
						<button name=\"sair\" type=\"submit\" class=\"botaovgm\" onClick=\"cancela(0,".$inform.",'".$clientR.">','".$executiveR."')\">Cancelar Informe</button>
						";
					}
	
				} else {
	
				  echo "<td class=\"texto\" colspan=\"3\" align=\"center\">&nbsp;</td>";
	
				}
	
				?>
	
				</td>
			 </tr>
            
	<?php 
		 }

    }  //if

} // while





if ($i == 0) {
?>
   
    <TR>
      <TD colspan="7" align=center>Nenhum Cliente Encontrado</TD>
    </TR>
<?php 
}


?></tbody>
