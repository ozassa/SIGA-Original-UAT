<?php 

    if ($val == 1) {
	$q = "UPDATE SinistroDetails SET selected = 1 WHERE id = $idDetails";	
    } else {
	$q = "UPDATE SinistroDetails SET selected = 0 WHERE id = $idDetails";	
    }
	//echo "<pre>$q</pre>";
	$cur = odbc_exec($db, $q);
        //echo "<pre>$cur</pre>";
?>