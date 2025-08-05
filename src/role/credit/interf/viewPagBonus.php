<?php  function TrataData($data, $tipo, $saida){

	#
	# Variavel $data é a String que contém a Data em qualquer formato
	# Variavel $tipo é que contém o tipo de formato data.
	# $tipo :
	#		1 - Brasil - No formato -> Dia/Mes/Ano ou DD/MM/YYYY
	#		2 - USA	 - No formato -> YYYY-Mes-Dia ou YYYY-MM-DD
	#
	# $saida :
	# 	    1 - Brasil
	# 	    2 - USA
	#
	# Obs
	# Esta função não funciona com timestemp no formato a seguir :
	# DD/MM/YYYY H:M:S.MS ou YYYY-MM-DD H:M:S:MS
	# Pode configurar o formato da Data

    $data = explode(" ", $data);

	if ( $tipo == 1) {
		list($dia, $mes, $ano) = explode("[/-]", $data[0]);
	}elseif ( $tipo == 2 ) {
		list($ano, $mes, $dia) = explode("[-/]", $data[0]);
	}else{
		$msg = "Erro - Formato de data não existe.";
	}

	if ($saida == 1) {
        return $dia."/".$mes."/".$ano;
	}elseif ($saida == 2){
		return $ano."-".$mes."-".$dia;
	}else{
		return 0;
	}
}

  $cur = odbc_exec($db, "SELECT   id, name, DATEADD ( MONTH ,7, endValidity ) as d_Pag_Bonus
                     FROM Inform
                     WHERE state = 11 and Ga = 1 and mModulos in (1, 2) and id = $idInform
                     order by name asc");
$ok = true;
if (odbc_fetch_row($cur)){
   $segurado = odbc_result($cur, "name");
   $d_Pag_Bonus = TrataData(odbc_result($cur, "d_Pag_Bonus"), 2, 1);
   
}else{
  $ok = false;
}

?>
<br><br><br><br>
<form name="frmBonus" action="../credit/Credit.php" method="post">
<input type="hidden" name="comm" value="notificaBonus">
<input type="hidden" name="idNotification" value="<?php echo $idNotification;?>">

<table align="center" width="60%" border="0" cellspacing="0" cellpadding="0">

   <?php  if ($ok){
   ?>
   <tr>
        <td align="right" style="width:150px;">Nome do Segurado :</td>
        <td style="width:110px;"><?php echo $segurado;?></td>
   </tr>
   <tr>
        <td align="right" style="width:150px;">Data Pagamento :</td>
        <td style="width:110px;"><font color="red"><?php echo $d_Pag_Bonus;?></font></td>
   </tr>
   <?php  }else{
   ?>
      <tr>
          <td colspan="2">Não existe pagamento de bonus</td>
      </tr>
   
   <?php  }
   ?>
   <tr>
        <td colspan="2">&nbsp;</td>
   </tr>
   <tr>
        <td colspan="2">&nbsp;</td>
   </tr>
   <tr>
        <td colspan="2" align="center"><input type="submit" name="ocultar" value="Ocultar Notificação"></td>
   </tr>
   
</table>
</form>
