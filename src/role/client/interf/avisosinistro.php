<?php require_once("../../../navegacao.php");?>

<div class="conteudopagina">
<?php  if ($msg){ ?>
   <p>Gerado Aviso de Sinistro</p>
<?php  } ?>
<table id="example">
   <caption>Lista de Importadores</caption>
  <thead>
  <tr>
    <th>&nbsp;</th>
    <th>Importador</th>
    <th>Pa&iacute;s</th>
  </tr>
  </thead>
  <tbody>
  <!-- início de um importador -->
<?php  $query = "
        SELECT imp.name, c.name, imp.id, imp.limCredit
	FROM Importer imp
          JOIN Country c ON (imp.idCountry = c.id)
	WHERE imp.idInform = $idInform
          AND imp.state NOT in (1,3,4,7,8)
          AND imp.id NOT in (
            SELECT s.idImporter
            FROM Sinistro s
            WHERE s.status >= 2 and s.status <> 7 and s.status <> 6
          )
	ORDER BY imp.name";

  $cur=odbc_exec($db, $query);
  $i = 0;
  while (odbc_fetch_row($cur)) {
    $i++;
    $idBuyer = odbc_result($cur,3);
    $aux = odbc_exec($db, "select credit from ChangeCredit where idImporter=$idBuyer");
    if(odbc_fetch_row($aux)){
?>

  <tr>
    <td><?php  echo $i ?></td>
    <td><a href="<?php  echo $root?>role/client/Client.php?comm=geraravisosinistro&idInform=<?php  echo $idInform;?>&idImporter=<?php  echo odbc_result($cur,3);?>#sinistro"><?php  echo odbc_result($cur,1);?></a></td>
    <td><?php  echo odbc_result($cur,2);?></td>
  </tr>

<?php  } // if

  } // while

?>
  </tbody>
    
</table>
  <div class="barrabotoes">
    <form action="<?php  echo $root;?>role/sinistro/Sinistro.php" method="post">
    <input type=hidden name="comm">
    <input type=hidden name="idInform" value="<?php  echo $idInform;?>">
    <?php  if ($role["client"]){ ?>
        <button class="botaoagm" onclick="this.form.comm.value='voltarCliente';this.form.submit()">Voltar</button>
    <?php  }else{?>
    	<button class="botaoagm" onclick="this.form.comm.value='voltarFunc';this.form.submit()">Voltar</button> 
    <?php  }?>
    </form>
  </div>

</div>