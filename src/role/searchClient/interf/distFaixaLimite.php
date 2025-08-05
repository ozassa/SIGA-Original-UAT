<?php

include_once('../../../navegacao.php'); 

    //inclusão


$queryX = "select i_Moeda, b.Nome, b.Sigla, a.currency,a.i_Produto from Moeda b inner join Inform a on a.currency = b.i_Moeda where a.id = $idInform";
$curY = odbc_exec($db, $queryX);
$nMoeda = odbc_result($curY, "i_Moeda");
$i_Produto = odbc_result($curY, "i_Produto");
  
if ($nMoeda == "1") {
  $extMoeda = "R\$";
}else if ($nMoeda == "2") {
  $extMoeda = "US\$";
}else{
  $extMoeda = "&euro;";
}

$qry = "select 
VFLC.i_Venda_Faixa,
Case
When VFLC.v_Faixa_Inicial = 0 Then 
'At&eacute ' + Cast(M.i_Moeda as varchar)  + 'X ' + Replace(Cast(VFLC.v_Faixa_Final as varchar), '.', ',')
When VFLC.v_Faixa_Final = 0 Then 
'Acima de ' + Cast(M.i_Moeda as varchar) + 'X ' + Replace(Cast(VFLC.v_Faixa_Inicial as varchar), '.', ',')
Else 
'De ' + Cast(M.i_Moeda as varchar) + 'X ' + Replace(Cast(VFLC.v_Faixa_Inicial as varchar), '.', ',') + ' at&eacute ' + Cast(M.i_Moeda as varchar) + 'X ' + Replace(Cast(VFLC.v_Faixa_Final as varchar), '.', ',')
End as Nome_Campo,
IsNull(IVFLC.n_Clientes, 0) as n_Clientes,
VFLC.v_Faixa_Inicial,
VFLC.v_Faixa_Final,
IsNull(IVFLC.v_Valor, 0) as v_Valor,M.i_Moeda

From 
Venda_Faixa_Limite_Credito VFLC
Inner Join Moeda M On
M.i_Moeda = $nMoeda
Left Join Inform_Venda_Faixa_Limite_Credito IVFLC On
IVFLC.i_Venda_Faixa = VFLC.i_Venda_Faixa
And IVFLC.i_Inform = ".$idInform."
Order By
VFLC.i_Venda_Faixa
"; 
$cur = odbc_exec($db,$qry);


?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">


  <script language="JavaScript" src="<?php echo $root; ?>scripts/utils.js" type="text/javascript"></script>

  <script  language="JavaScript">



  function calc(limite) {


    var i = 0;
    var total1 = 0; 
    var perc1 = 0;
    var perc2 = 0;
    var perc3 = 0;
    var perc4 = 0;
    var perc5 = 0;
    var valor1 = 0;
    var total2 = 0;
    var total3 = 0;
    var total4 = 0;
    var valor2 = 0;

     // Calclua o total de cliente  
     for(i= 0; i < limite; i++){  
      total1 = total1 + numVal(document.getElementById('n_Clientes'+i).value)/1;        
     }
     
     if(total1 > 0)
      document.getElementById('valor1').innerText = total1;
     else
      document.getElementById('valor1').innerText = '0.00';

     // Calclua o Percentual da quantidade cliente em relação ao total 
     i =0;
     
     for(i= 0; i < limite; i++){  
      if(numVal(document.getElementById('n_Clientes'+i).value) > 0){
        valor1 = numVal(document.getElementById('n_Clientes'+i).value)/1 / total1 *100;
        total2 = total2 + valor1;
      }else{
        valor1 = 0;
      }
      perc1 = valor1.toFixed(2);
      document.getElementById('Result_n_Cliente'+i).innerText =  MascaraDecimal(perc1)+'%'; 

     }
     
     if(total2 >0){
      perc2 = total2.toFixed(2);
      document.getElementById('Total_Perc_n_Cliente').innerText =  MascaraDecimal(perc2)+'%'; 
     }else
     document.getElementById('Total_Perc_n_Cliente').innerText =  '0,00%'; 
     //*********************************  
     
     i=0;
     
     // Calclua o total de cliente  
     for(i= 0; i < limite; i++){  
      total3 = total3 + numVal(document.getElementById('n_Valor'+i).value)/1;        

     }
     if(total3 >0){
      perc3 = total3.toFixed(2);
      document.getElementById('Total_Contas').innerText = MascaraDecimal(perc3);
     }else
     document.getElementById('Total_Contas').innerText = '0,00';

     i =0;
     for(i= 0; i < limite; i++){
      if(numVal(document.getElementById('n_Valor'+i).value) > 0){ 
        valor2 = numVal(document.getElementById('n_Valor'+i).value)/1 / total3 *100;
        total4 = total4 + valor2;
      }else{
        valor2 = 0;  
      }
      perc4 =  valor2.toFixed(2);
      document.getElementById('Result_Valor'+i).innerText =  MascaraDecimal(perc4)+'%'; 

     }
     
     if(total4 >0){
      perc5 = total4.toFixed(2);
      document.getElementById('Total_Perc_Contas').innerText =  MascaraDecimal(perc5)+'%'; 
     }else
     document.getElementById('Total_Perc_Contas').innerText =  '0,00%'; 
    //*********************************  

  }

  </script>

  <?php if($i_Produto == 2){  ?>
  <label>Instru&ccedil;&otilde;es: Considere os dados do &uacute;ltimo ano fiscal fechado.</label>  
  <?php }else{ ?>
  <label>Instru&ccedil;&otilde;es: Considere os dados do &uacute;ltimo ano fiscal fechado. Excluir vendas &agrave; vista, antecipadas e ao Governo. Manter apenas o faturamento segur&aacute;vel.</label> 
  <?php } ?> 

    <table summary="Submitted table designs" class="tabela01">
      <thead>
        <th>Faixa de Valor</th>
        <th  style="text-align:right">N&ordm; Clientes</th>
        <th  style="text-align:right">% Cliente</th>
        <th  style="text-align:right">Contas a Receber <?php echo ($extMoeda);?></th>
        <th  style="text-align:right">% Volume</th>
      </thead>
      <tbody>
        <?php
        $count = 0;
        while (odbc_fetch_row($cur)){ 
          $count++;
        }

        $cur = odbc_exec($db,$qry);

        $x = 1;
        while (odbc_fetch_row($cur)){ 
          $total1 += odbc_result($cur,'n_Clientes');
          $total3 += odbc_result($cur,'v_Valor');
          $x++;
        }
        $menor = 0;
        $cur = odbc_exec($db,$qry);
        $i = 0;
        while (odbc_fetch_row($cur)){ 
          if($i % 2 == 0){
            $row  = 'class="odd"';
          }else{
            $row  = '';
          }
          ?>
          <tr <?php echo $row;?>>

            <td><?php 

            if($i == 0){
              echo  "At&eacute; ".$extMoeda. " ". number_format(odbc_result($cur,'v_Faixa_Final'),2,',','.');
            }else if(($i+1) < $count){
              echo "De ".$extMoeda." ". number_format(odbc_result($cur,'v_Faixa_Inicial'),2,',','.'). " at&eacute; ".$extMoeda. " ". number_format(odbc_result($cur,'v_Faixa_Final'),2,',','.');
            }else{
              echo "Acima de ".$extMoeda. " ". number_format((odbc_result($cur,'v_Faixa_Inicial') +1),2,',','.');
            }
            ?>
          </td>
          <td align="right" style="text-align:right"><input type="hidden" class="semformatacao" name="i_Venda_Faixa[]"  id="i_Venda_Faixa" value="<?php echo odbc_result($cur,'i_Venda_Faixa');?>">
            <input type="text"disabled="disabled"  class="semformatacao" style="text-align:right" name="n_Clientes[]" id="n_Clientes<?php echo $i;?>" onBlur="javascript: calc(<?php echo $count;?>);"  value="<?php echo odbc_result($cur,'n_Clientes');?>"></td>
            <td align="right" style="text-align:right">
              <label id="Result_n_Cliente<?php echo $i;?>">
                <?php echo number_format((odbc_result($cur,'n_Clientes')/$total1)*100,2,',','.').'%';?>
              </label>
            </td>
            <td align="right" style="text-align:right"><input type="text" disabled="disabled"  class="semformatacao" style="text-align:right" name="n_Valor[]" id="n_Valor<?php echo $i;?>" onBlur="javascript: checkDecimals(this, this.value); calc(<?php echo $count;?>);" value="<?php echo number_format(odbc_result($cur,'v_Valor'),2,',','.');?>"></td>
            <td align="right" style="text-align:right"><label id="Result_Valor<?php echo $i;?>"><?php echo number_format((odbc_result($cur,'v_Valor')/$total3)*100,2,',','.').'%';?></label></td>
          </tr>
          <?php
          $i++;
             // $total1 += odbc_result($cur,'n_Clientes');
          if($total1 != 0){
            $total2 += (odbc_result($cur,'n_Clientes')/ $total1)*100;
          }
             // $total3 += odbc_result($cur,'v_Valor');
          if($total3 != 0){
            $total4 += (odbc_result($cur,'v_Valor')/$total3)*100;
          }
        }
        ?>

      </tbody>
      <tfoot>
        <tr>
          <th scope="col">Total</th>
          <input type="hidden" name="inicial" id="inicial" value="" />
          <th scope="col" style="text-align:right"><label id="valor1" style="color:#FFF"><?php echo $total1;?></label></th>
          <th scope="col" style="text-align:right"><label id="Total_Perc_n_Cliente" style="color:#FFF"><?php echo number_format($total2,2,',','.');?>%</label></th>
          <th scope="col" style="text-align:right"><label id="Total_Contas" style="color:#FFF"><?php echo number_format($total3,2,',','.');?></label></th>
          <th scope="col" style="text-align:right"><label id="Total_Perc_Contas" style="color:#FFF"><?php echo number_format($total4,2,',','.');?>%</label></th>            
        </tr>
      </tfoot>
    </table>

    <form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post">
      <input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
      <input type="hidden" name="reltipo" value="informI">
      <input type="hidden" name="comm" value="segVendExt">
      <div class="barrabotoes">
        <button name="voltar_bt" onclick="javascript: this.form.comm.value='open';this.form.submit();" class="botaovgg">Voltar</button>
          <!-- <button type="submit" name="ok_bt" class="botaoagm">OK</button>
          <button name="pdf" class="botaoagm" onclick="gerarPdf(this.form);">Vers&atilde;o PDF</button> -->
      </div>
    </form>


  <!-- FIM Conteudo Página -->
</div>