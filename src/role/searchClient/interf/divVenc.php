<?php

include_once('../../../navegacao.php'); 

    //inclusão
	
   	$idInform = $_REQUEST['idInform'];

$queryX = "SELECT i_Moeda, b.Nome, b.Sigla, a.currency 
           FROM Moeda b 
           INNER JOIN Inform a ON a.currency = b.i_Moeda 
           WHERE a.id = ?";
$stmt = odbc_prepare($db, $queryX);
odbc_execute($stmt, [$idInform]);

if ($stmt) {
    $nMoeda = odbc_result($stmt, "i_Moeda");
}

// Libera a conexão ODBC
odbc_free_result($stmt);

	
	 if ($nMoeda == "1") {
	     $extMoeda = "R\$";
     }else if ($nMoeda == "2") {
         $extMoeda = "US\$";
     }else{
         $extMoeda = "&euro;";
    }
   
   $qry = "SELECT Inf.id, DV.i_Dividas_Vencidas,
            CASE
                WHEN DV.d_Periodo_Inicial = 0 THEN 'At&eacute; ' + CAST(DV.d_Periodo_Final AS VARCHAR) + ' dias'
                WHEN DV.d_Periodo_Final = 0 THEN 'Acima de ' + CAST(DV.d_Periodo_Inicial AS VARCHAR) + ' dias'
                ELSE 'De ' + CAST(DV.d_Periodo_Inicial AS VARCHAR) + ' at&eacute; ' + CAST(DV.d_Periodo_Final AS VARCHAR) + ' dias'
            END AS Descricao,
            ISNULL(IDV.v_Valor, 0) AS v_Valor
        FROM Inform Inf
        CROSS JOIN Dividas_Vencidas DV
        LEFT JOIN Inform_Dividas_Vencidas IDV ON IDV.i_Dividas_Vencidas = DV.i_Dividas_Vencidas 
        AND IDV.i_Inform = Inf.id
        WHERE Inf.id = ? AND (DV.Situacao = 0 OR IDV.v_Valor IS NOT NULL)
        ORDER BY DV.d_Periodo_Inicial";
	

    

     
?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">


<script language="JavaScript" src="<?php echo $root; ?>scripts/utils.js" type="text/javascript"></script>

<script  language="JavaScript">
  
  

  function calc(limite) {
	  
	  
	   var i = 0;
	   var perc1 = 0;
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
	      // document.getElementById('valor2').innerText =  total2.toFixed(2)+'%'; 
	   }else{
	      // document.getElementById('valor2').innerText =  '0,00%'; 
	   }
	   //*********************************  
	   
	   i=0;
	   
	
	    
	  	   
   }
   
  

</script>


    <label>Instru&ccedil;&otilde;es: Informar posi&ccedil;&atilde;o atual de atrasos de pagamentos	</label>	
    <?php
	      $count1 = 0;
		  $count2 = 0;
		  $valida = 0;
		  $i = 0;
		 $stmt = odbc_prepare($db, $qry);
			odbc_execute($stmt, [$idInform]);

			$cur = $stmt;

			odbc_fetch_row($stmt);
		  
		  while (odbc_fetch_row($cur)){ 							   
			    if($valida != $tipovenda){  
			        $tipovenda = $valida;
					
			        $i++; 
			   }
			   
			   //if($i == 1){ 
			   //   $total1 += odbc_result($cur,'v_Valor');
			   //  $total2 = 100;
               //   $count1++;
		       //}else if ($i == 2){ 
			      $total3 += odbc_result($cur,'v_Valor');
				  $total4 = 100;
				  $count2++; 
			   //}
		 
		     
		  }
		  
		  
		 // $cur = odbc_exec($db,$qry);
		  $i = 0;
		  $x = 0;
		  $tipovenda = "";
		  $valida = 3;
		  while (odbc_fetch_row($cur)){ 							   
			   if($valida != $tipovenda){  
			        $tipovenda = $valida;
					//$x=0;
			           if($i == 1){ ?>
                               </tbody>
                               <!--
                               <tfoot>
                                   <tr>
                                     <th scope="col">Total</th>
                                     <th scope="col" style="text-align:right"><label id="valor1" style="color:#FFF"><?php echo number_format($total1,2,',','.');?></label></th>
                                     <th scope="col" style="text-align:right"><label id="valor2" style="color:#FFF"><?php echo number_format($total2,2,',','.');?></label></th>
                                    </tr>
                               </tfoot> 
                               -->           
						      </table>
                              
				 <?php }  ?> 
                 
                        <table summary="Submitted table designs" class="tabela01" style="width:450px; float:<?php if($i >1) echo 'right'; else echo 'left; margin:0 25px 0 0;'; ?>;">
					    			

				<?php  if($i < 3){ ?>
                                 <thead>
                                   <th width="50%">&nbsp;</th>
                                   <th width="25%" style="text-align:right">Valor <?php echo ($extMoeda);?></th>
                                   <th width="25%" style="text-align:right">% </th>						  
                                 </thead>
                                 <tbody>
                        <?php        
                         }else{  ?>
                                  <thead> 
                                   <th width="50%">&nbsp;</th>
                                   <th width="25%" style="text-align:right">Valor <?php echo ($extMoeda);?></th>
                                   <th width="25%" style="text-align:right">% </th>
                                 </thead>
                                 </tbody> 
                   <?php }  ?> 
                <?php
					
					$i++; 
			    }
			   
			   if($i == 1){  ?>
                   <tr class="odd">
                        <td><?php  echo odbc_result($cur,'Descricao');?></td>
                        <td>
                        	<?php  echo number_format(odbc_result($cur,'v_Valor'),2,',','.');?>
                        </td>
                        <td>
                        	<label id="percentual<?php echo $x;?>" style="text-align:right"><?php echo number_format((odbc_result($cur,'v_Valor') / $total3 * 100),2,',','.'); ?></label>
                       	</td>                   
                   </tr>
                   <?php
		     }else if ($i == 2){   ?>
				   <tr class="odd">
                        <td><?php  echo odbc_result($cur,'Descricao');?></td>
                        <td><?php  echo number_format(odbc_result($cur,'v_Valor'),2,',','.');?></td>
                        <td><label id="percentual<?php echo $x;?>" style="text-align:right"><?php echo number_format((odbc_result($cur,'v_Valor') / $total3 * 100),2,',','.'); ?></label></td>                     
                   </tr><?php
			   }
		      
			  if($x == 2){
				  $valida++; 
			  }
		 
		     $x++;
			 
		 }
	  
	      if($i > 1){ ?>
                  </tbody>
                  <tfoot>
                       <tr>
                         <th scope="col">Total</th>
                         <th scope="col" style="text-align:right"><label id="valor1" style="color:#FFF"><?php echo  number_format($total3,2,',','.');?></label></th>
                         <th scope="col" style="text-align:right"><label id="valor2" style="color:#FFF"><?php echo  number_format($total4,2,',','.');?></label></th>
                        </tr>
                   </tfoot>
                
              </table>
     <?php }  ?>
   
   <form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="post">
<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
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