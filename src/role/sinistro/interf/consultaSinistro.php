<?php require_once("../../../navegacao.php");?>

<div class="conteudopagina">
<?php if ($role["client"]){ ?>
   <p>&nbsp;</p>
<?php }else{ ?>
  <p>Consulta Sinistro</p>
	<?php $query = "SELECT name, contrat from Inform WHERE id = $idInform";
        $cur = odbc_exec($db,$query);
    ?>
      <li class="campo2colunas">
      		<label>Exportador</label>
            <?php echo (odbc_result($cur, 1));?>
      </li>
      <li class="campo2colunas">
      		<label>Ci Exportador</label>
            <?php echo (odbc_result($cur, 2));?>
      </li>
<?php } ?>
<table id="example">
  <caption>Sinistros Avisados</caption>
  <thead>
  	<tr>
          <th>N.</th>
          <th>Importador</th>
          <th>Pa&iacute;s/Ci Imp.</th>
          <th>Valor do Sinistro</th>
          <th>Valor da Indeniza&ccedil;&atilde;o</th>
          <th>Data de Aviso</th>
          <th>Situa&ccedil;&atilde;o</th>
    </tr>
  </thead>
  <tbody>
        
<?php $query = "SELECT si.id, inf.contrat, c.name, inf.limPagIndeniz, inf.prMin, inf.warantyInterest, inf.percCoverage, si.date, si.status, imp.c_Coface_Imp, imp.id, si.indenizacao, si.nSinistro, imp.name FROM Inform inf JOIN Importer imp ON (imp.idInform = inf.id) JOIN Sinistro si ON (si.idImporter = imp.id) JOIN Country c ON (c.id = imp.idCountry) WHERE inf.id = $idInform AND si.status >= 2 ORDER BY si.id";
    $cur = odbc_exec($db,$query);
    $i = 0;
    while (odbc_fetch_row($cur)) {
      $i++;
      $sinistro = odbc_result($cur,1); 
      $apolice = odbc_result($cur,2);
      $pais = odbc_result($cur,3);
      $data = odbc_result($cur,8);
      $status = odbc_result($cur,9);
      $ciImp = odbc_result($cur,10);
      $idImporter = odbc_result($cur,11);
      $indeniz = odbc_result($cur,12);
      $nSinistro = odbc_result($cur,13);
      $impName = odbc_result($cur,14);
      $q = "SELECT valueAbt, idDVE FROM SinistroDetails WHERE idSinistro = $sinistro";
      $var = odbc_exec($db,$q);
      $valueTotal = 0;
      while (odbc_fetch_row($var)){ 
         $valueTotal = $valueTotal + odbc_result($var, 1);
         $dve = odbc_result($var,2);
      }
?>
        <tr>    
          <td><?php echo $nSinistro; ?></td>
          <td>
            <?php if((($status == 2) || ($status == 6)) && ($role["client"])){?>
				<?php echo ($impName); ?>
            <?php }else{  ?>
                  <a class="texto" href="<?php echo $root;?>role/sinistro/Sinistro.php?comm=detalhesSinistro&idInform=<?php echo $idInform;?>&idSinistro=<?php echo $sinistro;?>&idImporter=<?php echo $idImporter;?>#sinistro"><?php echo ($impName);?></a>
            <?php }?>
          </td>
          <td><?php echo $pais; ?> / <?php echo $ciImp; ?></td>
          <td><?php echo number_format($valueTotal,2,",","."); ?></td>
          <td><?php echo number_format($indeniz,2,",","."); ?></td>
          <td><?php echo substr($data,8,2)."/".substr($data,5,2)."/".substr($data,2,2); ?></td>
          <td>
			<?php switch($status){
            case 2 : echo "Aviso"; break;
            case 3 : echo "Sinistro"; break;
            case 4 : echo "Suspenso"; break;
            case 5 : echo "Cancelado"; break;
            case 6 : echo "N&atilde;o Aceito"; break;
            case 7 : echo "Recuperado"; break;
            case 8 : echo "Inden. Aprovada"; break;
          }
		   ?>
          </td>
          
        </tr>
<?php } // while?>
		</tbody>
</table>
<form action="<?php echo $root?>role/sinistro/Sinistro.php" method="post">
<input type="hidden" name="comm">
<input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
<?php if ($role["client"]){ ?>
<div class="barrabotoes">
    <button class="botaoagm" onclick="this.form.comm.value='voltarCliente';this.form.submit()">Voltar</button>
</div>
<?php }else{?> 
<div class="barrabotoes">
    <button class="botaoagm" onclick="this.form.comm.value='voltarFunc';this.form.submit()">Voltar</button>
</div>
<?php }?>
</form>
</div>