<?php include_once('../../../navegacao.php'); ?>

<div class="conteudopagina">
<script language="JavaScript" src="<?php   echo $root;?>scripts/utils.js"></script>

<form action="<?php   echo $root;?>role/executive/Executive.php" method="post" name="f">
<input type="hidden" name="comm" value="buySubmit">
<input type="hidden" name="idInform" value="<?php   echo $idInform;?>">
<input type="hidden" name="idNotification" value="<?php   echo $idNotification;?>">
	<?php  if(!($idAnt || ($informState >= 1 && $informState <= 8))){ ?>
    <p>Aten&ccedil;&atilde;o: O limite de cr&eacute;dito &eacute; rotativo.  Deve ser calculado para cobrir a exposi&ccedil;&atilde;o m&aacute;xima do segurado em rela&ccedil;&atilde;o a cada importador.  Depende do valor e da frequ&ecirc;ncia dos embarques, assim como do prazo de pagamento.  Para cadastrar a Raz&atilde;o Social do Importador, n&atilde;o utilize ap&oacute;strofo ( ' ).  Em caso de d&uacute;vida, entre em contato conosco: <ul><li>S&atilde;o Paulo, ligue (11) 3284-3132;</li> <li>Rio Grande do Sul, ligue (51) 3249-7154;</li> <li>Demais localidades, ligue (21) 2510-5027</li></ul></p>
	<?php  } ?>

<table>
	<caption>QUADRO III - <?php   echo $idAnt ? '' : 'Principais';?> Compradores</caption>
  	<thead>
          <tr>
            <th colspan="7">Importadores Inclu&iacute;dos</th>
          </tr>
    </thead>
    <tbody>
	<?php  if($idAnt){ // se for renovacao
        $cc = odbc_exec($db, "update Importer set hold=0, endosso=0 WHERE idInform = $idInform");
      }
    
    
      $cur = odbc_exec($db,"select currency from Inform where id = $idInform");
      $currency = odbc_result($cur, 'currency');
      
      if ($currency == "2") {
         $moeda = "US$";
      }else{
         $moeda = "€";
      }
    
      $cur=odbc_exec(
        $db,
        "SELECT imp.name, address, risk, city, c.name, tel, prevExp12, limCredit, numShip12,
         periodicity, przPag, imp.id, imp.hold, cep, fax, contact, relation, seasonal
         FROM Importer imp JOIN Country c ON (idCountry = c.id)
         WHERE idInform = $idInform and imp.state <> 9 AND
         imp.id not in (select idImporter from ImporterRem)
         ORDER BY imp.id"
      );
      $i = 0;
      while (odbc_fetch_row($cur)) {
        $i++;
    ?>
    <tr>
        <td rowspan="6"><?php echo $i;?>
        <input type="hidden" name="buyId<?php   echo $i;?>" value="<?php   echo odbc_result ($cur, 'id');?>">
        <input type="checkbox" name="free<?php   echo $i;?>"<?php  if ($idAnt > 0) { ?> onFocus="blur();this.checked = true" onClick="blur();this.checked = true"<?php  } ?> value="1" <?php   echo odbc_result($cur,'hold') == 0 ? ' checked' : '';?>></td>
        <td>Raz&atilde;o</td><td colspan="2"><?php   echo odbc_result($cur,1);?></td>
        <td>Endere&ccedil;o</td><td colspan="2"><?php   echo odbc_result($cur,2);?></td>
    </tr>
    <tr>
        <td>Cidade</td><td><?php   echo odbc_result($cur,4);?></td>
        <td>CEP</td><td><?php   echo odbc_result($cur,14);?></td>
        <td>Pa&iacute;s</td><td><?php   echo odbc_result($cur,5);?></td>
    </tr>
    <tr>
        <td>Telefone</td><td><?php   echo odbc_result($cur,6);?></td>
        <td>Fax</td><td><?php   echo odbc_result($cur,15);?></td>
    </tr>
    <tr>
        <td>Contato</td><td><?php   echo odbc_result($cur,16);?></td>
        <td>Vendas Sazonais</td><td><?php   echo odbc_result($cur,18) ? "Sim" : "N&atilde;o";?></td>
        <td>Rela&ccedil;&atilde;o Comercial</td><td> desde o ano de <?php   echo odbc_result($cur,17);?></td>
    </tr>
    <tr>
        <td>Volume <?php   echo $moeda?></td><td><?php echo number_format(odbc_result($cur,7),2,",",".");?></td>
        <td>N&ordm; Emb./Ano:</td><td><?php   echo odbc_result($cur,9);?></td>
        <td>Exp. M&aacute;x. <?php   echo $moeda;?> (Mil)</td><td><input type="text" name="limCredit<?php   echo $i;?>" value="<?php   echo odbc_result($cur,8)/1000;?>" onBlur="checkDecimalsMil(this, this.value)" class="semformatacao" size="5"></td>
    </tr>
    <tr>
        <td>Per/Emb(dias)</td><td><?php   echo odbc_result($cur,10);?></td>
        <td>Prz./Pag.(dias)</td><td colspan="3"><?php   echo odbc_result($cur,11);?></td>
      </tr>
<?php  }
  if ($i == 0) {
?>
  <tr>
    <td colspan="10">Nenhum importador cadastrado</td>
  </tr>
<?php  }
?>
  </tbody>
</table>
<?php  if ($msg != "") {?>
  <p><?php   echo $msg;?></p>
<?php  } ?>
<div class="barrabotoes">
<button name="inicial"  onClick="this.form.comm.value='open';this.form.submit()" class="botaoagg">Tela Inicial</button>
<button name="anterior" onClick="this.form.comm.value='anterior';this.form.submit()" class="botaovgg">Tela Anterior</button>
<button name="proxima"  type="submit" class="botaoagg">Pr&oacute;xima Tela</button>
</div>
</form>
</div>