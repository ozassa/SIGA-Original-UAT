<?php
    require_once("../../../navegacao.php");
?>

<div class="conteudopagina">

<?php

if(! function_exists('getValidityDate')){
function getValidityDate($idBuyer){
  global $db;
  $x = odbc_exec($db, "select state, stateDate from ChangeCredit where idImporter=$idBuyer order by id desc");
  if(odbc_fetch_row($x)){
    $state = odbc_result($x, 1);
    if($state == 1){
      return getStrDate(substr(odbc_result($x, 2), 0, 10));
    }
  }
  return date("d/m/Y");
}
}

if(! function_exists('getStrDate')){
function getStrDate($str){
  $row = explode('-', $str);
  $ret = $row[2]. "/". $row[1] ."/". $row[0];
  if ($ret == '//')
    return '';
  return $ret;
}
}


$idInform = $field->getField ("idInform");

$strSQL = "SELECT DISTINCT Importer.name,  Country.code, Country.name, Importer.limCredit
	       FROM Country
           JOIN Importer ON Country.id = Importer.idCountry
	       WHERE (Importer.idInform = $idInform) 
		   AND (Importer.state = 3 OR Importer.state=4)
	       Order by Importer.name";

$Importers = odbc_exec ($db, $strSQL);

//print $strSQL;

$inf = odbc_exec ($db, "SELECT name, contrat, state, limPagIndeniz, prMTotal, percCoverage, currency FROM Inform WHERE id = $idInform");

if (odbc_fetch_row ($inf)) {
  $name    = odbc_result ($inf, 1);
  $contrat = odbc_result ($inf, 2);
  $state   = odbc_result ($inf, 3);

  $hcx_state = odbc_result ($inf, 3);
  $hcx_limPagIndeniz = odbc_result($inf, 4);
  $hcx_prMTotal = odbc_result($inf, 5);
  $hcx_percCoverage = odbc_result($inf, 6);

  $nMoeda = odbc_result($inf, 7);
  
  if ($nMoeda == "1") {
     $extMoeda = "R\$";
  }else if ($nMoeda == "2") {
     $extMoeda = "US\$";
  }else{
     $extMoeda = "&euro;";
  }
   ?>
   <ul>
   <li class="campo2colunas">
  <label>Segurado: </label>
     <?php echo $name;?>
   </li>
   <li class="campo2colunas">  
  <label>Ci Segurado: </label><?php echo $contrat; ?>
  </li>
<?php
} else {
  ?><li class="campo2colunas"><label>ERRO: Segurado n&atilde;o localizado</label></li>
<?php
}

?>
</ul>
<div style="clear:both">&nbsp;</div>

         <table summary="Submitted table designs" id="example">
               <thead>
                   <th scope="col">Comprador</th>
                   <th scope="col">Pa&iacute;s</th>
                   <th scope="col" style="text-align:right">Limite de Cr&eacute;dito Solicitado <?php  echo $extMoeda;?></th>
                  
              </thead>
             <tbody>
   
				<?php  $i = 0;
                $ii = 0;
                while(odbc_fetch_row($Importers)){
                
                  $nameImporter        = odbc_result($Importers, 1);
                  $countryCode         = odbc_result($Importers, 2);
                  $countryName         = odbc_result($Importers, 3);
                  $creditsolicitado    = number_format(odbc_result($Importers, 4), 0 , ",", ".");
				 
				  if ($i % 2 != 0) 
					  $odd = 'class="odd"';
				  else
					  $odd = "";
							
                  $i++;
                   
                ?>
                
                        <tr <?php  echo $odd;?>>
                        <td><?php  echo ($nameImporter);?></td>
                        <td><?php  echo ($countryName);?></td>
                        <td style="text-align:right" width="270"><?php  echo number_format($creditsolicitado,2,',','.');?></td>
                     </tr>
                <?php  } // while
                ?>
              </tbody>
           </table>

        <form action="<?php  echo $root;?>role/client/Client.php" method="get">
          <input type="hidden" name="comm" value="VoltarCliente">
          <input type="hidden" name="idInform" value="<?php  echo $idInform;?>">
          
          <div class="barrabotoes">
          <button class="botaovgm" type="button"  onClick="this.form.submit();">Voltar</button>
          </div>
        </form>
        
        <label><font size=2 color=red><?php  echo $msg;?></font></label>

</div>
