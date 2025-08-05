<div class="conteudopagina">
	<ul>    
	  <li class="campo2colunas">
        <label>Segurado</label>
		<?php echo  $nameExpo;?>
      </li>
      <li class="campo2colunas">
        <label>Ci Segurado</label>
		<?php echo  $ciExpo;?>
      </li>
	  </ul>
      <div class="divisoria01"></div>
      <form action="<?php echo $root;?>role/credit/Credit.php" method="post" name="coface_imp">
          <input type=hidden name=comm value="trataEndereco">
          <input type=hidden name=idNotification value="<?php echo $idNotification;?>">
          <input type=hidden name=idInform value="<?php echo $idInform;?>">
        

        <table>
            <thead>
              <tr>
                <th>Comprador</th>
                <th>Endere&ccedil;o</th>
                <th>Cidade</th>
                <th>Telefone</th>
                <th>CEP</th>
                <th>Aceitar</th>
             </tr>  
           </thead>
           <tbody>

		<?php  $i= 0;  
          
          if(odbc_fetch_row($cur))
          {
          
            do{
          
              $hc_imp_id    = odbc_result($cur, 'id'); 
              $hc_imp_name  = odbc_result($cur, 'name'); 
              $hc_add_id    = odbc_result($cur, 'idAddress');
              $hc_address   = odbc_result($cur, 'address');  
              $hc_city      = odbc_result($cur, 'city');   
              $hc_tel       = odbc_result($cur, 'tel');  
              $hc_cep       = odbc_result($cur, 'cep');  
            
            
        ?>
        
     <tr>
       <td><input type="hidden" name="frm_idImporter<?php echo $i;?>" value="<?php echo $hc_imp_id;?>"><?php echo $hc_imp_name;?></td>
       <td><input type="hidden" name="frm_id<?php echo $i;?>" value="<?php echo $hc_add_id;?>"><?php echo $hc_address;?></td>
       <td><?php echo $hc_city;?></td>
       <td><?php echo $hc_tel;?></td>
	   <td><?php echo $hc_cep;?></td>
	   <td><input type="checkbox" checked="checked" name="frm_aceitar<?php echo $i ?>" /> </td>
     </tr>

	<?php  $i++;
        }while(odbc_fetch_row($cur));
      }	
  
  
  


	if ($i == 0) {
	?>
	 <tr>
		<td colspan="6">Nenhum novo endere&ccedil;o encontrado!</TD>
	 </tr>
	<?php  } ?>
</tbody>
</table>
<div class="barrabotoes">
  <input type="hidden" name="frm_i" value="<?php echo  $i;?>">
  <button name="voltar" onClick="document.forms[1].submit()" class="botaoagm">Voltar</button>
  <?php  if ($i > 0) { ?>
  <button name="processar" type="submit" class="botaoagm">Processar</button>
  <?php  } ?>
</div>
</form>

<form action="<?php echo  $root;?>role/credit/Credit.php" method="post">
<input type="hidden" name="comm" value="open">
</form>

</div>