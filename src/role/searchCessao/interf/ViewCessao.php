<?php require_once("../../../navegacao.php"); ?>

<?php $exportadorFixo  = isset($_REQUEST['exportadorFixo']) ? $_REQUEST['exportadorFixo'] : false; ?>

<!-- CONTEÚDO PÁGINA - INÍCIO -->
<form name="frm" id="frm" action="<?php echo $root;?>role/searchCessao/SearchCessao.php" method="post">
    <div class="conteudopagina">
          <li class="campo2colunas"><label>Segurado</label>
        <input type="text" name="exportador">
    </li>
    
          <?php if ($exportadorFixo) {?>
      <div style="clear:both">&nbsp;</div>
          <?php }?>
          
          <li class="campo2colunas"><label>Comprador</label>
            <input type="text" name="importador">
          </li>
          
          <li class="campo2colunas"><label>Tipo do Banco</label>
            <select name="tpbanco">
                <option value="-1" selected>
                <option value="1">Banco do Brasil
                <option value="2">Bancos Parceiros
                <!--<option value=3>Outros bancos-->
            </select>
          </li>
          
          <li class="campo2colunas"><label>Banco</label>
            <input type="text" name="banco">
          </li>
          
          <li class="campo2colunas"><label>Status</label>
            <select name=status>
              <option value="-1" selected>
                  <option value=2>Ativo</option>
                  <option value=1>Solicitado</option>
                  <option value=3>Cancelado</option>
                  <option value=4>Cancelamento Solicitado</option>
                  <option value=5>Encerradas</option>
            </select>
          </li>
          
          <li class="barrabotoes" style="list-style:none;*margin-left:-15px">
            <input type="hidden" name="operacao" value="1">
            <button name="submit" type="submit" value="1" class="botaoagm">Pesquisar</button>
          </li>
    </div>
</form>

<?php 
  $op = isset($_POST['operacao']) ? $_POST['operacao'] : 0;
  if($op == 1){
    $tpbanco      = $_POST['tpbanco'];
    if ($tpbanco != '-1') {
      $tpbanco      = (isset($_POST['tpbanco']) && ctype_digit($_POST['tpbanco'])) ? (int)$_POST['tpbanco'] : null;
    }
    
    $status       = $_POST['status'];
    if ($status != '-1') {
      $status       = (isset($_POST['status']) && ctype_digit($_POST['status'])) ? (int)$_POST['status'] : null;
    }

    $exportador   = $_POST['exportador'];
    $importador   = $_POST['importador'];
    $banco        = $_POST['banco'];

    $order2     = '';

    $prep_array = array();

      //-------------------------------
      //-- Cessao do Banco do Brasil---
    if (trim("" . $tpbanco) == "-1" || trim("" . $tpbanco) == "1" ){  
        $wstr1 = " select  i.n_Apolice as Apolice, i.id as idInform, i.name, cd.status, cd.id as idCessao, ". 
          " b.name namebanco, b.id as idBanco, 1 as tpbanco, cd.codigo, cd.dateClient  " . 
          "  " .
          " FROM Inform i, CDBB cd, Banco b " ;
  
        $where1 = " WHERE i.id = cd.idInform " .
          " AND   b.id = 1 ";
  
        $order1 = " ORDER BY b.name, i.name ";
        $order1 = " ";
  
        if (trim("" . $exportador) != ""){
          $where1 = $where1 . " AND upper(i.name) like ? ";  
          $prep_array[] = "%".strtoupper ($exportador)."%";
        }

        if ($exportadorFixo){
          $where1 = $where1 . " AND  i.id = ? ";  
          $prep_array[] = $exportadorFixo;
        }

  
        if (trim("" . $importador) != ""){
          $where1 = $where1 . " AND  cd.id in ( " .
          " Select x.idCDBB FROM CDBBDetails x, Importer im " . 
          " WHERE x.idImporter = im.id " .
          " AND x.idCDBB = cd.id " .   
          " AND x.idCDBB = cd.id " .   
          " AND upper(im.name) like ? ) ";
          $prep_array[] = "%".strtoupper ($importador)."%";
        }

        if (trim("" . $banco) != ""){
          $where1 = $where1 . " AND upper(b.name) like ?  ";
          $prep_array[] = "%".strtoupper ($banco)."%";
        }
  
        if (trim("" . $status) != "-1"){
          if (trim("" . $status) == "2"){
              $where1 = $where1 . " AND (cd.status = 2 or cd.status = 4)  ";
        }else{
            $where1 = $where1 . " AND cd.status = ? ";
            $prep_array[] = $status;
        }   
        }
    }
      //--FIM BANCO DO BRASIL--------------------------------------

      //---------------------------------
      //-- Cessao Dos Bancos Parceiros---
    if (trim("" . $tpbanco) == "-1" || trim("" . $tpbanco) == "2" ){ 
        $wstr2 = " select  i.n_Apolice as Apolice, i.id as idInform, i.name, cd.status, cd.id as idCessao, ". 
          " b.name namebanco, b.id as idBanco, 2 as tpbanco, cd.codigo, cd.dateClient  " . 
          "  " .
          " FROM Inform i, CDParc cd, Banco b " ;
  
        $where2 = " WHERE i.id = cd.idInform " .
          " AND   b.id = cd.idBanco ";
  
        $order2 = " ORDER BY b.name, i.name ";
  
        if (trim("" . $exportador) != ""){
          $where2 = $where2 . " AND  upper(i.name) like ? ";  
          $prep_array[] = "%".strtoupper ($exportador)."%";
        }
  
        if ($exportadorFixo){
          $where2 = $where2 . " AND  i.id = ? ";
          $prep_array[] = $exportadorFixo;
        }
  
        if (trim("" . $importador) != ""){
          $where2 = $where2 . " AND  cd.id in ( " .
          " Select x.idCDParc FROM CDParcDetails x, Importer im " . 
          " WHERE x.idImporter = im.id " .
          " AND x.idCDParc = cd.id " .   
          " AND x.idCDParc = cd.id " .   
          " AND upper(im.name) like ? ) ";

          $prep_array[] = "%".strtoupper ($importador)."%";
        }

        if (trim("" . $banco) != ""){
          $where2 = $where2 . " AND upper(b.name) like ?  ";
          $prep_array[] = "%".strtoupper ($banco)."%";
        }
  
        if (trim("" . $status) != "-1"){
          if (trim("" . $status) == "6"){
              $where2 = $where2 . " AND (cd.status = 2 or cd.status = 4)  ";
        }else{
            $where2 = $where2 . " AND cd.status = ? ";
            $prep_array[] = $status;
        }   
        }
    }
      //--FIM BANCO Parceiros--------------------------------------
  
  
      //--Montando o select
      if (trim("" . $tpbanco) == "-1" ){
        $wstr = $wstr1 . $where1 . $order1 . " union " . $wstr2 . $where2 . $order2;
      }else{
        if (trim("" . $tpbanco) == "1" ){
          $wstr = $wstr1 . $where1 . $order2;
        }else{
          $wstr = $wstr2 . $where2 . $order2;
        }
      }
      
      $stmt    = odbc_prepare($db,  $wstr );
      $resulx = odbc_execute($stmt, $prep_array);
  } 
  // apresentacao do resultado
?>
<div class="conteudopagina">
  <table summary="Submitted table designs" id="example">
        <thead>
        <tr>
          <th>Apólice</th>
          <th>Segurado</th>
          <th>Cod.Cess&atilde;o</th>
          <th>Banco</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php 
        
        if(isset($_POST['submit'])){
           $i = 0;
           while ($row = odbc_fetch_array($stmt))
           {
           
              $i++;
              
              $hc_Apolice = $row['Apolice'];
              $hc_idCessao  = trim($row['idCessao']);
              $hc_name      = trim($row['name'] ?? '');
              $hc_namebanco = $row['namebanco'];
              $hc_status    = $row['status'];
              $hc_tpbanco   = $row['tpbanco'];
              $hc_idInform  = $row['idInform'];
              $hc_codigo  = $row['codigo'];
              $dateEnv = $row['dateClient'];
              list($ano, $mes, $dia) = explode('-', $dateEnv);
              
              if ($hc_tpbanco == 1)
              {
                //BB
                $hc_tpbanco = 1;
              }else if ($hc_tpbanco == 2)
              {
                 $hc_tpbanco = 2;
              }else
              {
                 $hc_tpbanco = 3;
              }
              
              
              
              if ($hc_status == 0)
              {
                $hc_status_desc = "Novo";
              }elseif ($hc_status == 1)
              {
                $hc_status_desc = "Solicitado"; 
              }elseif ($hc_status == 2)
              {
                $hc_status_desc = "Ativo";
              }elseif ($hc_status == 4)
              {
                $hc_status_desc = "Cancelamento Solicitado";
              }elseif ($hc_status == 3)
              {  
                $hc_status_desc = "Cancelado";
              }elseif ($hc_status == 5)
              {  
                $hc_status_desc = "Encerrada";
              }else
              {
                $hc_status_desc = "---";
              
              }
              
              
              if ($hc_name !="" )
              {
            
              
              ?>
              <tr> 
                 <td><?php  echo ($hc_Apolice);?></td>
                 <td><a href="<?php echo $root; ?>role/searchCessao/SearchCessaoDet.php?tipoBanco=<?php echo $hc_tpbanco; ?>&idCessao=<?php echo $hc_idCessao; ?>&idInform=<?php echo $hc_idInform; ?>"><?php  echo ($hc_name); ?></a></td>
                 <td><?php  echo $hc_codigo . "/" . $ano; ?>&nbsp;</td>
                 <td><?php  echo ($hc_namebanco);?></td>
                 <td><?php  echo $hc_status_desc;?></td>
               </tr>
              <?php 
          }
         } 
         if ($i == 0) {
         ?>
                <tr>
                    <td colspan="4">Nenhuma cess&atilde;o Encontrada</td>
                </tr>
        <?php  
                   }
                  
                }
                
                ?>
        </tbody>
  </table> 
    
  <div class="divisoria01"></div>
</div>