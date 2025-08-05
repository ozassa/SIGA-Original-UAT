<?php

include_once('../../../navegacao.php'); 

    //inclusão
	
   $idInform = $_REQUEST['idInform'];

$queryX = "SELECT i_Moeda, b.Nome, b.Sigla, a.currency 
           FROM Moeda b 
           INNER JOIN Inform a ON a.currency = b.i_Moeda 
           WHERE a.id = ?";

$stmt = odbc_prepare($db, $queryX);
if ($stmt) {
    odbc_execute($stmt, [$idInform]);
    $curY = odbc_fetch_array($stmt);
    if ($curY) {
        $nMoeda = $curY['i_Moeda'];
    }
    odbc_free_result($stmt);
}

	
	if ($nMoeda == "1") {
		$extMoeda = "R\$";
     	}else if ($nMoeda == "2") {
      		$extMoeda = "US\$";
     	}else{
      		$extMoeda = "&euro;";
    	}   
   
   
   	$qry = "SELECT 
            DPE.i_Perda_Efetiva,
            M.Sigla AS SiglaMoeda,
            CASE
                WHEN DPE.v_Faixa_Inicial = 0 THEN 'At&eacute ' + CAST(M.Sigla AS VARCHAR) + ' ' + REPLACE(CAST(DPE.v_Faixa_Final AS VARCHAR), '.', ',')
                WHEN DPE.v_Faixa_Final = 0 THEN 'Acima de ' + CAST(M.Sigla AS VARCHAR) + ' ' + REPLACE(CAST(DPE.v_Faixa_Inicial AS VARCHAR), '.', ',')
                ELSE 'De ' + CAST(M.Sigla AS VARCHAR) + ' ' + REPLACE(CAST(DPE.v_Faixa_Inicial AS VARCHAR), '.', ',') + ' at&eacute ' + CAST(M.Sigla AS VARCHAR) + ' ' + REPLACE(CAST(DPE.v_Faixa_Final AS VARCHAR), '.', ',')
            END AS Nome_Campo,
            DPE.v_Faixa_Inicial,
            DPE.v_Faixa_Final,
            Ano1.Ano1,
            ISNULL(IDPE1.n_Clientes, 0) AS Clientes1,
            ISNULL(IDPE1.v_Valor, 0) AS Valor1,
            Ano2.Ano2,
            ISNULL(IDPE2.n_Clientes, 0) AS Clientes2,
            ISNULL(IDPE2.v_Valor, 0) AS Valor2,
            Ano3.Ano3,
            ISNULL(IDPE3.n_Clientes, 0) AS Clientes3,
            ISNULL(IDPE3.v_Valor, 0) AS Valor3,
            Ano4.Ano4,
            ISNULL(IDPE4.n_Clientes, 0) AS Clientes4,
            ISNULL(IDPE4.v_Valor, 0) AS Valor4
        FROM 
            Detalhamento_Perda_Efetiva DPE
        INNER JOIN Moeda M ON M.i_Moeda = ?
        CROSS JOIN (SELECT YEAR(GETDATE()) Ano1) Ano1
        LEFT JOIN Inform_Detalhamento_Perda_Efetiva IDPE1 ON 
            IDPE1.i_Perda_Efetiva = DPE.i_Perda_Efetiva
            AND IDPE1.Ano = Ano1.Ano1
            AND IDPE1.i_Inform = ?
        CROSS JOIN (SELECT YEAR(GETDATE()) - 1 Ano2) Ano2
        LEFT JOIN Inform_Detalhamento_Perda_Efetiva IDPE2 ON 
            IDPE2.i_Perda_Efetiva = DPE.i_Perda_Efetiva
            AND IDPE2.Ano = Ano2.Ano2
            AND IDPE2.i_Inform = ?
        CROSS JOIN (SELECT YEAR(GETDATE()) - 2 Ano3) Ano3
        LEFT JOIN Inform_Detalhamento_Perda_Efetiva IDPE3 ON 
            IDPE3.i_Perda_Efetiva = DPE.i_Perda_Efetiva
            AND IDPE3.Ano = Ano3.Ano3
            AND IDPE3.i_Inform = ?
        CROSS JOIN (SELECT YEAR(GETDATE()) - 3 Ano4) Ano4
        LEFT JOIN Inform_Detalhamento_Perda_Efetiva IDPE4 ON 
            IDPE4.i_Perda_Efetiva = DPE.i_Perda_Efetiva
            AND IDPE4.Ano = Ano4.Ano4
            AND IDPE4.i_Inform = ?
        ORDER BY DPE.v_Faixa_Inicial";

$stmt = odbc_prepare($db, $qry);
if ($stmt) {
    odbc_execute($stmt, [$nMoeda, $idInform, $idInform, $idInform, $idInform]);
    $cur = $stmt; // Mantém o resultado em $cur
        odbc_free_result($stmt);

}


  
?>
<!-- CONTEÚDO PÁGINA - INÍCIO -->
<div class="conteudopagina">


<script language="JavaScript" src="<?php echo $root; ?>scripts/utils.js" type="text/javascript"></script>

<script  language="JavaScript">
  
  

  function calc(limite) {	  
	   var count  = limite;
	   var i = 0;
	   var total1 = 0;	
	   var total2 = 0;
	   var total3 = 0;
	   var total4 = 0;
	   var total5 = 0;
	   var total6 = 0;
	   var total7 = 0;
	   var total8 = 0;
	   
	   var x1 = 0;	
	 
	  
	   for(i =0; i < count; i++ ){ 
	     
	       //alert(numVal(document.getElementById('clientes1'+i).value));
	       if(numVal(document.getElementById('clientes1'+i).value) > 0){
			   x1   =  parseFloat(eval(numVal(document.getElementById('clientes1'+i).value)));
		       total1 = total1 + x1;	
		   }
		   if(numVal(document.getElementById('valor1'+i).value) > 0){
			   x1   = parseFloat(eval(numVal(document.getElementById('valor1'+i).value)));
		      total2 = total2 + x1;
		   }
		   
		   if(numVal(document.getElementById('clientes2'+i).value) > 0){
			  x1    = parseFloat(eval(numVal(document.getElementById('clientes2'+i).value)));
		      total3 = total3 + x1;
		   
		   }if(numVal(document.getElementById('valor2'+i).value) > 0){   
		      x1    = parseFloat(eval(numVal(document.getElementById('valor2'+i).value)));
			  total4 = total4 + x1;
		   
		   }if(numVal(document.getElementById('clientes3'+i).value) > 0){  
		      x1    =  parseFloat(eval(numVal(document.getElementById('clientes3'+i).value)));
			  total5 = total5 + x1;
		   
		   }if(numVal(document.getElementById('valor3'+i).value) > 0){ 
		      x1  = parseFloat(eval(numVal(document.getElementById('valor3'+i).value)));
			  total6 = total6 + x1;
		   
		   }if(numVal(document.getElementById('clientes4'+i).value) > 0){   
		      x1 =  parseFloat(eval(numVal(document.getElementById('clientes4'+i).value)));
			  total7 = total7 + x1;
		   
		   }if(numVal(document.getElementById('valor4'+i).value) > 0){
			  x1 =  parseFloat(eval(numVal(document.getElementById('valor4'+i).value)));
			  total8 = total8 + x1;
		   }
	   } 
	   
	   document.getElementById('total1').innerText = total1;
	   document.getElementById('total2').innerText = MascaraDecimal(total2);
	   document.getElementById('total3').innerText = total3; 
	   document.getElementById('total4').innerText = MascaraDecimal(total4);
	   document.getElementById('total5').innerText = total5;  
	   document.getElementById('total6').innerText = MascaraDecimal(total6);  
	   document.getElementById('total7').innerText = total7; 
	   document.getElementById('total8').innerText = MascaraDecimal(total8); 
	   	   
   }
   
  


</script>

<?php
	
	
?>
    
    <label>Instru&ccedil;&otilde;es: Detalhar a quantidade de clientes e valores acima declarados por ano e por faixa de valor.</label>
    
    <table summary="Submitted table designs" class="tabela01">
      
               <?php
			        $count = 0;
			        while (odbc_fetch_row($cur)){ 
					    $count++;
			        }
			        
										
                   $cur = odbc_exec($db,$qry);
				   $i = 0;
                    while (odbc_fetch_row($cur)){ 
					   if($i == 0){ ?>
                       
						    <thead>
                              <tr>
                                <th width="36%">&nbsp;</th>
                                <th width="16%" colspan="2" style="text-align:center"><?php echo odbc_result($cur,'Ano1');?>
                                   <input type="hidden" name="Ano1"  id="Ano1" value="<?php echo odbc_result($cur,'Ano1');?>">
                                </th>
                                
                                <th width="16%" colspan="2" style="text-align:center"><?php echo odbc_result($cur,'Ano2');?>
                                   <input type="hidden" name="Ano2"  id="Ano2" value="<?php echo odbc_result($cur,'Ano2');?>">
                                </th>
                               
                                <th width="16%" colspan="2" style="text-align:center"><?php echo odbc_result($cur,'Ano3');?>
                                   <input type="hidden" name="Ano3"  id="Ano3" value="<?php echo odbc_result($cur,'Ano3');?>">
                                </th>
                               
                                <th width="16%" colspan="2" style="text-align:center"><?php echo odbc_result($cur,'Ano4');?>
                                   <input type="hidden" name="Ano4"  id="Ano4" value="<?php echo odbc_result($cur,'Ano4');?>">
                                </th>
                               
                              </tr>
                              <tr>
                               <th width="36%" >Faixa de Valor <?php echo odbc_result($cur,'SiglaMoeda');?></th>
                               <th  style="text-align:center" width="8%">Qtde. de Clientes</th>
                               <th  style="text-align:center" width="8%">Valor</th>
                               <th  style="text-align:center" width="8%">Qtde. de Clientes</th>
                               <th  style="text-align:center" width="8%">Valor</th>
                               <th  style="text-align:center" width="8%">Qtde. de Clientes</th>
                               <th  style="text-align:center" width="8%">Valor</th>
                               <th  style="text-align:center" width="8%">Qtde. de Clientes</th>
                               <th  style="text-align:center" width="8%">Valor</th>
                               <tr>
                           </thead>
                           <tbody>
						   
					   <?php 
					   }
					
					
                       if($i % 2 == 0){
                           $row  = 'class="odd"';
                       }else{
                           $row  = '';
                       }
               ?>
                        <tr <?php echo $row;?>>                        
                            <td>
							    <?php 
							    if($i == 0){
									 echo  "At&eacute; ".$extMoeda. " ". number_format(odbc_result($cur,'v_Faixa_Final'),2,',','.');
								}else if(($i+1) < $count){
								     echo "De ".$extMoeda." ". number_format(odbc_result($cur,'v_Faixa_Inicial'),2,',','.'). " at&eacute; ".$extMoeda. " ". number_format(odbc_result($cur,'v_Faixa_Final'),2,',','.');
								}else{
								     echo "Acima de ".$extMoeda. " ". number_format((odbc_result($cur,'v_Faixa_Inicial')),2,',','.');
					            }
								?>
							    <?php //echo odbc_result($cur,'Nome_Campo');	?>
                                <input type="hidden" name="i_Perda_Efetiva[]"  id="i_Perda_Efetiva_<?php echo $i;?>" value="<?php echo odbc_result($cur,'i_Perda_Efetiva');?>">
                            </td>
                            <td style="text-align:right">
                            	<?php echo number_format(odbc_result($cur,'Clientes1'),0,',','.');	?>
                            </td>
                            <td style="text-align:right">
                            	<?php echo number_format(odbc_result($cur,'Valor1'),2,',','.');  ?>
                            </td>
                            <td style="text-align:right">
                            	<?php echo number_format(odbc_result($cur,'Clientes2'),0,',','.');	?>
                            </td>
                            <td style="text-align:right">
                            	<?php echo number_format(odbc_result($cur,'Valor2'),2,',','.');	?>
                            </td>
                            <td style="text-align:right">
                            	<?php echo number_format(odbc_result($cur,'Clientes3'),0,',','.');	?>
                            </td>
                            <td style="text-align:right">
                            	<?php echo number_format(odbc_result($cur,'Valor3'),2,',','.');	?>
                            </td>
                            <td style="text-align:right">
                            	<?php echo number_format(odbc_result($cur,'Clientes4'),0,',','.');	?>
                            </td>
                            <td style="text-align:right">
                            	<?php echo number_format(odbc_result($cur,'Valor4'),2,',','.');	?>
                            </td>
                        </tr>
              <?php
                       $i++;
					   $total1 += odbc_result($cur,'clientes1');	
					   $total2 += odbc_result($cur,'valor1');
					   $total3 += odbc_result($cur,'clientes2');
					   $total4 += odbc_result($cur,'valor2');
					   $total5 += odbc_result($cur,'clientes3');
					   $total6 += odbc_result($cur,'valor3');
					   $total7 += odbc_result($cur,'clientes4');
					   $total8 += odbc_result($cur,'valor4');
					  
                     }
                    ?>
              
           </tbody>
       <tfoot>
           <tr>
             <th scope="col">Total</th>
             <th scope="col" style="text-align:right"><label id="total1" style="color:#FFF"><?php echo number_format($total1,0,',','.');?></label></th>
             <th scope="col" style="text-align:right"><label id="total2" style="color:#FFF"><?php echo number_format($total2,2,',','.');?></label></th>
             <th scope="col" style="text-align:right"><label id="total3" style="color:#FFF"><?php echo number_format($total3,0,',','.');?></label></th>
             <th scope="col" style="text-align:right"><label id="total4" style="color:#FFF"><?php echo number_format($total4,2,',','.');?></label></th>            
             <th scope="col" style="text-align:right"><label id="total5" style="color:#FFF"><?php echo number_format($total5,0,',','.');?></label></th>
             <th scope="col" style="text-align:right"><label id="total6" style="color:#FFF"><?php echo number_format($total6,2,',','.');?></label></th>
             <th scope="col" style="text-align:right"><label id="total7" style="color:#FFF"><?php echo number_format($total7,0,',','.');?></label></th>
             <th scope="col" style="text-align:right"><label id="total8" style="color:#FFF"><?php echo number_format($total8,2,',','.');?></label></th>  
            </tr>
       </tfoot>
    </table>
 
    <form action="<?php echo $root; ?>role/searchClient/ListClient.php" method="get">
<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform, ENT_QUOTES, 'UTF-8'); ?>">
		  <input type="hidden" name="reltipo" value="informII">
		  <input type="hidden" name="comm" value="buyers">

		  <div class="barrabotoes">
		  	<button onclick="javascript: this.form.comm.value='open';this.form.submit();" class="botaovgg">Voltar</button>
		  </div>

		</form>
  


<!-- FIM Conteudo Página -->
</div>