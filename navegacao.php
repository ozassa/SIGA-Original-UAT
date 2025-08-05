<?php


	function UrlAtual() {
	  $dominio = $_SERVER['HTTP_HOST'];
	  $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
	  
	  return $url;
  }
	
	function UrlAtualMenu() {
		return $url;
	}

	function detect_encoding($string) {
    		return mb_detect_encoding($string.'x', 'UTF-8, ISO-8859-1');
		// return false;
  	}

  	$home  = "";
		if ($_SESSION['pefil'] == 'B') {
	    $home  = "?comm=openBanco";	
		} else if ($_SESSION['pefil'] == 'C') {
			$home  = "?idInform=".$idInform;
		} else if($_SESSION['pefil'] == 'CO' ) {
			$home  = '?comm=openConsultor';
		}
?>

<div id="titulo">
  <h1>
  	<?php 
      	if (mb_convert_encoding($title,'UTF-8', 'ISO-8859-1') == 'Título') {
			  $tit = 'Notificações';
			  $title = "Título";
		} else {
		    $tit  =  substr($title,0,55);
			  $leng = strlen($title); 
			  
			  if ($leng > 56) {
			    $tit = $tit."...";
			  }
		}

		if (detect_encoding($tit) != 'UTF-8') {
			$titulo_pagina = $tit;
			$desc_pagina = $title;
		} else {
			$titulo_pagina = mb_convert_encoding($tit,'ISO-8859-1', 'UTF-8');
			$desc_pagina = mb_convert_encoding($title,'ISO-8859-1', 'UTF-8');
		}

		echo $titulo_pagina;

		?>
	</h1>
</div>
<div class="divisoriaamarelo"></div>
<div id="navegacao"><span><a href="../access/Access.php<?php echo htmlspecialchars($home, ENT_QUOTES, 'UTF-8'); ?>">Home</a> &raquo;<a href="<?php echo htmlspecialchars(UrlAtual(), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($desc_pagina, ENT_QUOTES, 'UTF-8'); ?></a></span></div>
