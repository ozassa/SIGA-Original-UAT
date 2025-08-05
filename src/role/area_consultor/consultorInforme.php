<?php
 //error_reporting(E_ALL);
 $id = $_SESSION['userID'];


include_once('../../../navegacao.php');

?>
<div class="conteudopagina">
<li class="campo2colunas">
   <label><img src="../imagens/consultor.gif" border="0">Abaixo segue a lista de informes associados ao seu usu&aacute;rio</label>
</li>


<TABLE id="example" class="tabela01">
  <thead>
       <TR>
        <Th scope="col">Informe</Th>
        <Th scope="col">Cliente</Th>
        <Th scope="col">Status</Th>
        <Th scope="col">A&ccedil;&atilde;o</Th>
       </TR>
  </thead> 
  <tbody>
<?php

    //$query = " select i.id, i.name, i.idConsultor, i.state from Inform i where i.idConsultor = '$id' ";
    $query = " Select 
		i.id, 
		i.name, 
		i.idConsultor, 
		i.state 
	from 
		Inform i
	Inner Join consultor C On
		C.idconsultor = i.idConsultor
	Inner Join Corretor_Usuario CU On
		CU.i_Corretor = C.idconsultor
	where 
		i.state = 10
		And CU.i_Usuario = $id
	Order By
		i.n_Apolice";



    $cur = odbc_exec($db, $query);

    while (odbc_fetch_row($cur)){
      
       $id = odbc_result($cur,1);
       $nome = odbc_result($cur,2);
       $idconsultor = odbc_result($cur, 3);
       $state = odbc_result($cur, 4);
	   
	   if ($i % 2 == 0)
	      $cor = ' class= "odd" ';
	   else
	      $cor = ' ';
		  
	    $i++;
?>

      <tr <?php echo $cor;?>>
        <td><?php echo $id;?></td>
        <td ><?php echo $nome;?></td>
        <td><?php  
				 if($state==1)
				 {echo "Novo";}
				 elseif($state==2)
				 {echo "Preenchido";}
				 elseif($state==3)
				 {echo "Cr&eacute;dito";}
				 elseif($state==4)
				 {echo "Tarifado";}
				 elseif($state==5)
				 {echo "Oferta";}
				 elseif($state==6)
				 {echo "Proposta";}
				 elseif($state==10)
				 {echo "Ap&oacute;lice";}
				 else
				 {echo "Encerrado";}
    
             ?>
        </td>
        <td><a href="../inform/Inform.php?comm=open&idInform=<?php echo$id;?>">Entrar</a></td>
      </tr>
<?php  
  }   // Fecha while

     if ($i == 0) {  ?>
          <TR>
            <TD align="center" colspan=4>Nenhum Informe encontrado</TD>
          </TR>
<?php  }  ?>
   </tbody>
</table>

 <div style="clear:both">&nbsp;</div>   
    
   
    <form action="<?php echo $root;?>role/area_consultor/listConsultor.php" method="post">
        <input type=hidden name="comm" value="alteraSenha">
        <input type=hidden name="id" value="<?php echo $id;?>">
        <li class="campo3colunas">
          <label>Alterar senha:</label>
          <input type="password"  name="senha">
        </li>
        <div class="barrabotoes">          
         <button  class="botaoagm" name="botao" type="button"  onClick="this.form.submit()">Alterar</button>
        </div> 
    </form>
   

</div>