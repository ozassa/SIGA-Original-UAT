<?php  // Alterado Hicom (Gustavo) - 21/12/04 - adicionei botões Reenviar para Oferta
// e Reenviar para Tarifação

$proposta = "$root". "download/$key". "Prop.pdf";
$proposta_alterada = "$root". "download/$key". "Prop_alterada.pdf";
$parcela = "$root". "download/$key". "Parcela.pdf";

?>
<?php include_once('../../../navegacao.php'); ?>
<div class="conteudopagina">
<ul>
	<li><a href="<?php   echo $proposta;?>" target=_blank>Ver Proposta Original</a></li>
	
	<?php  
		if(file_exists($proposta_alterada)){  ?>
			<li><a href="<?php echo $proposta_alterada; ?>" target=_blank>Ver Proposta Alterada</a></li>
	<?php }   ?>
  	  
	<li><a href="<?php   echo $parcela;?>" target=_blank>Ver Fatura</a></li>
</ul>
<form action="<?php   echo $root;?>role/searchClient/ListClient.php" method="post">
	<input type="hidden" name="comm" value="view" />
	<input type="hidden" name="idInform" value="<?php   echo $idInform;?>" />
	<input type="hidden" name="idNotification" value="1" />
	<div class="barrabotoes">
		<button class="botaoagg" type="submit">Voltar</button>
		
		<?php  if($state == 6){ ?>
		<input type="hidden" name="key" value="<?php   echo $key;?>" />
		
		<button class="botaoagg" onClick="re_send(this.form, 1)">Reenviar Por Email</button>
	
		<?php  if ($role["executive"] || $role["credit"]) {
				$cur = odbc_exec($db, "SELECT dateFinanc, dateBack, pgOk, mailOk FROM Inform WHERE id = $idInform");
				if(odbc_fetch_row($cur)){
					if (!odbc_result($cur, "dateFinanc") && !odbc_result($cur, "dateBack") && !odbc_result($cur, "pgOk") && !odbc_result($cur, "mailOk")) {
		?>
		
		<button class="botaoagg" onClick="re_oferta(this.form)">Reenviar para Oferta</button>
		<button class="botaoagg" onClick="re_tarifacao(this.form)">Reenviar para Tarifação</button>
	
		<?php  }
				}
			}
		}
		?>
		<button class="botaoagg" onClick="re_estudo(this.form)">Enviar para reestudo</button>
		<button class="botaoagg" onClick="re_send(this.form, 0)">Cancelar Informe</button>
	</div>
</form>
</div>
<script language=javascript>
function re_send(f, email){
  f.action = '<?php   echo  $root;?>role/executive/Executive.php';
  if(email){
    f.comm.value = 'reenviarEmail';
    f.submit();
  }else{
    if(confirm('Isso cancelará informe. Continuar?')){
      f.comm.value = 'reenviar';
      f.submit();
    }
  }

}

function re_estudo(f){
  f.action = '<?php   echo  $root;?>role/executive/Executive.php';
  f.comm.value = 'reestudo';
  if(confirm('Isso cancelará a proposta atual. Continuar?')){
    f.submit();
  }
}

function re_oferta(f){
  f.action = '<?php   echo  $root;?>role/executive/Executive.php';
  f.comm.value = 're_oferta';
  if(confirm('Isso reenviará o Informe para Oferta. Continuar?')){
    f.submit();
  }
}
function re_tarifacao(f){
  f.action = '<?php   echo  $root;?>role/executive/Executive.php';
  f.comm.value = 're_tarifacao';
  if(confirm('Isso reenviará o Informe para Tarifação. Continuar?')){
    f.submit();
  }
}


</script>

