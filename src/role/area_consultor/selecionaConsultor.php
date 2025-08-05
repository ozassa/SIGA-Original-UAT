<script language="javascript">

function desabilita()
{
   document.getElementById("Consultor").disabled = false;
}
function habilita()
{
   document.getElementById("Consultor").disabled = true;
}

function verificaForm()
{
   if(!document.form1.concordo.checked)
   {
    verErro('Você deve concordar com os termos para prosseguir');
    return(0);
   }

   if( (document.form1.concorda2.checked) || ( (!document.form1.concorda.checked) && (!document.form1.concorda2.checked)) )
   {
    verErro('Você deve selecionar um consultor para prosseguir');
    return(0);
   }

   document.form1.submit();

}


function verificaForm2()
{

   if(!document.form1.concorda2.checked)
   {
    verErro('Você deve responder se deseja ou não selecionar um consultor para prosseguir');
    return(0);
   }

   document.form1.submit();

}

</script>

<br><br>

<!-- Tela em que o cliente escolhe o consultor desejado -->
<!-- Criado por Michel Saddock 12/09/2006 -->

<form name="form1" action="<?php echo $root;?>role/area_consultor/listConsultor.php" method="post">
<input type="hidden" name="comm" value="AdicionaConsultor">
<input type="hidden" name="cadastro" value="cadastrar">
<input type="hidden" name="idInform" value="<?php echo $idInform;?>">

<input type="hidden" name="status" value="<?php
if (($status)=="novo")
{
echo "novo";
}
else
{
echo "cliente";
}
?>">

<fieldset><legend>Deseja Selecionar um Consultor ?</legend>
&nbsp;&nbsp; <br><br>
&nbsp;&nbsp;<input type="radio" name="concorda" value="0" onClick="JavaScript: document.form1.concorda2.checked=false"  onFocus="javascript:desabilita()">&nbsp;Sim
&nbsp;&nbsp;<input type="radio" name="concorda2" value="1" onClick="JavaScript: document.form1.concorda.checked=false" onFocus="javascript:habilita()">&nbsp;Não
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<font class="texto">Selecione o consultor desejado:</font>
<select name="idConsultor" id="Consultor" class="texto"  disabled>
<option value="0">Selecione</option>

<?php
    $query = "SELECT * FROM consultor where ativo='1'";

    $cur = odbc_exec($db, $query);

    while (odbc_fetch_row($cur))
    {
      $idconsultor = odbc_result($cur,'idconsultor');
      $contato = odbc_result($cur, 'contato');
?>

<option value="<?php echo $idconsultor;?>"> <?php echo $contato;?> </option>

<?php
  } // Fecha while
?>
</select>

<br><br><br><br><br><br>
</fieldset>


<br><br>
<center>
Atenção, leia o informativo abaixo:<br><br>
<textarea name="aviso" cols= "45" rows= "5" id="textarea" class="texto">
“Estou ciente e de pleno acordo de que ao nomear um consultor estou dando pleno e total acesso aos dados constantes de minha apólice de seguro de crédito à exportação e poderes de, como titular fosse, declarar, consultar e proceder com quaisquer rotinas e funções existentes no site da SBCE visando o fiel gerenciamento de minha garantia de seguro”
</textarea>
<br><input type="checkbox" name="concordo" value="sim"><font class="texto">Li e concordo com os termos acima</font>

<br><br><br><br>
<input class="servicos" name="botao" type="button" value="Cadastrar consultor" onClick="this.form.comm.value='AdicionaConsultor';javascript:verificaForm()">
<?php
if (($status)=="novo")
{
?>
<input class="servicos" name="botao" type="button" value="Prosseguir sem consultor" onClick="this.form.comm.value='AdicionaConsultor';this.form.cadastro.value='continuar';javascript:verificaForm2()">
<?php
}
else
{
?>
<input class="servicos" name="botao" type="button" value="Voltar" onClick="javascript:history.go(-1)">
<?php
}
?>




</center>
</form>

<script>
  /*
  //####### ini ####### adicionado por eliel vieira - elumini - em 25/09/2007
  */
  document.form1.submit();
</script>
