<?php

/*
CREATE PROCEDURE ssI_Recuperacao
		@i_Aviso			int,
		@n_Seq			smallint OUTPUT,
		@Numero_Fatura		char(15) = NULL,
		@t_Recuperacao_COFACE	tinyint = NULL,
		@v_Recuperado		money = NULL,
		@d_Recuperacao		datetime = NULL,
		@v_Sinistro		money	OUTPUT,
		@v_Rec			money	OUTPUT
AS

BEGIN TRAN

SELECT	@n_Seq = ISNULL((SELECT MAX(n_Seq) FROM Recuperacao WHERE i_Aviso = @i_Aviso),0) + 1
INSERT	Recuperacao
(i_Aviso, n_Seq, Numero_Fatura, t_Recuperacao_COFACE,
v_Recuperado, d_Recuperacao, s_R, d_Situacao)
VALUES	
(@i_Aviso, @n_Seq, UPPER(@Numero_Fatura), @t_Recuperacao_COFACE,
@v_Recuperado, @d_Recuperacao, 1, GETDATE())

IF @@ERROR <> 0  GOTO on_Error

-- IF (SELECT s_Aviso FROM Aviso_Sinistro WHERE i_Aviso = @i_Aviso) < 4 
-- Se a situacao do aviso for 'indenizacao aprovada' ou posterior, 
-- a alteracao na recuperacao não afeta o valor do sinistro.
  BEGIN
    EXEC ssCalc_V_Sinistro @v_Sinistro OUTPUT, @i_Aviso
    EXEC ssCalc_V_Rec @v_Rec OUTPUT, @i_Aviso
--    SELECT @v_Sinistro = ISNULL((SELECT SUM(v_Fatura) FROM Perda WHERE i_Aviso = @i_Aviso),0)
--		+  ISNULL((SELECT SUM(v_Juros) FROM Perda WHERE i_Aviso = @i_Aviso) ,0)
--		-   ISNULL((SELECT SUM(v_Recuperado) 
--			  FROM Recuperacao
--			  WHERE i_Aviso = @i_Aviso AND (t_Recuperacao_COFACE = 1 OR
--				 t_Recuperacao_COFACE = 2)),0) 
--
    UPDATE Aviso_Sinistro
    SET	v_Sinistro = @v_Sinistro,
	v_Rec = @v_Rec
    WHERE	i_Aviso = @i_Aviso
  END
IF @@ERROR <> 0  GOTO on_Error

IF @t_Recuperacao_COFACE = 3 -- RESSARCIMENTO
   BEGIN
	EXEC ssI_PagRec 4, @i_Aviso, @n_Seq
   END
IF @@ERROR <> 0  GOTO on_Error

EXEC ssA_PagRec_Sin_Ind @i_Aviso -- Atualiza PagRec de Indenizacao
IF @@ERROR <> 0 GOTO on_Error

EXEC ssA_Sin_Res @i_Aviso -- Atualiza Sinistro_Resseguro
IF @@ERROR <> 0 GOTO on_Error

EXEC ssA_PagRec_Sin_Res @i_Aviso -- Atualiza PagRec de Sinistro_Resseguro
IF @@ERROR <> 0 GOTO on_Error

COMMIT TRAN
RETURN(0)

on_Error:
ROLLBACK TRAN
RAISERROR ('Problemas na inclusão dos dados',15,1)
RETURN(15)
*/

$x = odbc_exec($dbSisSeg,
	       "SELECT ISNULL((SELECT MAX(n_Seq) FROM Recuperacao WHERE i_Aviso = $i_Aviso), 0) + 1");
$n_Seq = odbc_result($x, 1);

if($custo){
  $t_Recuperacao_COFACE = 2;
}else{
  $t_Recuperacao_COFACE = 1;
}
$query = "INSERT into Recuperacao
          (i_Aviso, n_Seq, Numero_Fatura, t_Recuperacao_COFACE,
          v_Recuperado, d_Recuperacao, s_R, d_Situacao)
          VALUES
          ($i_Aviso, $n_Seq, '". strtoupper($Numero_Fatura). "', $t_Recuperacao_COFACE,
          ". number_format($valor, 0). ", '$dateA', 1, getdate())";
//echo "<pre>$query</pre>";
$x = odbc_exec($dbSisSeg, $query);
if(! $x){
  $ok = false;
  return;
}

require_once('calcVSinistro.php');
require_once('calcVRec.php');

$x = odbc_exec($dbSisSeg,
	       "UPDATE Aviso_Sinistro SET v_Sinistro=cast('$v_Sinistro' as money), v_Rec=cast('$v_Rec' as money) WHERE i_Aviso=$i_Aviso");
if(! $x){
  $ok = false;
  return;
}

//chama os stored procedures
// //$mss = mssql_connect('sbcesun', 'sa', ''); // pra funcionar na sbce
// $mss = mssql_connect('.', 'sa', '');
// if(! $mss){
//   $msg .= "Falha ao conectar-se ao banco de dados<br>";
//   $ok = FALSE;
//   return;
// }
// //if(! mssql_select_db('SisSegTeste', $mss)){ // pra funcionar na sbce
// if(! mssql_select_db('SisSeg', $mss)){
//   $msg .= "Falha ao selecionar ao banco de dados<br>";
//   $ok = FALSE;
//   return;
// }

// $ret = -1;
// $sp = mssql_init('ssI_PagRec', $mss);
// if(! $sp){
//   $msg .= "Falha ao iniciar stored procedure ssI_PagRec<br>";
//   $ok = FALSE;
//   return;
// }
// $quatro = 4; // nao posso passar o valor "4" direto para o mssql_bind, senao dá pau;
// mssql_bind($sp, "@T", &$quatro, SQLINT2);
// mssql_bind($sp, "@C1", &$i_Aviso, SQLINT2);
// mssql_bind($sp, "@C2", &$n_Seq, SQLINT2);
// mssql_bind($sp, "RETVAL", &$ret, SQLINT2);
// $r = mssql_execute($sp);
// unset($sp);
// if($ret != 0){
//   $msg .= "Erro ao executar procedure ssI_PagRec<br>";
//   $ok = FALSE;
//   return;
// }

$x = odbc_exec($dbSisSeg,
	       "INSERT PagRec  (i_Seg, i_BC, i_Aviso, n_Seq_Parcela, n_Sucursal, n_Ramo, n_Apolice, n_Endosso, c_Coface, n_Prop,
			d_Vencimento, v_Documento, AV.n_Moeda, t_Doc, s_Pagamento, d_Situacao, d_Sistema)
	SELECT	AV.i_Seg, BC.i_BC, AV.i_Aviso, RE.n_Seq, AV.n_Sucursal, AV.n_Ramo, AV.n_Apolice, 0, BC.c_Coface, BC.n_Prop,
			RE.d_Recuperacao, RE.v_Recuperado, BC.n_Moeda, 9, 1, GETDATE(), GETDATE()
	FROM Recuperacao RE
	JOIN Aviso_Sinistro AV ON RE.i_Aviso = AV.i_Aviso
	JOIN	Base_Calculo BC ON AV.n_Sucursal = BC.n_Sucursal AND AV.n_Ramo = BC.n_Ramo AND AV.n_Apolice = BC.n_Apolice AND BC.n_Endosso = 0
	WHERE	RE.i_Aviso = $i_Aviso AND RE.n_Seq = $n_Seq");

// $ret = -1;
// $sp = mssql_init('ssI_PagRec_Sin_Ind', $mss);
// if(! $sp){
//   $msg .= "Falha ao iniciar stored procedure ssI_PagRec_Sin_Ind<br>";
//   $ok = FALSE;
//   return;
// }
// mssql_bind($sp, "@i_Aviso", &$i_Aviso, SQLINT2);
// mssql_bind($sp, "RETVAL", &$ret, SQLINT2);
// $r = mssql_execute($sp);
// unset($sp);
// if($ret != 0){
//   $msg .= "Erro ao executar procedure ssI_PagRec_Sin_Ind<br>". mssql_get_last_message();
//   $ok = FALSE;
//   return;
// }

// $ret = -1;
// $sp = mssql_init('ssI_Sin_Res', $mss);
// if(! $sp){
//   $msg .= "Falha ao iniciar stored procedure ssI_Sin_Res<br>";
//   $ok = FALSE;
//   return;
// }
// mssql_bind($sp, "@i_Aviso", &$i_Aviso, SQLINT2);
// mssql_bind($sp, "RETVAL", &$ret, SQLINT2);
// $r = mssql_execute($sp);
// unset($sp);
// if($ret != 0){
//   $msg .= "Erro ao executar procedure ssI_Sin_Res<br>";
//   $ok = FALSE;
//   return;
// }

// $ret = -1;
// $sp = mssql_init('ssI_PagRec_Sin_Res', $mss);
// if(! $sp){
//   $msg .= "Falha ao iniciar stored procedure ssI_PagRec_Sin_Res<br>";
//   $ok = FALSE;
//   return;
// }
// mssql_bind($sp, "@i_Aviso", &$i_Aviso, SQLINT2);
// mssql_bind($sp, "RETVAL", &$ret, SQLINT2);
// $r = mssql_execute($sp);
// unset($sp);
// if($ret != 0){
//   $msg .= "Erro ao executar procedure ssI_PagRec_Sin_Res<br>";
//   $ok = FALSE;
//   return;
// }

//mssql_close($mss);
?>
