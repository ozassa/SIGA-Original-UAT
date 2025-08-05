<?php  // Mostra as diferenças entre siex e sisseg.....



if(! function_exists('getStrDate')){
  function getStrDate($str){
    $row = explode('-', $str);
    $ret = $row[2]. "/". $row[1] ."/". $row[0];
    if ($ret == '//')
      return '';
    return $ret;
  }
}


if(! function_exists('ret_dev_status'))
{
	function ret_dev_status($pinicio, $pcmb, $ptipo, $pselected, $psize, $pclass, $wfunc)
	{
	
		//$wret  = "<select name=""" . $pcmb . """  size=""" . $psize . """ " . $pclass . "  " . $wfunc . " >";
		
		
		$wret  = "<select name='$pcmb'  size='$psize'  $pclass  $wfunc >";
		   
		if (pinicio == "B") 
		{
		   $wret = $wret . "<option selected value=''></option>" . "<br>";
		}
		
		if ($pinicio == "T")
		{
		   $wret = $wret . "<option selected value=''>todos</option>" . "<br>";
		}
		
		if ($pinicio == "S")
		{
		   $wret = $wret . "<option selected value=''>Selecione</option>" . "<br>";
		}
		
		
        $wret = $wret . "<option " . is_selecionado ($pselected, "1") . " value='1'>" . ret_dve_de_status("1") . "</option>" . "<br>";
		$wret = $wret . "<option " . is_selecionado ($pselected, "2") . " value='2'>" . ret_dve_de_status("2") . "</option>" . "<br>";
		$wret = $wret . "<option " . is_selecionado ($pselected, "3") . " value='3'>" . ret_dve_de_status("3") . "</option>" . "<br>";
		$wret = $wret . "<option " . is_selecionado ($pselected, "4") . " value='4'>" . ret_dve_de_status("4") . "</option>" . "<br>";
				 
		$wret = $wret . "</select>";
		
		
		return  $wret;
	
	}

}
/////////////////////////////////////////////////////////////////////////////////////

if(! function_exists('ret_dev_rel'))
{
	function ret_dev_rel($pinicio, $pcmb,  $pselected,  $pclass, $wfunc)
	{
	
		
		// $wret  = "<select name='$pcmb'  size='$psize'  $pclass  $wfunc >";
		$wret  = "";   
		   
		if (pinicio == "B") 
		{
		   $wret = $wret . "" . "";
		}
		
		if ($pinicio == "T")
		{
		   $wret = $wret . "<input type='radio' name='$pcmb' value='' checked  $pclass  $wfunc >Todos";
		}
		
		if ($pinicio == "S")
		{
		   $wret = $wret . "<input type='radio' name='$pcmb' value='' checked  $pclass  $wfunc >Selecione";
		}
		
		
        $wret = $wret . "<input type='radio' name='$pcmb' value='1' " . is_check ($pselected, "1")  . "  $pclass  $wfunc >" . ret_dve_de_rel ("1");
		$wret = $wret . "<input type='radio' name='$pcmb' value='2' " . is_check ($pselected, "2")  . "  $pclass  $wfunc >" . ret_dve_de_rel ("2");
		
				 
		$wret = $wret . "</select>";
		
		
		return  $wret;
	
	}

}


/////////////////////////////////////////////////////////////////////////////////////
if(! function_exists('fmes'))
{

   function fmes($cur_mes)
   {
      if($cur_mes < 10)
	  {
	     $cur_mes = "0" . $cur_mes;
	  }
	  else
	  {
	     $cur_mes = "" . $cur_mes;
	  }
	  
	  return $cur_mes ;
	  
   }

}
////////////////////////////////////////////////////////////////////////////////////

if(! function_exists('ret_dif_data'))
{
	function ret_dif_data($db, $pinicio, $pcmb, $ptipo, $pselected, $psize, $pclass, $wfunc)
	{
	
		
		$wret  = "<select name='$pcmb'  size='$psize'  $pclass  $wfunc >";
		   
		if (pinicio == "B") 
		{
		   $wret = $wret . "<option selected value=''></option>" . "<br>";
		}
		
		if ($pinicio == "T")
		{
		   $wret = $wret . "<option selected value=''>todas</option>" . "<br>";
		}
		
		if ($pinicio == "S")
		{
		   $wret = $wret . "<option selected value=''>Selecione</option>" . "<br>";
		}
		
		
		// Ver a data atual 


        //21
		//49
		//42

		
		$sql = " SELECT  a.id, a.dt_proc from SINC_SISSEG a order by a.dt_proc desc  ";

        $cur = odbc_exec($db,$sql);
   
	
		$i = 1;
		 while(odbc_fetch_row($cur)  && $i < 30)
		{
		
		
		   $wid = odbc_result($cur, "id");
		   $wdt_proc = odbc_result($cur, "dt_proc");
		   
		   $wret = $wret . "<option " . is_selecionado ($pselected, $wid ) . " value='" . $wid . "'>" . getStrDate(substr($wdt_proc, 0, 10)) .  "</option>" . "<br>\n";   
		   

		   
		   $i = $i + 1;
		}
		
		
		
				 
		$wret = $wret . "</select>";
		
		
		return  $wret;
	
	}

}



////////////////////////////////////////////////////////////////////////////////////

if(! function_exists('is_selecionado'))
{
	function is_selecionado ($sel1 , $sel2)
	{
	   
	   $wis_selecionado = "";
	   
	   if ($sel1 == $sel2)
	   {
	      $wis_selecionado = "SELECTED";
	   }
	   else
	   {
	      $wis_selecionado = "";
	   } 
	
	
	   return $wis_selecionado;
	 }   
}

if(! function_exists('is_check'))
{
	function is_check ($sel1 , $sel2)
	{
	   
	   $wis_selecionado = "";
	   
	   if ($sel1 == $sel2)
	   {
	      $wis_selecionado = "CHECKED";
	   }
	   else
	   {
	      $wis_selecionado = "";
	   } 
	
	
	   return $wis_selecionado;
	 }   
}


if(! function_exists('ret_dve_de_status'))
{
	function ret_dve_de_status ($cod)
	{
	
       $wret = "";	   

	   if($cod=="1")
	   {
	      $wret = "Não enviada (Preenchendo)";	     
	   }
	   elseif ($cod=="2")
	   {
	       $wret = "Enviada";	    
	   }
	   elseif ($cod=="3")
	   {
	       $wret = "Alterada";	    
	   }
	   elseif ($cod=="4")
	   {
	       $wret = "Não Enviada e Alterada";	    
	   }
	   return $wret;
	   
	}
}

if(! function_exists('ret_dve_de_rel'))
{
	function ret_dve_de_rel ($cod)
	{
	
       $wret = "";	   

	   if($cod=="1")
	   {
	      $wret = "Por DVE";	     
	   }
	   elseif ($cod=="2")
	   {
	       $wret = "Por apólice";	    
	   }
	   
	   return $wret;
	   
	}
}

//////////////////////////////////////////////

// converte a data de yyyy-mm-dd para dd/mm/yyyy
if(! function_exists('ymd2dmy')){
  function ymd2dmy($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}

//////////////////////////////////////////////

function numApolice ($idInform,$db,$dbSisSeg) {
 
 // Obtem dados do inform
 $t = odbc_exec($db, "SELECT inf.id, inf.contrat, inf.i_Seg, inf.nProp, inf.name, inf.cnpj, inf.startValidity, inf.endValidity, inf.emailContact, inf.contact, inf.email from Inform inf WHERE inf.id = $idInform");
 $hc_infName = trim(odbc_result($t, "name"));
 $hc_startValidity = ymd2dmy(trim(odbc_result($t, "startValidity")));
 $hc_endValidity = ymd2dmy(trim(odbc_result($t, "endValidity")));
 $hc_i_Seg = trim(odbc_result($t, "i_Seg"));
 $hc_n_Prop = trim(odbc_result($t, "nProp"));
 $hc_c_Coface = trim(odbc_result($t, "contrat"));
 
 if (!$hc_c_Coface) $hc_c_Coface = " is null ";
 else $hc_c_Coface = " = ".$hc_c_Coface;
 
 if (!$hc_i_Seg) $hc_i_Seg = " is null ";
 else $hc_i_Seg = " = ".$hc_i_Seg;
 
 if (!$hc_n_Prop) $hc_n_Prop = " is null ";
 else $hc_n_Prop = " = ".$hc_n_Prop;
 
// echo  $hc_infName." - ".$hc_startValidity." - ".$hc_endValidity." - ".$hc_i_Seg." - ".$hc_n_Prop." - ".$hc_c_Coface;
 
 // Obtem o número da apólice
 $achou=false;
 
 $loc_sql =  "SELECT  n_Apolice ".
    "FROM  Base_Calculo ".
    "WHERE   c_Coface ".$hc_c_Coface.
    "   AND i_Seg ".$hc_i_Seg.
    "   AND n_Prop ".$hc_n_Prop.
    "   AND t_Apolice = 0 ".
    "ORDER BY i_BC DESC ";
 
 $t = odbc_exec($dbSisSeg, $loc_sql);
 if (!odbc_fetch_row($t))
 {
 
  $loc_sql =  "SELECT  n_Apolice ".
     "FROM  Base_Calculo ".
     "WHERE   c_Coface ".$hc_c_Coface.
     "   AND i_Seg ".$hc_i_Seg.
     "   AND n_Prop ".$hc_n_Prop." ".
     "ORDER BY i_BC DESC ";
 
    $t = odbc_exec($dbSisSeg, $loc_sql);
    if (!odbc_fetch_row($t))
    {
   $loc_sql =  "SELECT  n_Apolice ".
      "FROM  Base_Calculo ".
      "WHERE   c_Coface ".$hc_c_Coface.
      "   AND i_Seg ".$hc_i_Seg." ".
      "ORDER BY i_BC DESC ";
 
       $t = odbc_exec($dbSisSeg, $loc_sql);
    if (odbc_fetch_row($t))
       {
      $achou=true;  
    }
    }
    else
    {
       $achou=true;  
    }
 }
 else
 {
    $achou=true;  
 }
 
 if($achou)
 {
    $hc_n_Apolice = trim(odbc_result($t, "n_Apolice"));
 }
 else
 {
    $hc_n_Apolice = 0;
 }
 
  return $hc_n_Apolice;
}


//////////////////////////// INICIO DE PROGRAMA /////////////////////////////


?>
 
<form action="<?php echo  $root;?>role/credit/credit.php" method=post>
<table border="0" width="100%">

<tr>
   <td colspan="4">
   Dados para pesquisa:
   </td>
</tr>

<tr>
   <td colspan="4">
   <HR>
   </td>
</tr>

<tr>
   <td width="5%">
   Na&nbsp;data:
   </td>
   <td width="50%">
      
   <?php  echo ret_dif_data($db, "", "DATA", "", $DATA, "1", "", "");?>
   
   </td>
   
</tr>


<tr>   
   <td colspan="4" align="center">
   <input type="submit" value=" OK " name=submit class="servicos">
   </td>
</tr>   

<tr>
   <td colspan="4">
   <HR>
   </td>
</tr>
   
</table>
<input type="hidden" name="comm" value="showdifsisseg">
<input type="hidden" name="EXECUTAR" value="1">

</form>

<?php  //echo $DETALHE;

if ($EXECUTAR == "1")
{
   // monta o SQL...... (encontrar apócile: Base_Calculo )
   
      
   $sql = " SELECT  a.i_Seg, a.nProp, a.id as idInform, a.name, ";
   
   $sql = $sql . " b.tp_erro, b.idImporter, b.de_erro ";
      
   $sql = $sql . " FROM  Inform a, SINC_SISSEG_DET b  ";
   
   $sql = $sql . " WHERE a.id = b.idInform ";
   // $sql = $sql . " AND b.id_sinc = " . $DATA;
   
   if ("" . $DATA != "" )
   {
   
      $sql = $sql . " AND b.id_sinc = " . $DATA;
	   
   }
   

   $sql = $sql . " ORDER BY a.idInform, b.tp_erro ";	
   
   
   
   //echo " - " . $sql . " <BR>";
   
   $cur = odbc_exec($db, $sql);
   
   $i = 0;
   
   $total = 0;
   
   while(odbc_fetch_row($cur))
   {
       $i++;
	   
	   if ($i == 1)
	   {
				?>			
			      <table width="100%" cellspacing="0" cellpadding="2" align="center">
			      <tr class="bgAzul">			
			      <!--<td width="5%" align="center" class="bgAzul">Apólice</td>
				  -->
			      <td width="35%" align="left" class="bgAzul">Segurado</td>
			      <td width="5%" align="center" class="bgAzul">Tipo Erro</td>
			      <td width="60%" align="center" class="bgAzul">Erro</td>    
				  </tr>
				<?php  }
	   
	   echo "<tr";
       echo ($i % 2 == 0) ? " bgcolor = #e9e9e9": "";
       echo ">";
	   
	   ?>		
		      <!--<td  align="center" ><?php  echo numApolice (odbc_result($cur, "idInform"),$db,$dbSisSeg);?></td>
		      -->
			  <td  align="left" ><?php  echo odbc_result($cur, "name");?></td>
		      <td  align="center" ><?php  echo odbc_result($cur, "tp_erro");?></td>
		      <td  align="left" ><?php  echo odbc_result($cur, "de_erro");?></td>    
	   <?php  }
   
   
	   if ($i != 0)
	   {
	   ?>
	   
	   
	      </table>
		  <!-- 
		  <a href="<?php echo  $root;?>role/dve/interf/consultadve.php?comm=consultadve&EXECUTAR=<?php echo  $EXECUTAR;?>&DATA=<?php echo  $DATA;?>&STATUS=<?php echo  $STATUS;?>&EXL=1&DETALHE=<?php echo  $DETALHE;?>">Excel</a>
		  -->
		  
	   <?php  }
	   else
	   {
	      echo "<br><center>Nenhuma informação encontrada.</center><br>";
	   }
   
   
}

?>