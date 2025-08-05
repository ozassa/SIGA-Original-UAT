<?php /* codigo do procedure

CREATE Procedure ssCalc_V_Sinistro
	@v_Sinistro	money	OUTPUT,
	@i_Aviso		int
AS

DECLARE	@n_Sucursal	smallint,
	@n_Ramo	smallint,
	@n_Apolice	int,
	@p_Cobertura	numeric(7,3),
	@LMI		money,
	@v_PrMin	money,
	@v_Pago		money,
	@v_Perda	money,
	@v_Recup	money,
	@v_Credito	money,
	@i_Seg		int,
	@n_Imp	int

SELECT	@n_Sucursal = n_Sucursal,
	@n_Ramo = n_Ramo,
	@n_Apolice = n_Apolice,
	@i_Seg = i_Seg,
	@n_Imp = n_Imp,
	@v_Credito = ISNULL(v_Credito,0)
FROM	Aviso_Sinistro
WHERE	i_Aviso = @i_Aviso

SELECT 	@v_PrMin = PR.v_PrMin,
	@p_Cobertura = PR.p_Cobertura
FROM	Base_Calculo BC
JOIN	Proposta PR
ON	BC.c_Coface = PR.c_Coface AND
	BC.n_Prop = PR.n_Prop
WHERE 	BC.n_Sucursal = @n_Sucursal AND
	BC.n_Ramo = @n_Ramo AND
	BC.n_Apolice = @n_Apolice

// O valor de crédito passará a ser obtido no registro de sinistro
//SELECT	@v_Credito = ISNULL(v_Credito,0)
//FROM	Importador
//WHERE	i_Seg = @i_Seg AND n_Imp = @n_Imp


SELECT	@LMI = 30 * @v_PrMin

SELECT	@v_Pago = ISNULL(SUM(v_Parcela),0)
FROM	Parcela
WHERE	n_Sucursal = @n_Sucursal AND
	n_Ramo = @n_Ramo AND
	n_Apolice = @n_Apolice AND
	s_Parcela = 2 --pago

SELECT	@v_Pago, @v_PrMin

IF 	@v_Pago > @v_PrMin
SELECT	@LMI = 30 * @v_Pago

SELECT 	@v_Sinistro = 	ISNULL((SELECT SUM(v_Fatura) FROM Perda WHERE i_Aviso = @i_Aviso),0)
		+	ISNULL((SELECT SUM(v_Juros) FROM Perda WHERE i_Aviso = @i_Aviso),0) 
		-	ISNULL((SELECT SUM(v_Pago) FROM Perda WHERE i_Aviso = @i_Aviso),0) 
		-	ISNULL((SELECT SUM(v_Recuperado)
				FROM Recuperacao
				WHERE i_Aviso = @i_Aviso AND
				(t_Recuperacao_COFACE = 1 OR
				t_Recuperacao_COFACE = 2)),0) 

IF 	@v_Sinistro < 0
SELECT	@v_Sinistro = 0

IF	@v_Sinistro > @LMI/(@p_Cobertura/100)
SELECT	@v_Sinistro = @LMI/(@p_Cobertura/100)

IF	@v_Sinistro > @v_Credito
SELECT	@v_Sinistro = @v_Credito

RETURN

*/

if(! function_exists('calcVSin')){
  function calcVSin($i_Aviso){
    global $dbSisSeg;
    $x = odbc_exec($dbSisSeg,
		   "select n_Sucursal, n_Ramo, n_Apolice, i_Seg, n_Imp, v_Credito
                    from Aviso_Sinistro where i_Aviso=$i_Aviso");
    if(odbc_fetch_row($x)){
      $n_Sucursal = odbc_result($x, 1);
      $n_Ramo = odbc_result($x, 2);
      $n_Apolice = odbc_result($x, 3);
      $i_Seg = odbc_result($x, 4);
      $n_Imp = odbc_result($x, 5);
      $v_Credito = odbc_result($x, 6);
      if(! $v_Credito){
	$v_Credito = 0;
      }
    }

    $x = odbc_exec($dbSisSeg,
		   "select PR.v_PrMin, PR.p_Cobertura
                    from Base_Calculo BC join Proposta PR on BC.c_Coface=PR.c_Coface and BC.n_Prop=PR.n_Prop
                    where BC.n_Sucursal=$n_Sucursal and BC.n_Ramo=$n_Ramo and BC.n_Apolice=$n_Apolice");
    if(odbc_fetch_row($x)){
      $v_PrMin = odbc_result($x, 1);
      $p_Cobertura = odbc_result($x, 2);
    }

    $lmi = 30 * $v_PrMin;

    $v_Pago = 0;
    $x = odbc_exec($dbSisSeg,
		   "select SUM(v_Parcela) from Parcela where n_Sucursal=$n_Sucursal
                    and n_Ramo=$n_Ramo and n_Apolice=$n_Apolice and s_Parcela=2"); // pago
    if(odbc_fetch_row($x)){
      $v_Pago = odbc_result($x, 1);
      if(! $v_Pago){
	$v_Pago = 0;
      }
    }
    if($v_Pago > $v_PrMin){
      $lmi = 30 * $v_Pago;
    }

    $x = odbc_exec($dbSisSeg,
		   "SELECT ISNULL((SELECT SUM(v_Fatura) FROM Perda WHERE i_Aviso = $i_Aviso), 0)
		   + ISNULL((SELECT SUM(v_Juros) FROM Perda WHERE i_Aviso = $i_Aviso),0) 
		   - ISNULL((SELECT SUM(v_Pago) FROM Perda WHERE i_Aviso = $i_Aviso),0) 
		   - ISNULL((SELECT SUM(v_Recuperado)
			     FROM Recuperacao
			     WHERE i_Aviso = $i_Aviso AND
			     (t_Recuperacao_COFACE = 1 OR
			     t_Recuperacao_COFACE = 2)), 0)");
    if(odbc_fetch_row($x)){
      $v_Sinistro = odbc_result($x, 1);
    }

    if($v_Sinistro < 0){
      $v_Sinistro = 0;
    }
    if($v_Sinistro > $lmi / ($p_Cobertura / 100)){
      $v_Sinistro = $lmi / ($p_Cobertura / 100);
    }
    if($v_Sinistro > $v_Credito){
      $v_Sinistro = $v_Credito;
    }
    return $v_Sinistro;
  } // calcVSin
}

$v_Sinistro = calcVSin($i_Aviso);
?>
