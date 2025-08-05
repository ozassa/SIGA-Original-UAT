<?php  // Criado Hicom (Gustavo) - 15/12/04

$log_query = "";

/////////////////////////////////////////////////////////////////////////////
if(! function_exists('get_de_st_inform')){
  // Retorna o status do Inform
  function get_de_st_inform($status)
  {
     if($status == 1)
	 {
	    return "Novo";
		
	 }elseif ($status == 2)
	 {
	    return "Preenchido";  
		
	 }elseif ($status == 3)
	 {
	    return "Analisando";  
		
	 }elseif ($status == 4)
	 {
	    return "Tarifado";
		  
	 }elseif ($status == 5)
	 {
	    return "Oferta";
		  
	 }elseif ($status == 6)
	 {
	    return "Proposta";
		  
	 }elseif ($status == 7)
	 {
	    return "Confirmado";
		  
	 }elseif ($status == 8)
	 {
	    return "Alterado";  
		
	 }elseif ($status == 9)
	 {
	    return "Cancelado";  
		
	 }elseif ($status == 10)
	 {
	    return "Apólice";  
		
	 }elseif ($status == 11)
	 {
	    return "Encerrado";  
	 }else
	 {
	    return "Indefinido ($status)";  
	 }
  }
}
////////////////////////////////////////////////////////


if ($executa == 1) {
	$sql = "UPDATE Inform SET warantyInterest = $warantyInterest WHERE id = $idInform";
	$cur=odbc_exec($db, $sql);
	
	if($cur)
	{
		$log_query .= $sql;
	}
	
     //Registrar no Log (Sistema) - Criado Por Tiago V N - 03/07/2006
     // Tipo Log = Alteração opção de Juros de Mora (Tela Cliente) - 38
    $sql  = "Insert into Log (tipoLog, id_User, Inform, data, hora) Values ('38'," .
            "'$userID', '$idInform','".date("Y")."-".date("m")."-".date("d").
            "','".date("H").":".date("i").":".date("s")."')";
    if (odbc_exec($db, $sql) ) {
        $sql_id = "SELECT @@IDENTITY AS 'id_Log'";
   	    $cur = odbc_result(odbc_exec($db, $sql_id), 1);
        $sql = "Insert Into Log_Detalhes (id_Log, campo, valor, alteracao) ".
               "values ('$cur', '-', '$warantyInterest', 'Juros e Mora')";
 	    $rs = odbc_exec($db, $sql);
		
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	   //CRIADO POR WAGNER
	   // ESTA PARTE GUARDA NUMA TABELA  A QUERY DA AÇÃO REGISTRADA NA TABELA Log_Detalhes
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   
	   if ($rs) {
	      $sql_id_detalhes = "SELECT @@IDENTITY AS 'id_detalhes'";
	      $cur = odbc_result(odbc_exec($db, $sql_id_detalhes), 1);
	      $sql = "Insert Into Log_Detalhes_Query (id_detalhes, query) ".
	          "values ('$cur', '".str_replace("'","",$log_query)."')";
			  
			  //echo $sql;
	          odbc_exec($db, $sql);
	   }//fim if	
	   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		
		
		
    }
}

$sql = 	"SELECT 	name, state, startValidity, endValidity, warantyInterest, Ga ".
		"FROM 		Inform ".
		"WHERE 		id = $idInform ";
$cur  = odbc_exec($db,$sql);

$name = odbc_result($cur,"name");
$state =  odbc_result($cur,"state");
$startValidity = odbc_result($cur,"startValidity");
$endValidity = odbc_result($cur,"endValidity");
$warantyInterest = odbc_result($cur,"warantyInterest");
$ga				 = odbc_result($cur,"Ga");


require_once("../../../navegacao.php");?>

<div class="conteudopagina">
<li class="campo2colunas">
   <label>Segurado</label>
   <?php  echo $name;?>
</li>
<?php  if ($startValidity) {  ?>
         <li class="campo2colunas"><label>Vig&ecirc;ncia:</label>
            <?php  echo ymd2dmy($startValidity);?> &agrave; <?php  echo ymd2dmy( $endValidity);?>
          </li>
<?php  }

?>
<li class="campo2colunas">
   <label>Status:</label>
    <?php  echo get_de_st_inform($state);?>
</li>
<div style="clear:both">&nbsp;</div>

<form action="<?php  echo $root;?>role/client/Client.php" method="post">
	<li class="campo2colunas">
        <label>Cobertura Para Juros de Mora:</label>      
        <input type="hidden" name="idInform" value="<?php  echo $idInform;?>">
        <input type="hidden" name="comm" value="changeWarantyInterest">
        <input type="hidden" name="executa" value="1">
        <div class="formopcao">     
        <input type="radio" name="warantyInterest" <?php  if ($warantyInterest == 1) echo("checked ");?>value="1"></div><div class="formdescricao">SIM</div>
        <div class="formopcao">
        <input type="radio" name="warantyInterest" <?php  if ($warantyInterest == 0) echo("checked ");?>value="0"></div><div class="formdescricao">N&Aacute;O</div> 
	 </li>						
	
      <div class="barrabotoes">			
       
        <button class="botaovgm" type="button"  onClick="this.form.comm.value='open';this.form.submit()">Voltar</button>
        <button class="botaoagm" type="submit"  onClick="this.form.submit();">Alterar</button>
      </div>
      
		<?php  if ($ga==1){
				  if ($state==5){	 ?>
		            <label><font color='red'>MSG de teste para Apolice ou Proposta que são GA e esta solicitando Juros de Mora.</font></label>
		
		<?php     }
			  }	  
		?>
	
</form>