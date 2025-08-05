<?php  // converte a data de yyyy-mm-dd para dd/mm/yyyy

//function ymd2dmy($d){

//  if(preg_match("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $d, $v)){

//    return "$v[3]/$v[2]/$v[1]";

//  }

//  return $d;

//}



function typeString($tipo){

  switch($tipo){

  case 1: return 'Dados Cadastrais';

  case 2: return 'Natureza da Operação';

  case 3: return 'Parcela de Ajuste';

  case 4: return 'Prêmio Mínimo';

  }

  return "Tipo não definido: $tipo";

}



function stateString($state){

  switch($state){

  case 1: return 'Solicitado';

  case 2: return 'Ativo';

  case 3: return 'Cancelado';

  case 4: return 'A emitir';

  }

}





?>

<script>

<!--

function seleciona (obj) {

//  verErro(obj.selectedIndex);

  form = obj.form;

  if (obj.selectedIndex != 1) {

    form.formfocus.value='';

    form.fieldfocus.value=''

  }

 form. submit();

// -->

}

</script>
<?php require_once("../../../navegacao.php");?>
<div class="conteudopagina">
<form action="<?php  echo $root;?>role/client/Client.php#endosso" method="post" name="cadastro">

<input type="hidden" name="comm" value="newEndosso">
<input type="hidden" name="formfocus" value="cadastro">
<input type="hidden" name="fieldfocus" value="new_name">
<input type="hidden" name="idInform" value="<?php  echo $idInform;?>">
<input type="hidden" name="back" value="<?php  echo $back;?>">
<?php  if($role["client"]){ ?>
<p>Endosso</p>
<?php  }

if($role['endosso']){

  $cur = odbc_exec($db, "SELECT name, contrat FROM Inform WHERE id = $idInform");

  $name = odbc_result($cur, 1);

  $contrat = odbc_result($cur, 2);

?>
<li class="campo2colunas">
	<label>Nome</label>
    <?php  echo ($name);?>
</li>

<li class="campo2colunas">
	<label>Contrat</label>
    <?php  echo ($contrat);?>
</li>

<?php  }



  $cur = odbc_exec($db, "SELECT state FROM Inform WHERE id = $idInform");

  $state = odbc_result($cur, 1);



  if(($role['endosso']||$role['client']) && ($state == 10)){



?>
  
  <li class="campo2colunas">
  		<label>Solicitar</label>
        <select name="tipo" class="caixa" onChange="seleciona (this)">
	          <option value="0">Selecione a Modalidade</option>
              <option value="1">Dados Cadastrais</option>
              <option value="2">Natureza da Opera&ccedil;&atilde;o</option>
			  <?php  if ($role["backoffice"] ||$role["endosso"]){ ?>
              <option value="4">Pr&ecirc;mio M&iacute;nimo</option>
              <?php  } ?>
        </select>
  </li>
  

<?php  }?>

</form>

<?php  if($msg){

  echo "<p>$msg</p>";

}

?>

<table>
  <caption>Consultar</caption>
  <thead>
  <tr>

    <th>Data de Cria&ccedil;&atilde;o</th>
    <th>Tipo</th>
    <th>Status</th>

  </tr>
  </thead>
  <tbody>

	<?php  
    $cur = odbc_exec($db, "SELECT bornDate, tipo, state, id FROM Endosso WHERE idInform = $idInform order by bornDate");
    
    $i = 0;
    
    while(odbc_fetch_row($cur)) {
    
      $i++;
    
      $idEndosso = odbc_result($cur, 4);
    
      $tipo = odbc_result($cur, 2);
    
    ?>

  <tr>

    <td><a href="../client/Client.php?comm=ConsultaEndosso&idEndosso=<?php  echo $idEndosso;?>&idInform=<?php  echo $idInform;?>&tipo=<?php  echo $tipo;?>#endosso"><?php  echo ymd2dmy(odbc_result($cur, 1));?></a></td>

    <td><?php  echo typeString(odbc_result($cur, 2));?></td>

    <td><?php  echo stateString(odbc_result($cur, 3));?></td>

  </tr>

<?php  } // while?>
</tbody>
</table>

<?php  if($back){ ?>

<form action="<?php  echo $root;?>role/searchClient/ListClient.php" method="post">

<?php  }else{ ?>

<form action="<?php  echo $root;?>role/client/Client.php" method="post">

<?php  } ?>

<input type="hidden" name="idInform" value="<?php  echo $idInform;?>">

<input type="hidden" name="comm" value="open">

<div class="barrabotoes">
    <button class="botaoagm" type="submit">Voltar</button>
</div>

</form>
</div>