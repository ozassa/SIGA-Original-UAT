<?php /*
	Criado por Tiago V N - Elumini - 25/08/2006
*/
?>
<SCRIPT language="javascript">

	function enviaForm(){
		 if (document.all.arquivo.value==''){
		 	verErro('Selecione um arquivo para enviar sua DVE\'s !!!');
	     }else{
			document.all.comm.value='dImpDve';
			form.submit()
         }				
	}
	
	function env_volta(){
  		document.all.comm.value='view';
  		form.submit()
	}
</SCRIPT>
<?php // Fim aleterado Hicom (Gustavo)
?>

<a name=tabela></a>
<script language=javascript src="<?php echo  $root;?>scripts/utils.js"></script>
<script language=javascript src="<?php echo  $root;?>scripts/calendario.js"></script>
<?php if($client){
  echo "<center><h3>$title</h3></center>";
}else{
  require_once("../../../../site/includes/sbce.css");
}

$pode_enviar = pode_enviar();

echo "Segurado: $name<br>";
echo "Apólice n°: $apolice<br>";
echo "Vigência: $start à $end<br>";
echo "Período de Declaração: $inicio à $fim ($num". "ª DVE)<br>";

/*
if (!$user->hasRole('client')) {
echo "Data envio Dve: $sentDate<br>";
}
*/

echo "<br>";
?>
<form name="form" action="<?php echo  $root;?>role/dve/Dve.php#tabela" method="post" enctype="multipart/form-data">
<?php echo "<input type='hidden' name='segurado' value='$name'>";
echo "<input type='hidden' name='apolice' value='$apolice'>";
echo "<input type='hidden' name='ivigencia' value='$start'>";
echo "<input type='hidden' name='fvigencia' value='$end'>";
echo "<input type='hidden' name='inicio' value='$inicio'>";
echo "<input type='hidden' name='fim' value='$fim'>";
echo "<input type='hidden' name='numDVE' value='$num'>";
echo "<input type='hidden' name='num' value='$num'>";
echo "<input type='hidden' name='comm' value=''>";
echo "<input type='hidden' name='client' value='1'>";
echo "<input type='hidden' name='idInform' value='$idInform'>";
echo "<input type='hidden' name='idDVE' value='$idDVE'>";
echo "<input type='hidden' name='primeira_tela' value='1'>";
?>
<table border="0" cellspacing="3" cellpadding="1" align="center">
  <tr>
      <td>&nbsp;</td>
  <tr>
  <tr>
      <td><font style="font-size:16px; font-family:Arial;"> Selecione o arquivo para importação das DVE's</font></td>
  </tr>
  <tr>
      <td>&nbsp;</td>
  <tr>
  <tr>
       <td align="center"><input name="arquivo" type="file" style="width: 300px;" class="caixa" accept="text/plain"></td>
  </tr>
  <tr>
      <td>&nbsp;</td>
  <tr>
  <tr>
       <td align="center">
	   	<input type="button" onClick="env_volta()" value="Voltar" class="sair">	   
		<input type="button" class="servicos" value=" Enviar Arquivo " name="enviar" onClick="enviaForm()">	   	
	   </td>
  </tr>  
</table>
</form>
<?php if($erro){
?>
<table align=center><tr class=bgCinza><td><?php echo  $msg;?></td></tr></table>
<?php }
?>