<?php include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">
	<ul>
		<li class="campo2colunas">
			<label>Exportador</label>
			<?php echo $nameExpo;?>
		</li>
		<li class="campo2colunas">
			<label>Ci Exportador</label>
			<?php echo $ciExpo;?>
		</li>
	</ul>
	<?php  echo "<p>Cliente altera campos do importador $importer";?>
    <table>
        <caption>Campos alterados</caption>
        <thead>
            <tr>
                <th class=bgAzul>Campo</th>
                <th class=bgAzul>Valor Antigo</th>
                <th class=bgAzul>Valor Atual</th>
            </tr>
        </thead>
        <tbody>
    
		<?php  $v = array('cep' => 'CEP', 'fax' => 'Fax', 'contact' => 'Contato',
               'address' => 'Endereço', 'idCountry' => 'idCountry',
               'tel' => 'Telefone', 'prevExp12' => 'Previsão do Volume de Exportações',
               'limCredit' => 'Limite de Crédito', 'numShip12' => 'Número de embarques por ano',
               'periodicity' => 'Peridiocidade', 'risk' => 'Risco', 'city' => 'Cidade',
               'przPag' => 'Prazo de Pagamento', 'name' => 'Nome', 'divulgaNome' => 'Autor. divulgar nome',
               'emailContato' => 'Email contato');
        
        $i = 1;
        $fields = explode('&', base64_decode($fields_changed));
        
        foreach($fields as $f){
		
          list($nome, $valor) = explode('=', $f);
          $c = odbc_exec($db, "select $nome from Importer where id=$idBuyer");
          $x = odbc_result($c, 1);
		   
		  
		?>
        
        <tr>
            <td><?php echo $v[$nome];?></th>
            <td><?php echo $valor;?></th>
            <td><?php echo $x;?></th>
        </tr>
 <?php
          $i++;
		  
        }
 
 ?>
        </tbody>
    </table>

    <form action="<?php echo  $root;?>role/credit/Credit.php" method="post">
    <input type="hidden" name="comm" 			value="mudou">
    <input type="hidden" name="idNotification"  value="<?php echo  $idNotification;?>">
    <input type="hidden" name="idInform" 		value="<?php echo  $idInform;?>">
    <input type="hidden" name="idBuyer" 		value="<?php echo  $idBuyer;?>">
    <input type="hidden" name="done" 			value="1">
    <div class="barrabotoes">  
        <button class="botaovgm" type="button" onClick="this.form.comm.value='open'; this.form.submit()">Voltar</button>
        <button class="botaoagm" type="submit"/>OK</button>
    </div>  
    </form>
</div>