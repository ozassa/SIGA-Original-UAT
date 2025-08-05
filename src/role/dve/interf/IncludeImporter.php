
<?php
 include_once('../../../navegacao.php');
 
 ?>
<script language=javascript src="<?php echo  $root;?>scripts/calendario.js"></script>
<script language=javascript src="<?php echo  $root;?>scripts/utils.js"></script>

<div class="conteudopagina">

    
    <div style="clear:both">&nbsp;</div>
    <label>Preencha os campos abaixo e clique em 'Incluir'</label>
    
    <?php 
            $imp  = odbc_exec($db,"select * from Inform where id = ".$idInform." order by name");
    
             $nameRazao = odbc_result($imp, 'name');
             $doc = odbc_result($imp, 'contrat');
             $idCountry = odbc_result($imp, 'origemNegocio');
    
    ?>
    <li class="campo2colunas"> 
        <label>CI Importador</label>
        <?php echo ($doc); ?>
        
    </li>
    
    <li class="campo2colunas"> 
       <label>Raz&atilde;o Social</label>
       <?php echo ($nameRazao);?>
    </li>
    
    <?php 
    
    $fields_to_fill = array("embarque" => "Data de Embarque",
							"vencimento" => "Data de Vencimento",
							"fatura" => "N° Fatura",
							"total" => "Valor Total Embarcado",
							"proex" => "Valor Fin. PROEX (US$)",
							"ace" => "Valor Fin. ACE (US$)");
    
    ?>
     <form action="<?php echo $root;?>role/dve/Dve.php#importers" method="post" name="f">
          <li class="campo2colunas">
             <label>Pa&iacute;s</label>
             <?php  
			     echo odbc_result(odbc_exec($db, "select * from Country where id = ".$idCountry.""),'name'); ?>
          </li>
          <div style="clear:both">&nbsp;</div>
    <?php
    
    //do_select($idCountry);
    
     
   
    foreach($fields_to_fill as $name => $label){
        if(isset(${$name})){
          echo ('<li class="campo2colunas"><label>'.$label.'</label>
            <input style="width:280px;"'." type=text name=$name value=\"${$name}\"".
            ($name == 'embarque' || $name == 'vencimento' ?
             " onFocus='blur()'>&nbsp;<A HREF=\"javascript:showCalendar(document.f.$name)\"><img src=\"$root/images/calendario.gif\" width=24 height=20 border=0 alt=calendário></A>" :
             ($name == 'total' || $name == 'proex' || $name == 'ace' ?
              " onBlur='checkDecimals(this.form.$name, this.form.$name.value)'>" : '>')).
            "</li>");
        }
    }
    ?>
    <div style="clear:both">&nbsp;</div>
    
    <input type="hidden" name="importerName" value="<?php echo  $importerName;?>">
    <input type="hidden" name="comm" value="view">
    <input type="hidden" name="dve_action" value="includeImporter">
    <input type="hidden" name="idBuyer" value="<?php echo  $idBuyer;?>">
    <input type="hidden" name="newdve" value="<?php echo  $newdve;?>">
    <input type="hidden" name="client" value="<?php echo  $client;?>">
    <input type="hidden" name="idInform" value="<?php echo  $idInform;?>">
    <input type="hidden" name="idNotification" value="<?php echo  $idNotification;?>">
    <input type="hidden" name="idDVE" value="<?php echo  $idDVE;?>">
    
    
    <button class="botaoagm" type="button" onClick="this.form.dve_action.value='<?php echo  $dve_action == 'alterImporter' ? 'view' : 'selectImporters';?>';this.form.submit()">Voltar</button>
    
    <?php if($dve_action == 'alterImporter'){ ?>
        <input type="hidden" name="idDetail" value="<?php echo  $idDetail;?>">
        <button class="botaoagm" type="button" onClick="this.form.dve_action.value='alterImporter';this.form.submit()">Alterar</button>
    <?php } else { ?>
                <button class="botaoagm" type="button" onClick="this.form.submit()">Incluir</button>
    <?php } ?>
    </form>
    
    <div style="clear:both">&nbsp;</div>
    <li class="campo2colunas">
    <font color=#ff0000><?php echo  ($msg);?></font>
    </li>
    
    <div style="clear:both">&nbsp;</div>




<script language=javascript>
var f = document.f;
	f.total.value = f.total.value.split(".")[0];
	formatDecimals(f.total, f.total.value);
	f.proex.value = f.proex.value.split(".")[0];
	formatDecimals(f.proex, f.proex.value);
	f.ace.value = f.ace.value.split(".")[0];
	formatDecimals(f.ace, f.ace.value);
</script>

</div>
