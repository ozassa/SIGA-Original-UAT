<?php  /*
//####### ini ####### adicionado por eliel vieira - elumini - 28/01/2008
//
// pagina de redirecionamento para a pagina do relatorio com passagem
// do parametro de autorizacao de abertura da mesma, e com a definicao
// de exibicao em html.
// modificada para adicionar campos para filtro de pesquisa.
//
*/

//define a classe para abertura do banco de dados
//require("../../../dbOpen.php");

?>


<HTML><HEAD><TITLE>Relatório de Apólice</TITLE>
<?php  //define o estilo
  //require_once("../../../../../site/includes/sbce.css");

?>
</HEAD>
<BODY>
<form name="frm_redirect" action="view_rel_apolice.php" method="post" target="_blank" onSubmit="javascript:enviar_dados(this);">
<INPUT type="hidden" name="params" value="autorized">
<INPUT type="hidden" name="excel"  value="no">

  <TABLE align="center" width="70%" border="0" cellspacing="1" cellpadding="1" style="display:block;">

    <tr>
      <td align="center" colspan="2"><h5>Estatística - Relatório de Apólice</h5></td>
    </tr>
    <tr style="display:none;">
      <td width=40%>Nome</td>
      <td><INPUT type="text" name="edt_name" style="width:150px;" maxlength="255"></td>
    </tr>
    <tr>
      <td>Data Início de Vigência</td>
      <td><INPUT type="text" name="edt_startValidity" style="width:100px;" maxlength="10" onKeyUp="javascript:add_barras(this);"></td>
    </tr>
    <tr>
      <td>Data Fim de Vigência</td>
      <td><INPUT type="text" name="edt_endValidity" style="width:100px;" maxlength="10" onKeyUp="javascript:add_barras(this);"></td>
    </tr>
    <tr>
      <td>Prêmio Mínimo</td>
      <td><INPUT type="text" name="edt_prMin2" maxlength="25" style="width:150px;display:none;">

       <select name="edt_prMin" class="caixa" style="">
        <option value="">[TODOS]</option>
        <option value="1">Abaixo de 10.000,00</option>
        <option value="2">Entre 10.000,00 e 20.000,00</option>
        <option value="3">Entre 20.000,00 e 30.000,00</option>
        <option value="4">Entre 30.000,00 e 50.000,00</option>
        <option value="5">Entre 50.000,00 e 100.000,00</option>
        <option value="6">Acima de 100.000,00</option>
       </select>

      </td>
    </tr>
    <tr>
      <td>Cobertura PROEX</td>
      <td><INPUT type="text" name="edt_proex2" maxlength="255" style="width:150px;display:none;">

       <select name="edt_proex" class="caixa" style="">
        <option value="">[TODOS]</option>
        <option value="1">Sim</option>
        <option value="2">Não</option>
       </select>

      </td>
    </tr>
    <tr>
      <td>Cobertura Faturamento Segurável</td>
      <td><INPUT type="text" name="edt_caex2" maxlength="255" style="width:150px;display:none;">

       <select name="edt_caex" class="caixa" style="">
        <option value="">[TODOS]</option>
        <option value="1">Até 1 MM</option>
        <option value="2">Entre 1 e 5 MM</option>
        <option value="3">Entre 5 e 10 MM</option>
        <option value="4">Entre 10 e 15 MM</option>
        <option value="5">Acima de 15 MM</option>
       </select>

      </td>
    </tr>
    <tr>
      <td>Taxa de Prêmio</td>
      <td><INPUT type="text" name="edt_tx_premio2" maxlength="7" onKeyUp="javascript:tirar_virg(this);" style="width:150px;display:none;">

       <select name="edt_tx_premio" class="caixa" style="">
        <option value="">[TODOS]</option>
        <option value="1">Abaixo de 0,5%</option>
        <option value="2">Entre 0,5% e 1%</option>
        <option value="3">Entre 1% e 1,5%</option>
        <option value="4">Acima de 1,5%</option>
       </select>

      </td>
    </tr>
    <tr>
      <td>Aprovação</td>
      <td><INPUT type="text" name="edt_aprovacao2" maxlength="20" style="width:150px;display:none;">

       <select name="edt_aprovacao" class="caixa" style="">
        <option value="">[TODOS]</option>
        <option value="abaixo">Abaixo de 70%</option>
        <option value="acima">Acima de 70%</option>
       </select>

     </td>
    </tr>
    <tr>
      <td>Estado</td>
      <td>
       <INPUT type="text" name="edt_uf2" maxlength="2" style="width:150px;display:none;">

       <select name="edt_uf" class="caixa" style="/*font-family:courier;font-size:9px;*/">
        <option value="">[TODOS]</option>
<?php  $sql = " SELECT id, description, name FROM Region ORDER BY name ";

$rs = odbc_exec($db,$sql);

while (odbc_fetch_row($rs)) {
  echo "<option value='".odbc_result($rs,3)."'>".odbc_result($rs,3)." - ".odbc_result($rs, 2)."</option>";
}

?>
      </select>

      </td>
    </tr>

    <tr>
      <td colspan="2" align="center">&nbsp;</td>
    </tr>

    <tr>
      <td colspan="1" align="left"><input type="button" name="voltar" value="Voltar" class="servicos" onClick="javascript:history.go(-1);"></td>
      <td colspan="1" align="left"><input type="submit" name="enviar" value="Enviar" class="servicos"></td>
    </tr>
  </TABLE>

  <div id=MSG style="display:none;">
    <center>
      <h5><font color=blue>Por favor, aguarde. Carregando dados...</font></h5>
    </center>
  </div>

</form>
</BODY>
</HTML>

<script language="javascript">

function enviar_dados(obj) {
  //document.write('<h5>Por favor, aguarde. Carregando dados...</h5>');
  //frm_redirect.submit(true);
  //MSG.style.display = 'block';
  //window.open('../credit/view_rel_apolice.php','view_rel_apolice','scrollbars=yes,status=no,width=790,height=590,left=20,top=10,resizable=no');
}

function tirar_virg(obj) {

  str = document.frm_redirect.edt_tx_premio.value;
  achou = ver_pos(str,'.');
  if (achou<1) {
    document.frm_redirect.edt_tx_premio.value = str.replace(',','.');
    str_ult = document.frm_redirect.edt_tx_premio.value;
  } else {
      if (str.length>7) {
        document.frm_redirect.edt_tx_premio.value = str_ult;
      }
      if (str.length<8) {
        str_ult = document.frm_redirect.edt_tx_premio.value;
      }
  }

}

function ver_pos(cadeia,busca) {

   var posicao = -1;
   var n;
   var tamanho;
   var straux = cadeia + "WWWWWWWW";

   tamanho = straux.length ;

   for (n=0; n < tamanho; n++) {
     if (straux.substring(n, busca.length + n) == busca) {
       posicao = n;
       n = tamanho + 1
	 }
   }
   return posicao;

}


//adiciona barras para formatacao da data - formato: 17/08/2008
function add_barras(obj) {

  if (obj.value.length == 2) {
    obj.value = obj.value+'/';
  }
  if (obj.value.length == 5) {
    obj.value = obj.value+'/';
  }

}


</script>
