<?php

function ExportToExcel()
{
	global $cCharset;
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;Filename=Inform.xls");

	echo "<html>";
	echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
	
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$cCharset."\">";
	echo "<body>";
	echo "<table border=1>";

	WriteTableData();

	echo "</table>";
	echo "</body>";
	echo "</html>";
}


function WriteTableData()
{
	global $rs,$nPageSize,$strTableName,$conn;
	
	if(function_exists("ListFetchArray"))
		$row = ListFetchArray($rs);
	else
		$row = db_fetch_array($rs);	
	if(!$row)
		return;
// write header
	echo "<tr>";
	if($_REQUEST["type"]=="excel")
	{
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Id").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Waranty Interest").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Id Insured").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Id User").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("State").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("General State").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Vol State").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Seg State").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Financ State").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Buyers State").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Lost State").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Resp Name").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Ocupation").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Born Date").'</td>';	
		echo '<td style="width: 100" x:str>'.PrepareForExcel("Name").'</td>';	

	}
   echo "</tr>";
?>