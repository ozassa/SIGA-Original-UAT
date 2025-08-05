<?php 

/*
CREATE PROCEDURE ssCalc_V_Rec  
	@v_Rec	money	OUTPUT,
	@i_Aviso		int
AS

DECLARE 
	@tx_Rec	numeric(7,3),
	@tx_RecA	numeric(7,3),
	@v_Lim 	money,
	@v_Recup	money,
	@i_Seg		int,
	@n_Imp	int,
	@n_Pais 	int,
	@t_Risco 	smallint,
	@n_Per 	int,
	@n_PerL	int,
	@d_Interv 	datetime

Select @i_Seg = i_Seg, @n_Imp = n_Imp, @d_Interv = d_Interv
From	Aviso_Sinistro
Where	i_Aviso = @i_Aviso

Select @n_Per = Year(@d_Interv) * 100 + Month(@d_Interv)

-- Obter Valor Recuperado
Select 	@v_Recup = ISNULL((SELECT SUM(v_Recuperado) FROM Recuperacao WHERE i_Aviso = @i_Aviso AND (t_Recuperacao_COFACE IN (2,3))),0) 

Select @n_Pais = n_Pais from Importador  where i_Seg = @i_Seg and n_Imp = @n_Imp
	
Select @t_Risco = t_Risco_Pais from Pais where n_Pais = @n_Pais

Select @n_PerL = Max(n_Per) from Par_Rec where n_Per <= @n_Per
--Select @v_Lim = v_Lim from Par_Rec where n_Per <= @n_Per and n_Per >= @n_PerL

if @t_Risco = 1 
	Select @v_Lim = v_Lim, @tx_Rec = tx_Rec_Y, @tx_RecA = tx_Rec_Y_Ac from Par_Rec where n_Per <= @n_Per and n_Per >= @n_PerL
else
	Select @v_Lim = v_Lim, @tx_Rec = tx_Rec_Z, @tx_RecA = tx_Rec_Z_Ac from Par_Rec where n_Per <= @n_Per and n_Per >= @n_PerL
	
if @v_Lim > @v_Recup 
	set @v_Rec = round(@v_Recup * @tx_Rec / 100,2)
else
	set @v_Rec = round(@v_Lim * @tx_Rec / 100,2) +  round((@v_Recup - @v_Lim) * @tx_RecA / 100,2)

RETURN (0)

SET QUOTED_IDENTIFIER  OFF    SET ANSI_NULLS  ON 
*/

function calculaVRec($i_Aviso){
  global $dbSisSeg;
  $x = odbc_exec($dbSisSeg,
		 "SELECT i_Seg, n_Imp, d_Interv, Year(d_Interv) * 100 + Month(d_Interv)
                  FROM Aviso_Sinistro WHERE i_Aviso=$i_Aviso");
  if(odbc_fetch_row($x)){
    $i_Seg = odbc_result($x, 1);
    $n_Imp = odbc_result($x, 2);
    $d_Interv = odbc_result($x, 3);
    $n_Per = odbc_result($x, 4);
  }

  $x = odbc_exec($dbSisSeg,
		 "SELECT ISNULL((SELECT SUM(v_Recuperado) FROM Recuperacao 
                               WHERE i_Aviso = $i_Aviso AND (t_Recuperacao_COFACE IN (2, 3))), 0)");
  if(odbc_fetch_row($x)){
    $v_Recup = odbc_result($x, 1);
  }

  $x = odbc_exec($dbSisSeg,
		 "SELECT n_Pais FROM Importador WHERE i_Seg=$i_Seg AND n_Imp=$n_Imp");
  if(odbc_fetch_row($x)){
    $n_Pais = odbc_result($x, 1);
  }
  $x = odbc_exec($dbSisSeg, "SELECT t_Risco_Pais FROM Pais WHERE n_Pais=$n_Pais");
  if(odbc_fetch_row($x)){
    $t_Risco = odbc_result($x, 1);
  }
  $x = odbc_exec($dbSisSeg, "SELECT Max(n_Per) FROM Par_Rec WHERE n_Per <= $n_Per");
  if(odbc_fetch_row($x)){
    $n_PerL = odbc_result($x, 1);
  }

  if($t_Risco == 1){
    $q = "SELECT v_Lim, tx_Rec_Y, tx_Rec_Y_Ac FROM Par_Rec WHERE n_Per<=$n_Per AND n_Per>=$n_PerL";
    echo $q;
    $x = odbc_exec($dbSisSeg, $q);
    if(odbc_fetch_row($x)){
      $v_Lim = odbc_result($x, 1);
      $tx_Rec = odbc_result($x, 2);
      $tx_RecA = odbc_result($x, 3);
    }
  }else{
    $x = odbc_exec($dbSisSeg,
		   "SELECT v_Lim, tx_Rec_Z, tx_Rec_Z_Ac from Par_Rec where n_Per<=$n_Per and n_Per>=$n_PerL");
    if(odbc_fetch_row($x)){
      $v_Lim = odbc_result($x, 1);
      $tx_Rec = odbc_result($x, 2);
      $tx_RecA = odbc_result($x, 3);
    }
  }

  if($v_Lim > $v_Recup){
    return sprintf("%.2lf", $v_Recup * $tx_Rec / 100);
  }else{
    return sprintf("%.2lf", $v_Lim * $tx_Rec / 100) +  sprintf("%.2lf", ($v_Recup - $v_Lim) * $tx_RecA / 100);
  }
}

$v_Rec = calculaVRec($i_Aviso);
?>
