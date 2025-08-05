<?php /* codigo do procedure

CREATE PROCEDURE ssA_PagRec_Sin_Ind
	@i_Aviso		int
AS

BEGIN TRAN

UPDATE 	PagRec
SET	v_Documento = - AV.v_Sinistro * PR.p_Cobertura/100,
	d_Situacao = GETDATE()
FROM	Aviso_Sinistro AV
JOIN	Base_Calculo BC 	ON 	AV.n_Sucursal = BC.n_Sucursal AND AV.n_Ramo =  BC.n_Ramo AND
				AV.n_Apolice = BC.n_Apolice AND BC.n_Endosso = 0
JOIN	Proposta PR	ON	BC.c_Coface = PR.c_Coface AND BC.n_Prop = PR.n_Prop
JOIN	PagRec PA	ON	AV.i_Aviso = PA.i_Aviso
WHERE	PA.i_Aviso = @i_Aviso AND PA.t_Doc = 1002 AND PA.s_Pagamento = 0
IF @@ERROR <> 0 GOTO on_Error

COMMIT TRAN
RETURN (0)

on_Error:
ROLLBACK TRAN
RAISERROR ('Problemas na Alteração dos dados',15,1)
RETURN(15)

*/

$x = odbc_exec($dbSisSeg,
	       "UPDATE PagRec
                SET v_Documento = - AV.v_Sinistro * PR.p_Cobertura/100, d_Situacao = GETDATE()
                FROM Aviso_Sinistro AV
                JOIN Base_Calculo BC ON AV.n_Sucursal = BC.n_Sucursal AND AV.n_Ramo =  BC.n_Ramo
                AND AV.n_Apolice = BC.n_Apolice AND BC.n_Endosso = 0
                JOIN Proposta PR ON BC.c_Coface = PR.c_Coface AND BC.n_Prop = PR.n_Prop
                JOIN PagRec PA ON AV.i_Aviso = PA.i_Aviso
                WHERE PA.i_Aviso = $i_Aviso AND PA.t_Doc = 1002 AND PA.s_Pagamento = 0");
if(!$x){
  $ret = 15;
  $msg = 'Problemas na Alteração dos dados';
}
?>
