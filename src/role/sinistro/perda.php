<?php /*
 codigo original do procedure
CREATE PROCEDURE ssI_Perda
	@i_Aviso		int,		@n_Seq		smallint OUTPUT,
	@Numero_Fatura	char(15) = NULL,	@d_Fatura	datetime = NULL,
	@d_Embarque	datetime = NULL,	@d_Vencimento	datetime = NULL,
	@d_Prorrogacao	datetime = NULL,	@v_Fatura	money = NULL,
	@v_Juros	money = NULL,		@v_Pago	money = NULL,
	@v_Sinistro	money	OUTPUT,	@v_Perda	money	OUTPUT
AS

IF EXISTS (SELECT Numero_Fatura FROM Perda WHERE Numero_Fatura = @Numero_Fatura AND i_Aviso = @i_Aviso)
  BEGIN
	RAISERROR ('Numero de Fatura ja cadastrado',15,1)
	RETURN(15)
  END
ELSE
  BEGIN
	BEGIN TRAN

	SELECT	@n_Seq = ISNULL((SELECT MAX(n_Seq) FROM Perda WHERE i_Aviso = @i_Aviso),0) + 1
	INSERT	Perda
	(i_Aviso, n_Seq, Numero_Fatura, d_Fatura, d_Embarque, 
	d_Vencimento, d_Prorrogacao, v_Fatura, v_Juros, v_Pago)
	VALUES	
	(@i_Aviso, @n_Seq, UPPER(@Numero_Fatura), @d_Fatura, @d_Embarque,
	@d_Vencimento, @d_Prorrogacao, @v_Fatura, @v_Juros, @v_Pago)
	IF @@ERROR <> 0  GOTO on_Error

	-- Atualiza o campo v_Sinistro em Aviso_Sinistro
	EXEC ssCalc_V_Sinistro @v_Sinistro OUTPUT, @i_Aviso

	UPDATE 	Aviso_Sinistro
	SET	v_Sinistro = @v_Sinistro
	WHERE	i_Aviso = @i_Aviso
	IF @@ERROR <> 0  GOTO on_Error

	EXEC ssCalc_V_Perda @v_Perda OUTPUT, @i_Aviso

	EXEC ssA_PagRec_Sin_Ind @i_Aviso -- Atualiza PagRec de Indenização
	IF @@ERROR <> 0 GOTO on_Error

	EXEC ssA_Sin_Res @i_Aviso -- Atualiza Sinistro_Resseguro
	IF @@ERROR <> 0 GOTO on_Error

	EXEC ssA_PagRec_Sin_Res @i_Aviso -- Atualiza PagRec de Sinistro_Resseguro
	IF @@ERROR <> 0 GOTO on_Error

	COMMIT TRAN
	RETURN(0)
  END

on_Error:
	ROLLBACK TRAN
	RAISERROR ('Problemas na inclusão dos dados',15,1)
	RETURN(15)
*/

$ret = 0;
$x = odbc_exec($dbSisSeg,
	       "SELECT Numero_Fatura FROM Perda WHERE Numero_Fatura=$Numero_Fatura AND i_Aviso=$i_Aviso");

if(odbc_fetch_row($x)){
  $msg = 'Número de Fatura já cadastrado';
  $ret = 15;
}else{
  $x = odbc_exec($dbSisSeg,
		 "SELECT ISNULL((SELECT MAX(n_Seq) FROM Perda WHERE i_Aviso=$i_Aviso), 0) + 1");
  if(odbc_fetch_row($x)){
    $n_Seq = odbc_result($x, 1);
  }

  $x = odbc_exec($dbSisSeg,
		 "INSERT into Perda
	          (i_Aviso, n_Seq, Numero_Fatura, d_Fatura, d_Embarque,
	           d_Vencimento, d_Prorrogacao, v_Fatura, v_Juros, v_Pago)
	          VALUES
	          ($i_Aviso, $n_Seq, '$Numero_Fatura', '$d_Fatura', '$d_Embarque',
	           '$d_Vencimento', '$d_Prorrogacao', $v_Fatura, $v_Juros, $v_Pago)");
  if(! $x){
    $msg = 'Problema na inclusão dos dados';
    $ret = 15;
    return;
  }

  require_once("calcVSinistro.php");
  $x = odbc_exec($dbSisSeg,
		 "UPDATE Aviso_Sinistro	SET v_Sinistro=$v_Sinistro WHERE i_Aviso=$i_Aviso");
  if(! $x){
    $msg = 'Problema na inclusão dos dados';
    $ret = 15;
    return;
  }

  require_once("calcVPerda.php");
  if($ret != 0){
    $msg = 'Problema na inclusão dos dados 2';
    return;
  }

  require_once("pagRecSinInd.php");
  if($ret != 0){
    $msg = 'Problema na inclusão dos dados 3';
    return;
  }

  require_once("sinRes.php");
  if($ret != 0){
    $msg = 'Problema na inclusão dos dados 4';
    return;
  }

  require_once("pagRecSinRes.php");
  if($ret != 0){
    $msg = 'Problema na inclusão dos dados 5';
    return;
  }
}

?>
