
	<?php require_once("../../../navegacao.php");?>
    
    
    <!-- CONTEÚDO PÁGINA - INÍCIO -->
    <div class="conteudopagina">
	<table summary="Submitted table designs" id="example">
			<thead>
				<tr>
				  <th scope="col">Empresa</th>
				  <th scope="col">E-mail</th>

				  <th scope="col">Executivo Responsavel</th>
				  <th scope="col">Data de Cadastro</th>
			  </tr>
			</thead>	
			<tbody>
            <?php  $cur = odbc_exec ($db, "SELECT Inform.id, Inform.name, Inform.contact, Users.login, Inform.bornDate
				FROM Inform INNER JOIN
				    Insured ON Inform.idInsured = Insured.id INNER JOIN
				    Users ON Insured.idResp = Users.id
				WHERE (Inform.state = 0) OR
				    (Inform.state = 1) ORDER BY Inform.id");
			$i = 0;
		
			while (odbc_fetch_row ($cur)) {
				$nameInform	= odbc_result ($cur, 2);
				$idInform	= odbc_result ($cur, 1);
				$contactInform	= odbc_result ($cur, 3);
				$loginUsers	= odbc_result ($cur, 4);
				$bornDate	= odbc_result ($cur, 5);
				$i ++;
		
				$bornDate = substr($bornDate,8,2)."/".substr($bornDate,5,2)."/".substr($bornDate,0,4);	
?>
                <tr>
                    <td><a href="../searchClient/ListClient.php?comm=view&remove=ok&idInform=<?php   echo $idInform;?>"><?php   echo ($nameInform == '' ? 'Sem Nome' : $nameInform);?></a></td>
                    <td><?php   echo $loginUsers;?></td>
                
                    <td><?php   echo $contatctInform;?></td>
                    <td><?php   echo $bornDate;?></td>
                </tr>
<?php  }?>
			</tbody>		
					
					
		</table>
        <div class="divisoria01"></div>
        <?php 

			if($envia){	
		?>
      <div class="barrabotoes"><a href="pag_consumiveis.asp"><img src="<?php echo $host;?>images/botao_incluir.png" alt="" /></a></div>
      <?php }?>
    </div>
    <!-- CONTEÚDO PÁGINA - FIM -->



