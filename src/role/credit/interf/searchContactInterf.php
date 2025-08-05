<?php


  $idInform = $field->getField("idInform");
	
	$q = "SELECT name, contrat, id	FROM Inform WHERE id = $idInform";
	$c = odbc_exec($db, $q);

	if(odbc_fetch_row($c))      {
		$nameExpo = odbc_result($c, 1); 
		$ciExpo	  = odbc_result($c, 2); 
	} else {
		$nameExpo = 	"Erro";
	}
?>

<li class="campo2colunas">
    <label>Segurado</label>
    <?php echo ($nameExpo);?>
</li>
       
<li class="campo2colunas">
    <label>Ci Segurado</label>
    <?php echo ($ciExpo);?>
</li>

<div class="divisoria01"></div>

<table>
	<thead>
      <tr>
	  	  <th>Nome</th>
          <th>Telefone</th>
          <th>Cargo</th>
          <th>Tipo Contato</th>
          <th>E-mail</th>
          <th>Recebe E-mail</th>
          <th colspan="2">A&ccedil;&atilde;o</th>
      </tr>
    </thead>
	<tbody>
<?php  if(odbc_fetch_row($contact)){   
	     $cargo		= odbc_result($contact, 2);
	     $telefone          = odbc_result($contact, 4); 
	     $fax               = odbc_result($contact, 5); 
	     $Name              = odbc_result($contact, 1);
	     $email             = odbc_result($contact, 3); 
	     $idContact         = odbc_result($contact, 6); 
	     $emailCredit       = "SIM"; 
	     $type              = "i";
  	     $idContact             = odbc_result($contact, 'id');

   	
?>

    <tr>
		  <td><?php echo  ($Name);?></td>
          <td><?php echo  $telefone;?></td>
          <td><?php echo  ($cargo);?></td>
          <td><?php echo  'Principal';?></td>
          <td><?php echo  ($email);?></td>
          <td><?php echo  ($emailCredit);?></td>
          <td>
          <div align="center">
          <a href="<?php echo $root;?>role/credit/Credit.php?comm=AlterContact&princ=1&idInform=<?php echo $idInform;?>"><img src="<?php echo $root;?>/images/icone_editar.png" alt="" width="24" height="24" class="iconetabela" /></a>
          </div>
          </td>
          <td>
          <div align="center">
         <!-- <a href="<?php echo $root;?>role/credit/Credit.php?comm=DeleteContact&local=Inform&princ=1&idInform=<?php echo $idInform;?>">-->
                  <img src="<?php echo $root;?>images/icone_deletar.png" alt="" width="24" height="24" class="iconetabela" />
         <!--</a>-->
          </div>

<?php  }


	require_once("../credit/searchcontact.php");
?>


<?php  $i = 1;
	while(odbc_fetch_row($cC)){
	     $cargo		= odbc_result($cC, 7);
	     $telefone          = odbc_result($cC, 5); 
	     $fax               = odbc_result($cC, 6); 
	     $Name              = odbc_result($cC, 4);
	     $email             = odbc_result($cC, 8); 
	     $idContact         = odbc_result($cC, 1); 
	     $emailCredit       = odbc_result($cC, 9);
		 $i_Tipo_Contato    = odbc_result($cC, 'i_Tipo_Contato');
		 $Descricao         = odbc_result($cC, 'Descricao');
		 $type              = "c";

		 if ($emailCredit == "1")	
			$emailCredit = "SIM";
		 else
			$emailCredit = "NÃO";		

	     $i++;

?>
      <!-- Consulta da tabela Contact-->
      <tr>
 	  	  <td><?php echo  ($Name);?></td>
          <td><?php echo  ($telefone);?></td>
          <td><?php echo  ($cargo);?></td>
          <td><?php echo  ($Descricao)?></td> 
          <td><?php echo  ($email);?></td>
          <td><?php echo  ($emailCredit);?></td>
          <td>
          <div align="center">
          <a href="<?php echo $root;?>role/credit/Credit.php?comm=AlterContact&princ=1&idInform=<?php echo $idInform;?>&idContact=<?php echo $idContact;?>"><img src="<?php echo $root;?>/images/icone_editar.png" alt="" width="24" height="24" class="iconetabela" /></a>
          </div>
          </td>
          <td>
          <div align="center">
          <a href="<?php echo $root;?>role/credit/Credit.php?comm=DeleteContact&local=Contact&princ=1&idInform=<?php echo $idInform;?>&idContact=<?php echo $idContact;?>"><img src="<?php echo $root;?>images/icone_deletar.png" alt="" width="24" height="24" class="iconetabela" /></a>
          </div></td>
        </tr>
<?php  }
?>

</table>
 <div style="clear:both">&nbsp;</div>
<?php  if ($msg1 != "") { 
          ?><p style="color:#FF0000"><?php echo  $msg1;?></p>
<?php  } ?>

        <form action="<?php echo  $root;?>role/searchClient/ListClient.php">
          <input type="hidden" name="comm" value="view">
          <input type="hidden" name="idInform" value="<?php echo  $idInform;?>">
            <div class="barrabotoes">
                <button type="submit" class="botaoagm">Voltar</button>
            </div>
        </form>

