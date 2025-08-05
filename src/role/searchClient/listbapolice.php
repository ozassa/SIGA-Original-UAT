<?php 

 include_once('../../../navegacao.php');
/*
Criado por Rony - (Elumini)
Criacao da Interface Listar Informe
*/
$envia  = $_REQUEST['envia'];
?>

<div class="conteudopagina">
  <FORM id="Form2" name="Form2" action="<?php echo $root;?>role/searchClient/searchApolice.php" method="post">
   <li class="campo3colunas"><label>Nome</label>
     <input type="text" name="nome" size="30" value="">
   </li>  
   <li class="campo3colunas"><label>N&ordm; CI</label>
      <input type="text" name="napolice" size="30" class="caixa">
   </li>
   <li class="campo3colunas"><label>Status</label>
      <select name="stinforme">
           <option value=0></option>
           <option value=1>Novo</option>
           <option value=2>Preenchido</option>
           <option value=3>Validado</option>
           <option value=4>Analisado</option>
           <option value=5>Tarifado</option>
           <option value=6>Proposta</option>
           <option value=7>Confirmado</option>
           <option value=8>Alterado</option>
		   <option value=10>Ap&oacute;lice</option>
       </select>
   </li>
   
      <input type="hidden" name="envia"  value="1">
    <div class="barrabotoes">
      <button  type="submit" class="botaoagm" onClick="this.form.submit()">OK</button>
    </div>

    <table summary="Submitted table designs" id="example">
       <thead>
           <tr>
              <th scope="col">Nome Empresa</th>
              <th scope="col">N&ordm; de CI</th>
              <th scope="col">Status</th>
          </tr>
      </thead>
      <tbody>
<?php  if($envia){  

		   $st ="0";
		
		   $sql ="SELECT  id, name, contrat, state FROM Inform WHERE
				 state <> 11 And state <> 9 And name LIKE ('".strtoupper($nome)."%')";
		  if ( $stinforme <> 0 ) {
			 $sql.= " And state='$stinforme'";
		  }
  
		  $cur = odbc_exec($db, $sql);
			
		  $i = 0;
			 while (odbc_fetch_row ($cur)) {
					$st ="1";
					if ($i % 2 == 0  ) {
					   $color = 'class="odd"';
					   
					}else{
					   $color = "";
					  
					}
		          
				   
					switch (odbc_result($cur, 4) ) {
					case 1:
						   $status = "Novo";
						   break;
					case 2:
						   $status = "Preenchido";
						   break;
					case 3:
						   $status = "Validado";
						   break;
					case 4:
						   $status = "Analisado";
						   break;
					case 5:
						   $status = "Tarifado";
						   break;
					case 6:
						   $status = "Proposta";
						   break;
					case 7:
						   $status = "Confirmado";
						   break;
					case 8:
						   $status = "Alterado";
						   break;
					case 9:
						   $status = "Cancelado";
						   break;
					case 10:
						   $status = "Apólice";
						   break;	   
					}
				
				?>
				
                 <tr <?php echo $color;?>>                
				    <td><A href="searchApolice.php?comm=listlogin&idclient=<?php echo odbc_result($cur, 1);?>"><?php echo (ucfirst(strtolower(trim(odbc_result($cur, 2)))));?></a></td>
				    <td><?php echo odbc_result($cur, 3) ?></td>
                    <td><?php echo ($status);?></td>
                 </tr>
   <?php 
		       $i++;
		   }
		
		    if ( $st == "0" ) {    ?>
			        <TR> 
			             <TD colspan=3>Nenhuma Empresa ou Nº de CI Encontrado</TD>
			        </TR> 
	<?php	}
	
		
		}    ?>      

        </tbody>
      </table>

   </form>
   <div style="clear:both">&nbsp;</div>
</div>
