<!-- Alterado Hicom (Gustavo) - erro de javascript  - deixei os campos fieldfocus e formfocus
em branco pois nem sempre esse campo existe na tela seguinte, gerando um erro de javascript, 
uma vez q o modelo geral da página tentará dar foco ao objeto cujo nome está neste campo -->


<script language=javascript src="<?php echo  $root;?>scripts/utils.js"></script>
<?php
    include_once('../../../navegacao.php');
?>

<div class="conteudopagina">
 
    <li class="campo2colunas">
       <label>Segurado: </label>
         <?php echo ($namecl);  ?>
    </li>
    <div style="clear:both">&nbsp;</div>      
    <li class="campo2colunas">
       <label>Ap&oacute;lice n&deg;: </label>
         <?php echo $apolice;?>
    </li>
    
    <li class="campo2colunas">     
        <label>Vig&ecirc;ncia: </label>
        <?php echo $start. ' &agrave; '.$end;?>
    </li>
    <li class="campo2colunas">
       <label>Per&iacute;odo de Declara&ccedil;&atilde;o: </label>
         <?php echo $inicio .' &agrave; '.$fim.'('.$num." &ordf; DVE)";?>
    
    </li>


<form action="<?php echo  $root;?>role/dve/Dve.php#tabela" method=post>
    <input type="hidden" name="comm" value="modalidade">
    <input type="hidden" name=idInform value="<?php echo  $idInform;?>">
    <input type="hidden" name=idDVE value="<?php echo  $idDVE;?>">
    <input type="hidden" name=client value="<?php echo  $client;?>">
    <input type="hidden" name=fieldfocus value="">
    <input type="hidden" name=formfocus value="">

    <div style="clear:both">&nbsp;</div>
    <!-- fim alterado Hicom -->
    <li class="campo2colunas">
    <label>Escolha a Modalidade de Venda:</label>
     <select name="modalidade">
         <option value=1 <?php echo  $modalidade == 1 ? 'selected' : '';?>> &Agrave; vista, cobran&ccedil;a a prazo</option>  
         <option value=2 <?php echo  $modalidade == 2 ? 'selected' : '';?>> Via coligada</option>  
         <option value=3 <?php echo  $modalidade == 3 ? 'selected' : '';?>> Antecipado e/ou Carta de Cr&eacute;dito</option>  
      </select>
    </li>
<label>&nbsp;</label>
   <button class="botaoagm" type="button" onClick="this.form.submit()">OK</button>
</form>

<div style="clear:both">&nbsp;</div>

<form id="Form1" action="<?php echo  $root;?>role/dve/Dve.php#tabela" method=post name=formulario onSubmit="return consist(this)">
    <input type="hidden" name="comm" value="salvatotal">
    <input type="hidden" name="idInform" value="<?php echo  $idInform;?>">
    <input type="hidden" name="idDVE" value="<?php echo  $idDVE;?>">
    <input type="hidden" name="client" value="<?php echo  $client;?>">
    <input type="hidden" name="modalidade" value="<?php echo  $modalidade;?>">
    <input type="hidden" name="viewflag" value=1>
    <input type="hidden" name="primeira_tela" value="">

<label>Total das vendas com pagamento antecipado e/ou carta de cr&eacute;dito confirmada</label>

<li class="campo2colunas">
<label>Total Valor Embarcado</label>
<input type=text size=40 onBlur="if(this.value != '') { checkDecimals(this, this.value) }" name="totalEmbarcado" value="<?php echo  $total2 > 0 ? number_format($total2, 2, ',', '.') : '';?>"></td>
</li>

<div style="clear:both">&nbsp;</div>
<div class="barrabotoes">
    <button class="botaovgm" type="button" onClick="<?php echo  $role['dve'] ? '' : 'this.form.client.value=1;';?>this.form.viewflag.value=0;this.form.primeira_tela.value=1;this.form.comm.value='view';this.form.submit()">Voltar</button>
    <button class="botaoagm" type="button" onClick="this.form.submit()">Incluir</button>
    <button class="botaoagm" type="button" onClick="this.form.totalEmbarcado.value='';this.form.totalEmbarcado.focus()">Limpar</button>
    <!--<button class="botaoagm" type="button" onClick="if(confirm('Atenção. Ao concluir o período não será mais possível incluir novos faturamentos.\nTem certeza que deseja concluí-lo?')){ this.form.comm.value='view';this.form.submit(); }">Concluir</button>-->
<div class="barrabotoes">
</form>

<?php if($msg){  ?>
        <label><?php echo  $msg;?></label>
<?php } ?>

<script language=javascript>
document.Form1.totalEmbarcado.focus();

function consist(f){
  if(f.totalEmbarcado.value == '' || numVal(f.totalEmbarcado.value) == 0){
    verErro("Favor preencher o valor total embarcado");
    f.totalEmbarcado.value = '';
    f.totalEmbarcado.focus();
    return false;
  }
  return true;
}
</script>
</div>