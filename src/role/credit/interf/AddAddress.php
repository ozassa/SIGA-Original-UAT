<hr>
<?php  //ALTERADO HICOM 12/04/2004

$r = odbc_exec($db,
	       "select I.name, I.address, I.tel, I.city, C.name from Importer I, Country C ".
	       "where I.idCountry = C.id and I.id=$idBuyer");
if(odbc_fetch_row($r)){
  $nome     = odbc_result($r, 1);
  $endereco = odbc_result($r, 2);
  $fone     = odbc_result($r, 3);
  $cidade   = odbc_result($r, 4);
  $pais     = odbc_result($r, 5);
}




     //ALTERADO HICOM 12/04/2004
     $aux_stateExp = odbc_result(odbc_exec($db, "select state from Inform where id=" . $idInform), 1);

     if ($aux_stateExp==10)
	 {
	   $fl_default_email = "checked";
	   //HiCom: Solicitação da Andréa, sempre desmarcado.
	   $fl_default_email = "";	   
	   
	   
	 }
	 else
	 {
	   $fl_default_email = "";
	 }  
	   


?>

<table width=100% cellspacing=0 cellpadding=2 border=0 align="center"> 
    <tr>
      <td class="textoBold">Importador :</td>
      <td class="texto"><?php echo $nome;?></td>
    </tr>   
    <tr>
      <td class="textoBold">Endereço:</td>
      <td class="texto"><?php echo  $endereco;?> - <?php echo  $cidade;?> - <?php echo  $pais;?></td>
    </tr>   
    <tr>
      <td class="textoBold">Fone: </td>
      <td class="texto"><?php echo $fone;?></td>
    </tr>
</table>
<hr>
<p>&nbsp;</p>
<table width="100%" cellspacing=0 cellpadding=2 border=0 align="center"> 
<tr class="bgAzul">
  <th class="textoBold">Endereço</th>
  <th class="textoBold">CEP</th>
  <th class="textoBold">Cidade</th>
  <th class="textoBold">Fone</th>
  <th align="left">&nbsp;</th>
  <th align="left">&nbsp;</th>
</tr>

<?php  if($nao_tem){
  echo "<tr bgcolor=#a4a4a4><td align=middle colspan=6>Não há endereços adicionais para este importador</td></tr>";
}else{
  $i = 0;
  do{
    $id          = odbc_result($res, 'id');
    $address     = odbc_result($res, 'address');
    $cep         = odbc_result($res, 'cep');
    $city        = odbc_result($res, 'city');
    $tel         = odbc_result($res, 'tel');
    $state       = odbc_result($res, 'state');
    $inativeDate = ymd2dmy(odbc_result($res, 'inativeDate'));
    $i++;
    if($state == 1 || ($user->hasRole('credit') ||
		       $user->hasRole('creditInform') ||
		       $user->hasRole('creditManager'))){
      if($state == 1){
	echo "<tr". ($i % 2 == 0 ? ' bgcolor=#e9e9e9' : '').
	  "><form action=$root".
	  "role/credit/Credit.php method=post name=address$i ><td>".
	  "<input type=text size=15 name=novo_endereco value=\"$address\"></td>".
	  "<td><input type=text size=15 name=novo_cep value=\"$cep\"></td>".
	  "<td><input type=text size=15 name=nova_cidade value=\"$city\"></td>".
	  "<td><input type=text size=15 name=novo_telefone value=\"$tel\">";
      }else if($state == 2){
	echo "<tr". ($i % 2 == 0 ? ' bgcolor=#e9e9e9' : '').
	  "><form action=$root".
	  "role/credit/Credit.php method=post name=address$i >".
	  "<td class=texto align=center>$address</td>".
	  "<td class=texto align=center>$cep</td>".
	  "<td class=texto align=center>$city</td>".
	  "<td class=texto align=center>$tel";
      }
?>

<input type=hidden name=origem value=<?php echo  $origem;?>>
<input type=hidden name=idInform value=<?php echo  $idInform;?>>
<input type=hidden name=comm value="addAddress">
<input type=hidden name=idBuyer value=<?php echo  $idBuyer;?>>
<input type=hidden name=update value="1">
<input type=hidden name=id value=<?php echo  $id;?>>
<input type=hidden name=frm_env_mail>
<input type=hidden name=frm_nome value=<?php echo  $nome;?>>


<?php  echo "</td><td align=center class=texto>";
   if($state == 1){
     echo "<a href=\"javascript:window.document.address$i.submit()\" onClick=\" window.document.address$i.frm_env_mail.value = window.document.aux.frm_env_mail.checked;  \">Atualizar</a>";
   }
//       echo "<br><a href=\"$root".
// 	"role/credit/Credit.php?comm=addAddress&idBuyer=$idBuyer&origem=$origem&idInform=$idInform&remove=1&id=$id\">".
// 	"Remover</a>";
      if($user->hasRole('credit') ||
	 $user->hasRole('creditInform') ||
	 $user->hasRole('creditManager')){
	if($state == 1)
	  echo "<br><a href=\"$root".
	    "role/credit/Credit.php?comm=addAddress&idBuyer=$idBuyer&origem=$origem&idInform=$idInform&inativa=1&id=$id\"".
	    " onClick=\"return confirm('Deseja inativar este endereço?')\">".
	    "Inativar</a>";
	else if($state == 2)
	  echo "inativo deste $inativeDate";
      }
      echo "</td></form></tr>";
    }
  }while(odbc_fetch_row($res));
}

?>

</table>
<p>&nbsp;
<form name=aux>
<input type=checkbox name=frm_env_mail <?php echo  $fl_default_email;?>>Enviar e-mail de alteração de dados na atualização
</form>
</p>&nbsp;
<hr>
<form action="<?php echo  $root;?>role/credit/Credit.php" method=post>
<input type=hidden name=origem value=<?php echo  $origem;?>>
<input type=hidden name=idInform value=<?php echo  $idInform;?>>
<input type=hidden name=comm value="addAddress">
<input type=hidden name=idBuyer value=<?php echo  $idBuyer;?>>
<input type=hidden name=insert value=1>

<p>&nbsp;</p>

<table align=center border=0 width="100%" align="center">
<tr>
  <td width=90% align=center colspan=2>Inserir novo endereço:</td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <td>Endereço:</td>
  <td><input type=text size=40 name=novo_endereco></td>
</tr>
<tr>
  <td>CEP:</td>
  <td><input type=text name=novo_cep></td>
</tr>
<tr>
  <td>Cidade:</td>
  <td><input type=text name=nova_cidade></td>
</tr>
<tr>
  <td>Telefone:</td>
  <td><input type=text name=novo_telefone></td>
</tr>



<tr><td class=textoBold width=15%>e-mail:</td>
<td class=texto>
<input type=hidden name=frm_nome value=<?php echo  $nome;?>>
<input type=checkbox name=frm_env_mail <?php echo  $fl_default_email;?>>Enviar e-mail de alteração de dados na atualização
</td></tr>



<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <td align=center colspan=2><input type=button class="servicos" value="Voltar" onClick="this.form.comm.value='showBuyers'; this.form.submit()">                                   <input type=submit class="servicos" value="Inserir endereço">  </td>
</tr>
</table>
</form>

<?php  if($alert){ ?>
<script language=javascript>
verErro("Os campos de Endereço ,CEP, Cidade e Telefone  são obrigatórios");
</script>
<?php  } ?>
