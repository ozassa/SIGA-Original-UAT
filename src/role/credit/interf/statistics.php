<?php  if(!$act){
?>
<script>
	function mostraApCed()
	{
	
		var linha = document.getElementById('exibeApCed').style.display
		
		if(linha =="none" )
		{
			document.getElementById('exibeApCed').style.display = "inline";
		}else
		{
			document.getElementById('exibeApCed').style.display = "none";
		}
	}
</script>
<?php require_once("../../../navegacao.php");?>

<div class="conteudopagina">

		<li><a class="textoBold" href="javascript:mostraApCed();void(0)">Ap&oacute;lices Cedidas</a>

		<div style="display:none" id="exibeApCed">
			<br>
				&raquo;<a class="textoBold" href="../credit/Credit.php?comm=statistics&act=apoliceBB">Ap&oacute;lices Cedidas Banco do Brasil</a>
			<br>
				&raquo;<a class="textoBold" href="../credit/Credit.php?comm=statistics&act=apoliceBP">Ap&oacute;lices Cedidas Banco Parceiros</a>
			<br>
				&raquo;<a class="textoBold" href="../credit/Credit.php?comm=statistics&act=apoliceOB">Ap&oacute;lices Cedidas Outros Bancos</a>
		</div>

<li><a class="textoBold" href="../credit/Credit.php?comm=statistics&act=prospects">Tempo de an&aacute;lise - Prospectivos</a>
<li><a class="textoBold" href="../credit/Credit.php?comm=statistics&act=month">Consulta de Prospectivos por m&ecirc;s</a>
<li><a class="textoBold" href="../credit/Credit.php?comm=statistics&act=country">Tempo de an&aacute;lise - Segurados</a>
<?php  if ($role["policy"]) {
?>
<li><a class="textoBold" href="../credit/Credit.php?comm=statistics&act=relProducao">Relat&oacute;rio de Produ&ccedil;&atilde;o</a>
<?php  }
?>
<li><a class="textoBold" href="../credit/Credit.php?comm=statistics&act=relBonus">Relat&oacute;rio de B&ocirc;nus</a>
<li><a class="textoBold" href="../credit/Credit.php?comm=statistics&act=rel_apolice">Relat&oacute;rio de Ap&oacute;lice</a>
<li><a class="textoBold" href="../credit/Credit.php?comm=statistics&act=relVMI">Relat&oacute;rio de VMI</a>
</ul>
<?php  }else if($act == 'prospects'){
  require_once("../credit/prospects.php");
}else if($act == 'country'){
  require_once("../credit/country.php");
}else if($act == 'month'){
  require_once("../credit/month.php");
}else if ($act == 'relProducao'){
  require_once("../credit/viewRelProducao.php");
}else if ($act == 'relBonus') {
  require_once("../credit/interf/viewRelBonus.php");
}else if ($act == 'rel_apolice'){
  require_once("../credit/interf/view_rel_apolice_redirect.php");
}else if ($act == 'relVMI') {
  require_once("../credit/interf/view_rel_vmi.php");
}else if($act == 'apoliceBB'){
  require_once("../credit/interf/apolices_cedidas_BB.php");
}else if($act == 'apoliceBP'){
  require_once("../credit/interf/apolices_cedidas_BP.php");
}else if($act == 'apoliceOB'){
  require_once("../credit/interf/apolices_cedidas_OB.php");
}


/*
<li><a class="textoBold" href="#" onClick="javascript:window.open('../credit/interf/view_rel_apolice_redirect.php','view_rel_apolice','scrollbars=yes,status=no,width=790,height=590,left=20,top=10,resizable=no');">Relatório de Apólice</a>
*/

?>
</div>