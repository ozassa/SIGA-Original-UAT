<p>

<FORM action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post">
<select size="1" name="comm">
    <option value="generalInformation">Informações Gerais</option> *
    <option value="volVendExt">Distribuição de Vendas por Tipo de Pagamento</option>    *
    <option value="segVendExt">Segmentação de Previsão Vendas Externas</option> *
    <option value="prevFinanc">Previsão de Financiamento</option> *
    <option value="buyers">Principais Compradores</option> *
    <option value="lost">Análise de Perdas</option> *
    <option value="simul">Simulação de Prêmio</option> *
</select>
    <input type=hidden name="idInform" value="<?php echo $idInform; ?>">
    <input type="submit" value="OK">
</form>

</P>

<form>
<input type=hidden name=idInform value="<?php echo $field->getField("idInform"); ?>">
<input type=hidden name=idNotification value="<?php echo $field->getField("idNotification"); ?>">
<input type=hidden name=comm value="simul">
<P><input type=button value="Retornar" onClick="this.form.comm.value='open';this.form.submit()"> <INPUT type=submit value="Avançar"> </P>

</form>
