<?php 

/* codigo do procedure

CREATE Procedure ssCalc_V_Perda
	@v_Perda	money	OUTPUT,
	@i_Aviso		int
AS

SELECT 	@v_Perda = 	ISNULL((SELECT SUM(v_Fatura) FROM Perda WHERE i_Aviso = @i_Aviso),0)
		+	ISNULL((SELECT SUM(v_Juros) FROM Perda WHERE i_Aviso = @i_Aviso),0) 
		-	ISNULL((SELECT SUM(v_Pago) FROM Perda WHERE i_Aviso = @i_Aviso),0) 

RETURN
*/

if(! function_exists('calcVPerda')){
  function calcVPerda($i_Aviso){
    global $dbSisSeg;

    $x = odbc_exec($dbSisSeg,
		   "SELECT ISNULL((SELECT SUM(v_Fatura) FROM Perda WHERE i_Aviso = $i_Aviso), 0)
		         + ISNULL((SELECT SUM(v_Juros) FROM Perda WHERE i_Aviso = $i_Aviso), 0)
		         - ISNULL((SELECT SUM(v_Pago) FROM Perda WHERE i_Aviso = $i_Aviso), 0)");
    if(odbc_fetch_row($x)){
      return odbc_result($x, 1);
    }
  }
}

$v_Perda = calcVPerda($i_Aviso);
?>
