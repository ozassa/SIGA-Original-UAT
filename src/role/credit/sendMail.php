<?php  $fp = fsockopen("udp://tdc132", 1654, $errno, $errstr);

	if (!$fp) {
		echo "ERROR: $errno - $errstr<br>\n";
	} else {
		fwrite($fp, $idCookie);
		fclose($fp);
	}

?>