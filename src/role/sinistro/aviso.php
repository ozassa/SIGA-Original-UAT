<?php

/*CREATE PROCEDURE ssI_Aviso_Sinistro
	@i_Aviso	int = NULL OUTPUT,	@n_Sinistro	int = NULL OUTPUT,	@n_User	int = NULL,
	@n_Sucursal	int = NULL,		@n_Ramo	int = NULL,		@n_Apolice	int = NULL,
	@i_Seg		int,			@n_Imp	int = NULL,		@v_Sinistro	money = NULL,
	@v_Serv_Rec_COFACE	money = NULL,	@d_Aviso	datetime = NULL,	@d_Interv	datetime = NULL,
	@d_Prev_Pag	datetime = NULL,	@d_Pag_COFACE	datetime = NULL,@s_Aviso	int = NULL,
	@v_Credito	money = NULL,		@t_Coface		tinyint = 0,	@Coment	varchar(255) = NULL,
	@p_Exp_Recup numeric(7,3) = 0
AS

DECLARE	@d_Venc datetime, @dia tinyint, @n_Moeda tinyint, @n_Per int

-- Busca a moeda da apólice
SELECT @n_Moeda = n_Moeda 
FROM Base_Calculo
WHERE n_Sucursal = @n_Sucursal  AND n_Ramo = @n_Ramo AND n_Apolice = @n_Apolice AND n_Endosso = 0

-- Caso a intervenção tenha sido solicitada
IF @s_Aviso = 2
   BEGIN
	-- Gera número de sinistro
	SET @n_Sinistro = (SELECT MAX(n_Sinistro) FROM Aviso_Sinistro) + 1
	-- Define a data prevista para pagamento da indenização, caso não tenha sido informada
	IF @d_Prev_Pag IS NULL
		SET @d_Prev_Pag = DATEADD(mm, 5, @d_Interv)
	-- Define a data de vencimento do Sinistro_Resseguro
	SET @d_Venc = DATEADD(mm, 3, @d_Prev_Pag)
	SET @dia = DATEPART(dd, @d_Venc)
	SET @d_Venc = DATEADD (dd, 15-@dia, @d_Venc)
   END

-- Busca o valor fixo de recuperação da Coface
IF @t_Coface = 1 AND @v_Serv_Rec_COFACE = 0 AND NOT @d_Interv IS NULL
   BEGIN
	SET @n_Per = YEAR(@d_Interv) * 100 + MONTH(@d_Interv)
	SET @v_Serv_Rec_COFACE = (SELECT TOP 1 v_Un_Processo FROM Par_Rec WHERE n_Per <= @n_Per ORDER BY n_Per DESC)
   END

BEGIN TRAN

INSERT Aviso_Sinistro
	(n_User, n_Sinistro, n_Sucursal, n_Ramo, n_Apolice, i_Seg, n_Imp, v_Sinistro, d_Aviso, d_Interv, d_Prev_Pag, 
	s_Aviso, d_Situacao, d_Sistema, n_Moeda, v_Credito, v_Serv_Rec_COFACE, d_Pag_COFACE, Coment, p_Exp_Recup)
VALUES	(@n_User, @n_Sinistro, @n_Sucursal, @n_Ramo, @n_Apolice, @i_Seg, @n_Imp, @v_Sinistro, @d_Aviso, @d_Interv, @d_Prev_Pag,
		@s_Aviso, GETDATE(), GETDATE(), @n_Moeda, @v_Credito, @v_Serv_Rec_COFACE, @d_Pag_COFACE, @Coment, @p_Exp_Recup)
IF @@ERROR <> 0 GOTO on_Error

SET @i_Aviso = @@IDENTITY

-- Inclui PagRec Indenização, Sinistro_Resseguro e PagRec Sinistro_Resseguro
IF @s_Aviso = 2
   BEGIN
	-- Inclui PagRec Indenização (Situação 0)
	EXEC ssI_PagRec 3,@i_Aviso
	IF @@ERROR <> 0 GOTO on_Error

	-- Inclui Sinistro_Resseguro
	INSERT 	Sinistro_Resseguro
		(i_Aviso, c_Seg, s_SR, d_Situacao, d_Venc)
	SELECT	@i_Aviso, RE.c_Seg, 1, GETDATE(), @d_Venc
	FROM	Resseguro RE
	JOIN	Base_Calculo BC	ON	RE.i_BC = BC.i_BC
	JOIN	Aviso_Sinistro AV	ON	BC.n_Sucursal = AV.n_Sucursal AND BC.n_Ramo = AV.n_Ramo AND
					BC.n_Apolice = AV.n_Apolice AND BC.n_Endosso = 0
	WHERE	AV.i_Aviso = @i_Aviso
	IF @@ERROR <> 0 GOTO on_Error

	-- Inclui PagRec Sinistro_Resseguro (Situação 0)
	INSERT  PagRec	(i_Seg, i_Aviso, c_Seg, n_Sucursal, n_Ramo, n_Apolice, n_Endosso, c_Coface, n_Prop,
			d_Vencimento, n_Moeda, t_Doc, s_Pagamento, d_Situacao, d_Sistema,
			v_Documento)
	SELECT 		AV.i_Seg, AV.i_Aviso, RE.c_Seg, AV.n_Sucursal, AV.n_Ramo, AV.n_Apolice, 0, BC.c_Coface, BC.n_Prop,
			@d_Venc, AV.n_Moeda, 7, 0, getdate(), getdate(),
			AV.v_Sinistro * (PR.p_Cobertura/100) * (RE.p_Seguradora/100) * (RE.p_Resseguro/100)
	FROM	Aviso_Sinistro AV
	JOIN	Base_Calculo BC 	ON 	AV.n_Sucursal = BC.n_Sucursal AND AV.n_Ramo =  BC.n_Ramo AND
					AV.n_Apolice = BC.n_Apolice AND BC.n_Endosso = 0
	JOIN	Proposta PR	ON	BC.c_Coface = PR.c_Coface AND BC.n_Prop = PR.n_Prop
	JOIN	Resseguro RE	ON	RE.i_BC = BC.i_BC
	WHERE	i_Aviso =	@i_Aviso
	IF @@ERROR <> 0 GOTO on_Error
   END

COMMIT TRAN
RETURN (0)

on_Error:
ROLLBACK TRAN
RAISERROR ('Problemas na Inclusão dos Dados',15,1)
RETURN(15)

*/

$n_Sinistro = odbc_result(odbc_exec($dbSisSeg, "SELECT MAX(n_Sinistro) FROM Aviso_Sinistro"), 1) + 1;

$ret = 0;
$x = odbc_exec($dbSisSeg,
	       "SELECT n_Moeda FROM Base_Calculo
                WHERE n_Sucursal=$n_Sucursal  AND n_Ramo=$n_Ramo AND n_Apolice=$n_Apolice AND n_Endosso=0");
echo "SELECT n_Moeda FROM Base_Calculo WHERE n_Sucursal=$n_Sucursal  AND n_Ramo=$n_Ramo AND n_Apolice=$n_Apolice AND n_Endosso=0<br>";
if(odbc_fetch_row($x)){
  $n_Moeda = odbc_result($x, 1);
}

$x = odbc_exec($dbSisSeg,
	       "INSERT Aviso_Sinistro
	        (n_User, n_Sinistro, n_Sucursal, n_Ramo, n_Apolice, i_Seg, n_Imp, v_Sinistro, d_Aviso, d_Interv, d_Prev_Pag, 
	        s_Aviso, d_Situacao, d_Sistema, n_Moeda, v_Credito, v_Serv_Rec_COFACE, d_Pag_COFACE, Coment, p_Exp_Recup)
                VALUES ($n_User, $n_Sinistro, $n_Sucursal, $n_Ramo, $n_Apolice, $i_Seg, $n_Imp, $v_Sinistro, '$d_Aviso', '$d_Interv', NULL,
		$s_Aviso, GETDATE(), GETDATE(), $n_Moeda, NULL, NULL, NULL, NULL, $p_Exp_Recup)");

if(! $x){
  $msg = 'Problema na inclusão dos dados';
  $ret = 15;
  return;
}

$x = odbc_exec($dbSisSeg,
	       "select max(i_Aviso) from Aviso_Sinistro where n_Sinistro=$n_Sinistro and n_Apolice=$n_Apolice and i_Seg=$i_Seg");
$i_Aviso = odbc_result($x, 1);

if(! $i_Aviso){
  $msg = 'Problema na inclusão dos dados';
  $ret = 15;
  return;
}

?>
