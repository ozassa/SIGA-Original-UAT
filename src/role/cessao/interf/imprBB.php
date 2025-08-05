<?php 
   $sql = "SELECT b.name AS Nome_Banco, a.codigo AS Cod_Agencia, a.name AS Nome_Agencia
		FROM Agencia a INNER JOIN Banco b ON b.id = a.idBanco
		WHERE b.id = $idBanco And a.codigo = $agencia";
		
  $curr = odbc_exec ($db, $sql);

  $Nome_Banco = odbc_result($curr, 'Nome_Banco');
  $Cod_Agencia = odbc_result($curr, 'Cod_Agencia');
  $Nome_Agencia = odbc_result($curr, 'Nome_Agencia');
?>

<style>
  p {
    margin-bottom: 7px;
  }

  .p_left {
    width: 10%;
    float: left;
    font-weight: bold;
  }

  .p_right {
    float: left;
  }
</style>

<a name=cessao></a>
<?php require_once("../../../navegacao.php");?>

<div class="conteudopagina">

  <div class="p_left">
    <?php if ($Nome_Banco) { ?>
      <p>Banco:</p>
    <?php } ?>
    <?php if ($Cod_Agencia) { ?>
      <p>Ag&ecirc;ncia:</p>
    <?php } ?>
  </div>

  <div class="p_right">
    <?php if ($Nome_Banco) { ?>
      <p><?php echo $Nome_Banco; ?></p>
    <?php } ?>
    <?php if ($Cod_Agencia) { ?>
      <p><?php echo $Cod_Agencia; ?> - <?php echo $Nome_Agencia; ?></p>
    <?php } ?>
  </div>

  <li class="barrabotoes" style="list-style:none;*margin-left:-15px;">

  <div style="clear:both">&nbsp;</div>

  <form name=cessao_form action="<?php echo $root;?>role/cessao/Cessao.php#cessao" method="post">
    <input type=hidden name="comm" value="concluiBB">
    <input type=hidden name="agencia" value="<?php echo $agencia;?>">
    <input type=hidden name="idAgencia" value="<?php echo $idAgencia;?>">
    <input type=hidden name="idImporter" value="<?php echo $idImporter;?>">
    <input type=hidden name="idInform" value="<?php echo $idInform;?>">
    <input type=hidden name="idCDBB" value="<?php echo $idCDBB;?>">
    <input type=hidden name="idCDOB" value="<?php echo $idCDOB;?>">
    <input type=hidden name="idCDParc" value="<?php echo $idCDParc;?>">
    <input type=hidden name="idBanco" value="<?php echo $idBanco;?>">
    <input type=hidden name="tipoBanco" value="<?php echo $tipoBanco;?>">
    <input type=hidden name="total" value="<?php echo $total;?>">
    <input type=hidden name="totalR" value="<?php echo $totalR;?>">
  
    <p>* Para incluir ou excluir importadores na Cess&atilde;o de Direito clique em <b>Voltar</b>.</p>
  
    <p>* Para visualizar e imprimir uma pr&eacute;via do <b>Documento de Cl&aacute;usula Benefici&aacute;ria</b> clique em <b>Consultar</b>.</p>
 
    <p>* Para solicitar a Cess&atilde;o de Direito e emitir o <b>Documento de Cl&aacute;usula Benefici&aacute;ria</b> oficial clique em <b>Finalizar</b>.</p>
  
    <p>* <b>Aten&ccedil;&atilde;o:</b> o <b>Documento Oficial de Cl&aacute;usula Benefici&aacute;ria</b> dever&aacute; ser encaminhado &agrave; SBCE em 3 vias devidamente assinadas e carimbadas.</p>
 
    <?php 
      $link = $hostImagem . "src/role/cessao/cond_esp.php?idInform=$idInform&idAgencia=$idAgencia&agencia=$agencia&idBanco=$idBanco&idCDBB=$idCDBB&idCDOB=$idCDOB&idCDParc=$idCDParc&tipoBanco=$tipoBanco&total=$total&totalR=$totalR";
      $i = 1;
    
      while($i <= $total){
        $s = "sel$i";
        if(isset(${$s})){
          $imp = "idImporter$i";
          if(${$imp}){
            $link .= "&$imp=". ${$imp};
          } 
        }
        $i++;
      }

      $i = 1;
      while($i <= $totalR){
        $s = "selR$i";
        if(isset(${$s})){
          $imp = "idImporterR$i";
          if(${$imp}){
            $link .= "&$imp=". ${$imp};
          }
        }
        $i++;
      }
    ?>
    <div class="barrabotoes">
      <button class="botaovgm" type="button" onclick="this.form.comm.value='selImp';this.form.submit()">Voltar</button>
      <button class="botaoagm" type="button" onClick="imprimeC(this.form)">Consultar</button>
      <button class="botaoagm" type="button" onClick="this.form.submit()">Finalizar</button>
      <input  type="hidden" name="link" value="">
    </div>

  </form>

  <script language=javascript>
    function imprime(f) {
      w = window.open('<?php echo $link;?>&comm=gerapdf', 'pdf_windowoficial',
          'toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1');
      w.moveTo(5, 5);
      w.resizeTo(screen.availWidth - 35, screen.availHeight - 35);
      f.submit();
    }

    function imprimeC(f) {
      w = window.open('<?php echo $link;?>&comm=gerapdf&novalue=1&rascunho=true', 'pdf_window',
          'toolbar=0,location=0,directories=0,menubar=0,status=1,scrollbars=1,resizable=1');
      w.moveTo(5, 5);
      w.resizeTo(screen.availWidth - 35, screen.availHeight - 35);
    }
  </script>
</div>