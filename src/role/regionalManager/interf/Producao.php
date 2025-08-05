
<?php require_once("../../../navegacao.php"); ?>

 <div class="conteudopagina">    
	<form name="consulta" id="consulta"action="<?php echo $root; ?>role/regionalManager/RegionalManager.php" method="get">
          <ul>
          <li class="campo2colunas"><label>Ano</label>
           <select name="ano">
            <?php 
            for($i = 2002; $i <= date("Y"); $i++){
              echo "<option value=$i".($i == $ano ? ' selected' : '').
              "> $i </option>\n";
            }
            ?>
           </select>
          </li>
          </ul>
          <input type="hidden" name="comm" value="prod">
           <div class="barrabotoes">
               <button class="botaoagm" type="button" onclick="javascript: consulta.submit();">Pesquisar</button>
           </div>
    </form>
    <!-- CONTEÚDO PÁGINA - INÍCIO -->
    <?php 
    
    if(isset($_GET['ano'])){
      $ano = $_GET['ano'];
      
      $i = 0;
      $total1 = $total2 = 0;
    ?>
    
          <table  class="tabela01" style="width:100% !important;">
              <caption><?php echo htmlspecialchars($ano, ENT_QUOTES, 'UTF-8'); ?></caption>
                <thead>
                  <tr>
                      <th scope="col">Executivo</th>
                      <th scope="col">Ap&oacute;lices novas</th>
                      <th scope="col">Ap&oacute;lices renovadas</th>
                  </tr>
                </thead>
                <tbody>
                <?php 
                if(isset($v)){
                  foreach($v as $name => $c){?>
                   <tr>
                       <td><?php echo ($name);?></td>
                       <td><?php echo $c[0];?></td>
                       <td><?php echo $c[1];?></td>
                   </tr>
                 <?php
        					$i++;
        					$total1 += $c[0];
        					$total2 += $c[1];
        				  }
                }
        				  ?>
                  </tbody>
                  <tfoot>
                  <tr>
                       <td><label>Total</label></td>
                       <td><label><?php echo $total1;?></label></td>
                       <td><label><?php echo $total2;?></label></td>
                   </tr>
                  </tfoot>
           </table>
      
	<?php 
    }
    ?>
</div>












