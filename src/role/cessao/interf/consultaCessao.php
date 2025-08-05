<?php 

	if(check_menu(["bancoBB"], $role) ){
	  $query = "
		   SELECT DISTINCT inf.name, inf.id
		   FROM Inform inf
    		  JOIN UsersNurim us ON (us.idUser = ?)
			  JOIN Nurim nu ON (nu.id = us.idNurim)
			  JOIN Agencia ag ON (ag.idNurim = nu.id)
			  JOIN CDBB cd ON (cd.idAgencia = ag.id)
			  JOIN Importer imp ON (imp.idInform = inf.id)
			  JOIN CDBBDetails cdd ON (cdd.idCDBB = cd.id and cdd.idImporter = imp.id)
		WHERE inf.state in (10,11) 
		      AND cd.status in (2,4);";

	}else if($role["bancoParc"]){
	  $query = "
		   SELECT DISTINCT inf.name, inf.id
			FROM Inform inf
    			  JOIN Banco bc ON (bc.idUser = ?)
				  JOIN CDParc cd ON (cd.idBanco = bc.id)
				  JOIN Importer imp ON (imp.idInform = inf.id)
				  JOIN CDParcDetails cdd ON (cdd.idCDParc = cd.id AND cdd.idImporter = imp.id)
			WHERE inf.state in (10,11) AND cd.status in (2,4) 
		";
	}else{
	  $query = "
		   SELECT DISTINCT inf.name, inf.id
			FROM Inform inf
    			 JOIN Banco bc ON (bc.idUser = ?)
				  JOIN CDOB cd ON (cd.idBanco = bc.id)
				  JOIN Importer imp ON (imp.idInform = inf.id)
				  JOIN CDOBDetails cdd ON (cdd.idCDOB = cd.id AND cdd.idImporter = imp.id)
			WHERE inf.state in (10,11) AND cd.status in (2,4)
		";
	}


	$prepare = odbc_prepare($db, $query);

	$params = array($userID);

	$cur = odbc_execute($prepare, $params);


?>

<?php require_once("../../../navegacao.php"); ?>

<div class="conteudopagina">
	<table summary="Submitted table designs" id="example">
	  <caption>Lista de Segurados</caption>
	    <thead>
	      <tr>
	        <th>&nbsp;</Th>
	        <th>Raz&atilde;o Social</th>
	        <th>Declara&ccedil;&atilde;o de Regularidade</th>
	      </tr>
	    </thead>
	    <tbody>
		    <?php 
		    	//$cur=odbc_exec($db,$query);
		      $i = 0;
		      while (odbc_fetch_row($prepare)) {
		        $i++;
		        $idInform = odbc_result($prepare, 2); ?>
		      <tr>
		        <td><?php echo $i;?></td>
		        <td><a href="../cessao/Cessao.php?comm=consultaCessaoExp&idInform=<?php echo $idInform;?>&name=<?php echo odbc_result($prepare, 1);?>"><?php echo odbc_result($prepare,1);?></a></td>
		        <td><a href="<?php echo $root;?>role/cessao/nadaconsta.php?idInform=<?php echo $idInform;?>&codigo=<?php echo '0';?>" target=_blank>Imprimir</a></td>
		      </tr>
		    <?php } // while
		      if ($i == 0) { ?>
		      <tr>
		        <td colspan="7">Nenhum Exportador Cadastrado</td>
		      </tr>		    
		    <?php }?>
	    </tbody>
	  <div class="divisoria01"></div>
	</table>

  <div style="clear:both">&nbsp;</div> 
</div>