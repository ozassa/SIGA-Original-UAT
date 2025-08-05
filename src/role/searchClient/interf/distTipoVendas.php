<?php

include_once('../../../navegacao.php'); 

	    $queryX = "select i_Moeda, b.Nome, b.Sigla, a.currency from Moeda b inner join Inform a on a.currency = b.i_Moeda where a.id = $idInform";
    $curY = odbc_exec($db, $queryX);
    $nMoeda = odbc_result($curY, "i_Moeda");
	
	 if ($nMoeda == "1") {
	     $extMoeda = "R\$";
     }else if ($nMoeda == "2") {
         $extMoeda = "US\$";
     }else{
         $extMoeda = "&euro;";
    }


    $qry = "Select Inf.i_Produto, Inf.id, VTC.i_Venda_Canal, VTC.Tipo_Venda, VTC.Descricao, IsNull(IVTC.v_Valor, 0) as v_Valor
			From Inform Inf
			Inner Join Venda_Tipo_Canal VTC on VTC.i_Produto = Inf.i_Produto
			Left Join Inform_Venda_Tipo_Canal IVTC On IVTC.i_Venda_Canal = VTC.i_Venda_Canal And IVTC.i_Inform = Inf.id
			Where Inf.id = ".$idInform." And (VTC.Situacao = 0 Or IVTC.v_Valor Is Not Null)
			Order By VTC.i_Venda_Canal"; 
    $cur = odbc_exec($db,$qry);
	



     
?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">


<script language="JavaScript" src="<?php echo $root; ?>scripts/utils.js" type="text/javascript"></script>

<script  language="JavaScript">
  
  

  function calc(limite) {
	  
	  
	   var i = 0;
	   var perc1 = 0;
	   var perc2 = 0;
	   var total1 = 0;	
	   var valor1 = 0;
	   var total2 = 0;  
	   var valor2 = 0;
	   var total = 0;
	  
	   
	   // Calclua o total de cliente  
	   
	   for(i= 0; i < limite; i++){	
	        total1 = total1 + numVal(document.getElementById('v_Valor'+i).value)/1;  
			
	   }
	   
	   if(total1 > 0){
	      total = total1.toFixed(2); 
	      document.getElementById('valor1').innerText = MascaraDecimal(total);
	   }else{
	      document.getElementById('valor1').innerText = '0,00';
	   }
	   
	   // Calcula o Percentual da quantidade cliente em relação ao total 
	   i =0;
	   
	   
	   
	   for(i= 0; i < limite; i++){	
	      if(numVal(document.getElementById('v_Valor'+i).value) > 0){
			  valor1 = numVal(document.getElementById('v_Valor'+i).value)/1 / total1 *100;
			  total2 = total2 + valor1;
		  }else{
			  valor1 = 0;
		  }
		  perc1  = valor1.toFixed(2);
		  document.getElementById('percentual'+i).innerText =  MascaraDecimal(perc1)+'%'; 
		  
	   }
	   
	   
	   
	   if(total2 >0){
		   perc2 = total2.toFixed(2);
	       document.getElementById('valor2').innerText =  MascaraDecimal(perc2)+'%'; 
	   }else
	       document.getElementById('valor2').innerText =  '0,00%'; 
	   //*********************************  
	   
	   i=0;
	   
	
	    
	  	   
   }
   
   function calc1(limite) {
	  
	  
	   var i = 0;
	   var perc1 = 0;
	   var perc2 = 0;
	   var total1 = 0;	
	   var valor1 = 0;
	   var total2 = 0;  
	   var valor2 = 0;
	   var total = 0;
	  
	   
	   // Calclua o total de cliente  
	   
	   for(i= 0; i < limite; i++){	
	        total1 = total1 + numVal(document.getElementById('v_Valor1'+i).value)/1;  
			
	   }
	   
	   if(total1 > 0){
	      total = total1.toFixed(2); 
	      document.getElementById('valor3').innerText = MascaraDecimal(total);
	   }else{
	      document.getElementById('valor3').innerText = '0,00';
	   }
	   
	   // Calcula o Percentual da quantidade cliente em relação ao total 
	   i =0;
	   
	   
	   
	   for(i= 0; i < limite; i++){	
	      if(numVal(document.getElementById('v_Valor1'+i).value) > 0){
			  valor1 = numVal(document.getElementById('v_Valor1'+i).value)/1 / total1 *100;
			  total2 = total2 + valor1;
		  }else{
			  valor1 = 0;
		  }
		  perc1 = valor1.toFixed(2);
		  document.getElementById('percentual1'+i).innerText =  MascaraDecimal(perc1)+'%'; 
		  
	   }
	   
	   
	   
	   if(total2 >0){
	       perc2  = total2.toFixed(2);
		   document.getElementById('valor4').innerText =  MascaraDecimal(perc2)+'%'; 
	   }else
	       document.getElementById('valor4').innerText =  '0,00%'; 
	   //*********************************  
	   
	   i=0;
	   
	
	    
	  	   
   }

</script>

    
    <label>Instruções: Considere os dados do último ano fiscal fechado</label>	
    <?php
	
	      $count1 = 0;
		  $count2 = 0;
		  $i = 0;
		  
		  while (odbc_fetch_row($cur)){ 
							   
			   if(odbc_result($cur,'Tipo_Venda') != $tipovenda){  
			        $tipovenda = odbc_result($cur,'Tipo_Venda');
			        $i++; 
			   }
			   
			   if($i == 1){ 
			      $total1 += odbc_result($cur,'v_Valor');
				  $total2 = 100;
                  $count1++;
		       }else if ($i == 2){ 
			      $total3 += odbc_result($cur,'v_Valor');
				  $total4 = 100;
				  $count2++; 
			   }
		 
		     
		  }
		  
		  
		  $cur = odbc_exec($db,$qry);
		  $i = 0;
		  $x = 0;
		  $tipovenda = "";
		  while (odbc_fetch_row($cur)){ 							   
			   if(odbc_result($cur,'Tipo_Venda') != $tipovenda){  
			        $tipovenda = odbc_result($cur,'Tipo_Venda');
					$x=0;
			           if($i == 1){ ?>
                               </tbody>
                               <tfoot>
                                   <tr>
                                     <th scope="col">Total</th>
                                     <th scope="col" style="text-align:right"><label id="valor1" style="color:#FFF"><?php echo number_format($total1,2,',','.');?></label></th>
                                     <th scope="col" style="text-align:right"><label id="valor2" style="color:#FFF"><?php echo number_format($total2,2,',','.');?></label></th>
                                    </tr>
                               </tfoot>            
						      </table>
                              
				 <?php }  ?> 
                 
                        <table summary="Submitted table designs" class="tabela01" style="width:450px; float:<?php if($i >1) echo 'right'; else echo 'left; margin:0 25px 0 0;'; ?>;">
					    			

						<?php if($i == 0){ ?>
                                 <thead>
                                   <th width="50%"><?php echo odbc_result($cur,'Tipo_Venda');?></th>
                                   <th width="25%" style="text-align:right">Valor <?php echo ($extMoeda);?></th>
                                   <th width="25%" style="text-align:right">% </th>						  
                                 </thead>
                                 <tbody>
                        <?php        
                         }else{  ?>
                                  <thead> 
                                   <th width="50%"><?php echo odbc_result($cur,'Tipo_Venda');?></th>
                                   <th width="25%" style="text-align:right">Valor <?php echo ($extMoeda);?></th>
                                   <th width="25%" style="text-align:right">% </th>
                                 </thead>
                                 </tbody> 
                   <?php }  ?> 
                <?php
					
					$i++; 
			    }
			   
			   if($i == 1){  
			   		$percent = odbc_result($cur,'v_Valor') > '0' ? (odbc_result($cur,'v_Valor') / $total1 * 100) : '0'; ?>
		         <tr class="odd">
		              <td><?php  echo odbc_result($cur,'Descricao');?></td>
		              <td><input type="hidden" name="i_Venda_Canal[]" id="i_Venda_Canal<?php echo $x;?>" value="<?php echo odbc_result($cur,'i_Venda_Canal');?>">
		                  <input style="width:120px; text-align:right" type="text" disabled="disabled" name="v_Valor[]" id="v_Valor<?php echo $x;?>" value="<?php  echo number_format(odbc_result($cur,'v_Valor'),2,',','.');?>" onBlur="javascript: checkDecimals(this, this.value); calc(<?php echo $count1;?>);"></td>
		              <td><label id="percentual<?php echo $x;?>" style="text-align:right"><?php echo number_format($percent,2,',','.'); ?></label></td>                   
		         </tr><?php
		       }else if ($i == 2){   
			   		$percent = odbc_result($cur,'v_Valor') > '0' ? (odbc_result($cur,'v_Valor') / $total3 * 100) : '0'; ?>
					   <tr class="odd">
	                        <td><?php  echo odbc_result($cur,'Descricao');?></td>
	                        <td><input type="hidden" name="i_Venda_Canal[]" id="i_Venda_Canal1<?php echo $x;?>" value="<?php echo odbc_result($cur,'i_Venda_Canal');?>">
	                            <input style="width:120px; text-align:right" type="text" disabled="disabled" name="v_Valor[]" id="v_Valor1<?php echo $x;?>" value="<?php  echo number_format(odbc_result($cur,'v_Valor'),2,',','.');?>" onBlur="javascript: checkDecimals(this, this.value); calc1(<?php echo $count2;?>);"></td>
	                        <td><label id="percentual1<?php echo $x;?>" style="text-align:right"><?php echo number_format($percent,2,',','.'); ?></label></td>                     
	                   </tr><?php
			   }
		 
		     $x++;
		 }
	  
	      if($i > 1){ ?>
                  </tbody>
                  <tfoot>
                       <tr>
                         <th scope="col">Total</th>
                         <th scope="col" style="text-align:right"><label id="valor3" style="color:#FFF"><?php echo  number_format($total3,2,',','.');?></label></th>
                         <th scope="col" style="text-align:right"><label id="valor4" style="color:#FFF"><?php echo  number_format($total4,2,',','.');?></label></th>
                        </tr>
                   </tfoot>
                
              </table>
     <?php }  ?>
   
   <div style="clear:both">&nbsp;</div>
    
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