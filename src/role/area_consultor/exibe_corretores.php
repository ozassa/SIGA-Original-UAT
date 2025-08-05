<script language="javascript">
function envia()
{
location.href="listConsultor.php?comm=voltarCadastro";
}

function confirmaSubmit(mens) {

    var conf
    conf=confirm(mens);
    if (conf)
       return true;
    else
		    return false;

}

</script>
<div class="conteudopagina">
   <table summary="Submitted table designs" id="example">
       <thead>
       <tr>
            <th>Raz&atilde;o Social</th>
            <th>Consultor</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>C&oacute;digo SUSEP</th>
            <th colspan="3">A&ccedil;&atilde;o</th>
       </tr>
       </thead>
       <tbody>
		<?php $query = "SELECT * FROM consultor order by razao";
    
        $cur = odbc_exec($db, $query);
    
        while (odbc_fetch_row($cur)){
          $i++;
          $idconsultor = odbc_result($cur,1);
          $nome = odbc_result($cur,'contato');
          $contato = odbc_result($cur, 4);
          $email = odbc_result($cur, 6);
          $ativo = odbc_result($cur, 'ativo');
          $razao = odbc_result($cur, 'razao');
          $cSusep = odbc_result($cur, 'c_SUSEP');
          
         ?>
          <tr>
            <td><?php echo $razao;?></td>
            <td><?php echo $nome;?></td>
            <td><?php echo $contato;?></td>
            <td><?php echo $email;?></td>
            <td><?php echo $cSusep;?></td>
            <td>
            <a href="listConsultor.php?comm=editarConsultor&idconsultor=<?php echo $idconsultor;?>"> 
             <?php echo((($i % 2) != 0) ? "<img src=\"../../../images/icone_editar.png\" title=\"Editar\" width=\"24\" height=\"24\" class=\"iconetabela\">" : "<img src=\"../../../images/icone_editar.png\" title=\"Editar\" width=\"24\" height=\"24\" class=\"iconetabela\">");?>
            </a></td>
            <?php
            //print $ativo;
            if(($ativo)=='1'){  //1-ativo e 0-inativo ?>
                     <td><a href="listConsultor.php?comm=DesativarConsultor&idconsultor=<?php echo $idconsultor;?>&ativo=0"><img src="../imagens/ativado1.gif" title="Ativado" border="0"></a></td>
            <?php
            }else{   ?>
                     <td><a href="listConsultor.php?comm=DesativarConsultor&idconsultor=<?php echo $idconsultor;?>&ativo=1"><img src="../imagens/desativado1.gif" title="Desativado" border="0"></a></td>
             <?php
             }
             ?>
             
            <td>
            <a onClick="return confirmaSubmit('Deseja realmente excluir o consultor?')" href="listConsultor.php?comm=excluirConsultor&idconsultor=<?php echo $idconsultor;?>">
			<?php echo((($i % 2) != 0) ? "<img src=\"../../../images/icone_deletar.png\" title=\"Deletar\" width=\"24\" height=\"24\" class=\"iconetabela\">" : "<img src=\"../../../images/icone_deletar.png\" title=\"Deletar\" width=\"24\" height=\"24\" class=\"iconetabela\">");?>     		</a>
            </td>
      	</tr>

    <?php } // Fecha while
  
	  if ($i == 0)
	  {
	?>
	  <tr>
		<td colspan="6">Nenhum Consultor Cadastrado</td>
	  </tr>
	<?php }
	?>
    </tbody>

   </table>
   <div class="barrabotoes">
   		<a href="listConsultor.php?comm=voltarCadastro"><button name="sair" class="botaovpm">Voltar</button></a>
   </div>
</div>