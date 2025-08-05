<?php /* codigo do procedure

CREATE PROCEDURE ssA_Sin_Res
	@i_Aviso		int
AS

BEGIN TRAN

UPDATE 	Sinistro_Resseguro
SET	v_Rec_Rsg = case when AV.v_Sinistro * (PR.p_Cobertura/100) * (1-(RE.p_Seguradora/100) * (RE.p_Resseguro/100)) < RE.v_Max_Retencao
			then AV.v_Sinistro * (PR.p_Cobertura/100) * (RE.p_Seguradora/100) * (RE.p_Resseguro/100) else AV.v_Sinistro * (PR.p_Cobertura/100) - RE.v_Max_Retencao end,
	d_Situacao = GETDATE()
FROM	Aviso_Sinistro AV
JOIN	Base_Calculo BC 	ON 	AV.n_Sucursal = BC.n_Sucursal AND AV.n_Ramo =  BC.n_Ramo AND
				AV.n_Apolice = BC.n_Apolice AND BC.n_Endosso = 0
JOIN	Proposta PR	ON	BC.c_Coface = PR.c_Coface AND BC.n_Prop = PR.n_Prop
JOIN	Resseguro RE	ON	RE.i_BC = BC.i_BC
JOIN	Sinistro_Resseguro SR ON 	RE.c_Seg = SR.c_Seg AND AV.i_Aviso = SR.i_Aviso
WHERE	SR.i_Aviso = @i_Aviso
IF @@ERROR <> 0 GOTO on_Error

COMMIT TRAN
RETURN (0)

on_Error:
ROLLBACK TRAN
RAISERROR ('Problemas na Alteração dos dados',15,1)
RETURN(15)

*/

$x = odbc_exec($dbSisSeg,
	       "UPDATE  Sinistro_Resseguro
SET v_Rec_Rsg = case when AV.v_Sinistro * (PR.p_Cobertura/100) * (1-(RE.p_Seguradora/100) * (RE.p_Resseguro/100)) < RE.v_Max_Retencao
   then AV.v_Sinistro * (PR.p_Cobertura/100) * (RE.p_Seguradora/100) * (RE.p_Resseguro/100) else AV.v_Sinistro * (PR.p_Cobertura/100) - RE.v_Max_Retencao end,
 d_Situacao = GETDATE()
FROM Aviso_Sinistro AV
JOIN Base_Calculo BC  ON  AV.n_Sucursal = BC.n_Sucursal AND AV.n_Ramo =  BC.n_Ramo AND
    AV.n_Apolice = BC.n_Apolice AND BC.n_Endosso = 0
JOIN Proposta PR ON BC.c_Coface = PR.c_Coface AND BC.n_Prop = PR.n_Prop
JOIN Resseguro RE ON RE.i_BC = BC.i_BC
JOIN Sinistro_Resseguro SR ON  RE.c_Seg = SR.c_Seg AND AV.i_Aviso = SR.i_Aviso
WHERE SR.i_Aviso = $i_Aviso");

if(!$x){
  $ret = 15;
  $msg = 'Problemas na Alteração dos dados';
}

?>
