<?php 

	$sql = "select * from Inform where id = $idInform";
	$cur = odbc_exec($db, $sql);
	$moeda = odbc_result($cur, "currency");
	$iSubProduto = odbc_result($cur, "i_Sub_Produto");
	if ($moeda == "2") {
	   $ext = "US$";
	}else if ($moeda == "6") {
	   $ext = "";
	}
	
	$y = odbc_exec($db, "select Ga from Inform where id = '$idInform'");
	$ga = odbc_result($y, "Ga");
	$q = "SELECT name FROM Banco WHERE id = $idBanco";
	$bc = odbc_exec ($db, $q);
	$bancoName = odbc_result($bc, 1);
	
	if(! function_exists('getStrDate')){
	  function getStrDate($str){
		$row = explode('-', $str);
		$ret = $row[2]. "/". $row[1] ."/". $row[0];
		if ($ret == '//')
		  return '';
		return $ret;
	  }
	}
?>

<?php require_once("../../../navegacao.php");?>
<div class="conteudopagina">
<form action="<?php echo $root;?>role/cessao/Cessao.php#cessao" method="post" style="width:100% !important">
	<input type=hidden name="comm" value="gravaBB">
	<input type=hidden name="idInform" value="<?php echo $idInform;?>">
	<input type=hidden name="agencia" value="<?php echo $agencia;?>">
	<input type=hidden name="idAgencia" value="<?php echo $idAgencia;?>">
	<input type=hidden name="idImporter" value="">
	<a name=cessao></a>
	
	<H3>Cesso de Direito - <?php if($idBanco != 1){ echo $bancoName; }else{ echo "Banco do Brasil";}?></H3>
	 
	<?php if(isset($msgC)){?>
	       <label><?php echo $msgC;?></label>
	
	<?php }?>
	  <br />

<?php //
	//// Buscar o idAgencia
	//
	//$query = "SELECT id FROM CDBB WHERE status = 0 AND idAgencia = $idAgencia AND idInform = $idInform";
	////echo "<pre>$query</pre>";
	//$cur = odbc_exec ($db, $query);
	//if (odbc_fetch_row($cur)) {
	//  $idCDBB = odbc_result ($cur,'id');
	//} else {
	//  $q = "INSERT INTO CDBB (idInform, idAgencia) VALUES ($idInform, $idAgencia)";
	//  $cur = odbc_exec($db, $q);
	//  $q = "SELECT max(id) FROM CDBB";
	//  $cur = odbc_exec($db, $q);
	//  $idCDBB = odbc_result($cur, 1);
	//}

	$q = "SELECT name FROM Agencia WHERE id = $idAgencia";
	$ag = odbc_exec ($db, $q);
	$agName = odbc_result($ag, 1);
?>
	 <label><?php echo $bancoName;?></label> 
			<?php if(($tipoBanco == 1) || ($tipoBanco == 2)){?>
			  <label>Agncia: <?php echo $agencia;?> - <?php echo $agName;?></label>
			<?php }else{?>
			  <label>Agncia: <?php echo $agencia;?></label>
			<?php }?>
	
	<br /><br />
	
	
	<label>Lista de Importadores da Cesso de Direito</label>
		  
	<table id="example" width="100%">
	  
	
	 <thead>
	  <tr class="bgAzul">
		<th width="5%">&nbsp;</th>
		<th>Importador</th>
		<th>Pas</th>
		<th align="center">Lim. de Crdito<br>(<?php echo $ext?> Mil)</th>
		<Th align="right" width="20%">Crdito Temporrio (<?php echo $ext?> Mil)</Th>
	  </tr>
	  </thead>
	  <tbody>
		<?php 
			$link = $root . "role/cessao/cond_esp.php?consulta=1&idInform=$idInform&agencia=$agencia&idAgencia=$idAgencia&idBanco=$idBanco&tipoBanco=$tipoBanco&idCDBB=$idCDBB&idCDParc=$idCDParc&idCDOB=$idCDOB";
		

			if($tipoBanco == 1){
			  $query = "
					SELECT imp.name AS impName, c.name AS cName, imp.id, imp.credit
				FROM Importer imp
					  JOIN Country c ON (imp.idCountry = c.id)
					  JOIN CDBBDetails cdd ON (cdd.idImporter = imp.id)
				WHERE cdd.idCDBB = $idCDBB
				ORDER BY imp.name";
			}else if($tipoBanco == 2){
			  $query = "
					SELECT imp.name AS impName, c.name AS cName, imp.id, imp.credit
				FROM Importer imp
					  JOIN Country c ON (imp.idCountry = c.id)
					  JOIN CDParcDetails cdd ON (cdd.idImporter = imp.id)
				WHERE cdd.idCDParc = $idCDParc
				ORDER BY imp.name";
			}else{
			  $query = "
					SELECT imp.name AS impName, c.name AS cName, imp.id, imp.credit
				FROM Importer imp
					  JOIN Country c ON (imp.idCountry = c.id)
					  JOIN CDOBDetails cdd ON (cdd.idImporter = imp.id)
				WHERE cdd.idCDOB = $idCDOB
				ORDER BY imp.name";
			}


	  //####### ini ####### adicionado por eliel vieira - elumini - 13/05/2008
	  //Lista de Importadores da Cesso de Direito
	  //echo $query."<br>";
	
	
	  $contR = 0;
	  $cur=odbc_exec($db,$query);
	  $i = 0;
	  while (odbc_fetch_row($cur)) {
		$i++;
		$contR++;
		$idImporter = odbc_result($cur,'id');
		// 2018/06/29 - AIP: o id do importador no  necessario no link, e CD com mais de 50 importadores ultrapassava o tamanho mximo do link
		//$link .= "&idImporterR$i=$idImporter";
		
		//acrescentado por Wagner 25/08/2008
		//esta parte  uma copia do que esta no arquivo de src\role\cessao\interf\ViewCessaoDet.php
		//finalidade fornecer na area do cliente a informao do crdito temporrio para cada importador
		$wsql = "select case when limTemp < getdate() then 0 else creditTemp end creditTemp, limTemp, credit, getdate() as hoje from ChangeCredit where id=(select max(id) from ChangeCredit where idImporter= $idImporter) ";
		//echo $wsql;
	
		  $y = odbc_exec($db, $wsql);	
		  $hc_creditTemp_imp = odbc_result($y, 1);
		  $hc_limTemp_imp = odbc_result($y, 2);
		  $hc_credit_imp = odbc_result($y, 3);
	
		  if ($hc_limTemp_imp)
		  {
					
			if($hc_creditTemp_imp > 0) 
				$hc_creditTemp_imp = number_format($hc_creditTemp_imp/1000, 0, ",", ".") . "<br>at:&nbsp;" . getStrDate(substr($hc_limTemp_imp, 0, 10));
			else
				$hc_creditTemp_imp = "0";
	
	//		 $hc_creditTemp_imp = number_format($hc_creditTemp_imp/1000, 0, ",", ".") . "<br>at:&nbsp;" . getStrDate(substr($hc_limTemp_imp, 0, 10));
			 
			 //if(getTimeStamp(getStrDate(substr($hc_limTemp_imp, 0, 10))) >= time())
			 //{
	
			 //}
			 //else
			 //{
	
			 //}
		  }
		  else
		  {
			 $hc_creditTemp_imp = number_format(0, 0, ",", ".");
		  }
		  
		  
		
	?>
	  <tr <?php echo ((($i % 2) != 0) ? " bgcolor=\"#e9e9e9\"" : "");?>>
		<td width="5%"><?php echo $i;?></td>
		<td class="texto"><?php echo odbc_result($cur,'impName');?></td>
		<td class="texto"><?php echo odbc_result($cur,'cName');?></td>
		<td class="texto" align="center"><?php echo odbc_result($cur,4)/1000;?></td>
		<td align="right"><?echo $hc_creditTemp_imp; ?></td>
	  </tr>
	
	<?php } // while
	  if ($i == 0) {
	
	?>
	  </tbody>
	  <tfoot>
	  <Tr >
		<Th align="center" colspan=5 class="bgCinza">Nenhum Importador Cadastrado</Th>
	  </tr>
	
	<?php }
	$totalR = $i;
	?>
	
	</TABLE>
	
	<button type="button" class="botaovgm" onClick="this.form.comm.value='cessao';this.form.submit()">Voltar</button>
	
	<?php if($status == 1 || $status == 2){
				   /* if ($tipoBanco == 1){ */
				/*      if ($ga == 1) {
				?>
						 &nbsp;<INPUT class=servicos type=button value="Imprimir" onClick="javascript: verErro('Favor entrar em contato com o Departamento de Crdito.\n\nTelefones: 21 2510.5024 / 21 2510.5042.')">
				<?php }else{
				?>
						 &nbsp;<INPUT class=servicos type=button value="Imprimir" onClick="imprime(this.form)">
				<?php } */
				   /* }else{ */
				?>
				&nbsp;<button type="button" class="botaoagm" onClick="imprime(this.form)">Imprimir</button>
	
		<?php /* } */
		}
		
	
	?>
	
	
	</form>
	
	<script language=javascript>
		function imprime(f) {

       <?php if (!in_array($iSubProduto, [11, 12, 13, 14, 15, 16])) { ?>
    
            <?php if ($iSubProduto >= 16 && $iSubProduto <= 30) { ?>

var str = '../cessao/cond_espnovo.php?consulta=1&idInform=<?php echo $idInform; ?>&agencia=<?php echo $agencia; ?>'+
'&idAgencia=<?php echo $idAgencia; ?>&idBanco=<?php echo $idBanco; ?>&tipoBanco=<?php echo $tipoBanco; ?>&idCDBB=<?php echo $idCDBB; ?>&idCDParc=<?php echo $idCDParc; ?>&idCDOB=<?php echo $idCDOB; ?>&total=<?php echo $i; ?>&totalR='+
'&idImporterR1=<?php echo ""; ?>&comm=gerapdf';

imprimeCarta()




<?php } else { ?>

  var str = '../cessao/cond_esp.php?consulta=1&idInform=<?php echo $idInform; ?>&agencia=<?php echo $agencia; ?>'+
'&idAgencia=<?php echo $idAgencia; ?>&idBanco=<?php echo $idBanco; ?>&tipoBanco=<?php echo $tipoBanco; ?>&idCDBB=<?php echo $idCDBB; ?>&idCDParc=<?php echo $idCDParc; ?>&idCDOB=<?php echo $idCDOB; ?>&total=<?php echo $i; ?>&totalR='+
'&idImporterR1=<?php echo ""; ?>&comm=gerapdf';

<?php } ?>
    
    <?php } else { ?>

      var   str = '../cessao/cond_esp_banco_brasil.php?consulta=1&idInform=<?php echo $idInform; ?>&agencia=<?php echo $agencia; ?>'+
            '&idAgencia=<?php echo $idAgencia; ?>&idBanco=<?php echo $idBanco; ?>&tipoBanco=<?php echo $tipoBanco; ?>&idCDBB=<?php echo $idCDBB; ?>&idCDParc=<?php echo $idCDParc; ?>&idCDOB=<?php echo $idCDOB; ?>&total=<?php echo $i; ?>&totalR='+
            '&idImporterR1=<?php echo ""; ?>&comm=gerapdf';

            imprimeCarta()


    <?php } ?>
        //w = window.open(str, 'pdf_windowoficial','toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1');
        //w.moveTo(5, 5);
        //w.resizeTo(screen.availWidth - 35, screen.availHeight - 35);
        window.open(str)


}

function imprimeCarta(f) {

    console.log(<?php echo $iSubProduto ?>)


<?php if (!in_array($iSubProduto, [11, 12, 13, 14, 15, 16])) { ?>
    
            <?php if ($iSubProduto >= 16 && $iSubProduto <= 30) { ?>

var str2 = '../cessao/carta2.php?consulta=1&idInform=<?php echo $idInform; ?>&agencia=<?php echo $agencia; ?>'+
'&idAgencia=<?php echo $idAgencia; ?>&idBanco=<?php echo $idBanco; ?>&tipoBanco=<?php echo $tipoBanco; ?>&idCDBB=<?php echo $idCDBB; ?>&idCDParc=<?php echo $idCDParc; ?>&idCDOB=<?php echo $idCDOB; ?>&total=<?php echo $i; ?>&totalR='+
'&idImporterR1=<?php echo ""; ?>&comm=gerapdf';

<?php } else { ?>

<?php } ?>
    
    <?php } else { ?>

      var   str2 = '../cessao/carta.php?consulta=1&idInform=<?php echo $idInform; ?>&agencia=<?php echo $agencia; ?>'+
            '&idAgencia=<?php echo $idAgencia; ?>&idBanco=<?php echo $idBanco; ?>&tipoBanco=<?php echo $tipoBanco; ?>&idCDBB=<?php echo $idCDBB; ?>&idCDParc=<?php echo $idCDParc; ?>&idCDOB=<?php echo $idCDOB; ?>&total=<?php echo $i; ?>&totalR='+
            '&idImporterR1=<?php echo ""; ?>&comm=gerapdf';


    <?php } ?>
    	
        window.open(str2)
}



	</script>

</div>