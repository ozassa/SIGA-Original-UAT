  <?php
  
  include_once('../../../navegacao.php'); 
  
  $cur = odbc_exec($db,
	"SELECT Importer.name, Country.name, Importer.tel, Importer.contact,
		Importer.emailContato, Inform.name
	FROM Importer, Inform, Country
	WHERE Importer.id = $idBuyer AND
 	      Importer.idInform = Inform.id AND
	      Importer.idCountry = Country.id ");

    while (odbc_fetch_row($cur)) {


  


?>

<!-- CONTE�DO P�GINA - IN�CIO -->
<div class="conteudopagina">
<TABLE border="0" class="tabela01">
  <thead>
      <tr>
         <th colspan="2">&nbsp;</th>
      </tr>
  </thead>
  <TBODY>
 
  <tr class="odd">
    <td>Segurado:</td>
    <td><?php echo odbc_result($cur, 6);?></Td>
  </tr>
  
  <tr class="odd">
    <td><font color="#4169e1">Raz�o:</font></td>
    <td><?php echo  odbc_result($cur,1);?></td>
  </tr>
  <tr class="odd">
    <td><font color="#4169e1">Pa�s:</font></td>
    <td><?php echo  odbc_result($cur,2);?></td>
  </tr>
  <tr class="odd">
    <td><font color="#4169e1">Tel:</font></td>
    <td><?php echo  odbc_result($cur,3);?></td>
  </tr>
  <tr class="odd">
    <td><font color="#4169e1">Contato:</font></td>
    <td><?php echo  odbc_result($cur,4);?></td>
  </tr>
  <tr class="odd">
    <td><font color="#4169e1">E-mail:</font></td>
    <td><?php echo  odbc_result($cur,5);?></td>
  </tr>
  <tr class="odd">
    <td colspan="2">&nbsp;</td>
  </tr>
  <form action="<?php echo  $root;?>role/credit/credit.php" method=post name="ocultar">
        <input type="hidden" name="comm" id="comm" value="OcultarNotif">
        <input type="hidden" name="idNotification" value="<?php echo $idNotification;?>">
      <tr>
        <td align="center" colspan="2">
        
            <button type="button"  class="botaoagg"   onclick="if(confirm('Deseja ocultar a notifica��o?')){ document.ocultar.submit(); }" >Ocultar Notifica��o</button>
            <button type="button"  class="botaovgm" onclick="document.ocultar.comm.value='notif';document.ocultar.submit()">Voltar</button>
        </td>
      </tr>
  </form>
  </TBODY>
</table>
   <?php  }
   ?>
</div>