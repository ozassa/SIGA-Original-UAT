<BODY bgColor=#ffffcc>
<table width="100%" cellspacing=0 cellpadding=3 border=0>
     
	<tr>
		
     	<th><div align=center><FONT face=arial size=2 color=#000066>
	<form  action="<?php echo  $root;?>role/credit/Credit.php" method="get">
	<input type="hidden" name="comm" value="CountryConsult">


        <b>País:</b>&nbsp;&nbsp;</FONT>
	<input type="text" name=searchCountry style="WIDTH: 349px; HEIGHT: 22px" size=44>&nbsp;
                    <INPUT type="submit" name=submit value="OK"></th>     
 
    </form>

	</tr>
  

<?php  if($submit or $searchCountry){

		require_once("../credit/countryConsult.php");

	require_once("countryFiltro.php");

//	require_once("countryConsultInterf.php");  //formata a saida

}
?>


